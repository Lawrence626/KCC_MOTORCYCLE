<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Services\SupplierPerformanceService;
use App\Models\SynchronizationHistory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockArrivalNotice;
use App\Models\ReverseLogistics;
use App\Models\SupplierPriceHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OfflineReconciliationController extends Controller
{
    protected OfflineReconciliationService $reconciliationService;
    protected SupplierPerformanceService $performanceService;

    public function __construct(
        OfflineReconciliationService $reconciliationService,
        SupplierPerformanceService $performanceService
    ) {
        $this->reconciliationService = $reconciliationService;
        $this->performanceService = $performanceService;
    }

    /**
     * Display offline reconciliation overview
     */
    public function overview(Request $request)
    {
        $query = PurchaseOrder::query()->whereNotNull('sync_status');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        // Filter by sync status
        if ($request->has('sync_status') && $request->sync_status) {
            $query->where('sync_status', $request->sync_status);
        }

        $purchaseOrders = $query->with('items')->latest()->paginate(10)->withQueryString();
        $stats = $this->reconciliationService->getSynchronizationStats();
        $recentHistories = SynchronizationHistory::with(['exportedBy', 'importedBy'])->latest()->take(5)->get();

        return view('offline_reconciliation.offline_recon', [
            'purchaseOrders' => $purchaseOrders,
            'stats' => $stats,
            'recentHistories' => $recentHistories,
        ]);
    }

    /**
     * Display offline purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()->whereNotNull('sync_status');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        // Filter by sync status
        if ($request->has('sync_status') && $request->sync_status) {
            $query->where('sync_status', $request->sync_status);
        }

        $purchaseOrders = $query->with('items')->latest()->paginate(10)->withQueryString();

        // Active products with suppliers relation and productCatalog
        $rawProducts = Product::where('is_active', true)
            ->where('is_archived', false)
            ->with(['suppliers:id,name', 'productCatalog'])
            ->orderBy('name')
            ->get();

        // Product Descriptions from database
        $productDescriptions = \App\Models\ProductDescription::where('is_active', true)->orderBy('name')->get();
        $descMap = $productDescriptions->keyBy(fn($d) => strtolower(trim($d->name)));

        // All active suppliers
        $activeSuppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $suppliersByName = $activeSuppliers->keyBy('name');

        // Orders & history for supplier assessment calculation
        $allOrders = PurchaseOrder::with(['items', 'supplier'])->get();
        $ordersBySupplierName = $allOrders->groupBy('supplier_name');
        $ordersBySupplierId = $allOrders->groupBy('supplier_id');
        $arrivalNotices = StockArrivalNotice::all();
        $reverseLogistics = ReverseLogistics::all();
        $priceHistories = SupplierPriceHistory::all();

        // Compute Supplier Assessment metrics for each active supplier
        $enrichedSuppliers = $activeSuppliers->map(function ($supplier) use (
            $rawProducts,
            $ordersBySupplierName,
            $ordersBySupplierId,
            $arrivalNotices,
            $reverseLogistics,
            $priceHistories
        ) {
            $supplierProducts = $rawProducts->filter(function ($p) use ($supplier) {
                return $p->supplier_name === $supplier->name || $p->suppliers->contains('id', $supplier->id);
            });

            $supplierOrdersByName = $ordersBySupplierName->get($supplier->name, collect());
            $supplierOrdersById = $ordersBySupplierId->get($supplier->id, collect());
            $supplierOrders = $supplierOrdersByName->merge($supplierOrdersById)->unique('id')->values();

            $performance = $this->performanceService->calculateForSupplier(
                $supplier,
                $supplierProducts,
                $supplierOrders,
                $arrivalNotices,
                $reverseLogistics,
                $priceHistories
            );

            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'contact_person' => $supplier->contact_person ?? 'N/A',
                'contact_position' => $supplier->contact_position ?? '',
                'email' => $supplier->email ?? '',
                'phone' => $supplier->phone ?? '',
                'status' => $supplier->status ?? 'active',
                'performance_score' => $performance['performance_score'],
                'on_time_rate' => $performance['on_time_rate'],
                'quality_score' => $performance['quality_score'],
                'completion_rate' => $performance['completion_rate'],
                'defect_rate' => $performance['defect_rate'] ?? 0,
                'price_stability' => $performance['price_stability'] ?? 100,
                'orders_count' => $performance['orders_count'] ?? 0,
            ];
        });

        // Group price histories by product_id and supplier_id for supplier-specific pricing
        $supplierCostsByProduct = $priceHistories->groupBy('product_id')->map(function ($histories) {
            return $histories->groupBy('supplier_id')->map(function ($supplierHistory) {
                return (float) $supplierHistory->sortByDesc('id')->first()->supplier_cost;
            })->all();
        });

        // Map products with eligible suppliers and stock calculations
        $products = $rawProducts->map(function ($product) use ($suppliersByName, $supplierCostsByProduct, $descMap) {
            $supplierIds = $product->suppliers->pluck('id')->all();

            // Also check legacy supplier_name column
            if (!empty($product->supplier_name) && isset($suppliersByName[$product->supplier_name])) {
                $matchedSupplier = $suppliersByName[$product->supplier_name];
                if (!in_array($matchedSupplier->id, $supplierIds)) {
                    $supplierIds[] = $matchedSupplier->id;
                }
            }

            $stockQty = (int) $product->stock_quantity;
            $reorderLevel = (int) ($product->reorder_level ?? 10);
            $suggestedQty = ($stockQty <= $reorderLevel) ? max(1, ($reorderLevel * 2) - $stockQty) : 1;
            $supplierPrices = $supplierCostsByProduct[$product->id] ?? [];

            // Category resolution against ProductDescription
            $resolvedCategory = null;
            $catalogDesc = $product->productCatalog->product_description ?? null;
            $prodName = $product->product_name ?? null;
            $rawCat = $product->category ?? null;

            foreach ([$catalogDesc, $prodName, $rawCat] as $candidate) {
                if ($candidate && isset($descMap[strtolower(trim($candidate))])) {
                    $resolvedCategory = $descMap[strtolower(trim($candidate))]->name;
                    break;
                }
            }

            if (!$resolvedCategory) {
                foreach ($descMap as $lowerKey => $descObj) {
                    if ($prodName && (str_contains(strtolower($prodName), $lowerKey) || str_contains($lowerKey, strtolower($prodName)))) {
                        $resolvedCategory = $descObj->name;
                        break;
                    }
                }
            }

            // Fallback without 'Accessories'
            if (!$resolvedCategory) {
                if ($rawCat && strtolower(trim($rawCat)) !== 'accessories') {
                    $resolvedCategory = $rawCat;
                } elseif ($prodName) {
                    $resolvedCategory = $prodName;
                } else {
                    $resolvedCategory = 'General';
                }
            }

            $finalCategory = $resolvedCategory;

            // Display name
            $displayName = $product->name;
            if ($product->product_name && $product->product_name !== $product->name) {
                $displayName = $product->product_name . ' - ' . $product->name;
            } elseif (!$displayName) {
                $displayName = $product->product_name ?: 'Product #' . $product->id;
            }

            return [
                'id' => $product->id,
                'name' => $displayName,
                'product_name' => $product->product_name ?: $product->name,
                'sku' => $product->sku ?? 'N/A',
                'category' => $finalCategory,
                'brand' => $product->brand ?? '',
                'stock_quantity' => $stockQty,
                'reorder_level' => $reorderLevel,
                'is_low_stock' => ($stockQty <= $reorderLevel),
                'suggested_quantity' => $suggestedQty,
                'unit_price' => (float) $product->unit_price,
                'supplier_prices' => $supplierPrices,
                'supplier_ids' => array_values(array_unique($supplierIds)),
            ];
        });

        // Collect all categories (ProductDescription names + any extra product categories, excluding Accessories)
        $descCategories = $productDescriptions->pluck('name')->all();
        $prodCategories = $products->pluck('category')->filter()->all();
        $categories = collect(array_merge($descCategories, $prodCategories))
            ->filter()
            ->unique()
            ->reject(fn($c) => in_array(strtolower(trim($c)), ['accessories', 'accessory', 'uncategorized', 'unknown', 'none', 'n/a', '']))
            ->sort()
            ->values();

        // Category to Brands map for dependent dropdown filtering
        $categoryBrandsMap = [];
        foreach ($productDescriptions as $desc) {
            if (in_array(strtolower(trim($desc->name)), ['accessories', 'accessory'])) continue;
            $categoryBrandsMap[$desc->name] = is_array($desc->brands) ? array_values(array_filter($desc->brands)) : [];
        }

        // Also record brands found on products under each category
        foreach ($products as $p) {
            if (!empty($p['category']) && !empty($p['brand']) && strtolower(trim($p['category'])) !== 'accessories') {
                $cat = $p['category'];
                if (!isset($categoryBrandsMap[$cat])) {
                    $categoryBrandsMap[$cat] = [];
                }
                if (!in_array($p['brand'], $categoryBrandsMap[$cat])) {
                    $categoryBrandsMap[$cat][] = $p['brand'];
                }
            }
        }

        // All distinct brands across catalog and descriptions
        $descBrands = $productDescriptions->flatMap(fn($d) => is_array($d->brands) ? $d->brands : [])->all();
        $prodBrands = $products->pluck('brand')->filter()->all();
        $brands = collect(array_merge($descBrands, $prodBrands))->filter()->unique()->sort()->values();

        return view('offline-reconciliation.purchase-orders', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $enrichedSuppliers,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'categoryBrandsMap' => $categoryBrandsMap,
        ]);
    }

    /**
     * Get synchronization statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->reconciliationService->getSynchronizationStats();
        $pendingSync = $this->reconciliationService->getPendingSyncRecords();

        return response()->json([
            'stats' => $stats,
            'pending_sync' => $pendingSync,
        ]);
    }

    /**
     * Display synchronization history
     */
    public function history(Request $request)
    {
        $query = SynchronizationHistory::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('file_name', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('synchronization_status', $request->status);
        }

        $history = $query->with(['exportedBy', 'importedBy'])->latest()->paginate(10)->withQueryString();

        return view('offline-reconciliation.history', compact('history'));
    }

    /**
     * Display reconciliation report
     */
    public function report($id)
    {
        $syncHistory = SynchronizationHistory::with(['exportedBy', 'importedBy'])->findOrFail($id);
        $report = $this->reconciliationService->generateReconciliationReport($syncHistory);

        return view('offline-reconciliation.report', compact('syncHistory', 'report'));
    }

    /**
     * Delete synchronization history
     */
    public function destroyHistory($id)
    {
        $syncHistory = SynchronizationHistory::findOrFail($id);
        $syncHistory->delete();

        return back()->with('success', 'Synchronization history deleted successfully.');
    }

    /**
     * Display local order generation page
     */
    public function localOrders()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        return view('offline-reconciliation.local-orders', compact('suppliers'));
    }

    /**
     * Sync local order from offline storage
     */
    public function syncOrder(Request $request)
    {
        try {
            $orderData = $request->all();

            return DB::transaction(function () use ($orderData) {
                $purchaseOrder = PurchaseOrder::create([
                    'order_number' => $orderData['order_number'],
                    'supplier_id' => $orderData['supplier_id'] ?? null,
                    'supplier_name' => $orderData['supplier_name'] ?? null,
                    'status' => $orderData['status'] ?? 'pending',
                    'sync_status' => 'synchronized',
                    'notes' => $orderData['notes'] ?? null,
                    'total_amount' => $orderData['total_amount'] ?? 0,
                ]);

                if (isset($orderData['items']) && is_array($orderData['items'])) {
                    foreach ($orderData['items'] as $item) {
                        PurchaseOrderItem::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'product_id' => $item['product_id'] ?? null,
                            'product_name' => $item['product_name'] ?? 'Unknown Item',
                            'sku' => $item['sku'] ?? null,
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'unit_price' => (float) ($item['unit_price'] ?? 0),
                            'total_price' => (float) ($item['subtotal'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0))),
                        ]);
                    }
                }

                return response()->json(['success' => true, 'order_id' => $purchaseOrder->id]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
