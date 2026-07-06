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

        // Get product categories for lookup
        $productCategories = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_archived', false)
            ->pluck('category', 'id')
            ->all();

        // Process items and group by category
        return $transactions->flatMap(function ($transaction) use ($productCategories) {
            return collect($transaction->items ?? [])->map(function ($item) use ($productCategories) {
                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                $productId = $item['id'] ?? null;
                $categoryValue = $item['category'] ?? null;

                // Fallback to product's category if not found or is "Uncategorized"
                if ((!$categoryValue || strcasecmp(trim($categoryValue), 'uncategorized') === 0) && $productId) {
                    $categoryValue = $productCategories[$productId] ?? null;
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
    public function getStandardCategories(): array
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
            'Uncategorized' => '#6b7280',// gray-500
        ];
    }

    /**
     * Format category breakdown for frontend (chart display).
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function formatForChart(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $breakdown = $this->getCategoryBreakdownWithPercentages($startDate, $endDate);
        $colors = $this->getCategoryColors();
        $total = $breakdown->sum('amount');

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $legend = [];

        foreach ($breakdown as $category => $item) {
            $labels[] = $category;
            $data[] = $item['amount'];
            $backgroundColors[] = $colors[$category] ?? '#9ca3af';

            $legend[] = [
                'label' => $category,
                'value' => $item['amount'],
                'quantity' => $item['quantity'],
                'percentage' => $item['percentage'],
                'color' => $colors[$category] ?? '#9ca3af',
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors,
            'legend' => $legend,
            'total' => $total,
            'totalQuantity' => $breakdown->sum('quantity'),
            'chartConfig' => [
                'type' => 'doughnut',
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => true,
                    'plugins' => [
                        'legend' => [
                            'display' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Normalize category name.
     *
     * @param string|null $value
     * @return string
     */
    protected function normalizeCategory(?string $value): string
    {
        if (empty($value)) {
            return 'Uncategorized';
        }

        $normalized = trim($value);
        $normalized = !empty($normalized) ? $normalized : 'Uncategorized';
        
        // Map actual categories to predefined ones
        return $this->mapToPredefinedCategory($normalized);
    }

    /**
     * Map actual product categories to predefined 7 categories
     */
    protected function mapToPredefinedCategory(string $category): string
    {
        $categoryLower = strtolower($category);

        // Mapping rules for actual categories to predefined ones
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
            'rim' => 'Tires',
            'mags' => 'Tires',

            // Brakes group
            'brake' => 'Brakes',
            'brakes' => 'Brakes',
            'brake master' => 'Brakes',
            'brake shoe' => 'Brakes',
            'caliper' => 'Brakes',
            'disc' => 'Brakes',

            // Oils group
            'oil' => 'Oils',
            'oils' => 'Oils',
            'engine_oil' => 'Oils',
            'engine oil' => 'Oils',

            // Batteries group
            'battery' => 'Batteries',
            'batteries' => 'Batteries',

            // Accessories group (default for most things)
            'shock' => 'Accessories',
            'spring' => 'Accessories',
            'suspension' => 'Accessories',
            'seat' => 'Accessories',
            'mirror' => 'Accessories',
            'lever' => 'Accessories',
            'clutch' => 'Accessories',
            'perch' => 'Accessories',
            'stand' => 'Accessories',
            'support' => 'Accessories',
            'cover' => 'Accessories',
            'frame' => 'Accessories',
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

        // Default to Accessories for unknown categories
        return 'Accessories';
    }
}
