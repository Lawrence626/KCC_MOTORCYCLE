<div class="mt-6 overflow-hidden rounded-[26px] border border-slate-200">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
            <tr>
                <th class="px-4 py-3">PO No.</th>
                <th class="px-4 py-3">Supplier</th>
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Ordered</th>
                <th class="px-4 py-3">Received</th>
                <th class="px-4 py-3">Remaining</th>
                <th class="px-4 py-3">Order Date</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-700">
            @forelse($backOrders as $item)
                @php
                    $purchaseOrder = $item->purchaseOrder;
                    $receivedQuantity = $item->received_quantity ?? 0;
                    $remainingQuantity = max(0, $item->quantity - $receivedQuantity);
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold">{{ $purchaseOrder->order_number }}</td>
                    <td class="px-4 py-3">{{ $purchaseOrder->supplier_name }}</td>
                    <td class="px-4 py-3">{{ $item->product_name }}</td>
                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                    <td class="px-4 py-3">{{ $receivedQuantity }}</td>
                    <td class="px-4 py-3">{{ $remainingQuantity }}</td>
                    <td class="px-4 py-3">{{ $purchaseOrder->created_at->format('M j') }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">Waiting for Supplier</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('order.show', $purchaseOrder) }}" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">View Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-sm text-slate-500">No back ordered items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
