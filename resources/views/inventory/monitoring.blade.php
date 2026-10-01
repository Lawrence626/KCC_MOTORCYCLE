<x-layouts.app :title="__('Inventory Monitoring')">

    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Inventory Monitoring</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time tracking of inventory operations and movements</p>

        </div>
    </x-slot>
    <div class="space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
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

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Warehouse Dist.</p>
                        <div class="mt-1">
                            <p id="stat-warehouse-dist" class="text-2xl font-bold text-black">--</p>
                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-600">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                <span>0</span>
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                <span>0</span>
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                <span>0</span>
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-violet-500"></span>
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="rounded-[10px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                <div class="md:col-span-2">
                    <input id="searchInput" type="search" placeholder="Search by product name, SKU, brand, or category..." class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" />
                </div>

                <div class="relative z-[10]" data-dropdown-wrapper="statusFilter">
                    <input type="hidden" id="statusFilter" value="" />
                    <button type="button" id="statusFilterButton" onclick="toggleDropdown('statusFilterDropdown')" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                        <span>All Stock Status</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="statusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[20] mt-2 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                        <button type="button" onclick="selectDropdownOption('statusFilter', '', 'All Stock Status', 'statusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">All Stock Status</button>
                        <button type="button" onclick="selectDropdownOption('statusFilter', 'active', 'Active', 'statusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Active</button>
                        <button type="button" onclick="selectDropdownOption('statusFilter', 'low', 'Low Stock', 'statusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Low Stock</button>
                        <button type="button" onclick="selectDropdownOption('statusFilter', 'out', 'Out of Stock', 'statusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Out of Stock</button>
                    </div>
                </div>

                <div class="relative z-[10]" data-dropdown-wrapper="expiryStatusFilter">
                    <input type="hidden" id="expiryStatusFilter" value="" />
                    <button type="button" id="expiryStatusFilterButton" onclick="toggleDropdown('expiryStatusFilterDropdown')" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                        <span>All Expiry Status</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="expiryStatusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[20] mt-2 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                        <button type="button" onclick="selectDropdownOption('expiryStatusFilter', '', 'All Expiry Status', 'expiryStatusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">All Expiry Status</button>
                        <button type="button" onclick="selectDropdownOption('expiryStatusFilter', 'non_expiring', 'Non-expiring', 'expiryStatusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Non-expiring</button>
                        <button type="button" onclick="selectDropdownOption('expiryStatusFilter', 'expiring', 'Expiring Soon', 'expiryStatusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Expiring Soon</button>
                        <button type="button" onclick="selectDropdownOption('expiryStatusFilter', 'expired', 'Expired', 'expiryStatusFilterDropdown')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Expired</button>
                    </div>
                </div>

                <div>
                    <input id="restockDateFilter" type="date" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" />
                </div>
            </div>
        </div>
        <!-- Inventory Table -->
        <div class="overflow-hidden rounded-[14px] border border-slate-200 bg-white shadow-[0_14px_40px_-24px_rgba(0,0,0,0.32)]">
            <div class="w-full overflow-hidden rounded-[14px]">
                <table class="w-full text-left text-[11px] divide-y divide-slate-200 table-fixed">
                    <thead class="border-b border-slate-200 bg-[#0f172a] rounded-t-[14px] text-[10px] uppercase tracking-wider text-white">
                        <tr>
                            <th class="w-[3.5%] px-2 py-3 text-center font-semibold text-white rounded-tl-[14px]">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1] cursor-pointer" />
                            </th>
                            <th class="w-[15%] px-2.5 py-3 text-left font-semibold text-white truncate" title="Motorcycle Compatibility">Motorcycle Compatibility</th>
                            <th class="w-[10%] px-2 py-3 text-left font-semibold text-white truncate" title="Product Name">Product Name</th>
                            <th class="w-[12%] px-2 py-3 text-left font-semibold text-white truncate" title="SKU">SKU</th>
                            <th class="w-[8%] px-2 py-3 text-left font-semibold text-white truncate" title="Brand">Brand</th>
                            <th class="w-[5%] px-1.5 py-3 text-center font-semibold text-white truncate" title="Size">Size</th>
                            <th class="w-[5%] px-1.5 py-3 text-center font-semibold text-white truncate" title="Color">Color</th>
                            <th class="w-[6%] px-2 py-3 text-center font-semibold text-white truncate" title="Stock">Stock</th>
                            <th class="w-[9%] px-2 py-3 text-right font-semibold text-white truncate" title="Unit Price">Unit Price</th>
                            <th class="w-[11%] px-2 py-3 text-left font-semibold text-white truncate" title="Supplier">Supplier</th>
                            <th class="w-[8.5%] px-2 py-3 text-left font-semibold text-white truncate" title="Last Restock">Last Restock</th>
                            <th class="w-[9%] px-2 py-3 text-left font-semibold text-white truncate" title="Expiry">Expiry</th>
                            <th class="w-[8%] px-2 py-3 text-center font-semibold text-white rounded-tr-[14px] truncate" title="Actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-[11px]">
                        <tr>
                            <td colspan="13" class="px-4 py-8 text-center text-slate-500">Loading inventory...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50/80 px-3 py-2 text-xs">
                <p id="paginationInfo" class="text-slate-600 font-medium">Showing 0 of 0 items</p>
                <div id="paginationControls" class="flex gap-1">
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">2</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">3</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</button>
                </div>
            </div>
        </div>

        <!-- Legend & Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="rounded-[14px] border border-slate-200 bg-white p-3 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Stock Status Legend</h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-[#0f172a] px-2 py-0.5 text-[11px] font-semibold text-white">Active</span>
                        <span class="text-slate-600">Healthy stock</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-[#105f68] px-2 py-0.5 text-[11px] font-semibold text-white">Alert</span>
                        <span class="text-slate-600">Expiring soon</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-semibold text-white">Critical</span>
                        <span class="text-slate-600">Expired/Action</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-[#6EC1D1] px-2 py-0.5 text-[11px] font-semibold text-black">Low</span>
                        <span class="text-slate-600">Low stock</span>
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
                        <span class="text-slate-600"><strong>Non-Exp:</strong> Tracked without dates</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Alerts:</strong> 30 days prior</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Expired:</strong> Removal flagged</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 rounded-[14px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 bg-white rounded-t-[14px] px-4 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Inventory Movements</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Latest stock changes, restocks, and price updates.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative" data-dropdown-wrapper="movementDateFilter">
                        <input type="hidden" id="movementDateFilter" value="" />
                        <button type="button" id="movementDateFilterButton" onclick="toggleDropdown('movementDateFilterDropdown')" class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-900 flex items-center justify-between gap-2 min-w-[130px] hover:border-slate-400 focus:outline-none transition shadow-sm">
                            <span>All Time</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="movementDateFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 right-0 z-[99999] mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1.5 text-slate-900 space-y-0.5">
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', '', 'All Time', 'movementDateFilterDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">All Time</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'today', 'Today', 'movementDateFilterDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Today</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'yesterday', 'Yesterday', 'movementDateFilterDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Yesterday</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'last_7_days', 'Last 7 Days', 'movementDateFilterDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Last 7 Days</button>
                            <button type="button" onclick="selectDropdownOption('movementDateFilter', 'last_30_days', 'Last 30 Days', 'movementDateFilterDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition">Last 30 Days</button>
                        </div>
                    </div>
                    <button id="refreshMovementsBtn" class="rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-black hover:bg-[#59b2c2] transition shadow-sm cursor-pointer inline-flex items-center justify-center min-w-[130px]">Refresh</button>
                </div>
            </div>
            <div class="w-full overflow-hidden">
                <table class="w-full text-xs text-left divide-y divide-slate-200 table-fixed">
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
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500">Loading recent movements...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Movements Pagination -->
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                <p id="movementPaginationInfo" class="text-slate-600">Showing 0 of 0 movements</p>
                <div id="movementPaginationControls" class="flex gap-1">
                    <button class="movement-prev rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">← Prev</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">2</button>
                    <button class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">3</button>
                    <button class="movement-next rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4 py-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="const modal=document.getElementById('editProductModal'); if(modal){ modal.classList.add('hidden'); modal.style.display='none'; }"></div>
        <div class="relative w-full max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[95vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Edit Product</h2>
                    <p class="text-sm text-slate-900 font-medium">Update all product details.</p>
                </div>
                <button type="button" id="closeEditProductModal" onclick="event.preventDefault(); event.stopPropagation(); const modal=document.getElementById('editProductModal'); if(modal){ modal.classList.add('hidden'); modal.style.display='none'; }" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(95vh-100px)]">
                <form id="editProductForm" autocomplete="off">
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
                        <div class="space-y-1 relative" data-dropdown-wrapper="editProductCategory">
                            <label class="block text-xs font-medium text-slate-700">Category</label>
                            <input type="hidden" name="category" id="editCategory" value="" />
                            <button type="button" id="editCategoryButton" onclick="toggleDropdown('editCategoryDropdown')" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                                <span>Select category</span>
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="editCategoryDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[100] mt-1 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-1.5 max-h-[200px] overflow-y-auto">
                                <button type="button" onclick="selectDropdownOption('editCategory', '', 'Select category', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Select category</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'engine_oil', 'Engine Oil', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Engine Oil</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'lubricants', 'Lubricants', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Lubricants</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'battery', 'Battery', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Battery</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'spark_plug', 'Spark Plug', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Spark Plug</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'brake_pads', 'Brake Pads', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Brake Pads</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'tires', 'Tires', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Tires</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'filters', 'Filters', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Filters</button>
                                <button type="button" onclick="selectDropdownOption('editCategory', 'accessories', 'Accessories', 'editCategoryDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Accessories</button>
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
                        <button type="button" id="cancelEditProduct" onclick="event.preventDefault(); event.stopPropagation(); const modal=document.getElementById('editProductModal'); if(modal){ modal.classList.add('hidden'); modal.style.display='none'; }" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200 cursor-pointer">Cancel</button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 cursor-pointer">Save Changes</button>
                    </div>
                </form>
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
                apiSuppliers: '{{ route("api.suppliers") }}',
                apiStats: '{{ route("api.stats") }}',
                apiMovements: '{{ route("api.movements") }}',
                apiUpdatePriceBase: '{{ url("api/product") }}',
                productUpdateBase: '{{ url('product') }}'
            },
            baseUrl: '{{ url("") }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Custom Dropdown Helper functions
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (!dropdown) return;
            
            // Close all custom calendar cards
            document.querySelectorAll('.custom-calendar-card').forEach(card => card.classList.add('hidden'));

            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== id) {
                    menu.classList.add('hidden');
                    const btnId = menu.id.replace('Dropdown', 'Button');
                    const btn = document.getElementById(btnId);
                    if (btn) {
                        btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                    }
                }
            });
            
            dropdown.classList.toggle('hidden');
            const btnId = id.replace('Dropdown', 'Button');
            const btn = document.getElementById(btnId);
            if (btn) {
                if (dropdown.classList.contains('hidden')) {
                    btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                } else {
                    btn.classList.add('ring-1', 'ring-black/35', 'border-transparent');
                }
            }
        }

        function selectDropdownOption(inputId, value, label, dropdownId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(inputId + 'Button');
            const dropdown = document.getElementById(dropdownId);
            
            if (input) {
                input.value = value;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (button) {
                button.querySelector('span').textContent = label;
                button.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
            }
            if (dropdown) {
                // Remove highlight from all buttons in the dropdown
                const buttons = dropdown.querySelectorAll('button');
                buttons.forEach(btn => {
                    btn.classList.remove('bg-black/10', 'text-slate-900', 'font-semibold');
                    btn.classList.add('text-slate-700');
                });
                
                // Add highlight to the clicked button
                event.target.classList.add('bg-black/10', 'text-slate-900', 'font-semibold');
                event.target.classList.remove('text-slate-700');
                
                dropdown.classList.add('hidden');
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                    const btnId = menu.id.replace('Dropdown', 'Button');
                    const btn = document.getElementById(btnId);
                    if (btn) {
                        btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                    }
                });
            }
        });

        // Custom Date Picker Setup
        function setupCustomDatePicker(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.type = 'text';
            input.readOnly = true;
            input.placeholder = 'mm/dd/yyyy';
            input.className = 'w-full px-3 py-[11px] pr-10 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none cursor-pointer shadow-sm hover:border-slate-400 transition';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative w-full mt-0 z-[60]';
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
            card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-1 z-[90] w-full rounded-[14px] bg-white p-1.5 shadow-[0_16px_40px_rgba(0,0,0,0.12)] border border-slate-200 transition-all duration-200';
            wrapper.appendChild(card);

            if (input.value && input.value.includes('T')) {
                input.value = input.value.split('T')[0];
            }

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
                    <div class="flex items-center justify-between mb-1 px-0.5">
                        <button type="button" class="toggle-view-btn text-xs font-bold text-slate-900 hover:text-slate-700 inline-flex items-center gap-1 px-1 py-0.5 rounded-md hover:bg-slate-100 transition">
                            <span>${monthNames[month]} ${year}</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                    html += `<span class="h-5.5 flex items-center justify-center text-slate-300 text-[11px]">${daysInPrevMonth - i}</span>`;
                }

                const today = new Date();
                for (let day = 1; day <= daysInMonth; day++) {
                    const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                    const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                    let dayClasses = "h-5.5 w-5.5 mx-auto flex items-center justify-center rounded-md font-medium cursor-pointer transition-all duration-150 text-[11px] ";
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
                    html += `<span class="h-5.5 flex items-center justify-center text-slate-300 text-[11px]">${i}</span>`;
                }

                html += `
                    </div>
                    <div class="flex items-center justify-between mt-1 pt-1 border-t border-slate-100 text-[11px] font-semibold px-0.5">
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
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
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
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
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
                        input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                    });
                });
            }

            function renderMonthsView() {
                const year = currentDate.getFullYear();
                const shortMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                let html = `
                    <div class="flex items-center justify-between mb-1.5 pb-1.5 border-b border-slate-100 px-0.5">
                        <button type="button" class="prev-year-btn p-1 rounded-full text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-xs font-bold text-slate-900">${year}</span>
                        <button type="button" class="next-year-btn p-1 rounded-full text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-[11px]">
                `;

                shortMonths.forEach((m, idx) => {
                    const isSel = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === idx;
                    let mClasses = "py-1.5 rounded-lg text-center font-semibold cursor-pointer transition-all duration-150 ";
                    if (isSel) {
                        mClasses += "bg-[#6EC1D1] text-black font-bold shadow-md";
                    } else {
                        mClasses += "text-slate-700 hover:bg-slate-100";
                    }
                    html += `<button type="button" data-month="${idx}" class="month-btn ${mClasses}">${m}</button>`;
                });

                html += `
                    </div>
                    <div class="mt-1.5 text-right">
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
                // Close all dropdown menus
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                    const btnId = menu.id.replace('Dropdown', 'Button');
                    const btn = document.getElementById(btnId);
                    if (btn) {
                        btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                    }
                });
                document.querySelectorAll('.custom-calendar-card').forEach(c => {
                    if (c !== card) c.classList.add('hidden');
                });
                card.classList.toggle('hidden');
                const isOpen = !card.classList.contains('hidden');
                if (isOpen) {
                    render();
                    input.classList.add('ring-1', 'ring-black/35', 'border-transparent');
                } else {
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                }
                setIconActive(isOpen);
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    card.classList.add('hidden');
                    setIconActive(false);
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                }
            });
        }

        // Initialize Custom Date Picker
        setupCustomDatePicker('restockDateFilter');
        setupCustomDatePicker('editLastRestock');
        setupCustomDatePicker('editExpiryDate');
    </script>
    @vite('resources/js/monitoring.js')
</x-layouts.app>

