<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OfflineTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $records;

    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return collect($this->records);
    }

    public function headings(): array
    {
        return [
            'Type',
            'Order Number',
            'Product ID',
            'Product Name',
            'Supplier ID',
            'Supplier Name',
            'Quantity',
            'Quantity Change',
            'Unit Price',
            'Total Amount',
            'Status',
            'Sync Status',
            'Movement Type',
            'Expected Delivery Date',
            'Notes',
            'Created At',
            'Updated At',
        ];
    }

    public function map($record): array
    {
        if ($record['type'] === 'purchase_order') {
            return [
                $record['type'],
                $record['order_number'] ?? '',
                '',
                '',
                $record['supplier_id'] ?? '',
                $record['supplier_name'] ?? '',
                '',
                '',
                '',
                $record['total_amount'] ?? 0,
                $record['status'] ?? '',
                $record['sync_status'] ?? '',
                '',
                $record['expected_delivery_date'] ?? '',
                $record['notes'] ?? '',
                $record['created_at'] ?? '',
                $record['updated_at'] ?? '',
            ];
        } elseif ($record['type'] === 'inventory_movement') {
            return [
                $record['type'],
                '',
                $record['product_id'] ?? '',
                $record['product_name'] ?? '',
                '',
                $record['supplier_name'] ?? '',
                '',
                $record['quantity_change'] ?? 0,
                $record['unit_price'] ?? 0,
                '',
                '',
                $record['sync_status'] ?? '',
                $record['type'] ?? '',
                '',
                $record['notes'] ?? '',
                $record['created_at'] ?? '',
                $record['updated_at'] ?? '',
            ];
        }

        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
