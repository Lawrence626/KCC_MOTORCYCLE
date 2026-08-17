<div class="mt-6 overflow-hidden rounded-[10px] border border-slate-200">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-white">PO / Ref No.</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Type</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Supplier</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Product</th>
                <th class="px-4 py-3 text-center font-semibold text-white">Ordered / Defective</th>
                <th class="px-4 py-3 text-center font-semibold text-white">Received</th>
                <th class="px-4 py-3 text-center font-semibold text-white">Remaining</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Order Date</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Status</th>
                <th class="px-4 py-3 text-right font-semibold text-white">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-700">
            @php
                $hasShortDelivery = $backOrders && $backOrders->count() > 0;
                $hasReplacements = isset($replacementBackOrders) && $replacementBackOrders->count() > 0;
            @endphp

            @if($hasShortDelivery)
                @foreach($backOrders as $item)
                    @php
                        $purchaseOrder = $item->purchaseOrder;
                        $receivedQuantity = $item->received_quantity ?? 0;
                        $remainingQuantity = max(0, $item->quantity - $receivedQuantity);
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold">{{ $purchaseOrder->order_number }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">Short Delivery</span>
                        </td>
                        <td class="px-4 py-3">{{ $purchaseOrder->supplier_name }}</td>
                        <td class="px-4 py-3">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-center">{{ $receivedQuantity }}</td>
                        <td class="px-4 py-3 text-center font-bold text-amber-700">{{ $remainingQuantity }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">Waiting for Supplier</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('order.show', $purchaseOrder) }}" class="inline-flex rounded-[10px] bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">View Details</a>
                        </td>
                    </tr>
                @endforeach
            @endif

            @if($hasReplacements)
                @foreach($replacementBackOrders as $rbo)
                    <tr class="hover:bg-blue-50/40 bg-blue-50/20">
                        <td class="px-4 py-3 font-semibold text-blue-900">
                            {{ $rbo->replacement_order_number ?: ('RBO-' . $rbo->purchaseOrder->order_number) }}
                            <div class="text-[10px] font-normal text-slate-500 font-sans">Orig: {{ $rbo->purchaseOrder->order_number }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-800">Replacement</span>
                        </td>
                        <td class="px-4 py-3">{{ $rbo->supplier_name }}</td>
                        <td class="px-4 py-3 font-medium">{{ $rbo->product_name }}</td>
                        <td class="px-4 py-3 text-center text-rose-600 font-semibold">{{ $rbo->defective_quantity }} def.</td>
                        <td class="px-4 py-3 text-center">{{ $rbo->replacement_received_quantity }}</td>
                        <td class="px-4 py-3 text-center font-bold text-blue-700">{{ max(0, $rbo->defective_quantity - $rbo->replacement_received_quantity) }}</td>
                        <td class="px-4 py-3">
                            <div>{{ optional($rbo->resolved_at ?? $rbo->created_at)->format('M j, Y') }}</div>
                            @if($rbo->expected_replacement_date)
                                <div class="text-[10px] text-blue-700 font-semibold">Exp: {{ $rbo->expected_replacement_date->format('M j, Y') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-semibold text-blue-800">Awaiting Replacement</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('order.show', $rbo->purchaseOrder) }}" class="inline-flex rounded-[10px] bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm">Receive</a>
                        </td>
                    </tr>
                @endforeach
            @endif

            @if(!$hasShortDelivery && !$hasReplacements)
                <tr>
                    <td colspan="10" class="px-4 py-6 text-center text-sm text-slate-500">No back ordered items found.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
