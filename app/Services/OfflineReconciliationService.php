<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Models\SynchronizationHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $seenOrderNumbers = [];

        foreach ($records as $index => $record) {
            $orderNumber = $record['order_number'] ?? null;

            // Check if record is a duplicate in DB or in current batch
            if ($this->isDuplicate($record)) {
                $validationResults['duplicates'][] = [
                    'index' => $index,
                    'record' => $record,
                    'reason' => 'Order number already exists in database',
                ];
                continue;
            }

            if ($orderNumber && in_array($orderNumber, $seenOrderNumbers, true)) {
                $validationResults['duplicates'][] = [
                    'index' => $index,
                    'record' => $record,
                    'reason' => 'Duplicate order number within the imported file',
                ];
                continue;
            }

            $errors = $this->validateRecord($record);

            if (!empty($errors)) {
                $validationResults['invalid'][] = [
                    'index' => $index,
                    'record' => $record,
                    'errors' => $errors,
                ];
            } else {
                if ($orderNumber) {
                    $seenOrderNumbers[] = $orderNumber;
                }
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

        $type = $record['type'] ?? 'purchase_order';

        if ($type === 'purchase_order') {
            // 1. Order Number
            if (empty($record['order_number'])) {
                $errors[] = 'Purchase Order Number is required.';
            }

            // 2. Supplier Validation
            $supplier = null;
            if (!empty($record['supplier_id'])) {
                $supplier = Supplier::find($record['supplier_id']);
            }
            if (!$supplier && !empty($record['supplier_name'])) {
                $supplier = Supplier::where('name', $record['supplier_name'])->first();
            }

            if (!$supplier) {
                $errors[] = 'Supplier does not exist in the database (Supplier: ' . ($record['supplier_name'] ?? $record['supplier_id'] ?? 'N/A') . ').';
            }

            // 3. Items validation
            $items = $record['items'] ?? [];
            if (empty($items) || !is_array($items)) {
                $errors[] = 'Purchase order must contain at least one valid product item.';
            } else {
                foreach ($items as $itemIndex => $item) {
                    $itemNum = $itemIndex + 1;
                    
                    // Product existence
                    $product = null;
                    if (!empty($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                    }
                    if (!$product && !empty($item['sku'])) {
                        $product = Product::where('sku', trim($item['sku']))->first();
                    }
                    if (!$product && !empty($item['product_name'])) {
                        $pName = trim($item['product_name']);
                        $product = Product::where('name', $pName)
                            ->orWhere('sku', $pName)
                            ->first();
                        if (!$product && \Illuminate\Support\Facades\Schema::hasColumn('products', 'product_name')) {
                            $product = Product::where('product_name', $pName)->first();
                        }
                    }

                    if (!$product) {
                        $errors[] = "Item #{$itemNum}: Product does not exist in database ('" . ($item['product_name'] ?? $item['sku'] ?? $item['product_id'] ?? 'N/A') . "').";
                        continue;
                    }

                    // Quantity validation
                    $qty = (int) ($item['quantity'] ?? 0);
                    if ($qty <= 0) {
                        $errors[] = "Item #{$itemNum} ({$product->name}): Quantity must be greater than zero (given: {$qty}).";
                    }

                    // Price validation
                    $unitPrice = (float) ($item['unit_price'] ?? 0);
                    if ($unitPrice <= 0) {
                        $supplierCost = ($supplier && $product) ? SupplierPriceHistory::where('supplier_id', $supplier->id)
                            ->where('product_id', $product->id)
                            ->latest('id')
                            ->value('supplier_cost') : null;
                        $fallbackPrice = (float) ($supplierCost ?? $product?->unit_price ?? 0);
                        if ($fallbackPrice <= 0) {
                            $errors[] = "Item #{$itemNum} ({$product->name}): This product does not have a valid purchase price and cannot be added to the Purchase Order.";
                        }
                    }

                    // 4. Supplier-Product Eligibility Validation
                    if ($supplier && $product) {
                        $isEligible = $this->isSupplierEligibleForProduct($supplier, $product);
                        if (!$isEligible) {
                            $errors[] = "Supplier '{$supplier->name}' is not authorized to supply product '{$product->name}' (SKU: {$product->sku}).";
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Check if a supplier is eligible to supply a specific product
     */
    public function isSupplierEligibleForProduct(Supplier $supplier, Product $product): bool
    {
        // Check supplier_products pivot table
        $inPivot = DB::table('supplier_products')
            ->where('supplier_id', $supplier->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($inPivot) {
            return true;
        }

        // Check legacy supplier_name column on products
        if (!empty($product->supplier_name) && strcasecmp(trim($product->supplier_name), trim($supplier->name)) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if record is a duplicate
     */
    protected function isDuplicate(array $record): bool
    {
        $type = $record['type'] ?? 'purchase_order';

        if ($type === 'purchase_order') {
            return PurchaseOrder::where('order_number', $record['order_number'] ?? null)->exists();
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

                    $type = $record['type'] ?? 'purchase_order';

                    if ($type === 'purchase_order') {
                        $this->importPurchaseOrder($record, $importedBy);
                        $importedCount++;
                    } else {
                        $skippedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error('Failed to import record', [
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
     * Import a purchase order with items
     */
    protected function importPurchaseOrder(array $record, ?int $importedBy = null): void
    {
        // Resolve supplier
        $supplierId = $record['supplier_id'] ?? null;
        $supplierName = $record['supplier_name'] ?? null;

        if (!$supplierId && $supplierName) {
            $foundSupplier = Supplier::where('name', $supplierName)->first();
            $supplierId = $foundSupplier?->id;
        }

        $rawStatus = strtolower(trim($record['status'] ?? 'approved'));
        $finalStatus = in_array($rawStatus, ['approved', 'sent to supplier', 'in transit', 'completed'], true)
            ? $rawStatus
            : 'approved';

        $userId = $importedBy ?? auth()->id();

        $purchaseOrder = PurchaseOrder::updateOrCreate(
            ['order_number' => $record['order_number']],
            [
                'supplier_id' => $supplierId,
                'supplier_name' => $supplierName,
                'user_id' => $userId,
                'created_by_role' => 'admin',
                'status' => $finalStatus,
                'approved_at' => ($finalStatus === 'approved') ? now() : null,
                'sync_status' => 'synchronized',
                'notes' => $record['notes'] ?? null,
                'total_amount' => $record['total_amount'] ?? 0,
            ]
        );

        if (isset($record['items']) && is_array($record['items'])) {
            foreach ($record['items'] as $item) {
                $productId = $item['product_id'] ?? null;
                $productName = $item['product_name'] ?? 'Unknown Item';
                $sku = $item['sku'] ?? null;

                if (!$productId && $sku) {
                    $foundProduct = Product::where('sku', $sku)->first();
                    $productId = $foundProduct?->id;
                    $productName = $foundProduct?->product_name ?: ($foundProduct?->name ?: $productName);
                }

                $quantity = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $totalPrice = (float) ($item['subtotal'] ?? ($quantity * $unitPrice));

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                // Maintain supplier_products pivot table
                if ($supplierId && $productId) {
                    DB::table('supplier_products')->insertOrIgnore([
                        'supplier_id' => $supplierId,
                        'product_id' => $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Mark a record as duplicate (for tracking purposes)
     */
    protected function markAsDuplicate(array $record): void
    {
        Log::info('Duplicate record detected and skipped', ['record' => $record]);
    }

    /**
     * Get pending sync records
     */
    public function getPendingSyncRecords(): array
    {
        return [
            'purchase_orders' => PurchaseOrder::where('sync_status', 'pending_sync')->count(),
        ];
    }

    /**
     * Mark records as exported
     */
    public function markAsExported(array $recordIds, string $type = 'purchase_order'): void
    {
        if ($type === 'purchase_order') {
            PurchaseOrder::whereIn('id', $recordIds)->update(['sync_status' => 'exported']);
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
            ],
            'exported' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'exported')->count(),
            ],
            'synchronized' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'synchronized')->count(),
            ],
            'duplicate' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'duplicate')->count(),
            ],
            'failed' => [
                'purchase_orders' => PurchaseOrder::where('sync_status', 'failed')->count(),
            ],
        ];
    }
}

