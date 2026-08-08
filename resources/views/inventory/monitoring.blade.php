<x-layouts.app :title="__('Inventory Monitoring')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Inventory Monitoring</h1>
                    <p class="mt-1 text-xs text-slate-500">Real-time tracking of inventory operations and movements</p>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Total Products</p>
                        <p id="stat-total-products" class="text-xl font-semibold text-slate-900">--</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Total loaded products</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7l9-4 9 4-9 4-9-4z" />
                            <path d="M3 12l9 4 9-4" />
                            <path d="M3 17l9 4 9-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Inventory Value</p>
                        <p id="stat-total-value" class="text-xl font-semibold text-slate-900">--</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Total inventory worth</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 1v22" />
                            <path d="M7 5.5a5 5 0 0 1 10 0v2a5 5 0 0 1-10 0v-2z" />
                            <path d="M12 20h0" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Low Stock</p>
                        <p id="stat-low-stock" class="text-xl font-semibold text-slate-900">--</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Items below safe level</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Expiring Soon</p>
                        <p id="stat-expiring-soon" class="text-xl font-semibold text-slate-900">--</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Within 30 days</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 3" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Warehouse Dist.</p>
                        <p id="stat-warehouse-dist" class="text-xl font-semibold text-slate-900">--</p>
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
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 22h16V8.5L12 3 4 8.5V22z" />
                            <path d="M9 22V12h6v10" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                <div class="md:col-span-2">
                    <input id="searchInput" type="search" placeholder="Search by product name, SKU, brand, or category..." class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent" />
                </div>

                <div>
                    <select id="statusFilter" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                        <option value="">All Stock Status</option>
                        <option value="active">Active</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>

                <div>
                    <select id="expiryStatusFilter" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                        <option value="">All Expiry Status</option>
                        <option value="non_expiring">Non-expiring</option>
                        <option value="expiring">Expiring Soon</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

                <div>
                    <input id="restockDateFilter" type="date" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent" />
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-[0_14px_40px_-24px_rgba(0,0,0,0.32)]">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="border-b border-slate-200 bg-[#0f172a]">
                        <tr>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white w-6">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2] cursor-pointer" />
                            </th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Motorcycle Compatibility</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product Name</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Brand</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Size</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Color</th>
                            <th class="px-3 py-2 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Stock</th>
                            <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Unit Price</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Supplier</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Last Restock</th>
                            <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Expiry</th>
                            <th class="px-3 py-2 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        <tr>
                            <td colspan="13" class="px-3 py-8 text-center text-slate-500">Loading inventory...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                <p id="paginationInfo" class="text-slate-600">Showing 0 of 0 items</p>
                <div id="paginationControls" class="flex gap-1">
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</button>
                    <button class="rounded-[10px] bg-slate-400 px-2 py-1 text-xs font-semibold text-slate-900 hover:bg-slate-500">1</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">2</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">3</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</button>
                </div>
            </div>
        </div>

        <!-- Legend & Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
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
                        <span class="inline-flex items-center rounded-full bg-[#00fff2] px-2 py-0.5 text-[11px] font-semibold text-black">Low</span>
                        <span class="text-slate-600">Low stock</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
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

        <div class="mt-4 overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-[#0f172a] px-4 py-4 text-white">
                <div>
                    <h2 class="text-sm font-semibold text-white">Recent Inventory Movements</h2>
                    <p class="mt-1 text-xs text-slate-200">Latest stock changes, restocks, and price updates.</p>
                </div>
                <div class="flex items-center gap-2">
                    <select id="movementDateFilter" class="rounded-[10px] border border-white/20 bg-white/10 px-3 py-1.5 text-xs text-white placeholder:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#00fff2]">
                        <option value="" class="text-slate-900">All Time</option>
                        <option value="today" class="text-slate-900">Today</option>
                        <option value="yesterday" class="text-slate-900">Yesterday</option>
                        <option value="last_7_days" class="text-slate-900">Last 7 Days</option>
                        <option value="last_30_days" class="text-slate-900">Last 30 Days</option>
                    </select>
                    <button id="refreshMovementsBtn" class="rounded-full border border-[#00fff2]/40 bg-[#00fff2] px-3 py-1 text-xs font-semibold text-black hover:bg-[#00e6da] transition">Refresh</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 uppercase tracking-[0.18em] text-slate-700">Date</th>
                            <th class="px-3 py-2 uppercase tracking-[0.18em] text-slate-700">Product</th>
                            <th class="px-3 py-2 uppercase tracking-[0.18em] text-slate-700">Type</th>
                            <th class="px-3 py-2 uppercase tracking-[0.18em] text-slate-700 text-center">Qty</th>
                            <th class="px-3 py-2 uppercase tracking-[0.18em] text-slate-700">Details</th>
                        </tr>
                    </thead>
                    <tbody id="movementFeed" class="bg-white">
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
                    <button class="rounded-[10px] bg-slate-400 px-2 py-1 text-xs font-semibold text-slate-900 hover:bg-slate-500">1</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">2</button>
                    <button class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">3</button>
                    <button class="movement-next rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-[9999] flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 md:mx-0 sm:max-w-lg md:max-w-3xl lg:max-w-4xl overflow-hidden transform transition-all max-h-[90vh] relative z-[10000]">
                <!-- Header -->
                <div class="px-6 py-6 bg-[#0f172a] relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white mb-1">Edit Product</h2>
                            <p class="text-xs text-slate-300">Update all product details</p>
                        </div>
                        <button id="closeEditProductModal" onclick="event.preventDefault(); event.stopPropagation(); const modal=document.getElementById('editProductModal'); if(modal){ modal.classList.add('hidden'); modal.style.display='none'; }" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="editProductForm" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <input type="hidden" id="editProductId" />
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Motorcycle Compatibility</label>
                            <input type="text" id="editName" name="name" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Product Name</label>
                            <input type="text" id="editProductName" name="product_name" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">SKU</label>
                            <input type="text" id="editSku" name="sku" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Brand</label>
                            <input type="text" id="editBrand" name="brand" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Size</label>
                            <input type="text" id="editSize" name="size" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Color</label>
                            <input type="text" id="editColor" name="color" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Stock Quantity</label>
                            <input type="number" id="editStockQuantity" name="stock_quantity" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Unit Price</label>
                            <input type="number" step="0.01" id="editUnitPrice" name="unit_price" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Supplier</label>
                            <input type="text" id="editSupplier" name="supplier_name" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Category</label>
                            <select id="editCategory" name="category" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300">
                                <option value="">Select category</option>
                                <option value="engine_oil">Engine Oil</option>
                                <option value="lubricants">Lubricants</option>
                                <option value="battery">Battery</option>
                                <option value="spark_plug">Spark Plug</option>
                                <option value="brake_pads">Brake Pads</option>
                                <option value="tires">Tires</option>
                                <option value="filters">Filters</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Last Restock Date</label>
                            <input type="date" id="editLastRestock" name="last_restock_date" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Expiry Date</label>
                            <input type="date" id="editExpiryDate" name="expiry_date" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Reorder Level</label>
                            <input type="number" id="editReorderLevel" name="reorder_level" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700">Barcode</label>
                            <input type="text" id="editBarcode" name="barcode" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300" />
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700">Description</label>
                            <textarea id="editDescription" name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-medium text-slate-900 transition focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/20 hover:border-slate-300"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" id="cancelEditProduct" onclick="event.preventDefault(); event.stopPropagation(); const modal=document.getElementById('editProductModal'); if(modal){ modal.classList.add('hidden'); modal.style.display='none'; }" class="px-6 py-2.5 rounded-lg border-2 border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">Cancel</button>
                        <button type="submit" id="submitEditProduct" class="px-6 py-2.5 rounded-lg bg-[#00fff2] text-sm font-semibold text-black hover:bg-[#00e6da] transition shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.AllStocks = {
            routes: {
                apiProducts: '{{ route("api.products") }}',
                apiProductShowBase: '{{ url("api/products") }}',
                apiStats: '{{ route("api.stats") }}',
                apiMovements: '{{ route("api.movements") }}',
                apiUpdatePriceBase: '{{ url("api/product") }}',
                productUpdateBase: '{{ url('product') }}'
            },
            baseUrl: '{{ url("") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @vite('resources/js/monitoring.js')
</x-layouts.app>
