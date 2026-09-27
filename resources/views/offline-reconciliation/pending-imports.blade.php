<x-layouts.app :title="__('Pending Imports')">

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
            <div>
                <h1 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight leading-tight">Pending Imports</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Review and approve offline data imports.</p>
            </div>
            <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-1.5 rounded-[12px] border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200 cursor-pointer">
                <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Offline Home</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

        @include('partials.offline-submenu')

        <!-- Flash Alerts & Notifications -->
        @if(session('success'))
            <div id="pendingSuccessAlert" class="rounded-[15px] border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-950">Action Successful!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('pendingSuccessAlert').remove()" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const el = document.getElementById('pendingSuccessAlert');
                    if (el) {
                        el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        setTimeout(function() {
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(-10px)';
                            setTimeout(function() { el.remove(); }, 400);
                        }, 3500);
                    }
                });
            </script>
        @endif

        @if(session('error'))
            <div id="pendingErrorAlert" class="rounded-[15px] border border-red-300 bg-red-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-red-100 text-red-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-950">Action Failed</h3>
                        <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('pendingErrorAlert').remove()" class="text-red-700 hover:text-red-900 p-1 rounded-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    alert("Error: {{ session('error') }}");
                });
            </script>
        @endif

        @php
            $activeStatus = request('status', '');
            $statusLabels = [
                '' => 'All Status',
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ];
            $activeStatusLabel = $statusLabels[$activeStatus] ?? 'All Status';
            $pendingCount = isset($stats) ? $stats['pending'] : $pendingImports->where('status', 'pending')->count();
            $approvedCount = isset($stats) ? $stats['approved'] : $pendingImports->where('status', 'approved')->count();
            $rejectedCount = isset($stats) ? $stats['rejected'] : $pendingImports->where('status', 'rejected')->count();
        @endphp

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <button type="button" onclick="selectPendingStatus('pending', 'Pending')" class="text-left rounded-[20px] border {{ $activeStatus === 'pending' ? 'border-amber-400 ring-2 ring-amber-400/30 bg-amber-50/40' : 'border-slate-200 bg-white hover:border-amber-300' }} p-4 shadow-sm transition cursor-pointer">
                <p class="text-black text-xs font-semibold flex items-center justify-between">
                    <span>Pending Review</span>
                    @if($activeStatus === 'pending')
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded-full">Filtered</span>
                    @endif
                </p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingCount }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Awaiting approval</p>
                </div>
            </button>
            <button type="button" onclick="selectPendingStatus('approved', 'Approved')" class="text-left rounded-[20px] border {{ $activeStatus === 'approved' ? 'border-green-400 ring-2 ring-green-400/30 bg-green-50/40' : 'border-slate-200 bg-white hover:border-green-300' }} p-4 shadow-sm transition cursor-pointer">
                <p class="text-black text-xs font-semibold flex items-center justify-between">
                    <span>Approved</span>
                    @if($activeStatus === 'approved')
                        <span class="text-[10px] font-bold text-green-700 bg-green-100 px-1.5 py-0.5 rounded-full">Filtered</span>
                    @endif
                </p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-green-600">{{ $approvedCount }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Successfully synced</p>
                </div>
            </button>
            <button type="button" onclick="selectPendingStatus('rejected', 'Rejected')" class="text-left rounded-[20px] border {{ $activeStatus === 'rejected' ? 'border-red-400 ring-2 ring-red-400/30 bg-red-50/40' : 'border-slate-200 bg-white hover:border-red-300' }} p-4 shadow-sm transition cursor-pointer">
                <p class="text-black text-xs font-semibold flex items-center justify-between">
                    <span>Rejected</span>
                    @if($activeStatus === 'rejected')
                        <span class="text-[10px] font-bold text-red-700 bg-red-100 px-1.5 py-0.5 rounded-full">Filtered</span>
                    @endif
                </p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-red-600">{{ $rejectedCount }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Declined imports</p>
                </div>
            </button>
        </div>

        <!-- Pending Imports Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-semibold text-slate-900">Import Requests</h2>
                    @if($activeStatus)
                        <span class="text-xs font-medium text-slate-500">Filtered by: <strong class="text-slate-800">{{ $activeStatusLabel }}</strong></span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($activeStatus)
                        <button type="button" onclick="selectPendingStatus('', 'All Status')" class="px-2.5 py-1.5 rounded-[10px] border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-semibold text-slate-600 transition cursor-pointer flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span>Clear Filter</span>
                        </button>
                    @endif
                    <div class="relative z-[20] min-w-[160px]" data-dropdown-wrapper="pendingStatusFilter">
                        <input type="hidden" id="pendingStatusFilter" value="{{ $activeStatus }}" />
                        <button type="button" id="pendingStatusFilterButton" onclick="toggleDropdown('pendingStatusFilterDropdown', event)" class="w-full px-3.5 py-2 rounded-[14px] border border-slate-300 bg-white text-left text-xs sm:text-[13px] text-slate-800 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-2xs cursor-pointer">
                            <span id="pendingStatusFilterLabel" class="font-medium truncate">{{ $activeStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="pendingStatusFilterDropdown" class="dropdown-menu hidden absolute top-full right-0 z-[50] mt-1.5 w-full min-w-[160px] rounded-[14px] border border-slate-200/90 bg-white shadow-xl shadow-slate-200/60 p-1.5 space-y-0.5">
                            <button type="button" onclick="selectPendingStatus('', 'All Status')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] rounded-lg flex items-center justify-between transition cursor-pointer {{ $activeStatus === '' ? 'bg-slate-100 font-semibold text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>All Status</span>
                                @if($activeStatus === '') <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> @endif
                            </button>
                            <button type="button" onclick="selectPendingStatus('pending', 'Pending')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] rounded-lg flex items-center justify-between transition cursor-pointer {{ $activeStatus === 'pending' ? 'bg-amber-50 font-semibold text-amber-900' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>Pending</span>
                                @if($activeStatus === 'pending') <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> @endif
                            </button>
                            <button type="button" onclick="selectPendingStatus('approved', 'Approved')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] rounded-lg flex items-center justify-between transition cursor-pointer {{ $activeStatus === 'approved' ? 'bg-green-50 font-semibold text-green-900' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>Approved</span>
                                @if($activeStatus === 'approved') <svg class="w-3.5 h-3.5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> @endif
                            </button>
                            <button type="button" onclick="selectPendingStatus('rejected', 'Rejected')" class="w-full px-3.5 py-2 text-left text-xs sm:text-[13px] rounded-lg flex items-center justify-between transition cursor-pointer {{ $activeStatus === 'rejected' ? 'bg-red-50 font-semibold text-red-900' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>Rejected</span>
                                @if($activeStatus === 'rejected') <svg class="w-3.5 h-3.5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                    <table class="table-fixed w-full text-left text-xs text-slate-700">
                        <colgroup>
                            <col class="w-[22%]">
                            <col class="w-[13%]">
                            <col class="w-[7%]">
                            <col class="w-[7%]">
                            <col class="w-[7%]">
                            <col class="w-[9%]">
                            <col class="w-[10%]">
                            <col class="w-[25%]">
                        </colgroup>
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white">File Name</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Uploaded By</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Total</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Valid</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Invalid</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Dupes</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Status</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($pendingImports as $pending)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-3 py-2.5 text-slate-900 font-medium truncate" title="{{ $pending->file_name }}">{{ $pending->file_name }}</td>
                                <td class="px-3 py-2.5 text-slate-500 truncate">{{ $pending->uploadedBy?->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-right text-slate-700 font-semibold tabular-nums">{{ $pending->total_records }}</td>
                                <td class="px-3 py-2.5 text-right text-emerald-600 font-semibold tabular-nums">{{ $pending->valid_records }}</td>
                                <td class="px-3 py-2.5 text-right text-rose-500 font-semibold tabular-nums">{{ $pending->invalid_records }}</td>
                                <td class="px-3 py-2.5 text-right text-amber-500 font-semibold tabular-nums">{{ $pending->duplicate_records }}</td>
                                <td class="px-3 py-2.5">
                                    @if($pending->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>Pending
                                        </span>
                                    @elseif($pending->status == 'approved')
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 shrink-0"></span>Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($pending->status == 'pending')
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <button onclick="reviewImport({{ $pending->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:border-slate-300 transition-colors duration-150 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Review
                                        </button>
                                        <form action="{{ route('offline.pending.approve', $pending->id) }}" method="POST" onsubmit="return confirm('Approve this import?');" class="contents">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] border border-emerald-200 bg-emerald-50 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300 transition-colors duration-150 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('offline.pending.reject', $pending->id) }}" method="POST" onsubmit="return confirm('Reject this import?');" class="contents">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] border border-rose-200 bg-rose-50 text-xs font-semibold text-rose-600 hover:bg-rose-100 hover:border-rose-300 transition-colors duration-150 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 6L6 18M6 6l12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                    @elseif($pending->status == 'approved')
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $pending->reviewedBy?->name ?? '—' }}
                                    </span>
                                    @else
                                    <span class="text-xs text-slate-400 italic">No action</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-3 py-10 text-center text-slate-400 text-xs">No import records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

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
        <div id="reviewModal" class="fixed inset-0 hidden items-center justify-center z-50 px-4 py-6">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeModal()"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-slate-200">
                <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1 rounded-md bg-slate-200 text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Review Import Data</h3>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto max-h-[70vh]" id="modalContent">
                    <!-- Content loaded dynamically -->
                </div>
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-[10px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition shadow-sm cursor-pointer">
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
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            currentImportId = null;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        function togglePendingStatusDropdown() {
            document.getElementById('pendingStatusFilterDropdown')?.classList.toggle('hidden');
        }
        function selectPendingStatus(val, label) {
            document.getElementById('pendingStatusFilter').value = val;
            if (label && document.getElementById('pendingStatusFilterLabel')) {
                document.getElementById('pendingStatusFilterLabel').textContent = label;
            }
            document.getElementById('pendingStatusFilterDropdown')?.classList.add('hidden');

            const url = new URL(window.location.href);
            if (val) {
                url.searchParams.set('status', val);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('[data-dropdown-wrapper="pendingStatusFilter"]');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('pendingStatusFilterDropdown')?.classList.add('hidden');
            }
        });
    </script>
</x-layouts.app>
