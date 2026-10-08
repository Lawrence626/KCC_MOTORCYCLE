<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Models\SynchronizationHistory;
use App\Models\PendingImport;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OfflineTransactionsImport;

class ImportController extends Controller
{
    protected OfflineReconciliationService $reconciliationService;

    public function __construct(OfflineReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    /**
     * Display import page
     */
    public function index()
    {
        $recentImports = SynchronizationHistory::with(['importedBy', 'exportedBy'])
            ->whereNotNull('import_date')
            ->latest()
            ->paginate(10);

        $pendingImports = PendingImport::with(['uploadedBy', 'reviewedBy'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('offline-reconciliation.import', compact('recentImports', 'pendingImports'));
    }

    /**
     * Import transactions from uploaded file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:csv,txt,xlsx,xls,bin'],
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            // For CSV files, read manually to avoid Laravel Excel path issues
            if ($extension === 'csv' || $extension === 'txt') {
                $records = $this->readCsvFile($file);
            } else {
                // For Excel files, use Laravel Excel
                $import = new OfflineTransactionsImport();
                Excel::import($import, $file);
                $records = $import->getRecords();
            }

            \Log::info('Import records', ['count' => count($records), 'records' => $records]);

            if (empty($records)) {
                return back()->with('error', 'No valid records found in the file.');
            }

            // Validate records
            $validationResults = $this->reconciliationService->validateImport($records);

            \Log::info('Validation results', ['results' => $validationResults]);

            // Save to staging table for admin review
            $pendingImport = PendingImport::create([
                'file_name' => $fileName,
                'uploaded_by' => Auth::id(),
                'data' => $validationResults,
                'total_records' => count($records),
                'valid_records' => count($validationResults['valid']),
                'invalid_records' => count($validationResults['invalid']),
                'duplicate_records' => count($validationResults['duplicates']),
                'status' => 'pending',
            ]);

            \Log::info('Pending import created', ['id' => $pendingImport->id]);

            // Stage valid purchase orders as 'pending approval' so they appear in Order Management's Pending Approval filter
            foreach ($validationResults['valid'] as $record) {
                if (($record['type'] ?? 'purchase_order') === 'purchase_order' && !empty($record['order_number'])) {
                    $supplierId = $record['supplier_id'] ?? null;
                    $supplierName = $record['supplier_name'] ?? null;
                    if (!$supplierId && $supplierName) {
                        $supplierId = Supplier::where('name', $supplierName)->value('id');
                    }

                    $existingPO = PurchaseOrder::where('order_number', $record['order_number'])->first();
                    if (!$existingPO) {
                        $po = PurchaseOrder::create([
                            'order_number' => $record['order_number'],
                            'supplier_id' => $supplierId,
                            'supplier_name' => $supplierName,
                            'user_id' => Auth::id() ?? 1,
                            'created_by_role' => 'admin',
                            'status' => 'pending approval',
                            'sync_status' => 'imported',
                            'notes' => $record['notes'] ?? null,
                            'total_amount' => $record['total_amount'] ?? 0,
                            'created_at' => $record['created_at'] ?? now(),
                            'updated_at' => now(),
                        ]);

                        if (!empty($record['items']) && is_array($record['items'])) {
                            foreach ($record['items'] as $item) {
                                $productId = $item['product_id'] ?? null;
                                $productName = $item['product_name'] ?? 'Unknown Item';
                                $sku = $item['sku'] ?? null;
                                if (!$productId && $sku) {
                                    $p = Product::where('sku', $sku)->first();
                                    $productId = $p?->id;
                                    $productName = $p?->product_name ?: ($p?->name ?: $productName);
                                }
                                PurchaseOrderItem::create([
                                    'purchase_order_id' => $po->id,
                                    'product_id' => $productId,
                                    'product_name' => $productName,
                                    'sku' => $sku,
                                    'quantity' => (int) ($item['quantity'] ?? 1),
                                    'unit_price' => (float) ($item['unit_price'] ?? 0),
                                    'total_price' => (float) ($item['subtotal'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0))),
                                ]);
                            }
                        }
                    } else {
                        // If it existed as pending, make sure its status matches
                        if (in_array($existingPO->status, ['pending', 'pending approval'])) {
                            $existingPO->update(['status' => 'pending approval', 'sync_status' => 'imported']);
                        }
                    }
                }
            }

            $validCount = count($validationResults['valid']);
            $dupCount = count($validationResults['duplicates']);
            $invalidCount = count($validationResults['invalid']);

            $msg = "File \"{$fileName}\" uploaded successfully! Found {$validCount} valid order" . ($validCount === 1 ? '' : 's');
            if ($dupCount > 0) $msg .= ", {$dupCount} duplicate" . ($dupCount === 1 ? '' : 's');
            if ($invalidCount > 0) $msg .= ", {$invalidCount} invalid row" . ($invalidCount === 1 ? '' : 's');
            $msg .= ". Staged for review below.";

            return back()->with('success', $msg)
                ->with('pending_import_id', $pendingImport->id);

        } catch (\Exception $e) {
            \Log::error('Import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Read CSV file manually
     */
    protected function readCsvFile($file): array
    {
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle === false) {
            throw new \Exception('Unable to open file');
        }

        // Get header row
        $headers = fgetcsv($handle);
        
        if ($headers === false) {
            fclose($handle);
            throw new \Exception('Unable to read CSV header');
        }

        // Clean headers: remove UTF-8 BOM, trim, lowercase, replace spaces/dashes with underscores
        $cleanHeaders = array_map(function ($h) {
            $h = preg_replace('/^\xEF\xBB\xBF/', '', (string) $h);
            return strtolower(trim(str_replace([' ', '-'], '_', (string) $h)));
        }, $headers);

        $groupedOrders = [];

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row, fn($val) => trim((string)$val) !== ''))) {
                continue; // Skip empty rows
            }
            if (count($row) < count($cleanHeaders)) {
                $row = array_pad($row, count($cleanHeaders), '');
            } elseif (count($row) > count($cleanHeaders)) {
                $row = array_slice($row, 0, count($cleanHeaders));
            }

            $rawRow = array_combine($cleanHeaders, $row);
            $type = strtolower(trim($rawRow['type'] ?? ''));

            if ($type === 'purchase_order' || empty($type)) {
                $orderNumber = trim($rawRow['order_number'] ?? '');
                if (!$orderNumber) {
                    continue;
                }

                if (!isset($groupedOrders[$orderNumber])) {
                    $groupedOrders[$orderNumber] = [
                        'type' => 'purchase_order',
                        'order_number' => $orderNumber,
                        'supplier_id' => !empty($rawRow['supplier_id']) ? (int) $rawRow['supplier_id'] : null,
                        'supplier_name' => !empty($rawRow['supplier_name']) ? trim($rawRow['supplier_name']) : null,
                        'status' => !empty($rawRow['status']) && !in_array(strtolower(trim($rawRow['status'])), ['pending', 'pending approval']) ? trim($rawRow['status']) : 'pending approval',
                        'sync_status' => 'imported',
                        'notes' => !empty($rawRow['notes']) ? trim($rawRow['notes']) : null,
                        'total_amount' => !empty($rawRow['total_amount']) ? (float) $rawRow['total_amount'] : 0,
                        'created_at' => !empty($rawRow['created_at']) ? trim($rawRow['created_at']) : now(),
                        'updated_at' => !empty($rawRow['updated_at']) ? trim($rawRow['updated_at']) : now(),
                        'items' => [],
                    ];
                }

                if (!empty($rawRow['product_id']) || !empty($rawRow['product_name']) || !empty($rawRow['sku'])) {
                    $qty = (int) ($rawRow['quantity'] ?? 1);
                    $price = (float) ($rawRow['unit_price'] ?? 0);
                    $subtotal = !empty($rawRow['subtotal']) ? (float) $rawRow['subtotal'] : ($qty * $price);

                    $groupedOrders[$orderNumber]['items'][] = [
                        'product_id' => !empty($rawRow['product_id']) ? (int) $rawRow['product_id'] : null,
                        'product_name' => !empty($rawRow['product_name']) ? trim($rawRow['product_name']) : 'Item',
                        'sku' => !empty($rawRow['sku']) ? trim($rawRow['sku']) : null,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal,
                    ];
                }
            }
        }

        fclose($handle);

        // Recalculate total amount from items if needed
        foreach ($groupedOrders as &$order) {
            if (empty($order['total_amount']) && !empty($order['items'])) {
                $order['total_amount'] = array_sum(array_column($order['items'], 'subtotal'));
            }
        }

        return array_values($groupedOrders);
    }

    /**
     * Validate uploaded file before import
     */
    public function validateFile(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:csv,txt,xlsx,xls,bin'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            // For CSV files, read manually
            if ($extension === 'csv' || $extension === 'txt') {
                $records = $this->readCsvFile($file);
            } else {
                // For Excel files, use Laravel Excel
                $import = new OfflineTransactionsImport();
                Excel::import($import, $file);
                $records = $import->getRecords();
            }

            if (empty($records)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid purchase orders found in the file. Please check column headers and content.',
                ], 422);
            }

            $validationResults = $this->reconciliationService->validateImport($records);

            return response()->json([
                'success' => true,
                'validation_results' => $validationResults,
                'total_records' => count($records),
                'valid_records' => count($validationResults['valid']),
                'invalid_records' => count($validationResults['invalid']),
                'duplicate_records' => count($validationResults['duplicates']),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display pending imports for admin review
     */
    public function pendingImports(Request $request)
    {
        $query = PendingImport::query()->with(['uploadedBy', 'reviewedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendingImports = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'pending' => PendingImport::where('status', 'pending')->count(),
            'approved' => PendingImport::where('status', 'approved')->count(),
            'rejected' => PendingImport::where('status', 'rejected')->count(),
            'total' => PendingImport::count(),
        ];

        return view('offline-reconciliation.pending-imports', compact('pendingImports', 'stats'));
    }

    /**
     * Review specific pending import
     */
    public function review($id)
    {
        $pendingImport = PendingImport::with(['uploadedBy', 'reviewedBy'])->findOrFail($id);
        $data = $pendingImport->data;

        $html = '<div class="space-y-4">';
        
        // Summary Cards
        $html .= '<div class="grid grid-cols-2 md:grid-cols-4 gap-2">';
        $html .= '<div class="p-3 bg-slate-50 border border-slate-200 rounded-[12px]"><p class="text-xs text-slate-600 font-semibold">Total Records</p><p class="text-lg font-bold text-slate-900">' . $pendingImport->total_records . '</p></div>';
        $html .= '<div class="p-3 bg-green-50 border border-green-200 rounded-[12px]"><p class="text-xs text-green-700 font-semibold">Valid Records</p><p class="text-lg font-bold text-green-700">' . $pendingImport->valid_records . '</p></div>';
        $html .= '<div class="p-3 bg-red-50 border border-red-200 rounded-[12px]"><p class="text-xs text-red-700 font-semibold">Invalid Records</p><p class="text-lg font-bold text-red-700">' . $pendingImport->invalid_records . '</p></div>';
        $html .= '<div class="p-3 bg-amber-50 border border-amber-200 rounded-[12px]"><p class="text-xs text-amber-700 font-semibold">Duplicate Records</p><p class="text-lg font-bold text-amber-700">' . $pendingImport->duplicate_records . '</p></div>';
        $html .= '</div>';

        // Valid records preview with item details
        if (!empty($data['valid'])) {
            $html .= '<div><h4 class="text-xs uppercase tracking-wider font-bold text-green-800 mb-2 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Valid Records Ready for Sync (' . count($data['valid']) . ')</h4>';
            $html .= '<div class="max-h-72 overflow-y-auto border border-slate-200 rounded-[12px] divide-y divide-slate-100">';
            foreach ($data['valid'] as $record) {
                $isPO = ($record['type'] ?? '') === 'purchase_order';
                $orderNum = $record['order_number'] ?? 'N/A';
                $supplierName = $record['supplier_name'] ?? 'N/A';
                $totalAmount = number_format((float) ($record['total_amount'] ?? 0), 2);
                $itemsCount = !empty($record['items']) ? count($record['items']) : 0;

                $html .= '<div class="p-3 bg-white hover:bg-slate-50 transition">';
                $html .= '<div class="flex items-center justify-between text-xs mb-1.5">';
                $html .= '<span class="font-bold text-slate-900 font-mono">' . htmlspecialchars($orderNum) . '</span>';
                $html .= '<span class="font-semibold text-slate-700">₱' . $totalAmount . '</span>';
                $html .= '</div>';
                $html .= '<div class="flex items-center justify-between text-[11px] text-slate-500 mb-2">';
                $html .= '<span>Supplier: <strong class="text-slate-800">' . htmlspecialchars($supplierName) . '</strong></span>';
                $html .= '<span>' . $itemsCount . ' Item(s)</span>';
                $html .= '</div>';

                if ($isPO && !empty($record['items'])) {
                    $html .= '<div class="bg-slate-50 rounded-[8px] p-2 border border-slate-100 space-y-1">';
                    foreach ($record['items'] as $item) {
                        $pName = htmlspecialchars($item['product_name'] ?? 'Product');
                        $pSku = htmlspecialchars($item['sku'] ?? 'N/A');
                        $pQty = (int) ($item['quantity'] ?? 1);
                        $pPrice = number_format((float) ($item['unit_price'] ?? 0), 2);
                        $pSubtotal = number_format((float) ($item['subtotal'] ?? ($pQty * ($item['unit_price'] ?? 0))), 2);

                        $html .= '<div class="flex items-center justify-between text-[10px] text-slate-600">';
                        $html .= '<span>• ' . $pName . ' <span class="text-slate-400">(' . $pSku . ')</span> × ' . $pQty . ' @ ₱' . $pPrice . '</span>';
                        $html .= '<span class="font-semibold text-slate-800">₱' . $pSubtotal . '</span>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }

                $html .= '</div>';
            }
            $html .= '</div></div>';
        }

        // Invalid records with error reasons
        if (!empty($data['invalid'])) {
            $html .= '<div><h4 class="text-xs uppercase tracking-wider font-bold text-red-800 mb-2 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Invalid Records (' . count($data['invalid']) . ')</h4>';
            $html .= '<div class="max-h-56 overflow-y-auto border border-red-200 rounded-[12px] divide-y divide-red-100">';
            foreach ($data['invalid'] as $invalid) {
                $orderNum = $invalid['record']['order_number'] ?? ('Row #' . (($invalid['index'] ?? 0) + 1));
                $errors = $invalid['errors'] ?? ['Validation failed.'];
                $html .= '<div class="p-2.5 bg-red-50/50">';
                $html .= '<div class="font-bold text-xs text-red-900 font-mono mb-1">' . htmlspecialchars($orderNum) . '</div>';
                $html .= '<ul class="list-disc list-inside text-[11px] text-red-700 space-y-0.5">';
                foreach ($errors as $err) {
                    $html .= '<li>' . htmlspecialchars($err) . '</li>';
                }
                $html .= '</ul>';
                $html .= '</div>';
            }
            $html .= '</div></div>';
        }

        // Duplicate records
        if (!empty($data['duplicates'])) {
            $html .= '<div><h4 class="text-xs uppercase tracking-wider font-bold text-amber-800 mb-2 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Duplicate Records (' . count($data['duplicates']) . ')</h4>';
            $html .= '<div class="max-h-40 overflow-y-auto border border-amber-200 rounded-[12px] divide-y divide-amber-100">';
            foreach ($data['duplicates'] as $dup) {
                $orderNum = $dup['record']['order_number'] ?? ($dup['order_number'] ?? 'N/A');
                $reason = $dup['reason'] ?? 'Already exists in database';
                $html .= '<div class="p-2 bg-amber-50/50 flex items-center justify-between text-xs">';
                $html .= '<span class="font-bold text-amber-900 font-mono">' . htmlspecialchars($orderNum) . '</span>';
                $html .= '<span class="text-[11px] text-amber-700 font-medium">' . htmlspecialchars($reason) . '</span>';
                $html .= '</div>';
            }
            $html .= '</div></div>';
        }

        $html .= '</div>';

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Approve pending import
     */
    public function approve($id)
    {
        $pendingImport = PendingImport::findOrFail($id);

        if ($pendingImport->status !== 'pending') {
            return back()->with('error', 'This import has already been processed.');
        }

        try {
            \Log::info('Approving import', ['id' => $id, 'data' => $pendingImport->data]);

            // Import the valid records
            $syncHistory = $this->reconciliationService->importRecords(
                $pendingImport->data['valid'] ?? [],
                $pendingImport->file_name,
                Auth::id()
            );

            \Log::info('Import completed', ['sync_history_id' => $syncHistory->id]);

            // Update pending import status
            $pendingImport->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Update staged purchase orders to 'approved' status with approval timestamp
            $validOrders = $pendingImport->data['valid'] ?? [];
            foreach ($validOrders as $record) {
                if (!empty($record['order_number'])) {
                    PurchaseOrder::where('order_number', $record['order_number'])->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'sync_status' => 'synchronized',
                    ]);
                }
            }

            $validCount = count($validOrders);

            return back()->with('success', "Import Approved! Successfully synchronized {$validCount} purchase order" . ($validCount === 1 ? '' : 's') . " from file \"{$pendingImport->file_name}\" into the database.");
        } catch (\Exception $e) {
            \Log::error('Approve failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to approve import: ' . $e->getMessage());
        }
    }

    /**
     * Reject pending import
     */
    public function reject(Request $request, $id)
    {
        $pendingImport = PendingImport::findOrFail($id);

        if ($pendingImport->status !== 'pending') {
            return back()->with('error', 'This import has already been processed.');
        }

        // Create sync history for rejected import
        $syncHistory = SynchronizationHistory::create([
            'file_name' => $pendingImport->file_name,
            'import_date' => now(),
            'imported_by' => Auth::id(),
            'total_records' => $pendingImport->total_records,
            'imported_records' => 0,
            'duplicate_records' => $pendingImport->duplicate_records,
            'failed_records' => $pendingImport->total_records,
            'skipped_records' => 0,
            'synchronization_status' => 'failed',
            'notes' => 'Import rejected: ' . $request->input('rejection_reason'),
        ]);

        $pendingImport->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Update staged purchase orders to 'rejected' status
        $validOrders = $pendingImport->data['valid'] ?? [];
        foreach ($validOrders as $record) {
            if (!empty($record['order_number'])) {
                PurchaseOrder::where('order_number', $record['order_number'])->update([
                    'status' => 'rejected',
                    'sync_status' => 'rejected',
                ]);
            }
        }

        return back()->with('success', "Import for \"{$pendingImport->file_name}\" was rejected successfully.");
    }
}
