<x-layouts.app :title="__('Offline Reconciliation')">

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">

            <div>
                <h1 class="text-3xl font-bold text-slate-900">Offline Reconciliation</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage offline data synchronization and system logs.</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <button onclick="window.offlineManager && window.offlineManager.manualSync ? window.offlineManager.manualSync() : alert('Sync in progress...')" class="inline-flex items-center gap-1.5 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Sync Now</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">

        @include('partials.offline-submenu')

        <!-- Flash Alerts & Notifications -->
        @if(session('success'))
            <div id="overviewSuccessAlert" class="rounded-[15px] border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-950">Success!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('overviewSuccessAlert').remove()" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const el = document.getElementById('overviewSuccessAlert');
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
            <div id="overviewErrorAlert" class="rounded-[15px] border border-red-300 bg-red-50 p-4 shadow-sm flex items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-red-100 text-red-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-950">Error</h3>
                        <p class="text-xs text-red-700 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('overviewErrorAlert').remove()" class="text-red-700 hover:text-red-900 p-1 rounded-md transition cursor-pointer">
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

        <!-- Dashboard Metrics Widgets -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-black text-xs font-semibold">Pending Sync</p>
                        <div class="mt-1">
                            <p id="pending-sync-count" class="text-2xl font-bold text-black">--</p>
                            <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Offline transactions</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-black text-xs font-semibold">Exported Data</p>
                        <div class="mt-1">
                            <p id="exported-count" class="text-2xl font-bold text-black">--</p>
                            <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Ready for import</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-black text-xs font-semibold">Synchronized</p>
                        <div class="mt-1">
                            <p id="synchronized-count" class="text-2xl font-bold text-black">--</p>
                            <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Successfully synced</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-black text-xs font-semibold">Sync Errors</p>
                        <div class="mt-1">
                            <p id="failed-count" class="text-2xl font-bold text-black">--</p>
                            <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Failed sync attempts</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                        <svg class="w-5 h-5 text-[#145a66]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="{{ Route::has('offline.export') ? route('offline.export') : '#' }}" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition group cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#6EC1D1] text-black shadow-sm font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 group-hover:text-slate-700 transition">Export Data</p>
                        <p class="text-xs text-slate-500">Export offline transactions to CSV / Excel</p>
                    </div>
                    <span class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-[10px] bg-slate-100 text-slate-700 group-hover:bg-[#6EC1D1] group-hover:text-black transition">
                        Export
                    </span>
                </div>
            </a>

            <a href="{{ Route::has('offline.import') ? route('offline.import') : '#' }}" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition group cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#0f172a] text-white shadow-sm font-bold shrink-0">
                        <svg class="w-5 h-5 text-[#6EC1D1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 group-hover:text-slate-700 transition">Import Data</p>
                        <p class="text-xs text-slate-500">Import transactions from CSV / Excel</p>
                    </div>
                    <span class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-[10px] bg-slate-100 text-slate-700 group-hover:bg-[#0f172a] group-hover:text-white transition">
                        Import
                    </span>
                </div>
            </a>
        </div>

        <!-- Database Orders Table (System Records) -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        <span>Database Orders (System Records)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Orders synced or imported into the central system database</p>
                </div>

                <!-- Search & Filters -->
                <form method="GET" action="{{ route('offline.reconciliation') }}" class="flex flex-wrap items-center gap-2">
                    <div class="relative min-w-[220px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search PO # or supplier..." class="w-full pl-8 pr-3 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <select name="sync_status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-700 focus:outline-none shadow-2xs cursor-pointer">
                        <option value="">All Sync Status</option>
                        <option value="pending_sync" {{ request('sync_status') == 'pending_sync' ? 'selected' : '' }}>Pending Sync</option>
                        <option value="exported" {{ request('sync_status') == 'exported' ? 'selected' : '' }}>Exported</option>
                        <option value="imported" {{ request('sync_status') == 'imported' ? 'selected' : '' }}>Imported</option>
                        <option value="synchronized" {{ request('sync_status') == 'synchronized' ? 'selected' : '' }}>Synchronized</option>
                        <option value="duplicate" {{ request('sync_status') == 'duplicate' ? 'selected' : '' }}>Duplicate</option>
                        <option value="failed" {{ request('sync_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>

                    <button type="submit" class="px-3.5 py-1.5 rounded-[10px] border border-slate-200 bg-slate-100 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                        Filter
                    </button>
                    @if(request('search') || request('sync_status'))
                        <a href="{{ route('offline.reconciliation') }}" class="px-3 py-1.5 rounded-[10px] border border-slate-200 bg-white text-xs font-semibold text-slate-500 hover:bg-slate-50 transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-hidden rounded-[12px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Order Number</th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Supplier</th>
                                <th class="px-3.5 py-3 text-center font-semibold text-white">Items Count</th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">PO Status</th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Sync Status</th>
                                <th class="px-3.5 py-3 text-right font-semibold text-white">Total Amount</th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                        @if(isset($purchaseOrders) && $purchaseOrders->count() > 0)
                            @foreach($purchaseOrders as $po)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-3.5 py-3 text-slate-900 font-bold font-mono">{{ $po->order_number }}</td>
                                <td class="px-3.5 py-3 text-slate-700 font-medium">{{ $po->supplier_name }}</td>
                                <td class="px-3.5 py-3 text-center text-slate-600 font-semibold">{{ $po->items->count() }}</td>
                                <td class="px-3.5 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold @if($po->status == 'pending') bg-amber-100 text-amber-700 @elseif($po->status == 'approved') bg-blue-100 text-blue-700 @elseif($po->status == 'received') bg-green-100 text-green-700 @else bg-slate-100 text-slate-700 @endif">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold @if($po->sync_status == 'pending_sync') bg-amber-100 text-amber-700 @elseif($po->sync_status == 'exported') bg-blue-100 text-blue-700 @elseif($po->sync_status == 'synchronized') bg-green-100 text-green-700 @elseif($po->sync_status == 'duplicate') bg-red-100 text-red-700 @elseif($po->sync_status == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                        {{ str_replace('_', ' ', ucfirst($po->sync_status)) }}
                                    </span>
                                </td>
                                <td class="px-3.5 py-3 text-right text-slate-900 font-bold font-mono">₱{{ number_format($po->total_amount, 2) }}</td>
                                <td class="px-3.5 py-3 text-slate-600">{{ $po->created_at ? $po->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-3 py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-semibold text-slate-600">No database orders found</span>
                                        <span class="text-[11px] text-slate-400">Synchronized orders from central database will appear here</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <!-- Database Orders Pagination -->
                @if(isset($purchaseOrders) && method_exists($purchaseOrders, 'total'))
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs">
                    <p class="text-slate-600">
                        Showing {{ $purchaseOrders->total() > 0 ? $purchaseOrders->firstItem() : 0 }} - {{ $purchaseOrders->total() > 0 ? $purchaseOrders->lastItem() : 0 }} of {{ $purchaseOrders->total() }} items
                    </p>
                    @if($purchaseOrders->hasPages())
                        <div class="flex gap-1">
                            @if ($purchaseOrders->onFirstPage())
                                <button disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 opacity-50 cursor-not-allowed">← Prev</button>
                            @else
                                <a href="{{ $purchaseOrders->previousPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">← Prev</a>
                            @endif

                            @foreach ($purchaseOrders->getUrlRange(1, $purchaseOrders->lastPage()) as $page => $url)
                                @if ($page == $purchaseOrders->currentPage())
                                    <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">{{ $page }}</button>
                                @else
                                    <a href="{{ $url }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($purchaseOrders->hasMorePages())
                                <a href="{{ $purchaseOrders->nextPageUrl() }}" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Next →</a>
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
                @endif
            </div>
        </div>

        <!-- Recent Activity Table / Feed -->
        <div class="overflow-hidden rounded-[15px] border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-[#0f172a] px-4 py-3 text-white">
                <div>
                    <h2 class="text-sm font-semibold text-white">Recent Synchronization Activity</h2>
                    <p class="text-[11px] text-slate-300">Latest background and manual sync logs</p>
                </div>
                <a href="{{ Route::has('offline.history') ? route('offline.history') : '#' }}" class="rounded-[10px] border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold text-white hover:bg-white/20 transition cursor-pointer">View All Logged</a>
            </div>
            <div class="p-4">
                <div class="space-y-2" id="recent-activity">
                    @if(isset($recentHistories) && $recentHistories->count() > 0)
                        @foreach($recentHistories as $history)
                            <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-[14px] border border-slate-200/80">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-[10px] bg-black/10 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-slate-900 font-semibold truncate">{{ $history->file_name ?? 'Sync Batch #' . $history->id }}</p>
                                        <p class="text-[11px] text-slate-500">
                                            Status: <span class="font-bold text-slate-700">{{ ucfirst($history->synchronization_status ?? 'completed') }}</span> • 
                                            {{ $history->created_at ? $history->created_at->diffForHumans() : 'Recent' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('offline.report', $history->id) }}" class="px-2.5 py-1 text-[11px] font-bold rounded-[8px] bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 shrink-0">Report</a>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-[14px] border border-slate-200/80">
                            <div class="w-7 h-7 rounded-[10px] bg-black/10 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-slate-900 font-semibold">No recent activity</p>
                                <p class="text-[11px] text-slate-500">Start by exporting or importing data</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="{{ Route::has('offline.purchase-orders') ? route('offline.purchase-orders') : '#' }}" class="rounded-[18px] border border-slate-200 p-3.5 bg-white shadow-sm hover:border-[#0f172a] hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-[12px] flex items-center justify-center shrink-0 border border-slate-800 bg-[#0f172a]">
                        <svg class="w-4 h-4 text-[#6EC1D1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Purchase Orders</p>
                        <p class="text-[11px] text-slate-500">View offline POs</p>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-[11px] text-slate-600">
                    <span>Manage purchase orders</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

            <a href="{{ Route::has('offline.history') ? route('offline.history') : '#' }}" class="rounded-[18px] border border-slate-200 p-3.5 bg-white shadow-sm hover:border-[#0f172a] hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-[12px] flex items-center justify-center shrink-0 border border-slate-800 bg-[#0f172a]">
                        <svg class="w-4 h-4 text-[#6EC1D1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Sync History</p>
                        <p class="text-[11px] text-slate-500">View all syncs</p>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-[11px] text-slate-600">
                    <span>View synchronization logs</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="rounded-[18px] border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[10px] bg-black/10 flex items-center justify-center text-black shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 mb-0.5">How it works</p>
                        <p class="text-[11px] text-slate-600 leading-relaxed">When offline, all transactions are saved locally. Export them when internet connection is available, then import to the online system for synchronization.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-[10px] bg-black/10 flex items-center justify-center text-black shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 mb-0.5">Data Integrity</p>
                        <p class="text-[11px] text-slate-600 leading-relaxed">All imports are validated for duplicates and data integrity. Only valid records are synchronized to the online system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/offline-manager.js') }}"></script>
    <script>
        // Load synchronization stats
        document.addEventListener('DOMContentLoaded', function() {
            const statsRoute = '{{ Route::has('offline.api.stats') ? route('offline.api.stats') : url('/api/offline/stats') }}';
            fetch(statsRoute)
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.stats) return;
                    const stats = data.stats;
                    const pending = stats.pending_sync || {};
                    const exported = stats.exported || {};
                    const synchronized = stats.synchronized || {};
                    const failed = stats.failed || {};

                    // Update counts safely
                    const pendingEl = document.getElementById('pending-sync-count');
                    const exportedEl = document.getElementById('exported-count');
                    const synchronizedEl = document.getElementById('synchronized-count');
                    const failedEl = document.getElementById('failed-count');

                    if (pendingEl) pendingEl.textContent = pending.purchase_orders || 0;
                    if (exportedEl) exportedEl.textContent = exported.purchase_orders || 0;
                    if (synchronizedEl) synchronizedEl.textContent = synchronized.purchase_orders || 0;
                    if (failedEl) failedEl.textContent = failed.purchase_orders || 0;
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        });

        // Notification panel toggle
        window.toggleNotificationPanel = function(e) {
            if (e) e.stopPropagation();
            var panel = document.getElementById('notification-panel');
            if (!panel) return;

            // Close profile dropdown first if open
            var profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }

            const isOpen = !panel.classList.contains('hidden');
            if (isOpen) {
                panel.classList.add('hidden');
            } else {
                panel.classList.remove('hidden');
            }
        };

        // Mark all notifications as read
        window.markAllNotificationsRead = function() {
            // Implementation for marking notifications as read
            console.log('Mark all notifications as read');
        };

        // Open all notifications modal
        window.openAllNotificationsModal = function() {
            // Implementation for opening all notifications modal
            console.log('Open all notifications modal');
        };
    </script>
</x-layouts.app>
