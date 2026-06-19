<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function management(Request $request)
    {
        $receivedRange = $request->query('received_range', 'weekly');

        $lowStockProducts = Product::where('is_active', true)
            ->where('is_archived', false)
            ->whereRaw('stock_quantity < reorder_level')
            ->paginate(8)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();
        $totalOrders = PurchaseOrder::count();
        $inTransitTotal = PurchaseOrder::where('status', 'in transit')->sum('total_amount');

        $receivedCount = PurchaseOrder::where('status', 'delivered')
            ->when($receivedRange === 'daily', fn ($query) => $query->whereDate('updated_at', today()))
            ->when($receivedRange === 'weekly', fn ($query) => $query->whereBetween('updated_at', [today()->startOfWeek(), today()->endOfWeek()]))
            ->when($receivedRange === 'monthly', fn ($query) => $query->whereMonth('updated_at', today()->month)->whereYear('updated_at', today()->year))
            ->when($receivedRange === 'yearly', fn ($query) => $query->whereYear('updated_at', today()->year))
            ->count();

        $receivedLabel = match ($receivedRange) {
            'daily' => 'Today',
            'weekly' => 'This week',
            'monthly' => 'This month',
            'yearly' => 'This year',
            default => 'This week',
        };

        $recentOrders = PurchaseOrder::latest()->limit(10)->get();

        return view('purchase_order.order-management', [
            'lowStockProducts' => $lowStockProducts,
            'suppliers' => $suppliers,
            'totalOrders' => $totalOrders,
            'inTransitTotal' => $inTransitTotal,
            'receivedCount' => $receivedCount,
            'receivedRange' => $receivedRange,
            'receivedLabel' => $receivedLabel,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function receivedOrders(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $warehouse = $request->query('warehouse');

        $deliveredToday = PurchaseOrder::where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();

        $pendingConfirmation = PurchaseOrder::whereIn('status', ['pending', 'in transit'])
            ->count();

        $issuesFound = PurchaseOrder::where('status', 'issue')
            ->count();

        $orders = PurchaseOrder::when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchase_order.received-orders', [
            'deliveredToday' => $deliveredToday,
            'pendingConfirmation' => $pendingConfirmation,
            'issuesFound' => $issuesFound,
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
            'warehouse' => $warehouse,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'nullable|exists:products,id',
            'products.*.product_name' => 'nullable|string',
            'products.*.sku' => 'nullable|string',
            'products.*.quantity' => 'nullable|integer|min:1',
            'products.*.unit_price' => 'nullable|numeric|min:0',
            'products.*.selected' => 'nullable|accepted',
        ]);

        $selectedProducts = collect($validated['products'])
            ->filter(fn ($item) => isset($item['selected']) && $item['selected'])
            ->map(fn ($item) => [
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'] ?? null,
                'sku' => $item['sku'] ?? null,
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unit_price' => (float) ($item['unit_price'] ?? 0),
                'total_price' => (float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0),
            ])
            ->values();

        if ($selectedProducts->isEmpty()) {
            return back()->withErrors(['products' => 'Select at least one product to order.'])->withInput();
        }

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $totalAmount = $selectedProducts->sum('total_price');
        $orderNumber = 'PO-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

        DB::transaction(function () use ($supplier, $validated, $selectedProducts, $totalAmount, $orderNumber, &$purchaseOrder) {
            $purchaseOrder = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'status' => 'pending',
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            $purchaseOrder->items()->createMany($selectedProducts->toArray());
        });

        return redirect()->route('order.management')->with('success', 'Purchase order was created successfully.');
    }
}
