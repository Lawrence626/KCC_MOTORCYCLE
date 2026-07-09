<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrderSentMail;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        $receivedCount = PurchaseOrder::where('status', 'completed')
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

        $orders = $this->filteredPurchaseOrders($request, ['pending approval', 'approved', 'sent to supplier', 'in transit'], 'orders')
            ->latest()
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString();

        $backOrders = $this->filteredBackOrderItems($request)
            ->paginate(10, ['*'], 'back_orders_page')
            ->withQueryString();

        $receivedOrders = $this->filteredPurchaseOrders($request, ['completed', 'partially received'], 'received')
            ->latest()
            ->paginate(10, ['*'], 'received_page')
            ->withQueryString();

        $cancelledOrders = $this->filteredPurchaseOrders($request, ['rejected', 'cancelled'], 'cancelled')
            ->latest()
            ->paginate(10, ['*'], 'cancelled_page')
            ->withQueryString();

        return view('purchase_order.order-management', [
            'lowStockProducts' => $lowStockProducts,
            'suppliers' => $suppliers,
            'totalOrders' => $totalOrders,
            'inTransitTotal' => $inTransitTotal,
            'receivedCount' => $receivedCount,
            'receivedRange' => $receivedRange,
            'receivedLabel' => $receivedLabel,
            'orders' => $orders,
            'backOrders' => $backOrders,
            'receivedOrders' => $receivedOrders,
            'cancelledOrders' => $cancelledOrders,
            'activeTab' => $request->query('tab', 'orders'),
        ]);
    }

    public function create(Request $request)
    {
        $selectedProductIds = collect($request->query('product_id') ? [$request->query('product_id')] : [])
            ->merge($request->query('product_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $lowStockProducts = Product::where('is_active', true)
            ->where('is_archived', false)
            ->where(function ($query) use ($selectedProductIds) {
                $query->where('stock_quantity', '<=', 10);

                if (! empty($selectedProductIds)) {
                    $query->orWhereIn('id', $selectedProductIds);
                }
            })
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        return view('purchase_order.create', [
            'lowStockProducts' => $lowStockProducts,
            'suppliers' => Supplier::orderBy('name')->get(),
            'selectedProductIds' => $selectedProductIds,
        ]);
    }

    public function history(Request $request)
    {
        return redirect()->route('order.management');
    }

    private function filteredPurchaseOrders(Request $request, array $statuses, string $prefix)
    {
        $search = $request->query($prefix . '_search');
        $status = $request->query($prefix . '_status');
        $supplier = $request->query($prefix . '_supplier');

        return PurchaseOrder::whereIn('status', $statuses)
            ->when($status && in_array($status, $statuses, true), fn ($query) => $query->where('status', $status))
            ->when($supplier, fn ($query, $supplier) => $query->where('supplier_name', $supplier))
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            }));
    }

    private function filteredBackOrderItems(Request $request)
    {
        $search = $request->query('back_orders_search');
        $supplier = $request->query('back_orders_supplier');

        return PurchaseOrderItem::query()
            ->with('purchaseOrder')
            ->whereColumn('received_quantity', '<', 'quantity')
            ->whereHas('purchaseOrder', function ($query) use ($supplier) {
                $query->whereNotIn('status', ['rejected', 'cancelled'])
                    ->whereHas('items', fn ($query) => $query->where('received_quantity', '>', 0))
                    ->when($supplier, fn ($query, $supplier) => $query->where('supplier_name', $supplier));
            })
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhereHas('purchaseOrder', function ($query) use ($search) {
                        $query->where('order_number', 'like', "%{$search}%")
                            ->orWhere('supplier_name', 'like', "%{$search}%");
                    });
            }))
            ->latest();
    }

    public function receivedOrders(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $warehouse = $request->query('warehouse');

        $receivedStatuses = ['completed', 'partially received'];

        $deliveredToday = PurchaseOrder::whereIn('status', $receivedStatuses)
            ->whereDate('updated_at', today())
            ->count();

        $pendingConfirmation = PurchaseOrder::whereIn('status', ['pending approval', 'approved', 'sent to supplier', 'in transit', 'partially received'])
            ->count();

        $issuesFound = PurchaseOrder::where('status', 'rejected')
            ->count();

        $orders = PurchaseOrder::whereIn('status', $receivedStatuses)
            ->when($status && in_array($status, $receivedStatuses, true), fn ($query) => $query->where('status', $status))
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
            'products.*.selected' => 'sometimes|accepted',
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
                'order_number'            => $orderNumber,
                'supplier_id'             => $supplier->id,
                'supplier_name'           => $supplier->name,
                'status'                  => 'pending approval',
                'expected_delivery_date'  => $validated['expected_delivery_date'] ?? null,
                'notes'                   => $validated['notes'] ?? null,
                'total_amount'            => $totalAmount,
            ]);

            $purchaseOrder->items()->createMany($selectedProducts->toArray());

            // Ensure supplier_products pivot is up to date for each ordered product
            foreach ($selectedProducts as $item) {
                if (! empty($item['product_id'])) {
                    DB::table('supplier_products')->insertOrIgnore([
                        'supplier_id' => $supplier->id,
                        'product_id'  => $item['product_id'],
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        });

        $this->notifyAdminsOfNewPurchaseOrder($purchaseOrder);

        return redirect()->route('order.management')->with('success', 'Purchase order was created successfully. Waiting for admin approval.');
    }

    public function filteredSuppliers(Request $request): \Illuminate\Http\JsonResponse
    {
        $productIds = array_values(array_filter(array_map('intval', (array) $request->input('product_ids', []))));

        if (empty($productIds)) {
            return response()->json(['suppliers' => [], 'message' => 'Select at least one product first.']);
        }

        // Only include suppliers that supply EVERY selected product
        $supplierIds = DB::table('supplier_products')
            ->whereIn('product_id', $productIds)
            ->select('supplier_id')
            ->groupBy('supplier_id')
            ->havingRaw('COUNT(DISTINCT product_id) = ?', [count($productIds)])
            ->pluck('supplier_id');

        if ($supplierIds->isEmpty()) {
            return response()->json([
                'suppliers' => [],
                'message'   => 'No supplier can fulfill all selected products. Please select another supplier or split the purchase order.',
            ]);
        }

        $suppliers = Supplier::whereIn('id', $supplierIds)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'contact_person', 'email', 'phone']);

        return response()->json(['suppliers' => $suppliers, 'message' => null]);
    }

    public function supplierDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        $supplierId = (int) $request->input('supplier_id');
        $productIds = array_values(array_filter(array_map('intval', (array) $request->input('product_ids', []))));

        if (! $supplierId || empty($productIds)) {
            return response()->json(['error' => 'Invalid parameters.'], 422);
        }

        $supplier = Supplier::find($supplierId);
        if (! $supplier) {
            return response()->json(['error' => 'Supplier not found.'], 404);
        }

        $lastPO = PurchaseOrder::where('supplier_id', $supplierId)
            ->whereIn('status', ['completed', 'partially received'])
            ->latest('completed_at')
            ->first();

        $totalOrders    = PurchaseOrder::where('supplier_id', $supplierId)->count();
        $deliveredOrders = PurchaseOrder::where('supplier_id', $supplierId)->where('status', 'completed')->count();
        $reliabilityScore = $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100) : null;

        $priceHistories = [];
        foreach ($productIds as $productId) {
            $product = \App\Models\Product::find($productId);
            if (! $product) {
                continue;
            }

            $histories = SupplierPriceHistory::where('supplier_id', $supplierId)
                ->where('product_id', $productId)
                ->with('purchaseOrder:id,order_number')
                ->latest()
                ->take(10)
                ->get();

            $currentCost  = $histories->first()?->supplier_cost;
            $previousCost = $histories->skip(1)->first()?->supplier_cost;

            $changePercentage = null;
            $trend = 'stable';
            if ($currentCost !== null && $previousCost !== null && $previousCost > 0) {
                $changePercentage = round((((float) $currentCost - (float) $previousCost) / (float) $previousCost) * 100, 2);
                if ($changePercentage > 0.005) {
                    $trend = 'increasing';
                } elseif ($changePercentage < -0.005) {
                    $trend = 'decreasing';
                }
            }

            $recommendation = match ($trend) {
                'increasing' => 'Supplier cost has increased. Review the suggested retail price to maintain your target profit margin.',
                'decreasing' => 'Supplier cost has decreased. Maintaining the current retail price will increase your profit margin.',
                default      => 'Supplier pricing is stable. Maintain the current retail price.',
            };

            $priceHistories[] = [
                'product_id'        => $productId,
                'product_name'      => $product->product_name ?? $product->name,
                'current_cost'      => $currentCost !== null ? (float) $currentCost : null,
                'previous_cost'     => $previousCost !== null ? (float) $previousCost : null,
                'change_percentage' => $changePercentage,
                'trend'             => $trend,
                'recommendation'    => $recommendation,
                'histories'         => $histories->map(fn ($h) => [
                    'date'      => $h->created_at?->format('M j, Y'),
                    'cost'      => (float) $h->supplier_cost,
                    'po_number' => $h->purchaseOrder?->order_number,
                ])->values(),
            ];
        }

        return response()->json([
            'supplier' => [
                'id'                 => $supplier->id,
                'name'               => $supplier->name,
                'contact_person'     => $supplier->contact_person,
                'email'              => $supplier->email,
                'phone'              => $supplier->phone,
                'last_purchase_date' => $lastPO?->completed_at?->format('M j, Y')
                    ?? $lastPO?->updated_at?->format('M j, Y'),
                'reliability_score'  => $reliabilityScore,
                'total_orders'       => $totalOrders,
            ],
            'price_histories' => $priceHistories,
        ]);
    }

    public function supplierComparison(Request $request): \Illuminate\Http\JsonResponse
    {
        $productIds = array_values(array_filter(array_map('intval', (array) $request->input('product_ids', []))));

        if (empty($productIds)) {
            return response()->json(['comparison' => [], 'recommended' => null]);
        }

        $supplierIds = DB::table('supplier_products')
            ->whereIn('product_id', $productIds)
            ->select('supplier_id')
            ->groupBy('supplier_id')
            ->havingRaw('COUNT(DISTINCT product_id) = ?', [count($productIds)])
            ->pluck('supplier_id');

        if ($supplierIds->isEmpty()) {
            return response()->json(['comparison' => [], 'recommended' => null]);
        }

        $comparison = [];
        foreach ($supplierIds as $supplierId) {
            $supplier = Supplier::find($supplierId);
            if (! $supplier || $supplier->status !== 'active') {
                continue;
            }

            $totalCurrentCost = 0;
            $hasHistory       = false;
            $latestPODate     = null;
            $changes          = [];

            foreach ($productIds as $productId) {
                $histories = SupplierPriceHistory::where('supplier_id', $supplierId)
                    ->where('product_id', $productId)
                    ->latest()
                    ->take(2)
                    ->get();

                $current  = $histories->first();
                $previous = $histories->skip(1)->first();

                if ($current) {
                    $hasHistory = true;
                    $totalCurrentCost += (float) $current->supplier_cost;
                    if (! $latestPODate || $current->created_at > $latestPODate) {
                        $latestPODate = $current->created_at;
                    }
                    if ($previous && $previous->supplier_cost > 0) {
                        $changes[] = (((float) $current->supplier_cost - (float) $previous->supplier_cost) / (float) $previous->supplier_cost) * 100;
                    }
                }
            }

            $avgChange = count($changes) > 0 ? round(array_sum($changes) / count($changes), 2) : 0;

            $comparison[] = [
                'supplier_id'          => $supplierId,
                'supplier_name'        => $supplier->name,
                'latest_total_cost'    => $totalCurrentCost,
                'avg_change_percentage' => $avgChange,
                'last_purchase_date'   => $latestPODate?->format('M j, Y'),
                'has_history'          => $hasHistory,
            ];
        }

        usort($comparison, fn ($a, $b) => $a['latest_total_cost'] <=> $b['latest_total_cost']);

        // Recommend: lowest cost supplier
        $recommended = ! empty($comparison) ? $comparison[0] : null;

        $recommendedPayload = null;
        if ($recommended) {
            $reasons = ['Lowest current supplier cost'];
            if (abs($recommended['avg_change_percentage']) <= 5) {
                $reasons[] = 'Stable pricing';
            }
            if ($recommended['last_purchase_date']) {
                $reasons[] = 'Recent transaction history';
            }
            $recommendedPayload = [
                'id'      => $recommended['supplier_id'],
                'name'    => $recommended['supplier_name'],
                'reasons' => $reasons,
            ];
        }

        return response()->json([
            'comparison'  => $comparison,
            'recommended' => $recommendedPayload,
        ]);
    }

    private function notifyAdminsOfNewPurchaseOrder(PurchaseOrder $purchaseOrder): void
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            $key = $this->adminNotificationCacheKey($admin->id);
            $notifications = Cache::get($key, []);

            if (! is_array($notifications)) {
                $notifications = [];
            }

            $notifications[] = [
                'id' => Str::uuid()->toString(),
                'order_id' => $purchaseOrder->id,
                'order_number' => $purchaseOrder->order_number,
                'message' => 'New Purchase Order Submitted – Approval Required.',
                'url' => route('order.show', $purchaseOrder),
            ];

            Cache::put($key, $notifications, now()->addMinutes(10));
        }
    }

    private function adminNotificationCacheKey(int $adminId): string
    {
        return "admin_purchase_order_notifications:{$adminId}";
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items', 'supplier']);

        return view('purchase_order.order-detail', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending approval') {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only pending approval orders can be approved.');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order approved.');
    }

    public function reject(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['pending approval', 'approved'])) {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only pending approval or approved orders can be rejected.');
        }

        $purchaseOrder->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order rejected.');
    }

    public function sendToSupplier(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'approved') {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only approved orders can be sent to the supplier.');
        }

        $purchaseOrder->update([
            'status' => 'sent to supplier',
            'sent_to_supplier_at' => now(),
        ]);

        if ($purchaseOrder->supplier && $purchaseOrder->supplier->email) {
            Mail::to($purchaseOrder->supplier->email)->send(new PurchaseOrderSentMail($purchaseOrder));

            return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order sent to supplier by email.');
        }

        return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Purchase order marked as sent, but supplier has no email address.');
    }

    public function markInTransit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'sent to supplier') {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only orders sent to supplier can be marked in transit.');
        }

        $purchaseOrder->update([
            'status' => 'in transit',
            'in_transit_at' => now(),
        ]);

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order marked as in transit.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->loadMissing('items');
        $hasOutstandingItems = $purchaseOrder->items->contains(fn ($item) => (int) $item->received_quantity < (int) $item->quantity);

        if (! $hasOutstandingItems || in_array($purchaseOrder->status, ['rejected', 'cancelled'], true)) {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only purchase orders with outstanding quantities can be received.');
        }

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:purchase_order_items,id',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.received_quantity' => 'required|integer|min:0',
        ]);

        $receivedSomething = false;

        DB::transaction(function () use ($purchaseOrder, $validated, &$receivedSomething) {
            $receivedData = collect($validated['items'])->keyBy('item_id');

            foreach ($purchaseOrder->items as $item) {
                if (! isset($receivedData[$item->id])) {
                    continue;
                }

                $currentReceived = (int) ($item->received_quantity ?? 0);
                $remainingQuantity = max(0, $item->quantity - $currentReceived);
                $quantityChange = min($remainingQuantity, (int) $receivedData[$item->id]['received_quantity']);

                if ($quantityChange <= 0) {
                    continue;
                }

                // Update unit price if provided
                if (isset($receivedData[$item->id]['unit_price'])) {
                    $item->update(['unit_price' => $receivedData[$item->id]['unit_price']]);
                }

                $item->update(['received_quantity' => $currentReceived + $quantityChange]);
                $receivedSomething = true;

                $product = $item->product;
                if ($product) {
                    // Get the previous supplier cost from SupplierPriceHistory first
                    $previousCostRecord = SupplierPriceHistory::where('product_id', $product->id)
                        ->where('supplier_id', $purchaseOrder->supplier_id)
                        ->latest()
                        ->first();
                    
                    $previousCost = $previousCostRecord?->supplier_cost;
                    
                    // If no SupplierPriceHistory exists or previous_cost is null, check previous purchase orders from same supplier
                    if ($previousCost === null) {
                        $previousPoItem = PurchaseOrderItem::whereHas('purchaseOrder', function ($query) use ($purchaseOrder) {
                            $query->where('supplier_id', $purchaseOrder->supplier_id)
                                ->where('id', '!=', $purchaseOrder->id);
                        })
                            ->where('product_id', $product->id)
                            ->where('received_quantity', '>', 0)
                            ->latest()
                            ->first();
                        
                        $previousCost = $previousPoItem?->unit_price;
                    }
                    
                    $currentCost = (float) $item->unit_price;
                    $changePercentage = $previousCost && $previousCost > 0
                        ? round((($currentCost - (float) $previousCost) / (float) $previousCost) * 100, 2)
                        : 0;

                    $recommendation = match (true) {
                        $currentCost > $previousCost => 'Increase retail price',
                        $currentCost < $previousCost => 'Maintain or lower retail price',
                        default => 'Maintain current retail price',
                    };

                    $reason = match (true) {
                        $currentCost > $previousCost => 'Supplier cost increased while maintaining the desired profit margin.',
                        $currentCost < $previousCost => 'Supplier cost decreased, allowing for higher profit or more competitive pricing.',
                        default => 'Supplier cost has not changed.',
                    };

                    $targetProfitMargin = 0.30;
                    $suggestedRetailPrice = $currentCost > 0
                        ? round($currentCost / (1 - $targetProfitMargin), 2)
                        : 0;

                    SupplierPriceHistory::create([
                        'product_id' => $product->id,
                        'supplier_id' => $purchaseOrder->supplier_id,
                        'purchase_order_id' => $purchaseOrder->id,
                        'previous_cost' => $previousCost ?: null,
                        'supplier_cost' => $currentCost,
                        'change_percentage' => $changePercentage,
                        'recommendation' => $recommendation,
                        'reason' => $reason,
                        'suggested_retail_price' => $suggestedRetailPrice,
                    ]);

                    $product->increment('stock_quantity', $quantityChange);
                    $product->update(['last_restock_date' => now()]);

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'restock',
                        'quantity_change' => $quantityChange,
                        'unit_price' => $item->unit_price,
                        'supplier_name' => $purchaseOrder->supplier_name,
                        'notes' => 'Received from purchase order ' . $purchaseOrder->order_number,
                        'metadata' => [
                            'purchase_order_id' => $purchaseOrder->id,
                            'purchase_order_item_id' => $item->id,
                        ],
                    ]);
                }
            }

            $purchaseOrder->load('items');
            $allItemsReceived = $purchaseOrder->items->every(fn ($item) => (int) $item->received_quantity >= (int) $item->quantity);
            $anyItemsReceived = $purchaseOrder->items->contains(fn ($item) => (int) $item->received_quantity > 0);

            $purchaseOrder->update([
                'status' => $allItemsReceived ? 'completed' : ($anyItemsReceived ? 'partially received' : $purchaseOrder->status),
                'completed_at' => $allItemsReceived ? now() : null,
            ]);
        });

        if (! $receivedSomething) {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'No new quantities were received.');
        }

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order receipt recorded and inventory updated.');
    }
}
