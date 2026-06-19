<x-layouts.app :title="__('Archived Items')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Archived Items</h1>
                <p class="text-xs text-slate-500 mt-0.5">Archived inventory items. Restore or permanently delete</p>
            </div>
            <div class="flex gap-2 items-center">
                <button class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
                <button class="px-3 py-1.5 rounded-lg bg-cyan-600 text-white text-xs font-medium hover:bg-cyan-700 transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Go Back
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Archived Items</p>
                <p class="text-lg font-bold text-slate-900">12</p>
                <p class="text-xs text-slate-500 mt-0.5">Total archived</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Archive Value</p>
                <p class="text-lg font-bold text-slate-900">₱45,200</p>
                <p class="text-xs text-slate-500 mt-0.5">Total value</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Avg Price</p>
                <p class="text-lg font-bold text-slate-900">₱3,767</p>
                <p class="text-xs text-slate-500 mt-0.5">Per item</p>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg border border-slate-200 p-2.5 shadow-sm space-y-2">
            <input type="search" placeholder="Search by product name, SKU, or barcode..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />

            <div class="flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Category</label>
                    <select class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
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
                <div class="flex-1">
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Stock Level</label>
                    <select class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Levels</option>
                        <option value="high">High (>50)</option>
                        <option value="medium">Medium (10-50)</option>
                        <option value="low">Low (1-10)</option>
                        <option value="zero">Out of Stock</option>
                    </select>
                </div>
                <button class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition whitespace-nowrap">Apply</button>
                <button class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50 transition whitespace-nowrap">Reset</button>
                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                    <input type="checkbox" id="select-all" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                    <label for="select-all" class="cursor-pointer whitespace-nowrap">Select All</label>
                </div>
            </div>
        </div>

        <!-- Archived Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide w-6">
                                <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Product</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">SKU</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Category</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 uppercase tracking-wide">Stock</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Unit Price</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total Value</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Archived Date</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <!-- Sample Row 1 -->
                        <tr class="hover:bg-slate-50 transition opacity-70">
                            <td class="px-3 py-2">
                                <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                            </td>
                            <td class="px-3 py-2">
                                <div>
                                    <p class="font-medium text-slate-900">Discontinued Motor Oil</p>
                                    <p class="text-xs text-slate-500">Old Formula - 1L</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-slate-600">MOT-OLD-1L</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Engine Oil</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold text-slate-900">3</span>
                                    <span class="text-xs text-slate-500">units</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">₱150.00</td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">₱450</td>
                            <td class="px-3 py-2 text-slate-600">Jun 10, 2026</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex gap-1 justify-center">
                                    <button class="p-1 text-cyan-600 hover:bg-cyan-50 rounded transition" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    <button class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Permanently Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Sample Row 2 -->
                        <tr class="hover:bg-slate-50 transition opacity-70">
                            <td class="px-3 py-2">
                                <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                            </td>
                            <td class="px-3 py-2">
                                <div>
                                    <p class="font-medium text-slate-900">Obsolete Battery Model</p>
                                    <p class="text-xs text-slate-500">12V - Legacy</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-slate-600">BAT-OLD-12</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Battery</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold text-slate-900">1</span>
                                    <span class="text-xs text-slate-500">units</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">₱1,800.00</td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">₱1,800</td>
                            <td class="px-3 py-2 text-slate-600">Jun 8, 2026</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex gap-1 justify-center">
                                    <button class="p-1 text-cyan-600 hover:bg-cyan-50 rounded transition" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    <button class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Permanently Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Sample Row 3 -->
                        <tr class="hover:bg-slate-50 transition opacity-70">
                            <td class="px-3 py-2">
                                <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                            </td>
                            <td class="px-3 py-2">
                                <div>
                                    <p class="font-medium text-slate-900">Superseded Spark Plug</p>
                                    <p class="text-xs text-slate-500">Standard Grade</p>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-slate-600">SPA-STD-OLD</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Spark Plug</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold text-slate-900">8</span>
                                    <span class="text-xs text-slate-500">units</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">₱320.00</td>
                            <td class="px-3 py-2 text-right font-medium text-slate-900">₱2,560</td>
                            <td class="px-3 py-2 text-slate-600">Jun 9, 2026</td>
                            <td class="px-3 py-2 text-center">
                                <div class="flex gap-1 justify-center">
                                    <button class="p-1 text-cyan-600 hover:bg-cyan-50 rounded transition" title="Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    <button class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Permanently Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer - Pagination -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <span>Showing</span>
                    <select class="px-2 py-1 rounded border border-slate-300 bg-white text-xs">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <span>of 12 items</span>
                </div>
                <div class="flex gap-1">
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">← Prev</button>
                    <button class="px-2 py-1 rounded-lg bg-cyan-600 text-xs font-medium text-white hover:bg-cyan-700">1</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">Next →</button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar (visible when items selected) -->
        <div class="hidden bg-cyan-50 border border-cyan-200 rounded-lg p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="text-sm text-cyan-900">
                    <span class="font-semibold">2 items selected</span>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-lg border border-cyan-300 bg-white text-xs font-medium text-cyan-700 hover:bg-cyan-50 transition">
                        Restore Selected
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-red-300 bg-white text-xs font-medium text-red-600 hover:bg-red-50 transition">
                        Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
