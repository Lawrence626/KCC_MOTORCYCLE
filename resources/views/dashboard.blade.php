<x-layouts.app :title="__('Dashboard')">

    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Overview of sales, inventory and performance insights</p>

        </div>
    </x-slot>

    <div id="dashboard-root" data-dashboard-url="{{ route('dashboard.data') }}" data-refresh-interval="15000" class="space-y-4">
        @php
            $canSeeDeadStock = auth()->user() && in_array(auth()->user()->role, ['admin', 'inventory_clerk']);
        @endphp

        <!-- Stats Grid -->
        <div class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 {{ $canSeeDeadStock ? 'lg:grid-cols-3 xl:grid-cols-5' : 'lg:grid-cols-4' }} gap-x-5 gap-y-10">
                <!-- Total Sales -->
                 <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-black text-xs font-semibold truncate">Total Sales</p>
                            <div class="mt-1">
                                <p id="salesValue" class="text-2xl font-bold text-black truncate">—</p>
                                <p id="salesComparison" class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Loading…</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                            <svg class="text-[#145a66]" style="width: 1.125rem; height: 1.125rem;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Transaction -->
               <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-black text-xs font-semibold truncate" style="color: #000000;">Total Transaction</p>
                            <div class="mt-1">
                                <p id="transactionsValue" class="text-2xl font-bold truncate" style="color: #000000;">—</p>
                                <p id="transactionsComparison" class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Loading…</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                            <svg class="text-[#145a66]" style="width: 1.125rem; height: 1.125rem;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Profit -->
                <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-black text-xs font-semibold truncate" style="color: #000000;">Total Profit</p>
                            <div class="mt-1">
                                <p id="profitValue" class="text-2xl font-bold truncate" style="color: #000000;">—</p>
                                <p id="profitComparison" class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Loading…</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                            <svg class="text-[#145a66]" style="width: 1.125rem; height: 1.125rem;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Item Sold -->
                 <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-black text-xs font-semibold truncate" style="color: #000000;">Total Item Sold</p>
                            <div class="mt-1">
                                <p id="itemsSoldValue" class="text-2xl font-bold truncate" style="color: #030303;">—</p>
                                <p id="itemsSoldComparison" class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Loading…</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                            <svg class="w-4.5 h-4.5 text-[#145a66]" style="width: 1.125rem; height: 1.125rem;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                @if($canSeeDeadStock)
                <!-- Dead Stock Card -->
                <a href="{{ route('dss.dead-stock.index') }}" class="border border-gray-200 p-4 bg-white shadow-sm block hover:shadow-md hover:ring-2 hover:ring-[#6EC1D1] hover:border-[#6EC1D1] transition cursor-pointer group" style="border-radius: 20px;">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-black text-xs font-semibold truncate">Dead Stock</p>
                            <div class="mt-1">
                                <p id="deadStockCardItems" class="text-2xl font-bold text-black truncate">—</p>
                                <p id="deadStockCardValue" class="text-gray-500 text-xs leading-tight mt-1 font-medium truncate">Loading…</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                            <svg class="w-4.5 h-4.5 text-[#145a66]" style="width: 1.125rem; height: 1.125rem;" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.172a2 2 0 0 1 1.732 1l8 13.856A2 2 0 0 1 20 21H4a2 2 0 0 1-1.732-3l8-13.856a2 2 0 0 1 1.732-1zM11 9v4h2V9h-2zm0 6v2h2v-2h-2z"/>
                            </svg>
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>

        {{-- ═══ DEAD STOCK STAT CARD SCRIPT ═══ --}}
        <script>
        (function() {
            fetch('{{ route("api.dss.dashboard-stats") }}')
                .then(r => r.json())
                .then(data => {
                    // Update small card
                    const dsItems = document.getElementById('deadStockCardItems');
                    if (dsItems) dsItems.textContent = (data.total || 0) + ' Items';
                    const dsValue = document.getElementById('deadStockCardValue');
                    if (dsValue) dsValue.textContent = 'Value at Risk: ₱' + Number(data.totalValue || 0).toLocaleString('en-PH', {minimumFractionDigits:2});
                })
                .catch(() => {});
        })();
        </script>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-5 gap-y-15 mt-4">
            <!-- Sales Overview Chart -->
            <div id="salesOverviewCard" class="lg:col-span-2 border border-slate-200 relative overflow-hidden rounded-[15px] bg-white shadow-sm" style="min-height: 390px; box-sizing: border-box; border-radius: 15px;">

                <!-- Header (title + range buttons) -->
                <div id="salesOverviewHeader" class="bg-[#0f172a] px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2">
                       <h2 id="salesOverviewTitle" class="font-bold tracking-wide text-white text-lg">Sales Overview</h2>
                    </div>
                    {{-- Sales range dropdown card --}}
                    <div class="relative" id="salesRangeWrapper">
                        <button type="button" id="salesRangeDropdownBtn"
                            onclick="toggleSalesRangeDropdown(event)"
                            class="inline-flex items-center gap-2 rounded-[10px] border border-[#59b2c2] bg-[#6EC1D1] px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 min-w-[110px] justify-between">
                            <span id="salesRangeLabel">Monthly</span>
                            <svg id="salesRangeChevron" class="w-3.5 h-3.5 text-slate-900 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="salesRangeDropdown"
                            class="hidden absolute top-full right-0 z-50 mt-1.5 w-full rounded-[12px] border border-slate-700 bg-[#0f172a] shadow-2xl overflow-hidden">
                            <div class="p-1 space-y-0.5">
                                {{-- Hidden buttons keep the existing JS (.sales-range-btn + data-range) working --}}
                                <button type="button" data-range="daily"   class="sales-range-btn hidden"></button>
                                <button type="button" data-range="weekly"  class="sales-range-btn hidden"></button>
                                <button type="button" data-range="monthly" class="sales-range-btn hidden active"></button>
                                <button type="button" data-range="yearly"  class="sales-range-btn hidden"></button>

                                <button type="button" onclick="pickSalesRange('daily',   'Daily')"   id="salesRangeOpt-daily"   class="sales-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">Daily</button>
                                <button type="button" onclick="pickSalesRange('weekly',  'Weekly')"  id="salesRangeOpt-weekly"  class="sales-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">Weekly</button>
                                <button type="button" onclick="pickSalesRange('monthly', 'Monthly')" id="salesRangeOpt-monthly" class="sales-range-dd-opt w-full px-3 py-1 text-sm font-semibold rounded-[8px] transition-colors text-left bg-slate-700 text-white cursor-pointer">Monthly</button>
                                <button type="button" onclick="pickSalesRange('yearly',  'Yearly')"  id="salesRangeOpt-yearly"  class="sales-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">Yearly</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body (chart) -->
                <div id="salesOverviewBody" class="relative w-full p-2.5 pb-1" style="height:310px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Sales by Category (full-circle ring + white knockout center + neon-on-sale legend) -->
            <div class="border border-gray-200 p-3 rounded-[15px] flex flex-col" style="border-radius: 15px; background-color: #ffffff; min-height: 390px;">
                <h2 class="text-sm font-bold text-black mb-2" style="font-family: 'Poppins', sans-serif;">Sales by Category</h2>
                <div class="flex flex-col items-center gap-3 flex-1">
                    <div style="position: relative; width: 120px; height: 120px; max-width: 120px; max-height: 120px;" class="mx-auto flex items-center justify-center flex-shrink-0">
                        <canvas id="categoryChart"></canvas>
                        <div id="categoryCenterOverlay" style="
                            position: absolute; inset: 0;
                            display: flex; flex-direction: column;
                            align-items: center; justify-content: center;
                            text-align: center;
                            pointer-events: none;
                            padding-top: 14px;
                            transition: opacity 0.15s ease-in-out;">
                            <span id="categoryCenterValue" style="color: #000000; font-weight: 700; font-size: 14px; line-height: 1.1;">0</span>
                            <span id="categoryCenterCaption" style="color: rgba(0,0,0,0.6); font-size: 9px; margin-top: 3px;">No sales today</span>
                        </div>
                    </div>

                    <div id="categoryLegend" class="w-full space-y-1 text-xs flex-1"></div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-5 gap-y-15 mt-4">

            <!-- Inventory Levels -->
            <div id="inventoryCardWrap" class="relative">
                <div id="inventoryCard" class="border border-gray-200 p-3 flex flex-col justify-between" style="border-radius: 20px; background-color: #ffffff;">
                    <!-- Top Section: Doughnut Chart + 3-Column Summary Box -->
                    <div class="flex items-center gap-2.5">
                        <!-- Doughnut Chart Container with Center Text -->
                        <div style="position: relative; width: 76px; height: 76px; flex-shrink: 0;" class="flex items-center justify-center">
                            <canvas id="inventoryChart" width="76" height="76" style="width: 76px; height: 76px;"></canvas>
                            <div id="inventoryCenterOverlay" style="
                                position: absolute; inset: 0;
                                display: flex; flex-direction: column;
                                align-items: center; justify-content: center;
                                text-align: center;
                                pointer-events: none;">
                                <span id="totalProductsValue" class="text-sm font-bold text-black leading-none" style="font-family: 'Poppins', sans-serif;">0</span>
                                <span class="text-[7.5px] text-gray-500 font-medium leading-tight mt-0.5">Total Products</span>
                            </div>
                        </div>

                        <!-- 3-Column Stat Box -->
                        <div class="flex-1 bg-[#f8fafc] border border-slate-100 rounded-xl py-1.5 px-0.5 grid grid-cols-3 divide-x divide-slate-200/70 text-center">
                            <!-- In Stock -->
                            <div class="px-0.5 flex flex-col items-center justify-center">
                                <div class="flex items-center gap-1 justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#10b981] inline-block shrink-0"></span>
                                    <span class="text-[9px] font-medium text-slate-600 truncate">In Stock</span>
                                </div>
                                <span id="inStockTopCount" class="text-[11px] font-bold text-slate-900 mt-0.5 leading-tight">0</span>
                                <span id="inStockTopPct" class="text-[8.5px] text-slate-400 font-normal leading-tight">(0%)</span>
                            </div>

                            <!-- Low Stock -->
                            <div class="px-0.5 flex flex-col items-center justify-center">
                                <div class="flex items-center gap-1 justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#f59e0b] inline-block shrink-0"></span>
                                    <span class="text-[9px] font-medium text-slate-600 truncate">Low Stock</span>
                                </div>
                                <span id="lowStockTopCount" class="text-[11px] font-bold text-slate-900 mt-0.5 leading-tight">0</span>
                                <span id="lowStockTopPct" class="text-[8.5px] text-slate-400 font-normal leading-tight">(0%)</span>
                            </div>

                            <!-- Out of Stock -->
                            <div class="px-0.5 flex flex-col items-center justify-center">
                                <div class="flex items-center gap-1 justify-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444] inline-block shrink-0"></span>
                                    <span class="text-[9px] font-medium text-slate-600 truncate">Out of Stock</span>
                                </div>
                                <span id="outOfStockTopCount" class="text-[11px] font-bold text-slate-900 mt-0.5 leading-tight">0</span>
                                <span id="outOfStockTopPct" class="text-[8.5px] text-slate-400 font-normal leading-tight">(0%)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section: 3 Progress Bars -->
                    <div class="space-y-1.5 my-1">
                        <!-- In Stock Row -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 flex items-center justify-center shrink-0" style="border-radius: 8px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(16, 185, 129, 0.10) 100%); border: 1px solid rgba(16, 185, 129, 0.20);">
                                <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between text-xs leading-none mb-1">
                                    <span class="font-bold text-slate-800 text-[10.5px]">In Stock</span>
                                    <span class="text-slate-900 font-bold text-[10.5px]"><span id="inStockValue">0</span> <span id="inStockPercent" class="text-slate-400 font-normal text-[9px]">(0%)</span></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div id="inStockProgressBar" class="bg-[#10b981] h-1.5 rounded-full transition-all duration-500" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Row -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 flex items-center justify-center shrink-0" style="border-radius: 8px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.10) 100%); border: 1px solid rgba(245, 158, 11, 0.20);">
                                <svg class="w-3.5 h-3.5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between text-xs leading-none mb-1">
                                    <span class="font-bold text-slate-800 text-[10.5px]">Low Stock</span>
                                    <span class="text-slate-900 font-bold text-[10.5px]"><span id="lowStockValue">0</span> <span id="lowStockPercent" class="text-slate-400 font-normal text-[9px]">(0%)</span></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div id="lowStockProgressBar" class="bg-[#f59e0b] h-1.5 rounded-full transition-all duration-500" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Out of Stock Row -->
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 flex items-center justify-center shrink-0" style="border-radius: 8px; background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.10) 100%); border: 1px solid rgba(239, 68, 68, 0.20);">
                                <svg class="w-3.5 h-3.5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between text-xs leading-none mb-1">
                                    <span class="font-bold text-slate-800 text-[10.5px]">Out of Stock</span>
                                    <span class="text-slate-900 font-bold text-[10.5px]"><span id="outOfStockValue">0</span> <span id="outOfStockPercent" class="text-slate-400 font-normal text-[9px]">(0%)</span></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div id="outOfStockProgressBar" class="bg-[#ef4444] h-1.5 rounded-full transition-all duration-500" style="width: 0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Section: Restock Alert Banner -->
                    <a href="{{ route('warehouse.management') }}" id="inventoryRestockBanner" class="flex items-center justify-between px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 border border-gray-200 transition-colors group cursor-pointer text-decoration-none">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <svg class="w-4 h-4 text-red-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span id="inventoryRestockAlertText" class="text-[11px] font-semibold text-red-600 truncate">
                                <span id="restockItemCount">0</span> items need restocking
                            </span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-red-500 group-hover:translate-x-0.5 transition-transform shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>
 
            <!-- Top Selling Item (slideshow widget) -->
            <div id="topSellingWidget" class="lg:col-span-1 bg-[#ffffff] border border-gray-200 p-4 flex flex-col justify-between" style="border-radius: 20px;">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-black" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Top Selling Items</h2>
                    <button type="button" id="topSellingOpenBtn" class="text-[10px] font-semibold text-[#105f68] hover:text-[#0d4f56] hover:underline transition-colors cursor-pointer">View All</button>
                </div>
 
                <div id="topSellingCarousel" class="rounded-lg overflow-hidden" style="background:#fff; position: relative;">
                    <div id="topSlideTrack" style="display:flex;width:100%;height:130px;position:relative;">
                        <!-- slides inserted here (absolute positioned, cross-fade) -->
                    </div>
                    <!-- Small slideshow dot indicators, centered at the bottom of the carousel -->
                    <div id="topSlideDots" style="position:absolute; left:0; right:0; bottom:6px; display:flex; align-items:center; justify-content:center; gap:5px; z-index:5;"></div>
                </div>
 
                <div id="topSellingInfo" class="mt-2 text-xs text-gray-700">
                    <!-- rank and product name shown here -->
                    <div id="topSellingPlaceholder" class="text-sm text-gray-500">Loading…</div>
                </div>
            </div>

            <!-- Fast & Slow Moving Items -->
            <div id="fastSlowMovingCard" class="lg:col-span-1 border border-gray-200 p-4 flex flex-col justify-between" style="border-radius: 20px; background-color: #ffffff;">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-black" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Fast &amp; Slow Moving Items</h2>
                    <button type="button" id="viewAllFastSlowBtn" onclick="openFastSlowModal()" class="text-[10px] font-semibold text-[#105f68] hover:text-[#0d4f56] hover:underline transition-colors cursor-pointer">View All</button>
                </div>

                <div class="grid grid-cols-2 gap-3 flex-1 min-h-0">
                    <!-- Left Column: Fast Moving Items -->
                    <div class="flex flex-col min-w-0 pr-2 border-r border-gray-100">
                        <div class="flex items-center gap-1.5 mb-1.5 pb-1 border-b border-gray-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-black truncate block leading-none">Fast Moving</span>
                                <span class="text-[8px] text-gray-400 truncate block leading-none mt-0.5">High demand items</span>
                            </div>
                        </div>
                        <div id="fastMovingList" class="flex flex-col gap-1 overflow-y-auto flex-1 min-h-0 pr-0.5">
                            <div class="text-[11px] text-gray-400 my-auto text-center py-4">Loading…</div>
                        </div>
                    </div>

                    <!-- Right Column: Slow Moving Items -->
                    <div class="flex flex-col min-w-0 pl-1">
                        <div class="flex items-center gap-1.5 mb-1.5 pb-1 border-b border-gray-100">
                            <span class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0"></span>
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-black truncate block leading-none">Slow Moving</span>
                                <span class="text-[8px] text-gray-400 truncate block leading-none mt-0.5">Low demand items</span>
                            </div>
                        </div>
                        <div id="slowMovingList" class="flex flex-col gap-1 overflow-y-auto flex-1 min-h-0 pr-0.5">
                            <div class="text-[11px] text-gray-400 my-auto text-center py-4">Loading…</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ Top Selling Items Modal ═══ -->
            <div id="topItemsModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeTopItemsModal()"></div>
                <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[85vh] z-10">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                        <div>
                            <h3 class="text-xl font-bold text-black">Top Selling Items</h3>
                            <p class="text-sm text-slate-900 font-medium">Ranking of best performing products by revenue and quantity.</p>
                        </div>
                        <button type="button" onclick="closeTopItemsModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto flex-1 p-0">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-center font-semibold w-16">Rank</th>
                                    <th class="px-4 py-3 text-left font-semibold">Product</th>
                                    <th class="px-4 py-3 text-left font-semibold w-28">Category</th>
                                    <th class="px-4 py-3 text-left font-semibold w-24">Qty Sold</th>
                                    <th class="px-4 py-3 text-left font-semibold w-28">Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="topItemsModalBody" class="divide-y divide-slate-200 bg-white">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span id="topItemsModalItemCount" class="text-xs text-slate-500">0 items</span>
                        <button type="button" onclick="closeTopItemsModal()" class="rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20 cursor-pointer">Close</button>
                    </div>
                </div>
            </div>

            <!-- ═══ Fast & Slow Moving View All Modal ═══ -->
            <div id="fast-slow-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeFastSlowModal()"></div>
                <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[85vh]">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                        <div>
                            <h3 class="text-xl font-bold text-black">Fast & Slow Moving Items</h3>
                            <p class="text-sm text-slate-900 font-medium">Complete list of all product movement data.</p>
                        </div>
                        <button type="button" onclick="closeFastSlowModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Tab Buttons -->
                    <div class="flex items-center gap-2 bg-slate-50 px-6">
                        <button type="button" id="fsModalTabFast" onclick="switchFastSlowModalTab('fast')" class="px-4 py-3 text-sm font-bold text-slate-900 transition cursor-pointer">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>Fast Moving
                        </button>
                        <button type="button" id="fsModalTabSlow" onclick="switchFastSlowModalTab('slow')" class="px-4 py-3 text-sm font-semibold text-slate-400 hover:text-slate-700 transition cursor-pointer">
                            <span class="inline-block w-2 h-2 rounded-full bg-orange-500 mr-1.5"></span>Slow Moving
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto flex-1 p-0">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-center font-semibold w-16">Rank</th>
                                    <th class="px-4 py-3 text-left font-semibold">Product</th>
                                    <th class="px-4 py-3 text-left font-semibold w-28">Category</th>
                                    <th class="px-4 py-3 text-left font-semibold w-24">Qty Sold</th>
                                    <th class="px-4 py-3 text-left font-semibold w-28" id="fsModalRevenueCol">Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="fsModalTableBody" class="divide-y divide-slate-200 bg-white">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span id="fsModalItemCount" class="text-xs text-slate-500">0 items</span>
                        <button type="button" onclick="closeFastSlowModal()" class="rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Close</button>
                    </div>
                </div>
            </div>

        <!-- ═══ Toast Notifications Container ═══ -->
        <div id="inventory-toast-container" class="fixed top-22 right-6 z-[40] flex flex-col gap-3 pointer-events-none" style="max-width: 360px; width: 100%;"></div>

        <!-- ═══ View All Notifications Modal ═══ -->
        <div id="all-notifications-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeAllNotificationsModal()"></div>
            <div class="relative bg-white rounded-[28px] shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[85vh]">
                <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-black">All Inventory Notifications</h3>
                        <p class="text-sm text-slate-900 font-medium">History of low stock and out of stock alerts.</p>
                    </div>
                    <button type="button" onclick="closeAllNotificationsModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="modal-notification-list" class="divide-y divide-slate-100 overflow-y-auto p-6 space-y-3">
                    <!-- Loaded dynamically -->
                </div>
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span id="modal-notif-count" class="text-xs text-slate-500">0 notifications</span>
                    <button type="button" onclick="closeAllNotificationsModal()" class="rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Close</button>
                </div>
            </div>
        </div>

        <!-- Floating Low Stock Toast Banner (Pest test requirement) -->
        <div id="dashboardLowStockBanner" class="hidden fixed right-4 top-24 z-[9999] max-w-sm rounded-2xl border border-amber-200 bg-white p-4 shadow-2xl transition-all duration-300" role="status">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-[10px] flex items-center justify-center" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.10) 100%); border: 1px solid rgba(245, 158, 11, 0.20);">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9.5" fill="#d97706"></circle>
                        <line x1="12" y1="7.5" x2="12" y2="12.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"></line>
                        <circle cx="12" cy="16" r="1.1" fill="#ffffff"></circle>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 banner-title">Low Stock Alert</p>
                    <p class="mt-1 text-xs text-slate-600 banner-message">A product is running low on stock.</p>
                </div>
                <button type="button" id="dashboardLowStockBannerDismiss" class="text-slate-400 hover:text-slate-700 transition" aria-label="Dismiss toast">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <style>
        #dashboardProfileButton {
            background-color: transparent !important;
            color: #0f0f0f !important;
        }

        #dashboardProfileButton:hover {
            background-color: transparent !important;
            color: #9ca3af !important;
        }

        #dashboardProfileButton:hover #dashboardProfileArrow {
            color: #9ca3af !important;
        }

        /* ---- Card Heights Sync ---- */
        #inventoryCard, #topSellingWidget, #comparisonCard, #fastSlowMovingCard {
            height: 270px !important;
            max-height: 270px;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ---- Sales by Category (full-circle ring + legend) ---- */
        #categoryLegend .cat-legend-row {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 4px;
        }
        #categoryLegend .cat-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            flex-shrink: 0;
            display: inline-block;
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
        }
        #categoryLegend .cat-label {
            color: rgba(0,0,0,0.65);
            font-weight: 500;
        }
        #categoryLegend .cat-label-active {
            color: #000000;
            font-weight: 600;
        }

        /* ---- Top Selling slideshow: cross-fade slides ---- */
        #topSlideTrack .top-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 130px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }
        #topSlideTrack .top-slide.is-active {
            opacity: 1;
            pointer-events: auto;
        }
        #topSlideTrack .top-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
        }



        /* ---- Small slideshow dot indicators ---- */
        #topSlideDots .top-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background-color: #afafaf;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.12);
            transition: background-color 0.18s ease, transform 0.18s ease;
            cursor: pointer;
        }
        #topSlideDots .top-dot.is-active {
            background-color: #949494;
            transform: scale(1.15);
        }

        /* ---- Top Selling Items modal (light theme) ---- */
       #topItemsModal .modal-panel {
    background-color: #ffffff;
    color: #6d6d6d;
    opacity: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 16px;
    transform: scale(0.96) translateY(6px);
    transition: opacity 0.28s ease, transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
}
#topItemsModal.is-open .modal-panel {
    opacity: 1;
    transform: scale(1) translateY(0);
}
#topItemsModal .modal-overlay-bg {
    background: rgba(0,0,0,0.6);
    opacity: 0;
    transition: opacity 0.28s ease;
}
#topItemsModal.is-open .modal-overlay-bg {
    opacity: 1;
}
#topItemsModal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 18px 22px;
    background-color: #6EC1D1;
    border-radius: 16px 16px 0 0;
    flex-shrink: 0;
}
#topItemsModal .modal-header h3 {
    margin: 0;
    color: #000000;
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.15rem;
}
#topItemsModal .modal-header p {
    margin: 2px 0 0;
    color: #000000;
    font-size: 0.8rem;
}
#topItemsModal .modal-body {
    padding: 0;
    overflow: auto;
}
#topItemsModal table thead th {
    background-color: #0f172a !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 16px;
    border-bottom: 1px solid #334155;
}
#topItemsModal table tbody td {
    color: #0f172a;
    border-bottom: 1px solid #f1f5f9;
}
#topItemsModal #closeTopItemsModal {
    background-color: transparent !important;
    color: #000000 !important;
    border: none;
    border-radius: 10px;
    width: 28px;
    height: 28px;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    transition: background-color 0.18s ease;
}
#topItemsModal #closeTopItemsModal:hover {
    background-color: rgba(0,0,0,0.1) !important;
    color: #000000 !important;
}
        
        /* Range buttons: unselected = original light teal tint background with teal text, selected (clicked) = solid #36ADA3 with same color teal text */
        .sales-range-btn {
            background-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: none;
            padding-top: 0.35rem;
            padding-bottom: 0.35rem;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            transition: background-color 0.14s ease, color 0.14s ease;
        }

        .sales-range-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .sales-range-btn.active {
            background-color: #6EC1D1 !important;
            color: #000000ff !important;
            font-weight: 700 !important;
            border-color: transparent !important;
        }

        .sales-range-btn.active:hover {
            background-color: #59b2c2 !important;
            color: #000000ff !important;
        }
        

        /* Dashboard cards shadow — mimic POS terminal containers */
        .border.border-gray-200 {
            box-shadow: 0 10px 30px rgba(2,6,23,0.08);
            transition: box-shadow 0.18s ease;
        }
        /* Neutralize hover lift/shadow so shadow is constant like POS terminal */
        .border.border-gray-200:hover {
            box-shadow: 0 10px 30px rgba(2,6,23,0.08) !important;
            transform: none !important;
        }

        /* ── Toast Notifications ──────────────────────────── */
        #inventory-toast-container {
            max-width: 360px;
        }
        .inv-toast {
            pointer-events: auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
            overflow: hidden;
            width: 100%;
        }
        .inv-toast:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .inv-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .inv-toast.show:hover {
            transform: translateY(-2px);
        }
        .inv-toast.hide {
            transform: translateX(10%);
            opacity: 0;
            margin-top: -10px;
        }
        .inv-toast .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            transition: width linear;
        }
        .inv-toast.toast-warning .toast-progress { background: #6EC1D1; }
        .inv-toast.toast-critical .toast-progress { background: #0aada5; }
        .inv-toast-icon {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 16px;
        }
        .inv-toast.toast-warning .inv-toast-icon {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.10) 100%);
            border: 1px solid rgba(245, 158, 11, 0.20);
            color: #d97706;
        }
        .inv-toast.toast-critical .inv-toast-icon {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.10) 100%);
            border: 1px solid rgba(239, 68, 68, 0.20);
            color: #dc2626;
        }
        .inv-toast-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 4px;
            padding: 6px 12px; border-radius: 6px;
            font-size: 12px; font-weight: 500;
            border: 1px solid transparent; cursor: pointer; transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1;
        }
        .inv-toast-btn-order {
            background: #6EC1D1; color: #000;
        }
        .inv-toast-btn-order:hover { background: #59b2c2; }
        .inv-toast-btn-dismiss {
            background: #f9fafb; color: #4b5563; border-color: #e5e7eb;
        }
        .inv-toast-btn-dismiss:hover { background: #f3f4f6; color: #111827; }

        /* ── Notification Center Dropdown ──────────────── */
        .notif-item {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f3f4f6;
        }
        .notif-item:last-child {
            border-bottom: none;
        }
        .notif-item:hover { background-color: transparent; }
        .notif-item-unread {
            background-color: transparent;
        }
        .notif-item-unread:hover { background-color: #f8fafc; }
        
        .notif-status-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .notif-status-dot.unread  { background-color: #3b82f6; }
        .notif-status-dot.read    { background-color: transparent; }
        .notif-status-dot.resolved { background-color: #22c55e; }
        
        .notif-btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 4px 10px; border-radius: 6px;
            font-size: 11px; font-weight: 500;
            background: #6EC1D1; color: #000; border: 1px solid #6EC1D1;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .notif-btn:hover { background: #59b2c2; border-color: #59b2c2; color: #000; }
    </style>

@push('scripts')
    @vite('resources/js/dashboard.js')

    <script>
        // Top Selling widget script: uses dashboard.data -> top_items and localStorage posProductImages
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.getElementById('dashboard-root');
            if (!root) return;
            const url = root.dataset.dashboardUrl;
            const track = document.getElementById('topSlideTrack');
            const dotsWrap = document.getElementById('topSlideDots');
            const info = document.getElementById('topSellingInfo');
            const placeholder = document.getElementById('topSellingPlaceholder');
            const openBtn = document.getElementById('topSellingOpenBtn');

            let items = [];
            let idx = 0;
            let rot = null;

            function loadLocalImages(){
                try { return JSON.parse(localStorage.getItem('posProductImages') || '{}'); } catch(e){ return {}; }
            }

            // Cross-fade to a given slide index (used by both auto-rotate and the arrow button)
            function goToSlide(newIdx){
                if (!items || items.length === 0) return;
                newIdx = ((newIdx % items.length) + items.length) % items.length;
                idx = newIdx;

                const slides = track.querySelectorAll('.top-slide');
                slides.forEach((s, i) => {
                    s.classList.toggle('is-active', i === idx);
                });

                const dots = dotsWrap.querySelectorAll('.top-dot');
                dots.forEach((d, i) => {
                    d.classList.toggle('is-active', i === idx);
                });

                showInfo();
            }

            function resolveItemImage(it, localImages){
                if (!it) return null;
                if (it.image_url || it.image) return it.image_url || it.image;
                localImages = localImages || loadLocalImages();
                const pid = it.product_id ?? it.id ?? null;
                const keyCandidates = [];
                if (pid !== null && pid !== undefined) {
                    keyCandidates.push(pid);
                    keyCandidates.push(String(pid));
                }
                if (it.sku) keyCandidates.push(it.sku);
                if (it.name) keyCandidates.push(it.name);

                for (const k of keyCandidates) {
                    if (k in localImages && localImages[k]) return localImages[k];
                }

                const keys = Object.keys(localImages);
                if (it.sku) {
                    const matchSku = keys.find(k => k.toLowerCase() === String(it.sku).toLowerCase());
                    if (matchSku) return localImages[matchSku];
                }
                if (it.name) {
                    const matchName = keys.find(k => k.toLowerCase() === String(it.name).toLowerCase());
                    if (matchName) return localImages[matchName];
                }
                return null;
            }

            function renderSlides(){
                if (!track) return;
                track.innerHTML = '';
                if (dotsWrap) dotsWrap.innerHTML = '';

                if (!items || items.length === 0) {
                    if (rot) { clearInterval(rot); rot = null; }
                    track.innerHTML = `
                        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50/80 rounded-xl border border-dashed border-slate-200 text-center p-3 select-none">
                            <div class="w-12 h-12 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 mb-1.5 shadow-sm">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-600">No Top Selling Items</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Sales data will appear here once items are sold</span>
                        </div>
                    `;
                    idx = 0;
                    showInfo();
                    return;
                }

                const localImages = loadLocalImages();

                items.forEach((it, i) => {
                    const slide = document.createElement('div');
                    slide.className = 'top-slide' + (i === 0 ? ' is-active' : '');

                    const found = resolveItemImage(it, localImages);
                    if (found) {
                        const img = document.createElement('img');
                        img.src = found;
                        img.alt = it.name || it.item || 'Product';
                        img.className = 'w-full h-full object-cover';
                        img.onerror = () => {
                            slide.innerHTML = `
                                <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 border border-slate-200/60 rounded-lg p-2 text-center text-slate-400">
                                    <svg class="w-7 h-7 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[11px] font-semibold text-slate-700 truncate max-w-[90%]">${escapeHtml(it.name || 'Product')}</span>
                                </div>`;
                        };
                        slide.appendChild(img);
                    } else {
                        slide.innerHTML = `
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 border border-slate-200/60 rounded-lg p-2 text-center text-slate-400">
                                <svg class="w-7 h-7 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[11px] font-semibold text-slate-700 truncate max-w-[90%]">${escapeHtml(it.name || 'Product')}</span>
                            </div>`;
                    }
                    track.appendChild(slide);

                    // dot indicator (small circle), clickable to jump to that slide
                    if (dotsWrap && items.length > 1) {
                        const dot = document.createElement('span');
                        dot.className = 'top-dot' + (i === 0 ? ' is-active' : '');
                        dot.addEventListener('click', () => { goToSlide(i); startRotate(); });
                        dotsWrap.appendChild(dot);
                    }
                });

                idx = 0;
                showInfo();
            }

            function showInfo(){
                if (!items || items.length === 0) {
                    placeholder.innerHTML = '<span class="text-xs text-slate-400 font-medium">No sales recorded yet</span>';
                    return;
                }
                const top = items[idx] || items[0];
                const skuBadge = top.sku && top.sku !== 'N/A' ? `<span class="text-xs text-gray-500 font-mono font-normal">(${escapeHtml(top.sku)})</span>` : '';
                placeholder.innerHTML = `<div class="font-semibold text-gray-900">${escapeHtml(top.name)} ${skuBadge}</div><div class="text-gray-500">Rank ${idx + 1} • ${escapeHtml(top.category || '')}</div>`;
            }

            function startRotate(){
                if (rot) clearInterval(rot);
                if (!items || items.length <= 1) return;
                rot = setInterval(() => { goToSlide(idx + 1); }, 4000);
            }

            // Arrow button: fade to the next picture in the slideshow, then open the (centered) modal
            openBtn?.addEventListener('click', ()=>{
                if (items && items.length > 1) {
                    goToSlide(idx + 1);
                    startRotate();
                }
                openTopItemsModal(items);
            });

            window.renderTopSellingSlides = function(newItems) {
                items = newItems || [];
                renderSlides();
                startRotate();
            };

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r=>r.json()).then(data=>{
                items = data.top_items || data.topItems || [];
                renderSlides();
                startRotate();
            }).catch(()=>{ placeholder.innerHTML = '<span class="text-xs text-slate-400">Failed to load</span>'; });

         function openTopItemsModal(list){
            const modal = document.getElementById('topItemsModal');
            if (!modal) return;
            
            const localImages = loadLocalImages();
            const body = document.getElementById('topItemsModalBody');
            if (body) {
                body.innerHTML = '';
                const items = list || [];
                const itemCountEl = document.getElementById('topItemsModalItemCount');
                if (itemCountEl) itemCountEl.textContent = `${items.length} items`;

                items.forEach((it, i) => {
                    const imageUrl = resolveItemImage(it, localImages);
                    const imgHtml = imageUrl
                        ? `<div class="w-8 h-8 rounded-[6px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image:url('${imageUrl}');"></div>`
                        : `<div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>`;

                    const name = escapeHtml(it.name || 'Unknown Product');
                    const sku = escapeHtml(it.sku || 'N/A');
                    const category = escapeHtml(it.category || 'General');
                    const qty = it.qty ?? it.quantity ?? 0;
                    const revenue = it.revenue ? (new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'}).format(Number(it.revenue) || 0)) : '₱0.00';

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition';
                    tr.innerHTML = `
                        <td class="px-4 py-3 text-slate-700 font-bold text-xs text-center">${i+1}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                ${imgHtml}
                                <div>
                                    <div class="font-semibold text-slate-900 text-xs">${name}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">${sku}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-xs">${category}</td>
                        <td class="px-4 py-3 text-slate-900 font-semibold text-xs">${qty}</td>
                        <td class="px-4 py-3 text-slate-900 font-semibold text-xs">${revenue}</td>
                    `;
                    body.appendChild(tr);
                });
            }
            modal.classList.remove('hidden');
        }

        function closeTopItemsModal(){
            const modal = document.getElementById('topItemsModal');
            if (modal) modal.classList.add('hidden');
        }
        window.closeTopItemsModal = closeTopItemsModal;

            function escapeHtml(s){ return String(s||'').replace(/[&<>\"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
        });
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('salesOverviewOverlay');
            const viewReportBtn = document.getElementById('viewReportBtn');
            const viewReportLabel = document.getElementById('viewReportLabel');
            const viewReportArrow = document.getElementById('viewReportArrow');
            const calendarBtn = document.getElementById('calendarBtn');
            const closeSalesOverviewBtn = document.getElementById('closeSalesOverviewBtn');

            if (!overlay || !viewReportBtn) {
                return;
            }

            let isReportOpen = false;

            const playSlideOut = () => {
                overlay.classList.remove('overlay-slide-in');
                void overlay.offsetWidth;
                overlay.classList.add('overlay-slide-out');
            };

            const playSlideIn = () => {
                overlay.classList.remove('overlay-slide-out');
                void overlay.offsetWidth;
                overlay.classList.add('overlay-slide-in');
            };

            const openReport = () => {
                playSlideOut();
                try {
                    viewReportBtn.classList.add('is-active');
                    if (viewReportLabel) viewReportLabel.style.display = 'none';
                    if (viewReportArrow) viewReportArrow.style.transform = 'rotate(180deg)';
                    if (closeSalesOverviewBtn) closeSalesOverviewBtn.classList.remove('is-active');
                    // make sure the button is enabled
                    viewReportBtn.disabled = false;
                } catch (e) {
                    // swallow to avoid breaking future interactions
                    console.warn('openReport fallback:', e);
                }
                isReportOpen = true;
            };

            const closeReport = () => {
                playSlideIn();
                try {
                    viewReportBtn.classList.remove('is-active');
                    if (viewReportLabel) viewReportLabel.style.display = 'inline';
                    if (viewReportArrow) viewReportArrow.style.transform = 'rotate(0deg)';
                    // re-enable interactions explicitly in case overlay animations blocked pointer events
                    viewReportBtn.disabled = false;
                    viewReportBtn.classList.remove('is-hidden');
                } catch (e) {
                    console.warn('closeReport fallback:', e);
                }
                isReportOpen = false;
            };

            viewReportBtn.addEventListener('click', () => {
                if (!isReportOpen) {
                    openReport();
                } else {
                    closeReport();
                }
            });

            if (closeSalesOverviewBtn) {
                closeSalesOverviewBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    if (isReportOpen) {
                        closeSalesOverviewBtn.classList.add('is-active');
                        closeReport();
                    }
                });
            }

            // ===================== CALENDAR (pinned to the picture container, smooth open/close) =====================
            if (calendarBtn) {
                const calendarPanel = document.getElementById('calendarPanel');
                const calendarGrid = document.getElementById('calendarGrid');
                const monthYearDisplay = document.getElementById('monthYearDisplay');
                const prevMonthBtn = document.getElementById('prevMonthBtn');
                const nextMonthBtn = document.getElementById('nextMonthBtn');
                const closeCalendarBtn = document.getElementById('closeCalendarBtn');

                let currentMonth = new Date().getMonth();
                let currentYear = new Date().getFullYear();
                let selectedDate = null;
                const today = new Date();

                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];

                const generateCalendar = () => {
                    calendarGrid.innerHTML = '';
                    const firstDay = new Date(currentYear, currentMonth, 1);
                    const lastDay = new Date(currentYear, currentMonth + 1, 0);
                    const daysInMonth = lastDay.getDate();
                    const startingDayOfWeek = (firstDay.getDay() + 6) % 7;

                    monthYearDisplay.textContent = monthNames[currentMonth] + ' ' + currentYear;

                    const atMaxMonth = (currentYear === today.getFullYear() && currentMonth === 11);
                    if (nextMonthBtn) {
                        nextMonthBtn.disabled = atMaxMonth;
                        nextMonthBtn.style.opacity = atMaxMonth ? '0.3' : '1';
                        nextMonthBtn.style.cursor = atMaxMonth ? 'not-allowed' : 'pointer';
                        nextMonthBtn.style.pointerEvents = atMaxMonth ? 'none' : 'auto';
                    }

                    for (let i = 0; i < startingDayOfWeek; i++) {
                        const emptyCell = document.createElement('div');
                        calendarGrid.appendChild(emptyCell);
                    }

                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateCell = document.createElement('button');
                        dateCell.type = 'button';
                        dateCell.className = 'cal-day flex items-center justify-center mx-auto text-[11px] md:text-[12px] font-semibold rounded-full transition';
                        dateCell.style.width = '32px';
                        dateCell.style.height = '32px';
                        dateCell.style.minWidth = '32px';
                        dateCell.style.minHeight = '32px';
                        dateCell.textContent = day;

                        const cellDate = new Date(currentYear, currentMonth, day);
                        const isToday = cellDate.toDateString() === today.toDateString();
                        const isSelected = selectedDate && cellDate.toDateString() === selectedDate.toDateString();

                        if (isToday) {
                            dateCell.style.backgroundColor = '#0C7B93';
                            dateCell.style.color = '#ffffff';
                        } else if (isSelected) {
                            dateCell.style.backgroundColor = '#ffffff';
                            dateCell.style.color = '#000000';
                        } else {
                            dateCell.style.backgroundColor = 'transparent';
                            dateCell.style.color = 'rgba(255,255,255,0.85)';
                        }

                        dateCell.addEventListener('click', () => {
                            selectedDate = cellDate;
                            generateCalendar();
                            applySelectedDate();
                        });

                        calendarGrid.appendChild(dateCell);
                    }
                };

                const applySelectedDate = () => {
                    const date = selectedDate.toISOString().split('T')[0];
                    const url = document.getElementById('dashboard-root').dataset.dashboardUrl;
                    fetch(url + '?start_date=' + date + '&end_date=' + date, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.json())
                        .then(data => {
                            renderMetric(data.metrics?.sales, 'sales');
                            renderMetric(data.metrics?.transactions, 'transactions');
                            renderMetric(data.metrics?.profit, 'profit');
                            renderMetric(data.metrics?.items_sold, 'itemsSold');
                            renderSalesChart(data.sales_chart);
                            renderCategoryChart(data.category_chart);
                            if (typeof window.renderFastSlowMoving === 'function') {
                                window.renderFastSlowMoving(data.fast_moving, data.slow_moving, data.all_fast_moving, data.all_slow_moving);
                            }
                            if (typeof window.renderTopSellingSlides === 'function') {
                                window.renderTopSellingSlides(data.top_items || []);
                            } else if (typeof renderTopItems === 'function') {
                                renderTopItems(data.top_items);
                            }
                            if (typeof renderInventory === 'function') {
                                renderInventory(data.inventory);
                            }
                        });
                };

                const openCalendar = () => {
                    calendarBtn.classList.add('is-hidden');
                    if (viewReportBtn) viewReportBtn.classList.add('is-hidden');
                    calendarPanel.classList.remove('pointer-events-none');
                    void calendarPanel.offsetWidth;
                    calendarPanel.classList.add('is-open');
                };

                const closeCalendarModal = () => {
                    calendarPanel.classList.remove('is-open');
                    calendarBtn.classList.remove('is-hidden');
                    if (viewReportBtn) viewReportBtn.classList.remove('is-hidden');
                    window.setTimeout(() => {
                        calendarPanel.classList.add('pointer-events-none');
                    }, 350);
                };

                calendarBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    currentMonth = today.getMonth();
                    currentYear = today.getFullYear();
                    generateCalendar();
                    openCalendar();
                });

                prevMonthBtn.addEventListener('click', () => {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    generateCalendar();
                });

                nextMonthBtn.addEventListener('click', () => {
                    const atMaxMonth = (currentYear === today.getFullYear() && currentMonth === 11);
                    if (atMaxMonth) {
                        return;
                    }
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    generateCalendar();
                });

                if (closeCalendarBtn) {
                    closeCalendarBtn.addEventListener('click', closeCalendarModal);
                }
            }

        });
    </script>
    <script>
        // ══════════════════════════════════════════════════════════════
        // ── Inventory Alert Notification System (Toast + Bell) ───────
        // ══════════════════════════════════════════════════════════════

        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var _shownToastIds = {}; // Track which alerts have already been toasted this session
        var TOAST_DURATION = 9000; // 9 seconds

        // ── Utility ─────────────────────────────────────────────────
        function escHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        function timeAgo(dateStr) {
            if (!dateStr) return '';
            var now = new Date();
            var date = new Date(dateStr);
            var diffSec = Math.floor((now - date) / 1000);
            if (diffSec < 60) return 'Just now';
            var diffMin = Math.floor(diffSec / 60);
            if (diffMin < 60) return diffMin + (diffMin === 1 ? ' minute ago' : ' minutes ago');
            var diffHr = Math.floor(diffMin / 60);
            if (diffHr < 24) return diffHr + (diffHr === 1 ? ' hour ago' : ' hours ago');
            var diffDay = Math.floor(diffHr / 24);
            if (diffDay < 7) return diffDay + (diffDay === 1 ? ' day ago' : ' days ago');
            return date.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        // ══════════════════════════════════════════════════════════════
        // ── TOAST NOTIFICATIONS ──────────────────────────────────────
        // ══════════════════════════════════════════════════════════════
        function showInventoryToasts(alerts) {
            var currentUserRole = @json(auth()->user()->role ?? '');
            if (currentUserRole === 'cashier' || currentUserRole === 'warehouse_personnel') return;

            var container = document.getElementById('inventory-toast-container');
            if (!container) return;

            var panel = document.getElementById('notification-panel');
            if (panel && !panel.classList.contains('hidden')) {
                return; // Do not spawn toast popups while Notification Panel is open
            }

            var newAlerts = (Array.isArray(alerts) ? alerts : []).filter(function(a) {
                var toastKey = a.id + '_' + a.notification_type;
                return a.status === 'unread' && !_shownToastIds[toastKey];
            });

            // Show max 5 toasts at once to prevent overflow
            newAlerts.slice(0, 5).forEach(function(alert, idx) {
                var toastKey = alert.id + '_' + alert.notification_type;
                _shownToastIds[toastKey] = true;
                setTimeout(function() {
                    createToast(container, alert);
                }, idx * 200); // stagger by 200ms
            });
        }

        function createToast(container, alert) {
            var isCritical = alert.notification_type === 'out_of_stock';
            var toastClass = isCritical ? 'toast-critical' : 'toast-warning';
            var iconSVG = isCritical 
                ? '<svg class="w-4 h-4 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                : '<svg class="w-4 h-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
            var title = isCritical ? 'Out of Stock' : 'Low Stock';
            var stockText = isCritical ? '0 left' : alert.current_stock + ' remaining';
            
            var toast = document.createElement('div');
            toast.className = 'inv-toast ' + toastClass;
            toast.setAttribute('data-toast-alert-id', alert.id);
            toast.innerHTML =
                '<div class="flex items-start gap-3">' +
                    '<div class="inv-toast-icon">' +
                        iconSVG +
                    '</div>' +
                    '<div class="flex-1 min-w-0 pt-0.5">' +
                        '<div class="flex items-center justify-between mb-0.5">' +
                            '<p class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">' + title + '</p>' +
                            '<span class="text-[10px] font-bold text-slate-900">Just now</span>' +
                        '</div>' +
                        '<p class="text-sm font-semibold text-slate-900 truncate leading-tight mb-1">' + escHtml(alert.product_name) + '</p>' +
                        '<div class="flex items-center justify-between gap-2 text-xs text-slate-500 mb-3">' +
                            '<span class="truncate min-w-0" title="SKU: ' + escHtml(alert.sku) + '">SKU: ' + escHtml(alert.sku) + '</span>' +
                            '<span class="font-medium text-slate-600 whitespace-nowrap shrink-0">' + stockText + '</span>' +
                        '</div>' +
                        '<div class="flex items-center gap-2">' +
                            '<button type="button" class="inv-toast-btn inv-toast-btn-dismiss" data-toast-dismiss="' + alert.id + '">' +
                                'Dismiss' +
                            '</button>' +
                            '<a href="' + escHtml(alert.order_url || '/purchase-order/create') + '" class="inv-toast-btn inv-toast-btn-order">' +
                                'Order Now' +
                            '</a>' +
                        '</div>' +
                    '</div>' +
                    '<button type="button" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors" data-toast-close="' + alert.id + '" aria-label="Close">' +
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
                    '</button>' +
                '</div>' +
                '<div class="toast-progress" style="width:100%;"></div>';

            container.appendChild(toast);

            // Slide in
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    toast.classList.add('show');
                });
            });

            // Progress bar
            var progress = toast.querySelector('.toast-progress');
            if (progress) {
                progress.style.transitionDuration = TOAST_DURATION + 'ms';
                setTimeout(function() { progress.style.width = '0%'; }, 50);
            }

            // Auto-dismiss after timeout
            var autoTimer = setTimeout(function() {
                removeToast(toast);
            }, TOAST_DURATION);

            // Dismiss button — saves to notification center
            var dismissBtn = toast.querySelector('[data-toast-dismiss]');
            if (dismissBtn) {
                dismissBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearTimeout(autoTimer);
                    removeToast(toast);
                    // Mark as read (not dismissed from DB — keeps in notification center)
                    markNotificationRead(alert.id);
                });
            }

            // Close X button
            var closeBtn = toast.querySelector('[data-toast-close]');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearTimeout(autoTimer);
                    removeToast(toast);
                    markNotificationRead(alert.id);
                });
            }

            // Pause on hover
            toast.addEventListener('mouseenter', function() {
                clearTimeout(autoTimer);
                if (progress) {
                    progress.style.transitionDuration = '0ms';
                    progress.style.width = progress.getBoundingClientRect().width + 'px';
                }
            });
            toast.addEventListener('mouseleave', function() {
                var remaining = TOAST_DURATION * 0.4; // give 40% remaining time
                if (progress) {
                    progress.style.transitionDuration = remaining + 'ms';
                    progress.style.width = '0%';
                }
                autoTimer = setTimeout(function() {
                    removeToast(toast);
                }, remaining);
            });
        }

        function removeToast(toast) {
            if (!toast || toast._removing) return;
            toast._removing = true;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 500);
        }

        // Backward compatibility — called by dashboard.js
        function renderInventoryAlerts(alerts) {
            showInventoryToasts(alerts);
            loadNotificationCenter(); // Reload notification bell dropdown and badge in real-time!
        }

        // ══════════════════════════════════════════════════════════════
        // ── NOTIFICATION CENTER (Bell Icon) ──────────────────────────
        // ══════════════════════════════════════════════════════════════
        function loadNotificationCenter() {
            var currentUserRole = @json(auth()->user()->role ?? '');
            if (currentUserRole === 'cashier' || currentUserRole === 'warehouse_personnel') return;

            fetch('/api/inventory-notifications?limit=30', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderNotificationCenter(data.notifications || [], data.unread_count || 0);
            })
            .catch(function(err) { console.error('Failed to load notifications:', err); });
        }

        function renderNotificationCenter(notifications, unreadCount) {
            var panel = document.getElementById('notification-list');
            var empty = document.getElementById('notification-empty');
            var badge = document.getElementById('notification-badge');
            var centerBadge = document.getElementById('notif-center-unread-badge');
            if (!panel) return;

            // Update bell badge
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.classList.remove('hidden');
                    badge.style.display = 'inline-flex';
                    badge.style.alignItems = 'center';
                    badge.style.justifyContent = 'center';
                    if (unreadCount > 9) {
                        badge.style.width = 'auto';
                        badge.style.padding = '0 5px';
                    } else {
                        badge.style.width = '18px';
                        badge.style.padding = '0';
                    }
                } else {
                    badge.classList.add('hidden');
                    badge.style.display = 'none';
                }
            }
            if (centerBadge) {
                if (unreadCount > 0) {
                    centerBadge.textContent = unreadCount;
                    centerBadge.classList.remove('hidden');
                    centerBadge.style.display = 'inline-flex';
                    centerBadge.style.alignItems = 'center';
                    centerBadge.style.justifyContent = 'center';
                    if (unreadCount > 9) {
                        centerBadge.style.width = 'auto';
                        centerBadge.style.padding = '0 6px';
                    } else {
                        centerBadge.style.width = '20px';
                        centerBadge.style.padding = '0';
                    }
                } else {
                    centerBadge.classList.add('hidden');
                    centerBadge.style.display = 'none';
                }
            }

            panel.innerHTML = '';
            if (!notifications || notifications.length === 0) {
                if (empty) empty.classList.remove('hidden');
                return;
            }
            if (empty) empty.classList.add('hidden');

            notifications.forEach(function(n) {
                var isCritical = n.notification_type === 'out_of_stock';
                var iconSVG = isCritical 
                    ? '<svg class="w-4 h-4 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                    : '<svg class="w-4 h-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                var iconBgStyle = isCritical
                    ? 'background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.10) 100%); border: 1px solid rgba(239, 68, 68, 0.20); border-radius: 10px;'
                    : 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.10) 100%); border: 1px solid rgba(245, 158, 11, 0.20); border-radius: 10px;';
                var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                var statusClass = 'notif-item notif-item-' + n.status;
                var ago = timeAgo(n.created_at);
                var stockText = isCritical ? '0 left' : n.current_stock + ' remaining';
                var shortSku = n.sku.length > 15 ? n.sku.substring(0, 15) + '...' : n.sku;

                var item = document.createElement('div');
                item.className = statusClass + ' px-4 py-3 cursor-pointer';
                item.setAttribute('data-notif-id', n.id);

                item.innerHTML =
                    '<div class="flex items-start gap-3">' +
                        '<div class="flex-shrink-0 w-8 h-8 flex items-center justify-center" style="' + iconBgStyle + '">' +
                            iconSVG +
                        '</div>' +
                        '<div class="flex-1 min-w-0">' +
                            '<div class="flex items-center justify-between mb-0.5">' +
                                '<div class="flex items-center gap-1.5">' +
                                    '<span class="text-[10px] font-bold uppercase tracking-wider text-slate-700">' + typeLabel + '</span>' +
                                    (n.status === 'unread' ? '<span class="notif-status-dot unread"></span>' : '') +
                                '</div>' +
                                '<span class="text-[10px] ' + (n.status === 'unread' ? 'font-bold text-slate-900' : 'text-slate-400') + '">' + escHtml(ago) + '</span>' +
                            '</div>' +
                            '<p class="text-[13px] font-semibold text-slate-900 truncate mb-1">' + escHtml(n.product_name) + '</p>' +
                            '<div class="flex items-center justify-between">' +
                                '<div class="flex items-center gap-1.5 text-[11px] text-slate-500 whitespace-nowrap">' +
                                    '<span class="truncate max-w-[80px]">' + escHtml(shortSku) + '</span>' +
                                    '<span>&middot;</span>' +
                                    '<span class="font-medium whitespace-nowrap text-slate-600">' + stockText + '</span>' +
                                '</div>' +
                                (n.status !== 'resolved'
                                    ? '<a href="' + escHtml(n.order_url || '/purchase-order/create') + '" class="notif-btn flex-shrink-0" onclick="event.stopPropagation();">Order</a>'
                                    : '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-600 flex-shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Resolved</span>'
                                ) +
                            '</div>' +
                        '</div>' +
                    '</div>';

                // Mark as read on click
                if (n.status === 'unread') {
                    item.addEventListener('click', function() {
                        markNotificationRead(n.id);
                    });
                }

                panel.appendChild(item);
            });
        }

        function markNotificationRead(notifId) {
            fetch('/api/inventory-notifications/' + notifId + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(function() { loadNotificationCenter(); })
            .catch(function(err) { console.error('Mark read failed:', err); });
        }

        function markAllNotificationsRead() {
            // Immediately hide all badges
            var bellBadge = document.getElementById('notification-badge');
            var centerBadge = document.getElementById('notif-center-unread-badge');
            var headerBadge = document.getElementById('headerNotificationBadge');
            if (bellBadge) { bellBadge.classList.add('hidden'); bellBadge.style.display = 'none'; }
            if (centerBadge) { centerBadge.classList.add('hidden'); centerBadge.style.display = 'none'; }
            if (headerBadge) { headerBadge.classList.add('hidden'); headerBadge.style.display = 'none'; }

            fetch('/api/inventory-notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(function() {
                loadNotificationCenter();
                if (typeof loadHeaderNotifications === 'function') loadHeaderNotifications();
            })
            .catch(function(err) {
                console.error('Mark all read failed:', err);
                // Fallback: mark individually
                fetch('/api/inventory-notifications?limit=100', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var ids = (data.notifications || []).filter(function(n) { return n.status === 'unread'; }).map(function(n) { return n.id; });
                    return Promise.all(ids.map(function(id) {
                        return fetch('/api/inventory-notifications/' + id + '/read', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        });
                    }));
                })
                .then(function() {
                    loadNotificationCenter();
                    if (typeof loadHeaderNotifications === 'function') loadHeaderNotifications();
                });
            });
        }

        function toggleNotificationPanel(e) {
            e.stopPropagation();
            var panel = document.getElementById('notification-panel');
            if (!panel) return;

            // Close profile dropdown first
            var profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }

            // Close sales range dropdown if open
            var salesRangeDd = document.getElementById('salesRangeDropdown');
            var salesRangeChevron = document.getElementById('salesRangeChevron');
            if (salesRangeDd && !salesRangeDd.classList.contains('hidden')) {
                salesRangeDd.classList.add('hidden');
                if (salesRangeChevron) salesRangeChevron.style.transform = '';
            }

            // Dismiss all toast notifications
            var toastContainer = document.getElementById('inventory-toast-container');
            if (toastContainer) {
                toastContainer.innerHTML = '';
            }

            var isHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            if (isHidden) loadNotificationCenter();
        }

        function openAllNotificationsModal() {
            var modal = document.getElementById('all-notifications-modal');
            var list = document.getElementById('modal-notification-list');
            var count = document.getElementById('modal-notif-count');
            if (!modal || !list) return;

            // Hide the dropdown panel
            var panel = document.getElementById('notification-panel');
            if (panel) panel.classList.add('hidden');

            list.innerHTML = '<div class="p-6 text-center text-slate-500">Loading history…</div>';
            modal.classList.remove('hidden');

            fetch('/api/inventory-notifications?limit=250', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var notifications = data.notifications || [];
                if (count) count.textContent = notifications.length + ' notifications';
                
                if (notifications.length === 0) {
                    list.innerHTML = '<div class="p-6 text-center text-slate-400">No notification history.</div>';
                    return;
                }

                list.innerHTML = notifications.map(function(n) {
                    var isCritical = n.notification_type === 'out_of_stock';
                    var iconSVG = isCritical 
                        ? '<svg class="w-5 h-5 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                        : '<svg class="w-5 h-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                    var iconBgStyle = isCritical
                        ? 'background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.10) 100%); border: 1px solid rgba(239, 68, 68, 0.20);'
                        : 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.10) 100%); border: 1px solid rgba(245, 158, 11, 0.20);';
                    var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                    var ago = timeAgo(n.created_at);
                    var stockText = isCritical ? '0 left' : n.current_stock + ' remaining';
                    var statusClass = 'notif-item notif-item-' + n.status;

                    return '<div class="' + statusClass + ' p-4 rounded-[14px] border border-slate-200/80 bg-white hover:border-[#6EC1D1]/60 shadow-sm flex items-start justify-between gap-4 transition-all">' +
                        '<div class="flex items-start gap-4">' +
                            '<div class="flex-shrink-0 w-10 h-10 rounded-[10px] flex items-center justify-center" style="' + iconBgStyle + '">' +
                                iconSVG +
                            '</div>' +
                            '<div>' +
                                '<div class="flex items-center gap-2 mb-0.5">' +
                                    '<span class="text-[10px] font-bold uppercase tracking-wider text-slate-700">' + typeLabel + '</span>' +
                                    (n.status === 'unread' ? '<span class="notif-status-dot unread"></span>' : '') +
                                '</div>' +
                                '<p class="text-[14px] font-semibold text-slate-900 mt-1">' + escHtml(n.product_name) + '</p>' +
                                '<div class="flex items-center gap-2 mt-1">' +
                                    '<span class="text-xs text-slate-500">SKU: ' + escHtml(n.sku) + '</span>' +
                                    '<span class="text-slate-400">&middot;</span>' +
                                    '<span class="text-xs font-medium text-slate-600">' + stockText + '</span>' +
                                '</div>' +
                                '<p class="text-[11px] ' + (n.status === 'unread' ? 'font-bold text-slate-900' : 'text-slate-400') + ' mt-1.5">' + escHtml(ago) + '</p>' +
                            '</div>' +
                        '</div>' +
                        (n.status !== 'resolved'
                            ? '<a href="' + escHtml(n.order_url || '/purchase-order/create') + '" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#6EC1D1] text-black text-xs font-medium hover:bg-[#59b2c2] transition-colors">Order Now</a>'
                            : '<span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Resolved</span>'
                        ) +
                    '</div>';
                }).join('');
            })
            .catch(function(err) {
                list.innerHTML = '<div class="p-6 text-center text-rose-500">Failed to load notification history.</div>';
            });
        }

        function closeAllNotificationsModal() {
            var modal = document.getElementById('all-notifications-modal');
            if (modal) modal.classList.add('hidden');
        }

        // Close panel on outside click
        document.addEventListener('click', function(e) {
            var wrapper = document.getElementById('notification-bell-wrapper');
            var panel   = document.getElementById('notification-panel');
            if (panel && wrapper && !wrapper.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });

        // Close notification panel & dismiss all toast alerts when profile button is clicked
        document.getElementById('dashboardProfileButton')?.addEventListener('click', function() {
            var notifPanel = document.getElementById('notification-panel');
            if (notifPanel && !notifPanel.classList.contains('hidden')) {
                notifPanel.classList.add('hidden');
            }
            var salesRangeDd = document.getElementById('salesRangeDropdown');
            var salesRangeChevron = document.getElementById('salesRangeChevron');
            if (salesRangeDd && !salesRangeDd.classList.contains('hidden')) {
                salesRangeDd.classList.add('hidden');
                if (salesRangeChevron) salesRangeChevron.style.transform = '';
            }
            var toastContainer = document.getElementById('inventory-toast-container');
            if (toastContainer) {
                toastContainer.innerHTML = '';
            }
            document.querySelectorAll('[data-toast-notification]').forEach(function(t) {
                t.remove();
            });
        });

        // Initial load
        loadNotificationCenter();

        // ══════════════════════════════════════════════════════════════
        // ── FAST & SLOW MOVING VIEW ALL MODAL ────────────────────────
        // ══════════════════════════════════════════════════════════════
        var _fsModalCurrentTab = 'fast';

        function openFastSlowModal() {
            var modal = document.getElementById('fast-slow-modal');
            if (modal) modal.classList.remove('hidden');
            switchFastSlowModalTab(_fsModalCurrentTab || 'fast');
        }
        window.openFastSlowModal = openFastSlowModal;

        function closeFastSlowModal() {
            var modal = document.getElementById('fast-slow-modal');
            if (modal) modal.classList.add('hidden');
        }
        window.closeFastSlowModal = closeFastSlowModal;

        function switchFastSlowModalTab(tab) {
            _fsModalCurrentTab = tab;
            var fastTab = document.getElementById('fsModalTabFast');
            var slowTab = document.getElementById('fsModalTabSlow');
            var revenueCol = document.getElementById('fsModalRevenueCol');

            var allFast = window._fsModalAllFast || [];
            var allSlow = window._fsModalAllSlow || [];

            if (tab === 'fast') {
                if (fastTab) fastTab.className = 'px-4 py-3 text-sm font-bold text-slate-900 transition cursor-pointer';
                if (slowTab) slowTab.className = 'px-4 py-3 text-sm font-semibold text-slate-400 hover:text-slate-700 transition cursor-pointer';
                if (revenueCol) revenueCol.style.display = '';
                renderFastSlowModalTable(allFast, true);
            } else {
                if (fastTab) fastTab.className = 'px-4 py-3 text-sm font-semibold text-slate-400 hover:text-slate-700 transition cursor-pointer';
                if (slowTab) slowTab.className = 'px-4 py-3 text-sm font-bold text-slate-900 transition cursor-pointer';
                if (revenueCol) revenueCol.style.display = 'none';
                renderFastSlowModalTable(allSlow, false);
            }
        }
        window.switchFastSlowModalTab = switchFastSlowModalTab;

        function getFsModalProductImage(item) {
            if (!item) return null;
            if (item.image) return item.image;
            try {
                var stored = localStorage.getItem('posProductImages');
                if (stored) {
                    var images = JSON.parse(stored);
                    var productId = item.id || item.product_id;
                    if (productId && images[productId]) return images[productId];
                    if (item.sku && images[item.sku]) return images[item.sku];
                    if (item.name && images[item.name]) return images[item.name];

                    var keys = Object.keys(images);
                    if (item.sku) {
                        var matchSku = keys.find(function(k) { return k.toLowerCase() === String(item.sku).toLowerCase(); });
                        if (matchSku) return images[matchSku];
                    }
                    if (item.name) {
                        var matchName = keys.find(function(k) { return k.toLowerCase() === String(item.name).toLowerCase(); });
                        if (matchName) return images[matchName];
                    }
                }
            } catch (e) {}
            return null;
        }

        function renderFastSlowModalTable(items, showRevenue) {
            var tbody = document.getElementById('fsModalTableBody');
            var countEl = document.getElementById('fsModalItemCount');
            if (!tbody) return;

            if (!items || !items.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No data available</td></tr>';
                if (countEl) countEl.textContent = '0 items';
                return;
            }

            var currency = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
            var maxQty = Math.max.apply(null, items.map(function(i) { return Number(i.quantity || i.qty || 1); }));
            maxQty = Math.max(maxQty, 1);

            tbody.innerHTML = items.map(function(item, idx) {
                var imageUrl = getFsModalProductImage(item);
                var imgHtml = imageUrl
                    ? '<div class="w-8 h-8 rounded-[6px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image:url(\'' + imageUrl + '\');"></div>'
                    : '<div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>';

                var name = (item.name || 'Unknown Product').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                var sku = (item.sku || 'N/A').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                var category = (item.category || 'General').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                var qty = item.quantity ?? item.qty ?? 0;
                var revenue = item.revenue ? currency.format(Number(item.revenue) || 0) : '₱0.00';

                var revenueCell = showRevenue
                    ? '<td class="px-4 py-3 text-slate-900 font-semibold text-xs">' + revenue + '</td>'
                    : '<td class="px-4 py-3 text-left" style="display:none;"></td>';

                return '<tr class="hover:bg-slate-50 transition">' +
                    '<td class="px-4 py-3 text-slate-700 font-bold text-xs text-center">' + (idx + 1) + '</td>' +
                    '<td class="px-4 py-3">' +
                        '<div class="flex items-center gap-3">' +
                            imgHtml +
                            '<div>' +
                                '<div class="font-semibold text-slate-900 text-xs">' + name + '</div>' +
                                '<div class="text-[11px] text-slate-400 font-mono mt-0.5">' + sku + '</div>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td class="px-4 py-3 text-slate-600 text-xs">' + category + '</td>' +
                    '<td class="px-4 py-3 text-slate-900 font-semibold text-xs">' + qty + '</td>' +
                    revenueCell +
                    '</tr>';
            }).join('');

            if (countEl) countEl.textContent = items.length + ' item' + (items.length !== 1 ? 's' : '');
        }
    </script>
@endpush
</x-layouts.app>
