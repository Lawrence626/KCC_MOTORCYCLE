<x-layouts.app :title="__('Dashboard')">
    <div id="dashboard-root" data-dashboard-url="{{ route('dashboard.data') }}" data-refresh-interval="15000" class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 text-xs mt-0.5">Overview of sales, inventory and performance insights</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="dashboardRangeLabel" class="flex items-center gap-2 px-3 py-1 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    📅 Loading…
                </button>
                <button class="flex items-center gap-2 px-3 py-1 bg-white border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    📥 Export Report
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Sales -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-gray-600 text-xs font-medium">Total Sales</p>
                        <div class="mt-1">
                            <p id="salesValue" class="text-2xl font-bold text-gray-900">—</p>
                            <p id="salesComparison" class="text-teal-500 text-xs mt-1 font-medium">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Transaction -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-gray-600 text-xs font-medium">Total Transaction</p>
                        <div class="mt-1">
                            <p id="transactionsValue" class="text-2xl font-bold text-gray-900">—</p>
                            <p id="transactionsComparison" class="text-orange-500 text-xs mt-1 font-medium">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Profit -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-gray-600 text-xs font-medium">Total Profit</p>
                        <div class="mt-1">
                            <p id="profitValue" class="text-2xl font-bold text-gray-900">—</p>
                            <p id="profitComparison" class="text-blue-500 text-xs mt-1 font-medium">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Item Sold -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:border-gray-300 hover:shadow-sm transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-gray-600 text-xs font-medium">Total Item Sold</p>
                        <div class="mt-1">
                            <p id="itemsSoldValue" class="text-2xl font-bold text-gray-900">—</p>
                            <p id="itemsSoldComparison" class="text-red-500 text-xs mt-1 font-medium">Loading…</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <!-- Sales Overview Chart -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-3">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-bold text-gray-900">Sales Overview</h2>
                    <div class="flex gap-1">
                        <button type="button" data-range="daily" class="sales-range-btn px-2 py-0.5 text-xs font-medium text-gray-600 bg-slate-100 rounded transition">Day</button>
                        <button type="button" data-range="weekly" class="sales-range-btn px-2 py-0.5 text-xs font-medium text-gray-600 bg-slate-100 rounded transition">Week</button>
                        <button type="button" data-range="monthly" class="sales-range-btn px-2 py-0.5 text-xs font-medium text-white bg-teal-500 rounded transition">Month</button>
                    </div>
                </div>
                <canvas id="salesChart" height="60"></canvas>
            </div>

            <!-- Sales by Category -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <h2 class="text-sm font-bold text-gray-900 mb-2">Sales by Category</h2>
                <div class="flex gap-4 items-center">
                    <div class="flex-shrink-0">
                        <canvas id="categoryChart" width="100" height="100"></canvas>
                    </div>
                    <div id="categoryLegend" class="flex-1 space-y-1 text-xs"></div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <!-- Top Selling Items -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <h2 class="text-sm font-bold text-gray-900 mb-2">Top Selling Item</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left text-gray-600 font-medium py-1 px-1">Rank</th>
                                <th class="text-left text-gray-600 font-medium py-1 px-1">Item</th>
                                <th class="text-left text-gray-600 font-medium py-1 px-1">Category</th>
                                <th class="text-left text-gray-600 font-medium py-1 px-1">Qty</th>
                                <th class="text-left text-gray-600 font-medium py-1 px-1">Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="topItemsTableBody" class="divide-y divide-gray-100">
                            <tr>
                                <td colspan="5" class="py-2 px-1 text-center text-gray-500 text-xs">Loading…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Sales Comparison -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-3">
                <h2 class="text-sm font-bold text-gray-900 mb-2">Monthly Sales Comparison</h2>
                <canvas id="barChart" height="60"></canvas>
            </div>
        </div>

        <!-- Inventory Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Products -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Total Products</p>
                        <p id="totalProductsValue" class="text-2xl font-bold text-gray-900 mt-1">—</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Low Stock Items</p>
                        <p id="lowStockValue" class="text-2xl font-bold text-red-600 mt-1">—</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Out of Stock Items -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Out of Stock Items</p>
                        <p id="outOfStockValue" class="text-2xl font-bold text-gray-900 mt-1">—</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- In Stock Items -->
            <div class="bg-white rounded-lg border border-gray-200 p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium">In Stock Items</p>
                        <p id="inStockValue" class="text-2xl font-bold text-green-600 mt-1">—</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    @vite('resources/js/dashboard.js')
@endpush
</x-layouts.app>
