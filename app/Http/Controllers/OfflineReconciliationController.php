<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Models\SynchronizationHistory;
use App\Models\PurchaseOrder;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OfflineReconciliationController extends Controller
{
    protected OfflineReconciliationService $reconciliationService;

    public function __construct(OfflineReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    /**
     * Display offline purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();

        // Search
        if ($request->has('search')) {
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

        $purchaseOrders = $query->with('items')->latest()->paginate(10);
        $suppliers = \App\Models\Supplier::all();

        return view('offline-reconciliation.purchase-orders', compact('purchaseOrders', 'suppliers'));
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
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('file_name', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('synchronization_status', $request->status);
        }

        $history = $query->with(['exportedBy', 'importedBy'])->latest()->paginate(10);

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
     * Display inventory movements with sync status
     */
    public function inventoryMovements(Request $request)
    {
        $query = InventoryMovement::query()->with('product');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by sync status
        if ($request->has('sync_status') && $request->sync_status) {
            $query->where('sync_status', $request->sync_status);
        }

        $movements = $query->latest()->paginate(10);

        return view('offline-reconciliation.inventory-movements', compact('movements'));
    }

    /**
     * Display local order generation page
     */
    public function localOrders()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('offline-reconciliation.local-orders', compact('suppliers'));
    }

    /**
     * Sync local order from offline storage
     */
    public function syncOrder(Request $request)
    {
        try {
            $orderData = $request->all();
            
            $purchaseOrder = PurchaseOrder::create([
                'order_number' => $orderData['order_number'],
                'supplier_id' => $orderData['supplier_id'],
                'supplier_name' => $orderData['supplier_name'],
                'status' => $orderData['status'] ?? 'pending',
                'sync_status' => 'synchronized',
                'expected_delivery_date' => $orderData['expected_delivery_date'] ?? null,
                'notes' => $orderData['notes'] ?? null,
                'total_amount' => $orderData['total_amount'] ?? 0,
            ]);

            return response()->json(['success' => true, 'order_id' => $purchaseOrder->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync local inventory movement from offline storage
     */
    public function syncMovement(Request $request)
    {
        try {
            $movementData = $request->all();
            
            $movement = InventoryMovement::create([
                'product_id' => $movementData['product_id'] ?? null,
                'type' => $movementData['type'] ?? 'adjustment',
                'sync_status' => 'synchronized',
                'quantity_change' => $movementData['quantity_change'] ?? 0,
                'unit_price' => $movementData['unit_price'] ?? 0,
                'supplier_name' => $movementData['supplier_name'] ?? null,
                'notes' => $movementData['notes'] ?? null,
                'metadata' => $movementData['metadata'] ?? null,
            ]);

            return response()->json(['success' => true, 'movement_id' => $movement->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
