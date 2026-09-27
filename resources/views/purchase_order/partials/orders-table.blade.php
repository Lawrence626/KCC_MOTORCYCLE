@php
    $dateLabel = $dateLabel ?? 'ETA';
    $dateType = $dateType ?? 'eta';
    $emptyMessage = $emptyMessage ?? 'No purchase orders found.';
    $showAction = $showAction ?? true;
@endphp

<div class="mt-6 overflow-hidden rounded-[10px] border border-slate-200">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-white">Order</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Supplier</th>
                <th class="px-4 py-3 text-left font-semibold text-white">{{ $dateLabel }}</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Est. Delivery</th>
                <th class="px-4 py-3 text-left font-semibold text-white">Status</th>
                @if($showAction)
                    <th class="px-4 py-3 text-left font-semibold text-white">Action</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-700">
            @forelse($orders as $order)
                @php
                    $statusClass = match($order->status) {
                        'pending approval' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-sky-100 text-sky-800',
                        'sent to supplier' => 'bg-blue-100 text-blue-800',
                        'in transit' => 'bg-sky-100 text-sky-800',
                        'partially received' => 'bg-amber-100 text-amber-800',
                        'completed' => 'bg-emerald-100 text-emerald-800',
                        'archived' => 'bg-slate-100 text-slate-700',
                        'rejected', 'cancelled' => 'bg-rose-100 text-rose-800',
                        default => 'bg-slate-100 text-slate-700',
                    };

                    $dateValue = match($dateType) {
                        'received' => optional($order->completed_at)->format('M j, Y') ?? optional($order->updated_at)->format('M j, Y'),
                        'created' => optional($order->created_at)->format('M j, Y'),
                        default => optional($order->estimated_delivery_date ?? $order->expected_delivery_date)->format('M j, Y') ?? 'Not yet provided',
                    };

                    // Estimated delivery date logic
                    $estDate = $order->estimated_delivery_date ?? $order->expected_delivery_date;
                    $isOrderCompleted = in_array($order->status, ['completed', 'archived']);
                    $estDaysRemaining = $estDate ? (int) now()->startOfDay()->diffInDays($estDate->startOfDay(), false) : null;
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold">
                        @php
                            $firstItem = $order->items->first();
                            $itemCount = $order->items->count();
                        @endphp
                        <div class="flex items-center gap-2.5">
                            <div class="po-order-img-thumb w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300"
                                 data-id="{{ $firstItem?->product_id ?? '' }}"
                                 data-sku="{{ $firstItem?->sku ?? ($firstItem?->product?->sku ?? '') }}"
                                 data-name="{{ $firstItem?->product_name ?? ($firstItem?->product?->name ?? '') }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-slate-900 font-semibold truncate">{{ $order->order_number }}</div>
                                @if($firstItem)
                                    <div class="text-[10px] text-slate-400 font-normal truncate">
                                        {{ $firstItem->product_name }}{{ $itemCount > 1 ? ' +' . ($itemCount - 1) . ' more' : '' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $order->supplier_name }}</td>
                    <td class="px-4 py-3 text-slate-700 font-medium">{{ $dateValue }}</td>
                    <td class="px-4 py-3">
                        @if($estDate)
                            <div class="font-medium text-slate-900">{{ $estDate->format('M j, Y') }}</div>
                            @if(!$isOrderCompleted && $estDaysRemaining !== null)
                                @if($estDaysRemaining < 0)
                                    <div class="text-[11px] font-medium text-red-600">
                                        {{ abs($estDaysRemaining) }} {{ Str::plural('day', abs($estDaysRemaining)) }} overdue
                                    </div>
                                @elseif($estDaysRemaining === 0)
                                    <div class="text-[11px] font-medium text-amber-600">
                                        Due today
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400">
                                        In {{ $estDaysRemaining }} {{ Str::plural('day', $estDaysRemaining) }}
                                    </div>
                                @endif
                            @endif
                        @else
                            <span class="text-xs text-slate-400 italic">Not yet provided</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                            {{ ucwords($order->status) }}
                        </span>
                    </td>
                    @if($showAction)
                        <td class="px-4 py-3">
                            <a href="{{ route('order.show', $order) }}" class="inline-flex rounded-[10px] bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">View Details</a>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showAction ? 6 : 5 }}" class="px-4 py-6 text-center text-sm text-slate-500">{{ $emptyMessage }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
