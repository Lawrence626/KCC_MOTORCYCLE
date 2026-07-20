<?php

namespace App\Services;

use App\Models\FastMovingProduct;
use App\Models\SlowMovingProduct;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\DSSSettings;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesVelocityAnalysisService
{
    /**
     * Analyze all products for sales velocity (fast vs slow moving).
     */
    public function analyzeAllProducts(): void
    {
        $products = Product::where('is_active', true)
            ->where('is_archived', false)
            ->get();

        foreach ($products as $product) {
            $this->analyzeProductVelocity($product);
        }
    }

    /**
     * Analyze a single product's sales velocity.
     */
    public function analyzeProductVelocity(Product $product): void
    {
        $salesData = $this->getSalesData($product->id);

        // Calculate velocity score (units per day average)
        $velocityScore = $this->calculateVelocityScore($salesData);

        if ($velocityScore > 0) {
            // Classify as fast moving if meets threshold
            if ($this->isFastMoving($salesData)) {
                $this->updateFastMovingProduct($product->id, $salesData, $velocityScore);
            } else {
                // Remove from fast moving if previously listed
                FastMovingProduct::where('product_id', $product->id)->delete();
            }
        }

        // Update slow moving classification
        if ($this->isSlowMoving($product, $salesData)) {
            $this->updateSlowMovingProduct($product->id, $salesData, $velocityScore);
        } else {
            SlowMovingProduct::where('product_id', $product->id)->delete();
        }
    }

    /**
     * Get sales data for a product over different periods.
     */
    private function getSalesData(int $productId): array
    {
        $completedTransactions = POSTransaction::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        $now = Carbon::now();
        $units7 = 0;
        $units30 = 0;
        $units60 = 0;
        $units90 = 0;
        $lastSaleDate = null;

        foreach ($completedTransactions as $transaction) {
            $items = $transaction->items;

            if (is_array($items)) {
                foreach ($items as $item) {
                    if (isset($item['product_id']) && $item['product_id'] == $productId) {
                        $quantity = $item['quantity'] ?? 1;
                        $transactionDate = Carbon::parse($transaction->completed_at);

                        if (!$lastSaleDate) {
                            $lastSaleDate = $transactionDate;
                        }

                        $daysAgo = $now->diffInDays($transactionDate);

                        if ($daysAgo <= 7) {
                            $units7 += $quantity;
                        }
                        if ($daysAgo <= 30) {
                            $units30 += $quantity;
                        }
                        if ($daysAgo <= 60) {
                            $units60 += $quantity;
                        }
                        if ($daysAgo <= 90) {
                            $units90 += $quantity;
                        }
                    }
                }
            }
        }

        return [
            'units_7' => $units7,
            'units_30' => $units30,
            'units_60' => $units60,
            'units_90' => $units90,
            'last_sale_date' => $lastSaleDate,
        ];
    }

    /**
     * Calculate velocity score based on sales data.
     */
    private function calculateVelocityScore(array $salesData): float
    {
        // Weighted average: prioritize recent sales
        $score = ($salesData['units_7'] * 4 + $salesData['units_30'] * 2 + $salesData['units_60'] + $salesData['units_90'] * 0.5) / 90;
        return round($score, 2);
    }

    /**
     * Determine if product is fast moving.
     */
    private function isFastMoving(array $salesData): bool
    {
        $fastMovingThreshold = DSSSettings::getSetting('fast_moving_threshold_units', 50);
        return $salesData['units_30'] >= $fastMovingThreshold;
    }

    /**
     * Determine if product is slow moving.
     */
    private function isSlowMoving(Product $product, array $salesData): bool
    {
        $slowMovingThreshold = DSSSettings::getSetting('slow_moving_threshold_days', 60);
        
        if (!$salesData['last_sale_date']) {
            return true; // Never sold = slow moving
        }

        $daysWithoutSale = Carbon::now()->diffInDays($salesData['last_sale_date']);
        return $daysWithoutSale >= $slowMovingThreshold;
    }

    /**
     * Update fast moving product record.
     */
    private function updateFastMovingProduct(int $productId, array $salesData, float $velocityScore): void
    {
        $product = Product::find($productId);

        FastMovingProduct::updateOrCreate(
            ['product_id' => $productId],
            [
                'units_sold_7_days' => $salesData['units_7'],
                'units_sold_30_days' => $salesData['units_30'],
                'units_sold_60_days' => $salesData['units_60'],
                'units_sold_90_days' => $salesData['units_90'],
                'velocity_score' => $velocityScore,
                'turnover_rate' => $this->calculateTurnoverRate($product, $salesData),
                'current_stock' => $product->stock_quantity,
            ]
        );
    }

    /**
     * Update slow moving product record.
     */
    private function updateSlowMovingProduct(int $productId, array $salesData, float $velocityScore): void
    {
        $product = Product::find($productId);

        $daysWithoutSale = $salesData['last_sale_date'] 
            ? Carbon::now()->diffInDays($salesData['last_sale_date']) 
            : 999; // Default to 999 days if never sold

        SlowMovingProduct::updateOrCreate(
            ['product_id' => $productId],
            [
                'days_without_sale' => $daysWithoutSale,
                'last_sold_date' => $salesData['last_sale_date'],
                'units_sold_30_days' => $salesData['units_30'],
                'units_sold_60_days' => $salesData['units_60'],
                'units_sold_90_days' => $salesData['units_90'],
                'current_stock' => $product->stock_quantity,
                'stock_value' => $product->stock_quantity * $product->unit_price,
                'velocity_score' => $velocityScore,
            ]
        );
    }

    /**
     * Calculate inventory turnover rate.
     */
    private function calculateTurnoverRate(Product $product, array $salesData): float
    {
        if (!$product->stock_quantity || $salesData['units_90'] == 0) {
            return 0;
        }

        // Simplified turnover: units sold in 90 days / current stock
        return round($salesData['units_90'] / $product->stock_quantity, 2);
    }

    /**
     * Get top N fast moving products.
     */
    public function getTopFastMovingProducts(int $limit = 10): Collection
    {
        return FastMovingProduct::orderBy('velocity_score', 'desc')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    /**
     * Get top N slow moving products.
     */
    public function getTopSlowMovingProducts(int $limit = 10): Collection
    {
        return SlowMovingProduct::orderBy('velocity_score', 'asc')
            ->limit($limit)
            ->with('product')
            ->get();
    }
}
