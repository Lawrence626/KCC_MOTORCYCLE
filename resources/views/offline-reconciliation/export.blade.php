<x-layouts.app :title="__('Export Data')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Export Data</h1>
                <p class="text-xs text-slate-500 mt-0.5">Export offline transactions for synchronization</p>
            </div>
            <a href="{{ route('offline.reconciliation') }}" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Offline Home
            </a>
        </div>

        @include('partials.offline-submenu')

        <!-- Export Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Pending POs</p>
                <p class="text-2xl font-bold text-amber-600">{{ $pendingPurchaseOrders }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Purchase orders to export</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Pending Movements</p>
                <p class="text-2xl font-bold text-amber-600">{{ $pendingInventoryMovements }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Inventory movements to export</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Exported POs</p>
                <p class="text-2xl font-bold text-blue-600">{{ $exportedPurchaseOrders }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Already exported</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Exported Movements</p>
                <p class="text-2xl font-bold text-blue-600">{{ $exportedInventoryMovements }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Already exported</p>
            </div>
        </div>

        <!-- Export Form -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Export Configuration</h2>
            <form action="{{ route('offline.export.csv') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Export Type</label>
                        <select name="type" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            <option value="all">All Transactions</option>
                            <option value="purchase_orders">Purchase Orders Only</option>
                            <option value="inventory_movements">Inventory Movements Only</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Sync Status</label>
                        <select name="sync_status" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            <option value="pending_sync">Pending Sync</option>
                            <option value="exported">Exported</option>
                            <option value="all">All Statuses</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export as CSV
                    </button>
                    <form action="{{ route('offline.export.excel') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <input type="hidden" name="sync_status" value="pending_sync">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export as Excel
                        </button>
                    </form>
                </div>
            </form>
        </div>

        <!-- Recent Exports -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Export Information</h2>
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

        <!-- Navigation -->
        <div class="flex gap-2">
            <a href="{{ route('offline.purchase-orders') }}" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                View Purchase Orders
            </a>
            <a href="{{ route('offline.import') }}" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Import Data
            </a>
            <a href="{{ route('offline.history') }}" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Sync History
            </a>
        </div>
    </div>
</x-layouts.app>
