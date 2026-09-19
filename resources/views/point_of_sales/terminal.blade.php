<x-layouts.app :title="__('POS Terminal')">
    <div class="space-y-3 max-w-[1480px] mx-auto px-3">
         <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-1">
                <h1 class="text-4xl font-bold text-slate-900">Point of Sale (POS)</h1>
                <p class="text-gray-600 text-base mt-1">Process sales, service billing, and payments from one compact page.</p>
            </div>
            <div class="flex flex-wrap gap-2">
         <button id="posOpenDesktopScannerButton" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 font-semibold shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Scan QR
                </button>
                <button id="posOpenScannerButton" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 font-semibold shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Mobile Scanner
                </button>
                <button id="posOpenTransactionHistoryButton" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 font-semibold shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Transaction History
                </button>
                <a href="{{ route('archived') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 font-semibold shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archived Items
                </a>
            </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.65fr_1fr] gap-4 mt-6">
            <div class="space-y-3">
                <div class="rounded-[20px] border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <!-- Section Header Bar (matching All Stocks design) -->
                    <div class="bg-[#0f172a] px-6 py-4 border-b border-slate-800 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <!-- Left Side: Search Input + Filter Icon Button -->
                        <div class="flex items-center gap-2 flex-1 max-w-[420px]">
                            <div class="relative flex-1">
                                <label for="posProductSearchInput" class="sr-only">Search products</label>
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 12.65z"/></svg>
                                <input id="posProductSearchInput" type="text" placeholder="Search products..." class="w-full rounded-[10px] border border-slate-700 bg-slate-800/90 text-white placeholder:text-white placeholder-white pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#6EC1D1]" style="height: 42px;" />
                            </div>
                            <button id="posFilterToggleButton" type="button" aria-label="Open filter" class="inline-flex h-[42px] w-[42px] items-center justify-center rounded-[10px] bg-slate-800 text-white border border-slate-700 transition hover:bg-slate-700 focus:outline-none focus:ring-1 focus:ring-[#6EC1D1] flex-shrink-0">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-2.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            </button>
                        </div>

                        <!-- Right Side: Filter by + Two Dropdowns -->
                        <div class="flex flex-wrap items-center gap-2">
                            <label class="text-xs font-semibold text-white whitespace-nowrap">Filter by:</label>
                            <div class="relative">
                                <select id="posCategorySelect" class="hidden">
                                    <option value="All">All Categories</option>
                                    <!-- Categories will be loaded dynamically from product categories -->
                                </select>
                            </div>
                            <div class="relative">
                                <select id="posBrandSelect" class="hidden">
                                    <option value="All">All Brands</option>
                                    <!-- Brands will be loaded dynamically based on selected product category -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div id="posProductGrid" class="grid gap-3 grid-cols-3">
                            <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500">
                                Search for products or scan a barcode to load items from All Stocks.
                            </div>
                        </div>
                        <div id="posProductPagination" class="hidden mt-4 flex items-center justify-center gap-3 border-t border-slate-200 pt-3">
                        </div>
                    </div>
                </div>

            </div>

            <aside class="space-y-5 xl:sticky xl:top-4">
                <div class="rounded-[20px] border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <div class="bg-[#0f172a] px-6 py-4 flex items-center justify-between gap-3 border-b border-slate-800">
                        <div>
                            <h2 class="text-sm font-bold text-white">Cart</h2>
                            <p class="text-xs text-slate-300">Selected items show here.</p>
                        </div>
                        <button id="posEmptyCartButton" class="rounded-[10px] bg-[#6EC1D1] px-3 py-1.5 text-[11px] font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all duration-200">Empty</button>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="overflow-x-auto rounded-t-[10px] overflow-hidden">
                            <table id="posCartTable" class="min-w-full text-left text-[11px]">
                                <thead class="border-b border-slate-800 bg-[#0f172a] text-xs font-semibold uppercase tracking-wider text-white" style="background-color: #0f172a;">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold text-white rounded-tl-[10px]">Item</th>
                                        <th class="px-6 py-2 text-right font-semibold text-white">Price</th>
                                        <th class="px-3 py-2 text-center font-semibold text-white">Qty</th>
                                        <th class="px-6 py-2 text-right font-semibold text-white">Total</th>
                                        <th class="px-3 py-2 text-center font-semibold text-white rounded-tr-[10px]">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="posCartBody"></tbody>
                            </table>
                            <div id="posEmptyCartMessage" class="mt-6 rounded-[10px] border border-dashed border-slate-300 bg-slate-50 px-3 py-6 text-center text-slate-500 text-sm">Cart empty.</div>
                        </div>
                        <div class="rounded-[10px] border border-slate-200 bg-slate-50 p-4 shadow-sm">
                            <div class="grid gap-3">
                                <label class="block text-sm text-slate-700">
                                    <span class="font-semibold">Extra Charges</span>
                                    <input id="posExtraChargeInput" type="number" min="0" step="0.01" value="0" placeholder="0.00" class="mt-2 w-full rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" />
                                </label>
                                <div class="block text-sm text-slate-700">
                                    <div class="flex items-center justify-between">
                                        <label for="posDiscountInput" class="font-semibold cursor-pointer">Discount</label>
                                        <button type="button" id="posRemoveDiscountBtn" class="text-xs font-semibold text-red-600 hover:text-red-800 transition cursor-pointer hidden">Remove</button>
                                    </div>
                                    <input id="posDiscountInput" type="number" min="0" step="0.01" value="0" placeholder="0.00" class="mt-2 w-full rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" />
                                </div>
                            </div>
                        </div>
                        <div id="posCartFooter" class="space-y-2 hidden text-sm text-slate-600">
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between"><span>Subtotal</span><span id="posSubtotal">₱0.00</span></div>
                                <div class="flex items-center justify-between"><span>Services</span><span id="posServicesTotal">₱0.00</span></div>
                                <div class="flex items-center justify-between"><span>Extra</span><span id="posExtraCharge">₱0.00</span></div>
                                <div class="flex items-center justify-between"><span>Discount</span><span id="posDiscount">₱0.00</span></div>
                                <div class="flex items-center justify-between"><span>Included VAT (12%)</span><span id="posTax">₱0.00</span></div>
                                <div class="flex items-center justify-between text-base font-semibold text-slate-900"><span>Total</span><span id="posTotal">₱0.00</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[20px] border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <div class="bg-[#0f172a] px-6 py-4 flex items-center justify-between gap-3 border-b border-slate-800">
                        <h2 class="text-sm font-bold text-white">Services</h2>
                        <span class="text-xs text-slate-300">Add labor services</span>
                    </div>
                    <div class="p-4 grid gap-2 text-sm">
                        <label class="flex items-center gap-2 rounded-[10px] border border-slate-200 px-3 py-2 cursor-pointer hover:border-slate-400">
                            <input type="checkbox" value="installation" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                            <div class="space-y-0.5">
                                <p class="font-medium text-slate-900">Installation</p>
                                <p class="text-slate-500 text-[11px]">₱120</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 rounded-[10px] border border-slate-200 px-3 py-2 cursor-pointer hover:border-slate-400">
                            <input type="checkbox" value="tuneup" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                            <div class="space-y-0.5">
                                <p class="font-medium text-slate-900">Tune-up</p>
                                <p class="text-slate-500 text-[11px]">₱250</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 rounded-[10px] border border-slate-200 px-3 py-2 cursor-pointer hover:border-slate-400">
                            <input type="checkbox" value="brake_adjust" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                            <div class="space-y-0.5">
                                <p class="font-medium text-slate-900">Brake Adjust</p>
                                <p class="text-slate-500 text-[11px]">₱180</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="rounded-[20px] border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <div class="bg-[#0f172a] px-6 py-4 flex items-center justify-between gap-3 border-b border-slate-800">
                        <h2 class="text-sm font-bold text-white">Payment Method</h2>
                        <span class="text-xs text-slate-300">Quick select</span>
                    </div>
                    <div class="p-6 grid gap-3">
                        <label class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:border-slate-400">
                            <input type="radio" name="posPaymentMethod" value="cash" class="pos-payment-method h-4 w-4 rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]" checked />
                            <span>Cash</span>
                        </label>
                        <label class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:border-slate-400">
                            <input type="radio" name="posPaymentMethod" value="qr" class="pos-payment-method h-4 w-4 rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]" />
                            <span>QR PH</span>
                        </label>
                        <button id="posProceedPaymentButton" class="w-full rounded-[10px] bg-[#6EC1D1] px-3 py-2 text-sm font-bold text-black hover:bg-[#59b2c2] transition-all duration-200 shadow-sm">Proceed to Payment</button>
                    </div>
                </div>

            </aside>
        </div>
    </div>

    <div id="posPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posPaymentModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-4xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Process Payment</h2>
                    <p class="text-sm text-slate-900 font-medium">Review the transaction and confirm payment.</p>
                </div>
                <button id="posPaymentModalClose" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] px-6 py-6">
                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        <div>
                            <p class="font-semibold text-slate-900">Invoice #</p>
                            <p id="posPaymentInvoice">INV-000000</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Date</p>
                            <p id="posPaymentDate">Apr 30, 2024 10:30 AM</p>
                        </div>
                    </div>
                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-slate-900">Order Summary</h3>
                                <p class="text-xs text-slate-500">Items, services, and additional charges.</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm text-slate-700">
                                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-semibold">Item</th>
                                        <th class="px-3 py-2 text-left font-semibold">SKU</th>
                                        <th class="px-3 py-2 text-left font-semibold">Qty</th>
                                        <th class="px-3 py-2 text-right font-semibold">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="posPaymentItems"></tbody>
                            </table>
                        </div>
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <div class="flex items-center justify-between"><span>Subtotal</span><span id="posPaymentSubtotal">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Services</span><span id="posPaymentServices">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Extra Charges</span><span id="posPaymentExtra">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Discount</span><span id="posPaymentDiscount">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Included VAT (12%)</span><span id="posPaymentTax">₱0.00</span></div>
                            <div class="flex items-center justify-between text-base font-semibold text-slate-900"><span>Total</span><span id="posPaymentTotal">₱0.00</span></div>
                        </div>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">Payment Method</h3>
                        <div class="grid gap-3">
                            <label class="inline-flex items-center gap-3 rounded-[10px] border border-slate-200 px-4 py-3 cursor-pointer hover:border-slate-400 has-[:checked]:border-[#00fff2] has-[:checked]:bg-[#00fff2]/10 transition">
                                <input type="radio" name="posPaymentModalMethod" value="cash" class="h-4 w-4 text-[#105f68] rounded-[10px] focus:ring-[#105f68]" checked />
                                <span class="font-medium text-slate-900">Cash</span>
                            </label>
                            <label class="inline-flex items-center gap-3 rounded-[10px] border border-slate-200 px-4 py-3 cursor-pointer hover:border-slate-400 has-[:checked]:border-[#00fff2] has-[:checked]:bg-[#00fff2]/10 transition">
                                <input type="radio" name="posPaymentModalMethod" value="qr" class="h-4 w-4 text-[#105f68] rounded-[10px] focus:ring-[#105f68]" />
                                <span class="font-medium text-slate-900">QR PH</span>
                            </label>
                        </div>
                    </div>

                    <!-- Cash Tendered & Change Auto-Calculation -->
                    <div id="posCashPaymentDetails" class="rounded-[28px] border border-slate-200 bg-slate-50/70 p-4 space-y-3.5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Cash Received & Change</h3>
                            <span class="text-[11px] font-semibold text-[#105f68] bg-[#00fff2]/20 px-2 py-0.5 rounded-full">Auto-calculate</span>
                        </div>

                        <!-- Amount Paid / Tendered Input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Amount Paid by Customer <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₱</span>
                                <input type="number" 
                                       id="posAmountTenderedInput" 
                                       step="any" 
                                       min="0" 
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-3.5 py-2.5 rounded-[12px] border-2 border-slate-300 bg-white text-base font-bold text-slate-900 focus:outline-none focus:border-[#00fff2] focus:ring-2 focus:ring-[#00fff2]/30 transition shadow-sm" />
                            </div>
                        </div>

                        <!-- Quick Cash Suggestions -->
                        <div>
                            <span class="text-[11px] font-semibold text-slate-500 block mb-1.5">Quick Cash:</span>
                            <div id="posQuickCashPills" class="flex flex-wrap gap-1.5">
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-mode="exact">Exact</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-amount="100">₱100</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-amount="200">₱200</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-amount="500">₱500</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-amount="1000">₱1,000</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-amount="1500">₱1,500</button>
                                <button type="button" class="pos-quick-cash-pill px-2.5 py-1 rounded-[8px] bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-[#00fff2] hover:text-slate-900 hover:border-cyan-300 transition shadow-xs" data-amount="2000">₱2,000</button>
                            </div>
                        </div>

                        <!-- Change Box -->
                        <div id="posChangeDisplayBox" class="rounded-[16px] border border-slate-200 bg-white p-3.5 transition-all">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-600">Change (Sukli)</span>
                                <span id="posChangeDisplayAmount" class="text-lg font-extrabold text-emerald-600">₱0.00</span>
                            </div>
                            <div id="posInsufficientWarning" class="hidden mt-1.5 text-[11px] font-semibold text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span id="posInsufficientText">Insufficient amount</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button id="posPaymentModalCancel" class="flex-1 rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Cancel</button>
                        <button id="posPaymentModalConfirm" class="flex-1 rounded-[10px] bg-[#6EC1D1] px-4 py-3 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition shadow-sm">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="posReceiptOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posReceiptOverlay').classList.add('hidden')"></div>
        <div class="relative w-full max-w-3xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="receipt-content">
                <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-black text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold text-black">Payment Successful!</h2>
                            <p class="text-sm text-slate-900 font-medium">Transaction has been recorded successfully.</p>
                            <p class="mt-1 text-sm text-slate-900 font-medium">Invoice #: <span id="receiptInvoice">INV-000000</span></p>
                            <p id="receiptDate" class="hidden"></p>
                        </div>
                    </div>
                    <button id="posCloseReceiptButton" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div class="grid gap-4 sm:grid-cols-3 mb-6">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold text-slate-500">Amount Paid</p>
                            <p id="receiptPaid" class="mt-2 text-xl font-bold text-slate-900">₱0.00</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-emerald-50/70 p-4">
                            <p class="text-xs font-semibold text-emerald-700">Change (Sukli)</p>
                            <p id="receiptChange" class="mt-2 text-xl font-bold text-emerald-700">₱0.00</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold text-slate-500">Payment Method</p>
                            <p id="receiptPaymentMethod" class="mt-2 text-xl font-bold text-slate-900">Cash</p>
                        </div>
                    </div>
                    <div id="receiptInvoiceDetails" class="hidden">
                        <!-- Invoice Header with Logo and Business Details -->
                        <div class="mb-4 pb-3 border-b-2 border-slate-900">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-gray-700">KCC</span>
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-xs text-slate-900">MOTORCYCLE PARTS<br/>AND ACCESSORIES</h2>
                                        <p class="text-[10px] text-slate-600 mt-1 leading-tight">129 Motorcycle St., Barangay 123<br/>City, Philippines<br/>Tel: (02) 1234-56578</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-600"><span class="font-semibold">Invoice #:</span> <span id="invoiceNumHeader" class="font-bold text-slate-900">INV-000000</span></p>
                                    <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Date:</span> <span id="invoiceDateHeader" class="font-bold text-slate-900">Apr 30, 2024</span></p>
                                    <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Cashier:</span> <span id="receiptCashierName" class="font-bold text-slate-900">{{ auth()->user()?->name ?? 'Cashier' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-4">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b-2 border-slate-900 text-slate-900">
                                        <th class="text-left py-1 text-[10px] font-bold uppercase tracking-wide">Item</th>
                                        <th class="text-left py-1 text-[10px] font-bold uppercase tracking-wide">SKU</th>
                                        <th class="text-center py-1 text-[10px] font-bold uppercase tracking-wide">Qty</th>
                                        <th class="text-right py-1 text-[10px] font-bold uppercase tracking-wide">Price</th>
                                        <th class="text-right py-1 text-[10px] font-bold uppercase tracking-wide">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceItemsTable" class="text-slate-700">
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Section -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-600">Subtotal</span>
                                    <span id="invoiceSubtotal" class="font-semibold text-slate-900">₱0.00</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-600">Discount</span>
                                    <span id="invoiceDiscount" class="font-semibold text-slate-900">₱0.00</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-600">Included VAT (12%)</span>
                                    <span id="invoiceTax" class="font-semibold text-slate-900">₱0.00</span>
                                </div>
                                <div class="flex justify-between text-sm border-t-2 border-slate-900 pt-2 mt-2">
                                    <span class="font-bold text-slate-900">TOTAL</span>
                                    <span id="invoiceTotalAmount" class="font-bold text-[#105f68]">₱0.00</span>
                                </div>
                            </div>
                            <div class="space-y-2 text-right">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Amount Received</span>
                                    <span id="invoiceAmountReceived" class="text-sm font-bold text-slate-900">₱0.00</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Change</span>
                                    <span id="invoiceChange" class="text-sm font-bold text-[#105f68]">₱0.00</span>
                                </div>
                                <div class="flex flex-col mt-2">
                                    <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Payment Method</span>
                                    <span id="invoicePaymentMethod" class="text-xs font-bold text-slate-900">Cash</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button id="posCloseReceiptDoneButton" class="flex-1 rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Close</button>
                        <button id="posPrintReceiptButton" class="flex-1 rounded-[10px] bg-[#6EC1D1] px-4 py-3 text-sm font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 hidden">Print Receipt</button>
                        <button id="posViewInvoiceButton" class="flex-1 rounded-[10px] bg-[#6EC1D1] px-4 py-3 text-sm font-bold text-black shadow-sm hover:bg-[#59b2c2] ring-1 ring-slate-300">View Invoice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="posQRPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posQRPaymentModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Scan to Pay</h2>
                    <p class="text-sm text-slate-500">Use InstaPay or GCash to complete payment</p>
                </div>
                <button id="posCloseQRPaymentButton" class="rounded-full p-2 text-slate-500 hover:bg-black/40">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex flex-col items-center px-6 py-8">
                <div class="mb-6 text-center">
                    <p class="text-sm text-slate-600 mb-2">Amount to Pay</p>
                    <p id="posQRPaymentAmount" class="text-3xl font-bold text-slate-900">₱0.00</p>
                </div>
                <div id="posQRCodeContainer" class="mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div id="posQRCode" class="flex items-center justify-center" style="width: 240px; height: 240px;"></div>
                </div>
                <p class="text-sm text-slate-600 text-center mb-6">Scan the QR code with your mobile app to proceed with payment</p>
                <button id="posQRPaymentCompleteButton" class="w-full rounded-2xl bg-[#6EC1D1] px-4 py-3 text-sm font-bold text-black hover:bg-[#59b2c2]">Payment Complete</button>
            </div>
        </div>
    </div>

    <!-- Invoice View Modal -->
    <div id="posInvoiceModal" class="hidden fixed inset-0 z-[100000002] flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posInvoiceModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-3xl max-h-[100vh] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="receipt-content">
                <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-black text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                        <div>
                            <h2 class="text-xl font-bold text-black">Invoice Details</h2>
                            <p class="text-sm text-slate-800 font-medium">Transaction invoice details</p>
                            <p class="mt-2 text-sm text-slate-800 font-medium">Invoice #: <span id="posInvoiceNumber">INV-000000</span></p>
                        </div>
                    </div>
                    <button id="posCloseInvoiceModalButton" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <!-- Invoice Header with Logo and Business Details -->
                    <div class="mb-4 pb-3 border-b-2 border-slate-900">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex gap-2">
                                <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-gray-700">KCC</span>
                                </div>
                                <div>
                                    <h2 class="font-bold text-xs text-slate-900">MOTORCYCLE PARTS<br/>AND ACCESSORIES</h2>
                                    <p class="text-[10px] text-slate-600 mt-1 leading-tight">129 Motorcycle St., Barangay 123<br/>City, Philippines<br/>Tel: (02) 1234-56578</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-600"><span class="font-semibold">Invoice #:</span> <span id="posInvoiceNumHeader" class="font-bold text-slate-900">INV-000000</span></p>
                                <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Date:</span> <span id="posInvoiceDateHeader" class="font-bold text-slate-900">Apr 30, 2024</span></p>
                                <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Cashier:</span> <span id="posInvoiceCashierName" class="font-bold text-slate-900">{{ auth()->user()?->name ?? 'Cashier' }}</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-4">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="border-b-2 border-slate-900 text-slate-900">
                                    <th class="text-left py-1 text-[10px] font-bold uppercase tracking-wide">Item</th>
                                    <th class="text-left py-1 text-[10px] font-bold uppercase tracking-wide">SKU</th>
                                    <th class="text-center py-1 text-[10px] font-bold uppercase tracking-wide">Qty</th>
                                    <th class="text-right py-1 text-[10px] font-bold uppercase tracking-wide">Price</th>
                                    <th class="text-right py-1 text-[10px] font-bold uppercase tracking-wide">Total</th>
                                </tr>
                            </thead>
                            <tbody id="posInvoiceItemsTable" class="text-slate-700">
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Section -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm"><span>Subtotal</span><span id="posInvoiceSubtotal">₱0.00</span></div>
                            <div class="flex justify-between text-sm"><span>Services</span><span id="posInvoiceServices">₱0.00</span></div>
                            <div class="flex justify-between text-sm"><span>Extra</span><span id="posInvoiceExtra">₱0.00</span></div>
                            <div class="flex justify-between text-sm"><span>Discount</span><span id="posInvoiceDiscount">₱0.00</span></div>
                            <div class="flex justify-between text-sm"><span>Included VAT (12%)</span><span id="posInvoiceTax">₱0.00</span></div>
                            <div class="flex justify-between text-sm border-t-2 border-slate-900 pt-2 mt-2">
                                <span class="font-bold text-slate-900">TOTAL</span>
                                <span id="posInvoiceTotalAmount" class="font-bold text-[#105f68]">₱0.00</span>
                            </div>
                        </div>
                        <div class="space-y-2 text-right">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Amount Received</span>
                                <span id="posInvoiceAmountReceived" class="text-sm font-bold text-slate-900">₱0.00</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Change</span>
                                <span id="posInvoiceChange" class="text-sm font-bold text-[#105f68]">₱0.00</span>
                            </div>
                            <div class="flex flex-col mt-2">
                                <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Payment Method</span>
                                <span id="posInvoicePaymentMethod" class="text-xs font-bold text-slate-900">Cash</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button id="posCloseInvoiceDoneButton" class="flex-1 rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Close</button>
                        <button id="posPrintInvoiceButton" class="flex-1 rounded-[10px] bg-[#6EC1D1] px-4 py-3 text-sm font-bold text-black hover:bg-[#59b2c2]">Print Invoice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="posTransactionHistoryModal" class="hidden fixed inset-0 z-[100000001] flex items-center justify-center px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posTransactionHistoryModal').classList.add('hidden')"></div>
        <div class="relative flex w-full max-w-5xl max-h-[90vh] flex-col overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5 flex-shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-black">Transaction History</h2>
                    <p class="text-sm text-slate-900 font-medium">All completed transactions are recorded here. Filter by date to review specific sales.</p>
                </div>
                <button id="posCloseTransactionHistoryButton" class="rounded-[10px] p-2 text-black hover:bg-black/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-6 py-6 overflow-y-auto">
                <div class="grid gap-3 sm:grid-cols-[1.2fr_1fr_1fr] items-end">
                    <label class="block text-sm text-slate-700">
                        <span class="font-semibold">From</span>
                        <input id="posHistoryFilterFrom" type="date" class="mt-2 h-11 w-full rounded-[10px] border border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1]" style="accent-color: #6EC1D1; color-scheme: light;" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="font-semibold">To</span>
                        <input id="posHistoryFilterTo" type="date" class="mt-2 h-11 w-full rounded-[10px] border border-slate-300 bg-slate-50 px-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1]" style="accent-color: #6EC1D1; color-scheme: light;" />
                    </label>
                    <div class="flex items-center gap-3">
                        <button id="posHistoryFilterApplyButton" class="h-11 rounded-[10px] bg-[#6EC1D1] px-4 text-sm font-bold text-black hover:bg-[#59b2c2]">Apply Filter</button>
                        <button id="posHistoryFilterClearButton" class="h-11 rounded-[10px] border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-black/10">Clear</button>
                    </div>
                </div>
                <div class="overflow-x-auto overflow-y-auto max-h-[40vh] rounded-[10px] border border-slate-200">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-[#0f172a] text-white text-xs font-semibold uppercase tracking-wider sticky top-0 z-10" style="background-color: #0f172a;">
                            <tr class="border-b border-slate-800 bg-[#0f172a]" style="background-color: #0f172a;">
                                <th class="px-3 py-3 text-center w-10">
                                    <input type="checkbox" id="posSelectAllTransactions" class="rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer" />
                                </th>
                                <th class="px-3 py-3 text-white">Invoice</th>
                                <th class="px-3 py-3 text-white">SKU</th>
                                <th class="px-3 py-3 text-white">Date</th>
                                <th class="px-3 py-3 text-white">Method</th>
                                <th class="px-3 py-3 text-center text-white">Items</th>
                                <th class="px-4 py-3 text-right text-white min-w-[110px]">Total</th>
                                <th class="pl-8 pr-4 py-3 text-center text-white min-w-[130px]">Action</th>
                            </tr>
                        </thead>
                        <tbody id="posTransactionHistoryBody" class="divide-y divide-slate-200"></tbody>
                    </table>
                </div>
                <div id="posTransactionHistoryBulkActions" class="mt-4 hidden rounded-[10px] border border-red-200 bg-red-50 px-4 py-3 flex items-center justify-between">
                    <div class="text-sm text-red-900">
                        <span class="font-semibold" id="posSelectedCount">0</span> transactions selected
                    </div>
                    <button id="posBulkDeleteTransactions" class="rounded-[10px] bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete Selected</button>
                </div>
                <div id="posTransactionHistoryPagination" class="mt-4 hidden flex items-center justify-between rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                    <div id="posTransactionHistoryInfo" class="font-medium text-slate-600">Showing 0 of 0</div>
                    <div class="flex items-center gap-1.5">
                        <button id="posHistoryPrevPage" class="inline-flex items-center rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer">← Prev</button>
                        <div id="posHistoryPageNumbers" class="flex items-center gap-1"></div>
                        <button id="posHistoryNextPage" class="inline-flex items-center rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer">Next →</button>
                    </div>
                </div>
                <div id="posTransactionHistoryEmpty" class="rounded-[10px] border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center text-slate-500 text-sm hidden">No transactions match this date range.</div>
            </div>
        </div>
    </div>

    <!-- Desktop QR Scanner Modal -->
    <div id="posDesktopScannerModal" class="fixed inset-0 hidden items-center justify-center z-50 px-4 py-6" style="display: none;">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posDesktopScannerModal').style.display='none'"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
            <div class="bg-[#6EC1D1] px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-black/10 rounded-lg p-2">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812-1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-black">QR Code Scanner</h2>
                    </div>
                    <button id="posCloseDesktopScannerButton" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div id="posDesktopScannerReader" class="w-full bg-black rounded-xl overflow-hidden mb-4"></div>
                <div id="posDesktopScannerStatus" class="text-center text-sm text-slate-600">Position QR code within the frame</div>
            </div>
        </div>
    </div>

    <!-- Mobile Scanner Modal -->
    <div id="posMobileScannerModal" class="fixed inset-0 hidden items-center justify-center z-[9999] px-4 py-6">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('posMobileScannerModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-md bg-slate-800 rounded-2xl overflow-hidden shadow-2xl">
            <div class="bg-slate-800 px-4 py-3 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-white">POS Scanner</h1>
                    <p class="text-xs text-slate-400">Scan QR codes to add items to cart</p>
                </div>
                <button id="posCloseMobileScannerButton" class="rounded-full bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-black hover:bg-[#59b2c2] transition-colors">
                    Close
                </button>
            </div>

            <div class="flex flex-col items-center justify-center p-4">
                <div id="posMobileScannerReader" class="w-full bg-black rounded-2xl overflow-hidden"></div>
                <div id="posMobileScannerStatus" class="mt-4 text-center text-sm text-slate-400">
                    Position QR code within the frame
                </div>
            </div>

            <div class="bg-slate-800 px-4 py-3">
                <h2 class="text-sm font-semibold text-white mb-2">Recent Scans</h2>
                <div id="posMobileScannerRecent" class="space-y-2 max-h-40 overflow-y-auto">
                    <div class="text-center text-xs text-slate-500">No items scanned yet</div>
                </div>
            </div>

            <div class="bg-slate-900 px-4 py-2 border-t border-slate-700">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Connection:</span>
                    <span id="posMobileScannerConnection" class="text-green-400 font-medium">Connected</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.POS = {
            routes: {
                apiProducts: '{{ route("api.products") }}',
                mobileScanner: '{{ route("pos.mobile-scanner") }}'
            },
            cashier: '{{ auth()->user()?->name ?? "Cashier" }}'
        };

        document.addEventListener('DOMContentLoaded', function() {
            function setupCustomDatePicker(inputId) {
                const input = document.getElementById(inputId);
                if (!input) return;

                input.type = 'text';
                input.readOnly = true;
                input.placeholder = 'mm/dd/yyyy';
                input.className = 'h-11 w-full rounded-[14px] border border-slate-300 bg-white px-3.5 pr-10 text-sm text-slate-900 focus:outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-300 cursor-pointer shadow-sm transition';

                const wrapper = document.createElement('div');
                wrapper.className = 'relative w-full mt-2';
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
                card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-2 z-[99999] w-72 rounded-[18px] bg-white p-4 shadow-[0_16px_40px_rgba(0,0,0,0.12)] border border-slate-100 transition-all duration-200';
                wrapper.appendChild(card);

                let currentDate = new Date();
                let selectedDate = input.value ? new Date(input.value) : null;
                let viewMode = 'days';

                function render() {
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
                        <div class="flex items-center justify-between mb-3 px-1">
                            <button type="button" class="toggle-view-btn text-sm font-bold text-slate-900 hover:text-slate-700 inline-flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-slate-100 transition">
                                <span>${monthNames[month]} ${year}</span>
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div class="flex items-center gap-1">
                                <button type="button" class="prev-month-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Previous Month">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                </button>
                                <button type="button" class="next-month-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Next Month">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[11px] font-semibold text-slate-400">
                            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
                    `;

                    for (let i = firstDay - 1; i >= 0; i--) {
                        html += `<span class="h-7 flex items-center justify-center text-slate-300">${daysInPrevMonth - i}</span>`;
                    }

                    const today = new Date();
                    for (let day = 1; day <= daysInMonth; day++) {
                        const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                        const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                        let dayClasses = "h-7 w-7 mx-auto flex items-center justify-center rounded-lg font-medium cursor-pointer transition-all duration-150 ";
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
                        html += `<span class="h-7 flex items-center justify-center text-slate-300">${i}</span>`;
                    }

                    html += `
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100 text-xs font-semibold px-1">
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
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100 px-1">
                            <button type="button" class="prev-year-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="text-sm font-bold text-slate-900">${year}</span>
                            <button type="button" class="next-year-btn p-1.5 rounded-full text-slate-600 hover:bg-slate-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                    `;

                    shortMonths.forEach((m, idx) => {
                        const isSel = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === idx;
                        let mClasses = "py-2.5 rounded-xl text-center font-semibold cursor-pointer transition-all duration-150 ";
                        if (isSel) {
                            mClasses += "bg-[#6EC1D1] text-black font-bold shadow-md";
                        } else {
                            mClasses += "text-slate-700 hover:bg-slate-100";
                        }
                        html += `<button type="button" data-month="${idx}" class="month-btn ${mClasses}">${m}</button>`;
                    });

                    html += `
                        </div>
                        <div class="mt-3 text-right">
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
                    document.querySelectorAll('.custom-calendar-card').forEach(c => {
                        if (c !== card) c.classList.add('hidden');
                    });
                    card.classList.toggle('hidden');
                    const isOpen = !card.classList.contains('hidden');
                    if (isOpen) {
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

            setupCustomDatePicker('posHistoryFilterFrom');
            setupCustomDatePicker('posHistoryFilterTo');

            /**
             * Custom-styled dropdown to replace a native <select>, matching the
             * "Warehouse" dropdown design: rounded card, chevron, and the
             * currently selected item highlighted with a soft slate background.
             * The original <select> is kept (hidden) so any existing code that
             * reads/writes its .value or listens for its 'change' event keeps
             * working untouched. Options added dynamically via JS (e.g. AJAX-
             * populated categories/brands) are picked up automatically via a
             * MutationObserver.
             */
            function setupCustomSelectDropdown(selectId, placeholder) {
                const select = document.getElementById(selectId);
                if (!select || select.dataset.customized === 'true') return;
                select.dataset.customized = 'true';

                const wrapper = select.parentElement;
                wrapper.classList.add('relative', 'inline-block');

                const button = document.createElement('button');
                button.type = 'button';
                button.id = selectId + 'Button';
                button.className = 'custom-select-button inline-flex min-w-[130px] items-center justify-between gap-2 rounded-[10px] border border-slate-700 bg-slate-800 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-1 focus:ring-[#6EC1D1]';
                button.innerHTML = `
                    <span class="custom-select-label truncate"></span>
                    <svg class="h-3.5 w-3.5 flex-shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                `;
                wrapper.insertBefore(button, select);

                const panel = document.createElement('div');
                panel.className = 'custom-select-panel hidden absolute left-0 top-full z-50 mt-1 rounded-[12px] border border-slate-700/80 bg-[#0f172a] p-1.5 shadow-2xl text-white max-h-60 overflow-y-auto space-y-0.5';
                panel.style.minWidth = button.offsetWidth + 'px';
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
                        item.className = 'custom-select-item w-full rounded-[8px] px-3 py-2 text-left text-xs transition-colors duration-100 whitespace-nowrap ' +
                            (isSelected
                                ? 'bg-slate-800 font-bold text-white'
                                : 'text-slate-200 hover:text-white hover:bg-slate-800');
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
                    panel.style.minWidth = button.offsetWidth + 'px';
                    renderOptions();
                    panel.classList.remove('hidden');
                    chevron.classList.remove('text-slate-400');
                    chevron.classList.add('text-slate-600');
                }

                function closePanel() {
                    panel.classList.add('hidden');
                    chevron.classList.remove('text-slate-600');
                    chevron.classList.add('text-slate-400');
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

                // Keep the button label and (if open) the option list in sync
                // whenever options are added/removed/changed dynamically.
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