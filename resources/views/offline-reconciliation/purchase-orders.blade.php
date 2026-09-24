<x-layouts.app :title="__('Offline Purchase Orders')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between pt-2 pb-1 pl-1">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Offline Purchase Orders</h1>
                <p class="text-gray-600 text-xs mt-1">Generate and manage complete purchase orders locally during internet outages</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <a href="{{ route('offline.export') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-4 py-2 text-sm font-bold text-black shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Go to Export Data</span>
                </a>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Offline Status Banner -->
        <div id="offline-banner" class="bg-amber-50 border border-amber-200 rounded-[15px] p-4 hidden transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-xl text-amber-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-amber-900">You are currently offline</p>
                    <p class="text-xs text-amber-700">Orders are saved to local browser storage (IndexedDB). Prices and amounts are automatically calculated. When back online, export to CSV for admin reconciliation.</p>
                </div>
            </div>
        </div>

        <!-- Rich Alert Container for Notifications -->
        <div id="poAlertContainer" class="hidden"></div>

        <!-- Create Purchase Order Card -->
        <div class="rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm space-y-6">
            <!-- Form Header & Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Create Purchase Order (Offline)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Select products, set order quantities, choose an authorized supplier, and save locally</p>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-[12px] border border-slate-200">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase">PO #:</span>
                        <input type="text" id="order_number" class="px-2 py-1 rounded-[8px] border border-slate-300 bg-white text-xs font-bold text-slate-900 font-mono tracking-wide focus:outline-none w-48 shadow-2xs" readonly>
                        <button type="button" onclick="generateOrderNumber()" title="Generate New PO Number" class="p-1.5 border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 rounded-[8px] transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="clearCatalogFilters()" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition cursor-pointer" title="Reset and clear catalog search and category/brand filters">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Reset / Clear Filters</span>
                    </button>
                </div>
            </div>

            <!-- STEP 1: Product Selection Catalog -->
            <div class="rounded-[16px] border border-slate-200 bg-slate-50/50 p-4 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-600 text-white text-[11px] font-bold flex items-center justify-center">1</span>
                            <span>Step 1: Select Products from Catalog</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Search products and click "Add to Order". Unit purchase prices are automatically retrieved from the system.</p>
                    </div>

                    <!-- Catalog Search & Filter Controls -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="relative min-w-[200px]">
                            <input type="text" id="productSearchInput" oninput="renderProductCatalog()" placeholder="Search name, SKU, brand..." class="w-full pl-8 pr-3 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <select id="catalogCategoryFilter" onchange="onCategoryFilterChange()" class="px-2.5 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-700 focus:outline-none shadow-2xs">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                @if(strtolower(trim($cat)) !== 'accessories')
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endif
                            @endforeach
                        </select>

                        <select id="catalogBrandFilter" onchange="renderProductCatalog()" class="px-2.5 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-700 focus:outline-none shadow-2xs">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}">{{ $brand }}</option>
                            @endforeach
                        </select>

                        <button type="button" id="toggleLowStockBtn" onclick="toggleLowStockFilter()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900 hover:bg-amber-100 transition shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span id="lowStockBtnText">Low Stock Only</span>
                        </button>
                    </div>
                </div>

                <!-- Products Catalog Table -->
                <div class="overflow-hidden rounded-[12px] border border-slate-200 bg-white shadow-2xs">
                    <div class="overflow-x-auto max-h-[260px]">
                        <table class="min-w-full text-left text-xs text-slate-700">
                            <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-100 text-xs uppercase tracking-wider text-slate-700 font-semibold">
                                <tr>
                                    <th class="px-3 py-2.5 text-left">Product / SKU</th>
                                    <th class="px-3 py-2.5 text-left">Category & Brand</th>
                                    <th class="px-3 py-2.5 text-center">Current Stock</th>
                                    <th class="px-3 py-2.5 text-right">Unit Purchase Price</th>
                                    <th class="px-3 py-2.5 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white" id="productCatalogTbody">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Selected Order Items (Cart & Quantity Modification) -->
            <div id="selected-items-section" class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[11px] font-bold flex items-center justify-center">2</span>
                            <span>Step 2: Selected Order Items (Adjust Quantities)</span>
                            <span id="selected-items-badge" class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-full">0 items</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Change the quantity to order for each item. Subtotals and Total Amount calculate automatically.</p>
                    </div>
                    <button type="button" onclick="clearSelectedItems()" id="btnClearItems" class="hidden text-xs text-red-600 hover:text-red-700 font-semibold cursor-pointer">Clear All Items</button>
                </div>

                <div class="overflow-hidden rounded-[14px] border border-slate-200 bg-white shadow-2xs">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="px-3 py-3 text-left font-semibold text-white">Product</th>
                                    <th class="px-3 py-3 text-left font-semibold text-white">SKU</th>
                                    <th class="px-3 py-3 text-center font-semibold text-white">Current Stock</th>
                                    <th class="px-3 py-3 text-right font-semibold text-white" style="width: 170px;">Unit Price (₱)</th>
                                    <th class="px-3 py-3 text-center font-semibold text-white" style="width: 170px;">Quantity to Order</th>
                                    <th class="px-3 py-3 text-right font-semibold text-white" style="width: 170px;">Subtotal (₱)</th>
                                    <th class="px-3 py-3 text-center font-semibold text-white" style="width: 70px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white" id="selectedItemsTbody">
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                            <span class="font-medium text-slate-600">No items added yet</span>
                                            <span class="text-[11px] text-slate-400">Select products from the catalog in Step 1 to build your order</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="border-t-2 border-slate-300 bg-slate-50/80 font-semibold text-slate-900">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-xs">
                                        <span class="text-slate-500 font-normal mr-2">Total Units: <strong class="text-slate-800 font-bold" id="totalUnitsCount">0</strong></span>
                                        <span class="text-slate-800 font-bold uppercase tracking-wider">Grand Total Amount:</span>
                                    </td>
                                    <td colspan="3" class="px-4 py-3 text-right">
                                        <div class="text-base font-extrabold text-slate-950 font-mono" id="grandTotalDisplay">₱0.00</div>
                                        <div class="text-[10px] text-slate-500 font-normal">[Auto-calculated from items]</div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Eligible Supplier & Order Notes (MOVED BELOW SELECTED ORDER ITEMS) -->
            <div class="rounded-[16px] border border-slate-200 bg-slate-50/70 p-4 space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[11px] font-bold flex items-center justify-center">3</span>
                        <span>Step 3: Supplier Authorization & Order Notes</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Choose an authorized supplier capable of supplying the selected items, review performance assessment, and add notes</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <!-- Supplier Selector & Assessment -->
                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Eligible Supplier <span class="text-red-500">*</span></label>
                                <span id="supplier-eligibility-hint" class="text-[11px] text-slate-500">Showing all active suppliers</span>
                            </div>

                            <div class="relative z-[25]" data-dropdown-wrapper="supplierSelector">
                                <input type="hidden" id="supplier_id" value="" />
                                <button type="button" id="supplierSelectorBtn" onclick="toggleSupplierSelector()" class="w-full px-3.5 py-2.5 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm cursor-pointer">
                                    <span id="supplierSelectorLabel" class="font-medium">-- Select an Authorized Supplier --</span>
                                    <svg class="w-4 h-4 text-slate-500 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="supplierSelectorDropdown" class="hidden absolute top-full left-0 right-0 z-[30] mt-1 max-h-[260px] overflow-y-auto rounded-[12px] border border-slate-200 bg-white shadow-2xl p-1.5 space-y-1">
                                    <div id="supplierListContainer">
                                        <!-- Populated dynamically via JS -->
                                    </div>
                                </div>
                            </div>

                            <!-- Ineligibility Warning -->
                            <div id="supplier-mismatch-warning" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-[12px] flex items-start gap-2.5">
                                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div class="text-xs text-red-800">
                                    <span class="font-bold">No common authorized supplier:</span> The items currently in your cart cannot all be supplied by a single supplier. Please adjust item selection or create separate orders for different suppliers.
                                </div>
                            </div>
                        </div>

                        <!-- Supplier Assessment Display Card -->
                        <div id="supplier-assessment-card" class="hidden rounded-[15px] border border-slate-200 bg-white p-3.5 space-y-2.5 shadow-2xs">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900" id="assessment-supp-name">Supplier Name</span>
                                    <span id="assessment-status-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">Active</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[11px] text-slate-500 font-medium">Performance Rating:</span>
                                    <span id="assessment-overall-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800">92% Excellent</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                                <div class="bg-slate-50 p-2 rounded-[10px] border border-slate-200">
                                    <p class="text-[10px] text-slate-500 font-medium">On-Time</p>
                                    <p id="assessment-ontime" class="text-xs font-bold text-slate-900 mt-0.5">100%</p>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-[10px] border border-slate-200">
                                    <p class="text-[10px] text-slate-500 font-medium">Quality</p>
                                    <p id="assessment-quality" class="text-xs font-bold text-slate-900 mt-0.5">100%</p>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-[10px] border border-slate-200">
                                    <p class="text-[10px] text-slate-500 font-medium">Completion</p>
                                    <p id="assessment-completion" class="text-xs font-bold text-slate-900 mt-0.5">100%</p>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-[10px] border border-slate-200">
                                    <p class="text-[10px] text-slate-500 font-medium">Orders</p>
                                    <p id="assessment-orders" class="text-xs font-bold text-slate-900 mt-0.5">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes & Submission -->
                    <div class="space-y-3 flex flex-col justify-between">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Order Notes / Instructions (Optional)</label>
                            <textarea id="notes" rows="4" placeholder="Add specific instructions, reference numbers, or offline notes for this order..." class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:border-slate-400 transition shadow-sm"></textarea>
                        </div>

                        <!-- Bottom Finalize Action Bar -->
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                            <div class="text-xs text-slate-500">
                                <span>Ready to save locally in IndexedDB</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="saveCurrentOrder()" id="btnSaveOrderBottom" class="inline-flex items-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-6 py-2.5 text-xs font-bold text-black shadow-sm hover:bg-[#59b2c2] focus:outline-none transition cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                    <span>Save Purchase Order Locally</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Embedded Master Data from Server -->
    <script>
        const initialProducts = @json($products);
        const initialSuppliers = @json($suppliers);
        const categoryBrandsMap = @json($categoryBrandsMap ?? []);
        const allAvailableBrands = @json($brands ?? []);
    </script>
    <script>
        // Notification Panel Toggle
        function toggleNotificationPanel(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                        profileDropdown.classList.remove('opacity-100', 'scale-100');
                        profileDropdown.classList.add('opacity-0', 'scale-95');
                    }
                } else {
                    panel.classList.add('hidden');
                }
            }
        }

        // Helper functions
        function markAllNotificationsRead() {
            // Placeholder for marking all notifications as read
            console.log('Mark all notifications as read');
        }

        function openAllNotificationsModal() {
            // Placeholder for opening all notifications modal
            console.log('Open all notifications modal');
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            const notificationBell = document.getElementById('notification-bell-btn');
            const profileButton = document.getElementById('dashboardProfileButton');

            if (panel && !panel.contains(event.target) && notificationBell && !notificationBell.contains(event.target)) {
                panel.classList.add('hidden');
            }

            if (profileDropdown && !profileDropdown.contains(event.target) && profileButton && !profileButton.contains(event.target)) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.remove('opacity-100', 'scale-100');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }
        });
    </script>
    <script src="{{ asset('js/offline-manager.js') }}"></script>
    <script>
        // State Variables
        let catalogProducts = [];
        let catalogSuppliers = [];
        let selectedItems = []; // Array of { product_id, product_name, sku, category, brand, current_stock, unit_price, quantity, subtotal, supplier_ids, supplier_prices }
        let selectedSupplier = null; // { id, name, assessment, ... }
        let lowStockOnlyFilter = false;

        // Initialization
        document.addEventListener('DOMContentLoaded', function() {
            generateOrderNumber();

            // Immediate render with server master data so Step 1 & 2 are instantly active
            catalogProducts = initialProducts || [];
            catalogSuppliers = initialSuppliers || [];
            updateBrandFilterOptions(document.getElementById('catalogCategoryFilter')?.value || '');
            renderProductCatalog();
            updateEligibleSuppliers();
            renderSelectedItems();

            // Cache server master data into IndexedDB
            setTimeout(async () => {
                if (window.offlineManager) {
                    try {
                        if (initialProducts && initialProducts.length > 0) {
                            await offlineManager.cacheMasterData(initialProducts, initialSuppliers);
                        }
                        await loadMasterData();
                    } catch (err) {
                        console.warn('Offline cache init:', err);
                    }
                    updateOfflineBanner();
                }
            }, 100);
        });

        // Load master data from IndexedDB or initial payload
        async function loadMasterData() {
            try {
                const cachedProds = await offlineManager.getCachedProducts();
                const cachedSupps = await offlineManager.getCachedSuppliers();

                catalogProducts = (cachedProds && cachedProds.length > 0) ? cachedProds : (initialProducts || []);
                catalogSuppliers = (cachedSupps && cachedSupps.length > 0) ? cachedSupps : (initialSuppliers || []);
            } catch (e) {
                console.warn('Error loading from IndexedDB:', e);
                catalogProducts = initialProducts || [];
                catalogSuppliers = initialSuppliers || [];
            }

            updateBrandFilterOptions(document.getElementById('catalogCategoryFilter')?.value || '');
            renderProductCatalog();
            updateEligibleSuppliers();
        }

        function generateOrderNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            const orderNumber = `PO-${year}${month}${day}-${random}`;
            document.getElementById('order_number').value = orderNumber;
        }

        function updateOfflineBanner() {
            const banner = document.getElementById('offline-banner');
            if (offlineManager && offlineManager.isOffline()) {
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }
        }
        window.addEventListener('online', updateOfflineBanner);
        window.addEventListener('offline', updateOfflineBanner);

        // Helper: Get Applicable Purchase Price for Product
        function getProductApplicablePrice(product, supplierId = null) {
            if (!product) return 0;

            // 1. If a supplier is selected and supplier-specific price exists in supplier_prices mapping
            if (supplierId && product.supplier_prices && typeof product.supplier_prices[supplierId] !== 'undefined') {
                const suppPrice = Number(product.supplier_prices[supplierId]);
                if (!isNaN(suppPrice) && suppPrice > 0) {
                    return suppPrice;
                }
            }

            // 2. Default product unit_price
            const defaultPrice = Number(product.unit_price);
            if (!isNaN(defaultPrice) && defaultPrice > 0) {
                return defaultPrice;
            }

            return 0;
        }

        // Category change handler: updates brand dropdown dynamically and re-renders catalog
        function onCategoryFilterChange() {
            const selectedCat = document.getElementById('catalogCategoryFilter')?.value || '';
            updateBrandFilterOptions(selectedCat);
            renderProductCatalog();
        }

        // Dynamically update Brand Filter options based on selected category
        function updateBrandFilterOptions(selectedCategory = '') {
            const brandSelect = document.getElementById('catalogBrandFilter');
            if (!brandSelect) return;
            const currentBrand = brandSelect.value;

            let availableBrands = [];

            if (selectedCategory) {
                const selectedLower = selectedCategory.toLowerCase().trim();

                // 1. Get brands mapped from ProductDescription for this category
                for (const [catName, brands] of Object.entries(categoryBrandsMap || {})) {
                    if (catName.toLowerCase().trim() === selectedLower && Array.isArray(brands)) {
                        availableBrands.push(...brands);
                    }
                }

                // 2. Also collect brands from all catalog products matching this category
                const matchingProducts = catalogProducts.filter(p => {
                    const pCat = (p.category || '').toLowerCase().trim();
                    const pName = (p.product_name || '').toLowerCase().trim();
                    return pCat === selectedLower || pName === selectedLower;
                });
                matchingProducts.forEach(p => {
                    if (p.brand && typeof p.brand === 'string' && p.brand.trim()) {
                        availableBrands.push(p.brand.trim());
                    }
                });
            } else {
                // All brands
                availableBrands = [...allAvailableBrands];
                catalogProducts.forEach(p => {
                    if (p.brand && typeof p.brand === 'string' && p.brand.trim()) {
                        availableBrands.push(p.brand.trim());
                    }
                });
            }

            // Deduplicate (case-insensitive while preserving clean casing) and sort
            const brandMap = new Map();
            availableBrands.forEach(b => {
                if (b && typeof b === 'string' && b.trim()) {
                    const clean = b.trim();
                    const lower = clean.toLowerCase();
                    if (!brandMap.has(lower)) {
                        brandMap.set(lower, clean);
                    }
                }
            });

            const uniqueBrands = Array.from(brandMap.values()).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));

            brandSelect.innerHTML = '<option value="">All Brands</option>' +
                uniqueBrands.map(b => `<option value="${escapeHtml(b)}">${escapeHtml(b)}</option>`).join('');

            // Keep selected brand if still valid
            const brandExists = uniqueBrands.some(b => b.toLowerCase() === currentBrand.toLowerCase());
            if (currentBrand && brandExists) {
                brandSelect.value = brandMap.get(currentBrand.toLowerCase()) || currentBrand;
            } else {
                brandSelect.value = '';
            }
        }

        // Quick clear just the search & filter controls without resetting the order cart
        function clearCatalogFilters() {
            const searchInput = document.getElementById('productSearchInput');
            if (searchInput) searchInput.value = '';

            const catFilter = document.getElementById('catalogCategoryFilter');
            if (catFilter) catFilter.value = '';

            updateBrandFilterOptions('');

            lowStockOnlyFilter = false;
            const btn = document.getElementById('toggleLowStockBtn');
            const txt = document.getElementById('lowStockBtnText');
            if (btn) {
                btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900 hover:bg-amber-100 transition shadow-2xs cursor-pointer';
            }
            if (txt) {
                txt.textContent = 'Low Stock Only';
            }

            renderProductCatalog();
        }

        // Filter and Render Product Catalog
        function renderProductCatalog() {
            const search = (document.getElementById('productSearchInput')?.value || '').toLowerCase().trim();
            const catFilter = (document.getElementById('catalogCategoryFilter')?.value || '').toLowerCase().trim();
            const brandFilter = (document.getElementById('catalogBrandFilter')?.value || '').toLowerCase().trim();
            const tbody = document.getElementById('productCatalogTbody');
            if (!tbody) return;

            const suppId = selectedSupplier ? selectedSupplier.id : null;

            let filtered = catalogProducts.filter(p => {
                const prodCat = (p.category || '').toLowerCase().trim();
                const prodBrand = (p.brand || '').toLowerCase().trim();
                const prodName = (p.name || '').toLowerCase();
                const prodSku = (p.sku || '').toLowerCase();
                const origProdName = (p.product_name || '').toLowerCase().trim();

                const matchesSearch = !search || 
                    prodName.includes(search) ||
                    prodSku.includes(search) ||
                    prodBrand.includes(search) ||
                    prodCat.includes(search) ||
                    origProdName.includes(search);

                const matchesCat = !catFilter || 
                    prodCat === catFilter ||
                    origProdName === catFilter ||
                    (prodCat && prodCat.includes(catFilter)) ||
                    (catFilter && prodCat.startsWith(catFilter));

                const matchesBrand = !brandFilter || prodBrand === brandFilter;
                const matchesLowStock = !lowStockOnlyFilter || p.is_low_stock;

                return matchesSearch && matchesCat && matchesBrand && matchesLowStock;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-slate-500">
                            No matching products found in catalog.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filtered.map(p => {
                const isLowStock = p.is_low_stock;
                const inCart = selectedItems.find(item => item.product_id === p.id);
                const cartQty = inCart ? inCart.quantity : 0;
                const applicablePrice = getProductApplicablePrice(p, suppId);
                const hasValidPrice = applicablePrice > 0;

                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-3 py-2">
                            <div class="font-bold text-slate-900">${escapeHtml(p.name)}</div>
                            <div class="text-[11px] font-mono text-slate-500">SKU: ${escapeHtml(p.sku)}</div>
                        </td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700">${escapeHtml(p.category || 'General')}</span>
                            <span class="text-[11px] text-slate-500 ml-1">${escapeHtml(p.brand || '')}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="inline-flex items-center gap-1">
                                <span class="font-bold ${isLowStock ? 'text-red-600' : 'text-slate-800'}">${p.stock_quantity}</span>
                                <span class="text-[10px] text-slate-400">/ min ${p.reorder_level}</span>
                            </div>
                            ${isLowStock ? `<div class="text-[9px] font-bold text-red-500">Low Stock (Reorder: ${p.suggested_quantity})</div>` : ''}
                        </td>
                        <td class="px-3 py-2 text-right">
                            ${hasValidPrice ? `
                                <div class="font-mono font-bold text-slate-900">₱${applicablePrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                                <div class="text-[9px] text-slate-400 font-medium">Auto-retrieved</div>
                            ` : `
                                <span class="text-xs font-semibold text-red-500 italic">No price set</span>
                            `}
                        </td>
                        <td class="px-3 py-2 text-center">
                            ${hasValidPrice ? `
                                <button type="button" onclick="addProductToOrder(${p.id})" class="inline-flex items-center gap-1 px-3 py-1 rounded-[8px] border ${inCart ? 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100' : 'border-[#6EC1D1]/60 bg-[#6EC1D1]/20 hover:bg-[#6EC1D1] text-slate-900'} text-xs font-semibold transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>${inCart ? `Added (${cartQty})` : '+ Add to Order'}</span>
                                </button>
                            ` : `
                                <button type="button" onclick="alertNoPrice('${escapeHtml(p.name)}')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] border border-slate-200 bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed" title="This product does not have a valid purchase price and cannot be added to the Purchase Order.">
                                    <span>No Price</span>
                                </button>
                            `}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function alertNoPrice(productName = 'This product') {
            alert(`${productName} does not have a valid purchase price and cannot be added to the Purchase Order.`);
        }

        function toggleLowStockFilter() {
            lowStockOnlyFilter = !lowStockOnlyFilter;
            const btn = document.getElementById('toggleLowStockBtn');
            const txt = document.getElementById('lowStockBtnText');
            if (lowStockOnlyFilter) {
                btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] border border-red-400 bg-red-100 text-xs font-bold text-red-900 shadow-sm cursor-pointer';
                txt.textContent = 'Showing Low Stock (Active)';
            } else {
                btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900 hover:bg-amber-100 transition shadow-2xs cursor-pointer';
                txt.textContent = 'Low Stock Only';
            }
            renderProductCatalog();
        }

        // Add Product to Selected Order Items
        function addProductToOrder(productId) {
            const product = catalogProducts.find(p => p.id === productId);
            if (!product) return;

            const suppId = selectedSupplier ? selectedSupplier.id : null;
            const unitPrice = getProductApplicablePrice(product, suppId);

            // PRICE VALIDATION
            if (!unitPrice || isNaN(unitPrice) || unitPrice <= 0) {
                alert('This product does not have a valid purchase price and cannot be added to the Purchase Order.');
                return;
            }

            const existingIndex = selectedItems.findIndex(item => item.product_id === productId);
            if (existingIndex > -1) {
                selectedItems[existingIndex].quantity = (selectedItems[existingIndex].quantity || 0) + 1;
                selectedItems[existingIndex].unit_price = unitPrice;
                selectedItems[existingIndex].subtotal = selectedItems[existingIndex].quantity * unitPrice;
            } else {
                // Quantity starts at 0 as requested by the user
                const initialQty = 0;
                selectedItems.push({
                    product_id: product.id,
                    product_name: product.name,
                    sku: product.sku,
                    category: product.category,
                    brand: product.brand,
                    current_stock: product.stock_quantity,
                    unit_price: unitPrice, // Auto-generated / Read-only
                    quantity: initialQty, // Quantity to Order (Default 0, Editable by Admin)
                    subtotal: initialQty * unitPrice, // Auto-calculated (0.00)
                    supplier_ids: product.supplier_ids || [],
                    supplier_prices: product.supplier_prices || {}
                });
            }

            renderSelectedItems();
            renderProductCatalog();
            updateEligibleSuppliers();

            // Smoothly auto-scroll down to the selected items section
            const selectedSection = document.getElementById('selected-items-section');
            if (selectedSection) {
                selectedSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Auto-focus the quantity input for the added item so admin can immediately type the quantity
            setTimeout(() => {
                const qtyInput = document.getElementById(`item-qty-${productId}`);
                if (qtyInput) {
                    qtyInput.focus();
                    qtyInput.select();
                }
            }, 300);
        }

        // Recalculate item prices when supplier is selected or changed
        function recalculateItemPrices() {
            const suppId = selectedSupplier ? selectedSupplier.id : null;

            for (let i = 0; i < selectedItems.length; i++) {
                const item = selectedItems[i];
                const product = catalogProducts.find(p => p.id === item.product_id);
                if (product) {
                    const applicablePrice = getProductApplicablePrice(product, suppId);
                    if (applicablePrice > 0) {
                        item.unit_price = applicablePrice;
                        item.subtotal = item.quantity * applicablePrice;
                    }
                }
            }

            renderSelectedItems();
            renderProductCatalog();
        }

        // Remove item from selected order items
        function removeSelectedItem(productId) {
            selectedItems = selectedItems.filter(item => item.product_id !== productId);
            renderSelectedItems();
            renderProductCatalog();
            updateEligibleSuppliers();
        }

        function clearSelectedItems() {
            if (selectedItems.length === 0) return;
            if (!confirm('Clear all selected items from this purchase order?')) return;
            selectedItems = [];
            renderSelectedItems();
            renderProductCatalog();
            updateEligibleSuppliers();
        }

        // Quantity modification by Admin
        function updateItemQuantity(productId, newQty) {
            const index = selectedItems.findIndex(i => i.product_id === productId);
            if (index === -1) return;

            const qty = Math.max(0, parseInt(newQty) || 0);
            selectedItems[index].quantity = qty;
            selectedItems[index].subtotal = qty * selectedItems[index].unit_price;
            renderSelectedItems();
            renderProductCatalog();
        }

        // Render Selected Items Table & Totals (Read-only Price, Auto-calculated Subtotals & Grand Total)
        function renderSelectedItems() {
            const tbody = document.getElementById('selectedItemsTbody');
            const badge = document.getElementById('selected-items-badge');
            const clearBtn = document.getElementById('btnClearItems');
            const totalUnitsEl = document.getElementById('totalUnitsCount');
            const grandTotalEl = document.getElementById('grandTotalDisplay');

            const count = selectedItems.length;
            badge.textContent = `${count} ${count === 1 ? 'item' : 'items'}`;

            if (count === 0) {
                clearBtn.classList.add('hidden');
                totalUnitsEl.textContent = '0';
                grandTotalEl.textContent = '₱0.00';
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span class="font-medium text-slate-600">No items added yet</span>
                                <span class="text-[11px] text-slate-400">Select products from the catalog in Step 1 to build your order</span>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            clearBtn.classList.remove('hidden');

            let totalUnits = 0;
            let grandTotal = 0;

            tbody.innerHTML = selectedItems.map(item => {
                totalUnits += item.quantity;
                grandTotal += item.subtotal;

                return `
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-3 py-2.5">
                            <div class="font-bold text-slate-900">${escapeHtml(item.product_name)}</div>
                            <div class="text-[10px] text-slate-500">${escapeHtml(item.category || '')} - ${escapeHtml(item.brand || '')}</div>
                        </td>
                        <td class="px-3 py-2.5 font-mono text-slate-600 font-medium">${escapeHtml(item.sku)}</td>
                        <td class="px-3 py-2.5 text-center font-bold text-slate-700">${item.current_stock}</td>
                        <td class="px-3 py-2.5 text-right">
                            <div class="font-mono font-bold text-slate-900">₱${item.unit_price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                            <div class="text-[9px] text-slate-400 font-medium">[Auto-generated]</div>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" onclick="updateItemQuantity(${item.product_id}, Math.max(0, ${item.quantity} - 1))" class="w-7 h-7 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer" title="Decrease Quantity">-</button>
                                <input type="number" min="0" id="item-qty-${item.product_id}" data-item-qty-id="${item.product_id}" value="${item.quantity}" onchange="updateItemQuantity(${item.product_id}, this.value)" oninput="updateItemQuantity(${item.product_id}, this.value)" class="w-16 px-1.5 py-1 text-center text-xs font-bold text-slate-900 rounded-[8px] border ${item.quantity === 0 ? 'border-amber-400 bg-amber-50/70 ring-1 ring-amber-300' : 'border-slate-300 bg-white'} focus:outline-none focus:ring-1 focus:ring-black/30 shadow-2xs">
                                <button type="button" onclick="updateItemQuantity(${item.product_id}, ${item.quantity} + 1)" class="w-7 h-7 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition cursor-pointer" title="Increase Quantity">+</button>
                            </div>
                            ${item.quantity === 0 ? `<div class="text-[9px] font-semibold text-amber-600 mt-0.5">Enter quantity</div>` : ''}
                        </td>
                        <td class="px-3 py-2.5 text-right">
                            <div class="font-mono font-extrabold text-slate-900">₱${item.subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                            <div class="text-[9px] text-slate-400 font-medium">[Auto-calculated]</div>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <button type="button" onclick="removeSelectedItem(${item.product_id})" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition cursor-pointer" title="Remove item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            totalUnitsEl.textContent = totalUnits.toLocaleString();
            grandTotalEl.textContent = `₱${grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }

        // Supplier Selection & Eligibility Filtering Logic
        function updateEligibleSuppliers() {
            const listContainer = document.getElementById('supplierListContainer');
            const hint = document.getElementById('supplier-eligibility-hint');
            const warning = document.getElementById('supplier-mismatch-warning');
            if (!listContainer) return;

            let eligibleSuppliers = catalogSuppliers;

            if (selectedItems.length > 0) {
                // Find intersection of supplier_ids for all selected items
                eligibleSuppliers = catalogSuppliers.filter(supplier => {
                    return selectedItems.every(item => {
                        const sIds = item.supplier_ids || [];
                        return sIds.includes(supplier.id);
                    });
                });

                hint.textContent = `${eligibleSuppliers.length} supplier(s) authorized for all ${selectedItems.length} selected item(s)`;
            } else {
                hint.textContent = `Showing all ${catalogSuppliers.length} active suppliers`;
            }

            if (eligibleSuppliers.length === 0 && selectedItems.length > 0) {
                warning.classList.remove('hidden');
                listContainer.innerHTML = `
                    <div class="px-3 py-4 text-center text-xs text-red-600 font-medium">
                        No supplier offers all selected products.
                    </div>
                `;
                // Clear current selected supplier if no longer eligible
                if (selectedSupplier) {
                    clearSupplierSelection();
                }
                return;
            } else {
                warning.classList.add('hidden');
            }

            // Check if current selected supplier is still eligible
            if (selectedSupplier && !eligibleSuppliers.some(s => s.id === selectedSupplier.id)) {
                clearSupplierSelection();
            }

            listContainer.innerHTML = `
                <button type="button" onclick="selectSupplier('', '-- Select an Authorized Supplier --')" class="w-full px-3 py-2 text-left text-xs text-slate-500 hover:bg-slate-100 rounded-[8px]">
                    -- Select an Authorized Supplier --
                </button>
                ${eligibleSuppliers.map(s => {
                    const score = s.performance_score ?? 100;
                    const ratingColor = score >= 90 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' :
                                        score >= 75 ? 'text-cyan-700 bg-cyan-50 border-cyan-200' :
                                        score >= 60 ? 'text-amber-700 bg-amber-50 border-amber-200' :
                                        'text-red-700 bg-red-50 border-red-200';

                    return `
                        <button type="button" onclick="selectSupplier(${s.id}, '${escapeHtml(s.name)}')" class="w-full px-3 py-2.5 text-left text-xs text-slate-800 hover:bg-slate-100 rounded-[8px] flex items-center justify-between transition">
                            <div class="font-bold text-slate-900">${escapeHtml(s.name)}</div>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border ${ratingColor}">Score: ${score}%</span>
                                <span class="text-[10px] text-slate-400">On-Time: ${s.on_time_rate ?? 100}%</span>
                            </div>
                        </button>
                    `;
                }).join('')}
            `;
        }

        function toggleSupplierSelector() {
            document.getElementById('supplierSelectorDropdown')?.classList.toggle('hidden');
        }

        function selectSupplier(id, name) {
            document.getElementById('supplier_id').value = id || '';
            document.getElementById('supplierSelectorLabel').textContent = name;
            document.getElementById('supplierSelectorDropdown')?.classList.add('hidden');

            if (!id) {
                selectedSupplier = null;
                document.getElementById('supplier-assessment-card')?.classList.add('hidden');
                recalculateItemPrices();
                return;
            }

            const supplier = catalogSuppliers.find(s => s.id === Number(id));
            selectedSupplier = supplier || null;

            if (supplier) {
                renderSupplierAssessment(supplier);
            } else {
                document.getElementById('supplier-assessment-card')?.classList.add('hidden');
            }

            // Automatically update item unit prices based on selected supplier
            recalculateItemPrices();
        }

        function clearSupplierSelection() {
            document.getElementById('supplier_id').value = '';
            document.getElementById('supplierSelectorLabel').textContent = '-- Select an Authorized Supplier --';
            selectedSupplier = null;
            document.getElementById('supplier-assessment-card')?.classList.add('hidden');
            recalculateItemPrices();
        }

        function renderSupplierAssessment(s) {
            const card = document.getElementById('supplier-assessment-card');
            if (!card) return;

            card.classList.remove('hidden');
            document.getElementById('assessment-supp-name').textContent = s.name;
            
            const score = s.performance_score ?? 100;
            const overallBadge = document.getElementById('assessment-overall-badge');
            
            if (score >= 90) {
                overallBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                overallBadge.textContent = `${score}% Excellent`;
            } else if (score >= 75) {
                overallBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800';
                overallBadge.textContent = `${score}% Good`;
            } else if (score >= 60) {
                overallBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                overallBadge.textContent = `${score}% Fair`;
            } else {
                overallBadge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800';
                overallBadge.textContent = `${score}% Poor`;
            }

            document.getElementById('assessment-ontime').textContent = `${s.on_time_rate ?? 100}%`;
            document.getElementById('assessment-quality').textContent = `${s.quality_score ?? 100}%`;
            document.getElementById('assessment-completion').textContent = `${s.completion_rate ?? 100}%`;
            document.getElementById('assessment-orders').textContent = s.orders_count ?? 0;
        }

        // Dropdown Click-outside handler
        document.addEventListener('click', function(e) {
            const suppWrapper = document.querySelector('[data-dropdown-wrapper="supplierSelector"]');
            if (suppWrapper && !suppWrapper.contains(e.target)) {
                document.getElementById('supplierSelectorDropdown')?.classList.add('hidden');
            }

            const syncWrapper = document.querySelector('[data-dropdown-wrapper="syncStatusFilter"]');
            if (syncWrapper && !syncWrapper.contains(e.target)) {
                document.getElementById('syncStatusFilterDropdown')?.classList.add('hidden');
            }
        });

        // Save Current Order to IndexedDB (with Price Snapshot)
        async function saveCurrentOrder() {
            const poNumber = document.getElementById('order_number').value.trim();
            const suppId = document.getElementById('supplier_id').value;
            const notes = document.getElementById('notes').value.trim();

            if (!poNumber) {
                alert('Order number is required.');
                return;
            }

            if (!suppId || !selectedSupplier) {
                alert('Please select an authorized supplier for this purchase order in Step 3.');
                return;
            }

            if (selectedItems.length === 0) {
                alert('Please add at least one product item to the purchase order.');
                return;
            }

            // Verify all items have valid quantity (> 0)
            const zeroQtyItem = selectedItems.find(item => !item.quantity || item.quantity <= 0);
            if (zeroQtyItem) {
                alert(`Please set a valid quantity (> 0) for "${zeroQtyItem.product_name}".`);
                const inputEl = document.getElementById(`item-qty-${zeroQtyItem.product_id}`);
                if (inputEl) {
                    inputEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    inputEl.focus();
                    inputEl.select();
                }
                return;
            }

            // Verify all items have valid prices (> 0)
            const invalidPriceItem = selectedItems.find(item => !item.unit_price || item.unit_price <= 0);
            if (invalidPriceItem) {
                alert(`Product "${invalidPriceItem.product_name}" does not have a valid purchase price and cannot be ordered.`);
                return;
            }

            // Verify all items are authorized for this supplier
            const ineligible = selectedItems.find(item => !(item.supplier_ids || []).includes(selectedSupplier.id));
            if (ineligible) {
                alert(`Product "${ineligible.product_name}" is not authorized for supplier "${selectedSupplier.name}". Please adjust selection.`);
                return;
            }

            const totalAmount = selectedItems.reduce((sum, i) => sum + i.subtotal, 0);

            // Record snapshot
            const orderRecord = {
                order_number: poNumber,
                supplier_id: selectedSupplier.id,
                supplier_name: selectedSupplier.name,
                notes: notes,
                total_amount: totalAmount,
                status: 'pending',
                sync_status: 'pending_sync',
                items: selectedItems.map(i => ({
                    product_id: i.product_id,
                    product_name: i.product_name,
                    sku: i.sku,
                    category: i.category,
                    brand: i.brand,
                    quantity: i.quantity,
                    unit_price: i.unit_price, // Unit price snapshot at order creation
                    subtotal: i.subtotal
                })),
                supplier_assessment: {
                    performance_score: selectedSupplier.performance_score ?? 100,
                    on_time_rate: selectedSupplier.on_time_rate ?? 100,
                    quality_score: selectedSupplier.quality_score ?? 100,
                    completion_rate: selectedSupplier.completion_rate ?? 100
                },
                timestamp: new Date().toISOString()
            };

            const btnBottom = document.getElementById('btnSaveOrderBottom');
            if (btnBottom) btnBottom.disabled = true;

            try {
                await offlineManager.savePendingOrder(orderRecord);
                
                // Show rich auto-dismissing success notification banner
                const poAlert = document.getElementById('poAlertContainer');
                if (poAlert) {
                    poAlert.classList.remove('hidden');
                    poAlert.style.opacity = '1';
                    poAlert.style.transform = 'translateY(0)';
                    poAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    poAlert.innerHTML = `
                        <div class="rounded-[16px] border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                                        <span>Purchase Order Saved Successfully!</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900 font-mono">${poNumber}</span>
                                    </h3>
                                    <p class="text-xs text-emerald-800 mt-1">
                                        Order <strong>${poNumber}</strong> with <strong>${selectedItems.length}</strong> product line item${selectedItems.length === 1 ? '' : 's'} (Total: ₱${totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}) has been stored in local browser storage.
                                    </p>
                                    <p class="text-[11px] text-emerald-700 mt-0.5 font-medium">
                                        You can review and export it anytime on the <a href="{{ route('offline.export') }}" class="underline font-bold text-emerald-900">Export Data</a> page.
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('poAlertContainer').classList.add('hidden')" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-md transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    poAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Auto-dismiss after 3.5 seconds
                    if (window._poAlertTimer) clearTimeout(window._poAlertTimer);
                    window._poAlertTimer = setTimeout(() => {
                        poAlert.style.opacity = '0';
                        poAlert.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            poAlert.classList.add('hidden');
                            poAlert.style.opacity = '1';
                            poAlert.style.transform = 'translateY(0)';
                        }, 400);
                    }, 3500);
                }

                // Reset order form
                resetOrderForm();
            } catch (error) {
                console.error('Error saving order:', error);
                alert('Failed to save order locally: ' + (error.message || 'Unknown error'));
            } finally {
                if (btnBottom) btnBottom.disabled = false;
            }
        }

        function resetOrderForm() {
            generateOrderNumber();
            clearSupplierSelection();
            const notesEl = document.getElementById('notes');
            if (notesEl) notesEl.value = '';

            // Reset search & category & brand & low stock filters
            const searchInput = document.getElementById('productSearchInput');
            if (searchInput) searchInput.value = '';

            const catFilter = document.getElementById('catalogCategoryFilter');
            if (catFilter) catFilter.value = '';

            // Reset brand options to all brands
            updateBrandFilterOptions('');

            // Reset low stock filter toggle
            lowStockOnlyFilter = false;
            const btn = document.getElementById('toggleLowStockBtn');
            const txt = document.getElementById('lowStockBtnText');
            if (btn) {
                btn.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] border border-amber-300 bg-amber-50 text-xs font-semibold text-amber-900 hover:bg-amber-100 transition shadow-2xs cursor-pointer';
            }
            if (txt) {
                txt.textContent = 'Low Stock Only';
            }

            selectedItems = [];
            renderSelectedItems();
            renderProductCatalog();
            updateEligibleSuppliers();
        }

        // Utility: Escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
</x-layouts.app>
