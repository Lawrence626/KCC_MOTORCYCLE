<x-layouts.app :title="__('Create Purchase Order')">
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-slate-900">Create Purchase Order</h1>
            <p class="max-w-2xl text-sm text-slate-500">Select products first, then choose a qualified supplier. Pricing insights update automatically.</p>
        </div>
        <a href="{{ route('order.management') }}"
           class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900">
            ← Back to orders
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
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
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">1</span>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Select Products to Reorder</h2>
                    <p class="text-xs text-slate-500">Choose from low-stock products. The supplier list will update automatically.</p>
                </div>
            </div>

            <div class="p-6">
                @if(!empty($selectedProductIds))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        ✓ Low-stock alert pre-selected products for replenishment. Review and confirm your selection.
                    </div>
                @endif

                <div class="overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                            <tr>
                                <th class="px-4 py-3">
                                    <input type="checkbox" id="select-all-products"
                                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                           title="Select all" />
                                </th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Default Supplier</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Reorder Level</th>
                                <th class="px-4 py-3">Qty to Order</th>
                                <th class="px-4 py-3">Unit Price (₱)</th>
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
                                               value="{{ old('products.' . $idx . '.quantity', max(1, $product->reorder_level - $product->stock_quantity)) }}"
                                               class="w-20 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input name="products[{{ $idx }}][unit_price]"
                                               type="number" step="0.01" min="0"
                                               value="{{ old('products.' . $idx . '.unit_price', $product->unit_price) }}"
                                               class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">
                                        No low-stock products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 px-2">{{ $lowStockProducts->links() }}</div>

                <div id="selected-count-bar" class="mt-4 hidden rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 font-medium">
                    <span id="selected-count-text"></span>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 2 – SELECT SUPPLIER
        ════════════════════════════════════════════════════════════ --}}
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">2</span>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Select Supplier</h2>
                    <p class="text-xs text-slate-500">Only suppliers that can fulfill every selected product are shown.</p>
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

                <div id="no-product-hint" class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    ☝ Select at least one product above to see qualified suppliers.
                </div>

                <div id="no-supplier-message" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                <div id="supplier-dropdown-wrapper" class="hidden">
                    <label class="block text-xs font-semibold text-slate-500 mb-2">Supplier</label>
                    <select name="supplier_id" id="supplier-select" required
                            class="w-full max-w-sm rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="">Select supplier…</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 3 – SUPPLIER INFORMATION
        ════════════════════════════════════════════════════════════ --}}
        <div id="supplier-info-panel" class="hidden rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">3</span>
                <h2 class="text-sm font-semibold text-slate-800">Supplier Information</h2>
            </div>
            <div class="p-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Supplier Name</p>
                        <p id="si-name" class="text-sm font-semibold text-slate-800">—</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Contact Person</p>
                        <p id="si-contact" class="text-sm text-slate-700">—</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Last Purchase</p>
                        <p id="si-last-purchase" class="text-sm text-slate-700">—</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Reliability Score</p>
                        <p id="si-reliability" class="text-sm font-semibold text-slate-800">—</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             STEP 4+5+6 – PRICE HISTORY / SUMMARY / RECOMMENDATIONS
        ════════════════════════════════════════════════════════════ --}}
        <div id="price-analysis-panel" class="hidden rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white">4</span>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Supplier Price Analysis</h2>
                    <p class="text-xs text-slate-500">Historical costs, trends, and purchasing recommendations per product.</p>
                </div>
            </div>
            <div id="price-analysis-content" class="p-6 space-y-8">
                {{-- Injected by JavaScript --}}
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             BONUS – SUPPLIER COMPARISON TABLE
        ════════════════════════════════════════════════════════════ --}}
        <div id="comparison-panel" class="hidden rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-violet-600 text-xs font-bold text-white">★</span>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Supplier Comparison</h2>
                    <p class="text-xs text-slate-500">All qualified suppliers ranked by cost. Click Select to choose one.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div id="recommended-supplier-badge" class="hidden rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800"></div>
                <div class="overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
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
        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center gap-3">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-400 text-xs font-bold text-white">5</span>
                <h2 class="text-sm font-semibold text-slate-800">Order Details</h2>
            </div>
            <div class="p-6 grid gap-4 lg:grid-cols-2">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Expected Delivery Date</span>
                    <input name="expected_delivery_date"
                           value="{{ old('expected_delivery_date') }}"
                           type="date"
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Notes</span>
                    <textarea name="notes" rows="3"
                              class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">{{ old('notes') }}</textarea>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('order.management') }}"
               class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">
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
    let currentSupplierId  = null;
    let filterTimer        = null;

    // ── DOM references ─────────────────────────────────────────────────────────
    const checkboxes        = document.querySelectorAll('.product-checkbox');
    const selectAllBox      = $el('select-all-products');
    const supplierSelect    = $el('supplier-select');

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
                <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Current Cost</p>
                    <p class="text-base font-bold text-slate-800">${ph.current_cost != null ? fmt(ph.current_cost) : '—'}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Previous Cost</p>
                    <p class="text-base font-bold text-slate-600">${ph.previous_cost != null ? fmt(ph.previous_cost) : '—'}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Price Change</p>
                    <p class="text-base ${changeColor}">${fmtP(ph.change_percentage)}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-1">Trend</p>
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
            recBox.className   = `rounded-2xl border px-4 py-3 text-sm ${recStyle}`;
            recBox.textContent = ph.recommendation;
            section.appendChild(recBox);

            // History table
            if (ph.histories && ph.histories.length > 0) {
                const tableWrap = document.createElement('div');
                tableWrap.className = 'overflow-hidden rounded-3xl border border-slate-200';
                tableWrap.innerHTML = `
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
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

})();
</script>

</x-layouts.app>

