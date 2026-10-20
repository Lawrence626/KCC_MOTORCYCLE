<?php

namespace App\Services;

use App\Models\DeadStock;
use App\Models\DSSRecommendation;
use App\Models\Product;
use App\Models\FastMovingProduct;
use App\Models\DSSSettings;
use App\Models\POSTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DSSRecommendationEngineService
{
    protected SalesVelocityAnalysisService $velocityService;

    public function __construct(SalesVelocityAnalysisService $velocityService)
    {
        $this->velocityService = $velocityService;
    }

    /**
     * Generate recommendations for all active products in inventory.
     */
    public function generateAllRecommendations(): void
    {
        if (!DSSSettings::getSetting('dss_analysis_enabled', true)) {
            return;
        }

        $products = Product::where('is_active', true)
            ->where('is_archived', false)
            ->get();

        foreach ($products as $product) {
            $this->generateProductRecommendations($product);
        }
    }

    /**
     * Generate recommendations for a specific product.
     */
    public function generateProductRecommendations(Product $product): Collection
    {
        $recommendations = collect();

        // 1. Generate Inventory Reorder & Stock Level Recommendation
        if (DSSSettings::getSetting('reorder_recommendation_enabled', true)) {
            $invRec = $this->generateInventoryRecommendation($product);
            if ($invRec) {
                $recommendations->push($invRec);
            }
        }

        // 2. Generate Dead Stock Recommendations if applicable
        $deadStock = DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if ($deadStock) {
            if (DSSSettings::getSetting('promotion_recommendation_enabled', true)) {
                $promo = $this->generatePromotionRecommendation($product, $deadStock);
                if ($promo) $recommendations->push($promo);
            }

            if (DSSSettings::getSetting('discount_recommendation_enabled', true)) {
                $discount = $this->generateDiscountRecommendation($product, $deadStock);
                if ($discount) $recommendations->push($discount);
            }

            if (DSSSettings::getSetting('bundle_recommendation_enabled', true)) {
                $bundle = $this->generateBundleRecommendation($product, $deadStock);
                if ($bundle) $recommendations->push($bundle);
            }

            $relocate = $this->generateRelocationRecommendation($product, $deadStock);
            if ($relocate) $recommendations->push($relocate);

            $featured = $this->generateFeaturedDisplayRecommendation($product, $deadStock);
            if ($featured) $recommendations->push($featured);

            $social = $this->generateSocialMediaRecommendation($product, $deadStock);
            if ($social) $recommendations->push($social);

            $supplier = $this->generateSupplierReturnRecommendation($product, $deadStock);
            if ($supplier) $recommendations->push($supplier);
        }

        return $recommendations;
    }

    /**
     * Generate DSS inventory reorder and stock management recommendation.
     */
    public function generateInventoryRecommendation(Product $product, $startDate = null, $endDate = null, ?int $customThreshold = null): ?DSSRecommendation
    {
        $metrics = $this->getProductSalesMetrics($product, $startDate, $endDate);

        $salesThisMonth = $metrics['sales_this_month'];
        $salesPrevMonth = $metrics['sales_prev_month'];
        $units30Days = $metrics['units_30_days'];
        $currentStock = (int) $product->stock_quantity;
        $currentReorderLevel = (int) ($product->reorder_level ?? 10);
        $velocity = $metrics['daily_velocity'];
        $fastThreshold = $customThreshold ?? (int) DSSSettings::getSetting('fast_moving_threshold_units', 50);

        // Determine Classification
        $isFastMoving = ($salesThisMonth >= $fastThreshold)
            || ($units30Days >= $fastThreshold)
            || ($salesPrevMonth > 0 && $salesThisMonth >= ($salesPrevMonth * 1.5) && $salesThisMonth >= 15);

        $isNormal = !$isFastMoving && ($salesThisMonth >= 5 || $units30Days >= 5);
        $isLow = !$isFastMoving && !$isNormal;

        if ($isFastMoving) {
            return $this->buildFastMovingRecommendation($product, $metrics, $currentStock, $currentReorderLevel, $fastThreshold);
        } elseif ($isNormal) {
            return $this->buildNormalStockRecommendation($product, $metrics, $currentStock, $currentReorderLevel);
        } else {
            return $this->buildLowSalesRecommendation($product, $metrics, $currentStock, $currentReorderLevel);
        }
    }

    /**
     * Build fast-moving product reorder level recommendation.
     */
    protected function buildFastMovingRecommendation(
        Product $product,
        array $metrics,
        int $currentStock,
        int $currentReorderLevel,
        int $fastThreshold
    ): DSSRecommendation
    {
        $salesThisMonth = $metrics['sales_this_month'];
        $salesPrevMonth = $metrics['sales_prev_month'];
        $velocity = $metrics['daily_velocity'];

        // Calculate suggested reorder level and stock quantity
        $suggestedReorderLevel = max(
            $currentReorderLevel + 10,
            (int) ceil($velocity * 20),
            (int) ceil($salesThisMonth * 0.4)
        );

        $suggestedStockQuantity = max(
            $currentStock,
            (int) ceil($velocity * 45),
            $suggestedReorderLevel * 2,
            (int) ceil($salesThisMonth * 1.2)
        );

        $daysOfStockLeft = $velocity > 0 ? round($currentStock / $velocity, 1) : 999;
        $isInsufficient = ($currentStock <= $currentReorderLevel)
            || ($currentStock < ($salesThisMonth * 0.5))
            || ($daysOfStockLeft <= 14);

        $stockoutRisk = $isInsufficient ? 'high' : ($daysOfStockLeft <= 30 ? 'medium' : 'low');
        $priority = $isInsufficient ? 'Critical' : 'High';

        // Clear outdated conflicting inventory recommendations
        DSSRecommendation::where('product_id', $product->id)
            ->whereIn('recommendation_type', ['normal_stock', 'low_sales_review'])
            ->update(['is_active' => false]);

        $description = sprintf(
            "High sales detected this month (%d units sold%s). This product is classified as fast-moving. Consider increasing the reorder level from %d to %d units and maintaining higher stock levels (recommended: %d units) because the current inventory (%d units) may not be sufficient to support the current sales rate (%.1f units/day).%s",
            $salesThisMonth,
            $salesPrevMonth > 0 ? " vs {$salesPrevMonth} last month" : "",
            $currentReorderLevel,
            $suggestedReorderLevel,
            $suggestedStockQuantity,
            $currentStock,
            $velocity,
            $isInsufficient ? " Warning: Current stock is critically low and at high risk of stockout." : ""
        );

        $rationale = sprintf(
            "Actual sales performance: %d units sold this month (sales velocity: %.2f units/day, fast-moving threshold: %d units). Current stock of %d units against reorder point of %d units provides ~%.1f days of coverage. %s",
            $salesThisMonth,
            $velocity,
            $fastThreshold,
            $currentStock,
            $currentReorderLevel,
            $daysOfStockLeft,
            $isInsufficient
                ? "Immediate replenishment and increasing reorder point is necessary to prevent stockouts."
                : "Higher reorder point and buffer stock is recommended to keep up with surging customer demand."
        );

        $existing = DSSRecommendation::where('product_id', $product->id)
            ->where('recommendation_type', 'reorder_level')
            ->first();

        $actionTakenAt = null;
        $actionNotes = null;
        if ($existing && $existing->action_taken_at && $currentReorderLevel >= $suggestedReorderLevel) {
            $actionTakenAt = $existing->action_taken_at;
            $actionNotes = $existing->action_notes;
        }

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'reorder_level'],
            [
                'title' => $isInsufficient
                    ? 'Fast-Moving Stockout Warning: Increase Reorder Level'
                    : 'Fast-Moving Stock Alert: Increase Reorder Level',
                'description' => $description,
                'priority' => $priority,
                'metadata' => [
                    'classification' => 'fast_moving',
                    'sales_this_month' => $salesThisMonth,
                    'sales_prev_month' => $salesPrevMonth,
                    'units_30_days' => $metrics['units_30_days'],
                    'current_stock' => $currentStock,
                    'current_reorder_level' => $currentReorderLevel,
                    'suggested_reorder_level' => $suggestedReorderLevel,
                    'suggested_stock_quantity' => $suggestedStockQuantity,
                    'sales_velocity' => $velocity,
                    'days_of_stock_left' => $daysOfStockLeft,
                    'stockout_risk' => $stockoutRisk,
                    'threshold_used' => $fastThreshold,
                    'rationale' => $rationale,
                ],
                'is_active' => true,
                'action_taken_at' => $actionTakenAt,
                'action_notes' => $actionNotes,
                'generated_at' => $existing?->generated_at ?? now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Build normal sales recommendation.
     */
    protected function buildNormalStockRecommendation(
        Product $product,
        array $metrics,
        int $currentStock,
        int $currentReorderLevel
    ): DSSRecommendation
    {
        $salesThisMonth = $metrics['sales_this_month'];
        $salesPrevMonth = $metrics['sales_prev_month'];
        $velocity = $metrics['daily_velocity'];
        $daysOfStockLeft = $velocity > 0 ? round($currentStock / $velocity, 1) : 999;

        // Clear outdated conflicting inventory recommendations
        DSSRecommendation::where('product_id', $product->id)
            ->whereIn('recommendation_type', ['reorder_level', 'low_sales_review'])
            ->update(['is_active' => false]);

        $description = sprintf(
            "Sales performance is normal and steady this month (%d units sold%s). Current stock (%d units) and reorder level (%d units) are sufficient. Do not recommend increasing the reorder level unnecessarily.",
            $salesThisMonth,
            $salesPrevMonth > 0 ? " vs {$salesPrevMonth} units last month" : "",
            $currentStock,
            $currentReorderLevel
        );

        $rationale = sprintf(
            "Actual sales (%d units this month, %.2f units/day) reflect stable demand. Current stock of %d units covers approximately %.1f days of sales. Maintaining current reorder point of %d units preserves optimal working capital.",
            $salesThisMonth,
            $velocity,
            $currentStock,
            $daysOfStockLeft,
            $currentReorderLevel
        );

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'normal_stock'],
            [
                'title' => 'Maintain Current Reorder Level',
                'description' => $description,
                'priority' => 'Low',
                'metadata' => [
                    'classification' => 'normal_moving',
                    'sales_this_month' => $salesThisMonth,
                    'sales_prev_month' => $salesPrevMonth,
                    'units_30_days' => $metrics['units_30_days'],
                    'current_stock' => $currentStock,
                    'current_reorder_level' => $currentReorderLevel,
                    'sales_velocity' => $velocity,
                    'days_of_stock_left' => $daysOfStockLeft,
                    'stockout_risk' => 'none',
                    'rationale' => $rationale,
                ],
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Build low sales recommendation.
     */
    protected function buildLowSalesRecommendation(
        Product $product,
        array $metrics,
        int $currentStock,
        int $currentReorderLevel
    ): DSSRecommendation
    {
        $salesThisMonth = $metrics['sales_this_month'];
        $salesPrevMonth = $metrics['sales_prev_month'];
        $velocity = $metrics['daily_velocity'];
        $isExcessStock = $currentStock > ($currentReorderLevel * 3);

        // Clear outdated conflicting inventory recommendations
        DSSRecommendation::where('product_id', $product->id)
            ->whereIn('recommendation_type', ['reorder_level', 'normal_stock'])
            ->update(['is_active' => false]);

        $description = sprintf(
            "Low sales detected this month (%d units sold). Recommend maintaining the current reorder level (%d units) or reviewing whether excess stock (%d units) is being held to avoid unnecessary carrying costs.",
            $salesThisMonth,
            $currentReorderLevel,
            $currentStock
        );

        $rationale = sprintf(
            "Sales volume is low (%d units sold this month). Current stock (%d units) %s. Maintaining current reorder point prevents further inventory buildup.",
            $salesThisMonth,
            $currentStock,
            $isExcessStock ? "significantly exceeds current monthly velocity" : "remains steady"
        );

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'low_sales_review'],
            [
                'title' => 'Review Inventory & Maintain Reorder Level',
                'description' => $description,
                'priority' => $isExcessStock ? 'Medium' : 'Low',
                'metadata' => [
                    'classification' => 'slow_moving',
                    'sales_this_month' => $salesThisMonth,
                    'sales_prev_month' => $salesPrevMonth,
                    'units_30_days' => $metrics['units_30_days'],
                    'current_stock' => $currentStock,
                    'current_reorder_level' => $currentReorderLevel,
                    'sales_velocity' => $velocity,
                    'is_excess_stock' => $isExcessStock,
                    'rationale' => $rationale,
                ],
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Extract comprehensive sales metrics for a product.
     */
    public function getProductSalesMetrics(Product $product, $startDate = null, $endDate = null): array
    {
        $completedTransactions = POSTransaction::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        $now = Carbon::now();
        $isCustomRange = !empty($startDate) && !empty($endDate);
        $rangeStart = $isCustomRange ? Carbon::parse($startDate)->startOfDay() : $now->copy()->startOfMonth();
        $rangeEnd = $isCustomRange ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfMonth();

        $startOfPrevMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfPrevMonth = $now->copy()->subMonth()->endOfMonth();

        $salesThisMonth = 0;
        $salesPrevMonth = 0;
        $units7 = 0;
        $units30 = 0;
        $units60 = 0;
        $units90 = 0;
        $lastSaleDate = null;

        foreach ($completedTransactions as $transaction) {
            $items = $transaction->items;

            if (is_array($items)) {
                foreach ($items as $item) {
                    $itemProductId = $item['product_id'] ?? $item['id'] ?? null;
                    if ($itemProductId == $product->id) {
                        $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                        $transactionDate = Carbon::parse($transaction->completed_at);

                        if (!$lastSaleDate) {
                            $lastSaleDate = $transactionDate;
                        }

                        // Selected range / current month check
                        if ($transactionDate->between($rangeStart, $rangeEnd)) {
                            $salesThisMonth += $quantity;
                        }

                        // Previous month check
                        if ($transactionDate->between($startOfPrevMonth, $endOfPrevMonth)) {
                            $salesPrevMonth += $quantity;
                        }

                        $daysAgo = (int) abs($now->diffInDays($transactionDate));
                        if ($daysAgo <= 7) $units7 += $quantity;
                        if ($daysAgo <= 30) $units30 += $quantity;
                        if ($daysAgo <= 60) $units60 += $quantity;
                        if ($daysAgo <= 90) $units90 += $quantity;
                    }
                }
            }
        }

        $daysInPeriod = max(1, (int) $rangeStart->diffInDays($rangeEnd));
        $daysInCurrentMonthSoFar = max(1, (int) $now->day);
        $velocityPeriod = round($salesThisMonth / ($isCustomRange ? $daysInPeriod : $daysInCurrentMonthSoFar), 2);
        $velocity30 = round($units30 / 30, 2);
        $dailyVelocity = max($velocityPeriod, $velocity30);

        return [
            'sales_this_month' => $salesThisMonth,
            'sales_prev_month' => $salesPrevMonth,
            'units_7_days' => $units7,
            'units_30_days' => $units30,
            'units_60_days' => $units60,
            'units_90_days' => $units90,
            'daily_velocity' => $dailyVelocity,
            'last_sale_date' => $lastSaleDate,
            'is_custom_range' => $isCustomRange,
        ];
    }

    /**
     * Apply suggested reorder level from a recommendation.
     */
    public function applyReorderLevel(int $recommendationId): bool
    {
        $recommendation = DSSRecommendation::with('product')->find($recommendationId);

        if (!$recommendation || !$recommendation->product) {
            return false;
        }

        $suggestedReorderLevel = $recommendation->getSuggestedReorderLevel();

        if (!$suggestedReorderLevel) {
            return false;
        }

        $previousLevel = $recommendation->product->reorder_level;

        $recommendation->product->update([
            'reorder_level' => $suggestedReorderLevel,
        ]);

        $recommendation->markAsActioned(
            sprintf("Updated product reorder level from %d to %d units based on DSS recommendation.", $previousLevel, $suggestedReorderLevel)
        );

        return true;
    }

    /**
     * Generate promotion recommendation.
     */
    private function generatePromotionRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock) {
            return null;
        }

        $priority = $this->getPriorityForDaysWithoutSale($deadStock->days_without_sale);

        $description = sprintf(
            "This product has not been sold for over %d days. Consider creating a promotional campaign to boost sales.",
            $deadStock->days_without_sale
        );

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'promotion'],
            [
                'title' => 'Launch Promotional Campaign',
                'description' => $description,
                'priority' => $priority,
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Generate discount recommendation.
     */
    private function generateDiscountRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock || $deadStock->days_without_sale < 31) {
            return null;
        }

        $days = (int) $deadStock->days_without_sale;
        $autoRec = $deadStock->getAutomaticRecommendation();

        if ($days >= 91) {
            $minDiscount = 20;
            $maxDiscount = 20;
        } elseif ($days >= 61) {
            $minDiscount = 10;
            $maxDiscount = 15;
        } else {
            $minDiscount = 5;
            $maxDiscount = 5;
        }

        $priority = $this->getPriorityForDaysWithoutSale($days);

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'discount'],
            [
                'title' => 'Apply a discount to increase sales.',
                'description' => "Suggested Discount: {$autoRec['suggested_discount']}. Reason: {$autoRec['reason']}",
                'priority' => $priority,
                'metadata' => [
                    'suggested_discount_min' => $minDiscount,
                    'suggested_discount_max' => $maxDiscount,
                    'suggested_discount_label' => $autoRec['suggested_discount'],
                    'days_without_sale' => $days,
                ],
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Generate bundle recommendation.
     */
    private function generateBundleRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock) {
            return null;
        }

        // Get top fast moving products for bundling (max 5)
        $fastMoving = FastMovingProduct::orderBy('velocity_score', 'desc')
            ->limit(5)
            ->pluck('product_id')
            ->toArray();

        if (empty($fastMoving)) {
            return null;
        }

        $priority = $this->getPriorityForDaysWithoutSale($deadStock->days_without_sale);

        $fastMovingProducts = Product::whereIn('id', $fastMoving)->get();
        $bundleDescription = $fastMovingProducts->map(fn($p) => $p->name)->join(', ');

        $description = sprintf(
            "Bundle this product with these fast-moving items: %s. This creates value perception and helps move stale inventory.",
            $bundleDescription
        );

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'bundle'],
            [
                'title' => 'Create Bundle Offer',
                'description' => $description,
                'priority' => $priority,
                'metadata' => [
                    'bundle_product_ids' => $fastMoving,
                ],
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Generate warehouse relocation recommendation.
     */
    private function generateRelocationRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock || $deadStock->days_without_sale < 120) {
            return null;
        }

        $priority = $this->getPriorityForDaysWithoutSale($deadStock->days_without_sale);

        $description = "Consider moving this item to another warehouse or the Main Shop where customer demand is higher. Better visibility may increase sales.";

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'relocate'],
            [
                'title' => 'Relocate to Higher-Demand Location',
                'description' => $description,
                'priority' => $priority,
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Generate featured display recommendation.
     */
    private function generateFeaturedDisplayRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock || $deadStock->days_without_sale < 90) {
            return null;
        }

        $priority = $this->getPriorityForDaysWithoutSale($deadStock->days_without_sale);

        $description = "Place this product in a prominent location such as near the cashier or store entrance to increase visibility and customer interaction.";

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'featured_display'],
            [
                'title' => 'Featured Store Display',
                'description' => $description,
                'priority' => $priority,
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Generate social media promotion recommendation.
     */
    private function generateSocialMediaRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock || $deadStock->days_without_sale < 90) {
            return null;
        }

        $priority = $this->getPriorityForDaysWithoutSale($deadStock->days_without_sale);

        $description = "Advertise this product on Facebook, Instagram, or other social media platforms to reach a broader audience and drive online sales.";

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'social_media'],
            [
                'title' => 'Social Media Campaign',
                'description' => $description,
                'priority' => $priority,
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Generate supplier return recommendation.
     */
    private function generateSupplierReturnRecommendation(Product $product, ?DeadStock $deadStock = null): ?DSSRecommendation
    {
        $deadStock = $deadStock ?? DeadStock::where('product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$deadStock || $deadStock->days_without_sale < 180) {
            return null; // Only for critical priority
        }

        $description = sprintf(
            "This product has not sold in %d days. If supplier agreements allow, consider returning unsold inventory to free up cash and warehouse space.",
            $deadStock->days_without_sale
        );

        return DSSRecommendation::updateOrCreate(
            ['product_id' => $product->id, 'recommendation_type' => 'supplier_return'],
            [
                'title' => 'Return to Supplier',
                'description' => $description,
                'priority' => 'Critical',
                'is_active' => true,
                'generated_at' => now(),
                'last_updated_at' => now(),
            ]
        );
    }

    /**
     * Get priority level based on days without sale.
     */
    private function getPriorityForDaysWithoutSale(int $daysWithoutSale): string
    {
        if ($daysWithoutSale >= 180) {
            return 'Critical';
        }

        if ($daysWithoutSale >= 120) {
            return 'High';
        }

        if ($daysWithoutSale >= 90) {
            return 'Medium';
        }

        return 'Low';
    }

    /**
     * Get active recommendations for a product.
     */
    public function getProductRecommendations(int $productId): Collection
    {
        return DSSRecommendation::where('product_id', $productId)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Get pending recommendations (no action taken).
     */
    public function getPendingRecommendations(int $limit = 50): Collection
    {
        return DSSRecommendation::where('is_active', true)
            ->whereNull('action_taken_at')
            ->orderBy('priority', 'desc')
            ->orderBy('generated_at', 'desc')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    /**
     * Mark recommendation as actioned.
     */
    public function markAsActioned(int $recommendationId, string $notes = null): bool
    {
        $recommendation = DSSRecommendation::find($recommendationId);

        if (!$recommendation) {
            return false;
        }

        $recommendation->markAsActioned($notes);
        return true;
    }

    /**
     * Generate DSS Executive Summary metrics and fast-moving stock/reorder recommendations for Sales Analytics.
     */
    public function getExecutiveSummaryForSalesAnalytics($startDate = null, $endDate = null): array
    {
        $now = Carbon::now();
        $isCustom = !empty($startDate) && !empty($endDate);
        $start = $isCustom ? Carbon::parse($startDate)->startOfDay() : $now->copy()->startOfMonth();
        $end = $isCustom ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfMonth();

        // 1. Fetch completed POS transactions in range
        $transactions = POSTransaction::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$start, $end])
            ->get();

        $usedFallbackPeriod = false;
        // If current month has 0 transactions, evaluate recent period (past 180 days) so recommendations reflect actual data
        if (!$isCustom && $transactions->isEmpty()) {
            $fallbackStart = $now->copy()->subDays(180)->startOfDay();
            $fallbackTransactions = POSTransaction::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$fallbackStart, $end])
                ->get();
            if ($fallbackTransactions->isNotEmpty()) {
                $start = $fallbackStart;
                $transactions = $fallbackTransactions;
                $usedFallbackPeriod = true;
            }
        }

        // Aggregate product sales in period
        $productSales = [];
        $productRevenues = [];
        foreach ($transactions as $tx) {
            if (!is_array($tx->items)) continue;
            foreach ($tx->items as $item) {
                $pid = $item['id'] ?? $item['product_id'] ?? null;
                $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $price = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                if ($pid && $qty > 0) {
                    $productSales[$pid] = ($productSales[$pid] ?? 0) + $qty;
                    $productRevenues[$pid] = ($productRevenues[$pid] ?? 0) + ($qty * $price);
                }
            }
        }

        // Find products
        $candidateProductIds = array_keys($productSales);
        $products = Product::whereIn('id', $candidateProductIds)
            ->where('is_active', true)
            ->where('is_archived', false)
            ->get();

        $fastThreshold = (int) DSSSettings::getSetting('fast_moving_threshold_units', 50);
        $analyticsFastThreshold = min($fastThreshold, 15);

        $recommendations = collect();
        foreach ($products as $product) {
            $rec = $this->generateInventoryRecommendation($product, $start, $end, $analyticsFastThreshold);
            if ($rec && in_array($rec->recommendation_type, ['reorder_level', 'inventory_reorder'])) {
                $recommendations->push($rec);
            }
        }

        // Also check any active reorder recommendations already in DSS database
        $existingRecs = DSSRecommendation::where('is_active', true)
            ->whereIn('recommendation_type', ['reorder_level', 'inventory_reorder'])
            ->with('product')
            ->get();

        foreach ($existingRecs as $eRec) {
            if ($eRec->product && !$recommendations->contains('id', $eRec->id)) {
                $recommendations->push($eRec);
            }
        }

        // Format recommendations for view/API
        $formattedRecs = $recommendations->map(function (DSSRecommendation $r) use ($productSales) {
            $product = $r->product;
            $meta = $r->metadata ?? [];
            $salesVol = $meta['sales_this_month'] ?? ($productSales[$product?->id] ?? 0);
            $currReorder = (int) ($meta['current_reorder_level'] ?? ($product?->reorder_level ?? 10));
            $suggReorder = (int) ($meta['suggested_reorder_level'] ?? ($currReorder + 10));
            $currStock = (int) ($meta['current_stock'] ?? ($product?->stock_quantity ?? 0));
            $suggStock = (int) ($meta['suggested_stock_quantity'] ?? max($currStock, $suggReorder * 2));
            $risk = $meta['stockout_risk'] ?? ($currStock <= $currReorder ? 'high' : 'medium');
            $daysLeft = $meta['days_of_stock_left'] ?? ($currStock > 0 ? 30 : 0);
            $isActioned = !is_null($r->action_taken_at) || ($product && $product->reorder_level >= $suggReorder);

            return [
                'id' => $r->id,
                'product_id' => $product?->id,
                'product_name' => $product?->product_name ?: ($product?->name ?? 'Product #' . $r->product_id),
                'sku' => $product?->sku ?? 'N/A',
                'sales_volume' => $salesVol,
                'current_reorder_level' => $currReorder,
                'suggested_reorder_level' => $suggReorder,
                'current_stock' => $currStock,
                'suggested_stock_quantity' => $suggStock,
                'stockout_risk' => $risk,
                'days_of_stock_left' => $daysLeft,
                'is_actioned' => $isActioned,
                'action_taken_at' => $r->action_taken_at?->format('M d, Y H:i'),
                'title' => $r->title,
                'description' => $r->description,
                'rationale' => $meta['rationale'] ?? '',
                'apply_url' => route('dss.recommendations.apply-reorder', $r->id),
            ];
        })->sortByDesc('sales_volume')->values()->all();

        $hasFastMoving = count($formattedRecs) > 0;

        // Dynamic High Priority Banner
        if ($hasFastMoving) {
            $pendingCount = collect($formattedRecs)->where('is_actioned', false)->count();
            if ($pendingCount > 0) {
                $highPriorityBanner = "DSS Stock Alert: {$pendingCount} fast-moving product(s) detected with high sales volume. Increasing reorder levels is recommended to prevent stockouts.";
            } else {
                $highPriorityBanner = "DSS Stock Alert: Fast-moving products detected; recommended reorder level adjustments have been applied.";
            }
        } else {
            $highPriorityBanner = "DSS Inventory Health: Sales velocity and inventory reorder points are currently balanced.";
        }

        // Dynamic Opportunity text
        if (!empty($productRevenues)) {
            arsort($productRevenues);
            $topPid = array_key_first($productRevenues);
            $topProduct = $products->firstWhere('id', $topPid);
            $topRevenue = $productRevenues[$topPid];
            $topQty = $productSales[$topPid] ?? 0;
            $opportunity = ($topProduct?->product_name ?: 'Top product') . " is leading sales momentum with {$topQty} units sold (₱" . number_format($topRevenue, 2) . "). Maintaining higher buffer stock maximizes revenue during peak demand periods.";
        } else {
            $opportunity = "Explore promotional bundling for accessories to stimulate sales velocity across seasonal inventory lines.";
        }

        // Dynamic Attention text
        $highestRiskItem = collect($formattedRecs)->first(fn($item) => $item['stockout_risk'] === 'high' || $item['current_stock'] <= $item['current_reorder_level']);
        if ($highestRiskItem) {
            $attention = "Stockout warning: {$highestRiskItem['product_name']} has only {$highestRiskItem['current_stock']} units remaining in stock against high customer demand (~{$highestRiskItem['days_of_stock_left']} days coverage). Immediate replenishment and higher reorder point advised.";
        } elseif ($hasFastMoving) {
            $attention = "Fast-moving items are actively depleting stock. Monitor daily velocity to ensure incoming purchase orders arrive before minimum stock thresholds.";
        } else {
            $attention = "No critical stockout risks detected. Reorder thresholds across all product categories provide adequate coverage.";
        }

        // Dynamic Next Step text
        if ($hasFastMoving) {
            $nextStep = "Adopt DSS suggested reorder levels and adjust supplier purchase order lead times to maintain at least a 30-day stock buffer.";
        } else {
            $nextStep = "Align pricing and promotional calendars ahead of next month’s seasonal demand spikes.";
        }

        return [
            'has_fast_moving' => $hasFastMoving,
            'high_priority_banner' => $highPriorityBanner,
            'recommendations' => $formattedRecs,
            'opportunity' => $opportunity,
            'attention' => $attention,
            'next_step' => $nextStep,
            'period_label' => $isCustom ? 'Analyzed Period' : ($usedFallbackPeriod ? 'Recent 90 Days' : 'This Month'),
        ];
    }
}
