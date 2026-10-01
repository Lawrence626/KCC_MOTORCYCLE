<x-layouts.app :title="__('DSS Recommendation Details')">
<div class="container-fluid py-4 max-w-screen-xl mx-auto w-full space-y-6">
    <!-- Breadcrumb & Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('dss.recommendations.index') }}" class="hover:text-cyan-600 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Back to Recommendations
            </a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">{{ $recommendation->product->name ?? 'Product' }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block px-3 py-1 text-xs font-bold rounded-full {{ match($recommendation->priority) {
                'Critical' => 'bg-red-100 text-red-800 border border-red-200',
                'High' => 'bg-orange-100 text-orange-800 border border-orange-200',
                'Medium' => 'bg-amber-100 text-amber-800 border border-amber-200',
                'Low' => 'bg-slate-100 text-slate-700 border border-slate-200',
                default => 'bg-slate-100 text-slate-700',
            } }}">
                Priority: {{ $recommendation->priority }}
            </span>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
    </div>
    @endif

    @php
        $product = $recommendation->product;
        $meta = $recommendation->metadata ?? [];
        $isReorder = $recommendation->isReorderRecommendation();
        $isFastMoving = ($meta['classification'] ?? '') === 'fast_moving' || $isReorder;
        $salesThisMonth = $meta['sales_this_month'] ?? ($metrics['sales_this_month'] ?? 0);
        $salesPrevMonth = $meta['sales_prev_month'] ?? ($metrics['sales_prev_month'] ?? 0);
        $currentStock = $product->stock_quantity ?? 0;
        $currentReorder = $product->reorder_level ?? 10;
        $suggestedReorder = $meta['suggested_reorder_level'] ?? null;
        $suggestedStock = $meta['suggested_stock_quantity'] ?? null;
        $velocity = $meta['sales_velocity'] ?? ($metrics['daily_velocity'] ?? 0);
        $daysCoverage = $meta['days_of_stock_left'] ?? ($velocity > 0 ? round($currentStock / $velocity, 1) : 999);
        $stockoutRisk = $meta['stockout_risk'] ?? 'none';
    @endphp

    <!-- Recommendation Banner -->
    <div class="p-6 rounded-2xl border {{ $stockoutRisk === 'high' ? 'bg-red-50/70 border-red-200' : ($isFastMoving ? 'bg-amber-50/70 border-amber-200' : 'bg-blue-50/70 border-blue-200') }} shadow-sm">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    @if($stockoutRisk === 'high')
                        <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-red-600 text-white uppercase tracking-wider">
                            High Stockout Warning
                        </span>
                    @elseif($isFastMoving)
                        <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-amber-600 text-white uppercase tracking-wider">
                            Fast-Moving Product
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-blue-600 text-white uppercase tracking-wider">
                            Inventory Assessment
                        </span>
                    @endif
                    <span class="text-xs font-semibold text-slate-500">{{ $recommendation->getTypeLabel() }}</span>
                </div>
                <h2 class="text-xl md:text-2xl font-bold text-slate-900">{{ $recommendation->title }}</h2>
                <p class="text-sm md:text-base text-slate-700 leading-relaxed font-medium">
                    {{ $recommendation->description }}
                </p>
            </div>

            @if(!$recommendation->action_taken_at && $suggestedReorder)
            <div class="flex-shrink-0">
                <form action="{{ route('dss.recommendations.apply-reorder', $recommendation->id) }}" method="POST" onsubmit="return confirm('Apply suggested reorder level of {{ $suggestedReorder }} units?');">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-cyan-600 to-[#145a66] hover:opacity-95 text-white text-sm font-semibold rounded-xl shadow-md transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Apply Suggested Reorder Level ({{ $suggestedReorder }} units)
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Main Grid: Product, Inventory, and Sales Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product & Inventory Overview -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                </svg>
                Product & Stock Details
            </h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Product Name:</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $product->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">SKU:</span>
                    <span class="font-mono text-slate-700">{{ $product->sku ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Category:</span>
                    <span class="text-slate-700">{{ $product->category ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Brand:</span>
                    <span class="text-slate-700">{{ $product->brand ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Unit Price:</span>
                    <span class="font-semibold text-slate-800">₱{{ number_format($product->unit_price ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Current Stock:</span>
                    <span class="font-bold {{ $currentStock <= $currentReorder ? 'text-red-600' : 'text-slate-800' }}">
                        {{ $currentStock }} units
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Current Reorder Level:</span>
                    <span class="font-semibold text-slate-700">{{ $currentReorder }} units</span>
                </div>
                @if($product->suppliers && $product->suppliers->isNotEmpty())
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Supplier:</span>
                    <span class="text-slate-700 text-right">{{ $product->suppliers->pluck('name')->join(', ') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Actual Sales Performance Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                </svg>
                Sales Performance (Actual Data)
            </h3>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-[11px] font-semibold text-slate-500 uppercase">This Month's Sales</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $salesThisMonth }} <span class="text-xs font-normal text-slate-500">units</span></p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Previous Month</p>
                    <p class="text-2xl font-bold text-slate-700 mt-1">{{ $salesPrevMonth }} <span class="text-xs font-normal text-slate-500">units</span></p>
                </div>
            </div>

            <div class="space-y-3 text-sm pt-1">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Sales Velocity:</span>
                    <span class="font-bold text-slate-800">{{ $velocity }} units / day</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Stock Coverage:</span>
                    <span class="font-semibold {{ $daysCoverage <= 14 ? 'text-red-600' : 'text-slate-800' }}">
                        ~{{ $daysCoverage }} days remaining
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Stockout Risk:</span>
                    <span class="font-bold uppercase text-xs {{ $stockoutRisk === 'high' ? 'text-red-600' : ($stockoutRisk === 'medium' ? 'text-orange-600' : 'text-emerald-600') }}">
                        {{ $stockoutRisk }}
                    </span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Sales Velocity (30-day):</span>
                    <span class="text-slate-700">{{ $metrics['units_30_days'] ?? 0 }} units in last 30d</span>
                </div>
            </div>
        </div>

        <!-- DSS Intelligence & Rationale Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18"/>
                </svg>
                DSS Rationale & Action Plan
            </h3>

            <div class="space-y-3">
                @if($suggestedReorder)
                <div class="p-3 rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-900 text-xs space-y-1">
                    <div class="font-bold flex items-center justify-between">
                        <span>Suggested Reorder Level:</span>
                        <span class="text-sm font-extrabold text-cyan-700">{{ $suggestedReorder }} units</span>
                    </div>
                    @if($suggestedStock)
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Target Stock Level:</span>
                        <span class="font-semibold text-slate-800">{{ $suggestedStock }} units</span>
                    </div>
                    @endif
                </div>
                @endif

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Why this recommendation was generated:</p>
                    <p class="text-xs text-slate-700 leading-relaxed">
                        {{ $meta['rationale'] ?? 'Based on real-time evaluation of current month POS sales transactions and inventory levels.' }}
                    </p>
                </div>

                <!-- Action Taken Status / Form -->
                <div class="pt-2">
                    @if($recommendation->action_taken_at)
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
                            <div class="font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                Actioned on {{ $recommendation->action_taken_at->format('M d, Y h:i A') }}
                            </div>
                            @if($recommendation->action_notes)
                                <p class="mt-1 text-slate-600">Notes: {{ $recommendation->action_notes }}</p>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('dss.recommendations.action', $recommendation->id) }}" method="POST" class="space-y-2">
                            @csrf
                            <label class="block text-xs font-semibold text-slate-700">Document Action Taken</label>
                            <textarea name="action_notes" rows="2" class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:ring-2 focus:ring-cyan-500 focus:outline-none" placeholder="e.g. Purchase order PO-2026-001 created, supplier contacted..."></textarea>
                            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                Mark as Actioned
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
