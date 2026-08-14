<x-layouts.app :title="__('Product Categorization')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-2 py-1">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Product Categorization</h1>
                <p class="text-sm text-slate-500 mt-1">Add, update, and delete product categories. Set SKU (QR code) and filter compatibility for each motorcycle.</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Bulk Actions Toolbar -->
                <div id="bulkActionsToolbar" class="items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200" style="display:none">
                    <span class="text-xs text-slate-700 font-medium"><span id="selectedCount">0</span> selected</span>
                    <button id="bulkDeleteBtn" class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">
                        Move to Trash
                    </button>
                    <button id="clearSelectionBtn" class="text-xs text-slate-600 hover:text-slate-800 font-medium">Clear</button>
                </div>
                <!-- Trash Button with badge -->
                <button id="openTrashBtn" class="relative inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Trash</span>
                    <span id="trashBadge" class="absolute -top-1.5 -right-1.5 items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs font-bold" style="display:none">0</span>
                </button>
                <button id="openDeleteList" class="px-4 py-2 rounded-lg border border-red-200 bg-white text-red-600 text-sm font-semibold hover:bg-red-50 transition shadow-sm">Delete List</button>
                <button id="openAddProduct" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-[#00fff2] text-slate-900 text-sm font-semibold hover:bg-[#00e6da] transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Add Product</span>
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="rounded-[10px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search by brand, product description, or SKU..." class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                </div>
                <button id="clearFilterBtn" class="px-3.5 py-2 rounded-[12px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Clear Filter
                </button>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-hidden rounded-[10px] border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto rounded-[10px]">
                <table class="w-full text-xs">
                    <thead class="bg-[#0f172a] border-b border-slate-200 rounded-t-[10px]">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white w-10 rounded-tl-[10px]">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2] cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Brand</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product Description</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU (QR Code)</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Location</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Compatible Models</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Reorder Level</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white rounded-tr-[10px]">Actions</th>
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
        <div id="productOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all z-10">
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
                        <input type="text" id="productName" placeholder="e.g., APIDO" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300 mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Product Description (Category)</label>
                        <select id="productCategory" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300 mt-1" required>
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
                        <label class="text-xs font-semibold text-slate-700">SKU / QR Code</label>
                        <input type="text" id="productSku" placeholder="e.g., PIPE-APIDO" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300 mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Reorder Level</label>
                        <input type="number" id="productReorderLevel" min="0" value="10" class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300 mt-1" />
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-slate-700 mb-2 block">Compatible Motorcycle Models</label>
                        <div id="motorcycleList" class="space-y-2 max-h-40 overflow-y-auto border border-slate-200 rounded-lg p-3 bg-slate-50">
                            <!-- Motorcycle checkboxes will load here -->
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="CB150" class="motorcycle-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" />
                                <span class="text-xs text-slate-800">CB150</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="Wave110" class="motorcycle-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" />
                                <span class="text-xs text-slate-800">Wave 110</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="ADV160" class="motorcycle-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" />
                                <span class="text-xs text-slate-800">ADV 160</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="XRE300" class="motorcycle-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" />
                                <span class="text-xs text-slate-800">XRE 300</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 rounded-lg bg-[#00fff2] text-xs font-semibold text-black hover:bg-[#00e6da] transition shadow-sm">Save Product</button>
                    <button type="button" id="cancelProductModal" class="px-5 py-2 rounded-lg border-2 border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">Cancel</button>
                    <button type="button" id="deleteProductBtn" class="ml-auto px-4 py-2 rounded-lg border border-red-200 text-xs font-semibold text-red-600 hover:bg-red-50 hidden">Move to Trash</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete List Modal (soft-delete picker) -->
    <div id="deleteListModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div id="deleteListOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all z-10">
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
                        <thead class="bg-[#0f172a] border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white w-10">
                                    <input type="checkbox" id="selectAllDelete" class="rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]">
                                </th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Brand</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Category</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
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
        <div class="relative w-full max-w-2xl bg-white rounded-[28px] border border-slate-200 shadow-[0_30px_80px_rgba(15,23,42,0.18)] overflow-hidden transform transition-all z-10">
            <!-- Header (matching Add User Modal style) -->
            <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                <div>
                    <h3 class="text-xl font-bold text-black flex items-center gap-2">
                        Trash
                        <span class="text-sm font-medium text-slate-800">(<span id="trashCount">0</span> items)</span>
                    </h3>
                    <p class="text-sm text-slate-800 font-medium mt-0.5">Restore items or permanently delete them.</p>
                </div>
                <button id="closeTrashModal" type="button" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Table -->
                <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-[#0f172a] border-b border-slate-800 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white w-10">
                                    <input type="checkbox" id="selectAllTrash" class="rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]">
                                </th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Brand</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Category</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
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
                            class="rounded-[10px] bg-[#00fff2] px-4 py-2 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#00e6da] transition disabled:opacity-40 disabled:cursor-not-allowed inline-flex items-center gap-1.5"
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

    @vite('resources/js/product-categorization.js')
</x-layouts.app>
