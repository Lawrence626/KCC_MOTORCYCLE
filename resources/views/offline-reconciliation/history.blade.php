<x-layouts.app :title="__('Synchronization History')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Synchronization History</h1>
                <p class="text-gray-600 text-sm mt-1">Track all export and import operations</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Offline Home</span>
                </a>
                <form action="{{ route('offline.export.csv') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="sync_history">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-4 py-2 text-sm font-semibold text-black shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export History</span>
                    </button>
                </form>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Search & Filters -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input type="search" placeholder="Search by file name..." class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:border-slate-400 transition shadow-sm" />
                </div>
                <div class="relative z-[20]" data-dropdown-wrapper="historyStatusFilter">
                    <input type="hidden" id="historyStatusFilter" value="" />
                    <button type="button" id="historyStatusFilterButton" onclick="toggleHistoryStatusDropdown()" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                        <span id="historyStatusFilterLabel">All Status</span>
                        <svg class="w-4 h-4 text-slate-500 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="historyStatusFilterDropdown" class="hidden absolute top-full left-0 z-[30] mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                        <button type="button" onclick="selectHistoryStatus('', 'All Status')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">All Status</button>
                        <button type="button" onclick="selectHistoryStatus('pending', 'Pending')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Pending</button>
                        <button type="button" onclick="selectHistoryStatus('completed', 'Completed')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Completed</button>
                        <button type="button" onclick="selectHistoryStatus('failed', 'Failed')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Failed</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-[11px] uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white whitespace-nowrap">File Name</th>
                                <th class="px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Export Date</th>
                                <th class="px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Import Date</th>
                                <th class="px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Exported By</th>
                                <th class="px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Imported By</th>
                                <th class="px-3 py-3 text-right font-semibold text-white whitespace-nowrap">Total</th>
                                <th class="px-3 py-3 text-right font-semibold text-white whitespace-nowrap">Imported</th>
                                <th class="px-3 py-3 text-right font-semibold text-white whitespace-nowrap">Duplicates</th>
                                <th class="px-3 py-3 text-right font-semibold text-white whitespace-nowrap">Failed</th>
                                <th class="px-3 py-3 text-left font-semibold text-white whitespace-nowrap">Status</th>
                                <th class="px-3 py-3 text-center font-semibold text-white whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-xs bg-white">
                            @forelse($history as $sync)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2.5 text-slate-900 font-medium">{{ $sync->file_name }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $sync->export_date ? $sync->export_date->format('M d, Y H:i') : '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $sync->import_date ? $sync->import_date->format('M d, Y H:i') : '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $sync->exportedBy?->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $sync->importedBy?->name ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-900 font-bold">{{ $sync->total_records }}</td>
                                <td class="px-3 py-2.5 text-right text-emerald-600 font-bold">{{ $sync->imported_records }}</td>
                                <td class="px-3 py-2.5 text-right text-amber-600 font-bold">{{ $sync->duplicate_records }}</td>
                                <td class="px-3 py-2.5 text-right text-rose-600 font-bold">{{ $sync->failed_records }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold @if($sync->synchronization_status == 'completed') bg-emerald-100 text-emerald-800 @elseif($sync->synchronization_status == 'failed') bg-rose-100 text-rose-800 @else bg-slate-100 text-slate-700 @endif">
                                        {{ ucfirst($sync->synchronization_status) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($sync->synchronization_status == 'completed')
                                        <a href="{{ route('offline.report', $sync->id) }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-[8px] bg-cyan-50 text-cyan-700 hover:bg-cyan-100 transition">View Report</a>
                                        @endif
                                        <form action="{{ route('offline.history.destroy', $sync->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this synchronization history?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="px-3 py-8 text-center text-slate-500">No synchronization history found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $history->total() > 0 ? $history->firstItem() : 0 }} - {{ $history->total() > 0 ? $history->lastItem() : 0 }} of {{ $history->total() }} items
                    </p>
                    @if($history->hasPages())
                        <div class="flex gap-1">
                            @if ($history->onFirstPage())
                                <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            @else
                                <a href="{{ $history->previousPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</a>
                            @endif

                            @foreach ($history->getUrlRange(1, $history->lastPage()) as $page => $url)
                                @if ($page == $history->currentPage())
                                    <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($history->hasMorePages())
                                <a href="{{ $history->nextPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</a>
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
    function toggleHistoryStatusDropdown() {
        document.getElementById('historyStatusFilterDropdown')?.classList.toggle('hidden');
    }
    function selectHistoryStatus(val, label) {
        document.getElementById('historyStatusFilter').value = val;
        document.getElementById('historyStatusFilterLabel').textContent = label;
        document.getElementById('historyStatusFilterDropdown')?.classList.add('hidden');
    }
    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('[data-dropdown-wrapper="historyStatusFilter"]');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('historyStatusFilterDropdown')?.classList.add('hidden');
        }
    });
    </script>
</x-layouts.app>
