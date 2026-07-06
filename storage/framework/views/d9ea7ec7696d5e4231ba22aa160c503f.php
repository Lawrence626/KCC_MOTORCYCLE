<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('POS Terminal')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('POS Terminal'))]); ?>
    <div class="space-y-3 max-w-[1480px] mx-auto px-3">
        <div class="rounded-[28px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Point of Sale (POS)</h1>
                    <p class="text-sm text-slate-600">Process sales, service billing, and payments from one compact page.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <button id="posOpenDesktopScannerButton" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 hover:bg-slate-100">
                        <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Scan QR
                    </button>
                    <button id="posOpenScannerButton" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 hover:bg-slate-100">
                        <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Mobile Scanner
                    </button>
                    <button id="posOpenTransactionHistoryButton" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700 hover:bg-slate-100">
                        <svg class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        Transaction History
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.65fr_1fr] gap-4">
            <div class="space-y-3">
                <div class="rounded-[28px] border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex flex-wrap gap-2">
                            <button class="rounded-full bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm">All</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Exhaust</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Helmets</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Tires</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Brakes</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Oils</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Batteries</button>
                            <button class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 hover:border-emerald-500">Accessories</button>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="relative w-full sm:w-[260px]">
                                <input id="posProductSearchInput" type="text" placeholder="Search products..." class="w-full rounded-full border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                            </div>
                            <button class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-3 shadow-sm">
                    <div id="posProductGrid" class="grid gap-3 grid-cols-3">
                        <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500">
                            Search for products or scan a barcode to load items from All Stocks.
                        </div>
                    </div>
                    <div id="posProductPagination" class="hidden mt-4 flex items-center justify-center gap-3 border-t border-slate-200 pt-3">
                    </div>
                </div>

            </div>

            <aside class="space-y-3 xl:sticky xl:top-4">
                <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3 gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-slate-900">Cart</h2>
                            <p class="text-xs text-slate-500">Selected items show here.</p>
                        </div>
                        <button id="posEmptyCartButton" class="rounded-full border border-slate-300 px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">Empty</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table id="posCartTable" class="min-w-full text-left text-[11px]">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wide text-[10px]">
                                    <th class="px-2 py-2">Item</th>
                                    <th class="px-2 py-2">SKU</th>
                                    <th class="px-2 py-2 text-right">Price</th>
                                    <th class="px-2 py-2 text-center">Qty</th>
                                    <th class="px-2 py-2 text-right">Total</th>
                                    <th class="px-2 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="posCartBody"></tbody>
                        </table>
                        <div id="posEmptyCartMessage" class="rounded-[24px] border border-dashed border-slate-300 bg-slate-50 px-3 py-6 text-center text-slate-500 text-sm">Cart empty.</div>
                    </div>
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-4 shadow-sm mb-4">
                        <div class="grid gap-3">
                            <label class="block text-sm text-slate-700">
                                <span class="font-semibold">Extra Charges</span>
                                <input id="posExtraChargeInput" type="number" min="0" step="0.01" value="0" placeholder="0.00" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                            </label>
                            <label class="block text-sm text-slate-700">
                                <span class="font-semibold">Discount</span>
                                <input id="posDiscountInput" type="number" min="0" step="0.01" value="0" placeholder="0.00" class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                            </label>
                        </div>
                    </div>
                    <div id="posCartFooter" class="mt-4 space-y-2 hidden text-sm text-slate-600">
                        <div class="grid gap-2">
                            <div class="flex items-center justify-between"><span>Subtotal</span><span id="posSubtotal">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Services</span><span id="posServicesTotal">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Extra</span><span id="posExtraCharge">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Discount</span><span id="posDiscount">₱0.00</span></div>
                            <div class="flex items-center justify-between"><span>Included VAT (12%)</span><span id="posTax">₱0.00</span></div>
                            <div class="flex items-center justify-between text-base font-semibold text-slate-900"><span>Total</span><span id="posTotal">₱0.00</span></div>
                        </div>
                        <button id="posProceedPaymentButton" class="w-full rounded-2xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Proceed to Payment</button>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-slate-900">Payment Method</h2>
                        <span class="text-xs text-slate-500">Quick select</span>
                    </div>
                    <div class="grid gap-3">
                        <label class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="posPaymentMethod" value="cash" class="pos-payment-method h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400" checked />
                            <span>Cash</span>
                        </label>
                        <label class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-3 py-2 text-sm cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="posPaymentMethod" value="qr" class="pos-payment-method h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400" />
                            <span>QR PH</span>
                        </label>
                    </div>
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-slate-900">Services</h2>
                        <span class="text-xs text-slate-500">Add labor services</span>
                    </div>
                    <div class="grid gap-2 text-sm">
                        <label class="flex items-center gap-2 rounded-2xl border border-slate-200 px-3 py-2 cursor-pointer hover:border-emerald-500">
                            <input type="checkbox" value="installation" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400" />
                            <div class="space-y-0.5">
                                <p class="font-medium text-slate-900">Installation</p>
                                <p class="text-slate-500 text-[11px]">₱120</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 rounded-2xl border border-slate-200 px-3 py-2 cursor-pointer hover:border-emerald-500">
                            <input type="checkbox" value="tuneup" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400" />
                            <div class="space-y-0.5">
                                <p class="font-medium text-slate-900">Tune-up</p>
                                <p class="text-slate-500 text-[11px]">₱250</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 rounded-2xl border border-slate-200 px-3 py-2 cursor-pointer hover:border-emerald-500">
                            <input type="checkbox" value="brake_adjust" class="pos-service-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-400" />
                            <div class="space-y-0.5">
                                <p class="font-medium text-slate-900">Brake Adjust</p>
                                <p class="text-slate-500 text-[11px]">₱180</p>
                            </div>
                        </label>
                    </div>
                </div>

            </aside>
        </div>
    </div>

    <div id="posPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xl px-4 py-6">
        <div class="w-full max-w-4xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Process Payment</h2>
                    <p class="text-sm text-slate-500">Review the transaction and confirm payment.</p>
                </div>
                <button id="posPaymentModalClose" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
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
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wide">
                                        <th class="px-3 py-2">Item</th>
                                        <th class="px-3 py-2">Qty</th>
                                        <th class="px-3 py-2 text-right">Total</th>
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
                            <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 cursor-pointer hover:border-emerald-500">
                                <input type="radio" name="posPaymentModalMethod" value="cash" class="h-4 w-4 text-emerald-600 focus:ring-emerald-400" checked />
                                <span class="font-medium text-slate-900">Cash</span>
                            </label>
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 hover:border-emerald-500">
                                <input type="radio" name="posPaymentModalMethod" value="qr" class="h-4 w-4 text-emerald-600 focus:ring-emerald-400" />
                                <span class="font-medium text-slate-900">QR PH</span>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">Notes</h3>
                        <p class="text-sm text-slate-500">Confirm the transaction after verifying the total, services, and charges.</p>
                    </div>
                    <div class="flex gap-3">
                        <button id="posPaymentModalCancel" class="flex-1 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-50">Cancel</button>
                        <button id="posPaymentModalConfirm" class="flex-1 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Confirm Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="posReceiptOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-3xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="receipt-content">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">Payment Successful!</h2>
                            <p class="text-sm text-slate-500">Transaction has been recorded successfully.</p>
                            <p class="mt-2 text-sm text-slate-500">Invoice #: <span id="receiptInvoice">INV-000000</span></p>
                            <p id="receiptDate" class="hidden"></p>
                        </div>
                    </div>
                    <button id="posCloseReceiptButton" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <div class="grid gap-4 sm:grid-cols-2 mb-6">
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Paid Amount</p>
                            <p id="receiptPaid" class="mt-2 text-xl font-semibold text-slate-900">₱0.00</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Payment Method</p>
                            <p id="receiptPaymentMethod" class="mt-2 text-xl font-semibold text-slate-900">Cash</p>
                        </div>
                    </div>
                    <div id="receiptInvoiceDetails" class="hidden">
                        <!-- Invoice Header with Logo and Business Details -->
                        <div class="mb-4 pb-3 border-b-2 border-slate-900">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex gap-2">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-emerald-700">KCC</span>
                                    </div>
                                    <div>
                                        <h2 class="font-bold text-xs text-slate-900">MOTORCYCLE PARTS<br/>AND ACCESSORIES</h2>
                                        <p class="text-[10px] text-slate-600 mt-1 leading-tight">129 Motorcycle St., Barangay 123<br/>City, Philippines<br/>Tel: (02) 1234-56578</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-slate-600"><span class="font-semibold">Invoice #:</span> <span id="invoiceNumHeader" class="font-bold text-slate-900">INV-000000</span></p>
                                    <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Date:</span> <span id="invoiceDateHeader" class="font-bold text-slate-900">Apr 30, 2024</span></p>
                                    <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Cashier:</span> <span class="font-bold text-slate-900">Admin</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-4">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b-2 border-slate-900 text-slate-900">
                                        <th class="text-left py-1 text-[10px] font-bold uppercase tracking-wide">Item</th>
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
                                    <span id="invoiceTotalAmount" class="font-bold text-emerald-700">₱0.00</span>
                                </div>
                            </div>
                            <div class="space-y-2 text-right">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Amount Received</span>
                                    <span id="invoiceAmountReceived" class="text-sm font-bold text-slate-900">₱0.00</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Change</span>
                                    <span id="invoiceChange" class="text-sm font-bold text-emerald-600">₱0.00</span>
                                </div>
                                <div class="flex flex-col mt-2">
                                    <span class="text-[10px] text-slate-600 font-semibold mb-0.5">Payment Method</span>
                                    <span id="invoicePaymentMethod" class="text-xs font-bold text-slate-900">Cash</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button id="posViewInvoiceButton" class="flex-1 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">View Invoice</button>
                        <button id="posPrintReceiptButton" class="flex-1 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700 hidden">Print Receipt</button>
                        <button id="posCloseReceiptDoneButton" class="flex-1 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-50">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="posQRPaymentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 px-4 py-6">
        <div class="w-full max-w-md overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Scan to Pay</h2>
                    <p class="text-sm text-slate-500">Use InstaPay or GCash to complete payment</p>
                </div>
                <button id="posCloseQRPaymentButton" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
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
                <button id="posQRPaymentCompleteButton" class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Payment Complete</button>
            </div>
        </div>
    </div>

    <div id="posTransactionHistoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 px-4 py-6">
        <div class="w-full max-w-5xl max-h-[100vh] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Transaction History</h2>
                    <p class="text-sm text-slate-500">All completed transactions are recorded here. Filter by date to review specific sales.</p>
                </div>
                <button id="posCloseTransactionHistoryButton" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4 px-6 py-6">
                <div class="grid gap-3 sm:grid-cols-[1.2fr_1fr_1fr] items-end">
                    <label class="block text-sm text-slate-700">
                        <span class="font-semibold">From</span>
                        <input id="posHistoryFilterFrom" type="date" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="font-semibold">To</span>
                        <input id="posHistoryFilterTo" type="date" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                    </label>
                    <div class="flex items-center gap-3">
                        <button id="posHistoryFilterApplyButton" class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Apply Filter</button>
                        <button id="posHistoryFilterClearButton" class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-50">Clear</button>
                    </div>
                </div>
                <div class="overflow-x-auto overflow-y-auto max-h-[52vh]">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 text-[11px] uppercase tracking-wide">
                                <th class="px-3 py-3">Invoice</th>
                                <th class="px-3 py-3">Date</th>
                                <th class="px-3 py-3">Method</th>
                                <th class="px-3 py-3 text-center">Items</th>
                                <th class="px-3 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody id="posTransactionHistoryBody"></tbody>
                    </table>
                </div>
                <div id="posTransactionHistoryPagination" class="mt-4 hidden items-center justify-between rounded-[24px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    <div id="posTransactionHistoryInfo" class="font-medium">Showing 0 of 0</div>
                    <div class="flex items-center gap-2">
                        <button id="posHistoryPrevPage" class="rounded-full border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50" disabled>Previous</button>
                        <button id="posHistoryNextPage" class="rounded-full border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 disabled:opacity-50" disabled>Next</button>
                    </div>
                </div>
                <div id="posTransactionHistoryEmpty" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center text-slate-500 text-sm hidden">No transactions match this date range.</div>
            </div>
        </div>
    </div>

    <!-- Desktop QR Scanner Modal -->
    <div id="posDesktopScannerModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
            <div class="bg-gradient-to-r from-emerald-600 to-cyan-600 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">QR Code Scanner</h2>
                    </div>
                    <button id="posCloseDesktopScannerButton" class="text-white/80 hover:text-white transition">
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

    <script>
        window.POS = {
            routes: {
                apiProducts: '<?php echo e(route("api.products")); ?>',
                mobileScanner: '<?php echo e(route("pos.mobile-scanner")); ?>'
            }
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/pos_terminal.js']); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/point_of_sales/terminal.blade.php ENDPATH**/ ?>