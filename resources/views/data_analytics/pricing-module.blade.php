<x-layouts.app :title="__('Pricing Module')">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Pricing Module</h1>
                <p class="text-xs text-slate-500 mt-0.5">Analyze pricing trends, monitor stock value, and track recent price breaks.</p>
            </div>
            <a href="{{ route('analytics.pricing.export') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200">
                <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Export Price Report</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-black text-xs font-semibold">Average Unit Price</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">₱{{ number_format($averageUnitPrice, 2) }}</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Average current price for active inventory items.</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-black text-xs font-semibold">Most Expensive SKU</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black truncate">{{ $mostExpensive?->product_name ?: ($mostExpensive?->name ?? '—') }}</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium flex items-center gap-1.5">
                                <span>₱{{ number_format($mostExpensive?->unit_price ?? 0, 2) }}</span>
                                @if($mostExpensive?->sku)
                                    <span class="text-[10px]">&bull;</span>
                                    <span class="font-mono text-[11px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">{{ $mostExpensive->sku }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-black text-xs font-semibold">Cheapest SKU</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black truncate">{{ $cheapest?->product_name ?: ($cheapest?->name ?? '—') }}</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium flex items-center gap-1.5">
                                <span>₱{{ number_format($cheapest?->unit_price ?? 0, 2) }}</span>
                                @if($cheapest?->sku)
                                    <span class="text-[10px]">&bull;</span>
                                    <span class="font-mono text-[11px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">{{ $cheapest->sku }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M16 18l2.29-2.29-4.88-4.88-4 4L2 7.41 3.41 6l6 6 4-4 6.3 6.29L22 12v6z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @foreach($supplierCostAlerts as $alert)
            <div id="pricing-alert-banner-{{ $alert->id }}" class="rounded-[18px] border border-amber-200 bg-amber-50 p-4 shadow-sm transition-all duration-300 mb-3">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Pricing alert</p>
                        <h2 class="mt-1 text-base font-semibold text-amber-900">Supplier cost review needed</h2>
                        <p class="mt-1 text-sm text-amber-800">
                            {{ $alert->product?->product_name ?? 'A product' }} ({{ $alert->product?->sku ?? '—' }}) supplier cost increased by {{ number_format((float) $alert->change_percentage, 0) }}%. Review retail pricing to protect your target margin.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">⚠ Needs attention</span>
                        <button
                            type="button"
                            onclick="dismissAlert({{ $alert->id }}, '{{ $alert->product?->product_name ?? 'Product' }}', '{{ $alert->product?->sku ?? '' }}', {{ $alert->change_percentage }})"
                            title="Dismiss alert"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full text-amber-500 hover:bg-amber-100 hover:text-amber-800 transition"
                            aria-label="Dismiss pricing alert"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach


        <div id="supplier-cost-container" class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Supplier Cost Analysis</h2>
                    <p class="text-xs text-slate-500 mt-1">Track recent supplier cost shifts and pricing recommendations for administrators.</p>
                </div>
                <form method="GET" action="" class="flex flex-wrap items-center gap-2" id="supplier-search-form" onsubmit="event.preventDefault(); supplierFilterSubmit();">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            id="supplier-search-input"
                            value="{{ request('search') }}"
                            placeholder="Search product…"
                            class="pl-9 pr-4 py-[11px] text-xs rounded-[12px] border border-slate-300 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm w-52"
                            autocomplete="off"
                        />
                    </div>
                    {{-- Cost Change Filter --}}
                    <div class="relative z-[50]" data-dropdown-wrapper="costChangeFilter">
                        <input type="hidden" id="cost-change-filter" name="cost_change" value="{{ request('cost_change') }}" />
                        <button type="button" id="costChangeFilterButton" onclick="toggleDropdown('costChangeFilterDropdown')" class="px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between gap-2 hover:border-slate-400 focus:outline-none transition shadow-sm w-44">
                            <span class="flex items-center gap-2 truncate">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 6h18M7 12h10M11 18h2"/>
                                </svg>
                                <span id="costChangeFilterText" class="truncate font-medium">
                                    @switch(request('cost_change'))
                                        @case('none') No Change @break
                                        @case('up') Increased Cost @break
                                        @case('down') Decreased Cost @break
                                        @default All
                                    @endswitch
                                </span>
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div id="costChangeFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 right-0 z-[60] mt-1.5 w-full rounded-[12px] border border-slate-200 bg-white shadow-2xl p-1.5 space-y-0.5">
                            <button type="button" onclick="selectCostChangeOption('', 'All')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-medium whitespace-nowrap">All</button>
                            <button type="button" onclick="selectCostChangeOption('none', 'No Change')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-medium whitespace-nowrap">No Change</button>
                            <button type="button" onclick="selectCostChangeOption('up', 'Increased Cost')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-medium whitespace-nowrap">Increased Cost</button>
                            <button type="button" onclick="selectCostChangeOption('down', 'Decreased Cost')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px] transition font-medium whitespace-nowrap">Decreased Cost</button>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="supplierFilterSubmit()"
                        class="inline-flex items-center justify-center rounded-[12px] bg-[#0f172a] px-4 py-[11px] text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition"
                    >Search</button>
                    @if(request('search') || request('cost_change'))
                        <a href="{{ strtok(request()->fullUrl(), '?') }}"
                           class="inline-flex items-center justify-center rounded-[12px] border border-slate-300 bg-white px-3 py-[11px] text-xs font-semibold text-slate-600 shadow-sm hover:bg-slate-50 transition"
                           title="Clear filters">✕ Clear</a>
                    @endif
                </form>
            </div>
            <div class="mt-4 overflow-x-auto rounded-[10px] border border-slate-200">
                <table class="min-w-full text-left text-xs text-slate-700">
                    <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-white">Product</th>
                            <th class="px-4 py-3 text-left font-semibold text-white">Previous Cost</th>
                            <th class="px-4 py-3 text-left font-semibold text-white">Current Cost</th>
                            <th class="px-4 py-3 text-left font-semibold text-white">Change</th>
                            <th class="px-4 py-3 text-left font-semibold text-white">Suggested Retail Price</th>
                            <th class="px-4 py-3 text-left font-semibold text-white">Recommendation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($supplierCostAnalysis as $analysis)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="pricing-img-thumb w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300"
                                             data-id="{{ $analysis->product?->id ?? '' }}"
                                             data-sku="{{ $analysis->product?->sku ?? '' }}"
                                             data-name="{{ $analysis->product?->product_name ?? $analysis->product?->name ?? '' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 truncate">{{ $analysis->product?->product_name ?? 'Unknown' }}</div>
                                            <div class="text-[10px] text-slate-400 font-normal tracking-wide mt-0.5 truncate">{{ $analysis->product?->sku ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">{{ $analysis->previous_cost !== null && $analysis->previous_cost > 0 ? '₱' . number_format((float) $analysis->previous_cost, 2) : '—' }}</td>
                                <td class="px-4 py-4 text-slate-900">
                                    <div class="font-semibold">₱{{ number_format((float) $analysis->supplier_cost, 2) }}</div>
                                    <div class="text-[11px] text-slate-500 font-normal mt-1 border-t border-slate-100 pt-1 flex justify-between gap-4 whitespace-nowrap">
                                        <span>With VAT</span>
                                        <span class="font-semibold text-slate-900">₱{{ number_format((float) $analysis->supplier_cost * 1.12, 2) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $change = (float) ($analysis->change_percentage ?? 0);
                                        $badgeClass = $change > 0 ? 'bg-red-100 text-red-700' : ($change < 0 ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700');
                                        $changeLabel = $change != 0 ? (($change > 0 ? '+' : '') . number_format($change, 2) . '%') : '0.00%';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $changeLabel }}</span>
                                </td>
                                <td class="px-4 py-4 text-slate-900 font-semibold">
                                    @php
                                        $suggestedPrice = round((float) $analysis->supplier_cost * 1.20, 2);
                                    @endphp
                                    ₱{{ number_format($suggestedPrice, 2) }}
                                </td>
                                <td class="px-4 py-4 text-slate-700">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $analysis->recommendation ?? 'Maintain current retail price.' }}</span>
                                        @if($analysis->supplier_cost > 0)
                                            @php
                                                $currentRetail = (float) ($analysis->product?->unit_price ?? 0);
                                                $suggestedRetailWithVat = $suggestedPrice;
                                            @endphp
                                            <button 
                                                type="button" 
                                                class="text-slate-400 hover:text-slate-600 transition shrink-0 cursor-pointer inline-flex items-center"
                                                onclick="showReasonBreakdown(
                                                    '{{ addslashes($analysis->product?->product_name ?? 'Product') }}',
                                                    {{ (float) ($analysis->change_percentage ?? 0) }},
                                                    {{ (float) $analysis->supplier_cost }},
                                                    {{ (float) ($analysis->previous_cost ?? 0) }},
                                                    {{ $currentRetail }},
                                                    {{ $suggestedRetailWithVat }},
                                                    '{{ addslashes($analysis->recommendation ?? 'Maintain current retail price.') }}'
                                                )"
                                                title="View breakdown reason"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                                    @if(request('search'))
                                        No products found matching <strong>"{{ request('search') }}"</strong>.
                                    @else
                                        No supplier cost changes available yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4">
                {{ $supplierCostAnalysis->links() }}
            </div>

            @foreach($supplierCostAlerts as $alert)
                <div id="retail-reco-banner-{{ $alert->id }}" class="mt-4 rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Retail recommendation</p>
                        <button
                            type="button"
                            onclick="dismissAlert({{ $alert->id }}, '{{ $alert->product?->product_name ?? 'Product' }}', '{{ $alert->product?->sku ?? '' }}', {{ $alert->change_percentage }})"
                            title="Dismiss recommendation"
                            class="inline-flex items-center justify-center w-7 h-7 rounded-full text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition shrink-0 -mt-0.5"
                            aria-label="Dismiss retail recommendation"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $alert->product?->product_name ?? 'Selected product' }} ({{ $alert->product?->sku ?? '—' }})</p>
                            <p class="mt-1 text-sm text-slate-600">Current Retail Price: ₱{{ number_format((float) ($alert->product?->unit_price ?? 0), 2) }}</p>
                            <p class="text-sm text-slate-600">Suggested Retail Price: ₱{{ number_format((float) $alert->suggested_retail_price, 2) }}</p>
                            <p class="mt-2 text-sm text-slate-600">Reason: {{ $alert->reason ?? 'Supplier cost has changed.' }}</p>
                        </div>
                        <span class="rounded-full bg-[#0f172a] px-3 py-1 text-xs font-semibold text-white">{{ $alert->recommendation ?? 'Review pricing' }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.25fr_1fr] gap-3">
            <div class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Pricing Analysis Overview</h2>
                    <p class="text-xs text-slate-500 mt-1">Summary of supplier cost changes.</p>
                    
                    <div class="mt-4 h-40 w-full relative">
                        <canvas id="pricingAnalysisChart"></canvas>
                    </div>
                </div>
                
                <div class="mt-8 pt-4 border-t border-slate-100">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Products Analyzed: <span class="text-slate-900 font-semibold ml-1">{{ $pricingOverview['total'] }}</span></p>
                </div>
            </div>

            <div id="retail-price-container" class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm overflow-hidden flex flex-col justify-between h-full">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Retail Price Update</h2>
                    <p class="text-xs text-slate-500 mt-1">Track recent unit price revisions across inventory.</p>
                    <div class="mt-4 overflow-hidden rounded-[10px] border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-700 table-fixed">
                            <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                                <tr>
                                    <th class="w-[43%] px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Product</th>
                                    <th class="w-[18%] px-2.5 py-3 text-left font-semibold text-white whitespace-nowrap">Old</th>
                                    <th class="w-[20%] px-2.5 py-3 text-left font-semibold text-white whitespace-nowrap">New</th>
                                    <th class="w-[19%] px-2.5 py-3 text-left font-semibold text-white whitespace-nowrap">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($priceUpdates as $update)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="w-[43%] px-3 py-2.5 font-semibold text-slate-900">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="pricing-img-thumb w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300"
                                                     data-id="{{ $update->product?->id ?? '' }}"
                                                     data-sku="{{ $update->product?->sku ?? '' }}"
                                                     data-name="{{ $update->product?->product_name ?? $update->product?->name ?? '' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-semibold text-slate-900 truncate text-xs">{{ $update->product?->product_name ?: ($update->product?->name ?? 'Unknown') }}</div>
                                                    @if($update->product?->sku)
                                                        <div class="text-[10px] text-slate-400 font-normal tracking-wide mt-0.5 truncate">{{ $update->product->sku }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="w-[18%] px-2.5 py-2.5 text-slate-600 truncate whitespace-nowrap">{{ data_get($update, 'metadata.old_price') ? '₱' . number_format(data_get($update, 'metadata.old_price'), 2) : '—' }}</td>
                                        <td class="w-[20%] px-2.5 py-2.5 text-slate-900 font-semibold truncate whitespace-nowrap">₱{{ number_format((float) $update->unit_price, 2) }}</td>
                                        <td class="w-[19%] px-2.5 py-2.5 text-slate-500 truncate whitespace-nowrap text-[11px]">{{ $update->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-slate-500">No recent pricing updates available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-auto pt-3 -mx-4 -mb-4">
                    {{ $priceUpdates->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Info Modal --}}
    <div id="breakdown-info-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeBreakdownModal()"></div>
        <div onclick="event.stopPropagation()" class="relative bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="breakdown-modal-container">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-semibold text-slate-800" id="breakdown-modal-title">Price Breakdown Details</h3>
                </div>
                <button type="button" onclick="closeBreakdownModal()" class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6" id="breakdown-modal-body">
                <div class="flex items-start gap-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 shrink-0 mt-0.5">Reason</span>
                    <p class="text-sm text-slate-700 leading-relaxed" id="breakdown-modal-text"></p>
                </div>
            </div>
            <div class="border-t border-slate-100 px-6 py-3 bg-slate-50 flex justify-end">
                <button type="button" onclick="closeBreakdownModal()" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 transition cursor-pointer">
                    Dismiss
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const pricingAnalysisCanvas = document.getElementById('pricingAnalysisChart');
                if (pricingAnalysisCanvas && typeof Chart !== 'undefined') {
                    new Chart(pricingAnalysisCanvas, {
                        type: 'bar',
                        data: {
                            labels: ['Increased Cost', 'Decreased Cost', 'No Change'],
                            datasets: [{
                                data: [
                                    {{ $pricingOverview['increased'] }},
                                    {{ $pricingOverview['decreased'] }},
                                    {{ $pricingOverview['no_change'] }}
                                ],
                                backgroundColor: [
                                    '#ef4444', // red-500
                                    '#10b981', // emerald-500
                                    '#94a3b8'  // slate-400
                                ],
                                borderRadius: 6,
                            maxBarThickness: 50,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.parsed.y + ' products';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: {
                                    color: '#475569',
                                    font: { size: 12, weight: '500' }
                                },
                                border: { display: false }
                            },
                            y: {
                                grid: { display: false, drawBorder: false },
                                ticks: { display: false },
                                border: { display: false }
                            }
                        },
                        layout: {
                            padding: { top: 30 }
                        },
                        animation: {
                            onComplete: function() {
                                const chartInstance = this;
                                const ctx = chartInstance.ctx;
                                ctx.font = '600 13px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';
                                ctx.fillStyle = '#0f172a'; // slate-900

                                chartInstance.data.datasets.forEach(function(dataset, i) {
                                    const meta = chartInstance.getDatasetMeta(i);
                                    meta.data.forEach(function(bar, index) {
                                        const data = dataset.data[index];
                                        ctx.fillText(data, bar.x, bar.y - 8);
                                    });
                                });
                            }
                        }
                    }
                });
            }
        });
    </script>

        <script>
            /**
             * Handles AJAX loading for tables to prevent full page reloads.
             */
            function ajaxLoadTable(url, containerId) {
                var container = document.getElementById(containerId);
                if (!container) return;

                // Add a simple loading state
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(res) { return res.text(); })
                    .then(function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        var newContainer = doc.getElementById(containerId);
                        
                        if (newContainer) {
                            container.innerHTML = newContainer.innerHTML;
                            resolvePricingImages();
                        }
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    })
                    .catch(function(err) {
                        console.error('Error loading table data:', err);
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    });
            }

            // Intercept pagination clicks and clear filter clicks for AJAX containers
            document.addEventListener('click', function(e) {
                var container = e.target.closest('#supplier-cost-container, #retail-price-container');
                var link = e.target.closest('a');
                
                if (container && link && (link.closest('nav[role="navigation"]') || (link.hasAttribute('title') && link.getAttribute('title') === 'Clear filters'))) {
                    e.preventDefault();
                    ajaxLoadTable(link.href, container.id);
                }
            });

            /**
             * Submits the supplier filter form asynchronously.
             */
            function supplierFilterSubmit() {
                var form = document.getElementById('supplier-search-form');
                
                var formData = new FormData(form);
                formData.set('page', '1'); // always reset to page 1

                // Construct new URL with the form data
                var url = new URL(window.location.origin + window.location.pathname);
                url.search = new URLSearchParams(formData).toString();

                ajaxLoadTable(url.toString(), 'supplier-cost-container');
            }

            function dismissAlert(alertId, productName, sku, changePercentage) {
                var tokenEl = document.querySelector('meta[name="csrf-token"]');
                var token = tokenEl ? tokenEl.getAttribute('content') : '';

                // 1. Send AJAX request to dismiss the alert in the database
                fetch('/analytics/pricing/dismiss/' + alertId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        console.log('Alert dismissed in database successfully.');
                    }
                })
                .catch(function(err) {
                    console.error('Error dismissing alert in database:', err);
                });

                // 2. Add the notification to Dashboard Notifications localStorage
                var NOTIF_KEY = 'kcc_pricing_notifications';
                var list = [];
                try { list = JSON.parse(localStorage.getItem(NOTIF_KEY) || '[]'); } catch(e) {}
                
                var msg = productName + ' (' + (sku || '—') + ') supplier cost increased by ' + Math.round(changePercentage) + '%. Review retail pricing to protect your target margin.';
                
                // Add pricing alert notification
                list.unshift({
                    type:    'pricing_alert',
                    title:   'Pricing Alert: Supplier cost review needed',
                    message: msg,
                    time:    new Date().toLocaleString()
                });
                
                // Add retail recommendation notification
                list.unshift({
                    type:    'retail_reco',
                    title:   'Retail Recommendation: ' + productName + ' (' + (sku || '—') + ')',
                    message: 'Increase the retail price to maintain a 20% markup.',
                    time:    new Date().toLocaleString()
                });
                
                localStorage.setItem(NOTIF_KEY, JSON.stringify(list));

                // 3. Animate out and remove the alert card and the recommendation card from the UI
                var banner = document.getElementById('pricing-alert-banner-' + alertId);
                var card = document.getElementById('retail-reco-banner-' + alertId);

                [banner, card].forEach(function(el) {
                    if (!el) return;
                    el.style.transition = 'opacity 0.3s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease';
                    el.style.overflow   = 'hidden';
                    el.style.opacity    = '0';
                    el.style.maxHeight  = el.scrollHeight + 'px';
                     setTimeout(function () {
                         el.style.maxHeight = '0';
                         el.style.padding   = '0';
                         el.style.margin    = '0';
                         el.style.border    = 'none';
                     }, 300);
                     setTimeout(function () {
                         el.remove();
                     }, 700);
                 });
            }

            function showReasonBreakdown(productName, changePercentage, supplierCost, previousCost, currentRetail, suggestedRetailWithVat, recommendation) {
                const modal = document.getElementById('breakdown-info-modal');
                const modalContainer = document.getElementById('breakdown-modal-container');
                const modalTitle = document.getElementById('breakdown-modal-title');
                const modalBody = document.getElementById('breakdown-modal-body');
                
                if (!modal || !modalBody) return;

                const direction = changePercentage >= 0 ? 'increased' : 'decreased';
                const changeStr = Math.abs(changePercentage).toFixed(2);

                const vatAmount = (supplierCost * 0.12).toFixed(2);
                const vatInclusiveCost = (supplierCost * 1.12).toFixed(2);
                const currentRetailStr = currentRetail.toFixed(2);
                const suggestedRetailStr = suggestedRetailWithVat.toFixed(2);
                const formattedPreviousCost = previousCost > 0 ? '₱' + previousCost.toFixed(2) : '—';
                const formattedSupplierCost = '₱' + supplierCost.toFixed(2);

                // Determine case
                let isIncreased = changePercentage > 0;
                let isDecreased = changePercentage < 0;
                let isNoChange = changePercentage === 0;

                // Customize styling according to the case
                let statusHeader = '';
                let statusBgClass = '';
                let statusTextClass = '';
                let statusBorderClass = '';
                let statusIcon = '';
                let changeBadgeClass = '';
                let changePrefix = '';

                if (isIncreased) {
                    statusHeader = 'Price Adjustment Recommended';
                    statusBgClass = 'bg-amber-50';
                    statusTextClass = 'text-amber-800';
                    statusBorderClass = 'border-amber-200';
                    statusIcon = `<svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>`;
                    changeBadgeClass = 'bg-red-50 text-red-700 ring-red-600/10';
                    changePrefix = '+';
                } else if (isDecreased) {
                    statusHeader = 'Margin Enhancement Opportunity';
                    statusBgClass = 'bg-emerald-50';
                    statusTextClass = 'text-emerald-800';
                    statusBorderClass = 'border-emerald-200';
                    statusIcon = `<svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>`;
                    changeBadgeClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/10';
                    changePrefix = '-';
                } else {
                    statusHeader = 'No pricing adjustment needed.';
                    statusBgClass = 'bg-slate-50';
                    statusTextClass = 'text-slate-600';
                    statusBorderClass = 'border-slate-200';
                    statusIcon = `<svg class="w-5 h-5 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 11.518 1.397l-.041.02-.045.02a.75.75 0 11-.518-1.397l.045-.02zM12 22.5c5.799 0 10.5-4.701 10.5-10.5S17.799 1.5 12 1.5 1.5 6.201 1.5 12 6.201 22.5 12 22.5z"/>
                    </svg>`;
                    changeBadgeClass = 'bg-slate-50 text-slate-700 ring-slate-600/10';
                    changePrefix = '';
                }

                const changeBadge = `<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${changeBadgeClass}">${changePrefix}${changeStr}%</span>`;

                let bodyHtml = `
                    <div class="space-y-4">
                        <!-- Status Alert Banner -->
                        <div class="rounded-2xl border ${statusBorderClass} ${statusBgClass} p-4">
                            <div class="flex items-start gap-3">
                                ${statusIcon}
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900">${statusHeader}</h4>
                                    <p class="mt-1 text-xs ${statusTextClass} leading-relaxed">${recommendation}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Structured Breakdown Table -->
                        <div class="border border-slate-100 rounded-2xl overflow-hidden bg-slate-50 shadow-sm">
                            <div class="px-4 py-2 bg-slate-100 text-slate-500 font-semibold text-xs uppercase tracking-wider border-b border-slate-200/50">
                                Pricing Breakdown Details
                            </div>
                            <div class="divide-y divide-slate-100 bg-white">
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Previous Supplier Cost</span>
                                    <span class="font-medium text-slate-700">${formattedPreviousCost}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Current Supplier Cost</span>
                                    <span class="font-medium text-slate-900">${formattedSupplierCost}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Cost Change</span>
                                    <span>${changeBadge}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Supplier Cost</span>
                                    <span class="font-medium text-slate-700">${formattedSupplierCost}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">VAT (12%)</span>
                                    <span class="font-medium text-slate-700">₱${vatAmount}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs bg-slate-50/30">
                                    <span class="font-semibold text-slate-600">Total Cost (with VAT)</span>
                                    <span class="font-bold text-slate-900">₱${vatInclusiveCost}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Current Retail Price</span>
                                    <span class="font-medium text-slate-700">₱${currentRetailStr}</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs">
                                    <span class="text-slate-500">20% Markup</span>
                                    <span class="font-medium text-slate-700">20%</span>
                                </div>
                                <div class="px-4 py-2.5 flex justify-between items-center text-xs bg-slate-50/50">
                                    <span class="font-semibold text-slate-800">Suggested Retail Price</span>
                                    <span class="font-bold text-slate-950 text-sm">₱${suggestedRetailStr}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                modalTitle.textContent = 'Breakdown Details - ' + productName;
                modalBody.innerHTML = bodyHtml;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modalContainer.classList.remove('scale-95', 'opacity-0');
                    modalContainer.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function toggleDropdown(id) {
                const dropdown = document.getElementById(id);
                if (!dropdown) return;
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== id) {
                        menu.classList.add('hidden');
                    }
                });
                dropdown.classList.toggle('hidden');
            }

            function selectCostChangeOption(val, text) {
                const hiddenInput = document.getElementById('cost-change-filter');
                const textSpan = document.getElementById('costChangeFilterText');
                const dropdown = document.getElementById('costChangeFilterDropdown');
                if (hiddenInput) hiddenInput.value = val;
                if (textSpan) textSpan.textContent = text;
                if (dropdown) dropdown.classList.add('hidden');
                supplierFilterSubmit();
            }

            document.addEventListener('click', function(e) {
                if (!e.target.closest('[data-dropdown-wrapper]')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
                }
            });

            function closeBreakdownModal() {
                const modal = document.getElementById('breakdown-info-modal');
                const modalContainer = document.getElementById('breakdown-modal-container');
                if (!modal || !modalContainer) return;

                modalContainer.classList.remove('scale-100', 'opacity-100');
                modalContainer.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }, 300);
            }

            function resolvePricingImages() {
                try {
                    const stored = localStorage.getItem('posProductImages');
                    if (!stored) return;
                    const images = JSON.parse(stored);
                    const keys = Object.keys(images);

                    document.querySelectorAll('.pricing-img-thumb').forEach(container => {
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
                            container.className = 'pricing-img-thumb w-8 h-8 rounded-[6px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center';
                            container.style.backgroundImage = `url('${imgUrl}')`;
                        }
                    });
                } catch(e) {
                    console.error('Error resolving pricing images:', e);
                }
            }

            resolvePricingImages();
        </script>
    @endpush
</x-layouts.app>

