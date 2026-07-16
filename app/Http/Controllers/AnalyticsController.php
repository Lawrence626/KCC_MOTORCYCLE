<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\POSTransaction;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SupplierPriceHistory;
use App\Services\VatCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AnalyticsController extends Controller
{
    private VatCalculationService $vatService;

    public function __construct(VatCalculationService $vatService)
    {
        $this->vatService = $vatService;
    }

    private function activeProducts()
    {
        return Product::where('is_archived', false)
            ->where('is_active', true);
    }

    private function lowStockProducts()
    {
        return Product::where('is_archived', false)
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('stock_quantity', '>', 0)
            ->whereNotNull('reorder_level');
    }

    public function sales()
    {
        $products = $this->activeProducts();

        // Total inventory value (VAT-inclusive prices)
        $totalInventoryValue = (clone $products)->sum(DB::raw('stock_quantity * unit_price'));
        
        // Calculate included VAT from total inventory value
        $includedVat = $this->vatService->calculateIncludedVat($totalInventoryValue);
        
        // VATable sales (net of VAT)
        $vatableSales = $totalInventoryValue - $includedVat;

        $averageUnitPrice = $products->avg('unit_price') ?: 0;
        $totalUnitsInStock = (clone $products)->sum('stock_quantity');
        $healthySkus = (clone $products)
            ->where('stock_quantity', '>', DB::raw('reorder_level'))
            ->where('stock_quantity', '>', 0)
            ->count();
        $lowStockSkus = (clone $products)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('stock_quantity', '>', 0)
            ->count();
        $outOfStockSkus = (clone $products)
            ->where('stock_quantity', '<=', 0)
            ->count();

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $topProducts = $this->getTopSellingProductsFromTransactions(5, $startOfMonth, $endOfMonth);
        $categoryBreakdown = $this->getCategoryBreakdownFromTransactions($startOfMonth, $endOfMonth);

        $brandMomentum = $products
            ->select('brand', DB::raw('SUM(stock_quantity * unit_price) as value'))
            ->groupBy('brand')
            ->orderByDesc('value')
            ->limit(4)
            ->get()
            ->map(function ($row) use ($totalInventoryValue) {
                return [
                    'brand' => $row->brand ?: 'Unknown Brand',
                    'value' => $row->value,
                    'included_vat' => $this->vatService->calculateIncludedVat($row->value),
                    'vatable_sales' => $row->value - $this->vatService->calculateIncludedVat($row->value),
                    'share' => $totalInventoryValue > 0 ? round($row->value / $totalInventoryValue * 100, 0) : 0,
                ];
            });

        $topCategoryBreakdown = $categoryBreakdown->take(6);
        $otherCategories = $categoryBreakdown->slice(6);

        if ($otherCategories->isNotEmpty()) {
            $otherValue = $otherCategories->sum('value');
            $topCategoryBreakdown->push([
                'label' => 'Other',
                'value' => $otherValue,
                'share' => $totalInventoryValue > 0 ? round($otherValue / $totalInventoryValue * 100, 1) : 0,
                'formatted' => '₱' . number_format($otherValue, 2),
            ]);
        }



        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $salesByDay = PurchaseOrder::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(completed_at) as day, SUM(total_amount) as value')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->day => (float) $row->value];
            });

        $dailyLabels = [];
        $dailyValues = [];
        $daysInMonth = now()->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = now()->startOfMonth()->addDays($day - 1);
            $dateKey = $date->format('Y-m-d');
            $dailyLabels[] = $date->format('M j');
            $dailyValues[] = round($salesByDay->get($dateKey, 0), 2);
        }

        $weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        $weeklyValues = [0, 0, 0, 0, 0];

        foreach ($dailyValues as $index => $value) {
            $weekIndex = min(4, intdiv($index, 7));
            $weeklyValues[$weekIndex] += $value;
        }

        $weeklyValues = array_map(fn($value) => round($value, 2), $weeklyValues);

        $monthlyLabels = [];
        $monthlyValues = [];
        $monthStart = now()->subMonths(5)->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M');
        }

        $salesByMonth = PurchaseOrder::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$monthStart, $endOfMonth])
            ->selectRaw('DATE_FORMAT(completed_at, "%Y-%m") as month, SUM(total_amount) as value')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->month => (float) $row->value];
            });

        foreach ($monthlyLabels as $label) {
            $monthKey = now()->createFromFormat('M', $label)->format('Y-m');
            $monthlyValues[] = round($salesByMonth->get($monthKey, 0), 2);
        }

        $formattedCategoryBreakdown = $categoryBreakdown->map(function ($item) {
            return '₱' . number_format((float) ($item['value'] ?? 0), 2);
        })->values()->toArray();

        return view('data_analytics.sales-analytics', [
            'quickStats' => [
                'total_inventory_value' => $totalInventoryValue,
                'included_vat' => $includedVat,
                'vatable_sales' => $vatableSales,
                'average_unit_price' => $averageUnitPrice,
                'total_units_in_stock' => $totalUnitsInStock,
                'healthy_skus' => $healthySkus,
                'low_stock_skus' => $lowStockSkus,
                'out_of_stock_skus' => $outOfStockSkus,
            ],
            'salesTrend' => $this->buildSalesTrendData(),
            'categoryBreakdown' => [
                'labels' => $categoryBreakdown->pluck('label')->toArray(),
                'values' => $categoryBreakdown->pluck('value')->toArray(),
                'formatted' => $categoryBreakdown->map(fn($item) => '₱' . number_format($item['value'], 2))->values()->toArray(),
                'shares' => $categoryBreakdown->pluck('share')->toArray(),
            ],
            'topProducts' => $topProducts,
        ]);
    }

    protected function buildSalesTrendData()
    {
        $now = now();

        $monthlyLabels = [];
        $monthlyValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonthsNoOverflow($i);
            $monthlyLabels[] = $month->format('M');
            $monthlyValues[] = (float) POSTransaction::query()
                ->completed()
                ->whereBetween('completed_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('total_amount');
        }

        $dailyLabels = [];
        $dailyValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dailyLabels[] = $day->format('M j');
            $dailyValues[] = (float) POSTransaction::query()
                ->completed()
                ->whereDate('completed_at', $day)
                ->sum('total_amount');
        }

        $weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        $weeklyValues = [];
        $weekStart = $now->copy()->startOfDay()->subDays(34);

        for ($week = 0; $week < 5; $week++) {
            $start = $weekStart->copy()->addDays($week * 7);
            $end = $start->copy()->endOfDay()->addDays(6);
            $weeklyValues[] = (float) POSTransaction::query()
                ->completed()
                ->whereBetween('completed_at', [$start, $end])
                ->sum('total_amount');
        }

        return [
            'monthly' => ['labels' => $monthlyLabels, 'values' => $monthlyValues],
            'weekly' => ['labels' => $weeklyLabels, 'values' => $weeklyValues],
            'daily' => ['labels' => $dailyLabels, 'values' => $dailyValues],
        ];
    }

    public function pricing()
    {
        $products = $this->activeProducts();

        $averageUnitPrice = $products->avg('unit_price') ?: 0;
        $mostExpensive = $products->orderByDesc('unit_price')->first();
        $cheapest = $products->orderBy('unit_price')->first();

        $priceUpdates = InventoryMovement::where('type', 'price_update')
            ->where(function ($query) {
                $query->whereRaw("JSON_EXTRACT(metadata, '$.old_price') IS NOT NULL")
                    ->whereRaw("JSON_EXTRACT(metadata, '$.old_price') != unit_price");
            })
            ->latest()
            ->with('product')
            ->paginate(5, ['*'], 'price_page');

        $pricingByCategory = $products
            ->select('category', DB::raw('AVG(unit_price) as avg_price'), DB::raw('SUM(stock_quantity) as total_qty'))
            ->groupBy('category')
            ->orderByDesc('total_qty')
            ->get();

        // ── Supplier Cost Analysis ──────────────────────────────────────────
        // Strategy: fetch ALL eligible products, build the full analysis
        // collection, apply both filters (search + cost_change) across the
        // entire dataset, then manually paginate the filtered results.
        // This guarantees filters always operate on the whole dataset, not
        // just the current page.

        $search          = request('search');
        $costChangeFilter = request('cost_change'); // '', 'none', 'up', 'down'

        // 1. Get all product IDs that have at least one received PO item
        $productIdsWithPOs = PurchaseOrderItem::where('received_quantity', '>', 0)
            ->whereHas('purchaseOrder')
            ->select('product_id')
            ->distinct()
            ->pluck('product_id');

        // 2. Fetch ALL matching products (no pagination yet), applying name/SKU search at DB level
        $allProducts = Product::whereIn('id', $productIdsWithPOs)
            ->when($search, fn($q) => $q->where(fn($query) => $query->where('product_name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")))
            ->get();

        // 3. Fetch ALL received PO items for these products in one query
        $allProductIds = $allProducts->pluck('id');
        $poItems = PurchaseOrderItem::whereIn('product_id', $allProductIds)
            ->where('received_quantity', '>', 0)
            ->whereHas('purchaseOrder')
            ->with('purchaseOrder')
            ->get()
            ->groupBy('product_id');

        // 4. Build the full analysis collection for every fetched product
        $analysisCollection = $allProducts->map(function ($product) use ($poItems) {
            $items = collect($poItems->get($product->id, collect()))
                ->sortByDesc(fn($item) => $item->purchaseOrder?->id)
                ->values();

            $currentCost  = $items->count() > 0  ? (float) $items[0]->unit_price : 0;
            $previousCost = $items->count() >= 2 ? (float) $items[1]->unit_price : null;

            $changePercentage = $previousCost && $previousCost > 0
                ? round((($currentCost - $previousCost) / $previousCost) * 100, 2)
                : 0;

            $recommendation = match (true) {
                $previousCost !== null && $currentCost > $previousCost => 'Increase the retail price to maintain a 30% profit margin.',
                $previousCost !== null && $currentCost < $previousCost => 'Maintain the current retail price to increase profit margin.',
                default => 'Maintain current retail price.',
            };

            // Calculate the latest receipt date based on completed purchase orders
            $latestReceiptDate = null;
            if ($items->count() > 0) {
                $completedPo = $items->filter(fn($item) => $item->purchaseOrder?->status === 'completed' && $item->purchaseOrder?->completed_at)
                    ->sortByDesc(fn($item) => $item->purchaseOrder->completed_at)
                    ->first();

                if ($completedPo) {
                    $latestReceiptDate = $completedPo->purchaseOrder->completed_at;
                } else {
                    $latestReceiptDate = $items->sortByDesc(fn($item) => $item->purchaseOrder?->id)
                        ->first()?->purchaseOrder?->created_at;
                }
            }

            return (object) [
                'product'              => $product,
                'product_id'          => $product->id,
                'supplier_cost'       => $currentCost,
                'previous_cost'       => $previousCost,
                'change_percentage'   => $changePercentage,
                'recommendation'      => $recommendation,
                'suggested_retail_price' => $currentCost > 0 ? round(($currentCost * 1.12) / 0.70, 2) : 0,
                'supplier'            => null,
                'latest_receipt_date' => $latestReceiptDate,
            ];
        });

        // Calculate pricing overview metrics before applying the cost change filter
        $pricingOverview = [
            'increased' => $analysisCollection->filter(fn($entry) => (float) $entry->change_percentage > 0)->count(),
            'decreased' => $analysisCollection->filter(fn($entry) => (float) $entry->change_percentage < 0)->count(),
            'no_change' => $analysisCollection->filter(fn($entry) => (float) $entry->change_percentage == 0)->count(),
            'total'     => $analysisCollection->count(),
        ];

        // 5. Apply cost_change filter across the FULL collection
        if ($costChangeFilter !== null && $costChangeFilter !== '') {
            $analysisCollection = $analysisCollection->filter(function ($entry) use ($costChangeFilter) {
                $change = (float) $entry->change_percentage;
                return match ($costChangeFilter) {
                    'up'   => $change > 0,
                    'down' => $change < 0,
                    'none' => $change == 0,
                    default => true,
                };
            })->values();
        }

        // Sort the collection by latest receipt date descending
        $analysisCollection = $analysisCollection->sortByDesc(function ($entry) {
            if ($entry->latest_receipt_date) {
                return $entry->latest_receipt_date instanceof \Illuminate\Support\Carbon
                    ? $entry->latest_receipt_date->timestamp
                    : strtotime((string) $entry->latest_receipt_date);
            }
            return 0;
        })->values();

        // 6. Manually paginate the fully-filtered collection.
        //    When a filter changes, page resets to 1 (the form submit has no page param).
        $perPage     = 8;
        $currentPage = (int) request('page', 1);
        $pageItems   = $analysisCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $supplierCostAnalysis = new LengthAwarePaginator(
            $pageItems,
            $analysisCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $supplierCostAlerts = \App\Models\SupplierPriceHistory::where('change_percentage', '>', 0)
            ->where('is_dismissed', false)
            ->with('product')
            ->orderByDesc('created_at')
            ->get();

        $supplierCostHighlight = null;


        return view('data_analytics.pricing-module', [
            'averageUnitPrice' => $averageUnitPrice,
            'mostExpensive' => $mostExpensive,
            'cheapest' => $cheapest,
            'priceUpdates' => $priceUpdates,
            'pricingByCategory' => $pricingByCategory,
            'supplierCostAnalysis' => $supplierCostAnalysis,
            'supplierCostAlerts' => $supplierCostAlerts,
            'supplierCostHighlight' => $supplierCostHighlight,
            'pricingOverview' => $pricingOverview,
        ]);
    }

    public function overstocking()
    {
        $products = $this->activeProducts();

        $overstocked = $products
            ->whereColumn('stock_quantity', '>', 'reorder_level')
            ->orderByDesc(DB::raw('stock_quantity - reorder_level'))
            ->get(['id', 'name', 'product_name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price']);

        $totalExcessUnits = $overstocked->sum(function ($product) {
            return max(0, $product->stock_quantity - $product->reorder_level);
        });
        $totalOverstockValue = $overstocked->sum(function ($product) {
            return max(0, $product->stock_quantity - $product->reorder_level) * $product->unit_price;
        });
        $overstockSkuCount = $overstocked->count();

        $categoryBreakdown = $overstocked
            ->groupBy('category')
            ->map(function ($items) {
                return $items->sum(function ($product) {
                    return max(0, $product->stock_quantity - $product->reorder_level) * $product->unit_price;
                });
            })
            ->sortDesc();

        return view('data_analytics.overstocking-report', [
            'overstockedProducts' => $overstocked,
            'totalExcessUnits' => $totalExcessUnits,
            'totalOverstockValue' => $totalOverstockValue,
            'overstockSkuCount' => $overstockSkuCount,
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }

    public function outOfStock()
    {
        $products = $this->activeProducts();

        $outOfStock = (clone $products)
            ->where('stock_quantity', '<=', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'product_name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price', 'last_restock_date']);

        $lowStock = (clone $products)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('stock_quantity', '>', 0)
            ->whereNotNull('reorder_level')
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'product_name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price', 'last_restock_date']);

        $outOfStockCount = $outOfStock->count();
        $lowStockCount = $lowStock->count();

        return view('data_analytics.out-of-stock-report', [
            'outOfStockProducts' => $outOfStock,
            'lowStockProducts' => $lowStock,
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
        ]);
    }

    /**
     * Get top selling products from completed POS transactions.
     */
    private function getTopSellingProductsFromTransactions($limit = 5, $startDate = null, $endDate = null)
    {
        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        try {
            $transactions = POSTransaction::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->get();
        } catch (\Exception $e) {
            return collect();
        }

        if ($transactions->isEmpty()) {
            return collect();
        }

        $productSales = [];
        $productIds = [];

        foreach ($transactions as $transaction) {
            if (!is_array($transaction->items) || empty($transaction->items)) {
                continue;
            }

            foreach ($transaction->items as $item) {
                $productId = $item['id'] ?? null;
                $quantity = (int) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                if (!$productId || $quantity <= 0) {
                    continue;
                }

                $productIds[$productId] = $productId;

                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'qty' => 0,
                        'revenue' => 0,
                    ];
                }

                $productSales[$productId]['qty'] += $quantity;
                $productSales[$productId]['revenue'] += $quantity * $unitPrice;
            }
        }

        if (empty($productSales)) {
            return collect();
        }

        $products = Product::whereIn('id', array_keys($productIds))
            ->get(['id', 'name', 'product_name', 'category'])
            ->keyBy('id');

        $topProducts = collect($productSales)
            ->map(function ($sales, $productId) use ($products) {
                $product = $products->get($productId);
                return [
                    'id' => $productId,
                    'name' => $product?->product_name ?: ($product?->name ?? 'Unknown Product'),
                    'category' => $this->normalizeCategory($product?->category ?? 'Uncategorized'),
                    'qty' => $sales['qty'],
                    'revenue' => $sales['revenue'],
                ];
            })
            ->filter(fn ($product) => $product['qty'] > 0)
            ->values()
            ->groupBy(function ($product) {
                return strtolower(trim($product['name'])) . '|' . strtolower(trim($product['category']));
            })
            ->map(function ($groupedProducts) {
                $first = $groupedProducts->first();
                return [
                    'id' => $first['id'],
                    'name' => $first['name'],
                    'category' => $first['category'],
                    'qty' => $groupedProducts->sum('qty'),
                    'revenue' => $groupedProducts->sum('revenue'),
                ];
            })
            ->sortByDesc('qty')
            ->values()
            ->slice(0, $limit);

        return $topProducts->map(function ($product, $index) {
            return [
                'rank' => $index + 1,
                'name' => $product['name'],
                'category' => $product['category'],
                'qty' => $product['qty'],
                'revenue' => '₱' . number_format($product['revenue'], 2),
            ];
        });
    }

    private function getCategoryBreakdownFromTransactions($startDate = null, $endDate = null)
    {
        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $transactions = POSTransaction::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();

        $categoryRevenue = [];
        $productIds = [];

        foreach ($transactions as $transaction) {
            if (!is_array($transaction->items)) {
                continue;
            }

            foreach ($transaction->items as $item) {
                $productId = $item['id'] ?? null;
                if ($productId) {
                    $productIds[$productId] = $productId;
                }
            }
        }

        $productCategories = Product::whereIn('id', array_keys($productIds))
            ->pluck('category', 'id')
            ->all();

        foreach ($transactions as $transaction) {
            if (!is_array($transaction->items)) {
                continue;
            }

            foreach ($transaction->items as $item) {
                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                $productId = $item['id'] ?? null;
                $revenue = $quantity * $unitPrice;

                if ($revenue <= 0) {
                    continue;
                }

                $categoryValue = $item['category'] ?? null;
                if ((!$categoryValue || strcasecmp(trim($categoryValue), 'uncategorized') === 0) && $productId) {
                    $categoryValue = $productCategories[$productId] ?? null;
                }

                $category = trim((string) $categoryValue);
                if ($category === '') {
                    $category = 'Uncategorized';
                }

                $category = $this->normalizeCategory($category);
                $categoryRevenue[$category] = ($categoryRevenue[$category] ?? 0) + $revenue;
            }
        }

        if (empty($categoryRevenue)) {
            return collect();
        }

        $totalRevenue = array_sum($categoryRevenue);

        return collect($categoryRevenue)
            ->map(function ($value, $label) use ($totalRevenue) {
                return [
                    'label' => $label,
                    'value' => (float) $value,
                    'share' => $totalRevenue > 0 ? round(($value / $totalRevenue) * 100, 0) : 0,
                ];
            })
            ->sortByDesc('value')
            ->values();
    }

    protected function normalizeCategory($value)
    {
        $allowed = [
            'engine_oil' => 'Engine Oil',
            'engine oil' => 'Engine Oil',
            'oil' => 'Engine Oil',
            'battery' => 'Battery',
            'batteries' => 'Battery',
            'spark_plug' => 'Spark Plug',
            'spark plug' => 'Spark Plug',
            'sparkplug' => 'Spark Plug',
            'brake_pads' => 'Brake Pads',
            'brake pads' => 'Brake Pads',
            'brakes' => 'Brake Pads',
            'tires' => 'Tires',
            'tire' => 'Tires',
            'filters' => 'Filters',
            'filter' => 'Filters',
            'lubricants' => 'Lubricants',
            'lubricant' => 'Lubricants',
            'accessories' => 'Accessories',
            'accessory' => 'Accessories',
        ];

        if (empty($value)) {
            return 'Uncategorized';
        }

        $normalized = strtolower(trim($value));
        $normalized = str_replace(['-', '_'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        if (strpos($normalized, 'engine oil') !== false || (strpos($normalized, 'engine') !== false && strpos($normalized, 'oil') !== false)) {
            return 'Engine Oil';
        }

        if (strpos($normalized, 'battery') !== false) {
            return 'Battery';
        }

        if (strpos($normalized, 'spark') !== false) {
            return 'Spark Plug';
        }

        if (strpos($normalized, 'brake') !== false) {
            return 'Brake Pads';
        }

        if (strpos($normalized, 'tire') !== false || strpos($normalized, 'tyre') !== false) {
            return 'Tires';
        }

        if (strpos($normalized, 'filter') !== false) {
            return 'Filters';
        }

        if (strpos($normalized, 'lubricant') !== false || strpos($normalized, 'oil') !== false) {
            return 'Lubricants';
        }

        if (strpos($normalized, 'accessory') !== false) {
            return 'Accessories';
        }

        if (in_array($normalized, ['uncategorized', 'unknown', 'n/a'], true)) {
            return 'Uncategorized';
        }

        return $allowed[$normalized] ?? 'Uncategorized';
    }

    public function dismissAlert($id)
    {
        $history = \App\Models\SupplierPriceHistory::findOrFail($id);
        $history->update(['is_dismissed' => true]);

        return response()->json(['success' => true]);
    }
}
