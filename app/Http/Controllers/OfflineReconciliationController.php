<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Models\SynchronizationHistory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OfflineReconciliationController extends Controller
{
    protected OfflineReconciliationService $reconciliationService;

    public function __construct(
        OfflineReconciliationService $reconciliationService
    ) {
        $this->reconciliationService = $reconciliationService;
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
     * Redirect offline purchase orders to offline reconciliation overview
     */
    public function index(Request $request)
    {
        return redirect()->route('offline.reconciliation');
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
     * Sync local order from offline storage
     */
    public function syncOrder(Request $request)
    {
        try {
            $orderData = $request->all();

            return DB::transaction(function () use ($orderData) {
                $rawStatus = strtolower(trim($orderData['status'] ?? 'pending approval'));
                if ($rawStatus === 'pending') {
                    $rawStatus = 'pending approval';
                }
                $purchaseOrder = PurchaseOrder::create([
                    'order_number' => $orderData['order_number'],
                    'supplier_id' => $orderData['supplier_id'] ?? null,
                    'supplier_name' => $orderData['supplier_name'] ?? null,
                    'status' => $rawStatus,
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

    /**
     * Check which order numbers have already been synced or approved in the database
     */
    public function checkSyncedOrders(Request $request): JsonResponse
    {
        $orderNumbers = $request->input('order_numbers', []);

        if (empty($orderNumbers) || !is_array($orderNumbers)) {
            return response()->json([
                'success' => true,
                'synced_order_numbers' => [],
            ]);
        }

        // Clean and filter input
        $orderNumbers = array_filter(array_map('trim', $orderNumbers));

        if (empty($orderNumbers)) {
            return response()->json([
                'success' => true,
                'synced_order_numbers' => [],
            ]);
        }

        // Query database for existing order numbers
        $existingOrders = PurchaseOrder::whereIn('order_number', $orderNumbers)
            ->pluck('order_number')
            ->toArray();

        return response()->json([
            'success' => true,
            'synced_order_numbers' => array_values(array_unique($existingOrders)),
        ]);
    }
}
