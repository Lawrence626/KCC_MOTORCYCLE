<x-layouts.app :title="__('Overstocking Report')">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
    </style>

    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Overstocking Report</h1>
                <p class="text-xs text-slate-500 mt-1">Identify excess inventory and categories that are tying up working capital.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row pr-4">
                <a href="{{ route('analytics.overstocking.export') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Overstock Report</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Overstock SKUs</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($overstockSkuCount) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Products stocked above reorder levels.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v3.01c0 .72.38 1.36.96 1.72L3 20c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2l.04-11.27c.58-.36.96-1 .96-1.72V4c0-1.1-.9-2-2-2zM9 4h6v2H9V4zm10 16H5l-.03-10h14.06L19 20z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Excess Stock Units</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($totalExcessUnits) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Total units available beyond reorder point.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-6 h-6 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27-7.38 5.74zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Tied-up Capital Value</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">₱{{ number_format($totalOverstockValue ?? 0, 2) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Estimated cost value of excess units in stock.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.4fr_1fr] gap-3">
            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Overstocked Products</h2>
                    <p class="text-xs text-slate-500 mt-1">Products with inventory levels exceeding reorder limits.</p>
                </div>
                <div class="mt-4 overflow-hidden rounded-[10px] border border-slate-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="px-3 py-3 text-left font-semibold text-white">Product</th>
                                    <th class="px-3 py-3 text-left font-semibold text-white">Stock</th>
                                    <th class="px-3 py-3 text-left font-semibold text-white">Reorder</th>
                                    <th class="px-3 py-3 text-left font-semibold text-white">Excess</th>
                                    <th class="px-3 py-3 text-left font-semibold text-white">Tied Capital</th>
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
                    <!-- Overstock Pagination -->
                    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                        <p id="overstockPageInfo" class="text-slate-600">Showing 0 of 0 overstocked products</p>
                        <div id="overstockPaginationControls" class="flex gap-1">
                            <button id="overstockPrev" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">← Prev</button>
                            <button id="overstockNext" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col h-full">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Category exposure</h2>
                    <p class="text-xs text-slate-500 mt-1">Overstock exposure by product category.</p>
                </div>
                <div class="mt-4 space-y-2 flex-1 overflow-y-auto max-h-[340px] pr-1 custom-scrollbar">
                    @forelse($categoryBreakdown as $category => $value)
                        <div class="rounded-[14px] border border-slate-100 p-3" style="background: linear-gradient(50deg, #ffffff 0%, rgba(110, 193, 209, 0.18) 50%);">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900 text-xs">{{ $category ?: 'Uncategorized' }}</p>
                                    <p class="text-[11px] text-slate-500">Potential value by category</p>
                                </div>
                                <p class="text-slate-900 font-semibold text-xs">₱{{ number_format($value, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[14px] border border-slate-100 p-3 text-xs text-slate-500" style="background: linear-gradient(50deg, #ffffff 0%, rgba(110, 193, 209, 0.18) 50%);">No category overstock data available.</div>
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
            let currentPage = 1;

            window.goToOverstockPage = function(page) {
                currentPage = page;
                render();
            };

            function render() {
                const body = document.getElementById('overstockTableBody');
                const pageInfo = document.getElementById('overstockPageInfo');
                const controls = document.getElementById('overstockPaginationControls');
                if (!body || !pageInfo || !controls) return;

                const totalPages = Math.max(1, Math.ceil(overstockData.length / pageSize));
                currentPage = Math.min(Math.max(currentPage, 1), totalPages);
                const start = (currentPage - 1) * pageSize;
                const end = Math.min(start + pageSize, overstockData.length);
                const pageItems = overstockData.slice(start, end);

                if (pageItems.length === 0) {
                    body.innerHTML = `<tr><td colspan="5" class="px-3 py-6 text-center text-slate-500">No overstocked products found.</td></tr>`;
                    pageInfo.textContent = `Showing 0 of 0 overstocked products`;
                } else {
                    body.innerHTML = pageItems.map(product => `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-2.5 font-semibold text-slate-900">
                                <div class="truncate">${product.name}</div>
                                <div class="text-[11px] text-slate-400 font-mono truncate">${product.sku}</div>
                            </td>
                            <td class="px-3 py-2.5 text-slate-900"><div class="truncate">${product.stock_quantity.toLocaleString()}</div></td>
                            <td class="px-3 py-2.5 text-slate-600"><div class="truncate">${product.reorder_level.toLocaleString()}</div></td>
                            <td class="px-3 py-2.5 text-slate-900"><div class="truncate">${product.excess.toLocaleString()}</div></td>
                            <td class="px-3 py-2.5 text-slate-900 font-semibold"><div class="truncate">₱${product.value.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div></td>
                        </tr>
                    `).join('');
                    pageInfo.textContent = `Showing ${start + 1}-${end} of ${overstockData.length} overstocked products`;
                }

                let controlsHtml = `<button type="button" id="overstockPrev" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage <= 1 ? 'disabled' : ''}>← Prev</button>`;

                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }
                startPage = Math.max(1, startPage);

                for (let i = startPage; i <= endPage; i++) {
                    if (i === currentPage) {
                        controlsHtml += `<button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${i}</button>`;
                    } else {
                        controlsHtml += `<button type="button" onclick="goToOverstockPage(${i})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">${i}</button>`;
                    }
                }

                controlsHtml += `<button type="button" id="overstockNext" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage >= totalPages ? 'disabled' : ''}>Next →</button>`;

                controls.innerHTML = controlsHtml;

                document.getElementById('overstockPrev')?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        render();
                    }
                });

                document.getElementById('overstockNext')?.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        render();
                    }
                });
            }

            render();
        });
    </script>
</x-layouts.app>
