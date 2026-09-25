<x-layouts.app :title="__('Replacing Items')">
<<<<<<< HEAD
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Replacing Items</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage returned products and issue replacements.</p>
=======
    <div class="space-y-6">
        <!-- Header Section -->
         <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between pt-2 pb-1 pl-1">
                <div class="pl-3 lg:pl-1">
                <h1 class="text-3xl font-bold text-slate-900">Replacing Items</h1>
                <p class="text-xs text-slate-500 mt-1">Manage returned products and issue replacements.</p>
>>>>>>> 594490397ebecd1f37adadd252bb79d7a67298f2
            </div>
            <button onclick="openNewReplacementModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#6EC1D1] text-slate-900 rounded-[10px] font-bold text-xs border border-slate-200 hover:bg-[#59b2c2] transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Replacement
            </button>
        </div>
    </x-slot>

    <div class="space-y-4">

            <div class="flex-1 flex items-center gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                        <input
                            id="searchInput"
                            type="text" placeholder="Search receipt no. / product"
                                class="w-full pl-10 pr-10 py-2.5 rounded-[10px] border border-[#105f68]/20 ring-1 ring-black/10 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-transparent"
                        />
                </div>
                <!-- Custom Dropdown -->
                <div class="relative">
                    <button type="button" onclick="toggleStatusDropdown()" class="appearance-none pl-4 pr-10 py-2.5 rounded-[10px] border border-[#105f68]/20 ring-1 ring-black/10 bg-white text-sm font-medium text-slate-900 text-left hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 flex items-center gap-2 whitespace-nowrap w-40"
                        style="background-image:url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'none\' stroke=\'%2338445d\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.85rem center; background-size:1.2em; line-height:1.25rem;">
                        <span id="statusLabel">All Status</span>
                    </button>
                    <div id="statusDropdown" class="hidden absolute top-full mt-2 -right-0 w-40 bg-white border border-slate-300 rounded-lg shadow-xl z-50 p-3 space-y-1">
                        <button type="button" onclick="selectStatus('All Status')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">All Status</button>
                        <button type="button" onclick="selectStatus('Pending')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Pending</button>
                        <button type="button" onclick="selectStatus('Approved')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Approved</button>
                        <button type="button" onclick="selectStatus('Completed')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Completed</button>
                    </div>
                </div>
                <!-- Date Filter -->
                <div class="relative">
                    <button type="button" onclick="toggleDateDropdown()" class="appearance-none pl-4 pr-10 py-2.5 rounded-[10px] border border-[#105f68]/20 ring-1 ring-black/10 bg-white text-sm font-medium text-slate-900 text-left hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 flex items-center gap-2 whitespace-nowrap w-40"
                        style="background-image:url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 20 20\' fill=\'none\' stroke=\'%2338445d\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.85rem center; background-size:1.2em; line-height:1.25rem;">
                        <span id="dateFilterLabel">Today</span>
                    </button>
                    <input type="hidden" id="dateFilter" value="today" />
                    <div id="dateDropdown" class="hidden absolute top-full mt-2 -right-0 w-40 bg-white border border-slate-300 rounded-lg shadow-xl z-50 p-3 space-y-1">
                        <button type="button" onclick="selectDateFilter('today', 'Today')" class="w-full px-4 py-2 text-left text-sm bg-black/10 text-slate-900 font-semibold rounded-[10px]">Today</button>
                        <button type="button" onclick="selectDateFilter('this_week', 'This Week')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">This Week</button>
                        <button type="button" onclick="selectDateFilter('this_month', 'This Month')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">This Month</button>
                        <button type="button" onclick="selectDateFilter('last_month', 'Last Month')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Last Month</button>
                        <button type="button" onclick="selectDateFilter('custom', 'Custom Range')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Custom Range</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-[10px] border border-slate-200 overflow-hidden shadow-sm flex flex-col" style="min-height: calc(100vh - 220px);">
            <div class="overflow-x-auto flex-1 rounded-[10px]">
                <table class="w-full">
                    <thead class="rounded-t-[10px]">
                        <tr class="border-b border-slate-800 bg-[#0f172a] rounded-t-[10px]">
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider rounded-tl-[10px]">Receipt No.</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Returned Item</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Replacement Item</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider rounded-tr-[10px]">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="font-medium">No replacements yet</p>
                                <p class="text-sm mt-1">Start by clicking "New Replacement" to create your first replacement request</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t border-slate-200 px-6 py-4 flex items-center justify-between">
                <p class="text-sm text-slate-600">Showing 0 of 0 entries</p>
                <div class="flex gap-1">
                    <button class="px-3 py-1 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                    <button class="px-3 py-1 rounded-lg bg-black/10 text-sm font-medium text-slate-900 shadow-sm">1</button>
                    <button class="px-3 py-1 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Replacement Modal -->
    <div id="processModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeProcessModal()"></div>
        <div class="relative bg-white rounded-[28px] shadow-[0_30px_80px_rgba(15,23,42,0.15)] border border-slate-200/80 max-w-2xl w-full overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-slate-200/80">
                <h2 class="text-2xl font-semibold text-slate-900">Process Replacement</h2>
                <button onclick="closeProcessModal()" class="text-slate-500 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-5">
                <!-- Receipt No & Returned Item (Side by Side) -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Receipt No.</label>
                        <input id="receiptNo" type="text" readonly class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-600 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Returned Item</label>
                        <input id="returnedItem" type="text" readonly class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-600 text-sm" />
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Reason</label>
                    <select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent">
                        <option>Defective Item</option>
                        <option>Wrong Item Sent</option>
                        <option>Customer Request</option>
                        <option>Quality Issue</option>
                    </select>
                </div>

                <!-- Replacement Product & Quantity (Side by Side) -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Replacement Product</label>
                        <select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-[#105f68] focus:border-transparent">
                            <option>Search product...</option>
                            <option>Brembo Brake Pad</option>
                            <option>NGK Spark Plug</option>
                            <option>Motul 4T 10W40 Oil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-900 mb-1">Quantity</label>
                        <div class="flex items-center gap-2">
                            <button class="p-2 rounded-lg border border-slate-300 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="text" value="1" readonly class="flex-1 px-3 py-2 rounded-lg border border-slate-300 text-center text-slate-900 font-medium text-sm" />
                            <button class="p-2 rounded-lg border border-slate-300 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3 justify-end bg-slate-50">
                <button onclick="closeProcessModal()" class="px-5 py-3 rounded-2xl border border-slate-300 bg-white text-slate-900 font-semibold text-sm hover:bg-slate-100 transition-all">Cancel</button>
                <button class="px-5 py-3 rounded-2xl bg-[#105f68] text-white font-semibold text-sm hover:bg-[#0c474e] transition-all shadow-lg shadow-[#105f68]/20">Confirm Replacement</button>
            </div>
        </div>
    </div>

    <!-- New Replacement Modal -->
    <div id="newReplacementModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeNewReplacementModal()"></div>
        <div class="relative bg-white rounded-[28px] shadow-[0_30px_100px_rgba(15,23,42,0.18)] max-w-2xl w-full overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-transparent bg-[#6EC1D1] rounded-t-[28px]">
                <div>
                    <h2 class="text-xl font-bold text-black">New Replacement</h2>
                    <p class="text-sm text-slate-900 font-medium">Create and process replacement item requests.</p>
                </div>
                <button onclick="closeNewReplacementModal()" class="text-black transition-colors p-2 rounded-[10px] hover:bg-black/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <!-- Receipt Number -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Receipt No.</label>
                    <input id="newReceiptNo" type="text" placeholder="Enter receipt number" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-slate-900 placeholder:text-slate-400 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-transparent ring-1 ring-black/10 shadow-sm" />
                </div>

                <!-- Returned Item -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Returned Item</label>
                    <input id="newReturnedItem" type="hidden" value="" />
                    <button type="button" id="returnedItemButton" onclick="toggleDropdown('returnedItemDropdown')" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-left text-slate-900 text-xs font-medium flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-transparent ring-1 ring-black/10 shadow-sm">
                        <span id="returnedItemLabel">Select returned item...</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="returnedItemDropdown" class="hidden absolute z-50 right-0 left-auto mt-2 w-[220px] bg-white border border-slate-200 rounded-[10px] shadow-xl p-1.5 space-y-0.5 max-h-40 overflow-y-auto">
                        <button type="button" onclick="selectDropdown('newReturnedItem', 'Brembo Brake Pad', 'returnedItemLabel', 'returnedItemDropdown')" style="-webkit-tap-highlight-color: transparent;" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-black/5 focus:outline-none focus:bg-black/10 active:bg-black/10 rounded-[8px]">Brembo Brake Pad</button>
                        <button type="button" onclick="selectDropdown('newReturnedItem', 'NGK Spark Plug', 'returnedItemLabel', 'returnedItemDropdown')" style="-webkit-tap-highlight-color: transparent;" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-black/5 focus:outline-none focus:bg-black/10 active:bg-black/10 rounded-[8px]">NGK Spark Plug</button>
                        <button type="button" onclick="selectDropdown('newReturnedItem', 'Motul 4T 10W40 Oil', 'returnedItemLabel', 'returnedItemDropdown')" style="-webkit-tap-highlight-color: transparent;" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-black/5 focus:outline-none focus:bg-black/10 active:bg-black/10 rounded-[8px]">Motul 4T 10W40 Oil</button>
                    </div>
                </div>

                <!-- Reason -->
                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Reason</label>
                    <input id="newReason" type="hidden" value="Defective Item" />
                    <button type="button" id="newReasonButton" onclick="toggleDropdown('reasonDropdown')" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-left text-slate-900 text-xs font-medium flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-transparent ring-1 ring-black/10 shadow-sm">
                        <span id="newReasonLabel">Defective Item</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="reasonDropdown" class="hidden absolute z-50 right-0 left-auto mt-2 w-[220px] bg-white border border-slate-200 rounded-[10px] shadow-xl p-1.5 space-y-0.5 max-h-40 overflow-y-auto">
                        <button type="button" onclick="selectDropdown('newReason', 'Defective Item', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Defective Item</button>
                        <button type="button" onclick="selectDropdown('newReason', 'Wrong Item Sent', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Wrong Item Sent</button>
                        <button type="button" onclick="selectDropdown('newReason', 'Customer Request', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Customer Request</button>
                        <button type="button" onclick="selectDropdown('newReason', 'Quality Issue', 'newReasonLabel', 'reasonDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Quality Issue</button>
                    </div>
                </div>

                <!-- Replacement Product & Quantity (Side by Side) -->
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-3 relative">
                        <label class="block text-sm font-semibold text-slate-900 mb-3">Replacement Product</label>
                        <input id="newReplacementProduct" type="hidden" value="" />
                        <button type="button" id="replacementProductButton" onclick="toggleDropdown('replacementProductDropdown')" class="w-full px-5 py-3 rounded-[10px] border border-[#105f68]/20 bg-white text-left text-slate-900 text-xs font-medium flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-transparent ring-1 ring-black/10 shadow-sm">
                            <span id="newReplacementProductLabel">Select product...</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="replacementProductDropdown" class="hidden absolute z-50 right-0 left-auto mt-2 w-[220px] bg-white border border-slate-200 rounded-[10px] shadow-xl p-1.5 space-y-0.5 max-h-40 overflow-y-auto">
                            <button type="button" onclick="selectDropdown('newReplacementProduct', 'Brembo Brake Pad', 'newReplacementProductLabel', 'replacementProductDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Brembo Brake Pad</button>
                            <button type="button" onclick="selectDropdown('newReplacementProduct', 'NGK Spark Plug', 'newReplacementProductLabel', 'replacementProductDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">NGK Spark Plug</button>
                            <button type="button" onclick="selectDropdown('newReplacementProduct', 'Motul 4T 10W40 Oil', 'newReplacementProductLabel', 'replacementProductDropdown')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Motul 4T 10W40 Oil</button>
                        </div>
                    </div>
                    <div class="col-span-2 min-w-0">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Quantity</label>
                        <div class="flex items-center gap-2">
                            <button onclick="decreaseNewQuantity()" class="shrink-0 p-3 rounded-lg border border-slate-300 hover:bg-black/10 transition-colors">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input id="newQuantity" type="text" value="1" readonly class="flex-1 min-w-0 px-2 py-3 rounded-lg border border-slate-300 text-center text-slate-900 font-semibold text-base" />
                            <button onclick="increaseNewQuantity()" class="shrink-0 p-3 rounded-lg border border-slate-300 hover:bg-black/10 transition-colors">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="px-8 py-5 border-t border-slate-200 flex gap-3 justify-end bg-slate-50 rounded-b-[10px]">
                <button onclick="closeNewReplacementModal()" class="px-6 py-3 rounded-[10px] border border-slate-300 bg-white text-slate-900 font-semibold text-sm hover:bg-black/10 transition-all">Cancel</button>
                <button onclick="submitNewReplacement()" class="px-6 py-3 rounded-[10px] bg-[#6EC1D1] text-slate-900 font-bold text-sm hover:bg-[#59b2c2] transition-all shadow-md shadow-slate-900/10">Create Replacement</button>
            </div>
        </div>
    </div>

    @vite('resources/js/replacing-items.js')
</x-layouts.app>