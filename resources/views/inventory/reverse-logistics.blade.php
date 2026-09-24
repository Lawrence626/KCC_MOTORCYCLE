<x-layouts.app :title="__('Reverse Logistics')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-2 py-1 pt-2 pb-1 pl-1">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Reverse Logistics</h1>
                <p class="text-sm text-slate-500 mt-1">Manage returned products through inspection, repair, restocking, or disposal.</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <button id="openAddReturn" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-[#6EC1D1] text-slate-900 text-sm font-semibold hover:bg-[#59b2c2] transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Receive Returned Item</span>
                </button>
            </div>
        </div>

        <!-- Stats summary cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Returned Items</p>
                        <div class="mt-1">
                            <p id="statTotalReturns" class="text-2xl font-bold text-black">0</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Total received</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <polyline points="9 14 4 9 9 4"></polyline>
                            <path d="M20 20v-7a4 4 0 0 0-4-4H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Under Inspection</p>
                        <div class="mt-1">
                            <p id="statUnderReview" class="text-2xl font-bold text-black">0</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Awaiting check</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Ready for Restock</p>
                        <div class="mt-1">
                            <p id="statRestock" class="text-2xl font-bold text-black">0</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Approved items</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Under Repair</p>
                        <div class="mt-1">
                            <p id="statRepairQueue" class="text-2xl font-bold text-black">0</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Being repaired</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.4-2.4c.4-.4.4-1 0-1.3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="rounded-[10px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4 flex-1">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Search returned item</label>
                        <input id="searchInput" type="search" placeholder="Product, SKU, reason, warehouse" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm" />
                    </div>
                    <!-- Custom Dropdown Card: Status Filter -->
                    <div class="relative" data-dropdown-wrapper="statusFilter">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <input type="hidden" id="statusFilter" value="" />
                        <button type="button" id="statusFilterButton" onclick="toggleCustomDropdown('statusFilterDropdown', event)" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm">
                            <span id="statusFilterDisplay">All status</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="statusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('statusFilter', '', 'All status', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">All status</button>
                            <button type="button" onclick="selectCustomOption('statusFilter', 'Under Review', 'Under Review', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Under Review</button>
                            <button type="button" onclick="selectCustomOption('statusFilter', 'Pending Repair', 'Pending Repair', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Pending Repair</button>
                            <button type="button" onclick="selectCustomOption('statusFilter', 'Ready for Restock', 'Ready for Restock', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Ready for Restock</button>
                            <button type="button" onclick="selectCustomOption('statusFilter', 'Disposed', 'Disposed', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Disposed</button>
                        </div>
                    </div>

                    <!-- Custom Dropdown Card: Return Reason Filter -->
                    <div class="relative" data-dropdown-wrapper="reasonFilter">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Return reason</label>
                        <input type="hidden" id="reasonFilter" value="" />
                        <button type="button" id="reasonFilterButton" onclick="toggleCustomDropdown('reasonFilterDropdown', event)" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm">
                            <span id="reasonFilterDisplay">All reasons</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="reasonFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('reasonFilter', '', 'All reasons', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">All reasons</button>
                            <button type="button" onclick="selectCustomOption('reasonFilter', 'Defective', 'Defective', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Defective</button>
                            <button type="button" onclick="selectCustomOption('reasonFilter', 'Customer Return', 'Customer Return', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Customer Return</button>
                            <button type="button" onclick="selectCustomOption('reasonFilter', 'Wrong Item', 'Wrong Item Delivered', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Wrong Item Delivered</button>
                            <button type="button" onclick="selectCustomOption('reasonFilter', 'Quality Issue', 'Quality Issue', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Quality Issue</button>
                            <button type="button" onclick="selectCustomOption('reasonFilter', 'Damaged In Transit', 'Damaged In Transit', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Damaged In Transit</button>
                        </div>
                    </div>

                    <!-- Custom Dropdown Card: Item Condition Filter -->
                    <div class="relative" data-dropdown-wrapper="conditionFilter">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Item condition</label>
                        <input type="hidden" id="conditionFilter" value="" />
                        <button type="button" id="conditionFilterButton" onclick="toggleCustomDropdown('conditionFilterDropdown', event)" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm">
                            <span id="conditionFilterDisplay">All conditions</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="conditionFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('conditionFilter', '', 'All conditions', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">All conditions</button>
                            <button type="button" onclick="selectCustomOption('conditionFilter', 'Good', 'Good', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Good</button>
                            <button type="button" onclick="selectCustomOption('conditionFilter', 'Damaged', 'Damaged', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Damaged</button>
                            <button type="button" onclick="selectCustomOption('conditionFilter', 'Needs Repair', 'Needs Repair', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Needs Repair</button>
                            <button type="button" onclick="selectCustomOption('conditionFilter', 'Opened', 'Opened', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Opened</button>
                        </div>
                    </div>
                </div>
                <button id="clearFiltersBtn" class="px-3.5 py-2 rounded-[12px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shrink-0">Clear Filters</button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-[10px] border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto rounded-[10px]">
                <table class="w-full text-sm text-left whitespace-nowrap min-w-max">
                    <thead class="bg-[#0f172a] border-b border-slate-200 sticky-header text-xs uppercase tracking-wider rounded-t-[10px]">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-white rounded-tl-[10px]">Product</th>
                            <th class="px-4 py-3 font-semibold text-white">SKU</th>
                            <th class="px-4 py-3 font-semibold text-white">Return Reason</th>
                            <th class="px-4 py-3 font-semibold text-white">Condition</th>
                            <th class="px-4 py-3 font-semibold text-white">Quantity</th>
                            <th class="px-4 py-3 font-semibold text-white">Warehouse</th>
                            <th class="px-4 py-3 font-semibold text-white">Date Received</th>
                            <th class="px-4 py-3 font-semibold text-white">Status</th>
                            <th class="px-4 py-3 font-semibold text-center text-white rounded-tr-[10px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody" class="divide-y divide-slate-200 text-xs bg-white">
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">No reverse logistics records yet. Click “Receive Returned Item” to create your first record.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Log/Edit Return Modal -->
    <div id="returnModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div id="modalOverlay" class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl"></div>
        <div class="relative w-full max-w-3xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] z-10 flex flex-col">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5 shrink-0">
                <div>
                    <h2 id="modalTitle" class="text-xl font-bold text-black">Log a Returned Item</h2>
                    <p class="text-sm text-slate-900 font-medium">Capture return details, repair notes, and restock decisions in one place.</p>
                </div>
                <button id="closeModalBtn" type="button" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="returnForm" class="p-4 sm:p-5 pb-32 overflow-y-auto max-h-[calc(90vh-100px)] space-y-4 text-xs">
                <input type="hidden" id="recordId" />
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Product Name <span class="text-red-500">*</span></label>
                        <input id="productName" type="text" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="Example: Premium Brake Pad" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">SKU <span class="text-red-500">*</span></label>
                        <input id="sku" type="text" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="Example: BRK-PLD-09" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Quantity <span class="text-red-500">*</span></label>
                        <input id="quantity" type="number" min="1" value="1" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Warehouse <span class="text-red-500">*</span></label>
                        <input id="warehouse" type="text" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="Main Store / Service Bay" required />
                    </div>

                    <!-- Custom Dropdown Card: Return Reason -->
                    <div class="space-y-1 relative" data-dropdown-wrapper="returnReason">
                        <label class="block text-xs font-medium text-slate-700">Return Reason <span class="text-red-500">*</span></label>
                        <select id="returnReason" class="hidden" required>
                            <option value="Defective" selected>Defective</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Damaged In Transit">Damaged In Transit</option>
                        </select>
                        <button type="button" id="returnReasonButton" onclick="toggleCustomDropdown('returnReasonDropdown', event)" class="mt-1 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm">
                            <span id="returnReasonDisplay">Defective</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="returnReasonDropdown" class="dropdown-menu hidden absolute top-full right-0 z-[999] mt-1 w-[50%] max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('returnReason', 'Defective', 'Defective', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Defective</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Customer Return', 'Customer Return', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Customer Return</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Wrong Item Delivered', 'Wrong Item Delivered', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Wrong Item Delivered</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Quality Issue', 'Quality Issue', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Quality Issue</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Damaged In Transit', 'Damaged In Transit', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Damaged In Transit</button>
                        </div>
                    </div>

                    <!-- Custom Dropdown Card: Condition -->
                    <div class="space-y-1 relative" data-dropdown-wrapper="condition">
                        <label class="block text-xs font-medium text-slate-700">Condition <span class="text-red-500">*</span></label>
                        <select id="condition" class="hidden" required>
                            <option value="Good" selected>Good</option>
                            <option value="Opened">Opened</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Needs Repair">Needs Repair</option>
                        </select>
                        <button type="button" id="conditionButton" onclick="toggleCustomDropdown('conditionDropdown', event)" class="mt-1 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm">
                            <span id="conditionDisplay">Good</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="conditionDropdown" class="dropdown-menu hidden absolute top-full right-0 z-[999] mt-1 w-[50%] max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('condition', 'Good', 'Good', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Good</button>
                            <button type="button" onclick="selectCustomOption('condition', 'Opened', 'Opened', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Opened</button>
                            <button type="button" onclick="selectCustomOption('condition', 'Damaged', 'Damaged', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Damaged</button>
                            <button type="button" onclick="selectCustomOption('condition', 'Needs Repair', 'Needs Repair', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Needs Repair</button>
                        </div>
                    </div>

                    <!-- Custom Dropdown Card: Source -->
                    <div class="space-y-1 relative" data-dropdown-wrapper="source">
                        <label class="block text-xs font-medium text-slate-700">Source <span class="text-red-500">*</span></label>
                        <select id="source" class="hidden" required>
                            <option value="Customer Return" selected>Customer Return</option>
                            <option value="Supplier Return">Supplier Return</option>
                            <option value="Quality Inspection">Quality Inspection</option>
                            <option value="Service Center">Service Center</option>
                        </select>
                        <button type="button" id="sourceButton" onclick="toggleCustomDropdown('sourceDropdown', event)" class="mt-1 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm">
                            <span id="sourceDisplay">Customer Return</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="sourceDropdown" class="dropdown-menu hidden absolute top-full right-0 z-[999] mt-1 w-[50%] max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('source', 'Customer Return', 'Customer Return', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Customer Return</button>
                            <button type="button" onclick="selectCustomOption('source', 'Supplier Return', 'Supplier Return', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Supplier Return</button>
                            <button type="button" onclick="selectCustomOption('source', 'Quality Inspection', 'Quality Inspection', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Quality Inspection</button>
                            <button type="button" onclick="selectCustomOption('source', 'Service Center', 'Service Center', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Service Center</button>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Reported Date <span class="text-red-500">*</span></label>
                        <input id="reportedDate" type="date" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                    </div>

                    <!-- Custom Dropdown Card: Status -->
                    <div class="space-y-1 relative" data-dropdown-wrapper="status">
                        <label class="block text-xs font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                        <select id="status" class="hidden" required>
                            <option value="Under Review" selected>Under Review</option>
                            <option value="Pending Repair">Pending Repair</option>
                            <option value="Ready for Restock">Ready for Restock</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                        <button type="button" id="statusButton" onclick="toggleCustomDropdown('statusDropdown', event)" class="mt-1 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm">
                            <span id="statusDisplay">Under Review</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="statusDropdown" class="dropdown-menu hidden absolute top-full right-0 z-[999] mt-1 w-[50%] max-h-52 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('status', 'Under Review', 'Under Review', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Under Review</button>
                            <button type="button" onclick="selectCustomOption('status', 'Pending Repair', 'Pending Repair', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Pending Repair</button>
                            <button type="button" onclick="selectCustomOption('status', 'Ready for Restock', 'Ready for Restock', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Ready for Restock</button>
                            <button type="button" onclick="selectCustomOption('status', 'Disposed', 'Disposed', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-2 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-100 transition">Disposed</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-700">Notes</label>
                    <textarea id="notes" rows="2" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 resize-none" placeholder="Add additional context, inspection notes, or follow-up actions"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <button type="button" id="cancelModalBtn" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200">Cancel</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Save record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const recordsTableBody = document.getElementById('recordsTableBody');
        const statTotalReturns = document.getElementById('statTotalReturns');
        const statUnderReview = document.getElementById('statUnderReview');
        const statRestock = document.getElementById('statRestock');
        const statRepairQueue = document.getElementById('statRepairQueue');
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const reasonFilter = document.getElementById('reasonFilter');
        const conditionFilter = document.getElementById('conditionFilter');
        const openAddReturn = document.getElementById('openAddReturn');
        const returnModal = document.getElementById('returnModal');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const deleteRecordBtn = document.getElementById('deleteRecordBtn');
        const recordIdInput = document.getElementById('recordId');
        const modalTitle = document.getElementById('modalTitle');
        const returnForm = document.getElementById('returnForm');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');

        const inputs = {
            productName: document.getElementById('productName'),
            sku: document.getElementById('sku'),
            quantity: document.getElementById('quantity'),
            warehouse: document.getElementById('warehouse'),
            returnReason: document.getElementById('returnReason'),
            condition: document.getElementById('condition'),
            source: document.getElementById('source'),
            status: document.getElementById('status'),
            reportedDate: document.getElementById('reportedDate'),
            notes: document.getElementById('notes'),
        };

        let records = [];
        let filteredRecords = [];
        let editingId = null;

        async function loadRecords() {
            try {
                const response = await fetch('{{ route('api.reverse-logistics.index') }}');
                const result = await response.json();
                
                if (result.data) {
                    records = result.data;
                    filteredRecords = [...records];
                    renderStats();
                    applyFilters();
                }
            } catch (error) {
                console.error('Error loading records:', error);
            }
        }

        async function saveRecord(recordData) {
            try {
                const baseUrl = '{{ url('api/reverse-logistics') }}';
                const url = editingId 
                    ? `${baseUrl}/${editingId}`
                    : baseUrl;
                
                const method = editingId ? 'POST' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(recordData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await loadRecords();
                    return true;
                } else {
                    alert('Error saving record: ' + (result.message || 'Unknown error'));
                    return false;
                }
            } catch (error) {
                console.error('Error saving record:', error);
                alert('Error saving record: ' + error.message);
                return false;
            }
        }

        async function deleteRecord(id) {
            try {
                const response = await fetch(`{{ url('api/reverse-logistics') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await loadRecords();
                    return true;
                } else {
                    alert('Error deleting record: ' + (result.message || 'Unknown error'));
                    return false;
                }
            } catch (error) {
                console.error('Error deleting record:', error);
                alert('Error deleting record: ' + error.message);
                return false;
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function getStatusBadge(status) {
            const classes = {
                'Under Review': 'bg-[#105f68] text-white',
                'Pending Repair': 'bg-blue-600 text-white',
                'Ready for Restock': 'bg-[#6EC1D1] text-black',
                'Disposed': 'bg-[#0f172a] text-white',
            };
            return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${classes[status] || 'bg-slate-200 text-slate-700'}">${status}</span>`;
        }

        function renderStats() {
            statTotalReturns.textContent = records.length;
            statUnderReview.textContent = records.filter(item => item.status === 'Under Review').length;
            statRestock.textContent = records.filter(item => item.status === 'Ready for Restock').length;
            statRepairQueue.textContent = records.filter(item => item.status === 'Pending Repair').length;
        }

        function renderTable() {
            if (!filteredRecords.length) {
                recordsTableBody.innerHTML = '<tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">No records match your filters or the list is empty. Try changing the search or adding a new return.</td></tr>';
                return;
            }

            recordsTableBody.innerHTML = filteredRecords.map(record => `
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold text-slate-900">${record.product_name}</td>
                    <td class="px-4 py-3 text-slate-600">${record.sku}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] text-slate-700 font-medium">${record.return_reason}</span></td>
                    <td class="px-4 py-3 text-slate-600">${record.condition}</td>
                    <td class="px-4 py-3 text-slate-600 font-semibold">${record.quantity}</td>
                    <td class="px-4 py-3 text-slate-600">${record.warehouse}</td>
                    <td class="px-4 py-3 text-slate-600">${formatDate(record.reported_date)}</td>
                    <td class="px-4 py-3">${getStatusBadge(record.status)}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="relative inline-block">
                            <button onclick="toggleActionMenu(${record.id})" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div id="action-menu-${record.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10 text-xs">
                                <button onclick="viewDetails(${record.id})" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </button>
                                ${record.status === 'Under Review' ? `
                                <button onclick="updateStatus(${record.id}, 'Ready for Restock')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Restock
                                </button>
                                <button onclick="updateStatus(${record.id}, 'Pending Repair')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Send to Repair
                                </button>
                                <button onclick="updateStatus(${record.id}, 'Disposed')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Send to Disposal
                                </button>
                                ` : ''}
                                ${record.status === 'Pending Repair' ? `
                                <button onclick="updateStatus(${record.id}, 'Ready for Restock')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Restock
                                </button>
                                ` : ''}
                                ${record.status === 'Ready for Restock' ? `
                                <button onclick="processRestock(${record.id})" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Process Return
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function applyFilters() {
            const search = searchInput.value.trim().toLowerCase();
            const status = statusFilter.value;
            const reason = reasonFilter.value;
            const condition = conditionFilter.value;

            filteredRecords = records.filter(record => {
                const matchesSearch = search === '' || [
                    record.product_name,
                    record.sku,
                    record.return_reason,
                    record.condition,
                    record.warehouse,
                    record.source,
                    record.notes,
                ].some(value => String(value).toLowerCase().includes(search));

                const matchesStatus = !status || record.status === status;
                const matchesReason = !reason || record.return_reason === reason;
                const matchesCondition = !condition || record.condition === condition;

                return matchesSearch && matchesStatus && matchesReason && matchesCondition;
            });

            renderTable();
        }

        function toggleCustomDropdown(id, event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById(id);
            if (!menu) return;
            const isHidden = menu.classList.contains('hidden');
            
            document.querySelectorAll('.custom-calendar-card').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                m.classList.add('hidden');
                const bId = m.id.replace('Dropdown', 'Button');
                const btn = document.getElementById(bId);
                if (btn) btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
            });
            
            if (isHidden) {
                menu.classList.remove('hidden');
                const bId = id.replace('Dropdown', 'Button');
                const btn = document.getElementById(bId);
                if (btn) btn.classList.add('ring-1', 'ring-black/35', 'border-transparent');
            }
        }

        function selectCustomOption(selectId, value, displayText, displayId, dropdownId) {
            const selectElem = document.getElementById(selectId);
            const displayElem = document.getElementById(displayId);
            if (selectElem) {
                selectElem.value = value;
                selectElem.dispatchEvent(new Event('change'));
            }
            if (displayElem) displayElem.textContent = displayText || value || 'Select...';
            const menu = document.getElementById(dropdownId);
            if (menu) menu.classList.add('hidden');
        }

        window.toggleCustomDropdown = toggleCustomDropdown;
        window.selectCustomOption = selectCustomOption;

        function resetForm() {
            recordIdInput.value = '';
            editingId = null;
            modalTitle.textContent = 'Log a Returned Item';
            deleteRecordBtn?.classList.add('hidden');
            returnForm.reset();
            inputs.reportedDate.value = new Date().toISOString().split('T')[0];

            if (document.getElementById('returnReasonDisplay')) document.getElementById('returnReasonDisplay').textContent = 'Select reason';
            if (document.getElementById('conditionDisplay')) document.getElementById('conditionDisplay').textContent = 'Select condition';
            if (document.getElementById('sourceDisplay')) document.getElementById('sourceDisplay').textContent = 'Select source';
            if (document.getElementById('statusDisplay')) document.getElementById('statusDisplay').textContent = 'Under Review';
        }

        function openModal() {
            resetForm();
            returnModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            returnModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openEditRecord(id) {
            const record = records.find(item => item.id === id);
            if (!record) return;

            editingId = id;
            recordIdInput.value = id;
            modalTitle.textContent = 'Update Returned Item';
            deleteRecordBtn?.classList.remove('hidden');

            inputs.productName.value = record.product_name;
            inputs.sku.value = record.sku;
            inputs.quantity.value = record.quantity;
            inputs.warehouse.value = record.warehouse;
            inputs.returnReason.value = record.return_reason;
            inputs.condition.value = record.condition;
            inputs.source.value = record.source;
            inputs.status.value = record.status;
            inputs.reportedDate.value = record.reported_date;
            inputs.notes.value = record.notes || '';

            if (document.getElementById('returnReasonDisplay')) document.getElementById('returnReasonDisplay').textContent = record.return_reason || 'Select reason';
            if (document.getElementById('conditionDisplay')) document.getElementById('conditionDisplay').textContent = record.condition || 'Select condition';
            if (document.getElementById('sourceDisplay')) document.getElementById('sourceDisplay').textContent = record.source || 'Select source';
            if (document.getElementById('statusDisplay')) document.getElementById('statusDisplay').textContent = record.status || 'Under Review';

            returnModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function toggleActionMenu(id) {
            const menu = document.getElementById(`action-menu-${id}`);
            const allMenus = document.querySelectorAll('[id^="action-menu-"]');
            
            allMenus.forEach(m => {
                if (m.id !== menu.id) {
                    m.classList.add('hidden');
                }
            });
            
            menu.classList.toggle('hidden');
        }

        function viewDetails(id) {
            const record = records.find(item => item.id === id);
            if (!record) return;
            
            alert(`Product: ${record.product_name}\nSKU: ${record.sku}\nReason: ${record.return_reason}\nCondition: ${record.condition}\nQuantity: ${record.quantity}\nWarehouse: ${record.warehouse}\nStatus: ${record.status}\nNotes: ${record.notes || 'None'}`);
        }

        async function updateStatus(id, newStatus) {
            const record = records.find(item => item.id === id);
            if (!record) return;

            if (newStatus === 'Disposed') {
                if (!confirm('Send this item to disposal? This will create a record in the Item Disposal module.')) {
                    return;
                }

                // Call the disposal integration endpoint
                try {
                    const response = await fetch(`{{ url('item/disposal/from-reverse-logistics') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Item successfully added to Item Disposal module.');
                    } else {
                        alert('Failed to add to disposal: ' + (result.message || 'Unknown error'));
                        return;
                    }
                } catch (error) {
                    console.error('Error adding to disposal:', error);
                    alert('Error adding to disposal: ' + error.message);
                    return;
                }
            }

            const payload = {
                ...record,
                status: newStatus
            };

            editingId = id;
            const success = await saveRecord(payload);
            if (success) {
                // Close all menus
                document.querySelectorAll('[id^="action-menu-"]').forEach(m => m.classList.add('hidden'));
            }
        }

        async function processRestock(id) {
            const record = records.find(item => item.id === id);
            if (!record) return;

            if (!confirm(`Process return for ${record.product_name}? This will add ${record.quantity} units back to inventory.`)) {
                return;
            }

            // TODO: Implement actual restock logic to add to main inventory
            alert(`Restock processed for ${record.product_name}. (Inventory update not yet implemented)`);
            
            // Close all menus
            document.querySelectorAll('[id^="action-menu-"]').forEach(m => m.classList.add('hidden'));
        }

        async function deleteCurrentRecord() {
            if (!editingId) return;
            if (confirm('Delete this reverse logistics record?')) {
                const success = await deleteRecord(editingId);
                if (success) {
                    closeModal();
                }
            }
        }

        returnForm.addEventListener('submit', async event => {
            event.preventDefault();

            const payload = {
                product_name: inputs.productName.value.trim(),
                sku: inputs.sku.value.trim(),
                quantity: Number(inputs.quantity.value),
                warehouse: inputs.warehouse.value.trim(),
                return_reason: inputs.returnReason.value,
                condition: inputs.condition.value,
                source: inputs.source.value,
                status: inputs.status.value,
                reported_date: inputs.reportedDate.value,
                notes: inputs.notes.value.trim(),
            };

            const success = await saveRecord(payload);
            if (success) {
                closeModal();
            }
        });

        inputs.productName.addEventListener('input', function() {
            const productName = this.value.trim();
            const generatedSku = `KCC_${productName.replace(/[^A-Za-z0-9\-\+]/g, '')}`;
            inputs.sku.value = generatedSku;
        });

        openAddReturn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', closeModal);
        deleteRecordBtn?.addEventListener('click', deleteCurrentRecord);
        clearFiltersBtn.addEventListener('click', () => {
            searchInput.value = '';
            statusFilter.value = '';
            reasonFilter.value = '';
            conditionFilter.value = '';

            if (document.getElementById('statusFilterDisplay')) document.getElementById('statusFilterDisplay').textContent = 'All status';
            if (document.getElementById('reasonFilterDisplay')) document.getElementById('reasonFilterDisplay').textContent = 'All reasons';
            if (document.getElementById('conditionFilterDisplay')) document.getElementById('conditionFilterDisplay').textContent = 'All conditions';

            applyFilters();
        });

        [searchInput, statusFilter, reasonFilter, conditionFilter].forEach(element => {
            element.addEventListener('input', applyFilters);
            element.addEventListener('change', applyFilters);
        });

        // Close action menus & custom dropdown cards when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleActionMenu"]') && !e.target.closest('[id^="action-menu-"]')) {
                document.querySelectorAll('[id^="action-menu-"]').forEach(m => {
                    m.classList.add('hidden');
                });
            }
            if (!e.target.closest('[data-dropdown-wrapper]') && !e.target.closest('.dropdown-menu') && !e.target.closest('[onclick^="toggleCustomDropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                    const bId = menu.id.replace('Dropdown', 'Button');
                    const btn = document.getElementById(bId);
                    if (btn) btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                });
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !returnModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Custom Date Picker Setup matching Inventory Monitoring UI
        function setupCustomDatePicker(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.type = 'text';
            input.readOnly = true;
            input.placeholder = 'YYYY-MM-DD';
            input.className = 'w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 pr-10 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 cursor-pointer';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative w-full mt-0 z-[10]';
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
            card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-1 z-[90] w-full rounded-[12px] bg-white p-1.5 shadow-[0_12px_32px_rgba(0,0,0,0.12)] border border-slate-200 transition-all duration-200';
            wrapper.appendChild(card);

            let currentDate = new Date();
            let selectedDate = input.value ? new Date(input.value) : null;
            let viewMode = 'days';

            function render() {
                if (input.value) {
                    const parsed = new Date(input.value);
                    if (!isNaN(parsed.getTime())) {
                        selectedDate = parsed;
                    }
                } else {
                    selectedDate = null;
                }

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
                    <div class="flex items-center justify-between mb-0.5 px-0.5">
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
                    html += `<span class="h-5 flex items-center justify-center text-slate-300 text-[11px]">${daysInPrevMonth - i}</span>`;
                }

                const today = new Date();
                for (let day = 1; day <= daysInMonth; day++) {
                    const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                    const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                    let dayClasses = "h-5 w-5 mx-auto flex items-center justify-center rounded font-medium cursor-pointer transition-all duration-150 text-[11px] ";
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
                    html += `<span class="h-5 flex items-center justify-center text-slate-300 text-[11px]">${i}</span>`;
                }

                html += `
                    </div>
                    <div class="flex items-center justify-between mt-0.5 pt-0.5 border-t border-slate-100 text-[11px] font-semibold px-0.5">
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
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                    const bId = menu.id.replace('Dropdown', 'Button');
                    const btn = document.getElementById(bId);
                    if (btn) btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                });
                document.querySelectorAll('.custom-calendar-card').forEach(c => {
                    if (c !== card) c.classList.add('hidden');
                });
                card.classList.toggle('hidden');
                const isOpen = !card.classList.contains('hidden');
                if (isOpen) {
                    if (input.value) {
                        const parts = input.value.split('-');
                        if (parts.length === 3) {
                            currentDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                        }
                    }
                    render();
                }
                setIconActive(isOpen);
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    card.classList.add('hidden');
                    setIconActive(false);
                }
            });
        }

        setupCustomDatePicker('reportedDate');

        // Load initial data
        loadRecords();
    </script>
    @vite('resources/js/reverse-logistics.js')
</x-layouts.app>

