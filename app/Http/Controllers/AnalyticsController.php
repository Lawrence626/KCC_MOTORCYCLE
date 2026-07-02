<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\POSTransaction;
use App\Services\VatCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $topProducts = $this->getTopSellingProductsFromTransactions();

        $categoryBreakdown = (clone $products)
            ->select('category', DB::raw('SUM(stock_quantity * unit_price) as value'))
            ->groupBy('category')
            ->orderByDesc('value')
            ->get()
            ->map(function ($row) use ($totalInventoryValue) {
                $value = (float) $row->value;
                return [
                    'label' => $row->category ?: 'Uncategorized',
                    'value' => $value,
                    'included_vat' => $this->vatService->calculateIncludedVat($value),
                    'vatable_sales' => $value - $this->vatService->calculateIncludedVat($value),
                    'share' => $totalInventoryValue > 0 ? round($value / $totalInventoryValue * 100, 0) : 0,
                ];
            });

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
            'salesTrend' => [
                'weekly' => [
                    'labels' => $weeklyLabels,
                    'values' => $weeklyValues,
                ],
            ],
            'categoryBreakdown' => [
                'labels' => $categoryBreakdown->pluck('label')->toArray(),
                'values' => $categoryBreakdown->pluck('value')->toArray(),
                'shares' => $categoryBreakdown->pluck('share')->toArray(),
                'included_vat' => $categoryBreakdown->pluck('included_vat')->toArray(),
                'vatable_sales' => $categoryBreakdown->pluck('vatable_sales')->toArray(),
            ],
            'topProducts' => $topProducts,
        ]);    }

    public function pricing()
    {
        $products = $this->activeProducts();

        $averageUnitPrice = $products->avg('unit_price') ?: 0;
        $mostExpensive = $products->orderByDesc('unit_price')->first();
        $cheapest = $products->orderBy('unit_price')->first();

        $priceUpdates = InventoryMovement::where('type', 'price_update')
            ->latest()
            ->limit(10)
            ->with('product')
            ->get()
            ->map(function ($movement) {
                return [
                    'product' => $movement->product->name ?? 'Unknown',
                    'sku' => $movement->product->sku ?? 'N/A',
                    'old_price' => data_get($movement, 'metadata.old_price') ?? null,
                    'new_price' => $movement->unit_price,
                    'notes' => $movement->notes,
                    'updated_at' => $movement->created_at->format('M d, Y'),
                ];
            });

        $pricingByCategory = $products
            ->select('category', DB::raw('AVG(unit_price) as avg_price'), DB::raw('SUM(stock_quantity) as total_qty'))
            ->groupBy('category')
            ->orderByDesc('total_qty')
            ->get();

        return view('data_analytics.pricing-module', [
            'averageUnitPrice' => $averageUnitPrice,
            'mostExpensive' => $mostExpensive,
            'cheapest' => $cheapest,
            'priceUpdates' => $priceUpdates,
            'pricingByCategory' => $pricingByCategory,
        ]);
    }

    public function overstocking()
    {
        $products = $this->activeProducts();

        $overstocked = $products
            ->whereColumn('stock_quantity', '>', 'reorder_level')
            ->orderByDesc(DB::raw('stock_quantity - reorder_level'))
            ->get(['id', 'name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price']);

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
            ->get(['id', 'name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price', 'last_restock_date']);

        $lowStock = (clone $products)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('stock_quantity', '>', 0)
            ->whereNotNull('reorder_level')
            ->orderBy('stock_quantity')
            ->get(['id', 'name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price', 'last_restock_date']);

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
            ->get(['id', 'name', 'category'])
            ->keyBy('id');

        $topProducts = collect($productSales)
            ->map(function ($sales, $productId) use ($products) {
                $product = $products->get($productId);
                return [
                    'id' => $productId,
                    'name' => $product?->name ?? 'Unknown Product',
                    'category' => $product?->category ?? 'Uncategorized',
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
                'rank' => $index + 1,
                'name' => $product['name'],
                'category' => $product['category'],
                'qty' => $product['qty'],
                'revenue' => '₱' . number_format($product['revenue'], 2),
            ];
        });
    }
}
