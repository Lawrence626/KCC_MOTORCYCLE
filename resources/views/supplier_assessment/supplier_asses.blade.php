<x-layouts.app :title="__('Supplier Assessment')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="pl-3 lg:pl-1">
                <h1 class="text-3xl font-bold text-slate-900">Supplier Assessment</h1>
                <p class="text-gray-600 text-sm mt-1">Track supplier performance, manage supplier records, and inspect products with pricing at a glance.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button id="openSupplierModal" class="inline-flex items-center gap-2 rounded-[10px] bg-[#00FFF2] px-4 py-2 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#00D9CC] transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add supplier
                </button>
                <a href="{{ route('supplier.assessment.archived') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archive list
                </a>
            </div>
        </div>

        @if(session('success'))
            <div id="success-toast" class="fixed top-4 right-8 z-50 rounded-[10px] border border-[#00fff2] bg-[#e6fffe] p-4 text-sm font-medium text-slate-900 shadow-lg">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('success-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.5s ease';
                        setTimeout(() => toast.remove(), 500);
                    }
                }, 3000);
            </script>
        @endif

        @if($errors->any())
            <div class="rounded-[14px] border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold">Active Suppliers</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($quickStats['activeSuppliers']) }}</p>
                            <p class="text-gray-500 text-[11px] leading-tight mt-1 font-medium whitespace-nowrap">Currently active supplier records.</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                        <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold">Tracked Products</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($quickStats['trackedProducts']) }}</p>
                            <p class="text-gray-500 text-[11px] leading-tight mt-1 font-medium whitespace-nowrap">Total products linked across suppliers.</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                        <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold">Stock Inventory Value</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">&#8369;{{ number_format($quickStats['stockValue'], 2) }}</p>
                            <p class="text-gray-500 text-[11px] leading-tight mt-1 font-medium whitespace-nowrap">Combined value of supplier stock.</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                        <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05 1.18 1.91 2.53 1.91 1.29 0 2.13-.59 2.13-1.61 0-1.11-1.02-1.55-2.74-2.02-2.09-.56-3.72-1.35-3.72-3.47 0-1.89 1.45-3.09 3.11-3.43V4h2.67v1.93c1.61.32 2.82 1.43 2.92 3.16h-1.92c-.09-.91-.89-1.63-2.18-1.63-1.12 0-1.86.52-1.86 1.41 0 .96.89 1.38 2.49 1.84 2.19.62 3.97 1.46 3.97 3.65 0 2.01-1.52 3.23-3.32 3.73z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <section class="rounded-[15px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            <!-- Section Header Bar (matching All Stocks design) -->
            <div class="bg-[#0f172a] px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-800 rounded-t-[15px]">
                <div>
                    <h2 class="text-lg font-bold text-white">Select a Supplier</h2>
                    <p class="text-xs text-slate-300">Search and pick a supplier partner to inspect details below</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative w-72 md:w-80">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 12.65z"/>
                        </svg>
                        <input id="supplierSearch" type="search" placeholder="Search supplier, contact, email..." class="w-full rounded-[10px] border border-slate-700 bg-slate-800/90 pl-10 pr-4 py-2 text-xs text-white placeholder:text-slate-300 focus:outline-none focus:ring-1 focus:ring-[#00fff2]" />
                    </div>
                    <span id="supplierListCount" class="rounded-[10px] bg-[#00FFF2] px-3 py-2 text-xs font-bold text-slate-900 shadow-sm whitespace-nowrap">{{ number_format($supplierSummaries->count()) }} shown</span>
                </div>
            </div>

            <div class="p-5 space-y-4">
                <div id="supplierList" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"></div>
                <div id="supplierPagination" class="mt-4 flex flex-col items-center gap-2 sm:flex-row sm:justify-between border-t border-slate-100 pt-3"></div>
            </div>
        </section>

        <main id="supplierDetailsContainer" class="rounded-[15px] border border-slate-200 bg-white shadow-sm min-h-[350px] overflow-hidden">
            <div id="supplierDetailPlaceholder" class="p-6 py-16 text-center text-slate-500">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0v-4m0 4h4m-4-4l4 4"/>
                </svg>
                <p class="mt-4 text-lg font-semibold text-slate-900">Supplier details will appear here</p>
                <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">Click a supplier card from above to inspect pricing, performance, delivery reliability, and linked products.</p>
            </div>

            <section id="supplierDetailPanel" class="hidden space-y-6">
                <!-- Section Header Bar (matching All Stocks design) -->
                <div class="bg-[#0f172a] px-6 py-5 border-b border-slate-800 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between rounded-t-[15px]">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-semibold text-[#00fff2]">Supplier overview</p>
                        <h2 id="detailSupplierName" class="mt-0.5 text-2xl md:text-3xl font-bold text-white"></h2>
                        <p id="detailSupplierNotes" class="mt-1 text-xs text-slate-300"></p>
                        <p id="detailSupplierAddress" class="mt-1 text-xs text-slate-400"></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="rounded-[8px] bg-slate-800/90 px-3 py-1.5 border border-slate-700">
                            <p class="text-[10px] uppercase tracking-wider text-[#00fff2] font-semibold leading-tight">Role</p>
                            <p id="detailSupplierPosition" class="mt-0.5 font-semibold text-white text-[11px] leading-tight"></p>
                        </div>
                        <div class="rounded-[8px] bg-slate-800/90 px-3 py-1.5 border border-slate-700">
                            <p class="text-[10px] uppercase tracking-wider text-[#00fff2] font-semibold leading-tight">Primary Contact</p>
                            <p id="detailSupplierContact" class="mt-0.5 font-medium text-white text-[11px] leading-tight"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="detailEditSupplierButton" type="button" class="inline-flex items-center gap-1.5 rounded-[8px] bg-[#00FFF2] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#00D9CC] transition-all duration-200">
                                <svg class="h-3.5 w-3.5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            <button id="detailArchiveSupplierButton" type="button" class="inline-flex items-center gap-1.5 rounded-[8px] border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 transition-all duration-200">
                                <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                Archive
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 space-y-6">

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-black text-xs font-semibold">Performance Score</p>
                                <div class="mt-1">
                                    <p id="detailPerformanceScore" class="text-2xl font-bold text-black"></p>
                                    <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Overall vendor rating score.</p>
                                </div>
                            </div>
                            <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                                <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-black text-xs font-semibold">On-Time Delivery</p>
                                <div class="mt-1">
                                    <p id="detailOnTimeRate" class="text-2xl font-bold text-black"></p>
                                    <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Punctual shipment rate.</p>
                                </div>
                            </div>
                            <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                                <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="#000000" stroke="#000000" stroke-width="1">
                                    <rect x="9" y="1.5" width="6" height="2" rx="1"/>
                                    <line x1="17" y1="4" x2="19.5" y2="6.5" stroke="#000000" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="12" cy="14" r="8"/>
                                    <line x1="12" y1="14" x2="12" y2="10" stroke="#00fff2" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-black text-xs font-semibold">Order Completion</p>
                                <div class="mt-1">
                                    <p id="detailCompletionRate" class="text-2xl font-bold text-black"></p>
                                    <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Fulfilled orders without issues.</p>
                                </div>
                            </div>
                            <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                                <svg class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="#000000">
                                    <path d="M7 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-2.18a3 3 0 0 0-5.64 0H7zm5 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2zM7 9h10v1.5H7V9zm0 3h10v1.5H7V12zm0 3h6v1.5H7V15z"/>
                                    <circle cx="17" cy="17" r="5.5" fill="#00fff2"/>
                                    <path d="M17 22a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-2.2-5.1 1.4-1.4 1 1 2.2-2.2 1.4 1.4-3.6 3.6-2.4-2.4z" fill="#000000"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-black text-xs font-semibold">Total Products</p>
                                <div class="mt-1">
                                    <p id="detailProductCount" class="text-2xl font-bold text-black"></p>
                                    <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Supplied catalog items.</p>
                                </div>
                            </div>
                            <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                                <svg class="w-5 h-5 text-black" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Performance summary</p>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">Delivery & Order Reliability</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-[8px] bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200" id="detailDeliveredCount"></span>
                            <span class="rounded-[8px] bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200" id="detailOrdersCount"></span>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-slate-700 mb-3">Latest Orders</p>
                            <div id="detailOrderHistory" class="space-y-2.5"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-700 mb-3">Delivery Reliability</p>
                            <div class="space-y-4 rounded-[16px] bg-slate-50/70 border border-slate-200/80 p-4">
                                <div>
                                    <div class="flex justify-between text-xs text-slate-600 font-medium mb-1">
                                        <span>On-time deliveries</span>
                                        <span id="detailOnTimeText">0%</span>
                                    </div>
                                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                        <div id="detailOnTimeBar" class="h-full rounded-full bg-[#00FFF2]" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs text-slate-600 font-medium mb-1">
                                        <span>Order completion</span>
                                        <span id="detailCompletionText">0%</span>
                                    </div>
                                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                                        <div id="detailCompletionBar" class="h-full rounded-full bg-slate-900" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-6">
                    <div class="mb-4 flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Supplier pricing</p>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">Product Price List</h3>
                        </div>
                        <span id="detailTotalValue" class="rounded-[10px] bg-slate-100 px-3 py-1 text-xs font-bold text-slate-900 border border-slate-200"></span>
                    </div>
                    <div class="overflow-x-auto rounded-[10px] border border-slate-200">
                        <table class="min-w-full text-left text-sm text-slate-700">
                            <thead class="bg-[#0f172a] border-b border-slate-200 text-xs font-semibold text-white uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-white">Product</th>
                                    <th class="px-4 py-3 font-semibold text-white">SKU</th>
                                    <th class="px-4 py-3 font-semibold text-white">Category</th>
                                    <th class="px-4 py-3 font-semibold text-white">Stock</th>
                                    <th class="px-4 py-3 font-semibold text-white">Unit Price</th>
                                    <th class="px-4 py-3 font-semibold text-white">Restock</th>
                                </tr>
                            </thead>
                            <tbody id="detailProductTable" class="divide-y divide-slate-200 bg-white"></tbody>
                        </table>
                    </div>
                    <div id="productPagination" class="mt-4 flex flex-col items-center gap-2 sm:flex-row sm:justify-between"></div>
                </div>
            </section>

        <div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                    <div>
                        <h2 id="supplierModalTitle" class="text-xl font-bold text-black">Add supplier</h2>
                        <p id="supplierModalSubtitle" class="text-sm text-slate-800 font-medium">Create a supplier record and link products automatically.</p>
                    </div>
                    <button type="button" id="closeSupplierModal" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="supplierForm" method="POST" action="{{ route('supplier.assessment.store') }}" class="space-y-4 px-6 py-6">
                    @csrf
                    <input type="hidden" name="_method" id="supplierFormMethod" value="POST" />
                    <input type="hidden" name="supplier_id" id="supplierId" value="" />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Supplier name
                            <input id="supplierNameInput" name="name" type="text" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                        </label>
                        <label class="block text-sm font-medium text-slate-700">
                            Contact person
                            <input id="supplierContactInput" name="contact_person" type="text" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Email address
                            <input id="supplierEmailInput" name="email" type="email" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </label>
                        <label class="block text-sm font-medium text-slate-700">
                            Phone number
                            <input id="supplierPhoneInput" name="phone" type="text" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Contact position
                            <input id="supplierPositionInput" name="contact_position" type="text" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </label>
                        <label class="block text-sm font-medium text-slate-700">
                            Address
                            <input id="supplierAddressInput" name="address" type="text" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-1">
                        <label class="block text-sm font-medium text-slate-700">
                            Notes
                            <input id="supplierNotesInput" name="notes" type="text" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        </label>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button" id="cancelSupplierModal" class="rounded-[10px] border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all">Cancel</button>
                        <button type="submit" id="supplierModalSubmit" class="rounded-[10px] bg-[#00FFF2] px-5 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#00D9CC] transition-all">Save supplier</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="productsModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-4xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                    <div>
                        <h2 id="productsModalTitle" class="text-xl font-bold text-black">Supplier products</h2>
                        <p id="productsModalSubtitle" class="text-sm text-slate-800 font-medium">Review the products, pricing, and stock linked to this supplier.</p>
                    </div>
                    <button type="button" id="closeProductsModal" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-slate-700">
                            <thead class="bg-slate-50 text-xs font-semibold text-slate-700 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3">Unit price</th>
                                    <th class="px-4 py-3">Restock</th>
                                </tr>
                            </thead>
                            <tbody id="productsModalTableBody" class="divide-y divide-slate-200 bg-white"></tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-900">Prices and stock are pulled from current product records.</p>
                        <button type="button" id="closeProductsModalButton" class="rounded-[10px] border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <form id="archiveSupplierForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        @push('scripts')
            <script>
                const supplierSummaries = @json($supplierSummaries);
                const supplierModal = document.getElementById('supplierModal');
                const productsModal = document.getElementById('productsModal');
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
                const supplierSearch = document.getElementById('supplierSearch');
                const productsModalTitle = document.getElementById('productsModalTitle');
                const productsModalSubtitle = document.getElementById('productsModalSubtitle');
                const productsModalTableBody = document.getElementById('productsModalTableBody');
                const closeProductsModal = document.getElementById('closeProductsModal');
                const closeProductsModalButton = document.getElementById('closeProductsModalButton');
                const supplierList = document.getElementById('supplierList');
                const supplierListCount = document.getElementById('supplierListCount');
                const supplierPagination = document.getElementById('supplierPagination');
                const supplierDetailPanel = document.getElementById('supplierDetailPanel');
                const supplierDetailPlaceholder = document.getElementById('supplierDetailPlaceholder');
                const supplierDetailsContainer = document.getElementById('supplierDetailsContainer');
                const detailSupplierName = document.getElementById('detailSupplierName');
                const detailSupplierNotes = document.getElementById('detailSupplierNotes');
                const detailSupplierPosition = document.getElementById('detailSupplierPosition');
                const detailSupplierAddress = document.getElementById('detailSupplierAddress');
                const detailSupplierContact = document.getElementById('detailSupplierContact');
                const detailPerformanceScore = document.getElementById('detailPerformanceScore');
                const detailOnTimeRate = document.getElementById('detailOnTimeRate');
                const detailCompletionRate = document.getElementById('detailCompletionRate');
                const detailProductCount = document.getElementById('detailProductCount');
                const detailDeliveredCount = document.getElementById('detailDeliveredCount');
                const detailOrdersCount = document.getElementById('detailOrdersCount');
                const detailOrderHistory = document.getElementById('detailOrderHistory');
                const detailOnTimeBar = document.getElementById('detailOnTimeBar');
                const detailCompletionBar = document.getElementById('detailCompletionBar');
                const detailOnTimeText = document.getElementById('detailOnTimeText');
                const detailCompletionText = document.getElementById('detailCompletionText');
                const detailProductTable = document.getElementById('detailProductTable');
                const detailTotalValue = document.getElementById('detailTotalValue');
                const productPagination = document.getElementById('productPagination');
                const detailEditSupplierButton = document.getElementById('detailEditSupplierButton');
                const detailArchiveSupplierButton = document.getElementById('detailArchiveSupplierButton');
                const archiveSupplierForm = document.getElementById('archiveSupplierForm');
                let activeSupplier = null;
                let currentPage = 1;
                const itemsPerPage = 6;
                let currentProductPage = 1;
                const productsPerPage = 10;

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
                    toast.className = `fixed top-4 right-4 z-50 rounded-[10px] border p-4 text-sm font-medium shadow-lg transition-opacity duration-500 ${type === 'success' ? 'border-[#00fff2] bg-[#e6fffe] text-slate-900' : 'border-rose-200 bg-rose-50 text-rose-800'}`;
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
                    supplierModalTitle.textContent = 'Add supplier';
                    supplierModalSubtitle.textContent = 'Create a supplier record and link products automatically.';
                    supplierModalSubmit.textContent = 'Save supplier';
                }

                function fillSupplierForm(supplier) {
                    supplierModalTitle.textContent = supplier.id ? 'Edit supplier' : 'Add supplier';
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
                    supplierModalSubmit.textContent = supplier.id ? 'Update supplier' : 'Save supplier';
                }

                function renderSupplierList() {
                    supplierList.innerHTML = '';
                    let visibleCount = 0;
                    const filteredSuppliers = [];

                    supplierSummaries.forEach(supplier => {
                        const name = supplier.name.toLowerCase();
                        const contact = (supplier.contact_person || '').toLowerCase();
                        const contactPosition = (supplier.contact_position || '').toLowerCase();
                        const email = (supplier.email || supplier.phone || supplier.address || '').toLowerCase();
                        const searchValue = supplierSearch.value.trim().toLowerCase();

                        const matchesSearch = [name, contact, contactPosition, email].some(value => value.includes(searchValue));
                        if (matchesSearch) {
                            filteredSuppliers.push(supplier);
                        }
                    });

                    const totalPages = Math.ceil(filteredSuppliers.length / itemsPerPage);
                    currentPage = Math.min(currentPage, totalPages) || 1;
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    const paginatedSuppliers = filteredSuppliers.slice(startIndex, endIndex);

                    paginatedSuppliers.forEach(supplier => {
                        visibleCount += 1;
                        const card = document.createElement('div');
                        card.dataset.supplierName = supplier.name;
                        card.className = 'supplier-card w-full rounded-[10px] border border-slate-200 bg-white p-4 text-left shadow-sm transition-all duration-200 hover:border-slate-300 hover:shadow-md cursor-pointer';
                        card.innerHTML = `
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">${supplier.name}</p>
                                    <p class="mt-0.5 text-xs text-slate-500 truncate">${supplier.contact_person || 'No contact'} · ${supplier.email || supplier.phone || 'No email'}</p>
                                </div>
                                <span class="rounded-[8px] bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600 whitespace-nowrap border border-slate-200">${supplier.contact_position || 'Supplier'}</span>
                            </div>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                <div class="rounded-[10px] border border-slate-100 p-2 text-xs text-slate-600" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                                    <p class="font-bold text-slate-900 text-xs">${supplier.product_count}</p>
                                    <p class="text-[11px]">Products</p>
                                </div>
                                <div class="rounded-[10px] border border-slate-100 p-2 text-xs text-slate-600" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                                    <p class="font-bold text-slate-900 text-xs">${supplier.performance_score}</p>
                                    <p class="text-[11px]">Performance</p>
                                </div>
                            </div>
                        `;
                        supplierList.appendChild(card);
                    });

                    supplierListCount.textContent = `${visibleCount} shown`; // class already cyan via HTML, no re-render needed

                    // Render pagination
                    if (totalPages > 1) {
                        supplierPagination.innerHTML = `
                            <div class="text-xs text-slate-500">
                                Page ${currentPage} of ${totalPages}
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap justify-center">
                                <button data-page="${currentPage - 1}" class="pagination-btn px-2.5 py-1 text-xs rounded-[8px] border border-slate-200 transition-all ${currentPage === 1 ? 'text-slate-400 bg-slate-50 cursor-not-allowed' : 'text-slate-700 bg-white hover:bg-slate-100'}" ${currentPage === 1 ? 'disabled' : ''}>Prev</button>
                                ${Array.from({length: totalPages}, (_, i) => i + 1).map(page => `
                                    <button data-page="${page}" class="pagination-btn px-2.5 py-1 text-xs rounded-[8px] transition-all ${page === currentPage ? 'font-bold text-slate-900 bg-[#00FFF2] border border-slate-200 shadow-sm' : 'text-slate-700 border border-slate-200 bg-white hover:bg-slate-100'}">${page}</button>
                                `).join('')}
                                <button data-page="${currentPage + 1}" class="pagination-btn px-2.5 py-1 text-xs rounded-[8px] border border-slate-200 transition-all ${currentPage === totalPages ? 'text-slate-400 bg-slate-50 cursor-not-allowed' : 'text-slate-700 bg-white hover:bg-slate-100'}" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
                            </div>
                        `;
                    } else {
                        supplierPagination.innerHTML = '';
                    }
                }

                window.changePage = function(page) {
                    currentPage = page;
                    renderSupplierList();
                }

                window.changeProductPage = function(page) {
                    currentProductPage = page;
                    if (activeSupplier) {
                        setSupplierDetail(activeSupplier.name);
                    }
                }

                supplierList.addEventListener('click', event => {
                    const card = event.target.closest('.supplier-card');
                    if (card) {
                        setSupplierDetail(card.dataset.supplierName, true);
                    }
                });

                detailEditSupplierButton.addEventListener('click', () => {
                    if (!activeSupplier) return;
                    fillSupplierForm(activeSupplier);
                    openModal(supplierModal);
                });

                detailArchiveSupplierButton.addEventListener('click', () => {
                    if (!activeSupplier) return;
                    if (!activeSupplier.id) {
                        showToast('This supplier is not yet saved as a record and cannot be archived. Please add it first.', 'error');
                        return;
                    }
                    archiveSupplierForm.action = '{{ url('supplier-assessment/suppliers') }}/' + activeSupplier.id;
                    if (confirm(`Archive "${activeSupplier.name}"? This will remove it from active supplier listings.`)) {
                        archiveSupplierForm.submit();
                    }
                });

                function setSupplierDetail(supplierName, shouldScroll = false) {
                    const supplier = supplierSummaries.find(item => item.name === supplierName);
                    if (!supplier) return;

                    document.querySelectorAll('.supplier-card').forEach(card => {
                        if (card.dataset.supplierName === supplierName) {
                            card.classList.add('border-slate-400', 'bg-slate-50/70', 'shadow-md');
                            card.classList.remove('border-slate-200', 'bg-white');
                        } else {
                            card.classList.remove('border-slate-400', 'bg-slate-50/70', 'shadow-md');
                            card.classList.add('border-slate-200', 'bg-white');
                        }
                    });

                    supplierDetailPlaceholder.classList.add('hidden');
                    supplierDetailPanel.classList.remove('hidden');

                    if (activeSupplier && activeSupplier.name !== supplierName) {
                        currentProductPage = 1;
                    }

                    detailSupplierName.textContent = supplier.name;
                    detailSupplierNotes.textContent = supplier.notes || 'No additional notes provided.';
                    detailSupplierPosition.textContent = supplier.contact_position || 'Supplier';

                    // Location pin icon replaces the emoji, rendered via innerHTML
                    detailSupplierAddress.innerHTML = supplier.address
                        ? `<span class="inline-flex items-center gap-1.5">
                             <svg class="h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                             </svg>
                             <span>${supplier.address}</span>
                           </span>`
                        : '';

                    detailSupplierContact.textContent = supplier.contact_person ? `${supplier.contact_person} · ${supplier.email || supplier.phone || 'No contact info'}` : (supplier.email || supplier.phone || 'No contact info');
                    activeSupplier = supplier;

                    detailPerformanceScore.textContent = `${supplier.performance_score}/100`;
                    detailOnTimeRate.textContent = `${supplier.on_time_rate}%`;
                    detailCompletionRate.textContent = `${supplier.completion_rate}%`;
                    detailProductCount.textContent = supplier.product_count;
                    detailDeliveredCount.textContent = `${supplier.delivered_orders_count} delivered`;
                    detailOrdersCount.textContent = `${supplier.orders_count} orders`;
                    detailOnTimeBar.style.width = `${supplier.on_time_rate}%`;
                    detailCompletionBar.style.width = `${supplier.completion_rate}%`;
                    if (detailOnTimeText) detailOnTimeText.textContent = `${supplier.on_time_rate}%`;
                    if (detailCompletionText) detailCompletionText.textContent = `${supplier.completion_rate}%`;
                    detailTotalValue.textContent = `₱${Number(supplier.total_value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                    detailOrderHistory.innerHTML = '';
                    if (!supplier.orders.length) {
                        detailOrderHistory.innerHTML = '<div class="rounded-[12px] border border-slate-200/80 bg-slate-50/70 p-3.5 text-xs text-slate-500">No order history available for this supplier.</div>';
                    } else {
                        supplier.orders.slice(0, 3).forEach(order => {
                            detailOrderHistory.insertAdjacentHTML('beforeend', `
                                <div class="rounded-[12px] border border-slate-200 bg-white p-3.5 shadow-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-bold text-slate-900 text-xs">${order.order_number}</p>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-[6px] ${order.status.toLowerCase() === 'delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">${order.status}</span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-slate-500">
                                        Expected: ${order.expected_delivery_date || 'Unknown'} · Updated: ${order.updated_at || 'Unknown'}
                                    </div>
                                    <div class="mt-1.5 text-xs font-bold text-slate-900">₱${Number(order.total_amount).toFixed(2)}</div>
                                </div>
                            `);
                        });
                    }

                    detailProductTable.innerHTML = '';
                    if (!supplier.products.length) {
                        detailProductTable.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No product records linked to this supplier.</td></tr>';
                        productPagination.innerHTML = '';
                    } else {
                        const totalProductPages = Math.ceil(supplier.products.length / productsPerPage);
                        const productStartIndex = (currentProductPage - 1) * productsPerPage;
                        const productEndIndex = productStartIndex + productsPerPage;
                        const paginatedProducts = supplier.products.slice(productStartIndex, productEndIndex);

                        paginatedProducts.forEach(product => {
                            detailProductTable.insertAdjacentHTML('beforeend', `
                                <tr class="border-b border-slate-200 hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-semibold text-slate-900">${product.name}</td>
                                    <td class="px-4 py-3 text-slate-600">${product.sku}</td>
                                    <td class="px-4 py-3 text-slate-600">${product.category || 'Uncategorized'}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">${product.stock_quantity}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">₱${Number(product.price).toFixed(2)}</td>
                                    <td class="px-4 py-3 text-slate-600">${product.last_restock_date || 'N/A'}</td>
                                </tr>
                            `);
                        });

                        // Render product pagination
                        if (totalProductPages > 1) {
                            productPagination.innerHTML = `
                                <div class="text-xs text-slate-500">
                                    Page ${currentProductPage} of ${totalProductPages}
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap justify-center">
                                    <button type="button" onclick="window.changeProductPage(${currentProductPage - 1})" ${currentProductPage === 1 ? 'disabled' : ''} class="px-2.5 py-1 text-xs rounded-[8px] border border-slate-200 transition-all ${currentProductPage === 1 ? 'text-slate-400 bg-slate-50 cursor-not-allowed' : 'text-slate-700 bg-white hover:bg-slate-100'}">Prev</button>
                                    ${Array.from({length: totalProductPages}, (_, i) => i + 1).map(page => `
                                        <button type="button" onclick="window.changeProductPage(${page})" class="px-2.5 py-1 text-xs rounded-[8px] transition-all ${page === currentProductPage ? 'font-bold text-slate-900 bg-[#00FFF2] border border-slate-200 shadow-sm' : 'text-slate-700 border border-slate-200 bg-white hover:bg-slate-100'}">${page}</button>
                                    `).join('')}
                                    <button type="button" onclick="window.changeProductPage(${currentProductPage + 1})" ${currentProductPage === totalProductPages ? 'disabled' : ''} class="px-2.5 py-1 text-xs rounded-[8px] border border-slate-200 transition-all ${currentProductPage === totalProductPages ? 'disabled' : ''} ${currentProductPage === totalProductPages ? 'text-slate-400 bg-slate-50 cursor-not-allowed' : 'text-slate-700 bg-white hover:bg-slate-100'}">Next</button>
                                </div>
                            `;
                        } else {
                            productPagination.innerHTML = '';
                        }
                    }

                    if (shouldScroll) {
                        supplierDetailsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }

                function openProductsModal(supplierName) {
                    const supplier = supplierSummaries.find(item => item.name === supplierName);
                    if (!supplier) return;

                    productsModalTitle.textContent = `Products from ${supplier.name}`;
                    productsModalSubtitle.textContent = `${supplier.product_count} product${supplier.product_count === 1 ? '' : 's'} linked to this supplier.`;
                    productsModalTableBody.innerHTML = '';

                    if (!supplier.products.length) {
                        productsModalTableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No product records are currently linked to this supplier.</td></tr>';
                    } else {
                        supplier.products.forEach(product => {
                            productsModalTableBody.insertAdjacentHTML('beforeend', `
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-3 font-semibold text-slate-900">${product.name}</td>
                                    <td class="px-4 py-3 text-slate-600">${product.sku}</td>
                                    <td class="px-4 py-3 text-slate-600">${product.category}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">${product.stock_quantity}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">₱${Number(product.price).toFixed(2)}</td>
                                    <td class="px-4 py-3 text-slate-600">${product.last_restock_date || 'N/A'}</td>
                                </tr>
                            `);
                        });
                    }

                    openModal(productsModal);
                }

                openSupplierModalButton.addEventListener('click', () => {
                    resetSupplierForm();
                    openModal(supplierModal);
                });

                [closeSupplierModalButton, cancelSupplierModalButton].forEach(button => {
                    button.addEventListener('click', () => closeModal(supplierModal));
                });

                supplierSearch.addEventListener('input', renderSupplierList);

                // Event delegation for supplier pagination
                supplierPagination.addEventListener('click', (e) => {
                    const button = e.target.closest('.pagination-btn');
                    if (button) {
                        e.preventDefault();
                        const page = parseInt(button.dataset.page);
                        if (page >= 1 && page <= Math.ceil(supplierSummaries.length / itemsPerPage)) {
                            currentPage = page;
                            renderSupplierList();
                        }
                    }
                });

                [closeProductsModal, closeProductsModalButton].forEach(button => {
                    button.addEventListener('click', () => closeModal(productsModal));
                });

                renderSupplierList();

                // Auto-select supplier if passed in URL
                const urlParams = new URLSearchParams(window.location.search);
                const selectedSupplier = urlParams.get('selected_supplier');
                if (selectedSupplier) {
                    setTimeout(() => {
                        setSupplierDetail(selectedSupplier);
                    }, 100);
                }
            </script>
        @endpush
    </div>
</x-layouts.app>