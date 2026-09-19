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

        $movementData = $this->getProductMovementFromTransactions($monthStart->toDateString(), $endOfMonth->toDateString());

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
            'fastMoving'  => $movementData['fast'],
            'slowMoving'  => $movementData['slow'],
        ]);
    }

    /**
     * API endpoint: return the four date-filterable widget datasets as JSON.
     * Used by the global date-range calendar on the Sales Analytics page.
     */
    public function salesFilteredWidgets(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->get('end_date', now()->endOfMonth()->toDateString());

        // --- Category Distribution ---
        $categoryBreakdown = $this->getCategoryBreakdownFromTransactions($startDate, $endDate);

        $categoryData = [
            'labels'    => $categoryBreakdown->pluck('label')->toArray(),
            'values'    => $categoryBreakdown->pluck('value')->toArray(),
            'formatted' => $categoryBreakdown->map(fn($item) => '₱' . number_format($item['value'], 2))->values()->toArray(),
            'shares'    => $categoryBreakdown->pluck('share')->toArray(),
        ];

        // --- Top Selling Products (by revenue) ---
        $topProducts = $this->getTopSellingProductsFromTransactions(5, $startDate, $endDate);

        // --- Fast & Slow moving (by quantity) — reuse the same transaction scan ---
        $movementData = $this->getProductMovementFromTransactions($startDate, $endDate);

        return response()->json([
            'categoryBreakdown' => $categoryData,
            'topProducts'       => $topProducts->values()->toArray(),
            'fastMoving'        => $movementData['fast'],
            'slowMoving'        => $movementData['slow'],
        ]);
    }

    /**
     * Aggregate product sales from POS transactions for fast/slow movement tables.
     */
    private function getProductMovementFromTransactions($startDate, $endDate)
    {
        $transactions = POSTransaction::completed()
            ->dateRange($startDate, $endDate)
            ->get();

        $productSales = [];
        $productIds   = [];

        foreach ($transactions as $transaction) {
            if (!is_array($transaction->items)) {
                continue;
            }

            foreach ($transaction->items as $item) {
                $productId = $item['id'] ?? null;
                $quantity  = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);

                if (!$productId || $quantity <= 0) {
                    continue;
                }

                $productIds[$productId] = $productId;

                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = ['qty' => 0, 'revenue' => 0];
                }

                $productSales[$productId]['qty']     += $quantity;
                $productSales[$productId]['revenue'] += $quantity * $unitPrice;
            }
        }

        if (empty($productSales)) {
            return ['fast' => [], 'slow' => []];
        }

        $products = Product::whereIn('id', array_keys($productIds))
            ->get(['id', 'name', 'product_name', 'sku'])
            ->keyBy('id');

        $all = collect($productSales)
            ->map(function ($sales, $productId) use ($products) {
                $product = $products->get($productId);
                return [
                    'id'         => $productId,
                    'product_id' => $productId,
                    'name'       => $product?->product_name ?: ($product?->name ?? 'Unknown Product'),
                    'sku'        => $product?->sku ?? 'N/A',
                    'qty'        => $sales['qty'],
                    'revenue'    => $sales['revenue'],
                ];
            })
            ->filter(fn($p) => $p['qty'] > 0)
            ->values();

        $fast = $all->filter(fn($p) => $p['qty'] >= 10)->sortByDesc('qty')->take(5)->values()->map(fn($p, $index) => [
            'rank'       => $index + 1,
            'id'         => $p['id'],
            'product_id' => $p['product_id'],
            'name'       => $p['name'],
            'sku'        => $p['sku'],
            'qty'        => $p['qty'],
            'revenue'    => '₱' . number_format($p['revenue'], 2),
        ])->toArray();

        $slow = $all->filter(fn($p) => $p['qty'] < 10)->sortBy('qty')->take(5)->values()->map(fn($p, $index) => [
            'rank'       => $index + 1,
            'id'         => $p['id'],
            'product_id' => $p['product_id'],
            'name'       => $p['name'],
            'sku'        => $p['sku'],
            'qty'        => $p['qty'],
        ])->toArray();

        return ['fast' => $fast, 'slow' => $slow];
    }

    protected function buildSalesTrendData()
    {
        $now = now();

        $yearlyLabels = [];
        $yearlyValues = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->copy()->subYears($i);
            $yearlyLabels[] = $year->format('Y');
            $yearlyValues[] = (float) POSTransaction::query()
                ->completed()
                ->whereBetween('completed_at', [$year->copy()->startOfYear(), $year->copy()->endOfYear()])
                ->sum('total_amount');
        }

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
            'yearly' => ['labels' => $yearlyLabels, 'values' => $yearlyValues],
            'monthly' => ['labels' => $monthlyLabels, 'values' => $monthlyValues],
            'weekly' => ['labels' => $weeklyLabels, 'values' => $weeklyValues],
            'daily' => ['labels' => $dailyLabels, 'values' => $dailyValues],
        ];
    }

    public function pricing()
    {
        $products = $this->activeProducts();

        $averageUnitPrice = (clone $products)->avg('unit_price') ?: 0;
        $mostExpensive = (clone $products)->orderByDesc('unit_price')->first();
        $cheapest = (clone $products)->orderBy('unit_price')->first();

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
                $previousCost !== null && $currentCost > $previousCost => 'Increase the retail price to maintain a 20% markup.',
                $previousCost !== null && $currentCost < $previousCost => 'Maintain the current retail price to increase markup.',
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
                'suggested_retail_price' => $currentCost > 0 ? round($currentCost * 1.20, 2) : 0,
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
            $transactions = POSTransaction::completed()
                ->dateRange($startDate, $endDate)
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
            ->get(['id', 'name', 'product_name', 'category', 'sku'])
            ->keyBy('id');

        $topProducts = collect($productSales)
            ->map(function ($sales, $productId) use ($products) {
                $product = $products->get($productId);
                $cat = $product?->category;
                if (!$cat || strtolower(trim($cat)) === 'uncategorized') {
                    $cat = $product?->product_name ?: ($product?->name ?? 'Uncategorized');
                }
                
                return [
                    'id' => $productId,
                    'name' => $product?->product_name ?: ($product?->name ?? 'Unknown Product'),
                    'sku' => $product?->sku ?? 'N/A',
                    'category' => $this->normalizeCategory($cat),
                    'qty' => $sales['qty'],
                    'revenue' => $sales['revenue'],
                ];
            })
            ->filter(fn ($product) => $product['qty'] > 0)
            ->sortByDesc('qty')
            ->values()
            ->slice(0, $limit);

        return $topProducts->map(function ($product, $index) {
            return [
                'rank'       => $index + 1,
                'id'         => $product['id'] ?? null,
                'product_id' => $product['id'] ?? null,
                'name'       => $product['name'],
                'sku'        => $product['sku'],
                'category'   => $product['category'],
                'qty'        => $product['qty'],
                'revenue'    => '₱' . number_format($product['revenue'], 2),
            ];
        });
    }

    private function getCategoryBreakdownFromTransactions($startDate = null, $endDate = null)
    {
        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $transactions = POSTransaction::completed()
            ->dateRange($startDate, $endDate)
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

        $productsForCat = Product::whereIn('id', array_keys($productIds))
            ->get(['id', 'category', 'product_name', 'name']);
            
        $productCategories = [];
        foreach ($productsForCat as $prod) {
            $cat = $prod->category;
            if (!$cat || strtolower(trim($cat)) === 'uncategorized') {
                $cat = $prod->product_name ?: ($prod->name ?? 'Uncategorized');
            }
            $productCategories[$prod->id] = $cat;
        }

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
        if (empty($value)) {
            return 'Uncategorized';
        }

        $normalized = strtolower(trim((string) $value));
        $normalized = str_replace(['-', '_'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Brakes
        if (str_contains($normalized, 'brake') || str_contains($normalized, 'caliper') || str_contains($normalized, 'disc') || str_contains($normalized, 'lever')) {
            return 'Brakes';
        }

        // Exhaust
        if (str_contains($normalized, 'pipe') || str_contains($normalized, 'exhaust')) {
            return 'Exhaust';
        }

        // Tires
        if (str_contains($normalized, 'tire') || str_contains($normalized, 'tyre')) {
            return 'Tires';
        }

        // Oils
        if (str_contains($normalized, 'oil') || str_contains($normalized, 'lubricant')) {
            return 'Oils';
        }

        // Batteries
        if (str_contains($normalized, 'battery') || str_contains($normalized, 'batteries')) {
            return 'Batteries';
        }

        // Helmets
        if (str_contains($normalized, 'helmet')) {
            return 'Helmets';
        }

        // Catch-all for uncategorized
        if (in_array($normalized, ['uncategorized', 'unknown', 'n/a', ''], true)) {
            return 'Uncategorized';
        }

        // Everything else defaults to Accessories (Mags, Seats, Shocks, Mirrors, etc.)
        return 'Accessories';
    }

    public function dismissAlert($id)
    {
        $history = \App\Models\SupplierPriceHistory::findOrFail($id);
        $history->update(['is_dismissed' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Export Sales Analytics report as CSV
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->get('end_date', now()->endOfMonth()->toDateString());

        $topProducts  = $this->getTopSellingProductsFromTransactions(50, $startDate, $endDate);
        $movementData = $this->getProductMovementFromTransactions($startDate, $endDate);

        $fileName = 'sales_analytics_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($topProducts, $movementData, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Report metadata
            fputcsv($handle, ['Sales Analytics Report']);
            fputcsv($handle, ['Period:', $startDate . ' to ' . $endDate]);
            fputcsv($handle, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            // Top Selling Products
            fputcsv($handle, ['--- TOP SELLING PRODUCTS ---']);
            fputcsv($handle, ['Rank', 'Product Name', 'SKU', 'Category', 'Qty Sold', 'Revenue']);
            foreach ($topProducts as $product) {
                fputcsv($handle, [
                    $product['rank'],
                    $product['name'],
                    $product['sku'],
                    $product['category'],
                    $product['qty'],
                    $product['revenue'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- FAST MOVING PRODUCTS ---']);
            fputcsv($handle, ['Product Name', 'SKU', 'Qty Sold', 'Revenue']);
            foreach ($movementData['fast'] as $item) {
                fputcsv($handle, [$item['name'], $item['sku'], $item['qty'], $item['revenue']]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- SLOW MOVING PRODUCTS ---']);
            fputcsv($handle, ['Product Name', 'SKU', 'Qty Sold']);
            foreach ($movementData['slow'] as $item) {
                fputcsv($handle, [$item['name'], $item['sku'], $item['qty']]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Pricing Module report as CSV
     */
    public function exportPricing(Request $request)
    {
        $products = $this->activeProducts();

        $productIdsWithPOs = PurchaseOrderItem::where('received_quantity', '>', 0)
            ->whereHas('purchaseOrder')
            ->select('product_id')
            ->distinct()
            ->pluck('product_id');

        $allProducts = Product::whereIn('id', $productIdsWithPOs)->get();

        $allProductIds = $allProducts->pluck('id');
        $poItems = PurchaseOrderItem::whereIn('product_id', $allProductIds)
            ->where('received_quantity', '>', 0)
            ->whereHas('purchaseOrder')
            ->with('purchaseOrder')
            ->get()
            ->groupBy('product_id');

        $analysisCollection = $allProducts->map(function ($product) use ($poItems) {
            $items = collect($poItems->get($product->id, collect()))
                ->sortByDesc(fn($item) => $item->purchaseOrder?->id)
                ->values();

            $currentCost  = $items->count() > 0  ? (float) $items[0]->unit_price : 0;
            $previousCost = $items->count() >= 2 ? (float) $items[1]->unit_price : null;

            $changePercentage = $previousCost && $previousCost > 0
                ? round((($currentCost - $previousCost) / $previousCost) * 100, 2)
                : 0;

            return [
                'product_name'     => $product->product_name ?: ($product->name ?? 'Unknown'),
                'sku'              => $product->sku ?? 'N/A',
                'category'         => $product->category ?? 'Uncategorized',
                'retail_price'     => $product->unit_price ?? 0,
                'supplier_cost'    => $currentCost,
                'previous_cost'    => $previousCost ?? 'N/A',
                'change_pct'       => $changePercentage,
                'suggested_retail' => $currentCost > 0 ? round($currentCost * 1.20, 2) : 0,
            ];
        });

        $fileName = 'pricing_report_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($analysisCollection) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Pricing Module Report']);
            fputcsv($handle, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Retail Price (₱)', 'Latest Supplier Cost (₱)', 'Previous Cost (₱)', 'Cost Change (%)', 'Suggested Retail (₱)']);

            foreach ($analysisCollection as $row) {
                fputcsv($handle, [
                    $row['product_name'],
                    $row['sku'],
                    $row['category'],
                    number_format((float) $row['retail_price'], 2),
                    number_format((float) $row['supplier_cost'], 2),
                    $row['previous_cost'] !== 'N/A' ? number_format((float) $row['previous_cost'], 2) : 'N/A',
                    $row['change_pct'] . '%',
                    number_format((float) $row['suggested_retail'], 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Overstocking report as CSV
     */
    public function exportOverstocking(Request $request)
    {
        $overstocked = $this->activeProducts()
            ->whereColumn('stock_quantity', '>', 'reorder_level')
            ->orderByDesc(DB::raw('stock_quantity - reorder_level'))
            ->get(['id', 'name', 'product_name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price']);

        $fileName = 'overstocking_report_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($overstocked) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Overstocking Report']);
            fputcsv($handle, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);
            fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Stock Qty', 'Reorder Level', 'Excess Units', 'Unit Price (₱)', 'Excess Value (₱)']);

            foreach ($overstocked as $product) {
                $excessUnits = max(0, $product->stock_quantity - $product->reorder_level);
                $excessValue = $excessUnits * $product->unit_price;
                fputcsv($handle, [
                    $product->product_name ?: $product->name,
                    $product->sku ?? 'N/A',
                    $product->category ?? 'Uncategorized',
                    $product->stock_quantity,
                    $product->reorder_level,
                    $excessUnits,
                    number_format($product->unit_price, 2),
                    number_format($excessValue, 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Out-of-Stock report as CSV
     */
    public function exportOutOfStock(Request $request)
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

        $fileName = 'stockout_list_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($outOfStock, $lowStock) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Out of Stock & Low Stock Report']);
            fputcsv($handle, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            fputcsv($handle, ['--- OUT OF STOCK PRODUCTS ---']);
            fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Stock Qty', 'Reorder Level', 'Unit Price (₱)', 'Last Restock Date']);
            foreach ($outOfStock as $product) {
                fputcsv($handle, [
                    $product->product_name ?: $product->name,
                    $product->sku ?? 'N/A',
                    $product->category ?? 'Uncategorized',
                    $product->stock_quantity,
                    $product->reorder_level ?? 'N/A',
                    number_format($product->unit_price, 2),
                    $product->last_restock_date ? \Carbon\Carbon::parse($product->last_restock_date)->format('Y-m-d') : 'Never',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- LOW STOCK PRODUCTS ---']);
            fputcsv($handle, ['Product Name', 'SKU', 'Category', 'Stock Qty', 'Reorder Level', 'Unit Price (₱)', 'Last Restock Date']);
            foreach ($lowStock as $product) {
                fputcsv($handle, [
                    $product->product_name ?: $product->name,
                    $product->sku ?? 'N/A',
                    $product->category ?? 'Uncategorized',
                    $product->stock_quantity,
                    $product->reorder_level,
                    number_format($product->unit_price, 2),
                    $product->last_restock_date ? \Carbon\Carbon::parse($product->last_restock_date)->format('Y-m-d') : 'Never',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
