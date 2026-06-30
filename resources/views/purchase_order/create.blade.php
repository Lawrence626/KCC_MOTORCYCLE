<x-layouts.app :title="__('Create Purchase Order')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Create Purchase Order</h1>
                <p class="max-w-2xl text-sm text-slate-500">Choose supplier and select low-stock products to restock.</p>
            </div>
            <a href="{{ route('order.management') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900">Back to orders</a>
        </div>

        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <strong class="block font-semibold">Please fix the following:</strong>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('order.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid gap-4 lg:grid-cols-2">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="supplier_id" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option value="">Select supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Expected delivery date</span>
                        <input name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                </div>

                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Notes</span>
                    <textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">{{ old('notes') }}</textarea>
                </label>

                <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-semibold text-slate-700">Low stock products</h3>
                    <p class="mt-1 text-sm text-slate-500">Select the items to include in the order.</p>

                    <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                                <tr>
                                    <th class="px-4 py-3">Select</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Supplier</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3">Reorder</th>
                                    <th class="px-4 py-3">Qty to order</th>
                                    <th class="px-4 py-3">Unit price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-700">
                                @forelse($lowStockProducts as $product)
                                    <tr class="hover:bg-white">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="products[{{ $loop->index }}][selected]" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                            <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $product->id }}" />
                                            <input type="hidden" name="products[{{ $loop->index }}][product_name]" value="{{ $product->product_name ?? $product->name }}" />
                                            <input type="hidden" name="products[{{ $loop->index }}][sku]" value="{{ $product->sku }}" />
                                        </td>
                                        <td class="px-4 py-3">{{ $product->product_name ?? $product->name }}</td>
                                        <td class="px-4 py-3">{{ $product->supplier_name }}</td>
                                        <td class="px-4 py-3">{{ $product->sku }}</td>
                                        <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                                        <td class="px-4 py-3">{{ $product->reorder_level }}</td>
                                        <td class="px-4 py-3">
                                            <input name="products[{{ $loop->index }}][quantity]" type="number" min="1" value="{{ max(1, $product->reorder_level - $product->stock_quantity) }}" class="w-20 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input name="products[{{ $loop->index }}][unit_price]" type="number" step="0.01" min="0" value="{{ $product->unit_price }}" class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">No low-stock products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">{{ $lowStockProducts->links() }}</div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('order.management') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Submit order</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
