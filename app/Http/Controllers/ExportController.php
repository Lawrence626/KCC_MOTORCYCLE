<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Models\PurchaseOrder;
use App\Models\InventoryMovement;
use App\Models\SynchronizationHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OfflineTransactionsExport;

class ExportController extends Controller
{
    protected OfflineReconciliationService $reconciliationService;

    public function __construct(OfflineReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    /**
     * Display export page
     */
    public function index()
    {
        $pendingPurchaseOrders = PurchaseOrder::where('sync_status', 'pending_sync')->count();
        $pendingInventoryMovements = InventoryMovement::where('sync_status', 'pending_sync')->count();
        $exportedPurchaseOrders = PurchaseOrder::where('sync_status', 'exported')->count();
        $exportedInventoryMovements = InventoryMovement::where('sync_status', 'exported')->count();

        return view('offline-reconciliation.export', compact(
            'pendingPurchaseOrders',
            'pendingInventoryMovements',
            'exportedPurchaseOrders',
            'exportedInventoryMovements'
        ));
    }

    /**
     * Export pending transactions to CSV
     */
    public function exportCsv(Request $request)
    {
        $exportType = $request->input('type', 'all'); // all, purchase_orders, inventory_movements
        $syncStatus = $request->input('sync_status', 'pending_sync');

        $fileName = 'offline_transactions_' . date('Y_m_d') . '.csv';

        // Create synchronization history record
        $syncHistory = SynchronizationHistory::create([
            'file_name' => $fileName,
            'export_date' => now(),
            'exported_by' => Auth::id(),
            'total_records' => 0,
            'imported_records' => 0,
            'duplicate_records' => 0,
            'failed_records' => 0,
            'skipped_records' => 0,
            'synchronization_status' => 'pending',
        ]);

        $records = $this->getExportRecords($exportType, $syncStatus);
        $syncHistory->update(['total_records' => count($records)]);

        // Mark records as exported
        $this->markRecordsAsExported($records, $exportType);

        return Excel::download(new OfflineTransactionsExport($records), $fileName);
    }

    /**
     * Export pending transactions to Excel
     */
    public function exportExcel(Request $request)
    {
        $exportType = $request->input('type', 'all');
        $syncStatus = $request->input('sync_status', 'pending_sync');

        $fileName = 'offline_transactions_' . date('Y_m_d') . '.xlsx';

        // Create synchronization history record
        $syncHistory = SynchronizationHistory::create([
            'file_name' => $fileName,
            'export_date' => now(),
            'exported_by' => Auth::id(),
            'total_records' => 0,
            'imported_records' => 0,
            'duplicate_records' => 0,
            'failed_records' => 0,
            'skipped_records' => 0,
            'synchronization_status' => 'pending',
        ]);

        $records = $this->getExportRecords($exportType, $syncStatus);
        $syncHistory->update(['total_records' => count($records)]);

        // Mark records as exported
        $this->markRecordsAsExported($records, $exportType);

        return Excel::download(new OfflineTransactionsExport($records), $fileName);
    }

    /**
     * Get records for export
     */
    protected function getExportRecords(string $exportType, string $syncStatus): array
    {
        $records = [];

        if ($exportType === 'all' || $exportType === 'purchase_orders') {
            $purchaseOrders = PurchaseOrder::where('sync_status', $syncStatus)
                ->with('items')
                ->get();

            foreach ($purchaseOrders as $po) {
                $records[] = [
                    'type' => 'purchase_order',
                    'order_number' => $po->order_number,
                    'supplier_id' => $po->supplier_id,
                    'supplier_name' => $po->supplier_name,
                    'status' => $po->status,
                    'sync_status' => $po->sync_status,
                    'expected_delivery_date' => $po->expected_delivery_date,
                    'notes' => $po->notes,
                    'total_amount' => $po->total_amount,
                    'created_at' => $po->created_at,
                    'updated_at' => $po->updated_at,
                    'items' => $po->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                        ];
                    })->toArray(),
                ];
            }
        }

        if ($exportType === 'all' || $exportType === 'inventory_movements') {
            $movements = InventoryMovement::where('sync_status', $syncStatus)
                ->with('product')
                ->get();

            foreach ($movements as $movement) {
                $records[] = [
                    'type' => 'inventory_movement',
                    'product_id' => $movement->product_id,
                    'product_name' => $movement->product?->name,
                    'type' => $movement->type,
                    'sync_status' => $movement->sync_status,
                    'quantity_change' => $movement->quantity_change,
                    'unit_price' => $movement->unit_price,
                    'supplier_name' => $movement->supplier_name,
                    'notes' => $movement->notes,
                    'metadata' => $movement->metadata,
                    'created_at' => $movement->created_at,
                    'updated_at' => $movement->updated_at,
                ];
            }
        }

        return $records;
    }

    /**
     * Mark records as exported
     */
    protected function markRecordsAsExported(array $records, string $exportType): void
    {
        $poIds = [];
        $movementIds = [];

        foreach ($records as $record) {
            if ($record['type'] === 'purchase_order') {
                $poIds[] = $record['id'] ?? null;
            } elseif ($record['type'] === 'inventory_movement') {
                $movementIds[] = $record['id'] ?? null;
            }
        }

        if (!empty($poIds) && ($exportType === 'all' || $exportType === 'purchase_orders')) {
            PurchaseOrder::whereIn('id', array_filter($poIds))->update(['sync_status' => 'exported']);
        }

        if (!empty($movementIds) && ($exportType === 'all' || $exportType === 'inventory_movements')) {
            InventoryMovement::whereIn('id', array_filter($movementIds))->update(['sync_status' => 'exported']);
        }
    }
}
