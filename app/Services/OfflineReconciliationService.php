<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SynchronizationHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OfflineReconciliationService
{
    /**
     * Validate imported records before synchronization
     */
    public function validateImport(array $records): array
    {
        $validationResults = [
            'valid' => [],
            'invalid' => [],
            'duplicates' => [],
        ];

        foreach ($records as $index => $record) {
            $errors = $this->validateRecord($record);
            
            if (!empty($errors)) {
                $validationResults['invalid'][] = [
                    'index' => $index,
                    'record' => $record,
                    'errors' => $errors,
                ];
            } elseif ($this->isDuplicate($record)) {
                $validationResults['duplicates'][] = [
                    'index' => $index,
                    'record' => $record,
                ];
            } else {
                $validationResults['valid'][] = $record;
            }
        }

        return $validationResults;
    }

    /**
     * Validate a single record
     */
    protected function validateRecord(array $record): array
    {
        $errors = [];

        // Validate based on record type
        if (isset($record['type']) && $record['type'] === 'purchase_order') {
            if (empty($record['order_number'])) {
                $errors[] = 'Purchase Order Number is required';
            }

            // Only check for duplicates in validation, not existence of supplier
            // This allows importing with suppliers that don't exist yet
        } elseif (isset($record['type']) && $record['type'] === 'inventory_movement') {
            // Less strict validation for inventory movements
            // Only check if product_id is provided, not if it exists
            if (empty($record['product_name']) && empty($record['product_id'])) {
                $errors[] = 'Product name or ID is required';
            }
            if (isset($record['quantity_change']) && $record['quantity_change'] <= 0) {
                $errors[] = 'Quantity change must be greater than zero';
            }
        }

        return $errors;
    }

    /**
     * Check if record is a duplicate
     */
    protected function isDuplicate(array $record): bool
    {
        if (isset($record['type']) && $record['type'] === 'purchase_order') {
            return PurchaseOrder::where('order_number', $record['order_number'] ?? null)->exists();
        }

        if (isset($record['type']) && $record['type'] === 'inventory_movement') {
            // Check for duplicate by comparing key fields
            return InventoryMovement::where('product_id', $record['product_id'] ?? null)
                ->where('type', $record['type'] ?? null)
                ->where('quantity_change', $record['quantity_change'] ?? null)
                ->where('created_at', $record['created_at'] ?? null)
                ->exists();
        }

        return false;
    }

    /**
     * Import validated records
     */
    public function importRecords(array $records, string $fileName, int $importedBy): SynchronizationHistory
    {
        return DB::transaction(function () use ($records, $fileName, $importedBy) {
            $syncHistory = SynchronizationHistory::create([
                'file_name' => $fileName,
                'import_date' => now(),
                'imported_by' => $importedBy,
                'total_records' => count($records),
                'imported_records' => 0,
                'duplicate_records' => 0,
                'failed_records' => 0,
                'skipped_records' => 0,
                'synchronization_status' => 'in_progress',
            ]);

            $importedCount = 0;
            $duplicateCount = 0;
            $failedCount = 0;
            $skippedCount = 0;

            foreach ($records as $record) {
                try {
                    if ($this->isDuplicate($record)) {
                        $duplicateCount++;
                        $this->markAsDuplicate($record);
                        continue;
                    }

                    if ($record['type'] === 'purchase_order') {
                        $this->importPurchaseOrder($record);
                        $importedCount++;
                    } elseif ($record['type'] === 'inventory_movement') {
                        $this->importInventoryMovement($record);
                        $importedCount++;
                    } else {
                        $skippedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    \Log::error('Failed to import record', [
                        'record' => $record,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $syncHistory->update([
                'imported_records' => $importedCount,
                'duplicate_records' => $duplicateCount,
                'failed_records' => $failedCount,
                'skipped_records' => $skippedCount,
                'synchronization_status' => 'completed',
            ]);

            return $syncHistory;
        });
    }

    /**
     * Import a purchase order
     */
    protected function importPurchaseOrder(array $record): void
    {
        $purchaseOrder = PurchaseOrder::create([
            'order_number' => $record['order_number'],
            'supplier_id' => $record['supplier_id'] ?? null,
            'supplier_name' => $record['supplier_name'] ?? null,
            'status' => $record['status'] ?? 'pending',
            'sync_status' => 'synchronized',
            'expected_delivery_date' => $record['expected_delivery_date'] ?? null,
            'notes' => $record['notes'] ?? null,
            'total_amount' => $record['total_amount'] ?? 0,
        ]);

        if (isset($record['items']) && is_array($record['items'])) {
            foreach ($record['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => $item['total_price'] ?? 0,
                ]);
            }
        }
    }

    /**
     * Import an inventory movement
     */
    protected function importInventoryMovement(array $record): void
    {
        InventoryMovement::create([
            'product_id' => $record['product_id'] ?? null,
            'type' => $record['type'] ?? 'adjustment',
            'sync_status' => 'synchronized',
            'quantity_change' => $record['quantity_change'] ?? 0,
            'unit_price' => $record['unit_price'] ?? 0,
            'supplier_name' => $record['supplier_name'] ?? null,
            'notes' => $record['notes'] ?? null,
            'metadata' => $record['metadata'] ?? null,
        ]);
    }

    /**
     * Mark a record as duplicate (for tracking purposes)
     */
    protected function markAsDuplicate(array $record): void
    {
        // This is for tracking - we don't actually insert duplicates
        // We could log this or create a separate duplicates table if needed
        \Log::info('Duplicate record detected and skipped', ['record' => $record]);
    }

    /**
     * Get pending sync records
     */
    public function getPendingSyncRecords(): array
    {
        return [
            'purchase_orders' => PurchaseOrder::where('sync_status', 'pending_sync')->count(),
            'inventory_movements' => InventoryMovement::where('sync_status', 'pending_sync')->count(),
        ];
    }

    /**
     * Mark records as exported
     */
    public function markAsExported(array $recordIds, string $type): void
    {
        if ($type === 'purchase_order') {
            PurchaseOrder::whereIn('id', $recordIds)->update(['sync_status' => 'exported']);
        } elseif ($type === 'inventory_movement') {
            InventoryMovement::whereIn('id', $recordIds)->update(['sync_status' => 'exported']);
        }
    }

    /**
     * Generate reconciliation report
     */
    public function generateReconciliationReport(SynchronizationHistory $syncHistory): array
    {
        return [
            'sync_history_id' => $syncHistory->id,
            'file_name' => $syncHistory->file_name,
            'export_date' => $syncHistory->export_date?->format('Y-m-d H:i:s'),
            'import_date' => $syncHistory->import_date?->format('Y-m-d H:i:s'),
            'exported_by' => $syncHistory->exportedBy?->name,
            'imported_by' => $syncHistory->importedBy?->name,
            'total_records' => $syncHistory->total_records,
            'imported_records' => $syncHistory->imported_records,
            'duplicate_records' => $syncHistory->duplicate_records,
            'failed_records' => $syncHistory->failed_records,
            'skipped_records' => $syncHistory->skipped_records,
            'synchronization_status' => $syncHistory->synchronization_status,
            'notes' => $syncHistory->notes,
        ];
    }

    /**
     * Get synchronization statistics
     */
    public function getSynchronizationStats(): array
    {
        return [
            'pending_sync' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'pending_sync')->count(),
                'inventory_movements' => InventoryMovement::where('sync_status', 'pending_sync')->count(),
            ],
            'exported' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'exported')->count(),
                'inventory_movements' => InventoryMovement::where('sync_status', 'exported')->count(),
            ],
            'synchronized' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'synchronized')->count(),
                'inventory_movements' => InventoryMovement::where('sync_status', 'synchronized')->count(),
            ],
            'duplicate' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'duplicate')->count(),
                'inventory_movements' => InventoryMovement::where('sync_status', 'duplicate')->count(),
            ],
            'failed' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'failed')->count(),
                'inventory_movements' => InventoryMovement::where('sync_status', 'failed')->count(),
            ],
        ];
    }
}
