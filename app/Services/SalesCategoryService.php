<?php

namespace App\Services;

use App\Models\POSTransaction;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SalesCategoryService
{
    /**
     * Get category sales breakdown for a specific date range.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return Collection
     */
    public function getCategoryBreakdown(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ?? now()->startOfDay();
        $endDate = $endDate ?? now()->endOfDay();

        // Get transactions within date range
        $transactions = POSTransaction::query()
            ->completed()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();

        return $this->calculateCategoryBreakdown($transactions);
    }

    /**
     * Get today's category sales breakdown.
     *
     * @return Collection
     */
    public function getTodaysCategoryBreakdown(): Collection
    {
        return $this->getCategoryBreakdown(
            now()->startOfDay(),
            now()->endOfDay()
        );
    }

    /**
     * Calculate category breakdown from transactions.
     *
     * @param Collection $transactions
     * @return Collection
     */
    protected function calculateCategoryBreakdown(Collection $transactions): Collection
    {
        // Get all product IDs from transactions
        $productIds = $transactions->flatMap(function ($transaction) {
            return collect($transaction->items ?? [])->pluck('id');
        })->filter()->unique()->all();

        // Get product details for lookup
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get(['id', 'category', 'product_name', 'name', 'unit_price'])
            ->keyBy('id');

        // Process items and group by category
        return $transactions->flatMap(function ($transaction) use ($products) {
            return collect($transaction->items ?? [])->map(function ($item) use ($products) {
                $productId = $item['id'] ?? null;
                $product = $productId ? $products->get($productId) : null;

                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                if ($unitPrice <= 0 && $product) {
                    $unitPrice = (float) ($product->unit_price ?? 0);
                }

                $categoryValue = $item['category'] ?? null;
                // Fallback to product's category/product_name/name if not found or is "Uncategorized"
                if ((!$categoryValue || strcasecmp(trim($categoryValue), 'uncategorized') === 0) && $product) {
                    $categoryValue = $product->category;
                    if (!$categoryValue || strcasecmp(trim($categoryValue), 'uncategorized') === 0) {
                        $categoryValue = $product->product_name ?: ($product->name ?? 'Uncategorized');
                    }
                }

                $category = $this->normalizeCategory($categoryValue);

                return [
                    'category' => $category,
                    'amount' => $quantity * $unitPrice,
                    'quantity' => $quantity,
                ];
            });
        })->groupBy('category')->map(function ($items) {
            $amount = $items->sum('amount');
            return [
                'amount' => $amount,
                'quantity' => $items->sum('quantity'),
                'value' => $amount,
                'percentage' => 0, // Will be calculated after all items are summed
            ];
        })->sortByDesc('amount');
    }

    /**
     * Get category sales with percentages.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return Collection
     */
    public function getCategoryBreakdownWithPercentages(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $breakdown = $this->getCategoryBreakdown($startDate, $endDate);
        $total = $breakdown->sum('amount');

        if ($total <= 0) {
            return $breakdown;
        }

        return $breakdown->map(function ($item) use ($total) {
            $item['percentage'] = round(($item['amount'] / $total) * 100, 1);
            return $item;
        });
    }

    /**
     * Get top categories by sales.
     *
     * @param int $limit
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return Collection
     */
    public function getTopCategories(int $limit = 5, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        return $this->getCategoryBreakdownWithPercentages($startDate, $endDate)
            ->take($limit)
            ->values();
    }

    /**
     * Get total sales by category for a specific category.
     *
     * @param string $category
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return float
     */
    public function getCategorySalesTotal(string $category, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $breakdown = $this->getCategoryBreakdown($startDate, $endDate);
        $normalized = $this->normalizeCategory($category);

        return $breakdown->get($normalized, ['amount' => 0])['amount'] ?? 0;
    }

    /**
     * Get category sales count (number of items sold).
     *
     * @param string $category
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return int
     */
    public function getCategorySalesCount(string $category, ?Carbon $startDate = null, ?Carbon $endDate = null): int
    {
        $breakdown = $this->getCategoryBreakdown($startDate, $endDate);
        $normalized = $this->normalizeCategory($category);

        return (int) ($breakdown->get($normalized, ['quantity' => 0])['quantity'] ?? 0);
    }

    /**
     * Get all standard motorcycle categories.
     *
     * @return array
     */
    public static function getStandardCategories(): array
    {
        return [
            'Exhaust',
            'Helmets',
            'Tires',
            'Brakes',
            'Oils',
            'Batteries',
            'Accessories',
        ];
    }

    /**
     * Get category color palette.
     *
     * @return array
     */
    public function getCategoryColors(): array
    {
        return [
            'Exhaust' => '#06b6d4',      // cyan-500
            'Helmets' => '#a3e635',      // lime-400
            'Tires' => '#fbbf24',        // amber-400
            'Brakes' => '#ef4444',       // red-500
            'Oils' => '#fb923c',         // orange-400
            'Batteries' => '#3b82f6',    // blue-500
            'Accessories' => '#10b981',  // emerald-500
        ];
    }

    /**
     * Normalize category name.
     *
     * @param string|null $value
     * @return string
     */
    public static function normalizeCategory(?string $value): string
    {
        return self::mapToPredefinedCategory($value);
    }

    /**
     * Map actual product categories to predefined 7 categories:
     * Exhaust, Helmets, Tires, Brakes, Oils, Batteries, Accessories
     */
    public static function mapToPredefinedCategory(?string $category): string
    {
        if (empty($category)) {
            return 'Accessories';
        }

        $categoryLower = strtolower(trim($category));

        // If it's already one of the 7 predefined categories (case-insensitive)
        $standard = [
            'exhaust' => 'Exhaust',
            'helmets' => 'Helmets',
            'helmet' => 'Helmets',
            'tires' => 'Tires',
            'tire' => 'Tires',
            'brakes' => 'Brakes',
            'brake' => 'Brakes',
            'oils' => 'Oils',
            'oil' => 'Oils',
            'batteries' => 'Batteries',
            'battery' => 'Batteries',
            'accessories' => 'Accessories',
            'accessory' => 'Accessories',
        ];

        if (isset($standard[$categoryLower])) {
            return $standard[$categoryLower];
        }

        // Mapping rules for actual categories and product names
        $categoryMap = [
            // Exhaust group
            'pipe' => 'Exhaust',
            'exhaust' => 'Exhaust',
            'muffler' => 'Exhaust',
            'silencer' => 'Exhaust',

            // Helmets group
            'helmet' => 'Helmets',
            'helmets' => 'Helmets',

            // Tires/Wheels group
            'tire' => 'Tires',
            'tires' => 'Tires',
            'wheel' => 'Tires',
            'wheels' => 'Tires',
            'rim' => 'Tires',
            'rims' => 'Tires',
            'mags' => 'Tires',

            // Brakes group
            'brake' => 'Brakes',
            'brakes' => 'Brakes',
            'brake master' => 'Brakes',
            'brake master pair' => 'Brakes',
            'brake shoe' => 'Brakes',
            'caliper' => 'Brakes',
            'disc' => 'Brakes',

            // Oils group
            'oil' => 'Oils',
            'oils' => 'Oils',
            'engine_oil' => 'Oils',
            'engine oil' => 'Oils',
            'lubricant' => 'Oils',
            'lubricants' => 'Oils',

            // Batteries group
            'battery' => 'Batteries',
            'batteries' => 'Batteries',

            // Accessories group
            'shock' => 'Accessories',
            'frontshock' => 'Accessories',
            'monoshock' => 'Accessories',
            'spring' => 'Accessories',
            'suspension' => 'Accessories',
            'seat' => 'Accessories',
            'flatseat' => 'Accessories',
            'indo seat' => 'Accessories',
            'mirror' => 'Accessories',
            'side mirror' => 'Accessories',
            'lever' => 'Accessories',
            'clutch' => 'Accessories',
            'clutch perch' => 'Accessories',
            'perch' => 'Accessories',
            'stand' => 'Accessories',
            'center/side stand' => 'Accessories',
            'center stand' => 'Accessories',
            'side stand' => 'Accessories',
            'support' => 'Accessories',
            'engine support' => 'Accessories',
            'cover' => 'Accessories',
            'rad cover' => 'Accessories',
            'radiator' => 'Accessories',
            'frame' => 'Accessories',
            'monorack frame' => 'Accessories',
            'cvt' => 'Accessories',
            'cvt half' => 'Accessories',
            'hugger' => 'Accessories',
            'tire hugger' => 'Accessories',
            'spark plug' => 'Accessories',
            'spark_plug' => 'Accessories',
            'filter' => 'Accessories',
            'filters' => 'Accessories',
            'throttle' => 'Accessories',
            'quick throttle' => 'Accessories',
            'swing arm' => 'Accessories',
            'product desc' => 'Accessories',
        ];

        // Check if exact match exists
        if (isset($categoryMap[$categoryLower])) {
            return $categoryMap[$categoryLower];
        }

        // Check if category contains any of the mapping keys
        foreach ($categoryMap as $key => $predefined) {
            if (strpos($categoryLower, $key) !== false) {
                return $predefined;
            }
        }

        // Default to Accessories
        return 'Accessories';
    }
}
