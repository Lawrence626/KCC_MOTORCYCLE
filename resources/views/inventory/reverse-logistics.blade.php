<x-layouts.app :title="__('Reverse Logistics')">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Reverse Logistics</h1>
                <p class="text-sm text-slate-500 max-w-2xl">Track returned, defective, and repair-bound items with a dedicated reverse logistics dashboard. Manage return reasons, repair status, and restock decisions without pulling from the full product inventory.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <button id="openAddReturn" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-cyan-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20 hover:from-cyan-700 hover:to-cyan-600 transition">+ Log Returned Item</button>
                <button id="clearStorageBtn" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Reset list</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Returns Logged</p>
                <p id="statTotalReturns" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">All reverse logistics records</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Under Review</p>
                <p id="statUnderReview" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Awaiting quality decisions</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ready for Restock</p>
                <p id="statRestock" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Items cleared for return to stock</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Repair Queue</p>
                <p id="statRepairQueue" class="mt-4 text-3xl font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-2">Items sent for repair or inspection</p>
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
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Product</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">SKU</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Reason</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Condition</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Qty</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Warehouse</th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Reported</th>
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
        <div class="relative w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200 z-10">
            <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="modalTitle" class="text-xl font-semibold text-white">Log a Returned Item</h2>
                        <p class="text-sm text-slate-300 mt-1">Capture return details, repair notes, and restock decisions in one place.</p>
                    </div>
                    <button id="closeModalBtn" class="rounded-2xl bg-slate-800 px-3 py-2 text-slate-200 hover:bg-slate-700">✕</button>
                </div>
            </div>

            <form id="returnForm" class="space-y-6 p-8">
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
        const STORAGE_KEY = 'reverseLogisticsRecords';
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
        const clearStorageBtn = document.getElementById('clearStorageBtn');

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

        function loadRecords() {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    records = JSON.parse(saved);
                } catch (error) {
                    records = [];
                }
            }

            filteredRecords = [...records];
        }

        function saveRecords() {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(records));
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function getStatusBadge(status) {
            const classes = {
                'Under Review': 'bg-amber-100 text-amber-800',
                'Pending Repair': 'bg-sky-100 text-sky-800',
                'Ready for Restock': 'bg-emerald-100 text-emerald-800',
                'Disposed': 'bg-rose-100 text-rose-800',
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
                    <td class="px-5 py-4 font-semibold text-slate-900">${record.productName}</td>
                    <td class="px-5 py-4 text-slate-600">${record.sku}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700">${record.returnReason}</span></td>
                    <td class="px-5 py-4 text-slate-600">${record.condition}</td>
                    <td class="px-5 py-4 text-slate-600">${record.quantity}</td>
                    <td class="px-5 py-4 text-slate-600">${record.warehouse}</td>
                    <td class="px-5 py-4 text-slate-600">${formatDate(record.reportedDate)}</td>
                    <td class="px-5 py-4">${getStatusBadge(record.status)}</td>
                    <td class="px-5 py-4 text-center">
                        <button type="button" class="text-cyan-600 hover:text-cyan-700 text-sm font-semibold" onclick="openEditRecord(${record.id})">Edit</button>
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
                    record.productName,
                    record.sku,
                    record.returnReason,
                    record.condition,
                    record.warehouse,
                    record.source,
                    record.notes,
                ].some(value => String(value).toLowerCase().includes(search));

                const matchesStatus = !status || record.status === status;
                const matchesReason = !reason || record.returnReason === reason;
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

            inputs.productName.value = record.productName;
            inputs.sku.value = record.sku;
            inputs.quantity.value = record.quantity;
            inputs.warehouse.value = record.warehouse;
            inputs.returnReason.value = record.returnReason;
            inputs.condition.value = record.condition;
            inputs.source.value = record.source;
            inputs.status.value = record.status;
            inputs.reportedDate.value = record.reportedDate;
            inputs.notes.value = record.notes;

            returnModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function deleteCurrentRecord() {
            if (!editingId) return;
            records = records.filter(item => item.id !== editingId);
            saveRecords();
            applyFilters();
            renderStats();
            closeModal();
        }

        returnForm.addEventListener('submit', event => {
            event.preventDefault();

            const payload = {
                id: editingId || Date.now(),
                productName: inputs.productName.value.trim(),
                sku: inputs.sku.value.trim(),
                quantity: Number(inputs.quantity.value),
                warehouse: inputs.warehouse.value.trim(),
                returnReason: inputs.returnReason.value,
                condition: inputs.condition.value,
                source: inputs.source.value,
                status: inputs.status.value,
                reportedDate: inputs.reportedDate.value,
                notes: inputs.notes.value.trim(),
            };

            if (editingId) {
                records = records.map(item => item.id === editingId ? payload : item);
            } else {
                records.unshift(payload);
            }

            saveRecords();
            applyFilters();
            renderStats();
            closeModal();
        });

        openAddReturn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', closeModal);
        deleteRecordBtn.addEventListener('click', () => {
            if (confirm('Delete this reverse logistics record?')) {
                deleteCurrentRecord();
            }
        });
        clearStorageBtn.addEventListener('click', () => {
            if (confirm('Reset reverse logistics records to default demos?')) {
                localStorage.removeItem(STORAGE_KEY);
                loadRecords();
                applyFilters();
                renderStats();
            }
        });

        [searchInput, statusFilter, reasonFilter, conditionFilter].forEach(element => {
            element.addEventListener('input', applyFilters);
            element.addEventListener('change', applyFilters);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !returnModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        window.openEditRecord = openEditRecord;

        loadRecords();
        renderStats();
        applyFilters();
    </script>
    @vite('resources/js/reverse-logistics.js')
</x-layouts.app>
