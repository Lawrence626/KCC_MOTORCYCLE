<x-layouts.app :title="__('Out of Stock Report')">
    <div class="space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Out of Stock Report</h1>
                <p class="text-xs text-slate-500 mt-1">Monitor critical stockouts and low inventory that need replenishment first.</p>
            </div>
            <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">Download stockout list</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Out of stock SKUs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($outOfStockCount) }}</p>
                <p class="mt-1 text-xs text-slate-500">Products currently unavailable for sale.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Low stock SKUs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($lowStockCount) }}</p>
                <p class="mt-1 text-xs text-slate-500">Products at or below reorder level demanding urgent attention.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Action priority</p>
                <p class="mt-2 text-xl font-semibold text-slate-900">{{ $outOfStockCount > 0 ? 'Restock Out-of-Stock First' : 'Inventory Stable' }}</p>
                <p class="mt-1 text-xs text-slate-500">Recommended first step for replenishment planning.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Out of stock products</h2>
                        <p class="text-xs text-slate-500 mt-1">Products that need immediate restocking.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Critical</span>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Product</th>
                                <th class="px-3 py-3">Category</th>
                                <th class="px-3 py-3">SKU</th>
                                <th class="px-3 py-3">Last restock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($outOfStockProducts as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-3 font-semibold text-slate-900">{{ $product->name }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ $product->category }}</td>
                                    <td class="px-3 py-3 text-slate-900">{{ $product->sku }}</td>
                                    <td class="px-3 py-3 text-slate-500">{{ optional($product->last_restock_date)->format('M d, Y') ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-5 text-center text-slate-500">No out of stock items currently.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Low stock alerts</h2>
                        <p class="text-xs text-slate-500 mt-1">Products at or below reorder levels.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Review</span>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Product</th>
                                <th class="px-3 py-3">Stock</th>
                                <th class="px-3 py-3">Reorder</th>
                                <th class="px-3 py-3">Need</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($lowStockProducts as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-3 font-semibold text-slate-900">{{ $product->name }}</td>
                                    <td class="px-3 py-3 text-slate-900">{{ number_format($product->stock_quantity) }}</td>
                                    <td class="px-3 py-3 text-slate-600">{{ number_format($product->reorder_level) }}</td>
                                    <td class="px-3 py-3 text-slate-900">{{ number_format(max(0, $product->reorder_level - $product->stock_quantity)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-5 text-center text-slate-500">No low stock products detected.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
