<x-layouts.app :title="__('Overstocking Report')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Overstocking Report</h1>
                    <p class="text-xs text-slate-500 mt-1">Identify excess inventory and categories that are tying up working capital.</p>
                </div>
                <a href="{{ route('analytics.overstocking.export') }}" class="inline-flex items-center gap-1.5 justify-center rounded-[12px] border border-[#00fff2]/40 bg-[#00fff2] px-4 py-2 text-xs font-semibold text-black shadow-sm hover:bg-[#00e6da] transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Overstock Report
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Overstock SKUs</p>
                        <p class="text-xl font-semibold text-slate-900">{{ number_format($overstockSkuCount) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Products stocked above reorder levels.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 8l-9-4-9 4 9 4 9-4z" />
                            <path d="M21 12l-9 4-9-4" />
                            <path d="M21 16l-9 4-9-4" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Excess units</p>
                        <p class="text-xl font-semibold text-slate-900">{{ number_format($totalExcessUnits) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Total units above minimum target levels.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Potential overstock value</p>
                        <p class="text-xl font-semibold text-slate-900">₱{{ number_format($totalOverstockValue, 2) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Estimated capital tied up in surplus inventory.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.4fr_0.8fr] gap-3">
            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Overstocked products</h2>
                        <p class="text-xs text-slate-500 mt-1">SKU list sorted by excess quantity and inventory value.</p>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-[#105f68]">Paginated</span>
                </div>

                <div class="mt-4 flex-1 overflow-hidden">
                    <div class="overflow-hidden rounded-[14px] border border-slate-200">
                        <table class="w-full min-w-full table-fixed text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a]">
                                <tr class="sticky top-0 z-10 bg-[#0f172a]">
                                    <th class="w-[35%] px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product</th>
                                    <th class="w-[15%] px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Current Stock</th>
                                    <th class="w-[15%] px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Maximum Stock</th>
                                    <th class="w-[15%] px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Excess Stock</th>
                                    <th class="w-[20%] px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Value</th>
                                </tr>
                            </thead>
                            <tbody id="overstockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse($overstockedProducts as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-2.5 font-semibold text-slate-900">
                                            <div class="truncate">{{ $product->product_name ?: $product->name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono truncate">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-900"><div class="truncate">{{ number_format($product->stock_quantity) }}</div></td>
                                        <td class="px-3 py-2.5 text-slate-600"><div class="truncate">{{ number_format($product->reorder_level) }}</div></td>
                                        <td class="px-3 py-2.5 text-slate-900"><div class="truncate">{{ number_format(max(0, $product->stock_quantity - $product->reorder_level)) }}</div></td>
                                        <td class="px-3 py-2.5 text-slate-900 font-semibold"><div class="truncate">₱{{ number_format(max(0, $product->stock_quantity - $product->reorder_level) * $product->unit_price, 2) }}</div></td>
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

                <div class="mt-4 border-t border-slate-200 pt-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex gap-2">
                            <button id="overstockPrev" type="button" class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">← Previous</button>
                            <button id="overstockNext" type="button" class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">Next →</button>
                        </div>
                        <div class="flex-1 text-center min-w-[140px]">
                            <p id="overstockPageInfo" class="text-xs text-slate-600">Page 1 of 1</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Category exposure</h2>
                    <p class="text-xs text-slate-500 mt-1">Overstock exposure by product category.</p>
                </div>
                <div class="mt-4 space-y-2 flex-1 overflow-y-auto max-h-[340px] pr-1 sidebar-scroll">
                    @forelse($categoryBreakdown as $category => $value)
                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900 text-xs">{{ $category ?: 'Uncategorized' }}</p>
                                    <p class="text-[11px] text-slate-500">Potential value by category</p>
                                </div>
                                <p class="text-slate-900 font-semibold text-xs">₱{{ number_format($value, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-3 text-xs text-slate-500">No category overstock data available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @php
        $overstockJs = $overstockedProducts->map(function($product) {
            return [
                'name' => $product->product_name ?: $product->name,
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
