@forelse($lowStockProducts as $product)
    @php $idx = $loop->index; @endphp
    <tr class="product-row hover:bg-slate-50 transition-colors"
        data-product-id="{{ $product->id }}"
        data-product-name="{{ $product->product_name ?? $product->name }}"
        data-sku="{{ $product->sku }}"
        data-default-quantity="{{ max(1, 100 - $product->stock_quantity) }}"
        data-default-unit-price="{{ $product->unit_price }}">
        <td class="px-4 py-3">
            <input type="checkbox"
                   name="products[{{ $idx }}][selected]"
                   value="1"
                   {{ in_array($product->id, $selectedProductIds, true) ? 'checked' : '' }}
                   class="product-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
            <input type="hidden" name="products[{{ $idx }}][product_id]" value="{{ $product->id }}" />
            <input type="hidden" name="products[{{ $idx }}][product_name]" value="{{ $product->product_name ?? $product->name }}" />
            <input type="hidden" name="products[{{ $idx }}][sku]" value="{{ $product->sku }}" />
        </td>
        <td class="px-4 py-3 font-medium text-slate-800">{{ $product->product_name ?? $product->name }}</td>
        <td class="px-4 py-3">
            @php
                $mov = $product->movement_category ?? 'special_order';
                $movLabel = match($mov) {
                    'fast_moving'   => 'Fast moving',
                    'slow_moving'   => 'Slow moving',
                    'special_order' => 'Special order',
                    default         => ucfirst(str_replace('_', ' ', $mov)),
                };
                $movClass = match($mov) {
                    'fast_moving'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                    'slow_moving'   => 'bg-amber-50 text-amber-700 ring-amber-200',
                    'special_order' => 'bg-sky-50 text-sky-700 ring-sky-200',
                    default         => 'bg-slate-100 text-slate-600 ring-slate-200',
                };
            @endphp
            <span class="inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $movClass }}">
                {{ $movLabel }}
            </span>
        </td>
        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $product->sku }}</td>
        <td class="px-4 py-3">
            <span class="{{ $product->stock_quantity <= 0 ? 'text-rose-600 font-semibold' : 'text-amber-600 font-semibold' }}">
                {{ $product->stock_quantity }}
            </span>
        </td>
        <td class="px-4 py-3">{{ $product->reorder_level }}</td>
        <td class="px-4 py-3">
            <input name="products[{{ $idx }}][quantity]"
                   type="number" min="1"
                   value="{{ old('products.' . $idx . '.quantity', max(1, 100 - $product->stock_quantity)) }}"
                   class="w-20 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:outline-none focus:ring-1 focus:ring-black/35" />
        </td>
        <td class="px-4 py-3">
            <input name="products[{{ $idx }}][unit_price]"
                   type="number" step="0.01" min="0"
                   value="{{ old('products.' . $idx . '.unit_price', $product->unit_price) }}"
                   class="w-28 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:outline-none focus:ring-1 focus:ring-black/35" />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">
            No products found for this filter.
        </td>
    </tr>
@endforelse
