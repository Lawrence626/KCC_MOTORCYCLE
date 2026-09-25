    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Reverse Logistics</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage returned products through inspection, repair, restocking, or disposal.</p>
            </div>
            <button id="openAddReturn" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#6EC1D1] text-slate-900 text-xs font-semibold hover:bg-[#59b2c2] transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Receive Returned Item</span>
            </button>
        </div>
    </x-slot>
    <div class="space-y-4">

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
                    <button type="button" id="deleteRecordBtn" class="hidden inline-flex items-center justify-center gap-2 rounded-[10px] bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-rose-700 transition-all duration-200">Delete</button>
                    <button type="button" id="cancelModalBtn" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200">Cancel</button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Save record
                    </button>
                </div>
            </form>
        </div>
    </div>


    @vite('resources/js/reverse-logistics.js')
</x-layouts.app>

