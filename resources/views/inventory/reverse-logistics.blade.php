<x-layouts.app :title="__('Reverse Logistics')">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Reverse Logistics</h1>
                <p class="text-sm text-slate-500 max-w-2xl">Manage returned products through inspection, repair, restocking, or disposal.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <button id="openAddReturn" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-cyan-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20 hover:from-cyan-700 hover:to-cyan-600 transition">+ Receive Returned Item</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Returned Items</p>
                <p id="statTotalReturns" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Total returned products received</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Under Inspection</p>
                <p id="statUnderReview" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Items awaiting inspection</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ready for Restock</p>
                <p id="statRestock" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Approved to return to inventory</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Under Repair</p>
                <p id="statRepairQueue" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Items currently being repaired</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Search returned item</label>
                        <input id="searchInput" type="search" placeholder="Product, SKU, reason, warehouse" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select id="statusFilter" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none">
                            <option value="">All statuses</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Pending Repair">Pending Repair</option>
                            <option value="Ready for Restock">Ready for Restock</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Return reason</label>
                        <select id="reasonFilter" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none">
                            <option value="">All reasons</option>
                            <option value="Defective">Defective</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Wrong Item">Wrong Item Delivered</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Damaged In Transit">Damaged In Transit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Item condition</label>
                        <select id="conditionFilter" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none">
                            <option value="">All conditions</option>
                            <option value="Good">Good</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Needs Repair">Needs Repair</option>
                            <option value="Opened">Opened</option>
                        </select>
                    </div>
                </div>
                <button id="clearFiltersBtn" class="text-sm text-slate-600 hover:text-slate-900 font-medium">Clear Filters</button>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Product</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">SKU</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Return Reason</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Condition</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Quantity</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Warehouse</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Date Received</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody" class="divide-y divide-slate-200 bg-white">
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-sm text-slate-500">No reverse logistics records yet. Click “Log Returned Item” to create your first record.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="returnModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div id="modalOverlay" class="absolute inset-0 bg-slate-900/40"></div>
        <div class="relative w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200 z-10 max-h-[80vh]">
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="modalTitle" class="text-xl font-semibold text-white">Log a Returned Item</h2>
                        <p class="text-sm text-slate-300 mt-1">Capture return details, repair notes, and restock decisions in one place.</p>
                    </div>
                    <button id="closeModalBtn" class="rounded-2xl bg-slate-800 px-3 py-2 text-slate-200 hover:bg-slate-700">✕</button>
                </div>
            </div>

            <form id="returnForm" class="space-y-6 p-8 overflow-y-auto max-h-[calc(80vh-80px)]">
                <input type="hidden" id="recordId" />
                <div class="grid gap-4 xl:grid-cols-2">
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Product Name</label>
                        <input id="productName" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" placeholder="Example: Premium Brake Pad" required />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">SKU</label>
                        <input id="sku" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" placeholder="Example: BRK-PLD-09" required />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Quantity</label>
                        <input id="quantity" type="number" min="1" value="1" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" required />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Warehouse</label>
                        <input id="warehouse" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" placeholder="Main Store / Service Bay" required />
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Return Reason</label>
                        <select id="returnReason" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" required>
                            <option value="">Select reason</option>
                            <option value="Defective">Defective</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Damaged In Transit">Damaged In Transit</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Condition</label>
                        <select id="condition" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" required>
                            <option value="">Select condition</option>
                            <option value="Good">Good</option>
                            <option value="Opened">Opened</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Needs Repair">Needs Repair</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Source</label>
                        <select id="source" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" required>
                            <option value="">Select source</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Supplier Return">Supplier Return</option>
                            <option value="Quality Inspection">Quality Inspection</option>
                            <option value="Service Center">Service Center</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Status</label>
                        <select id="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" required>
                            <option value="Under Review">Under Review</option>
                            <option value="Pending Repair">Pending Repair</option>
                            <option value="Ready for Restock">Ready for Restock</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-semibold text-slate-600">Reported Date</label>
                        <input id="reportedDate" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" required />
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-600">Notes</label>
                    <textarea id="notes" rows="3" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-100 outline-none" placeholder="Add additional context, inspection notes, or follow-up actions"></textarea>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                    <button type="button" id="cancelModalBtn" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition sm:w-auto">Cancel</button>
                    <div class="flex flex-1 items-center justify-end gap-3 sm:flex-none">
                        <button type="button" id="deleteRecordBtn" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-100 transition">Delete</button>
                        <button type="submit" class="rounded-2xl bg-gradient-to-r from-cyan-600 to-cyan-500 px-4 py-3 text-sm font-semibold text-white hover:from-cyan-700 hover:to-cyan-600 transition">Save record</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const recordsTableBody = document.getElementById('recordsTableBody');
        const statTotalReturns = document.getElementById('statTotalReturns');
        const statUnderReview = document.getElementById('statUnderReview');
        const statRestock = document.getElementById('statRestock');
        const statRepairQueue = document.getElementById('statRepairQueue');
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const reasonFilter = document.getElementById('reasonFilter');
        const conditionFilter = document.getElementById('conditionFilter');
        const openAddReturn = document.getElementById('openAddReturn');
        const returnModal = document.getElementById('returnModal');
        const modalOverlay = document.getElementById('modalOverlay');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const deleteRecordBtn = document.getElementById('deleteRecordBtn');
        const recordIdInput = document.getElementById('recordId');
        const modalTitle = document.getElementById('modalTitle');
        const returnForm = document.getElementById('returnForm');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');

        const inputs = {
            productName: document.getElementById('productName'),
            sku: document.getElementById('sku'),
            quantity: document.getElementById('quantity'),
            warehouse: document.getElementById('warehouse'),
            returnReason: document.getElementById('returnReason'),
            condition: document.getElementById('condition'),
            source: document.getElementById('source'),
            status: document.getElementById('status'),
            reportedDate: document.getElementById('reportedDate'),
            notes: document.getElementById('notes'),
        };

        let records = [];
        let filteredRecords = [];
        let editingId = null;

        async function loadRecords() {
            try {
                const response = await fetch('{{ route('api.reverse-logistics.index') }}');
                const result = await response.json();
                
                if (result.data) {
                    records = result.data;
                    filteredRecords = [...records];
                    renderStats();
                    applyFilters();
                }
            } catch (error) {
                console.error('Error loading records:', error);
            }
        }

        async function saveRecord(recordData) {
            try {
                const baseUrl = '{{ url('api/reverse-logistics') }}';
                const url = editingId 
                    ? `${baseUrl}/${editingId}`
                    : baseUrl;
                
                const method = editingId ? 'POST' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(recordData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await loadRecords();
                    return true;
                } else {
                    alert('Error saving record: ' + (result.message || 'Unknown error'));
                    return false;
                }
            } catch (error) {
                console.error('Error saving record:', error);
                alert('Error saving record: ' + error.message);
                return false;
            }
        }

        async function deleteRecord(id) {
            try {
                const response = await fetch(`{{ url('api/reverse-logistics') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await loadRecords();
                    return true;
                } else {
                    alert('Error deleting record: ' + (result.message || 'Unknown error'));
                    return false;
                }
            } catch (error) {
                console.error('Error deleting record:', error);
                alert('Error deleting record: ' + error.message);
                return false;
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function getStatusBadge(status) {
            const classes = {
                'Under Review': 'bg-orange-100 text-orange-700',
                'Pending Repair': 'bg-blue-100 text-blue-700',
                'Ready for Restock': 'bg-green-100 text-green-700',
                'Disposed': 'bg-red-100 text-red-700',
            };
            return `<span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold ${classes[status] || 'bg-slate-100 text-slate-700'}">${status}</span>`;
        }

        function renderStats() {
            statTotalReturns.textContent = records.length;
            statUnderReview.textContent = records.filter(item => item.status === 'Under Review').length;
            statRestock.textContent = records.filter(item => item.status === 'Ready for Restock').length;
            statRepairQueue.textContent = records.filter(item => item.status === 'Pending Repair').length;
        }

        function renderTable() {
            if (!filteredRecords.length) {
                recordsTableBody.innerHTML = '<tr><td colspan="9" class="px-5 py-10 text-center text-sm text-slate-500">No records match your filters or the list is empty. Try changing the search or adding a new return.</td></tr>';
                return;
            }

            recordsTableBody.innerHTML = filteredRecords.map(record => `
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-4 font-semibold text-slate-900">${record.product_name}</td>
                    <td class="px-5 py-4 text-slate-600">${record.sku}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700">${record.return_reason}</span></td>
                    <td class="px-5 py-4 text-slate-600">${record.condition}</td>
                    <td class="px-5 py-4 text-slate-600">${record.quantity}</td>
                    <td class="px-5 py-4 text-slate-600">${record.warehouse}</td>
                    <td class="px-5 py-4 text-slate-600">${formatDate(record.reported_date)}</td>
                    <td class="px-5 py-4">${getStatusBadge(record.status)}</td>
                    <td class="px-5 py-4 text-center">
                        <div class="relative inline-block">
                            <button onclick="toggleActionMenu(${record.id})" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div id="action-menu-${record.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10">
                                <button onclick="viewDetails(${record.id})" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </button>
                                ${record.status === 'Under Review' ? `
                                <button onclick="updateStatus(${record.id}, 'Ready for Restock')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Restock
                                </button>
                                <button onclick="updateStatus(${record.id}, 'Pending Repair')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Send to Repair
                                </button>
                                <button onclick="updateStatus(${record.id}, 'Disposed')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Send to Disposal
                                </button>
                                ` : ''}
                                ${record.status === 'Pending Repair' ? `
                                <button onclick="updateStatus(${record.id}, 'Ready for Restock')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Restock
                                </button>
                                ` : ''}
                                ${record.status === 'Ready for Restock' ? `
                                <button onclick="processRestock(${record.id})" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Process Return
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function applyFilters() {
            const search = searchInput.value.trim().toLowerCase();
            const status = statusFilter.value;
            const reason = reasonFilter.value;
            const condition = conditionFilter.value;

            filteredRecords = records.filter(record => {
                const matchesSearch = search === '' || [
                    record.product_name,
                    record.sku,
                    record.return_reason,
                    record.condition,
                    record.warehouse,
                    record.source,
                    record.notes,
                ].some(value => String(value).toLowerCase().includes(search));

                const matchesStatus = !status || record.status === status;
                const matchesReason = !reason || record.return_reason === reason;
                const matchesCondition = !condition || record.condition === condition;

                return matchesSearch && matchesStatus && matchesReason && matchesCondition;
            });

            renderTable();
        }

        function resetForm() {
            recordIdInput.value = '';
            editingId = null;
            modalTitle.textContent = 'Log a Returned Item';
            deleteRecordBtn.classList.add('hidden');
            returnForm.reset();
            inputs.reportedDate.value = new Date().toISOString().split('T')[0];
        }

        function openModal() {
            resetForm();
            returnModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            returnModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openEditRecord(id) {
            const record = records.find(item => item.id === id);
            if (!record) return;

            editingId = id;
            recordIdInput.value = id;
            modalTitle.textContent = 'Update Returned Item';
            deleteRecordBtn.classList.remove('hidden');

            inputs.productName.value = record.product_name;
            inputs.sku.value = record.sku;
            inputs.quantity.value = record.quantity;
            inputs.warehouse.value = record.warehouse;
            inputs.returnReason.value = record.return_reason;
            inputs.condition.value = record.condition;
            inputs.source.value = record.source;
            inputs.status.value = record.status;
            inputs.reportedDate.value = record.reported_date;
            inputs.notes.value = record.notes || '';

            returnModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function toggleActionMenu(id) {
            const menu = document.getElementById(`action-menu-${id}`);
            const allMenus = document.querySelectorAll('[id^="action-menu-"]');
            
            allMenus.forEach(m => {
                if (m.id !== menu.id) {
                    m.classList.add('hidden');
                }
            });
            
            menu.classList.toggle('hidden');
        }

        function viewDetails(id) {
            const record = records.find(item => item.id === id);
            if (!record) return;
            
            alert(`Product: ${record.product_name}\nSKU: ${record.sku}\nReason: ${record.return_reason}\nCondition: ${record.condition}\nQuantity: ${record.quantity}\nWarehouse: ${record.warehouse}\nStatus: ${record.status}\nNotes: ${record.notes || 'None'}`);
        }

        async function updateStatus(id, newStatus) {
            const record = records.find(item => item.id === id);
            if (!record) return;

            if (newStatus === 'Disposed') {
                if (!confirm('Send this item to disposal? This will create a record in the Item Disposal module.')) {
                    return;
                }

                // Call the disposal integration endpoint
                try {
                    const response = await fetch(`{{ url('item/disposal/from-reverse-logistics') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Item successfully added to Item Disposal module.');
                    } else {
                        alert('Failed to add to disposal: ' + (result.message || 'Unknown error'));
                        return;
                    }
                } catch (error) {
                    console.error('Error adding to disposal:', error);
                    alert('Error adding to disposal: ' + error.message);
                    return;
                }
            }

            const payload = {
                ...record,
                status: newStatus
            };

            editingId = id;
            const success = await saveRecord(payload);
            if (success) {
                // Close all menus
                document.querySelectorAll('[id^="action-menu-"]').forEach(m => m.classList.add('hidden'));
            }
        }

        async function processRestock(id) {
            const record = records.find(item => item.id === id);
            if (!record) return;

            if (!confirm(`Process return for ${record.product_name}? This will add ${record.quantity} units back to inventory.`)) {
                return;
            }

            // TODO: Implement actual restock logic to add to main inventory
            alert(`Restock processed for ${record.product_name}. (Inventory update not yet implemented)`);
            
            // Close all menus
            document.querySelectorAll('[id^="action-menu-"]').forEach(m => m.classList.add('hidden'));
        }

        async function deleteCurrentRecord() {
            if (!editingId) return;
            if (confirm('Delete this reverse logistics record?')) {
                const success = await deleteRecord(editingId);
                if (success) {
                    closeModal();
                }
            }
        }

        returnForm.addEventListener('submit', async event => {
            event.preventDefault();

            const payload = {
                product_name: inputs.productName.value.trim(),
                sku: inputs.sku.value.trim(),
                quantity: Number(inputs.quantity.value),
                warehouse: inputs.warehouse.value.trim(),
                return_reason: inputs.returnReason.value,
                condition: inputs.condition.value,
                source: inputs.source.value,
                status: inputs.status.value,
                reported_date: inputs.reportedDate.value,
                notes: inputs.notes.value.trim(),
            };

            const success = await saveRecord(payload);
            if (success) {
                closeModal();
            }
        });

        inputs.productName.addEventListener('input', function() {
            const productName = this.value.trim();
            const generatedSku = `KCC_${productName.replace(/[^A-Za-z0-9\-\+]/g, '')}`;
            inputs.sku.value = generatedSku;
        });

        openAddReturn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', closeModal);
        deleteRecordBtn.addEventListener('click', deleteCurrentRecord);
        clearFiltersBtn.addEventListener('click', () => {
            searchInput.value = '';
            statusFilter.value = '';
            reasonFilter.value = '';
            conditionFilter.value = '';
            applyFilters();
        });

        [searchInput, statusFilter, reasonFilter, conditionFilter].forEach(element => {
            element.addEventListener('input', applyFilters);
            element.addEventListener('change', applyFilters);
        });

        // Close action menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleActionMenu"]') && !e.target.closest('[id^="action-menu-"]')) {
                document.querySelectorAll('[id^="action-menu-"]').forEach(m => {
                    m.classList.add('hidden');
                });
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !returnModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        window.openEditRecord = openEditRecord;

        // Load initial data
        loadRecords();
    </script>
    @vite('resources/js/reverse-logistics.js')
</x-layouts.app>
