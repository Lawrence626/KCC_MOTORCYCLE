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
                        'received' => optional($order->completed_at)->format('M j') ?? optional($order->updated_at)->format('M j'),
                        'created' => optional($order->created_at)->format('M j'),
                        default => optional($order->estimated_delivery_date ?? $order->expected_delivery_date)->format('M j') ?? 'TBD',
                    };

                    // Estimated delivery date logic
                    $estDate = $order->estimated_delivery_date;
                    $isOrderCompleted = in_array($order->status, ['completed', 'archived']);
                    $estDaysRemaining = $estDate ? (int) now()->startOfDay()->diffInDays($estDate->startOfDay(), false) : null;

                    if ($estDate === null) {
                        $estBadgeClass = 'bg-slate-100 text-slate-500';
                        $estText = 'Not yet provided';
                        $estIcon = '';
                    } elseif ($isOrderCompleted) {
                        $estBadgeClass = 'bg-emerald-100 text-emerald-700';
                        $estText = $estDate->format('M j');
                        $estIcon = '✅';
                    } elseif ($estDaysRemaining < 0) {
                        $estBadgeClass = 'bg-rose-100 text-rose-700';
                        $estText = $estDate->format('M j');
                        $estIcon = '🔴';
                    } elseif ($estDaysRemaining <= 2) {
                        $estBadgeClass = 'bg-amber-100 text-amber-700';
                        $estText = $estDate->format('M j');
                        $estIcon = '🟡';
                    } else {
                        $estBadgeClass = 'bg-emerald-100 text-emerald-700';
                        $estText = $estDate->format('M j');
                        $estIcon = '🟢';
                    }
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->supplier_name }}</td>
                    <td class="px-4 py-3">{{ $dateValue }}</td>
                    <td class="px-4 py-3">
                        @if($estDate)
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $estBadgeClass }}">
                                {{ $estIcon }} {{ $estText }}
                            </span>
                            @if(!$isOrderCompleted && $estDaysRemaining !== null)
                                <span class="block mt-0.5 text-[10px] {{ $estDaysRemaining < 0 ? 'text-rose-500' : ($estDaysRemaining <= 2 ? 'text-amber-500' : 'text-emerald-500') }}">
                                    @if($estDaysRemaining < 0)
                                        {{ abs($estDaysRemaining) }} {{ Str::plural('day', abs($estDaysRemaining)) }} overdue
                                    @elseif($estDaysRemaining === 0)
                                        Due today
                                    @else
                                        In {{ $estDaysRemaining }} {{ Str::plural('day', $estDaysRemaining) }}
                                    @endif
                                </span>
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
