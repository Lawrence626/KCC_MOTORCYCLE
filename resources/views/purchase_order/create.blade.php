<x-layouts.app :title="__('Create Purchase Order')">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Create Purchase Order</h1>
                <p class="text-xs text-slate-500 mt-0.5">Select products first, then choose a qualified supplier. Pricing insights update automatically.</p>
            </div>
            <a href="{{ route('order.management') }}"
               class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 transition-all duration-200">
                <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to orders
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="rounded-[10px] border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <strong class="block font-semibold">Please fix the following:</strong>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('order.store') }}" method="POST" id="po-form" class="space-y-6">
        @csrf
        <div id="selected-products-hidden-inputs"></div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 1 – SELECT PRODUCTS
        ════════════════════════════════════════════════════════════ --}}
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">1</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Select Products to Reorder</h2>
                    <p class="text-xs text-slate-300">Choose from low-stock products. The supplier list will update automatically.</p>
                </div>
            </div>

            <div class="p-6">
                @if(!empty($selectedProductIds))
                    <div class="mb-4 rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        ✓ Low-stock alert pre-selected products for replenishment. Review and confirm your selection.
                    </div>
                @endif

                {{-- Filter bar: Search (left) + Dropdown (right) --}}
                <div class="mb-4">
                    <p class="text-sm font-medium text-slate-700 mb-1">Low stock products</p>
                    <p class="text-xs text-slate-500 mb-2">Select the items to include in the order.</p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative flex-1 max-w-sm">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                </svg>
                            </span>
                            <input type="text"
                                   id="product-search"
                                   placeholder="Search by Product Name or SKU…"
                                   class="w-full rounded-[10px] border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 outline-none transition-colors focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="flex-shrink-0 relative z-50" data-dropdown-wrapper="movementFilter">
                            @php
                                $filters = [
                                    'all'           => 'All',
                                    'fast_moving'   => 'Fast moving',
                                    'slow_moving'   => 'Slow moving',
                                    'special_order' => 'Special order',
                                ];
                                $currentFilterLabel = $filters[$currentFilter ?? 'all'] ?? 'All';
                            @endphp
                            <input type="hidden" name="movement" id="movementFilterInput" value="{{ $currentFilter ?? 'all' }}" />
                            <button type="button" id="movementFilterButton" onclick="toggleDropdown('movementFilterDropdown')" class="rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-left text-sm text-slate-900 flex items-center justify-between gap-2 hover:ring-1 hover:ring-black/35 focus:outline-none focus:ring-1 focus:ring-black/35 min-w-[160px]">
                                <span>{{ $currentFilterLabel }}</span>
                                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="movementFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                                @foreach($filters as $key => $label)
                                    <button type="button" onclick="selectMovementFilter(event, '{{ $key }}', '{{ $label }}')" class="w-full px-4 py-2.5 text-left text-sm {{ ($currentFilter ?? 'all') === $key ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div id="table-wrapper" class="relative overflow-hidden rounded-[10px] border border-slate-200">
                    <div id="table-loading-overlay" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10 pointer-events-none">
                        <div class="flex items-center justify-center p-2.5 bg-slate-900 text-white rounded-full shadow-lg">
                            <svg class="animate-spin h-5 w-5 text-[#00fff2]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                    </div>
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200 rounded-t-[10px]" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-[10px]">
                                    <input type="checkbox" id="select-all-products"
                                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 "
                                           title="Select all" />
                                </th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Movement</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Reorder Level</th>
                                 <th class="px-4 py-3">
                                     <div class="flex items-center gap-1">
                                         <span>Qty to Order</span>
                                         <div class="group relative cursor-pointer inline-flex items-center justify-center">
                                             <svg class="h-3.5 w-3.5 text-slate-400 hover:text-slate-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                             </svg>
                                             <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 hidden group-hover:block w-48 rounded-xl bg-slate-900 px-3 py-2 text-[10px] font-semibold normal-case tracking-normal text-white text-center shadow-xl z-20">
                                                 <span class="relative z-10">Maximum Stock (100) − Current Stock</span>
                                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-4 border-transparent border-b-slate-900"></div>
                                             </div>
                                         </div>
                                     </div>
                                 </th>
                                <th class="px-4 py-3 rounded-tr-[10px]">Unit Price (₱)</th>
                            </tr>
                        </thead>
                        <tbody id="product-table-body" class="divide-y divide-slate-200 text-slate-700">
                            @include('purchase_order.partials.product-rows', ['lowStockProducts' => $lowStockProducts, 'selectedProductIds' => $selectedProductIds])
                        </tbody>
                    </table>
                </div>

                <div id="pagination-container" class="mt-4 px-2">{{ $lowStockProducts->links() }}</div>

                <div id="selected-count-bar" class="mt-4 hidden rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 font-medium">
                    <span id="selected-count-text"></span>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 2 – SELECT SUPPLIER
        ════════════════════════════════════════════════════════════ --}}
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">2</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Select Supplier</h2>
                    <p class="text-xs text-slate-300">Only suppliers that can fulfill every selected product are shown.</p>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div id="supplier-loading" class="hidden flex items-center gap-2 text-sm text-slate-500">
                    <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Finding qualified suppliers…
                </div>

                <div id="no-product-hint" class="rounded-[10px] border border-gray-300 bg-gray-200/50 px-4 py-3 text-sm text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    Select at least one product above to see qualified suppliers.
                </div>

                <div id="no-supplier-message" class="hidden rounded-[10px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                <div id="supplier-dropdown-wrapper" class="hidden relative max-w-sm z-30" data-dropdown-wrapper="supplierSelect">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Supplier</label>
                    <input type="hidden" name="supplier_id" id="supplier-select" value="" />
                    <button type="button"
                            id="supplierSelectButton"
                            onclick="toggleSupplierDropdown(event)"
                            class="w-full rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-left text-sm text-slate-900 flex items-center justify-between shadow-sm cursor-pointer hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 transition">
                        <span id="supplierSelectDisplay" class="truncate text-slate-400 font-normal">Select supplier…</span>
                        <svg id="supplierSelectArrow" class="w-4 h-4 text-slate-600 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="supplierSelectDropdown"
                         class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1.5 w-full rounded-[16px] border border-slate-200 bg-white shadow-xl p-3 space-y-1 max-h-60 overflow-y-auto">
                        <div id="supplierSelectList" class="space-y-1">
                            <!-- Supplier checkboxes dynamically loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 3 – SUPPLIER INFORMATION
        ════════════════════════════════════════════════════════════ --}}
        <div id="supplier-info-panel" class="hidden rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">3</span>
                <h2 class="text-sm font-semibold text-white">Supplier Information</h2>
            </div>
            <div class="p-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Supplier Name</p>
                        <p id="si-name" class="text-sm font-semibold text-slate-800">—</p>
                    </div>
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Contact Person</p>
                        <p id="si-contact" class="text-sm text-slate-700">—</p>
                    </div>
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Last Purchase</p>
                        <p id="si-last-purchase" class="text-sm text-slate-700">—</p>
                    </div>
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Reliability Score</p>
                        <p id="si-reliability" class="text-sm font-semibold text-slate-800">—</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 4+5+6 – PRICE HISTORY / SUMMARY / RECOMMENDATIONS
        ════════════════════════════════════════════════════════════ --}}
        <div id="price-analysis-panel" class="hidden rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">4</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Supplier Price Analysis</h2>
                    <p class="text-xs text-slate-300">Historical costs, trends, and purchasing recommendations per product.</p>
                </div>
            </div>
            <div id="price-analysis-content" class="p-6 space-y-8">
                {{-- Injected by JavaScript --}}
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             BONUS – SUPPLIER COMPARISON TABLE
        ════════════════════════════════════════════════════════════ --}}
        <div id="comparison-panel" class="hidden rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">★</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Supplier Comparison</h2>
                    <p class="text-xs text-slate-300">All qualified suppliers ranked by cost. Click Select to choose one.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div id="recommended-supplier-badge" class="hidden rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800"></div>
                <div class="overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3">Supplier</th>
                                <th class="px-4 py-3">Total Cost (₱)</th>
                                <th class="px-4 py-3">Avg Change</th>
                                <th class="px-4 py-3">Last Purchase</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="comparison-table-body" class="divide-y divide-slate-200 text-slate-700">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
            <a href="{{ route('order.management') }}"
               class="max-w-xs rounded-[10px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-black/10 transition-all duration-200">
                Cancel
            </a>
            <button type="submit"
                    class="max-w-xs inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#6EC1D1] px-5 py-3 text-sm font-semibold text-black shadow-sm hover:bg-[#59b2c2] transition-all duration-200">
                Submit Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    'use strict';

    const ROUTES = {
        filteredSuppliers: '{{ route("api.order.filtered_suppliers") }}',
        supplierDetails:   '{{ route("api.order.supplier_details") }}',
        comparison:        '{{ route("api.order.supplier_comparison") }}',
    };

    // Pre-selected IDs passed from the server (low-stock alert redirect)
    const preselectedIds = @json($selectedProductIds);

    // ── Helpers ────────────────────────────────────────────────────────────────
    const $el  = (id) => document.getElementById(id);
    const fmt  = (v) => v != null ? '₱' + Number(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '—';
    const fmtP = (v) => v != null ? (v > 0 ? '+' : '') + Number(v).toFixed(2) + '%' : '—';
    const icon = (t) => ({ increasing: '📈', decreasing: '📉', stable: '➡️' })[t] ?? '';

    function escHtml(str) {
        if (str == null) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

    // ── State ──────────────────────────────────────────────────────────────────
    let selectedProductIds = [];
    const selectedProductsStore = new Map();
    let currentSupplierId  = null;
    let filterTimer        = null;
    let isFetchingPage     = false;

    // ── DOM references ─────────────────────────────────────────────────────────
    const selectAllBox      = $el('select-all-products');
    const supplierSelect    = $el('supplier-select');
    const productSearch     = $el('product-search');
    const productTableBody  = $el('product-table-body') || document.querySelector('table tbody');
    const poForm            = $el('po-form');

    // ── Dropdown functions ────────────────────────────────────────────────────────
    function resetDropdownButtonStyles() {
        document.querySelectorAll('[id$="Button"]').forEach(btn => {
            btn.style.borderColor = '';
            btn.style.borderWidth = '';
            btn.style.boxShadow = '';
            btn.style.backgroundColor = '';
            const chevron = btn.querySelector('.w-4.h-4');
            if (chevron) chevron.style.color = '';
        });
    }

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const allDropdowns = document.querySelectorAll('.dropdown-menu');
        const button = document.getElementById(id.replace('Dropdown', 'Button'));

        allDropdowns.forEach(d => {
            if (d.id !== id) d.classList.add('hidden');
        });

        resetDropdownButtonStyles();

        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            if (button) {
                button.style.borderColor = 'rgba(0, 0, 0, 0.35)';
                button.style.borderWidth = '1px';
                button.style.boxShadow = 'none';
                button.style.backgroundColor = '#9ca3af !important';
                const chevron = button.querySelector('.w-4.h-4');
                if (chevron) chevron.style.color = 'black';
            }
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function selectMovementFilter(event, value, label) {
        if (event && typeof event.preventDefault === 'function') {
            event.preventDefault();
            event.stopPropagation();
        }

        document.getElementById('movementFilterInput').value = value;
        document.getElementById('movementFilterButton').querySelector('span').textContent = label;
        document.getElementById('movementFilterDropdown').classList.add('hidden');
        const button = document.getElementById('movementFilterButton');
        if (button) {
            button.style.borderColor = '';
            button.style.borderWidth = '';
            button.style.boxShadow = '';
        }

        // Navigate to new URL with filter via AJAX
        const url = new URL(window.location.href);
        url.searchParams.set('movement', value);
        url.searchParams.set('page', '1');
        fetchProducts(url.toString());
    }

    // Attach functions to window for inline onclick handlers
    window.toggleDropdown = toggleDropdown;
    window.selectMovementFilter = selectMovementFilter;

    // ── Supplier Dropdown Functions ───────────────────────────────────────────
    function updateSupplierTriggerDisplay(name) {
        const displaySpan = $el('supplierSelectDisplay');
        const button = $el('supplierSelectButton');
        if (!displaySpan) return;

        if (name) {
            displaySpan.textContent = name;
            displaySpan.className = 'truncate text-slate-900 font-medium text-sm';
            if (button) button.title = name;
        } else {
            displaySpan.textContent = 'Select supplier…';
            displaySpan.className = 'truncate text-slate-400 font-normal text-sm';
            if (button) button.title = '';
        }
    }

    function selectSupplier(id, name) {
        currentSupplierId = id ? parseInt(id, 10) : null;
        if (supplierSelect) {
            supplierSelect.value = currentSupplierId ? String(currentSupplierId) : '';
        }

        updateSupplierTriggerDisplay(name);

        const listContainer = $el('supplierSelectList');
        if (listContainer) {
            listContainer.querySelectorAll('button[data-supplier-id]').forEach(btn => {
                const isSelected = currentSupplierId && parseInt(btn.dataset.supplierId, 10) === currentSupplierId;
                if (isSelected) {
                    btn.className = 'w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-900 bg-black/10 rounded-[10px] transition cursor-pointer';
                } else {
                    btn.className = 'w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
                }
            });
        }

        if (currentSupplierId) {
            loadSupplierDetails(currentSupplierId);
        } else {
            hideSupplierPanels();
        }
    }

    function toggleSupplierDropdown(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const dropdown = $el('supplierSelectDropdown');
        const arrow = $el('supplierSelectArrow');
        if (!dropdown) return;

        const isCurrentlyHidden = dropdown.classList.contains('hidden');

        document.querySelectorAll('.dropdown-menu').forEach(d => {
            if (d !== dropdown) d.classList.add('hidden');
        });

        if (isCurrentlyHidden) {
            dropdown.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');
        } else {
            dropdown.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    }

    function closeSupplierDropdown() {
        const dropdown = $el('supplierSelectDropdown');
        const arrow = $el('supplierSelectArrow');
        if (dropdown) dropdown.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }

    window.toggleSupplierDropdown = toggleSupplierDropdown;
    window.closeSupplierDropdown = closeSupplierDropdown;
    window.selectSupplier = selectSupplier;

    document.addEventListener('click', function(event) {
        if (!event.target.closest('[data-dropdown-wrapper="supplierSelect"]')) {
            closeSupplierDropdown();
        }
        if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]') && !event.target.closest('#supplierSelectButton')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => {
                if (d.id !== 'supplierSelectDropdown') d.classList.add('hidden');
            });
            resetDropdownButtonStyles();
        }
    });

    // ── Store Synchronisation & State Management ──────────────────────────────
    function syncVisibleRowsToStore() {
        const rows = productTableBody ? productTableBody.querySelectorAll('.product-row') : [];
        rows.forEach(row => {
            const pid = parseInt(row.dataset.productId, 10);
            const cb = row.querySelector('.product-checkbox');
            if (!pid || !cb) return;

            if (cb.checked) {
                const qtyInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                const prev = selectedProductsStore.get(pid) || {};
                selectedProductsStore.set(pid, {
                    product_id: pid,
                    product_name: row.dataset.productName || row.querySelector('td:nth-child(2)')?.textContent?.trim() || prev.product_name || '',
                    sku: row.dataset.sku || row.querySelector('td:nth-child(4)')?.textContent?.trim() || prev.sku || '',
                    quantity: qtyInput ? (parseInt(qtyInput.value, 10) || 1) : (prev.quantity || parseInt(row.dataset.defaultQuantity, 10) || 1),
                    unit_price: priceInput ? (parseFloat(priceInput.value) || 0) : (prev.unit_price !== undefined ? prev.unit_price : (parseFloat(row.dataset.defaultUnitPrice) || 0)),
                    selected: true,
                });
            } else {
                selectedProductsStore.delete(pid);
            }
        });
    }

    function applyStoreToVisibleRows() {
        const rows = productTableBody ? productTableBody.querySelectorAll('.product-row') : [];
        rows.forEach(row => {
            const pid = parseInt(row.dataset.productId, 10);
            const cb = row.querySelector('.product-checkbox');
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[unit_price]"]');

            if (selectedProductsStore.has(pid)) {
                const item = selectedProductsStore.get(pid);
                if (cb) cb.checked = true;
                if (qtyInput && item.quantity !== undefined) qtyInput.value = item.quantity;
                if (priceInput && item.unit_price !== undefined) priceInput.value = item.unit_price;
            } else {
                if (cb) cb.checked = false;
            }
        });
    }

    function updateSelectAllCheckboxState() {
        if (!selectAllBox || !productTableBody) return;
        const visibleCheckboxes = Array.from(productTableBody.querySelectorAll('.product-checkbox'));
        if (visibleCheckboxes.length === 0) {
            selectAllBox.checked = false;
            selectAllBox.indeterminate = false;
            return;
        }
        const allChecked = visibleCheckboxes.every(cb => cb.checked);
        const someChecked = visibleCheckboxes.some(cb => cb.checked);

        selectAllBox.checked = allChecked;
        selectAllBox.indeterminate = !allChecked && someChecked;
    }

    function handleRowCheckboxChange(cb) {
        const row = cb.closest('.product-row');
        if (!row) return;
        const pid = parseInt(row.dataset.productId, 10);
        if (!pid) return;

        if (cb.checked) {
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[unit_price]"]');
            selectedProductsStore.set(pid, {
                product_id: pid,
                product_name: row.dataset.productName || row.querySelector('td:nth-child(2)')?.textContent?.trim() || '',
                sku: row.dataset.sku || row.querySelector('td:nth-child(4)')?.textContent?.trim() || '',
                quantity: qtyInput ? (parseInt(qtyInput.value, 10) || 1) : (parseInt(row.dataset.defaultQuantity, 10) || 1),
                unit_price: priceInput ? (parseFloat(priceInput.value) || 0) : (parseFloat(row.dataset.defaultUnitPrice) || 0),
                selected: true,
            });
        } else {
            selectedProductsStore.delete(pid);
        }

        updateSelectAllCheckboxState();
        onProductSelectionChange();
    }

    function handleRowInputChange(input) {
        const row = input.closest('.product-row');
        if (!row) return;
        const pid = parseInt(row.dataset.productId, 10);
        if (!pid) return;

        if (selectedProductsStore.has(pid)) {
            const item = selectedProductsStore.get(pid);
            if (input.name.includes('[quantity]')) {
                item.quantity = parseInt(input.value, 10) || 1;
            } else if (input.name.includes('[unit_price]')) {
                item.unit_price = parseFloat(input.value) || 0;
            }
        }
    }

    // ── AJAX Page Fetcher (No reload) ─────────────────────────────────────────
    async function fetchProducts(targetUrl, updateHistory = true) {
        if (isFetchingPage) return;
        isFetchingPage = true;

        syncVisibleRowsToStore();

        const overlay = $el('table-loading-overlay');
        if (overlay) overlay.classList.remove('hidden');
        if (productTableBody) productTableBody.classList.add('opacity-50');

        try {
            const fetchUrl = new URL(targetUrl, window.location.origin);
            const res = await fetch(fetchUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }

            const data = await res.json();

            if (productTableBody && data.table_html !== undefined) {
                productTableBody.innerHTML = data.table_html;
                resolvePoProductImages();
            }

            const paginationContainer = $el('pagination-container');
            if (paginationContainer && data.pagination_html !== undefined) {
                paginationContainer.innerHTML = data.pagination_html;
            }

            applyStoreToVisibleRows();
            updateSelectAllCheckboxState();

            if (updateHistory) {
                window.history.pushState({ url: targetUrl }, '', targetUrl);
            }

        } catch (err) {
            console.error('Failed to load products page:', err);
        } finally {
            isFetchingPage = false;
            if (overlay) overlay.classList.add('hidden');
            if (productTableBody) productTableBody.classList.remove('opacity-50');
        }
    }

    // ── Event Delegation for table rows ───────────────────────────────────────
    productTableBody?.addEventListener('change', function (e) {
        if (e.target.matches('.product-checkbox')) {
            handleRowCheckboxChange(e.target);
        }
    });

    productTableBody?.addEventListener('input', function (e) {
        if (e.target.matches('input[name*="[quantity]"]') || e.target.matches('input[name*="[unit_price]"]')) {
            handleRowInputChange(e.target);
        }
    });

    // ── Select-all toggle ─────────────────────────────────────────────────────
    selectAllBox?.addEventListener('change', function () {
        const isChecked = this.checked;
        const rows = productTableBody ? productTableBody.querySelectorAll('.product-row') : [];
        rows.forEach(row => {
            const cb = row.querySelector('.product-checkbox');
            const pid = parseInt(row.dataset.productId, 10);
            if (!cb || !pid) return;

            cb.checked = isChecked;
            if (isChecked) {
                const qtyInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                selectedProductsStore.set(pid, {
                    product_id: pid,
                    product_name: row.dataset.productName || row.querySelector('td:nth-child(2)')?.textContent?.trim() || '',
                    sku: row.dataset.sku || row.querySelector('td:nth-child(4)')?.textContent?.trim() || '',
                    quantity: qtyInput ? (parseInt(qtyInput.value, 10) || 1) : (parseInt(row.dataset.defaultQuantity, 10) || 1),
                    unit_price: priceInput ? (parseFloat(priceInput.value) || 0) : (parseFloat(row.dataset.defaultUnitPrice) || 0),
                    selected: true,
                });
            } else {
                selectedProductsStore.delete(pid);
            }
        });

        onProductSelectionChange();
    });

    // ── Intercept pagination clicks without page reload ───────────────────────
    document.addEventListener('click', function (e) {
        const pageLink = e.target.closest('#pagination-container a');
        if (pageLink) {
            e.preventDefault();
            const href = pageLink.getAttribute('href');
            if (href && href !== '#' && href !== 'javascript:void(0)') {
                fetchProducts(href);
            }
        }
    });

    // ── Browser back/forward navigation ───────────────────────────────────────
    window.addEventListener('popstate', function () {
        fetchProducts(window.location.href, false);
    });

    // ── Product search with debounce ──────────────────────────────────────────
    let searchTimer = null;
    function triggerSearch(term) {
        const url = new URL(window.location.href);
        if (term) {
            url.searchParams.set('search', term);
        } else {
            url.searchParams.delete('search');
        }
        url.searchParams.set('page', '1');
        fetchProducts(url.toString());
    }

    productSearch?.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            triggerSearch(this.value.trim());
        }, 300);
    });

    productSearch?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            triggerSearch(this.value.trim());
        }
    });

    // ── Supplier dropdown change ──────────────────────────────────────────────
    supplierSelect?.addEventListener('change', () => {
        const id = parseInt(supplierSelect.value, 10) || null;
        currentSupplierId = id;
        id ? loadSupplierDetails(id) : hideSupplierPanels();
    });

    // ── Product selection change ──────────────────────────────────────────────
    function onProductSelectionChange() {
        selectedProductIds = Array.from(selectedProductsStore.keys());

        // Count badge
        const bar = $el('selected-count-bar');
        const txt = $el('selected-count-text');
        if (selectedProductIds.length > 0) {
            txt.textContent = selectedProductIds.length === 1
                ? '1 product selected'
                : `${selectedProductIds.length} products selected`;
            bar.classList.remove('hidden');
        } else {
            bar.classList.add('hidden');
        }

        clearTimeout(filterTimer);
        filterTimer = setTimeout(refreshSupplierDropdown, 250);
    }

    // ── Form submission: submit all selected products from all pages ──────────
    poForm?.addEventListener('submit', function (e) {
        syncVisibleRowsToStore();

        if (selectedProductsStore.size === 0) {
            e.preventDefault();
            alert('Please select at least one product to order.');
            return false;
        }

        if (!supplierSelect || !supplierSelect.value) {
            e.preventDefault();
            alert('Please select a supplier.');
            $el('supplierSelectButton')?.focus();
            return false;
        }

        const hiddenContainer = $el('selected-products-hidden-inputs');
        if (hiddenContainer) {
            hiddenContainer.innerHTML = '';
            let idx = 0;
            selectedProductsStore.forEach(item => {
                const pSelected = document.createElement('input');
                pSelected.type = 'hidden';
                pSelected.name = `products[${idx}][selected]`;
                pSelected.value = '1';
                hiddenContainer.appendChild(pSelected);

                const pId = document.createElement('input');
                pId.type = 'hidden';
                pId.name = `products[${idx}][product_id]`;
                pId.value = item.product_id;
                hiddenContainer.appendChild(pId);

                const pName = document.createElement('input');
                pName.type = 'hidden';
                pName.name = `products[${idx}][product_name]`;
                pName.value = item.product_name;
                hiddenContainer.appendChild(pName);

                const pSku = document.createElement('input');
                pSku.type = 'hidden';
                pSku.name = `products[${idx}][sku]`;
                pSku.value = item.sku;
                hiddenContainer.appendChild(pSku);

                const pQty = document.createElement('input');
                pQty.type = 'hidden';
                pQty.name = `products[${idx}][quantity]`;
                pQty.value = item.quantity;
                hiddenContainer.appendChild(pQty);

                const pPrice = document.createElement('input');
                pPrice.type = 'hidden';
                pPrice.name = `products[${idx}][unit_price]`;
                pPrice.value = item.unit_price;
                hiddenContainer.appendChild(pPrice);

                idx++;
            });
        }

        // Temporarily disable the inputs in the table body so only hiddenContainer inputs are submitted
        const tableInputs = productTableBody.querySelectorAll('input, select');
        tableInputs.forEach(inp => { inp.disabled = true; });

        // Safety timeout to re-enable if submission was interrupted by client-side validation
        setTimeout(() => {
            tableInputs.forEach(inp => { inp.disabled = false; });
        }, 1500);
    });

    // ── Refresh supplier dropdown ─────────────────────────────────────────────
    async function refreshSupplierDropdown() {
        hideSupplierPanels();
        currentSupplierId = null;

        $el('no-product-hint').classList.add('hidden');
        $el('no-supplier-message').classList.add('hidden');
        $el('supplier-dropdown-wrapper').classList.add('hidden');
        $el('supplier-loading').classList.add('hidden');
        $el('comparison-panel').classList.add('hidden');

        if (selectedProductIds.length === 0) {
            $el('no-product-hint').classList.remove('hidden');
            return;
        }

        $el('supplier-loading').classList.remove('hidden');

        try {
            const url = new URL(ROUTES.filteredSuppliers, window.location.origin);
            selectedProductIds.forEach(id => url.searchParams.append('product_ids[]', id));

            const res  = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            $el('supplier-loading').classList.add('hidden');

            if (!data.suppliers || data.suppliers.length === 0) {
                const msg = $el('no-supplier-message');
                msg.textContent = data.message || 'No qualified supplier found for all selected products.';
                msg.classList.remove('hidden');
                return;
            }

            // Populate dropdown
            const listContainer = $el('supplierSelectList');
            if (listContainer) {
                listContainer.innerHTML = data.suppliers.map(s => {
                    const isSelected = currentSupplierId && parseInt(s.id, 10) === currentSupplierId;
                    return `
                        <button type="button"
                                data-supplier-id="${s.id}"
                                data-name="${escHtml(s.name)}"
                                onclick="selectSupplier(${s.id}, '${escHtml(s.name).replace(/'/g, "\\'")}'); closeSupplierDropdown();"
                                class="w-full px-4 py-2.5 text-left text-sm ${isSelected ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100'} rounded-[10px] transition cursor-pointer">
                            ${escHtml(s.name)}
                        </button>
                    `;
                }).join('');
            }

            $el('supplier-dropdown-wrapper').classList.remove('hidden');

            // Restore previously selected supplier if it is still valid
            if (currentSupplierId) {
                const found = data.suppliers.find(s => s.id === currentSupplierId);
                if (found) {
                    selectSupplier(found.id, found.name);
                } else {
                    selectSupplier(null, '');
                }
            } else {
                updateSupplierTriggerDisplay('');
            }

            loadComparison();

        } catch (e) {
            $el('supplier-loading').classList.add('hidden');
            console.error('filteredSuppliers error', e);
        }
    }

    // ── Load supplier details + price history ─────────────────────────────────
    async function loadSupplierDetails(supplierId) {
        hideSupplierPanels();
        try {
            const url = new URL(ROUTES.supplierDetails, window.location.origin);
            url.searchParams.set('supplier_id', supplierId);
            selectedProductIds.forEach(id => url.searchParams.append('product_ids[]', id));

            const res  = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.error) return;

            renderSupplierInfo(data.supplier);
            renderPriceAnalysis(data.price_histories);

        } catch (e) {
            console.error('supplierDetails error', e);
        }
    }

    // ── Render Step 3 ─────────────────────────────────────────────────────────
    function renderSupplierInfo(s) {
        $el('si-name').textContent          = s.name          || '—';
        $el('si-contact').textContent       = s.contact_person || '—';
        $el('si-last-purchase').textContent = s.last_purchase_date || 'No orders yet';

        const rel = $el('si-reliability');
        if (s.reliability_score != null) {
            rel.textContent = `${s.reliability_score}% (${s.total_orders} orders)`;
            rel.className = 'text-sm font-semibold ' + (s.reliability_score >= 80
                ? 'text-emerald-700'
                : s.reliability_score >= 50 ? 'text-amber-600' : 'text-rose-600');
        } else {
            rel.textContent = 'No order history';
            rel.className   = 'text-sm text-slate-500';
        }

        $el('supplier-info-panel').classList.remove('hidden');
    }

    // ── Render Steps 4+5+6 ───────────────────────────────────────────────────
    function renderPriceAnalysis(histories) {
        const container = $el('price-analysis-content');
        container.innerHTML = '';

        if (!histories || histories.length === 0) {
            container.innerHTML = '<p class="text-sm text-slate-500">No purchase history found for the selected products from this supplier.</p>';
            $el('price-analysis-panel').classList.remove('hidden');
            return;
        }

        histories.forEach(ph => {
            const section = document.createElement('div');
            section.className = 'space-y-4';

            // Product heading
            const heading = document.createElement('h3');
            heading.className   = 'text-sm font-semibold text-slate-800 border-b border-slate-100 pb-2';
            heading.textContent = ph.product_name;
            section.appendChild(heading);

            // Summary cards
            const changeColor = ph.change_percentage == null ? 'text-slate-600'
                : ph.change_percentage > 0 ? 'text-rose-600 font-semibold'
                : ph.change_percentage < 0 ? 'text-emerald-700 font-semibold'
                : 'text-slate-600';

            const summaryGrid = document.createElement('div');
            summaryGrid.className = 'grid gap-3 sm:grid-cols-2 lg:grid-cols-4';
            summaryGrid.innerHTML = `
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Current Cost</p>
                    <p class="text-base font-bold text-slate-800">${ph.current_cost != null ? fmt(ph.current_cost) : '—'}</p>
                </div>
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Previous Cost</p>
                    <p class="text-base font-bold text-slate-600">${ph.previous_cost != null ? fmt(ph.previous_cost) : '—'}</p>
                </div>
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Price Change</p>
                    <p class="text-base ${changeColor}">${fmtP(ph.change_percentage)}</p>
                </div>
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Trend</p>
                    <p class="text-base font-semibold text-slate-700">${icon(ph.trend)} ${cap(ph.trend)}</p>
                </div>
            `;
            section.appendChild(summaryGrid);

            // Recommendation
            const recStyle = ph.trend === 'increasing'
                ? 'border-amber-200 bg-amber-50 text-amber-800'
                : ph.trend === 'decreasing'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                    : 'border-slate-200 bg-slate-50 text-slate-700';
            const recBox = document.createElement('div');
            recBox.className   = `rounded-[10px] border px-4 py-3 text-sm ${recStyle}`;
            recBox.textContent = ph.recommendation;
            section.appendChild(recBox);

            // History table
            if (ph.histories && ph.histories.length > 0) {
                const tableWrap = document.createElement('div');
                tableWrap.className = 'overflow-hidden rounded-3xl border border-slate-200';
                tableWrap.innerHTML = `
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3">Purchase Date</th>
                                <th class="px-4 py-3">Purchase Order</th>
                                <th class="px-4 py-3">Supplier Cost</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            ${ph.histories.map(h => `
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2">${escHtml(h.date ?? '—')}</td>
                                    <td class="px-4 py-2 font-mono text-xs text-slate-500">${escHtml(h.po_number ?? '—')}</td>
                                    <td class="px-4 py-2 font-semibold">${fmt(h.cost)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                section.appendChild(tableWrap);
            } else {
                const noHist = document.createElement('p');
                noHist.className   = 'text-sm text-slate-500';
                noHist.textContent = 'No purchase history yet for this product from this supplier.';
                section.appendChild(noHist);
            }

            container.appendChild(section);
        });

        $el('price-analysis-panel').classList.remove('hidden');
    }

    // ── Load comparison table ─────────────────────────────────────────────────
    async function loadComparison() {
        $el('comparison-panel').classList.add('hidden');
        if (selectedProductIds.length === 0) return;

        try {
            const url = new URL(ROUTES.comparison, window.location.origin);
            selectedProductIds.forEach(id => url.searchParams.append('product_ids[]', id));

            const res  = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            // Only show comparison when 2+ suppliers compete
            if (!data.comparison || data.comparison.length < 2) return;

            renderComparison(data.comparison, data.recommended);

        } catch (e) {
            console.error('comparison error', e);
        }
    }

    // ── Render comparison table ───────────────────────────────────────────────
    function renderComparison(rows, recommended) {
        const tbody = $el('comparison-table-body');
        tbody.innerHTML = '';

        const badge = $el('recommended-supplier-badge');
        if (recommended) {
            badge.innerHTML = `<strong>⭐ Recommended Supplier: ${escHtml(recommended.name)}</strong> — ${recommended.reasons.map(r => escHtml(r)).join(', ')}`;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

        rows.forEach(r => {
            const isRec = recommended && r.supplier_id === recommended.id;
            const tr = document.createElement('tr');
            tr.className = isRec
                ? 'bg-emerald-50'
                : 'hover:bg-slate-50';

            const cc = r.avg_change_percentage > 0
                ? 'text-rose-600'
                : r.avg_change_percentage < 0 ? 'text-emerald-700' : 'text-slate-500';

            tr.innerHTML = `
                <td class="px-4 py-3 font-medium ${isRec ? 'text-emerald-800' : 'text-slate-800'}">
                    ${isRec ? '⭐ ' : ''}${escHtml(r.supplier_name)}
                </td>
                <td class="px-4 py-3 font-semibold">${r.has_history ? fmt(r.latest_total_cost) : '—'}</td>
                <td class="px-4 py-3 ${cc}">${r.has_history ? fmtP(r.avg_change_percentage) : '—'}</td>
                <td class="px-4 py-3 text-slate-500">${escHtml(r.last_purchase_date ?? '—')}</td>
                <td class="px-4 py-3">
                    <button type="button"
                            onclick="window._poSelectSupplier(${r.supplier_id})"
                            class="rounded-xl border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                        Select
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        $el('comparison-panel').classList.remove('hidden');
    }

    // ── Public helper for comparison "Select" button ──────────────────────────
    window._poSelectSupplier = function (supplierId) {
        const sId = parseInt(supplierId, 10);
        const optBtn = document.querySelector(`#supplierSelectList button[data-supplier-id="${sId}"]`);
        const name = optBtn ? optBtn.dataset.name : '';
        selectSupplier(sId, name);

        const btn = $el('supplierSelectButton') || $el('supplier-dropdown-wrapper');
        if (btn) {
            btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    // ── Hide downstream panels ────────────────────────────────────────────────
    function hideSupplierPanels() {
        $el('supplier-info-panel').classList.add('hidden');
        $el('price-analysis-panel').classList.add('hidden');
    }

    // ── Resolve Product Images from LocalStorage ────────────────────────────
    function resolvePoProductImages() {
        try {
            const stored = localStorage.getItem('posProductImages');
            if (!stored) return;
            const images = JSON.parse(stored);
            const keys = Object.keys(images);

            document.querySelectorAll('.po-product-img-thumb').forEach(container => {
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
                    container.className = 'po-product-img-thumb w-8 h-8 rounded-[6px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center';
                    container.style.backgroundImage = `url('${imgUrl}')`;
                }
            });
        } catch(e) {
            console.error('Error resolving PO product images:', e);
        }
    }

    // ── Boot: trigger initial state ───────────────────────────────────────────
    if (productTableBody) {
        const rows = productTableBody.querySelectorAll('.product-row');
        rows.forEach(row => {
            const cb = row.querySelector('.product-checkbox');
            const pid = parseInt(row.dataset.productId, 10);
            if (cb && (cb.checked || (Array.isArray(preselectedIds) && preselectedIds.includes(pid)))) {
                cb.checked = true;
                const qtyInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                selectedProductsStore.set(pid, {
                    product_id: pid,
                    product_name: row.dataset.productName || row.querySelector('td:nth-child(2)')?.textContent?.trim() || '',
                    sku: row.dataset.sku || row.querySelector('td:nth-child(4)')?.textContent?.trim() || '',
                    quantity: qtyInput ? (parseInt(qtyInput.value, 10) || 1) : (parseInt(row.dataset.defaultQuantity, 10) || 1),
                    unit_price: priceInput ? (parseFloat(priceInput.value) || 0) : (parseFloat(row.dataset.defaultUnitPrice) || 0),
                    selected: true,
                });
            }
        });
        updateSelectAllCheckboxState();
    }
    onProductSelectionChange();
    resolvePoProductImages();

})();
</script>

</x-layouts.app>
