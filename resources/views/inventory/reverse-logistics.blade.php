<x-layouts.app :title="__('Reverse Logistics')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Reverse Logistics</h1>
                    <p class="mt-1 text-xs text-slate-500 max-w-2xl">Manage returned products through inspection, repair, restocking, or disposal.</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <button id="openAddReturn" class="rounded-full border border-[#00fff2]/40 bg-[#00fff2] px-4 py-2 text-xs font-semibold text-black hover:bg-[#00e6da] transition shadow-sm">+ Receive Returned Item</button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
            <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Returned Items</p>
                        <p id="statTotalReturns" class="text-2xl font-semibold text-slate-900">0</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Total received</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Under Inspection</p>
                        <p id="statUnderReview" class="text-2xl font-semibold text-slate-900">0</p>
                        <p class="text-xs text-amber-600 mt-0.5">Awaiting check</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Ready for Restock</p>
                        <p id="statRestock" class="text-2xl font-semibold text-slate-900">0</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Approved items</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Under Repair</p>
                        <p id="statRepairQueue" class="text-2xl font-semibold text-slate-900">0</p>
                        <p class="text-xs text-blue-600 mt-0.5">Being repaired</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4 flex-1">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Search returned item</label>
                        <input id="searchInput" type="search" placeholder="Product, SKU, reason, warehouse" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                        <select id="statusFilter" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="">All statuses</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Pending Repair">Pending Repair</option>
                            <option value="Ready for Restock">Ready for Restock</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Return reason</label>
                        <select id="reasonFilter" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
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
                        <select id="conditionFilter" class="w-full rounded-[12px] border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="">All conditions</option>
                            <option value="Good">Good</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Needs Repair">Needs Repair</option>
                            <option value="Opened">Opened</option>
                        </select>
                    </div>
                </div>
                <button id="clearFiltersBtn" class="px-3.5 py-2 rounded-[12px] border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shrink-0">Clear Filters</button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-xs text-left">
                    <thead class="bg-[#0f172a] border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Return Reason</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Condition</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Quantity</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Warehouse</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Date Received</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Status</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody" class="divide-y divide-slate-200 text-xs bg-white">
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500">No reverse logistics records yet. Click “Receive Returned Item” to create your first record.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Log/Edit Return Modal -->
    <div id="returnModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div id="modalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200 z-10 max-h-[85vh]">
            <div class="px-6 py-5 bg-[#0f172a] relative flex items-start justify-between">
                <div>
                    <h2 id="modalTitle" class="text-lg font-bold text-white mb-0.5">Log a Returned Item</h2>
                    <p class="text-xs text-slate-300">Capture return details, repair notes, and restock decisions in one place.</p>
                </div>
                <button id="closeModalBtn" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">✕</button>
            </div>

            <form id="returnForm" class="space-y-4 p-6 overflow-y-auto max-h-[calc(85vh-80px)] text-xs">
                <input type="hidden" id="recordId" />
                <div class="grid gap-4 xl:grid-cols-2">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Product Name</label>
                        <input id="productName" type="text" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" placeholder="Example: Premium Brake Pad" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">SKU</label>
                        <input id="sku" type="text" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" placeholder="Example: BRK-PLD-09" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Quantity</label>
                        <input id="quantity" type="number" min="1" value="1" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Warehouse</label>
                        <input id="warehouse" type="text" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" placeholder="Main Store / Service Bay" required />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Return Reason</label>
                        <select id="returnReason" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" required>
                            <option value="">Select reason</option>
                            <option value="Defective">Defective</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                            <option value="Quality Issue">Quality Issue</option>
                            <option value="Damaged In Transit">Damaged In Transit</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Condition</label>
                        <select id="condition" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" required>
                            <option value="">Select condition</option>
                            <option value="Good">Good</option>
                            <option value="Opened">Opened</option>
                            <option value="Damaged">Damaged</option>
                            <option value="Needs Repair">Needs Repair</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Source</label>
                        <select id="source" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" required>
                            <option value="">Select source</option>
                            <option value="Customer Return">Customer Return</option>
                            <option value="Supplier Return">Supplier Return</option>
                            <option value="Quality Inspection">Quality Inspection</option>
                            <option value="Service Center">Service Center</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Status</label>
                        <select id="status" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" required>
                            <option value="Under Review">Under Review</option>
                            <option value="Pending Repair">Pending Repair</option>
                            <option value="Ready for Restock">Ready for Restock</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Reported Date</label>
                        <input id="reportedDate" type="date" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" required />
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-700">Notes</label>
                    <textarea id="notes" rows="3" class="w-full rounded-lg border-2 border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 transition focus:outline-none focus:border-[#00fff2] focus:ring-1 focus:ring-[#00fff2]/20 hover:border-slate-300" placeholder="Add additional context, inspection notes, or follow-up actions"></textarea>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between pt-2">
                    <button type="button" id="cancelModalBtn" class="w-full rounded-lg border-2 border-slate-200 bg-white px-5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition sm:w-auto">Cancel</button>
                    <div class="flex flex-1 items-center justify-end gap-3 sm:flex-none">
                        <button type="button" id="deleteRecordBtn" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">Delete</button>
                        <button type="submit" class="rounded-lg bg-[#00fff2] px-5 py-2 text-xs font-semibold text-black hover:bg-[#00e6da] transition shadow-sm">Save record</button>
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
                'Under Review': 'bg-[#105f68] text-white',
                'Pending Repair': 'bg-blue-600 text-white',
                'Ready for Restock': 'bg-[#00fff2] text-black',
                'Disposed': 'bg-[#0f172a] text-white',
            };
            return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${classes[status] || 'bg-slate-200 text-slate-700'}">${status}</span>`;
        }

        function renderStats() {
            statTotalReturns.textContent = records.length;
            statUnderReview.textContent = records.filter(item => item.status === 'Under Review').length;
            statRestock.textContent = records.filter(item => item.status === 'Ready for Restock').length;
            statRepairQueue.textContent = records.filter(item => item.status === 'Pending Repair').length;
        }

        function renderTable() {
            if (!filteredRecords.length) {
                recordsTableBody.innerHTML = '<tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">No records match your filters or the list is empty. Try changing the search or adding a new return.</td></tr>';
                return;
            }

            recordsTableBody.innerHTML = filteredRecords.map(record => `
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold text-slate-900">${record.product_name}</td>
                    <td class="px-4 py-3 text-slate-600">${record.sku}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] text-slate-700 font-medium">${record.return_reason}</span></td>
                    <td class="px-4 py-3 text-slate-600">${record.condition}</td>
                    <td class="px-4 py-3 text-slate-600 font-semibold">${record.quantity}</td>
                    <td class="px-4 py-3 text-slate-600">${record.warehouse}</td>
                    <td class="px-4 py-3 text-slate-600">${formatDate(record.reported_date)}</td>
                    <td class="px-4 py-3">${getStatusBadge(record.status)}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="relative inline-block">
                            <button onclick="toggleActionMenu(${record.id})" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div id="action-menu-${record.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10 text-xs">
                                <button onclick="viewDetails(${record.id})" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </button>
                                ${record.status === 'Under Review' ? `
                                <button onclick="updateStatus(${record.id}, 'Ready for Restock')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Restock
                                </button>
                                <button onclick="updateStatus(${record.id}, 'Pending Repair')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Send to Repair
                                </button>
                                <button onclick="updateStatus(${record.id}, 'Disposed')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Send to Disposal
                                </button>
                                ` : ''}
                                ${record.status === 'Pending Repair' ? `
                                <button onclick="updateStatus(${record.id}, 'Ready for Restock')" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Approve Restock
                                </button>
                                ` : ''}
                                ${record.status === 'Ready for Restock' ? `
                                <button onclick="processRestock(${record.id})" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
