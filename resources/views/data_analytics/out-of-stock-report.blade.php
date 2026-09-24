<x-layouts.app :title="__('Out of Stock Report')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Out of Stock Report</h1>
                <p class="text-sm text-slate-500 mt-1">Monitor critical stockouts and low inventory that need replenishment first.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row pr-4">
                <a href="{{ route('analytics.out_of_stock.export') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Download Stockout List</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Out of Stock SKUs</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($outOfStockCount) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Products currently unavailable for sale.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm5.31-3.1L6.1 5.69C7.45 4.63 9.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Low Stock SKUs</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($lowStockCount) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Products at or below reorder level demanding urgent attention.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.47 21h15.06c1.54 0 2.5-1.67 1.73-3L13.73 4.99c-.77-1.33-2.69-1.33-3.46 0L2.74 18c-.77 1.33.19 3 1.73 3zM13 18h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Action Priority</p>
                        <div class="mt-1">
                            <p class="text-lg font-bold text-black">{{ $outOfStockCount > 0 ? 'Restock Out-of-Stock First' : 'Inventory Stable' }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Recommended first step for replenishment planning.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 2v11h3v9l7-12h-4l4-8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between h-full overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Out of stock products</h2>
                        <p class="text-xs text-slate-500 mt-1">Products that need immediate restocking.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        Critical
                    </span>
                </div>

                <div class="mt-4 flex-1 flex flex-col justify-between overflow-hidden rounded-[10px] border border-slate-200">
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider font-semibold text-white">
                                <tr>
                                    <th class="w-[30%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Product</th>
                                    <th class="w-[20%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Category</th>
                                    <th class="w-[25%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">SKU</th>
                                    <th class="w-[25%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Last restock</th>
                                </tr>
                            </thead>
                            <tbody id="outOfStockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse(collect($outOfStockProducts)->take(5) as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-2.5 font-semibold text-slate-900">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate font-medium text-slate-900">{{ $product->product_name ?: $product->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600">
                                            <div class="truncate">{{ $product->category }}</div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500 font-mono text-[11px]">
                                            <div class="truncate">{{ $product->sku }}</div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500">
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
                    <!-- Out of Stock Pagination -->
                    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                        <p id="outOfStockPageInfo" class="text-slate-600">Showing {{ collect($outOfStockProducts)->count() > 0 ? 1 : 0 }}-{{ min(5, collect($outOfStockProducts)->count()) }} of {{ collect($outOfStockProducts)->count() }} products</p>
                        <div id="outOfStockPaginationControls" class="flex gap-1">
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                            <button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" {{ collect($outOfStockProducts)->count() <= 5 ? 'disabled' : '' }}>Next →</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between h-full overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Low stock products</h2>
                        <p class="text-xs text-slate-500 mt-1">Products at or below reorder levels.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Review
                    </span>
                </div>

                <div class="mt-4 flex-1 flex flex-col justify-between overflow-hidden rounded-[10px] border border-slate-200">
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="w-[40%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Product</th>
                                    <th class="w-[20%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Stock</th>
                                    <th class="w-[20%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Reorder</th>
                                    <th class="w-[20%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Need</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockTableBody" class="divide-y divide-slate-200 bg-white">
                                @forelse(collect($lowStockProducts)->take(5) as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3 py-2.5 font-semibold text-slate-900">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate font-medium text-slate-900">{{ $product->product_name ?: $product->name }}</div>
                                                    <div class="truncate text-[10px] text-slate-400 font-normal tracking-wide mt-0.5">{{ $product->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-900">
                                            <div class="truncate">{{ number_format($product->stock_quantity) }}</div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-600">
                                            <div class="truncate">{{ number_format($product->reorder_level) }}</div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-900">
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
                    <!-- Low Stock Pagination -->
                    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                        <p id="lowStockPageInfo" class="text-slate-600">Showing {{ collect($lowStockProducts)->count() > 0 ? 1 : 0 }}-{{ min(5, collect($lowStockProducts)->count()) }} of {{ collect($lowStockProducts)->count() }} products</p>
                        <div id="lowStockPaginationControls" class="flex gap-1">
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                            <button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" {{ collect($lowStockProducts)->count() <= 5 ? 'disabled' : '' }}>Next →</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $outOfStockJs = $outOfStockProducts->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name ?: $product->name,
                'category' => $product->category,
                'sku' => $product->sku,
                'last_restock_date' => optional($product->last_restock_date)->format('M d, Y') ?? 'N/A',
                'stock_quantity' => $product->stock_quantity ?? 0,
                'reorder_level' => $product->reorder_level ?? 0,
                'need' => max(0, 100 - ($product->stock_quantity ?? 0)),
            ];
        })->toArray();

        $lowStockJs = $lowStockProducts->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name ?: $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'reorder_level' => $product->reorder_level,
                'need' => max(0, ($product->reorder_level ?? 0) - $product->stock_quantity),
            ];
        })->toArray();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const outOfStockData = @json($outOfStockJs);
            const lowStockData = @json($lowStockJs);

            const getProductImage = (p) => {
                if (!p) return null;
                if (p.image) return p.image;
                try {
                    const stored = localStorage.getItem('posProductImages');
                    if (stored) {
                        const images = JSON.parse(stored);
                        const productId = p.id || p.product_id;
                        if (productId && images[productId]) return images[productId];
                        if (p.sku && images[p.sku]) return images[p.sku];
                        if (p.name && images[p.name]) return images[p.name];

                        const keys = Object.keys(images);
                        if (p.sku) {
                            const matchSku = keys.find(k => k.toLowerCase() === String(p.sku).toLowerCase());
                            if (matchSku) return images[matchSku];
                        }
                        if (p.name) {
                            const matchName = keys.find(k => k.toLowerCase() === String(p.name).toLowerCase());
                            if (matchName) return images[matchName];
                        }
                    }
                } catch (e) {}
                return null;
            };

            const renderProductImageHtml = (p) => {
                const imageUrl = getProductImage(p);
                return imageUrl
                    ? `<div class="w-8 h-8 rounded-[6px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image: url('${imageUrl}');"></div>`
                    : `<div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                       </div>`;
            };

            const state = {
                outOfStockPage: 1,
                lowStockPage: 1,
            };

            window.goToPage = function(stateKey, page) {
                state[stateKey] = page;
                if (stateKey === 'outOfStockPage') window.updateOutOfStock();
                if (stateKey === 'lowStockPage') window.updateLowStock();
            };

            function renderPaginatedTable({ data, stateKey, bodyId, infoId, controlsId, renderRow, emptyMessage }) {
                const body = document.getElementById(bodyId);
                const info = document.getElementById(infoId);
                const controls = document.getElementById(controlsId);

                if (!body || !info || !controls) return;

                const pageSize = 5;
                const page = state[stateKey] || 1;
                const totalPages = Math.max(1, Math.ceil(data.length / pageSize));
                const currentPage = Math.min(Math.max(page, 1), totalPages);
                state[stateKey] = currentPage;

                const start = (currentPage - 1) * pageSize;
                const end = Math.min(start + pageSize, data.length);
                const pageItems = data.slice(start, end);

                if (data.length === 0) {
                    info.textContent = 'Showing 0 of 0 products';
                    body.innerHTML = `<tr><td colspan="4" class="px-3 py-5 text-center text-slate-500">${emptyMessage}</td></tr>`;
                } else {
                    info.textContent = `Showing ${start + 1}-${end} of ${data.length} products`;
                    body.innerHTML = pageItems.map(renderRow).join('');
                }

                let controlsHtml = `<button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage <= 1 ? 'disabled' : ''} onclick="window.goToPage('${stateKey}', ${currentPage - 1})">← Prev</button>`;

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
                        controlsHtml += `<button type="button" onclick="window.goToPage('${stateKey}', ${i})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">${i}</button>`;
                    }
                }

                controlsHtml += `<button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage >= totalPages ? 'disabled' : ''} onclick="window.goToPage('${stateKey}', ${currentPage + 1})">Next →</button>`;

                controls.innerHTML = controlsHtml;
            }

            window.updateOutOfStock = function() {
                renderPaginatedTable({
                    data: outOfStockData,
                    stateKey: 'outOfStockPage',
                    bodyId: 'outOfStockTableBody',
                    infoId: 'outOfStockPageInfo',
                    controlsId: 'outOfStockPaginationControls',
                    renderRow: function(product) {
                        return `
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 font-semibold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        ${renderProductImageHtml(product)}
                                        <div class="min-w-0">
                                            <div class="truncate font-medium text-slate-900">${product.name}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-600">
                                    <div class="truncate">${product.category || 'N/A'}</div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-500 font-mono text-[11px]">
                                    <div class="truncate">${product.sku || 'N/A'}</div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-500">
                                    <div class="truncate">${product.last_restock_date || 'N/A'}</div>
                                </td>
                            </tr>
                        `.trim();
                    },
                    emptyMessage: 'No out of stock items currently.',
                });
            };

            window.updateLowStock = function() {
                renderPaginatedTable({
                    data: lowStockData,
                    stateKey: 'lowStockPage',
                    bodyId: 'lowStockTableBody',
                    infoId: 'lowStockPageInfo',
                    controlsId: 'lowStockPaginationControls',
                    renderRow: function(product) {
                        return `
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-2.5 font-semibold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        ${renderProductImageHtml(product)}
                                        <div class="min-w-0">
                                            <div class="truncate font-medium text-slate-900">${product.name}</div>
                                            <div class="truncate text-[10px] text-slate-400 font-normal tracking-wide mt-0.5">${product.sku || 'N/A'}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-slate-900"><div class="truncate">${product.stock_quantity}</div></td>
                                <td class="px-3 py-2.5 text-slate-600"><div class="truncate">${product.reorder_level}</div></td>
                                <td class="px-3 py-2.5 text-slate-900"><div class="truncate">${product.need}</div></td>
                            </tr>
                        `.trim();
                    },
                    emptyMessage: 'No low stock products detected.',
                });
            };

            window.updateOutOfStock();
            window.updateLowStock();
        });
    </script>
</x-layouts.app>
