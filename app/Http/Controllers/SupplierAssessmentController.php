<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SupplierAssessmentController extends Controller
{
    public function index()
    {
        $actualSupplierNames = Supplier::where('status', 'active')->pluck('name');

        $productSupplierNames = Product::whereNotNull('supplier_name')
            ->whereIn('supplier_name', $actualSupplierNames)
            ->pluck('supplier_name')
            ->filter()
            ->unique()
            ->values();

        $allSupplierNames = $productSupplierNames->merge($actualSupplierNames)->unique()->values();

        $products = Product::whereNotNull('supplier_name')
            ->whereIn('supplier_name', $actualSupplierNames)
            ->orderBy('supplier_name')
            ->orderByDesc('stock_quantity')
            ->get()
            ->groupBy('supplier_name');

        $orders = PurchaseOrder::whereIn('supplier_name', $allSupplierNames)
            ->get()
            ->groupBy('supplier_name');

        $suppliersByName = Supplier::whereIn('name', $allSupplierNames)->where('status', 'active')->get()->keyBy('name');

        $supplierSummaries = $allSupplierNames->map(function ($supplierName) use ($products, $orders, $suppliersByName) {
            $supplier = $suppliersByName->get($supplierName);
            $group = $products->get($supplierName, collect());
            $supplierOrders = $orders->get($supplierName, collect());
            $deliveredOrders = $supplierOrders->filter(fn ($order) => strtolower($order->status) === 'delivered');
            $onTimeDeliveries = $deliveredOrders->filter(function ($order) {
                if (!$order->expected_delivery_date) {
                    return false;
                }
                return optional($order->updated_at)->toDateString() <= optional($order->expected_delivery_date)->toDateString();
            });
            $totalValue = $group->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
            $avgPrice = $group->count() ? $group->avg('unit_price') : 0;
            $minPrice = $group->count() ? $group->min('unit_price') : 0;
            $maxPrice = $group->count() ? $group->max('unit_price') : 0;
            $priceRange = $maxPrice - $minPrice;
            $priceStdDeviation = $group->count() ? sqrt($group->avg(fn (Product $product) => pow($product->unit_price - $avgPrice, 2))) : 0;
            $lastRestock = $group->max('last_restock_date');
            $ordersCount = $supplierOrders->count();
            $deliveredCount = $deliveredOrders->count();
            $onTimeCount = $onTimeDeliveries->count();
            $orderCompletionRate = $ordersCount ? ($deliveredCount / $ordersCount) * 100 : 0;
            $onTimeRate = $deliveredCount ? ($onTimeCount / $deliveredCount) * 100 : 0;
            $performanceScore = round(($orderCompletionRate + $onTimeRate) / 2);

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
                'total_value' => $totalValue,
                'avg_price' => $avgPrice,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range' => $priceRange,
                'price_std_deviation' => $priceStdDeviation,
                'last_restock_date' => $lastRestock ? Carbon::parse($lastRestock)->toDateString() : null,
                'products' => $group->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->product_name ?: $product->name,
                    'sku' => $product->sku,
                    'price' => $product->unit_price,
                    'stock_quantity' => $product->stock_quantity,
                    'category' => $product->category,
                    'last_restock_date' => optional($product->last_restock_date)->toDateString(),
                ])->values(),
                'has_record' => $supplier !== null,
                'orders_count' => $ordersCount,
                'delivered_orders_count' => $deliveredCount,
                'on_time_deliveries' => $onTimeCount,
                'on_time_rate' => round($onTimeRate),
                'completion_rate' => round($orderCompletionRate),
                'performance_score' => $performanceScore,
                'orders' => $supplierOrders->map(fn ($order) => [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'expected_delivery_date' => optional($order->expected_delivery_date)->toDateString(),
                    'updated_at' => optional($order->updated_at)->toDateString(),
                    'total_amount' => (float) $order->total_amount,
                ])->values(),
            ];
        });

        $supplierSummaries = $supplierSummaries->filter(fn ($summary) => $summary->status === 'active');

        $totalSuppliers = $allSupplierNames->count();
        $totalProducts = Product::whereNotNull('supplier_name')->count();
        $totalStockValue = Product::whereNotNull('supplier_name')
            ->get()
            ->sum(fn (Product $product) => $product->stock_quantity * $product->unit_price);
        $activeSuppliers = $supplierSummaries->filter(fn ($summary) => $summary->status === 'active')->count();

        return view('supplier_assessment.supplier_asses', [
            'supplierSummaries' => $supplierSummaries,
            'quickStats' => [
                'totalSuppliers' => $totalSuppliers,
                'activeSuppliers' => $activeSuppliers,
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
        Supplier::create($data);

        return redirect()->route('supplier.assessment')->with('success', 'Supplier added successfully.');
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
