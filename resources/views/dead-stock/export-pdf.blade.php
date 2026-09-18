<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dead Stock Report — {{ $generatedAt }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.5; }
        .header { text-align: center; padding: 30px 20px 20px; border-bottom: 3px solid #0f172a; margin-bottom: 20px; }
        .header h1 { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .header p { font-size: 12px; color: #64748b; }
        .meta { display: flex; justify-content: space-between; padding: 0 20px; margin-bottom: 15px; font-size: 11px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin: 0 20px; }
        th { background-color: #f1f5f9; border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; font-weight: 600; }
        td { border: 1px solid #e2e8f0; padding: 7px 10px; font-size: 11px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { text-align: center; padding: 20px; margin-top: 15px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; }
        @media print {
            body { font-size: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding:15px; background:#f0fdf4; border-bottom:1px solid #bbf7d0;">
        <button onclick="window.print()" style="padding:10px 24px; background:#10b981; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <div class="header">
        <h1>KCC Motorcycle — Dead Stock Report</h1>
        <p>Products Unsold for {{ $thresholdDays }}+ Days | Generated: {{ $generatedAt }}</p>
    </div>

    <div class="meta">
        <span>Total Dead Stock Items: <strong>{{ $deadStocks->count() }}</strong></span>
        <span>Total Value at Risk: <strong>₱{{ number_format($deadStocks->sum('stock_value'), 2) }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Description</th>
                <th>Brand</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Value (₱)</th>
                <th class="text-center">Last Sold</th>
                <th class="text-center">Days Unsold</th>
                <th>Suggested Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deadStocks as $index => $ds)
            @php $product = $ds->product; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->description ?? $product->name ?? '' }}</td>
                <td>{{ $product->brand ?? '' }}</td>
                <td>{{ $product->product_name ?? $product->name ?? '' }}</td>
                <td>{{ $product->sku ?? '' }}</td>
                <td class="text-right">{{ $ds->current_stock }}</td>
                <td class="text-right">{{ number_format($ds->stock_value, 2) }}</td>
                <td class="text-center">{{ $ds->last_sold_date ? $ds->last_sold_date->format('M d, Y') : 'Never' }}</td>
                <td class="text-center"><strong>{{ $ds->days_without_sale }}</strong></td>
                <td>{{ $ds->analysis_notes ?? 'Monitor' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        KCC Motorcycle Inventory Management System — Decision Support System | Confidential
    </div>
</body>
</html>
