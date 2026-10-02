<x-layouts.app :title="__('POS Terminal')">
    <div class="space-y-4 max-w-[1520px] mx-auto px-3 sm:px-4 py-2">
        {{-- TOP HEADER BAR --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-1 pb-2">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Point of Sale</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Terminal
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Fast checkout, live stock integration, wireless barcode scanner sync & service billing.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- MOBILE SCANNER BUTTON (ONLY SCANNER BUTTON - DESKTOP QR REMOVED) --}}
                <button id="posOpenScannerButton" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-800 shadow-2xs hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                    <div class="w-5 h-5 rounded-lg bg-[#6EC1D1]/20 flex items-center justify-center text-[#105f68]">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <span>Mobile Scanner</span>
                </button>

                {{-- TRANSACTION HISTORY BUTTON --}}
                <button id="posOpenTransactionHistoryButton" type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-800 shadow-2xs hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                    <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Transaction History</span>
                </button>

                {{-- ARCHIVED ITEMS LINK --}}
                <a href="{{ route('pos.archived') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-700 shadow-2xs hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                    <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span>Archived Items</span>
                </a>
            </div>
        </div>

        {{-- MAIN POS WORKSPACE: LEFT (PRODUCTS) + RIGHT (ORDER CART & CHECKOUT) --}}
        <div class="grid grid-cols-1 xl:grid-cols-[1.65fr_1fr] gap-5 items-start">
            
            {{-- LEFT COLUMN: PRODUCT CATALOG & SEARCH --}}
            <div class="space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xs">
                    {{-- TOP DARK HEADER SEARCH BAR (MATCHING ALL STOCKS DESIGN) --}}
                    <div class="bg-[#0f172a] px-5 py-3.5 border-b border-slate-800 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        {{-- Search Input & Filter Button --}}
                        <div class="flex items-center gap-2.5 flex-1 max-w-[460px]">
                            <div class="relative flex-1">
                                <label for="posProductSearchInput" class="sr-only">Search products</label>
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 12.65z"/>
                                </svg>
                                <input id="posProductSearchInput" 
                                       type="text" 
                                       placeholder="Search by Product Name, SKU, or scan barcode..." 
                                       class="w-full rounded-xl border border-slate-700/80 bg-slate-800/90 text-white placeholder:text-slate-400 pl-10 pr-12 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-[#6EC1D1] focus:border-[#6EC1D1] transition shadow-inner" 
                                       style="height: 40px;" />
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-slate-500 bg-slate-700/60 px-1.5 py-0.5 rounded">Enter</span>
                            </div>
                        </div>

                        {{-- Dropdown Filters (Category & Brand) --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-medium text-slate-400 whitespace-nowrap">Filter:</span>
                            <div class="relative">
                                <select id="posCategorySelect" class="hidden">
                                    <option value="All">All Categories</option>
                                </select>
                            </div>
                            <div class="relative">
                                <select id="posBrandSelect" class="hidden">
                                    <option value="All">All Brands</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- PRODUCT CARDS GRID --}}
                    <div class="p-4 sm:p-5">
                        <div id="posProductGrid" class="grid gap-3.5 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-12 text-center text-slate-500">
                                <svg class="w-10 h-10 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <p class="text-sm font-semibold text-slate-700">Search for products or scan a barcode to add to cart</p>
                                <p class="text-xs text-slate-400 mt-1">Type in the search bar or connect your mobile scanner</p>
                            </div>
                        </div>

                        {{-- PAGINATION --}}
                        <div id="posProductPagination" class="hidden mt-5 flex items-center justify-center gap-2 border-t border-slate-100 pt-4"></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: CART, SERVICES & INSTANT CHECKOUT --}}
            <aside class="space-y-4 xl:sticky xl:top-3">
                {{-- CART SECTION --}}
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xs">
                    <div class="bg-[#0f172a] px-5 py-3.5 flex items-center justify-between gap-3 border-b border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-[#6EC1D1]/20 flex items-center justify-center text-[#6EC1D1]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-white tracking-tight">Order Cart</h2>
                            </div>
                        </div>
                        <button id="posEmptyCartButton" type="button" class="rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 px-2.5 py-1 text-[11px] font-bold transition cursor-pointer">
                            Clear Cart
                        </button>
                    </div>

                    <div class="p-4 space-y-4">
                        {{-- CART ITEMS TABLE --}}
                        <div class="overflow-x-auto rounded-xl border border-slate-100 bg-slate-50/50 max-h-[300px] overflow-y-auto">
                            <table id="posCartTable" class="min-w-full text-left text-xs">
                                <tbody id="posCartBody" class="divide-y divide-slate-200/70"></tbody>
                            </table>
                            <div id="posEmptyCartMessage" class="p-8 text-center text-slate-400 text-xs flex flex-col items-center justify-center gap-1.5">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Cart is currently empty.</span>
                            </div>
                        </div>

                        {{-- EXTRA CHARGES & DISCOUNT INPUTS --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3.5 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Extra Charges (₱)</label>
                                    <input id="posExtraChargeInput" 
                                           type="number" 
                                           min="0" 
                                           step="0.01" 
                                           value="0" 
                                           placeholder="0.00" 
                                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/40 shadow-2xs" />
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label for="posDiscountInput" class="text-[11px] font-bold text-slate-700 cursor-pointer">Discount (₱)</label>
                                        <button type="button" id="posRemoveDiscountBtn" class="text-[10px] font-bold text-rose-600 hover:text-rose-800 transition cursor-pointer hidden">Remove</button>
                                    </div>
                                    <input id="posDiscountInput" 
                                           type="number" 
                                           min="0" 
                                           step="0.01" 
                                           value="0" 
                                           placeholder="0.00" 
                                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/40 shadow-2xs" />
                                </div>
                            </div>
                        </div>

                        {{-- FINANCIAL SUMMARY --}}
                        <div id="posCartFooter" class="space-y-1.5 rounded-xl border border-slate-200 bg-slate-900 p-4 text-white">
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Items Subtotal</span>
                                <span id="posSubtotal" class="font-semibold text-white">₱0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Labor Services</span>
                                <span id="posServicesTotal" class="font-semibold text-white">₱0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Extra Charges</span>
                                <span id="posExtraCharge" class="font-semibold text-white">₱0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-amber-300">
                                <span>Discount Applied</span>
                                <span id="posDiscount" class="font-semibold text-amber-300">-₱0.00</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Included VAT (12%)</span>
                                <span id="posTax" class="font-semibold text-slate-300">₱0.00</span>
                            </div>
                            <div class="border-t border-slate-700 pt-2.5 mt-2 flex items-center justify-between">
                                <span class="text-sm font-bold text-white uppercase tracking-wider">Total Amount</span>
                                <span id="posTotal" class="text-xl font-extrabold text-[#6EC1D1]">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LABOR & SERVICES PANEL --}}
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xs">
                    <div class="bg-[#0f172a] px-5 py-3 flex items-center justify-between gap-3 border-b border-slate-800">
                        <h2 class="text-xs font-bold text-white tracking-tight">Labor & Installation Services</h2>
                        <span class="text-[10px] font-semibold text-slate-400">Add to billing</span>
                    </div>
                    <div class="p-3.5 grid gap-2 text-xs">
                        <label class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-slate-50/50 p-2.5 cursor-pointer hover:bg-slate-100/80 transition">
                            <div class="flex items-center gap-2.5">
                                <input type="checkbox" value="installation" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                                <span class="font-semibold text-slate-800">Parts Installation</span>
                            </div>
                            <span class="font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded-md text-[11px]">₱120.00</span>
                        </label>
                        <label class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-slate-50/50 p-2.5 cursor-pointer hover:bg-slate-100/80 transition">
                            <div class="flex items-center gap-2.5">
                                <input type="checkbox" value="tuneup" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                                <span class="font-semibold text-slate-800">Engine Tune-up</span>
                            </div>
                            <span class="font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded-md text-[11px]">₱250.00</span>
                        </label>
                        <label class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-slate-50/50 p-2.5 cursor-pointer hover:bg-slate-100/80 transition">
                            <div class="flex items-center gap-2.5">
                                <input type="checkbox" value="brake_adjust" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                                <span class="font-semibold text-slate-800">Brake Adjustment</span>
                            </div>
                            <span class="font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded-md text-[11px]">₱180.00</span>
                        </label>
                    </div>
                </div>

                {{-- PAYMENT METHOD & CHECKOUT ACTION --}}
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-xs">
                    <div class="bg-[#0f172a] px-5 py-3 flex items-center justify-between gap-3 border-b border-slate-800">
                        <h2 class="text-xs font-bold text-white tracking-tight">Payment Method</h2>
                        <span class="text-[10px] font-semibold text-slate-400">Select Mode</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white p-3 text-xs font-bold text-slate-800 cursor-pointer hover:border-slate-400 has-[:checked]:border-[#6EC1D1] has-[:checked]:bg-[#6EC1D1]/10 transition shadow-2xs">
                                <input type="radio" name="posPaymentMethod" value="cash" class="pos-payment-method h-4 w-4 rounded-full border-slate-300 text-[#105f68] focus:ring-[#105f68]" checked />
                                <span>Cash</span>
                            </label>
                            <label class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white p-3 text-xs font-bold text-slate-800 cursor-pointer hover:border-slate-400 has-[:checked]:border-[#6EC1D1] has-[:checked]:bg-[#6EC1D1]/10 transition shadow-2xs">
                                <input type="radio" name="posPaymentMethod" value="qr" class="pos-payment-method h-4 w-4 rounded-full border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                                <span>QR PH (GCash)</span>
                            </label>
                        </div>
                        <button id="posProceedPaymentButton" type="button" class="w-full rounded-xl bg-[#6EC1D1] py-3 text-xs font-extrabold text-slate-950 hover:bg-[#5bb0c0] transition-all shadow-xs cursor-pointer tracking-wider uppercase">
                            Proceed to Payment
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- MODAL: PROCESS PAYMENT --}}
    <div id="posPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="document.getElementById('posPaymentModal').classList.add('hidden')"></div>
        <div class="relative flex max-h-[calc(100dvh-2rem)] w-full max-w-4xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
            <div class="flex shrink-0 items-center justify-between border-b border-slate-800 bg-[#0f172a] px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#6EC1D1]/20 flex items-center justify-center text-[#6EC1D1]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Process Transaction Payment</h2>
                        <p class="text-xs text-slate-400">Review invoice order items and calculate change.</p>
                    </div>
                </div>
                <button id="posPaymentModalClose" type="button" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="min-h-0 flex-1 overflow-y-auto grid gap-6 lg:grid-cols-[1.2fr_0.8fr] px-6 py-6">
                {{-- Order Summary --}}
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs">
                        <div>
                            <p class="font-bold text-slate-400 uppercase tracking-wider">Invoice #</p>
                            <p id="posPaymentInvoice" class="font-mono font-bold text-slate-900 text-sm mt-0.5">INV-000000</p>
                        </div>
                        <div>
                            <p class="font-bold text-slate-400 uppercase tracking-wider">Date & Time</p>
                            <p id="posPaymentDate" class="font-semibold text-slate-800 text-xs mt-0.5">—</p>
                        </div>
                    </div>
                    
                    <div class="rounded-2xl border border-slate-200 overflow-hidden">
                        <div class="bg-slate-100 px-4 py-2.5 border-b border-slate-200 text-xs font-bold text-slate-700">
                            Purchased Items
                        </div>
                        <div class="max-h-[220px] overflow-y-auto">
                            <table class="min-w-full text-left text-xs text-slate-700">
                                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Item</th>
                                        <th class="px-3 py-2 text-left">SKU</th>
                                        <th class="px-3 py-2 text-center">Qty</th>
                                        <th class="px-3 py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="posPaymentItems" class="divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                        
                        <div class="p-4 bg-slate-50/80 border-t border-slate-200 space-y-1.5 text-xs text-slate-600">
                            <div class="flex items-center justify-between"><span>Subtotal</span><span id="posPaymentSubtotal" class="font-semibold text-slate-900">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Services</span><span id="posPaymentServices" class="font-semibold text-slate-900">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Extra Charges</span><span id="posPaymentExtra" class="font-semibold text-slate-900">₱0.00</span></div>
                            <div class="flex items-center justify-between text-amber-700"><span>Discount</span><span id="posPaymentDiscount" class="font-bold">-₱0.00</span></div>
                            <div class="flex items-center justify-between text-slate-500"><span>Included VAT (12%)</span><span id="posPaymentTax" class="font-semibold">₱0.00</span></div>
                            <div class="border-t border-slate-200 pt-2 mt-1 flex items-center justify-between text-base font-extrabold text-slate-900">
                                <span>Grand Total</span><span id="posPaymentTotal" class="text-emerald-700 font-extrabold">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cash & Change Calculation --}}
                <div class="space-y-4">
                    <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">Payment Method</h3>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 cursor-pointer text-xs font-bold transition has-[:checked]:border-[#6EC1D1] has-[:checked]:bg-[#6EC1D1]/10">
                                <input type="radio" name="posPaymentModalMethod" value="cash" class="h-4 w-4 text-[#105f68] focus:ring-[#105f68]" checked />
                                <span>Cash</span>
                            </label>
                            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 cursor-pointer text-xs font-bold transition has-[:checked]:border-[#6EC1D1] has-[:checked]:bg-[#6EC1D1]/10">
                                <input type="radio" name="posPaymentModalMethod" value="qr" class="h-4 w-4 text-[#105f68] focus:ring-[#105f68]" />
                                <span>QR PH</span>
                            </label>
                        </div>
                    </div>

                    <div id="posCashPaymentDetails" class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Cash Received & Change</h3>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Auto-calculate</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Amount Tendered <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₱</span>
                                <input type="number" 
                                       id="posAmountTenderedInput" 
                                       step="any" 
                                       min="0" 
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-base font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] transition shadow-2xs" />
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Quick Cash Presets:</span>
                            <div id="posQuickCashPills" class="flex flex-wrap gap-1.5">
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-mode="exact">Exact</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-amount="100">₱100</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-amount="200">₱200</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-amount="500">₱500</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-amount="1000">₱1,000</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-amount="1500">₱1,500</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-[#6EC1D1]/15 hover:border-[#6EC1D1] transition shadow-2xs" data-amount="2000">₱2,000</button>
                            </div>
                        </div>

                        <div id="posChangeDisplayBox" class="rounded-xl border border-slate-200 bg-white p-3.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-600">Change (Sukli)</span>
                                <span id="posChangeDisplayAmount" class="text-xl font-extrabold text-emerald-700">₱0.00</span>
                            </div>
                            <div id="posInsufficientWarning" class="hidden mt-2 text-[11px] font-bold text-rose-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span id="posInsufficientText">Amount tendered is less than total amount</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2.5 pt-2">
                        <button id="posPaymentModalCancel" type="button" class="flex-1 rounded-xl bg-slate-100 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-slate-200 transition cursor-pointer">Cancel</button>
                        <button id="posPaymentModalConfirm" type="button" class="flex-1 rounded-xl bg-[#6EC1D1] px-4 py-3 text-xs font-extrabold text-slate-950 hover:bg-[#5bb0c0] transition shadow-xs cursor-pointer uppercase tracking-wider">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: PAYMENT SUCCESS & RECEIPT OVERLAY --}}
    <div id="posReceiptOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="document.getElementById('posReceiptOverlay').classList.add('hidden')"></div>
        <div class="relative w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
            <div class="receipt-content">
                <div class="flex items-center justify-between border-b border-slate-800 bg-[#0f172a] px-6 py-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white">Payment Recorded Successfully!</h2>
                            <p class="text-xs text-slate-300">Invoice: <span id="receiptInvoice" class="font-mono font-bold text-[#6EC1D1]">INV-000000</span></p>
                            <p id="receiptDate" class="hidden"></p>
                        </div>
                    </div>
                    <button id="posCloseReceiptButton" type="button" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="grid gap-3 sm:grid-cols-3 mb-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Amount Paid</p>
                            <p id="receiptPaid" class="mt-1 text-lg font-bold text-slate-900">₱0.00</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Change (Sukli)</p>
                            <p id="receiptChange" class="mt-1 text-lg font-extrabold text-emerald-700">₱0.00</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payment Method</p>
                            <p id="receiptPaymentMethod" class="mt-1 text-lg font-bold text-slate-900">Cash</p>
                        </div>
                    </div>

                    <div id="receiptInvoiceDetails" class="hidden">
                        <div class="mb-4 pb-3 border-b-2 border-slate-900">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center font-black text-xs">
                                        KCC
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-xs text-slate-900">MOTORCYCLE PARTS & ACCESSORIES</h2>
                                        <p class="text-[10px] text-slate-600 mt-0.5">Barangay Zone 1, Digos City, Davao del Sur</p>
                                    </div>
                                </div>
                                <div class="text-right text-xs">
                                    <p><span class="font-semibold">Invoice #:</span> <span id="invoiceNumHeader" class="font-mono font-bold">INV-000000</span></p>
                                    <p class="mt-0.5"><span class="font-semibold">Date:</span> <span id="invoiceDateHeader">Apr 30, 2026</span></p>
                                    <p class="mt-0.5"><span class="font-semibold">Cashier:</span> <span id="receiptCashierName" class="font-bold">{{ auth()->user()?->name ?? 'Cashier' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b-2 border-slate-900 text-slate-900 text-[10px] font-bold uppercase tracking-wide">
                                        <th class="text-left py-1">Item</th>
                                        <th class="text-left py-1">SKU</th>
                                        <th class="text-center py-1">Qty</th>
                                        <th class="text-right py-1">Price</th>
                                        <th class="text-right py-1">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceItemsTable" class="text-slate-700"></tbody>
                            </table>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-200 text-xs">
                            <div class="space-y-1">
                                <div class="flex justify-between"><span>Subtotal</span><span id="invoiceSubtotal" class="font-semibold">₱0.00</span></div>
                                <div class="flex justify-between"><span>Discount</span><span id="invoiceDiscount" class="font-semibold">₱0.00</span></div>
                                <div class="flex justify-between text-slate-500"><span>Included VAT (12%)</span><span id="invoiceTax" class="font-semibold">₱0.00</span></div>
                                <div class="flex justify-between text-sm font-bold border-t-2 border-slate-900 pt-1 mt-1">
                                    <span>TOTAL</span><span id="invoiceTotalAmount" class="text-[#105f68]">₱0.00</span>
                                </div>
                            </div>
                            <div class="space-y-1 text-right">
                                <div><span class="text-[10px] text-slate-500">Amount Tendered</span><p id="invoiceAmountReceived" class="font-bold text-slate-900">₱0.00</p></div>
                                <div><span class="text-[10px] text-slate-500">Change</span><p id="invoiceChange" class="font-bold text-emerald-700">₱0.00</p></div>
                                <div><span class="text-[10px] text-slate-500">Mode</span><p id="invoicePaymentMethod" class="font-bold">Cash</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-2.5 sm:flex-row sm:items-center">
                        <button id="posCloseReceiptDoneButton" type="button" class="flex-1 rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-800 hover:bg-slate-200 transition cursor-pointer">Done / New Sale</button>
                        <button id="posPrintReceiptButton" type="button" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition cursor-pointer hidden">Print Receipt</button>
                        <button id="posViewInvoiceButton" type="button" class="flex-1 rounded-xl bg-[#6EC1D1] px-4 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-[#5bb0c0] transition cursor-pointer">View Printable Invoice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: QR PH PAYMENT SCAN --}}
    <div id="posQRPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="document.getElementById('posQRPaymentModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-800 bg-[#0f172a] px-6 py-4">
                <div>
                    <h2 class="text-base font-bold text-white">QR PH Customer Payment</h2>
                    <p class="text-xs text-slate-300">Scan via GCash, Maya, or any banking app</p>
                </div>
                <button id="posCloseQRPaymentButton" type="button" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex flex-col items-center px-6 py-8">
                <div class="mb-4 text-center">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Amount Due</p>
                    <p id="posQRPaymentAmount" class="text-3xl font-extrabold text-slate-900 mt-1">₱0.00</p>
                </div>
                <div id="posQRCodeContainer" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div id="posQRCode" class="flex items-center justify-center" style="width: 220px; height: 220px;"></div>
                </div>
                <p class="text-xs text-slate-500 text-center mb-6 max-w-xs">Have the customer scan the QR PH code above with their e-wallet or mobile banking app.</p>
                <button id="posQRPaymentCompleteButton" type="button" class="w-full rounded-xl bg-[#6EC1D1] px-4 py-3 text-xs font-extrabold text-slate-950 hover:bg-[#5bb0c0] transition shadow-xs cursor-pointer uppercase tracking-wider">Payment Received & Confirmed</button>
            </div>
        </div>
    </div>

    {{-- MODAL: INVOICE DETAILS VIEW --}}
    <div id="posInvoiceModal" class="hidden fixed inset-0 z-[100000002] flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="document.getElementById('posInvoiceModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-3xl max-h-[90vh] overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200 flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-800 bg-[#0f172a] px-6 py-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#6EC1D1]/20 text-[#6EC1D1] flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Invoice Details</h2>
                        <p class="text-xs text-slate-300">Invoice #: <span id="posInvoiceNumber" class="font-mono font-bold text-[#6EC1D1]">INV-000000</span></p>
                    </div>
                </div>
                <button id="posCloseInvoiceModalButton" type="button" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-6 overflow-y-auto flex-1 space-y-4">
                <div class="pb-3 border-b-2 border-slate-900">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex gap-2">
                            <div class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center font-black text-xs">
                                KCC
                            </div>
                            <div>
                                <h2 class="font-bold text-xs text-slate-900">MOTORCYCLE PARTS & ACCESSORIES</h2>
                                <p class="text-[10px] text-slate-600 mt-0.5">Barangay Zone 1, Digos City, Davao del Sur</p>
                            </div>
                        </div>
                        <div class="text-right text-xs">
                            <p><span class="font-semibold">Invoice #:</span> <span id="posInvoiceNumHeader" class="font-mono font-bold">INV-000000</span></p>
                            <p class="mt-0.5"><span class="font-semibold">Date:</span> <span id="posInvoiceDateHeader">Apr 30, 2026</span></p>
                            <p class="mt-0.5"><span class="font-semibold">Cashier:</span> <span id="posInvoiceCashierName" class="font-bold">{{ auth()->user()?->name ?? 'Cashier' }}</span></p>
                        </div>
                    </div>
                </div>

                <div>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b-2 border-slate-900 text-slate-900 text-[10px] font-bold uppercase tracking-wide">
                                <th class="text-left py-1">Item</th>
                                <th class="text-left py-1">SKU</th>
                                <th class="text-center py-1">Qty</th>
                                <th class="text-right py-1">Price</th>
                                <th class="text-right py-1">Total</th>
                            </tr>
                        </thead>
                        <tbody id="posInvoiceItemsTable" class="text-slate-700"></tbody>
                    </table>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-200 text-xs">
                    <div class="space-y-1">
                        <div class="flex justify-between"><span>Subtotal</span><span id="posInvoiceSubtotal" class="font-semibold">₱0.00</span></div>
                        <div class="flex justify-between"><span>Services</span><span id="posInvoiceServices" class="font-semibold">₱0.00</span></div>
                        <div class="flex justify-between"><span>Extra</span><span id="posInvoiceExtra" class="font-semibold">₱0.00</span></div>
                        <div class="flex justify-between text-amber-700"><span>Discount</span><span id="posInvoiceDiscount" class="font-bold">₱0.00</span></div>
                        <div class="flex justify-between text-slate-500"><span>Included VAT (12%)</span><span id="posInvoiceTax" class="font-semibold">₱0.00</span></div>
                        <div class="flex justify-between text-sm font-bold border-t-2 border-slate-900 pt-1 mt-1">
                            <span>TOTAL</span><span id="posInvoiceTotalAmount" class="text-[#105f68]">₱0.00</span>
                        </div>
                    </div>
                    <div class="space-y-1 text-right">
                        <div><span class="text-[10px] text-slate-500">Amount Tendered</span><p id="posInvoiceAmountReceived" class="font-bold text-slate-900">₱0.00</p></div>
                        <div><span class="text-[10px] text-slate-500">Change</span><p id="posInvoiceChange" class="font-bold text-emerald-700">₱0.00</p></div>
                        <div><span class="text-[10px] text-slate-500">Mode</span><p id="posInvoicePaymentMethod" class="font-bold">Cash</p></div>
                    </div>
                </div>

                <div class="pt-4 flex gap-2.5">
                    <button id="posCloseInvoiceDoneButton" type="button" class="flex-1 rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-800 hover:bg-slate-200 transition cursor-pointer">Close</button>
                    <button id="posPrintInvoiceButton" type="button" class="flex-1 rounded-xl bg-[#6EC1D1] px-4 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-[#5bb0c0] transition cursor-pointer">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: TRANSACTION HISTORY --}}
    <div id="posTransactionHistoryModal" class="hidden fixed inset-0 z-[100000001] flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs" onclick="document.getElementById('posTransactionHistoryModal').classList.add('hidden')"></div>
        <div class="relative flex w-full max-w-5xl max-h-[90vh] flex-col overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-800 bg-[#0f172a] px-6 py-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#6EC1D1]/20 text-[#6EC1D1] flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">POS Transaction Sales History</h2>
                        <p class="text-xs text-slate-300">Filter completed POS sales transactions by date range.</p>
                    </div>
                </div>
                <button id="posCloseTransactionHistoryButton" type="button" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-6 py-5 overflow-y-auto">
                <div class="grid gap-3 sm:grid-cols-[1.2fr_1fr_1fr] items-end bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">From Date</label>
                        <input id="posHistoryFilterFrom" type="date" class="mt-1.5 h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/40" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700">To Date</label>
                        <input id="posHistoryFilterTo" type="date" class="mt-1.5 h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/40" />
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="posHistoryFilterApplyButton" type="button" class="flex-1 h-10 rounded-xl bg-[#6EC1D1] px-4 text-xs font-bold text-slate-950 hover:bg-[#5bb0c0] transition cursor-pointer">Apply Filter</button>
                        <button id="posHistoryFilterClearButton" type="button" class="h-10 rounded-xl border border-slate-300 bg-white px-3.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition cursor-pointer">Clear</button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 max-h-[42vh] overflow-y-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="bg-[#0f172a] text-white text-[11px] font-bold uppercase tracking-wider sticky top-0 z-10">
                            <tr>
                                <th class="px-3.5 py-3 text-center w-10">
                                    <input type="checkbox" id="posSelectAllTransactions" class="rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer" />
                                </th>
                                <th class="px-3.5 py-3 text-white">Invoice #</th>
                                <th class="px-3.5 py-3 text-white">SKU</th>
                                <th class="px-3.5 py-3 text-white">Date</th>
                                <th class="px-3.5 py-3 text-white">Mode</th>
                                <th class="px-3.5 py-3 text-center text-white">Items</th>
                                <th class="px-4 py-3 text-right text-white min-w-[110px]">Total Amount</th>
                                <th class="pl-8 pr-4 py-3 text-center text-white min-w-[130px]">Action</th>
                            </tr>
                        </thead>
                        <tbody id="posTransactionHistoryBody" class="divide-y divide-slate-200"></tbody>
                    </table>
                </div>

                <div id="posTransactionHistoryBulkActions" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 flex items-center justify-between">
                    <div class="text-xs text-red-900 font-semibold">
                        <span class="font-bold" id="posSelectedCount">0</span> transaction(s) selected
                    </div>
                    <button id="posBulkDeleteTransactions" type="button" class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition cursor-pointer">Delete Selected</button>
                </div>

                <div id="posTransactionHistoryPagination" class="hidden flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                    <div id="posTransactionHistoryInfo" class="font-semibold text-slate-600">Showing 0 of 0</div>
                    <div class="flex items-center gap-1.5">
                        <button id="posHistoryPrevPage" type="button" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer">← Prev</button>
                        <div id="posHistoryPageNumbers" class="flex items-center gap-1"></div>
                        <button id="posHistoryNextPage" type="button" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer">Next →</button>
                    </div>
                </div>

                <div id="posTransactionHistoryEmpty" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500 text-xs hidden">
                    No transactions match the selected filter.
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: MOBILE SCANNER (WIRELESS PHONE SCANNER INTEGRATION & CAMERA) --}}
    <div id="posMobileScannerModal" class="fixed inset-0 hidden items-center justify-center z-[9999] px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/75 backdrop-blur-xs" onclick="document.getElementById('posMobileScannerModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-lg bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-700">
            <div class="bg-slate-950 px-5 py-4 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#6EC1D1]/20 text-[#6EC1D1] flex items-center justify-center">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white">Mobile & Wireless POS Scanner</h2>
                        <p class="text-[11px] text-slate-400">Scan QR / Barcodes with your mobile phone or camera</p>
                    </div>
                </div>
                <button id="posCloseMobileScannerButton" type="button" class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                {{-- PHONE WIRELESS SCANNER PAIRING BOX --}}
                <div class="bg-slate-800/90 rounded-2xl p-4 border border-slate-700/80 flex items-center gap-4">
                    <div class="bg-white p-2 rounded-xl flex-shrink-0 shadow-sm" id="posMobilePairingQRCode">
                        {{-- QR Code generated to /pos/mobile-scanner --}}
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center gap-1.5 text-emerald-400 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Wireless Phone Scanner</span>
                        </div>
                        <p class="text-slate-300 leading-relaxed">Scan this QR code with any smartphone camera to open the Wireless Scanner tool.</p>
                        <p class="text-[10px] text-slate-400">Items scanned on your phone will automatically appear in this POS cart instantly.</p>
                    </div>
                </div>

                {{-- CAMERA VIEWFINDER (IF USING LOCAL WEBCAM/TABLET) --}}
                <div class="rounded-2xl border border-slate-700 overflow-hidden bg-black relative">
                    <div id="posMobileScannerReader" class="w-full bg-black min-h-[180px]"></div>
                    <div id="posMobileScannerStatus" class="p-2.5 text-center text-xs font-semibold text-slate-400 bg-slate-900/90 border-t border-slate-800">
                        Position QR code or barcode in front of camera
                    </div>
                </div>

                {{-- RECENT SCANS LIST --}}
                <div class="bg-slate-800/80 rounded-2xl p-3.5 border border-slate-700/70">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-slate-200">Recent Scanned Items</h3>
                        <span class="text-[10px] font-semibold text-emerald-400">Live Synced</span>
                    </div>
                    <div id="posMobileScannerRecent" class="space-y-1.5 max-h-32 overflow-y-auto">
                        <div class="text-center text-xs text-slate-500 py-2">No items scanned yet</div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-950 px-5 py-3 border-t border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-400">Scanner Status:</span>
                <span id="posMobileScannerConnection" class="text-emerald-400 font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Ready & Listening
                </span>
            </div>
        </div>
    </div>

    {{-- SCRIPTS & INITIALIZATION --}}
    <script>
        window.POS = {
            routes: {
                apiProducts: '{{ route("api.products") }}',
                mobileScanner: '{{ route("pos.mobile-scanner") }}'
            },
            cashier: '{{ auth()->user()?->name ?? "Cashier" }}'
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR Code for Phone Scanner in Mobile Scanner Modal
            const mobilePairingContainer = document.getElementById('posMobilePairingQRCode');
            if (mobilePairingContainer && typeof QRCode !== 'undefined') {
                const scannerUrl = window.location.origin + '{{ route("pos.mobile-scanner") }}';
                new QRCode(mobilePairingContainer, {
                    text: scannerUrl,
                    width: 72,
                    height: 72,
                    colorDark: '#0f172a',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            // Custom Select Dropdown logic
            function setupCustomSelectDropdown(selectId, placeholder) {
                const select = document.getElementById(selectId);
                if (!select || select.dataset.customized === 'true') return;
                select.dataset.customized = 'true';

                const wrapper = select.parentElement;
                wrapper.classList.add('relative', 'inline-block');

                const button = document.createElement('button');
                button.type = 'button';
                button.id = selectId + 'Button';
                button.className = 'custom-select-button inline-flex min-w-[130px] items-center justify-between gap-2 rounded-xl border border-slate-700/80 bg-slate-800/90 px-3 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-slate-700 focus:outline-none focus:ring-1 focus:ring-[#6EC1D1]';
                button.innerHTML = `
                    <span class="custom-select-label truncate"></span>
                    <svg class="h-3.5 w-3.5 flex-shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                `;
                wrapper.insertBefore(button, select);

                const panel = document.createElement('div');
                panel.className = 'custom-select-panel hidden absolute left-0 top-full z-50 mt-1.5 rounded-xl border border-slate-700/80 bg-[#0f172a] p-1.5 shadow-2xl text-white max-h-60 overflow-y-auto space-y-0.5 min-w-[150px]';
                wrapper.appendChild(panel);

                const labelSpan = button.querySelector('.custom-select-label');
                const chevron = button.querySelector('svg');

                function renderOptions() {
                    panel.innerHTML = '';
                    Array.from(select.options).forEach((opt) => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.dataset.value = opt.value;
                        const isSelected = opt.value === select.value;
                        item.className = 'custom-select-item w-full rounded-lg px-3 py-2 text-left text-xs transition-colors duration-100 whitespace-nowrap ' +
                            (isSelected
                                ? 'bg-slate-800 font-bold text-[#6EC1D1]'
                                : 'text-slate-200 hover:text-white hover:bg-slate-800/70');
                        item.textContent = opt.textContent;
                        item.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (select.value !== opt.value) {
                                select.value = opt.value;
                                select.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            updateButtonLabel();
                            closePanel();
                        });
                        panel.appendChild(item);
                    });
                }

                function updateButtonLabel() {
                    const selectedOption = select.options[select.selectedIndex];
                    let text = selectedOption ? selectedOption.textContent.trim() : (placeholder || '');
                    if (text === 'All') {
                        text = placeholder || 'All';
                    }
                    labelSpan.textContent = text;
                }

                function openPanel() {
                    document.querySelectorAll('.custom-select-panel').forEach((p) => {
                        if (p !== panel) p.classList.add('hidden');
                    });
                    panel.style.minWidth = Math.max(button.offsetWidth, 150) + 'px';
                    renderOptions();
                    panel.classList.remove('hidden');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }

                function closePanel() {
                    panel.classList.add('hidden');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }

                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (panel.classList.contains('hidden')) {
                        openPanel();
                    } else {
                        closePanel();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) closePanel();
                });

                const observer = new MutationObserver(() => {
                    updateButtonLabel();
                    if (!panel.classList.contains('hidden')) renderOptions();
                });
                observer.observe(select, { childList: true, subtree: true, attributes: true });

                updateButtonLabel();
            }

            setupCustomSelectDropdown('posCategorySelect', 'All Categories');
            setupCustomSelectDropdown('posBrandSelect', 'All Brands');
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @vite(['resources/js/pos_terminal.js'])
</x-layouts.app>