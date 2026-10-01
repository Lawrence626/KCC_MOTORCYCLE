<div style="font-family:Arial,Helvetica,sans-serif;color:#111;line-height:1.5;">
    <h1 style="font-size:20px;margin-bottom:16px;">Purchase Order {{ $purchaseOrder->order_number }}</h1>
    <p style="margin-bottom:8px;">Hello {{ $purchaseOrder->supplier_name ?? $purchaseOrder->supplier?->name ?? 'Supplier' }},</p>
    <p style="margin-bottom:16px;">Please find the purchase order details below for your attention.</p>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;margin-bottom:16px;">
        <tr style="background:#f4f4f4;font-weight:bold;">
            <td>Order number</td>
            <td>{{ $purchaseOrder->order_number }}</td>
        </tr>
        <tr>
            <td>Total amount</td>
            <td>₱{{ number_format((float) ($purchaseOrder->total_amount ?? 0), 2) }}</td>
        </tr>
    </table>

    <h2 style="font-size:16px;margin-bottom:12px;">Order items</h2>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;margin-bottom:24px;">
        <thead style="background:#f4f4f4;font-weight:bold;">
            <tr>
                <td>Product</td>
                <td>SKU</td>
                <td>Quantity</td>
                <td>Unit price</td>
                <td>Total</td>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                @php
                    $uPrice = (float) ($item->unit_price ?? 0);
                    $totPrice = (float) ($item->total_price ?? ($uPrice * (int) ($item->quantity ?? 0)));
                @endphp
                <tr>
                    <td>{{ $item->product_name ?? $item->product?->name ?? 'Product' }}</td>
                    <td>{{ $item->sku ?? $item->product?->sku ?? 'N/A' }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>₱{{ number_format($uPrice, 2) }}</td>
                    <td>₱{{ number_format($totPrice, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($purchaseOrder->notes)
        <p style="margin-bottom:16px;"><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>
    @endif

    <p style="margin-bottom:0;">Thank you,<br>The Purchase Order Team</p>
</div>
