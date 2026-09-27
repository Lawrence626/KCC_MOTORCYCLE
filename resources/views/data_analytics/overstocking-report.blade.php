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


    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Overstocking Report</h1>
                <p class="text-xs text-slate-500 mt-0.5">Identify excess inventory and categories that are tying up working capital.</p>

            </div>
            <a href="{{ route('analytics.overstocking.export') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200">
                <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export Overstock Report</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

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
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate font-medium text-slate-900">{{ $product->product_name ?: $product->name }}</div>
                                                    <div class="text-[11px] text-slate-400 font-mono truncate">{{ $product->sku }}</div>
                                                </div>
                                            </div>
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
                <div id="categoryExposureList" class="mt-4 space-y-2 flex-1 overflow-y-auto max-h-[340px] pr-1 custom-scrollbar">
                    @forelse($categoryBreakdown as $category => $value)
                        @php
                            $sample = $overstockedProducts->firstWhere('category', $category);
                        @endphp
                        <div class="category-exposure-item rounded-[14px] border border-slate-100 p-3 transition hover:border-slate-200" style="background: linear-gradient(50deg, #ffffff 0%, rgba(110, 193, 209, 0.18) 50%);"
                             data-category="{{ $category }}"
                             data-product-id="{{ $sample?->id }}"
                             data-sku="{{ $sample?->sku }}"
                             data-name="{{ $sample?->product_name ?: $sample?->name }}">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="category-img-container w-9 h-9 rounded-[8px] bg-white/90 border border-slate-200/70 flex items-center justify-center flex-shrink-0 text-slate-500 shadow-sm overflow-hidden">
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 text-xs truncate">{{ $category ?: 'Uncategorized' }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">Potential value by category</p>
                                    </div>
                                </div>
                                <p class="text-slate-900 font-semibold text-xs whitespace-nowrap">₱{{ number_format($value, 2) }}</p>
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
                'id' => $product->id,
                'product_id' => $product->id,
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
        // Notification Panel Toggle
        function toggleNotificationPanel(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                        profileDropdown.classList.remove('opacity-100', 'scale-100');
                        profileDropdown.classList.add('opacity-0', 'scale-95');
                    }
                } else {
                    panel.classList.add('hidden');
                }
            }
        }

        // Helper functions
        function markAllNotificationsRead() {
            // Placeholder for marking all notifications as read
            console.log('Mark all notifications as read');
        }

        function openAllNotificationsModal() {
            // Placeholder for opening all notifications modal
            console.log('Open all notifications modal');
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            const notificationBell = document.getElementById('notification-bell-btn');
            const profileButton = document.getElementById('dashboardProfileButton');

            if (panel && !panel.contains(event.target) && notificationBell && !notificationBell.contains(event.target)) {
                panel.classList.add('hidden');
            }

            if (profileDropdown && !profileDropdown.contains(event.target) && profileButton && !profileButton.contains(event.target)) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.remove('opacity-100', 'scale-100');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageSize = 5;
            const overstockData = @json($overstockJs);
            let currentPage = 1;

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
                const brand = String(p.brand || '').trim().toUpperCase();
                const desc = String(p.product_description || p.category || '').trim().toUpperCase();
                const name = String(p.product_name || p.name || '').trim().toUpperCase();
                const isApido = brand.includes('APIDO') || name.includes('APIDO');
                const isPipe = desc.includes('PIPE') || name.includes('PIPE') || desc.includes('EXHAUST') || name.includes('EXHAUST');
                if (isApido || (isPipe && brand.includes('APIDO'))) {
                    const apidoImages = ['/images/products/apido_pipe_1.png', '/images/products/apido_pipe_2.png', '/images/products/apido_pipe_3.png'];
                    const seedStr = String(p.id || p.product_id || '') + String(p.name || p.product_name || p.sku || '');
                    let hash = 0;
                    for (let i = 0; i < seedStr.length; i++) {
                        hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
                    }
                    return apidoImages[Math.abs(hash) % apidoImages.length];
                }

                const isKvin = brand.includes('KVIN') || brand.includes('K-VIN') || brand.includes('K VIN') ||
                               name.includes('KVIN') || name.includes('K-VIN') || name.includes('K VIN');
                if (isKvin) {
                    const kvinImages = ['/images/products/kvin_pipe_1.png', '/images/products/kvin_pipe_2.png'];
                    const seedStr = String(p.id || p.product_id || '') + String(p.name || p.product_name || p.sku || '');
                    let hash = 0;
                    for (let i = 0; i < seedStr.length; i++) {
                        hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
                    }
                    return kvinImages[Math.abs(hash) % kvinImages.length];
                }

                const isTrc = brand === 'TRC' || brand.includes('TRC') || name.includes('TRC') || String(p.sku || '').toUpperCase().includes('TRC');
                if (isTrc) {
                    const trcImages = ['/images/products/trc_pipe_1.png', '/images/products/trc_pipe_2.png', '/images/products/trc_pipe_3.png'];
                    const seedStr = String(p.id || p.product_id || '') + String(p.name || p.product_name || p.sku || '');
                    let hash = 0;
                    for (let i = 0; i < seedStr.length; i++) {
                        hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
                    }
                    return trcImages[Math.abs(hash) % trcImages.length];
                }

                const isMt8 = brand === 'MT8' || brand.includes('MT8') || brand.includes('MT-8') || brand.includes('MT 8') ||
                              name.includes('MT8') || name.includes('MT-8') || name.includes('MT 8') ||
                              String(p.sku || '').toUpperCase().includes('MT8') || String(p.sku || '').toUpperCase().includes('MT-8');
                if (isMt8) {
                    const mt8Images = ['/images/products/mt8_pipe_1.png', '/images/products/mt8_pipe_2.png', '/images/products/mt8_pipe_3.png'];
                    const seedStr = String(p.id || p.product_id || '') + String(p.name || p.product_name || p.sku || '');
                    let hash = 0;
                    for (let i = 0; i < seedStr.length; i++) {
                        hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
                    }
                    return mt8Images[Math.abs(hash) % mt8Images.length];
                }

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
                                <div class="flex items-center gap-2.5">
                                    ${renderProductImageHtml(product)}
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-slate-900">${product.name}</div>
                                        <div class="text-[11px] text-slate-400 font-mono truncate">${product.sku || 'N/A'}</div>
                                    </div>
                                </div>
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

            function renderCategoryExposureImages() {
                const items = document.querySelectorAll('.category-exposure-item');
                items.forEach(item => {
                    const category = item.dataset.category || '';
                    const productId = item.dataset.productId || '';
                    const sku = item.dataset.sku || '';
                    const name = item.dataset.name || '';

                    const imgContainer = item.querySelector('.category-img-container');
                    if (!imgContainer) return;

                    const imageUrl = getProductImage({ id: productId, sku, name }) || getProductImage({ name: category });
                    if (imageUrl) {
                        imgContainer.innerHTML = '';
                        imgContainer.className = 'category-img-container w-9 h-9 rounded-[8px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center shadow-sm';
                        imgContainer.style.backgroundImage = `url('${imageUrl}')`;
                    } else {
                        imgContainer.className = 'category-img-container w-9 h-9 rounded-[8px] bg-slate-50 border border-slate-200/60 flex items-center justify-center flex-shrink-0 text-slate-300 shadow-sm overflow-hidden';
                        imgContainer.style.backgroundImage = 'none';
                        imgContainer.innerHTML = `<svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;
                    }
                });
            }

            // Initial render
            render();
            renderCategoryExposureImages();
        });
    </script>
</x-layouts.app>
