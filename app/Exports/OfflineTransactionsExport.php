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
            'SKU',
            'Supplier ID',
            'Supplier Name',
            'Quantity',
            'Unit Price',
            'Subtotal',
            'Total Amount',
            'Status',
            'Sync Status',
            'Notes',
            'Created At',
            'Updated At',
        ];
    }

    public function map($record): array
    {
        return [
            $record['type'] ?? 'purchase_order',
            $record['order_number'] ?? '',
            $record['product_id'] ?? '',
            $record['product_name'] ?? '',
            $record['sku'] ?? '',
            $record['supplier_id'] ?? '',
            $record['supplier_name'] ?? '',
            $record['quantity'] ?? '',
            $record['unit_price'] ?? 0,
            $record['subtotal'] ?? 0,
            $record['total_amount'] ?? 0,
            $record['status'] ?? '',
            $record['sync_status'] ?? '',
            $record['notes'] ?? '',
            $record['created_at'] ?? '',
            $record['updated_at'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F172A'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
