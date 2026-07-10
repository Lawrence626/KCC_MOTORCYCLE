<x-layouts.app :title="__('Replacing Items')">
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Replacing Items</h1>
                <p class="text-slate-600 text-sm mt-1">Manage returned products and issue replacements.</p>
            </div>
            <div class="flex items-center gap-4">
                <select id="dateFilter" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="flex items-center justify-between gap-4">
            <button onclick="openNewReplacementModal()" class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-2xl font-semibold text-base hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 ring-1 ring-emerald-500/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Replacement
            </button>

            <div class="flex-1 flex items-center gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        id="searchInput"
                        type="text"
                        placeholder="Search receipt no. / product"
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />
                </div>
                <select class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>Status: All</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Completed</option>
                </select>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Receipt No.</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Returned Item</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-900">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Replacement Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-900">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-900">Action</th>
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
                    <button class="px-3 py-1 rounded-lg bg-cyan-600 text-sm font-medium text-white">1</button>
                    <button class="px-3 py-1 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Next →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Replacement Modal -->
    <div id="processModal" class="hidden fixed inset-0 bg-slate-950/40 backdrop-blur-xl flex items-center justify-center z-50 px-4 py-6">
        <div class="bg-white/95 backdrop-blur-sm rounded-[28px] shadow-[0_30px_80px_rgba(15,23,42,0.15)] border border-slate-200/80 max-w-2xl w-full overflow-hidden">
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
                    <select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                        <select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                <button class="px-5 py-3 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20">Confirm Replacement</button>
            </div>
        </div>
    </div>

    <!-- New Replacement Modal -->
    <div id="newReplacementModal" class="hidden fixed inset-0 bg-slate-950/40 backdrop-blur-xl flex items-center justify-center z-50 px-4 py-6">
        <div class="bg-white/95 backdrop-blur-sm rounded-[28px] shadow-[0_30px_100px_rgba(15,23,42,0.18)] border border-slate-200/80 max-w-2xl w-full overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-slate-200/80">
                <h2 class="text-2xl font-semibold text-slate-900">New Replacement</h2>
                <button onclick="closeNewReplacementModal()" class="text-slate-500 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <!-- Receipt Number -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Receipt No.</label>
                    <input id="newReceiptNo" type="text" placeholder="INV-20260703-230225" maxlength="20" class="w-full px-5 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent shadow-sm" />
                    <p class="text-xs text-slate-500 mt-1">Format: INV-YYYYMMDD-XXXXXX</p>
                </div>

                <!-- Returned Item -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Returned Item (SKU)</label>
                    <div class="flex items-center">
                        <span class="px-4 py-3 rounded-l-2xl border border-r-0 border-slate-200 bg-slate-100 text-slate-600 font-semibold text-sm">KCC_</span>
                        <input id="newReturnedItem" type="text" placeholder="Enter product name" class="flex-1 px-5 py-3 rounded-r-2xl border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent shadow-sm" />
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-3">Reason</label>
                    <select id="newReason" class="w-full px-5 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent shadow-sm">
                        <option>Defective Item</option>
                        <option>Wrong Item Sent</option>
                        <option>Customer Request</option>
                        <option>Quality Issue</option>
                        <option>Others Reason</option>
                    </select>
                    <input id="customReason" type="text" placeholder="Specify reason..." class="hidden w-full mt-2 px-5 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent shadow-sm" />
                </div>

                <!-- Replacement Product & Quantity (Side by Side) -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-3">Replacement Product</label>
                        <div class="relative">
                            <svg class="absolute left-4 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input id="newReplacementProduct" type="text" placeholder="Search product..." list="productList" class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent shadow-sm" />
                            <datalist id="productList">
                                <option value="">Loading products...</option>
                            </datalist>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Quantity</label>
                        <div class="flex items-center gap-2">
                            <button onclick="decreaseNewQuantity()" class="p-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 transition-colors">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input id="newQuantity" type="text" value="1" readonly class="flex-1 px-3 py-2.5 rounded-lg border border-slate-300 text-center text-slate-900 font-semibold text-sm" />
                            <button onclick="increaseNewQuantity()" class="p-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 transition-colors">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="px-8 py-5 border-t border-slate-200 flex gap-3 justify-end bg-slate-50 rounded-b-[28px]">
                <button onclick="closeNewReplacementModal()" class="px-6 py-3 rounded-2xl border border-slate-300 bg-white text-slate-900 font-semibold text-sm hover:bg-slate-100 transition-all">Cancel</button>
                <button onclick="submitNewReplacement()" class="px-6 py-3 rounded-2xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20">Create Replacement</button>
            </div>
        </div>
    </div>

    @vite('resources/js/replacing-items.js')
</x-layouts.app>
