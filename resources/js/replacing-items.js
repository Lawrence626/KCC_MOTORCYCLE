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

window.addEventListener('DOMContentLoaded', updateDateDisplay);

window.openNewReplacementModal = openNewReplacementModal;
window.closeNewReplacementModal = closeNewReplacementModal;
window.increaseNewQuantity = increaseNewQuantity;
window.decreaseNewQuantity = decreaseNewQuantity;
window.submitNewReplacement = submitNewReplacement;
window.openProcessModal = openProcessModal;
window.closeProcessModal = closeProcessModal;
