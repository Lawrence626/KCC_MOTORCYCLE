<x-layouts.app :title="__('Inventory Monitoring')">
    <style>
        .dashboard-card { transition: all 0.3s ease; }
        .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
        .table-row-hover:hover { background-color: #f8fafc; }
        .sticky-header { position: sticky; top: 0; z-index: 10; }
        .sticky-first-col { position: sticky; left: 0; z-index: 5; }
        .progress-bar { height: 6px; border-radius: 3px; background-color: #e2e8f0; overflow: hidden; }
        .progress-fill { height: 100%; transition: width 0.3s ease; }
        .action-dropdown { position: relative; }
        .qr-thumbnail { width: 36px; height: 36px; object-fit: contain; }
        .product-image { width: 36px; height: 36px; object-fit: cover; border-radius: 6px; }
    </style>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Inventory Monitoring</h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time tracking of inventory operations, stock availability, and warehouse records.</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="addStockBtn" type="button" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#6EC1D1] text-slate-900 text-xs font-semibold hover:bg-[#59b2c2] transition shadow-sm cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Add Stock</span>
                </button>
                <div class="action-dropdown inline-block relative" data-dropdown-wrapper="moreActions">
                    <button type="button" onclick="toggleDropdown('moreActionsMenu', event)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-50 transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <span>More Actions</span>
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="moreActionsMenu" class="dropdown-menu hidden absolute right-0 top-full z-[50] mt-1 min-w-[160px] rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                        <button id="generateQrBtn" type="button" onclick="document.getElementById('moreActionsMenu').classList.add('hidden')" class="w-full px-2 py-1.5 text-left text-[11px] text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-semibold cursor-pointer">Generate QR Codes</button>
                        <button id="exportBtn" type="button" onclick="document.getElementById('moreActionsMenu').classList.add('hidden')" class="w-full px-2 py-1.5 text-left text-[11px] text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-semibold cursor-pointer">Export Inventory</button>
                        <a href="{{ route('archived') }}" onclick="document.getElementById('moreActionsMenu').classList.add('hidden')" class="block w-full px-2 py-1.5 text-left text-[11px] text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-semibold cursor-pointer">Archived Items</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Total Products</p>
                        <div class="mt-1">
                            <p id="stat-total-products" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Total loaded products</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="text-[#145a66]" style="width: 1.125rem; height: 1.125rem;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Inventory Value</p>
                        <div class="mt-1">
                            <p id="stat-total-value" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Total inventory worth</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Low Stock</p>
                        <div class="mt-1">
                            <p id="stat-low-stock" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Items below safe level</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M16 18l2.29-2.29-4.88-4.88-4 4L2 7.41 3.41 6l6 6 4-4 6.3 6.29L22 12v6z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Expiring Soon</p>
                        <div class="mt-1">
                            <p id="stat-expiring-soon" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Within 30 days</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.47 21h15.06c1.54 0 2.5-1.67 1.73-3L13.73 4.99c-.77-1.33-2.69-1.33-3.46 0L2.74 18c-.77 1.33.19 3 1.73 3zM13 18h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unified Filter Toolbar -->
        <div class="rounded-[10px] border border-slate-200 bg-white p-3 shadow-sm flex flex-col space-y-2 relative z-[30]">
            <!-- Primary Row -->
            <div class="flex flex-col lg:flex-row gap-2 relative z-[20]">
                <!-- Search -->
                <div class="relative flex-grow lg:w-[220px]">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="searchInput" type="search" placeholder="Search product, SKU, barcode, compatibility..." class="w-full pl-9 pr-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm h-10"/>
                </div>
                
                <div class="flex flex-wrap gap-2 lg:flex-nowrap">
                    <!-- Warehouse / Location Filter -->
                    <div class="relative z-[10] w-full lg:w-[130px]" data-dropdown-wrapper="warehouseFilter">
                        <input type="hidden" id="warehouseFilter" value="" />
                        <button type="button" id="warehouseFilterButton" onclick="toggleDropdown('warehouseFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                            <span>All Locations</span>
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="warehouseFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdownOption('warehouseFilter', '', 'All Locations', 'warehouseFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Locations</button>
                            <button type="button" onclick="selectDropdownOption('warehouseFilter', 'Shop', 'Shop', 'warehouseFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Shop</button>
                            <button type="button" onclick="selectDropdownOption('warehouseFilter', 'Warehouse A', 'Warehouse A', 'warehouseFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Warehouse A</button>
                            <button type="button" onclick="selectDropdownOption('warehouseFilter', 'Warehouse B', 'Warehouse B', 'warehouseFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Warehouse B</button>
                            <button type="button" onclick="selectDropdownOption('warehouseFilter', 'Warehouse C', 'Warehouse C', 'warehouseFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Warehouse C</button>
                            <button type="button" onclick="selectDropdownOption('warehouseFilter', 'Warehouse D', 'Warehouse D', 'warehouseFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Warehouse D</button>
                        </div>
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="relative z-[10] w-full lg:w-[140px]" data-dropdown-wrapper="categoryFilter">
                        <input type="hidden" id="categoryFilter" value="" />
                        <button type="button" id="categoryFilterButton" onclick="toggleDropdown('categoryFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                            <span class="truncate">All Categories</span>
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="categoryFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-56 max-h-60 overflow-y-auto rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdownOption('categoryFilter', '', 'All Categories', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Categories</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Exhaust', 'Exhaust', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Exhaust</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Helmets', 'Helmets', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Helmets</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Tires', 'Tires', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Tires</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Brakes', 'Brakes', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Brakes</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Oils', 'Oils', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Oils</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Batteries', 'Batteries', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Batteries</button>
                            <button type="button" onclick="selectDropdownOption('categoryFilter', 'Accessories', 'Accessories', 'categoryFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Accessories</button>
                        </div>
                    </div>

                    <!-- Product Name Filter -->
                    <div class="flex gap-1 w-full lg:w-[170px]">
                        <div class="relative z-[10] flex-1" data-dropdown-wrapper="productNameFilter">
                            <input type="hidden" id="productNameFilter" value="" />
                            <button type="button" id="productNameFilterButton" onclick="toggleDropdown('productNameFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                                <span class="truncate">All Products</span>
                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="productNameFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-60 max-h-60 overflow-y-auto rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                                <button type="button" onclick="selectDropdownOption('productNameFilter', '', 'All Products', 'productNameFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Products</button>
                            </div>
                        </div>
                        <button type="button" id="addNewProductDescBtn" class="shrink-0 px-2.5 rounded-[12px] bg-slate-100 border border-slate-300 text-slate-600 text-xs font-medium hover:bg-slate-200 transition h-10 flex items-center justify-center shadow-sm cursor-pointer" title="Add New Product Category">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    <!-- Brand Filter -->
                    <div class="relative z-[10] w-full lg:w-[120px]" data-dropdown-wrapper="brandFilter">
                        <input type="hidden" id="brandFilter" value="" />
                        <button type="button" id="brandFilterButton" onclick="toggleDropdown('brandFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                            <span class="truncate">All Brands</span>
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="brandFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-48 max-h-60 overflow-y-auto rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdownOption('brandFilter', '', 'All Brands', 'brandFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Brands</button>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="relative z-[10] w-full lg:w-[135px]" data-dropdown-wrapper="statusFilter">
                        <input type="hidden" id="statusFilter" value="" />
                        <button type="button" id="statusFilterButton" onclick="toggleDropdown('statusFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                            <span class="truncate">All Status</span>
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="statusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-full min-w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdownOption('statusFilter', '', 'All Status', 'statusFilterDropdown', event)" class="w-full px-3 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition whitespace-nowrap">All Status</button>
                            <button type="button" onclick="selectDropdownOption('statusFilter', 'active', 'Available', 'statusFilterDropdown', event)" class="w-full px-3 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition whitespace-nowrap">Available</button>
                            <button type="button" onclick="selectDropdownOption('statusFilter', 'low', 'Low Stock', 'statusFilterDropdown', event)" class="w-full px-3 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition whitespace-nowrap">Low Stock</button>
                            <button type="button" onclick="selectDropdownOption('statusFilter', 'out', 'Out of Stock', 'statusFilterDropdown', event)" class="w-full px-3 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition whitespace-nowrap">Out of Stock</button>
                        </div>
                    </div>

                    <button type="button" onclick="event.stopPropagation(); document.getElementById('advancedFiltersPanel').classList.toggle('hidden')" class="px-3 py-2 rounded-[12px] border border-slate-300 text-slate-700 text-xs font-medium hover:bg-slate-50 transition h-10 flex items-center gap-1.5 bg-white whitespace-nowrap shadow-sm cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filters
                    </button>
                    
                    <button type="button" onclick="resetFilters()" class="px-3 py-2 rounded-[12px] border border-slate-300 text-slate-500 text-xs hover:text-slate-800 hover:bg-slate-50 transition h-10 flex items-center justify-center bg-white shadow-sm whitespace-nowrap cursor-pointer" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Advanced Filters Panel -->
            <div id="advancedFiltersPanel" class="hidden pt-2 border-t border-slate-100 relative z-[1]">
                <div class="flex flex-wrap gap-2">
                    <!-- Expiry Status Filter -->
                    <div class="relative z-[10] flex-1 min-w-[150px]" data-dropdown-wrapper="expiryStatusFilter">
                        <input type="hidden" id="expiryStatusFilter" value="" />
                        <button type="button" id="expiryStatusFilterButton" onclick="toggleDropdown('expiryStatusFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                            <span>All Expiry</span>
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="expiryStatusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdownOption('expiryStatusFilter', '', 'All Expiry', 'expiryStatusFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Expiry</button>
                            <button type="button" onclick="selectDropdownOption('expiryStatusFilter', 'expiring', 'Expiring Soon', 'expiryStatusFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Expiring Soon</button>
                            <button type="button" onclick="selectDropdownOption('expiryStatusFilter', 'expired', 'Expired', 'expiryStatusFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Expired</button>
                            <button type="button" onclick="selectDropdownOption('expiryStatusFilter', 'non_expiring', 'Non-expiring', 'expiryStatusFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Non-expiring</button>
                        </div>
                    </div>

                    <!-- Size Filter -->
                    <div class="relative z-[10] flex-1 min-w-[150px]" data-dropdown-wrapper="sizeFilter">
                        <input type="hidden" id="sizeFilter" value="" />
                        <button type="button" id="sizeFilterButton" onclick="toggleDropdown('sizeFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm h-10">
                            <span>All Sizes</span>
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="sizeFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-full max-h-60 overflow-y-auto rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdownOption('sizeFilter', '', 'All Sizes', 'sizeFilterDropdown', event)" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Sizes</button>
                        </div>
                    </div>

                    <!-- Date of Stock Filter -->
                    <div class="flex-1 min-w-[170px] flex items-center gap-2">
                        <label class="text-xs text-slate-500 font-medium whitespace-nowrap">Stock Date</label>
                        <input type="text" id="dateOfStockFilter" placeholder="mm/dd/yyyy" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm h-10 bg-white cursor-pointer" />
                    </div>

                    <!-- Restock Date Filter -->
                    <div class="flex-1 min-w-[170px] flex items-center gap-2">
                        <label class="text-xs text-slate-500 font-medium whitespace-nowrap">Restock Date</label>
                        <input type="text" id="restockDateFilter" placeholder="mm/dd/yyyy" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm h-10 bg-white cursor-pointer" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Toolbar -->
        <div id="bulkActionsToolbar" class="hidden bg-[#6EC1D1]/15 border border-[#6EC1D1]/40 rounded-xl p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xs sm:text-sm font-semibold text-[#145a66]"><span id="selectedCount">0</span> products selected</span>
                    <button type="button" onclick="clearSelection()" class="text-xs text-[#145a66] hover:text-[#0f172a] underline cursor-pointer">Clear selection</button>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openQRCodeModal()" class="px-3 py-1.5 rounded-lg bg-[#105f68] text-white text-xs font-semibold hover:bg-[#0d4f56] transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Generate QR Codes</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="overflow-hidden rounded-[14px] border border-slate-200 bg-white shadow-[0_14px_40px_-24px_rgba(0,0,0,0.32)]">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left text-[11px] divide-y divide-slate-200 whitespace-nowrap min-w-max">
                    <thead class="border-b border-slate-200 bg-[#0f172a] text-[10px] uppercase tracking-wider text-white sticky-header">
                        <tr>
                            <th class="w-10 px-3 py-3 text-center font-semibold text-white sticky-first-col bg-[#0f172a]">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1] cursor-pointer" />
                            </th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="Product Name and Compatibility">Product / Compatibility</th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="SKU & Brand">SKU & Brand</th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="Category">Category</th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="Warehouse Location">Location</th>
                            <th class="px-3 py-3 text-center font-semibold text-white" title="Stock Quantity & Safe Level">Stock Level</th>
                            <th class="px-3 py-3 text-right font-semibold text-white" title="Unit Price">Unit Price</th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="Supplier">Supplier</th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="Last Restock Date">Restock</th>
                            <th class="px-3 py-3 text-left font-semibold text-white" title="Expiration Date">Expiry</th>
                            <th class="px-3 py-3 text-center font-semibold text-white" title="Stock Status">Status</th>
                            <th class="px-3 py-3 text-center font-semibold text-white" title="Actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-[11px]" id="productsTableBody">
                        <tr>
                            <td colspan="12" class="px-4 py-12 text-center text-slate-500">Loading inventory...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between border-t border-slate-200 bg-slate-50/70 px-4 py-3 gap-3 text-xs">
                <p id="paginationInfo" class="text-slate-600 font-medium">Showing 0 of 0 items</p>
                <div id="paginationControls" class="flex items-center gap-1.5">
                    <button class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</button>
                </div>
            </div>
        </div>

        <!-- Legend & Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="rounded-[14px] border border-slate-200 bg-white p-3 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Stock Status Legend</h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-emerald-600 px-2 py-0.5 text-[11px] font-semibold text-white">Available</span>
                        <span class="text-slate-600">Healthy stock level</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-orange-500 px-2 py-0.5 text-[11px] font-semibold text-white">Low Stock</span>
                        <span class="text-slate-600">Below safe reorder level</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-semibold text-white">No Stock</span>
                        <span class="text-slate-600">Out of stock, immediate reorder needed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-purple-600 px-2 py-0.5 text-[11px] font-semibold text-white">Overstock</span>
                        <span class="text-slate-600">Exceeds recommended storage capacity</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[14px] border border-slate-200 bg-white p-3 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Expiry Tracking</h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#105f68]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Non-Expiring:</strong> Tracked without expiration date</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Expiring Soon:</strong> Expires within 30 days</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Expired:</strong> Removal and item disposal flagged</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Inventory Movements -->
        <div class="mt-4 rounded-[14px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 bg-white rounded-t-[14px] px-4 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Inventory Movements</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Latest stock changes, restocks, and price updates.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative" data-dropdown-wrapper="movementDateFilter">
                        <input type="hidden" id="movementDateFilter" value="" />
                        <button type="button" id="movementDateFilterButton" onclick="toggleDropdown('movementDateFilterDropdown', event)" class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-900 flex items-center justify-between gap-2 min-w-[130px] hover:border-slate-400 focus:outline-none transition shadow-sm cursor-pointer">
                            <span>All Time</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="movementDateFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 right-0 z-[99999] mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1.5 text-slate-900 space-y-0.5">
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', '', 'All Time', 'movementDateFilterDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">All Time</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'today', 'Today', 'movementDateFilterDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Today</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'yesterday', 'Yesterday', 'movementDateFilterDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Yesterday</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'last_7_days', 'Last 7 Days', 'movementDateFilterDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Last 7 Days</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'last_30_days', 'Last 30 Days', 'movementDateFilterDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Last 30 Days</button>
                        </div>
                    </div>
                    <button type="button" id="refreshMovementsBtn" class="rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-black hover:bg-[#59b2c2] transition shadow-sm cursor-pointer inline-flex items-center justify-center min-w-[100px]">Refresh</button>
                </div>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="w-full text-xs text-left divide-y divide-slate-200 whitespace-nowrap min-w-max">
                    <thead class="bg-[#0f172a] text-xs font-semibold uppercase tracking-wider text-white border-b border-slate-200">
                        <tr>
                            <th class="w-[18%] px-3 py-3 text-left font-semibold text-white">Date</th>
                            <th class="w-[32%] px-3 py-3 text-left font-semibold text-white">Product</th>
                            <th class="w-[15%] px-3 py-3 text-left font-semibold text-white">Type</th>
                            <th class="w-[10%] px-3 py-3 text-center font-semibold text-white">Qty</th>
                            <th class="w-[25%] px-3 py-3 text-left font-semibold text-white">Details</th>
                        </tr>
                    </thead>
                    <tbody id="movementFeed" class="bg-white divide-y divide-slate-100">
                        <tr>
                            <td colspan="5" class="px-3.5 py-8 text-center text-slate-500">Loading recent movements...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Movements Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between border-t border-slate-200 bg-slate-50/70 px-4 py-3 gap-3 text-xs">
                <p id="movementPaginationInfo" class="text-slate-600 font-medium">Showing 0 of 0 movements</p>
                <div id="movementPaginationControls" class="flex items-center gap-1.5">
                    <button type="button" class="movement-prev rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>← Prev</button>
                    <button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                    <button type="button" class="movement-next rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition" disabled>Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div id="addStockModal" class="fixed inset-0 z-[100000] hidden flex items-center justify-center px-4 py-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" id="closeAddStockModalBackdrop" onclick="closeAddStockModal()"></div>
        <div class="relative w-full max-w-2xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Add Stock</h2>
                    <p class="text-sm text-slate-900 font-medium">Update your inventory with new stock.</p>
                </div>
                <button type="button" id="closeAddStockModal" onclick="closeAddStockModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(90vh-120px)]">
                <form id="addStockForm" class="space-y-4">
                    @csrf
                    <!-- Product Select Dropdown Card -->
                    <div class="space-y-1 relative z-[50]" data-dropdown-wrapper="addStockProduct">
                        <label class="block text-xs font-medium text-slate-700">Product <span class="text-red-500">*</span></label>
                        <select id="productSelect" name="product_id" class="hidden" required>
                            <option value="">Select a product...</option>
                        </select>
                        <button type="button" id="addStockProductButton" onclick="toggleDropdown('addStockProductDropdown', event)" class="mt-1 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm">
                            <span id="addStockProductDisplay">Select a product...</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="addStockProductDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full max-h-60 overflow-y-auto rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            <div class="p-3 text-left text-xs text-slate-500">Loading products...</div>
                        </div>
                    </div>

                    <!-- Quantity Input & Unit Price -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" id="quantityInput" name="quantity" min="1" value="1" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Price</label>
                            <input type="number" id="unitPriceInput" name="unit_price" min="0" step="0.01" placeholder="Optional" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                    </div>

                    <!-- Supplier Name -->
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Supplier</label>
                        <input type="text" id="supplierInput" name="supplier_name" placeholder="Optional" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                    </div>

                    <!-- Notes -->
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Notes</label>
                        <textarea id="notesInput" name="notes" rows="2" placeholder="Add any additional notes..." class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 resize-none"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end">
                        <button type="button" id="cancelAddStock" onclick="closeAddStockModal()" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200 cursor-pointer">Cancel</button>
                        <button type="submit" id="submitAddStock" class="inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Add Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="hidden fixed inset-0 z-[100000] flex items-center justify-center px-4 py-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeEditProductModal()"></div>
        <div class="relative w-full max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[95vh] overflow-y-auto z-10">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Edit Product</h2>
                    <p class="text-sm text-slate-900 font-medium">Update all product details.</p>
                </div>
                <button type="button" id="closeEditProductModal" onclick="closeEditProductModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(95vh-100px)]">
                <form id="editProductForm" autocomplete="off">
                    @csrf
                    <input type="hidden" id="editProductId" />
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Motorcycle Compatibility</label>
                            <input name="name" id="editName" required class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Product Name</label>
                            <input name="product_name" id="editProductName" required class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">SKU</label>
                            <input name="sku" id="editSku" required class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Brand</label>
                            <input name="brand" id="editBrand" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Size</label>
                            <input name="size" id="editSize" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Color</label>
                            <input name="color" id="editColor" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Stock Quantity</label>
                            <input name="stock_quantity" id="editStockQuantity" type="number" required class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Unit Price</label>
                            <input name="unit_price" id="editUnitPrice" type="number" step="0.01" required class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <!-- Suppliers Multi-Select Dropdown -->
                        <div class="space-y-1 relative z-[100]" data-dropdown-wrapper="editSuppliers">
                            <label class="block text-xs font-medium text-slate-700">Suppliers</label>
                            <input type="hidden" name="supplier_name" id="editSupplier" value="" />
                            <button type="button" id="editSuppliersButton" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm h-9 cursor-pointer transition">
                                <span id="editSuppliersDisplay" class="truncate text-slate-400">Select suppliers...</span>
                                <svg id="editSuppliersArrow" class="w-4 h-4 text-slate-500 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="editSuppliersDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[100] mt-1.5 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-2 space-y-1 max-h-56 overflow-y-auto">
                                <div id="editSuppliersList" class="space-y-0.5">
                                    <!-- Supplier checkboxes dynamically loaded here -->
                                </div>
                            </div>
                        </div>
                        <div class="space-y-1 relative z-[90]" data-dropdown-wrapper="editProductCategory">
                            <label class="block text-xs font-medium text-slate-700">Category</label>
                            <input type="hidden" name="category" id="editCategory" value="" />
                            <button type="button" id="editCategoryButton" onclick="toggleDropdown('editCategoryDropdown', event)" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm h-9 cursor-pointer">
                                <span>Select category</span>
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="editCategoryDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[100] mt-1 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-1.5 max-h-[200px] overflow-y-auto">
                                <button type="button" onclick="selectDropdownOption('editCategory', '', 'Select category', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Select category</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Exhaust', 'Exhaust', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Exhaust</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Helmets', 'Helmets', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Helmets</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Tires', 'Tires', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Tires</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Brakes', 'Brakes', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Brakes</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Oils', 'Oils', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Oils</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Batteries', 'Batteries', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Batteries</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'Accessories', 'Accessories', 'editCategoryDropdown', event)" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Accessories</button>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Last Restock Date</label>
                            <input type="text" name="last_restock_date" id="editLastRestock" placeholder="mm/dd/yyyy" readonly class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm cursor-pointer hover:border-slate-400 focus:outline-none transition" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Expiry Date</label>
                            <input type="text" name="expiry_date" id="editExpiryDate" placeholder="mm/dd/yyyy" readonly class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm cursor-pointer hover:border-slate-400 focus:outline-none transition" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Reorder Level</label>
                            <input type="number" name="reorder_level" id="editReorderLevel" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Barcode</label>
                            <input name="barcode" id="editBarcode" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <label class="block text-xs font-medium text-slate-700">Description</label>
                            <textarea name="description" id="editDescription" rows="3" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end">
                        <button type="button" id="cancelEditProduct" onclick="closeEditProductModal()" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200 cursor-pointer">Cancel</button>
                        <button type="submit" id="submitEditProduct" class="inline-flex items-center justify-center rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 cursor-pointer">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add New Product Category Modal -->
    <div id="addProductDescModal" class="hidden fixed inset-0 z-[10000] flex items-center justify-center px-4 py-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" id="closeProductDescOverlay" onclick="document.getElementById('addProductDescModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-[28px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] z-10">
            <div class="flex items-center justify-between bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h3 class="text-xl font-bold text-black">Add New Product Category</h3>
                </div>
                <button type="button" id="closeProductDescModal" onclick="document.getElementById('addProductDescModal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="addProductDescForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Product Category Name <span class="text-red-500">*</span></label>
                    <input type="text" id="newProductDescName" name="name" placeholder="e.g., Brake Pads" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Brand <span class="text-red-500">*</span></label>
                    <input type="text" id="newProductDescBrand" name="brand" placeholder="e.g., Bosch" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                </div>
                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" id="cancelProductDesc" onclick="document.getElementById('addProductDescModal').classList.add('hidden')" class="flex-1 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all cursor-pointer">Cancel</button>
                    <button type="submit" class="flex-1 rounded-[10px] bg-[#6EC1D1] px-4 py-2 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all cursor-pointer">Add Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Code Generation Modal -->
    <div id="qrCodeModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
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
                    <button type="button" onclick="document.getElementById('qrCodeModal').style.display='none'" class="text-white/80 hover:text-white transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Selection Info -->
                <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-4 border border-slate-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-emerald-100 rounded-lg p-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600">Selected Products</p>
                                <p class="text-2xl font-bold text-slate-900"><span id="qrSelectedCount">0</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Preview -->
                <div id="qrCodePreview" class="space-y-4">
                    <div id="qrLoading" class="hidden text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div>
                        <p class="text-sm text-slate-600">Generating QR codes...</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button type="button" onclick="printQRCodes()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print QR Codes
                    </button>
                    <button type="button" onclick="downloadQRCodes()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Image
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="viewDetailsModal" class="fixed inset-0 z-[100000] hidden flex items-center justify-center px-4 py-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('viewDetailsModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-2xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto z-10">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black" id="vdProductTitle">Product Details</h2>
                    <p class="text-sm text-slate-900 font-medium">View detailed product specifications.</p>
                </div>
                <button type="button" onclick="document.getElementById('viewDetailsModal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body Content -->
            <div class="p-6">
                <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-xs">
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">SKU</span>
                        <span class="text-slate-900 font-semibold font-mono" id="vdSku"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Barcode</span>
                        <div id="vdBarcodeContainer" class="mt-1">
                            <svg id="vdBarcode"></svg>
                            <span class="text-slate-900 font-semibold font-mono hidden" id="vdBarcodeText"></span>
                        </div>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Brand</span>
                        <span class="text-slate-900 font-semibold" id="vdBrand"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Supplier</span>
                        <span class="text-slate-900 font-semibold" id="vdSupplier"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Size</span>
                        <span class="text-slate-900 font-semibold" id="vdSize"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Color</span>
                        <span class="text-slate-900 font-semibold" id="vdColor"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Compatible Models</span>
                        <span class="text-slate-900 font-semibold" id="vdCompatibleModels"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Reorder Level</span>
                        <span class="text-slate-900 font-semibold" id="vdReorderLevel"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">VAT details</span>
                        <span class="text-slate-900 font-semibold" id="vdVat"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date of Stock</span>
                        <span class="text-slate-900 font-semibold" id="vdDateOfStock"></span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Expiration Date</span>
                        <span class="text-slate-900 font-semibold" id="vdExpirationDate"></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="document.getElementById('viewDetailsModal').classList.add('hidden')" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200 cursor-pointer">Close</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 transform translate-x-full transition-transform duration-300 z-50">
        <div class="bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-4 flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p id="toastMessage" class="text-sm font-medium text-slate-900"></p>
            </div>
        </div>
    </div>

    @php
        $activeSuppliersList = \App\Models\Supplier::orderBy('name')
            ->where(function ($query) {
                $query->where('status', 'active')->orWhereNull('status');
            })
            ->get(['id', 'name']);
        if ($activeSuppliersList->isEmpty()) {
            $activeSuppliersList = \App\Models\Supplier::orderBy('name')->get(['id', 'name']);
        }
    @endphp

    <script>
        window.AllStocks = {
            suppliers: @json($activeSuppliersList),
            routes: {
                apiProducts: '{{ route("api.products") }}',
                apiProductShowBase: '{{ url("api/products") }}',
                apiProductDescriptions: '{{ url("api/product-descriptions") }}',
                apiSuppliers: '{{ route("api.suppliers") }}',
                stockAdd: '{{ route("stock.add") }}',
                apiStats: '{{ route("api.stats") }}',
                apiMovements: '{{ route("api.movements") }}',
                stockExport: '{{ route("stock.export") }}',
                stockImport: '{{ route("stock.import") }}',
                productUpdateBase: '{{ url('product') }}'
            },
            baseUrl: '{{ url("") }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[data-dropdown-wrapper]')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
                document.querySelectorAll('.custom-calendar-card').forEach(c => c.classList.add('hidden'));
            }
        });

        // Custom Dropdown Helper functions
        function toggleDropdown(id, event) {
            if (event) {
                if (event.preventDefault) event.preventDefault();
                if (event.stopPropagation) event.stopPropagation();
            }

            let dropdown = document.getElementById(id);
            if (!dropdown) dropdown = document.getElementById('dropdown-' + id);
            if (!dropdown) return;
            
            // Close all custom calendar cards
            document.querySelectorAll('.custom-calendar-card').forEach(card => card.classList.add('hidden'));

            const currentWrapper = dropdown.closest('[data-dropdown-wrapper]');

            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.add('hidden');
                    const w = menu.closest('[data-dropdown-wrapper]');
                    if (w) w.style.zIndex = '';
                }
            });
            
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                if (currentWrapper) currentWrapper.style.zIndex = '100';
            } else {
                if (currentWrapper) currentWrapper.style.zIndex = '';
            }
        }

        function selectDropdownOption(inputId, value, label, dropdownId, event) {
            if (event) {
                if (event.preventDefault) event.preventDefault();
                if (event.stopPropagation) event.stopPropagation();
            }
            const input = document.getElementById(inputId);
            if (input) {
                input.value = value;
                const wrapper = input.closest('[data-dropdown-wrapper]');
                if (wrapper) {
                    const btn = wrapper.querySelector('button span');
                    if (btn) btn.textContent = label;
                }
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const display = document.getElementById('addStockProductDisplay');
            if (display && inputId === 'productSelect') {
                display.textContent = label;
            }
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.add('hidden');
                const wrapper = dropdown.closest('[data-dropdown-wrapper]');
                if (wrapper) wrapper.style.zIndex = '';
            }
        }

        // Custom Date Picker Setup
        function setupCustomDatePicker(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.type = 'text';
            input.readOnly = true;
            input.placeholder = 'mm/dd/yyyy';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative w-full mt-0 z-[40]';
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
            card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-1 z-[999] w-full min-w-[220px] rounded-[12px] bg-white p-2 shadow-[0_12px_32px_rgba(0,0,0,0.15)] border border-slate-100 transition-all duration-200';
            wrapper.appendChild(card);

            if (input.value && input.value.includes('T')) {
                input.value = input.value.split('T')[0];
            }

            let currentDate = new Date();
            let selectedDate = input.value ? new Date(input.value) : null;
            let viewMode = 'days';

            function render() {
                if (viewMode === 'days') renderDaysView();
                else renderMonthsView();
            }

            function renderDaysView() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();

                let html = `
                    <div class="flex items-center justify-between mb-1 px-0.5">
                        <button type="button" class="toggle-view-btn text-[12px] font-bold text-slate-900 hover:text-slate-700 inline-flex items-center gap-1 px-1 py-0.5 rounded hover:bg-slate-100 transition">
                            <span>${monthNames[month]} ${year}</span>
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="flex items-center gap-0.5">
                            <button type="button" class="prev-month-btn p-0.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Previous Month">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button type="button" class="next-month-btn p-0.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Next Month">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 text-center mb-0.5 text-[10px] font-semibold text-slate-400">
                        <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 text-center text-[11px]">
                `;

                for (let i = firstDay - 1; i >= 0; i--) {
                    html += `<span class="h-5 flex items-center justify-center text-slate-300 text-[10px]">${daysInPrevMonth - i}</span>`;
                }

                const today = new Date();
                for (let day = 1; day <= daysInMonth; day++) {
                    const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                    const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                    let dayClasses = "h-5 w-5 mx-auto flex items-center justify-center rounded font-medium cursor-pointer transition-all duration-150 text-[10px] ";
                    if (isSelected) {
                        dayClasses += "bg-[#6EC1D1] text-slate-900 font-bold shadow-xs";
                    } else if (isToday) {
                        dayClasses += "bg-[#6EC1D1]/25 text-slate-900 font-bold ring-1 ring-[#6EC1D1]";
                    } else {
                        dayClasses += "text-slate-700 hover:bg-slate-100";
                    }

                    html += `<button type="button" data-day="${day}" class="day-btn ${dayClasses}">${day}</button>`;
                }

                const totalSlots = firstDay + daysInMonth;
                const nextDays = (7 - (totalSlots % 7)) % 7;
                for (let i = 1; i <= nextDays; i++) {
                    html += `<span class="h-5 flex items-center justify-center text-slate-300 text-[10px]">${i}</span>`;
                }

                html += `
                    </div>
                    <div class="flex items-center justify-between mt-1 pt-1 border-t border-slate-100 text-[10px] font-semibold px-0.5">
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
                    <div class="flex items-center justify-between mb-1 pb-1 border-b border-slate-100 px-0.5">
                        <button type="button" class="prev-year-btn p-0.5 rounded-full text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-[12px] font-bold text-slate-900">${year}</span>
                        <button type="button" class="next-year-btn p-0.5 rounded-full text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-[10px]">
                `;

                shortMonths.forEach((m, idx) => {
                    const isSel = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === idx;
                    let mClasses = "py-1 rounded text-center font-semibold cursor-pointer transition-all duration-150 ";
                    if (isSel) {
                        mClasses += "bg-[#6EC1D1] text-black font-bold shadow-sm";
                    } else {
                        mClasses += "text-slate-700 hover:bg-slate-100";
                    }
                    html += `<button type="button" data-month="${idx}" class="month-btn ${mClasses}">${m}</button>`;
                });

                html += `
                    </div>
                    <div class="mt-1 text-right">
                        <button type="button" class="back-days-btn text-[10px] font-bold text-black hover:underline">Back to Days</button>
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
                if (input.value && input.value.includes('T')) {
                    input.value = input.value.split('T')[0];
                }
                document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
                document.querySelectorAll('.custom-calendar-card').forEach(c => {
                    if (c !== card) c.classList.add('hidden');
                });
                card.classList.toggle('hidden');
                const isOpen = !card.classList.contains('hidden');
                if (isOpen) render();
                setIconActive(isOpen);
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    card.classList.add('hidden');
                    setIconActive(false);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupCustomDatePicker('editLastRestock');
            setupCustomDatePicker('editExpiryDate');
            setupCustomDatePicker('dateOfStockFilter');
            setupCustomDatePicker('restockDateFilter');
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    @vite('resources/js/monitoring.js')
</x-layouts.app>
