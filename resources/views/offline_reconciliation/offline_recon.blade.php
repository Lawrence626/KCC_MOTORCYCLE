<x-layouts.app :title="__('Offline Reconciliation')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Offline Reconciliation</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage offline data synchronization</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <button onclick="window.offlineManager.manualSync()" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Sync Now
                </button>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Dashboard Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs text-amber-600 font-medium">Pending Sync</span>
                </div>
                <p class="text-2xl font-bold text-slate-900" id="pending-sync-count">--</p>
                <p class="text-xs text-slate-500 mt-0.5">Offline transactions</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <span class="text-xs text-blue-600 font-medium">Exported</span>
                </div>
                <p class="text-2xl font-bold text-slate-900" id="exported-count">--</p>
                <p class="text-xs text-slate-500 mt-0.5">Ready for import</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs text-green-600 font-medium">Synchronized</span>
                </div>
                <p class="text-2xl font-bold text-slate-900" id="synchronized-count">--</p>
                <p class="text-xs text-slate-500 mt-0.5">Successfully synced</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="text-xs text-red-600 font-medium">Failed</span>
                </div>
                <p class="text-2xl font-bold text-slate-900" id="failed-count">--</p>
                <p class="text-xs text-slate-500 mt-0.5">Sync errors</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <a href="{{ route('offline.export') }}" class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Export Data</p>
                        <p class="text-cyan-100 text-xs">Export offline transactions to CSV/Excel</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('offline.import') }}" class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Import Data</p>
                        <p class="text-emerald-100 text-xs">Import transactions from CSV/Excel</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Recent Synchronization Activity</h2>
                <a href="{{ route('offline.history') }}" class="text-xs text-cyan-600 hover:text-cyan-700 font-medium">View All</a>
            </div>
            <div class="p-4">
                <div class="space-y-3" id="recent-activity">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-slate-900 font-medium">No recent activity</p>
                            <p class="text-xs text-slate-500">Start by exporting or importing data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <a href="{{ route('offline.inventory-movements') }}" class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Inventory Movements</p>
                        <p class="text-xs text-slate-500">View stock movements</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">Track inventory changes</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('offline.purchase-orders') }}" class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Purchase Orders</p>
                        <p class="text-xs text-slate-500">View offline POs</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">Manage purchase orders</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>

            <a href="{{ route('offline.history') }}" class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Sync History</p>
                        <p class="text-xs text-slate-500">View all syncs</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-600">View synchronization logs</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 mb-1">How it works</p>
                        <p class="text-xs text-slate-600 leading-relaxed">When offline, all transactions are saved locally. Export them when internet is available, then import to the online system for synchronization.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900 mb-1">Data Integrity</p>
                        <p class="text-xs text-slate-600 leading-relaxed">All imports are validated for duplicates and data integrity. Only valid records are synchronized to the online system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/offline-manager.js') }}"></script>
    <script>
        // Load synchronization stats
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route('offline.api.stats') }}')
                .then(response => response.json())
                .then(data => {
                    const stats = data.stats;
                    const pending = stats.pending_sync;

                    // Update counts
                    document.getElementById('pending-sync-count').textContent =
                        (pending.purchase_orders || 0) + (pending.inventory_movements || 0);
                    document.getElementById('exported-count').textContent =
                        (stats.exported.purchase_orders || 0) + (stats.exported.inventory_movements || 0);
                    document.getElementById('synchronized-count').textContent =
                        (stats.synchronized.purchase_orders || 0) + (stats.synchronized.inventory_movements || 0);
                    document.getElementById('failed-count').textContent =
                        (stats.failed.purchase_orders || 0) + (stats.failed.inventory_movements || 0);
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        });
    </script>
</x-layouts.app>
