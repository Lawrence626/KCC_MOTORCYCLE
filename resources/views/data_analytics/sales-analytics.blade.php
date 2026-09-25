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
            'yearly' => [
                'labels' => ['2022', '2023', '2024', '2025', '2026'],
                'values' => [0, 0, 0, 0, 0],
            ],
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

        $fastMoving = $fastMoving ?? [];
        $slowMoving = $slowMoving ?? [];

        $topProducts = $topProducts ?? [
            ['rank' => 1, 'name' => 'Akrapovic Exhaust', 'category' => 'Exhausts', 'qty' => 132, 'revenue' => '₱15,840'],
            ['rank' => 2, 'name' => 'SHARK EVO Helmet', 'category' => 'Helmets', 'qty' => 98, 'revenue' => '₱11,760'],
            ['rank' => 3, 'name' => 'Dunlop Q3+ Tire', 'category' => 'Tires', 'qty' => 84, 'revenue' => '₱10,080'],
            ['rank' => 4, 'name' => 'Brembo Brake Pads', 'category' => 'Brakes', 'qty' => 65, 'revenue' => '₱5,850'],
            ['rank' => 5, 'name' => 'Cub Battery', 'category' => 'Accessories', 'qty' => 58, 'revenue' => '₱4,640'],
        ];
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Sales Analytics</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track revenue performance, product demand, and market momentum in a compact analytics workspace.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" id="globalDateRange" readonly
                           class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white pl-3 pr-8 py-1.5 text-xs text-slate-900 shadow-sm focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition cursor-pointer w-[220px]"
                           placeholder="Select date range">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <a href="{{ route('analytics.sales.export') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200">
                    <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Report</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-5">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Inventory Value</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">₱{{ number_format($quickStats['total_inventory_value'], 2) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Current value of stocked items across active inventory.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Average Unit Price</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">₱{{ number_format($quickStats['average_unit_price'], 2) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Average per-unit price for products currently in stock.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Units In Stock</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($quickStats['total_units_in_stock']) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Total quantity of items currently available for sale.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Healthy SKUs</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($quickStats['healthy_skus']) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">SKUs with stock above reorder threshold and ready to sell.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Low Stock SKUs</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($quickStats['low_stock_skus']) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Items at or below reorder level that need replenishment soon.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.47 21h15.06c1.54 0 2.5-1.67 1.73-3L13.73 4.99c-.77-1.33-2.69-1.33-3.46 0L2.74 18c-.77 1.33.19 3 1.73 3zM13 18h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Out Of Stock SKUs</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ number_format($quickStats['out_of_stock_skus']) }}</p>
                            <p class="text-gray-500 text-[10px] mt-1 font-medium leading-tight">Products currently unavailable that need immediate restock.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-4.5 h-4.5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm5.31-3.1L6.1 5.69C7.45 4.63 9.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 1: Sales Trend (xl:col-span-2) & Category Distribution (xl:col-span-1) Aligned -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 items-stretch">
            <!-- Sales Trend -->
            <div id="salesTrendCard" class="xl:col-span-2 border border-slate-200 relative overflow-hidden rounded-[15px] bg-white shadow-sm h-[350px] flex flex-col justify-between" style="border-radius: 15px;">
                <!-- Header (title + range buttons) -->
                <div id="salesTrendHeader" class="bg-[#0f172a] px-6 py-4 flex items-center justify-between border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <div>
                            <h2 id="salesTrendTitle" class="font-bold tracking-wide text-white text-lg">Sales trend</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Revenue progression across the selected date range.</p>
                        </div>
                    </div>
                    {{-- Sales trend range dropdown card --}}
                    <div class="relative" id="salesTrendRangeWrapper">
                        <button type="button" id="salesTrendRangeDropdownBtn"
                            onclick="toggleSalesTrendRangeDropdown(event)"
                            class="inline-flex items-center gap-2 rounded-[10px] border border-[#59b2c2] bg-[#6EC1D1] px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 min-w-[110px] justify-between">
                            <span id="salesTrendRangeLabel">Monthly</span>
                            <svg id="salesTrendRangeChevron" class="w-3.5 h-3.5 text-slate-900 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="salesTrendRangeDropdown"
                            class="hidden absolute top-full right-0 z-50 mt-1.5 w-full rounded-[12px] border border-slate-700 bg-[#0f172a] shadow-2xl overflow-hidden">
                            <div class="p-1 space-y-0.5">
                                {{-- Hidden buttons keep the existing JS (.sales-trend-range-btn + data-range) working --}}
                                <button type="button" data-range="daily"   class="sales-trend-range-btn sales-range-btn hidden"></button>
                                <button type="button" data-range="weekly"  class="sales-trend-range-btn sales-range-btn hidden"></button>
                                <button type="button" data-range="monthly" class="sales-trend-range-btn sales-range-btn hidden active"></button>
                                <button type="button" data-range="yearly"  class="sales-trend-range-btn sales-range-btn hidden"></button>

                                <button type="button" onclick="pickSalesTrendRange('daily',   'Daily')"   id="salesTrendRangeOpt-daily"   class="sales-trend-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">Daily</button>
                                <button type="button" onclick="pickSalesTrendRange('weekly',  'Weekly')"  id="salesTrendRangeOpt-weekly"  class="sales-trend-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">Weekly</button>
                                <button type="button" onclick="pickSalesTrendRange('monthly', 'Monthly')" id="salesTrendRangeOpt-monthly" class="sales-trend-range-dd-opt w-full px-3 py-1 text-sm font-semibold rounded-[8px] transition-colors text-left bg-slate-700 text-white cursor-pointer">Monthly</button>
                                <button type="button" onclick="pickSalesTrendRange('yearly',  'Yearly')"  id="salesTrendRangeOpt-yearly"  class="sales-trend-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">Yearly</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body (chart) -->
                <div id="salesTrendBody" class="relative w-full p-4 flex-1">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="xl:col-span-1 rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm relative h-[350px] flex flex-col justify-between" id="categoryDistributionSection" style="border-radius: 15px;">
                <div id="categoryLoadingOverlay" class="hidden absolute inset-0 bg-white/80 rounded-[15px] z-10 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2"><svg class="animate-spin h-6 w-6 text-[#105f68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Loading…</span></div>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Category distribution</h2>
                        <p class="text-xs text-slate-500 mt-1">Revenue contribution per product category.</p>
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#105f68]">Revenue</span>
                </div>
                <div class="my-auto grid gap-3 lg:grid-cols-[auto_1fr] items-center justify-center w-full" id="categoryContentGrid">
                    <div id="categoryChartWrapper" style="position: relative; width: 150px; height: 150px; max-width: 150px; max-height: 150px;" class="flex items-center justify-center shrink-0 mx-auto">
                        <canvas id="categoryChart" class="max-h-full max-w-full"></canvas>
                        <div id="categoryCenterOverlay" style="
                            position: absolute; inset: 0;
                            display: flex; flex-direction: column;
                            align-items: center; justify-content: center;
                            text-align: center;
                            pointer-events: none;
                            padding-top: 14px;
                            transition: opacity 0.15s ease-in-out;">
                            @php
                                $initialCatTotal = array_sum($categoryBreakdown['values'] ?? []);
                            @endphp
                            <span id="categoryCenterValue" style="color: #000000; font-weight: 700; font-size: 14px; line-height: 1.1;">{{ $initialCatTotal > 0 ? ('₱' . number_format($initialCatTotal, 2)) : '0' }}</span>
                            <span id="categoryCenterCaption" style="color: rgba(0,0,0,0.6); font-size: 9px; margin-top: 3px;">{{ $initialCatTotal > 0 ? 'Total Sales' : 'No sales in period' }}</span>
                        </div>
                    </div>
                    <div id="categoryLegend" class="max-h-52 overflow-y-auto pr-1 space-y-1.5 text-sm my-auto w-full max-w-[160px] mx-auto">
                        @php
                            $predefinedCategoryDefs = [
                                ['name' => 'Exhaust', 'color' => '#00f700'],
                                ['name' => 'Helmets', 'color' => '#da0e0e'],
                                ['name' => 'Tires', 'color' => '#f1a204'],
                                ['name' => 'Brakes', 'color' => '#5541ec'],
                                ['name' => 'Oils', 'color' => '#0948be'],
                                ['name' => 'Batteries', 'color' => '#e93071'],
                                ['name' => 'Accessories', 'color' => '#45AAF2'],
                            ];

                            $categoryColorMap = [
                                'exhaust' => '#00f700',
                                'exhausts' => '#00f700',
                                'pipe' => '#00f700',
                                'helmets' => '#da0e0e',
                                'helmet' => '#da0e0e',
                                'tires' => '#f1a204',
                                'tire' => '#f1a204',
                                'tire hugger' => '#f1a204',
                                'brakes' => '#5541ec',
                                'brake' => '#5541ec',
                                'brake pads' => '#5541ec',
                                'oils' => '#0948be',
                                'oil' => '#0948be',
                                'engine oil' => '#0948be',
                                'lubricants' => '#0948be',
                                'batteries' => '#e93071',
                                'battery' => '#e93071',
                                'accessories' => '#45AAF2',
                                'shock' => '#8b5cf6',
                                'swing arm' => '#10b981',
                                'engine support' => '#f97316',
                                'side mirror' => '#06b6d4',
                                'monorack frame' => '#64748b',
                                'quick throttle' => '#ec4899',
                                'spark plug' => '#eab308',
                                'filters' => '#14b8a6',
                            ];
                            $defaultPalette = ['#45AAF2', '#00f700', '#da0e0e', '#f1a204', '#5541ec', '#0948be', '#e93071', '#8b5cf6', '#10b981', '#f97316', '#06b6d4', '#ec4899'];

                            $incomingCategoryData = [];
                            foreach (($categoryBreakdown['labels'] ?? []) as $index => $lbl) {
                                $val = (float) data_get($categoryBreakdown, 'values.' . $index, 0);
                                $normKey = strtolower(trim($lbl));
                                $singularKey = rtrim($normKey, 's');
                                $incomingCategoryData[$normKey] = [
                                    'label' => $lbl,
                                    'value' => $val,
                                    'formatted' => data_get($categoryBreakdown, 'formatted.' . $index, '₱' . number_format($val, 2)),
                                    'share' => data_get($categoryBreakdown, 'shares.' . $index, 0),
                                ];
                                if ($singularKey !== $normKey) {
                                    $incomingCategoryData[$singularKey] = $incomingCategoryData[$normKey];
                                }
                            }

                            $allDisplayCategories = [];
                            $matchedKeys = [];

                            foreach ($predefinedCategoryDefs as $def) {
                                $key = strtolower(trim($def['name']));
                                $singularKey = rtrim($key, 's');
                                $matched = $incomingCategoryData[$key] ?? $incomingCategoryData[$singularKey] ?? null;
                                if ($matched) {
                                    $matchedKeys[strtolower(trim($matched['label']))] = true;
                                    $matchedKeys[$key] = true;
                                    $matchedKeys[$singularKey] = true;
                                }
                                $allDisplayCategories[] = [
                                    'name' => $def['name'],
                                    'color' => $def['color'],
                                    'has_value' => $matched && $matched['value'] > 0,
                                    'formatted' => $matched ? $matched['formatted'] : '',
                                    'share' => $matched ? $matched['share'] : 0,
                                ];
                            }

                            $extraPaletteIdx = 0;
                            foreach (($categoryBreakdown['labels'] ?? []) as $index => $lbl) {
                                $normKey = strtolower(trim($lbl));
                                if (!isset($matchedKeys[$normKey])) {
                                    $val = (float) data_get($categoryBreakdown, 'values.' . $index, 0);
                                    $dotColor = $categoryColorMap[$normKey] ?? $defaultPalette[$extraPaletteIdx % count($defaultPalette)];
                                    $extraPaletteIdx++;
                                    $allDisplayCategories[] = [
                                        'name' => $lbl,
                                        'color' => $dotColor,
                                        'has_value' => $val > 0,
                                        'formatted' => data_get($categoryBreakdown, 'formatted.' . $index, '₱' . number_format($val, 2)),
                                        'share' => data_get($categoryBreakdown, 'shares.' . $index, 0),
                                    ];
                                }
                            }
                        @endphp
                        @foreach($allDisplayCategories as $catItem)
                            <div class="flex items-center gap-2 rounded-[9px] border border-slate-200 bg-slate-50 px-2.5 py-1.5 w-full">
                                <span class="h-2 w-2 rounded-full flex-shrink-0" style="background-color: {{ $catItem['color'] }};"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900 text-xs truncate leading-tight">{{ $catItem['name'] }}</p>
                                    @if($catItem['has_value'])
                                        <p class="text-slate-500 text-[10px] leading-tight truncate mt-0.5">
                                            {{ $catItem['formatted'] }} • {{ $catItem['share'] }}%
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Combined Product Analytics (Full Width, Matching Sales Trend Header & Buttons 1-by-1) -->
        <div class="border border-slate-200 relative overflow-hidden rounded-[28px] bg-white shadow-sm min-h-[340px] flex flex-col justify-start" id="combinedProductsSection">
            <div id="topProductsLoadingOverlay" class="hidden absolute inset-0 bg-white/80 rounded-[28px] z-20 flex items-center justify-center">
                <div class="flex flex-col items-center gap-2"><svg class="animate-spin h-6 w-6 text-[#105f68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-xs text-slate-400">Loading…</span></div>
            </div>

            <!-- Header Banner (Clean Light) -->
            <div id="combinedProductsHeader" class="px-6 py-4 flex items-center justify-between border-b border-slate-200 bg-white">
                <div class="flex items-center gap-2">
                    <div>
                        <h2 id="combinedProductTitle" class="text-base font-semibold text-slate-900">Top selling products</h2>
                        <p id="combinedProductSubtitle" class="text-xs text-slate-500 mt-0.5">The best performing SKUs by revenue and volume.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="switchProductTab('top')" id="productTabBtn-top" class="product-tab-btn rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all bg-[#0f172a] text-white border-[#0f172a] shadow-sm">Top Selling</button>
                    <button type="button" onclick="switchProductTab('fast')" id="productTabBtn-fast" class="product-tab-btn rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50">Fast-Moving</button>
                    <button type="button" onclick="switchProductTab('slow')" id="productTabBtn-slow" class="product-tab-btn rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50">Slow-Moving</button>
                </div>
            </div>

            <!-- Body Container -->
            <div class="p-4 flex-1 flex flex-col justify-between overflow-hidden">
                <!-- Tab 1: Top Selling -->
                <div id="productTab-top" class="product-tab-content flex-1 flex flex-col justify-between overflow-hidden rounded-[10px] border border-slate-200">
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="px-4 py-3 text-center font-semibold text-white w-16 whitespace-nowrap">Rank</th>
                                    <th class="px-4 py-3 text-left font-semibold text-white">Product</th>
                                    <th class="px-4 py-3 text-left font-semibold text-white w-44 whitespace-nowrap">Category</th>
                                    <th class="px-4 py-3 text-center font-semibold text-white w-32 whitespace-nowrap">Units</th>
                                    <th class="px-4 py-3 text-right font-semibold text-white w-36 whitespace-nowrap">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white" id="topProductsBody">
                                @forelse(collect($topProducts ?? [])->take(5) as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-2.5 text-center font-semibold text-slate-900 w-16 whitespace-nowrap">{{ $product['rank'] }}</td>
                                        <td class="px-4 py-2.5 text-left">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $product['sku'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-left text-slate-600 w-44 whitespace-nowrap">{{ $product['category'] }}</td>
                                        <td class="px-4 py-2.5 text-center text-slate-900 w-32 whitespace-nowrap">{{ $product['qty'] }}</td>
                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-900 w-36 whitespace-nowrap">{{ $product['revenue'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-slate-500">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Tab 1 Pagination -->
                    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                        <p id="topProductsPageInfo" class="text-slate-600">Showing {{ collect($topProducts ?? [])->count() > 0 ? 1 : 0 }}-{{ min(5, collect($topProducts ?? [])->count()) }} of {{ collect($topProducts ?? [])->count() }} products</p>
                        <div id="topProductsPaginationControls" class="flex gap-1">
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                            <button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" {{ collect($topProducts ?? [])->count() <= 5 ? 'disabled' : '' }}>Next →</button>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Fast-Moving -->
                <div id="productTab-fast" class="product-tab-content hidden flex-1 flex flex-col justify-between overflow-hidden rounded-[10px] border border-slate-200">
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="px-4 py-3 text-center font-semibold text-white w-16 whitespace-nowrap">Rank</th>
                                    <th class="px-4 py-3 text-left font-semibold text-white">Product</th>
                                    <th class="px-4 py-3 text-center font-semibold text-white w-40 whitespace-nowrap">Quantity Sold</th>
                                    <th class="px-4 py-3 text-right font-semibold text-white w-36 whitespace-nowrap">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white" id="fastMovingProductsBody">
                                @forelse(collect($fastMoving ?? [])->take(5) as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-2.5 text-center font-semibold text-slate-900 w-16 whitespace-nowrap">{{ $product['rank'] ?? $loop->iteration }}</td>
                                        <td class="px-4 py-2.5 text-left">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $product['sku'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-center text-slate-900 w-40 whitespace-nowrap">{{ $product['qty'] }}</td>
                                        <td class="px-4 py-2.5 text-right font-semibold text-slate-900 w-36 whitespace-nowrap">{{ $product['revenue'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-slate-500">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Tab 2 Pagination -->
                    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                        <p id="fastMovingPageInfo" class="text-slate-600">Showing {{ collect($fastMoving ?? [])->count() > 0 ? 1 : 0 }}-{{ min(5, collect($fastMoving ?? [])->count()) }} of {{ collect($fastMoving ?? [])->count() }} products</p>
                        <div id="fastMovingPaginationControls" class="flex gap-1">
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                            <button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" {{ collect($fastMoving ?? [])->count() <= 5 ? 'disabled' : '' }}>Next →</button>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Slow-Moving -->
                <div id="productTab-slow" class="product-tab-content hidden flex-1 flex flex-col justify-between overflow-hidden rounded-[10px] border border-slate-200">
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="px-4 py-3 text-center font-semibold text-white w-16 whitespace-nowrap">Rank</th>
                                    <th class="px-4 py-3 text-left font-semibold text-white">Product</th>
                                    <th class="px-4 py-3 text-left font-semibold text-white w-36 whitespace-nowrap">Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white" id="slowMovingProductsBody">
                                @forelse(collect($slowMoving ?? [])->take(5) as $product)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-2.5 text-center font-semibold text-slate-900 w-16 whitespace-nowrap">{{ $product['rank'] ?? $loop->iteration }}</td>
                                        <td class="px-4 py-2.5 text-left">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">{{ $product['sku'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5 text-left text-slate-900 w-36 whitespace-nowrap">{{ $product['qty'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-slate-500">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Tab 3 Pagination -->
                    <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                        <p id="slowMovingPageInfo" class="text-slate-600">Showing {{ collect($slowMoving ?? [])->count() > 0 ? 1 : 0 }}-{{ min(5, collect($slowMoving ?? [])->count()) }} of {{ collect($slowMoving ?? [])->count() }} products</p>
                        <div id="slowMovingPaginationControls" class="flex gap-1">
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>← Prev</button>
                            <button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</button>
                            <button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" {{ collect($slowMoving ?? [])->count() <= 5 ? 'disabled' : '' }}>Next →</button>
                        </div>
                    </div>
                </div>   </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mt-6">
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
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#105f68]">Opportunity</p>
                        <p class="mt-2 text-xs text-slate-700">Boost cross-sell bundles for high-margin accessories during weekend promotions.</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#105f68]">Attention</p>
                        <p class="mt-2 text-xs text-slate-700">Review Mindanao stock replenishment after a strong 7.1% lift in sales demand.</p>
                    </div>
                    <div class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-[#105f68]">Next step</p>
                        <p class="mt-2 text-xs text-slate-700">Align pricing and promotions ahead of next month’s seasonal demand spike.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Header JavaScript --}}
        <script>
            // Notification Panel Toggle
            function toggleNotificationPanel(event) {
                event.stopPropagation();
                const panel = document.getElementById('notification-panel');
                const profileDropdown = document.getElementById('dashboardProfileDropdown');
                if (panel) {
                    const isHidden = panel.classList.contains('hidden');
                    if (isHidden) {
                        panel.classList.remove('hidden');
                        if (profileDropdown) {
                            profileDropdown.classList.add('hidden');
                            profileDropdown.classList.remove('opacity-100', 'scale-100');
                            profileDropdown.classList.add('opacity-0', 'scale-95');
                        }
                    } else {
                        panel.classList.add('hidden');
                    }
                }
            }

            // Helper functions
            function markAllNotificationsRead() {
                // Placeholder for marking all notifications as read
                console.log('Mark all notifications as read');
            }

            function openAllNotificationsModal() {
                // Placeholder for opening all notifications modal
                console.log('Open all notifications modal');
            }

            // Close dropdowns when clicking outside
            window.addEventListener('click', function(event) {
                const panel = document.getElementById('notification-panel');
                const profileDropdown = document.getElementById('dashboardProfileDropdown');
                const notificationBell = document.getElementById('notification-bell-btn');
                const profileButton = document.getElementById('dashboardProfileButton');

                if (panel && !panel.contains(event.target) && notificationBell && !notificationBell.contains(event.target)) {
                    panel.classList.add('hidden');
                }

                if (profileDropdown && !profileDropdown.contains(event.target) && profileButton && !profileButton.contains(event.target)) {
                    profileDropdown.classList.add('hidden');
                    profileDropdown.classList.remove('opacity-100', 'scale-100');
                    profileDropdown.classList.add('opacity-0', 'scale-95');
                }
            });
        </script>
        {{-- Flatpickr CDN --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <style>
            #globalDateRange {
                border-color: #cbd5e1 !important;
            }
            #globalDateRange:hover,
            #globalDateRange.active,
            #globalDateRange:focus {
                border-color: #94a3b8 !important;
                box-shadow: none !important;
                outline: none !important;
            }

            /* Senior UI/UX Datepicker Customization */
            .flatpickr-calendar {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 4px 10px -2px rgba(0, 0, 0, 0.04);
                border-radius: 12px;
                padding: 12px;
                font-family: inherit;
                width: 260px !important;
                min-width: 260px !important;
                max-width: 260px !important;
                opacity: 0;
                transform: translateY(4px);
                transition: opacity 0.2s ease, transform 0.2s ease;
            }
            .flatpickr-calendar.animate.open {
                animation: fpFadeInDown 200ms ease forwards;
            }
            @keyframes fpFadeInDown {
                from { opacity: 0; transform: translateY(4px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .flatpickr-wrapper {
                display: block !important;
                width: 100% !important;
            }
            .flatpickr-calendar.static {
                top: calc(100% + 6px) !important;
                left: 0 !important;
                right: auto !important;
                margin-top: 0 !important;
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
                margin-bottom: 8px;
                position: relative;
                padding: 0 4px;
            }
            .flatpickr-month {
                height: 32px !important;
            }
            .flatpickr-current-month {
                font-size: 13px !important;
                font-weight: 600 !important;
                color: #334155 !important;
                padding: 0 !important;
                height: 32px !important;
                line-height: 32px !important;
                left: 0 !important;
                width: 100% !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                gap: 4px;
            }
            .flatpickr-monthDropdown-months {
                background: transparent !important;
                border: none !important;
                border-radius: 6px !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                color: #334155 !important;
                cursor: pointer !important;
                padding: 2px 6px !important;
                margin: 0 !important;
                outline: none !important;
                box-shadow: none !important;
                appearance: none !important;
                -webkit-appearance: none !important;
                -moz-appearance: none !important;
                transition: background-color 0.15s ease;
            }
            .flatpickr-monthDropdown-months:hover, .flatpickr-monthDropdown-months:focus {
                background: #f1f5f9 !important;
                color: #0f172a !important;
            }
            .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month {
                background-color: #ffffff !important;
                color: #334155 !important;
                font-weight: 500 !important;
                padding: 6px 10px !important;
            }
            .flatpickr-current-month input.cur-year {
                font-size: 13px !important;
                font-weight: 600 !important;
                color: #334155 !important;
                border: none !important;
                background: transparent !important;
                padding: 2px 4px !important;
                border-radius: 6px !important;
                outline: none !important;
                box-shadow: none !important;
                transition: background-color 0.15s ease;
            }
            .flatpickr-current-month input.cur-year:hover, .flatpickr-current-month input.cur-year:focus {
                background: #f1f5f9 !important;
                color: #0f172a !important;
            }
            .flatpickr-current-month .numInputWrapper span.arrowUp,
            .flatpickr-current-month .numInputWrapper span.arrowDown {
                display: none !important;
            }
            .flatpickr-current-month span.cur-month {
                font-weight: 600 !important;
                color: #334155 !important;
                margin-left: 2px;
            }
            .flatpickr-prev-month, .flatpickr-next-month {
                position: absolute !important;
                top: 2px !important;
                height: 28px !important;
                width: 28px !important;
                border-radius: 8px !important;
                background: transparent !important;
                border: none !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
                transition: all 0.15s ease;
                z-index: 10;
            }
            .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
                background: #f1f5f9 !important;
                cursor: pointer;
            }
            .flatpickr-prev-month svg, .flatpickr-next-month svg {
                width: 12px;
                height: 12px;
                fill: #64748b !important;
            }
            .flatpickr-prev-month { left: 4px !important; }
            .flatpickr-next-month { right: 4px !important; }

            .flatpickr-weekdays {
                height: 28px !important;
                margin-bottom: 2px;
                width: 100% !important;
            }
            .flatpickr-weekdaycontainer {
                display: grid !important;
                grid-template-columns: repeat(7, 1fr);
                width: 100% !important;
            }
            span.flatpickr-weekday {
                color: #94a3b8 !important;
                font-weight: 600 !important;
                font-size: 11px !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 100% !important;
            }

            .dayContainer {
                display: grid !important;
                grid-template-columns: repeat(7, 1fr) !important;
                gap: 2px;
                justify-content: stretch;
            }
            .flatpickr-day {
                width: 100% !important;
                max-width: 100% !important;
                height: 34px !important;
                line-height: 34px !important;
                border-radius: 8px !important;
                font-weight: 400 !important;
                color: #334155 !important;
                font-size: 13px !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                transition: all 0.15s ease;
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
                background: rgba(0, 255, 242, 0.15) !important;
                color: #0f172a !important;
                border-radius: 0 !important;
                box-shadow: -2px 0 0 rgba(0, 255, 242, 0.15), 2px 0 0 rgba(0, 255, 242, 0.15) !important;
            }
            .flatpickr-day.selected,
            .flatpickr-day.startRange,
            .flatpickr-day.selected.inRange,
            .flatpickr-day.startRange.inRange,
            .flatpickr-day.selected:focus,
            .flatpickr-day.startRange:focus,
            .flatpickr-day.selected:hover,
            .flatpickr-day.startRange:hover,
            .flatpickr-day.selected.prevMonthDay,
            .flatpickr-day.startRange.prevMonthDay,
            .flatpickr-day.selected.nextMonthDay,
            .flatpickr-day.startRange.nextMonthDay {
                background: #6EC1D1 !important;
                color: #000000 !important;
                border-radius: 8px !important;
                box-shadow: none !important;
                font-weight: 700 !important;
                z-index: 2;
            }
            .flatpickr-day.endRange,
            .flatpickr-day.endRange.inRange,
            .flatpickr-day.endRange:focus,
            .flatpickr-day.endRange:hover,
            .flatpickr-day.endRange.prevMonthDay,
            .flatpickr-day.endRange.nextMonthDay {
                background: #0f172a !important;
                color: #ffffff !important;
                border-radius: 8px !important;
                box-shadow: none !important;
                font-weight: 700 !important;
                z-index: 3;
            }
            .flatpickr-day.startRange {
                box-shadow: none !important;
            }
            .flatpickr-day.endRange {
                box-shadow: none !important;
            }
            .flatpickr-day.startRange.endRange {
                background: #6EC1D1 !important;
                color: #000000 !important;
                box-shadow: none !important;
            }
            .flatpickr-day.today {
                border: none !important;
                background: rgba(0, 0, 0, 0.10) !important;
                color: #000000 !important;
                font-weight: 600 !important;
            }
            .flatpickr-day.today.startRange {
                background: #6EC1D1 !important;
                color: #000000 !important;
                border: none !important;
            }
            .flatpickr-day.today.endRange {
                background: #0f172a !important;
                color: #ffffff !important;
                border: none !important;
            }
            .flatpickr-day.today.inRange {
                background: rgba(110, 193, 209, 0.18) !important;
                color: #0f172a !important;
                border: 1px solid #cbd5e1 !important;
            }
            .flatpickr-day.flatpickr-disabled {
                color: #cbd5e1 !important;
            }
            /* Dashboard Range Button Styling */
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
                color: #000000 !important;
                font-weight: 700 !important;
                border-color: transparent !important;
            }

            .sales-range-btn.active:hover {
                background-color: #59b2c2 !important;
                color: #000000 !important;
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
                yearly: {
                    labels: ['2022', '2023', '2024', '2025', '2026'],
                    values: [0, 0, 0, 0, 0],
                },
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
                const yearlyLabels = [];
                const yearlyValues = [];
                for (let index = 4; index >= 0; index -= 1) {
                    const yr = now.getFullYear() - index;
                    yearlyLabels.push(String(yr));
                    yearlyValues.push(0);
                }

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

                    const yearLabel = String(date.getFullYear());
                    const yearIndex = yearlyLabels.indexOf(yearLabel);
                    if (yearIndex !== -1) {
                        yearlyValues[yearIndex] += total;
                    }

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
                    yearly: { labels: yearlyLabels, values: yearlyValues.map((value) => Number(value.toFixed(2))) },
                    monthly: { labels: monthlyLabels, values: monthlyValues.map((value) => Number(value.toFixed(2))) },
                    weekly: { labels: weeklyLabels, values: weeklyValues.map((value) => Number(value.toFixed(2))) },
                    daily: { labels: dailyLabels, values: dailyValues.map((value) => Number(value.toFixed(2))) },
                };
            };

            const serverSalesTrend = @json($salesTrend ?? null);
            const salesTrendData = (() => {
                const localData = buildTrendData();
                const serverData = serverSalesTrend
                    && serverSalesTrend.yearly && Array.isArray(serverSalesTrend.yearly.values)
                    && serverSalesTrend.monthly && Array.isArray(serverSalesTrend.monthly.values)
                    && serverSalesTrend.weekly && Array.isArray(serverSalesTrend.weekly.values)
                    && serverSalesTrend.daily && Array.isArray(serverSalesTrend.daily.values)
                    ? serverSalesTrend
                    : (serverSalesTrend && serverSalesTrend.monthly && Array.isArray(serverSalesTrend.monthly.values) ? serverSalesTrend : null);

                if (serverData) {
                    return serverData;
                }

                return localData;
            })();

            let trendGradient = null;
            if (salesTrendCtx && salesTrendCtx.getContext) {
                const ctx2d = salesTrendCtx.getContext('2d');
                if (ctx2d) {
                    const h = salesTrendCtx.height || salesTrendCtx.clientHeight || 300;
                    trendGradient = ctx2d.createLinearGradient(0, 0, 0, h);
                    trendGradient.addColorStop(0, 'rgba(110, 193, 209, 0.65)');
                    trendGradient.addColorStop(0.5, 'rgba(110, 193, 209, 0.35)');
                    trendGradient.addColorStop(1, 'rgba(110, 193, 209, 0.08)');
                }
            }

            const salesTrendChartConfig = {
                type: 'line',
                data: {
                    labels: salesTrendData.monthly.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: salesTrendData.monthly.values,
                        borderColor: '#6EC1D1',
                        backgroundColor: trendGradient || 'rgba(110, 193, 209, 0.25)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.55,
                        cubicInterpolationMode: 'monotone',
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#6EC1D1',
                        pointHoverBackgroundColor: '#6EC1D1',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#1a1a1a',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#6EC1D1',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: false,
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 13, weight: 'bold' },
                            callbacks: {
                                label: (context) => '₱' + Number(context.parsed.y || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                            },
                        },
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

            const SALES_TREND_RANGE_LABELS = { daily: 'Daily', weekly: 'Weekly', monthly: 'Monthly', yearly: 'Yearly' };

            const setActiveSalesTrendButton = (activeRange) => {
                salesTrendRangeButtons.forEach((button) => {
                    const isActive = button.dataset.range === activeRange;
                    button.classList.toggle('active', isActive);
                });

                // sync dropdown trigger label
                const label = document.getElementById('salesTrendRangeLabel');
                if (label) label.textContent = SALES_TREND_RANGE_LABELS[activeRange] || activeRange;

                // sync dropdown option highlights
                ['daily', 'weekly', 'monthly', 'yearly'].forEach((r) => {
                    const opt = document.getElementById('salesTrendRangeOpt-' + r);
                    if (!opt) return;
                    if (r === activeRange) {
                        opt.className = 'sales-trend-range-dd-opt w-full px-3 py-1 text-sm font-semibold rounded-[8px] transition-colors text-left bg-slate-700 text-white cursor-pointer';
                    } else {
                        opt.className = 'sales-trend-range-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer';
                    }
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

            // ── Sales Trend Range Dropdown helpers ──
            window.toggleSalesTrendRangeDropdown = function (e) {
                if (e) e.stopPropagation();
                const dd = document.getElementById('salesTrendRangeDropdown');
                const chevron = document.getElementById('salesTrendRangeChevron');
                if (!dd) return;
                const isHidden = dd.classList.contains('hidden');
                dd.classList.toggle('hidden', !isHidden);
                if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
            };

            window.pickSalesTrendRange = function (range, labelText) {
                const dd = document.getElementById('salesTrendRangeDropdown');
                const chevron = document.getElementById('salesTrendRangeChevron');
                if (dd) dd.classList.add('hidden');
                if (chevron) chevron.style.transform = '';
                updateSalesTrendChart(range);
            };

            document.addEventListener('click', function (e) {
                const wrapper = document.getElementById('salesTrendRangeWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    const dd = document.getElementById('salesTrendRangeDropdown');
                    const chevron = document.getElementById('salesTrendRangeChevron');
                    if (dd) dd.classList.add('hidden');
                    if (chevron) chevron.style.transform = '';
                }
            });

            setActiveSalesTrendButton('monthly');

            // ═══════════════════════════════════════════
            // CATEGORY CHART (initial render from server)
            // ═══════════════════════════════════════════
            const CATEGORY_DEFS = [
                { name: 'Exhaust', color: '#00f700' },
                { name: 'Helmets', color: '#da0e0e' },
                { name: 'Tires', color: '#f1a204' },
                { name: 'Brakes', color: '#5541ec' },
                { name: 'Oils', color: '#0948be' },
                { name: 'Batteries', color: '#e93071' },
                { name: 'Accessories', color: '#45AAF2' },
            ];

            const categoryColorMap = {
                'exhaust': '#00f700',
                'exhausts': '#00f700',
                'pipe': '#00f700',
                'helmets': '#da0e0e',
                'helmet': '#da0e0e',
                'tires': '#f1a204',
                'tire': '#f1a204',
                'tire hugger': '#f1a204',
                'brakes': '#5541ec',
                'brake': '#5541ec',
                'brake pads': '#5541ec',
                'oils': '#0948be',
                'oil': '#0948be',
                'engine oil': '#0948be',
                'lubricants': '#0948be',
                'batteries': '#e93071',
                'battery': '#e93071',
                'accessories': '#45AAF2',
                'shock': '#8b5cf6',
                'swing arm': '#10b981',
                'engine support': '#f97316',
                'side mirror': '#06b6d4',
                'monorack frame': '#64748b',
                'quick throttle': '#ec4899',
                'spark plug': '#eab308',
                'filters': '#14b8a6',
            };
            const fallbackCategoryColors = ['#45AAF2', '#00f700', '#da0e0e', '#f1a204', '#5541ec', '#0948be', '#e93071', '#8b5cf6', '#10b981', '#f97316', '#06b6d4', '#ec4899'];

            const getCategoryColor = (label, index = 0) => {
                if (!label) return fallbackCategoryColors[index % fallbackCategoryColors.length];
                const key = String(label).trim().toLowerCase();
                return categoryColorMap[key] || fallbackCategoryColors[index % fallbackCategoryColors.length];
            };

            let categoryChartInstance = null;

            const initCategoryChart = (labels = [], values = []) => {
                const categoryCtx = document.getElementById('categoryChart');
                if (!categoryCtx) return;

                if (categoryChartInstance) {
                    categoryChartInstance.destroy();
                    categoryChartInstance = null;
                }

                // Filter out non-positive/empty values for the actual chart slices
                const validData = [];
                (labels || []).forEach((label, idx) => {
                    const val = Number(values[idx] || 0);
                    if (val > 0) {
                        validData.push({
                            label: label,
                            value: val,
                            color: getCategoryColor(label, idx)
                        });
                    }
                });

                const total = (values || []).reduce((sum, v) => sum + Number(v || 0), 0);
                const isEmpty = validData.length === 0;
                const activeCount = validData.length;
                const hasMultiple = !isEmpty && activeCount > 1;

                const EMPTY_RING_COLOR = '#E5E7EB';
                const chartLabels = isEmpty ? ['No data'] : validData.map(d => d.label);
                const chartValues = isEmpty ? [1] : validData.map(d => d.value);
                const chartColors = isEmpty ? [EMPTY_RING_COLOR] : validData.map(d => d.color);

                categoryChartInstance = new Chart(categoryCtx, {
                    type: 'doughnut',
                    plugins: [],
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartValues,
                            backgroundColor: chartColors,
                            borderColor: hasMultiple ? '#ffffff' : 'transparent',
                            borderWidth: hasMultiple ? 2.5 : 0,
                            hoverBorderColor: hasMultiple ? '#ffffff' : 'transparent',
                            hoverBorderWidth: hasMultiple ? 2.5 : 0,
                            borderRadius: 0,
                            hoverOffset: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        spacing: hasMultiple ? 1 : 0,
                        circumference: 360,
                        rotation: -90,
                        layout: { padding: 0 },
                        onHover: (event, elements) => {
                            const overlay = document.getElementById('categoryCenterOverlay');
                            if (overlay) {
                                overlay.style.opacity = (elements && elements.length > 0) ? '0' : '1';
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: !isEmpty,
                                backgroundColor: '#1a1a1a',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: (context) => {
                                    const dataPoints = context.tooltip?.dataPoints;
                                    if (dataPoints && dataPoints.length > 0) {
                                        const dp = dataPoints[0];
                                        const colors = dp.dataset?.backgroundColor;
                                        if (Array.isArray(colors)) {
                                            return colors[dp.dataIndex] || '#00f700';
                                        }
                                        if (typeof colors === 'string') {
                                            return colors;
                                        }
                                    }
                                    return '#00f700';
                                },
                                borderWidth: 0.8,
                                padding: 6,
                                titleFont: { size: 11 },
                                bodyFont: { size: 11 },
                                displayColors: true,
                                boxWidth: 10,
                                boxHeight: 10,
                                boxPadding: 6,
                                usePointStyle: false,
                                callbacks: {
                                    title: () => '',
                                    label: (context) => {
                                        const value = context.parsed || 0;
                                        return `${context.label}: ₱${Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                    },
                                    labelColor: (context) => {
                                        const color = (context.dataset.backgroundColor && context.dataset.backgroundColor[context.dataIndex]) || '#00f700';
                                        return {
                                            borderColor: color,
                                            backgroundColor: color,
                                            borderWidth: 0,
                                            borderRadius: 2,
                                        };
                                    },
                                }
                            }
                        }
                    }
                });

                if (!categoryCtx.dataset.hasLeaveListener) {
                    categoryCtx.dataset.hasLeaveListener = 'true';
                    categoryCtx.addEventListener('mouseleave', () => {
                        const overlay = document.getElementById('categoryCenterOverlay');
                        if (overlay) overlay.style.opacity = '1';
                    });
                }

                const centerValueEl = document.getElementById('categoryCenterValue');
                const centerCaptionEl = document.getElementById('categoryCenterCaption');
                if (centerValueEl) {
                    centerValueEl.textContent = isEmpty ? '0' : '₱' + Number(total).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                if (centerCaptionEl) {
                    centerCaptionEl.textContent = isEmpty ? 'No sales in period' : 'Total Sales';
                }
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

            const renderCategoryLegend = (labels = [], formatted = [], shares = []) => {
                const legend = document.getElementById('categoryLegend');
                if (!legend) return;

                const incomingMap = {};
                (labels || []).forEach((label, index) => {
                    const key = String(label).trim().toLowerCase();
                    const singularKey = key.endsWith('s') ? key.slice(0, -1) : key;
                    const item = {
                        label: label,
                        formatted: formatted[index] || '',
                        share: shares[index] || 0,
                        hasValue: true
                    };
                    incomingMap[key] = item;
                    if (singularKey !== key) {
                        incomingMap[singularKey] = item;
                    }
                });

                const allCategories = [];
                const matchedKeys = {};

                CATEGORY_DEFS.forEach(cat => {
                    const key = cat.name.toLowerCase();
                    const singularKey = key.endsWith('s') ? key.slice(0, -1) : key;
                    const matched = incomingMap[key] || incomingMap[singularKey] || null;
                    if (matched) {
                        matchedKeys[matched.label.toLowerCase()] = true;
                        matchedKeys[key] = true;
                        matchedKeys[singularKey] = true;
                    }
                    allCategories.push({
                        name: cat.name,
                        color: cat.color,
                        hasValue: !!matched,
                        formatted: matched ? matched.formatted : '',
                        share: matched ? matched.share : 0
                    });
                });

                let extraIndex = 0;
                (labels || []).forEach((lbl, idx) => {
                    const key = String(lbl).trim().toLowerCase();
                    if (!matchedKeys[key]) {
                        const color = categoryColorMap[key] || fallbackCategoryColors[extraIndex % fallbackCategoryColors.length];
                        extraIndex++;
                        allCategories.push({
                            name: lbl,
                            color: color,
                            hasValue: true,
                            formatted: formatted[idx] || '',
                            share: shares[idx] || 0
                        });
                    }
                });

                legend.className = 'max-h-52 overflow-y-auto pr-1 space-y-1.5 text-sm my-auto w-full max-w-[160px] mx-auto';
                legend.innerHTML = allCategories.map((cat) => {
                    return `
                        <div class="flex items-center gap-2 rounded-[9px] border border-slate-200 bg-slate-50 px-2.5 py-1.5 w-full">
                            <span class="h-2 w-2 rounded-full flex-shrink-0" style="background-color: ${cat.color};"></span>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-slate-900 text-xs truncate leading-tight">${cat.name}</p>
                                ${cat.hasValue ? `<p class="text-slate-500 text-[10px] leading-tight truncate mt-0.5">${cat.formatted} • ${cat.share}%</p>` : ''}
                            </div>
                        </div>
                    `;
                }).join('');
            };

let topProductsData = @json($topProducts ?? []);
            let fastMovingData = @json($fastMoving ?? []);
            let slowMovingData = @json($slowMoving ?? []);

            const tabPages = { top: 1, fast: 1, slow: 1 };
            const pageSize = 5;

            window.goToProductPage = function(tab, page) {
                tabPages[tab] = page;
                if (tab === 'top') renderTopProducts(topProductsData);
                if (tab === 'fast') renderFastMoving(fastMovingData);
                if (tab === 'slow') renderSlowMoving(slowMovingData);
            };

            function updateTabPagination(tab, totalItems, infoId, controlsId, singularLabel = 'products') {
                const info = document.getElementById(infoId);
                const controls = document.getElementById(controlsId);
                if (!info || !controls) return { start: 0, end: 0, pageItems: [] };

                const totalPages = Math.max(1, Math.ceil(totalItems.length / pageSize));
                tabPages[tab] = Math.min(Math.max(tabPages[tab] || 1, 1), totalPages);
                const currentPage = tabPages[tab];
                const start = (currentPage - 1) * pageSize;
                const end = Math.min(start + pageSize, totalItems.length);
                const pageItems = totalItems.slice(start, end);

                if (totalItems.length === 0) {
                    info.textContent = `Showing 0 of 0 ${singularLabel}`;
                } else {
                    info.textContent = `Showing ${start + 1}-${end} of ${totalItems.length} ${singularLabel}`;
                }

                let controlsHtml = `<button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage <= 1 ? 'disabled' : ''} onclick="goToProductPage('${tab}', ${currentPage - 1})">← Prev</button>`;

                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }
                startPage = Math.max(1, startPage);

                for (let i = startPage; i <= endPage; i++) {
                    if (i === currentPage) {
                        controlsHtml += `<button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${i}</button>`;
                    } else {
                        controlsHtml += `<button type="button" onclick="goToProductPage('${tab}', ${i})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">${i}</button>`;
                    }
                }

                controlsHtml += `<button type="button" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage >= totalPages ? 'disabled' : ''} onclick="goToProductPage('${tab}', ${currentPage + 1})">Next →</button>`;

                controls.innerHTML = controlsHtml;
                return { start, end, pageItems };
            }

            const getAnalyticsProductImage = (p) => {
                if (!p) return null;
                if (p.image) return p.image;
                try {
                    const stored = localStorage.getItem('posProductImages');
                    if (stored) {
                        const images = JSON.parse(stored);
                        const productId = p.id || p.product_id;
                        if (productId && images[productId]) return images[productId];
                        if (p.sku && images[p.sku]) return images[p.sku];
                        if (p.name && images[p.name]) return images[p.name];

                        const keys = Object.keys(images);
                        if (p.sku) {
                            const matchSku = keys.find(k => k.toLowerCase() === String(p.sku).toLowerCase());
                            if (matchSku) return images[matchSku];
                        }
                        if (p.name) {
                            const matchName = keys.find(k => k.toLowerCase() === String(p.name).toLowerCase());
                            if (matchName) return images[matchName];
                        }
                    }
                } catch (e) {}
                return null;
            };

            const renderProductImageHtml = (p) => {
                const imageUrl = getAnalyticsProductImage(p);
                return imageUrl
                    ? `<div class="w-8 h-8 rounded-[6px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image: url('${imageUrl}');"></div>`
                    : `<div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                       </div>`;
            };

            const renderTopProducts = (products) => {
                topProductsData = products || [];
                const tbody = document.getElementById('topProductsBody');
                if (!tbody) return;

                const { pageItems, start } = updateTabPagination('top', topProductsData, 'topProductsPageInfo', 'topProductsPaginationControls', 'products');

                if (!pageItems.length) {
                    tbody.innerHTML = emptyStateRow(5, 'No sales data available for the selected date range.');
                    return;
                }

                tbody.innerHTML = pageItems.map((p, idx) => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-2.5 text-center font-semibold text-slate-900 w-16 whitespace-nowrap">${p.rank || (start + idx + 1)}</td>
                        <td class="px-4 py-2.5 text-left">
                            <div class="flex items-center gap-2.5">
                                ${renderProductImageHtml(p)}
                                <div>
                                    <div class="font-medium text-slate-900">${p.name}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">${p.sku || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-left text-slate-600 w-44 whitespace-nowrap">${p.category}</td>
                        <td class="px-4 py-2.5 text-center text-slate-900 w-32 whitespace-nowrap">${p.qty}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-slate-900 w-36 whitespace-nowrap">${p.revenue}</td>
                    </tr>
                `).join('');
            };

            const renderFastMoving = (products) => {
                fastMovingData = products || [];
                const tbody = document.getElementById('fastMovingProductsBody');
                if (!tbody) return;

                const { pageItems, start } = updateTabPagination('fast', fastMovingData, 'fastMovingPageInfo', 'fastMovingPaginationControls', 'products');

                if (!pageItems.length) {
                    tbody.innerHTML = emptyStateRow(4, 'No sales data available for the selected date range.');
                    return;
                }

                tbody.innerHTML = pageItems.map((p, idx) => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-2.5 text-center font-semibold text-slate-900 w-16 whitespace-nowrap">${p.rank || (start + idx + 1)}</td>
                        <td class="px-4 py-2.5 text-left">
                            <div class="flex items-center gap-2.5">
                                ${renderProductImageHtml(p)}
                                <div>
                                    <div class="font-medium text-slate-900">${p.name}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">${p.sku || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center text-slate-900 w-40 whitespace-nowrap">${p.qty}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-slate-900 w-36 whitespace-nowrap">${p.revenue}</td>
                    </tr>
                `).join('');
            };

            const renderSlowMoving = (products) => {
                slowMovingData = products || [];
                const tbody = document.getElementById('slowMovingProductsBody');
                if (!tbody) return;

                const { pageItems, start } = updateTabPagination('slow', slowMovingData, 'slowMovingPageInfo', 'slowMovingPaginationControls', 'products');

                if (!pageItems.length) {
                    tbody.innerHTML = emptyStateRow(3, 'No sales data available for the selected date range.');
                    return;
                }

                tbody.innerHTML = pageItems.map((p, idx) => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-2.5 text-center font-semibold text-slate-900 w-16 whitespace-nowrap">${p.rank || (start + idx + 1)}</td>
                        <td class="px-4 py-2.5 text-left">
                            <div class="flex items-center gap-2.5">
                                ${renderProductImageHtml(p)}
                                <div>
                                    <div class="font-medium text-slate-900">${p.name}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono tracking-wide">${p.sku || 'N/A'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-left text-slate-900 w-36 whitespace-nowrap">${p.qty}</td>
                    </tr>
                `).join('');
            };

            // Render with images on initial load
            renderTopProducts(topProductsData);
            renderFastMoving(fastMovingData);
            renderSlowMoving(slowMovingData);

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
                    static: true,
                    onOpen: (selectedDates, dateStr, instance) => {
                        if (instance.calendarContainer && dateInput) {
                            instance.calendarContainer.style.setProperty('width', dateInput.offsetWidth + 'px', 'important');
                        }
                        dateInput.classList.add('active');
                        const icon = dateInput.parentNode.querySelector('svg');
                        if (icon) {
                            icon.classList.remove('text-slate-400');
                            icon.classList.add('text-slate-600');
                        }
                    },
                    onClose: (selectedDates, dateStr, instance) => {
                        dateInput.classList.remove('active');
                        const icon = dateInput.parentNode.querySelector('svg');
                        if (icon) {
                            icon.classList.remove('text-slate-600');
                            icon.classList.add('text-slate-400');
                        }
                    },
                    onChange: (selectedDates) => {
                        if (selectedDates.length === 2) {
                            const start = selectedDates[0].toISOString().split('T')[0];
                            const end = selectedDates[1].toISOString().split('T')[0];
                            fetchFilteredWidgets(start, end);
                        }
                    }
                });
            }

            // Product tab switcher function
            window.switchProductTab = function(tabName) {
                const titles = {
                    top: { title: 'Top selling products', subtitle: 'The best performing SKUs by revenue and volume.' },
                    fast: { title: 'Fast-moving products', subtitle: 'Top 5 best-selling products by quantity.' },
                    slow: { title: 'Slow-moving products', subtitle: 'Bottom 5 least-selling products by quantity.' }
                };

                // Hide all tab contents
                document.querySelectorAll('.product-tab-content').forEach(el => el.classList.add('hidden'));

                // Show target tab
                const targetContent = document.getElementById('productTab-' + tabName);
                if (targetContent) targetContent.classList.remove('hidden');

                // Update title & subtitle
                const t = titles[tabName];
                if (t) {
                    const titleEl = document.getElementById('combinedProductTitle');
                    const subEl = document.getElementById('combinedProductSubtitle');
                    if (titleEl) titleEl.textContent = t.title;
                    if (subEl) subEl.textContent = t.subtitle;
                }

                // Update tab buttons style (matching User Management buttons)
                document.querySelectorAll('.product-tab-btn').forEach(btn => {
                    btn.className = 'product-tab-btn rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all bg-white text-slate-700 border-slate-200 hover:bg-slate-50';
                });

                const activeBtn = document.getElementById('productTabBtn-' + tabName);
                if (activeBtn) {
                    activeBtn.className = 'product-tab-btn rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all bg-[#0f172a] text-white border-[#0f172a] shadow-sm';
                }
            };
        </script>
    @endpush
</x-layouts.app>
