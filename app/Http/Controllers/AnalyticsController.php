<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
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
        return Product::where('is_archived', false);
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

        $topProducts = (clone $products)
            ->select('id', 'name', 'category', 'stock_quantity', 'unit_price')
            ->orderByDesc(DB::raw('stock_quantity * unit_price'))
            ->limit(5)
            ->get()
            ->map(function ($product, $index) {
                $revenue = $product->stock_quantity * $product->unit_price;
                return [
                    'rank' => $index + 1,
                    'name' => $product->name,
                    'category' => $product->category,
                    'qty' => $product->stock_quantity,
                    'revenue' => number_format($revenue, 2),
                    'included_vat' => number_format($this->vatService->calculateIncludedVat($revenue), 2),
                    'vatable_sales' => number_format($revenue - $this->vatService->calculateIncludedVat($revenue), 2),
                ];
            });

        $categoryBreakdown = (clone $products)
            ->select('category', DB::raw('SUM(stock_quantity * unit_price) as value'))
            ->groupBy('category')
            ->orderByDesc('value')
            ->get()
            ->map(function ($row) use ($totalInventoryValue) {
                return [
                    'label' => $row->category ?: 'Uncategorized',
                    'value' => $row->value,
                    'included_vat' => $this->vatService->calculateIncludedVat($row->value),
                    'vatable_sales' => $row->value - $this->vatService->calculateIncludedVat($row->value),
                    'share' => $totalInventoryValue > 0 ? round($row->value / $totalInventoryValue * 100, 0) : 0,
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

        $inventoryTrend = InventoryMovement::whereIn('type', ['import', 'restock'])
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as day, SUM(quantity_change * unit_price) as value')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->day => (float) $row->value];
            });

        $trendLabels = [];
        $trendValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('M j');
            $dateKey = now()->subDays($i)->format('Y-m-d');
            $trendLabels[] = $day;
            $trendValues[] = round($inventoryTrend->get($dateKey, 0), 2);
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
                'labels' => $trendLabels,
                'values' => $trendValues,
            ],
            'categoryBreakdown' => [
                'labels' => $categoryBreakdown->pluck('label')->toArray(),
                'values' => $categoryBreakdown->pluck('share')->toArray(),
                'included_vat' => $categoryBreakdown->pluck('included_vat')->toArray(),
                'vatable_sales' => $categoryBreakdown->pluck('vatable_sales')->toArray(),
            ],
            'topProducts' => $topProducts,
            'brandMomentum' => $brandMomentum,
        ]);
    }

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
            ->limit(20)
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

        $outOfStock = $products
            ->where('stock_quantity', '<=', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'category', 'stock_quantity', 'reorder_level', 'unit_price', 'last_restock_date']);

        $lowStock = $products
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('stock_quantity', '>', 0)
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
}
