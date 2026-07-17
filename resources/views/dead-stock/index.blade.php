<x-layouts.app :title="__('Dead Stock Analysis')" :stretch="true">
<div id="dead-stock-root"
     data-recalculate-url="{{ route('dss.dead-stock.recalculate') }}"
     data-export-excel-url="{{ route('dss.dead-stock.export-excel') }}"
     data-export-pdf-url="{{ route('dss.dead-stock.export-pdf') }}"
     data-api-url="{{ route('api.dss.dead-stocks.index') }}"
     data-dashboard-stats-url="{{ route('api.dss.dashboard-stats') }}"
     data-csrf="{{ csrf_token() }}"
     class="h-full flex flex-col overflow-hidden bg-slate-50/50">

    {{-- ═══════ SCROLLABLE CONTENT ═══════ --}}
    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">

        {{-- ═══ HEADER ═══ --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-semibold text-slate-900 tracking-tight">Dead Stock Analysis</h1>
                <p class="text-sm text-slate-500 mt-1 font-medium">
                    Inventory items without sales for <span class="text-slate-700 font-semibold">{{ $thresholdDays }} days</span> or more.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('dss.dead-stock.export-excel') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-slate-900 shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('dss.dead-stock.export-pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-slate-900 shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Export PDF
                </a>
                <form action="{{ route('dss.dead-stock.recalculate') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-teal-600 text-white hover:bg-teal-700 shadow-sm transition-all duration-200 focus:ring-2 focus:ring-teal-500/20 focus:outline-none" onclick="this.innerHTML='<svg class=\'w-4 h-4 animate-spin\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15\'/></svg> Analyzing...'; this.disabled=true; this.closest('form').submit();">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Recalculate Analysis
                    </button>
                </form>
            </div>
        </div>

        {{-- ═══ SUCCESS ALERTS ═══ --}}
        @if(session('success'))
        <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-medium text-teal-800 flex items-center gap-3">
            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- ═══ KPI CARDS ═══ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
            {{-- Total (Primary KPI) --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 px-5 py-4 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Items</p>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-[28px] leading-tight font-semibold text-slate-900">{{ $totalDeadStocks }}</p>
                </div>
            </div>
            
            {{-- Value --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 px-5 py-4 flex flex-col justify-between">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Value at Risk</p>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-[20px] leading-tight font-semibold text-slate-900">₱{{ number_format($totalValue, 0) }}</p>
                </div>
            </div>

            {{-- Critical --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 px-5 py-4 flex flex-col justify-between group">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide group-hover:text-red-500 transition-colors">Critical</p>
                    <div class="w-2 h-2 rounded-full bg-red-500 mt-1"></div>
                </div>
                <div>
                    <p class="text-xl font-semibold text-slate-900">{{ $countByPriority['Critical'] ?? 0 }}</p>
                </div>
            </div>

            {{-- High --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 px-5 py-4 flex flex-col justify-between group">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide group-hover:text-orange-500 transition-colors">High</p>
                    <div class="w-2 h-2 rounded-full bg-orange-500 mt-1"></div>
                </div>
                <div>
                    <p class="text-xl font-semibold text-slate-900">{{ $countByPriority['High'] ?? 0 }}</p>
                </div>
            </div>

            {{-- Medium --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 px-5 py-4 flex flex-col justify-between group">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide group-hover:text-amber-500 transition-colors">Medium</p>
                    <div class="w-2 h-2 rounded-full bg-amber-500 mt-1"></div>
                </div>
                <div>
                    <p class="text-xl font-semibold text-slate-900">{{ $countByPriority['Medium'] ?? 0 }}</p>
                </div>
            </div>

            {{-- Low --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 px-5 py-4 flex flex-col justify-between group">
                <div class="flex items-start justify-between mb-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide group-hover:text-blue-500 transition-colors">Low</p>
                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-1"></div>
                </div>
                <div>
                    <p class="text-xl font-semibold text-slate-900">{{ $countByPriority['Low'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- ═══ AT-RISK PRODUCTS ═══ --}}
        @if($atRiskProducts->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-2">
            <div class="px-5 py-3 bg-amber-50/50 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-sm font-medium text-slate-700">Approaching Dead Stock Threshold ({{ $atRiskProducts->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-5 py-2.5 text-left text-xs font-medium text-slate-500">Product</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500">SKU</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-500">Days Unsold</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-500">Current Stock</th>
                            <th class="px-5 py-2.5 text-right text-xs font-medium text-slate-500">Last Sold</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($atRiskProducts as $atRisk)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-2.5 font-medium text-slate-700">{{ $atRisk['product']->name ?? '' }}</td>
                            <td class="px-4 py-2.5 text-xs text-slate-500 font-mono">{{ $atRisk['product']->sku ?? '' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="inline-flex items-center text-xs font-medium text-slate-700">{{ $atRisk['days_without_sale'] }} days</span>
                            </td>
                            <td class="px-4 py-2.5 text-right text-slate-600">{{ $atRisk['product']->stock_quantity }}</td>
                            <td class="px-5 py-2.5 text-right text-xs text-slate-500">
                                {{ $atRisk['last_sold_date'] ? $atRisk['last_sold_date']->format('M d, Y') : 'Never' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ═══ MAIN DATA TABLE ═══ --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
            {{-- Toolbar --}}
            <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('dss.dead-stock.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 min-w-[240px]">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inventory..."
                               class="w-full pl-9 pr-3 py-1.5 text-sm rounded-lg border border-slate-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-200 transition outline-none">
                    </div>
                    <select name="priority" class="px-3 py-1.5 text-sm rounded-lg border border-slate-200 focus:border-teal-500 outline-none bg-white min-w-[130px]">
                        <option value="">All Priorities</option>
                        <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical</option>
                        <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                        <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                    </select>
                    <select name="sort_by" class="px-3 py-1.5 text-sm rounded-lg border border-slate-200 focus:border-teal-500 outline-none bg-white min-w-[150px]">
                        <option value="days_without_sale" {{ request('sort_by', 'days_without_sale') === 'days_without_sale' ? 'selected' : '' }}>Longest Unsold</option>
                        <option value="stock_value" {{ request('sort_by') === 'stock_value' ? 'selected' : '' }}>Highest Value</option>
                        <option value="current_stock" {{ request('sort_by') === 'current_stock' ? 'selected' : '' }}>Highest Stock</option>
                        <option value="last_sold_date" {{ request('sort_by') === 'last_sold_date' ? 'selected' : '' }}>Last Sold</option>
                    </select>
                    <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors" title="Apply Filters">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </button>
                    @if(request('search') || request('priority') || request('sort_by'))
                    <a href="{{ route('dss.dead-stock.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition">Clear</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-3 text-xs font-medium text-slate-500">Product</th>
                            <th class="px-4 py-3 text-xs font-medium text-slate-500">SKU</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500">Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500">Value</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500">Days Unsold</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500">Action</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($deadStocks as $ds)
                        @php
                            $product = $ds->product;
                            $priorityStyles = [
                                'Critical' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10',
                                'High' => 'bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/10',
                                'Medium' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/10',
                                'Low' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/10',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            {{-- Product Details --}}
                            <td class="px-5 py-3 max-w-[260px]">
                                <a href="{{ route('dss.dead-stock.show', $ds->id) }}" class="block truncate font-medium text-slate-900 group-hover:text-teal-600 transition">
                                    {{ $product->description ?? $product->name ?? 'N/A' }}
                                </a>
                                @if($product->brand || $product->product_name)
                                <div class="truncate text-[11px] text-slate-400 mt-0.5">
                                    {{ implode(' • ', array_filter([$product->brand, $product->product_name])) }}
                                </div>
                                @endif
                            </td>
                            {{-- SKU --}}
                            <td class="px-4 py-3 text-xs text-slate-500 font-mono">{{ $product->sku ?? '—' }}</td>
                            {{-- Stock --}}
                            <td class="px-4 py-3 text-right text-slate-700">{{ $ds->current_stock }}</td>
                            {{-- Value --}}
                            <td class="px-4 py-3 text-right text-slate-700">₱{{ number_format($ds->stock_value, 0) }}</td>
                            {{-- Days Unsold --}}
                            <td class="px-4 py-3 text-right font-medium text-slate-700">{{ $ds->days_without_sale }}</td>
                            {{-- Priority Status --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium {{ $priorityStyles[$ds->priority_level] ?? 'bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/10' }}">
                                    {{ $ds->priority_level }}
                                </span>
                            </td>
                            {{-- Suggested Action --}}
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $ds->analysis_notes ?? 'Monitor' }}
                            </td>
                            {{-- Actions --}}
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="openSalesHistoryModal({{ $ds->id }}, '{{ addslashes($product->name ?? $product->description ?? '') }}')" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Sales History">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </button>
                                    <button onclick="openDiscountModal({{ $ds->id }}, '{{ addslashes($product->name ?? '') }}', {{ $product->unit_price ?? 0 }})" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Apply Discount">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </button>
                                    <form action="{{ route('dss.dead-stock.resolve', $ds->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Mark this product as resolved?')" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Mark Resolved">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('dss.dead-stock.show', $ds->id) }}" class="p-1.5 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-slate-900">No dead stock found</h4>
                                    <p class="text-sm text-slate-500 mt-1">Your inventory is well managed. Adjust the filters above if you're looking for something specific.</p>
                                    @if(request('search') || request('priority'))
                                    <a href="{{ route('dss.dead-stock.index') }}" class="mt-4 text-sm font-medium text-teal-600 hover:text-teal-700">Clear all filters</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($deadStocks->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
                {{ $deadStocks->appends(request()->query())->links('pagination::tailwind') }}
            </div>
            @endif
        </div>

    </div>{{-- end scrollable --}}
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SALES HISTORY MODAL --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="salesHistoryModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeSalesHistoryModal()"></div>
    <div class="absolute inset-4 md:inset-y-12 md:inset-x-[15%] lg:inset-x-[20%] bg-white rounded-xl shadow-xl flex flex-col overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Sales History</h3>
                <p id="salesHistoryProductName" class="text-sm text-slate-500 mt-0.5"></p>
            </div>
            <button onclick="closeSalesHistoryModal()" class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/50">
            {{-- Sales Trend Chart --}}
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
                <h4 class="text-sm font-medium text-slate-700 mb-4">Monthly Trend</h4>
                <div style="height: 200px;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
            {{-- Transaction History --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h4 class="text-sm font-medium text-slate-700">Recent Transactions</h4>
                </div>
                <div id="salesHistoryTransactions">
                    <div class="py-8 flex justify-center">
                        <svg class="w-6 h-6 text-slate-300 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- APPLY DISCOUNT MODAL --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="discountModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeDiscountModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-white">
            <h3 class="text-base font-semibold text-slate-900">Apply Discount</h3>
            <div class="mt-1 flex items-center gap-2">
                <p id="discountProductName" class="text-sm text-slate-600 truncate"></p>
                <span class="text-slate-300">•</span>
                <p id="discountCurrentPrice" class="text-sm font-medium text-slate-700 whitespace-nowrap"></p>
            </div>
        </div>
        <form id="discountForm" method="POST" class="p-5 space-y-5 bg-slate-50/50">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-2">Quick Presets</label>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="setDiscount('percentage', 10, this)" class="discount-preset-btn px-3 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:border-teal-500 hover:text-teal-700 bg-white transition-colors">10%</button>
                    <button type="button" onclick="setDiscount('percentage', 15, this)" class="discount-preset-btn px-3 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:border-teal-500 hover:text-teal-700 bg-white transition-colors">15%</button>
                    <button type="button" onclick="setDiscount('percentage', 20, this)" class="discount-preset-btn px-3 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:border-teal-500 hover:text-teal-700 bg-white transition-colors">20%</button>
                    <button type="button" onclick="setDiscount('percentage', 25, this)" class="discount-preset-btn px-3 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:border-teal-500 hover:text-teal-700 bg-white transition-colors">25%</button>
                </div>
            </div>
            
            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center" aria-hidden="true"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center"><span class="px-2 bg-slate-50 text-xs text-slate-500">or custom amount</span></div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-2">Discount Value</label>
                <div class="flex items-center gap-2">
                    <select name="discount_type" id="discountType" class="px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-shadow">
                        <option value="percentage">Percent (%)</option>
                        <option value="fixed">Fixed (₱)</option>
                    </select>
                    <input type="number" name="discount_value" id="discountValue" min="0" step="0.01" placeholder="0.00" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-shadow">
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeDiscountModal()" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-100 bg-white transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-medium hover:bg-teal-700 shadow-sm transition-colors">Apply</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- JAVASCRIPT --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
    let salesTrendChartInstance = null;

    // ───── Sales History Modal ─────
    function openSalesHistoryModal(deadStockId, productName) {
        document.getElementById('salesHistoryProductName').textContent = productName;
        document.getElementById('salesHistoryModal').classList.remove('hidden');
        document.getElementById('salesHistoryTransactions').innerHTML = '<div class="py-8 flex justify-center"><svg class="w-6 h-6 text-slate-300 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

        fetch(`/api/dss/dead-stocks/${deadStockId}/sales-history`)
            .then(r => r.json())
            .then(data => {
                // Build Chart
                if (salesTrendChartInstance) salesTrendChartInstance.destroy();
                const ctx = document.getElementById('salesTrendChart').getContext('2d');
                salesTrendChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.trend.labels,
                        datasets: [{
                            label: 'Units Sold',
                            data: data.trend.data,
                            borderColor: '#0f172a',
                            backgroundColor: 'rgba(15, 23, 42, 0.04)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#0f172a',
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, border: { display: false }, grid: { color: '#f1f5f9' } },
                            x: { ticks: { font: { size: 10 } }, border: { display: false }, grid: { display: false } }
                        }
                    }
                });

                // Build transactions list
                const txContainer = document.getElementById('salesHistoryTransactions');
                if (!data.transactions || data.transactions.length === 0) {
                    txContainer.innerHTML = '<p class="text-sm text-slate-500 py-8 text-center">No recent transactions.</p>';
                    return;
                }
                let html = '<table class="w-full text-sm text-left"><thead><tr class="bg-slate-50 border-b border-slate-100"><th class="px-4 py-2 font-medium text-slate-500">Date</th><th class="px-4 py-2 font-medium text-slate-500">Invoice</th><th class="px-4 py-2 text-right font-medium text-slate-500">Qty</th><th class="px-4 py-2 text-right font-medium text-slate-500">Total</th></tr></thead><tbody class="divide-y divide-slate-100">';
                data.transactions.forEach(tx => {
                    html += `<tr class="hover:bg-slate-50/50"><td class="px-4 py-2.5 text-slate-600">${tx.date}</td><td class="px-4 py-2.5 text-slate-500 font-mono text-xs">${tx.invoice || '—'}</td><td class="px-4 py-2.5 text-right text-slate-700">${tx.quantity}</td><td class="px-4 py-2.5 text-right font-medium text-slate-700">₱${Number(tx.total).toLocaleString('en-PH', {minimumFractionDigits:0})}</td></tr>`;
                });
                html += '</tbody></table>';
                txContainer.innerHTML = html;
            })
            .catch(err => {
                document.getElementById('salesHistoryTransactions').innerHTML = '<p class="text-sm text-rose-500 py-4 text-center">Failed to load sales history.</p>';
            });
    }

    function closeSalesHistoryModal() {
        document.getElementById('salesHistoryModal').classList.add('hidden');
        if (salesTrendChartInstance) { salesTrendChartInstance.destroy(); salesTrendChartInstance = null; }
    }

    // ───── Discount Modal ─────
    function openDiscountModal(deadStockId, productName, currentPrice) {
        document.getElementById('discountProductName').textContent = productName;
        document.getElementById('discountCurrentPrice').textContent = '₱' + Number(currentPrice).toLocaleString('en-PH', {minimumFractionDigits:2});
        document.getElementById('discountForm').action = `/dss/dead-stock/${deadStockId}/apply-discount`;
        document.getElementById('discountValue').value = '';
        document.querySelectorAll('.discount-preset-btn').forEach(b => {
            b.classList.remove('border-teal-500', 'bg-teal-50', 'text-teal-700');
            b.classList.add('border-slate-200');
        });
        document.getElementById('discountModal').classList.remove('hidden');
    }

    function closeDiscountModal() {
        document.getElementById('discountModal').classList.add('hidden');
    }

    function setDiscount(type, value, btn) {
        document.getElementById('discountType').value = type;
        document.getElementById('discountValue').value = value;
        document.querySelectorAll('.discount-preset-btn').forEach(b => {
            b.classList.remove('border-teal-500', 'bg-teal-50', 'text-teal-700');
            b.classList.add('border-slate-200');
        });
        btn.classList.remove('border-slate-200');
        btn.classList.add('border-teal-500', 'bg-teal-50', 'text-teal-700');
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSalesHistoryModal();
            closeDiscountModal();
        }
    });
</script>
</x-layouts.app>
