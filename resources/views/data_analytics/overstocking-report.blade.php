<x-layouts.app :title="__('Overstocking Report')">
    <div class="space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Overstocking Report</h1>
                <p class="text-xs text-slate-500 mt-1">Identify excess inventory and categories that are tying up working capital.</p>
            </div>
            <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">Export overstock data</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Overstock SKUs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($overstockSkuCount) }}</p>
                <p class="mt-1 text-xs text-slate-500">Products stocked above reorder levels.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Excess units</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($totalExcessUnits) }}</p>
                <p class="mt-1 text-xs text-slate-500">Total units above minimum target levels.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Potential overstock value</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">₱{{ number_format($totalOverstockValue, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">Estimated capital tied up in surplus inventory.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.4fr_0.8fr] gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Overstocked products</h2>
                        <p class="text-xs text-slate-500 mt-1">SKU list sorted by excess quantity and inventory value.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Top 20</span>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Reorder</th>
                                <th class="px-4 py-3">Excess</th>
                                <th class="px-4 py-3">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($overstockedProducts as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-4 font-semibold text-slate-900">{{ $product->name }} <span class="text-xs text-slate-500">{{ $product->sku }}</span></td>
                                    <td class="px-4 py-4 text-slate-900">{{ number_format($product->stock_quantity) }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ number_format($product->reorder_level) }}</td>
                                    <td class="px-4 py-4 text-slate-900">{{ number_format(max(0, $product->stock_quantity - $product->reorder_level)) }}</td>
                                    <td class="px-4 py-4 text-slate-900">₱{{ number_format(max(0, $product->stock_quantity - $product->reorder_level) * $product->unit_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-500">No overstocked products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Category exposure</h2>
                    <p class="text-xs text-slate-500 mt-1">Overstock exposure by product category.</p>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse($categoryBreakdown as $category => $value)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $category ?: 'Uncategorized' }}</p>
                                    <p class="text-xs text-slate-500">Potential value by category</p>
                                </div>
                                <p class="text-slate-900 font-semibold">₱{{ number_format($value, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-slate-500">No category overstock data available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
