<?php

namespace App\Http\Controllers;

use App\Services\OfflineReconciliationService;
use App\Models\SynchronizationHistory;
use App\Models\PendingImport;
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
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        try {
            // For CSV files, read manually to avoid Laravel Excel path issues
            if ($extension === 'csv') {
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

            return back()->with('success', 'File uploaded successfully. Pending admin review.')
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
        $records = [];
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

        // Read data rows
        $rowNumber = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($row) !== count($headers)) {
                continue; // Skip malformed rows
            }

            $record = array_combine($headers, $row);
            $record = $this->mapRowToRecord($record);
            
            if ($record) {
                $records[] = $record;
            }
        }

        fclose($handle);
        return $records;
    }

    /**
     * Map CSV row to record format
     */
    protected function mapRowToRecord(array $row): ?array
    {
        if (empty($row['type'])) {
            return null;
        }

        $type = strtolower($row['type']);

        if ($type === 'purchase_order') {
            return [
                'type' => 'purchase_order',
                'order_number' => $row['order_number'] ?? null,
                'supplier_id' => $row['supplier_id'] ?? null,
                'supplier_name' => $row['supplier_name'] ?? null,
                'status' => $row['status'] ?? 'pending',
                'sync_status' => 'imported',
                'expected_delivery_date' => $row['expected_delivery_date'] ?? null,
                'notes' => $row['notes'] ?? null,
                'total_amount' => $row['total_amount'] ?? 0,
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
                'items' => [],
            ];
        } elseif ($type === 'inventory_movement') {
            return [
                'type' => 'inventory_movement',
                'product_id' => $row['product_id'] ?? null,
                'product_name' => $row['product_name'] ?? null,
                'type' => $row['movement_type'] ?? 'adjustment',
                'sync_status' => 'imported',
                'quantity_change' => $row['quantity_change'] ?? 0,
                'unit_price' => $row['unit_price'] ?? 0,
                'supplier_name' => $row['supplier_name'] ?? null,
                'notes' => $row['notes'] ?? null,
                'metadata' => null,
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
            ];
        }

        return null;
    }

    /**
     * Validate uploaded file before import
     */
    public function validateFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            // For CSV files, read manually
            if ($extension === 'csv') {
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
                    'message' => 'No valid records found in the file.',
                ]);
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
                'message' => 'Validation failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Display pending imports for admin review
     */
    public function pendingImports(Request $request)
    {
        $query = PendingImport::query()->with(['uploadedBy', 'reviewedBy']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $pendingImports = $query->latest()->paginate(10);

        return view('offline-reconciliation.pending-imports', compact('pendingImports'));
    }

    /**
     * Review specific pending import
     */
    public function review($id)
    {
        $pendingImport = PendingImport::with(['uploadedBy', 'reviewedBy'])->findOrFail($id);
        $data = $pendingImport->data;

        $html = '<div class="space-y-4">';
        
        // Summary
        $html .= '<div class="grid grid-cols-2 md:grid-cols-4 gap-2">';
        $html .= '<div class="p-3 bg-slate-50 rounded-lg"><p class="text-xs text-slate-600">Total Records</p><p class="text-lg font-bold text-slate-900">' . $pendingImport->total_records . '</p></div>';
        $html .= '<div class="p-3 bg-green-50 rounded-lg"><p class="text-xs text-green-600">Valid Records</p><p class="text-lg font-bold text-green-700">' . $pendingImport->valid_records . '</p></div>';
        $html .= '<div class="p-3 bg-red-50 rounded-lg"><p class="text-xs text-red-600">Invalid Records</p><p class="text-lg font-bold text-red-700">' . $pendingImport->invalid_records . '</p></div>';
        $html .= '<div class="p-3 bg-amber-50 rounded-lg"><p class="text-xs text-amber-600">Duplicate Records</p><p class="text-lg font-bold text-amber-700">' . $pendingImport->duplicate_records . '</p></div>';
        $html .= '</div>';

        // Valid records preview
        if (!empty($data['valid'])) {
            $html .= '<div><h4 class="text-sm font-semibold text-slate-900 mb-2">Valid Records (' . count($data['valid']) . ')</h4>';
            $html .= '<div class="max-h-60 overflow-y-auto border border-slate-200 rounded-lg">';
            $html .= '<table class="w-full text-xs">';
            $html .= '<thead class="bg-slate-50"><tr><th class="px-2 py-1 text-left">Type</th><th class="px-2 py-1 text-left">Details</th></tr></thead>';
            $html .= '<tbody>';
            foreach (array_slice($data['valid'], 0, 10) as $record) {
                $html .= '<tr class="border-t">';
                $html .= '<td class="px-2 py-1">' . ucfirst($record['type'] ?? 'Unknown') . '</td>';
                $html .= '<td class="px-2 py-1">' . ($record['order_number'] ?? $record['product_name'] ?? 'N/A') . '</td>';
                $html .= '</tr>';
            }
            if (count($data['valid']) > 10) {
                $html .= '<tr><td colspan="2" class="px-2 py-1 text-slate-500">... and ' . (count($data['valid']) - 10) . ' more</td></tr>';
            }
            $html .= '</tbody></table></div></div>';
        }

        // Invalid records
        if (!empty($data['invalid'])) {
            $html .= '<div><h4 class="text-sm font-semibold text-red-900 mb-2">Invalid Records (' . count($data['invalid']) . ')</h4>';
            $html .= '<div class="max-h-40 overflow-y-auto border border-red-200 rounded-lg">';
            foreach (array_slice($data['invalid'], 0, 5) as $invalid) {
                $html .= '<div class="p-2 bg-red-50 border-b border-red-100">';
                $html .= '<p class="text-xs text-red-700">Row ' . ($invalid['index'] + 1) . ': ' . implode(', ', $invalid['errors']) . '</p>';
                $html .= '</div>';
            }
            if (count($data['invalid']) > 5) {
                $html .= '<p class="text-xs text-red-600 p-2">... and ' . (count($data['invalid']) - 5) . ' more</p>';
            }
            $html .= '</div></div>';
        }

        // Duplicate records
        if (!empty($data['duplicates'])) {
            $html .= '<div><h4 class="text-sm font-semibold text-amber-900 mb-2">Duplicate Records (' . count($data['duplicates']) . ')</h4>';
            $html .= '<div class="max-h-40 overflow-y-auto border border-amber-200 rounded-lg">';
            foreach (array_slice($data['duplicates'], 0, 5) as $duplicate) {
                $html .= '<div class="p-2 bg-amber-50 border-b border-amber-100">';
                $html .= '<p class="text-xs text-amber-700">' . ($duplicate['order_number'] ?? $duplicate['product_name'] ?? 'N/A') . '</p>';
                $html .= '</div>';
            }
            if (count($data['duplicates']) > 5) {
                $html .= '<p class="text-xs text-amber-600 p-2">... and ' . (count($data['duplicates']) - 5) . ' more</p>';
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

            return back()->with('success', 'Import approved and synchronized successfully.');
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

        return back()->with('success', 'Import rejected successfully.');
    }
}
