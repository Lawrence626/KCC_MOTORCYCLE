<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrderSentMail;
use App\Models\InventoryMovement;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WarehouseShelf;
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
        // Get all active products (not just low stock) for filtering by movement category
        $query = Product::where('is_active', true)
            ->where('is_archived', false);

        // Get all products first to assign movement categories
        $allProducts = $query->get();
        $recentSales = $this->getRecentProductSales(30);

        $allProducts->transform(function ($product) use ($recentSales) {
            $soldLast30Days = $recentSales[$product->id] ?? 0;
            $product->sales_count = $soldLast30Days;
            $product->movement_category = $this->getProductMovementCategory($soldLast30Days);
            return $product;
        });

        // Apply client-side filter based on movement category
        $movementFilter = $request->query('movement', 'all');
        if ($movementFilter === 'all') {
            // Show fast moving and slow moving, exclude special order
            $allProducts = $allProducts->where('movement_category', '!=', 'special_order');
        } else {
            // Show only the selected category
            $allProducts = $allProducts->where('movement_category', $movementFilter);
        }

        // Manually paginate the filtered collection
        $page = $request->query('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $allProducts->slice($offset, $perPage),
            $allProducts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('purchase_order.create', [
            'lowStockProducts' => $paginatedProducts,
            'suppliers' => Supplier::orderBy('name')->get(),
            'currentFilter' => $movementFilter,
        ]);
    }

    private function getRecentProductSales(int $days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        $transactions = POSTransaction::completed()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();

        $productSales = [];

        foreach ($transactions as $transaction) {
            if (!is_array($transaction->items) || empty($transaction->items)) {
                continue;
            }

            foreach ($transaction->items as $item) {
                $productId = $item['id'] ?? null;
                $quantity = (int) ($item['quantity'] ?? 0);

                if (!$productId || $quantity <= 0) {
                    continue;
                }

                $productSales[$productId] = ($productSales[$productId] ?? 0) + $quantity;
            }
        }

        return $productSales;
    }

    private function getProductMovementCategory(int $salesCount): string
    {
        // For testing purposes, assign random categories if no sales data
        // More balanced distribution: 50% fast_moving, 30% slow_moving, 20% special_order
        if ($salesCount === 0) {
            $random = rand(1, 10);
            if ($random <= 5) {
                return 'fast_moving';
            } elseif ($random <= 8) {
                return 'slow_moving';
            }
            return 'special_order';
        }

        if ($salesCount >= 10) {
            return 'fast_moving';
        }

        if ($salesCount >= 1) {
            return 'slow_moving';
        }

        return 'special_order';
    }

    public function history(Request $request)
    {
        $search = $request->query('search');
        $supplier = $request->query('supplier');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $orders = PurchaseOrder::whereIn('status', ['completed', 'archived'])
            ->when($search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            }))
            ->when($supplier, fn ($query, $supplier) => $query->where('supplier_name', $supplier))
            ->when($dateFrom, fn ($query, $dateFrom) => $query->whereDate('completed_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query, $dateTo) => $query->whereDate('completed_at', '<=', $dateTo))
            ->latest('completed_at')
            ->paginate(10)
            ->withQueryString();

        return view('purchase_order.history', [
            'orders' => $orders,
            'suppliers' => Supplier::orderBy('name')->get(),
            'search' => $search,
            'supplierFilter' => $supplier,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
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

        $purchaseOrder = DB::transaction(function () use ($supplier, $validated, $selectedProducts, $totalAmount, $orderNumber) {
            $purchaseOrder = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'status' => 'pending approval',
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
            ]);

            $purchaseOrder->items()->createMany($selectedProducts->toArray());

            return $purchaseOrder;
        });

        $this->notifyAdminsOfNewPurchaseOrder($purchaseOrder);

        return redirect()->route('order.management')->with('success', 'Purchase order was created successfully. Waiting for admin approval.');
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

                // Only record received quantity, do NOT update inventory yet
                $item->update(['received_quantity' => $currentReceived + $quantityChange]);
                $receivedSomething = true;
            }

            $purchaseOrder->load('items');
            $allItemsReceived = $purchaseOrder->items->every(fn ($item) => (int) $item->received_quantity >= (int) $item->quantity);
            $anyItemsReceived = $purchaseOrder->items->contains(fn ($item) => (int) $item->received_quantity > 0);

            $purchaseOrder->update([
                'status' => $allItemsReceived ? 'awaiting confirmation' : ($anyItemsReceived ? 'partially received' : $purchaseOrder->status),
                'completed_at' => $allItemsReceived ? now() : null,
            ]);
        });

        if (! $receivedSomething) {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'No new quantities were received.');
        }

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order receipt recorded. Please confirm to add to inventory.');
    }

    public function confirmReceive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (! in_array($purchaseOrder->status, ['awaiting confirmation', 'partially received'], true)) {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only orders awaiting confirmation can be confirmed.');
        }

        $validated = $request->validate([
            'warehouse_index' => 'required|integer|min:0|max:2',
            'shelf_id' => 'nullable|integer|exists:warehouse_shelves,id',
        ]);

        $warehouseIndex = $validated['warehouse_index'];
        $shelfId = $validated['shelf_id'] ?? null;

        DB::transaction(function () use ($purchaseOrder, $warehouseIndex, $shelfId) {
            $purchaseOrder->loadMissing('items');

            foreach ($purchaseOrder->items as $item) {
                if ((int) $item->received_quantity === 0) {
                    continue;
                }

                $product = $item->product;
                if ($product) {
                    // Update product stock quantity
                    $product->increment('stock_quantity', $item->received_quantity);
                    $product->update(['last_restock_date' => now()]);

                    // Create stock arrival notice for warehouse assignment
                    StockArrivalNotice::create([
                        'product_id'             => $product->id,
                        'product_name'           => $product->name,
                        'sku'                    => $product->sku,
                        'quantity'               => $item->received_quantity,
                        'purchase_order_id'      => $purchaseOrder->id,
                        'purchase_order_number'  => $purchaseOrder->order_number,
                        'supplier_name'          => $purchaseOrder->supplier_name,
                        'arrived_at'             => now(),
                        'is_assigned'            => false,
                    ]);

                    // Create inventory movement record
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'restock',
                        'quantity_change' => $item->received_quantity,
                        'unit_price' => $item->unit_price,
                        'supplier_name' => $purchaseOrder->supplier_name,
                        'notes' => 'Received from purchase order ' . $purchaseOrder->order_number,
                        'metadata' => [
                            'purchase_order_id' => $purchaseOrder->id,
                            'purchase_order_item_id' => $item->id,
                            'warehouse_index' => $warehouseIndex,
                            'shelf_id' => $shelfId,
                        ],
                    ]);

                    // Add to warehouse shelf if specified
                    if ($shelfId) {
                        $shelf = WarehouseShelf::find($shelfId);
                        if ($shelf) {
                            $products = $shelf->products ?? [];
                            $existingProductIndex = collect($products)->search(function ($p) use ($product) {
                                return isset($p['product_id']) && $p['product_id'] === $product->id;
                            });

                            if ($existingProductIndex !== false) {
                                // Update existing product quantity
                                $products[$existingProductIndex]['qty'] += $item->received_quantity;
                            } else {
                                // Add new product to shelf
                                $products[] = [
                                    'product_id' => $product->id,
                                    'sku' => $product->sku,
                                    'name' => $product->name,
                                    'qty' => $item->received_quantity,
                                    'price' => (float) $item->unit_price,
                                ];
                            }

                            $shelf->update(['products' => array_values($products)]);
                        }
                    }
                }
            }

            // Update purchase order status to completed
            $purchaseOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order confirmed and inventory updated.');
    }
}
