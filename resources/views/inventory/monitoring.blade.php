<x-layouts.app :title="__('Inventory Monitoring')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Inventory Monitoring</h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time tracking of inventory operations and movements</p>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 mb-0.5">Total Stock Items</p>
                        <p id="stat-total-products" class="text-lg font-bold text-slate-900">--</p>
                        <p class="text-xs text-cyan-600 mt-1">Real inventory count</p>
                    </div>
                    <div class="p-2 bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 mb-0.5">Active Inventory</p>
                        <p id="stat-active-items" class="text-lg font-bold text-slate-900">--</p>
                        <p class="text-xs text-cyan-600 mt-1">Above reorder level</p>
                    </div>
                    <div class="p-2 bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 mb-0.5">Low Stock Alerts</p>
                        <p id="stat-low-stock" class="text-lg font-bold text-slate-900">--</p>
                        <p class="text-xs text-cyan-600 mt-1">Below reorder level</p>
                    </div>
                    <div class="p-2 bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6.343 3a6 6 0 100 12A6 6 0 006.343 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 mb-0.5">Out of Stock</p>
                        <p id="stat-out-of-stock" class="text-lg font-bold text-slate-900">--</p>
                        <p class="text-xs text-cyan-600 mt-1">Needs replenishment</p>
                    </div>
                    <div class="p-2 bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 mb-0.5">Expiring Soon</p>
                        <p id="stat-expiring-soon" class="text-lg font-bold text-slate-900">--</p>
                        <p class="text-xs text-amber-600 mt-1">Within 30 days</p>
                    </div>
                    <div class="p-2 bg-amber-100 rounded-lg">
                        <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 0h-1v4h-1m2-7a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-600 mb-0.5">Expired Inventory</p>
                        <p id="stat-expired" class="text-lg font-bold text-slate-900">--</p>
                        <p class="text-xs text-rose-600 mt-1">Past expiry date</p>
                    </div>
                    <div class="p-2 bg-rose-100 rounded-lg">
                        <svg class="w-6 h-6 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m1 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-lg border border-slate-200 p-2 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                <div class="md:col-span-2">
                    <input id="searchInput" type="search" placeholder="Search by product name, SKU, brand, or category..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                </div>

                <div>
                    <select id="statusFilter" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Stock Status</option>
                        <option value="active">Active</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>

                <div>
                    <select id="expiryStatusFilter" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Expiry Status</option>
                        <option value="non_expiring">Non-expiring</option>
                        <option value="expiring">Expiring Soon</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

                <div>
                    <input id="restockDateFilter" type="date" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide w-6">
                                <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Motorcycle Compatibility</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Product Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">SKU</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Brand</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Size</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Color</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 uppercase tracking-wide">Stock</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Unit Price</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Supplier</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Last Restock</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Expiry</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
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
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p id="paginationInfo" class="text-slate-600">Showing 0 of 0 items</p>
                <div class="flex gap-1">
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">← Prev</button>
                    <button class="px-2 py-1 rounded-lg bg-cyan-600 text-xs font-medium text-white hover:bg-cyan-700">1</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">2</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">3</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">Next →</button>
                </div>
            </div>
        </div>

        <!-- Legend & Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3">
                <h3 class="font-semibold text-slate-900 mb-2 text-sm">Stock Status Legend</h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Active</span>
                        <span class="text-slate-600">Healthy stock</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Alert</span>
                        <span class="text-slate-600">Expiring soon</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Critical</span>
                        <span class="text-slate-600">Expired/Action</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Low</span>
                        <span class="text-slate-600">Low stock</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3">
                <h3 class="font-semibold text-slate-900 mb-2 text-sm">Expiry Tracking</h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-slate-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Non-Exp:</strong> Tracked without dates</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-slate-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Alerts:</strong> 30 days prior</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-slate-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-600"><strong>Expired:</strong> Removal flagged</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden mt-4">
            <div class="px-4 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Recent Inventory Movements</h2>
                    <p class="text-xs text-slate-500 mt-1">Latest stock changes, restocks, and price updates.</p>
                </div>
                <button id="refreshMovementsBtn" class="rounded-full border border-slate-300 bg-white px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">Refresh</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 uppercase tracking-wide text-slate-600">Date</th>
                            <th class="px-3 py-2 uppercase tracking-wide text-slate-600">Product</th>
                            <th class="px-3 py-2 uppercase tracking-wide text-slate-600">Type</th>
                            <th class="px-3 py-2 uppercase tracking-wide text-slate-600 text-center">Qty</th>
                            <th class="px-3 py-2 uppercase tracking-wide text-slate-600">Details</th>
                        </tr>
                    </thead>
                    <tbody id="movementFeed" class="bg-white">
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500">Loading recent movements...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.AllStocks = {
            routes: {
                apiProducts: '{{ route("api.products") }}',
                apiStats: '{{ route("api.stats") }}',
                apiMovements: '{{ route("api.movements") }}',
                apiUpdatePriceBase: '{{ url("api/product") }}'
            },
            baseUrl: '{{ url("") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @vite('resources/js/allstocks.js')
</x-layouts.app>
