<x-layouts.app :title="__('Export Data')">

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Export Data</h1>
                <p class="text-xs text-slate-500 mt-0.5">Select and export local offline purchase orders to CSV for synchronization.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Overview</span>
                </a>
                <a href="{{ route('order.create') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Create Purchase Order</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">

        @include('partials.offline-submenu')

        <!-- Rich Alert Container for Notifications -->
        <div id="exportAlertContainer" class="hidden"></div>

        <!-- Summary Metrics Widgets -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-[20px] border border-amber-200 bg-amber-50/50 p-4 shadow-sm">
                <p class="text-amber-900 text-xs font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Local Offline POs</span>
                </p>
                <div class="mt-1">
                    <p id="stat-total-orders" class="text-2xl font-bold text-amber-800">0</p>
                    <p class="text-amber-800/70 text-[10px] leading-tight mt-1 font-medium">Ready in browser storage</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Total Offline Products</p>
                <div class="mt-1">
                    <p id="stat-total-items" class="text-2xl font-bold text-slate-900">0</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Products across all orders</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Total Offline Value</p>
                <div class="mt-1">
                    <p id="stat-total-value" class="text-2xl font-bold text-slate-900 font-mono">₱0.00</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Cumulative order amount</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-cyan-200 bg-cyan-50/50 p-4 shadow-sm">
                <p class="text-cyan-900 text-xs font-bold">Selected for Export</p>
                <div class="mt-1">
                    <p id="stat-selected-count" class="text-2xl font-bold text-cyan-800">0</p>
                    <p id="stat-selected-subtext" class="text-cyan-800/70 text-[10px] leading-tight mt-1 font-medium">0 of 0 orders checked</p>
                </div>
            </div>
        </div>

        <!-- Main Local Orders Export Card -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-5 shadow-sm space-y-4">
            <!-- Filter & Action Controls -->
            <div class="space-y-3 border-b border-slate-100 pb-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#6EC1D1]"></span>
                            <span>Select Local Orders to Export</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Choose orders using the checkboxes below. Latest orders appear at the top.</p>
                    </div>

                    <!-- Top Action Buttons -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="cleanSyncedOrdersManual()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-[10px] border border-cyan-300 bg-cyan-50 text-cyan-950 text-xs font-bold hover:bg-cyan-100 transition cursor-pointer" title="Check and remove orders already synced into the central database">
                            <svg class="w-3.5 h-3.5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Clean Synced</span>
                        </button>
                        <button type="button" onclick="selectAllFilteredOrders(true)" class="px-3 py-2 rounded-[10px] border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Select All
                        </button>
                        <button type="button" onclick="selectAllFilteredOrders(false)" class="px-3 py-2 rounded-[10px] border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Deselect All
                        </button>
                        <button type="button" onclick="toggleArchiveList()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-[10px] border border-amber-300 bg-amber-50 text-amber-900 hover:bg-amber-100 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            <span>Archived Orders</span>
                            <span id="archive-count" class="ml-1 px-1.5 py-0.2 rounded-full bg-amber-200 text-amber-900 text-[10px] font-bold">0</span>
                        </button>
                    </div>
                </div>

                <!-- Date & Search Filtration Bar -->
                <div class="bg-slate-50/80 p-3 rounded-[14px] border border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <!-- Quick Date Presets -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1">Date:</span>
                        <button type="button" onclick="setDatePreset('all')" id="preset-all" class="date-preset-btn px-2.5 py-1 text-xs font-semibold rounded-[8px] border transition bg-[#0f172a] text-white border-[#0f172a]">All</button>
                        <button type="button" onclick="setDatePreset('today')" id="preset-today" class="date-preset-btn px-2.5 py-1 text-xs font-semibold rounded-[8px] border transition bg-white text-slate-700 border-slate-200 hover:bg-slate-100">Today</button>
                        <button type="button" onclick="setDatePreset('weekly')" id="preset-weekly" class="date-preset-btn px-2.5 py-1 text-xs font-semibold rounded-[8px] border transition bg-white text-slate-700 border-slate-200 hover:bg-slate-100">Weekly</button>
                        <button type="button" onclick="setDatePreset('monthly')" id="preset-monthly" class="date-preset-btn px-2.5 py-1 text-xs font-semibold rounded-[8px] border transition bg-white text-slate-700 border-slate-200 hover:bg-slate-100">Monthly</button>
                        <button type="button" onclick="setDatePreset('yearly')" id="preset-yearly" class="date-preset-btn px-2.5 py-1 text-xs font-semibold rounded-[8px] border transition bg-white text-slate-700 border-slate-200 hover:bg-slate-100">Yearly</button>
                    </div>

                    <!-- Date Range Inputs & Search -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-[10px] border border-slate-300 shadow-2xs">
                            <label for="filterDateFrom" class="text-[11px] font-semibold text-slate-500">From:</label>
                            <input type="date" id="filterDateFrom" onchange="applyFilters()" class="text-xs text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                        </div>

                        <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-[10px] border border-slate-300 shadow-2xs">
                            <label for="filterDateTo" class="text-[11px] font-semibold text-slate-500">To:</label>
                            <input type="date" id="filterDateTo" onchange="applyFilters()" class="text-xs text-slate-800 bg-transparent focus:outline-none cursor-pointer">
                        </div>

                        <div class="relative min-w-[180px]">
                            <input type="text" id="filterSearch" oninput="applyFilters()" placeholder="Search PO # or supplier..." class="w-full pl-7 pr-3 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <button type="button" onclick="resetFilters()" title="Reset Filters" class="px-2.5 py-1.5 rounded-[10px] border border-slate-200 bg-white hover:bg-slate-100 text-xs text-slate-600 transition cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Local Orders Table with Checkboxes (Newest First) -->
            <div class="overflow-hidden rounded-[14px] border border-slate-200 bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3.5 py-3 text-center font-semibold text-white" style="width: 44px;">
                                    <input type="checkbox" id="masterCheckbox" onchange="toggleMasterCheckbox(this)" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-0 cursor-pointer accent-[#6EC1D1]">
                                </th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Order Number</th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Supplier</th>
                                <th class="px-3.5 py-3 text-center font-semibold text-white">Items Count</th>
                                <th class="px-3.5 py-3 text-right font-semibold text-white">Total Amount</th>
                                <th class="px-3.5 py-3 text-left font-semibold text-white">Date & Time Saved</th>
                                <th class="px-3.5 py-3 text-center font-semibold text-white" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="localOrdersTbody">
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-1">
                                        <svg class="w-6 h-6 text-slate-300 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span class="text-xs">Loading local orders from browser storage...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-t border-slate-200 bg-slate-50 px-4 py-3 text-xs gap-2">
                    <div class="text-slate-600">
                        <span>Showing <strong id="showingCountText" class="text-slate-900 font-bold">0</strong> orders</span>
                        <span class="text-slate-400 mx-1">•</span>
                        <span><strong id="selectedCountFooter" class="text-cyan-800 font-bold">0</strong> selected for export</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="exportSelectedOrders()" id="btnExportFooter" class="inline-flex items-center gap-1.5 rounded-[8px] bg-[#6EC1D1] px-4 py-1.5 text-xs font-bold text-black hover:bg-[#59b2c2] transition cursor-pointer">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Export The Selected Orders</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Line Item Details Modal -->
        <div id="orderDetailsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="closeOrderDetailsModal()"></div>
            <div class="relative w-full max-w-3xl overflow-hidden rounded-[24px] bg-white shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4 shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>Local Order Details</span>
                            <span id="modalOrderNumber" class="text-xs font-mono font-bold px-2 py-0.5 bg-slate-200 text-slate-800 rounded-md">PO-XXXX</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Line items breakdown, quantities, and supplier information</p>
                    </div>
                    <button onclick="closeOrderDetailsModal()" class="rounded-[10px] p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 transition cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto space-y-4" id="modalOrderContent">
                    <!-- Dynamic content -->
                </div>
                <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-between shrink-0">
                    <span class="text-xs text-slate-500">Stored locally in IndexedDB</span>
                    <button onclick="closeOrderDetailsModal()" class="px-4 py-2 rounded-[10px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Archive List Modal -->
        <div id="archiveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="toggleArchiveList()"></div>
            <div class="relative w-full max-w-3xl overflow-hidden rounded-[24px] bg-white shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-4 shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-black">Archived Local Orders</h3>
                        <p class="text-xs text-slate-900 font-medium">Orders archived locally in browser storage</p>
                    </div>
                    <button onclick="toggleArchiveList()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div id="archiveList">
                        <p class="text-xs text-slate-500 text-center py-6">No archived orders</p>
                    </div>
                </div>
                <div class="px-6 py-3 border-t border-slate-200 bg-slate-50 flex justify-end shrink-0">
                    <button onclick="toggleArchiveList()" class="px-4 py-2 rounded-[10px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Reconciliation Workflow Guide -->
        <div class="rounded-[15px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-[#0f172a] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-sm font-semibold text-white">Offline Purchase Order Reconciliation Workflow</h2>
                <div class="flex flex-wrap items-center gap-2">
                   
                </div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <p class="text-xs font-bold text-slate-900">1. Create & Save Locally</p>
                        <p class="text-[11px] text-slate-600 mt-1">Orders created offline are snapshotted and safely preserved in local IndexedDB.</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <p class="text-xs font-bold text-slate-900">2. Filter & Checkbox Export</p>
                        <p class="text-[11px] text-slate-600 mt-1">Filter by date and check the exact orders you want to export to a clean CSV file.</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <p class="text-xs font-bold text-slate-900">3. Import & Approve</p>
                        <p class="text-[11px] text-slate-600 mt-1">When internet connection is available, upload the CSV in the Import tab for admin reconciliation and sync.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- IndexedDB Offline Manager Script -->
    <script>
        // Notification Panel Toggle
        function toggleNotificationPanel(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                        profileDropdown.classList.remove('opacity-100', 'scale-100');
                        profileDropdown.classList.add('opacity-0', 'scale-95');
                    }
                } else {
                    panel.classList.add('hidden');
                }
            }
        }

        // Helper functions
        function markAllNotificationsRead() {
            // Placeholder for marking all notifications as read
            console.log('Mark all notifications as read');
        }

        function openAllNotificationsModal() {
            // Placeholder for opening all notifications modal
            console.log('Open all notifications modal');
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            const notificationBell = document.getElementById('notification-bell-btn');
            const profileButton = document.getElementById('dashboardProfileButton');

            if (panel && !panel.contains(event.target) && notificationBell && !notificationBell.contains(event.target)) {
                panel.classList.add('hidden');
            }

            if (profileDropdown && !profileDropdown.contains(event.target) && profileButton && !profileButton.contains(event.target)) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.remove('opacity-100', 'scale-100');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }
        });
    </script>
    <script>
    let localPendingOrders = [];
    let filteredOrders = [];
    let selectedOrderIds = new Set();
    let currentPreset = 'all';

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initExportPage, 200);
    });

    async function initExportPage() {
        try {
            if (!window.offlineManager) {
                setTimeout(initExportPage, 200);
                return;
            }

            // Automatically purge orders that have already been imported/approved in central DB
            if (typeof offlineManager.cleanupSyncedOrders === 'function') {
                await offlineManager.cleanupSyncedOrders();
            }

            const orders = await offlineManager.getAllFromStore('pending_orders') || [];
            
            // Sort NEWEST / LATEST first by timestamp descending or id descending
            localPendingOrders = orders.sort((a, b) => {
                const timeA = a.timestamp ? new Date(a.timestamp).getTime() : 0;
                const timeB = b.timestamp ? new Date(b.timestamp).getTime() : 0;
                if (timeB !== timeA) {
                    return timeB - timeA;
                }
                return (b.id || 0) - (a.id || 0);
            });

            // Default: do not pre-select orders (user will check them manually)
            selectedOrderIds = new Set();

            updateStats();
            applyFilters();
            await loadArchiveList();
        } catch (e) {
            console.error('Error initializing export page:', e);
        }
    }

    async function cleanSyncedOrdersManual() {
        if (!window.offlineManager) return;
        try {
            const cleaned = await offlineManager.cleanupSyncedOrders();
            if (Array.isArray(cleaned) && cleaned.length > 0) {
                alert(`✅ Successfully removed ${cleaned.length} already-synchronized order(s) from your local browser storage: ${cleaned.join(', ')}`);
            } else {
                alert('No synchronized orders to clean. All local pending orders are up to date.');
            }
            await initExportPage();
        } catch (e) {
            console.error('Manual clean failed:', e);
            alert('Clean check completed.');
            await initExportPage();
        }
    }

    function updateStats() {
        const totalCount = localPendingOrders.length;
        let totalItems = 0;
        let totalValue = 0;

        localPendingOrders.forEach(o => {
            if (Array.isArray(o.items)) {
                totalItems += o.items.reduce((sum, it) => sum + (Number(it.quantity) || 0), 0);
            }
            totalValue += Number(o.total_amount) || 0;
        });

        document.getElementById('stat-total-orders').textContent = totalCount;
        document.getElementById('stat-total-items').textContent = totalItems;
        document.getElementById('stat-total-value').textContent = '₱' + totalValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function applyFilters() {
        const search = (document.getElementById('filterSearch')?.value || '').toLowerCase().trim();
        const dateFromVal = document.getElementById('filterDateFrom')?.value;
        const dateToVal = document.getElementById('filterDateTo')?.value;

        const dateFrom = dateFromVal ? new Date(dateFromVal + 'T00:00:00') : null;
        const dateTo = dateToVal ? new Date(dateToVal + 'T23:59:59') : null;

        filteredOrders = localPendingOrders.filter(order => {
            // Search match
            const matchesSearch = !search ||
                (order.order_number && order.order_number.toLowerCase().includes(search)) ||
                (order.supplier_name && order.supplier_name.toLowerCase().includes(search));

            // Date match
            let matchesDate = true;
            if (order.timestamp) {
                const orderDate = new Date(order.timestamp);
                if (dateFrom && orderDate < dateFrom) {
                    matchesDate = false;
                }
                if (dateTo && orderDate > dateTo) {
                    matchesDate = false;
                }
            }

            return matchesSearch && matchesDate;
        });

        renderTable();
        updateSelectedCounter();
    }

    function renderTable() {
        const tbody = document.getElementById('localOrdersTbody');
        if (!tbody) return;

        if (filteredOrders.length === 0) {
            if (localPendingOrders.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-3 py-10 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span class="font-semibold text-slate-700">No local orders found in browser storage</span>
                                <span class="text-xs text-slate-400">Create an order first on the <a href="{{ route('order.create') }}" class="text-cyan-700 underline font-semibold">Create Purchase Order page</a></span>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-semibold text-slate-600">No orders match the selected date range or search</span>
                                <button type="button" onclick="resetFilters()" class="text-xs text-cyan-700 underline font-semibold mt-1">Reset Filters</button>
                            </div>
                        </td>
                    </tr>
                `;
            }
            document.getElementById('showingCountText').textContent = '0';
            document.getElementById('masterCheckbox').checked = false;
            return;
        }

        document.getElementById('showingCountText').textContent = filteredOrders.length;

        // Render rows with checkboxes
        tbody.innerHTML = filteredOrders.map(order => {
            const isChecked = selectedOrderIds.has(order.id);
            const itemsCount = Array.isArray(order.items) ? order.items.length : 0;
            const totalUnits = Array.isArray(order.items) ? order.items.reduce((s, it) => s + (Number(it.quantity) || 0), 0) : 0;
            const dateStr = order.timestamp ? new Date(order.timestamp).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' }) : 'N/A';
            const totalFormatted = (order.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            return `
                <tr class="hover:bg-slate-50/80 transition cursor-pointer ${isChecked ? 'bg-cyan-50/30' : ''}" onclick="handleRowClick(event, ${order.id})">
                    <td class="px-3.5 py-3 text-center" onclick="event.stopPropagation()">
                        <input type="checkbox" value="${order.id}" ${isChecked ? 'checked' : ''} onchange="toggleOrderCheckbox(${order.id}, this.checked)" class="w-4 h-4 rounded border-slate-300 text-cyan-600 focus:ring-0 cursor-pointer accent-[#6EC1D1]">
                    </td>
                    <td class="px-3.5 py-3 font-mono font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                        <span>${escapeHtml(order.order_number || 'N/A')}</span>
                    </td>
                    <td class="px-3.5 py-3 font-semibold text-slate-700">${escapeHtml(order.supplier_name || 'N/A')}</td>
                    <td class="px-3.5 py-3 text-center text-slate-600">
                        <span class="font-bold text-slate-800">${itemsCount}</span> <span class="text-[11px] text-slate-400">(${totalUnits} pcs)</span>
                    </td>
                    <td class="px-3.5 py-3 text-right font-mono font-bold text-slate-900">₱${totalFormatted}</td>
                    <td class="px-3.5 py-3 text-slate-600">${dateStr}</td>
                    <td class="px-3.5 py-3 text-center" onclick="event.stopPropagation()">
                        <div class="inline-flex items-center gap-1.5 justify-center">
                            <button type="button" onclick="viewOrderDetails(${order.id})" class="px-2 py-1 text-[11px] font-bold rounded-[8px] border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 transition cursor-pointer" title="View Details">
                                View
                            </button>
                            <button type="button" onclick="archiveOrder(${order.id})" class="px-2 py-1 text-[11px] font-bold rounded-[8px] bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition cursor-pointer" title="Archive Order">
                                Archive
                            </button>
                            <button type="button" onclick="deleteLocalOrder(${order.id})" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition cursor-pointer" title="Delete Order">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Update master checkbox state
        const allFilteredChecked = filteredOrders.length > 0 && filteredOrders.every(o => selectedOrderIds.has(o.id));
        document.getElementById('masterCheckbox').checked = allFilteredChecked;
    }

    function handleRowClick(event, id) {
        if (event.target.tagName === 'INPUT' || event.target.tagName === 'BUTTON') return;
        const isChecked = selectedOrderIds.has(id);
        toggleOrderCheckbox(id, !isChecked);
    }

    function toggleOrderCheckbox(id, checked) {
        if (checked) {
            selectedOrderIds.add(id);
        } else {
            selectedOrderIds.delete(id);
        }
        renderTable();
        updateSelectedCounter();
    }

    function toggleMasterCheckbox(master) {
        const checkAll = master.checked;
        filteredOrders.forEach(o => {
            if (checkAll) {
                selectedOrderIds.add(o.id);
            } else {
                selectedOrderIds.delete(o.id);
            }
        });
        renderTable();
        updateSelectedCounter();
    }

    function selectAllFilteredOrders(selectAll) {
        filteredOrders.forEach(o => {
            if (selectAll) {
                selectedOrderIds.add(o.id);
            } else {
                selectedOrderIds.delete(o.id);
            }
        });
        renderTable();
        updateSelectedCounter();
    }

    function updateSelectedCounter() {
        const selectedCount = selectedOrderIds.size;
        const totalCount = localPendingOrders.length;

        const btnFooter = document.getElementById('btnExportFooter');
        const statCount = document.getElementById('stat-selected-count');
        const statSubtext = document.getElementById('stat-selected-subtext');
        const countFooter = document.getElementById('selectedCountFooter');

        if (statCount) statCount.textContent = selectedCount;
        if (statSubtext) statSubtext.textContent = `${selectedCount} of ${totalCount} orders checked`;
        if (countFooter) countFooter.textContent = selectedCount;

        const disabled = (selectedCount === 0);
        if (btnFooter) {
            btnFooter.disabled = disabled;
            btnFooter.classList.toggle('opacity-50', disabled);
            btnFooter.classList.toggle('cursor-not-allowed', disabled);
        }
    }

    // Export Selected Orders to CSV
    async function exportSelectedOrders() {
        if (selectedOrderIds.size === 0) {
            alert('Please check at least one order to export.');
            return;
        }

        const ordersToExport = localPendingOrders.filter(o => selectedOrderIds.has(o.id));
        if (ordersToExport.length === 0) {
            alert('No matching orders found to export.');
            return;
        }

        const exportedCount = offlineManager.exportOrdersToCsv(ordersToExport);
        const ymd = new Date().toISOString().slice(0, 10).replace(/-/g, '_');
        const fileName = (exportedCount && exportedCount.fileName) ? exportedCount.fileName : `offline_transactions_${ymd}.csv`;

        let totalItems = 0;
        let totalAmount = 0;
        ordersToExport.forEach(o => {
            totalItems += Array.isArray(o.items) ? o.items.length : 0;
            totalAmount += Number(o.total_amount || 0);
        });

        // Automatically archive exported orders so they no longer clutter the pending export table
        for (const o of ordersToExport) {
            try {
                await offlineManager.archiveOrder(o.id);
            } catch (e) {
                console.warn('Could not archive exported order #' + o.id, e);
            }
        }
        await initExportPage();

        const alertContainer = document.getElementById('exportAlertContainer');
        if (alertContainer) {
            alertContainer.classList.remove('hidden');
            alertContainer.style.opacity = '1';
            alertContainer.style.transform = 'translateY(0)';
            alertContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alertContainer.innerHTML = `
                <div class="rounded-[16px] border border-emerald-300 bg-emerald-50 p-4 shadow-sm flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-emerald-950 flex items-center gap-2">
                                <span>Export Successful!</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900">${ordersToExport.length} Order${ordersToExport.length === 1 ? '' : 's'}</span>
                            </h3>
                            <p class="text-xs text-emerald-800 mt-1">
                                Successfully generated and exported <strong>${ordersToExport.length}</strong> purchase order${ordersToExport.length === 1 ? '' : 's'} (${totalItems} product item${totalItems === 1 ? '' : 's'}, Total Amount: ₱${totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}) into <code class="px-1.5 py-0.5 rounded bg-emerald-100 font-mono text-[11px] text-emerald-950 font-bold">${fileName}</code>.
                            </p>
                            <p class="text-[11px] text-emerald-700 mt-0.5 font-medium">
                                The CSV file has started downloading to your device and is ready to be transferred to the online system.
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('exportAlertContainer').classList.add('hidden')" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-md transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Auto-dismiss after 3.5 seconds with smooth fade out
            if (window._exportAlertTimer) clearTimeout(window._exportAlertTimer);
            window._exportAlertTimer = setTimeout(() => {
                alertContainer.style.opacity = '0';
                alertContainer.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    alertContainer.classList.add('hidden');
                    alertContainer.style.opacity = '1';
                    alertContainer.style.transform = 'translateY(0)';
                }, 400);
            }, 3500);
        }
    }

    // Date Presets Helper
    function setDatePreset(preset) {
        currentPreset = preset;
        document.querySelectorAll('.date-preset-btn').forEach(btn => {
            btn.classList.remove('bg-[#0f172a]', 'text-white', 'border-[#0f172a]');
            btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
        });

        const activeBtn = document.getElementById(`preset-${preset}`);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
            activeBtn.classList.add('bg-[#0f172a]', 'text-white', 'border-[#0f172a]');
        }

        const dateFromInput = document.getElementById('filterDateFrom');
        const dateToInput = document.getElementById('filterDateTo');

        const now = new Date();
        const formatDate = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        if (preset === 'today') {
            const todayStr = formatDate(now);
            dateFromInput.value = todayStr;
            dateToInput.value = todayStr;
        } else if (preset === 'weekly') {
            const d = new Date(now);
            const day = d.getDay(); // 0 is Sunday, 1 is Monday...
            const diff = d.getDate() - day + (day === 0 ? -6 : 1); // Monday
            const startOfWeek = new Date(d.setDate(diff));
            dateFromInput.value = formatDate(startOfWeek);
            dateToInput.value = formatDate(now);
        } else if (preset === 'monthly') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            dateFromInput.value = formatDate(firstDay);
            dateToInput.value = formatDate(now);
        } else if (preset === 'yearly') {
            const firstDay = new Date(now.getFullYear(), 0, 1);
            dateFromInput.value = formatDate(firstDay);
            dateToInput.value = formatDate(now);
        } else { // all
            dateFromInput.value = '';
            dateToInput.value = '';
        }

        applyFilters();
    }

    function resetFilters() {
        document.getElementById('filterSearch').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        setDatePreset('all');
    }

    // Order Line Item Details Modal
    async function viewOrderDetails(id) {
        try {
            const order = localPendingOrders.find(o => o.id === id);
            if (!order) return;

            document.getElementById('modalOrderNumber').textContent = order.order_number || 'PO-XXXX';

            const items = Array.isArray(order.items) ? order.items : [];
            const dateStr = order.timestamp ? new Date(order.timestamp).toLocaleString() : 'N/A';
            const totalAmt = (order.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            let itemsHtml = items.map((item, idx) => {
                const subtotal = (item.subtotal || (item.quantity * item.unit_price) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const unitPrice = (item.unit_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                return `
                    <tr class="border-b border-slate-100">
                        <td class="px-3 py-2.5 font-bold text-slate-800">${escapeHtml(item.product_name || 'Product #' + (idx+1))}</td>
                        <td class="px-3 py-2.5 text-slate-600 font-mono">${escapeHtml(item.sku || 'N/A')}</td>
                        <td class="px-3 py-2.5 text-right font-mono">₱${unitPrice}</td>
                        <td class="px-3 py-2.5 text-center font-bold text-slate-900">${item.quantity}</td>
                        <td class="px-3 py-2.5 text-right font-mono font-bold text-slate-900">₱${subtotal}</td>
                    </tr>
                `;
            }).join('');

            document.getElementById('modalOrderContent').innerHTML = `
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-[16px] border border-slate-200">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Supplier</p>
                        <p class="text-xs font-bold text-slate-900 mt-0.5">${escapeHtml(order.supplier_name || 'N/A')}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saved On</p>
                        <p class="text-xs font-medium text-slate-800 mt-0.5">${dateStr}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Amount</p>
                        <p class="text-sm font-extrabold text-slate-950 font-mono mt-0.5">₱${totalAmt}</p>
                    </div>
                </div>

                ${order.notes ? `
                    <div class="p-3 bg-blue-50/60 rounded-[12px] border border-blue-100">
                        <p class="text-[10px] font-bold text-blue-900 uppercase tracking-wider">Order Notes</p>
                        <p class="text-xs text-blue-950 mt-0.5">${escapeHtml(order.notes)}</p>
                    </div>
                ` : ''}

                <div class="overflow-hidden rounded-[14px] border border-slate-200 bg-white">
                    <table class="min-w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2.5">Product</th>
                                <th class="px-3 py-2.5">SKU</th>
                                <th class="px-3 py-2.5 text-right">Unit Price</th>
                                <th class="px-3 py-2.5 text-center">Qty</th>
                                <th class="px-3 py-2.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml || '<tr><td colspan="5" class="p-4 text-center text-slate-400">No items recorded</td></tr>'}
                        </tbody>
                    </table>
                </div>
            `;

            document.getElementById('orderDetailsModal').classList.remove('hidden');
        } catch (e) {
            console.error('Error viewing order details:', e);
        }
    }

    function closeOrderDetailsModal() {
        document.getElementById('orderDetailsModal').classList.add('hidden');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Archive and Delete Local Orders
    async function archiveOrder(id) {
        if (!confirm('Archive this offline purchase order?')) return;

        try {
            await offlineManager.archiveOrder(id);
            selectedOrderIds.delete(id);
            await initExportPage();
            await loadArchiveList();
            alert('Order moved to Archived Orders successfully.');
        } catch (error) {
            console.error('Error archiving order:', error);
            alert('Failed to archive order: ' + (error.message || 'Unknown error'));
        }
    }

    async function deleteLocalOrder(id) {
        if (!confirm('Are you sure you want to permanently delete this local purchase order?')) return;

        try {
            await offlineManager.deleteOrder('pending_orders', id);
            selectedOrderIds.delete(id);
            await initExportPage();
            alert('Local order deleted.');
        } catch (error) {
            console.error('Error deleting local order:', error);
            alert('Failed to delete order: ' + (error.message || 'Unknown error'));
        }
    }

    // Archive Modal Operations
    function toggleArchiveList() {
        const modal = document.getElementById('archiveModal');
        if (!modal) return;
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            loadArchiveList();
        }
    }

    async function loadArchiveList() {
        if (!offlineManager || !offlineManager.db) return;

        try {
            const archivedOrders = await offlineManager.getAllFromStore('archived_orders') || [];
            const archiveList = document.getElementById('archiveList');
            const archiveCount = document.getElementById('archive-count');

            if (archiveCount) archiveCount.textContent = archivedOrders.length;
            if (!archiveList) return;

            if (archivedOrders.length === 0) {
                archiveList.innerHTML = '<p class="text-xs text-slate-500 text-center py-6">No archived orders in browser storage</p>';
                return;
            }

            archiveList.innerHTML = `
                <div class="overflow-hidden rounded-[14px] border border-slate-200">
                    <table class="w-full text-xs">
                        <thead class="bg-[#0f172a] font-semibold text-white uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-3.5 py-2.5 text-left font-semibold text-white">Order Number</th>
                                <th class="px-3.5 py-2.5 text-left font-semibold text-white">Supplier</th>
                                <th class="px-3.5 py-2.5 text-right font-semibold text-white">Total Amount</th>
                                <th class="px-3.5 py-2.5 text-left font-semibold text-white">Archived Date</th>
                                <th class="px-3.5 py-2.5 text-center font-semibold text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            ${archivedOrders.map(order => `
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3.5 py-3 font-mono font-bold text-slate-900">${escapeHtml(order.order_number || 'N/A')}</td>
                                    <td class="px-3.5 py-3 text-slate-700 font-semibold">${escapeHtml(order.supplier_name || 'N/A')}</td>
                                    <td class="px-3.5 py-3 text-right font-mono font-bold text-slate-900">₱${Number(order.total_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                    <td class="px-3.5 py-3 text-slate-500 text-[11px]">${order.archived_at ? new Date(order.archived_at).toLocaleString() : '-'}</td>
                                    <td class="px-3.5 py-3 text-center">
                                        <div class="inline-flex items-center gap-1.5 justify-center">
                                            <button type="button" onclick="restoreOrder(${order.id})" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-[8px] bg-cyan-50 border border-cyan-200 text-cyan-800 hover:bg-cyan-100 transition cursor-pointer" title="Restore order back to active local orders">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                                </svg>
                                                <span>Restore</span>
                                            </button>
                                            <button type="button" onclick="deleteArchivedOrder(${order.id})" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-[8px] bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 transition cursor-pointer" title="Permanently delete from storage">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } catch (error) {
            console.error('Error loading archive list:', error);
        }
    }

    async function restoreOrder(id) {
        if (!confirm('Restore this order to active local orders?')) return;

        try {
            await offlineManager.restoreOrder(id);
            await initExportPage();
            await loadArchiveList();
            alert('Order restored to active local orders.');
        } catch (error) {
            console.error('Error restoring order:', error);
            alert('Failed to restore order: ' + (error.message || 'Unknown error'));
        }
    }

    async function deleteArchivedOrder(id) {
        if (!confirm('Permanently delete this archived order?')) return;

        try {
            await offlineManager.deleteOrder('archived_orders', id);
            await loadArchiveList();
            alert('Archived order deleted.');
        } catch (error) {
            console.error('Error deleting archived order:', error);
            alert('Failed to delete archived order: ' + (error.message || 'Unknown error'));
        }
    }
    </script>
</x-layouts.app>

