<x-layouts.app :title="__('All Stocks')">
    <style>
        .dashboard-card { transition: all 0.3s ease; }
        .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); }
        .table-row-hover:hover { background-color: #f8fafc; }
        .sticky-header { position: sticky; top: 0; z-index: 10; }
        .sticky-first-col { position: sticky; left: 0; z-index: 5; }
        .progress-bar { height: 6px; border-radius: 3px; background-color: #e2e8f0; overflow: hidden; }
        .progress-fill { height: 100%; transition: width 0.3s ease; }
        .action-dropdown { position: relative; }
        .dropdown-menu { display: none; position: absolute; right: 0; top: 100%; z-index: 50; min-width: 160px; }
        .dropdown-menu.show { display: block; }
        .qr-thumbnail { width: 48px; height: 48px; object-fit: contain; }
        .product-image { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; }
        @media (max-width: 768px) {
            .table-responsive { display: none; }
            .mobile-cards { display: block; }
        }
        @media (min-width: 769px) {
            .table-responsive { display: block; }
            .mobile-cards { display: none; }
        }
    </style>

    <div class="space-y-4 max-w-screen-2xl mx-auto w-full">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">All Stocks</h1>
                <p class="text-sm text-slate-500 mt-0.5">Complete inventory overview with stock availability and warehouse information.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="action-dropdown inline-block relative">
                    <button onclick="document.getElementById('moreActionsMenu').classList.toggle('hidden')" class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition shadow-sm flex items-center gap-2">
                        More Actions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="moreActionsMenu" class="hidden absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-slate-200 z-50 py-1">
                        <button id="generateQrBtn" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">Generate QR Codes</button>
                        <button id="exportBtn" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">Export Inventory</button>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="{{ route('archived') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">Archived Items</a>
                    </div>
                </div>
                <button id="addStockBtn" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-semibold hover:bg-cyan-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Stock
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Products</p>
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                </div>
                <p id="stat-total-products" class="text-2xl font-bold text-slate-900 leading-none">--</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Inventory Value</p>
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p id="stat-total-value" class="text-2xl font-bold text-slate-900 leading-none">--</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Low Stock</p>
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77-1.333.192 3 1.732 3z"/></svg>
                </div>
                <p id="stat-low-stock" class="text-2xl font-bold text-slate-900 leading-none">--</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Expiring Soon</p>
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p id="stat-expiring-soon" class="text-2xl font-bold text-slate-900 leading-none">--</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Warehouse Dist.</p>
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-medium mt-1">
                    <div class="flex items-center gap-1" title="Shop"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span id="stat-shop" class="text-slate-700">--</span></div>
                    <div class="flex items-center gap-1" title="Warehouse A"><span class="w-2 h-2 rounded-full bg-green-500"></span><span id="stat-warehouse-a" class="text-slate-700">--</span></div>
                    <div class="flex items-center gap-1" title="Warehouse B"><span class="w-2 h-2 rounded-full bg-yellow-500"></span><span id="stat-warehouse-b" class="text-slate-700">--</span></div>
                    <div class="flex items-center gap-1" title="Warehouse C"><span class="w-2 h-2 rounded-full bg-purple-500"></span><span id="stat-warehouse-c" class="text-slate-700">--</span></div>
                </div>
            </div>
        </div>

        <!-- Unified Filter Toolbar -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm flex flex-col p-2 space-y-2">
            <!-- Primary Row -->
            <div class="flex flex-col lg:flex-row gap-2">
                <!-- Search -->
                <div class="relative flex-grow lg:w-[200px]">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="searchInput" type="search" placeholder="Search product, SKU, barcode..." class="w-full pl-9 pr-3 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 placeholder-slate-400 bg-slate-50 hover:bg-white transition h-9"/>
                </div>
                
                <div class="flex flex-wrap gap-2 lg:flex-nowrap">
                    <select id="warehouseFilter" class="px-2 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white w-full lg:w-[120px] h-9">
                        <option value="">All Locations</option>
                        <option value="Shop">Shop</option>
                        <option value="Warehouse A">Warehouse A</option>
                        <option value="Warehouse B">Warehouse B</option>
                        <option value="Warehouse C">Warehouse C</option>
                    </select>
                    
                    <select id="categoryFilter" class="px-2 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white w-full lg:w-[130px] h-9">
                        <option value="">All Categories</option>
                        <option value="Tires & Wheels">Tires & Wheels</option>
                        <option value="Brakes">Brakes</option>
                        <option value="Engine & Transmission">Engine & Transmission</option>
                        <option value="Suspension">Suspension</option>
                        <option value="Electrical">Electrical</option>
                        <option value="Exhaust">Exhaust</option>
                        <option value="Cooling System">Cooling System</option>
                        <option value="Body Parts">Body Parts</option>
                        <option value="Controls (Levers, Clutch, etc.)">Controls (Levers, Clutch, etc.)</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Helmets & Safety Gear">Helmets & Safety Gear</option>
                        <option value="Oils & Lubricants">Oils & Lubricants</option>
                    </select>

                    <div class="flex gap-1 w-full lg:w-[160px]">
                        <select id="productNameFilter" class="flex-1 px-2 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white w-full h-9">
                            <option value="">All Products</option>
                        </select>
                        <button type="button" id="addNewProductDescBtn" class="shrink-0 px-2 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-600 text-xs font-medium hover:bg-slate-200 transition h-9" title="Add New Product Description">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    <select id="brandFilter" class="px-3 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white w-full lg:w-[110px] h-9">
                        <option value="">All Brands</option>
                    </select>

                    <select id="statusFilter" class="px-2 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white w-full lg:w-[110px] h-9">
                        <option value="">All Statuses</option>
                        <option value="active">Available</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>

                    <button onclick="document.getElementById('advancedFiltersPanel').classList.toggle('hidden')" class="px-3 py-2 rounded-md border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition h-9 flex items-center gap-1 bg-white whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filters
                    </button>
                    
                    <button onclick="resetFilters()" class="px-3 py-2 rounded-md text-slate-500 text-sm hover:text-slate-800 transition h-9 whitespace-nowrap" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Advanced Filters Panel -->
            <div id="advancedFiltersPanel" class="hidden pt-2 border-t border-slate-100">
                <div class="flex flex-wrap gap-2">
                    <div class="flex-1 min-w-[150px]">
                        <select id="expiryStatusFilter" class="w-full px-3 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white h-9">
                            <option value="">All Expiry</option>
                            <option value="expiring">Expiring Soon</option>
                            <option value="expired">Expired</option>
                            <option value="non_expiring">Non-expiring</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <select id="sizeFilter" class="w-full px-3 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white h-9">
                            <option value="">All Sizes</option>
                            <option value="190">190</option>
                            <option value="230">230</option>
                            <option value="260">260</option>
                            <option value="300">300</option>
                            <option value="305">305</option>
                            <option value="320">320</option>
                            <option value="330">330</option>
                            <option value="335">335</option>
                            <option value="365">365</option>
                            <option value="60/80 17">60/80 17</option>
                            <option value="70/80 14">70/80 14</option>
                            <option value="70/80 17">70/80 17</option>
                            <option value="70/90 12">70/90 12</option>
                            <option value="70/90 14">70/90 14</option>
                            <option value="70/90 17">70/90 17</option>
                            <option value="80/80 14">80/80 14</option>
                            <option value="80/80 17">80/80 17</option>
                            <option value="80/90 14">80/90 14</option>
                            <option value="80/90 17">80/90 17</option>
                            <option value="90/80 14">90/80 14</option>
                            <option value="90/80 17">90/80 17</option>
                            <option value="90/90 10">90/90 10</option>
                            <option value="90/90 14">90/90 14</option>
                            <option value="100/80 10">100/80 10</option>
                            <option value="100/80 14">100/80 14</option>
                            <option value="100/90 12">100/90 12</option>
                            <option value="110/70 13">110/70 13</option>
                            <option value="110/80 14">110/80 14</option>
                            <option value="120/70 12">120/70 12</option>
                            <option value="120/70 13">120/70 13</option>
                            <option value="120/70 17">120/70 17</option>
                            <option value="130/70 12">130/70 12</option>
                            <option value="130/70 13">130/70 13</option>
                            <option value="140/70 14">140/70 14</option>
                            <option value="140/70 17">140/70 17</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[150px] flex items-center gap-2">
                        <label class="text-xs text-slate-500 font-medium whitespace-nowrap">Date of Stock</label>
                        <input type="date" id="dateOfStockFilter" class="w-full px-3 py-2 rounded-md border border-slate-200 text-sm focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white h-9">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Toolbar -->
        <div id="bulkActionsToolbar" class="hidden bg-cyan-50 border border-cyan-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-cyan-900"><span id="selectedCount">0</span> products selected</span>
                    <button onclick="clearSelection()" class="text-sm text-cyan-600 hover:text-cyan-700">Clear selection</button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                        Bulk Edit
                    </button>
                    <button class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                        Adjust Price
                    </button>
                    <button class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                        Archive
                    </button>
                    <button class="px-4 py-2 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-medium hover:bg-red-100 transition">
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden w-full max-w-full">
            <div class="overflow-x-auto table-responsive w-full">
                <table class="w-full text-sm text-left whitespace-nowrap min-w-max">
                    <thead class="bg-slate-50 border-b border-slate-200 sticky-header text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold sticky-first-col bg-slate-50 w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            </th>
                            <th class="px-4 py-3 font-semibold">Product</th>
                            <th class="px-4 py-3 font-semibold">Category</th>
                            <th class="px-4 py-3 font-semibold">Location (Qty)</th>
                            <th class="px-4 py-3 font-semibold">Stock</th>
                            <th class="px-4 py-3 font-semibold">Price</th>
                            <th class="px-4 py-3 font-semibold text-center">Status</th>
                            <th class="px-4 py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white" id="productsTableBody">
                        <!-- Data will be loaded from JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm text-slate-600">
                    Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalItems">0</span> products
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-600">Rows per page:</span>
                        <select id="perPageSelect" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div id="paginationControls" class="flex gap-1">
                        <!-- Pagination buttons will be generated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Add Stock Modal -->
    <div id="addStockModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 md:mx-0 sm:max-w-lg md:max-w-2xl lg:max-w-3xl overflow-hidden transform transition-all max-h-[85vh]">
            <!-- Header with gradient -->
            <div class="px-6 py-6 bg-linear-to-r from-slate-900 via-slate-800 to-slate-900 relative">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-1">Add Stock</h2>
                        <p class="text-xs text-slate-300">Update your inventory with new stock</p>
                    </div>
                    <button id="closeAddStockModal" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <!-- Accent bar -->
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-cyan-500 via-cyan-400 to-cyan-500"></div>
            </div>

            <!-- Form Content -->
            <form id="addStockForm" class="space-y-5 p-6">
                @csrf

                <!-- Product Select -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" clip-rule="evenodd"/></svg>
                            Product
                        </span>
                    </label>
                    <select id="productSelect" name="product_id" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 placeholder-slate-400 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" required>
                        <option value="">Select a product...</option>
                    </select>
                </div>

                <!-- Quantity Input -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                Quantity
                            </span>
                        </label>
                        <input type="number" id="quantityInput" name="quantity" min="1" value="1" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" required />
                    </div>

                    <!-- Unit Price -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path d="M8.16 5a.75.75 0 00-.73.73v2.02H5a.75.75 0 000 1.5h2.43v2.02a.75.75 0 001.5 0V9.25h2.43a.75.75 0 000-1.5H9.66V5.73A.75.75 0 008.16 5z"/><path fill-rule="evenodd" d="M10 18A8 8 0 1 0 10 2a8 8 0 0 0 0 16zm0-1.5A6.5 6.5 0 1 0 10 3.5a6.5 6.5 0 0 0 0 13z" clip-rule="evenodd"/></svg>
                                Price
                            </span>
                        </label>
                        <input type="number" id="unitPriceInput" name="unit_price" min="0" step="0.01" placeholder="Optional" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 placeholder-slate-400 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                    </div>
                </div>

                <!-- Supplier Name -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10.5 1.5H5.75A2.25 2.25 0 003.5 3.75v12.5A2.25 2.25 0 005.75 18.5h8.5a2.25 2.25 0 002.25-2.25V6.5m-11-4v3m6-3v3m-6 2h6M3.5 13h13"/></svg>
                            Supplier
                        </span>
                    </label>
                    <input type="text" id="supplierInput" name="supplier_name" placeholder="Optional" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 placeholder-slate-400 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-slate-700">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-cyan-600" fill="currentColor" viewBox="0 0 20 20"><path d="M3.5 2.75A1.75 1.75 0 015.25 1h9.5a1.75 1.75 0 011.75 1.75v14.5a1.75 1.75 0 01-1.75 1.75h-9.5a1.75 1.75 0 01-1.75-1.75V2.75zm1.5 0v14.5c0 .138.112.25.25.25h9.5a.25.25 0 00.25-.25V2.75a.25.25 0 00-.25-.25h-9.5a.25.25 0 00-.25.25z"/></svg>
                            Notes
                        </span>
                    </label>
                    <textarea id="notesInput" name="notes" rows="2" placeholder="Add any additional notes..." class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 placeholder-slate-400 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300 resize-none"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" id="cancelAddStock" class="flex-1 px-4 py-2.5 rounded-lg border-2 border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition hover:border-slate-400">
                        Cancel
                    </button>
                    <button type="submit" id="submitAddStock" class="flex-1 px-4 py-2.5 rounded-lg bg-linear-to-r from-cyan-600 to-cyan-500 text-white text-sm font-semibold hover:from-cyan-700 hover:to-cyan-600 transition shadow-lg hover:shadow-cyan-600/30">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Add Stock
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-[9999]">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 md:mx-0 sm:max-w-lg md:max-w-3xl lg:max-w-4xl overflow-hidden transform transition-all max-h-[90vh] relative z-[10000]">
                <!-- Header with gradient -->
                <div class="px-6 py-6 bg-linear-to-r from-slate-900 via-slate-800 to-slate-900 relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white mb-1">Edit Product</h2>
                            <p class="text-xs text-slate-300">Update all product details</p>
                        </div>
                        <button id="closeEditProductModal" onclick="document.getElementById('editProductModal').classList.add('hidden')" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Accent bar -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-linear-to-r from-cyan-500 via-cyan-400 to-cyan-500"></div>
                </div>

                <!-- Form Content -->
                <form id="editProductForm" class="space-y-4 p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" id="editProductId" name="id" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Motorcycle Compatibility -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Motorcycle Compatibility</label>
                            <input type="text" id="editName" name="name" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" required />
                        </div>

                        <!-- Product Name -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Product Name</label>
                            <input type="text" id="editProductName" name="product_name" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- SKU -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">SKU</label>
                            <input type="text" id="editSku" name="sku" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Brand -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Brand</label>
                            <input type="text" id="editBrand" name="brand" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Size -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Size</label>
                            <input type="text" id="editSize" name="size" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Color -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Color</label>
                            <input type="text" id="editColor" name="color" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Stock Quantity -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Stock Quantity</label>
                            <input type="number" id="editStockQuantity" name="stock_quantity" min="0" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" required />
                        </div>

                        <!-- Unit Price -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Unit Price</label>
                            <input type="number" id="editUnitPrice" name="unit_price" min="0" step="0.01" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" required />
                        </div>

                        <!-- Supplier -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Supplier</label>
                            <input type="text" id="editSupplier" name="supplier_name" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Category</label>
                            <select id="editCategory" name="category" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300">
                                <option value="">Select category</option>
                                <option value="engine_oil">Engine Oil</option>
                                <option value="battery">Battery</option>
                                <option value="spark_plug">Spark Plug</option>
                                <option value="brake_pads">Brake Pads</option>
                                <option value="tires">Tires</option>
                                <option value="filters">Filters</option>
                                <option value="lubricants">Lubricants</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>

                        <!-- Last Restock Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Last Restock Date</label>
                            <input type="date" id="editLastRestock" name="last_restock_date" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Expiry Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Expiry Date</label>
                            <input type="date" id="editExpiryDate" name="expiry_date" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Reorder Level -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Reorder Level</label>
                            <input type="number" id="editReorderLevel" name="reorder_level" min="0" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>

                        <!-- Barcode -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Barcode</label>
                            <input type="text" id="editBarcode" name="barcode" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Description</label>
                        <textarea id="editDescription" name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300 resize-none"></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button type="button" id="cancelEditProduct" onclick="document.getElementById('editProductModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 rounded-lg border-2 border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition hover:border-slate-400 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" id="submitEditProduct" class="flex-1 px-4 py-2.5 rounded-lg bg-linear-to-r from-cyan-600 to-cyan-500 text-white text-sm font-semibold hover:from-cyan-700 hover:to-cyan-600 transition shadow-lg hover:shadow-cyan-600/30">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Save Changes
                            </span>
                        </button>
                    </div>
                </form>
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

    <!-- Add New Product Description Modal -->
    <div id="addProductDescModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 sm:max-w-md overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-500">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Add New Product Description</h3>
                        <button id="closeProductDescModal" class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <form id="addProductDescForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Product Description Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="newProductDescName" 
                               name="name" 
                               placeholder="e.g., Brake Pads" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Brand <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="newProductDescBrand" 
                               name="brand" 
                               placeholder="e.g., Bosch" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                               required>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" id="cancelProductDesc" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                            Add Description
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Code Generation Modal -->
    <div id="qrCodeModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
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
                    <button onclick="document.getElementById('qrCodeModal').style.display='none'" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
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
                    <!-- QR codes will be generated here -->
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button onclick="printQRCodes()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print QR Codes
                    </button>
                    <button onclick="downloadQRCodes()" class="flex-1 px-4 py-3 rounded-xl bg-[#105f68] text-white font-semibold hover:bg-[#0d4f56] transition shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Image
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="viewDetailsModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-[9999]">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h2 class="text-lg font-bold text-slate-900" id="vdProductTitle">Product Details</h2>
                    <button onclick="document.getElementById('viewDetailsModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-sm">
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">SKU</span>
                            <span class="text-slate-900 font-mono" id="vdSku"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Barcode</span>
                            <div id="vdBarcodeContainer" class="mt-1">
                                <svg id="vdBarcode"></svg>
                                <span class="text-slate-900 font-mono hidden" id="vdBarcodeText"></span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Brand</span>
                            <span class="text-slate-900" id="vdBrand"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Supplier</span>
                            <span class="text-slate-900" id="vdSupplier"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Size</span>
                            <span class="text-slate-900" id="vdSize"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Color</span>
                            <span class="text-slate-900" id="vdColor"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Compatible Models</span>
                            <span class="text-slate-900" id="vdCompatibleModels"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Reorder Level</span>
                            <span class="text-slate-900" id="vdReorderLevel"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">VAT details</span>
                            <span class="text-slate-900" id="vdVat"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Date of Stock</span>
                            <span class="text-slate-900" id="vdDateOfStock"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Expiration Date</span>
                            <span class="text-slate-900" id="vdExpirationDate"></span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button onclick="document.getElementById('viewDetailsModal').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-100 transition">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.AllStocks = {
            routes: {
                apiProducts: '{{ route("api.products") }}',
                apiProductShowBase: '{{ url("api/products") }}',
                apiProductDescriptions: '{{ url("api/product-descriptions") }}',
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
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    @vite('resources/js/allstocks.js')
</x-layouts.app>
