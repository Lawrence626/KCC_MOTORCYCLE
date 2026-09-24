<x-layouts.app :title="__('Dead Stock Detail — ' . ($deadStock->product->name ?? 'Product'))">
<div class="h-full w-full flex flex-col overflow-hidden">
    <div class="flex-1 w-full overflow-y-auto px-4 md:px-6 pt-0 pb-5 space-y-4">

        <div class="w-full px-1 pt-2">
            <div class="flex items-start justify-between">
                <h1 class="text-3xl font-bold text-slate-900">
                    {{ $deadStock->product->description ?? $deadStock->product->name ?? 'Product' }}
                </h1>
                <a href="{{ route('dss.dead-stock.index') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 transition-all shrink-0 self-end">
                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
            <p class="text-xs text-slate-500 mt-1">Dead Stock Analysis & Recommendations</p>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-xs font-bold bg-rose-100 text-rose-700 mt-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>{{ $deadStock->days_without_sale }} Days Unsold</span>
            </span>
        </div>

        @if(session('success'))
        <div id="deadStockShowSuccessAlert" class="rounded-[14px] border border-teal-200 bg-teal-50 px-4 py-3 text-xs font-semibold text-teal-900 flex items-center gap-3">
            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- ═══ COMBINED PRODUCT DETAILS, INVENTORY & ANALYSIS (Sales Analytics Top Selling Products Style) ═══ --}}
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
        <div class="w-full border border-slate-200 relative overflow-hidden rounded-[15px] bg-white shadow-sm flex flex-col justify-start" id="deadStockDetailSection">
            <!-- Header Banner (Dark Header Matching Recommendation with Dropdown) -->
            <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 bg-[#0f172a] gap-3">
                <div>
                    <h2 id="dsTabTitle" class="text-xs font-bold uppercase tracking-wider text-white">Product Information</h2>
                    <p id="dsTabSubtitle" class="text-[11px] text-slate-400 mt-0.5">Specifications, categorization, and identification.</p>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <div class="relative" data-dropdown-wrapper="dsViewMenu">
                        <button type="button"
                                id="dsViewDropdownBtn"
                                onclick="toggleDsViewDropdown(event)"
                                class="inline-flex items-center gap-2 rounded-[10px] border border-[#59b2c2] bg-[#6EC1D1] px-3.5 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-colors min-w-[150px] justify-between cursor-pointer">
                            <span id="dsActiveViewLabel">Product Information</span>
                            <svg id="dsDropdownChevron" class="w-3.5 h-3.5 text-slate-900 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div id="dsViewDropdownMenu"
                             class="hidden absolute top-full right-0 z-50 mt-1.5 w-52 rounded-[12px] border border-slate-700 bg-[#0f172a] shadow-2xl overflow-hidden">
                            <div class="p-1 space-y-0.5">
                                <button type="button"
                                        onclick="selectDsView('product', 'Product Information', 'Product Information', 'Specifications, categorization, and identification.')"
                                        id="dsViewOpt-product"
                                        class="ds-view-option w-full px-3 py-1.5 text-xs font-semibold rounded-[8px] transition-colors text-left bg-slate-700 text-white cursor-pointer">
                                    Product Information
                                </button>
                                <button type="button"
                                        onclick="selectDsView('inventory', 'Inventory Status', 'Inventory Status', 'Assigned warehouse, stock quantity, and valuation.')"
                                        id="dsViewOpt-inventory"
                                        class="ds-view-option w-full px-3 py-1.5 text-xs font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">
                                    Inventory Status
                                </button>
                                <button type="button"
                                        onclick="selectDsView('analysis', 'Dead Stock Analysis', 'Dead Stock Analysis', 'Days without sale, inactive duration, and metrics.')"
                                        id="dsViewOpt-analysis"
                                        class="ds-view-option w-full px-3 py-1.5 text-xs font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer">
                                    Dead Stock Analysis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body Container -->
            <div class="p-6">
                <!-- Tab 1: Product Information -->
                <div id="dsTab-product" class="ds-tab-content space-y-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pb-4 border-b border-slate-100">
                        <div id="deadStockProductImage" class="w-16 h-16 rounded-[12px] bg-slate-50 border border-slate-200/80 flex items-center justify-center flex-shrink-0 text-slate-300 shadow-sm overflow-hidden bg-cover bg-center"
                             data-id="{{ $deadStock->product->id ?? '' }}"
                             data-sku="{{ $deadStock->product->sku ?? '' }}"
                             data-name="{{ $deadStock->product->product_name ?? $deadStock->product->name ?? '' }}">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-slate-900">{{ $deadStock->product->description ?? $deadStock->product->name ?? 'Product' }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-600 font-semibold">{{ $deadStock->product->sku ?? 'N/A' }}</span>
                                @if($deadStock->product->category)
                                <span class="text-xs text-slate-500 font-medium">• Category: <strong class="text-slate-700">{{ $deadStock->product->category }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($infoFields as $label => $value)
                        <div class="p-3.5 rounded-[12px] bg-slate-50/70 border border-slate-200/70">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ $label }}</span>
                            <p class="text-xs font-semibold text-slate-800">{{ $value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tab 2: Inventory Status -->
                <div id="dsTab-inventory" class="ds-tab-content hidden space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Warehouse</span>
                            <p class="text-2xl font-bold text-slate-900 mt-1 truncate">{{ $deadStock->warehouse->name ?? 'Main' }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Assigned storage</p>
                        </div>
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Stock Quantity</span>
                            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $deadStock->current_stock }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Units in warehouse</p>
                        </div>
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Selling Price</span>
                            <p class="text-2xl font-bold text-slate-900 mt-1">₱{{ number_format($product->unit_price, 2) }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Per unit retail price</p>
                        </div>
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Inventory Value</span>
                            <p class="text-2xl font-bold text-slate-900 mt-1">₱{{ number_format($deadStock->stock_value, 2) }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Capital tied up</p>
                        </div>
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Reorder Level</span>
                            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $product->reorder_level ?? 'N/A' }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Threshold trigger</p>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Dead Stock Analysis -->
                <div id="dsTab-analysis" class="ds-tab-content hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80 flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Days Without Sale</span>
                                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $deadStock->days_without_sale }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Inactive inventory duration</p>
                            </div>
                            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                                <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80 flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Last Sold Date</span>
                                <p class="text-2xl font-bold text-slate-900 mt-1">
                                    @if($deadStock->last_sold_date)
                                        {{ $deadStock->last_sold_date->format('M d, Y') }}
                                    @else
                                        <span>Never</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Previous transaction record</p>
                            </div>
                            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                                <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-4 rounded-[14px] bg-slate-50/70 border border-slate-200/80 flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Detected On</span>
                                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $deadStock->detected_at ? $deadStock->detected_at->format('M d, Y') : 'N/A' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Initial dead stock identification</p>
                            </div>
                            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0 ml-2" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                                <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ═══ RECOMMENDATION ═══ --}}
        @php
            $autoRec = $deadStock->getAutomaticRecommendation();
            $product = $deadStock->product;
            $hasDiscount = !empty($product->discount_type) && !empty($product->discount_value);
        @endphp
        <div class="w-full bg-white rounded-[15px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-[#0f172a] px-5 py-3.5 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
                    Recommendation
                </h3>
                @if($hasDiscount)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-[10px] text-xs font-semibold bg-[#6EC1D1]/20 text-[#6EC1D1]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#6EC1D1]"></span>
                        <span>Discount Applied: {{ $product->discount_type === 'percentage' ? $product->discount_value . '%' : '₱' . number_format($product->discount_value, 2) }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-[10px] text-xs font-semibold bg-amber-500/20 text-amber-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span>Action Recommended</span>
                    </span>
                @endif
            </div>

            <div class="p-5 space-y-4">
                {{-- Automatic Recommendation Card --}}
                <div class="p-4 sm:p-5 rounded-[14px] bg-slate-50/70 border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3.5 flex-1 min-w-0">
                        <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                            <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suggested Action</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-[6px] text-xs font-bold border {{ $autoRec['badge_color'] }}">
                                    {{ $autoRec['suggested_discount'] }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 mt-0.5">{{ $autoRec['recommendation'] }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $autoRec['reason'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex-shrink-0 flex items-center gap-2 pt-2 md:pt-0">
                        @if(!$hasDiscount)
                            @if($autoRec['suggested_discount_value'] > 0)
                            <button type="button"
                                    onclick="openShowDiscountModalWithVal({{ $autoRec['suggested_discount_value'] }})"
                                    class="inline-flex items-center justify-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-4 py-2.5 text-xs font-bold text-black shadow-sm hover:bg-[#59b2c2] transition cursor-pointer">
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                                <span>Apply {{ $autoRec['suggested_discount_value'] }}% Discount</span>
                            </button>
                            @else
                            <button type="button"
                                    onclick="openShowDiscountModal()"
                                    class="inline-flex items-center justify-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-4 py-2.5 text-xs font-bold text-black shadow-sm hover:bg-[#59b2c2] transition cursor-pointer">
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                                <span>Apply Discount</span>
                            </button>
                            @endif
                        @else
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        onclick="openShowDiscountModal()"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-[10px] border border-slate-200 bg-white text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition cursor-pointer">
                                    Update Discount
                                </button>
                                <form action="{{ route('dss.dead-stock.remove-discount', $deadStock->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Remove discount from this product?')"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-[10px] border border-rose-200 bg-rose-50 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition cursor-pointer">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Additional insights (bundles, promotions) if present from recommendation engine --}}
                @php
                    $otherRecs = $recommendations->where('recommendation_type', '!=', 'discount');
                @endphp
                @if($otherRecs->isNotEmpty())
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Additional Insights</p>
                    <div class="space-y-2">
                        @foreach($otherRecs as $rec)
                        <div class="p-3.5 rounded-[12px] bg-slate-50/70 border border-slate-200/80 flex items-start justify-between gap-3 shadow-xs">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-[8px] flex items-center justify-center flex-shrink-0 mt-0.5" style="background-color: rgba(110, 193, 209, 0.18);">
                                    @if($rec->recommendation_type === 'promotion')
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                    @elseif($rec->recommendation_type === 'bundle')
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    @elseif($rec->recommendation_type === 'relocate')
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    @elseif($rec->recommendation_type === 'featured_display')
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    @elseif($rec->recommendation_type === 'supplier_return')
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    @else
                                        <svg class="w-4 h-4 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">{{ $rec->getTypeLabel() }}</h4>
                                    <p class="text-xs text-slate-600 mt-0.5">{{ $rec->description }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══ SALES HISTORY ═══ --}}
        <div id="salesHistorySection" class="w-full bg-white rounded-[15px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-[#0f172a] px-5 py-3.5 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">
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
                <div class="rounded-[14px] border border-slate-200 overflow-hidden shadow-xs">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-[#0f172a] text-white border-b border-slate-800">
                                <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider text-white">Date</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider text-white">Invoice</th>
                                <th class="px-3 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider text-white">Quantity</th>
                                <th class="px-3 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider text-white">Unit Price</th>
                                <th class="px-3 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider text-white">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($salesHistory['transactions'] as $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-2.5 text-slate-700 font-medium">{{ $tx['date'] }}</td>
                                <td class="px-3 py-2.5 text-slate-500 font-mono text-xs">{{ $tx['invoice'] ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-right font-bold text-slate-800">{{ $tx['quantity'] }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-600">₱{{ number_format($tx['unit_price'], 2) }}</td>
                                <td class="px-3 py-2.5 text-right font-bold text-slate-800">₱{{ number_format($tx['total'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="py-8 text-center">
                    <p class="text-xs text-slate-400">No sales transactions found for this product.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══ FAST MOVING PRODUCTS (for bundle) ═══ --}}
        @if(!empty($fastMovingProducts) && $fastMovingProducts->isNotEmpty())
        <div class="w-full bg-white rounded-[15px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-[#0f172a] px-5 py-3.5 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#6EC1D1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Top Fast Moving Products — Bundle Partners</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">These products can be bundled with this dead stock item to boost sales</p>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($fastMovingProducts as $fm)
                    <div class="flex items-center gap-3 p-3 rounded-[12px] bg-slate-50/70 border border-slate-200/80 hover:bg-slate-50 transition shadow-xs">
                        <div class="w-9 h-9 rounded-[8px] bg-[rgba(110,193,209,0.18)] flex items-center justify-center text-sm flex-shrink-0">
                            <svg class="w-4.5 h-4.5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $fm->product->name ?? $fm->product->product_name ?? '' }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-slate-400 font-mono">{{ $fm->product->sku ?? '' }}</span>
                                <span class="text-[10px] font-bold text-[#145a66]">Score: {{ $fm->velocity_score }}</span>
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
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[15px] shadow-[0_40px_120px_rgba(15,23,42,0.18)] overflow-hidden" style="border: none;">
        <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Apply Discount</h3>
                <p class="text-xs text-slate-800 mt-0.5">{{ $deadStock->product->description ?? $deadStock->product->name ?? 'Product' }} — ₱{{ number_format($deadStock->product->unit_price ?? 0, 2) }}</p>
            </div>
            <button type="button" onclick="closeShowDiscountModal()" class="rounded-[10px] p-1.5 text-black hover:bg-black/10 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('dss.dead-stock.apply-discount', $deadStock->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Quick Discount</label>
                <div class="grid grid-cols-5 gap-1.5">
                    <button type="button" onclick="setShowDiscount('percentage', 5, this)" class="show-discount-btn px-2.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:border-[#6EC1D1] hover:bg-[#6EC1D1]/10 transition text-center cursor-pointer">5%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 10, this)" class="show-discount-btn px-2.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:border-[#6EC1D1] hover:bg-[#6EC1D1]/10 transition text-center cursor-pointer">10%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 15, this)" class="show-discount-btn px-2.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:border-[#6EC1D1] hover:bg-[#6EC1D1]/10 transition text-center cursor-pointer">15%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 20, this)" class="show-discount-btn px-2.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:border-[#6EC1D1] hover:bg-[#6EC1D1]/10 transition text-center cursor-pointer">20%</button>
                    <button type="button" onclick="setShowDiscount('percentage', 25, this)" class="show-discount-btn px-2.5 py-2.5 rounded-[10px] border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:border-[#6EC1D1] hover:bg-[#6EC1D1]/10 transition text-center cursor-pointer">25%</button>
                </div>
            </div>
            <div class="flex items-center gap-3"><div class="h-px flex-1 bg-slate-200"></div><span class="text-xs text-slate-400">or</span><div class="h-px flex-1 bg-slate-200"></div></div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Custom Amount</label>
                <div class="flex items-center gap-2">
                    <div class="relative w-16 shrink-0" data-dropdown-wrapper="showDiscountTypeDropdown">
                        <input type="hidden" name="discount_type" id="showDiscountType" value="percentage">
                        <button type="button"
                                id="showDiscountTypeBtn"
                                onclick="toggleCustomModalDropdown('showDiscountTypeMenu', event)"
                                class="w-full h-10 px-2.5 rounded-[10px] border border-slate-300 bg-white text-xs font-bold text-slate-800 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm cursor-pointer">
                            <span id="showDiscountTypeDisplay">%</span>
                            <svg id="showDiscountTypeChevron" class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="showDiscountTypeMenu" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-1 w-full rounded-[10px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                            <button type="button"
                                    onclick="selectModalDiscountType('percentage', '%', event)"
                                    class="modal-discount-opt w-full text-center px-2 py-1.5 text-xs font-bold text-slate-800 hover:bg-slate-100 rounded-[6px] transition cursor-pointer">
                                %
                            </button>
                            <button type="button"
                                    onclick="selectModalDiscountType('fixed', '₱', event)"
                                    class="modal-discount-opt w-full text-center px-2 py-1.5 text-xs font-bold text-slate-800 hover:bg-slate-100 rounded-[6px] transition cursor-pointer">
                                ₱
                            </button>
                        </div>
                    </div>
                    <input type="number" name="discount_value" id="showDiscountValue" min="0" step="0.01" placeholder="0" class="flex-1 h-10 px-3 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 focus:border-slate-400 hover:border-slate-400 transition shadow-sm">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeShowDiscountModal()" class="flex-1 rounded-[10px] bg-black/10 px-4 py-2.5 text-xs font-semibold text-slate-900 hover:bg-black/20 transition cursor-pointer">Cancel</button>
                <button type="submit" class="flex-1 rounded-[10px] bg-[#6EC1D1] px-4 py-2.5 text-xs font-bold text-black hover:bg-[#59b2c2] transition cursor-pointer shadow-sm">Apply</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ───── Sales Trend Chart ─────
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
                        backgroundColor: 'rgba(110, 193, 209, 0.25)',
                        borderColor: '#145a66',
                        borderWidth: 1.5,
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
                        imgEl.className = 'w-12 h-12 rounded-[10px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center shadow-sm';
                        imgEl.style.backgroundImage = `url('${imgUrl}')`;
                    }
                }
            }
        } catch(e) {
            console.error('Error loading dead stock product photo:', e);
        }
    });

    // ───── Overview Dropdown View Switching ─────
    function toggleDsViewDropdown(event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById('dsViewDropdownMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function selectDsView(tab, label, title, subtitle) {
        // Switch tab content visibility
        document.querySelectorAll('.ds-tab-content').forEach(el => el.classList.add('hidden'));
        const activeContent = document.getElementById('dsTab-' + tab);
        if (activeContent) activeContent.classList.remove('hidden');

        // Update dropdown button label
        const labelEl = document.getElementById('dsActiveViewLabel');
        if (labelEl) labelEl.textContent = label;

        // Update header title & subtitle
        const titleEl = document.getElementById('dsTabTitle');
        const subtitleEl = document.getElementById('dsTabSubtitle');
        if (titleEl && title) titleEl.textContent = title;
        if (subtitleEl && subtitle) subtitleEl.textContent = subtitle;

        // Update selection styling in dark menu (sales range style)
        document.querySelectorAll('.ds-view-option').forEach(opt => {
            opt.classList.remove('bg-slate-700', 'text-white', 'font-semibold');
            opt.classList.add('text-slate-400', 'font-normal');
        });
        const selectedOpt = document.getElementById('dsViewOpt-' + tab);
        if (selectedOpt) {
            selectedOpt.classList.remove('text-slate-400', 'font-normal');
            selectedOpt.classList.add('bg-slate-700', 'text-white', 'font-semibold');
        }

        // Close dropdown menu
        const menu = document.getElementById('dsViewDropdownMenu');
        if (menu) menu.classList.add('hidden');
    }

    function switchDsTab(tab) {
        const labels = {
            'product': ['Product Information', 'Product Information', 'Specifications, categorization, and identification.'],
            'inventory': ['Inventory Status', 'Inventory Status', 'Assigned warehouse, stock quantity, and valuation.'],
            'analysis': ['Dead Stock Analysis', 'Dead Stock Analysis', 'Days without sale, inactive duration, and metrics.']
        };
        const info = labels[tab] || ['Product Information', 'Product Information', ''];
        selectDsView(tab, info[0], info[1], info[2]);
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('[data-dropdown-wrapper="dsViewMenu"]');
        if (wrapper && !wrapper.contains(e.target)) {
            const menu = document.getElementById('dsViewDropdownMenu');
            if (menu) menu.classList.add('hidden');
        }
        const modalDdWrapper = document.querySelector('[data-dropdown-wrapper="showDiscountTypeDropdown"]');
        if (modalDdWrapper && !modalDdWrapper.contains(e.target)) {
            const menu = document.getElementById('showDiscountTypeMenu');
            if (menu) menu.classList.add('hidden');
        }
    });

    // ───── Discount Modal ─────
    function openShowDiscountModal() { document.getElementById('showDiscountModal').classList.remove('hidden'); }
    function closeShowDiscountModal() {
        document.getElementById('showDiscountModal').classList.add('hidden');
        const menu = document.getElementById('showDiscountTypeMenu');
        if (menu) menu.classList.add('hidden');
    }

    function toggleCustomModalDropdown(menuId, event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById(menuId);
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function selectModalDiscountType(type, symbol, event) {
        if (event) event.stopPropagation();
        const input = document.getElementById('showDiscountType');
        const display = document.getElementById('showDiscountTypeDisplay');
        const menu = document.getElementById('showDiscountTypeMenu');
        if (input) input.value = type;
        if (display) display.textContent = symbol;
        if (menu) menu.classList.add('hidden');
    }

    function setShowDiscount(type, value, btn) {
        const input = document.getElementById('showDiscountType');
        const display = document.getElementById('showDiscountTypeDisplay');
        if (input) input.value = type;
        if (display) display.textContent = type === 'percentage' ? '%' : '₱';
        document.getElementById('showDiscountValue').value = value;
        document.querySelectorAll('.show-discount-btn').forEach(b => b.classList.remove('border-[#6EC1D1]', 'bg-[#6EC1D1]/20', 'text-black'));
        if (btn) {
            btn.classList.add('border-[#6EC1D1]', 'bg-[#6EC1D1]/20', 'text-black');
        }
    }
    function openShowDiscountModalWithVal(val) {
        openShowDiscountModal();
        let matchedBtn = null;
        document.querySelectorAll('.show-discount-btn').forEach(b => {
            if (parseInt(b.textContent) === parseInt(val)) {
                matchedBtn = b;
            }
        });
        setShowDiscount('percentage', val, matchedBtn);
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeShowDiscountModal(); });
</script>
</x-layouts.app>
