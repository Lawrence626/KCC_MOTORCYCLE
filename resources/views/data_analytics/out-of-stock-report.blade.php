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

                <div class="mt-4 flex-1">
                    <div class="overflow-hidden rounded-2xl border border-slate-100">
                        <table class="w-full min-w-full table-fixed text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                                <tr class="sticky top-0 z-10 bg-slate-50">
                                    <th class="w-[40%] px-3 py-3">Product</th>
                                    <th class="w-[20%] px-3 py-3">Stock</th>
                                    <th class="w-[20%] px-3 py-3">Reorder</th>
                                    <th class="w-[20%] px-3 py-3">Need</th>
                                </tr>
                            </thead>
                            <tbody id="outOfStockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse($outOfStockProducts as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-3 font-semibold text-slate-900">
                                            <div class="truncate">{{ $product->product_name ?: $product->name }}</div>
                                            <div class="truncate text-[10px] text-slate-400 font-normal tracking-wide mt-0.5">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ number_format($product->stock_quantity ?? 0) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <div class="truncate">{{ number_format($product->reorder_level ?? 0) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ number_format(max(0, 100 - ($product->stock_quantity ?? 0))) }}</div>
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

                <div class="mt-4 border-t border-slate-200 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <label for="outOfStockPageSize" class="text-sm text-slate-600">Rows per page:</label>
                        <select id="outOfStockPageSize" class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm font-medium text-slate-700 outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 cursor-pointer">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button id="outOfStockPrev" type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">← Prev</button>
                        <div id="outOfStockPageNumbers" class="flex items-center gap-1.5"></div>
                        <button id="outOfStockNext" type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">Next →</button>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Low stock products</h2>
                        <p class="text-xs text-slate-500 mt-1">Products at or below reorder levels.</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-400">Review</span>
                </div>

                <div class="mt-4 flex-1">
                    <div class="overflow-hidden rounded-2xl border border-slate-100">
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
                                            <div class="truncate text-[10px] text-slate-400 font-normal tracking-wide mt-0.5">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ number_format($product->stock_quantity) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            <div class="truncate">{{ number_format($product->reorder_level) }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-900">
                                            <div class="truncate">{{ number_format(max(0, 100 - $product->stock_quantity)) }}</div>
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

                <div class="mt-4 border-t border-slate-200 pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <label for="lowStockPageSize" class="text-sm text-slate-600">Rows per page:</label>
                        <select id="lowStockPageSize" class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm font-medium text-slate-700 outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 cursor-pointer">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button id="lowStockPrev" type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">← Prev</button>
                        <div id="lowStockPageNumbers" class="flex items-center gap-1.5"></div>
                        <button id="lowStockNext" type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">Next →</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $outOfStockJs = $outOfStockProducts->map(function($product) {
            return [
                'name' => $product->product_name ?: $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity ?? 0,
                'reorder_level' => $product->reorder_level ?? 0,
                'need' => max(0, 100 - ($product->stock_quantity ?? 0)),
            ];
        })->toArray();

        $lowStockJs = $lowStockProducts->map(function($product) {
            return [
                'name' => $product->product_name ?: $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'reorder_level' => $product->reorder_level,
                'need' => max(0, 100 - $product->stock_quantity),
            ];
        })->toArray();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const outOfStockData = @json($outOfStockJs);
            const lowStockData = @json($lowStockJs);

            const state = {
                outOfStockPage: 1,
                outOfStockPageSize: 5,
                lowStockPage: 1,
                lowStockPageSize: 5,
            };

            window.goToPage = function(stateKey, page) {
                state[stateKey] = page;
                if (stateKey === 'outOfStockPage') window.updateOutOfStock();
                if (stateKey === 'lowStockPage') window.updateLowStock();
            };

            function renderTable({ data, stateKey, bodyId, numbersId, prevId, nextId, renderRow, emptyMessage }) {
                const body = document.getElementById(bodyId);
                const numbersContainer = document.getElementById(numbersId);
                const prevButton = document.getElementById(prevId);
                const nextButton = document.getElementById(nextId);

                if (!body || !numbersContainer || !prevButton || !nextButton) return;

                const pageSize = state[stateKey + 'Size'];
                const page = state[stateKey];
                const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
                const currentPage = Math.min(Math.max(page, 1), totalPages);
                
                const start = (currentPage - 1) * pageSize;
                const pageItems = data.slice(start, start + pageSize);

                if (pageItems.length === 0) {
                    body.innerHTML = `<tr><td colspan="4" class="px-3 py-5 text-center text-slate-500">${emptyMessage}</td></tr>`;
                } else {
                    body.innerHTML = pageItems.map(renderRow).join('');
                }

                prevButton.disabled = currentPage <= 1;
                nextButton.disabled = currentPage >= totalPages;
                prevButton.setAttribute('aria-disabled', String(prevButton.disabled));
                nextButton.setAttribute('aria-disabled', String(nextButton.disabled));

                let html = '';
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    if (i === currentPage) {
                        html += `<button type="button" class="w-8 h-8 flex items-center justify-center rounded-lg bg-cyan-600 text-white text-sm font-medium shadow-sm transition">${i}</button>`;
                    } else {
                        html += `<button type="button" onclick="window.goToPage('${stateKey}', ${i})" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-medium transition hover:bg-slate-50">${i}</button>`;
                    }
                }
                numbersContainer.innerHTML = html;

                return currentPage;
            }

            function setupPagination({ data, stateKey, bodyId, numbersId, prevId, nextId, selectId, renderRow, emptyMessage }) {
                const prevButton = document.getElementById(prevId);
                const nextButton = document.getElementById(nextId);
                const selectSize = document.getElementById(selectId);

                const update = () => {
                    state[stateKey] = renderTable({
                        data,
                        stateKey,
                        bodyId,
                        numbersId,
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
                        const pageSize = state[stateKey + 'Size'];
                        const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
                        if (state[stateKey] < totalPages) {
                            state[stateKey] += 1;
                            update();
                        }
                    });
                }

                if (selectSize) {
                    selectSize.addEventListener('change', function() {
                        state[stateKey + 'Size'] = parseInt(this.value, 10);
                        state[stateKey] = 1;
                        update();
                    });
                }

                return update;
            }

            window.updateOutOfStock = setupPagination({
                data: outOfStockData,
                stateKey: 'outOfStockPage',
                bodyId: 'outOfStockTableBody',
                numbersId: 'outOfStockPageNumbers',
                prevId: 'outOfStockPrev',
                nextId: 'outOfStockNext',
                selectId: 'outOfStockPageSize',
                renderRow: function(product) {
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900">
                                <div class="truncate">${product.name}</div>
                                <div class="truncate text-[10px] text-slate-400 font-normal tracking-wide mt-0.5">${product.sku || 'N/A'}</div>
                            </td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.stock_quantity}</div></td>
                            <td class="px-3 py-3 text-slate-600"><div class="truncate">${product.reorder_level}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.need}</div></td>
                        </tr>
                    `.trim();
                },
                emptyMessage: 'No out of stock items currently.',
            });

            window.updateLowStock = setupPagination({
                data: lowStockData,
                stateKey: 'lowStockPage',
                bodyId: 'lowStockTableBody',
                numbersId: 'lowStockPageNumbers',
                prevId: 'lowStockPrev',
                nextId: 'lowStockNext',
                selectId: 'lowStockPageSize',
                renderRow: function(product) {
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-semibold text-slate-900">
                                <div class="truncate">${product.name}</div>
                                <div class="truncate text-[10px] text-slate-400 font-normal tracking-wide mt-0.5">${product.sku || 'N/A'}</div>
                            </td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.stock_quantity}</div></td>
                            <td class="px-3 py-3 text-slate-600"><div class="truncate">${product.reorder_level}</div></td>
                            <td class="px-3 py-3 text-slate-900"><div class="truncate">${product.need}</div></td>
                        </tr>
                    `.trim();
                },
                emptyMessage: 'No low stock products detected.',
            });

            window.updateOutOfStock();
            window.updateLowStock();
        });
    </script>
</x-layouts.app>
