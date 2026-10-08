<x-layouts.app :title="__('Reverse Logistics')">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Reverse Logistics</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage returned products through inspection, repair, restocking, or disposal.</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="openAddReturn" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-[10px] bg-[#6EC1D1] text-slate-900 text-xs font-semibold hover:bg-[#59b2c2] transition shadow-sm shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Receive Returned Item</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Stats summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Total Received</p>
                        <div class="mt-1">
                            <p id="statTotalReturns" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">all time</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l-5-5m0 0l5-5m-5 5h12a4 4 0 014 4v7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Under Inspection</p>
                        <div class="mt-1">
                            <p id="statUnderReview" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">awaiting review</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Ready to Restock</p>
                        <div class="mt-1">
                            <p id="statRestock" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">approved items</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Under Repair</p>
                        <div class="mt-1">
                            <p id="statRepairQueue" class="text-2xl font-bold text-black">--</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">in repair bay</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="rounded-[10px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2.5 items-end">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Search Returns</label>
                    <input id="searchInput" type="search" placeholder="Search product, SKU, reason..." class="w-full px-3.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-xs sm:text-[13px] text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-400 focus:border-slate-400 hover:border-slate-400 transition shadow-2xs" />
                </div>

                <!-- Custom Dropdown: Status Filter -->
                <div class="relative z-[30]" data-dropdown-wrapper="statusFilter">
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                    <input type="hidden" id="statusFilter" value="" />
                    <button type="button" id="statusFilterButton" onclick="toggleCustomDropdown('statusFilterDropdown', event)" class="w-full px-3.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-left text-xs sm:text-[13px] text-slate-800 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-2xs cursor-pointer">
                        <span id="statusFilterDisplay" class="truncate">All status</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="statusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[50] mt-1.5 w-full max-h-52 overflow-y-auto rounded-[10px] border border-slate-200/90 bg-white shadow-xl shadow-slate-200/60 p-1.5 space-y-0.5">
                        <button type="button" onclick="selectCustomOption('statusFilter', '', 'All status', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">All status</button>
                        <button type="button" onclick="selectCustomOption('statusFilter', 'Under Review', 'Under Review', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Under Review</button>
                        <button type="button" onclick="selectCustomOption('statusFilter', 'Pending Repair', 'Pending Repair', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Pending Repair</button>
                        <button type="button" onclick="selectCustomOption('statusFilter', 'Ready for Restock', 'Ready for Restock', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Ready for Restock</button>
                        <button type="button" onclick="selectCustomOption('statusFilter', 'Disposed', 'Disposed', 'statusFilterDisplay', 'statusFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Disposed</button>
                    </div>
                </div>

                <!-- Custom Dropdown: Return Reason Filter -->
                <div class="relative z-[30]" data-dropdown-wrapper="reasonFilter">
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Return Reason</label>
                    <input type="hidden" id="reasonFilter" value="" />
                    <button type="button" id="reasonFilterButton" onclick="toggleCustomDropdown('reasonFilterDropdown', event)" class="w-full px-3.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-left text-xs sm:text-[13px] text-slate-800 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-2xs cursor-pointer">
                        <span id="reasonFilterDisplay" class="truncate">All reasons</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="reasonFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[50] mt-1.5 w-full max-h-52 overflow-y-auto rounded-[10px] border border-slate-200/90 bg-white shadow-xl shadow-slate-200/60 p-1.5 space-y-0.5">
                        <button type="button" onclick="selectCustomOption('reasonFilter', '', 'All reasons', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">All reasons</button>
                        <button type="button" onclick="selectCustomOption('reasonFilter', 'Defective', 'Defective', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Defective</button>
                        <button type="button" onclick="selectCustomOption('reasonFilter', 'Customer Return', 'Customer Return', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Customer Return</button>
                        <button type="button" onclick="selectCustomOption('reasonFilter', 'Wrong Item', 'Wrong Item Delivered', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Wrong Item Delivered</button>
                        <button type="button" onclick="selectCustomOption('reasonFilter', 'Quality Issue', 'Quality Issue', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Quality Issue</button>
                        <button type="button" onclick="selectCustomOption('reasonFilter', 'Damaged In Transit', 'Damaged In Transit', 'reasonFilterDisplay', 'reasonFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Damaged In Transit</button>
                    </div>
                </div>

                <!-- Custom Dropdown: Item Condition Filter -->
                <div class="relative z-[30]" data-dropdown-wrapper="conditionFilter">
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Condition</label>
                    <input type="hidden" id="conditionFilter" value="" />
                    <button type="button" id="conditionFilterButton" onclick="toggleCustomDropdown('conditionFilterDropdown', event)" class="w-full px-3.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-left text-xs sm:text-[13px] text-slate-800 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-2xs cursor-pointer">
                        <span id="conditionFilterDisplay" class="truncate">All conditions</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="conditionFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[50] mt-1.5 w-full max-h-52 overflow-y-auto rounded-[10px] border border-slate-200/90 bg-white shadow-xl shadow-slate-200/60 p-1.5 space-y-0.5">
                        <button type="button" onclick="selectCustomOption('conditionFilter', '', 'All conditions', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">All conditions</button>
                        <button type="button" onclick="selectCustomOption('conditionFilter', 'Good', 'Good', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Good</button>
                        <button type="button" onclick="selectCustomOption('conditionFilter', 'Damaged', 'Damaged', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Damaged</button>
                        <button type="button" onclick="selectCustomOption('conditionFilter', 'Needs Repair', 'Needs Repair', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Needs Repair</button>
                        <button type="button" onclick="selectCustomOption('conditionFilter', 'Opened', 'Opened', 'conditionFilterDisplay', 'conditionFilterDropdown')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer">Opened</button>
                    </div>
                </div>

                <div>
                    <button id="clearFiltersBtn" class="w-full px-3.5 py-2.5 rounded-[10px] bg-[#6EC1D1] text-slate-900 text-xs sm:text-[13px] font-bold hover:bg-[#59b2c2] transition shadow-sm cursor-pointer inline-flex items-center justify-center">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-hidden rounded-[14px] border border-slate-200 bg-white shadow-[0_14px_40px_-24px_rgba(0,0,0,0.32)]">
            <div class="w-full overflow-x-auto">
                <table class="w-full table-fixed text-left text-[11px] divide-y divide-slate-200">
                    <thead class="border-b border-slate-200 bg-[#0f172a] rounded-t-[14px] text-[10px] uppercase tracking-wider text-white">
                        <tr>
                            <th class="w-[20%] px-3.5 py-3 font-semibold rounded-tl-[14px] truncate" title="Product Name">Product Name</th>
                            <th class="w-[12%] px-3 py-3 font-semibold truncate" title="SKU">SKU</th>
                            <th class="w-[14%] px-3 py-3 font-semibold truncate" title="Return Reason">Return Reason</th>
                            <th class="w-[11%] px-3 py-3 font-semibold truncate" title="Condition">Condition</th>
                            <th class="w-[7%] px-2.5 py-3 font-semibold text-center truncate" title="Qty">Qty</th>
                            <th class="w-[12%] px-3 py-3 font-semibold truncate" title="Warehouse">Warehouse</th>
                            <th class="w-[10%] px-3 py-3 font-semibold truncate" title="Received Date">Received Date</th>
                            <th class="w-[10%] px-3 py-3 font-semibold text-center truncate" title="Status">Status</th>
                            <th class="w-[8%] px-3 py-3 font-semibold text-center rounded-tr-[14px] truncate" title="Actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody" class="divide-y divide-slate-100 bg-white text-[11px]">
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">Loading records...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="detailsModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4 py-4">
        <div id="detailsModalOverlay" class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl"></div>
        <div class="relative w-full max-w-2xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[95vh] overflow-y-auto">
            <div class="flex items-center justify-between bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black leading-tight">Return Record Details</h2>
                    <p class="text-sm text-slate-900 font-medium">Complete inspection & workflow status</p>
                </div>
                <button type="button" id="closeDetailsModalBtn" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="detailsModalContent" class="p-5 sm:p-6 space-y-4 text-xs">
                <!-- Injected via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Log/Edit Return Modal -->
    <div id="returnModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4 py-4">
        <div id="modalOverlay" class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl"></div>
        <div class="relative w-full max-w-2xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[95vh] overflow-y-auto">
            <div class="flex items-center justify-between bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 id="modalTitle" class="text-xl font-bold text-black leading-tight">Log Returned Item</h2>
                    <p class="text-sm text-slate-900 font-medium">Capture return details, inspection results, and workflow decisions</p>
                </div>
                <button type="button" id="closeModalBtn" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="returnForm" class="p-5 sm:p-6 space-y-4 text-xs">
                <input type="hidden" id="recordId" />
                
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Product Name <span class="text-rose-500">*</span></label>
                        <input id="productName" type="text" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="e.g. Premium Brake Pad" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">SKU <span class="text-rose-500">*</span></label>
                        <input id="sku" type="text" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="e.g. BRK-PAD-001" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Quantity <span class="text-rose-500">*</span></label>
                        <input id="quantity" type="number" min="1" value="1" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Warehouse / Location <span class="text-rose-500">*</span></label>
                        <input id="warehouse" type="text" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="e.g. Main Hub / Bay A-02" required />
                    </div>

                    <!-- Custom Dropdown: Return Reason -->
                    <div class="space-y-1 relative z-[20]" data-dropdown-wrapper="returnReason">
                        <label class="block text-xs font-medium text-slate-700">Return Reason <span class="text-rose-500">*</span></label>
                        <select id="returnReason" class="hidden" required>
                            <option value="Defective" selected>Defective</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Damaged In Transit">Damaged In Transit</option>
                        </select>
                        <button type="button" id="returnReasonButton" onclick="toggleCustomDropdown('returnReasonDropdown', event)" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm transition cursor-pointer">
                            <span id="returnReasonDisplay">Defective</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="returnReasonDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('returnReason', 'Defective', 'Defective', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Defective</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Customer Return', 'Customer Return', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Customer Return</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Wrong Item Delivered', 'Wrong Item Delivered', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Wrong Item Delivered</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Quality Issue', 'Quality Issue', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Quality Issue</button>
                            <button type="button" onclick="selectCustomOption('returnReason', 'Damaged In Transit', 'Damaged In Transit', 'returnReasonDisplay', 'returnReasonDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Damaged In Transit</button>
                        </div>
                    </div>

                    <!-- Custom Dropdown: Condition -->
                    <div class="space-y-1 relative z-[20]" data-dropdown-wrapper="condition">
                        <label class="block text-xs font-medium text-slate-700">Condition <span class="text-rose-500">*</span></label>
                        <select id="condition" class="hidden" required>
                            <option value="Good" selected>Good</option>
                            <option value="Opened">Opened</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Needs Repair">Needs Repair</option>
                        </select>
                        <button type="button" id="conditionButton" onclick="toggleCustomDropdown('conditionDropdown', event)" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm transition cursor-pointer">
                            <span id="conditionDisplay">Good</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="conditionDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('condition', 'Good', 'Good', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Good</button>
                            <button type="button" onclick="selectCustomOption('condition', 'Opened', 'Opened', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Opened</button>
                            <button type="button" onclick="selectCustomOption('condition', 'Damaged', 'Damaged', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Damaged</button>
                            <button type="button" onclick="selectCustomOption('condition', 'Needs Repair', 'Needs Repair', 'conditionDisplay', 'conditionDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Needs Repair</button>
                        </div>
                    </div>

                    <!-- Custom Dropdown: Source -->
                    <div class="space-y-1 relative z-[10]" data-dropdown-wrapper="source">
                        <label class="block text-xs font-medium text-slate-700">Source <span class="text-rose-500">*</span></label>
                        <select id="source" class="hidden" required>
                            <option value="Customer Return" selected>Customer Return</option>
                            <option value="Supplier Return">Supplier Return</option>
                            <option value="Quality Inspection">Quality Inspection</option>
                            <option value="Service Center">Service Center</option>
                        </select>
                        <button type="button" id="sourceButton" onclick="toggleCustomDropdown('sourceDropdown', event)" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm transition cursor-pointer">
                            <span id="sourceDisplay">Customer Return</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="sourceDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('source', 'Customer Return', 'Customer Return', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Customer Return</button>
                            <button type="button" onclick="selectCustomOption('source', 'Supplier Return', 'Supplier Return', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Supplier Return</button>
                            <button type="button" onclick="selectCustomOption('source', 'Quality Inspection', 'Quality Inspection', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Quality Inspection</button>
                            <button type="button" onclick="selectCustomOption('source', 'Service Center', 'Service Center', 'sourceDisplay', 'sourceDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Service Center</button>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-slate-700">Reported Date <span class="text-rose-500">*</span></label>
                        <input id="reportedDate" type="date" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                    </div>

                    <!-- Custom Dropdown: Status -->
                    <div class="space-y-1 relative z-[10] sm:col-span-2" data-dropdown-wrapper="status">
                        <label class="block text-xs font-medium text-slate-700">Workflow Status <span class="text-rose-500">*</span></label>
                        <select id="status" class="hidden" required>
                            <option value="Under Review" selected>Under Review</option>
                            <option value="Pending Repair">Pending Repair</option>
                            <option value="Ready for Restock">Ready for Restock</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                        <button type="button" id="statusButton" onclick="toggleCustomDropdown('statusDropdown', event)" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-300 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm transition cursor-pointer">
                            <span id="statusDisplay">Under Review</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="statusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCustomOption('status', 'Under Review', 'Under Review', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Under Review</button>
                            <button type="button" onclick="selectCustomOption('status', 'Pending Repair', 'Pending Repair', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Pending Repair</button>
                            <button type="button" onclick="selectCustomOption('status', 'Ready for Restock', 'Ready for Restock', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Ready for Restock</button>
                            <button type="button" onclick="selectCustomOption('status', 'Disposed', 'Disposed', 'statusDisplay', 'statusDropdown')" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer">Disposed</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-slate-700">Notes & Inspection Findings</label>
                    <textarea id="notes" rows="2" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 resize-none transition" placeholder="Add inspection observations, batch tags, or follow-up tasks"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex items-center justify-between gap-2">
                    <button type="button" id="deleteRecordBtn" class="hidden inline-flex items-center justify-center gap-1.5 rounded-[10px] bg-white border border-slate-300 px-4 py-2.5 text-xs sm:text-sm font-semibold text-rose-600 shadow-sm hover:bg-slate-50 transition cursor-pointer">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete</span>
                    </button>
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" id="cancelModalBtn" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 cursor-pointer">
                            <span>Save Record</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @vite('resources/js/reverse-logistics.js')
</x-layouts.app>
