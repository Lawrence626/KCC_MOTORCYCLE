<x-layouts.app :title="__('Product Categorization')">

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Product Categorization</h1>
                <p class="text-xs text-slate-500 mt-0.5">Add, update, and delete product categories.</p>

            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Bulk Actions Toolbar -->
                <div id="bulkActionsToolbar" class="items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200" style="display:none">
                    <span class="text-xs text-slate-700 font-medium"><span id="selectedCount">0</span> selected</span>
                    <button id="bulkDeleteBtn" class="px-2.5 py-1 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">Move to Trash</button>
                    <button id="clearSelectionBtn" class="text-xs text-slate-600 hover:text-slate-800 font-medium">Clear</button>
                </div>
                <button id="openTrashBtn" class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Trash</span>
                    <span id="trashBadge" class="absolute -top-1.5 -right-1.5 items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs font-bold" style="display:none">0</span>
                </button>
                <button id="openDeleteList" class="px-3 py-1.5 rounded-lg border border-red-200 bg-white text-red-600 text-xs font-semibold hover:bg-red-50 transition shadow-sm">Delete List</button>
                <button id="openAddProduct" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#6EC1D1] text-slate-900 text-xs font-semibold hover:bg-[#59b2c2] transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Add Product</span>
                </button>
            </div>
        </div>
    </x-slot>
    <div class="space-y-4">

        <!-- Filter Section -->
        <div class="rounded-[10px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex-1 min-w-0">
                    <input type="text" id="searchInput" placeholder="Search by brand, product category, or SKU..." class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:border-transparent">
                </div>
                <button id="clearFilterBtn" class="px-3.5 py-2 rounded-[12px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Clear Filter
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-hidden rounded-[10px] border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto rounded-[10px]">
                <table class="w-full text-sm text-left whitespace-nowrap min-w-max">
                    <thead class="bg-[#0f172a] border-b border-slate-200 sticky-header text-[10px] uppercase tracking-wider rounded-t-[10px] text-white">
                        <tr>
                            <th class="px-3.5 py-3 font-semibold text-center text-white w-10 rounded-tl-[10px]">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1] cursor-pointer">
                            </th>
                            <th class="px-3.5 py-3 font-semibold text-left text-white">Brand</th>
                            <th class="px-3.5 py-3 font-semibold text-left text-white">Product Category</th>
                            <th class="px-3.5 py-3 font-semibold text-left text-white">SKU (QR Code)</th>
                            <th class="px-3.5 py-3 font-semibold text-left text-white">Location</th>
                            <th class="px-3.5 py-3 font-semibold text-left text-white">Compatible Models</th>
                            <th class="px-3.5 py-3 font-semibold text-center text-white">Reorder Level</th>
                            <th class="px-3.5 py-3 font-semibold text-center text-white rounded-tr-[10px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="divide-y divide-slate-200 text-xs">
                        <!-- Products will be loaded here -->
                        <tr class="hover:bg-slate-50">
                            <td colspan="8" class="px-6 py-8 text-center text-slate-500">No products found. Add one to get started.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div id="productOverlay" class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all z-10">
            <div class="px-6 py-5 bg-[#0f172a] relative flex items-start justify-between">
                <div>
                    <h3 id="modalTitle" class="text-lg font-bold text-white mb-0.5">Add Product</h3>
                    <p class="text-xs text-slate-300">Set product category, SKU, and motorcycle compatibility</p>
                </div>
                <button id="closeProductModal" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">✕</button>
            </div>

            <form id="productForm" class="p-6">
                <input type="hidden" id="productId" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Product Name (Brand)</label>
                        <input type="text" id="productName" placeholder="e.g., APIDO" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#6EC1D1] focus:ring-1 focus:ring-[#6EC1D1]/20 hover:border-slate-300 mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Product Category</label>
                        <select id="productCategory" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#6EC1D1] focus:ring-1 focus:ring-[#6EC1D1]/20 hover:border-slate-300 mt-1" required>
                            <option value="">Select category</option>
                            <option value="ENGINE OIL">ENGINE OIL</option>
                            <option value="GEAR OIL">GEAR OIL</option>
                            <option value="BRAKE FLUID (BRAKE OIL)">BRAKE FLUID (BRAKE OIL)</option>
                            <option value="COOLANT / RADIATOR COOLANT">COOLANT / RADIATOR COOLANT</option>
                            <option value="CVT CLEANER">CVT CLEANER</option>
                            <option value="TIRE SEALANT">TIRE SEALANT</option>
                            <option value="PIPE">PIPE</option>
                            <option value="SHOCK">SHOCK</option>
                            <option value="SWING ARM">SWING ARM</option>
                            <option value="ENGINE SUPPORT">ENGINE SUPPORT</option>
                            <option value="SIDE MIRROR">SIDE MIRROR</option>
                            <option value="TIRE HUGGER">TIRE HUGGER</option>
                            <option value="MONORACK FRAME">MONORACK FRAME</option>
                            <option value="QUICK THROTTLE">QUICK THROTTLE</option>
                            <option value="TIRE">TIRE</option>
                        </select>
                    </div>

                    <!-- Warehouse Selection Dropdown -->
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Warehouse Location <span class="text-red-500">*</span></label>
                        <select id="productWarehouse" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#6EC1D1] focus:ring-1 focus:ring-[#6EC1D1]/20 hover:border-slate-300 mt-1" required>
                            <option value="">Select warehouse</option>
                            <option value="Warehouse A">Warehouse A</option>
                            <option value="Warehouse B">Warehouse B</option>
                            <option value="Warehouse C">Warehouse C</option>
                            <option value="Warehouse D">Warehouse D</option>
                            <option value="SHOP">Shop (Main Store)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700">Reorder Level</label>
                        <input type="number" id="productReorderLevel" min="0" value="10" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#6EC1D1] focus:ring-1 focus:ring-[#6EC1D1]/20 hover:border-slate-300 mt-1" />
                    </div>

                    <!-- Extra Volume / Liter Field (for Oils / Fluids) -->
                    <div id="oilVolumeGroup" class="col-span-2 hidden bg-cyan-50/50 p-3 rounded-xl border border-cyan-200">
                        <label id="oilVolumeLabel" class="text-xs font-semibold text-slate-800 block mb-1">Volume / Liters (e.g., 800mL, 1L)</label>
                        <input type="text" id="productSize" placeholder="e.g., 800mL, 1L, 1.2L" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" />
                        <div id="oilVolumePills" class="mt-2 flex flex-wrap gap-1.5 items-center">
                            <span class="text-[11px] text-slate-500 font-medium mr-1">Quick volume:</span>
                            <button type="button" class="volume-pill px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 border border-slate-200 shadow-sm transition" data-volume="800mL">800mL</button>
                            <button type="button" class="volume-pill px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 border border-slate-200 shadow-sm transition" data-volume="1L">1L</button>
                            <button type="button" class="volume-pill px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 border border-slate-200 shadow-sm transition" data-volume="1.2L">1.2L</button>
                            <button type="button" class="volume-pill px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 border border-slate-200 shadow-sm transition" data-volume="120mL">120mL</button>
                            <button type="button" class="volume-pill px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 border border-slate-200 shadow-sm transition" data-volume="500mL">500mL</button>
                            <button type="button" class="volume-pill px-2.5 py-1 rounded-md text-xs font-semibold bg-white text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 border border-slate-200 shadow-sm transition" data-volume="4L">4L</button>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-700">SKU / QR Code</label>
                        <input type="text" id="productSku" placeholder="e.g., KCC_PIPE_APIDO_001" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300 mt-1" required />
                    </div>

                    <!-- General Item / Universal Checkbox -->
                    <div class="col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" id="isGeneralCheckbox" class="mt-0.5 rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2] w-4 h-4 cursor-pointer" />
                            <div>
                                <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>General Item / Universal</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Fits all models</span>
                                </span>
                                <p class="text-[11px] text-slate-500 mt-0.5">Check if this product does not require specific motorcycle models (e.g., Oils, universal accessories).</p>
                            </div>
                        </label>
                    </div>

                    <!-- Compatible Motorcycle Models Section -->
                    <div id="compatibleModelsSection" class="col-span-2">
                        <label class="text-xs font-semibold text-slate-700 mb-2 block">Compatible Motorcycle Models</label>
                        <div id="motorcycleList" class="space-y-2 max-h-40 overflow-y-auto border border-slate-200 rounded-lg p-3 bg-slate-50">
                            <!-- Motorcycle checkboxes will load here -->
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="CB150" class="motorcycle-checkbox rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]" />
                                <span class="text-xs text-slate-800">CB150</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="Wave110" class="motorcycle-checkbox rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]" />
                                <span class="text-xs text-slate-800">Wave 110</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="ADV160" class="motorcycle-checkbox rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]" />
                                <span class="text-xs text-slate-800">ADV 160</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="XRE300" class="motorcycle-checkbox rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]" />
                                <span class="text-xs text-slate-800">XRE 300</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 rounded-lg bg-[#6EC1D1] text-xs font-semibold text-black hover:bg-[#59b2c2] transition shadow-sm">Save Product</button>
                    <button type="button" id="cancelProductModal" class="px-5 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</button>
                    <button type="button" id="deleteProductBtn" class="ml-auto px-4 py-2 rounded-lg border border-red-200 text-xs font-semibold text-red-600 hover:bg-red-50 hidden">Move to Trash</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete List Modal (soft-delete picker) -->
    <div id="deleteListModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div id="deleteListOverlay" class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all z-10">
            <div class="px-6 py-5 bg-[#0f172a] relative flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white mb-0.5">Delete Products</h3>
                    <p class="text-xs text-slate-300">Select products to move to Trash — they can be restored anytime.</p>
                </div>
                <button id="closeDeleteListModal" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">✕</button>
            </div>

            <div class="p-6">
                <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-[#0f172a] border-b border-slate-200 sticky top-0 text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-white w-10">
                                    <input type="checkbox" id="selectAllDelete" class="rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]">
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-white">Brand</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">Category</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">SKU</th>
                            </tr>
                        </thead>
                        <tbody id="deleteListTableBody" class="divide-y divide-slate-200">
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-700"><span id="deleteSelectedCount">0</span> selected</span>
                    <div class="flex items-center gap-3">
                        <button id="cancelDeleteList" class="px-4 py-2 rounded-lg border-2 border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button id="confirmBulkDelete" class="px-4 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">Move to Trash</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trash / Restore Modal -->
    <div id="trashModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeTrashModal()"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-[28px] shadow-[0_30px_80px_rgba(15,23,42,0.18)] overflow-hidden transform transition-all z-10">
            <!-- Header (matching Add User Modal style) -->
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h3 class="text-xl font-bold text-black flex items-center gap-2">
                        Trash
                        <span class="text-sm font-medium text-slate-900">(<span id="trashCount">0</span> items)</span>
                    </h3>
                    <p class="text-sm text-slate-900 font-medium mt-0.5">Restore items or permanently delete them.</p>
                </div>
                <button id="closeTrashModal" type="button" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Table -->
                <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-[#0f172a] border-b border-slate-800 sticky top-0 text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-white w-10">
                                    <input type="checkbox" id="selectAllTrash" class="rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]">
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-white">Brand</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">Category</th>
                                <th class="px-4 py-3 text-left font-semibold text-white">SKU</th>
                            </tr>
                        </thead>
                        <tbody id="trashTableBody" class="divide-y divide-slate-100">
                            <!-- Deleted products loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Footer actions -->
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-600"><span id="trashSelectedCount">0</span> selected</span>
                    <div class="flex items-center gap-3">
                        <button onclick="closeTrashModal()" class="rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">Close</button>
                        <button id="restoreSelectedBtn"
                            class="rounded-[10px] bg-[#6EC1D1] px-4 py-2 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-1.5"
                            disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Restore Selected
                        </button>
                        <button id="permanentDeleteBtn"
                            class="rounded-[10px] bg-red-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                            disabled>
                            Delete Forever
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // Notification panel toggle function
        window.toggleNotificationPanel = function(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-panel');
            const dropdown = document.getElementById('dashboardProfileDropdown');

            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (dropdown) {
                        dropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                        dropdown.classList.remove('block', 'opacity-100', 'scale-100');
                    }
                } else {
                    panel.classList.add('hidden');
                }
            }
        };

        // Mark all notifications as read
        window.markAllNotificationsRead = function() {
            console.log('Mark all notifications as read');
        };

        // Open all notifications modal
        window.openAllNotificationsModal = function() {
            console.log('Open all notifications modal');
        };

        // Close notification panel when clicking outside
        window.addEventListener('click', function(e) {
            const panel = document.getElementById('notification-panel');
            const bellBtn = document.getElementById('notification-bell-btn');
            if (panel && !panel.classList.contains('hidden') && !panel.contains(e.target) && !bellBtn.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });
    </script>

    @vite('resources/js/product-categorization.js')
</x-layouts.app>
