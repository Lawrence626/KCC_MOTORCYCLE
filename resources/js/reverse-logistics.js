document.addEventListener('DOMContentLoaded', () => {
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
            const response = await fetch('/api/reverse-logistics');
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
            const url = editingId 
                ? `/api/reverse-logistics/${editingId}`
                : '/api/reverse-logistics';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch(`/api/reverse-logistics/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
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
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (Number.isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function getStatusBadge(status) {
        const classes = {
            'Under Review': 'bg-[#105f68] text-white',
            'Pending Repair': 'bg-blue-600 text-white',
            'Ready for Restock': 'bg-[#6EC1D1] text-black',
            'Restocked': 'bg-emerald-600 text-white',
            'Disposed': 'bg-[#0f172a] text-white',
        };
        return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${classes[status] || 'bg-slate-200 text-slate-700'}">${status}</span>`;
    }

    function renderStats() {
        if (!statTotalReturns) return;
        statTotalReturns.textContent = records.length;
        statUnderReview.textContent = records.filter(item => item.status === 'Under Review').length;
        statRestock.textContent = records.filter(item => item.status === 'Ready for Restock').length;
        statRepairQueue.textContent = records.filter(item => item.status === 'Pending Repair').length;
    }

    function renderTable() {
        if (!recordsTableBody) return;
        if (!filteredRecords.length) {
            recordsTableBody.innerHTML = '<tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">No records match your filters or the list is empty. Try changing the search or adding a new return.</td></tr>';
            return;
        }

        recordsTableBody.innerHTML = filteredRecords.map(record => `
            <tr class="hover:bg-slate-50 border-b border-slate-100">
                <td class="px-4 py-3 font-semibold text-slate-900">${record.product_name || record.productName || '-'}</td>
                <td class="px-4 py-3 text-slate-600 font-mono text-[11px]">${record.sku || '-'}</td>
                <td class="px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] text-slate-700 font-medium">${record.return_reason || record.returnReason || '-'}</span></td>
                <td class="px-4 py-3 text-slate-600">${record.condition || '-'}</td>
                <td class="px-4 py-3 text-slate-600 font-semibold">${record.quantity}</td>
                <td class="px-4 py-3 text-slate-600">${record.warehouse || '-'}</td>
                <td class="px-4 py-3 text-slate-600">${formatDate(record.reported_date || record.reportedDate)}</td>
                <td class="px-4 py-3">${getStatusBadge(record.status)}</td>
                <td class="px-4 py-3 text-center">
                    <div class="relative inline-block">
                        <button type="button" data-action-toggle="${record.id}" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg>
                        </button>
                        <div id="action-menu-${record.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10 text-xs text-left">
                            <button type="button" data-action-view="${record.id}" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Details
                            </button>
                            <button type="button" data-action-edit="${record.id}" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Record
                            </button>
                            ${record.status === 'Under Review' ? `
                            <button type="button" data-action-status="${record.id}" data-new-status="Ready for Restock" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve Restock
                            </button>
                            <button type="button" data-action-status="${record.id}" data-new-status="Pending Repair" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Send to Repair
                            </button>
                            <button type="button" data-action-status="${record.id}" data-new-status="Disposed" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Send to Disposal
                            </button>
                            ` : ''}
                            ${record.status === 'Pending Repair' ? `
                            <button type="button" data-action-status="${record.id}" data-new-status="Ready for Restock" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve Restock
                            </button>
                            ` : ''}
                            ${record.status === 'Ready for Restock' ? `
                            <button type="button" data-action-restock="${record.id}" class="w-full px-4 py-2 text-left text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Process Return (Restock)
                            </button>
                            ` : ''}
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');

        // Attach event listeners to dynamic table action buttons
        recordsTableBody.querySelectorAll('[data-action-toggle]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.actionToggle;
                toggleActionMenu(id);
            });
        });

        recordsTableBody.querySelectorAll('[data-action-view]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = Number(btn.dataset.actionView);
                viewDetails(id);
            });
        });

        recordsTableBody.querySelectorAll('[data-action-edit]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = Number(btn.dataset.actionEdit);
                openEditRecord(id);
            });
        });

        recordsTableBody.querySelectorAll('[data-action-status]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = Number(btn.dataset.actionStatus);
                const newStatus = btn.dataset.newStatus;
                updateStatus(id, newStatus);
            });
        });

        recordsTableBody.querySelectorAll('[data-action-restock]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = Number(btn.dataset.actionRestock);
                processRestock(id);
            });
        });
    }

    function applyFilters() {
        if (!searchInput || !statusFilter || !reasonFilter || !conditionFilter) return;

        const search = searchInput.value.trim().toLowerCase();
        const status = statusFilter.value;
        const reason = reasonFilter.value;
        const condition = conditionFilter.value;

        filteredRecords = records.filter(record => {
            const matchesSearch = search === '' || [
                record.product_name || record.productName,
                record.sku,
                record.return_reason || record.returnReason,
                record.condition,
                record.warehouse,
                record.source,
                record.notes,
            ].some(value => String(value || '').toLowerCase().includes(search));

            const matchesStatus = !status || record.status === status;
            const matchesReason = !reason || (record.return_reason || record.returnReason) === reason;
            const matchesCondition = !condition || record.condition === condition;

            return matchesSearch && matchesStatus && matchesReason && matchesCondition;
        });

        renderTable();
    }

    function toggleCustomDropdown(id, event) {
        if (event) event.stopPropagation();
        const menu = document.getElementById(id);
        if (!menu) return;
        const isHidden = menu.classList.contains('hidden');
        
        document.querySelectorAll('.custom-calendar-card').forEach(c => c.classList.add('hidden'));
        document.querySelectorAll('.dropdown-menu').forEach(m => {
            m.classList.add('hidden');
            const bId = m.id.replace('Dropdown', 'Button');
            const btn = document.getElementById(bId);
            if (btn) btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
        });
        
        if (isHidden) {
            menu.classList.remove('hidden');
            const bId = id.replace('Dropdown', 'Button');
            const btn = document.getElementById(bId);
            if (btn) btn.classList.add('ring-1', 'ring-black/35', 'border-transparent');
        }
    }

    function selectCustomOption(selectId, value, displayText, displayId, dropdownId) {
        const selectElem = document.getElementById(selectId);
        const displayElem = document.getElementById(displayId);
        if (selectElem) {
            selectElem.value = value;
            selectElem.dispatchEvent(new Event('change'));
        }
        if (displayElem) displayElem.textContent = displayText || value || 'Select...';
        const menu = document.getElementById(dropdownId);
        if (menu) menu.classList.add('hidden');
    }

    window.toggleCustomDropdown = toggleCustomDropdown;
    window.selectCustomOption = selectCustomOption;

    function resetForm() {
        if (!returnForm) return;
        recordIdInput.value = '';
        editingId = null;
        modalTitle.textContent = 'Log a Returned Item';
        if (deleteRecordBtn) deleteRecordBtn.classList.add('hidden');
        returnForm.reset();
        if (inputs.reportedDate) inputs.reportedDate.value = new Date().toISOString().split('T')[0];

        if (document.getElementById('returnReasonDisplay')) document.getElementById('returnReasonDisplay').textContent = 'Select reason';
        if (document.getElementById('conditionDisplay')) document.getElementById('conditionDisplay').textContent = 'Select condition';
        if (document.getElementById('sourceDisplay')) document.getElementById('sourceDisplay').textContent = 'Select source';
        if (document.getElementById('statusDisplay')) document.getElementById('statusDisplay').textContent = 'Under Review';
    }

    function openModal() {
        resetForm();
        if (returnModal) {
            returnModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeModal() {
        if (returnModal) {
            returnModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openEditRecord(id) {
        const record = records.find(item => item.id === id);
        if (!record) return;

        editingId = id;
        recordIdInput.value = id;
        modalTitle.textContent = 'Update Returned Item';
        if (deleteRecordBtn) deleteRecordBtn.classList.remove('hidden');

        inputs.productName.value = record.product_name || record.productName || '';
        inputs.sku.value = record.sku || '';
        inputs.quantity.value = record.quantity || 1;
        inputs.warehouse.value = record.warehouse || '';
        inputs.returnReason.value = record.return_reason || record.returnReason || '';
        inputs.condition.value = record.condition || '';
        inputs.source.value = record.source || '';
        inputs.status.value = record.status || 'Under Review';
        inputs.reportedDate.value = record.reported_date || record.reportedDate || '';
        inputs.notes.value = record.notes || '';

        if (document.getElementById('returnReasonDisplay')) document.getElementById('returnReasonDisplay').textContent = record.return_reason || record.returnReason || 'Select reason';
        if (document.getElementById('conditionDisplay')) document.getElementById('conditionDisplay').textContent = record.condition || 'Select condition';
        if (document.getElementById('sourceDisplay')) document.getElementById('sourceDisplay').textContent = record.source || 'Select source';
        if (document.getElementById('statusDisplay')) document.getElementById('statusDisplay').textContent = record.status || 'Under Review';

        returnModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function toggleActionMenu(id) {
        const menu = document.getElementById(`action-menu-${id}`);
        const allMenus = document.querySelectorAll('[id^="action-menu-"]');
        
        allMenus.forEach(m => {
            if (m.id !== `action-menu-${id}`) {
                m.classList.add('hidden');
            }
        });
        
        if (menu) menu.classList.toggle('hidden');
    }

    function viewDetails(id) {
        const record = records.find(item => item.id === id);
        if (!record) return;
        
        alert(`Product: ${record.product_name || record.productName}\nSKU: ${record.sku}\nReason: ${record.return_reason || record.returnReason}\nCondition: ${record.condition}\nQuantity: ${record.quantity}\nWarehouse: ${record.warehouse}\nStatus: ${record.status}\nNotes: ${record.notes || 'None'}`);
    }

    async function updateStatus(id, newStatus) {
        const record = records.find(item => item.id === id);
        if (!record) return;

        if (newStatus === 'Disposed') {
            if (!confirm('Send this item to disposal? This will create a record in the Item Disposal module.')) {
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(`/item/disposal/from-reverse-logistics/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
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
            product_name: record.product_name || record.productName,
            sku: record.sku,
            quantity: record.quantity,
            warehouse: record.warehouse,
            return_reason: record.return_reason || record.returnReason,
            condition: record.condition,
            source: record.source,
            status: newStatus,
            reported_date: record.reported_date || record.reportedDate,
            notes: record.notes || '',
        };

        editingId = id;
        const success = await saveRecord(payload);
        if (success) {
            document.querySelectorAll('[id^="action-menu-"]').forEach(m => m.classList.add('hidden'));
        }
    }

    async function processRestock(id) {
        const record = records.find(item => item.id === id);
        if (!record) return;

        if (!confirm(`Process return for ${record.product_name || record.productName}? This will add ${record.quantity} units back to inventory.`)) {
            return;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch(`/api/reverse-logistics/${id}/restock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();
            if (result.success) {
                alert(result.message || 'Restock processed successfully!');
                await loadRecords();
            } else {
                alert('Restock failed: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error processing restock:', error);
            alert('Error processing restock: ' + error.message);
        }
        
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

    if (returnForm) {
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
    }

    if (inputs.productName) {
        inputs.productName.addEventListener('input', function() {
            const productName = this.value.trim();
            if (productName) {
                const generatedSku = `KCC_${productName.replace(/[^A-Za-z0-9\-\+]/g, '')}`;
                inputs.sku.value = generatedSku;
            }
        });
    }

    if (openAddReturn) openAddReturn.addEventListener('click', openModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
    if (deleteRecordBtn) deleteRecordBtn.addEventListener('click', deleteCurrentRecord);

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (reasonFilter) reasonFilter.value = '';
            if (conditionFilter) conditionFilter.value = '';

            if (document.getElementById('statusFilterDisplay')) document.getElementById('statusFilterDisplay').textContent = 'All status';
            if (document.getElementById('reasonFilterDisplay')) document.getElementById('reasonFilterDisplay').textContent = 'All reasons';
            if (document.getElementById('conditionFilterDisplay')) document.getElementById('conditionFilterDisplay').textContent = 'All conditions';

            applyFilters();
        });
    }

    [searchInput, statusFilter, reasonFilter, conditionFilter].forEach(element => {
        if (!element) return;
        element.addEventListener('input', applyFilters);
        element.addEventListener('change', applyFilters);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-action-toggle]') && !e.target.closest('[id^="action-menu-"]')) {
            document.querySelectorAll('[id^="action-menu-"]').forEach(m => {
                m.classList.add('hidden');
            });
        }
        if (!e.target.closest('[data-dropdown-wrapper]') && !e.target.closest('.dropdown-menu') && !e.target.closest('[onclick^="toggleCustomDropdown"]')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
                const bId = menu.id.replace('Dropdown', 'Button');
                const btn = document.getElementById(bId);
                if (btn) btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
            });
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && returnModal && !returnModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Custom Date Picker Setup
    function setupCustomDatePicker(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.type = 'text';
        input.readOnly = true;
        input.placeholder = 'YYYY-MM-DD';
        input.className = 'w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 pr-10 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 cursor-pointer';

        const wrapper = document.createElement('div');
        wrapper.className = 'relative w-full mt-0 z-[10]';
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
        card.className = 'custom-calendar-card hidden absolute top-full left-0 mt-1 z-[999999] w-full rounded-[12px] bg-white p-1.5 shadow-[0_12px_32px_rgba(0,0,0,0.12)] border border-slate-200 transition-all duration-200';
        wrapper.appendChild(card);

        let currentDate = new Date();
        let selectedDate = input.value ? new Date(input.value) : null;
        let viewMode = 'days';

        function render() {
            if (input.value) {
                const parsed = new Date(input.value);
                if (!isNaN(parsed.getTime())) {
                    selectedDate = parsed;
                }
            } else {
                selectedDate = null;
            }

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
                <div class="flex items-center justify-between mb-0.5 px-0.5">
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
                html += `<span class="h-5 flex items-center justify-center text-slate-300 text-[11px]">${daysInPrevMonth - i}</span>`;
            }

            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const isSelected = selectedDate && selectedDate.getFullYear() === year && selectedDate.getMonth() === month && selectedDate.getDate() === day;
                const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;

                let dayClasses = "h-5 w-5 mx-auto flex items-center justify-center rounded font-medium cursor-pointer transition-all duration-150 text-[11px] ";
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
                html += `<span class="h-5 flex items-center justify-center text-slate-300 text-[11px]">${i}</span>`;
            }

            html += `
                </div>
                <div class="flex items-center justify-between mt-0.5 pt-0.5 border-t border-slate-100 text-[11px] font-semibold px-0.5">
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
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
                const bId = menu.id.replace('Dropdown', 'Button');
                const btn = document.getElementById(bId);
                if (btn) btn.classList.remove('ring-1', 'ring-black/35', 'border-transparent');
            });
            document.querySelectorAll('.custom-calendar-card').forEach(c => {
                if (c !== card) c.classList.add('hidden');
            });
            card.classList.toggle('hidden');
            const isOpen = !card.classList.contains('hidden');
            if (isOpen) {
                if (input.value) {
                    const parts = input.value.split('-');
                    if (parts.length === 3) {
                        currentDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    }
                }
                render();
            }
            setIconActive(isOpen);
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                card.classList.add('hidden');
                setIconActive(false);
            }
        });
    }

    setupCustomDatePicker('reportedDate');

    // Load initial data
    loadRecords();
});
