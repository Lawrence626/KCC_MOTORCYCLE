<?php

namespace App\Http\Controllers;

use App\Models\FastMovingProduct;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\ReverseLogistics;
use App\Models\SlowMovingProduct;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Services\SupplierPerformanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SupplierAssessmentController extends Controller
{
    public function index(SupplierPerformanceService $performanceService)
    {
        $activeSuppliers = Supplier::where('status', 'active')->get();
        $actualSupplierNames = $activeSuppliers->pluck('name');

        $productSupplierNames = Product::whereNotNull('supplier_name')
            ->whereIn('supplier_name', $actualSupplierNames)
            ->pluck('supplier_name')
            ->filter()
            ->unique()
            ->values();

        $allSupplierNames = $productSupplierNames->merge($actualSupplierNames)->unique()->values();

        $products = Product::whereNotNull('supplier_name')
            ->whereIn('supplier_name', $allSupplierNames)
            ->orderBy('supplier_name')
            ->orderByDesc('stock_quantity')
            ->get()
            ->groupBy('supplier_name');

        $orders = PurchaseOrder::with(['items', 'supplier'])
            ->where(function ($q) use ($activeSuppliers, $allSupplierNames) {
                $q->whereIn('supplier_name', $allSupplierNames)
                  ->orWhereIn('supplier_id', $activeSuppliers->pluck('id'));
            })
            ->get();

        $ordersBySupplierName = $orders->groupBy('supplier_name');
        $ordersBySupplierId = $orders->groupBy('supplier_id');

        $arrivalNotices = StockArrivalNotice::all();
        $reverseLogistics = ReverseLogistics::all();
        $priceHistories = SupplierPriceHistory::all();

        $suppliersByName = $activeSuppliers->keyBy('name');

        // Aggregate product sales from completed POS transactions
        $completedTransactions = POSTransaction::where('status', 'completed')->get();
        $salesByProductId = [];
        $salesBySku = [];
        $salesByName = [];

        foreach ($completedTransactions as $transaction) {
            $items = $transaction->items;
            if (!is_array($items) && is_string($items)) {
                $items = json_decode($items, true);
            }
            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
                $pId = $item['id'] ?? $item['product_id'] ?? null;
                $sku = $item['sku'] ?? null;
                $name = $item['name'] ?? $item['product_name'] ?? null;

                if ($pId) {
                    $salesByProductId[$pId] = ($salesByProductId[$pId] ?? 0) + $qty;
                }
                if ($sku) {
                    $salesBySku[$sku] = ($salesBySku[$sku] ?? 0) + $qty;
                }
                if ($name) {
                    $salesByName[$name] = ($salesByName[$name] ?? 0) + $qty;
                }
            }
        }

        $fastMovingIds = FastMovingProduct::pluck('product_id')->filter()->all();
        $slowMovingIds = SlowMovingProduct::pluck('product_id')->filter()->all();

        $supplierSummaries = $allSupplierNames->map(function ($supplierName) use (
            $products,
            $ordersBySupplierName,
            $ordersBySupplierId,
            $suppliersByName,
            $performanceService,
            $arrivalNotices,
            $reverseLogistics,
            $priceHistories,
            $salesByProductId,
            $salesBySku,
            $salesByName,
            $fastMovingIds,
            $slowMovingIds
        ) {
            $supplier = $suppliersByName->get($supplierName);
            $group = $products->get($supplierName, collect());

            $supplierOrdersByName = $ordersBySupplierName->get($supplierName, collect());
            $supplierOrdersById = $supplier?->id ? $ordersBySupplierId->get($supplier->id, collect()) : collect();
            $supplierOrders = $supplierOrdersByName->merge($supplierOrdersById)->unique('id')->values();

            $performance = $performanceService->calculateForSupplier(
                $supplier,
                $group,
                $supplierOrders,
                $arrivalNotices,
                $reverseLogistics,
                $priceHistories
            );

            $totalValue = $group->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
            $avgPrice = $group->count() ? $group->avg('unit_price') : 0;
            $minPrice = $group->count() ? $group->min('unit_price') : 0;
            $maxPrice = $group->count() ? $group->max('unit_price') : 0;
            $priceRange = $maxPrice - $minPrice;
            $priceStdDeviation = $group->count() ? sqrt($group->avg(fn (Product $product) => pow($product->unit_price - $avgPrice, 2))) : 0;
            $lastRestock = $group->max('last_restock_date');

            // Map products with movement classification
            $mappedProducts = $group->map(function (Product $product) use (
                $salesByProductId,
                $salesBySku,
                $salesByName,
                $fastMovingIds,
                $slowMovingIds
            ) {
                $unitsSold = $salesByProductId[$product->id]
                    ?? ($salesBySku[$product->sku]
                    ?? ($salesByName[$product->product_name ?: $product->name] ?? 0));

                $isExplicitFast = in_array($product->id, $fastMovingIds);
                $isExplicitSlow = in_array($product->id, $slowMovingIds);

                // Fast moving if units sold >= 10, or explicitly in fast moving products table, or units sold >= 3
                $isFast = $isExplicitFast || ($unitsSold >= 10) || (!$isExplicitSlow && $unitsSold >= 3);
                $movementCategory = $isFast ? 'fast_moving' : 'slow_moving';
                $movementLabel = $isFast ? 'Fast Moving' : 'Slow Moving';

                return [
                    'id' => $product->id,
                    'name' => $product->product_name ?: $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $product->unit_price,
                    'stock_quantity' => (int) $product->stock_quantity,
                    'category' => $product->category,
                    'last_restock_date' => optional($product->last_restock_date)->toDateString(),
                    'units_sold' => (int) $unitsSold,
                    'movement_category' => $movementCategory,
                    'movement_label' => $movementLabel,
                ];
            })->values();

            $fastMovingProducts = $mappedProducts->where('movement_category', 'fast_moving')->sortByDesc('units_sold')->values();
            $slowMovingProducts = $mappedProducts->where('movement_category', 'slow_moving')->sortByDesc('stock_quantity')->values();

            return (object) [
                'id' => $supplier?->id,
                'name' => $supplierName,
                'contact_person' => $supplier?->contact_person,
                'contact_position' => $supplier?->contact_position,
                'address' => $supplier?->address,
                'email' => $supplier?->email,
                'phone' => $supplier?->phone,
                'status' => $supplier?->status ?? 'active',
                'notes' => $supplier?->notes,
                'product_count' => $group->count(),
                'fast_moving_count' => $fastMovingProducts->count(),
                'slow_moving_count' => $slowMovingProducts->count(),
                'fast_moving_products' => $fastMovingProducts->all(),
                'slow_moving_products' => $slowMovingProducts->all(),
                'total_value' => $totalValue,
                'avg_price' => $avgPrice,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range' => $priceRange,
                'price_std_deviation' => $priceStdDeviation,
                'last_restock_date' => $lastRestock ? Carbon::parse($lastRestock)->toDateString() : null,
                'products' => $mappedProducts->all(),
                'has_record' => $supplier !== null,
                'orders_count' => $performance['orders_count'],
                'delivered_orders_count' => $performance['delivered_orders_count'],
                'on_time_deliveries' => $performance['on_time_deliveries'],
                'on_time_rate' => $performance['on_time_rate'],
                'on_time_rate_precise' => $performance['on_time_rate_precise'],
                'completion_rate' => $performance['completion_rate'],
                'completion_rate_precise' => $performance['completion_rate_precise'],
                'total_quantity_ordered' => $performance['total_quantity_ordered'],
                'total_quantity_received' => $performance['total_quantity_received'],
                'defective_quantity' => $performance['defective_quantity'],
                'defect_rate' => $performance['defect_rate'],
                'quality_score' => $performance['quality_score'],
                'price_stability' => $performance['price_stability'],
                'price_stability_precise' => $performance['price_stability_precise'],
                'performance_score' => $performance['performance_score'],
                'orders' => $performance['orders'],
            ];
        });

        $supplierSummaries = $supplierSummaries->filter(fn ($summary) => $summary->status === 'active')->values();

        $totalSuppliers = $allSupplierNames->count();
        $totalProducts = Product::whereNotNull('supplier_name')->count();
        $totalStockValue = Product::whereNotNull('supplier_name')
            ->get()
            ->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
        $activeSuppliersCount = $supplierSummaries->filter(fn ($summary) => $summary->status === 'active')->count();

        return view('supplier_assessment.supplier_asses', [
            'supplierSummaries' => $supplierSummaries,
            'quickStats' => [
                'totalSuppliers' => $totalSuppliers,
                'activeSuppliers' => $activeSuppliersCount,
                'trackedProducts' => $totalProducts,
                'stockValue' => $totalStockValue,
            ],
        ]);
    }

    public function archived()
    {
        $archivedSuppliers = Supplier::where('status', 'inactive')->paginate(10);

        return view('supplier_assessment.archived', [
            'archivedSuppliers' => $archivedSuppliers,
        ]);
    }

    public function restore(Supplier $supplier)
    {
        $supplier->update(['status' => 'active']);

        return redirect()->route('supplier.assessment.archived')->with('success', 'Supplier restored successfully.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_position' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        $data['status'] = $data['status'] ?? 'active';
        $supplier = Supplier::create($data);

        return redirect()->route('supplier.assessment', ['selected_supplier' => $supplier->name])->with('success', 'Supplier added successfully.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplier->id)],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_position' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        $oldName = $supplier->name;
        if (!array_key_exists('status', $data)) {
            unset($data['status']);
        }
        $supplier->update($data);

        if ($oldName !== $supplier->name) {
            Product::where('supplier_name', $oldName)->update(['supplier_name' => $supplier->name]);
        }

        return redirect()->route('supplier.assessment', ['selected_supplier' => $supplier->name])->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['status' => 'inactive']);

        return redirect()->route('supplier.assessment')->with('success', 'Supplier archived successfully.');
    }
}
