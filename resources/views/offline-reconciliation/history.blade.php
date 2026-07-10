<x-layouts.app :title="__('Synchronization History')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Synchronization History</h1>
                <p class="text-xs text-slate-500 mt-0.5">Track all export and import operations</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('offline.reconciliation') }}" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Offline Home
                </a>
                <form action="{{ route('offline.export.csv') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="sync_history">
                    <button type="submit" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export History
                    </button>
                </form>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg border border-slate-200 p-2.5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <input type="text" placeholder="Search by file name..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                </div>
                <div>
                    <select class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">File Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Export Date</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Import Date</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Exported By</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Imported By</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Imported</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Duplicates</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Failed</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($history as $sync)
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium">{{ $sync->file_name }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $sync->export_date ? $sync->export_date->format('M d, Y H:i') : '-' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $sync->import_date ? $sync->import_date->format('M d, Y H:i') : '-' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $sync->exportedBy?->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $sync->importedBy?->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium">{{ $sync->total_records }}</td>
                            <td class="px-3 py-2 text-right text-green-600 font-medium">{{ $sync->imported_records }}</td>
                            <td class="px-3 py-2 text-right text-amber-600 font-medium">{{ $sync->duplicate_records }}</td>
                            <td class="px-3 py-2 text-right text-red-600 font-medium">{{ $sync->failed_records }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($sync->synchronization_status == 'completed') bg-green-100 text-green-700 @elseif($sync->synchronization_status == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                    {{ ucfirst($sync->synchronization_status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex gap-1">
                                    @if($sync->synchronization_status == 'completed')
                                    <a href="{{ route('offline.report', $sync->id) }}" class="text-cyan-600 hover:text-cyan-700 text-xs font-medium">View Report</a>
                                    @endif
                                    <form action="{{ route('offline.history.destroy', $sync->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this synchronization history?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700" title="Delete">
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
                <p class="text-slate-600">Showing {{ $history->firstItem() }} to {{ $history->lastItem() }} of {{ $history->total() }} items</p>
                {{ $history->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
