<x-layouts.app :title="__('DSS Inventory Recommendations')">
    <div class="container-fluid py-4 max-w-screen-2xl mx-auto w-full space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg bg-amber-500/10 text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900">DSS Inventory Recommendations</h1>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Data-driven Decision Support System analyzing current month sales performance, stock velocity, and reorder levels to prevent stockouts.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('dss.recommendations.recalculate') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-600 to-[#145a66] text-white text-sm font-semibold rounded-lg hover:opacity-95 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                        <span>Run DSS Analysis</span>
                    </button>
                </form>
                <a href="{{ route('dss.dead-stock.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                    </svg>
                    <span>Dead Stock Analysis</span>
                </a>
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

        @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">&times;</button>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Active Recommendations</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Across all products</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>
                    </svg>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fast-Moving & Reorder Alerts</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $stats['fast_moving'] ?? 0 }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">High sales volume / stockout risk</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/>
                    </svg>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Action</p>
                    <h3 class="text-2xl font-bold text-amber-500 mt-1">{{ $stats['pending'] ?? 0 }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Awaiting admin review</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Actioned Recommendations</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['actioned'] ?? 0 }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Applied or reviewed</p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="actioned" {{ request('status') === 'actioned' ? 'selected' : '' }}>Actioned</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Recommendation Type</label>
                    <select name="type" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        <option value="">All Types</option>
                        <option value="reorder_level" {{ request('type') === 'reorder_level' ? 'selected' : '' }}>Reorder Level Adjustment (Fast-Moving)</option>
                        <option value="normal_stock" {{ request('type') === 'normal_stock' ? 'selected' : '' }}>Normal Stock Maintenance</option>
                        <option value="low_sales_review" {{ request('type') === 'low_sales_review' ? 'selected' : '' }}>Low Sales Stock Review</option>
                        <option value="promotion" {{ request('type') === 'promotion' ? 'selected' : '' }}>Promotional Campaign</option>
                        <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>Price Reduction</option>
                        <option value="bundle" {{ request('type') === 'bundle' ? 'selected' : '' }}>Bundle Offer</option>
                        <option value="relocate" {{ request('type') === 'relocate' ? 'selected' : '' }}>Warehouse Relocation</option>
                        <option value="featured_display" {{ request('type') === 'featured_display' ? 'selected' : '' }}>Featured Display</option>
                        <option value="social_media" {{ request('type') === 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="supplier_return" {{ request('type') === 'supplier_return' ? 'selected' : '' }}>Supplier Return</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Priority Level</label>
                    <select name="priority" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        <option value="">All Priorities</option>
                        <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical</option>
                        <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                        <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Filter
                    </button>
                    <a href="{{ route('dss.recommendations.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Recommendations Table Card -->
        <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h5 class="font-bold text-slate-800 text-base">Active Recommendations & Analysis</h5>
                <span class="text-xs font-medium text-slate-500">Showing {{ $recommendations->total() }} results</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
                            <th class="px-5 py-3.5">Product Details</th>
                            <th class="px-4 py-3.5">Current Inventory</th>
                            <th class="px-4 py-3.5">Recommendation Type</th>
                            <th class="px-4 py-3.5">Sales Data / Reason</th>
                            <th class="px-4 py-3.5">Priority</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recommendations as $rec)
                        @php
                            $isReorder = $rec->isReorderRecommendation();
                            $meta = $rec->metadata ?? [];
                            $salesMonth = $meta['sales_this_month'] ?? null;
                            $suggestedReorder = $meta['suggested_reorder_level'] ?? null;
                            $suggestedStock = $meta['suggested_stock_quantity'] ?? null;
                            $velocity = $meta['sales_velocity'] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">
                                    <a href="{{ route('dss.recommendations.show', $rec->id) }}" class="hover:text-cyan-600 transition">
                                        {{ $rec->product->name ?? 'N/A' }}
                                    </a>
                                </div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">
                                    SKU: {{ $rec->product->sku ?? 'N/A' }}
                                    @if($rec->product && $rec->product->category)
                                        &bull; <span class="text-slate-600">{{ $rec->product->category }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-xs space-y-0.5">
                                    <div><span class="text-slate-500">Stock:</span> <span class="font-bold text-slate-800">{{ $rec->product->stock_quantity ?? 0 }} units</span></div>
                                    <div><span class="text-slate-500">Reorder Level:</span> <span class="font-semibold text-slate-700">{{ $rec->product->reorder_level ?? 10 }} units</span></div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @if($isReorder)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        {{ $rec->getTypeLabel() }}
                                    </span>
                                @elseif($rec->recommendation_type === 'normal_stock')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        {{ $rec->getTypeLabel() }}
                                    </span>
                                @elseif($rec->recommendation_type === 'low_sales_review')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        {{ $rec->getTypeLabel() }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $rec->getTypeLabel() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 max-w-xs">
                                <p class="text-xs text-slate-700 line-clamp-2">{{ $rec->description }}</p>
                                @if($isReorder && $suggestedReorder)
                                    <div class="mt-1 text-[11px] font-semibold text-cyan-700">
                                        Suggested Reorder Point: {{ $suggestedReorder }} units
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-block px-2 py-0.5 text-xs font-bold rounded {{ match($rec->priority) {
                                    'Critical' => 'bg-red-100 text-red-800',
                                    'High' => 'bg-orange-100 text-orange-800',
                                    'Medium' => 'bg-amber-100 text-amber-800',
                                    'Low' => 'bg-slate-100 text-slate-700',
                                    default => 'bg-slate-100 text-slate-700',
                                } }}">
                                    {{ $rec->priority }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($rec->action_taken_at)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        Actioned
                                    </span>
                                    <div class="text-[10px] text-slate-400">{{ $rec->action_taken_at->format('M d, Y') }}</div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('dss.recommendations.show', $rec->id) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-xs font-semibold transition" title="View Full Analysis">
                                        Details
                                    </a>
                                    @if(!$rec->action_taken_at && $isReorder && $suggestedReorder)
                                    <form action="{{ route('dss.recommendations.apply-reorder', $rec->id) }}" method="POST" class="inline" onsubmit="return confirm('Apply suggested reorder level of {{ $suggestedReorder }} units for this product?');">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-md text-xs font-semibold shadow-sm transition" title="Apply Suggested Reorder Level">
                                            Apply ({{ $suggestedReorder }})
                                        </button>
                                    </form>
                                    @endif
                                    @if(!$rec->action_taken_at)
                                    <button type="button" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold shadow-sm transition actionBtn" data-id="{{ $rec->id }}" title="Mark as Actioned">
                                        ✓ Action
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-400 py-12">
                                <div class="max-w-sm mx-auto">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                                    </svg>
                                    <p class="text-sm font-semibold text-slate-700">No recommendations found</p>
                                    <p class="text-xs text-slate-500 mt-1">Try changing your filters or click "Run DSS Analysis" above to calculate recommendations based on current sales.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($recommendations->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50">
                {{ $recommendations->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Mark Actioned Modal -->
    <div id="actionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-slate-50 px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-800">Mark Recommendation as Actioned</h3>
                <button type="button" id="actionModalClose" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="actionForm" class="p-5">
                @csrf
                <div class="mb-4">
                    <p class="text-xs text-slate-500 mb-3">Record the action notes taken to address this inventory recommendation.</p>
                    <label for="action_notes" class="block text-xs font-semibold text-slate-700 mb-1">Action Notes (Optional)</label>
                    <textarea id="action_notes" name="action_notes" rows="3" placeholder="e.g. Reorder level updated, supplier purchase order placed, inventory restocked..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" id="actionModalCancelBtn" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition shadow-sm">Confirm Action</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentRecommendationId = null;
        const actionModal = document.getElementById('actionModal');
        const actionForm = document.getElementById('actionForm');

        function openActionModal(id) {
            currentRecommendationId = id;
            if (actionForm) actionForm.reset();
            if (actionModal) {
                actionModal.classList.remove('hidden');
                actionModal.classList.add('flex');
            }
        }

        function closeActionModal() {
            if (actionModal) {
                actionModal.classList.add('hidden');
                actionModal.classList.remove('flex');
            }
        }

        document.querySelectorAll('.actionBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                openActionModal(this.dataset.id);
            });
        });

        const closeBtn = document.getElementById('actionModalClose');
        if (closeBtn) closeBtn.addEventListener('click', closeActionModal);
        const cancelBtn = document.getElementById('actionModalCancelBtn');
        if (cancelBtn) cancelBtn.addEventListener('click', closeActionModal);

        if (actionForm) {
            actionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const actionNotes = document.getElementById('action_notes').value;
                const url = '{{ url("dss/recommendations") }}/' + currentRecommendationId + '/action';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ action_notes: actionNotes })
                })
                .then(r => {
                    if (!r.ok) throw new Error('Action failed');
                    closeActionModal();
                    window.location.reload();
                })
                .catch(function() {
                    alert('Error marking recommendation as actioned. Please try again.');
                });
            });
        }
    </script>
</x-layouts.app>
