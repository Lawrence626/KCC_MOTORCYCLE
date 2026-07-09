function openNewReplacementModal() {
    document.getElementById('newReplacementModal').classList.remove('hidden');
}

function closeNewReplacementModal() {
    document.getElementById('newReplacementModal').classList.add('hidden');
    document.getElementById('newReceiptNo').value = '';
    document.getElementById('newReturnedItem').value = '';
    document.getElementById('newReason').value = 'Defective Item';
    document.getElementById('newReplacementProduct').value = '';
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

function submitNewReplacement() {
    const receiptNo = document.getElementById('newReceiptNo')?.value;
    const returnedItem = document.getElementById('newReturnedItem')?.value;
    const reason = document.getElementById('newReason')?.value;
    const replacementProduct = document.getElementById('newReplacementProduct')?.value;
    const quantity = document.getElementById('newQuantity')?.value;

    if (!receiptNo || !returnedItem || !replacementProduct) {
        alert('Please fill in all required fields');
        return;
    }

    console.log('New Replacement Submitted:', {
        receiptNo,
        returnedItem,
        reason,
        replacementProduct,
        quantity
    });

    alert('✓ Replacement request created successfully!');
    closeNewReplacementModal();
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

function updateDateDisplay() {
    const dateDisplay = document.getElementById('dateDisplay');
    if (!dateDisplay) return;
    const today = new Date();
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    dateDisplay.textContent = today.toLocaleDateString('en-US', options);
}

function toggleStatusDropdown() {
    const dropdown = document.getElementById('statusDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function selectStatus(status) {
    const label = document.getElementById('statusLabel');
    const dropdown = document.getElementById('statusDropdown');
    
    if (label) label.textContent = status;
    
    // Remove emerald from all buttons and add to the selected one
    const dropdownButtons = dropdown?.querySelectorAll('button');
    dropdownButtons?.forEach(btn => {
        if (btn.textContent.trim() === status) {
            btn.classList.add('bg-emerald-100', 'text-emerald-700', 'font-semibold');
            btn.classList.remove('hover:bg-slate-100', 'text-slate-700');
        } else {
            btn.classList.remove('bg-emerald-100', 'text-emerald-700', 'font-semibold');
            btn.classList.add('hover:bg-slate-100', 'text-slate-700');
        }
    });
    
    if (dropdown) dropdown.classList.add('hidden');
}

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function selectDropdown(inputId, value, labelId, dropdownId) {
    const input = document.getElementById(inputId);
    const label = document.getElementById(labelId);
    const dropdown = document.getElementById(dropdownId);

    if (input) input.value = value;
    if (label) label.textContent = value;
    if (dropdown) dropdown.classList.add('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const statusDropdown = document.getElementById('statusDropdown');
    const returnedDropdown = document.getElementById('returnedItemDropdown');
    const reasonDropdown = document.getElementById('reasonDropdown');
    const statusButton = event.target.closest('button[onclick*="toggleStatusDropdown"]');
    const returnedButton = event.target.closest('#returnedItemButton');
    const reasonButton = event.target.closest('#newReasonButton');

    if (!statusButton && statusDropdown && !statusDropdown.contains(event.target)) {
        statusDropdown.classList.add('hidden');
    }
    if (!returnedButton && returnedDropdown && !returnedDropdown.contains(event.target)) {
        returnedDropdown.classList.add('hidden');
    }
    if (!reasonButton && reasonDropdown && !reasonDropdown.contains(event.target)) {
        reasonDropdown.classList.add('hidden');
    }
});

window.addEventListener('DOMContentLoaded', function() {
    updateDateDisplay();
    // Initialize first option as selected
    selectStatus('Status: All');
});

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
