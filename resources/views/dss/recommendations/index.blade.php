<x-layouts.app :title="__('DSS Recommendations')">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">DSS Recommendations</h1>
                <p class="text-xs text-slate-500 mt-0.5">Intelligent recommendations to move dead stock inventory</p>
            </div>
            <a href="{{ route('dss.dead-stock.index') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
                Dead Stock List
            </a>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- Statistics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-[14px] p-4 shadow-sm">
                <p class="text-xs text-slate-500 font-medium">Total Active</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-[14px] p-4 shadow-sm">
                <p class="text-xs text-slate-500 font-medium">Pending Action</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-[14px] p-4 shadow-sm">
                <p class="text-xs text-slate-500 font-medium">Actioned</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['actioned'] ?? 0 }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-[14px] p-4 shadow-sm">
                <p class="text-xs text-slate-500 font-medium">Completion Rate</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] > 0 ? round(($stats['actioned'] / $stats['total']) * 100) : 0 }}%</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">Filter Recommendations</h2>
            </div>
            <div class="p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                        <select name="status" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="actioned" {{ request('status') === 'actioned' ? 'selected' : '' }}>Actioned</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
                        <select name="type" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            <option value="">All Types</option>
                            <option value="promotion" {{ request('type') === 'promotion' ? 'selected' : '' }}>Promotional Campaign</option>
                            <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>Price Reduction</option>
                            <option value="bundle" {{ request('type') === 'bundle' ? 'selected' : '' }}>Bundle Offer</option>
                            <option value="relocate" {{ request('type') === 'relocate' ? 'selected' : '' }}>Warehouse Relocation</option>
                            <option value="featured_display" {{ request('type') === 'featured_display' ? 'selected' : '' }}>Featured Display</option>
                            <option value="social_media" {{ request('type') === 'social_media' ? 'selected' : '' }}>Social Media</option>
                            <option value="supplier_return" {{ request('type') === 'supplier_return' ? 'selected' : '' }}>Supplier Return</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                        <select name="priority" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                            <option value="">All Priorities</option>
                            <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                            <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Apply Filters
                    </button>
                </form>
            </div>
        </div>

        {{-- Recommendations Table --}}
        <div class="bg-white border border-slate-200 rounded-[14px] shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-800 bg-[#0f172a] flex items-center justify-between rounded-t-[14px]">
                <h2 class="text-xs font-bold uppercase tracking-wider text-white">Recommendations List</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Generated</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recommendations as $recommendation)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('dss.recommendations.show', $recommendation->id) }}" class="font-medium text-slate-900 hover:text-cyan-600 transition-colors">
                                    {{ $recommendation->product->name ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs font-mono">{{ $recommendation->product->sku ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
                                    {{ $recommendation->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $priorityClasses = [
                                        'Critical' => 'bg-red-100 text-red-700',
                                        'High' => 'bg-orange-100 text-orange-700',
                                        'Medium' => 'bg-amber-100 text-amber-700',
                                        'Low' => 'bg-sky-100 text-sky-700',
                                    ];
                                    $cls = $priorityClasses[$recommendation->priority] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold {{ $cls }}">{{ $recommendation->priority }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($recommendation->action_taken_at)
                                    <span class="inline-flex px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Actioned</span>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $recommendation->action_taken_at->format('M d, Y') }}</p>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $recommendation->generated_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('dss.recommendations.show', $recommendation->id) }}" class="inline-flex items-center gap-1 rounded-[8px] border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                    @if(!$recommendation->action_taken_at)
                                    <button type="button" class="actionBtn inline-flex items-center gap-1 rounded-[8px] border border-emerald-200 px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-50 transition-colors" data-id="{{ $recommendation->id }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Action
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400 text-sm">No recommendations found matching your filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recommendations->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $recommendations->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Action Modal --}}
    <div id="actionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-[16px] shadow-2xl w-full max-w-md mx-4">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Mark Recommendation as Actioned</h3>
                <button type="button" id="actionModalClose" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="actionForm" class="p-5">
                @csrf
                <div class="mb-4">
                    <label for="action_notes" class="block text-xs font-medium text-slate-700 mb-1">Action Notes (Optional)</label>
                    <textarea id="action_notes" name="action_notes" rows="3" placeholder="Document what action was taken..." class="w-full rounded-[10px] border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-400 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="actionModalCancelBtn" class="rounded-[10px] border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="rounded-[10px] bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                        Confirm Action
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentRecommendationId = null;
        const actionModal = document.getElementById('actionModal');
        const actionForm = document.getElementById('actionForm');

        document.querySelectorAll('.actionBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                currentRecommendationId = this.dataset.id;
                actionForm.reset();
                actionModal.classList.remove('hidden');
                actionModal.classList.add('flex');
            });
        });

        function closeActionModal() {
            actionModal.classList.add('hidden');
            actionModal.classList.remove('flex');
        }

        document.getElementById('actionModalClose').addEventListener('click', closeActionModal);
        document.getElementById('actionModalCancelBtn').addEventListener('click', closeActionModal);

        actionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const actionNotes = document.getElementById('action_notes').value;
            const url = '{{ route("dss.recommendations.action", ":id") }}'.replace(':id', currentRecommendationId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ action_notes: actionNotes })
            })
            .then(r => r.json())
            .then(function() {
                closeActionModal();
                setTimeout(() => location.reload(), 500);
            })
            .catch(function() {
                alert('Error marking recommendation as actioned. Please try again.');
            });
        });
    </script>
</x-layouts.app>
