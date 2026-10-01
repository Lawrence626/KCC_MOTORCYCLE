<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Models\PurchaseOrder;
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
        $exportedPurchaseOrders = PurchaseOrder::where('sync_status', 'exported')->count();

        return view('offline-reconciliation.export', compact(
            'pendingPurchaseOrders',
            'exportedPurchaseOrders'
        ));
    }

    /**
     * Export pending transactions to CSV
     */
    public function exportCsv(Request $request)
    {
        $exportType = $request->input('type', 'purchase_orders');
        $syncStatus = $request->input('sync_status', 'pending_sync');

        $records = $this->getExportRecords($exportType, $syncStatus);
        $fileName = $this->generateExportFileName($records, 'csv');

        // Create synchronization history record
        $syncHistory = SynchronizationHistory::create([
            'file_name' => $fileName,
            'export_date' => now(),
            'exported_by' => Auth::id(),
            'total_records' => count($records),
            'imported_records' => 0,
            'duplicate_records' => 0,
            'failed_records' => 0,
            'skipped_records' => 0,
            'synchronization_status' => 'pending',
        ]);

        // Mark records as exported
        $this->markRecordsAsExported($records);

        return Excel::download(new OfflineTransactionsExport($records), $fileName);
    }

    /**
     * Export pending transactions to Excel
     */
    public function exportExcel(Request $request)
    {
        $exportType = $request->input('type', 'purchase_orders');
        $syncStatus = $request->input('sync_status', 'pending_sync');

        $records = $this->getExportRecords($exportType, $syncStatus);
        $fileName = $this->generateExportFileName($records, 'xlsx');

        // Create synchronization history record
        $syncHistory = SynchronizationHistory::create([
            'file_name' => $fileName,
            'export_date' => now(),
            'exported_by' => Auth::id(),
            'total_records' => count($records),
            'imported_records' => 0,
            'duplicate_records' => 0,
            'failed_records' => 0,
            'skipped_records' => 0,
            'synchronization_status' => 'pending',
        ]);

        // Mark records as exported
        $this->markRecordsAsExported($records);

        return Excel::download(new OfflineTransactionsExport($records), $fileName);
    }

    /**
     * Generate standard filename corresponding to PO numbers
     */
    protected function generateExportFileName(array $records, string $extension = 'csv'): string
    {
        $poNumbers = [];
        foreach ($records as $record) {
            if (!empty($record['order_number'])) {
                $cleanPo = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $record['order_number']);
                if ($cleanPo !== '') {
                    $poNumbers[$cleanPo] = true;
                }
            }
        }

        $poList = array_keys($poNumbers);
        if (count($poList) === 1) {
            $poPart = '_' . $poList[0];
        } elseif (count($poList) > 1 && count($poList) <= 3) {
            $poPart = '_' . implode('_', $poList);
        } elseif (count($poList) > 3) {
            $poPart = '_' . $poList[0] . '_to_' . end($poList) . '_(' . count($poList) . '_orders)';
        } else {
            $poPart = '_ORDERS_' . date('Y_m_d');
        }

        return 'KCC_MOTORCYCLE' . $poPart . '.' . $extension;
    }

    /**
     * Get records for export
     */
    protected function getExportRecords(string $exportType, string $syncStatus): array
    {
        $records = [];

        $purchaseOrders = PurchaseOrder::where('sync_status', $syncStatus)
            ->with(['items.product', 'supplier'])
            ->get();

        foreach ($purchaseOrders as $po) {
            if ($po->items->isEmpty()) {
                $records[] = [
                    'id' => $po->id,
                    'type' => 'purchase_order',
                    'order_number' => $po->order_number,
                    'product_id' => '',
                    'product_name' => '',
                    'sku' => '',
                    'supplier_id' => $po->supplier_id,
                    'supplier_name' => $po->supplier_name,
                    'quantity' => 0,
                    'unit_price' => 0,
                    'subtotal' => 0,
                    'total_amount' => $po->total_amount,
                    'status' => $po->status,
                    'sync_status' => $po->sync_status,
                    'notes' => $po->notes,
                    'created_at' => $po->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $po->updated_at?->format('Y-m-d H:i:s'),
                ];
            } else {
                foreach ($po->items as $item) {
                    $records[] = [
                        'id' => $po->id,
                        'type' => 'purchase_order',
                        'order_number' => $po->order_number,
                        'product_id' => $item->product_id ?? '',
                        'product_name' => $item->product_name ?: ($item->product?->product_name ?: $item->product?->name),
                        'sku' => $item->sku ?: $item->product?->sku,
                        'supplier_id' => $po->supplier_id,
                        'supplier_name' => $po->supplier_name,
                        'quantity' => (int) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'subtotal' => (float) $item->total_price,
                        'total_amount' => (float) $po->total_amount,
                        'status' => $po->status,
                        'sync_status' => $po->sync_status,
                        'notes' => $po->notes,
                        'created_at' => $po->created_at?->format('Y-m-d H:i:s'),
                        'updated_at' => $po->updated_at?->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        return $records;
    }

    /**
     * Mark records as exported
     */
    protected function markRecordsAsExported(array $records): void
    {
        $poIds = [];

        foreach ($records as $record) {
            if (($record['type'] ?? '') === 'purchase_order' && !empty($record['id'])) {
                $poIds[] = $record['id'];
            }
        }

        $uniquePoIds = array_values(array_unique(array_filter($poIds)));

        if (!empty($uniquePoIds)) {
            PurchaseOrder::whereIn('id', $uniquePoIds)->update(['sync_status' => 'exported']);
        }
    }
}

