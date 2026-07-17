<x-layouts.app :title="__('Warehouse Management')">
    <style>
        :root {
            --brand: #0f766e; /* professional teal */
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #6b7280;
            --border: rgba(148,163,184,0.2);
        }
        .wm-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .wm-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .wm-location { background: #f8fafc; border: 1px dashed rgba(15,118,110,0.16); }
        .product-chip { background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.12); color: var(--brand-dark); font-size: 0.78rem; padding: 0.35rem 0.6rem; border-radius: 0.8rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; }
        .product-chip .left { display:flex; flex-direction:column; gap:0.08rem; }
        .product-chip .name { font-weight:600; font-size:0.84rem; color:#0f172a; }
        .product-chip .meta { font-size:0.62rem; color:#475569; }
        .product-chip .qty { font-weight:700; font-size:0.84rem; color:#0f172a; margin-left:0.4rem; min-width:44px; text-align:right; }
        .warehouse-shelves { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: minmax(220px, auto); }
        .map-unit { min-height: 220px; }
        .modal-panel { width: min(100%, 960px); border-radius: 1.5rem; background: #ffffff; box-shadow: 0 28px 80px rgba(15,23,42,0.18); }
        .modal-field { border: 1px solid rgba(148,163,184,0.35); background: #f8fafc; border-radius: 0.85rem; }
        .modal-field input,
        .modal-field select { border: none; background: transparent; outline: none; }
        .modal-field label { color: #334155; }
        .product-row-card { background: #f8fafc; border: 1px solid rgba(148,163,184,0.2); border-radius: 1rem; padding: 0.85rem; }
        .product-row-card .row-grid { gap: 0.75rem; }
        .product-row-card .product-sku,
        .product-row-card .product-qty,
        .product-row-card .product-price,
        .product-row-card .product-select { background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 0.85rem; }
        .product-row-card .product-sku { background: #f1f5f9; }
        .product-row-card .product-select,
        .product-row-card .product-sku,
        .product-row-card .product-qty,
        .product-row-card .product-price { padding: 0.75rem; }
        .remove-product-row { color: #ef4444; transition: color 0.2s ease; }
        .remove-product-row:hover { color: #b91c1c; }
        .modal-actions { border-top: 1px solid rgba(148,163,184,0.25); padding-top: 0.85rem; }
        .modal-footer-button { border-radius: 0.85rem; padding: 0.75rem 1.2rem; font-weight: 600; }
        .modal-footer-button.primary { background: var(--brand); color: #fff; }
        .modal-footer-button.secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); }
        .product-row-card label { font-size: 0.72rem; }
        .product-row-card .remove-product-row { font-size: 0.85rem; }
        .modal-panel { max-height: 95vh; overflow: auto; }
        #modal-product-rows { max-height: 440px; }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.85rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f766e; color: #fff; border-radius: 1rem; box-shadow: 0 18px 50px rgba(15,23,42,0.18); padding: 0.85rem 1rem; font-size: 0.95rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f766e; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 1rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 1024px) { .warehouse-shelves { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 770px) {
            .warehouse-shelves { grid-template-columns: 1fr; }
            .product-row-card { padding: 0.85rem; }
            .map-unit { min-height: 200px; }
        }
    </style>

    <div class="space-y-6">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Warehouse Management</h1>
                <p class="mt-2 text-sm text-gray-500">Track and manage storage locations and products across your warehouses.</p>
            </div>
                <div class="flex items-center space-x-3">
                <input id="wm-search" type="search" placeholder="Search product or SKU..." class="px-5 py-3 text-base border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300" />
                <button id="view-archived-shelves" type="button" class="px-3 py-2 border rounded-xl text-sm">Archived Shelves</button>
                <button id="view-archived-warehouses" type="button" class="px-3 py-2 border rounded-xl text-sm">Archived Warehouses</button>
                <button id="add-warehouse-button" type="button" class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl text-sm font-medium shadow-md hover:from-blue-700 hover:to-blue-800 transition">Add Warehouse</button>
                <button id="add-shelf-button" type="button" class="inline-flex items-center px-3 py-2 wm-badge rounded-xl text-sm font-medium">Add Shelf</button>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <label class="text-sm font-medium text-slate-700">Select a warehouse:</label>
            <select id="warehouse-selector" class="px-4 py-3 border rounded-xl bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                @foreach($warehouses as $wh)
                    <option value="{{ $wh['id'] }}">{{ $wh['name'] }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-3">
                <div class="rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                    <p class="uppercase tracking-[0.18em] text-xs text-slate-400">Products</p>
                    <p id="selectedWarehouseProducts" class="mt-1 text-lg font-semibold text-slate-900">0</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                    <p class="uppercase tracking-[0.18em] text-xs text-slate-400">Empty Slots</p>
                    <p id="selectedWarehouseEmptySlots" class="mt-1 text-lg font-semibold text-slate-900">0</p>
                </div>
            </div>
        </div>


        <div class="grid gap-6 mt-4">

        {{-- ── Pending Warehouse Assignment panel (collapsible) ── --}}
        <div id="pending-arrivals-panel" class="wm-card rounded-xl border-l-4 border-amber-400 bg-amber-50 overflow-hidden">
            {{-- Header / toggle bar --}}
            <button
                type="button"
                id="pending-arrivals-toggle"
                class="w-full flex items-center justify-between px-5 py-3 hover:bg-amber-100/60 transition"
                aria-expanded="{{ count($pendingArrivals) > 0 ? 'true' : 'false' }}"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-amber-900">New Stock — Pending Warehouse Assignment</span>
                    <span id="pending-arrivals-count" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ count($pendingArrivals) > 0 ? 'bg-amber-300 text-amber-900' : 'bg-slate-100 text-slate-500' }}">
                        {{ count($pendingArrivals) }} pending
                    </span>
                </div>
                <svg id="pending-arrivals-chevron" class="w-4 h-4 text-amber-600 transition-transform duration-200 {{ count($pendingArrivals) > 0 ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Collapsible body --}}
            <div id="pending-arrivals-body" class="{{ count($pendingArrivals) > 0 ? '' : 'hidden' }} px-5 pb-5">
                <p class="text-xs text-amber-700 mb-3">Items that arrived from confirmed purchase orders are listed here. Assign each one to a warehouse before shelving.</p>

                @if(count($pendingArrivals) > 0)
                <div class="overflow-x-auto rounded-lg border border-amber-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-100 text-sm" id="pending-arrivals-table">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">SKU</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">PO #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Arrived</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="pending-arrivals-tbody">
                            @foreach($pendingArrivals as $arrival)
                            <tr id="arrival-row-{{ $arrival['id'] }}" class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $arrival['product_name'] }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $arrival['sku'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-slate-800">{{ $arrival['quantity'] }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $arrival['purchase_order_number'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $arrival['supplier_name'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $arrival['arrived_at'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        class="assign-arrival-btn inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition shadow-sm"
                                        data-id="{{ $arrival['id'] }}"
                                        data-name="{{ $arrival['product_name'] }}"
                                        data-qty="{{ $arrival['quantity'] }}"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        Assign
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center rounded-lg border border-dashed border-amber-200 bg-white py-8 text-center">
                    <svg class="w-8 h-8 text-amber-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold text-slate-500">All caught up!</p>
                    <p class="text-xs text-slate-400 mt-1">New stocks from confirmed purchase orders will appear here.</p>
                </div>
                @endif
            </div>
        </div>
        {{-- ── end Pending Warehouse Assignment panel ── --}}

            @foreach($warehouses as $wh)
                <div class="wm-card rounded-lg p-4 shadow-sm wh-card" data-id="{{ $wh['id'] }}" style="display:none;">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-md flex items-center justify-center text-white font-semibold wm-badge">{{ strtoupper(substr($wh['code'], -1)) }}</div>
                                <div>
                                    <div class="text-lg font-semibold text-slate-900">{{ $wh['name'] }}</div>
                                    <div class="text-xs text-gray-500">Code: {{ $wh['code'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <select class="warehouse-action-select text-sm text-slate-700 px-4 py-2 border border-gray-200 rounded-full bg-white hover:bg-gray-50 cursor-pointer appearance-none pr-8" data-id="{{ $wh['id'] }}">
                                <option value="">Actions</option>
                                <option value="archive-warehouse">Archive Warehouse</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="map-container border rounded-md p-3 bg-gray-50">
                            <div class="warehouse-shelves grid gap-6" data-id="{{ $wh['id'] }}"></div>
                            <div class="pagination mt-4 flex items-center justify-between text-sm text-gray-600">
                                <button type="button" class="prev-page px-3 py-2 border rounded-md bg-white" data-id="{{ $wh['id'] }}">Previous</button>
                                <div class="page-info" data-id="{{ $wh['id'] }}">Page 1 of 1</div>
                                <button type="button" class="next-page px-3 py-2 border rounded-md bg-white" data-id="{{ $wh['id'] }}">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <script>
        window.WarehouseData = {
            warehouses: @json($warehouses),
            products: @json($products),
            routes: {
                addProduct: '{{ route('warehouse.management.add_product') }}',
                saveShelf: '{{ route('warehouse.management.save_shelf') }}',
                mobileScanner: '{{ route("warehouse.mobile.scanner") }}'
            },
            csrfToken: '{{ csrf_token() }}'
        };

        document.addEventListener('DOMContentLoaded', function() {
            const warehouses = window.WarehouseData.warehouses;
            const products = window.WarehouseData.products;
            const PRODUCTS_PER_SHELF = 10;
            const SHELVES_PER_PAGE = 4;
            const warehousePage = {};
            let warehouseSearchQuery = '';
            const searchInput = document.getElementById('wm-search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    warehouseSearchQuery = this.value.trim().toLowerCase();
                    const currentId = getCurrentWarehouseId();
                    if (currentId) {
                        const currentIndex = warehouses.findIndex(wh => wh.id === currentId);
                        if (currentIndex !== -1) {
                            warehousePage[currentIndex] = 0;
                            renderWarehousePage(currentId, 0);
                        }
                    }
                });
            }

            function updateWarehouseStats(index) {
                const warehouse = warehouses[index];
                if (!warehouse || !warehouse.locations) return;

                let totalProducts = 0;
                let usedSlots = 0;

                warehouse.locations.forEach(location => {
                    if (Array.isArray(location.products)) {
                        totalProducts += location.products.length;
                        usedSlots += location.products.length;
                    }
                });

                const totalSlots = warehouse.locations.length * PRODUCTS_PER_SHELF;
                const emptySlots = Math.max(0, totalSlots - usedSlots);

                const productsEl = document.getElementById(`warehouseProducts-${index}`);
                const emptyEl = document.getElementById(`warehouseEmptySlots-${index}`);
                const selectedProductsEl = document.getElementById('selectedWarehouseProducts');
                const selectedEmptyEl = document.getElementById('selectedWarehouseEmptySlots');
                if (productsEl) productsEl.textContent = totalProducts.toString();
                if (emptyEl) emptyEl.textContent = emptySlots.toString();
                if (selectedProductsEl) selectedProductsEl.textContent = totalProducts.toString();
                if (selectedEmptyEl) selectedEmptyEl.textContent = emptySlots.toString();
            }

            const warehouseSelector = document.getElementById('warehouse-selector');
            const toastContainer = document.getElementById('toast-container');

            if (warehouses.length) {
                warehouseSelector.value = warehouses[0].id;
                showWarehouse(warehouses[0].id);
            }

            function showToast(message, type = 'success', options = {}) {
                if (!toastContainer) return;
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                const undo = options.undo;
                const inner = document.createElement('div');
                inner.style.display = 'flex';
                inner.style.alignItems = 'center';
                inner.style.gap = '0.8rem';
                const text = document.createElement('span');
                text.textContent = message;
                inner.appendChild(text);

                if (undo && typeof undo.callback === 'function') {
                    const undoBtn = document.createElement('button');
                    undoBtn.type = 'button';
                    undoBtn.className = 'mr-2';
                    undoBtn.style.background = 'transparent';
                    undoBtn.style.border = '1px solid rgba(255,255,255,0.18)';
                    undoBtn.style.padding = '4px 8px';
                    undoBtn.style.borderRadius = '6px';
                    undoBtn.textContent = undo.label || 'Undo';
                    undoBtn.addEventListener('click', function() {
                        try { undo.callback(); } catch (e) { console.error(e); }
                        if (toast.parentNode) toast.parentNode.removeChild(toast);
                        clearTimeout(timeout);
                    });
                    inner.appendChild(undoBtn);
                }

                const dismiss = document.createElement('button');
                dismiss.type = 'button';
                dismiss.setAttribute('aria-label', 'Dismiss notification');
                dismiss.textContent = '✕';
                dismiss.addEventListener('click', () => { if (toast.parentNode) toast.parentNode.removeChild(toast); clearTimeout(timeout); });

                toast.appendChild(inner);
                toast.appendChild(dismiss);
                toastContainer.appendChild(toast);
                const timeout = setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, options.ttl || 4500);
            }

            function showWarehouse(warehouseId) {
                document.querySelectorAll('.wh-card').forEach(el => el.style.display = 'none');
                const el = document.querySelector(`.wh-card[data-id="${warehouseId}"]`);
                if (el) {
                    el.style.display = 'block';
                    const warehouseIndex = warehouses.findIndex(wh => wh.id === warehouseId);
                    renderWarehousePage(warehouseId, warehousePage[warehouseId] || 0);
                    updateWarehouseStats(warehouseIndex);
                }
            }

            document.getElementById('modal-close').addEventListener('click', closeModal);
            document.getElementById('modal-cancel').addEventListener('click', closeModal);
            document.getElementById('modal-form').addEventListener('submit', handleModalSave);
            const addShelfButton = document.getElementById('add-shelf-button');
            const viewArchivedButton = document.getElementById('view-archived-shelves');
            const viewArchivedWarehousesButton = document.getElementById('view-archived-warehouses');
            const archivedBackdrop = document.getElementById('archived-backdrop');
            const archivedList = document.getElementById('archived-list');
            const archivedClose = document.getElementById('archived-close');
            const archivedDone = document.getElementById('archived-done');
            function getCurrentWarehouseId() {
                const visibleCard = Array.from(document.querySelectorAll('.wh-card')).find(card => card.style.display !== 'none');
                return visibleCard ? parseInt(visibleCard.dataset.id, 10) : warehouses[0]?.id;
            }

            function getCurrentWarehouseIndex() {
                const warehouseId = getCurrentWarehouseId();
                return warehouses.findIndex(wh => wh.id === warehouseId);
            }

            function findShelfBySlotIndex(warehouse, slotIndex) {
                if (!warehouse || !Array.isArray(warehouse.locations)) {
                    return null;
                }
                return warehouse.locations.find(shelf => shelf && shelf.slot_index === slotIndex) || null;
            }

            function getNextShelfIndex(warehouse) {
                if (!warehouse || !Array.isArray(warehouse.locations)) {
                    return 0;
                }
                const existingIndexes = warehouse.locations
                    .filter(shelf => shelf && typeof shelf.slot_index === 'number')
                    .map(shelf => shelf.slot_index);
                return existingIndexes.length ? Math.max(...existingIndexes) + 1 : 0;
            }

            function dedupeWarehouseLocations(warehouse) {
                // Keep only one shelf per slot_index. Prefer shelves that have products.
                if (!warehouse || !Array.isArray(warehouse.locations)) return;
                const byIndex = new Map();
                warehouse.locations.forEach(shelf => {
                    if (!shelf || typeof shelf.slot_index !== 'number') return;
                    const key = shelf.slot_index;
                    const existing = byIndex.get(key);
                    if (!existing) {
                        byIndex.set(key, shelf);
                        return;
                    }
                    const existingHas = Array.isArray(existing.products) && existing.products.length > 0;
                    const shelfHas = Array.isArray(shelf.products) && shelf.products.length > 0;
                    if (existingHas && !shelfHas) {
                        // keep existing
                    } else if (!existingHas && shelfHas) {
                        // prefer the shelf that actually has products
                        byIndex.set(key, shelf);
                    } else {
                        // both have products or both empty — prefer existing (stable)
                    }
                });
                warehouse.locations = Array.from(byIndex.values()).sort((a, b) => (a.slot_index || 0) - (b.slot_index || 0));
            }

            function goToLastWarehousePage(index) {
                const warehouse = warehouses[index];
                const totalPages = Math.max(1, Math.ceil(warehouse.locations.length / SHELVES_PER_PAGE));
                warehousePage[index] = totalPages - 1;
            }

            const modalWarehouseSelect = document.getElementById('modal-warehouse-select');
            if (modalWarehouseSelect) {
                modalWarehouseSelect.addEventListener('change', function() {
                    const warehouseIndex = parseInt(this.value, 10);
                    if (!Number.isNaN(warehouseIndex) && warehouses[warehouseIndex]) {
                        updateModalShelfTemplate(warehouseIndex);
                    }
                });
            }
            if (warehouseSelector) {
                warehouseSelector.addEventListener('change', function() {
                    const warehouseId = parseInt(this.value, 10);
                    if (!Number.isNaN(warehouseId)) {
                        showWarehouse(warehouseId);
                    }
                });
            }

            if (addShelfButton) {
                addShelfButton.addEventListener('click', function() {
                    const currentWarehouse = getCurrentWarehouseIndex();
                    const nextSlot = getNextShelfIndex(warehouses[currentWarehouse]);
                    showModal('Add Shelf', currentWarehouse, nextSlot, 'addShelf');
                });
            }

            // Add Warehouse Modal
            const addWarehouseButton = document.getElementById('add-warehouse-button');
            const addWarehouseModal = document.getElementById('add-warehouse-modal');
            const addWarehouseForm = document.getElementById('add-warehouse-form');
            const cancelAddWarehouseButton = document.getElementById('cancel-add-warehouse');

            if (addWarehouseButton) {
                addWarehouseButton.addEventListener('click', function() {
                    addWarehouseModal.classList.remove('hidden');
                    addWarehouseModal.classList.add('flex');
                });
            }

            if (cancelAddWarehouseButton) {
                cancelAddWarehouseButton.addEventListener('click', function() {
                    addWarehouseModal.classList.add('hidden');
                    addWarehouseModal.classList.remove('flex');
                    addWarehouseForm.reset();
                });
            }

            if (addWarehouseForm) {
                addWarehouseForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const name = document.getElementById('warehouse-name').value.trim();
                    const code = 'WH-' + name.toUpperCase().replace(/\s+/g, '-').substring(0, 10);

                    try {
                        const response = await fetch('/warehouse-management/add-warehouse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                name: name,
                                code: code,
                            }),
                        });

                        const result = await response.json();

                        if (result.success) {
                            showToast('Warehouse added successfully!', 'success');
                            addWarehouseModal.classList.add('hidden');
                            addWarehouseModal.classList.remove('flex');
                            addWarehouseForm.reset();

                            // Reload page to show new warehouse
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showToast(result.message || 'Failed to add warehouse', 'error');
                        }
                    } catch (error) {
                        console.error('Error adding warehouse:', error);
                        showToast('Failed to add warehouse. Please try again.', 'error');
                    }
                });
            }

            // Transfer Shelf Modal
            const transferShelfModal = document.getElementById('transfer-shelf-modal');
            const transferShelfForm = document.getElementById('transfer-shelf-form');
            const cancelTransferShelfButton = document.getElementById('cancel-transfer-shelf');

            if (cancelTransferShelfButton) {
                cancelTransferShelfButton.addEventListener('click', function() {
                    transferShelfModal.classList.add('hidden');
                    transferShelfModal.classList.remove('flex');
                    transferShelfForm.reset();
                });
            }

            if (transferShelfForm) {
                transferShelfForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const sourceWarehouseId = document.getElementById('transfer-source-warehouse-id').value;
                    const destinationWarehouseId = document.getElementById('transfer-destination-warehouse').value;
                    const slotIndex = document.getElementById('transfer-slot-index').value;

                    try {
                        const response = await fetch('/warehouse-management/transfer-shelf', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                source_warehouse_id: sourceWarehouseId,
                                destination_warehouse_id: destinationWarehouseId,
                                slot_index: slotIndex,
                            }),
                        });

                        const result = await response.json();

                        if (result.success) {
                            showToast('Shelf transferred successfully!', 'success');
                            transferShelfModal.classList.add('hidden');
                            transferShelfModal.classList.remove('flex');
                            transferShelfForm.reset();

                            // Reload page to show updated warehouse
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showToast(result.message || 'Failed to transfer shelf', 'error');
                        }
                    } catch (error) {
                        console.error('Error transferring shelf:', error);
                        showToast('Failed to transfer shelf. Please try again.', 'error');
                    }
                });
            }

            function openTransferShelfModal(warehouseId, slotIndex) {
                const warehouse = warehouses.find(wh => wh.id === warehouseId);
                const shelf = findShelfBySlotIndex(warehouse, slotIndex);

                if (!warehouse || !shelf) {
                    showToast('Shelf not found', 'error');
                    return;
                }

                document.getElementById('transfer-source-warehouse-id').value = warehouseId;
                document.getElementById('transfer-slot-index').value = slotIndex;
                document.getElementById('transfer-current-shelf').value = `${shelf.name} (${warehouse.name})`;

                // Populate destination warehouse dropdown (exclude current warehouse)
                const destinationSelect = document.getElementById('transfer-destination-warehouse');
                destinationSelect.innerHTML = '<option value="">Select destination warehouse</option>';

                warehouses.forEach(wh => {
                    if (wh.id !== warehouseId) {
                        const option = document.createElement('option');
                        option.value = wh.id;
                        option.textContent = wh.name;
                        destinationSelect.appendChild(option);
                    }
                });

                transferShelfModal.classList.remove('hidden');
                transferShelfModal.classList.add('flex');
            }

            // QR Code Generation and Scanning
            const generateQRButton = document.getElementById('wm-generate-qr');
            const scanQRButton = document.getElementById('wm-scan-qr');
            const mobileScannerButton = document.getElementById('wm-mobile-scanner');
            const restockDateInput = document.getElementById('wm-qr-restock-date');

            // Set restock date to today
            const today = new Date().toISOString().split('T')[0];
            if (restockDateInput) {
                restockDateInput.value = today;
            }

            // Initialize with 3 product rows
            if (document.getElementById('wm-product-rows')) {
                for (let i = 0; i < 3; i++) {
                    wmAddProductRow();
                }
            }

            if (generateQRButton) {
                generateQRButton.addEventListener('click', function() {
                    document.getElementById('wm-qr-modal').style.display = 'flex';
                });
            }

            if (scanQRButton) {
                scanQRButton.addEventListener('click', function() {
                    document.getElementById('wm-scan-modal').style.display = 'flex';
                    wmInitScanner();
                });
            }

            if (mobileScannerButton) {
                mobileScannerButton.addEventListener('click', wmOpenMobileScanner);
            }

            // Start polling for mobile scanner data
            wmStartMobileScannerPolling();

            let wmScanner = null;

            function wmInitScanner() {
                if (!wmScanner) {
                    wmScanner = new Html5Qrcode("wm-scanner-reader");
                }

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                wmScanner.start(
                    { facingMode: "environment" },
                    config,
                    wmOnScanSuccess,
                    wmOnScanFailure
                ).catch(err => {
                    console.error("Warehouse scanner error:", err);
                    document.getElementById('wm-scanner-status').textContent = 'Camera access denied or not available';
                    document.getElementById('wm-scanner-status').classList.add('text-red-400');
                });
            }

            function wmCloseScanner() {
                document.getElementById('wm-scan-modal').style.display = 'none';
                if (wmScanner) {
                    wmScanner.stop().catch(err => console.error(err));
                }
                document.getElementById('wm-scanner-status').textContent = 'Position QR code within the frame';
                document.getElementById('wm-scanner-status').classList.remove('text-red-400');
            }

            function wmOnScanSuccess(decodedText, decodedResult) {
                document.getElementById('wm-scanner-status').textContent = 'Scanned: ' + decodedText;
                document.getElementById('wm-scanner-status').classList.add('text-green-400');

                setTimeout(() => {
                    document.getElementById('wm-scanner-status').classList.remove('text-green-400');
                    document.getElementById('wm-scanner-status').textContent = 'Position QR code within the frame';
                }, 2000);

                // Handle scanned QR code - add to warehouse
                wmHandleScannedCode(decodedText);
            }

            function wmOnScanFailure(error) {
                // Ignore scan failures
            }

            let wmGeneratedQRs = [];
            let wmCurrentPage = 0;
            const WM_QR_PER_PAGE = 6;
            let wmScannedNewStock = null;
            let wmScannedItems = [];
            let wmScanCurrentPage = 0;
            const WM_SCAN_PER_PAGE = 4;

            async function wmHandleScannedCode(code) {
                try {
                    const parsed = JSON.parse(code);
                    const productName = parsed.product_name;
                    const sku = parsed.sku;
                    const restockDate = parsed.restock_date;

                    if (productName && sku && parsed.type === 'new_stock') {
                        // Check if already scanned
                        const alreadyScanned = wmScannedItems.find(item => item.sku === sku);
                        if (alreadyScanned) {
                            showToast('This item is already scanned', 'error');
                            return;
                        }

                        // Add to scanned items list
                        wmScannedItems.push({ productName, sku, restockDate });
                        wmUpdateScannedList();
                        showToast(`Scanned: ${productName}`, 'success');
                    } else {
                        showToast('Invalid new stock QR code', 'error');
                    }
                } catch (e) {
                    showToast('Invalid QR code format', 'error');
                }
            }

            function wmUpdateScannedList() {
                const scannedItemsDiv = document.getElementById('wm-scanned-items');
                const scanList = document.getElementById('wm-scan-list');
                const scanCount = document.getElementById('wm-scan-count');
                const proceedBtn = document.getElementById('wm-proceed-btn');
                const pagination = document.getElementById('wm-scan-pagination');

                if (wmScannedItems.length > 0) {
                    scannedItemsDiv.classList.remove('hidden');
                    scanCount.textContent = wmScannedItems.length;
                    proceedBtn.disabled = false;

                    // Show pagination if needed
                    if (wmScannedItems.length > WM_SCAN_PER_PAGE) {
                        pagination.classList.remove('hidden');
                        pagination.classList.add('flex');
                    } else {
                        pagination.classList.add('hidden');
                        pagination.classList.remove('flex');
                    }

                    // Render current page
                    const start = wmScanCurrentPage * WM_SCAN_PER_PAGE;
                    const end = Math.min(start + WM_SCAN_PER_PAGE, wmScannedItems.length);
                    const pageItems = wmScannedItems.slice(start, end);

                    scanList.innerHTML = pageItems.map((item, index) => {
                        const actualIndex = start + index;
                        return `
                        <div class="flex items-center justify-between bg-slate-50 rounded-lg p-3 border border-slate-200">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">${item.productName}</p>
                                <p class="text-xs text-slate-600">SKU: ${item.sku}</p>
                                <p class="text-xs text-slate-500">Restock: ${item.restockDate || 'N/A'}</p>
                            </div>
                            <button onclick="wmRemoveScannedItem(${actualIndex})" class="text-red-500 hover:text-red-700 p-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    }).join('');

                    // Update pagination controls
                    const totalPages = Math.ceil(wmScannedItems.length / WM_SCAN_PER_PAGE);
                    document.getElementById('wm-scan-page-info').textContent = `Page ${wmScanCurrentPage + 1} of ${totalPages}`;
                    document.getElementById('wm-scan-prev-page').disabled = wmScanCurrentPage === 0;
                    document.getElementById('wm-scan-next-page').disabled = wmScanCurrentPage >= totalPages - 1;
                } else {
                    scannedItemsDiv.classList.add('hidden');
                    proceedBtn.disabled = true;
                    wmScanCurrentPage = 0;
                }
            }

            function wmScanPrevPage() {
                if (wmScanCurrentPage > 0) {
                    wmScanCurrentPage--;
                    wmUpdateScannedList();
                }
            }

            function wmScanNextPage() {
                const totalPages = Math.ceil(wmScannedItems.length / WM_SCAN_PER_PAGE);
                if (wmScanCurrentPage < totalPages - 1) {
                    wmScanCurrentPage++;
                    wmUpdateScannedList();
                }
            }

            function wmOpenMobileScanner() {
                window.open('{{ route("warehouse.mobile.scanner") }}', 'WarehouseMobileScanner', 'width=400,height=600');
            }

            function wmStartMobileScannerPolling() {
                setInterval(() => {
                    const scannedData = localStorage.getItem('warehouseScannedItems');
                    const timestamp = localStorage.getItem('warehouseScanTimestamp');

                    if (scannedData && timestamp) {
                        const scanTime = parseInt(timestamp);
                        const now = Date.now();

                        // Only process if data is recent (within 5 seconds)
                        if (now - scanTime < 5000) {
                            try {
                                const items = JSON.parse(scannedData);

                                // Add items to scanned list
                                items.forEach(item => {
                                    const alreadyScanned = wmScannedItems.find(i => i.sku === item.sku);
                                    if (!alreadyScanned) {
                                        wmScannedItems.push({
                                            productName: item.product_name,
                                            sku: item.sku,
                                            restockDate: item.restock_date
                                        });
                                    }
                                });

                                wmUpdateScannedList();

                                // Clear localStorage after processing
                                localStorage.removeItem('warehouseScannedItems');
                                localStorage.removeItem('warehouseScanTimestamp');

                                showToast('Received items from mobile scanner', 'success');
                            } catch (e) {
                                console.error('Error parsing mobile scanner data:', e);
                            }
                        }
                    }
                }, 1000);
            }

            function wmRemoveScannedItem(index) {
                wmScannedItems.splice(index, 1);
                // Adjust page if needed
                const totalPages = Math.ceil(wmScannedItems.length / WM_SCAN_PER_PAGE);
                if (wmScanCurrentPage >= totalPages && wmScanCurrentPage > 0) {
                    wmScanCurrentPage = totalPages - 1;
                }
                wmUpdateScannedList();
            }

            function wmClearScannedItems() {
                wmScannedItems = [];
                wmScanCurrentPage = 0;
                wmUpdateScannedList();
            }

            function wmProceedToDetails() {
                if (wmScannedItems.length === 0) return;

                wmCloseScanner();

                // Open modal for first item
                wmScannedNewStock = wmScannedItems[0];
                wmOpenNewStockModal();
            }

            function wmOpenNewStockModal() {
                if (!wmScannedNewStock) return;

                document.getElementById('wm-ns-product-name').textContent = wmScannedNewStock.productName;
                document.getElementById('wm-ns-sku').textContent = wmScannedNewStock.sku;
                document.getElementById('wm-ns-restock-date').textContent = wmScannedNewStock.restockDate;

                // Clear form fields
                document.getElementById('wm-ns-price').value = '';
                document.getElementById('wm-ns-quantity').value = '';
                document.getElementById('wm-ns-category').value = '';
                document.getElementById('wm-ns-description').value = '';

                // Update button text if multiple items
                const saveBtn = document.querySelector('#wm-new-stock-modal button[onclick="wmSaveNewStock()"]');
                const currentIndex = wmScannedItems.findIndex(item => item.sku === wmScannedNewStock.sku);
                if (currentIndex < wmScannedItems.length - 1) {
                    saveBtn.textContent = `Save & Next (${currentIndex + 1}/${wmScannedItems.length})`;
                } else {
                    saveBtn.textContent = 'Save & Add to Warehouse';
                }

                document.getElementById('wm-new-stock-modal').style.display = 'flex';
            }

            function wmCloseNewStockModal() {
                document.getElementById('wm-new-stock-modal').style.display = 'none';
                wmScannedNewStock = null;
            }

            async function wmSaveNewStock() {
                if (!wmScannedNewStock) return;

                const price = parseFloat(document.getElementById('wm-ns-price').value);
                const quantity = parseInt(document.getElementById('wm-ns-quantity').value);
                const category = document.getElementById('wm-ns-category').value;
                const description = document.getElementById('wm-ns-description').value;

                if (!price || !quantity || !category) {
                    showToast('Please fill in price, quantity, and category', 'error');
                    return;
                }

                try {
                    // Create product in All Stocks using stock.add endpoint
                    const response = await fetch('/stock/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            name: wmScannedNewStock.productName,
                            sku: wmScannedNewStock.sku,
                            price: price,
                            category: category,
                            description: description,
                            quantity: quantity,
                        }),
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to create product');
                    }

                    const result = await response.json();
                    const productId = result.id || result.product_id;

                    if (!productId) {
                        throw new Error('No product ID returned');
                    }

                    // Save product data for warehouse
                    wmScannedNewStock.productId = productId;
                    wmScannedNewStock.price = price;
                    wmScannedNewStock.quantity = quantity;
                    wmScannedNewStock.restockDate = wmScannedNewStock.restockDate || restockDate;

                    // Check if there are more items
                    const currentIndex = wmScannedItems.findIndex(item => item.sku === wmScannedNewStock.sku);
                    if (currentIndex < wmScannedItems.length - 1) {
                        // Move to next item
                        wmScannedNewStock = wmScannedItems[currentIndex + 1];
                        wmCloseNewStockModal();
                        setTimeout(() => wmOpenNewStockModal(), 100);
                        showToast('Product saved. Next item...', 'success');
                    } else {
                        // All items done, add to warehouse
                        wmCloseNewStockModal();
                        wmAddAllToWarehouse();
                    }

                } catch (error) {
                    console.error('Error saving new stock:', error);
                    showToast(error.message || 'Failed to create product. Please try again.', 'error');
                }
            }

            async function wmAddAllToWarehouse() {
                const currentWarehouse = getCurrentWarehouseIndex();
                const nextSlot = getNextShelfIndex(warehouses[currentWarehouse]);
                showModal('Add Product', currentWarehouse, nextSlot, 'addProduct');

                // Pre-fill with all scanned products
                setTimeout(() => {
                    const container = document.getElementById('modal-product-rows');
                    container.innerHTML = '';

                    wmScannedItems.forEach(item => {
                        if (item.productId) {
                            const row = document.createElement('div');
                            row.className = 'product-row';
                            row.innerHTML = `
                                <div class="flex gap-3">
                                    <select class="product-select flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm">
                                        <option value="">Select product</option>
                                    </select>
                                    <input type="text" class="product-sku w-24 px-3 py-2 border border-slate-300 rounded-lg text-sm" value="${item.sku}">
                                    <input type="number" class="product-qty w-20 px-3 py-2 border border-slate-300 rounded-lg text-sm" value="${item.quantity}">
                                    <input type="number" class="product-price w-24 px-3 py-2 border border-slate-300 rounded-lg text-sm" value="${item.price}">
                                    <input type="date" class="product-restock w-32 px-3 py-2 border border-slate-300 rounded-lg text-sm" value="${item.restockDate || ''}">
                                </div>
                            `;
                            container.appendChild(row);

                            // Set the select value
                            const select = row.querySelector('select');
                            select.value = item.productId;
                        }
                    });

                    wmScannedItems = [];
                    showToast('All products created. Please select a shelf to add them.', 'success');
                }, 100);
            }

            function wmAddProductRow() {
                const container = document.getElementById('wm-product-rows');
                if (!container) return;

                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 bg-slate-50 rounded-lg p-3';
                row.innerHTML = `
                    <input type="text" class="wm-product-name flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Enter product name">
                    <div class="wm-sku-display text-xs font-mono font-bold text-slate-700 min-w-[100px]">KCC_</div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;

                // Auto-generate SKU as user types
                const nameInput = row.querySelector('.wm-product-name');
                const skuDisplay = row.querySelector('.wm-sku-display');

                nameInput.addEventListener('input', function() {
                    const name = this.value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
                    skuDisplay.textContent = name ? `KCC_${name}` : 'KCC_';
                });

                container.appendChild(row);
            }

            function wmGenerateAllQR() {
                const productRows = document.querySelectorAll('#wm-product-rows > div');
                const restockDate = document.getElementById('wm-qr-restock-date').value;
                const previewContainer = document.getElementById('wm-qr-preview');

                const products = [];
                productRows.forEach(row => {
                    const nameInput = row.querySelector('.wm-product-name');
                    const name = nameInput.value.trim();
                    if (name) {
                        const sku = `KCC_${name.toUpperCase().replace(/[^A-Z0-9]/g, '')}`;
                        products.push({ name, sku });
                    }
                });

                if (products.length === 0) {
                    showToast('Please enter at least one product name', 'error');
                    return;
                }

                // Show loading
                previewContainer.innerHTML = '<div id="wm-qr-loading" class="text-center py-12"><div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div><p class="text-sm text-slate-600">Generating QR codes...</p></div>';

                setTimeout(() => {
                    previewContainer.innerHTML = '';
                    wmGeneratedQRs = [];
                    wmCurrentPage = 0;

                    products.forEach((product, index) => {
                        // Create QR code data for new stock
                        const qrData = JSON.stringify({
                            product_name: product.name,
                            sku: product.sku,
                            restock_date: restockDate,
                            type: 'new_stock'
                        });

                        wmGeneratedQRs.push({
                            product,
                            qrData,
                            index
                        });
                    });

                    wmRenderQRPage();

                    // Show pagination if needed
                    const pagination = document.getElementById('wm-qr-pagination');
                    if (wmGeneratedQRs.length > WM_QR_PER_PAGE) {
                        pagination.classList.remove('hidden');
                        pagination.classList.add('flex');
                    } else {
                        pagination.classList.add('hidden');
                        pagination.classList.remove('flex');
                    }

                    showToast(`Generated ${products.length} QR codes successfully`, 'success');
                }, 500);
            }

            function wmRenderQRPage() {
                const previewContainer = document.getElementById('wm-qr-preview');
                previewContainer.innerHTML = '';

                const start = wmCurrentPage * WM_QR_PER_PAGE;
                const end = Math.min(start + WM_QR_PER_PAGE, wmGeneratedQRs.length);
                const pageItems = wmGeneratedQRs.slice(start, end);

                pageItems.forEach((item) => {
                    const { product, qrData, index } = item;

                    const qrCard = document.createElement('div');
                    qrCard.className = 'border border-slate-200 rounded-lg p-4 bg-white';
                    qrCard.innerHTML = `
                        <div class="flex items-start gap-4">
                            <div id="wm-qr-code-${index}" class="w-24 h-24 flex items-center justify-center bg-white"></div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900 text-sm">${product.name}</h3>
                                <p class="text-xs text-slate-600">SKU: ${product.sku}</p>
                                <p class="text-xs text-slate-600">Restock: ${document.getElementById('wm-qr-restock-date').value}</p>
                                <p class="text-xs text-slate-500 mt-1">Scan to add new stock to system</p>
                            </div>
                        </div>
                    `;

                    previewContainer.appendChild(qrCard);

                    // Generate QR code
                    try {
                        const qrElement = document.getElementById(`wm-qr-code-${index}`);
                        new QRCode(qrElement, {
                            text: qrData,
                            width: 96,
                            height: 96,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } catch (error) {
                        console.error('Error generating QR code:', error);
                    }
                });

                // Update pagination controls
                const totalPages = Math.ceil(wmGeneratedQRs.length / WM_QR_PER_PAGE);
                document.getElementById('wm-page-info').textContent = `Page ${wmCurrentPage + 1} of ${totalPages}`;
                document.getElementById('wm-prev-page').disabled = wmCurrentPage === 0;
                document.getElementById('wm-next-page').disabled = wmCurrentPage >= totalPages - 1;
            }

            function wmPrevPage() {
                if (wmCurrentPage > 0) {
                    wmCurrentPage--;
                    wmRenderQRPage();
                }
            }

            function wmNextPage() {
                const totalPages = Math.ceil(wmGeneratedQRs.length / WM_QR_PER_PAGE);
                if (wmCurrentPage < totalPages - 1) {
                    wmCurrentPage++;
                    wmRenderQRPage();
                }
            }

            function wmPrintAllQR() {
                if (wmGeneratedQRs.length === 0) {
                    showToast('Please generate QR codes first', 'error');
                    return;
                }

                try {
                    const printWindow = window.open('', '_blank');

                    if (!printWindow) {
                        showToast('Popup blocked. Please allow popups.', 'error');
                        return;
                    }

                    let htmlContent = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>New Stock QR Codes</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    padding: 20px;
                                }
                                .qr-grid {
                                    display: grid;
                                    grid-template-columns: repeat(2, 1fr);
                                    gap: 20px;
                                    margin-top: 20px;
                                }
                                .qr-card {
                                    border: 1px solid #ccc;
                                    padding: 15px;
                                    border-radius: 8px;
                                    text-align: center;
                                    page-break-inside: avoid;
                                }
                                .qr-card img {
                                    max-width: 150px;
                                    border: 1px solid #eee;
                                    padding: 10px;
                                }
                                .qr-card .info {
                                    margin: 10px 0;
                                    font-size: 12px;
                                }
                                @media print {
                                    .qr-grid {
                                        grid-template-columns: repeat(3, 1fr);
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <h2>New Stock QR Codes</h2>
                            <p>Restock Date: ${document.getElementById('wm-qr-restock-date').value}</p>
                            <div class="qr-grid">
                    `;

                    // Generate all QR codes for printing
                    wmGeneratedQRs.forEach((item, index) => {
                        const { product, qrData } = item;

                        // Create a temporary canvas to generate QR code
                        const tempDiv = document.createElement('div');
                        tempDiv.style.display = 'none';
                        document.body.appendChild(tempDiv);

                        const tempQrElement = document.createElement('div');
                        tempDiv.appendChild(tempQrElement);

                        new QRCode(tempQrElement, {
                            text: qrData,
                            width: 150,
                            height: 150,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });

                        setTimeout(() => {
                            const canvas = tempQrElement.querySelector('canvas');
                            if (canvas) {
                                const dataUrl = canvas.toDataURL('image/png');

                                htmlContent += `
                                    <div class="qr-card">
                                        <div class="info">
                                            <p><strong>${product.name}</strong></p>
                                            <p>${product.sku}</p>
                                            <p>Restock: ${document.getElementById('wm-qr-restock-date').value}</p>
                                        </div>
                                        <img src="${dataUrl}" alt="QR Code">
                                    </div>
                                `;
                            }

                            document.body.removeChild(tempDiv);
                        }, 100);
                    });

                    setTimeout(() => {
                        htmlContent += `
                            </div>
                            <p style="margin-top: 20px; font-size: 12px;">Scan each QR code to add new stock to the system</p>
                        </body>
                        </html>
                        `;

                        printWindow.document.write(htmlContent);
                        printWindow.document.close();
                        setTimeout(() => {
                            printWindow.print();
                        }, 500);
                    }, wmGeneratedQRs.length * 150);

                } catch (error) {
                    console.error('Error printing:', error);
                    showToast('Error opening print dialog', 'error');
                }
            }

            // Expose functions globally
            window.wmAddProductRow = wmAddProductRow;
            window.wmGenerateAllQR = wmGenerateAllQR;
            window.wmPrintAllQR = wmPrintAllQR;
            window.wmPrevPage = wmPrevPage;
            window.wmNextPage = wmNextPage;
            window.wmCloseScanner = wmCloseScanner;
            window.wmCloseNewStockModal = wmCloseNewStockModal;
            window.wmSaveNewStock = wmSaveNewStock;
            window.wmUpdateScannedList = wmUpdateScannedList;
            window.wmRemoveScannedItem = wmRemoveScannedItem;
            window.wmClearScannedItems = wmClearScannedItems;
            window.wmProceedToDetails = wmProceedToDetails;
            window.wmAddAllToWarehouse = wmAddAllToWarehouse;
            window.wmScanPrevPage = wmScanPrevPage;
            window.wmScanNextPage = wmScanNextPage;


            // Archived pagination variables
            let archivedItems = [];
            let archivedPage = 0;
            const ARCHIVED_PER_PAGE = 6;

            function findArchivedShelfBySlotIndex(warehouse, slotIndex) {
                if (!warehouse || !Array.isArray(warehouse.archivedShelves)) {
                    return null;
                }
                return warehouse.archivedShelves.find(shelf => shelf && shelf.slot_index === slotIndex) || null;
            }

            function renderArchivedPage(pageIndex = 0) {
                archivedList.innerHTML = '';
                const total = archivedItems.length;
                const totalPages = Math.max(1, Math.ceil(total / ARCHIVED_PER_PAGE));
                archivedPage = Math.max(0, Math.min(totalPages - 1, pageIndex));
                const start = archivedPage * ARCHIVED_PER_PAGE;
                const slice = archivedItems.slice(start, start + ARCHIVED_PER_PAGE);

                slice.forEach(item => {
                    const { whIndex, locIndex, whName, loc } = item;
                    const el = document.createElement('div');
                    el.className = 'p-3 rounded-md border border-slate-200 bg-white flex items-center justify-between';
                    el.innerHTML = `<div><div class="font-medium">${loc.name}</div><div class="text-xs text-slate-500">${whName}</div></div><div><button class="restore-shelf px-3 py-1 rounded-md text-sm bg-emerald-600 text-white" data-widx="${whIndex}" data-lidx="${locIndex}">Restore</button></div>`;
                    archivedList.appendChild(el);

                    const btn = el.querySelector('.restore-shelf');
                    btn.addEventListener('click', async function() {
                        const wi = parseInt(this.dataset.widx, 10);
                        const li = parseInt(this.dataset.lidx, 10);
                        const warehouse = warehouses[wi];
                        const shelf = findArchivedShelfBySlotIndex(warehouse, li);
                        if (!warehouse || !shelf) {
                            return;
                        }

                        const saved = await saveShelfData({
                            warehouse_id: warehouses[wi].id,
                            slot_index: li,
                            name: shelf.name,
                            products: shelf.products || [],
                            archived: false,
                        });
                        if (!saved) {
                            return;
                        }

                        shelf.archived = false;
                        warehouse.locations = warehouse.locations || [];
                        warehouse.locations.push(shelf);
                        // remove duplicates preferring shelves with products
                        dedupeWarehouseLocations(warehouse);
                        warehouse.archivedShelves = (warehouse.archivedShelves || []).filter(item => item.slot_index !== li);

                        showToast(`${shelf.name} restored to ${warehouse.name}.`, 'success');
                        archivedItems = archivedItems.filter(it => !(it.whIndex === wi && it.locIndex === li));
                        renderArchivedPage(archivedPage);
                        const current = getCurrentWarehouseIndex();
                        if (current === wi) {
                            renderWarehousePage(warehouses[wi].id, warehousePage[wi]);
                            updateWarehouseStats(wi);
                        }
                    });
                });

                // update page info
                const pageInfo = document.getElementById('archived-page-info');
                if (pageInfo) pageInfo.textContent = `Page ${archivedPage + 1} of ${Math.max(1, Math.ceil(archivedItems.length / ARCHIVED_PER_PAGE))}`;
                document.getElementById('archived-prev').disabled = archivedPage === 0;
                document.getElementById('archived-next').disabled = archivedPage >= Math.ceil(archivedItems.length / ARCHIVED_PER_PAGE) - 1;
            }

            if (viewArchivedButton) {
                viewArchivedButton.addEventListener('click', function() {
                    // gather archived shelves into an array for pagination
                    archivedItems = [];
                    warehouses.forEach((wh, widx) => {
                        (wh.archivedShelves || []).forEach((loc) => {
                            if (loc && loc.archived) {
                                archivedItems.push({ whIndex: widx, locIndex: loc.slot_index, whName: wh.name, loc });
                            }
                        });
                    });

                    archivedPage = 0;
                    renderArchivedPage(0);
                    archivedBackdrop.classList.remove('hidden');
                    archivedBackdrop.classList.add('flex');
                });
            }

            if (viewArchivedWarehousesButton) {
                viewArchivedWarehousesButton.addEventListener('click', function() {
                    window.location.href = '/warehouse-management/archived';
                });
            }

            // Handle warehouse action dropdown
            document.querySelectorAll('.warehouse-action-select').forEach(select => {
                select.addEventListener('change', function() {
                    const action = this.value;
                    const warehouseId = parseInt(this.dataset.id, 10);

                    if (action === 'archive-warehouse') {
                        archiveWarehouse(warehouseId);
                    }

                    this.value = '';
                });
            });

            async function archiveWarehouse(warehouseId) {
                if (!confirm('Are you sure you want to archive this warehouse? This will hide it from the main view.')) {
                    return;
                }

                try {
                    const response = await fetch(`/warehouse-management/archive/${warehouseId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast('Warehouse archived successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showToast(result.message || 'Failed to archive warehouse', 'error');
                    }
                } catch (error) {
                    console.error('Error archiving warehouse:', error);
                    showToast('Failed to archive warehouse. Please try again.', 'error');
                }
            }

            document.getElementById('archived-prev').addEventListener('click', function() { renderArchivedPage(archivedPage - 1); });
            document.getElementById('archived-next').addEventListener('click', function() { renderArchivedPage(archivedPage + 1); });

            if (archivedClose) archivedClose.addEventListener('click', () => { archivedBackdrop.classList.add('hidden'); archivedBackdrop.classList.remove('flex'); });
            if (archivedDone) archivedDone.addEventListener('click', () => { archivedBackdrop.classList.add('hidden'); archivedBackdrop.classList.remove('flex'); });

            document.querySelectorAll('.prev-page').forEach(button => {
                button.addEventListener('click', function() {
                    const warehouseId = parseInt(this.dataset.id, 10);
                    const warehouseIndex = warehouses.findIndex(wh => wh.id === warehouseId);
                    warehousePage[warehouseIndex] = Math.max(0, warehousePage[warehouseIndex] - 1);
                    renderWarehousePage(warehouseId, warehousePage[warehouseIndex]);
                });
            });

            document.querySelectorAll('.next-page').forEach(button => {
                button.addEventListener('click', function() {
                    const warehouseId = parseInt(this.dataset.id, 10);
                    const warehouseIndex = warehouses.findIndex(wh => wh.id === warehouseId);
                    const warehouse = warehouses[warehouseIndex];
                    const totalPages = Math.max(1, Math.ceil(warehouse.locations.length / SHELVES_PER_PAGE));
                    warehousePage[warehouseIndex] = Math.min(totalPages - 1, warehousePage[warehouseIndex] + 1);
                    renderWarehousePage(warehouseId, warehousePage[warehouseIndex]);
                });
            });

            function shelfMatchesQuery(loc, query) {
                if (!query) {
                    return true;
                }
                if (!loc || loc.archived) {
                    return false;
                }
                const normalized = query.toLowerCase();
                if ((loc.name || '').toLowerCase().includes(normalized)) {
                    return true;
                }
                return Array.isArray(loc.products) && loc.products.some(product => {
                    const name = (product.name || '').toLowerCase();
                    const skuValue = (product.sku || `KCC_${(product.name || '').replace(/[^A-Za-z0-9\-\+]/g, '')}`).toLowerCase();
                    return name.includes(normalized) || skuValue.includes(normalized);
                });
            }

            function getFilteredShelfSlots(warehouse) {
                const query = warehouseSearchQuery.trim().toLowerCase();
                const allSlots = warehouse.locations.map((loc) => ({ loc, slotIndex: loc?.slot_index ?? null }));
                if (!query) {
                    return allSlots;
                }
                return allSlots.filter(item => shelfMatchesQuery(item.loc, query));
            }

            function renderWarehousePage(warehouseId, pageIndex = 0) {
                const warehouseIndex = warehouses.findIndex(wh => wh.id === warehouseId);
                const warehouse = warehouses[warehouseIndex];
                // ensure no duplicate slot_index entries before rendering
                dedupeWarehouseLocations(warehouse);
                const shelfSlots = getFilteredShelfSlots(warehouse);
                const totalPages = Math.max(1, Math.ceil(shelfSlots.length / SHELVES_PER_PAGE));
                const start = pageIndex * SHELVES_PER_PAGE;
                const shelvesContainer = document.querySelector(`.warehouse-shelves[data-id="${warehouseId}"]`);
                const pageInfo = document.querySelector(`.page-info[data-id="${warehouseId}"]`);
                const prevButton = document.querySelector(`.prev-page[data-id="${warehouseId}"]`);
                const nextButton = document.querySelector(`.next-page[data-id="${warehouseId}"]`);
                warehousePage[warehouseIndex] = Math.min(Math.max(0, pageIndex), totalPages - 1);

                shelvesContainer.classList.add('grid', 'grid-cols-2', 'gap-4');

                let html = '';
                const currentPageSlots = shelfSlots.slice(warehousePage[warehouseIndex] * SHELVES_PER_PAGE, warehousePage[warehouseIndex] * SHELVES_PER_PAGE + SHELVES_PER_PAGE);
                if (!currentPageSlots.length) {
                    const query = warehouseSearchQuery.trim();
                    html = `<div class="col-span-2 p-8 rounded-xl border border-dashed text-center text-sm text-slate-500 bg-slate-50">${query ? `No shelves found for "${query}".` : 'No shelves available.'}</div>`;
                } else {
                    currentPageSlots.forEach(item => {
                        const slotIndex = item.slotIndex;
                        const loc = item.loc || null;
                        const locView = (loc && loc.archived) ? null : loc;
                        const slotCount = Array.isArray(locView?.products) ? locView.products.length : 0;
                        let productsHtml = '';
                        for (let productSlot = 0; productSlot < PRODUCTS_PER_SHELF; productSlot += 1) {
                            const product = (locView ? (locView.products[productSlot] || null) : null);
                            if (product) {
                                const priceText = (product.price || product.price === 0) ? Number(product.price).toFixed(2) : '-';
                                let rawSku = (product.sku || product.name || '').replace(/[^A-Za-z0-9\-\+]/g, '');
                                const displaySku = rawSku.toUpperCase().startsWith('KCC_') ? rawSku : `KCC_${rawSku}`;
                                productsHtml += `<div class="product-chip rounded-xl bg-emerald-50 border border-emerald-100">
                                    <div class="left">
                                        <div class="name">${product.name}</div>
                                        <div class="meta">SKU: ${displaySku} • Price: ${priceText}</div>
                                    </div>
                                    <div class="qty">Qty: ${product.qty}</div>
                                </div>`;
                            } else {
                                productsHtml += `<div class="product-chip opacity-50 rounded-xl px-3 py-2 text-sm text-gray-500 border border-dashed border-gray-200">Empty slot</div>`;
                            }
                        }
                        const shelfTitle = locView?.name || 'Empty shelf';
                        const shelfCountText = locView ? `${slotCount}/${PRODUCTS_PER_SHELF} products` : `0/${PRODUCTS_PER_SHELF} products`;
                        const addProductOption = (locView && Array.isArray(locView.products) && locView.products.length < PRODUCTS_PER_SHELF) ? '<option value="add-product">Add Product</option>' : '';
                        html += `
                            <div class="map-unit rounded-2xl shadow-sm relative bg-white border border-gray-200" style="padding: 1rem;">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-base font-semibold text-slate-900">${shelfTitle}</div>
                                        <div class="text-xs text-gray-400 mt-1">${shelfCountText}</div>
                                    </div>
                                    <div>
                                        <label class="sr-only" for="action-select-${warehouseId}-${slotIndex}">Shelf actions</label>
                                        <select id="action-select-${warehouseId}-${slotIndex}" class="action-select text-sm text-slate-700 px-4 py-2 border border-gray-200 rounded-full bg-white hover:bg-gray-50 cursor-pointer appearance-none pr-8" data-id="${warehouseId}" data-slot="${slotIndex}">
                                            <option value="">Actions</option>
                                            ${locView ? `${addProductOption}<option value="edit-shelf">Edit Shelf</option><option value="transfer-products">Transfer Products</option><option value="transfer-shelf">Transfer Shelf</option><option value="archive-shelf">Archive Shelf</option>` : '<option value="add-shelf">Add Shelf</option>'}
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4 grid gap-2 grid-cols-2">
                                    ${productsHtml}
                                </div>
                            </div>
                        `;
                    });
                }

                shelvesContainer.innerHTML = html;
                pageInfo.textContent = `Page ${warehousePage[warehouseIndex] + 1} of ${totalPages}`;
                prevButton.disabled = warehousePage[warehouseIndex] === 0;
                nextButton.disabled = warehousePage[warehouseIndex] === totalPages - 1;

                shelvesContainer.querySelectorAll('.action-select').forEach(selectEl => {
                    selectEl.addEventListener('change', function() {
                        const action = this.value;
                        const warehouseId = parseInt(this.dataset.id, 10);
                        const warehouseIndex = warehouses.findIndex(wh => wh.id === warehouseId);
                        const slotIndex = parseInt(this.dataset.slot, 10);
                        if (action === 'add-shelf') {
                            openShelfModal(warehouseIndex, slotIndex);
                        } else if (action === 'edit-shelf') {
                            openEditShelfModal(warehouseIndex, slotIndex);
                        } else if (action === 'transfer-products') {
                            openTransferModal(warehouseIndex, slotIndex);
                        } else if (action === 'transfer-shelf') {
                            openTransferShelfModal(warehouseId, slotIndex);
                        } else if (action === 'add-product') {
                            openProductModal(warehouseIndex, slotIndex);
                        } else if (action === 'archive-shelf') {
                            archiveShelf(warehouseIndex, slotIndex);
                        } else if (action === 'delete-shelf') {
                            deleteShelf(warehouseIndex, slotIndex);
                        }
                        this.value = '';
                    });
                });

                updateWarehouseStats(warehouseIndex);
            }

            function openShelfModal(warehouseIndex, slotIndex) {
                if (Number.isNaN(warehouseIndex)) return;
                showModal('Add Shelf', warehouseIndex, slotIndex, 'addShelf');
            }

            function openEditShelfModal(warehouseIndex, slotIndex) {
                if (Number.isNaN(warehouseIndex)) return;
                showModal('Edit Shelf', warehouseIndex, slotIndex, 'editShelf');
            }

            function openProductModal(warehouseIndex, slotIndex) {
                if (Number.isNaN(warehouseIndex)) return;
                showModal('Add Product', warehouseIndex, slotIndex, 'addProduct');
            }

            function openTransferModal(warehouseIndex, slotIndex) {
                if (Number.isNaN(warehouseIndex)) return;
                const currentShelf = findShelfBySlotIndex(warehouses[warehouseIndex], slotIndex);
                if (!currentShelf || !currentShelf.products || currentShelf.products.length === 0) {
                    showToast('This shelf has no products to transfer.', 'error');
                    return;
                }
                // Open transfer modal with current shelf info
                window.location.href = `/warehouse/transfer/${warehouses[warehouseIndex].id}/${slotIndex}`;
            }

            function updateModalShelfTemplate(warehouseIndex) {
                const shelfName = document.getElementById('modal-shelf-name');
                const modalSlot = document.getElementById('modal-slot');
                const modalWarehouseIndex = document.getElementById('modal-warehouse-index');
                const warehouse = warehouses[warehouseIndex];
                const nextSlot = getNextShelfIndex(warehouse);
                shelfName.value = '';
                shelfName.placeholder = 'Enter shelf name';
                modalSlot.value = nextSlot;
                modalWarehouseIndex.value = warehouseIndex;
            }

            function showModal(title, warehouseIndex, slot, mode = 'addShelf') {
                document.getElementById('modal-title').textContent = title;
                document.getElementById('modal-mode').value = mode;
                const shelfName = document.getElementById('modal-shelf-name');
                const currentShelf = findShelfBySlotIndex(warehouses[warehouseIndex], slot);
                const modalWarehouseSelect = document.getElementById('modal-warehouse-select');

                if (mode === 'addShelf') {
                    modalWarehouseSelect.disabled = false;
                    modalWarehouseSelect.value = warehouseIndex;
                    updateModalShelfTemplate(warehouseIndex);
                    shelfName.readOnly = false;
                    resetProductRows([]);
                } else {
                    modalWarehouseSelect.disabled = true;
                    modalWarehouseSelect.value = warehouseIndex;
                    document.getElementById('modal-warehouse-index').value = warehouseIndex;
                    document.getElementById('modal-slot').value = slot;

                    if (mode === 'addProduct' && currentShelf) {
                        shelfName.value = currentShelf.name;
                        shelfName.readOnly = true;
                        resetProductRows(currentShelf.products);
                    } else if (mode === 'editShelf' && currentShelf) {
                        shelfName.value = currentShelf.name;
                        shelfName.readOnly = false;
                        resetProductRows(currentShelf.products);
                    } else {
                        shelfName.value = currentShelf ? currentShelf.name : '';
                        shelfName.readOnly = false;
                        resetProductRows([]);
                    }
                }

                document.getElementById('modal-backdrop').classList.remove('hidden');
                document.getElementById('modal-backdrop').classList.add('flex');
            }

            async function saveStock(productId, quantity, price, notes) {
                try {
                    const response = await fetch('{{ route('warehouse.management.add_product') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity,
                            unit_price: price,
                            notes,
                        }),
                    });
                    const responseText = await response.text();
                    let result = null;
                    try {
                        result = JSON.parse(responseText);
                    } catch (parseError) {
                        if (!response.ok) {
                            alert(responseText || 'Failed to save stock.');
                            return false;
                        }
                    }
                    if (!response.ok || !result?.success) {
                        alert(result?.message || 'Failed to save stock.');
                        return false;
                    }
                    return true;
                } catch (error) {
                    alert('Failed to save stock: ' + error.message);
                    return false;
                }
            }

            async function saveShelfData(shelfData) {
                try {
                    const response = await fetch('{{ route('warehouse.management.save_shelf') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(shelfData),
                    });
                    const responseText = await response.text();
                    console.error('saveShelf response', response.status, responseText);
                    let result = null;
                    try {
                        result = JSON.parse(responseText);
                    } catch (parseError) {
                        if (!response.ok) {
                            alert(responseText || 'Failed to save shelf.');
                            return false;
                        }
                    }
                    if (!response.ok || !result?.success) {
                        alert(result?.message || responseText || 'Failed to save shelf.');
                        return false;
                    }
                    return true;
                } catch (error) {
                    alert('Failed to save shelf: ' + error.message);
                    return false;
                }
            }

            function closeModal() {
                document.getElementById('modal-backdrop').classList.add('hidden');
                document.getElementById('modal-backdrop').classList.remove('flex');
            }

            function getProductRows() {
                return Array.from(document.querySelectorAll('.product-row')).map(row => {
                    const name = row.querySelector('.product-name').value.trim();
                    const sku = row.querySelector('.product-sku').value.trim();
                    const qty = parseInt(row.querySelector('.product-qty').value, 10) || 0;
                    const price = parseFloat(row.querySelector('.product-price').value) || 0;
                    return { sku, name, qty, price };
                }).filter(p => p.name && p.qty > 0);
            }

            function addProductRow(product = {}, insertAt) {
                const container = document.getElementById('modal-product-rows');
                if (container.querySelectorAll('.product-row').length >= PRODUCTS_PER_SHELF) {
                    return;
                }
                const row = document.createElement('div');
                row.className = 'product-row product-row-card';
                row.innerHTML = `
                    <div class="row-grid grid gap-4 md:grid-cols-[1.8fr_1fr_0.9fr_0.9fr_0.35fr] items-end">
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Product</label>
                            <input type="text" class="product-name mt-1 block w-full px-4 py-3 text-sm" value="${product.name || ''}" placeholder="Product name" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">SKU</label>
                            <input type="text" class="product-sku mt-1 block w-full px-4 py-3 text-sm" value="${product.sku || ''}" placeholder="SKU" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Qty</label>
                            <input type="number" min="0" class="product-qty mt-1 block w-full px-4 py-3 text-sm" value="${product.qty || ''}" placeholder="Qty" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Price</label>
                            <input type="number" step="0.01" min="0" class="product-price mt-1 block w-full px-4 py-3 text-sm" value="${product.price || ''}" placeholder="Price" />
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="button" class="remove-product-row text-sm font-semibold">Remove</button>
                        </div>
                    </div>
                `;

                // insert at position if provided
                const children = container.querySelectorAll('.product-row');
                if (typeof insertAt === 'number' && insertAt >= 0 && insertAt < children.length) {
                    container.insertBefore(row, children[insertAt]);
                } else {
                    container.appendChild(row);
                }

                const skuInput = row.querySelector('.product-sku');
                const priceInput = row.querySelector('.product-price');
                const nameInput = row.querySelector('.product-name');

                // SKU autofill: generate SKU from product name as `KCC_<sanitized>`.
                const generateSku = (n) => `KCC_${(n || '').replace(/[^A-Za-z0-9\-\+]/g, '')}`;
                let lastAutoSku = '';
                if (product.sku) {
                    skuInput.value = product.sku || '';
                    lastAutoSku = skuInput.value;
                } else {
                    skuInput.value = '';
                }
                priceInput.value = product.price || '';

                nameInput.addEventListener('input', () => {
                    const nameVal = nameInput.value.trim();
                    const gen = generateSku(nameVal);
                    // only auto-overwrite if SKU was empty or matches previously auto-generated SKU
                    if (!skuInput.value || skuInput.value === lastAutoSku) {
                        skuInput.value = gen;
                        lastAutoSku = gen;
                    }
                });

                // also fill on blur if sku still empty
                nameInput.addEventListener('blur', () => {
                    if (!skuInput.value) {
                        skuInput.value = generateSku(nameInput.value.trim());
                    }
                });

                row.querySelector('.remove-product-row').addEventListener('click', () => {
                    // Allow removing product rows from the modal with confirmation and offer Undo
                    const container = document.getElementById('modal-product-rows');
                    const confirmRemove = confirm('Remove this product row? This will remove it from the shelf when you save.');
                    if (!confirmRemove) return;
                    const productData = (function() {
                        const select = row.querySelector('.product-select');
                        const sku = row.querySelector('.product-sku').value || '';
                        const name = select.selectedOptions[0]?.dataset.name || '';
                        const qty = parseInt(row.querySelector('.product-qty').value, 10) || 0;
                        const price = parseFloat(row.querySelector('.product-price').value) || 0;
                        const product_id = select.value ? parseInt(select.value, 10) : null;
                        return { product_id, sku, name, qty, price };
                    })();
                    const index = Array.prototype.indexOf.call(container.querySelectorAll('.product-row'), row);
                    row.remove();
                    updateAddRowButtonState();
                    showToast('Product row removed.', 'success', {
                        undo: {
                            label: 'Undo',
                            callback: function() {
                                addProductRow(productData, index);
                                showToast('Product row restored.', 'success');
                            }
                        }
                    });
                });
                updateAddRowButtonState();
            }

            function updateAddRowButtonState() {
                const container = document.getElementById('modal-product-rows');
                const addButton = document.getElementById('modal-add-product-row');
                const rowCount = container.querySelectorAll('.product-row').length;
                addButton.disabled = rowCount >= PRODUCTS_PER_SHELF;
                addButton.classList.toggle('opacity-40', addButton.disabled);
            }

            function resetProductRows(products = []) {
                const container = document.getElementById('modal-product-rows');
                container.innerHTML = '';
                const rows = products.slice(0, PRODUCTS_PER_SHELF);
                if (rows.length) {
                    rows.forEach(addProductRow);
                } else {
                    for (let i = 0; i < 3; i += 1) {
                        addProductRow();
                    }
                }
                updateAddRowButtonState();
            }

            document.getElementById('modal-add-product-row').addEventListener('click', function() {
                addProductRow();
            });

            async function archiveShelf(warehouseIndex, slot) {
                if (Number.isNaN(warehouseIndex)) return;
                const warehouse = warehouses[warehouseIndex];
                const shelf = findShelfBySlotIndex(warehouse, slot);
                if (!shelf) {
                    closeModal();
                    return;
                }
                shelf.archived = true;
                const saved = await saveShelfData({
                    warehouse_id: warehouses[warehouseIndex].id,
                    slot_index: slot,
                    name: shelf.name,
                    products: shelf.products || [],
                    archived: true,
                });
                if (!saved) {
                    return;
                }

                warehouse.locations = warehouse.locations || [];
                warehouse.archivedShelves = warehouse.archivedShelves || [];
                const shelfIndex = warehouse.locations.findIndex(item => item && item.slot_index === slot);
                if (shelfIndex !== -1) {
                    warehouse.locations.splice(shelfIndex, 1);
                }
                warehouse.archivedShelves.push(shelf);
                warehouse.archivedShelves.sort((a, b) => (a.slot_index || 0) - (b.slot_index || 0));

                renderWarehousePage(warehouses[warehouseIndex].id, warehousePage[warehouseIndex]);
                updateWarehouseStats(warehouseIndex);
                closeModal();
                showToast(`${shelf.name || 'Shelf '+(slot+1)} archived from ${warehouse.name}.`, 'success');
            }

            function deleteShelf(warehouseIndex, slot) {
                if (Number.isNaN(warehouseIndex)) return;

                if (confirm('Are you sure you want to delete this shelf? All products will be removed.')) {
                    const warehouse = warehouses[warehouseIndex];
                    const shelfIndex = warehouse.locations.findIndex(shelf => shelf && shelf.slot_index === slot);
                    if (shelfIndex !== -1) {
                        warehouse.locations.splice(shelfIndex, 1);
                        renderWarehousePage(warehouses[warehouseIndex].id, warehousePage[warehouseIndex]);
                        updateWarehouseStats(warehouseIndex);
                    }
                }
                closeModal();
            }

            async function handleModalSave(event) {
                event.preventDefault();
                const modalWarehouseSelect = document.getElementById('modal-warehouse-select');
                const warehouseIndex = modalWarehouseSelect
                    ? parseInt(modalWarehouseSelect.value, 10)
                    : parseInt(document.getElementById('modal-warehouse-index').value, 10);
                const slotIndex = parseInt(document.getElementById('modal-slot').value, 10);
                const mode = document.getElementById('modal-mode').value;
                const shelfName = document.getElementById('modal-shelf-name').value.trim() || `New Shelf ${slotIndex + 1}`;
                const rows = getProductRows().slice(0, PRODUCTS_PER_SHELF);

                if (!Number.isNaN(warehouseIndex) && warehouses[warehouseIndex]) {
                    if (mode === 'editShelf') {
                        const existingShelf = findShelfBySlotIndex(warehouses[warehouseIndex], slotIndex);
                        if (existingShelf) {
                            existingShelf.name = shelfName;
                            existingShelf.products = rows.map(row => ({
                                product_id: row.product_id,
                                sku: row.sku,
                                name: row.name,
                                qty: row.qty,
                                price: row.price,
                            }));

                            for (const row of rows) {
                                if (!row.product_id || row.qty <= 0) continue;
                                await saveStock(row.product_id, row.qty, row.price, `Updated in shelf ${existingShelf.name}`);
                            }
                            const saved = await saveShelfData({
                                warehouse_id: warehouses[warehouseIndex].id,
                                slot_index: slotIndex,
                                name: existingShelf.name,
                                products: existingShelf.products,
                                archived: existingShelf.archived || false,
                            });
                            if (!saved) {
                                return;
                            }
                            renderWarehousePage(warehouses[warehouseIndex].id, warehousePage[warehouseIndex]);
                            closeModal();
                            showToast(`Shelf "${shelfName}" updated successfully`, 'success');
                            return;
                        }
                    }

                    if (mode === 'addProduct') {
                        const existingShelf = findShelfBySlotIndex(warehouses[warehouseIndex], slotIndex);
                        if (existingShelf) {
                            existingShelf.products = rows.map(row => ({
                                product_id: row.product_id,
                                sku: row.sku,
                                name: row.name,
                                qty: row.qty,
                                price: row.price,
                            }));

                            for (const row of rows) {
                                if (!row.product_id || row.qty <= 0) continue;
                                await saveStock(row.product_id, row.qty, row.price, `Added to shelf ${existingShelf.name}`);
                            }
                            const saved = await saveShelfData({
                                warehouse_id: warehouses[warehouseIndex].id,
                                slot_index: slotIndex,
                                name: existingShelf.name,
                                products: existingShelf.products,
                                archived: existingShelf.archived || false,
                            });
                            if (!saved) {
                                return;
                            }
                            renderWarehousePage(warehouses[warehouseIndex].id, warehousePage[warehouseIndex]);
                            closeModal();
                            return;
                        }
                    }

                    for (const row of rows) {
                        if (!row.product_id || row.qty <= 0) continue;
                        const saved = await saveStock(row.product_id, row.qty, row.price, `Added to ${shelfName}`);
                        if (!saved) {
                            return;
                        }
                    }

                    const newShelf = {
                        name: shelfName,
                        slot_index: slotIndex,
                        layout: { x: 1, y: 1, w: 3, h: 2 },
                        products: rows.map(row => ({
                            product_id: row.product_id,
                            sku: row.sku,
                            name: row.name,
                            qty: row.qty,
                            price: row.price,
                        })),
                    };

                    const warehouse = warehouses[warehouseIndex];
                    warehouse.locations.push(newShelf);
                    // ensure duplicates are removed (prefer shelves with products)
                    dedupeWarehouseLocations(warehouse);

                    const shelfSaved = await saveShelfData({
                        warehouse_id: warehouses[warehouseIndex].id,
                        slot_index: slotIndex,
                        name: newShelf.name,
                        products: newShelf.products,
                        archived: false,
                    });
                    if (!shelfSaved) {
                        return;
                    }

                    goToLastWarehousePage(warehouseIndex);
                    renderWarehousePage(warehouses[warehouseIndex].id, warehousePage[warehouseIndex]);
                    showToast(`Shelf ${warehouse.locations.length} added for warehouse ${warehouse.name}.`, 'success');
                }

                closeModal();
            }
        });
    </script>

    <div id="modal-backdrop" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50 px-4 py-8">
        <div class="modal-panel p-6 max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 id="modal-title" class="text-2xl font-semibold text-slate-900">Modal Title</h2>
                <button id="modal-close" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition hover:bg-slate-200">✕</button>
            </div>

            <form id="modal-form" class="space-y-6">
                <input type="hidden" id="modal-warehouse-index" />
                <input type="hidden" id="modal-slot" />
                <input type="hidden" id="modal-mode" value="addShelf" />

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_1fr]">
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Warehouse</label>
                            <select id="modal-warehouse-select" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900">
                                @foreach($warehouses as $index => $wh)
                                    <option value="{{ $index }}">{{ $wh['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Shelf Name</label>
                            <input id="modal-shelf-name" type="text" class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300" placeholder="Enter shelf name" />
                        </div>
                    </div>

                    <div class="modal-field p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Products (Max 10)</p>
                                <p class="text-xs text-slate-500 mt-1">Choose existing inventory items, quantity, and price before saving.</p>
                            </div>
                            <button type="button" id="modal-add-product-row" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add product</button>
                        </div>
                        <div id="modal-product-rows" class="grid gap-3 max-h-[540px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="modal-actions flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" id="modal-cancel" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Save shelf</button>
                </div>
            </form>
        </div>
    </div>

    <div id="archived-backdrop" class="fixed inset-0 bg-black/30 hidden items-center justify-center z-50 px-4 py-8">
        <div class="modal-panel p-6 max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Archived Shelves</h3>
                <button id="archived-close" class="text-slate-600">✕</button>
            </div>
            <div id="archived-list" class="space-y-2 max-h-64 overflow-y-auto border rounded-md p-3 bg-surface"></div>

            <div class="archived-pagination mt-4 flex items-center justify-center gap-3">
                <button id="archived-prev" class="px-3 py-1 rounded-md border text-sm">Previous</button>
                <div id="archived-page-info" class="text-sm text-slate-600">Page 1 of 1</div>
                <button id="archived-next" class="px-3 py-1 rounded-md border text-sm">Next</button>
            </div>

            <div class="mt-4 text-right">
                <button id="archived-done" class="px-4 py-2 rounded-md bg-slate-100">Close</button>
            </div>
        </div>
    </div>

        <div class="mt-4 text-sm text-gray-500">
            <strong class="text-slate-800">Note:</strong> This page is now backed by live inventory data from the system. Shelves display actual products and quantities where available.
        </div>
    </div>

    <!-- QR Code Generation Modal -->
    <div id="wm-qr-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="bg-[#105f68] px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Generate QR Codes</h2>
                    </div>
                    <button onclick="document.getElementById('wm-qr-modal').style.display='none'" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Product Rows -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">New Stock Products</p>
                            <p class="text-xs text-slate-500 mt-1">Enter product names to generate QR codes for multiple items at once.</p>
                        </div>
                        <button type="button" onclick="wmAddProductRow()" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add Product</button>
                    </div>
                    <div id="wm-product-rows" class="grid gap-3 max-h-[300px] overflow-y-auto"></div>
                </div>

                <!-- Restock Date Display (Fixed to today) -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                    <label class="block text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Restock Date (Today)
                    </label>
                    <input type="text" id="wm-qr-restock-date" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-slate-50 text-sm transition" readonly>
                </div>

                <!-- QR Code Preview -->
                <div id="wm-qr-preview" class="space-y-4">
                    <div id="wm-qr-loading" class="hidden text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div>
                        <p class="text-sm text-slate-600">Generating QR codes...</p>
                    </div>
                    <!-- QR codes will be generated here -->
                </div>

                <!-- Pagination -->
                <div id="wm-qr-pagination" class="hidden flex items-center justify-between pt-4 border-t border-slate-200">
                    <button onclick="wmPrevPage()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-prev-page">Previous</button>
                    <span id="wm-page-info" class="text-sm text-slate-600">Page 1 of 1</span>
                    <button onclick="wmNextPage()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-next-page">Next</button>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button onclick="wmGenerateAllQR()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md">
                        Generate All QR Codes
                    </button>
                    <button onclick="wmPrintAllQR()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md">
                        Print All QR Codes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Stock Details Modal -->
    <div id="wm-new-stock-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
            <div class="bg-[#105f68] px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">New Stock Details</h2>
                    </div>
                    <button onclick="wmCloseNewStockModal()" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <p class="text-sm text-slate-600"><strong>Product Name:</strong> <span id="wm-ns-product-name">-</span></p>
                    <p class="text-sm text-slate-600"><strong>SKU:</strong> <span id="wm-ns-sku">-</span></p>
                    <p class="text-sm text-slate-600"><strong>Restock Date:</strong> <span id="wm-ns-restock-date">-</span></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Price</label>
                    <input type="number" id="wm-ns-price" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" placeholder="Enter price" step="0.01">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity</label>
                    <input type="number" id="wm-ns-quantity" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" placeholder="Enter quantity" min="1">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                    <select id="wm-ns-category" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                        <option value="">Select category</option>
                        <option value="Parts">Parts</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Oil">Oil</option>
                        <option value="Tires">Tires</option>
                        <option value="Battery">Battery</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description (Optional)</label>
                    <textarea id="wm-ns-description" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" rows="2" placeholder="Enter description"></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button onclick="wmCloseNewStockModal()" class="flex-1 px-4 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                    <button onclick="wmSaveNewStock()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md">Save & Add to Warehouse</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Scanner Modal -->
    <div id="wm-scan-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4">
            <div class="bg-[#105f68] px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">QR Code Scanner</h2>
                    </div>
                    <button onclick="wmCloseScanner()" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-col md:flex-row p-6 gap-6">
                <!-- Scanner Section -->
                <div class="flex-1">
                    <div id="wm-scanner-reader" class="w-full bg-black rounded-xl overflow-hidden min-h-[400px]"></div>
                    <div id="wm-scanner-status" class="text-center text-sm text-slate-600 mt-2">Position QR code within the frame</div>
                </div>

                <!-- Scanned Items Section -->
                <div class="w-full md:w-80">
                    <div id="wm-scanned-items" class="hidden">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Scanned Items (<span id="wm-scan-count">0</span>)</h3>
                            <button onclick="wmClearScannedItems()" class="text-xs text-red-500 hover:text-red-700">Clear All</button>
                        </div>
                        <div id="wm-scan-list" class="max-h-48 overflow-y-auto space-y-2"></div>

                        <!-- Pagination -->
                        <div id="wm-scan-pagination" class="hidden flex items-center justify-between mt-3 pt-3 border-t border-slate-200">
                            <button onclick="wmScanPrevPage()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-scan-prev-page">Previous</button>
                            <span id="wm-scan-page-info" class="text-xs text-slate-600">Page 1 of 1</span>
                            <button onclick="wmScanNextPage()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-scan-next-page">Next</button>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button onclick="wmCloseScanner()" class="flex-1 px-4 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                        <button onclick="wmProceedToDetails()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md" id="wm-proceed-btn" disabled>Proceed to Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Add Warehouse Modal -->
    <div id="add-warehouse-modal" class="fixed inset-0 bg-black/50 z-[100000002] hidden flex items-center justify-center">
        <div class="modal-panel p-8 max-w-md">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Add New Warehouse</h2>
            <form id="add-warehouse-form" class="space-y-4">
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Warehouse Name</label>
                    <input type="text" id="warehouse-name" name="name" required class="w-full px-4 py-3" placeholder="Enter warehouse name">
                </div>
                <div class="modal-actions flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel-add-warehouse" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Create Warehouse</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Shelf Modal -->
    <div id="transfer-shelf-modal" class="fixed inset-0 bg-black/50 z-[100000002] hidden flex items-center justify-center">
        <div class="modal-panel p-8 max-w-md">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Transfer Shelf</h2>
            <form id="transfer-shelf-form" class="space-y-4">
                <input type="hidden" id="transfer-source-warehouse-id">
                <input type="hidden" id="transfer-slot-index">
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Current Shelf</label>
                    <input type="text" id="transfer-current-shelf" class="w-full px-4 py-3" readonly>
                </div>
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Destination Warehouse</label>
                    <select id="transfer-destination-warehouse" class="w-full px-4 py-3" required>
                        <option value="">Select destination warehouse</option>
                    </select>
                </div>
                <div class="modal-actions flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel-transfer-shelf" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Transfer Shelf</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Stock Arrival Modal -->
    <div id="assign-arrival-modal" class="fixed inset-0 bg-black/50 z-[100000003] hidden items-center justify-center">
        <div class="modal-panel p-8 max-w-md w-full">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900">Assign New Stock to Warehouse</h2>
                <button type="button" id="close-assign-arrival" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200">✕</button>
            </div>

            <div id="assign-arrival-info" class="mb-5 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm">
                <p class="font-semibold text-amber-900" id="assign-arrival-product-name">—</p>
                <p class="text-amber-700 mt-0.5">Qty: <span id="assign-arrival-qty" class="font-semibold">—</span></p>
            </div>

            <form id="assign-arrival-form" class="space-y-4">
                <input type="hidden" id="assign-arrival-id">

                <div class="modal-field p-4">
                    <label class="block text-sm font-semibold text-slate-800 mb-2">Destination Warehouse <span class="text-red-500">*</span></label>
                    <select id="assign-arrival-warehouse" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900" required>
                        <option value="">Select warehouse</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh['id'] }}">{{ $wh['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-field p-4">
                    <label class="block text-sm font-semibold text-slate-800 mb-2">Date Assigned</label>
                    <input type="date" id="assign-arrival-date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900">
                </div>

                <div class="modal-field p-4">
                    <label class="block text-sm font-semibold text-slate-800 mb-2">Note <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea id="assign-arrival-note" rows="3" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900" placeholder="e.g. Placed in Shelf A-2 area..."></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-assign-arrival" class="modal-footer-button secondary flex-1">Cancel</button>
                    <button type="submit" class="modal-footer-button primary flex-1">Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Collapsible toggle for Pending Arrivals panel
        (function () {
            const toggleBtn = document.getElementById('pending-arrivals-toggle');
            const body = document.getElementById('pending-arrivals-body');
            const chevron = document.getElementById('pending-arrivals-chevron');
            if (!toggleBtn || !body) return;

            toggleBtn.addEventListener('click', function () {
                const isOpen = !body.classList.contains('hidden');
                body.classList.toggle('hidden', isOpen);
                chevron.classList.toggle('rotate-180', !isOpen);
                toggleBtn.setAttribute('aria-expanded', String(!isOpen));
            });
        })();
    </script>

    <script>
        // Assign Stock Arrival logic
        (function () {
            const modal = document.getElementById('assign-arrival-modal');
            const form = document.getElementById('assign-arrival-form');
            const dateInput = document.getElementById('assign-arrival-date');

            // Default date to today
            if (dateInput) {
                dateInput.value = new Date().toISOString().split('T')[0];
            }

            function openModal(id, name, qty) {
                document.getElementById('assign-arrival-id').value = id;
                document.getElementById('assign-arrival-product-name').textContent = name;
                document.getElementById('assign-arrival-qty').textContent = qty;
                document.getElementById('assign-arrival-warehouse').value = '';
                document.getElementById('assign-arrival-note').value = '';
                if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.getElementById('close-assign-arrival').addEventListener('click', closeModal);
            document.getElementById('cancel-assign-arrival').addEventListener('click', closeModal);

            document.querySelectorAll('.assign-arrival-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    openModal(this.dataset.id, this.dataset.name, this.dataset.qty);
                });
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const id = document.getElementById('assign-arrival-id').value;
                const warehouseId = document.getElementById('assign-arrival-warehouse').value;
                const note = document.getElementById('assign-arrival-note').value.trim();
                const assignedDate = dateInput ? dateInput.value : '';

                if (!warehouseId) {
                    alert('Please select a destination warehouse.');
                    return;
                }

                const submitBtn = form.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving…';

                try {
                    const response = await fetch(`/warehouse-management/stock-arrival/${id}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            warehouse_id: parseInt(warehouseId, 10),
                            note: note || null,
                            assigned_date: assignedDate || null,
                        }),
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Remove row from table
                        const row = document.getElementById(`arrival-row-${id}`);
                        if (row) row.remove();

                        // Update badge count
                        const tbody = document.getElementById('pending-arrivals-tbody');
                        const remaining = tbody ? tbody.querySelectorAll('tr').length : 0;
                        const badge = document.getElementById('pending-arrivals-count');
                        if (badge) badge.textContent = `${remaining} pending`;

                        // Hide entire panel if no rows left
                        if (remaining === 0) {
                            const panel = document.getElementById('pending-arrivals-panel');
                            if (panel) panel.remove();
                        }

                        closeModal();

                        // Show toast via existing warehouse toast function (if available)
                        if (typeof showToast === 'function') {
                            showToast('Stock assigned to warehouse successfully!', 'success');
                        }
                    } else {
                        alert(result.message || 'Failed to assign stock. Please try again.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('An error occurred. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Confirm Assignment';
                }
            });
        })();
    </script>
</x-layouts.app>
