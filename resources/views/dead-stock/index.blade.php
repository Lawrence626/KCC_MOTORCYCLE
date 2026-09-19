<x-layouts.app :title="__('Dead Stock Analysis')">
    <div id="dead-stock-root"
         data-export-excel-url="{{ route('dss.dead-stock.export-excel') }}"
         data-export-pdf-url="{{ route('dss.dead-stock.export-pdf') }}"
         data-api-url="{{ route('api.dss.dead-stocks.index') }}"
         data-dashboard-stats-url="{{ route('api.dss.dashboard-stats') }}"
         data-csrf="{{ csrf_token() }}"
         class="space-y-4">

        {{-- ═══ HEADER ═══ --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Dead Stock Analysis</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Inventory items without sales for <span class="text-slate-900 font-semibold">{{ $thresholdDays }} days</span> or more.
                </p>
            </div>
            <div class="flex items-center gap-2 pr-4">
                <div class="relative" data-dropdown-wrapper="exportMenu">
                    <button type="button"
                            id="exportDropdownBtn"
                            onclick="toggleCustomDropdown('exportDropdownMenu', event)"
                            class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200">
                        <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Export</span>
                        <svg class="h-4 w-4 text-slate-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="exportDropdownMenu"
                         class="dropdown-menu hidden absolute right-0 top-full z-50 mt-1.5 w-44 rounded-[12px] border border-slate-200 bg-white p-1.5 shadow-xl space-y-0.5">
                        <a href="{{ route('dss.dead-stock.export-excel') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                           onclick="exportDeadStock('csv', event)"
                           class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-[8px] transition cursor-pointer">
                            <svg class="h-4 w-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Export CSV</span>
                        </a>
                        <a href="{{ route('dss.dead-stock.export-pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                           target="_blank"
                           onclick="exportDeadStock('pdf', event)"
                           class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-[8px] transition cursor-pointer">
                            <svg class="h-4 w-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span>Export PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- ═══ SUCCESS ALERTS ═══ --}}
        @if(session('success'))
        <div id="deadStockSuccessAlert" class="rounded-[14px] border border-teal-200 bg-teal-50 px-4 py-3 text-xs font-semibold text-teal-900 flex items-center gap-3">
            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- ═══ KPI CARDS ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            {{-- Total Items --}}
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Total Items</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ $totalDeadStocks }}</p>
                            <p class="text-gray-500 text-[11px] mt-1 font-medium truncate">Identified items</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Value at Risk --}}
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Value at Risk</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">₱{{ number_format($totalValue, 0) }}</p>
                            <p class="text-gray-500 text-[11px] mt-1 font-medium truncate">Total capital locked</p>
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

        {{-- ═══ MAIN DATA TABLE ═══ --}}
        <div class="bg-white rounded-[20px] border border-slate-200 shadow-sm flex flex-col overflow-hidden">
            {{-- Toolbar --}}
            <div class="px-4 py-3 border-b border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="GET" action="{{ route('dss.dead-stock.index') }}" id="deadStockFilterForm" class="w-full flex flex-col sm:flex-row items-center gap-2">
                    <div class="relative w-full max-w-[450px]">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inventory..."
                               class="w-full pl-9 pr-3 h-9 text-xs rounded-[12px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm">
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Custom Sort Dropdown -->
                        <div class="relative min-w-[150px]" data-dropdown-wrapper="sortFilter">
                            <input type="hidden" name="sort_by" id="sortFilter" value="{{ request('sort_by', 'days_without_sale') }}" />
                            <button type="button" id="sortFilterBtn" onclick="toggleCustomDropdown('sortFilterDropdown', event)" class="w-full h-9 rounded-[12px] border border-slate-300 bg-white px-3 text-left text-xs text-slate-900 flex items-center justify-between gap-2 hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm">
                                <span id="sortFilterDisplay">
                                    @switch(request('sort_by', 'days_without_sale'))
                                        @case('stock_value') Highest Value @break
                                        @case('current_stock') Highest Stock @break
                                        @case('last_sold_date') Last Sold @break
                                        @default Longest Unsold
                                    @endswitch
                                </span>
                                <svg class="w-4 h-4 text-slate-500 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div id="sortFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full min-w-[150px] rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                                <button type="button" onclick="selectDeadStockFilter('sortFilter', 'days_without_sale', 'Longest Unsold', 'sortFilterDisplay', 'sortFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Longest Unsold</button>
                                <button type="button" onclick="selectDeadStockFilter('sortFilter', 'stock_value', 'Highest Value', 'sortFilterDisplay', 'sortFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Highest Value</button>
                                <button type="button" onclick="selectDeadStockFilter('sortFilter', 'current_stock', 'Highest Stock', 'sortFilterDisplay', 'sortFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Highest Stock</button>
                                <button type="button" onclick="selectDeadStockFilter('sortFilter', 'last_sold_date', 'Last Sold', 'sortFilterDisplay', 'sortFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Last Sold</button>
                            </div>
                        </div>

                        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                        <button type="submit" class="shrink-0 h-9 w-9 rounded-[12px] border border-slate-300 bg-white text-slate-700 hover:bg-black/10 transition shadow-sm flex items-center justify-center" title="Apply Filters">
                            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </button>
                        @if(request('search') || request('sort_by'))
                        <a href="{{ route('dss.dead-stock.index') }}" id="clearDeadStockFilters" class="text-xs font-semibold text-slate-500 hover:text-slate-700 transition">Clear</a>
                        @endif
                    </div>
                </form>
            </div>

            <div id="deadStockTableContainer" class="relative transition-opacity duration-200">
                @include('dead-stock.partials.table')
            </div>
        </div>
    </div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
    let isFetchingTable = false;

    async function loadDeadStockTable(url, updateHistory = true) {
        if (isFetchingTable) return;
        const container = document.getElementById('deadStockTableContainer');
        if (!container) return;

        isFetchingTable = true;
        container.style.opacity = '0.4';
        container.style.pointerEvents = 'none';

        try {
            const fetchUrl = new URL(url, window.location.origin);

            const res = await fetch(fetchUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

            const data = await res.json();
            if (data && data.html !== undefined) {
                container.innerHTML = data.html;
                if (updateHistory) {
                    window.history.pushState({ url: fetchUrl.toString() }, '', fetchUrl.toString());
                }
                resolveDeadStockImages();

                const rect = container.getBoundingClientRect();
                if (rect.top < 70) {
                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        } catch (err) {
            console.error('Failed to load table via AJAX, falling back to full page load:', err);
            window.location.href = url;
        } finally {
            isFetchingTable = false;
            if (container) {
                container.style.opacity = '1';
                container.style.pointerEvents = '';
            }
        }
    }

    // Intercept clicks on pagination navigation links
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#deadStockTableContainer nav[role="navigation"] a, #deadStockTableContainer .pagination a, #deadStockTableContainer a[rel="prev"], #deadStockTableContainer a[rel="next"]');
        if (!paginationLink) return;

        e.preventDefault();
        loadDeadStockTable(paginationLink.href, true);
    });

    // Intercept search/sort filter form submit for seamless table updates
    document.getElementById('deadStockFilterForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        const url = `${this.action}?${params.toString()}`;
        loadDeadStockTable(url, true);
    });

    // Intercept clear filters link
    document.addEventListener('click', function(e) {
        const clearBtn = e.target.closest('#clearDeadStockFilters');
        if (!clearBtn) return;
        e.preventDefault();
        const searchInput = document.querySelector('#deadStockFilterForm input[name="search"]');
        if (searchInput) searchInput.value = '';
        const sortInput = document.getElementById('sortFilter');
        if (sortInput) sortInput.value = 'days_without_sale';
        const sortDisplay = document.getElementById('sortFilterDisplay');
        if (sortDisplay) sortDisplay.textContent = 'Sort by Days';
        loadDeadStockTable(clearBtn.href, true);
    });

    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.url) {
            loadDeadStockTable(e.state.url, false);
        } else {
            loadDeadStockTable(window.location.href, false);
        }
    });

    function toggleCustomDropdown(dropdownId, event) {
        if (event) event.stopPropagation();
        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;
        const isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        if (isHidden) {
            dropdown.classList.remove('hidden');
        }
    }

    function selectDeadStockFilter(inputId, value, displayText, displayId, dropdownId) {
        const inputElem = document.getElementById(inputId);
        const displayElem = document.getElementById(displayId);
        const dropdown = document.getElementById(dropdownId);

        if (inputElem) inputElem.value = value;
        if (displayElem) displayElem.textContent = displayText;
        if (dropdown) dropdown.classList.add('hidden');

        const form = document.getElementById('deadStockFilterForm');
        if (form) {
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown-wrapper]')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    // Close dropdowns with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    function exportDeadStock(format, event) {
        if (event) {
            event.preventDefault();
        }
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        const baseUrl = format === 'csv'
            ? '{{ route("dss.dead-stock.export-excel") }}'
            : '{{ route("dss.dead-stock.export-pdf") }}';
        const params = window.location.search;
        const finalUrl = baseUrl + (params ? params : '');
        if (format === 'pdf') {
            window.open(finalUrl, '_blank');
        } else {
            window.location.href = finalUrl;
        }
    }

    function resolveDeadStockImages() {
        try {
            const stored = localStorage.getItem('posProductImages');
            if (!stored) return;
            const images = JSON.parse(stored);
            const keys = Object.keys(images);

            document.querySelectorAll('.deadstock-img-thumb').forEach(container => {
                const id = container.dataset.id;
                const sku = container.dataset.sku;
                const name = container.dataset.name;

                let imgUrl = null;
                if (id && images[id]) imgUrl = images[id];
                else if (sku && images[sku]) imgUrl = images[sku];
                else if (name && images[name]) imgUrl = images[name];
                else {
                    if (sku) {
                        const matchSku = keys.find(k => k.toLowerCase() === String(sku).toLowerCase());
                        if (matchSku) imgUrl = images[matchSku];
                    }
                    if (!imgUrl && name) {
                        const matchName = keys.find(k => k.toLowerCase() === String(name).toLowerCase());
                        if (matchName) imgUrl = images[matchName];
                    }
                }

                if (imgUrl) {
                    container.innerHTML = '';
                    container.className = 'deadstock-img-thumb w-8 h-8 rounded-[6px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center';
                    container.style.backgroundImage = `url('${imgUrl}')`;
                }
            });
        } catch(e) {
            console.error('Error resolving dead stock images:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', resolveDeadStockImages);
</script>
</x-layouts.app>
