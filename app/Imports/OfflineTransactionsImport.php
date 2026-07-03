<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OfflineTransactionsImport implements ToCollection, WithHeadingRow
{
    protected $records = [];

    public function collection(Collection $collection)
    {
        // Skip header row if not using WithHeadingRow
        foreach ($collection as $row) {
            $record = $this->mapRowToRecord($row);
            if ($record) {
                $this->records[] = $record;
            }
        }
    }

    protected function mapRowToRecord($row): ?array
    {
        // Skip empty rows
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
                'items' => $this->parseItems($row),
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

    protected function parseItems($row): array
    {
        // For simplicity, items would need to be in a separate sheet or parsed from a JSON string
        // This is a placeholder implementation
        return [];
    }

    public function getRecords(): array
    {
        return $this->records;
    }
}
