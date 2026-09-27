<x-layouts.app :title="__('Synchronization History')">

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight leading-tight">Synchronization History</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Track all export and import operations.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-1.5 rounded-[12px] border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200 cursor-pointer">
                    <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Offline Home</span>
                </a>

                <form action="{{ route('offline.export.csv') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="sync_history">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-[12px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3.5 py-2 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Export History</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">

        @include('partials.offline-submenu')

        <!-- Search & Filters -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('offline.history') }}" id="historyFilterForm" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="relative">
                    <input type="search" name="search" id="historySearchInput" value="{{ request('search') }}" placeholder="Search by file name..." class="w-full pl-8 pr-3 py-2.5 rounded-[14px] border border-slate-300 bg-white text-xs sm:text-[13px] text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400 hover:border-slate-400 transition shadow-2xs" />
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="relative z-[20]" data-dropdown-wrapper="historyStatusFilter">
                    <input type="hidden" name="status" id="historyStatusFilter" value="{{ request('status', '') }}" />
                    @php
                        $histStatuses = [
                            '' => 'All Status',
                            'pending' => 'Pending',
                            'completed' => 'Completed',
                            'failed' => 'Failed',
                        ];
                        $curHistStatus = request('status', '');
                        $curHistLabel = $histStatuses[$curHistStatus] ?? 'All Status';
                    @endphp
                    <button type="button" id="historyStatusFilterButton" onclick="toggleDropdown('historyStatusFilterDropdown', event)" class="w-full px-3.5 py-2.5 rounded-[14px] border border-slate-300 bg-white text-left text-xs sm:text-[13px] text-slate-800 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-2xs cursor-pointer">
                        <span id="historyStatusFilterLabel">{{ $curHistLabel }}</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="historyStatusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[50] mt-1.5 w-full rounded-[14px] border border-slate-200/90 bg-white shadow-xl shadow-slate-200/60 p-1.5 space-y-0.5">
                        @foreach($histStatuses as $hVal => $hLbl)
                            <button type="button" onclick="selectHistoryStatus('{{ $hVal }}', '{{ $hLbl }}', event)" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] text-slate-700 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition font-normal cursor-pointer {{ $curHistStatus === $hVal ? 'bg-slate-100 font-semibold text-slate-900' : '' }}">{{ $hLbl }}</button>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="w-full overflow-hidden">
                    <table class="w-full table-fixed text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-[10.5px] uppercase tracking-wider text-white">
                            <tr>
                                <th class="w-[18%] px-2.5 py-3 text-left font-semibold text-white truncate">File Name</th>
                                <th class="w-[11%] px-2 py-3 text-left font-semibold text-white truncate">Export Date</th>
                                <th class="w-[11%] px-2 py-3 text-left font-semibold text-white truncate">Import Date</th>
                                <th class="w-[9%] px-2 py-3 text-left font-semibold text-white truncate">Exported By</th>
                                <th class="w-[9%] px-2 py-3 text-left font-semibold text-white truncate">Imported By</th>
                                <th class="w-[6%] px-1.5 py-3 text-right font-semibold text-white truncate">Total</th>
                                <th class="w-[6%] px-1.5 py-3 text-right font-semibold text-white truncate">Imported</th>
                                <th class="w-[6%] px-1.5 py-3 text-right font-semibold text-white truncate">Duplicates</th>
                                <th class="w-[6%] px-1.5 py-3 text-right font-semibold text-white truncate">Failed</th>
                                <th class="w-[8%] px-1.5 py-3 text-center font-semibold text-white truncate">Status</th>
                                <th class="w-[10%] px-1.5 py-3 text-center font-semibold text-white truncate">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-xs bg-white">
                            @forelse($history as $sync)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="w-[18%] px-2.5 py-2.5 text-slate-900 font-medium truncate" title="{{ $sync->file_name }}">{{ $sync->file_name }}</td>
                                <td class="w-[11%] px-2 py-2.5 text-slate-600 truncate">{{ $sync->export_date ? $sync->export_date->format('M d, Y H:i') : '-' }}</td>
                                <td class="w-[11%] px-2 py-2.5 text-slate-600 truncate">{{ $sync->import_date ? $sync->import_date->format('M d, Y H:i') : '-' }}</td>
                                <td class="w-[9%] px-2 py-2.5 text-slate-600 truncate" title="{{ $sync->exportedBy?->name ?? '-' }}">{{ $sync->exportedBy?->name ?? '-' }}</td>
                                <td class="w-[9%] px-2 py-2.5 text-slate-600 truncate" title="{{ $sync->importedBy?->name ?? '-' }}">{{ $sync->importedBy?->name ?? '-' }}</td>
                                <td class="w-[6%] px-1.5 py-2.5 text-right text-slate-900 font-bold truncate">{{ $sync->total_records }}</td>
                                <td class="w-[6%] px-1.5 py-2.5 text-right text-emerald-600 font-bold truncate">{{ $sync->imported_records }}</td>
                                <td class="w-[6%] px-1.5 py-2.5 text-right text-amber-600 font-bold truncate">{{ $sync->duplicate_records }}</td>
                                <td class="w-[6%] px-1.5 py-2.5 text-right text-rose-600 font-bold truncate">{{ $sync->failed_records }}</td>
                                <td class="w-[8%] px-1.5 py-2.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold @if($sync->synchronization_status == 'completed') bg-emerald-100 text-emerald-800 @elseif($sync->synchronization_status == 'failed') bg-rose-100 text-rose-800 @else bg-slate-100 text-slate-700 @endif">
                                        {{ ucfirst($sync->synchronization_status) }}
                                    </span>
                                </td>
                                <td class="w-[10%] px-1.5 py-2.5 text-center whitespace-nowrap overflow-hidden">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        @if($sync->synchronization_status == 'completed')
                                        <a href="{{ route('offline.report', $sync->id) }}" class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold rounded-[6px] bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border border-cyan-200/60 shadow-sm transition shrink-0">Report</a>
                                        @else
                                        <span class="inline-block w-[45px] text-center text-slate-300 text-xs shrink-0">—</span>
                                        @endif
                                        <form action="{{ route('offline.history.destroy', $sync->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this synchronization history?');" class="inline-flex items-center shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded transition inline-flex items-center justify-center" title="Delete record">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <script>
    function selectHistoryStatus(val, label, e) {
        if (e) e.stopPropagation();
        const input = document.getElementById('historyStatusFilter');
        const labelEl = document.getElementById('historyStatusFilterLabel');
        const menu = document.getElementById('historyStatusFilterDropdown');
        if (input) input.value = val;
        if (labelEl) labelEl.textContent = label;
        if (menu) menu.classList.add('hidden');
        const form = document.getElementById('historyFilterForm');
        if (form) form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('historySearchInput');
        const form = document.getElementById('historyFilterForm');
        let debounceTimer;
        if (searchInput && form) {
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }
    });
    </script>
</x-layouts.app>
