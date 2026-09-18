<x-layouts.app :title="__('Dead Stock Detail — ' . ($deadStock->product->name ?? 'Product'))">
<div class="h-full flex flex-col overflow-hidden">
    <div class="flex-1 overflow-y-auto px-4 md:px-6 py-5 space-y-5">

        {{-- ═══ HEADER ═══ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('dss.dead-stock.index') }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">
                        {{ $deadStock->product->description ?? $deadStock->product->name ?? 'Product' }}
                    </h1>
                    <p class="text-sm text-slate-500 mt-0.5">Dead Stock Analysis & Recommendations</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold border border-rose-200 bg-rose-50 text-rose-700">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    {{ $deadStock->days_without_sale }} Days Unsold
                </span>
            </div>
        </div>

        @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- ═══ PRODUCT DETAILS + INVENTORY + ANALYSIS ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Product Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Product Information
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div id="deadStockProductImage" class="w-14 h-14 rounded-[10px] bg-slate-50 border border-slate-200/70 flex items-center justify-center flex-shrink-0 text-slate-300 shadow-sm overflow-hidden bg-cover bg-center"
                             data-id="{{ $deadStock->product->id ?? '' }}"
                             data-sku="{{ $deadStock->product->sku ?? '' }}"
                             data-name="{{ $deadStock->product->product_name ?? $deadStock->product->name ?? '' }}">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $deadStock->product->description ?? $deadStock->product->name ?? 'Product' }}</h4>
                            <p class="text-xs text-slate-400 font-mono mt-0.5 truncate">{{ $deadStock->product->sku ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @php
                        $product = $deadStock->product;
                        $infoFields = [
                            'Description' => $product->description ?? 'N/A',
                            'Brand' => $product->brand ?? 'N/A',
                            'Product Name' => $product->product_name ?? $product->name ?? 'N/A',
                            'Compatible Model' => $product->compatibility ?? 'N/A',
                            'Category' => $product->category ?? 'N/A',
                            'SKU' => $product->sku ?? 'N/A',
                        ];
                    @endphp
                    @foreach($infoFields as $label => $value)
                    <div class="flex justify-between items-start gap-2">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider whitespace-nowrap">{{ $label }}</span>
                        <span class="text-sm text-slate-800 font-medium text-right">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Inventory Status --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Inventory Status
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Warehouse</span>
                        <span class="text-sm text-slate-800 font-medium">{{ $deadStock->warehouse->name ?? 'Main' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Stock Quantity</span>
                        <span class="text-lg font-bold text-slate-800">{{ $deadStock->current_stock }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Selling Price</span>
                        <span class="text-sm text-slate-800 font-medium">₱{{ number_format($product->unit_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Inventory Value</span>
                        <span class="text-lg font-bold text-rose-600">₱{{ number_format($deadStock->stock_value, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Reorder Level</span>
                        <span class="text-sm text-slate-800 font-medium">{{ $product->reorder_level ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Dead Stock Analysis --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/60">
                    <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Dead Stock Analysis
                    </h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Priority Level</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $priorityColors[$deadStock->priority_level] ?? '' }}">
                            {{ $deadStock->priority_level }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Days Without Sale</span>
                        <span class="text-2xl font-extrabold text-rose-600">{{ $deadStock->days_without_sale }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Last Sold Date</span>
                        <span class="text-sm text-slate-800 font-medium">
                            @if($deadStock->last_sold_date)
                                {{ $deadStock->last_sold_date->format('M d, Y') }}
                            @else
                                <span class="text-rose-500">Never</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Detected On</span>
                        <span class="text-sm text-slate-800 font-medium">{{ $deadStock->detected_at ? $deadStock->detected_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Suggested Action</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                            {{ $deadStock->analysis_notes ?? 'Monitor' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ACTION BUTTONS ═══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-700 mb-3">Quick Actions</h3>
            <div class="flex flex-wrap gap-2">
                <button onclick="openShowDiscountModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-semibold hover:bg-emerald-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    Apply Discount
                </button>
                <a href="{{ route('dss.recommendations.index', ['product_id' => $deadStock->product_id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-semibold hover:bg-indigo-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    View All Recommendations
                </a>
                <button onclick="document.getElementById('salesHistorySection').scrollIntoView({behavior:'smooth'})" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-sm font-semibold hover:bg-purple-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    View Sales History
                </button>
            </div>
        </div>

        {{-- ═══ DSS RECOMMENDATIONS ═══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    DSS Recommendations ({{ $recommendations->count() }})
                </h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recommendations as $rec)
                @php
                    $typeIcons = [
                        'promotion' => ['icon' => '📣', 'bg' => 'bg-orange-50 border-orange-200', 'text' => 'text-orange-700'],
                        'discount' => ['icon' => '💰', 'bg' => 'bg-emerald-50 border-emerald-200', 'text' => 'text-emerald-700'],
                        'bundle' => ['icon' => '📦', 'bg' => 'bg-blue-50 border-blue-200', 'text' => 'text-blue-700'],
                        'relocate' => ['icon' => '🏪', 'bg' => 'bg-purple-50 border-purple-200', 'text' => 'text-purple-700'],
                        'featured_display' => ['icon' => '⭐', 'bg' => 'bg-amber-50 border-amber-200', 'text' => 'text-amber-700'],
                        'social_media' => ['icon' => '📱', 'bg' => 'bg-pink-50 border-pink-200', 'text' => 'text-pink-700'],
                        'supplier_return' => ['icon' => '🔄', 'bg' => 'bg-red-50 border-red-200', 'text' => 'text-red-700'],
                    ];
                    $style = $typeIcons[$rec->recommendation_type] ?? ['icon' => '📋', 'bg' => 'bg-slate-50 border-slate-200', 'text' => 'text-slate-700'];
                    $recPriorityColors = [
                        'Critical' => 'bg-red-100 text-red-700',
                        'High' => 'bg-orange-100 text-orange-700',
                        'Medium' => 'bg-amber-100 text-amber-700',
                        'Low' => 'bg-blue-100 text-blue-700',
                    ];
                @endphp
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-lg">{{ $style['icon'] }}</span>
                                <h4 class="text-sm font-bold text-slate-800">{{ $rec->getTypeLabel() }}</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $recPriorityColors[$rec->priority] ?? 'bg-slate-100 text-slate-600' }}">{{ $rec->priority }}</span>
                                @if($rec->action_taken_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">✓ Actioned</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Pending</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $rec->description }}</p>

                            {{-- Bundle products --}}
                            @if($rec->recommendation_type === 'bundle' && isset($rec->metadata['bundle_product_ids']))
                            <div class="mt-3 p-3 rounded-xl bg-blue-50/60 border border-blue-100">
                                <p class="text-xs font-semibold text-blue-700 mb-1.5">Recommended Bundle Partners (Fast Moving)</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($rec->getBundleProducts() as $bundleProduct)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-blue-200 text-xs font-medium text-blue-800">
                                        🔥 {{ $bundleProduct->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Discount metadata --}}
                            @if($rec->recommendation_type === 'discount' && isset($rec->metadata['suggested_discount_min']))
                            <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200">
                                <span class="text-xs font-semibold text-emerald-700">Suggested: {{ $rec->metadata['suggested_discount_min'] }}% – {{ $rec->metadata['suggested_discount_max'] }}%</span>
                            </div>
                            @endif

                            {{-- Action taken --}}
                            @if($rec->action_taken_at)
                            <div class="mt-2 p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                                <p class="text-xs text-emerald-700">
                                    <strong>Actioned:</strong> {{ $rec->action_taken_at->format('M d, Y h:i A') }}
                                    @if($rec->action_notes)
                                        — {{ $rec->action_notes }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>

                        {{-- Action button --}}
                        @if(!$rec->action_taken_at)
                        <form action="{{ route('dss.recommendations.action', $rec->id) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="action_notes" value="Marked as actioned from dead stock detail page">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border {{ $style['bg'] }} {{ $style['text'] }} hover:opacity-80 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Mark Done
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    <p class="text-sm text-slate-500">No recommendations generated yet. Try recalculating the analysis.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ═══ SALES HISTORY ═══ --}}
        <div id="salesHistorySection" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-white">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Sales History
                </h3>
            </div>
            <div class="p-5">
                {{-- Sales Trend Chart --}}
                <div class="mb-5" style="height: 250px;">
                    <canvas id="showSalesTrendChart"></canvas>
                </div>

                {{-- Transaction Table --}}
                @if(!empty($salesHistory['transactions']) && count($salesHistory['transactions']) > 0)
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($salesHistory['transactions'] as $tx)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-2.5 text-slate-700">{{ $tx['date'] }}</td>
                                <td class="px-3 py-2.5 text-slate-500 font-mono text-xs">{{ $tx['invoice'] ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-right font-semibold text-slate-800">{{ $tx['quantity'] }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-600">₱{{ number_format($tx['unit_price'], 2) }}</td>
                                <td class="px-3 py-2.5 text-right font-semibold text-slate-800">₱{{ number_format($tx['total'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="py-8 text-center">
                    <p class="text-sm text-slate-400">No sales transactions found for this product.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══ FAST MOVING PRODUCTS (for bundle) ═══ --}}
        @if(!empty($fastMovingProducts) && $fastMovingProducts->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-white">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="text-lg">🔥</span>
                    Top Fast Moving Products — Bundle Partners
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">These products can be bundled with this dead stock item to boost sales</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($fastMovingProducts as $fm)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50/40 border border-blue-100">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-lg flex-shrink-0">🔥</div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $fm->product->name ?? $fm->product->product_name ?? '' }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-slate-400 font-mono">{{ $fm->product->sku ?? '' }}</span>
                                <span class="text-[10px] font-bold text-blue-600">Score: {{ $fm->velocity_score }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>{{-- end scrollable --}}
</div>

{{-- ═══ SHOW PAGE DISCOUNT MODAL ═══ --}}
<div id="showDiscountModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeShowDiscountModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-white">
            <h3 class="text-lg font-bold text-slate-800">Apply Discount</h3>
            <p class="text-sm text-slate-500 mt-0.5">{{ $deadStock->product->description ?? $deadStock->product->name ?? 'Product' }} — ₱{{ number_format($deadStock->product->unit_price ?? 0, 2) }}</p>
        </div>
        <form action="{{ route('dss.dead-stock.apply-discount', $deadStock->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Quick Discount</label>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" onclick="setShowDiscount('percentage', 10, this)" class="show-discount-btn px-3 py-2.5 rounded-xl border-2 border-slate-200 text-sm font-bold text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">10%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 15, this)" class="show-discount-btn px-3 py-2.5 rounded-xl border-2 border-slate-200 text-sm font-bold text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">15%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 20, this)" class="show-discount-btn px-3 py-2.5 rounded-xl border-2 border-slate-200 text-sm font-bold text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">20%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 25, this)" class="show-discount-btn px-3 py-2.5 rounded-xl border-2 border-slate-200 text-sm font-bold text-slate-700 hover:border-emerald-400 hover:bg-emerald-50 transition text-center">25%</button>
                </div>
            </div>
            <div class="flex items-center gap-3"><div class="h-px flex-1 bg-slate-200"></div><span class="text-xs text-slate-400">or</span><div class="h-px flex-1 bg-slate-200"></div></div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Custom Amount</label>
                <div class="flex items-center gap-2">
                    <select name="discount_type" id="showDiscountType" class="px-3 py-2 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="percentage">%</option>
                        <option value="fixed">₱</option>
                    </select>
                    <input type="number" name="discount_value" id="showDiscountValue" min="0" step="0.01" placeholder="0" class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:border-emerald-400 outline-none">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeShowDiscountModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 shadow-sm transition">Apply</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ───── Sales Trend Chart (server-rendered) ─────
    document.addEventListener('DOMContentLoaded', function() {
        const trendData = @json($salesHistory['trend'] ?? ['labels' => [], 'data' => []]);
        const ctx = document.getElementById('showSalesTrendChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trendData.labels,
                    datasets: [{
                        label: 'Units Sold',
                        data: trendData.data,
                        backgroundColor: 'rgba(139,92,246,0.15)',
                        borderColor: '#8b5cf6',
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
                    }
                }
            });
        }
        // Resolve product photo from localStorage
        try {
            const stored = localStorage.getItem('posProductImages');
            if (stored) {
                const images = JSON.parse(stored);
                const imgEl = document.getElementById('deadStockProductImage');
                if (imgEl) {
                    const id = imgEl.dataset.id;
                    const sku = imgEl.dataset.sku;
                    const name = imgEl.dataset.name;
                    const keys = Object.keys(images);

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
                        imgEl.innerHTML = '';
                        imgEl.className = 'w-14 h-14 rounded-[10px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center shadow-sm';
                        imgEl.style.backgroundImage = `url('${imgUrl}')`;
                    }
                }
            }
        } catch(e) {
            console.error('Error loading dead stock product photo:', e);
        }
    });

    // ───── Discount Modal ─────
    function openShowDiscountModal() { document.getElementById('showDiscountModal').classList.remove('hidden'); }
    function closeShowDiscountModal() { document.getElementById('showDiscountModal').classList.add('hidden'); }
    function setShowDiscount(type, value, btn) {
        document.getElementById('showDiscountType').value = type;
        document.getElementById('showDiscountValue').value = value;
        document.querySelectorAll('.show-discount-btn').forEach(b => b.classList.remove('border-emerald-500', 'bg-emerald-50'));
        btn.classList.add('border-emerald-500', 'bg-emerald-50');
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeShowDiscountModal(); });
</script>
</x-layouts.app>
