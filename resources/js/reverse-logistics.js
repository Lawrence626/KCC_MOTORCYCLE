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
    if (!statTotalReturns || !statUnderReview || !statRestock || !statRepairQueue) return;
    statTotalReturns.textContent = records.length;
    statUnderReview.textContent = records.filter(item => item.status === 'Under Review').length;
    statRestock.textContent = records.filter(item => item.status === 'Ready for Restock').length;
    statRepairQueue.textContent = records.filter(item => item.status === 'Pending Repair').length;
}

function renderTable() {
    if (!recordsTableBody) return;

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
            <td class="px-5 py-4 text-center"><button type="button" class="text-cyan-600 hover:text-cyan-700 text-sm font-semibold" onclick="openEditRecord(${record.id})">Edit</button></td>
        </tr>
    `).join('');
}

function applyFilters() {
    if (!searchInput || !statusFilter || !reasonFilter || !conditionFilter) return;

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
    if (!returnForm) return;
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

function handleFormSubmit(event) {
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
}

function handleClearStorage() {
    if (confirm('Reset reverse logistics records to default demos?')) {
        localStorage.removeItem(STORAGE_KEY);
        loadRecords();
        applyFilters();
        renderStats();
    }
}

window.openEditRecord = openEditRecord;

if (openAddReturn) openAddReturn.addEventListener('click', openModal);
if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
if (deleteRecordBtn) deleteRecordBtn.addEventListener('click', () => {
    if (confirm('Delete this reverse logistics record?')) {
        deleteCurrentRecord();
    }
});
if (clearStorageBtn) clearStorageBtn.addEventListener('click', handleClearStorage);
if (returnForm) returnForm.addEventListener('submit', handleFormSubmit);

[searchInput, statusFilter, reasonFilter, conditionFilter].forEach(element => {
    if (!element) return;
    element.addEventListener('input', applyFilters);
    element.addEventListener('change', applyFilters);
});

document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && returnModal && !returnModal.classList.contains('hidden')) {
        closeModal();
    }
});

loadRecords();
renderStats();
applyFilters();
