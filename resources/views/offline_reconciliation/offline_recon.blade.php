<x-layouts.app :title="__('Offline Reconciliation')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Offline Reconciliation</h1>
                <p class="text-gray-600 text-sm mt-1">Manage offline data synchronization and system logs</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <button onclick="window.offlineManager && window.offlineManager.manualSync ? window.offlineManager.manualSync() : alert('Sync in progress...')" class="inline-flex items-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-4 py-2 text-sm font-semibold text-black shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Sync Now</span>
                </button>
            </div>
        </div>

        @include('partials.offline-submenu')

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
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <a href="{{ Route::has('offline.inventory-movements') ? route('offline.inventory-movements') : '#' }}" class="rounded-[18px] border border-slate-200 p-3.5 bg-white shadow-sm hover:border-[#0f172a] hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-[12px] flex items-center justify-center shrink-0 border border-slate-800 bg-[#0f172a]">
                        <svg class="w-4 h-4 text-[#6EC1D1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Inventory Movements</p>
                        <p class="text-[11px] text-slate-500">View stock movements</p>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-[11px] text-slate-600">
                    <span>Track inventory changes</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

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

                    if (pendingEl) pendingEl.textContent = (pending.purchase_orders || 0) + (pending.inventory_movements || 0);
                    if (exportedEl) exportedEl.textContent = (exported.purchase_orders || 0) + (exported.inventory_movements || 0);
                    if (synchronizedEl) synchronizedEl.textContent = (synchronized.purchase_orders || 0) + (synchronized.inventory_movements || 0);
                    if (failedEl) failedEl.textContent = (failed.purchase_orders || 0) + (failed.inventory_movements || 0);
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        });
    </script>
</x-layouts.app>
