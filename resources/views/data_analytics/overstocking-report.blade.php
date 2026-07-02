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
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Overstocked products</h2>
                        <p class="text-xs text-slate-500 mt-1">SKU list sorted by excess quantity and inventory value.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Paginated</span>
                </div>

                <div class="mt-4 flex-1 overflow-hidden">
                    <div class="overflow-hidden rounded-2xl border border-slate-100">
                        <table class="w-full min-w-full table-fixed text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                                <tr class="sticky top-0 z-10 bg-slate-50">
                                    <th class="w-[35%] px-3 py-3">Product</th>
                                    <th class="w-[15%] px-3 py-3">Stock</th>
                                    <th class="w-[15%] px-3 py-3">Reorder</th>
                                    <th class="w-[15%] px-3 py-3">Excess</th>
                                    <th class="w-[20%] px-3 py-3">Value</th>
                                </tr>
                            </thead>
                            <tbody id="overstockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse($overstockedProducts as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-3 font-semibold text-slate-900">
                                            <div class="truncate">{{ $product->name }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900"><div class="truncate">{{ number_format($product->stock_quantity) }}</div></td>
                                        <td class="px-3 py-3 text-slate-600"><div class="truncate">{{ number_format($product->reorder_level) }}</div></td>
                                        <td class="px-3 py-3 text-slate-900"><div class="truncate">{{ number_format(max(0, $product->stock_quantity - $product->reorder_level)) }}</div></td>
                                        <td class="px-3 py-3 text-slate-900"><div class="truncate">₱{{ number_format(max(0, $product->stock_quantity - $product->reorder_level) * $product->unit_price, 2) }}</div></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-slate-500">No overstocked products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 border-t border-slate-200 pt-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex gap-3">
                            <button id="overstockPrev" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">← Previous</button>
                            <button id="overstockNext" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">Next →</button>
                        </div>
                        <div class="flex-1 text-center min-w-[140px]">
                            <p id="overstockPageInfo" class="text-sm text-slate-600">Page 1 of 1</p>
                        </div>
                    </div>
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

    @php
        $overstockJs = $overstockedProducts->map(function($product) {
            return [
                'name' => $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'reorder_level' => $product->reorder_level,
                'excess' => max(0, $product->stock_quantity - $product->reorder_level),
                'value' => max(0, $product->stock_quantity - $product->reorder_level) * $product->unit_price,
            ];
        })->toArray();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageSize = 5;
            const overstockData = @json($overstockJs);
            const state = { overstockPage: 1 };

            function renderTable({ data, page, bodyId, infoId, prevId, nextId, renderRow, emptyMessage }) {
                const body = document.getElementById(bodyId);
                const pageInfo = document.getElementById(infoId);
                const prevButton = document.getElementById(prevId);
                const nextButton = document.getElementById(nextId);
                if (!body || !pageInfo || !prevButton || !nextButton) {
                    return 1;
                }

                const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
                const currentPage = Math.min(Math.max(page, 1), totalPages);
                const start = (currentPage - 1) * pageSize;
                const pageItems = data.slice(start, start + pageSize);

                if (pageItems.length === 0) {
                    body.innerHTML = `<tr><td colspan="5" class="px-3 py-5 text-center text-slate-500">${emptyMessage}</td></tr>`;
                } else {
                    body.innerHTML = pageItems.map(renderRow).join('');
                }

                pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
                prevButton.disabled = currentPage <= 1;
                nextButton.disabled = currentPage >= totalPages;
                prevButton.setAttribute('aria-disabled', String(prevButton.disabled));
                nextButton.setAttribute('aria-disabled', String(nextButton.disabled));
                return currentPage;
            }

            function setupPagination({ data, stateKey, bodyId, infoId, prevId, nextId, renderRow, emptyMessage }) {
                const prevButton = document.getElementById(prevId);
                const nextButton = document.getElementById(nextId);

                const update = () => {
                    state[stateKey] = renderTable({
                        data,
                        page: state[stateKey],
                        bodyId,
                        infoId,
                        prevId,
                        nextId,
                        renderRow,
                        emptyMessage,
                    });
                };

                if (prevButton) {
                    prevButton.addEventListener('click', function() {
                        if (state[stateKey] > 1) {
                            state[stateKey] -= 1;
                            update();
                        }
                    });
                }

                if (nextButton) {
                    nextButton.addEventListener('click', function() {
                        const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
                        if (state[stateKey] < totalPages) {
                            state[stateKey] += 1;
                            update();
                        }
                    });
                }

                update();
            }

            setupPagination({
                data: overstockData,
                stateKey: 'overstockPage',
                bodyId: 'overstockTableBody',
                infoId: 'overstockPageInfo',
                prevId: 'overstockPrev',
                nextId: 'overstockNext',
                renderRow: function(product) {
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900"><div class="truncate">${product.name}</div><div class="text-xs text-slate-500 truncate">${product.sku}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.stock_quantity.toLocaleString()}</div></td>
                            <td class="px-3 py-3 text-slate-600"><div class="truncate">${product.reorder_level.toLocaleString()}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.excess.toLocaleString()}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">₱${product.value.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div></td>
                        </tr>
                    `.trim();
                },
                emptyMessage: 'No overstocked products found.',
            });
        });
    </script>
</x-layouts.app>
