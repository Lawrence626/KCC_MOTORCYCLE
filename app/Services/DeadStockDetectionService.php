<?php

namespace App\Services;

use App\Models\DeadStock;
use App\Models\Product;
use App\Models\POSTransaction;
use App\Models\DSSSettings;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DeadStockDetectionService
{
    /**
     * Analyze all products and detect dead stocks.
     */
    public function analyzeAllProducts(): Collection
    {
        $thresholdDays = DSSSettings::getSetting('dead_stock_threshold_days', 30);
        
        // Get all active products with stock greater than 0
        $products = Product::where('is_active', true)
            ->where('is_archived', false)
            ->where('stock_quantity', '>', 0)
            ->get();

        $deadStocks = collect();

        foreach ($products as $product) {
            $deadStock = $this->checkProductForDeadStock($product, $thresholdDays);
            
            if ($deadStock) {
                $deadStocks->push($deadStock);
            }
        }

        // Deactivate dead stock records for products that no longer qualify
        $activeDeadStockProductIds = $deadStocks->pluck('product_id')->toArray();
        DeadStock::where('is_active', true)
            ->whereNotIn('product_id', $activeDeadStockProductIds)
            ->update(['is_active' => false]);

        return $deadStocks;
    }

    /**
     * Check a single product for dead stock status.
     */
    public function checkProductForDeadStock(Product $product, ?int $thresholdDays = null): ?DeadStock
    {
        if (!$thresholdDays) {
            $thresholdDays = DSSSettings::getSetting('dead_stock_threshold_days', 30);
        }

        // Skip if no stock
        if ($product->stock_quantity <= 0) {
            return null;
        }

        // Skip if archived or inactive
        if ($product->is_archived || !$product->is_active) {
            return null;
        }

        // Business rule: Do not classify products that have never been received into inventory
        // A product is considered "received" if it has a last_restock_date or has been sold before
        $lastSale = $this->getLastSaleDate($product->id);
        $hasBeenReceived = $product->last_restock_date || $lastSale;

        if (!$hasBeenReceived) {
            return null;
        }

        if (!$lastSale) {
            // Product has been restocked but never sold
            $referenceDate = $product->last_restock_date ?? $product->created_at;
            $daysWithoutSale = $referenceDate ? (int) abs(Carbon::now()->diffInDays($referenceDate)) : $thresholdDays + 1;
        } else {
            $daysWithoutSale = (int) abs(Carbon::now()->diffInDays($lastSale));
        }

        // Check if meets dead stock criteria
        if ($daysWithoutSale < $thresholdDays) {
            return null;
        }

        // Determine priority level
        $priorityLevel = $this->determinePriorityLevel($daysWithoutSale);

        // Calculate stock value
        $stockValue = $product->stock_quantity * $product->unit_price;

        // Determine the primary suggested action
        $suggestedAction = $this->getSuggestedAction($daysWithoutSale, $product->stock_quantity, $stockValue);

        // Create or update dead stock record
        $deadStock = DeadStock::updateOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => null],
            [
                'days_without_sale' => $daysWithoutSale,
                'last_sold_date' => $lastSale,
                'current_stock' => $product->stock_quantity,
                'stock_value' => $stockValue,
                'priority_level' => $priorityLevel,
                'is_active' => true,
                'detected_at' => now(),
                'last_analyzed_at' => now(),
                'analysis_notes' => $suggestedAction,
            ]
        );

        return $deadStock;
    }

    /**
     * Get the last sale date for a product from POS transactions.
     */
    public function getLastSaleDate(int $productId): ?Carbon
    {
        $completedTransactions = POSTransaction::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        foreach ($completedTransactions as $transaction) {
            $items = $transaction->items; // JSON array

            if (is_array($items)) {
                foreach ($items as $item) {
                    $itemProductId = $item['product_id'] ?? $item['id'] ?? null;
                    if ($itemProductId && $itemProductId == $productId) {
                        return Carbon::parse($transaction->completed_at);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Determine priority level based on days without sale.
     */
    public function determinePriorityLevel(int $daysWithoutSale): string
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
     * Get the primary suggested action based on business rules.
     */
    public function getSuggestedAction(int $daysWithoutSale, int $stockQty, float $stockValue): string
    {
        if ($daysWithoutSale >= 91) {
            return '20% Discount';
        }

        if ($daysWithoutSale >= 61) {
            return '10%–15% Discount';
        }

        if ($daysWithoutSale >= 31) {
            return '5% Discount';
        }

        return 'Monitor';
    }

    /**
     * Get count of dead stocks by priority level.
     */
    public function getCountByPriority(): array
    {
        return [
            'Critical' => DeadStock::where('is_active', true)->where('priority_level', 'Critical')->count(),
            'High' => DeadStock::where('is_active', true)->where('priority_level', 'High')->count(),
            'Medium' => DeadStock::where('is_active', true)->where('priority_level', 'Medium')->count(),
            'Low' => DeadStock::where('is_active', true)->where('priority_level', 'Low')->count(),
        ];
    }

    /**
     * Get total dead stock count.
     */
    public function getTotalCount(): int
    {
        return DeadStock::where('is_active', true)->count();
    }

    /**
     * Get total dead stock value.
     */
    public function getTotalValue(): float
    {
        return (float) DeadStock::where('is_active', true)->sum('stock_value');
    }

    /**
     * Clear a dead stock record (mark as resolved).
     */
    public function clearDeadStock(int $productId): bool
    {
        return DeadStock::where('product_id', $productId)->update(['is_active' => false]) > 0;
    }

    /**
     * Get products at risk of becoming dead stock (warning threshold).
     */
    public function getAtRiskProducts(int $warningDays = 60): Collection
    {
        $products = Product::where('is_active', true)
            ->where('is_archived', false)
            ->where('stock_quantity', '>', 0)
            ->get();

        $atRisk = collect();

        foreach ($products as $product) {
            $lastSale = $this->getLastSaleDate($product->id);

            if (!$lastSale) {
                $daysWithoutSale = $product->created_at ? (int) abs(Carbon::now()->diffInDays($product->created_at)) : 0;
            } else {
                $daysWithoutSale = (int) abs(Carbon::now()->diffInDays($lastSale));
            }

            // Include products approaching warning threshold
            if ($daysWithoutSale >= $warningDays && $daysWithoutSale < DSSSettings::getSetting('dead_stock_threshold_days', 30)) {
                $atRisk->push([
                    'product' => $product,
                    'days_without_sale' => $daysWithoutSale,
                    'last_sold_date' => $lastSale,
                ]);
            }
        }

        return $atRisk;
    }

    /**
     * Get enriched dead stock records with full product details for the index page.
     */
    public function getDeadStocksWithDetails(?string $search = null, ?string $priority = null, ?string $sortBy = 'days_without_sale', string $sortOrder = 'desc', int $perPage = 25)
    {
        $query = DeadStock::where('is_active', true)
            ->with(['product', 'warehouse', 'recommendations']);

        // Search
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('compatibility', 'like', "%{$search}%");
            });
        }

        // Filter by priority
        if ($priority && in_array($priority, ['Critical', 'High', 'Medium', 'Low'])) {
            $query->where('priority_level', $priority);
        }

        // Sort
        $validSortFields = ['days_without_sale', 'stock_value', 'current_stock', 'priority_level', 'last_sold_date', 'detected_at'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'days_without_sale';
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get sales history for a specific product.
     */
    public function getProductSalesHistory(int $productId, int $months = 12): array
    {
        $transactions = POSTransaction::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', Carbon::now()->subMonths($months))
            ->orderBy('completed_at', 'asc')
            ->get();

        $salesHistory = [];
        $monthlySales = [];

        foreach ($transactions as $transaction) {
            $items = $transaction->items;

            if (is_array($items)) {
                foreach ($items as $item) {
                    $itemProductId = $item['product_id'] ?? $item['id'] ?? null;
                    if ($itemProductId && $itemProductId == $productId) {
                        $date = Carbon::parse($transaction->completed_at);
                        $monthKey = $date->format('Y-m');
                        $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                        $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);

                        if (!isset($monthlySales[$monthKey])) {
                            $monthlySales[$monthKey] = ['units' => 0, 'revenue' => 0];
                        }

                        $monthlySales[$monthKey]['units'] += $quantity;
                        $monthlySales[$monthKey]['revenue'] += $quantity * $unitPrice;

                        $salesHistory[] = [
                            'date' => $date->format('M d, Y'),
                            'invoice' => $transaction->invoice_number,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total' => $quantity * $unitPrice,
                        ];
                    }
                }
            }
        }

        // Build monthly trend for chart
        $trendLabels = [];
        $trendData = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $key = $month->format('Y-m');
            $trendLabels[] = $month->format('M Y');
            $trendData[] = $monthlySales[$key]['units'] ?? 0;
        }

        return [
            'transactions' => array_reverse($salesHistory),
            'trend' => [
                'labels' => $trendLabels,
                'data' => $trendData,
            ],
            'total_units_sold' => array_sum(array_column($monthlySales, 'units')),
            'total_revenue' => array_sum(array_map(fn($m) => $m['revenue'], $monthlySales ?: [['revenue' => 0]])),
        ];
    }

    /**
     * Get the current threshold setting.
     */
    public function getThresholdDays(): int
    {
        return (int) DSSSettings::getSetting('dead_stock_threshold_days', 30);
    }
}
