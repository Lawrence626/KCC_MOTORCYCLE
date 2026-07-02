<x-layouts.app :title="__('Pending Imports')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Pending Imports</h1>
                <p class="text-xs text-slate-500 mt-0.5">Review and approve offline data imports</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Pending Review</p>
                <p class="text-2xl font-bold text-amber-600">{{ $pendingImports->where('status', 'pending')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Awaiting approval</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ $pendingImports->where('status', 'approved')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Successfully synced</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Rejected</p>
                <p class="text-2xl font-bold text-red-600">{{ $pendingImports->where('status', 'rejected')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Declined imports</p>
            </div>
        </div>

        <!-- Pending Imports Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Import Requests</h2>
                <select class="px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">File Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Uploaded By</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Valid</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Invalid</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Duplicates</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($pendingImports as $pending)
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium">{{ $pending->file_name }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $pending->uploadedBy?->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium">{{ $pending->total_records }}</td>
                            <td class="px-3 py-2 text-right text-green-600 font-medium">{{ $pending->valid_records }}</td>
                            <td class="px-3 py-2 text-right text-red-600 font-medium">{{ $pending->invalid_records }}</td>
                            <td class="px-3 py-2 text-right text-amber-600 font-medium">{{ $pending->duplicate_records }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($pending->status == 'pending') bg-amber-100 text-amber-700 @elseif($pending->status == 'approved') bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($pending->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                @if($pending->status == 'pending')
                                <div class="flex gap-1">
                                    <button onclick="reviewImport({{ $pending->id }})" class="text-cyan-600 hover:text-cyan-700" title="Review">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('offline.pending.approve', $pending->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this import?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-700" title="Approve">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('offline.pending.reject', $pending->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this import?');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-700" title="Reject">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @elseif($pending->status == 'approved')
                                <span class="text-green-600 text-xs">Approved by {{ $pending->reviewedBy?->name ?? '-' }}</span>
                                @else
                                <span class="text-red-600 text-xs">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500">No pending imports found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600">Showing {{ $pendingImports->firstItem() }} to {{ $pendingImports->lastItem() }} of {{ $pendingImports->total() }} items</p>
                {{ $pendingImports->links() }}
            </div>
        </div>

        <!-- Review Modal -->
        <div id="reviewModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Review Import Data</h3>
                    <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto max-h-[70vh]" id="modalContent">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex justify-end gap-2">
                    <button onclick="closeModal()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentImportId = null;

        function reviewImport(id) {
            currentImportId = id;
            const modal = document.getElementById('reviewModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = '<p class="text-slate-500 text-center py-8">Loading...</p>';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            fetch(`/offline-reconciliation/pending-imports/${id}/review`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        content.innerHTML = data.html;
                    } else {
                        content.innerHTML = '<p class="text-red-600 text-center py-8">Error loading data</p>';
                    }
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-red-600 text-center py-8">Error loading data</p>';
                });
        }

        function closeModal() {
            const modal = document.getElementById('reviewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentImportId = null;
        }
    </script>
</x-layouts.app>
