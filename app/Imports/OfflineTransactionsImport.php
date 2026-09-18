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
        $groupedOrders = [];
        $movements = [];

        foreach ($collection as $row) {
            $rowArray = $row->toArray();
            
            // Normalize keys to lowercase trimmed strings
            $normalized = [];
            foreach ($rowArray as $k => $v) {
                $cleanKey = strtolower(trim(str_replace([' ', '-'], '_', (string) $k)));
                $normalized[$cleanKey] = is_string($v) ? trim($v) : $v;
            }

            $type = strtolower((string) ($normalized['type'] ?? ''));

            if ($type === 'purchase_order') {
                $orderNumber = $normalized['order_number'] ?? null;
                if (!$orderNumber) {
                    continue;
                }

                if (!isset($groupedOrders[$orderNumber])) {
                    $groupedOrders[$orderNumber] = [
                        'type' => 'purchase_order',
                        'order_number' => $orderNumber,
                        'supplier_id' => !empty($normalized['supplier_id']) ? (int) $normalized['supplier_id'] : null,
                        'supplier_name' => $normalized['supplier_name'] ?? null,
                        'status' => $normalized['status'] ?? 'pending',
                        'sync_status' => 'imported',
                        'notes' => $normalized['notes'] ?? null,
                        'total_amount' => !empty($normalized['total_amount']) ? (float) $normalized['total_amount'] : 0,
                        'created_at' => $normalized['created_at'] ?? now(),
                        'updated_at' => $normalized['updated_at'] ?? now(),
                        'items' => [],
                    ];
                }

                if (!empty($normalized['product_id']) || !empty($normalized['product_name']) || !empty($normalized['sku'])) {
                    $qty = (int) ($normalized['quantity'] ?? 1);
                    $price = (float) ($normalized['unit_price'] ?? 0);
                    $subtotal = !empty($normalized['subtotal']) ? (float) $normalized['subtotal'] : ($qty * $price);

                    $groupedOrders[$orderNumber]['items'][] = [
                        'product_id' => !empty($normalized['product_id']) ? (int) $normalized['product_id'] : null,
                        'product_name' => $normalized['product_name'] ?? 'Item',
                        'sku' => $normalized['sku'] ?? null,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal,
                    ];
                }
            } elseif ($type === 'inventory_movement') {
                $movements[] = [
                    'type' => 'inventory_movement',
                    'product_id' => !empty($normalized['product_id']) ? (int) $normalized['product_id'] : null,
                    'product_name' => $normalized['product_name'] ?? null,
                    'type_movement' => $normalized['movement_type'] ?? ($normalized['type_movement'] ?? 'adjustment'),
                    'sync_status' => 'imported',
                    'quantity_change' => (int) ($normalized['quantity_change'] ?? 0),
                    'unit_price' => (float) ($normalized['unit_price'] ?? 0),
                    'supplier_name' => $normalized['supplier_name'] ?? null,
                    'notes' => $normalized['notes'] ?? null,
                    'metadata' => null,
                    'created_at' => $normalized['created_at'] ?? now(),
                    'updated_at' => $normalized['updated_at'] ?? now(),
                ];
            }
        }

        // Calculate total amounts if missing
        foreach ($groupedOrders as &$order) {
            if (empty($order['total_amount']) && !empty($order['items'])) {
                $order['total_amount'] = array_sum(array_column($order['items'], 'subtotal'));
            }
        }

        $this->records = array_merge(array_values($groupedOrders), $movements);
    }

    public function getRecords(): array
    {
        return $this->records;
    }
}
