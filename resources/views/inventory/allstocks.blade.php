<x-layouts.app :title="__('All Stocks')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">All Stocks</h1>
                <p class="text-xs text-slate-500 mt-0.5">Complete inventory list with pricing and categories</p>
            </div>
            <div class="flex gap-2 items-center">
                <button id="exportBtn" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
                <form id="importForm" class="hidden">
                    @csrf
                    <input type="file" id="importFile" accept=".xlsx,.xls" />
                </form>
                <label for="importFile" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Import
                </label>
                <button id="addStockBtn" class="px-3 py-1.5 rounded-lg bg-cyan-600 text-white text-xs font-medium hover:bg-cyan-700 transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Stock
                </button>
            </div>
        </div>

        <!-- Quick Stats (Total Products + Total Value) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Total Products</p>
                <p id="stat-total-products" class="text-2xl font-bold text-slate-900">--</p>
                <p class="text-xs text-slate-500 mt-0.5">All categories</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Total Value</p>
                <p id="stat-total-value" class="text-2xl font-bold text-slate-900">--</p>
                <p class="text-xs text-slate-500 mt-0.5">Current stock</p>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg border border-slate-200 p-2.5 shadow-sm space-y-2">
            <input
                id="searchInput"
                type="search"
                placeholder="Search by product name, SKU, barcode, or brand..."
                class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
            />

            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 items-end">
                <div>
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Category</label>
                    <select id="categoryFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Categories</option>
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

                <div>
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Product Name</label>
                    <select id="productNameFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Products</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Brand</label>
                    <select id="brandFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Brands</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Size</label>
                    <select id="sizeFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Sizes</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Stock Status</label>
                    <select id="statusFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Expiry Status</label>
                    <select id="expiryStatusFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Expiry</option>
                        <option value="expiring">Expiring Soon</option>
                        <option value="expired">Expired</option>
                        <option value="non_expiring">Non-expiring</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- All Stocks Table -->
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
                    <tbody class="divide-y divide-slate-200">
                        <!-- Data will be loaded from Excel import -->
                    </tbody>
                </table>
            </div>

            <!-- Table Footer - Pagination & Actions -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <span>Showing</span>
                    <select class="px-2 py-1 rounded border border-slate-300 bg-white text-xs">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <span>of 2,847 items</span>
                </div>
                <div class="flex gap-1">
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">← Prev</button>
                    <button class="px-2 py-1 rounded-lg bg-cyan-600 text-xs font-medium text-white hover:bg-cyan-700">1</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">2</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">3</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">Next →</button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar (visible when items selected) -->
        <div class="hidden bg-cyan-50 border border-cyan-200 rounded-lg p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="text-sm text-cyan-900">
                    <span class="font-semibold">3 items selected</span>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-lg border border-cyan-300 bg-white text-xs font-medium text-cyan-700 hover:bg-cyan-50 transition">
                        Bulk Edit
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-cyan-300 bg-white text-xs font-medium text-cyan-700 hover:bg-cyan-50 transition">
                        Adjust Price
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                        Archive
                    </button>
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

    <script>
        window.AllStocks = {
            routes: {
                apiProducts: '{{ route("api.products") }}',
                stockAdd: '{{ route("stock.add") }}',
                apiStats: '{{ route("api.stats") }}',
                apiMovements: '{{ route("api.movements") }}',
                stockExport: '{{ route("stock.export") }}',
                stockImport: '{{ route("stock.import") }}'
            },
            baseUrl: '{{ url("") }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @vite('resources/js/allstocks.js')
</x-layouts.app>
