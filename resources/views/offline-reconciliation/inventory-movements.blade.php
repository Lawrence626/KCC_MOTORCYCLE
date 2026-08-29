<x-layouts.app :title="__('Inventory Movements')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Inventory Movements</h1>
                <p class="text-gray-600 text-sm mt-1">Manage inventory movements created during offline operations</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Offline Home</span>
                </a>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Pending Sync</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-amber-600">{{ $movements->where('sync_status', 'pending_sync')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Awaiting export</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Exported</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-blue-600">{{ $movements->where('sync_status', 'exported')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Ready for import</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Synchronized</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-green-600">{{ $movements->where('sync_status', 'synchronized')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Successfully synced</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Failed</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-red-600">{{ $movements->where('sync_status', 'failed')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Sync errors</p>
                </div>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div>
                    <input type="text" placeholder="Search by product name or SKU..." class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:border-slate-400 transition shadow-sm" />
                </div>
                <div class="relative z-[20]" data-dropdown-wrapper="movementStatusFilter">
                    <input type="hidden" id="movementStatusFilter" value="" />
                    <button type="button" id="movementStatusFilterButton" onclick="toggleMovementStatusDropdown()" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                        <span id="movementStatusFilterLabel">All Sync Status</span>
                        <svg class="w-4 h-4 text-slate-500 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="movementStatusFilterDropdown" class="hidden absolute top-full left-0 z-[30] mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5 max-h-[220px] overflow-y-auto">
                        <button type="button" onclick="selectMovementStatus('', 'All Sync Status')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">All Sync Status</button>
                        <button type="button" onclick="selectMovementStatus('pending_sync', 'Pending Sync')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Pending Sync</button>
                        <button type="button" onclick="selectMovementStatus('exported', 'Exported')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Exported</button>
                        <button type="button" onclick="selectMovementStatus('imported', 'Imported')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Imported</button>
                        <button type="button" onclick="selectMovementStatus('synchronized', 'Synchronized')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Synchronized</button>
                        <button type="button" onclick="selectMovementStatus('duplicate', 'Duplicate')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Duplicate</button>
                        <button type="button" onclick="selectMovementStatus('failed', 'Failed')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Failed</button>
                    </div>
                </div>
                <div>
                    <a href="{{ route('offline.export') }}" class="inline-flex items-center justify-center gap-2 w-full px-3 py-[11px] rounded-[12px] border border-slate-200 bg-white text-xs font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">
                        <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export Data</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white">Product</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Type</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Sync Status</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Quantity Change</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Unit Price</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Supplier</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($movements as $movement)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2.5 text-slate-900 font-medium">{{ $movement->product?->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($movement->type == 'in') bg-green-100 text-green-700 @elseif($movement->type == 'out') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($movement->sync_status == 'pending_sync') bg-amber-100 text-amber-700 @elseif($movement->sync_status == 'exported') bg-blue-100 text-blue-700 @elseif($movement->sync_status == 'synchronized') bg-green-100 text-green-700 @elseif($movement->sync_status == 'duplicate') bg-red-100 text-red-700 @elseif($movement->sync_status == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                        {{ str_replace('_', ' ', ucfirst($movement->sync_status)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right text-slate-900 font-medium">{{ $movement->quantity_change }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-900 font-medium">₱{{ number_format($movement->unit_price, 2) }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $movement->supplier_name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-slate-500">No inventory movements found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $movements->total() > 0 ? $movements->firstItem() : 0 }} - {{ $movements->total() > 0 ? $movements->lastItem() : 0 }} of {{ $movements->total() }} items
                    </p>
                    @if($movements->hasPages())
                        <div class="flex gap-1">
                            @if ($movements->onFirstPage())
                                <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            @else
                                <a href="{{ $movements->previousPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</a>
                            @endif

                            @foreach ($movements->getUrlRange(1, $movements->lastPage()) as $page => $url)
                                @if ($page == $movements->currentPage())
                                    <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($movements->hasMorePages())
                                <a href="{{ $movements->nextPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</a>
                            @else
                                <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</button>
                            @endif
                        </div>
                    @else
                        <div class="flex gap-1">
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">1</button>
                            <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">Next →</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMovementStatusDropdown() {
            document.getElementById('movementStatusFilterDropdown')?.classList.toggle('hidden');
        }
        function selectMovementStatus(val, label) {
            document.getElementById('movementStatusFilter').value = val;
            document.getElementById('movementStatusFilterLabel').textContent = label;
            document.getElementById('movementStatusFilterDropdown')?.classList.add('hidden');
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('[data-dropdown-wrapper="movementStatusFilter"]');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('movementStatusFilterDropdown')?.classList.add('hidden');
            }
        });
    </script>
</x-layouts.app>
