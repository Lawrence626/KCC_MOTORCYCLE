<x-layouts.app :title="__('Shop Inventory Management')">
    <style>
        :root {
            --brand: #0f766e;
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #64748b;
            --border: rgba(226, 232, 240, 0.8);
        }
        .si-card-glow { transition: all 0.3s ease; }
        .si-card-glow:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04); }
        .product-chip { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 0.85rem; 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            transition: all 0.2s ease;
        }
        .product-chip:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
        .chip-header { 
            background: linear-gradient(135deg, #0f766e, #115e59); 
            padding: 0.5rem 0.75rem; 
        }
        .chip-desc { color: #ffffff; font-weight: 700; font-size: 0.82rem; letter-spacing: 0.01em; }
        .chip-body { padding: 0.6rem 0.75rem; display: flex; flex-direction: column; gap: 0.22rem; }
        .chip-row { display: flex; align-items: center; justify-content: space-between; gap: 0.35rem; }
        .chip-label { font-size: 0.72rem; color: #64748b; font-weight: 500; }
        .chip-value { font-size: 0.76rem; color: #0f172a; font-weight: 600; }
        .chip-value.sku { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: #0f766e; font-size: 0.72rem; background: #ccfbf1; padding: 0.1rem 0.35rem; border-radius: 0.35rem; }
        .chip-value.price { color: #047857; font-weight: 700; }
        .chip-value.qty { color: #0284c7; font-weight: 700; }
        .shop-shelves { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem; }
        .map-unit { min-height: 220px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; }
        .modal-panel { width: min(100%, 960px); border-radius: 1.5rem; background: #ffffff; box-shadow: 0 28px 80px rgba(15,23,42,0.18); }
        .modal-field { border: 1px solid rgba(148,163,184,0.35); background: #f8fafc; border-radius: 0.85rem; }
        .modal-field input { border: none; background: transparent; outline: none; }
        .modal-field select { border: 1px solid rgba(148,163,184,0.35); background: #ffffff; outline: none; border-radius: 0.5rem; }
        .modal-field label { color: #334155; }
        select option { background: #ffffff; color: #334155; padding: 8px 12px; }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.85rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f766e; color: #fff; border-radius: 1rem; box-shadow: 0 18px 50px rgba(15,23,42,0.18); padding: 0.85rem 1rem; font-size: 0.95rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f766e; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 1rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 1024px) { .shop-shelves { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 770px) {
            .shop-shelves { grid-template-columns: 1fr; }
            .map-unit { min-height: 200px; }
        }
    </style>

    <div class="space-y-6 max-w-screen-2xl mx-auto w-full">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

        <!-- Header Block -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Shop Inventory Items</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-800 border border-cyan-200">POS Retail</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">Track and manage products across shop shelves for active point-of-sale transactions.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 relative z-[100000001]">
                <button id="add-shelf-button" type="button" class="px-4 py-2.5 rounded-xl bg-cyan-600 text-white text-sm font-semibold hover:bg-cyan-700 transition shadow-sm flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]" onclick="openAddShelfModal()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Shelf
                </button>
                <button id="transfer-from-warehouse" type="button" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Transfer from Warehouse
                </button>
                <button id="view-history" type="button" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition shadow-sm flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    History Logs
                </button>
                <a href="{{ route('shop.inventory.archived') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition shadow-sm flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archive List
                </a>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between si-card-glow">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Products</span>
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center text-cyan-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p id="total-products" class="text-2xl font-extrabold text-slate-900 leading-none">{{ $totalProducts ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">Total items across shop shelves</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between si-card-glow">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Shelves</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p id="total-shelves" class="text-2xl font-extrabold text-slate-900 leading-none">{{ $totalShelves ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">Configured POS retail shelves</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between si-card-glow">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">POS Readiness</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-extrabold text-emerald-600 leading-none">Active</p>
                    <p class="text-xs text-slate-400 mt-1">Live barcode & POS scanner sync</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm flex flex-col justify-between si-card-glow">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Quick Transfer</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-2xl font-extrabold text-slate-900 leading-none">Warehouse</p>
                    <p class="text-xs text-slate-400 mt-1">Direct stock restock channel</p>
                </div>
            </div>
        </div>

        <!-- Unified Search & Filter Toolbar -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-3">
            <div class="flex flex-col md:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <svg class="absolute left-3 top.5 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="search-input" class="w-full pl-9 pr-4 py-2 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 transition" placeholder="Search products, shelves, or sections...">
                </div>

                <div class="flex flex-wrap md:flex-nowrap items-center gap-2.5 w-full md:w-auto">
                    <select id="product-description-filter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 bg-white text-slate-700">
                        <option value="">All Descriptions</option>
                        @php
                            $descriptions = \App\Models\ProductDescription::where('is_active', true)->orderBy('name')->get();
                            foreach($descriptions as $desc):
                        @endphp
                            <option value="{{ $desc->name }}">{{ $desc->name }}</option>
                        @php endforeach; @endphp
                    </select>

                    <select id="brand-filter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 bg-white text-slate-700">
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

                    <select id="section-filter" class="px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 bg-white text-slate-700">
                        <option value="">All Sections</option>
                    </select>

                    <button id="clear-search" class="px-3.5 py-2 text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition whitespace-nowrap flex items-center gap-1.5" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Shop Shelves Grid / Empty State -->
        <div class="mt-2">
            @if($shelves->count() > 0)
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold bg-gradient-to-r from-teal-600 to-cyan-600 shadow-sm text-base">S</div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Shop Retail Layout</h2>
                                <p class="text-xs text-slate-500">Active shop shelves configured for retail POS transactions</p>
                            </div>
                        </div>
                    </div>

                    <div class="shop-shelves grid gap-6" id="shop-shelves-grid">
                        @foreach($shelves as $shelf)
                        <div class="map-unit shelf-card bg-slate-50/50 border border-slate-200 rounded-2xl p-4 transition hover:border-cyan-200 hover:shadow-md">
                            <div class="flex items-start justify-between mb-4 pb-3 border-b border-slate-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold bg-gradient-to-br from-cyan-600 to-teal-700 shadow-sm text-sm">
                                        {{ strtoupper(substr($shelf->name, -1)) }}
                                    </div>
                                    <div>
                                        <div class="text-base font-bold text-slate-900 shelf-name">{{ $shelf->name }}</div>
                                        <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="shelf-location font-medium text-slate-600">{{ $shelf->location ?? 'Shop Floor' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative">
                                    <select class="action-select text-xs font-semibold text-slate-700 px-3.5 py-1.5 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 cursor-pointer shadow-xs focus:ring-2 focus:ring-cyan-500 transition" onchange="handleShelfAction(this, {{ $shelf->id }})">
                                        <option value="">Actions ▾</option>
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
                                    <div class="grid grid-cols-2 gap-2.5">
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
                                                $expiryColor = $p?->expiry_status === 'expired' ? 'text-red-600' : ($p?->expiry_status === 'expiring' ? 'text-amber-600' : 'text-slate-600');
                                            @endphp
                                            <div class="product-chip">
                                                <div class="chip-header">
                                                    <div class="chip-desc truncate" title="{{ $desc }}">{{ $desc }}</div>
                                                </div>
                                                <div class="chip-body">
                                                    <div class="chip-row"><span class="chip-label">Brand:</span><span class="chip-value truncate">{{ $brand }}</span></div>
                                                    <div class="chip-row"><span class="chip-label">Compatible:</span><span class="chip-value truncate">{{ $compatible }}</span></div>
                                                    <div class="chip-row"><span class="chip-label">SKU:</span><span class="chip-value sku truncate">{{ $sku }}</span></div>
                                                    <div class="chip-row"><span class="chip-label">Price:</span><span class="chip-value price">{{ $priceFormatted }}</span></div>
                                                    <div class="chip-row"><span class="chip-label">Qty:</span><span class="chip-value qty">{{ $qty }}</span></div>
                                                    <div class="chip-row"><span class="chip-label">Expiry:</span><span class="chip-value {{ $expiryColor }}">{{ $expiry }}</span></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($remainingProducts > 0)
                                    <p class="text-xs font-medium text-slate-400 mt-2.5">+{{ $remainingProducts }} more products on shelf</p>
                                    @endif
                                @else
                                    <div class="py-8 text-center bg-white rounded-xl border border-dashed border-slate-200">
                                        <p class="text-xs font-medium text-slate-400">No products assigned to this shelf</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination Controls -->
                    <div id="pagination-controls" class="pagination mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-600 hidden">
                        <button type="button" id="prev-page" class="px-3.5 py-1.5 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition text-xs font-semibold" disabled>Previous</button>
                        <div class="text-xs font-medium">Page <span id="current-page">1</span> of <span id="total-pages">1</span></div>
                        <button type="button" id="next-page" class="px-3.5 py-1.5 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition text-xs font-semibold">Next</button>
                    </div>
                </div>
            @else
                <!-- Redesigned Empty State -->
                <div id="no-shelves-message" class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-50 to-emerald-50 border border-cyan-100 flex items-center justify-center mx-auto mb-4 text-cyan-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">No Shop Shelves Configured</h3>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">You haven't set up any shop shelves yet. Create your first shelf or transfer products from the warehouse to start managing POS retail inventory.</p>
                    
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" class="px-4 py-2.5 rounded-xl bg-cyan-600 text-white text-sm font-semibold hover:bg-cyan-700 transition shadow-sm flex items-center gap-2" onclick="openAddShelfModal()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add First Shelf
                        </button>
                        <button type="button" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm flex items-center gap-2" onclick="document.getElementById('transfer-from-warehouse').click()">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Transfer from Warehouse
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Transfer from Warehouse Modal -->
    <div id="transfer-warehouse-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="modal-panel p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Transfer from Warehouse to Shop</h2>
            <form id="transfer-warehouse-form" class="space-y-4">
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Destination Shop Shelf</label>
                    <select name="shop_shelf_id" required class="w-full px-4 py-3">
                        <option value="">Select a shelf</option>
                    </select>
                </div>
                <div class="space-y-3">
                    <label class="text-sm font-medium">Products to Transfer</label>

                    <!-- Shelf and Product Selection -->
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Warehouse</label>
                            <select id="warehouse-select" class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Select warehouse</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Warehouse Shelf</label>
                            <select id="warehouse-shelf-select" class="w-full px-3 py-2 border rounded-lg text-sm" disabled>
                                <option value="">Select shelf</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Product</label>
                            <select id="warehouse-product-select" class="w-full px-3 py-2 border rounded-lg text-sm" disabled>
                                <option value="">Select product</option>
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-gray-500 mb-1">Quantity</label>
                            <input type="number" id="transfer-quantity" class="w-full px-3 py-2 border rounded-lg text-sm" min="1" value="1" disabled>
                        </div>
                        <div class="flex items-end">
                            <button type="button" id="add-to-transfer" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Selected Products List -->
                    <div id="selected-products-list" class="space-y-2 mt-3">
                        <!-- Selected products will be shown here -->
                    </div>

                    <p id="no-products-selected" class="text-gray-500 text-sm text-center py-4">No products selected for transfer</p>
                </div>
                <div class="modal-actions flex justify-end gap-3">
                    <button type="button" id="cancel-transfer-warehouse" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Transfer Products</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Between Shelves Modal -->
    <div id="transfer-shelves-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="modal-panel p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Transfer Between Shop Shelves</h2>
            <form id="transfer-shelves-form" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="modal-field">
                        <label class="block text-sm font-medium mb-2">Source Shelf</label>
                        <select name="source_shelf_id" required class="w-full px-4 py-3">
                            <option value="">Select source shelf</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label class="block text-sm font-medium mb-2">Destination Shelf</label>
                        <select name="destination_shelf_id" required class="w-full px-4 py-3">
                            <option value="">Select destination shelf</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-sm font-medium">Products to Transfer</label>
                    <div id="source-shelf-products-container" class="space-y-2">
                        <!-- Source shelf products will be loaded here -->
                    </div>
                </div>
                <div class="modal-actions flex justify-end gap-3">
                    <button type="button" id="cancel-transfer-shelves" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Transfer Products</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Return to Warehouse Modal -->
    <div id="return-warehouse-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="modal-panel p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Return Products to Warehouse</h2>
            <div id="return-shelf-source-label" class="mb-5 px-3 py-2 bg-slate-100 border border-slate-300 rounded-lg text-xs font-semibold text-slate-600 tracking-wide uppercase"></div>
            <form id="return-warehouse-form">
                <input type="hidden" id="return-shelf-id" name="shelf_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Destination Warehouse Shelf</label>
                    <select id="return-warehouse-shelf-select" name="warehouse_shelf_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; background: white; color: black; font-size: 14px;">
                        <option value="">Select warehouse shelf</option>
                    </select>
                </div>
                <div class="space-y-3 mb-4">
                    <label class="text-sm font-medium">Products to Return</label>
                    <div id="return-shelf-products-container" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Shelf products will be loaded here -->
                    </div>
                </div>
                <div class="modal-actions flex justify-end gap-3">
                    <button type="button" id="cancel-return-warehouse" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Return Products</button>
                </div>
            </form>
        </div>
    </div>

    <!-- History Modal -->
    <div id="history-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
        <div class="modal-panel p-8">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Shop Inventory History</h2>

            <!-- Date Filter -->
            <div class="flex gap-3 mb-4">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Date Range</label>
                    <select id="history-date-range" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Action Type</label>
                    <select id="history-action-type" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Actions</option>
                        <option value="transfer_in">Transfer In (from Warehouse)</option>
                        <option value="return_to_warehouse">Return to Warehouse</option>
                        <option value="updated">Product Updated</option>
                        <option value="pos_sale">POS Sale</option>
                    </select>
                </div>
                <div id="custom-date-range" class="hidden flex gap-3 flex-1">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">From</label>
                        <input type="date" id="history-from-date" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-1">To</label>
                        <input type="date" id="history-to-date" class="w-full px-3 py-2 border rounded-lg text-sm">
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="button" id="apply-date-filter" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700">Filter</button>
                </div>
            </div>

            <div id="history-container" class="space-y-3 max-h-96 overflow-y-auto">
                <!-- History will be loaded here -->
            </div>
            <div class="modal-actions flex justify-end gap-3 mt-6">
                <button type="button" id="close-history" class="modal-footer-button secondary">Close</button>
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
                            <label class="block text-xs font-medium text-slate-700">Product Description</label>
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
                            <button type="button" class="remove-product-row text-sm font-semibold text-red-500 hover:text-red-700">Remove</button>
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

        async function loadShopSections() {
            try {
                const response = await fetch('/api/shop-inventory/shop-sections', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const locationSelect = document.getElementById('add-shelf-location');
                locationSelect.innerHTML = '<option value="">Select location</option>';

                if (data.success && data.sections && data.sections.length > 0) {
                    let hasAvailable = false;
                    data.sections.forEach(section => {
                        if (section.available > 0) {
                            hasAvailable = true;
                        }
                        const option = document.createElement('option');
                        option.value = section.name;
                        if (section.available === 0) {
                            option.textContent = `${section.name} (Full - ${section.shelf_count}/${section.max} shelves)`;
                            option.disabled = true;
                        } else {
                            option.textContent = `${section.name} (${section.available} available shelves - ${section.shelf_count}/${section.max})`;
                        }
                        locationSelect.appendChild(option);
                    });

                    // Add "Add Section" option if no available sections
                    if (!hasAvailable) {
                        const addSectionOption = document.createElement('option');
                        addSectionOption.value = 'ADD_NEW_SECTION';
                        addSectionOption.textContent = '+ Add New Section';
                        addSectionOption.style.fontWeight = 'bold';
                        addSectionOption.style.color = '#059669';
                        locationSelect.appendChild(addSectionOption);
                    }
                }

                // Always add "Add Section" option at the end
                const addSectionOption = document.createElement('option');
                addSectionOption.value = 'ADD_NEW_SECTION';
                addSectionOption.textContent = '+ Add New Section';
                addSectionOption.style.fontWeight = 'bold';
                addSectionOption.style.color = '#059669';
                locationSelect.appendChild(addSectionOption);
            } catch (error) {
                console.error('Error loading shop sections:', error);
            }
        }

        function closeAddShelfModal() {
            document.getElementById('add-shelf-modal-backdrop').classList.add('hidden');
            document.getElementById('add-shelf-modal-backdrop').classList.remove('flex');
            document.getElementById('add-shelf-modal-form').reset();
        }

        async function loadSectionsForFilter() {
            try {
                const response = await fetch('/api/shop-inventory/shop-sections', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                const sectionFilter = document.getElementById('section-filter');
                sectionFilter.innerHTML = '<option value="">All Sections</option>';

                if (data.success && data.sections && data.sections.length > 0) {
                    data.sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.name;
                        option.textContent = section.name;
                        sectionFilter.appendChild(option);
                    });
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
            row.className = 'flex items-center gap-2 bg-slate-50 rounded-lg p-3';
            row.innerHTML = `
                <div class="flex items-center gap-1 flex-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">PRODUCT NAME:</label>
                    <input type="text" class="add-shelf-product-name flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter product name">
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">SKU:</label>
                    <div class="add-shelf-sku-display text-xs font-mono font-bold text-slate-700 min-w-[100px]">KCC_</div>
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">QTY:</label>
                    <input type="number" class="add-shelf-product-qty w-16 px-2 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="1" value="1">
                </div>
                <div class="flex items-center gap-1">
                    <label class="text-xs font-semibold text-slate-600 whitespace-nowrap">PRICE:</label>
                    <input type="number" class="add-shelf-product-price w-20 px-2 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" min="0" step="0.01">
                </div>
                <button type="button" onclick="this.parentElement.remove(); updateAddShelfProductButton();" class="text-red-500 hover:text-red-700 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                shelfSelect.innerHTML = '<option value="">Select a shelf</option>';
                shelves.forEach(shelf => {
                    shelfSelect.innerHTML += `<option value="${shelf.id}">${shelf.name} (${shelf.location || 'No location'})</option>`;
                });

                // Load warehouse shelves
                const warehouseResponse = await fetch('/api/shop-inventory/warehouse-shelves', {
                    headers: { 'Accept': 'application/json' }
                });
                const warehouseData = await warehouseResponse.json();

                // Populate warehouse dropdown
                const warehouseSelect = document.getElementById('warehouse-select');
                warehouseSelect.innerHTML = '<option value="">Select warehouse</option>';
                warehouseData.data.forEach(warehouse => {
                    warehouseSelect.innerHTML += `<option value="${warehouse.id}">${warehouse.name}</option>`;
                });

                // Store warehouse data for later use
                window.warehouseData = warehouseData.data;

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
                document.getElementById('add-to-transfer').disabled = true;

                document.getElementById('transfer-warehouse-modal').classList.remove('hidden');
                document.getElementById('transfer-warehouse-modal').classList.add('flex');
            } catch (error) {
                console.error('Error loading transfer data:', error);
                alert('Error loading transfer data: ' + error.message);
            }
        }

        // Handle warehouse selection
        document.getElementById('warehouse-select').addEventListener('change', function() {
            const warehouseId = parseInt(this.value);
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

            if (!warehouseId && warehouseId !== 0) {
                return;
            }

            // Populate shelf dropdown for selected warehouse
            const warehouse = window.warehouseData.find(w => w.id === warehouseId);
            if (warehouse && warehouse.shelves) {
                shelfSelect.innerHTML = '<option value="">Select shelf</option>';
                warehouse.shelves.forEach(shelf => {
                    const products = JSON.parse(shelf.products || '[]');
                    const hasProducts = products && products.length > 0;
                    if (hasProducts) {
                        shelfSelect.innerHTML += `<option value="${shelf.id}">${shelf.name} (${products.length} products)</option>`;
                    }
                });
                shelfSelect.disabled = false;
            }
        });

        // Handle warehouse shelf selection
        document.getElementById('warehouse-shelf-select').addEventListener('change', function() {
            const shelfId = this.value;
            const productSelect = document.getElementById('warehouse-product-select');
            const quantityInput = document.getElementById('transfer-quantity');
            const addButton = document.getElementById('add-to-transfer');

            if (!shelfId) {
                productSelect.innerHTML = '<option value="">Select product</option>';
                productSelect.disabled = true;
                quantityInput.disabled = true;
                quantityInput.value = 1;
                addButton.disabled = true;
                return;
            }

            // Find the shelf in warehouse data
            let selectedShelf = null;
            window.warehouseData.forEach(warehouse => {
                const shelf = warehouse.shelves.find(s => s.id === parseInt(shelfId));
                if (shelf) selectedShelf = shelf;
            });

            if (selectedShelf) {
                const products = JSON.parse(selectedShelf.products || '[]');
                productSelect.innerHTML = '<option value="">Select product</option>';
                products.forEach(product => {
                    productSelect.innerHTML += `<option value="${product.sku}" data-qty="${product.qty}" data-name="${product.name}" data-shelf-id="${selectedShelf.id}">${product.name} (${product.sku}) - Qty: ${product.qty}</option>`;
                });
                productSelect.disabled = false;
            }

            quantityInput.disabled = true;
            quantityInput.value = 1;
            addButton.disabled = true;
        });

        // Handle product selection
        document.getElementById('warehouse-product-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxQty = selectedOption.dataset.qty;
            const quantityInput = document.getElementById('transfer-quantity');
            const addButton = document.getElementById('add-to-transfer');

            if (!this.value) {
                quantityInput.disabled = true;
                addButton.disabled = true;
                return;
            }

            quantityInput.max = maxQty;
            quantityInput.value = 1;
            quantityInput.disabled = false;
            addButton.disabled = false;
        });

        // Handle add to transfer
        document.getElementById('add-to-transfer').addEventListener('click', function() {
            const productSelect = document.getElementById('warehouse-product-select');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const quantityInput = document.getElementById('transfer-quantity');

            const product = {
                id: productSelect.value,
                name: selectedOption.dataset.name,
                sku: productSelect.value,
                quantity: parseInt(quantityInput.value),
                warehouse_shelf_id: selectedOption.dataset.shelfId
            };

            // Check if product already in list
            const existingIndex = selectedTransferProducts.findIndex(p => p.id === product.id);
            if (existingIndex >= 0) {
                selectedTransferProducts[existingIndex].quantity += product.quantity;
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
                productRow.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
                productRow.innerHTML = `
                    <div class="flex-1">
                        <p class="font-medium text-sm">${product.name}</p>
                        <p class="text-xs text-gray-500">SKU: ${product.sku} | Qty: ${product.quantity}</p>
                    </div>
                    <button type="button" onclick="removeFromTransfer(${index})" class="text-red-600 hover:text-red-700 text-sm">Remove</button>
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

                console.log('Fetching history with URL:', url);

                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const history = await response.json();

                console.log('History response:', history);

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
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
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
                alert('Error loading history: ' + error.message);
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

            if (!shopShelfId) {
                alert('Please select a destination shelf');
                return;
            }

            if (selectedTransferProducts.length === 0) {
                alert('Please select at least one product to transfer');
                return;
            }

            const transfers = selectedTransferProducts.map(product => ({
                product_id: product.id,
                product_name: product.name,
                warehouse_shelf_id: product.warehouse_shelf_id,
                quantity: product.quantity
            }));

            const payload = {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                shop_shelf_id: shopShelfId,
                transfers: transfers
            };

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
                    showToast(data.message || 'Transfer successful', 'success');
                    closeTransferWarehouseModal();
                    window.location.reload();
                } else {
                    showToast(data.message || 'Transfer failed', 'error');
                }
            } catch (error) {
                showToast('Error transferring products', 'error');
            }
        }
    </script>

    <script src="/js/shop_inventory.js"></script>

    <!-- Add Shelf Modal -->
    <div id="add-shelf-modal-backdrop" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-[100000002] px-4 py-8">
        <div class="modal-panel p-6 max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-slate-900">Add Shelf</h2>
                <button id="add-shelf-modal-close" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition hover:bg-slate-200">✕</button>
            </div>

            <form id="add-shelf-modal-form" class="space-y-6">
                <div class="space-y-4">
                    <div class="modal-field p-4">
                        <label class="block text-sm font-semibold text-slate-800 mb-2">Shelf Name</label>
                        <input id="add-shelf-name" type="text" class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300" placeholder="Enter shelf name" required />
                    </div>

                    <div class="modal-field p-4">
                        <label class="block text-sm font-semibold text-slate-800 mb-2">Location</label>
                        <select id="add-shelf-location" class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300 bg-white">
                            <option value="">Select location</option>
                        </select>
                        <input type="text" id="add-shelf-new-section" class="hidden block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300 mt-2" placeholder="Enter new section name" />
                    </div>

                    <div class="modal-field p-4">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Shelf Capacity</label>
                        <input id="add-shelf-capacity" type="number" min="1" value="10"
                               class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="e.g. 10" />
                        <p class="text-xs text-slate-400 mt-1">💡 You can increase this to allow more products per shelf. Default is 10.</p>
                    </div>

                    <div class="modal-field p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Products</p>
                                <p class="text-xs text-slate-500 mt-1">Enter product name, quantity, and price. SKU will auto-generate.</p>
                            </div>
                            <button type="button" id="add-shelf-product-row" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add product</button>
                        </div>
                        <div id="add-shelf-product-rows" class="grid gap-3 max-h-[540px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="modal-actions flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" id="add-shelf-modal-cancel" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Save shelf</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Shelf Modal -->
    <div id="edit-modal-backdrop" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50 px-4 py-8">
        <div class="modal-panel p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 id="edit-modal-title" class="text-2xl font-semibold text-slate-900">Edit Shelf</h2>
                <button id="edit-modal-close" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition hover:bg-slate-200">✕</button>
            </div>

            <form id="edit-modal-form" class="space-y-6">
                <input type="hidden" id="edit-shelf-id" />

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Shelf Name</label>
                            <input id="edit-shelf-name" type="text" class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter shelf name" required />
                        </div>
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Location</label>
                            <input id="edit-shelf-location" type="text" class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Enter location" />
                        </div>
                    </div>

                    <div class="modal-field p-4">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">Shelf Capacity</label>
                        <input id="edit-shelf-capacity" type="number" min="1" value="10"
                               class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="e.g. 10" />
                        <p class="text-xs text-slate-400 mt-1">💡 You can increase this to allow more products per shelf.</p>
                    </div>

                    <div class="modal-field p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Products</p>
                                <p class="text-xs text-slate-500 mt-1">Choose existing inventory items, quantity, and price before saving.</p>
                            </div>
                            <button type="button" id="edit-add-product-row" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add product</button>
                        </div>
                        <div id="edit-product-rows" class="grid gap-3 max-h-[400px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="modal-actions flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" id="edit-modal-cancel" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
