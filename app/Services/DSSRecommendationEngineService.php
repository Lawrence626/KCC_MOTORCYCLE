<?php

namespace App\Services;

use App\Models\DeadStock;
use App\Models\DSSRecommendation;
use App\Models\Product;
use App\Models\FastMovingProduct;
use App\Models\DSSSettings;
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
     * Generate recommendations for all dead stock products.
     */
    public function generateAllRecommendations(): void
    {
        if (!DSSSettings::getSetting('dss_analysis_enabled', true)) {
            return;
        }

        $deadStocks = DeadStock::where('is_active', true)->get();

        foreach ($deadStocks as $deadStock) {
            $this->generateProductRecommendations($deadStock->product);
        }
    }

    /**
     * Generate recommendations for a specific product.
     */
    public function generateProductRecommendations(Product $product): Collection
    {
        // Get existing recommendations for cleanup
        $existingRecommendations = DSSRecommendation::where('product_id', $product->id)
            ->where('is_active', true)
            ->get();

        $recommendations = collect();

        // Generate all applicable recommendations
        if (DSSSettings::getSetting('promotion_recommendation_enabled', true)) {
            $promo = $this->generatePromotionRecommendation($product);
            if ($promo) $recommendations->push($promo);
        }

        if (DSSSettings::getSetting('discount_recommendation_enabled', true)) {
            $discount = $this->generateDiscountRecommendation($product);
            if ($discount) $recommendations->push($discount);
        }

        if (DSSSettings::getSetting('bundle_recommendation_enabled', true)) {
            $bundle = $this->generateBundleRecommendation($product);
            if ($bundle) $recommendations->push($bundle);
        }

        $relocate = $this->generateRelocationRecommendation($product);
        if ($relocate) $recommendations->push($relocate);

        $featured = $this->generateFeaturedDisplayRecommendation($product);
        if ($featured) $recommendations->push($featured);

        $social = $this->generateSocialMediaRecommendation($product);
        if ($social) $recommendations->push($social);

        $supplier = $this->generateSupplierReturnRecommendation($product);
        if ($supplier) $recommendations->push($supplier);

        return $recommendations;
    }

    /**
     * Generate promotion recommendation.
     */
    private function generatePromotionRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
    private function generateDiscountRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
    private function generateBundleRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
    private function generateRelocationRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
    private function generateFeaturedDisplayRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
    private function generateSocialMediaRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
    private function generateSupplierReturnRecommendation(Product $product): ?DSSRecommendation
    {
        $deadStock = DeadStock::where('product_id', $product->id)
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
}
