function openNewReplacementModal() {
    document.getElementById('newReplacementModal').classList.remove('hidden');
    loadShopProducts();
}

async function loadShopProducts() {
    try {
        const response = await fetch('/api/shop-inventory/shop-products', {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();

        const productList = document.getElementById('productList');
        const productInput = document.getElementById('newReplacementProduct');

        if (result.success && result.data && productList) {
            // Store products for filtering
            window.shopProducts = result.data;

            // Initially empty - will populate on input
            productList.innerHTML = '';

            // Add input event listener for dynamic filtering
            if (productInput && !productInput.hasAttribute('data-filter-added')) {
                productInput.setAttribute('data-filter-added', 'true');
                productInput.addEventListener('input', function () {
                    filterProductDatalist(this.value);
                });
            }
        }
    } catch (error) {
        console.error('Error loading shop products:', error);
    }
}

function filterProductDatalist(searchTerm) {
    const productList = document.getElementById('productList');
    if (!productList || !window.shopProducts) return;

    productList.innerHTML = '';

    // Only show results if user types at least 5 characters
    if (searchTerm.length < 5) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Type at least 5 characters to search...';
        productList.appendChild(option);
        return;
    }

    const searchLower = searchTerm.toLowerCase();
    const filtered = window.shopProducts
        .filter(product =>
            product.name.toLowerCase().includes(searchLower) ||
            product.sku.toLowerCase().includes(searchLower)
        )
        .slice(0, 10); // Limit to top 10 results

    if (filtered.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'No products found';
        productList.appendChild(option);
        return;
    }

    filtered.forEach(product => {
        const option = document.createElement('option');
        option.value = product.name;
        option.textContent = `${product.name} (${product.sku})`;
        productList.appendChild(option);
    });
}

function closeNewReplacementModal() {
    document.getElementById('newReplacementModal').classList.add('hidden');
    document.getElementById('newReceiptNo').value = '';
    document.getElementById('newReturnedItem').value = '';
    document.getElementById('newReason').value = 'Defective Item';
    document.getElementById('customReason').value = '';
    document.getElementById('customReason').classList.add('hidden');
    document.getElementById('newReplacementProduct').value = '';
    document.getElementById('newReplacementProductLabel').textContent = 'Select product...';
    document.getElementById('newQuantity').value = '1';
}

function increaseNewQuantity() {
    const qty = document.getElementById('newQuantity');
    if (!qty) return;
    qty.value = parseInt(qty.value, 10) + 1;
}

function decreaseNewQuantity() {
    const qty = document.getElementById('newQuantity');
    if (!qty) return;
    const current = parseInt(qty.value, 10);
    if (current > 1) {
        qty.value = current - 1;
    }
}

async function submitNewReplacement() {
    const receiptNo = document.getElementById('newReceiptNo')?.value;
    const returnedItem = document.getElementById('newReturnedItem')?.value;
    const reason = document.getElementById('newReason')?.value;
    const customReason = document.getElementById('customReason')?.value;
    const replacementProduct = document.getElementById('newReplacementProduct')?.value;
    const quantity = document.getElementById('newQuantity')?.value;

    if (!receiptNo || !returnedItem || !replacementProduct) {
        alert('Please fill in all required fields');
        return;
    }

    // Validate receipt number format (INV-YYYYMMDD-XXXXXX)
    const receiptPattern = /^INV-\d{8}-\d{6}$/;
    if (!receiptPattern.test(receiptNo)) {
        alert('Invalid receipt number format. Use: INV-YYYYMMDD-XXXXXX');
        return;
    }

    // If "Others Reason" is selected, use custom reason
    const finalReason = reason === 'Others Reason' ? customReason : reason;

    if (reason === 'Others Reason' && !customReason) {
        alert('Please specify the reason');
        return;
    }

    // Add KCC_ prefix to returned item
    const returnedItemSKU = 'KCC_' + returnedItem;

    console.log('New Replacement Submitted:', {
        receiptNo,
        returnedItem: returnedItemSKU,
        reason: finalReason,
        replacementProduct,
        quantity
    });

    try {
        const response = await fetch('/api/replacements', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({
                receipt_no: receiptNo,
                returned_item: returnedItemSKU,
                reason: finalReason,
                replacement_product: replacementProduct,
                quantity: parseInt(quantity, 10)
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('✓ Replacement request created successfully!');
            closeNewReplacementModal();
            loadReplacements(); // Refresh the table
        } else {
            alert('Failed to create replacement: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error creating replacement:', error);
        alert('Error creating replacement. Please try again.');
    }
}

async function loadReplacements() {
    try {
        const response = await fetch('/api/replacements', {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();

        if (result.success && result.data) {
            renderReplacementsTable(result.data);
        }
    } catch (error) {
        console.error('Error loading replacements:', error);
    }
}

function renderReplacementsTable(replacements) {
    const tbody = document.querySelector('tbody');
    if (!tbody) return;

    if (replacements.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="font-medium">No replacements yet</p>
                    <p class="text-sm mt-1">Start by clicking "New Replacement" to create your first replacement request</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = replacements.map(replacement => {
        let actionButtons = '';

        if (replacement.status.toLowerCase() === 'pending') {
            actionButtons = `
                <button onclick="validateReplacement(${replacement.id})" class="text-blue-600 hover:text-blue-700 font-medium mr-2">Validate</button>
                <button onclick="deleteReplacement(${replacement.id})" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
            `;
        } else if (replacement.status.toLowerCase() === 'approved') {
            actionButtons = `
                <button onclick="completeReplacement(${replacement.id})" class="text-emerald-600 hover:text-emerald-700 font-medium">Complete</button>
            `;
        } else {
            actionButtons = `<span class="text-slate-400">No actions</span>`;
        }

        return `
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-slate-900">${replacement.receipt_no}</td>
                <td class="px-6 py-4 text-sm text-slate-700">${new Date(replacement.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-sm text-slate-700">${replacement.returned_item}</td>
                <td class="px-6 py-4 text-sm text-slate-700 text-center">${replacement.quantity}</td>
                <td class="px-6 py-4 text-sm text-slate-700">${replacement.replacement_product}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(replacement.status)}">
                        ${replacement.status}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-center">
                    ${actionButtons}
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'approved': return 'bg-blue-100 text-blue-800';
        case 'completed': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

async function validateReplacement(id) {
    if (!confirm('Are you sure you want to validate this replacement? This will change the status to Approved.')) {
        return;
    }

    try {
        const response = await fetch(`/api/replacements/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ status: 'approved' })
        });

        const result = await response.json();

        if (result.success) {
            alert('✓ Replacement validated successfully!');
            loadReplacements();
        } else {
            alert('Failed to validate replacement: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error validating replacement:', error);
        alert('Error validating replacement. Please try again.');
    }
}

async function completeReplacement(id) {
    if (!confirm('Are you sure you want to complete this replacement?')) {
        return;
    }

    try {
        const response = await fetch(`/api/replacements/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ status: 'completed' })
        });

        const result = await response.json();

        if (result.success) {
            alert('✓ Replacement completed successfully!');
            loadReplacements();
        } else {
            alert('Failed to complete replacement: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error completing replacement:', error);
        alert('Error completing replacement. Please try again.');
    }
}

async function deleteReplacement(id) {
    if (!confirm('Are you sure you want to delete this replacement? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/api/replacements/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('✓ Replacement deleted successfully!');
            loadReplacements();
        } else {
            alert('Failed to delete replacement: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error deleting replacement:', error);
        alert('Error deleting replacement. Please try again.');
    }
}

function openProcessModal(refNo, item) {
    const receiptInput = document.getElementById('receiptNo');
    const returnedItemInput = document.getElementById('returnedItem');
    const modal = document.getElementById('processModal');

    if (receiptInput) receiptInput.value = refNo;
    if (returnedItemInput) returnedItemInput.value = item;
    if (modal) modal.classList.remove('hidden');
}

function closeProcessModal() {
    const modal = document.getElementById('processModal');
    if (modal) modal.classList.add('hidden');
}

function handleDateFilterChange() {
    const dateFilter = document.getElementById('dateFilter');
    if (!dateFilter) return;

    const selectedValue = dateFilter.value;
    console.log('Date filter changed to:', selectedValue);

    // Implement date filtering logic here
    // This would typically fetch data based on the selected date range
    switch (selectedValue) {
        case 'today':
            console.log('Filtering for today');
            break;
        case 'this_week':
            console.log('Filtering for this week');
            break;
        case 'this_month':
            console.log('Filtering for this month');
            break;
        case 'last_month':
            console.log('Filtering for last month');
            break;
        case 'custom':
            console.log('Show custom date range picker');
            break;
    }
}

function closeAllDropdowns(exceptId) {
    console.log('closeAllDropdowns called, keeping open:', exceptId);
    const allDropdownIds = ['statusDropdown', 'returnedItemDropdown', 'reasonDropdown', 'replacementProductDropdown', 'dateDropdown'];
    allDropdownIds.forEach(id => {
        if (id === exceptId) return;
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
}

function toggleStatusDropdown() {
    const dropdown = document.getElementById('statusDropdown');
    if (!dropdown) return;
    const isHidden = dropdown.classList.contains('hidden');
    closeAllDropdowns('statusDropdown');
    if (isHidden) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

function selectStatus(status) {
    const label = document.getElementById('statusLabel');
    const dropdown = document.getElementById('statusDropdown');

    if (label) label.textContent = status;

    const dropdownButtons = dropdown?.querySelectorAll('button');
    dropdownButtons?.forEach(btn => {
        if (btn.textContent.trim() === status) {
            btn.classList.add('bg-black/10', 'text-slate-900', 'font-semibold');
            btn.classList.remove('hover:bg-slate-100', 'text-slate-700');
        } else {
            btn.classList.remove('bg-black/10', 'text-slate-900', 'font-semibold');
            btn.classList.add('hover:bg-slate-100', 'text-slate-700');
        }
    });

    if (dropdown) dropdown.classList.add('hidden');
}

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (!dropdown) return;
    const isHidden = dropdown.classList.contains('hidden');
    closeAllDropdowns(id);
    if (isHidden) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

function selectDropdown(inputId, value, labelId, dropdownId) {
    const input = document.getElementById(inputId);
    const label = document.getElementById(labelId);
    const dropdown = document.getElementById(dropdownId);

    if (input) input.value = value;
    if (label) label.textContent = value;

    const dropdownButtons = dropdown?.querySelectorAll('button');
    dropdownButtons?.forEach(btn => {
        if (btn.textContent.trim() === value) {
            btn.classList.add('bg-black/10', 'text-slate-900', 'font-semibold');
            btn.classList.remove('hover:bg-slate-100', 'text-slate-700');
        } else {
            btn.classList.remove('bg-black/10', 'text-slate-900', 'font-semibold');
            btn.classList.add('hover:bg-slate-100', 'text-slate-700');
        }
    });

    if (dropdown) dropdown.classList.add('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const statusDropdown = document.getElementById('statusDropdown');
    const returnedDropdown = document.getElementById('returnedItemDropdown');
    const reasonDropdown = document.getElementById('reasonDropdown');
    const replacementProductDropdown = document.getElementById('replacementProductDropdown');
    const dateDropdown = document.getElementById('dateDropdown');
    const statusButton = event.target.closest('button[onclick*="toggleStatusDropdown"]');
    const returnedButton = event.target.closest('#returnedItemButton');
    const reasonButton = event.target.closest('#newReasonButton');
    const replacementProductButton = event.target.closest('#replacementProductButton');
    const dateButton = event.target.closest('button[onclick*="toggleDateDropdown"]');

    if (!statusButton && statusDropdown && !statusDropdown.contains(event.target)) {
        statusDropdown.classList.add('hidden');
    }
    if (!returnedButton && returnedDropdown && !returnedDropdown.contains(event.target)) {
        returnedDropdown.classList.add('hidden');
    }
    if (!reasonButton && reasonDropdown && !reasonDropdown.contains(event.target)) {
        reasonDropdown.classList.add('hidden');
    }
    if (!replacementProductButton && replacementProductDropdown && !replacementProductDropdown.contains(event.target)) {
        replacementProductDropdown.classList.add('hidden');
    }
    if (!dateButton && dateDropdown && !dateDropdown.contains(event.target)) {
        dateDropdown.classList.add('hidden');
    }
});

function toggleDateDropdown() {
    const dropdown = document.getElementById('dateDropdown');
    if (!dropdown) return;
    const isHidden = dropdown.classList.contains('hidden');
    closeAllDropdowns('dateDropdown');
    if (isHidden) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

function selectDateFilter(value, label) {
    const input = document.getElementById('dateFilter');
    const labelEl = document.getElementById('dateFilterLabel');
    const dropdown = document.getElementById('dateDropdown');

    if (input) input.value = value;
    if (labelEl) labelEl.textContent = label;

    const dropdownButtons = dropdown?.querySelectorAll('button');
    dropdownButtons?.forEach(btn => {
        if (btn.textContent.trim() === label) {
            btn.classList.add('bg-black/10', 'text-slate-900', 'font-semibold');
            btn.classList.remove('hover:bg-slate-100', 'text-slate-700');
        } else {
            btn.classList.remove('bg-black/10', 'text-slate-900', 'font-semibold');
            btn.classList.add('hover:bg-slate-100', 'text-slate-700');
        }
    });

    if (dropdown) dropdown.classList.add('hidden');

    handleDateFilterChange();
}

window.addEventListener('DOMContentLoaded', function () {
    // Load replacements on page load
    loadReplacements();

    // Handle reason dropdown change to show/hide custom reason input
    const reasonSelect = document.getElementById('newReason');
    const customReasonInput = document.getElementById('customReason');

    if (reasonSelect && customReasonInput) {
        reasonSelect.addEventListener('change', function () {
            if (this.value === 'Others Reason') {
                customReasonInput.classList.remove('hidden');
                customReasonInput.focus();
            } else {
                customReasonInput.classList.add('hidden');
                customReasonInput.value = '';
            }
        });
    }

    // Handle search input for filtering
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            filterReplacements(searchTerm);
        });
    }
});

function filterReplacements(searchTerm) {
    const tableRows = document.querySelectorAll('tbody tr');

    tableRows.forEach(row => {
        if (row.querySelector('td[colspan]')) {
            // Skip empty state row
            return;
        }

        const refNo = row.cells[0]?.textContent.toLowerCase() || '';
        const returnedItem = row.cells[2]?.textContent.toLowerCase() || '';
        const replacementItem = row.cells[4]?.textContent.toLowerCase() || '';

        const matchesSearch = searchTerm === '' ||
            refNo.includes(searchTerm) ||
            returnedItem.includes(searchTerm) ||
            replacementItem.includes(searchTerm);

        row.style.display = matchesSearch ? '' : 'none';
    });
}

window.openNewReplacementModal = openNewReplacementModal;
window.closeNewReplacementModal = closeNewReplacementModal;
window.increaseNewQuantity = increaseNewQuantity;
window.decreaseNewQuantity = decreaseNewQuantity;
window.submitNewReplacement = submitNewReplacement;
window.openProcessModal = openProcessModal;
window.closeProcessModal = closeProcessModal;
window.toggleStatusDropdown = toggleStatusDropdown;
window.selectStatus = selectStatus;
window.toggleDropdown = toggleDropdown;
window.selectDropdown = selectDropdown;
window.validateReplacement = validateReplacement;
window.completeReplacement = completeReplacement;
window.deleteReplacement = deleteReplacement;
window.toggleDateDropdown = toggleDateDropdown;
window.selectDateFilter = selectDateFilter;