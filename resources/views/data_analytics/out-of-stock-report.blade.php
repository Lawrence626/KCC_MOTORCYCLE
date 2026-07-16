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
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Out of stock products</h2>
                        <p class="text-xs text-slate-500 mt-1">Products that need immediate restocking.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Critical</span>
                </div>

                <div class="mt-4 flex-1 overflow-hidden">
                    <div class="h-[340px] overflow-y-auto overflow-x-hidden rounded-2xl border border-slate-100">
                        <table class="w-full min-w-full table-fixed text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                                <tr class="sticky top-0 z-10 bg-slate-50">
                                    <th class="w-[40%] px-3 py-3">Product</th>
                                    <th class="w-[20%] px-3 py-3">Category</th>
                                    <th class="w-[20%] px-3 py-3">SKU</th>
                                    <th class="w-[20%] px-3 py-3">Last restock</th>
                                </tr>
                            </thead>
                            <tbody id="outOfStockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse($outOfStockProducts as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-3 font-semibold text-slate-900">
                                            <div class="truncate">{{ $product->product_name ?: $product->name }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <div class="truncate">{{ $product->category }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-500">
                                            <div class="truncate">{{ optional($product->last_restock_date)->format('M d, Y') ?? 'N/A' }}</div>
                                        </td>
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

                <div class="mt-4 border-t border-slate-200 pt-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex gap-3">
                            <button id="outOfStockPrev" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">← Previous</button>
                            <button id="outOfStockNext" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">Next →</button>
                        </div>
                        <div class="flex-1 text-center min-w-[140px]">
                            <p id="outOfStockPageInfo" class="text-sm text-slate-600">Page 1 of 1</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Low stock alerts</h2>
                        <p class="text-xs text-slate-500 mt-1">Products at or below reorder levels.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Review</span>
                </div>

                <div class="mt-4 flex-1 overflow-hidden">
                    <div class="h-[340px] overflow-y-auto overflow-x-hidden rounded-2xl border border-slate-100">
                        <table class="w-full min-w-full table-fixed text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                                <tr class="sticky top-0 z-10 bg-slate-50">
                                    <th class="w-[40%] px-3 py-3">Product</th>
                                    <th class="w-[20%] px-3 py-3">Stock</th>
                                    <th class="w-[20%] px-3 py-3">Reorder</th>
                                    <th class="w-[20%] px-3 py-3">Need</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse($lowStockProducts as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-3 font-semibold text-slate-900">
                                            <div class="truncate">{{ $product->product_name ?: $product->name }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ number_format($product->stock_quantity) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <div class="truncate">{{ number_format($product->reorder_level) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ number_format(max(0, $product->reorder_level - $product->stock_quantity)) }}</div>
                                        </td>
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

                <div class="mt-4 border-t border-slate-200 pt-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex gap-3">
                            <button id="lowStockPrev" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">← Previous</button>
                            <button id="lowStockNext" type="button" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">Next →</button>
                        </div>
                        <div class="flex-1 text-center min-w-[140px]">
                            <p id="lowStockPageInfo" class="text-sm text-slate-600">Page 1 of 1</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $outOfStockJs = $outOfStockProducts->map(function($product) {
            return [
                'name' => $product->product_name ?: $product->name,
                'category' => $product->category,
                'sku' => $product->sku,
                'last_restock_date' => optional($product->last_restock_date)->format('Y-m-d'),
            ];
        })->toArray();

        $lowStockJs = $lowStockProducts->map(function($product) {
            return [
                'name' => $product->product_name ?: $product->name,
                'stock_quantity' => $product->stock_quantity,
                'reorder_level' => $product->reorder_level,
                'need' => max(0, $product->reorder_level - $product->stock_quantity),
            ];
        })->toArray();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageSize = 5;

            const outOfStockData = @json($outOfStockJs);
            const lowStockData = @json($lowStockJs);

            const state = {
                outOfStockPage: 1,
                lowStockPage: 1,
            };

            function formatDate(value) {
                if (!value) {
                    return 'N/A';
                }
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) {
                    return value;
                }
                return new Intl.DateTimeFormat('en', { month: 'short', day: '2-digit', year: 'numeric' }).format(date);
            }

            function renderTable({ data, page, bodyId, infoId, prevId, nextId, renderRow, emptyMessage }) {
                const body = document.getElementById(bodyId);
                const pageInfo = document.getElementById(infoId);
                const prevButton = document.getElementById(prevId);
                const nextButton = document.getElementById(nextId);

                if (!body || !pageInfo || !prevButton || !nextButton) {
                    return;
                }

                const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
                const currentPage = Math.min(Math.max(page, 1), totalPages);
                const start = (currentPage - 1) * pageSize;
                const pageItems = data.slice(start, start + pageSize);

                if (pageItems.length === 0) {
                    body.innerHTML = `<tr><td colspan="4" class="px-3 py-5 text-center text-slate-500">${emptyMessage}</td></tr>`;
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
                data: outOfStockData,
                stateKey: 'outOfStockPage',
                bodyId: 'outOfStockTableBody',
                infoId: 'outOfStockPageInfo',
                prevId: 'outOfStockPrev',
                nextId: 'outOfStockNext',
                renderRow: function(product) {
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900"><div class="truncate">${product.name}</div></td>
                            <td class="px-3 py-3 text-slate-600"><div class="truncate">${product.category}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.sku}</div></td>
                            <td class="px-3 py-3 text-slate-500"><div class="truncate">${formatDate(product.last_restock_date)}</div></td>
                        </tr>
                    `.trim();
                },
                emptyMessage: 'No out of stock items currently.',
            });

            setupPagination({
                data: lowStockData,
                stateKey: 'lowStockPage',
                bodyId: 'lowStockTableBody',
                infoId: 'lowStockPageInfo',
                prevId: 'lowStockPrev',
                nextId: 'lowStockNext',
                renderRow: function(product) {
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900"><div class="truncate">${product.name}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.stock_quantity}</div></td>
                            <td class="px-3 py-3 text-slate-600"><div class="truncate">${product.reorder_level}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.need}</div></td>
                        </tr>
                    `.trim();
                },
                emptyMessage: 'No low stock products detected.',
            });
        });
    </script>
</x-layouts.app>
