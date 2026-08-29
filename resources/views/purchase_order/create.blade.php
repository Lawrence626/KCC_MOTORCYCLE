<x-layouts.app :title="__('Create Purchase Order')">
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
       
            <div class="pl-3 lg:pl-2">
            <h1 class="text-3xl font-bold text-slate-900">Create Purchase Order</h1>
            <p class="max-w-2xl text-sm text-slate-500">Select products first, then choose a qualified supplier. Pricing insights update automatically.</p>
        </div>
        <a href="{{ route('order.management') }}"
           class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
            <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to orders
        </a>
    </div>

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

                <div class="overflow-hidden rounded-[10px] border border-slate-200">
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
                                <th class="px-4 py-3">Default Supplier</th>
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
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            @forelse($lowStockProducts as $product)
                                @php $idx = $loop->index; @endphp
                                <tr class="product-row hover:bg-slate-50 transition-colors"
                                    data-product-id="{{ $product->id }}">
                                    <td class="px-4 py-3">
                                        <input type="checkbox"
                                               name="products[{{ $idx }}][selected]"
                                               value="1"
                                               {{ in_array($product->id, $selectedProductIds, true) ? 'checked' : '' }}
                                               class="product-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                        <input type="hidden" name="products[{{ $idx }}][product_id]" value="{{ $product->id }}" />
                                        <input type="hidden" name="products[{{ $idx }}][product_name]" value="{{ $product->product_name ?? $product->name }}" />
                                        <input type="hidden" name="products[{{ $idx }}][sku]" value="{{ $product->sku }}" />
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $product->product_name ?? $product->name }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $mov = $product->movement_category ?? 'special_order';
                                            $movLabel = match($mov) {
                                                'fast_moving'   => 'Fast moving',
                                                'slow_moving'   => 'Slow moving',
                                                'special_order' => 'Special order',
                                                default         => ucfirst(str_replace('_', ' ', $mov)),
                                            };
                                            $movClass = match($mov) {
                                                'fast_moving'   => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                                'slow_moving'   => 'bg-amber-50 text-amber-700 ring-amber-200',
                                                'special_order' => 'bg-sky-50 text-sky-700 ring-sky-200',
                                                default         => 'bg-slate-100 text-slate-600 ring-slate-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $movClass }}">
                                            {{ $movLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">{{ $product->supplier_name ?? '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $product->sku }}</td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $product->stock_quantity <= 0 ? 'text-rose-600 font-semibold' : 'text-amber-600 font-semibold' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $product->reorder_level }}</td>
                                    <td class="px-4 py-3">
                                        <input name="products[{{ $idx }}][quantity]"
                                               type="number" min="1"
                                               value="{{ old('products.' . $idx . '.quantity', max(1, 100 - $product->stock_quantity)) }}"
                                               class="w-20 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:outline-none focus:ring-1 focus:ring-black/35" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input name="products[{{ $idx }}][unit_price]"
                                               type="number" step="0.01" min="0"
                                               value="{{ old('products.' . $idx . '.unit_price', $product->unit_price) }}"
                                               class="w-28 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:outline-none focus:ring-1 focus:ring-black/35" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500">
                                        No products found for this filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 px-2">{{ $lowStockProducts->links() }}</div>

                <div id="selected-count-bar" class="mt-4 hidden rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 font-medium">
                    <span id="selected-count-text"></span>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 2 – SELECT SUPPLIER
        ════════════════════════════════════════════════════════════ --}}
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
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

                <div id="supplier-dropdown-wrapper" class="hidden">
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Supplier</label>
                    <select name="supplier_id" id="supplier-select" required
                            class="w-full max-w-sm rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 outline-none">
                        <option value="">Select supplier…</option>
                    </select>
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

        {{-- ══════════════════════════════════════════════════════════
             ORDER DETAILS
        ════════════════════════════════════════════════════════════ --}}
       <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden min-h-[510px]">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">5</span>
                <h2 class="text-sm font-semibold text-white">Order Details</h2>
            </div>
            <div class="p-6 grid gap-4 relative">
                <label class="block text-sm text-slate-700 w-72">
                    <span class="text-xs font-semibold text-slate-500">Expected Delivery Date</span>
                    <input name="expected_delivery_date"
                           id="expectedDeliveryDate"
                           value="{{ old('expected_delivery_date') }}"
                           type="date"
                           class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Notes</span>
                    <textarea name="notes" rows="6"
                              class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 outline-none">{{ old('notes') }}</textarea>
                </label>
            </div>
{{-- Actions --}}
<div class="px-6 pb-6 pt-15 flex flex-wrap items-center justify-end gap-3">
    <a href="{{ route('order.management') }}"
       class="max-w-xs rounded-[10px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-black/10 transition-all duration-200">
        Cancel
    </a>
    <button type="submit"
            class="max-w-xs inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#6EC1D1] px-5 py-3 text-sm font-semibold text-black shadow-sm hover:bg-[#59b2c2] transition-all duration-200">
        Submit Purchase Order
    </button>
</div>
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
    let currentSupplierId  = null;
    let filterTimer        = null;

    // ── DOM references ─────────────────────────────────────────────────────────
    const checkboxes        = document.querySelectorAll('.product-checkbox');
    const selectAllBox      = $el('select-all-products');
    const supplierSelect    = $el('supplier-select');
    const productSearch     = $el('product-search');
    const productRows       = document.querySelectorAll('.product-row');
    const productTableBody  = document.querySelector('table tbody');

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

        // Navigate to new URL with filter
        const url = new URL(window.location.href);
        url.searchParams.set('movement', value);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    // Attach functions to window for inline onclick handlers
    window.toggleDropdown = toggleDropdown;
    window.selectMovementFilter = selectMovementFilter;

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
            resetDropdownButtonStyles();
        }
    });

    // ── Product search (client-side filtering) ────────────────────────────────
    let noMatchRow = null;

    function createNoMatchRow() {
        if (noMatchRow) return noMatchRow;
        noMatchRow = document.createElement('tr');
        noMatchRow.id = 'no-match-row';
        noMatchRow.innerHTML = '<td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500">No matching products found.</td>';
        return noMatchRow;
    }

    function applySearch() {
        const term = (productSearch?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        productRows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const skuCell  = row.querySelector('td:nth-child(5)');
            const name = (nameCell?.textContent || '').toLowerCase();
            const sku  = (skuCell?.textContent || '').toLowerCase();

            if (!term || name.includes(term) || sku.includes(term)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show / hide "no matching products" message
        const existing = $el('no-match-row');
        if (visibleCount === 0 && productRows.length > 0) {
            if (!existing && productTableBody) {
                productTableBody.appendChild(createNoMatchRow());
            }
        } else if (existing) {
            existing.remove();
            noMatchRow = null;
        }
    }

    productSearch?.addEventListener('input', applySearch);

    // ── Select-all toggle ─────────────────────────────────────────────────────
    selectAllBox?.addEventListener('change', function () {
        checkboxes.forEach(cb => { cb.checked = this.checked; });
        onProductSelectionChange();
    });

    // ── Per-product checkbox ──────────────────────────────────────────────────
    checkboxes.forEach(cb => cb.addEventListener('change', () => {
        const all  = [...checkboxes].every(c => c.checked);
        const none = [...checkboxes].every(c => !c.checked);
        if (selectAllBox) {
            selectAllBox.checked       = all;
            selectAllBox.indeterminate = !all && !none;
        }
        onProductSelectionChange();
    }));

    // ── Supplier dropdown change ──────────────────────────────────────────────
    supplierSelect?.addEventListener('change', () => {
        const id = parseInt(supplierSelect.value, 10) || null;
        currentSupplierId = id;
        id ? loadSupplierDetails(id) : hideSupplierPanels();
    });

    // ── Product selection change ──────────────────────────────────────────────
    function onProductSelectionChange() {
        selectedProductIds = [...document.querySelectorAll('.product-checkbox:checked')]
            .map(cb => parseInt(cb.closest('tr').dataset.productId, 10))
            .filter(Boolean);

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
            supplierSelect.innerHTML = '<option value="">Select supplier…</option>';
            data.suppliers.forEach(s => {
                const opt = document.createElement('option');
                opt.value       = s.id;
                opt.textContent = s.name;
                supplierSelect.appendChild(opt);
            });
            $el('supplier-dropdown-wrapper').classList.remove('hidden');

            // Restore previously selected supplier if it is still valid
            if (currentSupplierId) {
                const stillValid = [...supplierSelect.options].some(o => parseInt(o.value, 10) === currentSupplierId);
                if (stillValid) {
                    supplierSelect.value = currentSupplierId;
                    loadSupplierDetails(currentSupplierId);
                }
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
        supplierSelect.value = supplierId;
        currentSupplierId    = supplierId;
        loadSupplierDetails(supplierId);
        $el('supplier-select').scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    // ── Hide downstream panels ────────────────────────────────────────────────
    function hideSupplierPanels() {
        $el('supplier-info-panel').classList.add('hidden');
        $el('price-analysis-panel').classList.add('hidden');
    }

    // ── Boot: trigger initial state ───────────────────────────────────────────
    onProductSelectionChange();

    // ── Custom Date Picker for Expected Delivery Date ───────────────────────
    function setupCustomDatePicker(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.type = 'text';
        input.readOnly = true;
        input.placeholder = 'mm/dd/yyyy';
        input.className = 'h-11 w-full rounded-[14px] border border-slate-300 bg-white px-3.5 pr-10 text-sm text-slate-900 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 cursor-pointer shadow-sm transition';

        const wrapper = document.createElement('div');
        wrapper.className = 'relative w-full mt-2 z-[999999]';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        // Add calendar icon inside input
        const icon = document.createElement('div');
        icon.className = 'absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 transition-colors duration-150';
        icon.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;
        wrapper.appendChild(icon);

        function setIconActive(isActive) {
            if (isActive) {
                icon.classList.remove('text-slate-400');
                icon.classList.add('text-slate-600');
            } else {
                icon.classList.remove('text-slate-600');
                icon.classList.add('text-slate-400');
            }
        }

        const card = document.createElement('div');
        card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-2 z-[999999] w-72 rounded-[18px] bg-white p-4 shadow-[0_16px_40px_rgba(0,0,0,0.12)] border border-slate-100 transition-all duration-200';
        wrapper.appendChild(card);

        let currentDate = new Date();
        let selectedDate = input.value ? new Date(input.value) : null;
        let viewMode = 'days';

        function render() {
            if (viewMode === 'days') {
                renderDaysView();
            } else {
                renderMonthsView();
            }
        }

        function renderDaysView() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();

            let html = `
                <div class="flex items-center justify-between mb-3 px-1">
                    <button type="button" class="toggle-view-btn text-sm font-bold text-slate-900 hover:text-slate-700 inline-flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-slate-100 transition">
                        <span>${monthNames[month]} ${year}</span>
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="flex items-center gap-1">
                        <button type="button" class="prev-month-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Previous Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button type="button" class="next-month-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Next Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[11px] font-semibold text-slate-400">
                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                </div>
                <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
            `;

            for (let i = firstDay - 1; i >= 0; i--) {
                html += `<span class="h-7 flex items-center justify-center text-slate-300">${daysInPrevMonth - i}</span>`;
            }

            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                let dayClasses = "h-7 w-7 mx-auto flex items-center justify-center rounded-lg font-medium cursor-pointer transition-all duration-150 ";
                if (isSelected) {
                    dayClasses += "bg-[#0f172a] text-white font-bold shadow-sm";
                } else if (isToday) {
                    dayClasses += "bg-[#6EC1D1] text-black font-bold shadow-sm";
                } else {
                    dayClasses += "text-slate-700 hover:bg-slate-100";
                }

                html += `<button type="button" data-day="${day}" class="day-btn ${dayClasses}">${day}</button>`;
            }

            const totalSlots = firstDay + daysInMonth;
            const nextDays = (7 - (totalSlots % 7)) % 7;
            for (let i = 1; i <= nextDays; i++) {
                html += `<span class="h-7 flex items-center justify-center text-slate-300">${i}</span>`;
            }

            html += `
                </div>
                <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold px-1">
                    <button type="button" class="clear-btn text-slate-500 hover:text-red-600 transition">Clear</button>
                    <button type="button" class="today-btn text-slate-900 font-bold hover:underline transition">Today</button>
                </div>
            `;

            card.innerHTML = html;

            card.querySelector('.toggle-view-btn')?.addEventListener('click', (e) => { e.stopPropagation(); viewMode = 'months'; render(); });
            card.querySelector('.prev-month-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() - 1); render(); });
            card.querySelector('.next-month-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() + 1); render(); });
            card.querySelector('.clear-btn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                selectedDate = null;
                input.value = '';
                input.dispatchEvent(new Event('change', { bubbles: true }));
                card.classList.add('hidden');
                setIconActive(false);
            });
            card.querySelector('.today-btn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                selectedDate = new Date();
                currentDate = new Date();
                const yyyy = selectedDate.getFullYear();
                const mm = String(selectedDate.getMonth() + 1).padStart(2, '0');
                const dd = String(selectedDate.getDate()).padStart(2, '0');
                input.value = `${yyyy}-${mm}-${dd}`;
                input.dispatchEvent(new Event('change', { bubbles: true }));
                card.classList.add('hidden');
                setIconActive(false);
            });

            card.querySelectorAll('.day-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const day = parseInt(btn.dataset.day);
                    selectedDate = new Date(year, month, day);
                    const yyyy = year;
                    const mm = String(month + 1).padStart(2, '0');
                    const dd = String(day).padStart(2, '0');
                    input.value = `${yyyy}-${mm}-${dd}`;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    card.classList.add('hidden');
                    setIconActive(false);
                });
            });
        }

        function renderMonthsView() {
            const year = currentDate.getFullYear();
            const shortMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

            let html = `
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100 px-1">
                    <button type="button" class="prev-year-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="text-sm font-bold text-slate-900">${year}</span>
                    <button type="button" class="next-year-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2 text-xs">
            `;

            shortMonths.forEach((m, idx) => {
                const isSel = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === idx;
                let mClasses = "py-2.5 rounded-xl text-center font-semibold cursor-pointer transition-all duration-150 ";
                if (isSel) {
                    mClasses += "bg-[#6EC1D1] text-black font-bold shadow-md";
                } else {
                    mClasses += "text-slate-700 hover:bg-slate-100";
                }
                html += `<button type="button" data-month="${idx}" class="month-btn ${mClasses}">${m}</button>`;
            });

            html += `
                </div>
                <div class="mt-3 text-right">
                    <button type="button" class="back-days-btn text-xs font-bold text-black hover:underline">Back to Days</button>
                </div>
            `;

            card.innerHTML = html;

            card.querySelector('.prev-year-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setFullYear(year - 1); render(); });
            card.querySelector('.next-year-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setFullYear(year + 1); render(); });
            card.querySelector('.back-days-btn')?.addEventListener('click', (e) => { e.stopPropagation(); viewMode = 'days'; render(); });

            card.querySelectorAll('.month-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const mIdx = parseInt(btn.dataset.month);
                    currentDate.setMonth(mIdx);
                    viewMode = 'days';
                    render();
                });
            });
        }

        input.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('.custom-calendar-card').forEach(c => {
                if (c !== card) c.classList.add('hidden');
            });
            card.classList.toggle('hidden');
            const isOpen = !card.classList.contains('hidden');
            if (isOpen) {
                render();
            }
            setIconActive(isOpen);
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                card.classList.add('hidden');
                setIconActive(false);
            }
        });
    }

    // Initialize custom date picker
    setupCustomDatePicker('expectedDeliveryDate');

})();
</script>

</x-layouts.app>
