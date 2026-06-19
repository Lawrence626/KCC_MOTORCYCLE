   // Empty list - fetch from API in production
        let disposalItems = [];

        const modal = document.getElementById('disposalModal');
        const overlay = document.getElementById('disposalOverlay');
        const openBtn = document.getElementById('openAddDisposal');
        const closeBtn = document.getElementById('closeDisposalModal');
        const cancelBtn = document.getElementById('cancelDisposalModal');
        const deleteBtn = document.getElementById('deleteDisposalBtn');
        const form = document.getElementById('disposalForm');
        const disposalId = document.getElementById('disposalId');
        const itemName = document.getElementById('itemName');
        const itemQuantity = document.getElementById('itemQuantity');
        const disposalReason = document.getElementById('disposalReason');
        const itemNotes = document.getElementById('itemNotes');
        const itemStatus = document.getElementById('itemStatus');
        const dateIdentified = document.getElementById('dateIdentified');
        const modalTitle = document.getElementById('modalTitle');
        let currentEditId = null;

        function openModal(id = null) {
            currentEditId = id;
            form.reset();

            if (id) {
                const item = disposalItems.find(i => i.id === id);
                if (item) {
                    disposalId.value = id;
                    itemName.value = item.name;
                    itemQuantity.value = item.quantity;
                    disposalReason.value = item.reason;
                    itemNotes.value = item.notes;
                    itemStatus.value = item.status;
                    dateIdentified.value = item.dateIdentified;
                    modalTitle.textContent = 'Edit Disposal Item';
                    deleteBtn.classList.remove('hidden');
                }
            } else {
                disposalId.value = '';
                dateIdentified.value = new Date().toISOString().split('T')[0];
                modalTitle.textContent = 'Add Item for Disposal';
                deleteBtn.classList.add('hidden');
            }
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function getStatusBadge(status) {
            const badges = {
                'Pending': '<span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">⏳ Pending</span>',
                'Approved': '<span class="px-3 py-1 rounded-full bg-cyan-100 text-cyan-800 text-xs font-medium">✓ Approved</span>',
                'Disposed': '<span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium">✓ Disposed</span>'
            };
            return badges[status] || '<span class="text-slate-600">Unknown</span>';
        }

        function updateStats() {
            const total = disposalItems.length;
            const pending = disposalItems.filter(i => i.status === 'Pending').length;
            const approved = disposalItems.filter(i => i.status === 'Approved').length;
            const disposed = disposalItems.filter(i => i.status === 'Disposed').length;

            document.getElementById('totalItems').textContent = total;
            document.getElementById('pendingItems').textContent = pending;
            document.getElementById('approvedItems').textContent = approved;
            document.getElementById('disposedItems').textContent = disposed;
        }

        function renderTable() {
            const tbody = document.getElementById('disposalTableBody');
            if (disposalItems.length === 0) {
                tbody.innerHTML = '<tr class="hover:bg-slate-50"><td colspan="6" class="px-6 py-8 text-center text-slate-500">No items identified for disposal yet.</td></tr>';
                updateStats();
                return;
            }
            tbody.innerHTML = disposalItems.map(item => `
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900">${item.name}</td>
                    <td class="px-6 py-3 text-slate-600"><span class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-xs">${item.reason}</span></td>
                    <td class="px-6 py-3 text-slate-600">${item.quantity}</td>
                    <td class="px-6 py-3">${getStatusBadge(item.status)}</td>
                    <td class="px-6 py-3 text-slate-600 text-xs">${item.dateIdentified}</td>
                    <td class="px-6 py-3 text-center">
                        <button onclick="openEditDisposal(${item.id})" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">Edit</button>
                    </td>
                </tr>
            `).join('');
            updateStats();
        }

        function openEditDisposal(id) {
            openModal(id);
        }

        openBtn.addEventListener('click', () => openModal());
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            if (disposalId.value) {
                const item = disposalItems.find(i => i.id === parseInt(disposalId.value));
                if (item) {
                    item.name = itemName.value;
                    item.quantity = itemQuantity.value;
                    item.reason = disposalReason.value;
                    item.notes = itemNotes.value;
                    item.status = itemStatus.value;
                    item.dateIdentified = dateIdentified.value;
                }
            } else {
                disposalItems.push({
                    id: Math.max(...disposalItems.map(i => i.id), 0) + 1,
                    name: itemName.value,
                    reason: disposalReason.value,
                    quantity: itemQuantity.value,
                    status: itemStatus.value,
                    dateIdentified: dateIdentified.value,
                    notes: itemNotes.value
                });
            }
            renderTable();
            closeModal();
        });

        deleteBtn.addEventListener('click', () => {
            if (currentEditId && confirm('Are you sure you want to delete this disposal item?')) {
                disposalItems = disposalItems.filter(i => i.id !== currentEditId);
                renderTable();
                closeModal();
            }
        });

        // Initial render
        renderTable();
