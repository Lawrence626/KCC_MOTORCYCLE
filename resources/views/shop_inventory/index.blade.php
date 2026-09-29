<x-layouts.app :title="__('Shop Inventory Management')">
    <style>
        :root {
            --brand: #0f172a;
            --brand-soft: #e2e8f0;
            --brand-dark: #020617;
            --accent-cyan: #6EC1D1;
            --accent-cyan-hover: #59b2c2;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #64748b;
            --border: rgba(226, 232, 240, 0.8);
        }
        .si-badge { background: #0f172a; color: #fff; }
        .si-card { border: 1px solid border-slate-200; background: #ffffff; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1); }
        .product-chip { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
        .chip-header { background: #0f172a; padding: 0.35rem 0.6rem; border-bottom: 1px solid #1e293b; }
        .chip-desc { color: #ffffff; font-weight: 600; font-size: 0.75rem; line-height: 1rem; }
        .chip-body { padding: 0.4rem 0.6rem; display: flex; flex-direction: column; gap: 0.15rem; }
        .chip-row { display: flex; align-items: baseline; justify-content: space-between; gap: 0.25rem; font-size: 0.7rem; }
        .chip-label { color: #64748b; font-weight: 500; }
        .chip-value { color: #0f172a; font-weight: 600; }
        .chip-value.sku { font-family: monospace; color: #0891b2; font-weight: 700; font-size: 0.68rem; }
        .chip-value.price { color: #047857; font-weight: 700; }
        .chip-value.qty { color: #1d4ed8; font-weight: 700; }
        .shop-shelves { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .map-unit { min-height: 180px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 0.85rem; }
        .modal-panel { width: min(100%, 920px); border-radius: 20px; background: #ffffff; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .modal-field { border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 12px; }
        .modal-field input { border: none; background: transparent; outline: none; }
        .modal-field select { border: 1px solid #cbd5e1; background: #ffffff; outline: none; border-radius: 10px; }
        .modal-field label { color: #334155; }
        select option { background: #ffffff; color: #334155; padding: 6px 10px; }
        .product-row-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.75rem; }
        .product-row-card .row-grid { gap: 0.5rem; }
        .product-row-card .product-sku,
        .product-row-card .product-desc,
        .product-row-card .product-brand,
        .product-row-card .product-compatible,
        .product-row-card .product-qty,
        .product-row-card .product-price,
        .product-row-card .product-select { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.5rem 0.75rem; font-size: 0.75rem; }
        .product-row-card .product-sku { background: #f1f5f9; }
        .remove-product-row,
        .remove-product-row:hover,
        .remove-product-row:focus,
        .remove-product-row:active {
            color: #000000 !important;
            font-size: 0.75rem;
        }
        .modal-actions { border-top: 1px solid #e2e8f0; padding-top: 0.75rem; }
        .modal-footer-button { border-radius: 10px; padding: 0.4rem 0.9rem; font-size: 0.75rem; font-weight: 600; transition: all 0.15s ease; }
        .modal-footer-button.primary { background: #6EC1D1; color: #000; border: 1px solid rgba(110, 193, 209, 0.4); }
        .modal-footer-button.primary:hover { background: #59b2c2; }
        .modal-footer-button.secondary { background: #ffffff; color: #334155; border: 1px solid #cbd5e1; }
        .modal-footer-button.secondary:hover { background: #f8fafc; }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.75rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f172a; color: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(15,23,42,0.18); padding: 0.6rem 0.85rem; font-size: 0.8rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f172a; border-left: 4px solid #6EC1D1; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 0.9rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 1024px) { .shop-shelves { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 770px) {
            .shop-shelves { grid-template-columns: 1fr; }
            .product-row-card { padding: 0.65rem; }
            .map-unit { min-height: 180px; }
        }
    </style>


    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Shop Inventory Items</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track and manage products across shop shelves for POS sales.</p>

            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button id="add-shelf-button" type="button" class="inline-flex items-center gap-1.5 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap" onclick="openAddShelfModal()">
                    + Add Shelf
                </button>
                <button id="transfer-from-warehouse" type="button" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-900 bg-[#0f172a] px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transfer from Warehouse
                </button>
                <button id="view-history" type="button" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200 cursor-pointer whitespace-nowrap">
                    <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    History Logs
                </button>
                <a href="{{ route('shop.inventory.archived') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200 whitespace-nowrap">
                    <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archive List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Total Shop Products</p>
                        <div class="mt-1">
                            <p id="total-products" class="text-2xl font-bold text-black">{{ number_format($totalProducts ?? 0) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Items allocated across active shop shelves.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Total Shop Shelves</p>
                        <div class="mt-1">
                            <p id="total-shelves" class="text-2xl font-bold text-black">{{ number_format($totalShelves ?? 0) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Active retail display shelves in shop.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-2">
                <div class="relative w-full lg:w-64">
                    <input type="search" id="search-input" class="w-full pl-9 pr-3 py-1.5 text-xs rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" placeholder="Search product name, SKU, shelf...">
                    <svg class="absolute left-2.5 top-2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="flex flex-wrap items-center justify-end gap-2 w-full lg:w-auto">
                    <div class="relative inline-block" id="dd-desc-wrapper">
                        <select id="product-description-filter" class="hidden">
                            <option value="">All Categories</option>
                            @php
                                $descriptions = \App\Models\ProductDescription::where('is_active', true)->orderBy('name')->get();
                                foreach($descriptions as $desc):
                            @endphp
                                <option value="{{ $desc->name }}">{{ $desc->name }}</option>
                            @php endforeach; @endphp
                        </select>
                        <button type="button" onclick="toggleDropdown('dd-desc-menu', event)" class="px-3 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm cursor-pointer flex items-center gap-2">
                            <span id="dd-desc-label">All Categories</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dd-desc-menu" class="hidden absolute left-0 top-full z-50 mt-1 min-w-[160px] rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                            <button type="button" onclick="selectShopDropdownOption('product-description-filter', 'dd-desc-label', 'dd-desc-menu', '', 'All Descriptions')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">All Categories</button>
                            @foreach($descriptions as $desc)
                                <button type="button" onclick="selectShopDropdownOption('product-description-filter', 'dd-desc-label', 'dd-desc-menu', '{{ addslashes($desc->name) }}', '{{ addslashes($desc->name) }}')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">
                                    {{ $desc->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative inline-block" id="dd-brand-wrapper">
                        <select id="brand-filter" class="hidden">
                            <option value="">All Brands</option>
                            @php
                                $brands = \App\Models\Product::where('is_archived', false)
                                    ->whereNotNull('brand')
                                    ->where('brand', '!=', '')
                                    ->distinct()
                                    ->orderBy('brand')
                                    ->pluck('brand')
                                    ->toArray();
                                foreach($brands as $brand):
                            @endphp
                                <option value="{{ $brand }}">{{ $brand }}</option>
                            @php endforeach; @endphp
                        </select>
                        <button type="button" onclick="toggleDropdown('dd-brand-menu', event)" class="px-3 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm cursor-pointer flex items-center gap-2">
                            <span id="dd-brand-label">All Brands</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dd-brand-menu" class="hidden absolute left-0 top-full z-50 mt-1 min-w-[140px] rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                            <button type="button" onclick="selectShopDropdownOption('brand-filter', 'dd-brand-label', 'dd-brand-menu', '', 'All Brands')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">All Brands</button>
                            @foreach($brands as $brand)
                                <button type="button" onclick="selectShopDropdownOption('brand-filter', 'dd-brand-label', 'dd-brand-menu', '{{ addslashes($brand) }}', '{{ addslashes($brand) }}')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">
                                    {{ $brand }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative inline-block" id="dd-section-wrapper">
                        <select id="section-filter" class="hidden">
                            <option value="">All Sections</option>
                        </select>
                        <button type="button" onclick="toggleDropdown('dd-section-menu', event)" class="px-3 py-1.5 text-xs font-semibold rounded-[10px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm cursor-pointer flex items-center gap-2">
                            <span id="dd-section-label">All Sections</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="dd-section-menu" class="hidden absolute left-0 top-full z-50 mt-1 min-w-[140px] rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                            <button type="button" onclick="selectShopDropdownOption('section-filter', 'dd-section-label', 'dd-section-menu', '', 'All Sections')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">All Sections</button>
                        </div>
                    </div>

                    <button id="clear-search" onclick="document.getElementById('dd-desc-label').textContent='All Descriptions'; document.getElementById('dd-brand-label').textContent='All Brands'; document.getElementById('dd-section-label').textContent='All Sections';" class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-[10px] transition cursor-pointer">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 mt-2">
            @if($shelves->count() > 0)
                <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-[10px] bg-[#0f172a] text-white text-xs font-bold shadow-sm">S</div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Shop Inventory Display</h2>
                                <p class="text-[11px] text-slate-500">Active retail shelves & POS items</p>
                            </div>
                        </div>
                    </div>

                    <div class="map-container border border-slate-200 rounded-[14px] p-3 bg-slate-50">
                        <div class="shop-shelves grid gap-4" id="shop-shelves-grid">
                            @foreach($shelves as $shelf)
                            <div class="map-unit shelf-card">
                                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200/60">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-[8px] bg-[#6EC1D1] text-black text-xs font-bold shadow-sm">{{ strtoupper(substr($shelf->name, -1)) }}</div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-900 shelf-name">{{ $shelf->name }}</div>
                                            <div class="text-[10px] text-slate-500 shelf-location">{{ $shelf->location ?? 'No location' }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <select class="action-select text-xs text-slate-700 px-2.5 py-1 border border-slate-300 rounded-[10px] bg-white hover:bg-slate-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#6EC1D1]" onchange="handleShelfAction(this, {{ $shelf->id }})">
                                            <option value="">Actions</option>
                                            <option value="edit-shelf">Edit Shelf</option>
                                            <option value="transfer-products">Transfer Products</option>
                                            <option value="return-to-warehouse">Return to Warehouse</option>
                                            <option value="archive-shelf">Archive Shelf</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    @if($shelf->shop_inventory && $shelf->shop_inventory->count() > 0)
                                        @php
                                            $productsToShow = $shelf->shop_inventory->take(10);
                                            $remainingProducts = $shelf->shop_inventory->count() - 10;
                                        @endphp
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($productsToShow as $item)
                                                @php
                                                    $p   = $item->product;
                                                    $cat = $p?->productCatalog;
                                                    $desc       = $cat?->product_description ?? $p?->description ?? $p?->name ?? 'Unknown';
                                                    $brand      = $cat?->brand               ?? $p?->brand       ?? '—';
                                                    $compatible = $cat?->product_name        ?? $p?->compatibility ?? '—';
                                                    $sku        = $cat?->sku                 ?? $p?->sku          ?? '—';
                                                    $price      = $p?->unit_price ?? 0;
                                                    $qty        = $item->quantity;
                                                    $priceFormatted = '₱' . number_format($price, 2);
                                                    $expiry     = $p?->expiry_date ? \Carbon\Carbon::parse($p->expiry_date)->format('M d, Y') : 'N/A';
                                                    $expiryColor = $p?->expiry_status === 'expired' ? 'text-red-600 font-bold' : ($p?->expiry_status === 'expiring' ? 'text-amber-600 font-semibold' : 'text-slate-700');
                                                @endphp
                                                <div class="product-chip">
                                                    <div class="chip-header">
                                                        <div class="chip-desc truncate" title="{{ $desc }}">{{ $desc }}</div>
                                                    </div>
                                                    <div class="chip-body">
                                                        <div class="chip-row"><span class="chip-label">Brand:</span><span class="chip-value truncate">{{ $brand }}</span></div>
                                                        <div class="chip-row"><span class="chip-label">Compat:</span><span class="chip-value truncate">{{ $compatible }}</span></div>
                                                        <div class="chip-row"><span class="chip-label">SKU:</span><span class="chip-value sku">{{ $sku }}</span></div>
                                                        <div class="chip-row"><span class="chip-label">Price:</span><span class="chip-value price">{{ $priceFormatted }}</span></div>
                                                        <div class="chip-row"><span class="chip-label">Qty:</span><span class="chip-value qty">{{ $qty }}</span></div>
                                                        <div class="chip-row"><span class="chip-label">Expiry:</span><span class="chip-value {{ $expiryColor }}">{{ $expiry }}</span></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($remainingProducts > 0)
                                        <p class="text-[11px] text-slate-500 font-medium mt-1.5 text-right">+{{ $remainingProducts }} more products</p>
                                        @endif
                                    @else
                                        <div class="py-6 text-center text-xs text-slate-400 bg-white/50 rounded-xl border border-dashed border-slate-200">No products on this shelf</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination Controls -->
                        <div id="pagination-controls" class="flex items-center justify-between border-t border-slate-200 bg-white px-3 py-2 text-xs rounded-xl mt-3 hidden">
                            <p class="text-slate-600">Page <span id="current-page" class="font-semibold text-slate-900">1</span> of <span id="total-pages" class="font-semibold text-slate-900">1</span></p>
                            <div class="flex gap-1">
                                <button type="button" id="prev-page" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">← Prev</button>
                                <button type="button" id="next-page" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div id="no-shelves-message" class="rounded-[20px] border border-slate-200 bg-white py-12 text-center shadow-sm">
                    <div class="text-slate-400 text-sm font-semibold mb-1">No shop shelves found</div>
                    <div class="text-slate-500 text-xs">Transfer products from warehouse to create shelves</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Transfer from Warehouse Modal -->
    <div id="transfer-warehouse-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeTransferWarehouseModal()"></div>
        <div class="relative modal-panel w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Transfer from Warehouse to Shop</h2>
                    <p class="text-sm text-slate-900 font-medium">Select a warehouse, shelf, and product to transfer items into shop shelves.</p>
                </div>
                <button type="button" onclick="closeTransferWarehouseModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="transfer-warehouse-form" class="px-6 py-6 space-y-5">
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Destination Shop Shelf</label>
                    <select name="shop_shelf_id" required class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm">
                        <option value="">Select a shelf</option>
                    </select>
                </div>

                <div class="rounded-[28px] border border-slate-200 p-4 bg-white space-y-3">
                    <label class="block text-sm font-semibold text-slate-900">Select Warehouse & Products</label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">1. Warehouse</label>
                            <select id="warehouse-select" class="block w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm">
                                <option value="">Select warehouse</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">2. Warehouse Shelf</label>
                            <select id="warehouse-shelf-select" class="block w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm" disabled>
                                <option value="">Select shelf</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2 items-end">
                        <div class="sm:col-span-7">
                            <label class="block text-xs font-medium text-slate-600 mb-1">3. Product</label>
                            <select id="warehouse-product-select" class="block w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm" disabled>
                                <option value="">Select product</option>
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Quantity</label>
                            <input type="number" id="transfer-quantity" class="block w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm" min="1" value="1" disabled>
                        </div>
                        <div class="sm:col-span-2">
                            <button type="button" id="add-to-transfer" class="w-full rounded-[10px] bg-[#0f172a] px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer" disabled>
                                + Add
                            </button>
                        </div>
                    </div>

                    <!-- Selected Products List -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <label class="block text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wider">Items to Transfer:</label>
                        <div id="selected-products-list" class="space-y-2 max-h-48 overflow-y-auto"></div>
                        <p id="no-products-selected" class="text-slate-400 text-xs text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">No products added yet. Select a warehouse, shelf, product and click "+ Add".</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-transfer-warehouse" onclick="closeTransferWarehouseModal()" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" id="submit-transfer-warehouse-btn" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer flex items-center gap-2">
                        <span>Transfer Products</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Between Shelves Modal -->
    <div id="transfer-shelves-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('transfer-shelves-modal').classList.add('hidden')"></div>
        <div class="relative modal-panel w-full max-w-lg overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Transfer Between Shop Shelves</h2>
                    <p class="text-sm text-slate-900 font-medium">Move products between active shop shelf locations.</p>
                </div>
                <button type="button" onclick="document.getElementById('transfer-shelves-modal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="transfer-shelves-form" class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Source Shelf</label>
                        <select name="source_shelf_id" required class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm">
                            <option value="">Select source shelf</option>
                        </select>
                    </div>
                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Destination Shelf</label>
                        <select name="destination_shelf_id" required class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm">
                            <option value="">Select destination shelf</option>
                        </select>
                    </div>
                </div>
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white space-y-3">
                    <label class="block text-sm font-semibold text-slate-900">Products to Transfer</label>
                    <div id="source-shelf-products-container" class="space-y-2 max-h-60 overflow-y-auto">
                        <!-- Source shelf products will be loaded here -->
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-transfer-shelves" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Transfer Products</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return to Warehouse Modal -->
    <div id="return-warehouse-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('return-warehouse-modal').classList.add('hidden')"></div>
        <div class="relative modal-panel w-full max-w-lg overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Return Products to Warehouse</h2>
                    <p class="text-sm text-slate-900 font-medium">Transfer inventory back from shop shelves to warehouse.</p>
                </div>
                <button type="button" onclick="document.getElementById('return-warehouse-modal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="return-warehouse-form" class="px-6 py-6 space-y-5">
                <div id="return-shelf-source-label" class="px-4 py-2.5 bg-slate-100 border border-slate-300 rounded-[12px] text-xs font-bold text-slate-700 tracking-wide uppercase"></div>
                <input type="hidden" id="return-shelf-id" name="shelf_id">
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Destination Warehouse Shelf</label>
                    <select id="return-warehouse-shelf-select" name="warehouse_shelf_id" required class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm">
                        <option value="">Select warehouse shelf</option>
                    </select>
                </div>
                <div class="rounded-[28px] border border-slate-200 p-4 bg-white space-y-3">
                    <label class="block text-sm font-semibold text-slate-900">Products to Return</label>
                    <div id="return-shelf-products-container" class="space-y-2 max-h-60 overflow-y-auto">
                        <!-- Shelf products will be loaded here -->
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-return-warehouse" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Return Products</button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Modal -->
    <div id="history-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('history-modal').classList.add('hidden')"></div>
        <div class="relative modal-panel w-full max-w-3xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)] flex flex-col">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Shop Inventory History</h2>
                    <p class="text-sm text-slate-900 font-medium">Audit inventory transfers, sales, and modifications.</p>
                </div>
                <button type="button" onclick="document.getElementById('history-modal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                <!-- Date Filter -->
                <div class="flex flex-wrap gap-3 p-4 rounded-[28px] border border-slate-200 bg-white">
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Date Range</label>
                        <select id="history-date-range" class="w-full px-3 py-2 border border-slate-300 rounded-[12px] text-sm focus:outline-none focus:ring-1 focus:ring-black/35">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Action Type</label>
                        <select id="history-action-type" class="w-full px-3 py-2 border border-slate-300 rounded-[12px] text-sm focus:outline-none focus:ring-1 focus:ring-black/35">
                            <option value="">All Actions</option>
                            <option value="transfer_in">Transfer In (from Warehouse)</option>
                            <option value="return_to_warehouse">Return to Warehouse</option>
                            <option value="updated">Product Updated</option>
                            <option value="pos_sale">POS Sale</option>
                        </select>
                    </div>
                    <div id="custom-date-range" class="hidden flex gap-3 flex-1 min-w-[200px]">
                        <div class="flex-1 min-w-0">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">From</label>
                            <input type="date" id="history-from-date" class="w-full px-3 py-2 border border-slate-300 rounded-[12px] text-sm focus:outline-none focus:ring-1 focus:ring-black/35">
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">To</label>
                            <input type="date" id="history-to-date" class="w-full px-3 py-2 border border-slate-300 rounded-[12px] text-sm focus:outline-none focus:ring-1 focus:ring-black/35">
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="apply-date-filter" class="px-4 py-2 bg-[#6EC1D1] text-black font-bold rounded-[10px] text-sm hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Filter</button>
                    </div>
                </div>

                <div id="history-container" class="space-y-3 max-h-96 overflow-y-auto rounded-[20px] border border-slate-200 p-4 bg-slate-50">
                    <!-- History will be loaded here -->
                </div>
            </div>

            <div class="flex items-center justify-end px-6 py-4 border-t border-slate-200 bg-slate-50">
                <button type="button" id="close-history" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Close</button>
            </div>
        </div>
    </div>

    <script>
        function handleShelfAction(select, shelfId) {
            const action = select.value;
            select.value = ''; // Reset select

            if (action === 'edit-shelf') {
                openEditShelfModal(shelfId);
            } else if (action === 'transfer-products') {
                window.location.href = `/shop-inventory/transfer/${shelfId}`;
            } else if (action === 'return-to-warehouse') {
                openReturnWarehouseModal(shelfId);
            } else if (action === 'archive-shelf') {
                archiveShelf(shelfId);
            }
        }

        async function openEditShelfModal(shelfId) {
            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}/data`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const shelfData = await response.json();

                if (!shelfData) {
                    alert('Shelf not found');
                    return;
                }

                document.getElementById('edit-modal-title').textContent = 'Edit Shelf';
                document.getElementById('edit-shelf-id').value = shelfId;
                document.getElementById('edit-shelf-name').value = shelfData.name || '';
                document.getElementById('edit-shelf-location').value = shelfData.location || '';
                // Pre-fill shelf capacity
                const editCapInput = document.getElementById('edit-shelf-capacity');
                if (editCapInput) editCapInput.value = shelfData.capacity || 10;

                // Load existing products
                const productRows = document.getElementById('edit-product-rows');
                productRows.innerHTML = '';

                if (shelfData.shop_inventory && shelfData.shop_inventory.length > 0) {
                    shelfData.shop_inventory.forEach(item => {
                        const p   = item.product || {};
                        const cat = p.product_catalog || {};
                        addEditProductRow({
                            product_id:      item.product_id,
                            description:     cat.product_description || p.description || p.name || '',
                            brand:           cat.brand               || p.brand       || '',
                            compatible_model:cat.product_name        || p.compatibility || '',
                            sku:             cat.sku                 || p.sku         || '',
                            qty:             item.quantity,
                            price:           p.unit_price ?? 0,
                        });
                    });
                }

                document.getElementById('edit-modal-backdrop').classList.remove('hidden');
                document.getElementById('edit-modal-backdrop').classList.add('flex');
            } catch (error) {
                alert('Error loading shelf data');
            }
        }

        function addEditProductRow(product = {}) {
            const container = document.getElementById('edit-product-rows');
            // Read capacity from the edit modal capacity input
            const capacityInput = document.getElementById('edit-shelf-capacity');
            const maxCapacity = capacityInput ? parseInt(capacityInput.value) || 999 : 999;
            if (container.children.length >= maxCapacity) {
                showToast(`Shelf is full (capacity: ${maxCapacity}). Increase the shelf capacity to add more.`, 'error');
                return;
            }
            const row = document.createElement('div');
            row.className = 'product-row-card';
            row.dataset.productId = product.product_id || '';
            row.innerHTML = `
                <div class="space-y-2">
                    <div class="grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Product Category</label>
                            <input type="text" class="product-desc mt-1 block w-full px-4 py-3 text-sm" value="${product.description || ''}" placeholder="e.g. CALIPER" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Brand</label>
                            <input type="text" class="product-brand mt-1 block w-full px-4 py-3 text-sm" value="${product.brand || ''}" placeholder="e.g. RCB S26" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Compatible</label>
                            <input type="text" class="product-compatible mt-1 block w-full px-4 py-3 text-sm" value="${product.compatible_model || ''}" placeholder="e.g. SNIPER 150/155" />
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-[1.5fr_1fr_0.7fr_0.7fr_auto]">
                        <div>
                            <label class="block text-xs font-medium text-slate-700">SKU</label>
                            <input type="text" class="product-sku mt-1 block w-full px-4 py-3 text-sm" value="${product.sku || ''}" placeholder="SKU" />
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Qty</label>
                            <input type="number" min="0" class="product-qty mt-1 block w-full px-4 py-3 text-sm" value="${product.qty || ''}" placeholder="Qty" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700">Price</label>
                            <input type="number" step="0.01" min="0" class="product-price mt-1 block w-full px-4 py-3 text-sm" value="${product.price || ''}" placeholder="Price" />
                        </div>
                        <div class="flex items-end pb-1">
                            <button type="button" class="remove-product-row text-sm font-semibold text-black hover:text-slate-700">Remove</button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(row);

            row.querySelector('.remove-product-row').addEventListener('click', function() {
                row.remove();
            });
        }

        function generateSKU(productName) {
            // Take product name, remove special chars, convert to uppercase
            const cleanName = productName.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
            // Add KCC prefix
            return 'KCC-' + cleanName;
        }

        function getEditProductRows() {
            return Array.from(document.querySelectorAll('#edit-product-rows .product-row-card')).map(row => {
                const description     = row.querySelector('.product-desc')?.value.trim()       || '';
                const brand           = row.querySelector('.product-brand')?.value.trim()      || '';
                const compatible_model= row.querySelector('.product-compatible')?.value.trim() || '';
                const sku             = row.querySelector('.product-sku')?.value.trim()        || '';
                const qty             = parseInt(row.querySelector('.product-qty')?.value, 10)  || 0;
                const price           = parseFloat(row.querySelector('.product-price')?.value)  || 0;
                return { name: description || sku, description, brand, compatible_model, sku, qty, price };
            }).filter(p => p.description || p.sku);
        }

        function closeEditModal() {
            document.getElementById('edit-modal-backdrop').classList.add('hidden');
            document.getElementById('edit-modal-backdrop').classList.remove('flex');
        }

        function closeReturnWarehouseModal() {
            document.getElementById('return-warehouse-modal').classList.add('hidden');
            document.getElementById('return-warehouse-modal').classList.remove('flex');
        }

        async function openReturnWarehouseModal(shelfId) {
            try {
                document.getElementById('return-shelf-id').value = shelfId;

                // Load warehouse shelves
                const warehouseResponse = await fetch('/api/shop-inventory/warehouse-shelves', {
                    headers: { 'Accept': 'application/json' }
                });
                const warehouseData = await warehouseResponse.json();
                console.log('Warehouse API response:', warehouseData);
                const warehouseShelves = Array.isArray(warehouseData.data) ? warehouseData.data : (Array.isArray(warehouseData) ? warehouseData : []);

                console.log('Warehouse shelves:', warehouseShelves);
                console.log('Number of shelves:', warehouseShelves.length);

                const warehouseSelect = document.getElementById('return-warehouse-shelf-select');
                console.log('Select element:', warehouseSelect);

                warehouseSelect.innerHTML = '<option value="">Select warehouse shelf</option>';

                // New structure: warehouses grouped by warehouse_id
                warehouseShelves.forEach(warehouse => {
                    // Add warehouse as a group header
                    const warehouseGroup = document.createElement('optgroup');
                    warehouseGroup.label = warehouse.name;

                    // Add shelves under this warehouse
                    if (warehouse.shelves && warehouse.shelves.length > 0) {
                        warehouse.shelves.forEach(shelf => {
                            const option = document.createElement('option');
                            option.value = shelf.id;
                            option.textContent = `${shelf.name} (Slot ${shelf.slot_index})`;
                            warehouseGroup.appendChild(option);
                            console.log('Added option:', option.textContent);
                        });
                    }

                    warehouseSelect.appendChild(warehouseGroup);
                });

                console.log('Select element after population:', warehouseSelect);
                console.log('Number of options:', warehouseSelect.options.length);
                console.log('Options HTML:', warehouseSelect.innerHTML);

                // Load shelf products
                const response = await fetch(`/shop-inventory/shelf/${shelfId}/data`, {
                    headers: { 'Accept': 'application/json' }
                });
                const shelfData = await response.json();

                const container = document.getElementById('return-shelf-products-container');
                container.innerHTML = '';

                // Set shelf source label
                const sourceLabel = document.getElementById('return-shelf-source-label');
                const shelfLocation = shelfData.location ? ` — ${shelfData.location}` : '';
                sourceLabel.textContent = `From: ${shelfData.name || 'Unknown Shelf'}${shelfLocation}`;

                if (shelfData.shop_inventory && shelfData.shop_inventory.length > 0) {
                    shelfData.shop_inventory.forEach(item => {
                        const productDiv = document.createElement('div');
                        productDiv.className = 'flex items-center gap-3 p-3 border rounded-lg';
                        const checkboxId = `return-check-${item.id}`;
                        productDiv.innerHTML = `
                            <input type="checkbox" id="${checkboxId}" class="return-select w-4 h-4 accent-emerald-600 cursor-pointer flex-shrink-0" checked
                                   data-inventory-id="${item.id}">
                            <label for="${checkboxId}" class="flex-1 cursor-pointer">
                                <p class="font-medium text-sm">${item.product ? item.product.name : 'Unknown'}</p>
                                <p class="text-xs text-gray-500">${item.product ? item.product.sku : ''}</p>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" class="return-qty w-20 px-2 py-1 border rounded text-sm"
                                       min="1" max="${item.quantity}" value="${item.quantity}"
                                       data-inventory-id="${item.id}" data-product-id="${item.product_id}">
                                <span class="text-xs text-gray-500">of ${item.quantity}</span>
                            </div>
                        `;
                        // Toggle qty input when checkbox changes
                        const checkbox = productDiv.querySelector('.return-select');
                        const qtyInput = productDiv.querySelector('.return-qty');
                        checkbox.addEventListener('change', function() {
                            qtyInput.disabled = !this.checked;
                            productDiv.classList.toggle('opacity-40', !this.checked);
                        });
                        container.appendChild(productDiv);
                    });
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">No products on this shelf</p>';
                }

                document.getElementById('return-warehouse-modal').classList.remove('hidden');
                document.getElementById('return-warehouse-modal').classList.add('flex');
            } catch (error) {
                showToast('Error loading shelf data', 'error');
            }
        }

        async function handleReturnWarehouse(e) {
            e.preventDefault();
            const shelfId = document.getElementById('return-shelf-id').value;
            const warehouseShelfId = document.getElementById('return-warehouse-shelf-select').value;
            const returns = [];

            if (!warehouseShelfId) {
                showToast('Please select a warehouse shelf', 'error');
                return;
            }

            document.querySelectorAll('#return-shelf-products-container .return-select').forEach(checkbox => {
                if (!checkbox.checked) return;
                const inventoryId = checkbox.dataset.inventoryId;
                const qtyInput = document.querySelector(`#return-shelf-products-container .return-qty[data-inventory-id="${inventoryId}"]`);
                const qty = qtyInput ? parseInt(qtyInput.value, 10) : 0;
                if (qty > 0) {
                    returns.push({
                        inventory_id: inventoryId,
                        product_id: qtyInput.dataset.productId,
                        quantity: qty
                    });
                }
            });

            if (returns.length === 0) {
                showToast('Please select products to return', 'error');
                return;
            }

            try {
                const response = await fetch('/api/shop-inventory/return-to-warehouse', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        shelf_id: shelfId,
                        warehouse_shelf_id: warehouseShelfId,
                        returns: returns
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message || 'Products returned to warehouse successfully', 'success');
                    closeReturnWarehouseModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showToast(data.message || 'Failed to return products', 'error');
                }
            } catch (error) {
                showToast('Error returning products', 'error');
            }
        }

        async function handleEditModalSave(e) {
            e.preventDefault();
            const shelfId = document.getElementById('edit-shelf-id').value;
            const products = getEditProductRows();

            const payload = {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                _method: 'PUT',
                name: document.getElementById('edit-shelf-name').value,
                location: document.getElementById('edit-shelf-location').value,
                products: products
            };

            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message || 'Shelf updated successfully', 'success');
                    closeEditModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showToast(data.message || 'Failed to update shelf', 'error');
                }
            } catch (error) {
                showToast('Error updating shelf', 'error');
            }
        }

        async function archiveShelf(shelfId) {
            if (!confirm('Are you sure you want to archive this shelf? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    showToast('Shelf archived successfully', 'success');
                    window.location.reload();
                } else {
                    showToast(data.message || 'Failed to archive shelf', 'error');
                }
            } catch (error) {
                showToast('Error archiving shelf', 'error');
            }
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()">✕</button>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        function openAddShelfModal() {
            console.log('Opening Add Shelf modal');
            const backdrop = document.getElementById('add-shelf-modal-backdrop');
            if (!backdrop) {
                console.error('Modal backdrop not found');
                return;
            }
            backdrop.classList.remove('hidden');
            backdrop.classList.add('flex');

            // Load shop sections
            loadShopSections();

            // Initialize with 3 product rows
            const container = document.getElementById('add-shelf-product-rows');
            if (container) {
                container.innerHTML = '';
                for (let i = 0; i < 3; i++) {
                    addShelfProductRow();
                }
                updateAddShelfProductButton();
            }
        }

        function toggleAddShelfLocationDropdown(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('addShelfLocationDropdownMenu');
            if (menu) menu.classList.toggle('hidden');
        }

        function selectAddShelfLocationOption(val, labelText) {
            const selectEl = document.getElementById('add-shelf-location');
            const labelSpan = document.getElementById('addShelfLocationSelectLabel');
            const menu = document.getElementById('addShelfLocationDropdownMenu');
            const newSectionInput = document.getElementById('add-shelf-new-section');

            if (selectEl) {
                selectEl.value = val;
                selectEl.dispatchEvent(new Event('change'));
            }
            if (labelSpan) labelSpan.textContent = labelText;
            if (menu) menu.classList.add('hidden');

            if (val === 'ADD_NEW_SECTION') {
                if (newSectionInput) {
                    newSectionInput.classList.remove('hidden');
                    newSectionInput.required = true;
                    newSectionInput.focus();
                }
            } else {
                if (newSectionInput) {
                    newSectionInput.classList.add('hidden');
                    newSectionInput.required = false;
                }
            }
        }

        async function loadShopSections() {
            try {
                const response = await fetch('/api/shop-inventory/shop-sections', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const locationSelect = document.getElementById('add-shelf-location');
                const locationMenu = document.getElementById('addShelfLocationDropdownMenu');
                const locationLabel = document.getElementById('addShelfLocationSelectLabel');

                if (locationSelect) locationSelect.innerHTML = '<option value="">Select location</option>';
                if (locationLabel) locationLabel.textContent = 'Select location';

                let menuHtml = '';

                if (data.success && data.sections && data.sections.length > 0) {
                    data.sections.forEach(section => {
                        const optText = section.available === 0 
                            ? `${section.name} (Full - ${section.shelf_count}/${section.max} shelves)` 
                            : `${section.name} (${section.available} available shelves - ${section.shelf_count}/${section.max})`;
                        
                        if (locationSelect) {
                            const option = document.createElement('option');
                            option.value = section.name;
                            option.textContent = optText;
                            if (section.available === 0) option.disabled = true;
                            locationSelect.appendChild(option);
                        }

                        if (section.available > 0) {
                            menuHtml += `<button type="button" onclick="selectAddShelfLocationOption('${section.name}', '${section.name}')" class="w-full text-left px-3 py-2.5 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">${optText}</button>`;
                        } else {
                            menuHtml += `<button type="button" disabled class="w-full text-left px-3 py-2.5 rounded-[8px] text-xs font-semibold text-slate-400 bg-slate-50 cursor-not-allowed">${optText}</button>`;
                        }
                    });
                }

                menuHtml += `<button type="button" onclick="selectAddShelfLocationOption('ADD_NEW_SECTION', '+ Add New Section')" class="w-full text-left px-3 py-2.5 rounded-[8px] text-xs font-bold text-black hover:bg-slate-100 transition cursor-pointer">+ Add New Section</button>`;

                if (locationMenu) locationMenu.innerHTML = menuHtml;

                if (locationSelect) {
                    const addSectionOption = document.createElement('option');
                    addSectionOption.value = 'ADD_NEW_SECTION';
                    addSectionOption.textContent = '+ Add New Section';
                    locationSelect.appendChild(addSectionOption);
                }
            } catch (error) {
                console.error('Error loading shop sections:', error);
            }
        }

        function closeAddShelfModal() {
            document.getElementById('add-shelf-modal-backdrop').classList.add('hidden');
            document.getElementById('add-shelf-modal-backdrop').classList.remove('flex');
            document.getElementById('add-shelf-modal-form').reset();
        }

        function toggleDropdown(menuId, e) {
            if (e) e.stopPropagation();
            const target = document.getElementById(menuId);
            document.querySelectorAll('.dropdown-menu, [id$="-menu"]').forEach(m => {
                if (m !== target) m.classList.add('hidden');
            });
            if (target) target.classList.toggle('hidden');
        }

        function selectShopDropdownOption(selectId, labelId, menuId, val, labelText) {
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

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id$="-menu"]').forEach(m => m.classList.add('hidden'));
            }
        });

        async function loadSectionsForFilter() {
            try {
                const response = await fetch('/api/shop-inventory/shop-sections', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const sectionFilter = document.getElementById('section-filter');
                if (sectionFilter) sectionFilter.innerHTML = '<option value="">All Sections</option>';

                const sectionMenu = document.getElementById('dd-section-menu');
                if (sectionMenu) {
                    let html = `<button type="button" onclick="selectShopDropdownOption('section-filter', 'dd-section-label', 'dd-section-menu', '', 'All Sections')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">All Sections</button>`;
                    if (data.success && data.sections && data.sections.length > 0) {
                        data.sections.forEach(section => {
                            if (sectionFilter) {
                                const option = document.createElement('option');
                                option.value = section.name;
                                option.textContent = section.name;
                                sectionFilter.appendChild(option);
                            }
                            html += `<button type="button" onclick="selectShopDropdownOption('section-filter', 'dd-section-label', 'dd-section-menu', '${section.name}', '${section.name}')" class="w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer">${section.name}</button>`;
                        });
                    }
                    sectionMenu.innerHTML = html;
                }
            } catch (error) {
                console.error('Error loading sections for filter:', error);
            }
        }

        let currentPage = 1;
        const itemsPerPage = 4;

        function filterShelves() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
            const sectionFilter = document.getElementById('section-filter').value.toLowerCase().trim();
            const shelfCards = document.querySelectorAll('.shelf-card');

            console.log('Filtering - Search:', searchTerm, 'Section:', sectionFilter);
            console.log('Total shelf cards:', shelfCards.length);

            let visibleCount = 0;
            const visibleCards = [];

            shelfCards.forEach(card => {
                const shelfName = card.querySelector('.shelf-name')?.textContent.toLowerCase().trim() || '';
                const shelfLocation = card.querySelector('.shelf-location')?.textContent.toLowerCase().trim() || '';
                const products = Array.from(card.querySelectorAll('.product-chip .name')).map(p => p.textContent.toLowerCase().trim());

                console.log('Card - Name:', shelfName, 'Location:', shelfLocation);

                // Check if matches search term
                const matchesSearch = searchTerm === '' ||
                    shelfName.includes(searchTerm) ||
                    shelfLocation.includes(searchTerm) ||
                    products.some(p => p.includes(searchTerm));

                // Check if matches section filter (exact match)
                const matchesSection = sectionFilter === '' || shelfLocation === sectionFilter;

                console.log('Matches Search:', matchesSearch, 'Matches Section:', matchesSection);

                // Show/hide card
                if (matchesSearch && matchesSection) {
                    card.style.display = 'block';
                    card.dataset.visible = 'true';
                    visibleCards.push(card);
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                    card.dataset.visible = 'false';
                }
            });

            console.log('Visible shelves:', visibleCount);

            // Reset to page 1 and apply pagination
            currentPage = 1;
            applyPagination(visibleCards);
        }

        function applyPagination(visibleCards) {
            const totalPages = Math.ceil(visibleCards.length / itemsPerPage);

            // Update pagination controls
            document.getElementById('total-pages').textContent = totalPages;
            document.getElementById('current-page').textContent = currentPage;

            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            const paginationControls = document.getElementById('pagination-controls');

            if (totalPages > 1) {
                paginationControls.classList.remove('hidden');
                prevBtn.disabled = currentPage === 1;
                nextBtn.disabled = currentPage === totalPages;
            } else {
                paginationControls.classList.add('hidden');
            }

            // Show/hide cards based on current page
            visibleCards.forEach((card, index) => {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                if (index >= start && index < end) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function goToPage(page) {
            const visibleCards = Array.from(document.querySelectorAll('.shelf-card')).filter(card => card.dataset.visible === 'true');
            const totalPages = Math.ceil(visibleCards.length / itemsPerPage);

            if (page < 1 || page > totalPages) return;

            currentPage = page;
            applyPagination(visibleCards);
        }

        function clearFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('section-filter').value = '';
            filterShelves();
        }

        function addShelfProductRow() {
            const container = document.getElementById('add-shelf-product-rows');
            if (!container) return;

            // Read capacity from the add modal capacity input
            const capacityInput = document.getElementById('add-shelf-capacity');
            const maxCapacity = capacityInput ? parseInt(capacityInput.value) || 999 : 999;
            const currentRows = container.children.length;
            if (currentRows >= maxCapacity) {
                showToast(`Shelf is full (capacity: ${maxCapacity}). Increase the shelf capacity to add more.`, 'error');
                return;
            }

            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 bg-slate-50 rounded-[16px] border border-slate-200/80 p-3';
            row.innerHTML = `
                <div class="flex items-center gap-1 flex-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">PRODUCT NAME:</label>
                    <input type="text" class="add-shelf-product-name flex-1 px-3 py-2 border border-slate-300 rounded-[12px] text-sm bg-white focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" placeholder="Enter product name">
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">SKU:</label>
                    <div class="add-shelf-sku-display text-xs font-mono font-bold text-slate-700 min-w-[100px]">KCC_</div>
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">QTY:</label>
                    <input type="number" class="add-shelf-product-qty w-16 px-2 py-2 border border-slate-300 rounded-[12px] text-sm bg-white focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" min="1" value="1">
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">PRICE:</label>
                    <input type="number" class="add-shelf-product-price w-20 px-2 py-2 border border-slate-300 rounded-[12px] text-sm bg-white focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" min="0" step="0.01">
                </div>
                <button type="button" onclick="this.parentElement.remove(); updateAddShelfProductButton();" class="text-black hover:bg-black/10 p-2 rounded-[10px] transition cursor-pointer">
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;

            // Auto-generate SKU as user types
            const nameInput = row.querySelector('.add-shelf-product-name');
            const skuDisplay = row.querySelector('.add-shelf-sku-display');

            nameInput.addEventListener('input', function() {
                const name = this.value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
                skuDisplay.textContent = name ? `KCC_${name}` : 'KCC_';
            });

            container.appendChild(row);
            updateAddShelfProductButton();
        }

        function updateAddShelfProductButton() {
            const container = document.getElementById('add-shelf-product-rows');
            const button = document.getElementById('add-shelf-product-row');
            if (!container || !button) return;

            const currentRows = container.children.length;
            if (currentRows >= 10) {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        async function handleAddShelfSave(e) {
            e.preventDefault();

            // Collect product data from rows
            const productRows = document.querySelectorAll('#add-shelf-product-rows > div');
            const products = [];
            productRows.forEach(row => {
                const nameInput = row.querySelector('.add-shelf-product-name');
                const skuDisplay = row.querySelector('.add-shelf-sku-display');
                const qtyInput = row.querySelector('.add-shelf-product-qty');
                const priceInput = row.querySelector('.add-shelf-product-price');
                const name = nameInput.value.trim();
                if (name) {
                    products.push({
                        name: name,
                        sku: skuDisplay.textContent,
                        qty: parseInt(qtyInput.value) || 1,
                        price: parseFloat(priceInput.value) || 0
                    });
                }
            });

            const locationSelect = document.getElementById('add-shelf-location');
            let locationValue = locationSelect.value;

            // If adding new section, use the input value
            if (locationValue === 'ADD_NEW_SECTION') {
                locationValue = document.getElementById('add-shelf-new-section').value;
            }

            const shelfData = {
                name: document.getElementById('add-shelf-name').value,
                location: locationValue,
                products: products
            };

            try {
                const response = await fetch('/shop-inventory/shelf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(shelfData)
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message || 'Shelf created successfully', 'success');
                    closeAddShelfModal();
                    window.location.reload();
                } else {
                    showToast(data.message || 'Failed to create shelf', 'error');
                }
            } catch (error) {
                showToast('Error creating shelf', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load sections for filter dropdown
            loadSectionsForFilter();

            // Apply initial pagination to all shelves
            const allShelves = Array.from(document.querySelectorAll('.shelf-card'));
            allShelves.forEach(card => card.dataset.visible = 'true');
            applyPagination(allShelves);

            // Search and filter functionality
            const searchInput = document.getElementById('search-input');
            const sectionFilter = document.getElementById('section-filter');
            const clearSearchBtn = document.getElementById('clear-search');

            if (searchInput) {
                searchInput.addEventListener('input', filterShelves);
            }

            if (sectionFilter) {
                sectionFilter.addEventListener('change', filterShelves);
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', clearFilters);
            }

            // Pagination controls
            const prevPageBtn = document.getElementById('prev-page');
            const nextPageBtn = document.getElementById('next-page');

            if (prevPageBtn) {
                prevPageBtn.addEventListener('click', () => goToPage(currentPage - 1));
            }

            if (nextPageBtn) {
                nextPageBtn.addEventListener('click', () => goToPage(currentPage + 1));
            }

            // Add Shelf
            const addShelfButton = document.getElementById('add-shelf-button');
            if (addShelfButton) {
                addShelfButton.addEventListener('click', openAddShelfModal);
            }

            const addShelfModalClose = document.getElementById('add-shelf-modal-close');
            if (addShelfModalClose) {
                addShelfModalClose.addEventListener('click', closeAddShelfModal);
            }

            const addShelfModalCancel = document.getElementById('add-shelf-modal-cancel');
            if (addShelfModalCancel) {
                addShelfModalCancel.addEventListener('click', closeAddShelfModal);
            }

            const addShelfProductRowBtn = document.getElementById('add-shelf-product-row');
            if (addShelfProductRowBtn) {
                addShelfProductRowBtn.addEventListener('click', addShelfProductRow);
            }

            const addShelfModalForm = document.getElementById('add-shelf-modal-form');
            if (addShelfModalForm) {
                addShelfModalForm.addEventListener('submit', handleAddShelfSave);
            }

            // Handle location dropdown change
            const addShelfLocation = document.getElementById('add-shelf-location');
            if (addShelfLocation) {
                addShelfLocation.addEventListener('change', function() {
                    const newSectionInput = document.getElementById('add-shelf-new-section');
                    if (this.value === 'ADD_NEW_SECTION') {
                        newSectionInput.classList.remove('hidden');
                        newSectionInput.classList.add('block');
                        newSectionInput.required = true;
                    } else {
                        newSectionInput.classList.add('hidden');
                        newSectionInput.classList.remove('block');
                        newSectionInput.required = false;
                        newSectionInput.value = '';
                    }
                });
            }

            // Edit Shelf
            document.getElementById('edit-modal-close').addEventListener('click', closeEditModal);
            document.getElementById('edit-modal-cancel').addEventListener('click', closeEditModal);
            document.getElementById('edit-modal-form').addEventListener('submit', handleEditModalSave);
            document.getElementById('edit-add-product-row').addEventListener('click', () => addEditProductRow());

            // Transfer from warehouse
            document.getElementById('transfer-from-warehouse').addEventListener('click', openTransferWarehouseModal);
            document.getElementById('cancel-transfer-warehouse').addEventListener('click', closeTransferWarehouseModal);
            document.getElementById('transfer-warehouse-form').addEventListener('submit', handleTransferWarehouse);

            // Return to warehouse
            document.getElementById('cancel-return-warehouse').addEventListener('click', closeReturnWarehouseModal);
            document.getElementById('return-warehouse-form').addEventListener('submit', handleReturnWarehouse);

            // History
            document.getElementById('view-history').addEventListener('click', loadHistory);
            document.getElementById('close-history').addEventListener('click', closeHistoryModal);
            document.getElementById('apply-date-filter').addEventListener('click', loadHistory);
            document.getElementById('history-date-range').addEventListener('change', handleDateRangeChange);
            document.getElementById('history-action-type').addEventListener('change', loadHistory);
        });

        let warehouseProductsData = [];
        let selectedTransferProducts = [];

        async function openTransferWarehouseModal() {
            const transferBtn = document.getElementById('transfer-from-warehouse');
            const originalBtnHtml = transferBtn ? transferBtn.innerHTML : '';

            if (transferBtn) {
                transferBtn.disabled = true;
                transferBtn.classList.add('opacity-75', 'cursor-wait');
                transferBtn.innerHTML = `
                    <svg class="animate-spin h-3.5 w-3.5 text-white inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Loading...</span>
                `;
            }

            try {
                // Reset state
                selectedTransferProducts = [];
                updateSelectedProductsList();

                // Load shop shelves
                const shelvesResponse = await fetch('/api/shop-inventory/shelves', {
                    headers: { 'Accept': 'application/json' }
                });
                const shelvesData = await shelvesResponse.json();
                const shelves = Array.isArray(shelvesData.data) ? shelvesData.data : (Array.isArray(shelvesData) ? shelvesData : []);

                const shelfSelect = document.querySelector('#transfer-warehouse-form select[name="shop_shelf_id"]');
                shelfSelect.innerHTML = '<option value="">Select destination shop shelf</option>';
                shelves.forEach(shelf => {
                    shelfSelect.innerHTML += `<option value="${shelf.id}">${shelf.name} (${shelf.location || 'No location'})</option>`;
                });

                // Load warehouse shelves
                const warehouseResponse = await fetch('/api/shop-inventory/warehouse-shelves', {
                    headers: { 'Accept': 'application/json' }
                });
                const warehouseData = await warehouseResponse.json();
                const warehouses = Array.isArray(warehouseData.data) ? warehouseData.data : (Array.isArray(warehouseData) ? warehouseData : []);

                // Store warehouse data for later use
                window.warehouseData = warehouses;

                // Populate warehouse dropdown
                const warehouseSelect = document.getElementById('warehouse-select');
                warehouseSelect.innerHTML = '<option value="">Select warehouse</option>';
                warehouses.forEach(warehouse => {
                    const shelfCount = warehouse.shelves ? warehouse.shelves.length : 0;
                    warehouseSelect.innerHTML += `<option value="${warehouse.id}">${warehouse.name} (${shelfCount} shelves)</option>`;
                });

                // Reset shelf dropdown
                const warehouseShelfSelect = document.getElementById('warehouse-shelf-select');
                warehouseShelfSelect.innerHTML = '<option value="">Select shelf</option>';
                warehouseShelfSelect.disabled = true;

                // Reset product dropdown
                const productSelect = document.getElementById('warehouse-product-select');
                productSelect.innerHTML = '<option value="">Select product</option>';
                productSelect.disabled = true;

                // Reset quantity input
                const quantityInput = document.getElementById('transfer-quantity');
                quantityInput.value = 1;
                quantityInput.disabled = true;
                quantityInput.max = 1;

                // Reset add button
                const addBtn = document.getElementById('add-to-transfer');
                if (addBtn) addBtn.disabled = true;

                document.getElementById('transfer-warehouse-modal').classList.remove('hidden');
                document.getElementById('transfer-warehouse-modal').classList.add('flex');
            } catch (error) {
                console.error('Error loading transfer data:', error);
                showToast('Error loading warehouse data: ' + error.message, 'error');
            } finally {
                if (transferBtn) {
                    transferBtn.disabled = false;
                    transferBtn.classList.remove('opacity-75', 'cursor-wait');
                    transferBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        // Handle warehouse selection
        document.getElementById('warehouse-select').addEventListener('change', function() {
            const warehouseId = this.value;
            const shelfSelect = document.getElementById('warehouse-shelf-select');
            const productSelect = document.getElementById('warehouse-product-select');
            const quantityInput = document.getElementById('transfer-quantity');
            const addButton = document.getElementById('add-to-transfer');

            // Reset downstream dropdowns
            shelfSelect.innerHTML = '<option value="">Select shelf</option>';
            shelfSelect.disabled = true;
            productSelect.innerHTML = '<option value="">Select product</option>';
            productSelect.disabled = true;
            quantityInput.disabled = true;
            quantityInput.value = 1;
            addButton.disabled = true;

            if (!warehouseId) {
                return;
            }

            // Populate shelf dropdown for selected warehouse
            const warehouse = (window.warehouseData || []).find(w => String(w.id) === String(warehouseId));
            if (warehouse && warehouse.shelves && warehouse.shelves.length > 0) {
                shelfSelect.innerHTML = '<option value="">Select shelf</option>';
                warehouse.shelves.forEach(shelf => {
                    let products = [];
                    if (typeof shelf.products === 'string') {
                        try { products = JSON.parse(shelf.products); } catch (e) { products = []; }
                    } else if (Array.isArray(shelf.products)) {
                        products = shelf.products;
                    }
                    const count = products.length;
                    const countLabel = count > 0 ? ` (${count} product${count > 1 ? 's' : ''})` : ' (Empty)';
                    shelfSelect.innerHTML += `<option value="${shelf.id}">${shelf.name}${countLabel}</option>`;
                });
                shelfSelect.disabled = false;
            } else {
                shelfSelect.innerHTML = '<option value="">No shelves in this warehouse</option>';
                shelfSelect.disabled = true;
            }
        });

        // Handle warehouse shelf selection
        document.getElementById('warehouse-shelf-select').addEventListener('change', function() {
            const shelfId = this.value;
            const warehouseId = document.getElementById('warehouse-select').value;
            const productSelect = document.getElementById('warehouse-product-select');
            const quantityInput = document.getElementById('transfer-quantity');
            const addButton = document.getElementById('add-to-transfer');

            productSelect.innerHTML = '<option value="">Select product</option>';
            productSelect.disabled = true;
            quantityInput.disabled = true;
            quantityInput.value = 1;
            addButton.disabled = true;

            if (!shelfId) {
                return;
            }

            // Find the shelf in warehouse data
            const warehouse = (window.warehouseData || []).find(w => String(w.id) === String(warehouseId));
            const selectedShelf = warehouse && warehouse.shelves ? warehouse.shelves.find(s => String(s.id) === String(shelfId)) : null;

            if (selectedShelf) {
                let products = [];
                if (typeof selectedShelf.products === 'string') {
                    try { products = JSON.parse(selectedShelf.products); } catch (e) { products = []; }
                } else if (Array.isArray(selectedShelf.products)) {
                    products = selectedShelf.products;
                }

                if (products && products.length > 0) {
                    productSelect.innerHTML = '<option value="">Select product</option>';
                    products.forEach((product, idx) => {
                        const pName = product.name || product.description || 'Product';
                        const pSku = product.sku || ('PROD-' + (product.id || idx));
                        const pQty = parseInt(product.qty ?? product.stock_quantity ?? product.quantity ?? 1);
                        const safeName = pName.replace(/"/g, '&quot;');
                        productSelect.innerHTML += `<option value="${pSku}" data-qty="${pQty}" data-name="${safeName}" data-shelf-id="${selectedShelf.id}" data-product-id="${product.id || ''}">${pName} (${pSku}) — Stock: ${pQty}</option>`;
                    });
                    productSelect.disabled = false;
                } else {
                    productSelect.innerHTML = '<option value="">No products on this shelf</option>';
                    productSelect.disabled = true;
                }
            }
        });

        // Handle product selection
        document.getElementById('warehouse-product-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxQty = parseInt(selectedOption?.dataset?.qty) || 1;
            const quantityInput = document.getElementById('transfer-quantity');
            const addButton = document.getElementById('add-to-transfer');

            if (!this.value) {
                quantityInput.disabled = true;
                addButton.disabled = true;
                return;
            }

            quantityInput.max = maxQty;
            quantityInput.min = 1;
            quantityInput.value = 1;
            quantityInput.disabled = false;
            addButton.disabled = false;
        });

        // Handle add to transfer
        document.getElementById('add-to-transfer').addEventListener('click', function() {
            const productSelect = document.getElementById('warehouse-product-select');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const quantityInput = document.getElementById('transfer-quantity');

            if (!productSelect.value || !selectedOption) {
                showToast('Please select a product', 'error');
                return;
            }

            const maxQty = parseInt(selectedOption.dataset.qty) || 1;
            let qty = parseInt(quantityInput.value) || 1;
            if (qty < 1) qty = 1;
            if (qty > maxQty) {
                showToast(`Max available quantity is ${maxQty}`, 'error');
                qty = maxQty;
            }

            const product = {
                id: selectedOption.dataset.productId || productSelect.value,
                name: selectedOption.dataset.name,
                sku: productSelect.value,
                quantity: qty,
                warehouse_shelf_id: selectedOption.dataset.shelfId,
                max_qty: maxQty
            };

            // Check if product already in list from this shelf
            const existingIndex = selectedTransferProducts.findIndex(p => p.sku === product.sku && p.warehouse_shelf_id === product.warehouse_shelf_id);
            if (existingIndex >= 0) {
                const newTotal = selectedTransferProducts[existingIndex].quantity + product.quantity;
                if (newTotal > maxQty) {
                    showToast(`Cannot transfer more than total stock (${maxQty})`, 'error');
                    selectedTransferProducts[existingIndex].quantity = maxQty;
                } else {
                    selectedTransferProducts[existingIndex].quantity = newTotal;
                }
            } else {
                selectedTransferProducts.push(product);
            }

            updateSelectedProductsList();

            // Reset selection
            productSelect.value = '';
            quantityInput.value = 1;
            quantityInput.disabled = true;
            this.disabled = true;
        });

        function updateSelectedProductsList() {
            const container = document.getElementById('selected-products-list');
            const noProductsMsg = document.getElementById('no-products-selected');

            container.innerHTML = '';

            if (selectedTransferProducts.length === 0) {
                noProductsMsg.style.display = 'block';
                return;
            }

            noProductsMsg.style.display = 'none';

            selectedTransferProducts.forEach((product, index) => {
                const productRow = document.createElement('div');
                productRow.className = 'flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl';
                productRow.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-xs text-slate-900 truncate">${product.name}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">SKU: <span class="font-mono text-cyan-700 font-semibold">${product.sku}</span> | Transfer Qty: <span class="font-bold text-slate-900">${product.quantity}</span></p>
                    </div>
                    <button type="button" onclick="removeFromTransfer(${index})" class="text-xs font-semibold text-red-600 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50 transition cursor-pointer">Remove</button>
                `;
                container.appendChild(productRow);
            });
        }

        function removeFromTransfer(index) {
            selectedTransferProducts.splice(index, 1);
            updateSelectedProductsList();
        }

        function handleDateRangeChange() {
            const range = document.getElementById('history-date-range').value;
            const customRangeDiv = document.getElementById('custom-date-range');

            if (range === 'custom') {
                customRangeDiv.classList.remove('hidden');
            } else {
                customRangeDiv.classList.add('hidden');
                // Auto-apply filter for preset ranges
                if (range) {
                    loadHistory();
                }
            }
        }

        async function loadHistory() {
            try {
                const range = document.getElementById('history-date-range').value;
                const actionType = document.getElementById('history-action-type').value;
                const fromDate = document.getElementById('history-from-date').value;
                const toDate = document.getElementById('history-to-date').value;

                let url = '/api/shop-inventory/history';
                const params = new URLSearchParams();

                // Add action type filter
                if (actionType) {
                    params.append('action_type', actionType);
                }

                // Calculate dates based on preset range
                if (range && range !== 'custom') {
                    const now = new Date();
                    let startDate, endDate;

                    switch (range) {
                        case 'today':
                            startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                            endDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
                            break;
                        case 'this_week':
                            startDate = new Date(now);
                            startDate.setDate(now.getDate() - now.getDay());
                            startDate.setHours(0, 0, 0, 0);
                            endDate = new Date(now);
                            endDate.setDate(now.getDate() + (6 - now.getDay()) + 1);
                            endDate.setHours(0, 0, 0, 0);
                            break;
                        case 'this_month':
                            startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                            endDate = new Date(now.getFullYear(), now.getMonth() + 1, 1);
                            break;
                        case 'this_year':
                            startDate = new Date(now.getFullYear(), 0, 1);
                            endDate = new Date(now.getFullYear() + 1, 0, 1);
                            break;
                    }

                    if (startDate && endDate) {
                        params.append('from_date', startDate.toISOString().split('T')[0]);
                        params.append('to_date', endDate.toISOString().split('T')[0]);
                    }
                } else if (range === 'custom') {
                    // Use custom date inputs
                    if (fromDate) {
                        params.append('from_date', fromDate);
                    }
                    if (toDate) {
                        params.append('to_date', toDate);
                    }
                }

                if (params.toString()) {
                    url += '?' + params.toString();
                }

                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const history = await response.json();

                const container = document.getElementById('history-container');
                container.innerHTML = '';

                if (!history.data || history.data.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">No history found</p>';
                } else {
                    history.data.forEach(item => {
                        const actionColors = {
                            'transfer_in': 'bg-green-100 text-green-800',
                            'transfer_out': 'bg-orange-100 text-orange-800',
                            'updated': 'bg-blue-100 text-blue-800',
                            'pos_sale': 'bg-purple-100 text-purple-800',
                            'return_to_warehouse': 'bg-red-100 text-red-800'
                        };

                        const actionLabels = {
                            'transfer_in': 'Transfer In',
                            'transfer_out': 'Transfer Out',
                            'updated': 'Updated',
                            'pos_sale': 'POS Sale',
                            'return_to_warehouse': 'Return to Warehouse'
                        };

                        const actionType = item.action_type || 'unknown';
                        const colorClass = actionColors[actionType] || 'bg-gray-100 text-gray-800';
                        const actionLabel = actionLabels[actionType] || actionType;

                        const historyItem = document.createElement('div');
                        historyItem.className = 'p-4 bg-gray-50 rounded-lg';
                        historyItem.innerHTML = `
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium ${colorClass}">${actionLabel}</span>
                                        <span class="text-xs text-gray-500">${new Date(item.created_at).toLocaleString()}</span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">${item.product?.name || 'Unknown Product'}</p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Shelf: ${item.shop_shelf?.name || 'Unknown'} |
                                        Quantity: ${item.quantity_change || 0} |
                                        User: ${item.user?.name || 'Unknown'}
                                    </p>
                                    ${item.notes ? `<p class="text-xs text-gray-500 mt-1 italic">${item.notes}</p>` : ''}
                                </div>
                            </div>
                        `;
                        container.appendChild(historyItem);
                    });
                }

                document.getElementById('history-modal').classList.remove('hidden');
                document.getElementById('history-modal').classList.add('flex');
            } catch (error) {
                showToast('Error loading history: ' + error.message, 'error');
            }
        }

        function closeHistoryModal() {
            document.getElementById('history-modal').classList.add('hidden');
            document.getElementById('history-modal').classList.remove('flex');
        }

        function closeTransferWarehouseModal() {
            document.getElementById('transfer-warehouse-modal').classList.add('hidden');
            document.getElementById('transfer-warehouse-modal').classList.remove('flex');
        }

        async function handleTransferWarehouse(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const shopShelfId = formData.get('shop_shelf_id');
            const submitBtn = document.getElementById('submit-transfer-warehouse-btn') || e.target.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            if (!shopShelfId) {
                showToast('Please select a destination shop shelf', 'error');
                return;
            }

            if (selectedTransferProducts.length === 0) {
                showToast('Please select at least one product to transfer', 'error');
                return;
            }

            const transfers = selectedTransferProducts.map(product => ({
                product_id: product.sku || product.id,
                product_name: product.name,
                warehouse_shelf_id: product.warehouse_shelf_id,
                quantity: product.quantity
            }));

            const payload = {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                shop_shelf_id: shopShelfId,
                transfers: transfers
            };

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-black inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Transferring...</span>
                `;
            }

            try {
                const response = await fetch('/api/shop-inventory/transfer-from-warehouse', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message || 'Products transferred to shop shelf successfully!', 'success');
                    closeTransferWarehouseModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Transfer failed', 'error');
                }
            } catch (error) {
                showToast('Error transferring products: ' + error.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        }
    </script>

    <!-- Add Shelf Modal -->
    <div id="add-shelf-modal-backdrop" class="fixed inset-0 hidden items-center justify-center z-[100000002] px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeAddShelfModal()"></div>
        <div class="relative modal-panel w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)] flex flex-col">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Add Shelf</h2>
                    <p class="text-sm text-slate-900 font-medium">Configure shelf location, capacity, and products before saving.</p>
                </div>
                <button id="add-shelf-modal-close" type="button" onclick="closeAddShelfModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="add-shelf-modal-form" class="flex flex-col flex-1 overflow-hidden">
                <div class="px-6 py-6 overflow-y-auto space-y-5 flex-1">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Shelf Name</label>
                            <input id="add-shelf-name" type="text" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" placeholder="Enter shelf name" required />
                        </div>

                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Location</label>
                            <select id="add-shelf-location" class="hidden">
                                <option value="">Select location</option>
                            </select>
                            <div class="relative" id="addShelfLocationDropdownWrapper">
                                <button type="button" onclick="toggleAddShelfLocationDropdown(event)" class="w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-left text-sm font-medium text-slate-900 flex items-center justify-between gap-2 hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm cursor-pointer">
                                    <span id="addShelfLocationSelectLabel">Select location</span>
                                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div id="addShelfLocationDropdownMenu" class="hidden absolute left-0 right-0 top-full z-[100000005] mt-1 rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-52 overflow-y-auto">
                                </div>
                            </div>
                            <input type="text" id="add-shelf-new-section" class="hidden block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm mt-2" placeholder="Enter new section name" />
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Shelf Capacity</label>
                        <input id="add-shelf-capacity" type="number" min="1" value="10"
                               class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm"
                               placeholder="e.g. 10" />
                        <p class="text-xs text-slate-500 mt-2 font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6C8.8 12.16 8 10.66 8 9c0-2.21 1.79-4 4-4s4 1.79 4 4c0 1.66-.8 3.16-2.15 4.1z"/>
                            </svg>
                            <span>You can increase this to allow more products per shelf. Default is 10.</span>
                        </p>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">Products</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Enter product name, quantity, and price. SKU will auto-generate.</p>
                            </div>
                            <button type="button" id="add-shelf-product-row" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">+ Add product</button>
                        </div>
                        <div id="add-shelf-product-rows" class="grid gap-3 max-h-[540px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <button type="button" id="add-shelf-modal-cancel" onclick="closeAddShelfModal()" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Save shelf</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Shelf Modal -->
    <div id="edit-modal-backdrop" class="fixed inset-0 hidden items-center justify-center z-50 px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeEditModal()"></div>
        <div class="relative modal-panel w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)] flex flex-col">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 id="edit-modal-title" class="text-xl font-bold text-black">Edit Shelf</h2>
                    <p class="text-sm text-slate-900 font-medium">Update shelf information and assigned products.</p>
                </div>
                <button id="edit-modal-close" type="button" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="edit-modal-form" class="flex flex-col flex-1 overflow-hidden">
                <input type="hidden" id="edit-shelf-id" />

                <div class="px-6 py-6 overflow-y-auto space-y-5 flex-1">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Shelf Name</label>
                            <input id="edit-shelf-name" type="text" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" placeholder="Enter shelf name" required />
                        </div>
                        <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                            <label class="block text-sm font-semibold text-slate-900 mb-2">Location</label>
                            <input id="edit-shelf-location" type="text" class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm" placeholder="Enter location" />
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <label class="block text-sm font-semibold text-slate-900 mb-1">Shelf Capacity</label>
                        <input id="edit-shelf-capacity" type="number" min="1" value="10"
                               class="block w-full rounded-[12px] border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 transition shadow-sm"
                               placeholder="e.g. 10" />
                        <p class="text-xs text-slate-500 mt-2 font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7zm2.85 11.1l-.85.6V16h-4v-2.3l-.85-.6C8.8 12.16 8 10.66 8 9c0-2.21 1.79-4 4-4s4 1.79 4 4c0 1.66-.8 3.16-2.15 4.1z"/>
                            </svg>
                            <span>You can increase this to allow more products per shelf.</span>
                        </p>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4 bg-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">Products</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Choose existing inventory items, quantity, and price before saving.</p>
                            </div>
                            <button type="button" id="edit-add-product-row" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">+ Add product</button>
                        </div>
                        <div id="edit-product-rows" class="grid gap-3 max-h-[400px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50">
                    <button type="button" id="edit-modal-cancel" class="rounded-[10px] bg-black/10 px-5 py-2.5 text-sm font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2.5 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
</x-layouts.app>
