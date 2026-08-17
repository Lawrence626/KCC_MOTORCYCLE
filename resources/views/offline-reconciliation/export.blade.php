<x-layouts.app :title="__('Export Data')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Export Data</h1>
                <p class="text-gray-600 text-sm mt-1">Export offline transactions for synchronization</p>
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

        <!-- Export Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Pending POs</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingPurchaseOrders }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Purchase orders to export</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Pending Movements</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-amber-600">{{ $pendingInventoryMovements }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Inventory movements to export</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Exported POs</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-blue-600">{{ $exportedPurchaseOrders }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Already exported</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 shadow-sm" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <p class="text-black text-xs font-semibold">Exported Movements</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-blue-600">{{ $exportedInventoryMovements }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Already exported</p>
                </div>
            </div>
        </div>

        <!-- Export Form -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('offline.export.csv') }}" method="POST">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Export Configuration</h2>
                    <div class="flex items-center gap-2">
                        <button type="submit" formaction="{{ route('offline.export.excel') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-[#00fff2]/40 bg-[#00fff2] px-4 py-2 text-sm font-semibold text-black shadow-sm hover:bg-[#00e6da] focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Export as Excel</span>
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Export as CSV</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative z-[20]" data-dropdown-wrapper="exportTypeFilter">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Export Type</label>
                        <input type="hidden" name="type" id="exportType" value="all" />
                        <button type="button" id="exportTypeButton" onclick="toggleExportTypeDropdown()" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                            <span id="exportTypeLabel">All Transactions</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="exportTypeDropdown" class="hidden absolute top-full left-0 z-[30] mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                            <button type="button" onclick="selectExportType('all', 'All Transactions')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">All Transactions</button>
                            <button type="button" onclick="selectExportType('purchase_orders', 'Purchase Orders Only')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Purchase Orders Only</button>
                            <button type="button" onclick="selectExportType('inventory_movements', 'Inventory Movements Only')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Inventory Movements Only</button>
                        </div>
                    </div>
                    <div class="relative z-[20]" data-dropdown-wrapper="exportSyncStatusFilter">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sync Status</label>
                        <input type="hidden" name="sync_status" id="exportSyncStatus" value="pending_sync" />
                        <button type="button" id="exportSyncStatusButton" onclick="toggleExportSyncStatusDropdown()" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                            <span id="exportSyncStatusLabel">Pending Sync</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="exportSyncStatusDropdown" class="hidden absolute top-full left-0 z-[30] mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                            <button type="button" onclick="selectExportSyncStatus('pending_sync', 'Pending Sync')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Pending Sync</button>
                            <button type="button" onclick="selectExportSyncStatus('exported', 'Exported')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Exported</button>
                            <button type="button" onclick="selectExportSyncStatus('all', 'All Statuses')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">All Statuses</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Recent Exports -->
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-[#0f172a] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-sm font-semibold text-white">Export Information</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('offline.purchase-orders') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 hover:border-slate-600 transition-all duration-200">
                        <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>View Purchase Orders</span>
                    </a>
                    <a href="{{ route('offline.import') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 hover:border-slate-600 transition-all duration-200">
                        <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Import Data</span>
                    </a>
                    <a href="{{ route('offline.history') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-slate-700 hover:border-slate-600 transition-all duration-200">
                        <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Sync History</span>
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Export Format</p>
                            <p class="text-xs text-slate-600 mt-1">Choose between CSV or Excel (.xlsx) format for exporting your offline transactions.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">File Naming</p>
                            <p class="text-xs text-slate-600 mt-1">Exported files are automatically named with the format: offline_transactions_YYYY_MM_DD.csv or .xlsx</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Sync Status Update</p>
                            <p class="text-xs text-slate-600 mt-1">After export, records are automatically marked as "Exported" in the system.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleExportTypeDropdown() {
        document.getElementById('exportSyncStatusDropdown')?.classList.add('hidden');
        document.getElementById('exportTypeDropdown').classList.toggle('hidden');
    }
    function selectExportType(val, label) {
        document.getElementById('exportType').value = val;
        document.getElementById('exportTypeLabel').textContent = label;
        document.getElementById('exportTypeDropdown').classList.add('hidden');
    }
    function toggleExportSyncStatusDropdown() {
        document.getElementById('exportTypeDropdown')?.classList.add('hidden');
        document.getElementById('exportSyncStatusDropdown').classList.toggle('hidden');
    }
    function selectExportSyncStatus(val, label) {
        document.getElementById('exportSyncStatus').value = val;
        document.getElementById('exportSyncStatusLabel').textContent = label;
        document.getElementById('exportSyncStatusDropdown').classList.add('hidden');
    }
    document.addEventListener('click', function(e) {
        const wrapper1 = document.querySelector('[data-dropdown-wrapper="exportTypeFilter"]');
        if (wrapper1 && !wrapper1.contains(e.target)) {
            document.getElementById('exportTypeDropdown')?.classList.add('hidden');
        }
        const wrapper2 = document.querySelector('[data-dropdown-wrapper="exportSyncStatusFilter"]');
        if (wrapper2 && !wrapper2.contains(e.target)) {
            document.getElementById('exportSyncStatusDropdown')?.classList.add('hidden');
        }
    });
    </script>
</x-layouts.app>
