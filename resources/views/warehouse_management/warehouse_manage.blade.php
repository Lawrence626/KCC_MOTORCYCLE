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
        .warehouse-action-select, .action-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23253858%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 10px auto;
            padding-right: 2rem !important;
        }
        .wm-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .wm-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .wm-location { background: #f8fafc; border: 1px dashed rgba(15,118,110,0.16); }
        .product-chip { background: #ffffff; border: 1px solid rgba(16,185,129,0.18); color: var(--brand-dark); border-radius: 0.9rem; display:flex; flex-direction:column; gap:0; overflow:hidden; box-shadow: 0 2px 8px rgba(15,118,110,0.06); }
        .product-chip summary { list-style: none; outline: none; }
        .product-chip summary::-webkit-details-marker { display: none; }
        .product-chip[open] .details-arrow { transform: rotate(180deg); }
        .product-chip .chip-header { background: linear-gradient(90deg, #ecfdf5, #d1fae5); padding: 0.45rem 0.75rem; border-bottom: 1px solid rgba(16,185,129,0.14); }
        .product-chip .chip-body { padding: 0.5rem 0.75rem; display:flex; flex-direction:column; gap:0.18rem; }
        .product-chip .chip-row { display:flex; align-items:baseline; gap:0.3rem; font-size:0.72rem; }
        .product-chip .chip-label { color:#64748b; font-weight:600; min-width:4.8rem; flex-shrink:0; }
        .product-chip .chip-value { color:#0f172a; font-weight:500; word-break:break-word; }
        .product-chip .chip-value.sku { font-family: monospace; font-size:0.68rem; color:#0f766e; font-weight:700; }
        .product-chip .chip-value.price { color:#065f46; font-weight:700; }
        .product-chip .chip-value.qty { color:#1e40af; font-weight:700; }
        .product-chip .chip-desc { font-size:0.78rem; font-weight:700; color:#0f172a; }
        .warehouse-shelves { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: minmax(220px, auto); }
        .map-unit { min-height: 220px; }
        .modal-panel { width: min(100%, 960px); border-radius: 1.5rem; background: #ffffff; box-shadow: 0 28px 80px rgba(15,23,42,0.18); }
        .modal-field { border: 1px solid rgba(148,163,184,0.35); background: #f8fafc; border-radius: 10px; }
        .modal-field input,
        .modal-field select { border: none; background: transparent; outline: none; }
        .modal-field label { color: #334155; }
        .product-row-card { position: relative; background: #f8fafc; border: 1px solid rgba(148,163,184,0.2); border-radius: 1rem; padding: 0.85rem; }
        .product-row-card .row-grid { gap: 0.75rem; }
        .product-row-card .product-sku,
        .product-row-card .product-brand,
        .product-row-card .product-compatible,
        .product-row-card .product-qty,
        .product-row-card .product-price,
        .product-row-card .product-select { background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 10px; }
        .product-row-card .product-sku { background: #f1f5f9; }
        .product-row-card .product-name {
            background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 10px; padding: 0.75rem;
        }
        .product-row-card input:focus,
        .product-row-card select:focus,
        .modal-panel input:focus,
        .modal-panel select:focus,
        .modal-panel textarea:focus {
            outline: none !important;
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.35) !important;
        }
        .remove-product-row,
        .remove-product-row:hover,
        .remove-product-row:focus,
        .remove-product-row:active,
        .remove-product-row:visited {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }
        .modal-actions { border-top: 1px solid rgba(148,163,184,0.25); padding-top: 0.85rem; }
        .modal-footer-button { border-radius: 0.85rem; padding: 0.75rem 1.2rem; font-weight: 600; }
        .modal-footer-button.primary { background: var(--brand); color: #fff; }
        .modal-footer-button.secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); }
        .product-row-card label { font-size: 0.72rem; }
        .product-row-card .remove-product-row { font-size: 0.75rem; }
        .modal-panel { max-height: 95vh; overflow: auto; }
        #modal-product-rows { max-height: 560px; overflow-y: auto; }
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


    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Warehouse Management</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track and manage storage locations and products across your warehouses.</p>

            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button id="add-shelf-button" type="button" onclick="openAddShelfModal()"
                        class="inline-flex items-center gap-1.5 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap">
                    + Add Shelf
                </button>
                <button id="add-warehouse-button" type="button" onclick="openAddWarehouseModal()"
                        class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-900 bg-[#0f172a] px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap">
                    + Add Warehouse
                </button>
                <button id="view-archived-shelves" type="button" onclick="openArchivedShelvesModal()"
                        class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap">
                    Archived Shelves
                </button>
                <button id="view-archived-warehouses" type="button"
                        class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap">
                    Archived Warehouses
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

        {{-- â•â•â• FILTERS & STATS â•â•â• --}}
        <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center gap-2">
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    <div class="relative w-full sm:w-56">
                        <input id="wm-search" type="search" placeholder="Search product or SKU..."
                               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm h-9" />
                        <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="relative inline-block" id="dd-warehouse-wrapper">
                        <select id="warehouse-selector" class="hidden">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh['id'] }}">{{ $wh['name'] }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="toggleDropdown('dd-warehouse-menu', event)" class="h-9 px-3 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm cursor-pointer inline-flex items-center gap-2">
                            <span id="dd-warehouse-label">{{ $warehouses[0]['name'] ?? 'Select Warehouse' }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dd-warehouse-menu" class="hidden absolute left-0 top-full z-50 mt-1 min-w-[140px] rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                            @foreach($warehouses as $wh)
                                <button type="button" onclick="selectDropdownOption('warehouse-selector', 'dd-warehouse-label', 'dd-warehouse-menu', '{{ $wh['id'] }}', '{{ addslashes($wh['name']) }}')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">
                                    {{ $wh['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative inline-block" id="dd-desc-wrapper">
                        <select id="wm-product-description-filter" class="hidden">
                            <option value="">All Categories</option>
                        </select>
                        <button type="button" onclick="toggleDropdown('dd-desc-menu', event)" class="h-9 px-3 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm cursor-pointer inline-flex items-center gap-2">
                            <span id="dd-desc-label">All Categories</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dd-desc-menu" class="hidden absolute left-0 top-full z-50 mt-1 min-w-[160px] rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                            <button type="button" onclick="selectDropdownOption('wm-product-description-filter', 'dd-desc-label', 'dd-desc-menu', '', 'All Categories')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">All Categories</button>
                        </div>
                    </div>

                    <div class="relative inline-block" id="dd-brand-wrapper">
                        <select id="wm-brand-filter" class="hidden">
                            <option value="">All Brands</option>
                        </select>
                        <button type="button" onclick="toggleDropdown('dd-brand-menu', event)" class="h-9 px-3 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm cursor-pointer inline-flex items-center gap-2">
                            <span id="dd-brand-label">All Brands</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dd-brand-menu" class="hidden absolute left-0 top-full z-50 mt-1 min-w-[140px] rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                            <button type="button" onclick="selectDropdownOption('wm-brand-filter', 'dd-brand-label', 'dd-brand-menu', '', 'All Brands')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">All Brands</button>
                        </div>
                    </div>

                    <button id="wm-clear-filters" type="button" class="h-9 px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-[10px] transition cursor-pointer inline-flex items-center">
                        Clear
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div class="rounded-[12px] border border-slate-200 bg-white px-3.5 py-1.5 shadow-sm min-w-[90px]">
                        <p class="text-xs font-semibold text-slate-700">Products</p>
                        <p id="selectedWarehouseProducts" class="text-3xl font-bold text-slate-900">0</p>
                    </div>
                    <div class="rounded-[12px] border border-slate-200 bg-white px-3.5 py-1.5 shadow-sm min-w-[90px]">
                        <p class="text-xs font-semibold text-slate-700">Empty Slots</p>
                        <p id="selectedWarehouseEmptySlots" class="text-3xl font-bold text-slate-900">0</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="grid gap-4 mt-0">

        {{-- â”€â”€ Pending Warehouse Assignment panel (collapsible) â”€â”€ --}}
        <div id="pending-arrivals-panel" class="rounded-[20px] border border-slate-200 border-l-[5px] border-l-[#6EC1D1] shadow-sm overflow-hidden" style="background: linear-gradient(50deg, #ffffff 0%, rgba(110, 193, 209, 0.12) 50%);">
            {{-- Header / toggle bar --}}
            <button
                type="button"
                id="pending-arrivals-toggle"
                class="w-full flex items-center justify-between px-5 py-4 hover:bg-black/5 transition cursor-pointer"
                aria-expanded="{{ count($pendingArrivals) > 0 ? 'true' : 'false' }}"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#6EC1D1] text-black border border-slate-300/60 shadow-sm flex-shrink-0">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.707.293h3.172a1 1 0 01.707-.293l2.414-2.414a1 1 0 01.707-.293H20"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-slate-900">New Stock: Pending Warehouse Assignment</span>
                    <span id="pending-arrivals-count" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ count($pendingArrivals) > 0 ? 'bg-cyan-100 text-cyan-900 border border-cyan-200' : 'bg-slate-100 text-slate-500' }}">
                        {{ count($pendingArrivals) }} pending
                    </span>
                </div>
                <svg id="pending-arrivals-chevron" class="w-4 h-4 text-slate-500 transition-transform duration-200 {{ count($pendingArrivals) > 0 ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Collapsible body --}}
            <div id="pending-arrivals-body" class="{{ count($pendingArrivals) > 0 ? '' : 'hidden' }} px-5 pb-5">
                <p class="text-xs text-slate-600 mb-3 font-medium">Items that arrived from confirmed purchase orders are listed here. Assign each one to a warehouse before shelving.</p>

                @if(count($pendingArrivals) > 0)
                <div class="overflow-x-auto rounded-[14px] border border-slate-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-xs" id="pending-arrivals-table">
                        <thead class="bg-[#0f172a] border-b border-slate-200 text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-white">Product</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">SKU</th>
                                <th class="px-4 py-3 text-center font-semibold text-white">Qty</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">PO #</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">Supplier</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">Arrived</th>
                                <th class="px-4 py-3 text-center font-semibold text-white">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="pending-arrivals-tbody">
                            @foreach($pendingArrivals as $arrival)
                            <tr id="arrival-row-{{ $arrival['id'] }}" class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $arrival['product_name'] }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $arrival['sku'] ?? 'â€”' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-slate-800">{{ $arrival['quantity'] }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $arrival['purchase_order_number'] ?? 'â€”' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $arrival['supplier_name'] ?? 'â€”' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $arrival['arrived_at'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        class="assign-arrival-btn inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-3 py-1.5 text-xs font-semibold text-black hover:bg-[#59b2c2] transition shadow-sm cursor-pointer"
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
                <div class="flex flex-col items-center justify-center rounded-[14px] border border-dashed border-cyan-200/80 bg-white/80 py-8 text-center">
                    <svg class="w-8 h-8 text-cyan-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold text-slate-700">All caught up!</p>
                    <p class="text-xs text-slate-400 mt-1">New stocks from confirmed purchase orders will appear here.</p>
                </div>
                @endif
            </div>
        </div>
        {{-- â”€â”€ end Pending Warehouse Assignment panel â”€â”€ --}}

            @foreach($warehouses as $wh)
                <div class="rounded-[15px] border border-slate-200 bg-white overflow-hidden shadow-sm wh-card" data-id="{{ $wh['id'] }}" style="display:none;">
                    <div class="flex items-center justify-between bg-[#0f172a] px-5 py-3.5 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-[#6EC1D1] text-black font-bold text-sm shadow-sm">{{ strtoupper(substr($wh['code'], -1)) }}</div>
                            <div>
                                <div class="text-sm font-bold text-white tracking-wide">{{ $wh['name'] }}</div>
                                <div class="text-[11px] text-slate-300">Code: {{ $wh['code'] }}</div>
                            </div>
                        </div>
                        <div class="relative inline-block">
                            <select class="warehouse-action-select hidden" data-id="{{ $wh['id'] }}">
                                <option value="">Actions</option>
                                <option value="archive-warehouse">Archive Warehouse</option>
                            </select>
                            <button type="button" onclick="toggleDropdown('wh-actions-menu-{{ $wh['id'] }}', event)" class="px-3.5 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-700 bg-slate-800 text-white hover:bg-slate-700 cursor-pointer focus:outline-none focus:ring-1 focus:ring-[#6EC1D1] transition shadow-sm flex items-center gap-2">
                                <span>Actions</span>
                                <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="wh-actions-menu-{{ $wh['id'] }}" class="hidden absolute right-0 top-full z-50 mt-1.5 w-max min-w-[170px] rounded-[12px] border border-slate-700/80 bg-[#0f172a] shadow-2xl p-1.5 space-y-0.5 text-white">
                                <button type="button" onclick="triggerWarehouseAction('{{ $wh['id'] }}', 'archive-warehouse'); toggleDropdown('wh-actions-menu-{{ $wh['id'] }}', event);" class="w-full text-left px-3.5 py-2 rounded-[8px] text-xs font-semibold text-white hover:bg-slate-800 transition cursor-pointer flex items-center gap-2.5 whitespace-nowrap">
                                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    <span>Archive Warehouse</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="map-container border border-slate-200 rounded-[14px] p-3 bg-slate-50">
                            <div class="warehouse-shelves grid gap-4" data-id="{{ $wh['id'] }}"></div>
                            <div class="pagination mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-600 border-t border-slate-200/80 pt-3">
                                <div class="showing-info text-slate-500 font-medium text-xs" data-id="{{ $wh['id'] }}">Showing shelves</div>
                                <div class="flex items-center gap-1">
                                    <button type="button" class="prev-page rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition" data-id="{{ $wh['id'] }}">â† Prev</button>
                                    <div class="page-numbers flex items-center gap-1" data-id="{{ $wh['id'] }}"></div>
                                    <button type="button" class="next-page rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition" data-id="{{ $wh['id'] }}">Next â†’</button>
                                </div>
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

        function toggleModalWarehouseDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('modalWarehouseDropdownMenu');
            const wrapper = document.getElementById('modalWarehouseDropdownWrapper');
            if (menu) {
                const isOpening = menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                if (wrapper) wrapper.style.zIndex = isOpening ? '60' : '';
            }
        }

        function selectModalWarehouseOption(val, labelText) {
            const selectEl = document.getElementById('modal-warehouse-select');
            const labelSpan = document.getElementById('modalWarehouseSelectLabel');
            const menu = document.getElementById('modalWarehouseDropdownMenu');
            const wrapper = document.getElementById('modalWarehouseDropdownWrapper');
            if (selectEl) {
                selectEl.value = val;
                selectEl.dispatchEvent(new Event('change'));
            }
            if (labelSpan) labelSpan.textContent = labelText;
            if (menu) menu.classList.add('hidden');
            if (wrapper) wrapper.style.zIndex = '';
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('modalWarehouseDropdownMenu');
            const wrapper = document.getElementById('modalWarehouseDropdownWrapper');
            if (menu && wrapper && !wrapper.contains(e.target)) {
                menu.classList.add('hidden');
                wrapper.style.zIndex = '';
            }
        });

        function toggleDropdown(menuId, event) {
            if (event) event.stopPropagation();
            const targetMenu = document.getElementById(menuId);
            document.querySelectorAll('[id$="-menu"], .shelf-action-menu').forEach(menu => {
                if (menu !== targetMenu) menu.classList.add('hidden');
            });
            if (targetMenu) targetMenu.classList.toggle('hidden');
        }

        function selectDropdownOption(selectId, labelId, menuId, val, labelText) {
            const selectEl = document.getElementById(selectId);
            const labelSpan = document.getElementById(labelId);
            const menu = document.getElementById(menuId);
            if (selectEl) {
                selectEl.value = val;
                selectEl.dispatchEvent(new Event('change'));
            }
            if (labelSpan) labelSpan.textContent = labelText;
            if (menu) menu.classList.add('hidden');
        }

        function triggerWarehouseAction(warehouseId, actionVal) {
            const selectEl = document.querySelector(`.warehouse-action-select[data-id="${warehouseId}"]`);
            if (selectEl) {
                selectEl.value = actionVal;
                selectEl.dispatchEvent(new Event('change'));
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id$="-menu"], .shelf-action-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        let currentArchivedPage = 1;
        const archivedItemsPerPage = 5;

        function openArchivedShelvesModal() {
            const modal = document.getElementById('archived-backdrop');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                renderArchivedShelvesList();
            }
        }

        function closeArchivedShelvesModal() {
            const modal = document.getElementById('archived-backdrop');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function renderArchivedShelvesList() {
            const listEl = document.getElementById('archived-list');
            const pageInfo = document.getElementById('archived-page-info');
            const prevBtn = document.getElementById('archived-prev');
            const nextBtn = document.getElementById('archived-next');
            if (!listEl) return;

            const warehouses = window.WarehouseData?.warehouses || [];
            let allArchivedShelves = [];

            warehouses.forEach(wh => {
                if (Array.isArray(wh.archivedShelves)) {
                    wh.archivedShelves.forEach(shelf => {
                        allArchivedShelves.push({ ...shelf, warehouseName: wh.name, warehouseId: wh.id });
                    });
                }
                if (Array.isArray(wh.locations)) {
                    wh.locations.filter(loc => loc && loc.archived).forEach(shelf => {
                        allArchivedShelves.push({ ...shelf, warehouseName: wh.name, warehouseId: wh.id });
                    });
                }
            });

            if (allArchivedShelves.length === 0) {
                listEl.innerHTML = `
                    <div class="text-center py-8 text-slate-500 text-sm">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        No archived shelves found.
                    </div>
                `;
                if (pageInfo) pageInfo.textContent = 'Page 1 of 1';
                if (prevBtn) prevBtn.disabled = true;
                if (nextBtn) nextBtn.disabled = true;
                return;
            }

            const totalPages = Math.ceil(allArchivedShelves.length / archivedItemsPerPage);
            if (currentArchivedPage > totalPages) currentArchivedPage = totalPages;
            if (currentArchivedPage < 1) currentArchivedPage = 1;

            const startIdx = (currentArchivedPage - 1) * archivedItemsPerPage;
            const pageItems = allArchivedShelves.slice(startIdx, startIdx + archivedItemsPerPage);

            listEl.innerHTML = pageItems.map(item => `
                <div class="flex items-center justify-between p-3.5 bg-white rounded-[14px] border border-slate-200 shadow-sm">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">${item.name || 'Unnamed Shelf'}</p>
                        <p class="text-xs text-slate-500 mt-0.5">${item.warehouseName} â€¢ ${item.products ? item.products.length : 0} items</p>
                    </div>
                    <button type="button" onclick="restoreArchivedShelf('${item.slot_index}', '${item.warehouseId}')" class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">
                        Restore
                    </button>
                </div>
            `).join('');

            if (pageInfo) pageInfo.textContent = `Page ${currentArchivedPage} of ${totalPages}`;
            if (prevBtn) prevBtn.disabled = currentArchivedPage <= 1;
            if (nextBtn) nextBtn.disabled = currentArchivedPage >= totalPages;
        }

        function openAddShelfModal() {
            if (window.openShelfModalGlobal) {
                window.openShelfModalGlobal();
            } else {
                const modal = document.getElementById('modal-backdrop');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            }
        }

        function closeShelfModal() {
            const modal = document.getElementById('modal-backdrop');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openAddWarehouseModal() {
            const modal = document.getElementById('add-warehouse-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeAddWarehouseModal() {
            const modal = document.getElementById('add-warehouse-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                const form = document.getElementById('add-warehouse-form');
                if (form) form.reset();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const addWarehouseBtn = document.getElementById('add-warehouse-button');
            if (addWarehouseBtn) {
                addWarehouseBtn.addEventListener('click', openAddWarehouseModal);
            }
            const cancelWarehouseBtn = document.getElementById('cancel-add-warehouse');
            if (cancelWarehouseBtn) {
                cancelWarehouseBtn.addEventListener('click', closeAddWarehouseModal);
            }
            const addWarehouseForm = document.getElementById('add-warehouse-form');
            if (addWarehouseForm) {
                addWarehouseForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const nameInput = document.getElementById('warehouse-name');
                    const name = nameInput ? nameInput.value.trim() : '';
                    if (!name) return;

                    const submitBtn = addWarehouseForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Creatingâ€¦';
                    }

                    try {
                        const response = await fetch('{{ route("warehouse.management.add_warehouse") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ name: name })
                        });
                        const data = await response.json();
                        if (data.success || response.ok) {
                            closeAddWarehouseModal();
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to create warehouse.');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'Create Warehouse';
                            }
                        }
                    } catch (err) {
                        console.error('Error creating warehouse:', err);
                        window.location.reload();
                    }
                });
            }
        });
    </script>

    <div id="modal-backdrop" class="fixed inset-0 z-[100000002] hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeShelfModal()"></div>
        <div class="relative modal-panel w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)] flex flex-col">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 id="modal-title" class="text-xl font-bold text-black">Add Shelf</h2>
                    <p class="text-sm text-slate-900 font-medium">Configure shelf location, capacity, and products before saving.</p>
                </div>
                <button id="modal-close" type="button" onclick="closeShelfModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="modal-form" class="flex flex-col flex-1 overflow-hidden">
                <div class="px-6 py-6 overflow-y-auto space-y-5 flex-1">
                    <input type="hidden" id="modal-warehouse-index" />
                    <input type="hidden" id="modal-slot" />
                    <input type="hidden" id="modal-mode" value="addShelf" />

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_1fr]">
                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Warehouse</label>
                            <select id="modal-warehouse-select" class="hidden">
                                @foreach($warehouses as $index => $wh)
                                    <option value="{{ $index }}">{{ $wh['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="relative" id="modalWarehouseDropdownWrapper">
                                <button type="button" onclick="toggleModalWarehouseDropdown(event)" class="w-full rounded-[10px] border border-slate-300 bg-white px-4 py-3 text-left text-sm font-medium text-slate-900 flex items-center justify-between gap-2 hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm cursor-pointer">
                                    <span id="modalWarehouseSelectLabel">{{ $warehouses[0]['name'] ?? 'Select Warehouse' }}</span>
                                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div id="modalWarehouseDropdownMenu" class="hidden absolute right-0 left-auto top-full z-50 mt-1 w-[220px] rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                    @foreach($warehouses as $index => $wh)
                                        <button type="button" onclick="selectModalWarehouseOption('{{ $index }}', '{{ addslashes($wh['name']) }}')" class="w-full text-left px-3 py-2.5 rounded-[8px] text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">{{ $wh['name'] }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Shelf Name</label>
                            <input id="modal-shelf-name" type="text" class="block w-full rounded-[10px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" placeholder="Enter shelf name" />
                        </div>
                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white lg:col-span-2">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Shelf Capacity</label>
                            <input id="modal-shelf-capacity" type="number" min="1" value="10"
                                   class="block w-full rounded-[10px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm"
                                   placeholder="Enter shelf capacity" />
                            <p class="text-xs text-slate-500 mt-2 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6C8.8 12.16 8 10.66 8 9c0-2.21 1.79-4 4-4s4 1.79 4 4c0 1.66-.8 3.16-2.15 4.1z"/>
                                </svg>
                                <span>You can increase this to allow more products per shelf. Default is 10.</span>
                            </p>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">Products</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Choose existing inventory items, quantity, and price before saving.</p>
                            </div>
                            <button type="button" id="modal-add-product-row" onclick="if(window.addProductRowGlobal) window.addProductRowGlobal();" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">+ Add product</button>
                        </div>
                        <div id="modal-product-rows" class="grid gap-3 max-h-[540px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <button type="button" id="modal-cancel" onclick="closeShelfModal()" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Save shelf</button>
                </div>
            </form>
        </div>
    </div>

    <div id="archived-backdrop" class="fixed inset-0 z-[100000002] hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeArchivedShelvesModal()"></div>
        <div class="relative modal-panel w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)] flex flex-col">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Archived Shelves</h2>
                    <p class="text-sm text-slate-900 font-medium">View and restore archived shelf locations.</p>
                </div>
                <button id="archived-close" type="button" onclick="closeArchivedShelvesModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                <div id="archived-list" class="space-y-2 max-h-64 overflow-y-auto rounded-[20px] border border-slate-200 p-4 bg-slate-50"></div>

                <div class="archived-pagination flex items-center justify-center gap-3 pt-2">
                    <button id="archived-prev" type="button" onclick="if(currentArchivedPage>1){currentArchivedPage--;renderArchivedShelvesList();}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer disabled:opacity-50">Previous</button>
                    <div id="archived-page-info" class="text-xs font-semibold text-slate-600">Page 1 of 1</div>
                    <button id="archived-next" type="button" onclick="currentArchivedPage++;renderArchivedShelvesList();" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer disabled:opacity-50">Next</button>
                </div>
            </div>

            <div class="flex items-center justify-end px-6 py-4 border-t border-slate-200 bg-slate-50">
                <button id="archived-done" type="button" onclick="closeArchivedShelvesModal()" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Close</button>
            </div>
        </div>
    </div>

        <div class="mt-4 text-sm text-gray-500">
            <strong class="text-slate-800">Note:</strong> This page is now backed by live inventory data from the system. Shelves display actual products and quantities where available.
        </div>
    </div>

    <!-- QR Code Generation Modal -->
    <div id="wm-qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('wm-qr-modal').style.display='none'"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
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
    <div id="wm-new-stock-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="wmCloseNewStockModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
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
    <div id="wm-scan-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="wmCloseScanner()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4">
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
                <div class="flex-1 min-w-0">
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
    <div id="add-warehouse-modal" class="fixed inset-0 z-[100000002] hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('add-warehouse-modal').classList.add('hidden')"></div>
        <div class="relative modal-panel w-full max-w-md overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Add New Warehouse</h2>
                    <p class="text-sm text-slate-900 font-medium">Create a new storage location facility.</p>
                </div>
                <button type="button" onclick="document.getElementById('add-warehouse-modal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="add-warehouse-form" class="px-6 py-6 space-y-5">
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Warehouse Name</label>
                    <input type="text" id="warehouse-name" name="name" required class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" placeholder="Enter warehouse name">
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-add-warehouse" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Create Warehouse</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Shelf Modal -->
    <div id="transfer-shelf-modal" class="fixed inset-0 z-[100000002] hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('transfer-shelf-modal').classList.add('hidden');document.getElementById('transfer-shelf-modal').classList.remove('flex');"></div>
        <div class="relative modal-panel w-full max-w-md overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Transfer Shelf</h2>
                    <p class="text-sm text-slate-900 font-medium">Relocate shelf to another warehouse.</p>
                </div>
                <button type="button" onclick="document.getElementById('transfer-shelf-modal').classList.add('hidden');document.getElementById('transfer-shelf-modal').classList.remove('flex');" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="transfer-shelf-form" class="px-6 py-6 space-y-5">
                <input type="hidden" id="transfer-source-warehouse-id">
                <input type="hidden" id="transfer-slot-index">
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Current Shelf</label>
                    <input type="text" id="transfer-current-shelf" class="block w-full rounded-[12px] border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-900 focus:outline-none" readonly>
                </div>
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Destination Warehouse</label>
                    <select id="transfer-destination-warehouse" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" required>
                        <option value="">Select destination warehouse</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-transfer-shelf" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Transfer Shelf</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Stock Arrival Modal -->
    <div id="assign-arrival-modal" class="fixed inset-0 z-[100000003] hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('assign-arrival-modal').classList.add('hidden')"></div>
        <div class="relative modal-panel w-full max-w-md overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Assign Stock Arrival</h2>
                    <p class="text-sm text-slate-900 font-medium">Assign arrived inventory to destination warehouse.</p>
                </div>
                <button type="button" id="close-assign-arrival" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="assign-arrival-form" class="px-6 py-6 space-y-5">
                <input type="hidden" id="assign-arrival-id">

                <div id="assign-arrival-info" class="rounded-[20px] bg-amber-50 border border-amber-200 p-4 text-sm">
                    <p class="font-bold text-amber-900" id="assign-arrival-product-name">â€”</p>
                    <p class="text-amber-700 mt-1 font-medium">Qty: <span id="assign-arrival-qty" class="font-bold">â€”</span></p>
                </div>

                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Destination Warehouse <span class="text-red-500">*</span></label>
                    <select id="assign-arrival-warehouse" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" required>
                        <option value="">Select warehouse</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh['id'] }}">{{ $wh['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Date Assigned</label>
                    <input type="date" id="assign-arrival-date" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm">
                </div>

                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Note <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea id="assign-arrival-note" rows="3" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" placeholder="e.g. Placed in Shelf A-2 area..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-assign-arrival" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer flex-1">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer flex-1">Confirm Assignment</button>
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
                submitBtn.textContent = 'SavingÃ¢â‚¬Â¦';

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

        // Notification panel toggle
        window.toggleNotificationPanel = function(e) {
            if (e) e.stopPropagation();
            var panel = document.getElementById('notification-panel');
            if (!panel) return;

            // Close profile dropdown first if open
            var profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }

            const isOpen = !panel.classList.contains('hidden');
            if (isOpen) {
                panel.classList.add('hidden');
            } else {
                panel.classList.remove('hidden');
            }
        };

        // Mark all notifications as read
        window.markAllNotificationsRead = function() {
            // Implementation for marking notifications as read
            console.log('Mark all notifications as read');
        };

        // Open all notifications modal
        window.openAllNotificationsModal = function() {
            // Implementation for opening all notifications modal
            console.log('Open all notifications modal');
        };
    </script>

    <script src="{{ asset('js/warehouse_management.js') }}?v={{ time() }}"></script>
</x-layouts.app>

