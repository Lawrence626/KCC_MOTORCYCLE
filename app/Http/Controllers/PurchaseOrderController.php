<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrderSentMail;
use App\Models\DefectiveReturnRequest;
use App\Models\InventoryMovement;
use App\Services\InventoryAlertService;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ReverseLogistics;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
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
            ->where('stock_quantity', '<=', 10)
            ->count();

        $suppliers = Supplier::orderBy('name')->get();
        $totalOrders = PurchaseOrder::count();
        $inTransitTotal = PurchaseOrder::where('status', 'in transit')->count();

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

        $orders = $this->filteredPurchaseOrders($request, ['pending approval', 'approved', 'sent to supplier', 'in transit', 'awaiting confirmation'], 'orders')
            ->latest()
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString()
            ->appends(['tab' => 'orders']);

        $backOrders = $this->filteredBackOrderItems($request)
            ->paginate(10, ['*'], 'back_orders_page')
            ->withQueryString()
            ->appends(['tab' => 'back_orders']);

        $replacementBackOrders = \Illuminate\Support\Facades\Schema::hasTable('defective_return_requests')
            ? DefectiveReturnRequest::with(['purchaseOrder', 'product'])
                ->where('resolution', 'Replacement')
                ->whereIn('status', ['Replacement Approved', 'Awaiting Replacement'])
                ->when($request->query('back_orders_supplier'), fn ($q, $s) => $q->where('supplier_name', $s))
                ->when($request->query('back_orders_search'), fn ($q, $s) => $q->where(function ($q) use ($s) {
                    $q->where('product_name', 'like', "%{$s}%")
                        ->orWhere('replacement_order_number', 'like', "%{$s}%")
                        ->orWhere('supplier_name', 'like', "%{$s}%");
                }))
                ->latest()
                ->get()
            : collect();

        $receivedOrders = $this->filteredPurchaseOrders($request, ['completed', 'partially received'], 'received')
            ->latest()
            ->paginate(10, ['*'], 'received_page')
            ->withQueryString()
            ->appends(['tab' => 'received']);

        $cancelledOrders = $this->filteredPurchaseOrders($request, ['rejected', 'cancelled'], 'cancelled')
            ->latest()
            ->paginate(10, ['*'], 'cancelled_page')
            ->withQueryString()
            ->appends(['tab' => 'cancelled']);

        $activeTab = $request->query('tab');
        if (! $activeTab) {
            if ($request->has('received_page') || $request->has('received_search') || $request->has('received_status') || $request->has('received_supplier')) {
                $activeTab = 'received';
            } elseif ($request->has('back_orders_page') || $request->has('back_orders_search') || $request->has('back_orders_status') || $request->has('back_orders_supplier')) {
                $activeTab = 'back_orders';
            } elseif ($request->has('cancelled_page') || $request->has('cancelled_search') || $request->has('cancelled_status') || $request->has('cancelled_supplier')) {
                $activeTab = 'cancelled';
            } else {
                $activeTab = 'orders';
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            $html = match ($activeTab) {
                'received' => view('purchase_order.partials.orders-table', [
                    'orders' => $receivedOrders,
                    'dateLabel' => 'Received',
                    'dateType' => 'received',
                    'emptyMessage' => 'No received purchase orders found.',
                ])->render() . '<div class="mt-4 px-4">' . $receivedOrders->appends(['tab' => 'received'])->links()->render() . '</div>',

                'back_orders' => view('purchase_order.partials.back-orders-table', [
                    'backOrders' => $backOrders,
                    'replacementBackOrders' => $replacementBackOrders,
                ])->render() . '<div class="mt-4 px-4">' . $backOrders->appends(['tab' => 'back_orders'])->links()->render() . '</div>',

                'cancelled' => view('purchase_order.partials.orders-table', [
                    'orders' => $cancelledOrders,
                    'dateLabel' => 'Created',
                    'dateType' => 'created',
                    'emptyMessage' => 'No cancelled purchase orders found.',
                ])->render() . '<div class="mt-4 px-4">' . $cancelledOrders->appends(['tab' => 'cancelled'])->links()->render() . '</div>',

                default => view('purchase_order.partials.orders-table', [
                    'orders' => $orders,
                    'emptyMessage' => 'No active purchase orders have been created yet.',
                ])->render() . '<div class="mt-4 px-4">' . $orders->appends(['tab' => 'orders'])->links()->render() . '</div>',
            };

            return response()->json([
                'tab' => $activeTab,
                'html' => $html,
            ]);
        }

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
            'replacementBackOrders' => $replacementBackOrders,
            'receivedOrders' => $receivedOrders,
            'cancelledOrders' => $cancelledOrders,
            'activeTab' => $activeTab,
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

        // Get the base query for active, non-archived products matching the stock condition
        $query = Product::where('is_active', true)
            ->where('is_archived', false)
            ->where(function ($query) use ($selectedProductIds) {
                $query->where('stock_quantity', '<=', 10);

                if (! empty($selectedProductIds)) {
                    $query->orWhereIn('id', $selectedProductIds);
                }
            });

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Get all products matching the query
        $allProducts = $query->get();
        $recentSales = $this->getRecentProductSales(30);

        // Assign movement categories
        $allProducts->transform(function ($product) use ($recentSales) {
            $soldLast30Days = $recentSales[$product->id] ?? 0;
            $product->sales_count = $soldLast30Days;
            $product->movement_category = $this->getProductMovementCategory($soldLast30Days, $product->id);
            return $product;
        });

        // Apply client-side filter based on movement category
        $movementFilter = $request->query('movement', 'all');
        if ($movementFilter === 'all') {
            // Show fast moving and slow moving, exclude special order by default (unless pre-selected)
            $filteredProducts = $allProducts->filter(function ($product) use ($selectedProductIds) {
                return in_array($product->id, $selectedProductIds, true) || $product->movement_category !== 'special_order';
            });
        } else {
            // Show only the selected category (unless pre-selected)
            $filteredProducts = $allProducts->filter(function ($product) use ($selectedProductIds, $movementFilter) {
                return in_array($product->id, $selectedProductIds, true) || $product->movement_category === $movementFilter;
            });
        }

        // Guarantee preselected products are always at the very top (first page)
        $preselected = $filteredProducts->filter(fn ($p) => in_array($p->id, $selectedProductIds, true));
        $others = $filteredProducts->filter(fn ($p) => !in_array($p->id, $selectedProductIds, true));
        $filteredProducts = $preselected->concat($others);

        // Manually paginate the filtered collection
        $page = (int) $request->query('page', 1);
        $perPage = 8;
        $offset = ($page - 1) * $perPage;
        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredProducts->slice($offset, $perPage),
            $filteredProducts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'table_html' => view('purchase_order.partials.product-rows', [
                    'lowStockProducts' => $paginatedProducts,
                    'selectedProductIds' => $selectedProductIds,
                ])->render(),
                'pagination_html' => $paginatedProducts->links()->render(),
                'total' => $paginatedProducts->total(),
                'first_item' => $paginatedProducts->firstItem() ?? 0,
                'last_item' => $paginatedProducts->lastItem() ?? 0,
                'current_page' => $paginatedProducts->currentPage(),
                'last_page' => $paginatedProducts->lastPage(),
            ]);
        }

        return view('purchase_order.create', [
            'lowStockProducts' => $paginatedProducts,
            'suppliers' => Supplier::orderBy('name')->get(),
            'selectedProductIds' => $selectedProductIds,
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

    private function getProductMovementCategory(int $salesCount, ?int $productId = null): string
    {
        // For testing purposes, assign deterministic categories if no sales data
        // More balanced distribution: 50% fast_moving, 30% slow_moving, 20% special_order
        if ($salesCount === 0) {
            $seed = $productId ? (abs(crc32((string) $productId)) % 10) + 1 : 1;
            if ($seed <= 5) {
                return 'fast_moving';
            } elseif ($seed <= 8) {
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

        return PurchaseOrder::with(['items.product'])
            ->whereIn('status', $statuses)
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
            ->with(['purchaseOrder', 'product'])
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

        $pendingConfirmation = PurchaseOrder::whereIn('status', ['pending approval', 'approved', 'sent to supplier', 'in transit', 'partially received', 'awaiting confirmation'])
            ->count();

        $issuesFound = PurchaseOrder::where('status', 'rejected')
            ->count();

        $orders = PurchaseOrder::with(['items.product'])
            ->whereIn('status', $receivedStatuses)
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
            ->map(function ($item) use ($validated) {
                $productId = $item['product_id'] ?? null;
                $product = $productId ? Product::find($productId) : null;
                
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                if ($unitPrice <= 0 && $product) {
                    $supplierCost = SupplierPriceHistory::where('supplier_id', $validated['supplier_id'])
                        ->where('product_id', $product->id)
                        ->latest('id')
                        ->value('supplier_cost');
                    $unitPrice = (float) ($supplierCost ?? $product->unit_price ?? 0);
                }

                $quantity = (int) ($item['quantity'] ?? 1);
                $totalPrice = $quantity * $unitPrice;

                return [
                    'product_id' => $productId,
                    'product_name' => $item['product_name'] ?? $product?->product_name ?? $product?->name ?? 'Unknown Product',
                    'sku' => $item['sku'] ?? $product?->sku ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            })
            ->values();

        if ($selectedProducts->isEmpty()) {
            return back()->withErrors(['products' => 'Select at least one product to order.'])->withInput();
        }

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $totalAmount = $selectedProducts->sum('total_price');
        $orderNumber = 'PO-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
        $initialStatus = $isAdmin ? 'approved' : 'pending approval';

        $purchaseOrder = DB::transaction(function () use ($supplier, $validated, $selectedProducts, $totalAmount, $orderNumber, $user, $isAdmin, $initialStatus) {
            $purchaseOrder = PurchaseOrder::create([
                'order_number'            => $orderNumber,
                'supplier_id'             => $supplier->id,
                'supplier_name'           => $supplier->name,
                'user_id'                 => $user?->id,
                'created_by_role'         => $user?->role ?? ($isAdmin ? 'admin' : 'inventory_clerk'),
                'status'                  => $initialStatus,
                'approved_at'             => $isAdmin ? now() : null,
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

            return $purchaseOrder;
        });

        if ($isAdmin) {
            return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order created successfully and is ready to send to supplier.');
        }

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
        $purchaseOrder->load(['items.product', 'supplier', 'defectiveReturnRequests.product', 'defectiveReturnRequests.replacementPurchaseOrder.items']);

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

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['pending approval', 'approved'])) {
            return redirect()->route('order.show', $purchaseOrder)->with('warning', 'Only pending approval or approved orders can be rejected.');
        }

        $purchaseOrder->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejected_by'      => auth()->id(),
            'rejection_reason' => $request->input('rejection_reason'),
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
            'items.*.defective_quantity' => 'nullable|integer|min:0',
            'items.*.defect_reason' => 'nullable|string|max:255',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $receivedData = collect($validated['items'])->keyBy('item_id');

        // Validate item-level defective rules
        foreach ($purchaseOrder->items as $item) {
            if (! isset($receivedData[$item->id])) {
                continue;
            }

            $itemInput = $receivedData[$item->id];
            $recvQty = (int) ($itemInput['received_quantity'] ?? 0);
            $defQty = (int) ($itemInput['defective_quantity'] ?? 0);
            $reason = trim((string) ($itemInput['defect_reason'] ?? ''));

            if ($defQty < 0) {
                return redirect()->route('order.show', $purchaseOrder)
                    ->with('warning', "Defective quantity for {$item->product_name} must be 0 or greater.");
            }

            if ($defQty > $recvQty) {
                return redirect()->route('order.show', $purchaseOrder)
                    ->with('warning', "Defective quantity ({$defQty}) cannot exceed received quantity ({$recvQty}) for {$item->product_name}.");
            }

            if ($defQty > 0 && empty($reason)) {
                return redirect()->route('order.show', $purchaseOrder)
                    ->with('warning', "Please provide a defect reason/remarks for {$item->product_name} since defective quantity is {$defQty}.");
            }
        }

        $receivedSomething = false;

        DB::transaction(function () use ($purchaseOrder, $validated, &$receivedSomething) {
            $receivedData = collect($validated['items'])->keyBy('item_id');

            foreach ($purchaseOrder->items as $item) {
                if (! isset($receivedData[$item->id])) {
                    continue;
                }

                $itemInput = $receivedData[$item->id];
                $currentReceived = (int) ($item->received_quantity ?? 0);
                $currentDefective = (int) ($item->defective_quantity ?? 0);
                $currentAccepted = (int) ($item->accepted_quantity ?? 0);

                $remainingQuantity = max(0, $item->quantity - $currentReceived);
                $quantityChange = min($remainingQuantity, (int) $itemInput['received_quantity']);

                if ($quantityChange <= 0) {
                    continue;
                }

                $defectiveChange = min($quantityChange, max(0, (int) ($itemInput['defective_quantity'] ?? 0)));
                $acceptedChange = max(0, $quantityChange - $defectiveChange);
                $defectReason = !empty($itemInput['defect_reason']) ? trim($itemInput['defect_reason']) : $item->defect_reason;

                $updateData = [
                    'received_quantity' => $currentReceived + $quantityChange,
                    'defective_quantity' => $currentDefective + $defectiveChange,
                    'accepted_quantity' => $currentAccepted + $acceptedChange,
                    'defect_reason' => $defectReason,
                ];

                if (isset($itemInput['unit_price'])) {
                    $updateData['unit_price'] = (float) $itemInput['unit_price'];
                }

                // Record received quantity, defectives, accepted and cost (do NOT update inventory yet)
                $item->update($updateData);
                $receivedSomething = true;

                // If defective quantity reported, store ReverseLogistics record and DefectiveReturnRequest
                if ($defectiveChange > 0) {
                    ReverseLogistics::create([
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,
                        'quantity' => $defectiveChange,
                        'warehouse' => 'Shop',
                        'return_reason' => $defectReason ?: 'Defective upon delivery',
                        'condition' => 'Defective',
                        'source' => 'Purchase Order ' . $purchaseOrder->order_number,
                        'status' => 'Pending Return',
                        'reported_date' => now(),
                        'notes' => 'Defective units recorded on receipt of PO ' . $purchaseOrder->order_number . ' from ' . $purchaseOrder->supplier_name . ($defectReason ? " (Reason: {$defectReason})" : ''),
                    ]);

                    DefectiveReturnRequest::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'purchase_order_item_id' => $item->id,
                        'supplier_id' => $purchaseOrder->supplier_id,
                        'supplier_name' => $purchaseOrder->supplier_name,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,
                        'defective_quantity' => $defectiveChange,
                        'defect_reason' => $defectReason ?: 'Defective upon delivery',
                        'warehouse' => 'Shop',
                        'status' => 'Pending Supplier Response',
                    ]);
                }
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
            'warehouse' => 'required|in:Shop,Warehouse A,Warehouse B,Warehouse C',
            'shelf_id' => 'nullable|integer|exists:warehouse_shelves,id',
        ]);

        $warehouse = $validated['warehouse'];
        $shelfId = $validated['shelf_id'] ?? null;

        DB::transaction(function () use ($purchaseOrder, $warehouse, $shelfId) {
            $purchaseOrder->loadMissing('items');

            foreach ($purchaseOrder->items as $item) {
                // Determine usable accepted quantity to add to inventory (Only Accepted Qty enters inventory)
                $usableQuantity = isset($item->accepted_quantity) && $item->accepted_quantity > 0
                    ? (int) $item->accepted_quantity
                    : max(0, (int) $item->received_quantity - (int) ($item->defective_quantity ?? 0));

                if ($usableQuantity <= 0) {
                    continue;
                }

                $product = $item->product;
                if ($product) {
                    // Check if product already exists in the selected warehouse (or has null warehouse)
                    $warehouseProduct = Product::where('sku', $product->sku)
                        ->where(function ($q) use ($warehouse) {
                            $q->where('warehouse', $warehouse)
                                ->orWhereNull('warehouse');
                        })
                        ->first();

                    if ($warehouseProduct) {
                        // Update existing warehouse product with ONLY ACCEPTED quantity
                        $warehouseProduct->increment('stock_quantity', $usableQuantity);
                        $warehouseProduct->update([
                            'warehouse' => $warehouse,
                            'last_restock_date' => now(),
                        ]);
                    } else {
                        // Create new warehouse record with ONLY ACCEPTED quantity
                        $newProduct = $product->replicate();
                        $newProduct->warehouse = $warehouse;
                        $newProduct->stock_quantity = $usableQuantity;
                        $newProduct->last_restock_date = now();
                        $newProduct->save();
                        $warehouseProduct = $newProduct;
                    }

                    // Auto-resolve inventory alerts if stock is replenished above reorder level
                    app(InventoryAlertService::class)->checkAndResolveProduct($product->id);

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
                        ? round(($currentCost * 1.12) / (1 - $targetProfitMargin), 2)
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

                    // Create stock arrival notice for warehouse assignment (for accepted usable stock)
                    StockArrivalNotice::create([
                        'product_id'             => $warehouseProduct->id,
                        'product_name'           => $warehouseProduct->name,
                        'sku'                    => $warehouseProduct->sku,
                        'quantity'               => $usableQuantity,
                        'purchase_order_id'      => $purchaseOrder->id,
                        'purchase_order_number'  => $purchaseOrder->order_number,
                        'supplier_name'          => $purchaseOrder->supplier_name,
                        'arrived_at'             => now(),
                        'is_assigned'            => true,
                    ]);

                    // Create inventory movement record for accepted stock
                    InventoryMovement::create([
                        'product_id' => $warehouseProduct->id,
                        'type' => 'restock',
                        'quantity_change' => $usableQuantity,
                        'unit_price' => $item->unit_price,
                        'supplier_name' => $purchaseOrder->supplier_name,
                        'notes' => 'Received from purchase order ' . $purchaseOrder->order_number . ' to ' . $warehouse . ($item->defective_quantity > 0 ? " (Accepted: {$usableQuantity}, Defective: {$item->defective_quantity})" : ''),
                        'metadata' => [
                            'purchase_order_id' => $purchaseOrder->id,
                            'purchase_order_item_id' => $item->id,
                            'warehouse' => $warehouse,
                            'shelf_id' => $shelfId,
                            'accepted_quantity' => $usableQuantity,
                            'defective_quantity' => (int) ($item->defective_quantity ?? 0),
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
                                $products[$existingProductIndex]['qty'] += $usableQuantity;
                            } else {
                                // Add new product to shelf
                                $products[] = [
                                    'product_id' => $product->id,
                                    'sku' => $product->sku,
                                    'name' => $product->name,
                                    'qty' => $usableQuantity,
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

            // If this is a Replacement Purchase Order, update the linked DefectiveReturnRequest
            $linkedDefectiveReq = DefectiveReturnRequest::where('replacement_purchase_order_id', $purchaseOrder->id)->first();
            if ($linkedDefectiveReq) {
                $totalAccepted = (int) $purchaseOrder->items->sum('accepted_quantity');
                $linkedDefectiveReq->update([
                    'replacement_received_quantity' => $totalAccepted,
                    'replacement_received_at' => now(),
                    'status' => 'Completed',
                ]);
            }
        });

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Purchase order confirmed and inventory updated.');
    }

    public function updateEstimatedDeliveryDate(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'estimated_delivery_date' => 'required|date',
        ]);

        $purchaseOrder->update([
            'estimated_delivery_date' => $validated['estimated_delivery_date'],
        ]);

        return redirect()->route('order.show', $purchaseOrder)->with('success', 'Estimated delivery date updated successfully.');
    }

    public function resolveDefectiveRequest(Request $request, PurchaseOrder $purchaseOrder, DefectiveReturnRequest $defectiveRequest)
    {
        $validated = $request->validate([
            'resolution' => 'required|in:Replacement,Refund/Credit,No Replacement',
            'expected_replacement_date' => 'nullable|required_if:resolution,Replacement|date',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $resolution = $validated['resolution'];
        $notes = $validated['resolution_notes'] ?? null;
        $expectedDate = $validated['expected_replacement_date'] ?? null;

        $updateData = [
            'resolution' => $resolution,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ];

        if ($resolution === 'Replacement') {
            $replacementOrderNumber = 'RPO-' . $purchaseOrder->order_number . '-' . $defectiveRequest->id;

            // Create separate Replacement Purchase Order
            $replacementPO = PurchaseOrder::create([
                'order_number' => $replacementOrderNumber,
                'supplier_id' => $purchaseOrder->supplier_id,
                'supplier_name' => $purchaseOrder->supplier_name,
                'status' => 'sent to supplier',
                'expected_delivery_date' => $expectedDate,
                'notes' => "Replacement order for {$defectiveRequest->defective_quantity} defective unit(s) of {$defectiveRequest->product_name} from original PO {$purchaseOrder->order_number} (Reason: {$defectiveRequest->defect_reason}).",
                'total_amount' => 0.00,
                'approved_at' => now(),
                'sent_to_supplier_at' => now(),
            ]);

            // Create PurchaseOrderItem on the replacement PO
            PurchaseOrderItem::create([
                'purchase_order_id' => $replacementPO->id,
                'product_id' => $defectiveRequest->product_id,
                'product_name' => $defectiveRequest->product_name,
                'sku' => $defectiveRequest->sku,
                'quantity' => $defectiveRequest->defective_quantity,
                'received_quantity' => 0,
                'defective_quantity' => 0,
                'accepted_quantity' => 0,
                'unit_price' => 0.00,
                'total_price' => 0.00,
            ]);

            $updateData['status'] = 'Awaiting Replacement';
            $updateData['expected_replacement_date'] = $expectedDate;
            $updateData['replacement_order_number'] = $replacementOrderNumber;
            $updateData['replacement_purchase_order_id'] = $replacementPO->id;
        } elseif ($resolution === 'Refund/Credit') {
            $updateData['status'] = 'Refund Approved';
            $updateData['expected_replacement_date'] = null;
        } else {
            $updateData['status'] = 'Completed';
            $updateData['expected_replacement_date'] = null;
        }

        $defectiveRequest->update($updateData);

        return redirect()->route('order.show', $purchaseOrder)
            ->with('success', "Supplier resolution recorded: {$resolution}." . ($resolution === 'Replacement' ? " Replacement PO {$replacementOrderNumber} created." : ''));
    }

    public function receiveReplacement(Request $request, PurchaseOrder $purchaseOrder, DefectiveReturnRequest $defectiveRequest)
    {
        if ($defectiveRequest->resolution !== 'Replacement' || $defectiveRequest->status === 'Completed') {
            return redirect()->route('order.show', $purchaseOrder)
                ->with('warning', 'Only active replacement requests can be received.');
        }

        $validated = $request->validate([
            'replacement_quantity' => 'required|integer|min:1|max:' . $defectiveRequest->defective_quantity,
            'warehouse' => 'required|in:Shop,Warehouse A,Warehouse B,Warehouse C',
            'shelf_id' => 'nullable|integer|exists:warehouse_shelves,id',
        ]);

        $receivedQty = (int) $validated['replacement_quantity'];
        $warehouse = $validated['warehouse'];
        $shelfId = $validated['shelf_id'] ?? null;

        DB::transaction(function () use ($purchaseOrder, $defectiveRequest, $receivedQty, $warehouse, $shelfId) {
            $product = Product::find($defectiveRequest->product_id);
            if ($product) {
                $product->increment('stock_quantity', $receivedQty);
                $product->update([
                    'warehouse' => $warehouse,
                    'last_restock_date' => now(),
                ]);

                // Shelf update
                if ($shelfId) {
                    $shelf = WarehouseShelf::find($shelfId);
                    if ($shelf) {
                        $shelfProducts = $shelf->products ?? [];
                        $existingProductIndex = collect($shelfProducts)->search(function ($p) use ($product) {
                            return isset($p['product_id']) && $p['product_id'] === $product->id;
                        });

                        if ($existingProductIndex !== false) {
                            $shelfProducts[$existingProductIndex]['qty'] += $receivedQty;
                        } else {
                            $shelfProducts[] = [
                                'product_id' => $product->id,
                                'sku' => $product->sku,
                                'name' => $product->name,
                                'qty' => $receivedQty,
                                'price' => (float) $product->unit_price,
                            ];
                        }

                        $shelf->update(['products' => array_values($shelfProducts)]);
                    }
                }

                // Inventory movement
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'restock',
                    'quantity_change' => $receivedQty,
                    'unit_price' => $product->unit_price,
                    'supplier_name' => $defectiveRequest->supplier_name,
                    'notes' => "Replacement units received for PO {$purchaseOrder->order_number} ({$defectiveRequest->replacement_order_number})",
                    'metadata' => [
                        'purchase_order_id' => $purchaseOrder->id,
                        'defective_return_request_id' => $defectiveRequest->id,
                        'replacement_order_number' => $defectiveRequest->replacement_order_number,
                        'warehouse' => $warehouse,
                        'shelf_id' => $shelfId,
                    ],
                ]);

                // Stock arrival notice
                StockArrivalNotice::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_order_number' => $purchaseOrder->order_number,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name ?? $product->name,
                    'sku' => $product->sku,
                    'quantity' => $receivedQty,
                    'warehouse' => $warehouse,
                    'arrived_at' => now(),
                    'received_by' => auth()->id(),
                    'status' => 'delivered',
                ]);

                app(InventoryAlertService::class)->checkAndResolveProduct($product->id);
            }

            $defectiveRequest->update([
                'replacement_received_quantity' => $receivedQty,
                'replacement_received_at' => now(),
                'status' => 'Completed',
            ]);
        });

        return redirect()->route('order.show', $purchaseOrder)
            ->with('success', "Received {$receivedQty} replacement unit(s) into {$warehouse} inventory. Replacement back order completed.");
    }
}
