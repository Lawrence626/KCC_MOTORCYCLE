<x-layouts.app :title="__('Product Categorization')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Product Categorization</h1>
                <p class="text-xs text-slate-500 mt-1">Add, update, and delete product categories. Set SKU (QR code) and filter compatibility for each motorcycle.</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Bulk Actions Toolbar -->
                <div id="bulkActionsToolbar" class="items-center gap-3 bg-slate-50 px-4 py-2 rounc:\Users\ilano\Herd\KCC_MOTORCYCLE c:\Users\ilano\Herd\Kcc_chassisded-lg border border-slate-200" style="display:none">
                    <span class="text-sm text-slate-700"><span id="selectedCount">0</span> selected</span>
                    <button id="bulkDeleteBtn" class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition">
                        Move to Trash
                    </button>
                    <button id="clearSelectionBtn" class="text-sm text-slate-600 hover:text-slate-800">Clear</button>
                </div>
                <!-- Trash Button with badge -->
                <button id="openTrashBtn" class="relative px-4 py-2 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Trash
                    <span id="trashBadge" class="absolute -top-1.5 -right-1.5 items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs font-bold" style="display:none">0</span>
                </button>
                <button id="openDeleteList" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 font-semibold hover:bg-red-50 transition">Delete List</button>
                <button id="openAddProduct" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">+ Add Product</button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search by brand, product description, or SKU..." class="w-full px-4 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
                <button id="clearFilterBtn" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                    Clear Filter
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900 w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            </th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Brand</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Product Description</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">SKU (QR Code)</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Location</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Compatible Models</th>
                            <th class="px-6 py-3 text-center font-semibold text-slate-900">Reorder Level</th>
                            <th class="px-6 py-3 text-center font-semibold text-slate-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="divide-y divide-slate-200">
                        <!-- Products will be loaded here -->
                        <tr class="hover:bg-slate-50">
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No products found. Add one to get started.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div id="productOverlay" class="absolute inset-0 bg-black/40"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl border border-slate-200 p-6 z-10">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-slate-900">Add Product</h3>
                    <p class="text-xs text-slate-500">Set product category, SKU, and motorcycle compatibility</p>
                </div>
                <button id="closeProductModal" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form id="productForm">
                <input type="hidden" id="productId" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Product Name (Brand)</label>
                        <input type="text" id="productName" placeholder="e.g., APIDO" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Product Description (Category)</label>
                        <select id="productCategory" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required>
                            <option value="">Select category</option>
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
                    <div class="col-span-2">
                        <label class="text-xs text-slate-500 font-medium">SKU / QR Code</label>
                        <input type="text" id="productSku" placeholder="e.g., PIPE-APIDO" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Reorder Level</label>
                        <input type="number" id="productReorderLevel" min="0" value="10" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" />
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-slate-500 font-medium mb-2 block">Compatible Motorcycle Models</label>
                        <div id="motorcycleList" class="space-y-2 max-h-40 overflow-y-auto">
                            <!-- Motorcycle checkboxes will load here -->
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="CB150" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">CB150</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="Wave110" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">Wave 110</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="ADV160" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">ADV 160</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="XRE300" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">XRE 300</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">Save Product</button>
                    <button type="button" id="cancelProductModal" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Cancel</button>
                    <button type="button" id="deleteProductBtn" class="ml-auto px-4 py-2 rounded-lg border border-red-200 text-sm text-red-600 hover:bg-red-50 hidden">Move to Trash</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete List Modal (soft-delete picker) -->
    <div id="deleteListModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div id="deleteListOverlay" class="absolute inset-0 bg-black/40"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl border border-slate-200 p-6 z-10">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Delete Products</h3>
                    <p class="text-xs text-slate-500">Select products to move to Trash — they can be restored anytime.</p>
                </div>
                <button id="closeDeleteListModal" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <div class="max-h-96 overflow-y-auto border border-slate-100 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900 w-10">
                                <input type="checkbox" id="selectAllDelete" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            </th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900">Brand</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900">Category</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900">SKU</th>
                        </tr>
                    </thead>
                    <tbody id="deleteListTableBody" class="divide-y divide-slate-200">
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <span class="text-sm text-slate-700"><span id="deleteSelectedCount">0</span> selected</span>
                <div class="flex items-center gap-3">
                    <button id="cancelDeleteList" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Cancel</button>
                    <button id="confirmBulkDelete" class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition">Move to Trash</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Trash / Restore Modal -->
    <div id="trashModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40" onclick="closeTrashModal()"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl border border-slate-200 p-6 z-10">
            <!-- Header -->
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Trash
                        <span class="text-sm font-normal text-slate-400">(<span id="trashCount">0</span> items)</span>
                    </h3>
                    <p class="text-xs text-slate-500">Restore items or permanently delete them.</p>
                </div>
                <button id="closeTrashModal" class="text-slate-400 hover:text-slate-600 text-xl leading-none">✕</button>
            </div>

            <!-- Table -->
            <div class="max-h-96 overflow-y-auto border border-slate-100 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900 w-10">
                                <input type="checkbox" id="selectAllTrash" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            </th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900">Brand</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900">Category</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-900">SKU</th>
                        </tr>
                    </thead>
                    <tbody id="trashTableBody" class="divide-y divide-slate-100">
                        <!-- Deleted products loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Footer actions -->
            <div class="mt-4 flex items-center justify-between">
                <span class="text-sm text-slate-600"><span id="trashSelectedCount">0</span> selected</span>
                <div class="flex items-center gap-3">
                    <button onclick="closeTrashModal()" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700 hover:bg-slate-50">Close</button>
                    <button id="restoreSelectedBtn"
                        class="px-4 py-2 rounded-lg border border-cyan-200 text-cyan-700 font-semibold hover:bg-cyan-50 transition disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        ↩ Restore Selected
                    </button>
                    <button id="permanentDeleteBtn"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        Delete Forever
                    </button>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/product-categorization.js')
</x-layouts.app>
