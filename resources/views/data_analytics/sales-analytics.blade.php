<x-layouts.app :title="__('Sales Analytics')">
    @php
        $quickStats = $quickStats ?? [
            'total_inventory_value' => 0,
            'average_unit_price' => 0,
            'total_units_in_stock' => 0,
            'healthy_skus' => 0,
            'low_stock_skus' => 0,
            'out_of_stock_skus' => 0,
        ];

        $salesTrend = $salesTrend ?? [
            'monthly' => [
                'labels' => ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                'values' => [0, 0, 11800, 14200, 16800, 18400],
            ],
            'weekly' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                'values' => [0, 0, 0, 0, 0],
            ],
            'daily' => [
                'labels' => ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25', 'Apr 30'],
                'values' => [11800, 14200, 13500, 15800, 17200, 16800, 18400],
            ],
        ];

        $categoryBreakdown = $categoryBreakdown ?? [
            'labels' => ['Exhausts', 'Helmets', 'Tires', 'Brakes', 'Accessories'],
            'values' => [34, 24, 18, 12, 12],
            'shares' => [34, 24, 18, 12, 12],
            'formatted' => ['₱34.00', '₱24.00', '₱18.00', '₱12.00', '₱12.00'],
        ];

        $categoryBreakdown['formatted'] = $categoryBreakdown['formatted'] ?? array_map(fn($value) => '₱' . number_format((float) $value, 2), $categoryBreakdown['values'] ?? []);
        $categoryBreakdown['shares'] = $categoryBreakdown['shares'] ?? array_fill(0, count($categoryBreakdown['labels'] ?? []), 0);



        $topProducts = $topProducts ?? [
            ['rank' => 1, 'name' => 'Akrapovic Exhaust', 'category' => 'Exhausts', 'qty' => 132, 'revenue' => '₱15,840'],
            ['rank' => 2, 'name' => 'SHARK EVO Helmet', 'category' => 'Helmets', 'qty' => 98, 'revenue' => '₱11,760'],
            ['rank' => 3, 'name' => 'Dunlop Q3+ Tire', 'category' => 'Tires', 'qty' => 84, 'revenue' => '₱10,080'],
            ['rank' => 4, 'name' => 'Brembo Brake Pads', 'category' => 'Brakes', 'qty' => 65, 'revenue' => '₱5,850'],
            ['rank' => 5, 'name' => 'Cub Battery', 'category' => 'Accessories', 'qty' => 58, 'revenue' => '₱4,640'],
        ];
    @endphp

    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Sales Analytics</h1>
                    <p class="text-xs text-slate-500 mt-1">Track revenue performance, product demand, and market momentum in a compact analytics workspace.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="relative">
                        <input type="text" id="globalDateRange" readonly
                               class="inline-flex items-center justify-center rounded-[12px] border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-900 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#00fff2] transition cursor-pointer w-[260px]"
                               placeholder="Select date range">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-base">📅</span>
                    </div>
                    <a href="{{ route('analytics.sales.export') }}" class="inline-flex items-center gap-1.5 justify-center rounded-[12px] border border-[#00fff2]/40 bg-[#00fff2] px-4 py-2 text-xs font-semibold text-black shadow-sm hover:bg-[#00e6da] transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Report
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Inventory value</p>
                        <p class="text-xl font-semibold text-slate-900">₱{{ number_format($quickStats['total_inventory_value'], 2) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Current value of stocked items across active inventory.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 1v22" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Average unit price</p>
                        <p class="text-xl font-semibold text-slate-900">₱{{ number_format($quickStats['average_unit_price'], 2) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Average per-unit price for products currently in stock.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                            <line x1="7" y1="7" x2="7.01" y2="7" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Units in stock</p>
                        <p class="text-xl font-semibold text-slate-900">{{ number_format($quickStats['total_units_in_stock']) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Total quantity of items currently available for sale.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Healthy SKUs</p>
                        <p class="text-xl font-semibold text-slate-900">{{ number_format($quickStats['healthy_skus']) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">SKUs with stock above reorder threshold and ready to sell.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Low stock SKUs</p>
                        <p class="text-xl font-semibold text-slate-900">{{ number_format($quickStats['low_stock_skus']) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Items at or below reorder level that need replenishment soon.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Out of stock SKUs</p>
                        <p class="text-xl font-semibold text-slate-900">{{ number_format($quickStats['out_of_stock_skus']) }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Products currently unavailable that need immediate restock.</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 items-start gap-4">
            <div class="xl:col-span-2 rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Sales trend</h2>
                        <p class="text-xs text-slate-500 mt-1">Revenue progression across the selected date range.</p>
                    </div>
                    <div class="inline-flex rounded-[12px] border border-slate-200 bg-slate-50 p-1 text-[11px] font-medium text-slate-600">
                        <button type="button" data-range="monthly" class="sales-trend-range-btn px-2.5 py-1 rounded-[8px] bg-[#0f172a] text-white font-semibold">Monthly</button>
                        <button type="button" data-range="weekly" class="sales-trend-range-btn px-2.5 py-1 rounded-[8px] hover:bg-slate-100 font-semibold">Weekly</button>
                        <button type="button" data-range="daily" class="sales-trend-range-btn px-2.5 py-1 rounded-[8px] hover:bg-slate-100 font-semibold">Daily</button>
                    </div>
                </div>
                <div class="mt-4 h-40">
                    <canvas id="salesTrendChart" class="h-full w-full"></canvas>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm relative" id="categoryDistributionSection">
                <div id="categoryLoadingOverlay" class="hidden absolute inset-0 bg-white/80 rounded-[20px] z-10 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2"><svg class="animate-spin h-6 w-6 text-[#105f68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Loading…</span></div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Category distribution</h2>
                        <p class="text-xs text-slate-500 mt-1">Revenue contribution per product category.</p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-[#105f68]">Revenue</span>
                </div>
                <div class="mt-4 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-start">
                    <div class="h-52 w-full">
                        <canvas id="categoryChart" class="h-full w-full"></canvas>
                    </div>
                    <div id="categoryLegend" class="max-h-52 overflow-y-auto pr-1 space-y-2 text-sm">
                        @php
                            $legendColors = ['bg-teal-500','bg-emerald-500','bg-amber-500','bg-sky-500','bg-rose-500','bg-violet-500','bg-cyan-500','bg-lime-500','bg-fuchsia-500','bg-orange-500'];
                        @endphp
                        @foreach($categoryBreakdown['labels'] as $index => $label)
                            <div class="flex items-center gap-3 rounded-[12px] border border-slate-200 bg-slate-50 p-2.5">
                                <span class="h-2.5 w-2.5 rounded-full {{ $legendColors[$index % count($legendColors)] }}"></span>
                                <div>
                                    <p class="font-semibold text-slate-900 text-xs">{{ $label }}</p>
                                    <p class="text-slate-500 text-[11px]">
                                        {{ data_get($categoryBreakdown, 'formatted.' . $index, '—') }} • {{ data_get($categoryBreakdown, 'shares.' . $index, 0) }}%
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden" id="topProductsSection">
                <div id="topProductsLoadingOverlay" class="hidden absolute inset-0 bg-white/80 rounded-[20px] z-10 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2"><svg class="animate-spin h-6 w-6 text-[#105f68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Loading…</span></div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Top selling products</h2>
                        <p class="text-[11px] text-slate-500 mt-1">The best performing SKUs by revenue and volume.</p>
                    </div>
                    <button class="inline-flex items-center gap-2 rounded-[12px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition" id="viewAllTopProductsBtn">View all</button>
                </div>
                <div class="mt-4 overflow-x-auto rounded-[14px] border border-slate-200">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a]">
                            <tr>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Rank</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Category</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Units</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="topProductsBody">
                            @forelse($topProducts as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-2.5 font-semibold text-slate-900">{{ $product['rank'] }}</td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $product['sku'] ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-600">{{ $product['category'] }}</td>
                                    <td class="px-3 py-2.5 text-slate-900">{{ $product['qty'] }}</td>
                                    <td class="px-3 py-2.5 font-semibold text-slate-900">{{ $product['revenue'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-slate-500">No sales data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden" id="fastMovingSection">
                <div id="fastMovingLoadingOverlay" class="hidden absolute inset-0 bg-white/80 rounded-[20px] z-10 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2"><svg class="animate-spin h-6 w-6 text-[#105f68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Loading…</span></div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Fast-moving products</h2>
                        <p class="text-[11px] text-slate-500 mt-1">Top 5 best-selling products by quantity.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto rounded-[14px] border border-slate-200">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a]">
                            <tr>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product</th>
                                <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Quantity Sold</th>
                                <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="fastMovingProductsBody">
                            @forelse($fastMoving as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-2.5">
                                        <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $product['sku'] ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-slate-900">{{ $product['qty'] }}</td>
                                    <td class="px-3 py-2.5 text-right font-semibold text-slate-900">{{ $product['revenue'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-center text-slate-500">No sales data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden" id="slowMovingSection">
                <div id="slowMovingLoadingOverlay" class="hidden absolute inset-0 bg-white/80 rounded-[20px] z-10 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2"><svg class="animate-spin h-6 w-6 text-[#105f68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Loading…</span></div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Slow-moving products</h2>
                        <p class="text-[11px] text-slate-500 mt-1">Bottom 5 least-selling products by quantity.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto rounded-[14px] border border-slate-200">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a]">
                            <tr>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product</th>
                                <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="slowMovingProductsBody">
                            @forelse($slowMoving as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-2.5">
                                        <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $product['sku'] ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-3 py-2.5 text-right text-slate-900">{{ $product['qty'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-4 text-center text-slate-500">No sales data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-3 rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Executive summary</h2>
                        <p class="text-xs text-slate-500 mt-1">Key observations and recommended actions for the next period.</p>
                    </div>
                    <div class="rounded-[12px] bg-slate-50 border border-slate-200 px-3 py-2 text-xs text-slate-600">
                        <span class="font-semibold text-slate-900">High priority:</span> Focus on helmet campaigns for continued revenue growth.
                    </div>
                </div>
                <div class="mt-4 grid gap-3 lg:grid-cols-3">
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#105f68]">Opportunity</p>
                        <p class="mt-2 text-xs text-slate-700">Boost cross-sell bundles for high-margin accessories during weekend promotions.</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#105f68]">Attention</p>
                        <p class="mt-2 text-xs text-slate-700">Review Mindanao stock replenishment after a strong 7.1% lift in sales demand.</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#105f68]">Next step</p>
                        <p class="mt-2 text-xs text-slate-700">Align pricing and promotions ahead of next month’s seasonal demand spike.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Flatpickr CDN --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <style>
            /* Senior UI/UX Datepicker Customization */
            .flatpickr-calendar {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
                border-radius: 24px;
                padding: 16px;
                font-family: inherit;
                width: 320px !important;
                opacity: 0;
                transform: translateY(10px) scale(0.95);
                transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .flatpickr-calendar.animate.open {
                animation: fpFadeInDown 300ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @keyframes fpFadeInDown {
                from { opacity: 0; transform: translateY(10px) scale(0.95); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            .flatpickr-calendar::before, .flatpickr-calendar::after {
                display: none !important;
            }
            
            /* Layout fixes for Flatpickr default constraints */
            .flatpickr-innerContainer, .flatpickr-rContainer, .dayContainer, .flatpickr-days {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
            }

            .flatpickr-months {
                margin-bottom: 12px;
                position: relative;
                padding: 0 8px;
            }
            .flatpickr-month {
                height: 36px !important;
            }
            .flatpickr-current-month {
                font-size: 15px !important;
                font-weight: 700 !important;
                color: #0f172a !important;
                padding: 0 !important;
                height: 36px !important;
                line-height: 36px !important;
                left: 0 !important;
                width: 100% !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
            }
            .flatpickr-current-month input.cur-year {
                font-weight: 700 !important;
                color: #64748b !important;
            }
            .flatpickr-current-month span.cur-month {
                font-weight: 700 !important;
                color: #0f172a !important;
                margin-left: 4px;
            }
            .flatpickr-prev-month, .flatpickr-next-month {
                position: absolute !important;
                top: 2px !important;
                height: 32px !important;
                width: 32px !important;
                border-radius: 10px !important;
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
                transition: all 0.2s ease;
                z-index: 10;
            }
            .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
                background: #f1f5f9 !important;
                border-color: #cbd5e1 !important;
                cursor: pointer;
            }
            .flatpickr-prev-month svg, .flatpickr-next-month svg {
                width: 12px;
                height: 12px;
                fill: #475569 !important;
            }
            .flatpickr-prev-month { left: 8px !important; }
            .flatpickr-next-month { right: 8px !important; }
            
            .flatpickr-weekdays {
                height: 28px !important;
                margin-bottom: 4px;
                width: 100% !important;
            }
            .flatpickr-weekdaycontainer {
                display: grid !important;
                grid-template-columns: repeat(7, 1fr);
                width: 100% !important;
            }
            span.flatpickr-weekday {
                color: #94a3b8 !important;
                font-weight: 700 !important;
                font-size: 11px !important;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 100% !important;
            }
            
            .dayContainer {
                display: grid !important;
                grid-template-columns: repeat(7, 1fr) !important;
                gap: 4px;
                justify-content: stretch;
            }
            .flatpickr-day {
                width: 100% !important; /* Forces it to fill the grid cell instead of 14% of the cell */
                max-width: 100% !important;
                height: 36px !important;
                line-height: 36px !important;
                border-radius: 10px !important;
                font-weight: 500 !important;
                color: #334155 !important;
                font-size: 13px !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .flatpickr-day:hover {
                background: #f1f5f9 !important;
                color: #0f172a !important;
            }
            .flatpickr-day.inRange,
            .flatpickr-day.prevMonthDay.inRange,
            .flatpickr-day.nextMonthDay.inRange,
            .flatpickr-day.today.inRange,
            .flatpickr-day.prevMonthDay.today.inRange,
            .flatpickr-day.nextMonthDay.today.inRange {
                background: #f8fafc !important;
                color: #0f172a !important;
                border-radius: 0 !important;
                box-shadow: -4px 0 0 #f8fafc, 4px 0 0 #f8fafc !important;
            }
            .flatpickr-day.selected,
            .flatpickr-day.startRange,
            .flatpickr-day.endRange,
            .flatpickr-day.selected.inRange,
            .flatpickr-day.startRange.inRange,
            .flatpickr-day.endRange.inRange,
            .flatpickr-day.selected:focus,
            .flatpickr-day.startRange:focus,
            .flatpickr-day.endRange:focus,
            .flatpickr-day.selected:hover,
            .flatpickr-day.startRange:hover,
            .flatpickr-day.endRange:hover,
            .flatpickr-day.selected.prevMonthDay,
            .flatpickr-day.startRange.prevMonthDay,
            .flatpickr-day.endRange.prevMonthDay,
            .flatpickr-day.selected.nextMonthDay,
            .flatpickr-day.startRange.nextMonthDay,
            .flatpickr-day.endRange.nextMonthDay {
                background: #0f172a !important;
                color: #ffffff !important;
                border-radius: 10px !important;
                box-shadow: 0 4px 10px rgba(15, 23, 42, 0.25) !important;
                z-index: 2;
            }
            .flatpickr-day.startRange {
                box-shadow: 4px 0 0 #f8fafc, 0 4px 10px rgba(15, 23, 42, 0.25) !important;
            }
            .flatpickr-day.endRange {
                box-shadow: -4px 0 0 #f8fafc, 0 4px 10px rgba(15, 23, 42, 0.25) !important;
            }
            .flatpickr-day.startRange.endRange {
                box-shadow: 0 4px 10px rgba(15, 23, 42, 0.25) !important;
            }
            .flatpickr-day.today {
                border: 1px solid #e2e8f0 !important;
                background: #ffffff !important;
                color: #0f172a !important;
            }
            .flatpickr-day.flatpickr-disabled {
                color: #cbd5e1 !important;
            }
            .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay {
                color: #94a3b8 !important;
                font-weight: 500 !important;
            }
        </style>
        <script>
            // ═══════════════════════════════════════════
            // SALES TREND (independent — NOT date-filtered)
            // ═══════════════════════════════════════════
            const salesTrendRangeButtons = document.querySelectorAll('.sales-trend-range-btn');
            const salesTrendCtx = document.getElementById('salesTrendChart');
            const posSalesStorageKey = 'posTransactionHistory';

            const defaultSalesTrendData = {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    values: [0, 0, 0, 0, 0, 0],
                },
                weekly: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                    values: [0, 0, 0, 0, 0],
                },
                daily: {
                    labels: ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25', 'Apr 30'],
                    values: [0, 0, 0, 0, 0, 0, 0],
                }
            };

            const parseLocalTransactionHistory = () => {
                try {
                    const stored = localStorage.getItem(posSalesStorageKey);
                    if (!stored) return [];
                    const parsed = JSON.parse(stored);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    console.error('Could not read POS transaction history', error);
                    return [];
                }
            };

            const salesFromTransactions = (transactions) => {
                return transactions
                    .map((transaction) => ({
                        date: transaction.createdAt ? new Date(transaction.createdAt) : null,
                        total: Number(transaction.total) || 0,
                    }))
                    .filter((transaction) => transaction.date instanceof Date && !Number.isNaN(transaction.date.getTime()) && transaction.total > 0);
            };

            const buildTrendData = () => {
                const transactions = salesFromTransactions(parseLocalTransactionHistory());
                if (!transactions.length) {
                    return defaultSalesTrendData;
                }

                const now = new Date();
                const monthlyLabels = [];
                const monthlyValues = [];
                for (let index = 5; index >= 0; index -= 1) {
                    const month = new Date(now.getFullYear(), now.getMonth() - index, 1);
                    monthlyLabels.push(month.toLocaleString('en-US', { month: 'short' }));
                    monthlyValues.push(0);
                }

                const dailyLabels = [];
                const dailyValues = [];
                for (let index = 6; index >= 0; index -= 1) {
                    const day = new Date(now.getFullYear(), now.getMonth(), now.getDate() - index);
                    dailyLabels.push(day.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                    dailyValues.push(0);
                }

                const weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
                const weeklyValues = [0, 0, 0, 0, 0];
                const weekStart = new Date(now);
                weekStart.setHours(0, 0, 0, 0);
                weekStart.setDate(weekStart.getDate() - 34);

                transactions.forEach(({ date, total }) => {
                    const diffMs = date.getTime() - weekStart.getTime();
                    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

                    const monthLabel = date.toLocaleString('en-US', { month: 'short' });
                    const monthIndex = monthlyLabels.indexOf(monthLabel);
                    if (monthIndex !== -1) {
                        monthlyValues[monthIndex] += total;
                    }

                    const dayLabel = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    const dayIndex = dailyLabels.indexOf(dayLabel);
                    if (dayIndex !== -1) {
                        dailyValues[dayIndex] += total;
                    }

                    if (diffDays >= 0 && diffDays < 35) {
                        const weekIndex = Math.min(4, Math.floor(diffDays / 7));
                        weeklyValues[weekIndex] += total;
                    }
                });

                return {
                    monthly: { labels: monthlyLabels, values: monthlyValues.map((value) => Number(value.toFixed(2))) },
                    weekly: { labels: weeklyLabels, values: weeklyValues.map((value) => Number(value.toFixed(2))) },
                    daily: { labels: dailyLabels, values: dailyValues.map((value) => Number(value.toFixed(2))) },
                };
            };

            const serverSalesTrend = @json($salesTrend ?? null);
            const salesTrendData = (() => {
                const localData = buildTrendData();
                const serverData = serverSalesTrend && serverSalesTrend.monthly && Array.isArray(serverSalesTrend.monthly.values)
                    && serverSalesTrend.weekly && Array.isArray(serverSalesTrend.weekly.values)
                    && serverSalesTrend.daily && Array.isArray(serverSalesTrend.daily.values)
                    ? serverSalesTrend
                    : null;

                if (serverData) {
                    return serverData;
                }

                return localData;
            })();

            const salesTrendChartConfig = {
                type: 'line',
                data: {
                    labels: salesTrendData.monthly.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: salesTrendData.monthly.values,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.12)',
                        pointBackgroundColor: '#0f766e',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#475569' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: {
                                color: '#475569',
                                callback: (value) => '₱' + value.toLocaleString()
                            }
                        }
                    }
                }
            };

            let salesTrendChart;
            if (salesTrendCtx) {
                salesTrendChart = new Chart(salesTrendCtx, salesTrendChartConfig);
            }

            const setActiveSalesTrendButton = (activeRange) => {
                salesTrendRangeButtons.forEach((button) => {
                    const isActive = button.dataset.range === activeRange;
                    button.classList.toggle('bg-slate-900', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('shadow-sm', isActive);
                    button.classList.toggle('bg-slate-100', !isActive);
                    button.classList.toggle('text-slate-600', !isActive);
                });
            };

            const updateSalesTrendChart = (range) => {
                const nextData = salesTrendData[range];
                if (!salesTrendChart || !nextData) {
                    return;
                }
                salesTrendChart.data.labels = nextData.labels;
                salesTrendChart.data.datasets[0].data = nextData.values;
                salesTrendChart.update();
                setActiveSalesTrendButton(range);
            };

            salesTrendRangeButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    updateSalesTrendChart(button.dataset.range);
                });
            });

            setActiveSalesTrendButton('monthly');

            // ═══════════════════════════════════════════
            // CATEGORY CHART (initial render from server)
            // ═══════════════════════════════════════════
            const chartColors = ['#0f766e', '#16a34a', '#f59e0b', '#0ea5e9', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#ec4899', '#f97316'];
            const legendColorClasses = ['bg-teal-500','bg-emerald-500','bg-amber-500','bg-sky-500','bg-rose-500','bg-violet-500','bg-cyan-500','bg-lime-500','bg-fuchsia-500','bg-orange-500'];
            let categoryChartInstance = null;

            const initCategoryChart = (labels, values) => {
                const categoryCtx = document.getElementById('categoryChart');
                if (!categoryCtx) return;

                if (categoryChartInstance) {
                    categoryChartInstance.destroy();
                    categoryChartInstance = null;
                }

                if (!labels.length) return;

                const backgroundColors = labels.map((_, index) => chartColors[index % chartColors.length]);
                categoryChartInstance = new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: backgroundColors,
                            borderColor: '#ffffff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((sum, item) => sum + Number(item || 0), 0);
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                        return `${context.label}: ₱${Number(value).toLocaleString()} (${percent}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            };

            // Initial category chart from server data
            initCategoryChart(@json($categoryBreakdown['labels']), @json($categoryBreakdown['values']));

            // ═══════════════════════════════════════════
            // GLOBAL DATE RANGE CALENDAR + WIDGET REFRESH
            // ═══════════════════════════════════════════
            const loadingOverlayIds = [
                'categoryLoadingOverlay',
                'topProductsLoadingOverlay',
                'fastMovingLoadingOverlay',
                'slowMovingLoadingOverlay',
            ];

            const showLoadingOverlays = () => {
                loadingOverlayIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) { el.classList.remove('hidden'); el.classList.add('flex'); }
                });
            };

            const hideLoadingOverlays = () => {
                loadingOverlayIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) { el.classList.add('hidden'); el.classList.remove('flex'); }
                });
            };

            const emptyStateRow = (colspan, message) =>
                `<tr><td colspan="${colspan}" class="px-3 py-6 text-center text-slate-400 text-sm">${message}</td></tr>`;

            const renderCategoryLegend = (labels, formatted, shares) => {
                const legend = document.getElementById('categoryLegend');
                if (!legend) return;

                if (!labels.length) {
                    legend.innerHTML = '<p class="text-sm text-slate-400 text-center py-4">No sales data available for the selected date range.</p>';
                    return;
                }

                legend.innerHTML = labels.map((label, index) => {
                    const colorClass = legendColorClasses[index % legendColorClasses.length];
                    return `
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <span class="h-2.5 w-2.5 rounded-full ${colorClass}"></span>
                            <div>
                                <p class="font-semibold text-slate-900">${label}</p>
                                <p class="text-slate-500">${formatted[index] || '—'} • ${shares[index] || 0}%</p>
                            </div>
                        </div>
                    `;
                }).join('');
            };

            const renderTopProducts = (products) => {
                const tbody = document.getElementById('topProductsBody');
                if (!tbody) return;

                if (!products.length) {
                    tbody.innerHTML = emptyStateRow(5, 'No sales data available for the selected date range.');
                    return;
                }

                tbody.innerHTML = products.map(p => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-3 py-3 font-semibold text-slate-900">${p.rank}</td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-slate-900">${p.name}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">${p.sku || 'N/A'}</div>
                        </td>
                        <td class="px-3 py-3 text-slate-600">${p.category}</td>
                        <td class="px-3 py-3 text-slate-900">${p.qty}</td>
                        <td class="px-3 py-3 font-semibold text-slate-900">${p.revenue}</td>
                    </tr>
                `).join('');
            };

            const renderFastMoving = (products) => {
                const tbody = document.getElementById('fastMovingProductsBody');
                if (!tbody) return;

                if (!products.length) {
                    tbody.innerHTML = emptyStateRow(3, 'No sales data available for the selected date range.');
                    return;
                }

                tbody.innerHTML = products.map(p => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-3 py-3">
                            <div class="font-medium text-slate-900">${p.name}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">${p.sku || 'N/A'}</div>
                        </td>
                        <td class="px-3 py-3 text-right text-slate-900">${p.qty}</td>
                        <td class="px-3 py-3 text-right font-semibold text-slate-900">${p.revenue}</td>
                    </tr>
                `).join('');
            };

            const renderSlowMoving = (products) => {
                const tbody = document.getElementById('slowMovingProductsBody');
                if (!tbody) return;

                if (!products.length) {
                    tbody.innerHTML = emptyStateRow(2, 'No sales data available for the selected date range.');
                    return;
                }

                tbody.innerHTML = products.map(p => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-3 py-3">
                            <div class="font-medium text-slate-900">${p.name}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">${p.sku || 'N/A'}</div>
                        </td>
                        <td class="px-3 py-3 text-right text-slate-900">${p.qty}</td>
                    </tr>
                `).join('');
            };

            const fetchFilteredWidgets = async (startDate, endDate) => {
                showLoadingOverlays();

                try {
                    const response = await fetch(`/api/analytics/sales-widgets?start_date=${startDate}&end_date=${endDate}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });

                    if (!response.ok) throw new Error('Request failed');

                    const data = await response.json();

                    // Category Distribution
                    const cb = data.categoryBreakdown || { labels: [], values: [], formatted: [], shares: [] };
                    initCategoryChart(cb.labels, cb.values);
                    renderCategoryLegend(cb.labels, cb.formatted, cb.shares);

                    // Top Selling Products
                    renderTopProducts(data.topProducts || []);

                    // Fast-Moving Products
                    renderFastMoving(data.fastMoving || []);

                    // Slow-Moving Products
                    renderSlowMoving(data.slowMoving || []);

                } catch (error) {
                    console.error('Error fetching filtered widgets:', error);
                    // Show empty states on error
                    initCategoryChart([], []);
                    renderCategoryLegend([], [], []);
                    renderTopProducts([]);
                    renderFastMoving([]);
                    renderSlowMoving([]);
                } finally {
                    hideLoadingOverlays();
                }
            };

            // Flatpickr initialization
            const dateInput = document.getElementById('globalDateRange');
            if (dateInput) {
                const now = new Date();
                const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);

                flatpickr(dateInput, {
                    mode: 'range',
                    dateFormat: 'M j, Y',
                    defaultDate: [startOfMonth, endOfMonth],
                    maxDate: 'today',
                    onChange: (selectedDates) => {
                        if (selectedDates.length === 2) {
                            const start = selectedDates[0].toISOString().split('T')[0];
                            const end = selectedDates[1].toISOString().split('T')[0];
                            fetchFilteredWidgets(start, end);
                        }
                    }
                });
            }

            // "View all" button
            const viewAllBtn = document.getElementById('viewAllTopProductsBtn');
            if (viewAllBtn) {
                viewAllBtn.addEventListener('click', () => {
                    console.log('View all top products');
                });
            }
        </script>
    @endpush
</x-layouts.app>
