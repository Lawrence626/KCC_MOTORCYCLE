<x-layouts.app :title="__('Archived Items')">
    <div class="flex flex-col gap-5" style="min-height: calc(100vh - 200px);">
        <!-- Header -->
       <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Archived Items</h1>
                <p class="text-2XL text-slate-500 mt-0.5">Archived inventory items. Restore or permanently delete</p>
            </div>
            <div class="flex gap-2 items-center">
                <div class="relative flex-shrink-0" data-dropdown-wrapper="backNavigation">
                    <button type="button" id="backNavigationButton" onclick="toggleCustomDropdown('backNavigationDropdown', event)" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                        <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>BACK</span>
                        <svg class="w-3.5 h-3.5 text-slate-500 ml-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="backNavigationDropdown" class="dropdown-menu hidden absolute top-full right-0 z-[999] mt-1.5 w-48 rounded-[12px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-1">
                        <a href="{{ route('allstocks') }}" class="block text-left px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">Back to All Stocks</a>
                        <a href="{{ route('pos.terminal') }}" class="block text-left px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">Back to POS</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="border border-slate-200 p-5 bg-white shadow-sm flex items-start justify-between gap-3" style="border-radius: 28px;">
                <div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Archived Items</p>
                    <p id="archivedCount" class="text-2xl font-bold text-slate-900">0</p>
                    <p class="text-sm text-slate-500 mt-1">Total archived</p>
                </div>
                <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[rgba(110,193,209,0.18)] text-[#145a66]">
                    <!-- Cube Icon (represents inventory items) -->
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.378 1.602a.75.75 0 00-.756 0L3 6.632l9 5.25 9-5.25-8.622-5.03zM21.75 7.93l-9 5.25v9.344l8.628-5.032a.75.75 0 00.372-.648V7.93zM11.25 22.523v-9.344l-9-5.25v8.914c0 .267.141.514.372.648l8.628 5.032z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
            <div class="border border-slate-200 p-5 bg-white shadow-sm flex items-start justify-between gap-3" style="border-radius: 28px;">
                <div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Archive Value</p>
                    <p id="archiveValue" class="text-2xl font-bold text-slate-900">₱0</p>
                    <p class="text-sm text-slate-500 mt-1">Total value</p>
                </div>
                <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[rgba(110,193,209,0.18)] text-[#145a66]">
                    <!-- Credit Card Icon (represents monetary value) -->
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.5 3.75a3 3 0 00-3 3v.75h21v-.75a3 3 0 00-3-3h-15z" />
                        <path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 003 3h15a3 3 0 003-3v-7.5zm-18 3.75a.75.75 0 01.75-.75h6a.75.75 0 010 1.5h-6a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
            <div class="border border-slate-200 p-5 bg-white shadow-sm flex items-start justify-between gap-3" style="border-radius: 28px;">
                <div>
                    <p class="text-sm text-slate-600 font-medium mb-1">Avg Price</p>
                    <p id="avgPrice" class="text-2xl font-bold text-slate-900">₱0</p>
                    <p class="text-sm text-slate-500 mt-1">Per item</p>
                </div>
                <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[rgba(110,193,209,0.18)] text-[#145a66]">
                    <!-- Bar Chart Icon (represents average / stats) -->
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-[10px] border border-slate-200 p-4 space-y-4 shadow-sm">
            <input id="searchInput" type="search" placeholder="Search by product name, SKU, or barcode..." class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-white text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent" />

            <div class="flex gap-3 items-end">
                <div class="flex-1 relative">
                    <label class="block text-slate-600 font-medium mb-1.5 text-sm">Category</label>
                    <input type="hidden" id="categoryFilter" value="" />
                    <button type="button" id="categoryDropdownBtn" onclick="toggleCategoryDropdown()" class="w-full px-4 py-3 rounded-[10px] border border-slate-200 bg-white text-sm text-left text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-300 focus:border-transparent hover:border-slate-400 flex items-center justify-between transition">
                        <span id="categoryLabel">All Categories</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" id="categoryChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="categoryDropdown" class="hidden absolute top-full mt-2 left-0 w-full bg-white border border-slate-100 rounded-[18px] shadow-[0_16px_40px_rgba(0,0,0,0.12)] z-50 p-3 space-y-1 max-h-60 overflow-y-auto">
                        <button type="button" onclick="selectCategory('', 'All Categories')" class="w-full px-4 py-2 text-left text-sm bg-slate-100 text-slate-900 font-semibold rounded-[10px] category-option" data-value="">All Categories</button>
                        <button type="button" onclick="selectCategory('Exhaust', 'Exhaust')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Exhaust">Exhaust</button>
                        <button type="button" onclick="selectCategory('Helmets', 'Helmets')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Helmets">Helmets</button>
                        <button type="button" onclick="selectCategory('Tires', 'Tires')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Tires">Tires</button>
                        <button type="button" onclick="selectCategory('Brakes', 'Brakes')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Brakes">Brakes</button>
                        <button type="button" onclick="selectCategory('Oils', 'Oils')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Oils">Oils</button>
                        <button type="button" onclick="selectCategory('Batteries', 'Batteries')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Batteries">Batteries</button>
                        <button type="button" onclick="selectCategory('Accessories', 'Accessories')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] category-option" data-value="Accessories">Accessories</button>
                    </div>
                </div>
                <button id="applyFilter" class="h-[46px] px-6 rounded-[10px] border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-black/10 transition whitespace-nowrap flex items-center justify-center">Apply</button>
                <button id="resetFilter" class="h-[46px] px-6 rounded-[10px] border border-slate-300 bg-white text-sm font-semibold text-slate-600 hover:bg-black/10 transition whitespace-nowrap flex items-center justify-center">Reset</button>
            </div>
        </div>

        <!-- Archived Table -->
        <div class="bg-white rounded-[10px] border border-slate-200 shadow-sm flex-1 flex flex-col overflow-visible">
            <div class="overflow-x-auto overflow-y-visible rounded-[10px]">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="border-b border-slate-200 bg-[#0f172a] rounded-t-[10px] text-xs uppercase tracking-wider text-white">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold text-white rounded-tl-[10px]">Product</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">SKU</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Category</th>
                            <th class="px-3 py-3 text-center font-semibold text-white">Stock</th>
                            <th class="px-3 py-3 text-right font-semibold text-white">Unit Price</th>
                            <th class="px-3 py-3 text-right font-semibold text-white">Total Value</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Archived Date</th>
                            <th class="px-3 py-3 text-center font-semibold text-white rounded-tr-[10px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="archivedTableBody" class="divide-y divide-slate-200">
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500">Loading archived items...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer - Pagination -->
            <div id="pagination" class="hidden px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs overflow-visible relative z-30 pb-2">
                <div class="flex items-center gap-2 text-slate-600">
                    <span>Showing</span>
                    <div class="relative inline-block z-50" data-dropdown-wrapper="perPage">
                        <input type="hidden" id="perPage" value="10" />
                        <button type="button" id="perPageButton" onclick="toggleCustomDropdown('perPageDropdown', event)" class="px-2.5 py-1 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 flex items-center justify-between gap-1.5 hover:border-slate-400 focus:outline-none transition shadow-sm h-8 min-w-[56px]">
                            <span id="perPageDisplay">10</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="perPageDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1 w-full min-w-[60px] rounded-[10px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                            <button type="button" onclick="selectPerPage(10)" class="w-full text-left px-2 py-1 rounded-md text-xs font-semibold text-slate-900 bg-black/10 transition">10</button>
                            <button type="button" onclick="selectPerPage(25)" class="w-full text-left px-2 py-1 rounded-md text-xs font-medium text-slate-700 hover:bg-slate-100 transition">25</button>
                            <button type="button" onclick="selectPerPage(50)" class="w-full text-left px-2 py-1 rounded-md text-xs font-medium text-slate-700 hover:bg-slate-100 transition">50</button>
                            <button type="button" onclick="selectPerPage(100)" class="w-full text-left px-2 py-1 rounded-md text-xs font-medium text-slate-700 hover:bg-slate-100 transition">100</button>
                        </div>
                    </div>
                    <span id="showingText">of 0 items</span>
                </div>
                <div class="flex gap-1 items-center">
                    <button id="prevPage" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition">← Prev</button>
                    <div id="pageNumbers" class="flex items-center gap-1"></div>
                    <button id="nextPage" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition">Next →</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        #searchInput::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
            height: 14px;
            width: 14px;
            cursor: pointer;
            background-color: black;
            -webkit-mask-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 105.7 7.11L10.59 12l-4.9 4.89a1 1 0 101.41 1.41L12 13.41l4.89 4.9a1 1 0 001.41-1.41L13.41 12l4.9-4.89a1 1 0 000-1.4z"/></svg>');
            mask-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18.3 5.71a1 1 0 00-1.41 0L12 10.59 7.11 5.7A1 1 0 105.7 7.11L10.59 12l-4.9 4.89a1 1 0 101.41 1.41L12 13.41l4.89 4.9a1 1 0 001.41-1.41L13.41 12l4.9-4.89a1 1 0 000-1.4z"/></svg>');
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-position: center;
            mask-position: center;
        }
    </style>

    <script>
        let currentPage = 1;
        let perPage = 10;
        let totalPages = 1;

        async function loadArchivedProducts() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            
            const url = new URL('/api/products/archived', window.location.origin);
            url.searchParams.set('page', currentPage);
            url.searchParams.set('per_page', perPage);
            if (search) url.searchParams.set('search', search);
            if (category) url.searchParams.set('category', category);

            try {
                const response = await fetch(url);
                const result = await response.json();
                
                console.log('Archived products response:', result);
                
                if (result.success) {
                    renderTable(result.data);
                    updatePagination(result.pagination);
                    updateStats(result.data);
                } else {
                    console.error('API returned error:', result.message);
                    document.getElementById('archivedTableBody').innerHTML = '<tr><td colspan="8" class="px-3 py-8 text-center text-red-500">Error loading archived items: ' + (result.message || 'Unknown error') + '</td></tr>';
                }
            } catch (error) {
                console.error('Error loading archived products:', error);
                document.getElementById('archivedTableBody').innerHTML = '<tr><td colspan="8" class="px-3 py-8 text-center text-red-500">Error loading archived items. Please check console for details.</td></tr>';
            }
        }

        function getArchivedProductImage(p) {
            if (!p) return null;
            if (p.image) return p.image;
            try {
                const stored = localStorage.getItem('posProductImages');
                if (stored) {
                    const images = JSON.parse(stored);
                    const productId = p.id || p.product_id;
                    if (productId && images[productId]) return images[productId];
                    if (p.sku && images[p.sku]) return images[p.sku];

                    const keys = Object.keys(images);
                    if (p.sku) {
                        const matchSku = keys.find(k => k.toLowerCase() === String(p.sku).toLowerCase());
                        if (matchSku) return images[matchSku];
                    }
                }
            } catch (e) {}
            return null;
        }

        function renderArchivedProductImageHtml(p) {
            const imageUrl = getArchivedProductImage(p);
            return imageUrl
                ? `<div class="w-8 h-8 rounded-[6px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image: url('${imageUrl}');"></div>`
                : `<div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                   </div>`;
        }

        function renderTable(products) {
            const tbody = document.getElementById('archivedTableBody');
            tbody.innerHTML = '';

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-8 text-center text-slate-500">No archived items found</td></tr>';
                return;
            }

            products.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50 transition opacity-70';
                const unitPrice = parseFloat(product.unit_price) || 0;
                const totalValue = (product.stock_quantity || 0) * unitPrice;
                const archivedDate = product.updated_at ? new Date(product.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
                
                row.innerHTML = `
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2.5">
                            ${renderArchivedProductImageHtml(product)}
                            <div>
                                <p class="font-medium text-slate-900">${product.product_name || product.name || 'Unnamed'}</p>
                                <p class="text-xs text-slate-500">${product.brand ? 'Brand: ' + product.brand : (product.description || '')}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-slate-600">${product.sku || 'N/A'}</td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">${product.category || 'Uncategorized'}</span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex flex-col items-center">
                            <span class="font-semibold text-slate-900">${product.stock_quantity || 0}</span>
                            <span class="text-xs text-slate-500">units</span>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-right font-medium text-slate-900">₱${unitPrice.toFixed(2)}</td>
                    <td class="px-3 py-2 text-right font-medium text-slate-900">₱${totalValue.toFixed(2)}</td>
                    <td class="px-3 py-2 text-slate-600">${archivedDate}</td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex gap-1 justify-center">
                            <button onclick="restoreProduct(${product.id})" class="p-1 text-slate-900 hover:bg-slate-100 rounded transition" title="Restore">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <button onclick="permanentDeleteProduct(${product.id})" class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Permanently Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function updatePagination(pagination) {
            totalPages = pagination.last_page || 1;
            currentPage = pagination.current_page || 1;
            
            document.getElementById('showingText').textContent = `of ${pagination.total} items`;
            document.getElementById('prevPage').disabled = currentPage <= 1;
            document.getElementById('nextPage').disabled = currentPage >= totalPages;

            const pageNumbersContainer = document.getElementById('pageNumbers');
            if (pageNumbersContainer) {
                let numsHtml = '';
                for (let p = 1; p <= totalPages; p++) {
                    if (p === currentPage) {
                        numsHtml += `<button type="button" disabled class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">${p}</button>`;
                    } else {
                        numsHtml += `<button type="button" onclick="goToPage(${p})" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">${p}</button>`;
                    }
                }
                pageNumbersContainer.innerHTML = numsHtml;
            }
            
            const paginationDiv = document.getElementById('pagination');
            if (pagination.total > 0) {
                paginationDiv.classList.remove('hidden');
            } else {
                paginationDiv.classList.add('hidden');
            }
        }

        function goToPage(p) {
            currentPage = p;
            loadArchivedProducts();
        }

        function updateStats(products) {
            const count = products.length;
            const totalValue = products.reduce((sum, p) => sum + ((p.stock_quantity || 0) * (p.unit_price || 0)), 0);
            const avgPrice = count > 0 ? totalValue / count : 0;
            
            document.getElementById('archivedCount').textContent = count;
            document.getElementById('archiveValue').textContent = `₱${totalValue.toFixed(2)}`;
            document.getElementById('avgPrice').textContent = `₱${avgPrice.toFixed(2)}`;
        }

        async function restoreProduct(id) {
            if (!confirm('Are you sure you want to restore this product?')) return;
            
            try {
                const response = await fetch(`/api/product/${id}/restore`, { method: 'POST' });
                const result = await response.json();
                
                if (result.success) {
                    alert('Product restored successfully');
                    loadArchivedProducts();
                } else {
                    alert('Failed to restore product: ' + result.message);
                }
            } catch (error) {
                console.error('Error restoring product:', error);
                alert('Error restoring product');
            }
        }

        async function permanentDeleteProduct(id) {
            if (!confirm('Are you sure you want to permanently delete this product? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`/api/product/${id}/permanent`, { method: 'DELETE' });
                const result = await response.json();
                
                if (result.success) {
                    alert('Product permanently deleted');
                    loadArchivedProducts();
                } else {
                    alert('Failed to delete product: ' + result.message);
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Error deleting product');
            }
        }

        // Custom Dropdown Toggle (for per-page card)
        function toggleCustomDropdown(id, event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById(id);
            if (!dropdown) return;
            const isHidden = dropdown.classList.contains('hidden');

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });

            if (isHidden) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function selectPerPage(val) {
            perPage = parseInt(val);
            document.getElementById('perPage').value = val;
            document.getElementById('perPageDisplay').textContent = val;
            
            const dropdown = document.getElementById('perPageDropdown');
            if (dropdown) {
                dropdown.querySelectorAll('button').forEach(btn => {
                    if (btn.textContent.trim() === String(val)) {
                        btn.className = 'w-full text-left px-2 py-1 rounded-md text-xs font-semibold text-slate-900 bg-black/10 transition';
                    } else {
                        btn.className = 'w-full text-left px-2 py-1 rounded-md text-xs font-medium text-slate-700 hover:bg-slate-100 transition';
                    }
                });
                dropdown.classList.add('hidden');
            }
            currentPage = 1;
            loadArchivedProducts();
        }

        // Category custom dropdown
        function toggleCategoryDropdown() {
            const dd = document.getElementById('categoryDropdown');
            dd.classList.toggle('hidden');
        }

        function selectCategory(value, label) {
            document.getElementById('categoryFilter').value = value;
            document.getElementById('categoryLabel').textContent = label;
            document.getElementById('categoryDropdown').classList.add('hidden');

            // Highlight active option
            document.querySelectorAll('.category-option').forEach(btn => {
                const isActive = btn.dataset.value === value;
                btn.classList.toggle('bg-slate-100', isActive);
                btn.classList.toggle('text-slate-900', isActive);
                btn.classList.toggle('font-semibold', isActive);
                btn.classList.toggle('text-slate-700', !isActive);
                btn.classList.toggle('hover:bg-slate-100', !isActive);
            });
        }

        // Click outside to close custom dropdowns
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('categoryDropdown');
            const btn = document.getElementById('categoryDropdownBtn');
            if (dd && btn && !dd.contains(e.target) && !btn.contains(e.target)) {
                dd.classList.add('hidden');
            }
            if (!e.target.closest('[data-dropdown-wrapper]')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
            }
        });

        // Event listeners
        document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; loadArchivedProducts(); });
        document.getElementById('resetFilter').addEventListener('click', () => {
            document.getElementById('searchInput').value = '';
            selectCategory('', 'All Categories');
            currentPage = 1;
            loadArchivedProducts();
        });
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) { currentPage--; loadArchivedProducts(); } });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; loadArchivedProducts(); } });

        // Load on page load
        loadArchivedProducts();
    </script>
</x-layouts.app>