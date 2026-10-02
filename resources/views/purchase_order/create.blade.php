<x-layouts.app :title="__('Create Purchase Order')">

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Create Purchase Order</h1>
                <p class="text-xs text-slate-500 mt-0.5">Select products first, then compare and choose a qualified supplier. Pricing insights update automatically.</p>
            </div>
            <a href="{{ route('order.management') }}"
               class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to orders
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="rounded-[10px] border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
            <strong class="block font-semibold">Please fix the following:</strong>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Offline Outage Banner --}}
    <div id="po-create-offline-banner" class="hidden rounded-[14px] border border-amber-300 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm mb-4 transition-all duration-300">
        <div class="flex items-start gap-3">
            <div class="p-2 bg-amber-100 border border-amber-200 rounded-xl text-amber-700 shrink-0">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-amber-950">Internet connection lost — Offline Mode Activated</p>
                <p class="text-xs text-amber-800 mt-0.5">You can safely save this purchase order locally. When your internet connection is restored, you will be notified to export and synchronize it for final processing before sending to supplier.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('order.store') }}" method="POST" id="po-form" class="space-y-6">
        @csrf
        <div id="selected-products-hidden-inputs"></div>

        {{-- STEP 1 - SELECT PRODUCTS --}}
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">1</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Select Products to Reorder</h2>
                    <p class="text-xs text-slate-300">Choose from low-stock products. The qualified suppliers will update automatically.</p>
                </div>
            </div>

            <div class="p-6">
                @if(!empty($selectedProductIds))
                    <div class="mb-4 rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Low-stock alert pre-selected products for replenishment. Review and confirm your selection.</span>
                    </div>
                @endif

                {{-- Filter bar: Search (left) + Dropdown (right) --}}
                <div class="mb-4">
                    <p class="text-sm font-medium text-slate-700 mb-1">Low stock products</p>
                    <p class="text-xs text-slate-500 mb-2">Select the items to include in the order.</p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative flex-1 max-w-sm">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                </svg>
                            </span>
                            <input type="text"
                                   id="product-search"
                                   placeholder="Search by Product Name or SKU..."
                                   class="w-full rounded-[10px] border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 outline-none transition-colors focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </div>
                        <div class="flex-shrink-0 relative z-50" data-dropdown-wrapper="movementFilter">
                            @php
                                $filters = [
                                    'all'           => 'All',
                                    'fast_moving'   => 'Fast moving',
                                    'slow_moving'   => 'Slow moving',
                                    'special_order' => 'Special order',
                                ];
                                $currentFilterLabel = $filters[$currentFilter ?? 'all'] ?? 'All';
                            @endphp
                            <input type="hidden" name="movement" id="movementFilterInput" value="{{ $currentFilter ?? 'all' }}" />
                            <button type="button" id="movementFilterButton" onclick="toggleDropdown('movementFilterDropdown')" class="rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-left text-sm text-slate-900 flex items-center justify-between gap-2 hover:ring-1 hover:ring-black/35 focus:outline-none focus:ring-1 focus:ring-black/35 min-w-[160px]">
                                <span>{{ $currentFilterLabel }}</span>
                                <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="movementFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                                @foreach($filters as $key => $label)
                                    <button type="button" onclick="selectMovementFilter(event, '{{ $key }}', '{{ $label }}')" class="w-full px-4 py-2.5 text-left text-sm {{ ($currentFilter ?? 'all') === $key ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div id="table-wrapper" class="relative overflow-hidden rounded-[10px] border border-slate-200">
                    <div id="table-loading-overlay" class="hidden absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-10 pointer-events-none">
                        <div class="flex items-center justify-center p-2.5 bg-slate-900 text-white rounded-full shadow-lg">
                            <svg class="animate-spin h-5 w-5 text-[#00fff2]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                    </div>
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200 rounded-t-[10px]" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-[10px]">
                                    <input type="checkbox" id="select-all-products"
                                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 "
                                           title="Select all" />
                                </th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Movement</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Reorder Level</th>
                                 <th class="px-4 py-3">
                                     <div class="flex items-center gap-1">
                                         <span>Qty to Order</span>
                                         <div class="group relative cursor-pointer inline-flex items-center justify-center">
                                             <svg class="h-3.5 w-3.5 text-slate-400 hover:text-slate-600 transition" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                 <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                             </svg>
                                             <div class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 mt-2 hidden group-hover:block w-48 rounded-xl bg-slate-900 px-3 py-2 text-[10px] font-semibold normal-case tracking-normal text-white text-center shadow-xl z-20">
                                                 <span class="relative z-10">Maximum Stock (100) - Current Stock</span>
                                                 <div class="absolute bottom-full left-1/2 -translate-x-1/2 border-4 border-transparent border-b-slate-900"></div>
                                             </div>
                                         </div>
                                     </div>
                                 </th>
                                <th class="px-4 py-3 rounded-tr-[10px]">Unit Price (₱)</th>
                            </tr>
                        </thead>
                        <tbody id="product-table-body" class="divide-y divide-slate-200 text-slate-700">
                            @include('purchase_order.partials.product-rows', ['lowStockProducts' => $lowStockProducts, 'selectedProductIds' => $selectedProductIds])
                        </tbody>
                    </table>
                </div>

                <div id="pagination-container" class="mt-4 px-2">{{ $lowStockProducts->links() }}</div>

                <div id="selected-count-bar" class="mt-4 hidden rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 font-medium">
                    <span id="selected-count-text"></span>
                </div>
            </div>
        </div>

        {{-- STEP 2 - SUPPLIER COMPARISON (Placed ABOVE Select Supplier for clear decision making) --}}
        <div id="comparison-panel" class="hidden rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center justify-between bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">2</span>
                    <div>
                        <h2 class="text-sm font-semibold text-white">Supplier Comparison</h2>
                        <p class="text-xs text-slate-300">All qualified suppliers ranked by cost & metrics. Click View to inspect supplier assessment performance.</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#6EC1D1]/20 text-[#6EC1D1] border border-[#6EC1D1]/40">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Performance Matrix
                </span>
            </div>
            <div class="p-6 space-y-4">
                <div id="recommended-supplier-badge" class="hidden rounded-[10px] bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800"></div>
                <div class="overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3">Supplier</th>
                                <th class="px-4 py-3">Total Cost (₱)</th>
                                <th class="px-4 py-3">Avg Change</th>
                                <th class="px-4 py-3">Last Purchase</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="comparison-table-body" class="divide-y divide-slate-200 text-slate-700">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- STEP 3 - SELECT SUPPLIER (Placed BELOW Supplier Comparison) --}}
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">3</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Select Supplier</h2>
                    <p class="text-xs text-slate-300">Choose your preferred supplier directly or select one from the comparison table above.</p>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div id="supplier-loading" class="hidden flex items-center gap-2 text-sm text-slate-500">
                    <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Finding qualified suppliers...
                </div>

                <div id="no-product-hint" class="rounded-[10px] border border-gray-300 bg-gray-200/50 px-4 py-3 text-sm text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    Select at least one product above to see qualified suppliers.
                </div>

                <div id="no-supplier-message" class="hidden rounded-[10px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                <div id="supplier-dropdown-wrapper" class="hidden relative max-w-sm z-30" data-dropdown-wrapper="supplierSelect">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Supplier</label>
                    <input type="hidden" name="supplier_id" id="supplier-select" value="" />
                    <button type="button"
                            id="supplierSelectButton"
                            onclick="toggleSupplierDropdown(event)"
                            class="w-full rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-left text-sm text-slate-900 flex items-center justify-between shadow-sm cursor-pointer hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 transition">
                        <span id="supplierSelectDisplay" class="truncate text-slate-400 font-normal">Select supplier...</span>
                        <svg id="supplierSelectArrow" class="w-4 h-4 text-slate-600 shrink-0 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="supplierSelectDropdown"
                         class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1.5 w-full rounded-[16px] border border-slate-200 bg-white shadow-xl p-3 space-y-1 max-h-60 overflow-y-auto">
                        <div id="supplierSelectList" class="space-y-1">
                            <!-- Supplier checkboxes dynamically loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 4 - SUPPLIER INFORMATION --}}
        <div id="supplier-info-panel" class="hidden rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">4</span>
                <h2 class="text-sm font-semibold text-white">Supplier Information</h2>
            </div>
            <div class="p-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Supplier Name</p>
                        <p id="si-name" class="text-sm font-semibold text-slate-800">—</p>
                    </div>
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Contact Person</p>
                        <p id="si-contact" class="text-sm text-slate-700">—</p>
                    </div>
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Last Purchase</p>
                        <p id="si-last-purchase" class="text-sm text-slate-700">—</p>
                    </div>
                    <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Performance Score</p>
                        <p id="si-reliability" class="text-sm font-semibold text-slate-800">—</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 5 - PRICE HISTORY / SUMMARY / RECOMMENDATIONS --}}
        <div id="price-analysis-panel" class="hidden rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center gap-3 bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">5</span>
                <div>
                    <h2 class="text-sm font-semibold text-white">Supplier Price Analysis</h2>
                    <p class="text-xs text-slate-300">Historical costs, trends, and purchasing recommendations per product.</p>
                </div>
            </div>
            <div id="price-analysis-content" class="p-6 space-y-8">
                {{-- Injected by JavaScript --}}
            </div>
        </div>

        {{-- STEP 6 - DELIVERY SCHEDULE & ORDER NOTES (7 Days Allotted) --}}
        @php
            $defaultExpectedDelivery = \App\Models\PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7)->format('Y-m-d');
        @endphp
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-800 px-6 py-4 flex items-center justify-between bg-[#0f172a] rounded-t-[15px]" style="background-color: #0f172a;">
                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#6EC1D1] text-xs font-bold text-black">6</span>
                    <div>
                        <h2 class="text-sm font-semibold text-white">Delivery Schedule & Order Notes</h2>
                        <p class="text-xs text-slate-300">Set expected arrival date (auto-calculated 7 days from today) and special instructions.</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#6EC1D1]/20 text-[#6EC1D1] border border-[#6EC1D1]/40">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    7 Days Delivery Allotment
                </span>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Expected Delivery Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="expected_delivery_date"
                           id="expected_delivery_date"
                           value="{{ old('expected_delivery_date', $defaultExpectedDelivery) }}"
                           required
                           class="w-full rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-2xs" />
                    <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Auto-calculated 7 days from today. Ready before sending to supplier.
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Order Notes / Remarks (Optional)
                    </label>
                    <textarea name="notes"
                              id="po-notes"
                              rows="3"
                              placeholder="Add special instructions, priority notes, or delivery terms..."
                              class="w-full rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-2xs resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- SUPPLIER PERFORMANCE MODAL (DIRECT SUPPLIER ASSESSMENT METRICS) --}}
        <div id="supplier-performance-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-3xl rounded-[20px] bg-white shadow-2xl overflow-hidden border border-slate-200 my-8">
                {{-- Modal Header --}}
                <div class="bg-[#0f172a] px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#6EC1D1]/20 text-[#6EC1D1] border border-[#6EC1D1]/40">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-white" id="perf-modal-title">Supplier Performance Review</h3>
                            <p class="text-xs text-slate-300">Supplier Assessment evaluation, KPI metrics, and pricing history</p>
                        </div>
                    </div>
                    <button type="button" onclick="window.closeSupplierPerformanceModal()" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div id="perf-modal-content" class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    {{-- Dynamically populated --}}
                </div>

                {{-- Modal Footer --}}
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button type="button" onclick="window.closeSupplierPerformanceModal()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Back to Order</span>
                    </button>
                    <button type="button" id="perf-modal-select-btn" class="inline-flex items-center gap-1.5 rounded-xl bg-[#6EC1D1] px-5 py-2.5 text-xs font-bold text-slate-950 hover:bg-[#5bb0c0] transition shadow-xs cursor-pointer">
                        <span>Select This Supplier</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Offline PO Save Success Modal --}}
        <div id="offlineSuccessModal" class="hidden fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
                {{-- Modal Header --}}
                <div class="bg-[#0f172a] px-6 py-5 border-b border-slate-800 flex items-center justify-between text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white leading-tight">Purchase Order Saved Locally (Offline)</h3>
                            <p class="text-xs text-slate-300 mt-0.5">Stored securely in your local browser storage</p>
                        </div>
                    </div>
                    <button type="button" onclick="window.closeOfflineSuccessModal()" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-4">
                    <div class="bg-amber-50 border border-amber-300/80 rounded-2xl p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs text-amber-900 leading-relaxed">
                            <strong>Offline Storage Notice:</strong> Ang Purchase Order na ito ay <strong>matagumpay na na-save sa lokal na IndexedDB database</strong> ng iyong device. Kapag nagkaroon muli ng internet connection, pumunta lamang sa <strong>Offline Reconciliation > Export Data</strong> para i-export at i-sync ito.
                        </div>
                    </div>

                    {{-- Order Details Summary Card --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-2.5 text-xs text-slate-700">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="text-slate-500 font-medium">Order Number:</span>
                            <span id="offline-modal-po-num" class="font-bold text-slate-900 font-mono text-sm"></span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="text-slate-500 font-medium">Supplier:</span>
                            <span id="offline-modal-supplier" class="font-semibold text-slate-900"></span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="text-slate-500 font-medium">Total Items:</span>
                            <span id="offline-modal-items-count" class="font-semibold text-slate-900"></span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <span class="text-slate-500 font-medium">Expected Delivery:</span>
                            <span id="offline-modal-delivery" class="font-semibold text-slate-900"></span>
                        </div>
                        <div class="flex items-center justify-between pt-1 text-sm">
                            <span class="text-slate-900 font-bold">Total Amount:</span>
                            <span id="offline-modal-total" class="font-bold text-slate-900 font-mono text-base"></span>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-slate-100 px-6 py-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" onclick="window.closeOfflineSuccessModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs cursor-pointer">
                        Create Another Order
                    </button>
                    <a href="{{ route('offline.export') }}" class="rounded-xl bg-[#0f172a] text-white px-5 py-2.5 text-xs font-bold hover:bg-slate-800 transition shadow-sm inline-flex items-center gap-2 cursor-pointer">
                        <span>Go to Export & Sync</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
            <div id="offline-order-indicator" class="hidden items-center gap-2 text-xs font-semibold text-amber-900 bg-amber-50 border border-amber-300 px-3 py-1.5 rounded-[10px]">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                <span>Offline Mode: Order will be saved locally to browser storage (IndexedDB)</span>
            </div>
            <div class="flex items-center justify-end gap-3 ml-auto">
                <a href="{{ route('order.management') }}"
                   class="max-w-xs rounded-[10px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-black/10 transition-all duration-200">
                    Cancel
                </a>
                <button type="submit"
                        id="submit-po-button"
                        class="max-w-xs inline-flex items-center justify-center gap-2 rounded-[10px] bg-[#6EC1D1] px-5 py-3 text-sm font-semibold text-black shadow-sm hover:bg-[#59b2c2] transition-all duration-200 cursor-pointer">
                    <span id="submit-po-text">Submit Purchase Order</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    'use strict';

    const ROUTES = {
        filteredSuppliers: '{{ route("api.order.filtered_suppliers") }}',
        supplierDetails:   '{{ route("api.order.supplier_details") }}',
        comparison:        '{{ route("api.order.supplier_comparison") }}',
        productSearch:     '{{ route("order.create") }}'
    };

    // Pre-selected IDs passed from the server (low-stock alert redirect)
    const preselectedIds = @json($selectedProductIds);

    // Helpers
    const $el  = (id) => document.getElementById(id);
    const fmt  = (v) => v != null ? '₱' + Number(v).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '—';
    const fmtP = (v) => v != null ? (v > 0 ? '+' : '') + Number(v).toFixed(2) + '%' : '—';
    const icon = (t) => ({ increasing: '↑', decreasing: '↓', stable: '→' })[t] ?? '';

    function escHtml(str) {
        if (str == null) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

    // State
    let selectedProductIds = [];
    const selectedProductsStore = new Map();
    let currentSupplierId  = null;
    let filterTimer        = null;
    let isFetchingPage     = false;

    // DOM references
    const selectAllBox      = $el('select-all-products');
    const supplierSelect    = $el('supplier-select');
    const productTableBody  = $el('product-table-body');
    const paginationWrap    = $el('pagination-container');
    const poForm            = $el('po-form');

    // Dropdown functions
    window.toggleDropdown = function(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const button = document.getElementById(dropdownId.replace('Dropdown', 'Button'));
        
        document.querySelectorAll('.dropdown-menu').forEach(d => {
            if (d.id !== dropdownId) d.classList.add('hidden');
        });
        
        if (dropdown) {
            dropdown.classList.toggle('hidden');
            if (button) {
                const arrow = button.querySelector('svg');
                if (arrow) {
                    arrow.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            }
        }
    };

    window.selectMovementFilter = function(event, key, label) {
        event.stopPropagation();
        
        const input = document.getElementById('movementFilterInput');
        const button = document.getElementById('movementFilterButton');
        const dropdown = document.getElementById('movementFilterDropdown');
        
        if (input) input.value = key;
        if (button) {
            const span = button.querySelector('span');
            if (span) span.textContent = label;
            const arrow = button.querySelector('svg');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
        
        if (dropdown) {
            dropdown.querySelectorAll('button').forEach(btn => {
                btn.className = 'w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]';
            });
            event.target.className = 'w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-900 bg-black/10 rounded-[10px]';
            dropdown.classList.add('hidden');
        }
        
        // Trigger filter
        const searchVal = $el('product-search')?.value.trim() || '';
        fetchProducts(searchVal, key);
    };

    // Supplier Dropdown Functions
    function updateSupplierSelectButton(name, count) {
        const displaySpan = $el('supplierSelectDisplay');
        const button = $el('supplierSelectButton');
        
        if (!displaySpan || !button) return;

        if (name) {
            displaySpan.textContent = name;
            displaySpan.className = 'truncate text-slate-900 font-medium';
        } else {
            displaySpan.textContent = 'Select supplier...';
            displaySpan.className = 'truncate text-slate-400 font-normal';
        }
    }

    function selectSupplier(id, name) {
        currentSupplierId = id ? parseInt(id, 10) : null;
        
        if (supplierSelect) {
            supplierSelect.value = currentSupplierId ? String(currentSupplierId) : '';
        }

        updateSupplierSelectButton(name, 0);

        const listContainer = $el('supplierSelectList');
        if (listContainer) {
            listContainer.querySelectorAll('button[data-supplier-id]').forEach(btn => {
                const isSelected = currentSupplierId && parseInt(btn.dataset.supplierId, 10) === currentSupplierId;
                if (isSelected) {
                    btn.className = 'w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-900 bg-black/10 rounded-[10px] flex items-center justify-between';
                    if (!btn.querySelector('.check-icon')) {
                        btn.innerHTML += '<svg class="check-icon w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    }
                } else {
                    btn.className = 'w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] flex items-center justify-between';
                    const check = btn.querySelector('.check-icon');
                    if (check) check.remove();
                }
            });
        }

        closeSupplierDropdown();

        if (currentSupplierId) {
            loadSupplierDetails(currentSupplierId);
        } else {
            hideSupplierPanels();
        }
    }

    window.toggleSupplierDropdown = function(event) {
        event?.stopPropagation();
        const dropdown = $el('supplierSelectDropdown');
        const arrow = $el('supplierSelectArrow');
        if (!dropdown) return;

        const isHidden = dropdown.classList.contains('hidden');
        
        document.querySelectorAll('.dropdown-menu').forEach(d => {
            if (d.id !== 'supplierSelectDropdown') d.classList.add('hidden');
        });

        if (isHidden) {
            dropdown.classList.remove('hidden');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        } else {
            dropdown.classList.add('hidden');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    };

    function closeSupplierDropdown() {
        const dropdown = $el('supplierSelectDropdown');
        const arrow = $el('supplierSelectArrow');
        if (dropdown) dropdown.classList.add('hidden');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-dropdown-wrapper="supplierSelect"]')) {
            closeSupplierDropdown();
        }
        if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]') && !event.target.closest('#supplierSelectButton')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => {
                if (d.id !== 'supplierSelectDropdown') d.classList.add('hidden');
            });
        }
    });

    // Store Synchronisation & State Management
    function syncVisibleRowsToStore() {
        if (!productTableBody) return;
        const rows = productTableBody.querySelectorAll('.product-row');
        rows.forEach(row => {
            const pid = parseInt(row.dataset.productId, 10);
            const cb = row.querySelector('.product-checkbox');
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[unit_price]"]');
            const isChecked = cb ? cb.checked : false;

            if (isChecked) {
                const qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
                const price = priceInput ? (parseFloat(priceInput.value) || 0) : 0;
                const name = row.dataset.productName || row.querySelector('td:nth-child(2)')?.textContent?.trim() || '';
                const sku = row.dataset.sku || row.querySelector('td:nth-child(4)')?.textContent?.trim() || '';

                selectedProductsStore.set(pid, {
                    product_id: pid,
                    product_name: name,
                    sku: sku,
                    quantity: qty,
                    unit_price: price,
                    selected: true
                });
            } else {
                selectedProductsStore.delete(pid);
            }
        });
        selectedProductIds = Array.from(selectedProductsStore.keys());
    }

    function applyStoreToVisibleRows() {
        if (!productTableBody) return;
        const rows = productTableBody.querySelectorAll('.product-row');
        rows.forEach(row => {
            const pid = parseInt(row.dataset.productId, 10);
            const cb = row.querySelector('.product-checkbox');
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[unit_price]"]');

            if (selectedProductsStore.has(pid)) {
                const stored = selectedProductsStore.get(pid);
                if (cb) cb.checked = true;
                if (qtyInput && stored.quantity != null) qtyInput.value = stored.quantity;
                if (priceInput && stored.unit_price != null) priceInput.value = stored.unit_price;
                row.classList.add('bg-emerald-50/50');
            } else {
                if (cb) cb.checked = false;
                row.classList.remove('bg-emerald-50/50');
            }
        });
    }

    function syncHiddenInputs() {
        const container = $el('selected-products-hidden-inputs');
        if (!container) return;
        container.innerHTML = '';

        let index = 0;
        selectedProductsStore.forEach((item, pid) => {
            const pidInput = document.createElement('input');
            pidInput.type = 'hidden';
            pidInput.name = `products[${index}][product_id]`;
            pidInput.value = pid;

            const nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = `products[${index}][product_name]`;
            nameInput.value = item.product_name;

            const skuInput = document.createElement('input');
            skuInput.type = 'hidden';
            skuInput.name = `products[${index}][sku]`;
            skuInput.value = item.sku;

            const qtyInput = document.createElement('input');
            qtyInput.type = 'hidden';
            qtyInput.name = `products[${index}][quantity]`;
            qtyInput.value = item.quantity;

            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = `products[${index}][unit_price]`;
            priceInput.value = item.unit_price;

            const selInput = document.createElement('input');
            selInput.type = 'hidden';
            selInput.name = `products[${index}][selected]`;
            selInput.value = '1';

            container.appendChild(pidInput);
            container.appendChild(nameInput);
            container.appendChild(skuInput);
            container.appendChild(qtyInput);
            container.appendChild(priceInput);
            container.appendChild(selInput);

            index++;
        });
    }

    function updateSelectedCountBar() {
        const countBar = $el('selected-count-bar');
        const countText = $el('selected-count-text');
        if (!countBar || !countText) return;

        const count = selectedProductsStore.size;
        if (count > 0) {
            countText.textContent = `${count} product${count > 1 ? 's' : ''} selected across all pages for purchase order.`;
            countBar.classList.remove('hidden');
        } else {
            countBar.classList.add('hidden');
        }
    }

    function updateSelectAllCheckboxState() {
        if (!selectAllBox || !productTableBody) return;
        const visibleCheckboxes = productTableBody.querySelectorAll('.product-checkbox');
        if (visibleCheckboxes.length === 0) {
            selectAllBox.checked = false;
            selectAllBox.indeterminate = false;
            return;
        }

        let allChecked = true;
        let noneChecked = true;

        visibleCheckboxes.forEach(cb => {
            if (cb.checked) {
                noneChecked = false;
            } else {
                allChecked = false;
            }
        });

        selectAllBox.checked = allChecked;
        selectAllBox.indeterminate = !allChecked && !noneChecked;
    }

    // AJAX Page Fetcher (No reload)
    async function fetchProducts(search = '', movement = 'all', pageUrl = null) {
        const overlay = $el('table-loading-overlay');
        if (overlay) overlay.classList.remove('hidden');
        isFetchingPage = true;

        try {
            syncVisibleRowsToStore();

            const url = pageUrl ? new URL(pageUrl, window.location.origin) : new URL(ROUTES.productSearch, window.location.origin);
            if (!pageUrl) {
                if (search) url.searchParams.set('search', search);
                if (movement && movement !== 'all') url.searchParams.set('movement', movement);
            }

            const res = await fetch(url.toString(), {
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error(`HTTP error ${res.status}`);

            const htmlText = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlText, 'text/html');

            const newTbody = doc.getElementById('product-table-body');
            const newPagination = doc.getElementById('pagination-container');

            if (newTbody && productTableBody) {
                productTableBody.innerHTML = newTbody.innerHTML;
            }

            if (newPagination && paginationWrap) {
                paginationWrap.innerHTML = newPagination.innerHTML;
            }

            applyStoreToVisibleRows();
            updateSelectAllCheckboxState();
            updateSelectedCountBar();
            resolvePoProductImages();

            if (pageUrl && window.history) {
                window.history.pushState({ path: url.toString() }, '', url.toString());
            }

        } catch (err) {
            console.error('Failed to fetch products page:', err);
        } finally {
            if (overlay) overlay.classList.add('hidden');
            isFetchingPage = false;
        }
    }

    // Event Delegation for table rows
    if (productTableBody) {
        productTableBody.addEventListener('change', function(e) {
            const target = e.target;
            const row = target.closest('.product-row');
            if (!row) return;

            syncVisibleRowsToStore();
            applyStoreToVisibleRows();
            updateSelectAllCheckboxState();
            updateSelectedCountBar();
            syncHiddenInputs();
            if (loadedPriceHistories && loadedPriceHistories.length > 0) {
                renderPriceAnalysis(loadedPriceHistories);
            }
            debouncedLoadComparison();
            onProductSelectionChange();
        });

        productTableBody.addEventListener('input', function(e) {
            const target = e.target;
            if (target.matches('input[name*="[unit_price]"]') || target.matches('input[name*="[quantity]"]')) {
                syncVisibleRowsToStore();
                syncHiddenInputs();
                if (loadedPriceHistories && loadedPriceHistories.length > 0) {
                    renderPriceAnalysis(loadedPriceHistories);
                }
                debouncedLoadComparison();
            }
        });
    }

    // Select-all toggle
    if (selectAllBox) {
        selectAllBox.addEventListener('change', function() {
            if (!productTableBody) return;
            const isChecked = selectAllBox.checked;
            const visibleCheckboxes = productTableBody.querySelectorAll('.product-checkbox');

            visibleCheckboxes.forEach(cb => {
                cb.checked = isChecked;
                const row = cb.closest('.product-row');
                if (row) {
                    const pid = parseInt(row.dataset.productId, 10);
                    if (isChecked) {
                        const qtyInput = row.querySelector('input[name*="[quantity]"]');
                        const priceInput = row.querySelector('input[name*="[unit_price]"]');
                        selectedProductsStore.set(pid, {
                            product_id: pid,
                            product_name: row.dataset.productName || '',
                            sku: row.dataset.sku || '',
                            quantity: qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1,
                            unit_price: priceInput ? (parseFloat(priceInput.value) || 0) : 0,
                            selected: true
                        });
                        row.classList.add('bg-emerald-50/50');
                    } else {
                        selectedProductsStore.delete(pid);
                        row.classList.remove('bg-emerald-50/50');
                    }
                }
            });

            selectedProductIds = Array.from(selectedProductsStore.keys());
            updateSelectedCountBar();
            syncHiddenInputs();
            onProductSelectionChange();
        });
    }

    // Intercept pagination clicks without page reload
    if (paginationWrap) {
        paginationWrap.addEventListener('click', function(e) {
            const anchor = e.target.closest('a');
            if (anchor && anchor.href) {
                e.preventDefault();
                fetchProducts('', 'all', anchor.href);
            }
        });
    }

    // Browser back/forward navigation
    window.addEventListener('popstate', function() {
        fetchProducts('', 'all', window.location.href);
    });

    // Product search with debounce
    const searchInput = $el('product-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => {
                const searchVal = searchInput.value.trim();
                const movementVal = $el('movementFilterInput')?.value || 'all';
                fetchProducts(searchVal, movementVal);
            }, 350);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(filterTimer);
                const searchVal = searchInput.value.trim();
                const movementVal = $el('movementFilterInput')?.value || 'all';
                fetchProducts(searchVal, movementVal);
            }
        });
    }

    // Supplier dropdown change
    supplierSelect?.addEventListener('change', () => {
        const id = parseInt(supplierSelect.value, 10) || null;
        if (id) {
            loadSupplierDetails(id);
        } else {
            hideSupplierPanels();
        }
    });

    // Product selection change
    function onProductSelectionChange() {
        syncHiddenInputs();
        updateSelectedCountBar();

        if (selectedProductIds.length === 0) {
            $el('no-product-hint').classList.remove('hidden');
            $el('supplier-dropdown-wrapper').classList.add('hidden');
            $el('no-supplier-message').classList.add('hidden');
            $el('comparison-panel').classList.add('hidden');
            hideSupplierPanels();
            return;
        }

        $el('no-product-hint').classList.add('hidden');
        refreshSuppliers();
        loadComparison();
    }

    // Form submission: submit all selected products from all pages
    if (poForm) {
        poForm.addEventListener('submit', function (e) {
            syncVisibleRowsToStore();
            syncHiddenInputs();

            if (selectedProductsStore.size === 0) {
                e.preventDefault();
                alert('Please select at least one product to create a purchase order.');
                return;
            }

            if (!supplierSelect || !supplierSelect.value) {
                e.preventDefault();
                alert('Please select a qualified supplier.');
                $el('supplierSelectButton')?.focus();
                return;
            }
        });
    }

    // Helper: format select option label
    function supplierLabel(s) {
        const parts = [s.name];
        if (s.contact_person) parts.push(`(${s.contact_person})`);
        return parts.join(' ');
    }

    // Refresh supplier dropdown
    async function refreshSuppliers() {
        $el('no-product-hint').classList.add('hidden');
        $el('no-supplier-message').classList.add('hidden');
        $el('supplier-dropdown-wrapper').classList.add('hidden');
        $el('supplier-loading').classList.add('hidden');

        if (selectedProductIds.length === 0) {
            $el('no-product-hint').classList.remove('hidden');
            hideSupplierPanels();
            return;
        }

        $el('supplier-loading').classList.remove('hidden');

        try {
            const url = new URL(ROUTES.filteredSuppliers, window.location.origin);
            selectedProductIds.forEach(id => url.searchParams.append('product_ids[]', id));

            const res  = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            $el('supplier-loading').classList.add('hidden');

            if (!data.suppliers || data.suppliers.length === 0) {
                const msgBox = $el('no-supplier-message');
                msgBox.textContent = data.message || 'No supplier can fulfill all selected products.';
                msgBox.classList.remove('hidden');
                supplierSelect.value = '';
                updateSupplierSelectButton('', 0);
                hideSupplierPanels();
                return;
            }

            const listContainer = $el('supplierSelectList');
            if (listContainer) {
                listContainer.innerHTML = '';
                
                data.suppliers.forEach(s => {
                    const isSelected = currentSupplierId && s.id === currentSupplierId;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.dataset.supplierId = s.id;
                    btn.dataset.name = s.name;
                    btn.className = isSelected 
                        ? 'w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-900 bg-black/10 rounded-[10px] flex items-center justify-between'
                        : 'w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] flex items-center justify-between';
                    
                    btn.innerHTML = `
                        <span>${escHtml(supplierLabel(s))}</span>
                        ${isSelected ? '<svg class="check-icon w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' : ''}
                    `;
                    
                    btn.onclick = (e) => {
                        e.stopPropagation();
                        selectSupplier(s.id, s.name);
                    };
                    listContainer.appendChild(btn);
                });
            }

            $el('supplier-dropdown-wrapper').classList.remove('hidden');

            const stillValid = data.suppliers.some(s => s.id === currentSupplierId);
            if (!stillValid) {
                if (data.suppliers.length === 1) {
                    selectSupplier(data.suppliers[0].id, data.suppliers[0].name);
                } else {
                    supplierSelect.value = '';
                    currentSupplierId = null;
                    updateSupplierSelectButton('', 0);
                    hideSupplierPanels();
                }
            } else {
                const activeSup = data.suppliers.find(s => s.id === currentSupplierId);
                if (activeSup) {
                    updateSupplierSelectButton(activeSup.name, 0);
                    loadSupplierDetails(currentSupplierId);
                }
            }

        } catch (e) {
            $el('supplier-loading').classList.add('hidden');
            console.error('refreshSuppliers error', e);
        }
    }

    let loadedPriceHistories = [];

    // Load supplier details + price history
    async function loadSupplierDetails(supplierId) {
        if (!supplierId || selectedProductIds.length === 0) return;

        try {
            const url = new URL(ROUTES.supplierDetails, window.location.origin);
            url.searchParams.set('supplier_id', supplierId);
            selectedProductIds.forEach(id => url.searchParams.append('product_ids[]', id));

            const res  = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.supplier) {
                renderSupplierInfo(data.supplier);
                loadedPriceHistories = data.price_histories || [];
                renderPriceAnalysis(loadedPriceHistories);
            }
        } catch (e) {
            console.error('loadSupplierDetails error', e);
        }
    }

    // Render Step 4 - Supplier Information
    function renderSupplierInfo(s) {
        $el('si-name').textContent          = s.name          || '—';
        $el('si-contact').textContent       = s.contact_person || '—';
        $el('si-last-purchase').textContent = s.last_purchase_date || 'None';

        const relEl = $el('si-reliability');
        if (s.performance_score !== undefined && s.performance_score !== null) {
            const scoreClass = s.performance_score >= 80 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (s.performance_score >= 60 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200');
            relEl.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ${scoreClass}">${s.performance_score}/100 Rating</span>`;
        } else {
            relEl.textContent = '—';
        }

        $el('supplier-info-panel').classList.remove('hidden');
    }

    // Render Step 5 - Price Analysis (Real-time dynamic calculation)
    function renderPriceAnalysis(histories) {
        const container = $el('price-analysis-content');
        container.innerHTML = '';

        const list = histories && histories.length > 0 ? histories : loadedPriceHistories;
        if (!list || list.length === 0) {
            $el('price-analysis-panel').classList.add('hidden');
            return;
        }

        list.forEach(ph => {
            const stored = selectedProductsStore.get(ph.product_id);
            const userPrice = stored && stored.unit_price > 0 ? stored.unit_price : null;
            const baselinePrice = ph.previous_cost != null ? ph.previous_cost : (ph.catalog_price || ph.current_cost);
            
            const currentCost = userPrice != null ? userPrice : (ph.current_cost != null ? ph.current_cost : baselinePrice);
            const previousCost = baselinePrice;

            let changePercentage = 0;
            let trend = 'stable';

            if (previousCost && previousCost > 0 && currentCost != null && Math.abs(currentCost - previousCost) > 0.001) {
                changePercentage = Number((((currentCost - previousCost) / previousCost) * 100).toFixed(2));
                if (changePercentage > 0.005) {
                    trend = 'increasing';
                } else if (changePercentage < -0.005) {
                    trend = 'decreasing';
                }
            }

            const suggestedRetail = currentCost ? (currentCost * 1.20) : null;
            let recommendation = ph.recommendation;

            if (trend === 'increasing') {
                recommendation = `Supplier cost has increased (+${changePercentage}%). Review suggested retail price (${fmt(suggestedRetail)}) to maintain 20% markup.`;
            } else if (trend === 'decreasing') {
                recommendation = `Supplier cost has decreased (${changePercentage}%). Maintaining current retail price will increase your profit margin.`;
            } else {
                recommendation = 'Supplier pricing is stable. Maintain the current retail price.';
            }

            const section = document.createElement('div');
            section.className = 'space-y-3 pb-6 border-b border-slate-200 last:border-0 last:pb-0';

            const header = document.createElement('h3');
            header.className   = 'text-sm font-semibold text-slate-800';
            header.textContent = ph.product_name;
            section.appendChild(header);

            const summaryGrid = document.createElement('div');
            summaryGrid.className = 'grid grid-cols-2 sm:grid-cols-4 gap-4';

            const changeColor = changePercentage > 0
                ? 'text-rose-600 font-bold'
                : changePercentage < 0 ? 'text-emerald-600 font-bold' : 'text-slate-600 font-bold';

            summaryGrid.innerHTML = `
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Current Cost</p>
                    <p class="text-base font-bold text-slate-800">${currentCost != null ? fmt(currentCost) : '—'}</p>
                </div>
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Previous Cost</p>
                    <p class="text-base font-bold text-slate-600">${previousCost != null ? fmt(previousCost) : '—'}</p>
                </div>
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Price Change</p>
                    <p class="text-base ${changeColor}">${fmtP(changePercentage)}</p>
                </div>
                <div class="rounded-[10px] bg-slate-50 border border-slate-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Trend</p>
                    <p class="text-base font-semibold text-slate-700">${icon(trend)} ${cap(trend)}</p>
                </div>
            `;
            section.appendChild(summaryGrid);

            // Recommendation Alert Box
            const recStyle = trend === 'increasing'
                ? 'border-amber-200 bg-amber-50 text-amber-800'
                : trend === 'decreasing'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                    : 'border-slate-200 bg-slate-50 text-slate-700';
            const recBox = document.createElement('div');
            recBox.className   = `rounded-[10px] border px-4 py-3 text-sm ${recStyle}`;
            recBox.textContent = recommendation;
            section.appendChild(recBox);

            // History table
            if (ph.histories && ph.histories.length > 0) {
                const tableWrap = document.createElement('div');
                tableWrap.className = 'overflow-hidden rounded-3xl border border-slate-200';
                tableWrap.innerHTML = `
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider border-b border-slate-200" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3">Purchase Date</th>
                                <th class="px-4 py-3">Purchase Order</th>
                                <th class="px-4 py-3">Supplier Cost</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            ${ph.histories.map(h => `
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2">${escHtml(h.date ?? '—')}</td>
                                    <td class="px-4 py-2 font-mono text-xs text-slate-500">${escHtml(h.po_number ?? '—')}</td>
                                    <td class="px-4 py-2 font-semibold">${fmt(h.cost)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                section.appendChild(tableWrap);
            } else {
                const noHist = document.createElement('p');
                noHist.className   = 'text-sm text-slate-500';
                noHist.textContent = 'No purchase history yet for this product from this supplier.';
                section.appendChild(noHist);
            }

            container.appendChild(section);
        });

        $el('price-analysis-panel').classList.remove('hidden');
    }

    let comparisonDebounceTimer = null;
    function debouncedLoadComparison() {
        clearTimeout(comparisonDebounceTimer);
        comparisonDebounceTimer = setTimeout(() => {
            loadComparison();
        }, 300);
    }

    // Load comparison table
    async function loadComparison() {
        if (selectedProductIds.length === 0) {
            $el('comparison-panel').classList.add('hidden');
            return;
        }

        try {
            const url = new URL(ROUTES.comparison, window.location.origin);
            selectedProductsStore.forEach((item, pid) => {
                url.searchParams.append('product_ids[]', pid);
                if (item.quantity) url.searchParams.append(`quantities[${pid}]`, item.quantity);
                if (item.unit_price) url.searchParams.append(`prices[${pid}]`, item.unit_price);
            });

            const res  = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (!data.comparison || data.comparison.length < 1) {
                $el('comparison-panel').classList.add('hidden');
                return;
            }

            renderComparison(data.comparison, data.recommended);

        } catch (e) {
            console.error('comparison error', e);
        }
    }

    // Render comparison table
    function renderComparison(rows, recommended) {
        const tbody = $el('comparison-table-body');
        tbody.innerHTML = '';

        const badge = $el('recommended-supplier-badge');
        if (recommended) {
            badge.innerHTML = `
                <div class="flex items-center gap-2 text-emerald-900 font-bold">
                    <svg class="w-5 h-5 text-amber-500 fill-amber-400 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <span>Recommended Supplier: ${escHtml(recommended.name)}</span>
                    <span class="font-normal text-emerald-800 text-xs">— ${recommended.reasons.map(r => escHtml(r)).join(', ')}</span>
                </div>
            `;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

        rows.forEach(r => {
            const isRec = recommended && r.supplier_id === recommended.id;
            const tr = document.createElement('tr');
            tr.className = isRec
                ? 'bg-emerald-50/60'
                : 'hover:bg-slate-50';

            const cc = r.avg_change_percentage > 0
                ? 'text-rose-600'
                : r.avg_change_percentage < 0 ? 'text-emerald-700' : 'text-slate-500';

            tr.innerHTML = `
                <td class="px-4 py-3 font-medium ${isRec ? 'text-emerald-900' : 'text-slate-800'}">
                    <div class="flex items-center gap-1.5">
                        ${isRec ? '<span class="inline-flex items-center gap-1 text-emerald-800 bg-emerald-100/90 px-2 py-0.5 rounded-md text-[11px] font-bold shrink-0"><svg class="w-3.5 h-3.5 text-amber-500 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>Best Value</span>' : ''}
                        <span>${escHtml(r.supplier_name)}</span>
                    </div>
                </td>
                <td class="px-4 py-3 font-semibold">${r.latest_total_cost > 0 ? fmt(r.latest_total_cost) : '—'}</td>
                <td class="px-4 py-3 ${cc}">${r.has_history ? fmtP(r.avg_change_percentage) : '—'}</td>
                <td class="px-4 py-3 text-slate-500">${escHtml(r.last_purchase_date ?? '—')}</td>
                <td class="px-4 py-3">
                    <button type="button"
                            onclick="window._poViewSupplierPerformance(${r.supplier_id})"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-cyan-300 bg-cyan-50 px-3 py-1.5 text-xs font-bold text-cyan-700 hover:bg-cyan-100 transition shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>View</span>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        $el('comparison-panel').classList.remove('hidden');
    }

    // Modal Helpers
    window.closeSupplierPerformanceModal = function() {
        const modal = document.getElementById('supplier-performance-modal');
        if (modal) modal.classList.add('hidden');
    };

    window.selectSupplierAndCloseModal = function(supplierId, supplierName) {
        selectSupplier(supplierId, supplierName);
        window.closeSupplierPerformanceModal();
        const btn = document.getElementById('supplierSelectButton') || document.getElementById('supplier-dropdown-wrapper');
        if (btn) {
            btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    window._poViewSupplierPerformance = async function (supplierId) {
        const modal = document.getElementById('supplier-performance-modal');
        const content = document.getElementById('perf-modal-content');
        const selectBtn = document.getElementById('perf-modal-select-btn');
        if (!modal || !content) return;

        modal.classList.remove('hidden');
        content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-slate-500">
                <svg class="w-8 h-8 animate-spin text-[#6EC1D1] mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-semibold">Loading supplier assessment performance metrics...</p>
            </div>
        `;

        try {
            const url = new URL(ROUTES.supplierDetails, window.location.origin);
            url.searchParams.set('supplier_id', supplierId);
            selectedProductIds.forEach(id => url.searchParams.append('product_ids[]', id));

            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (!data || !data.supplier) {
                content.innerHTML = `<div class="p-6 text-center text-rose-600 font-semibold">Unable to load supplier performance records.</div>`;
                return;
            }

            const s = data.supplier;
            const priceHistories = data.price_histories || [];

            if (selectBtn) {
                selectBtn.onclick = () => window.selectSupplierAndCloseModal(s.id, s.name);
                selectBtn.innerHTML = `<span>Select ${escHtml(s.name)}</span>`;
            }

            const score = s.performance_score || 0;
            const scoreBadgeClass = score >= 80 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : (score >= 60 ? 'bg-amber-50 text-amber-800 border-amber-300' : 'bg-rose-50 text-rose-800 border-rose-300');
            const scoreDotColor = score >= 80 ? 'bg-emerald-500' : (score >= 60 ? 'bg-amber-500' : 'bg-rose-500');

            let itemsHtml = '';
            priceHistories.forEach(ph => {
                const changeColor = ph.change_percentage > 0 ? 'text-rose-600 font-bold' : (ph.change_percentage < 0 ? 'text-emerald-600 font-bold' : 'text-slate-600');
                const trendIcon = ph.trend === 'increasing' ? '↑' : (ph.trend === 'decreasing' ? '↓' : '→');
                const recClass = ph.trend === 'increasing' ? 'bg-amber-50 text-amber-800 border-amber-200' : (ph.trend === 'decreasing' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-50 text-slate-700 border-slate-200');

                let pastPOs = '';
                if (ph.histories && ph.histories.length > 0) {
                    pastPOs = `
                        <div class="mt-3 overflow-hidden rounded-xl border border-slate-200">
                            <table class="min-w-full text-xs text-left">
                                <thead class="bg-slate-100 text-slate-600 font-semibold border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-1.5">Date</th>
                                        <th class="px-3 py-1.5">PO Number</th>
                                        <th class="px-3 py-1.5">Supplier Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    ${ph.histories.map(h => `
                                        <tr>
                                            <td class="px-3 py-1.5">${escHtml(h.date || '—')}</td>
                                            <td class="px-3 py-1.5 font-mono text-slate-500">${escHtml(h.po_number || '—')}</td>
                                            <td class="px-3 py-1.5 font-semibold">${fmt(h.cost)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                }

                itemsHtml += `
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-sm font-bold text-slate-900">${escHtml(ph.product_name)}</h4>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                Trend: ${trendIcon} ${cap(ph.trend)}
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                            <div class="rounded-lg bg-slate-50 p-2">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Current Cost</p>
                                <p class="text-sm font-bold text-slate-900">${ph.current_cost !== null ? fmt(ph.current_cost) : '—'}</p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-2">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Previous Cost</p>
                                <p class="text-sm font-bold text-slate-600">${ph.previous_cost !== null ? fmt(ph.previous_cost) : '—'}</p>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-2">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Price Change</p>
                                <p class="text-sm ${changeColor}">${fmtP(ph.change_percentage)}</p>
                            </div>
                        </div>
                        ${ph.recommendation ? `
                            <div class="mt-3 rounded-lg border px-3 py-2 text-xs font-medium ${recClass}">
                                ${escHtml(ph.recommendation)}
                            </div>
                        ` : ''}
                        ${pastPOs}
                    </div>
                `;
            });

            content.innerHTML = `
                {{-- Supplier Header Profile --}}
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100/60 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">${escHtml(s.name)}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">${escHtml(s.contact_person || 'No Contact Person')} • ${escHtml(s.phone || 'No Phone')} • ${escHtml(s.email || 'No Email')}</p>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold border ${scoreBadgeClass} shadow-2xs">
                                <span class="w-2.5 h-2.5 rounded-full ${scoreDotColor}"></span>
                                ${score}/100 Assessment Rating
                            </span>
                        </div>
                    </div>

                    {{-- Supplier Assessment 4 Core KPI Cards --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-4">
                        <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                            <p class="text-[10px] uppercase font-bold text-slate-400">On-Time Delivery</p>
                            <p class="text-base font-extrabold text-slate-900 mt-0.5">${s.on_time_rate}%</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">${s.delivered_orders} / ${s.total_orders} orders</p>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                            <p class="text-[10px] uppercase font-bold text-slate-400">Order Completion</p>
                            <p class="text-base font-extrabold text-slate-900 mt-0.5">${s.completion_rate}%</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Fulfillment accuracy</p>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                            <p class="text-[10px] uppercase font-bold text-slate-400">Quality Score</p>
                            <p class="text-base font-extrabold text-emerald-700 mt-0.5">${s.quality_score}%</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">${s.defect_rate}% defect rate</p>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200 p-3 text-center shadow-2xs">
                            <p class="text-[10px] uppercase font-bold text-slate-400">Price Stability</p>
                            <p class="text-base font-extrabold text-cyan-700 mt-0.5">${s.price_stability}%</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Rate consistency</p>
                        </div>
                    </div>
                </div>

                {{-- Products & Pricing Analysis --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Pricing Analysis for Selected Products</h4>
                        <span class="text-xs text-slate-400">${priceHistories.length} item(s) checked</span>
                    </div>
                    ${itemsHtml || '<p class="text-sm text-slate-500 italic">No pricing records found for this supplier.</p>'}
                </div>
            `;
        } catch (err) {
            console.error('Error fetching supplier performance:', err);
            content.innerHTML = `<div class="p-6 text-center text-rose-600 font-semibold">An error occurred while loading performance data.</div>`;
        }
    };

    window._poSelectSupplier = function (supplierId) {
        const sId = parseInt(supplierId, 10);
        const optBtn = document.querySelector(`#supplierSelectList button[data-supplier-id="${sId}"]`);
        const name = optBtn ? optBtn.dataset.name : '';
        selectSupplier(sId, name);

        const btn = $el('supplierSelectButton') || $el('supplier-dropdown-wrapper');
        if (btn) {
            btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    // Hide downstream panels
    function hideSupplierPanels() {
        $el('supplier-info-panel').classList.add('hidden');
        $el('price-analysis-panel').classList.add('hidden');
    }

    // Resolve Product Images from LocalStorage
    function resolvePoProductImages() {
        try {
            const stored = localStorage.getItem('posProductImages');
            if (!stored) return;
            const images = JSON.parse(stored);
            const keys = Object.keys(images);

            document.querySelectorAll('.po-product-img-thumb').forEach(container => {
                const id = container.dataset.id;
                const sku = container.dataset.sku;
                const name = container.dataset.name;

                let imgUrl = null;
                if (id && images[id]) imgUrl = images[id];
                else if (sku && images[sku]) imgUrl = images[sku];
                else if (name && images[name]) imgUrl = images[name];
                else {
                    if (sku) {
                        const matchSku = keys.find(k => k.toLowerCase() === String(sku).toLowerCase());
                        if (matchSku) imgUrl = images[matchSku];
                    }
                    if (!imgUrl && name) {
                        const matchName = keys.find(k => k.toLowerCase() === String(name).toLowerCase());
                        if (matchName) imgUrl = images[matchName];
                    }
                }

                if (imgUrl) {
                    container.innerHTML = '';
                    container.className = 'po-product-img-thumb w-8 h-8 rounded-[6px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center';
                    container.style.backgroundImage = `url('${imgUrl}')`;
                }
            });
        } catch(e) {
            console.error('Error resolving PO product images:', e);
        }
    }

    // Boot: trigger initial state
    if (productTableBody) {
        const rows = productTableBody.querySelectorAll('.product-row');
        rows.forEach(row => {
            const cb = row.querySelector('.product-checkbox');
            const pid = parseInt(row.dataset.productId, 10);
            if (cb && (cb.checked || (Array.isArray(preselectedIds) && preselectedIds.includes(pid)))) {
                cb.checked = true;
                const qtyInput = row.querySelector('input[name*="[quantity]"]');
                const priceInput = row.querySelector('input[name*="[unit_price]"]');
                selectedProductsStore.set(pid, {
                    product_id: pid,
                    product_name: row.dataset.productName || row.querySelector('td:nth-child(2)')?.textContent?.trim() || '',
                    sku: row.dataset.sku || row.querySelector('td:nth-child(4)')?.textContent?.trim() || '',
                    quantity: qtyInput ? (parseInt(qtyInput.value, 10) || 1) : (parseInt(row.dataset.defaultQuantity, 10) || 1),
                    unit_price: priceInput ? (parseFloat(priceInput.value) || 0) : (parseFloat(row.dataset.defaultUnitPrice) || 0),
                    selected: true,
                });
                row.classList.add('bg-emerald-50/50');
            }
        });
        selectedProductIds = Array.from(selectedProductsStore.keys());
    }

    syncHiddenInputs();
    updateSelectAllCheckboxState();
    updateSelectedCountBar();
    resolvePoProductImages();

    if (selectedProductIds.length > 0) {
        onProductSelectionChange();
    }

    // OFFLINE CAPABILITY SUPPORT FOR PO CREATION
    function updateCreatePoOfflineState() {
        const isOffline = (typeof navigator !== 'undefined' && typeof navigator.onLine === 'boolean') ? !navigator.onLine : false;
        const banner = $el('po-create-offline-banner');
        const indicator = $el('offline-order-indicator');
        const submitBtnText = $el('submit-po-text');

        if (isOffline) {
            if (banner) banner.classList.remove('hidden');
            if (indicator) {
                indicator.classList.remove('hidden');
                indicator.classList.add('inline-flex');
            }
            if (submitBtnText) {
                submitBtnText.textContent = 'Save Locally (Offline)';
            }
        } else {
            if (banner) banner.classList.add('hidden');
            if (indicator) {
                indicator.classList.add('hidden');
                indicator.classList.remove('inline-flex');
            }
            if (submitBtnText) {
                submitBtnText.textContent = 'Submit Purchase Order';
            }
        }
    }

    window.addEventListener('online', updateCreatePoOfflineState);
    window.addEventListener('offline', updateCreatePoOfflineState);
    updateCreatePoOfflineState();

    function calculateSevenWorkingDays(startDate = new Date()) {
        let date = new Date(startDate);
        date.setDate(date.getDate() + 7);
        return date.toISOString().split('T')[0];
    }

    if (poForm) {
        poForm.addEventListener('submit', async function(e) {
            const isOffline = (typeof navigator !== 'undefined' && typeof navigator.onLine === 'boolean') ? !navigator.onLine : false;
            
            if (isOffline) {
                e.preventDefault();
                e.stopPropagation();

                syncVisibleRowsToStore();

                if (selectedProductsStore.size === 0) {
                    alert('Please select at least one product to create an order.');
                    return;
                }

                if (!currentSupplierId) {
                    alert('Please select an authorized supplier before saving the order.');
                    return;
                }

                const supplierName = $el('supplierSelectDisplay')?.textContent?.trim() || 'Authorized Supplier';
                const notes = $el('po-notes')?.value || '';
                const expectedDelivery = $el('expected_delivery_date')?.value || calculateSevenWorkingDays();
                
                const items = [];
                let totalAmount = 0;

                selectedProductsStore.forEach((item, pid) => {
                    const qty = item.quantity || 1;
                    const price = item.unit_price || 0;
                    const subtotal = qty * price;
                    totalAmount += subtotal;

                    items.push({
                        product_id: pid,
                        product_name: item.product_name,
                        sku: item.sku,
                        quantity: qty,
                        unit_price: price,
                        subtotal: subtotal
                    });
                });

                const poNumber = 'PO-' + new Date().toISOString().replace(/\D/g, '').slice(0, 14) + '-' + Math.random().toString(36).substring(2, 6).toUpperCase();

                const orderRecord = {
                    order_number: poNumber,
                    supplier_id: currentSupplierId,
                    supplier_name: supplierName,
                    items: items,
                    total_amount: totalAmount,
                    status: 'pending',
                    sync_status: 'locally_saved',
                    expected_delivery_date: expectedDelivery,
                    notes: notes,
                    timestamp: new Date().toISOString()
                };

                try {
                    if (window.offlineManager) {
                        await window.offlineManager.savePendingOrder(orderRecord);
                        if (typeof window.offlineManager.updateOfflineReconSidebar === 'function') {
                            await window.offlineManager.updateOfflineReconSidebar();
                        }
                        if (typeof window.offlineManager.updatePendingOfflineSyncAlert === 'function') {
                            await window.offlineManager.updatePendingOfflineSyncAlert();
                        }
                    }

                    // Reset form selection
                    selectedProductsStore.clear();
                    selectedProductIds = [];
                    applyStoreToVisibleRows();
                    updateSelectAllCheckboxState();
                    onProductSelectionChange();
                    if ($el('po-notes')) $el('po-notes').value = '';

                    // Display rich confirmation modal
                    window.showOfflineSuccessModal(orderRecord);

                } catch(err) {
                    console.error('Failed to save offline order:', err);
                    alert('Error saving order locally: ' + (err.message || err));
                }
            }
        });
    }

    window.showOfflineSuccessModal = function(order) {
        const modal = $el('offlineSuccessModal');
        if (!modal) {
            alert(`Purchase Order #${order.order_number} has been SAVED LOCALLY in offline mode!\n\nSupplier: ${order.supplier_name}\nTotal: ₱${Number(order.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}\nExpected Delivery: ${order.expected_delivery_date}\n\nWhen internet is restored, you can Export & Sync this order.`);
            return;
        }

        if ($el('offline-modal-po-num')) $el('offline-modal-po-num').textContent = order.order_number;
        if ($el('offline-modal-supplier')) $el('offline-modal-supplier').textContent = order.supplier_name;
        if ($el('offline-modal-items-count')) $el('offline-modal-items-count').textContent = `${order.items.length} product(s) (${order.items.reduce((s, i) => s + i.quantity, 0)} total units)`;
        if ($el('offline-modal-delivery')) $el('offline-modal-delivery').textContent = `${order.expected_delivery_date} (7 Working Days)`;
        if ($el('offline-modal-total')) $el('offline-modal-total').textContent = '₱' + Number(order.total_amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });

        modal.classList.remove('hidden');
    };

    window.closeOfflineSuccessModal = function() {
        const modal = $el('offlineSuccessModal');
        if (modal) modal.classList.add('hidden');
    };

})();
</script>

</x-layouts.app>