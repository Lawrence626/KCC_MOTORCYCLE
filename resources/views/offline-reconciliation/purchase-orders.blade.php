<x-layouts.app :title="__('Offline Purchase Orders')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Offline Purchase Orders</h1>
                <p class="text-gray-600 text-sm mt-1">Generate orders locally during internet outages</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <a href="{{ Route::has('offline.reconciliation') ? route('offline.reconciliation') : url('/offline-reconciliation') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back to Offline Home</span>
                </a>
                <button onclick="toggleArchiveList()" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200 cursor-pointer">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <span>Archive (<span id="archive-count">0</span>)</span>
                </button>
            </div>
        </div>

        @include('partials.offline-submenu')

        <!-- Archive List Modal -->
        <div id="archiveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="toggleArchiveList()"></div>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
                <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-black">Archived Orders</h3>
                        <p class="text-sm text-slate-900 font-medium">Previously archived purchase orders.</p>
                    </div>
                    <button onclick="toggleArchiveList()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div id="archiveList">
                        <p class="text-sm text-slate-500 text-center py-4">No archived orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offline Status Banner -->
        <div id="offline-banner" class="bg-amber-50 border border-amber-200 rounded-lg p-4 hidden">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-900">You are currently offline</p>
                    <p class="text-xs text-amber-700">Orders will be saved locally. Export to CSV when ready to sync.</p>
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-5 shadow-sm">
            <form id="localOrderForm">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Create Purchase Order (Offline)</h2>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] border border-[#6EC1D1]/40 bg-[#6EC1D1] px-4 py-2 text-sm font-semibold text-black shadow-sm hover:bg-[#59b2c2] focus:outline-none transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Save Order Locally</span>
                        </button>
                        <button type="button" onclick="exportLocalOrders()" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 focus:outline-none transition-all duration-200">
                            <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Export to CSV</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Order Number</label>
                        <input type="text" name="order_number" id="order_number" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-slate-50 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:border-slate-400 transition shadow-sm" readonly>
                        <p class="text-[10px] text-slate-500 mt-1">Auto-generated</p>
                    </div>
                    <div class="relative z-[10]" data-dropdown-wrapper="supplierFilter">
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Supplier</label>
                        <input type="hidden" name="supplier_id" id="supplier_id" value="" />
                        <button type="button" id="supplierFilterButton" onclick="toggleSupplierDropdown()" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                            <span id="supplierFilterLabel">Select Supplier</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="supplierFilterDropdown" class="hidden absolute top-full left-0 z-[20] -mt-1 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5 max-h-[250px] overflow-y-auto">
                            <button type="button" onclick="selectSupplier('', 'Select Supplier')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">Select Supplier</button>
                            @foreach($suppliers ?? [] as $supplier)
                            <button type="button" onclick="selectSupplier('{{ $supplier->id }}', '{{ $supplier->name }}')" class="w-full px-4 py-2.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Expected Delivery Date</label>
                        <input type="text" name="expected_delivery_date" id="expected_delivery_date" placeholder="mm/dd/yyyy" readonly class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none cursor-pointer shadow-sm hover:border-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount" step="0.01" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:border-slate-400 transition shadow-sm" required>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-[11px] rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:border-slate-400 transition shadow-sm"></textarea>
                </div>
            </form>
        </div>

        <script>
        function toggleSupplierDropdown() {
            document.querySelectorAll('.custom-calendar-card').forEach(c => c.classList.add('hidden'));
            document.getElementById('syncStatusFilterDropdown')?.classList.add('hidden');
            const dd = document.getElementById('supplierFilterDropdown');
            dd.classList.toggle('hidden');
        }
        function selectSupplier(id, name) {
            document.getElementById('supplier_id').value = id;
            document.getElementById('supplierFilterLabel').textContent = name;
            document.getElementById('supplierFilterDropdown').classList.add('hidden');
        }
        function toggleSyncStatusDropdown() {
            document.querySelectorAll('.custom-calendar-card').forEach(c => c.classList.add('hidden'));
            document.getElementById('supplierFilterDropdown')?.classList.add('hidden');
            const dd = document.getElementById('syncStatusFilterDropdown');
            dd.classList.toggle('hidden');
        }
        function selectSyncStatus(val, label) {
            document.getElementById('syncStatusFilter').value = val;
            document.getElementById('syncStatusFilterLabel').textContent = label;
            document.getElementById('syncStatusFilterDropdown').classList.add('hidden');
        }
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('[data-dropdown-wrapper="supplierFilter"]');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('supplierFilterDropdown')?.classList.add('hidden');
            }
            const syncWrapper = document.querySelector('[data-dropdown-wrapper="syncStatusFilter"]');
            if (syncWrapper && !syncWrapper.contains(e.target)) {
                document.getElementById('syncStatusFilterDropdown')?.classList.add('hidden');
            }
        });
        </script>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Local Orders</p>
                <div class="mt-1">
                    <p id="local-count" class="text-2xl font-bold text-amber-600">0</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Saved locally</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Pending Sync</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-amber-600">{{ $purchaseOrders->where('sync_status', 'pending_sync')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Database orders</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Exported</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-blue-600">{{ $purchaseOrders->where('sync_status', 'exported')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Ready for import</p>
                </div>
            </div>
            <div class="rounded-[20px] border border-slate-200 p-4 bg-white shadow-sm">
                <p class="text-black text-xs font-semibold">Synchronized</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-green-600">{{ $purchaseOrders->where('sync_status', 'synchronized')->count() }}</p>
                    <p class="text-slate-500 text-[10px] leading-tight mt-1 font-medium">Successfully synced</p>
                </div>
            </div>
        </div>

        <!-- Local Orders Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-slate-900">Local Orders (Saved in Browser)</h2>
                <span class="text-xs font-semibold text-slate-500" id="pending-count">0 orders</span>
            </div>
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white">Order Number</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Supplier</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Total</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Date</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="localOrdersTable">
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">No local orders</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Local Orders -->
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
                    <p class="text-slate-600" id="localOrdersPaginationInfo">Showing 0 of 0 items</p>
                    <div class="flex gap-1" id="localOrdersPagination">
                        <button onclick="changeLocalOrdersPage(-1)" disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">← Prev</button>
                        <button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">1</button>
                        <button onclick="changeLocalOrdersPage(1)" disabled class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Orders Table -->
        <div class="rounded-[15px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-slate-900">Database Orders (Synced)</h2>
                <div class="relative z-[20] min-w-[160px]" data-dropdown-wrapper="syncStatusFilter">
                    <input type="hidden" id="syncStatusFilter" value="" />
                    <button type="button" id="syncStatusFilterButton" onclick="toggleSyncStatusDropdown()" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:border-slate-400 focus:outline-none transition shadow-sm">
                        <span id="syncStatusFilterLabel">All Sync Status</span>
                        <svg class="w-4 h-4 text-slate-500 ml-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="syncStatusFilterDropdown" class="hidden absolute top-full right-0 z-[30] mt-1 w-full min-w-[160px] max-h-[220px] overflow-y-auto rounded-[12px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                        <button type="button" onclick="selectSyncStatus('', 'All Sync Status')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">All Sync Status</button>
                        <button type="button" onclick="selectSyncStatus('pending_sync', 'Pending Sync')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Pending Sync</button>
                        <button type="button" onclick="selectSyncStatus('exported', 'Exported')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Exported</button>
                        <button type="button" onclick="selectSyncStatus('imported', 'Imported')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Imported</button>
                        <button type="button" onclick="selectSyncStatus('synchronized', 'Synchronized')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Synchronized</button>
                        <button type="button" onclick="selectSyncStatus('duplicate', 'Duplicate')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Duplicate</button>
                        <button type="button" onclick="selectSyncStatus('failed', 'Failed')" class="w-full px-3 py-1.5 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[8px]">Failed</button>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-[10px] border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs text-slate-700">
                        <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                            <tr>
                                <th class="px-3 py-3 text-left font-semibold text-white">Order Number</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Supplier</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Status</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Sync Status</th>
                                <th class="px-3 py-3 text-right font-semibold text-white">Total Amount</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Expected Delivery</th>
                                <th class="px-3 py-3 text-left font-semibold text-white">Created At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($purchaseOrders as $po)
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium">{{ $po->order_number }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $po->supplier_name }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($po->status == 'pending') bg-amber-100 text-amber-700 @elseif($po->status == 'approved') bg-blue-100 text-blue-700 @elseif($po->status == 'received') bg-green-100 text-green-700 @else bg-slate-100 text-slate-700 @endif">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($po->sync_status == 'pending_sync') bg-amber-100 text-amber-700 @elseif($po->sync_status == 'exported') bg-blue-100 text-blue-700 @elseif($po->sync_status == 'synchronized') bg-green-100 text-green-700 @elseif($po->sync_status == 'duplicate') bg-red-100 text-red-700 @elseif($po->sync_status == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                    {{ str_replace('_', ' ', ucfirst($po->sync_status)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium">Γé▒{{ number_format($po->total_amount, 2) }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : '-' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $po->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No database orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-3 py-2 text-xs">
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
            </div>
        </div>
    </div>

    <script src="{{ asset('js/offline-manager.js') }}"></script>
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for offline manager to initialize
            setTimeout(() => {
                if (offlineManager && offlineManager.db) {
                    updateOfflineBanner();
                    loadLocalOrders();
                    generateOrderNumber();
                } else {
                    console.error('Offline manager not initialized');
                }
            }, 500);

            // Form submission
            document.getElementById('localOrderForm').addEventListener('submit', handleOrderSubmit);
        });

        function generateOrderNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            const orderNumber = `PO-${year}${month}${day}-${random}`;
            document.getElementById('order_number').value = orderNumber;
        }

        function updateOfflineBanner() {
            const banner = document.getElementById('offline-banner');
            if (offlineManager && offlineManager.isOffline()) {
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }
        }

        async function handleOrderSubmit(e) {
            e.preventDefault();

            // Prevent multiple submissions
            const submitButton = e.target.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';

            const orderData = {
                order_number: document.getElementById('order_number').value,
                supplier_id: document.getElementById('supplier_id').value,
                supplier_name: document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].text,
                expected_delivery_date: document.getElementById('expected_delivery_date').value,
                total_amount: parseFloat(document.getElementById('total_amount').value),
                notes: document.getElementById('notes').value,
                status: 'pending',
                sync_status: 'pending_sync'
            };

            try {
                // Always save to IndexedDB
                await offlineManager.savePendingOrder(orderData);
                alert('Order saved locally. Export to CSV when ready to sync.');

                // Reset form and reload local orders
                document.getElementById('localOrderForm').reset();
                generateOrderNumber(); // Generate new order number
                loadLocalOrders();

            } catch (error) {
                console.error('Error saving order:', error);
                if (error.message.includes('already exists')) {
                    alert('Order with this number already exists locally.');
                } else {
                    alert('Failed to save order. Please try again.');
                }
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = `
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Save Order Locally
                `;
            }
        }

        let localOrdersCurrentPage = 1;
        const localOrdersPerPage = 5;
        let allLocalOrders = [];

        async function loadLocalOrders() {
            if (!offlineManager || !offlineManager.db) {
                console.error('Offline manager or database not ready');
                return;
            }

            try {
                const orders = await offlineManager.getAllFromStore('pending_orders');
                allLocalOrders = orders;
                const tbody = document.getElementById('localOrdersTable');
                const countElement = document.getElementById('pending-count');
                const localCountElement = document.getElementById('local-count');

                const count = orders.length || 0;
                countElement.textContent = `${count} orders`;
                localCountElement.textContent = count;

                if (count === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-8 text-center text-slate-500">No local orders</td></tr>';
                    updateLocalOrdersPagination();
                    return;
                }

                renderLocalOrdersPage();
            } catch (error) {
                console.error('Error loading local orders:', error);
            }
        }

        function renderLocalOrdersPage() {
            const tbody = document.getElementById('localOrdersTable');
            const startIndex = (localOrdersCurrentPage - 1) * localOrdersPerPage;
            const endIndex = startIndex + localOrdersPerPage;
            const pageOrders = allLocalOrders.slice(startIndex, endIndex);

            if (pageOrders.length === 0 && allLocalOrders.length > 0) {
                localOrdersCurrentPage = 1;
                renderLocalOrdersPage();
                return;
            }

            tbody.innerHTML = pageOrders.map(order => `
                <tr>
                    <td class="px-3 py-2 text-slate-900 font-medium">${order.order_number}</td>
                    <td class="px-3 py-2 text-slate-600">${order.supplier_name}</td>
                    <td class="px-3 py-2 text-right text-slate-900 font-medium">Γé▒${order.total_amount.toFixed(2)}</td>
                    <td class="px-3 py-2 text-slate-600">${new Date(order.timestamp).toLocaleDateString()}</td>
                    <td class="px-3 py-2">
                        <button onclick="archiveOrder(${order.id})" class="text-amber-600 hover:text-amber-700 font-medium">Archive</button>
                    </td>
                </tr>
            `).join('');

            updateLocalOrdersPagination();
        }

        function updateLocalOrdersPagination() {
            const totalPages = Math.ceil(allLocalOrders.length / localOrdersPerPage) || 1;
            const startItem = allLocalOrders.length === 0 ? 0 : (localOrdersCurrentPage - 1) * localOrdersPerPage + 1;
            const endItem = Math.min(localOrdersCurrentPage * localOrdersPerPage, allLocalOrders.length);

            const infoEl = document.getElementById('localOrdersPaginationInfo');
            if (infoEl) {
                infoEl.textContent = `Showing ${startItem}-${endItem} of ${allLocalOrders.length} items`;
            }

            const container = document.getElementById('localOrdersPagination');
            if (!container) return;

            let buttonsHtml = `<button onclick="changeLocalOrdersPage(-1)" ${localOrdersCurrentPage <= 1 ? 'disabled' : ''} class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">← Prev</button>`;

            for (let i = 1; i <= totalPages; i++) {
                if (i === localOrdersCurrentPage) {
                    buttonsHtml += `<button class="rounded-[10px] bg-slate-200 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-900">${i}</button>`;
                } else {
                    buttonsHtml += `<button onclick="goToLocalOrdersPage(${i})" class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">${i}</button>`;
                }
            }

            buttonsHtml += `<button onclick="changeLocalOrdersPage(1)" ${localOrdersCurrentPage >= totalPages ? 'disabled' : ''} class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>`;

            container.innerHTML = buttonsHtml;
        }

        function goToLocalOrdersPage(page) {
            localOrdersCurrentPage = page;
            renderLocalOrdersPage();
        }

        function changeLocalOrdersPage(delta) {
            const totalPages = Math.ceil(allLocalOrders.length / localOrdersPerPage);
            const newPage = localOrdersCurrentPage + delta;

            if (newPage >= 1 && newPage <= totalPages) {
                localOrdersCurrentPage = newPage;
                renderLocalOrdersPage();
            }
        }

        async function deleteOrder(id) {
            if (!confirm('Are you sure you want to delete this order?')) return;

            const transaction = offlineManager.db.transaction(['pending_orders'], 'readwrite');
            const store = transaction.objectStore('pending_orders');
            store.delete(id);

            transaction.oncomplete = () => {
                loadLocalOrders();
            };
        }

        async function archiveOrder(id) {
            if (!confirm('Are you sure you want to archive this order?')) return;

            try {
                // Get the order first
                const order = await offlineManager.getOrderById('pending_orders', id);

                // Add to archived_orders store
                const transaction = offlineManager.db.transaction(['archived_orders'], 'readwrite');
                const archiveStore = transaction.objectStore('archived_orders');
                await new Promise((resolve, reject) => {
                    const request = archiveStore.add({
                        ...order,
                        archived_at: new Date().toISOString()
                    });
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => reject(request.error);
                });

                // Remove from pending_orders
                const pendingStore = transaction.objectStore('pending_orders');
                await new Promise((resolve, reject) => {
                    const request = pendingStore.delete(id);
                    request.onsuccess = () => resolve();
                    request.onerror = () => reject(request.error);
                });

                alert('Order archived successfully.');
                loadLocalOrders();
                loadArchiveList();
            } catch (error) {
                console.error('Error archiving order:', error);
                alert('Failed to archive order.');
            }
        }

        function toggleArchiveList() {
            const modal = document.getElementById('archiveModal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                loadArchiveList();
            }
        }

        async function loadArchiveList() {
            if (!offlineManager || !offlineManager.db) return;

            try {
                const archivedOrders = await offlineManager.getAllFromStore('archived_orders');
                const archiveList = document.getElementById('archiveList');
                const archiveCount = document.getElementById('archive-count');

                archiveCount.textContent = archivedOrders.length;

                if (archivedOrders.length === 0) {
                    archiveList.innerHTML = '<p class="text-sm text-slate-500 text-center py-4">No archived orders</p>';
                    return;
                }

                archiveList.innerHTML = `
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Order Number</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Supplier</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700">Total</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Archived</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            ${archivedOrders.map(order => `
                                <tr>
                                    <td class="px-3 py-2 text-slate-900 font-medium">${order.order_number}</td>
                                    <td class="px-3 py-2 text-slate-600">${order.supplier_name}</td>
                                    <td class="px-3 py-2 text-right text-slate-900 font-medium">Γé▒${order.total_amount.toFixed(2)}</td>
                                    <td class="px-3 py-2 text-slate-600">${new Date(order.archived_at).toLocaleDateString()}</td>
                                    <td class="px-3 py-2">
                                        <button onclick="restoreOrder(${order.id})" class="text-cyan-600 hover:text-cyan-700 font-medium">Restore</button>
                                        <button onclick="deleteArchivedOrder(${order.id})" class="text-red-600 hover:text-red-700 font-medium ml-2">Delete</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } catch (error) {
                console.error('Error loading archive list:', error);
            }
        }

        async function restoreOrder(id) {
            if (!confirm('Are you sure you want to restore this order?')) return;

            try {
                // Get the archived order
                const order = await offlineManager.getOrderById('archived_orders', id);

                // Remove archived_at field
                const { archived_at, ...orderData } = order;

                // Add back to pending_orders
                const transaction = offlineManager.db.transaction(['pending_orders', 'archived_orders'], 'readwrite');
                const pendingStore = transaction.objectStore('pending_orders');
                await new Promise((resolve, reject) => {
                    const request = pendingStore.add(orderData);
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => reject(request.error);
                });

                // Remove from archived_orders
                const archiveStore = transaction.objectStore('archived_orders');
                await new Promise((resolve, reject) => {
                    const request = archiveStore.delete(id);
                    request.onsuccess = () => resolve();
                    request.onerror = () => reject(request.error);
                });

                alert('Order restored successfully.');
                loadLocalOrders();
                loadArchiveList();
            } catch (error) {
                console.error('Error restoring order:', error);
                alert('Failed to restore order.');
            }
        }

        async function deleteArchivedOrder(id) {
            if (!confirm('Are you sure you want to permanently delete this order?')) return;

            const transaction = offlineManager.db.transaction(['archived_orders'], 'readwrite');
            const store = transaction.objectStore('archived_orders');
            store.delete(id);

            transaction.oncomplete = () => {
                loadArchiveList();
            };
        }

        async function exportLocalOrders() {
            const orders = offlineManager.getPendingOrders();

            if (orders.length === 0) {
                alert('No local orders to export.');
                return;
            }

            // Convert to CSV
            const headers = ['type', 'order_number', 'supplier_id', 'supplier_name', 'status', 'expected_delivery_date', 'notes', 'total_amount', 'created_at'];
            const csvContent = [
                headers.join(','),
                ...orders.map(order => [
                    'purchase_order',
                    order.order_number,
                    order.supplier_id,
                    order.supplier_name,
                    order.status,
                    order.expected_delivery_date,
                    order.notes,
                    order.total_amount,
                    order.timestamp
                ].join(','))
            ].join('\n');

            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `offline_orders_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);

            alert('CSV exported successfully. Upload to Import Data page for admin approval.');
        }

        // Listen for offline/online changes
        window.addEventListener('online', updateOfflineBanner);
        window.addEventListener('offline', updateOfflineBanner);

        function setupCustomDatePicker(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.type = 'text';
            input.readOnly = true;
            input.placeholder = 'mm/dd/yyyy';
            input.className = 'w-full px-3 py-[11px] pr-10 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none cursor-pointer shadow-sm hover:border-slate-400 transition';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative w-full mt-0 z-[60]';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const icon = document.createElement('div');
            icon.className = 'absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 transition-colors duration-150';
            icon.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`;
            wrapper.appendChild(icon);

            function setIconActive(isActive) {
                if (isActive) {
                    icon.classList.remove('text-slate-400');
                    icon.classList.add('text-slate-600');
                } else {
                    icon.classList.remove('text-slate-600');
                    icon.classList.add('text-slate-400');
                }
            }

            const card = document.createElement('div');
            card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-2 z-[999999] w-full rounded-[14px] bg-white p-1.5 shadow-[0_16px_40px_rgba(0,0,0,0.12)] border border-slate-200 transition-all duration-200';
            wrapper.appendChild(card);

            if (input.value && input.value.includes('T')) {
                input.value = input.value.split('T')[0];
            }

            let currentDate = new Date();
            let selectedDate = input.value ? new Date(input.value) : null;
            let viewMode = 'days';

            function render() {
                if (viewMode === 'days') {
                    renderDaysView();
                } else {
                    renderMonthsView();
                }
            }

            function renderDaysView() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();

                let html = `
                    <div class="flex items-center justify-between mb-1 px-0.5">
                        <button type="button" class="toggle-view-btn text-xs font-bold text-slate-900 hover:text-slate-700 inline-flex items-center gap-1 px-1 py-0.5 rounded-md hover:bg-slate-100 transition">
                            <span>${monthNames[month]} ${year}</span>
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="flex items-center gap-0.5">
                            <button type="button" class="prev-month-btn p-0.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Previous Month">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button type="button" class="next-month-btn p-0.5 rounded-full text-slate-600 hover:bg-slate-100 transition" title="Next Month">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 text-center mb-0.5 text-[10px] font-semibold text-slate-400">
                        <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 text-center text-[11px]">
                `;

                for (let i = firstDay - 1; i >= 0; i--) {
                    html += `<span class="h-5.5 flex items-center justify-center text-slate-300 text-[11px]">${daysInPrevMonth - i}</span>`;
                }

                const today = new Date();
                for (let day = 1; day <= daysInMonth; day++) {
                    const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                    const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                    let dayClasses = "h-5.5 w-5.5 mx-auto flex items-center justify-center rounded-md font-medium cursor-pointer transition-all duration-150 text-[11px] ";
                    if (isSelected) {
                        dayClasses += "bg-[#0f172a] text-white font-bold shadow-sm";
                    } else if (isToday) {
                        dayClasses += "bg-[#6EC1D1] text-black font-bold shadow-sm";
                    } else {
                        dayClasses += "text-slate-700 hover:bg-slate-100";
                    }

                    html += `<button type="button" data-day="${day}" class="day-btn ${dayClasses}">${day}</button>`;
                }

                const totalSlots = firstDay + daysInMonth;
                const nextDays = (7 - (totalSlots % 7)) % 7;
                for (let i = 1; i <= nextDays; i++) {
                    html += `<span class="h-5.5 flex items-center justify-center text-slate-300 text-[11px]">${i}</span>`;
                }

                html += `
                    </div>
                    <div class="flex items-center justify-between mt-1 pt-1 border-t border-slate-100 text-[11px] font-semibold px-0.5">
                        <button type="button" class="clear-btn text-slate-500 hover:text-red-600 transition">Clear</button>
                        <button type="button" class="today-btn text-slate-900 font-bold hover:underline transition">Today</button>
                    </div>
                `;

                card.innerHTML = html;

                card.querySelector('.toggle-view-btn')?.addEventListener('click', (e) => { e.stopPropagation(); viewMode = 'months'; render(); });
                card.querySelector('.prev-month-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() - 1); render(); });
                card.querySelector('.next-month-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() + 1); render(); });
                card.querySelector('.clear-btn')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedDate = null;
                    input.value = '';
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    card.classList.add('hidden');
                    setIconActive(false);
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                });
                card.querySelector('.today-btn')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedDate = new Date();
                    currentDate = new Date();
                    const yyyy = selectedDate.getFullYear();
                    const mm = String(selectedDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(selectedDate.getDate()).padStart(2, '0');
                    input.value = `${yyyy}-${mm}-${dd}`;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    card.classList.add('hidden');
                    setIconActive(false);
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                });

                card.querySelectorAll('.day-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const day = parseInt(btn.dataset.day);
                        selectedDate = new Date(year, month, day);
                        const yyyy = year;
                        const mm = String(month + 1).padStart(2, '0');
                        const dd = String(day).padStart(2, '0');
                        input.value = `${yyyy}-${mm}-${dd}`;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                        card.classList.add('hidden');
                        setIconActive(false);
                        input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                    });
                });
            }

            function renderMonthsView() {
                const year = currentDate.getFullYear();
                const shortMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                let html = `
                    <div class="flex items-center justify-between mb-1.5 pb-1.5 border-b border-slate-100 px-0.5">
                        <button type="button" class="prev-year-btn p-1 rounded-full text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-xs font-bold text-slate-900">${year}</span>
                        <button type="button" class="next-year-btn p-1 rounded-full text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-1 text-[11px]">
                `;

                shortMonths.forEach((m, idx) => {
                    const isSel = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === idx;
                    let mClasses = "py-1.5 rounded-lg text-center font-semibold cursor-pointer transition-all duration-150 ";
                    if (isSel) {
                        mClasses += "bg-[#6EC1D1] text-black font-bold shadow-md";
                    } else {
                        mClasses += "text-slate-700 hover:bg-slate-100";
                    }
                    html += `<button type="button" data-month="${idx}" class="month-btn ${mClasses}">${m}</button>`;
                });

                html += `
                    </div>
                    <div class="mt-1.5 text-right">
                        <button type="button" class="back-days-btn text-xs font-bold text-black hover:underline">Back to Days</button>
                    </div>
                `;

                card.innerHTML = html;

                card.querySelector('.prev-year-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setFullYear(year - 1); render(); });
                card.querySelector('.next-year-btn')?.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setFullYear(year + 1); render(); });
                card.querySelector('.back-days-btn')?.addEventListener('click', (e) => { e.stopPropagation(); viewMode = 'days'; render(); });

                card.querySelectorAll('.month-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const mIdx = parseInt(btn.dataset.month);
                        currentDate.setMonth(mIdx);
                        viewMode = 'days';
                        render();
                    });
                });
            }

            input.addEventListener('click', (e) => {
                e.stopPropagation();
                document.getElementById('supplierFilterDropdown')?.classList.add('hidden');
                document.getElementById('syncStatusFilterDropdown')?.classList.add('hidden');
                document.querySelectorAll('.custom-calendar-card').forEach(c => {
                    if (c !== card) c.classList.add('hidden');
                });
                card.classList.toggle('hidden');
                const isOpen = !card.classList.contains('hidden');
                if (isOpen) {
                    render();
                    input.classList.add('ring-1', 'ring-black/35', 'border-transparent');
                } else {
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                }
                setIconActive(isOpen);
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) {
                    card.classList.add('hidden');
                    setIconActive(false);
                    input.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
                }
            });
        }

        setupCustomDatePicker('expected_delivery_date');
    </script>
</x-layouts.app>
