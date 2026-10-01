<x-layouts.app :title="__('Supplier Assessment')">

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Supplier Assessment</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track supplier performance, evaluate inventory turnover, and inspect product pricing.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="openSupplierModal" class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-3.5 py-2 text-xs font-bold text-slate-900 border border-slate-200/80 shadow-sm hover:bg-[#59b2c2] hover:shadow transition-all duration-200 cursor-pointer">
                    <svg class="h-3.5 w-3.5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Supplier
                </button>
                <a href="{{ route('supplier.assessment.archived') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all duration-200">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archive List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div id="success-toast" class="fixed top-4 right-8 z-50 rounded-[12px] border border-[#6EC1D1] bg-teal-50 px-4 py-3.5 text-sm font-medium text-slate-900 shadow-xl flex items-center gap-2">
                <svg class="w-4 h-4 text-teal-700" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('success-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.4s ease';
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 3500);
            </script>
        @endif

        @if($errors->any())
            <div class="rounded-[16px] border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-sm">
                <div class="font-bold mb-1">Please fix the following errors:</div>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Top Overview Stats Cards (Matching Modern Dashboard Palette) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Active Suppliers -->
            <div class="border border-gray-200 p-4 bg-white shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold truncate">Active Suppliers</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black truncate">{{ number_format($quickStats['activeSuppliers']) }}</p>
                            <p class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Currently active supplier partners.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tracked Products -->
            <div class="border border-gray-200 p-4 bg-white shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold truncate">Tracked Products</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black truncate">{{ number_format($quickStats['trackedProducts']) }}</p>
                            <p class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Catalog items linked across suppliers.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Supplier Stock Value -->
            <div class="border border-gray-200 p-4 bg-white shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold truncate">Supplier Stock Value</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black truncate">&#8369;{{ number_format($quickStats['stockValue'], 2) }}</p>
                            <p class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Combined inventory value across vendors.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05 1.18 1.91 2.53 1.91 1.29 0 2.13-.59 2.13-1.61 0-1.11-1.02-1.55-2.74-2.02-2.09-.56-3.72-1.35-3.72-3.47 0-1.89 1.45-3.09 3.11-3.43V4h2.67v1.93c1.61.32 2.82 1.43 2.92 3.16h-1.92c-.09-.91-.89-1.63-2.18-1.63-1.12 0-1.86.52-1.86 1.41 0 .96.89 1.38 2.49 1.84 2.19.62 3.97 1.46 3.97 3.65 0 2.01-1.52 3.23-3.32 3.73z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Dropdown Selector Section (Clean & Space-Saving) -->
        <section class="relative z-30 rounded-[20px] border border-slate-200 bg-white shadow-sm">
            <div class="bg-[#0f172a] px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-800 rounded-t-[20px]">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="h-4 w-4 text-[#6EC1D1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4m0 4h4m-4-4l4 4"/>
                        </svg>
                        Supplier Selection
                    </h2>
                    <p class="text-xs text-slate-300 mt-0.5">Select a supplier from the dropdown below to inspect performance score, pricing, and fast/slow moving items.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-[10px] bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm whitespace-nowrap">
                        {{ number_format($supplierSummaries->count()) }} Active Suppliers
                    </span>
                </div>
            </div>

            <div class="p-5 space-y-4 rounded-b-[20px]">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative flex-1">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Select Supplier Partner</label>
                        
                        <!-- Custom Searchable Dropdown Trigger -->
                        <div class="relative">
                            <button id="supplierDropdownBtn" type="button" class="w-full flex items-center justify-between gap-3 rounded-[12px] border border-slate-300 bg-slate-50/80 px-4 py-3 text-left text-sm font-semibold text-slate-900 hover:bg-slate-100 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] transition-all duration-200 cursor-pointer shadow-xs">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-8 h-8 rounded-[8px] bg-[#6EC1D1]/20 flex items-center justify-center flex-shrink-0 text-slate-900">
                                        <svg class="w-4 h-4 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4m0 4h4m-4-4l4 4"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p id="dropdownSelectedName" class="text-sm font-bold text-slate-900 truncate">Choose a supplier to inspect...</p>
                                        <p id="dropdownSelectedMeta" class="text-xs text-slate-500 truncate">Click to search and select supplier partner</p>
                                    </div>
                                </div>
                                <svg id="dropdownChevron" class="h-5 w-5 text-slate-500 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu (Floating with high z-index and shadow) -->
                            <div id="supplierDropdownMenu" class="hidden absolute top-full left-0 right-0 z-50 mt-1.5 rounded-[14px] border border-slate-200 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.18)] overflow-hidden">
                                <div class="p-2.5 border-b border-slate-100 bg-slate-50/90">
                                    <div class="relative">
                                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 12.65z"/>
                                        </svg>
                                        <input id="supplierDropdownSearch" type="search" placeholder="Type supplier name, contact, email..." class="w-full rounded-[8px] border border-slate-200 bg-white pl-9 pr-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1]" />
                                    </div>
                                </div>
                                <div id="supplierDropdownOptions" class="max-h-72 overflow-y-auto divide-y divide-slate-100 bg-white">
                                    @foreach($supplierSummaries as $supplierItem)
                                        <div data-supplier-name="{{ $supplierItem->name }}" class="supplier-option-item p-3.5 hover:bg-slate-50 transition cursor-pointer flex items-center justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $supplierItem->name }}</p>
                                                    <span class="rounded-[6px] bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">{{ $supplierItem->contact_position ?? 'Supplier' }}</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $supplierItem->contact_person ?? 'No contact' }} · {{ $supplierItem->email ?? ($supplierItem->phone ?? 'No contact info') }}</p>
                                            </div>
                                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                                <span class="rounded-[6px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold">🔥 {{ $supplierItem->fast_moving_count }}</span>
                                                <span class="rounded-[6px] bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 text-[10px] font-bold">⏳ {{ $supplierItem->slow_moving_count }}</span>
                                                <span class="rounded-[6px] bg-slate-100 text-slate-700 border border-slate-200 px-2 py-0.5 text-[10px] font-semibold">{{ $supplierItem->product_count }} items</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Supplier Action Buttons when selected -->
                    <div id="supplierActionButtons" class="hidden flex items-end gap-2 pt-2 md:pt-0">
                        <button id="quickEditBtn" type="button" class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-4 py-3 text-xs font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all whitespace-nowrap cursor-pointer">
                            <svg class="h-4 w-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Supplier
                        </button>
                        <button id="quickArchiveBtn" type="button" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-xs font-semibold text-slate-700 shadow-sm hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-all whitespace-nowrap cursor-pointer">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            Archive
                        </button>
                    </div>
                </div>

                <!-- Selected Supplier Quick Summary Ribbon -->
                <div id="selectedSupplierRibbon" class="hidden rounded-[14px] bg-slate-50 border border-slate-200/80 p-3.5 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="font-bold text-slate-900 text-sm" id="ribbonSupplierName">-</span>
                            <span class="rounded-[6px] bg-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-700" id="ribbonSupplierRole">Supplier</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span id="ribbonContactPerson">No contact person</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span id="ribbonContactEmail">No email</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-700">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span id="ribbonAddress">No address provided</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Supplier Details Main Section -->
        <main id="supplierDetailsContainer" class="rounded-[20px] border border-slate-200 bg-white shadow-sm min-h-[350px] overflow-hidden">
            <!-- Empty State Placeholder -->
            <div id="supplierDetailPlaceholder" class="p-8 py-20 text-center text-slate-500">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3.5">
                    <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4m0 4h4m-4-4l4 4"/>
                    </svg>
                </div>
                <p class="text-lg font-bold text-slate-900">Select a Supplier from the dropdown above</p>
                <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">Choose a supplier above to inspect their performance score, fast &amp; slow moving items, delivery reliability, and product price list.</p>
            </div>

            <!-- Detail Panel (Shown when supplier is selected) -->
            <section id="supplierDetailPanel" class="hidden space-y-6">
                <!-- Supplier Overview Header Banner -->
                <div class="bg-[#0f172a] px-6 py-5 border-b border-slate-800 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between rounded-t-[20px]">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider font-bold text-[#6EC1D1]">Supplier Overview</p>
                        <h2 id="detailSupplierName" class="mt-0.5 text-2xl font-bold text-white"></h2>
                        <p id="detailSupplierNotes" class="mt-1 text-xs text-slate-300"></p>
                        <p id="detailSupplierAddress" class="mt-1 text-xs text-slate-400"></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="rounded-[8px] bg-slate-800/90 px-3 py-1.5 border border-slate-700">
                            <p class="text-[10px] uppercase tracking-wider text-[#6EC1D1] font-semibold leading-tight">Role</p>
                            <p id="detailSupplierPosition" class="mt-0.5 font-semibold text-white text-[11px] leading-tight"></p>
                        </div>
                        <div class="rounded-[8px] bg-slate-800/90 px-3 py-1.5 border border-slate-700">
                            <p class="text-[10px] uppercase tracking-wider text-[#6EC1D1] font-semibold leading-tight">Primary Contact</p>
                            <p id="detailSupplierContact" class="mt-0.5 font-medium text-white text-[11px] leading-tight"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="detailEditSupplierButton" type="button" class="inline-flex items-center gap-1.5 rounded-[8px] bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 cursor-pointer">
                                <svg class="h-3.5 w-3.5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            <button id="detailArchiveSupplierButton" type="button" class="inline-flex items-center gap-1.5 rounded-[8px] border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 transition-all duration-200 cursor-pointer">
                                <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                Archive
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 space-y-6">
                    <!-- Key Assessment Metric Cards (4 Cards matching exactly your screenshot!) -->
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Performance Score -->
                        <div class="border border-gray-200 p-4 bg-white shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-black text-xs font-semibold truncate">Performance Score</p>
                                    <div class="mt-1">
                                        <p id="detailPerformanceScore" class="text-2xl font-bold text-black truncate">0/100</p>
                                        <p class="text-gray-500 text-xs mt-1 font-medium leading-tight truncate">Overall vendor rating score.</p>
                                    </div>
                                </div>
                                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                                    <svg class="w-5 h-5 text-[#145a66]" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Fast Moving Items Count -->
                        <div class="border border-emerald-200/80 p-4 bg-emerald-50/30 shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <p class="text-emerald-950 text-xs font-bold truncate">Fast Moving Items</p>
                                    </div>
                                    <div class="mt-1">
                                        <p id="detailFastMovingCount" class="text-2xl font-bold text-emerald-700 truncate">0</p>
                                        <p class="text-emerald-700/80 text-xs mt-1 font-medium leading-tight truncate">High demand &amp; sales velocity.</p>
                                    </div>
                                </div>
                                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 rounded-[10px] bg-emerald-100 text-emerald-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Slow Moving Items Count -->
                        <div class="border border-amber-200/80 p-4 bg-amber-50/30 shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>
                                        <p class="text-amber-950 text-xs font-bold truncate">Slow Moving Items</p>
                                    </div>
                                    <div class="mt-1">
                                        <p id="detailSlowMovingCount" class="text-2xl font-bold text-amber-700 truncate">0</p>
                                        <p class="text-amber-700/80 text-xs mt-1 font-medium leading-tight truncate">Low turnover / zero recent sales.</p>
                                    </div>
                                </div>
                                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 rounded-[10px] bg-amber-100 text-amber-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Total Products Count -->
                        <div class="border border-gray-200 p-4 bg-white shadow-sm hover:shadow-md transition-shadow" style="border-radius: 20px;">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-black text-xs font-semibold truncate">Total Supplied Catalog</p>
                                    <div class="mt-1">
                                        <p id="detailProductCount" class="text-2xl font-bold text-black truncate">0</p>
                                        <p class="text-gray-500 text-xs mt-1 font-medium leading-tight truncate">Total products from this supplier.</p>
                                    </div>
                                </div>
                                <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                                    <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fast & Slow Moving Products Breakdown (The 2 Big Beautiful Containers) -->
                    <div class="border-t border-slate-200 pt-6">
                        <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider font-bold text-[#145a66]">Movement Analysis Per Supplier</p>
                                <h3 class="text-lg font-bold text-slate-900">Fast &amp; Slow Moving Products</h3>
                            </div>
                            <span class="text-xs text-slate-500">Classified based on POS transaction sales velocity &amp; turnover rate</span>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-1 lg:grid-cols-2">
                            <!-- Left Column: Fast Moving Items -->
                            <div class="rounded-[16px] border border-emerald-200 bg-white shadow-sm overflow-hidden flex flex-col">
                                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-5 py-3.5 flex items-center justify-between text-white">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.82-1.17 2.05-1.785 3.322-.44.912-.86 1.874-1.25 2.766-.39.892-.74 1.705-1.03 2.378a9.42 9.42 0 00-.73 2.152A6.993 6.993 0 005 16a7 7 0 0013.93-1.03c.047-.328.07-.663.07-1.002 0-2.316-.95-4.408-2.484-5.91a8.96 8.96 0 00-2.348-1.572c-.596-.282-1.182-.628-1.773-1.026v-.907z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-bold text-sm">Fast Moving Products</span>
                                    </div>
                                    <span id="fastMovingBadgeCount" class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold backdrop-blur-sm">0 items</span>
                                </div>
                                <div id="fastMovingContainer" class="p-4 space-y-2.5 flex-1 max-h-80 overflow-y-auto">
                                    <!-- Populated via JavaScript -->
                                </div>
                            </div>

                            <!-- Right Column: Slow Moving Items -->
                            <div class="rounded-[16px] border border-amber-200 bg-white shadow-sm overflow-hidden flex flex-col">
                                <div class="bg-gradient-to-r from-amber-600 to-orange-700 px-5 py-3.5 flex items-center justify-between text-white">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-bold text-sm">Slow Moving Products</span>
                                    </div>
                                    <span id="slowMovingBadgeCount" class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold backdrop-blur-sm">0 items</span>
                                </div>
                                <div id="slowMovingContainer" class="p-4 space-y-2.5 flex-1 max-h-80 overflow-y-auto">
                                    <!-- Populated via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance & Delivery Reliability Section -->
                    <div class="border-t border-slate-200 pt-6">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider font-bold text-slate-500">Performance Summary</p>
                                <h3 class="mt-0.5 text-lg font-bold text-slate-900">Delivery &amp; Order Reliability</h3>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-[8px] bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200" id="detailDeliveredCount">0 delivered</span>
                                <span class="rounded-[8px] bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200" id="detailOrdersCount">0 orders</span>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Latest Purchase Orders</p>
                                <div id="detailOrderHistory" class="space-y-2.5"></div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Fulfillment &amp; Accuracy Metrics</p>
                                <div class="space-y-4 rounded-[16px] bg-slate-50/80 border border-slate-200 p-4">
                                    <div>
                                        <div class="flex justify-between text-xs text-slate-600 font-medium mb-1.5">
                                            <span>On-Time Delivery</span>
                                            <span id="detailOnTimeText" class="font-bold text-slate-900">0%</span>
                                        </div>
                                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                            <div id="detailOnTimeBar" class="h-full rounded-full bg-[#6EC1D1] transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-xs text-slate-600 font-medium mb-1.5">
                                            <span>Order Completion</span>
                                            <span id="detailCompletionText" class="font-bold text-slate-900">0%</span>
                                        </div>
                                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                            <div id="detailCompletionBar" class="h-full rounded-full bg-slate-900 transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                                        <span class="text-slate-500">Quality &amp; Defect Rating:</span>
                                        <span id="detailQualityScore" class="font-bold text-emerald-700">100/100 Quality</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Price List Table with Movement Filter Tabs -->
                    <div class="border-t border-slate-200 pt-6">
                        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider font-bold text-slate-500">Catalog &amp; Pricing</p>
                                <h3 class="mt-0.5 text-lg font-bold text-slate-900">Product Price List</h3>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Filter Tabs (All / Fast / Slow) -->
                                <div class="flex items-center rounded-[10px] bg-slate-100 p-1 border border-slate-200 text-xs">
                                    <button type="button" onclick="setProductFilter('all')" id="filterTabAll" class="rounded-[8px] px-3 py-1 font-bold transition-all bg-white text-slate-900 shadow-sm cursor-pointer">
                                        All (<span id="tabCountAll">0</span>)
                                    </button>
                                    <button type="button" onclick="setProductFilter('fast_moving')" id="filterTabFast" class="rounded-[8px] px-3 py-1 font-semibold transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                                        🔥 Fast (<span id="tabCountFast">0</span>)
                                    </button>
                                    <button type="button" onclick="setProductFilter('slow_moving')" id="filterTabSlow" class="rounded-[8px] px-3 py-1 font-semibold transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                                        ⏳ Slow (<span id="tabCountSlow">0</span>)
                                    </button>
                                </div>

                                <!-- Product Search Box -->
                                <div class="relative w-48 sm:w-60">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 12.65z"/>
                                    </svg>
                                    <input id="productSearchInput" type="search" placeholder="Search product / SKU..." class="w-full rounded-[10px] border border-slate-200 bg-white pl-8 pr-3 py-1.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1]" />
                                </div>

                                <span id="detailTotalValue" class="rounded-[10px] bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 whitespace-nowrap">₱0.00</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-[14px] border border-slate-200 shadow-xs">
                            <table class="min-w-full text-left text-sm text-slate-700">
                                <thead class="bg-[#0f172a] border-b border-slate-800 text-xs font-semibold text-white uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3.5 font-semibold text-white">Product</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">SKU</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">Category</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">Stock</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">Unit Price</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">Units Sold</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">Movement Status</th>
                                        <th class="px-4 py-3.5 font-semibold text-white">Last Restock</th>
                                    </tr>
                                </thead>
                                <tbody id="detailProductTable" class="divide-y divide-slate-100 bg-white"></tbody>
                            </table>
                        </div>
                        <div id="productPagination" class="mt-4 flex flex-col items-center gap-2 sm:flex-row sm:justify-between"></div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Add / Edit Supplier Modal -->
        <div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-[24px] bg-white shadow-[0_30px_80px_rgba(15,23,42,0.22)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 bg-[#0f172a] px-6 py-5">
                    <div>
                        <h2 id="supplierModalTitle" class="text-lg font-bold text-white">Add Supplier</h2>
                        <p id="supplierModalSubtitle" class="text-xs text-slate-300">Create a supplier record and link products automatically.</p>
                    </div>
                    <button type="button" id="closeSupplierModal" class="rounded-[10px] p-2 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="supplierForm" method="POST" action="{{ route('supplier.assessment.store') }}" class="space-y-4 px-6 py-6">
                    @csrf
                    <input type="hidden" name="_method" id="supplierFormMethod" value="POST" />
                    <input type="hidden" name="supplier_id" id="supplierId" value="" />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-xs font-semibold text-slate-700">
                            Supplier Name <span class="text-rose-500">*</span>
                            <input id="supplierNameInput" name="name" type="text" placeholder="e.g. Honda Philippines" class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition" required />
                        </label>
                        <label class="block text-xs font-semibold text-slate-700">
                            Contact Person
                            <input id="supplierContactInput" name="contact_person" type="text" placeholder="e.g. Juan Dela Cruz" class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition" />
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-xs font-semibold text-slate-700">
                            Email Address
                            <input id="supplierEmailInput" name="email" type="email" placeholder="e.g. vendor@supplier.com" class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition" />
                        </label>
                        <label class="block text-xs font-semibold text-slate-700">
                            Phone Number
                            <input id="supplierPhoneInput" name="phone" type="text" placeholder="e.g. +63 912 345 6789" class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition" />
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-xs font-semibold text-slate-700">
                            Contact Position / Role
                            <input id="supplierPositionInput" name="contact_position" type="text" placeholder="e.g. Sales Manager" class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition" />
                        </label>
                        <label class="block text-xs font-semibold text-slate-700">
                            Address / Location
                            <input id="supplierAddressInput" name="address" type="text" placeholder="e.g. Quezon City, Metro Manila" class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition" />
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-1">
                        <label class="block text-xs font-semibold text-slate-700">
                            Notes / Terms
                            <textarea id="supplierNotesInput" name="notes" rows="2" placeholder="Key vendor terms, delivery schedule, payment conditions..." class="mt-1.5 w-full rounded-[10px] border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:bg-white transition"></textarea>
                        </label>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end pt-3 border-t border-slate-100">
                        <button type="button" id="cancelSupplierModal" class="rounded-[10px] border border-slate-200 bg-white px-5 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-100 transition-all cursor-pointer">Cancel</button>
                        <button type="submit" id="supplierModalSubmit" class="rounded-[10px] bg-[#6EC1D1] px-5 py-2 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all cursor-pointer">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hidden Archive Supplier Form -->
        <form id="archiveSupplierForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        @push('scripts')
            <script>
                const rawSummaries = @json($supplierSummaries);
                const supplierSummaries = Array.isArray(rawSummaries) ? rawSummaries : Object.values(rawSummaries);

                // Modal elements
                const supplierModal = document.getElementById('supplierModal');
                const supplierForm = document.getElementById('supplierForm');
                const supplierModalTitle = document.getElementById('supplierModalTitle');
                const supplierModalSubtitle = document.getElementById('supplierModalSubtitle');
                const supplierFormMethod = document.getElementById('supplierFormMethod');
                const supplierId = document.getElementById('supplierId');
                const supplierNameInput = document.getElementById('supplierNameInput');
                const supplierContactInput = document.getElementById('supplierContactInput');
                const supplierPositionInput = document.getElementById('supplierPositionInput');
                const supplierAddressInput = document.getElementById('supplierAddressInput');
                const supplierEmailInput = document.getElementById('supplierEmailInput');
                const supplierPhoneInput = document.getElementById('supplierPhoneInput');
                const supplierNotesInput = document.getElementById('supplierNotesInput');
                const supplierModalSubmit = document.getElementById('supplierModalSubmit');
                const openSupplierModalButton = document.getElementById('openSupplierModal');
                const closeSupplierModalButton = document.getElementById('closeSupplierModal');
                const cancelSupplierModalButton = document.getElementById('cancelSupplierModal');
                const archiveSupplierForm = document.getElementById('archiveSupplierForm');

                // Dropdown elements
                const supplierDropdownBtn = document.getElementById('supplierDropdownBtn');
                const supplierDropdownMenu = document.getElementById('supplierDropdownMenu');
                const supplierDropdownSearch = document.getElementById('supplierDropdownSearch');
                const supplierDropdownOptions = document.getElementById('supplierDropdownOptions');
                const dropdownChevron = document.getElementById('dropdownChevron');
                const dropdownSelectedName = document.getElementById('dropdownSelectedName');
                const dropdownSelectedMeta = document.getElementById('dropdownSelectedMeta');

                // Ribbon & Action elements
                const selectedSupplierRibbon = document.getElementById('selectedSupplierRibbon');
                const supplierActionButtons = document.getElementById('supplierActionButtons');
                const quickEditBtn = document.getElementById('quickEditBtn');
                const quickArchiveBtn = document.getElementById('quickArchiveBtn');
                const ribbonSupplierName = document.getElementById('ribbonSupplierName');
                const ribbonSupplierRole = document.getElementById('ribbonSupplierRole');
                const ribbonContactPerson = document.getElementById('ribbonContactPerson');
                const ribbonContactEmail = document.getElementById('ribbonContactEmail');
                const ribbonAddress = document.getElementById('ribbonAddress');

                // Detail elements
                const supplierDetailsContainer = document.getElementById('supplierDetailsContainer');
                const supplierDetailPlaceholder = document.getElementById('supplierDetailPlaceholder');
                const supplierDetailPanel = document.getElementById('supplierDetailPanel');
                const detailSupplierName = document.getElementById('detailSupplierName');
                const detailSupplierNotes = document.getElementById('detailSupplierNotes');
                const detailSupplierPosition = document.getElementById('detailSupplierPosition');
                const detailSupplierAddress = document.getElementById('detailSupplierAddress');
                const detailSupplierContact = document.getElementById('detailSupplierContact');
                const detailPerformanceScore = document.getElementById('detailPerformanceScore');
                const detailFastMovingCount = document.getElementById('detailFastMovingCount');
                const detailSlowMovingCount = document.getElementById('detailSlowMovingCount');
                const detailProductCount = document.getElementById('detailProductCount');
                const detailDeliveredCount = document.getElementById('detailDeliveredCount');
                const detailOrdersCount = document.getElementById('detailOrdersCount');
                const detailOrderHistory = document.getElementById('detailOrderHistory');
                const detailOnTimeBar = document.getElementById('detailOnTimeBar');
                const detailCompletionBar = document.getElementById('detailCompletionBar');
                const detailOnTimeText = document.getElementById('detailOnTimeText');
                const detailCompletionText = document.getElementById('detailCompletionText');
                const detailQualityScore = document.getElementById('detailQualityScore');
                const detailTotalValue = document.getElementById('detailTotalValue');
                const detailEditSupplierButton = document.getElementById('detailEditSupplierButton');
                const detailArchiveSupplierButton = document.getElementById('detailArchiveSupplierButton');

                // Fast & Slow moving widgets
                const fastMovingBadgeCount = document.getElementById('fastMovingBadgeCount');
                const fastMovingContainer = document.getElementById('fastMovingContainer');
                const slowMovingBadgeCount = document.getElementById('slowMovingBadgeCount');
                const slowMovingContainer = document.getElementById('slowMovingContainer');

                // Product table & filter elements
                const detailProductTable = document.getElementById('detailProductTable');
                const productPagination = document.getElementById('productPagination');
                const productSearchInput = document.getElementById('productSearchInput');
                const tabCountAll = document.getElementById('tabCountAll');
                const tabCountFast = document.getElementById('tabCountFast');
                const tabCountSlow = document.getElementById('tabCountSlow');
                const filterTabAll = document.getElementById('filterTabAll');
                const filterTabFast = document.getElementById('filterTabFast');
                const filterTabSlow = document.getElementById('filterTabSlow');

                let activeSupplier = null;
                let currentProductPage = 1;
                const productsPerPage = 10;
                let currentProductFilter = 'all'; // 'all', 'fast_moving', 'slow_moving'

                function openModal(modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal(modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                function showToast(message, type = 'success') {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-50 rounded-[12px] border px-4 py-3 text-xs font-semibold shadow-xl transition-opacity duration-500 ${type === 'success' ? 'border-[#6EC1D1] bg-teal-50 text-slate-900' : 'border-rose-200 bg-rose-50 text-rose-800'}`;
                    toast.textContent = message;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 500);
                    }, 3000);
                }

                function resetSupplierForm() {
                    supplierForm.reset();
                    supplierForm.action = '{{ route('supplier.assessment.store') }}';
                    supplierFormMethod.value = 'POST';
                    supplierId.value = '';
                    supplierModalTitle.textContent = 'Add Supplier';
                    supplierModalSubtitle.textContent = 'Create a supplier record and link products automatically.';
                    supplierModalSubmit.textContent = 'Save Supplier';
                }

                function fillSupplierForm(supplier) {
                    supplierModalTitle.textContent = supplier.id ? 'Edit Supplier' : 'Add Supplier';
                    supplierModalSubtitle.textContent = supplier.id
                        ? 'Update the supplier details and product links.'
                        : 'Create a supplier record and keep product links intact.';
                    supplierFormMethod.value = supplier.id ? 'PATCH' : 'POST';
                    supplierId.value = supplier.id || '';
                    supplierNameInput.value = supplier.name || '';
                    supplierContactInput.value = supplier.contact_person || '';
                    supplierPositionInput.value = supplier.contact_position || '';
                    supplierAddressInput.value = supplier.address || '';
                    supplierEmailInput.value = supplier.email || '';
                    supplierPhoneInput.value = supplier.phone || '';
                    supplierNotesInput.value = supplier.notes || '';
                    supplierForm.action = supplier.id
                        ? '{{ url('supplier-assessment/suppliers') }}/' + supplier.id
                        : '{{ route('supplier.assessment.store') }}';
                    supplierModalSubmit.textContent = supplier.id ? 'Update Supplier' : 'Save Supplier';
                }

                // Render Searchable Dropdown Options
                function renderDropdownOptions() {
                    const searchTerm = (supplierDropdownSearch.value || '').trim().toLowerCase();
                    supplierDropdownOptions.innerHTML = '';

                    const filtered = supplierSummaries.filter(s => {
                        const name = (s.name || '').toLowerCase();
                        const contact = (s.contact_person || '').toLowerCase();
                        const email = (s.email || '').toLowerCase();
                        return name.includes(searchTerm) || contact.includes(searchTerm) || email.includes(searchTerm);
                    });

                    if (filtered.length === 0) {
                        supplierDropdownOptions.innerHTML = `
                            <div class="p-4 text-center text-xs text-slate-500">
                                No suppliers matching "${searchTerm}"
                            </div>
                        `;
                        return;
                    }

                    filtered.forEach(supplier => {
                        const isSelected = activeSupplier && activeSupplier.name === supplier.name;
                        const optionItem = document.createElement('div');
                        optionItem.dataset.supplierName = supplier.name;
                        optionItem.className = `supplier-option-item p-3.5 hover:bg-slate-50 transition cursor-pointer flex items-center justify-between gap-3 ${isSelected ? 'bg-teal-50/70 border-l-4 border-[#6EC1D1]' : ''}`;
                        optionItem.innerHTML = `
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-slate-900 truncate">${supplier.name}</p>
                                    <span class="rounded-[6px] bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">${supplier.contact_position || 'Supplier'}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">${supplier.contact_person || 'No contact'} · ${supplier.email || supplier.phone || 'No direct info'}</p>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <span class="rounded-[6px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold">🔥 ${supplier.fast_moving_count ?? 0}</span>
                                <span class="rounded-[6px] bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 text-[10px] font-bold">⏳ ${supplier.slow_moving_count ?? 0}</span>
                                <span class="rounded-[6px] bg-slate-100 text-slate-700 border border-slate-200 px-2 py-0.5 text-[10px] font-semibold">${supplier.product_count ?? 0} items</span>
                            </div>
                        `;

                        optionItem.addEventListener('click', () => {
                            setSupplierDetail(supplier.name, true);
                            closeDropdown();
                        });

                        supplierDropdownOptions.appendChild(optionItem);
                    });
                }

                function toggleDropdown() {
                    const isHidden = supplierDropdownMenu.classList.contains('hidden');
                    if (isHidden) {
                        openDropdown();
                    } else {
                        closeDropdown();
                    }
                }

                function openDropdown() {
                    supplierDropdownMenu.classList.remove('hidden');
                    dropdownChevron.classList.add('rotate-180');
                    renderDropdownOptions();
                    setTimeout(() => supplierDropdownSearch.focus(), 50);
                }

                function closeDropdown() {
                    supplierDropdownMenu.classList.add('hidden');
                    dropdownChevron.classList.remove('rotate-180');
                }

                // Global document click to close dropdown
                document.addEventListener('click', (e) => {
                    if (!supplierDropdownBtn.contains(e.target) && !supplierDropdownMenu.contains(e.target)) {
                        closeDropdown();
                    }
                });

                supplierDropdownBtn.addEventListener('click', toggleDropdown);
                supplierDropdownSearch.addEventListener('input', renderDropdownOptions);

                // Option click delegation
                supplierDropdownOptions.addEventListener('click', (e) => {
                    const item = e.target.closest('.supplier-option-item');
                    if (item && item.dataset.supplierName) {
                        setSupplierDetail(item.dataset.supplierName, true);
                        closeDropdown();
                    }
                });

                // Set Active Supplier and Render Full Assessment Details
                function setSupplierDetail(supplierName, shouldScroll = false) {
                    const supplier = supplierSummaries.find(item => item.name === supplierName);
                    if (!supplier) return;

                    activeSupplier = supplier;
                    currentProductPage = 1;

                    // Update Trigger Label
                    dropdownSelectedName.textContent = supplier.name;
                    dropdownSelectedMeta.textContent = `${supplier.product_count ?? 0} products · 🔥 ${supplier.fast_moving_count ?? 0} Fast Moving · ⏳ ${supplier.slow_moving_count ?? 0} Slow Moving · Score: ${supplier.performance_score ?? 0}/100`;

                    // Update Summary Ribbon
                    selectedSupplierRibbon.classList.remove('hidden');
                    supplierActionButtons.classList.remove('hidden');
                    ribbonSupplierName.textContent = supplier.name;
                    ribbonSupplierRole.textContent = supplier.contact_position || 'Supplier';
                    ribbonContactPerson.textContent = supplier.contact_person ? `${supplier.contact_person} (${supplier.contact_position || 'Contact'})` : 'No contact person assigned';
                    ribbonContactEmail.textContent = supplier.email ? supplier.email : (supplier.phone || 'No email/phone recorded');
                    ribbonAddress.textContent = supplier.address || 'No physical address specified';

                    // Show Main Detail Panel
                    supplierDetailPlaceholder.classList.add('hidden');
                    supplierDetailPanel.classList.remove('hidden');

                    detailSupplierName.textContent = supplier.name;
                    detailSupplierNotes.textContent = supplier.notes || 'No additional notes provided.';
                    detailSupplierPosition.textContent = supplier.contact_position || 'Supplier';

                    detailSupplierAddress.innerHTML = supplier.address
                        ? `<span class="inline-flex items-center gap-1.5">
                             <svg class="h-3.5 w-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                             </svg>
                             <span>${supplier.address}</span>
                           </span>`
                        : '';

                    detailSupplierContact.textContent = supplier.contact_person
                        ? `${supplier.contact_person} · ${supplier.email || supplier.phone || 'No contact info'}`
                        : (supplier.email || supplier.phone || 'No contact info');

                    // Metric Cards
                    detailPerformanceScore.textContent = `${supplier.performance_score ?? 0}/100`;
                    detailFastMovingCount.textContent = `${supplier.fast_moving_count ?? 0}`;
                    detailSlowMovingCount.textContent = `${supplier.slow_moving_count ?? 0}`;
                    detailProductCount.textContent = supplier.product_count ?? 0;
                    detailDeliveredCount.textContent = `${supplier.delivered_orders_count ?? 0} delivered`;
                    detailOrdersCount.textContent = `${supplier.orders_count ?? 0} orders`;

                    // Progress Bars
                    detailOnTimeBar.style.width = `${Math.min(100, Math.max(0, supplier.on_time_rate || 0))}%`;
                    detailCompletionBar.style.width = `${Math.min(100, Math.max(0, supplier.completion_rate || 0))}%`;
                    if (detailOnTimeText) detailOnTimeText.textContent = `${supplier.on_time_rate ?? 0}%`;
                    if (detailCompletionText) detailCompletionText.textContent = `${supplier.completion_rate ?? 0}%`;
                    if (detailQualityScore) detailQualityScore.textContent = `${supplier.quality_score ?? 100}/100 Quality (Defect: ${supplier.defect_rate ?? 0}%)`;
                    detailTotalValue.textContent = `₱${Number(supplier.total_value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                    // Render Fast & Slow Moving Lists
                    renderFastSlowMovingBreakdown(supplier);

                    // Render Order History
                    renderOrderHistory(supplier);

                    // Render Product Price List Table
                    renderProductTable();

                    if (shouldScroll) {
                        supplierDetailsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }

                // Render Fast and Slow Moving Lists for Active Supplier (Matching Screenshot Cards)
                function renderFastSlowMovingBreakdown(supplier) {
                    const fastProducts = supplier.fast_moving_products || [];
                    const slowProducts = supplier.slow_moving_products || [];

                    fastMovingBadgeCount.textContent = `${fastProducts.length} item${fastProducts.length === 1 ? '' : 's'}`;
                    slowMovingBadgeCount.textContent = `${slowProducts.length} item${slowProducts.length === 1 ? '' : 's'}`;

                    // Render Fast Moving List
                    fastMovingContainer.innerHTML = '';
                    if (fastProducts.length === 0) {
                        fastMovingContainer.innerHTML = `
                            <div class="py-10 text-center text-xs text-slate-500">
                                <svg class="w-8 h-8 mx-auto text-emerald-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                No fast-moving products recorded for this supplier.
                            </div>
                        `;
                    } else {
                        fastProducts.forEach(prod => {
                            const card = document.createElement('div');
                            card.className = 'rounded-[14px] border border-emerald-200/80 bg-white p-3.5 hover:bg-emerald-50/40 hover:border-emerald-300 transition-all flex items-center justify-between gap-3 shadow-xs';
                            card.innerHTML = `
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-900 truncate uppercase">${prod.name}</p>
                                        <span class="rounded-[6px] bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 text-[10px] font-semibold">${prod.category || 'General'}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate mt-1 font-medium">SKU: <span class="font-mono text-slate-700">${prod.sku || 'N/A'}</span> · Stock: <span class="font-bold text-slate-900">${prod.stock_quantity}</span></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="inline-flex items-center gap-1 rounded-[8px] bg-emerald-600 text-white px-2.5 py-0.5 text-[10px] font-bold shadow-xs">
                                        🔥 ${prod.units_sold} sold
                                    </span>
                                    <p class="text-xs font-bold text-slate-900 mt-1">₱${Number(prod.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                                </div>
                            `;
                            fastMovingContainer.appendChild(card);
                        });
                    }

                    // Render Slow Moving List
                    slowMovingContainer.innerHTML = '';
                    if (slowProducts.length === 0) {
                        slowMovingContainer.innerHTML = `
                            <div class="py-10 text-center text-xs text-slate-500">
                                <svg class="w-8 h-8 mx-auto text-amber-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                No slow-moving products for this supplier.
                            </div>
                        `;
                    } else {
                        slowProducts.forEach(prod => {
                            const card = document.createElement('div');
                            card.className = 'rounded-[14px] border border-amber-200/80 bg-white p-3.5 hover:bg-amber-50/40 hover:border-amber-300 transition-all flex items-center justify-between gap-3 shadow-xs';
                            card.innerHTML = `
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-900 truncate uppercase">${prod.name}</p>
                                        <span class="rounded-[6px] bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 text-[10px] font-semibold">${prod.category || 'General'}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate mt-1 font-medium">SKU: <span class="font-mono text-slate-700">${prod.sku || 'N/A'}</span> · Current Stock: <span class="font-bold text-slate-900">${prod.stock_quantity}</span></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="inline-flex items-center gap-1 rounded-[8px] bg-amber-100 text-amber-900 border border-amber-200 px-2.5 py-0.5 text-[10px] font-bold">
                                        ⏳ ${prod.units_sold} sold
                                    </span>
                                    <p class="text-xs font-bold text-slate-900 mt-1">₱${Number(prod.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                                </div>
                            `;
                            slowMovingContainer.appendChild(card);
                        });
                    }
                }

                // Render Latest Orders
                function renderOrderHistory(supplier) {
                    detailOrderHistory.innerHTML = '';
                    if (!supplier.orders || !supplier.orders.length) {
                        detailOrderHistory.innerHTML = '<div class="rounded-[14px] border border-slate-200 bg-slate-50/70 p-4 text-xs text-slate-500 text-center">No order history available for this supplier.</div>';
                    } else {
                        supplier.orders.slice(0, 5).forEach(order => {
                            const statusLower = (order.status || '').toLowerCase();
                            const isDelivered = statusLower === 'completed' || statusLower === 'delivered';
                            const isPartiallyReceived = statusLower === 'partially received';
                            const statusBadgeClass = isDelivered
                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                : (isPartiallyReceived ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200');
                            const receivedDisplay = order.received_date || order.completed_at || 'Pending';

                            detailOrderHistory.insertAdjacentHTML('beforeend', `
                                <div class="rounded-[14px] border border-slate-200 bg-white p-3.5 shadow-xs hover:border-slate-300 transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900 text-xs font-mono">${order.order_number}</p>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-[6px] ${statusBadgeClass}">${order.status}</span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        Expected: ${order.expected_delivery_date || 'Not specified'} · Received: ${receivedDisplay}
                                    </div>
                                    <div class="mt-1.5 text-xs font-bold text-slate-900">₱${Number(order.total_amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                                </div>
                            `);
                        });
                    }
                }

                // Filter & Render Product Price List Table
                function setProductFilter(filterType) {
                    currentProductFilter = filterType;
                    currentProductPage = 1;

                    // Update Tab UI
                    [filterTabAll, filterTabFast, filterTabSlow].forEach(tab => {
                        tab.classList.remove('bg-white', 'text-slate-900', 'shadow-sm', 'font-bold');
                        tab.classList.add('text-slate-600', 'font-semibold');
                    });

                    if (filterType === 'all') {
                        filterTabAll.classList.add('bg-white', 'text-slate-900', 'shadow-sm', 'font-bold');
                        filterTabAll.classList.remove('text-slate-600');
                    } else if (filterType === 'fast_moving') {
                        filterTabFast.classList.add('bg-white', 'text-slate-900', 'shadow-sm', 'font-bold');
                        filterTabFast.classList.remove('text-slate-600');
                    } else if (filterType === 'slow_moving') {
                        filterTabSlow.classList.add('bg-white', 'text-slate-900', 'shadow-sm', 'font-bold');
                        filterTabSlow.classList.remove('text-slate-600');
                    }

                    renderProductTable();
                }

                window.setProductFilter = setProductFilter;

                function renderProductTable() {
                    if (!activeSupplier) return;

                    const allProducts = activeSupplier.products || [];
                    const fastCount = (activeSupplier.fast_moving_products || []).length;
                    const slowCount = (activeSupplier.slow_moving_products || []).length;

                    tabCountAll.textContent = allProducts.length;
                    tabCountFast.textContent = fastCount;
                    tabCountSlow.textContent = slowCount;

                    const search = (productSearchInput.value || '').trim().toLowerCase();

                    // Apply filters
                    let filtered = allProducts.filter(p => {
                        if (currentProductFilter !== 'all' && p.movement_category !== currentProductFilter) {
                            return false;
                        }
                        if (search) {
                            const name = (p.name || '').toLowerCase();
                            const sku = (p.sku || '').toLowerCase();
                            const cat = (p.category || '').toLowerCase();
                            return name.includes(search) || sku.includes(search) || cat.includes(search);
                        }
                        return true;
                    });

                    detailProductTable.innerHTML = '';
                    if (filtered.length === 0) {
                        detailProductTable.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-xs text-slate-500 font-medium">No products match the selected filter.</td></tr>';
                        productPagination.innerHTML = '';
                        return;
                    }

                    const totalPages = Math.ceil(filtered.length / productsPerPage);
                    currentProductPage = Math.min(currentProductPage, totalPages) || 1;
                    const startIndex = (currentProductPage - 1) * productsPerPage;
                    const endIndex = startIndex + productsPerPage;
                    const paginated = filtered.slice(startIndex, endIndex);

                    paginated.forEach(product => {
                        const isFast = product.movement_category === 'fast_moving';
                        const badge = isFast
                            ? '<span class="inline-flex items-center gap-1 rounded-[6px] bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-700">🔥 Fast Moving</span>'
                            : '<span class="inline-flex items-center gap-1 rounded-[6px] bg-amber-50 border border-amber-200 px-2 py-0.5 text-[10px] font-bold text-amber-700">⏳ Slow Moving</span>';

                        detailProductTable.insertAdjacentHTML('beforeend', `
                            <tr class="border-b border-slate-100 hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3.5 font-bold text-slate-900 text-xs">${product.name}</td>
                                <td class="px-4 py-3.5 text-slate-600 font-mono text-xs">${product.sku || 'N/A'}</td>
                                <td class="px-4 py-3.5 text-slate-600 text-xs">${product.category || 'Uncategorized'}</td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 text-xs">${product.stock_quantity}</td>
                                <td class="px-4 py-3.5 font-bold text-slate-900 text-xs">₱${Number(product.price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                <td class="px-4 py-3.5 font-bold text-slate-800 text-xs">${product.units_sold ?? 0}</td>
                                <td class="px-4 py-3.5">${badge}</td>
                                <td class="px-4 py-3.5 text-slate-500 text-xs">${product.last_restock_date || 'N/A'}</td>
                            </tr>
                        `);
                    });

                    // Render pagination
                    if (totalPages > 1) {
                        productPagination.innerHTML = `
                            <div class="text-xs text-slate-500">
                                Showing ${startIndex + 1} to ${Math.min(endIndex, filtered.length)} of ${filtered.length} products
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap justify-center">
                                <button type="button" onclick="window.changeProductPage(${currentProductPage - 1})" ${currentProductPage === 1 ? 'disabled' : ''} class="px-3 py-1.5 text-xs rounded-[8px] border border-slate-200 transition-all cursor-pointer ${currentProductPage === 1 ? 'text-slate-400 bg-slate-50 cursor-not-allowed' : 'text-slate-700 bg-white hover:bg-slate-100'}">Prev</button>
                                ${Array.from({length: totalPages}, (_, i) => i + 1).map(page => `
                                    <button type="button" onclick="window.changeProductPage(${page})" class="px-3 py-1.5 text-xs rounded-[8px] transition-all cursor-pointer ${page === currentProductPage ? 'font-bold text-slate-900 bg-[#6EC1D1] border border-slate-200 shadow-sm' : 'text-slate-700 border border-slate-200 bg-white hover:bg-slate-100'}">${page}</button>
                                `).join('')}
                                <button type="button" onclick="window.changeProductPage(${currentProductPage + 1})" ${currentProductPage === totalPages ? 'disabled' : ''} class="px-3 py-1.5 text-xs rounded-[8px] border border-slate-200 transition-all cursor-pointer ${currentProductPage === totalPages ? 'text-slate-400 bg-slate-50 cursor-not-allowed' : 'text-slate-700 bg-white hover:bg-slate-100'}">Next</button>
                            </div>
                        `;
                    } else {
                        productPagination.innerHTML = '';
                    }
                }

                window.changeProductPage = function(page) {
                    currentProductPage = page;
                    renderProductTable();
                };

                productSearchInput.addEventListener('input', () => {
                    currentProductPage = 1;
                    renderProductTable();
                });

                // Edit & Archive Handlers
                function handleEditActiveSupplier() {
                    if (!activeSupplier) return;
                    fillSupplierForm(activeSupplier);
                    openModal(supplierModal);
                }

                function handleArchiveActiveSupplier() {
                    if (!activeSupplier) return;
                    if (!activeSupplier.id) {
                        showToast('This supplier record is not yet saved to the database.', 'error');
                        return;
                    }
                    archiveSupplierForm.action = '{{ url('supplier-assessment/suppliers') }}/' + activeSupplier.id;
                    if (confirm(`Archive "${activeSupplier.name}"? This will remove it from the active supplier list.`)) {
                        archiveSupplierForm.submit();
                    }
                }

                detailEditSupplierButton.addEventListener('click', handleEditActiveSupplier);
                quickEditBtn.addEventListener('click', handleEditActiveSupplier);

                detailArchiveSupplierButton.addEventListener('click', handleArchiveActiveSupplier);
                quickArchiveBtn.addEventListener('click', handleArchiveActiveSupplier);

                // Add Supplier Modal Trigger
                openSupplierModalButton.addEventListener('click', () => {
                    resetSupplierForm();
                    openModal(supplierModal);
                });

                [closeSupplierModalButton, cancelSupplierModalButton].forEach(button => {
                    button.addEventListener('click', () => closeModal(supplierModal));
                });

                // Populate dropdown options on load
                renderDropdownOptions();

                // Initial Load: Manual selection only (Do not auto-select unless explicitly in URL)
                const urlParams = new URLSearchParams(window.location.search);
                const selectedSupplierParam = urlParams.get('selected_supplier');

                if (selectedSupplierParam) {
                    setSupplierDetail(selectedSupplierParam);
                }
            </script>
        @endpush
    </div>
</x-layouts.app>