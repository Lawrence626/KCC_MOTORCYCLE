<x-layouts.app :title="__('Pending Imports')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Pending Imports</h1>
                <p class="text-gray-600 text-sm mt-1">Review and approve offline data imports</p>
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

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Pending Review</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingImports->where('status', 'pending')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Awaiting approval</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Approved</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-green-600">{{ $pendingImports->where('status', 'approved')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Successfully synced</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Rejected</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-red-600">{{ $pendingImports->where('status', 'rejected')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Declined imports</p>
                </div>
            </div>
        </div>

        <!-- Pending Imports Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-slate-900">Import Requests</h2>
                <div class="relative z-[20] min-w-[140px]" data-dropdown-wrapper="pendingStatusFilter">
                    <input type="hidden" id="pendingStatusFilter" value="" />
                    <button type="button" id="pendingStatusFilterButton" onclick="togglePendingStatusDropdown()" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                        <span id="pendingStatusFilterLabel">All Status</span>
                        <svg class="w-4 h-4 text-slate-500 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="pendingStatusFilterDropdown" class="hidden absolute top-full right-0 z-[30] mt-1 w-full min-w-[140px] rounded-[12px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                        <button type="button" onclick="selectPendingStatus('', 'All Status')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">All Status</button>
                        <button type="button" onclick="selectPendingStatus('pending', 'Pending')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Pending</button>
                        <button type="button" onclick="selectPendingStatus('approved', 'Approved')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Approved</button>
                        <button type="button" onclick="selectPendingStatus('rejected', 'Rejected')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Rejected</button>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a]">
                            <tr>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">File Name</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Uploaded By</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Total</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Valid</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Invalid</th>
                                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Duplicates</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Status</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
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
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $pendingImports->total() > 0 ? $pendingImports->firstItem() : 0 }} - {{ $pendingImports->total() > 0 ? $pendingImports->lastItem() : 0 }} of {{ $pendingImports->total() }} items
                    </p>
                    @if($pendingImports->hasPages())
                        <div class="flex gap-1">
                            @if ($pendingImports->onFirstPage())
                                <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            @else
                                <a href="{{ $pendingImports->previousPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</a>
                            @endif

                            @foreach ($pendingImports->getUrlRange(1, $pendingImports->lastPage()) as $page => $url)
                                @if ($page == $pendingImports->currentPage())
                                    <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($pendingImports->hasMorePages())
                                <a href="{{ $pendingImports->nextPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</a>
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

        function togglePendingStatusDropdown() {
            document.getElementById('pendingStatusFilterDropdown')?.classList.toggle('hidden');
        }
        function selectPendingStatus(val, label) {
            document.getElementById('pendingStatusFilter').value = val;
            document.getElementById('pendingStatusFilterLabel').textContent = label;
            document.getElementById('pendingStatusFilterDropdown')?.classList.add('hidden');
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('[data-dropdown-wrapper="pendingStatusFilter"]');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('pendingStatusFilterDropdown')?.classList.add('hidden');
            }
        });
    </script>
</x-layouts.app>
