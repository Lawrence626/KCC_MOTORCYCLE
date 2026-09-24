<div class="overflow-x-auto">
    <table class="w-full text-xs text-center">
        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-white whitespace-nowrap">Product</th>
                <th class="px-4 py-3 text-center font-semibold text-white whitespace-nowrap">SKU</th>
                <th class="px-4 py-3 text-center font-semibold text-white whitespace-nowrap">Stock</th>
                <th class="px-4 py-3 text-center font-semibold text-white whitespace-nowrap">Value</th>
                <th class="px-4 py-3 text-center font-semibold text-white whitespace-nowrap">Days Unsold</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 bg-white">
            @forelse($deadStocks as $ds)
            @php
                $product = $ds->product;
            @endphp
            <tr onclick="window.location='{{ route('dss.dead-stock.show', $ds->id) }}'"
                tabindex="0"
                role="button"
                onkeydown="if(event.key==='Enter') window.location='{{ route('dss.dead-stock.show', $ds->id) }}'"
                class="cursor-pointer hover:bg-slate-50/80 transition-colors group">
                {{-- Product Details --}}
                <td class="px-5 py-3.5 max-w-[280px] text-left">
                    <div class="flex items-center gap-2.5">
                        <div class="deadstock-img-thumb w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300"
                             data-id="{{ $product->id ?? '' }}"
                             data-sku="{{ $product->sku ?? '' }}"
                             data-name="{{ $product->name ?? $product->description ?? '' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block truncate font-medium text-black">
                                {{ $product->description ?? $product->name ?? 'N/A' }}
                            </span>
                            @if($product->brand || $product->product_name)
                            <div class="truncate text-[11px] text-slate-400 mt-0.5">
                                {{ implode(' • ', array_filter([$product->brand, $product->product_name])) }}
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                {{-- SKU --}}
                <td class="px-4 py-3.5 text-center text-xs text-slate-500 font-mono">{{ $product->sku ?? '—' }}</td>
                {{-- Stock --}}
                <td class="px-4 py-3.5 text-center text-slate-700 font-medium">{{ $ds->current_stock }}</td>
                {{-- Value --}}
                <td class="px-4 py-3.5 text-center text-slate-700 font-medium">₱{{ number_format($ds->stock_value, 0) }}</td>
                {{-- Days Unsold --}}
                <td class="px-4 py-3.5 text-center font-bold text-slate-700">{{ $ds->days_without_sale }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12">
                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-medium text-slate-900">No dead stock found</h4>
                        <p class="text-sm text-slate-500 mt-1">All inventory items have been sold within the last 30 days.</p>
                        @if(request('search'))
                        <a href="{{ route('dss.dead-stock.index') }}" class="mt-4 text-sm font-medium text-teal-600 hover:text-teal-700">Clear all filters</a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($deadStocks->hasPages())
<div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
    {{ $deadStocks->appends(request()->query())->links('pagination::tailwind') }}
</div>
@endif
