<x-layouts.app :title="__('Synchronization History')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Synchronization History</h1>
                    <p class="mt-1 text-xs text-slate-500">Track all export and import operations</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-[12px] bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Offline Home
                    </a>
                    <form action="{{ route('offline.export.csv') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="type" value="sync_history">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-[12px] border border-[#00fff2]/40 bg-[#00fff2] text-black hover:bg-[#00e6da] shadow-sm transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export History
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Search & Filters -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <input type="search" placeholder="Search by file name..." class="w-full px-3 py-1.5 text-xs rounded-[12px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition" />
                </div>
                <div>
                    <select class="w-full px-3 py-1.5 text-xs rounded-[12px] border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent cursor-pointer">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="border-b border-slate-200 bg-[#0f172a]">
                        <tr>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">File Name</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Export Date</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Import Date</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Exported By</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Imported By</th>
                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Total</th>
                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Imported</th>
                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Duplicates</th>
                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Failed</th>
                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Status</th>
                            <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Actions</th>
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
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600">Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} items</p>
                <div class="pagination-container">
                    {{ $history->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
