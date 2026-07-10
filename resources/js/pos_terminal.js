const posState = {
    cart: [],
    services: [
        { id: 'installation', label: 'Parts Installation', price: 120.00 },
        { id: 'tuneup', label: 'Engine Tune-up', price: 250.00 },
        { id: 'brake_adjust', label: 'Brake Adjustment', price: 180.00 },
    ],
    selectedServices: new Set(),
    extraCharge: 0,
    discount: 0,
    paymentMethod: 'cash',
    productImages: {},
    lastReceipt: null,
    transactionHistory: [],
    transactionHistoryPage: 1,
    transactionHistoryPageSize: 8,
    apiProductsUrl: window.POS?.routes?.apiProducts || '/api/shop-inventory/products',
    productPage: 1,
    productPageSize: 9,
    productTotalCount: 0,
    productSearchQuery: '',
};

function loadTransactionHistory() {
    try {
        const stored = localStorage.getItem('posTransactionHistory');
        return stored ? JSON.parse(stored) : [];
    } catch (error) {
        console.error('Failed to load transaction history', error);
        return [];
    }
}

function saveTransactionHistory() {
    try {
        localStorage.setItem('posTransactionHistory', JSON.stringify(posState.transactionHistory));
    } catch (error) {
        console.error('Failed to save transaction history', error);
    }
}

function formatDateForHistory(date) {
    const d = new Date(date);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatDateIso(date) {
    const d = new Date(date);
    if (Number.isNaN(d.getTime())) return '';
    return d.toISOString().slice(0, 10);
}

function recordTransaction(invoice, date, total, paymentMethod, items) {
    // Include SKU and other details in transaction history items
    const itemsWithDetails = items.map(item => {
        const cartItem = posState.cart.find(c => c.id === item.id);
        return {
            id: item.id,
            name: item.name,
            sku: cartItem?.sku || '',
            compatibility: cartItem?.compatibility || '',
            quantity: item.qty,
            price: item.price,
        };
    });

    const transaction = {
        invoice,
        date,
        total,
        paymentMethod,
        items: itemsWithDetails,
        createdAt: new Date().toISOString(),
        services: {
            selected: Array.from(posState.selectedServices),
            total: Array.from(posState.selectedServices).reduce((sum, serviceId) => {
                const service = posState.services.find(s => s.id === serviceId);
                return service ? sum + service.price : sum;
            }, 0)
        },
        extraCharge: Number(document.getElementById('posExtraChargeInput')?.value || 0),
        discount: Number(document.getElementById('posDiscountInput')?.value || 0),
        tax: 0,
        amountReceived: posState.amountReceived || total,
        change: posState.change || 0,
    };
    posState.transactionHistory.unshift(transaction);
    saveTransactionHistory();

    // Also save to database
    saveTransactionToDatabase(invoice, total, paymentMethod, items);
}

function saveTransactionToDatabase(invoice, total, paymentMethod, items) {
    // Calculate totals for the database
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const extra = Number(document.getElementById('posExtraChargeInput')?.value || 0);
    const discount = Number(document.getElementById('posDiscountInput')?.value || 0);
    const servicesTotal = Array.from(posState.selectedServices).reduce((sum, serviceId) => {
        const service = posState.services.find(s => s.id === serviceId);
        return service ? sum + service.price : sum;
    }, 0);
    const subtotalWithExtras = subtotal + servicesTotal + extra - discount;
    const tax = Math.max(0, subtotalWithExtras * 0.12);

    // Prepare items with additional data
    const transactionItems = items.map(item => {
        const cartItem = posState.cart.find(c => c.id === item.id);
        return {
            id: item.id,
            name: item.name,
            sku: cartItem?.sku || '',
            compatibility: cartItem?.compatibility || '',
            quantity: item.qty,
            unit_price: item.price,
            category: cartItem?.category || 'Uncategorized',
        };
    });

    const payload = {
        invoice_number: invoice,
        items: transactionItems,
        subtotal: subtotal,
        services_total: servicesTotal,
        extra_charge: extra,
        discount: discount,
        tax: tax,
        total_amount: total,
        payment_method: paymentMethod === 'qr' ? 'qr' : 'cash',
    };

    fetch('/api/pos/transactions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify(payload),
    })
    .then(response => {
        if (!response.ok) {
            console.error('Failed to save transaction to database', response.status);
            return;
        }
        return response.json();
    })
    .then(data => {
        console.log('Transaction saved to database:', data);
    })
    .catch(error => {
        console.error('Error saving transaction to database:', error);
    });
}

function formatCurrency(value) {
    return `₱${Number(value || 0).toFixed(2)}`;
}

function findCartItem(productId) {
    return posState.cart.find(item => item.id === productId);
}

function updateTotals() {
    const subtotal = posState.cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0);
    const servicesTotal = Array.from(posState.selectedServices).reduce((sum, serviceId) => {
        const service = posState.services.find(s => s.id === serviceId);
        return service ? sum + service.price : sum;
    }, 0);
    const extra = parseFloat(posState.extraCharge) || 0;
    const discount = parseFloat(posState.discount) || 0;

    // VAT-Inclusive: Total = Subtotal + Services + Extra - Discount
    // VAT is already included in all prices
    const total = Math.max(0, subtotal + servicesTotal + extra - discount);

    // Included VAT = Total × (12 / 112)
    const includedVat = total * (12 / 112);

    // VATable Sales = Total - Included VAT
    const vatableSales = total - includedVat;

    document.getElementById('posSubtotal').textContent = formatCurrency(subtotal);
    document.getElementById('posServicesTotal').textContent = formatCurrency(servicesTotal);
    document.getElementById('posExtraCharge').textContent = formatCurrency(extra);
    document.getElementById('posDiscount').textContent = formatCurrency(discount);
    document.getElementById('posTotal').textContent = formatCurrency(total);
    const posTaxEl = document.getElementById('posTax');
    if (posTaxEl) posTaxEl.textContent = formatCurrency(includedVat);
    const posSubtotalSummaryEl = document.getElementById('posSubtotalSummary');
    if (posSubtotalSummaryEl) posSubtotalSummaryEl.textContent = formatCurrency(subtotal);
    const posServicesSummaryEl = document.getElementById('posServicesSummary');
    if (posServicesSummaryEl) posServicesSummaryEl.textContent = formatCurrency(servicesTotal);
    const posExtraSummaryEl = document.getElementById('posExtraSummary');
    if (posExtraSummaryEl) posExtraSummaryEl.textContent = formatCurrency(extra);
    const posDiscountSummaryEl = document.getElementById('posDiscountSummary');
    if (posDiscountSummaryEl) posDiscountSummaryEl.textContent = formatCurrency(discount);
    const posTotalSummaryEl = document.getElementById('posTotalSummary');
    if (posTotalSummaryEl) posTotalSummaryEl.textContent = formatCurrency(total);
}

function saveProductImagePreview(cardId, dataUrl) {
    try {
        posState.productImages[cardId] = dataUrl;
        const serialized = JSON.stringify(posState.productImages);
        const sizeInMB = new Blob([serialized]).size / (1024 * 1024);
        console.log(`Saving ${Object.keys(posState.productImages).length} images, total size: ${sizeInMB.toFixed(2)}MB`);

        localStorage.setItem('posProductImages', JSON.stringify(posState.productImages));
        console.log('✓ Saved product image for card:', cardId);
    } catch (error) {
        console.error('Failed to save product image preview:', error.message);
        if (error.name === 'QuotaExceededError') {
            console.warn('localStorage quota exceeded. Clearing old images and retrying...');
            posState.productImages = {};
            try {
                localStorage.setItem('posProductImages', JSON.stringify({}));
                posState.productImages[cardId] = dataUrl;
                localStorage.setItem('posProductImages', JSON.stringify(posState.productImages));
                console.log('✓ Saved after clearing');
            } catch (retryError) {
                console.error('Still cannot save:', retryError.message);
            }
        }
    }
}

function loadProductImagePreviews() {
    try {
        const stored = localStorage.getItem('posProductImages');
        posState.productImages = stored ? JSON.parse(stored) : {};
        console.log(`Loaded ${Object.keys(posState.productImages).length} product images from storage`);
    } catch (error) {
        console.error('Failed to load product image previews', error);
        posState.productImages = {};
    }
}

function applyProductImagePreviews() {
    Object.entries(posState.productImages).forEach(([cardId, dataUrl]) => {
        const input = document.querySelector(`.pos-image-uploader[data-id="${cardId}"]`);
        const card = input?.closest('.pos-image-upload-card');
        if (!card) return;
        const preview = card.querySelector('.pos-image-preview');
        if (preview) preview.style.backgroundImage = `url('${dataUrl}')`;
    });
}

function filterTransactionHistory(fromDate, toDate) {
    return posState.transactionHistory.filter(transaction => {
        if (!transaction.createdAt) return false;
        const created = new Date(transaction.createdAt);
        if (Number.isNaN(created.getTime())) return false;

        if (fromDate) {
            const from = new Date(fromDate + 'T00:00:00');
            if (created < from) return false;
        }
        if (toDate) {
            const to = new Date(toDate + 'T23:59:59');
            if (created > to) return false;
        }
        return true;
    });
}

function goToTransactionHistoryPage(page) {
    const filteredTransactions = filterTransactionHistory(
        document.getElementById('posHistoryFilterFrom')?.value,
        document.getElementById('posHistoryFilterTo')?.value
    );
    const pageSize = posState.transactionHistoryPageSize;
    const totalPages = Math.max(1, Math.ceil(filteredTransactions.length / pageSize));
    posState.transactionHistoryPage = Math.min(Math.max(page, 1), totalPages);
    renderTransactionHistory();
}

function renderTransactionHistory() {
    const body = document.getElementById('posTransactionHistoryBody');
    const empty = document.getElementById('posTransactionHistoryEmpty');
    const pagination = document.getElementById('posTransactionHistoryPagination');
    const pageInfo = document.getElementById('posTransactionHistoryInfo');
    const prevButton = document.getElementById('posHistoryPrevPage');
    const nextButton = document.getElementById('posHistoryNextPage');
    if (!body || !empty || !pagination || !pageInfo || !prevButton || !nextButton) return;

    const fromDate = document.getElementById('posHistoryFilterFrom')?.value;
    const toDate = document.getElementById('posHistoryFilterTo')?.value;
    const transactions = filterTransactionHistory(fromDate, toDate);
    const pageSize = posState.transactionHistoryPageSize;
    const totalPages = Math.max(1, Math.ceil(transactions.length / pageSize));
    posState.transactionHistoryPage = Math.min(Math.max(posState.transactionHistoryPage, 1), totalPages);

    const startIndex = (posState.transactionHistoryPage - 1) * pageSize;
    const endIndex = startIndex + pageSize;
    const pageTransactions = transactions.slice(startIndex, endIndex);

    body.innerHTML = '';
    if (transactions.length === 0) {
        empty.classList.remove('hidden');
        pagination.classList.add('hidden');
        return;
    }

    empty.classList.add('hidden');
    pagination.classList.remove('hidden');

    pageTransactions.forEach(transaction => {
        const row = document.createElement('tr');
        row.className = 'border-b border-slate-200';
        const isChecked = selectedInvoices.has(transaction.invoice);
        const skus = transaction.items.map(item => item.sku || 'N/A').join(', ');
        row.innerHTML = `
            <td class="px-3 py-4 text-center">
                <input type="checkbox" class="transaction-checkbox rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer" data-invoice="${transaction.invoice}" ${isChecked ? 'checked' : ''}>
            </td>
            <td class="px-3 py-4 text-slate-700 font-medium">${transaction.invoice}</td>
            <td class="px-3 py-4 text-slate-700 text-xs">${skus}</td>
            <td class="px-3 py-4 text-slate-700">${formatDateForHistory(transaction.createdAt)}</td>
            <td class="px-3 py-4 text-slate-700">${transaction.paymentMethod === 'cash' ? 'Cash' : 'QR PH'}</td>
            <td class="px-3 py-4 text-center text-slate-700">${transaction.items.length}</td>
            <td class="px-3 py-4 text-right text-slate-900 font-semibold">${formatCurrency(transaction.total)}</td>
            <td class="px-3 py-4 text-center">
                <button onclick="viewTransactionInvoice('${transaction.invoice}')" class="text-emerald-600 hover:text-emerald-700 font-medium text-xs mr-2">View</button>
                <button onclick="deleteTransaction('${transaction.invoice}')" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
            </td>
        `;
        body.appendChild(row);
    });

    const startDisplay = startIndex + 1;
    const endDisplay = Math.min(endIndex, transactions.length);
    pageInfo.textContent = `Showing ${startDisplay}-${endDisplay} of ${transactions.length}`;
    prevButton.disabled = posState.transactionHistoryPage <= 1;
    nextButton.disabled = posState.transactionHistoryPage >= totalPages;
}

function openTransactionHistory() {
    const modal = document.getElementById('posTransactionHistoryModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    renderTransactionHistory();
}

function closeTransactionHistory() {
    const modal = document.getElementById('posTransactionHistoryModal');
    if (!modal) return;
    modal.classList.add('hidden');
}

function viewTransactionInvoice(invoiceNumber) {
    const transaction = posState.transactionHistory.find(t => t.invoice === invoiceNumber);
    if (!transaction) {
        alert('Transaction not found');
        return;
    }

    const invoiceModal = document.getElementById('posInvoiceModal');
    if (!invoiceModal) return;

    // Populate invoice details
    document.getElementById('posInvoiceNumber').textContent = transaction.invoice;
    document.getElementById('posInvoiceNumHeader').textContent = transaction.invoice;
    document.getElementById('posInvoiceDateHeader').textContent = formatDateForHistory(transaction.createdAt);
    document.getElementById('posInvoicePaymentMethod').textContent = transaction.paymentMethod === 'cash' ? 'Cash' : 'QR PH';
    document.getElementById('posInvoiceTotalAmount').textContent = formatCurrency(transaction.total);
    document.getElementById('posInvoiceAmountReceived').textContent = formatCurrency(transaction.amountReceived || transaction.total);
    document.getElementById('posInvoiceChange').textContent = formatCurrency(transaction.change || 0);

    // Calculate and populate summary
    const subtotal = transaction.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const services = transaction.services?.total || 0;
    const extra = transaction.extraCharge || 0;
    const discount = transaction.discount || 0;
    const tax = transaction.tax || 0;

    document.getElementById('posInvoiceSubtotal').textContent = formatCurrency(subtotal);
    document.getElementById('posInvoiceServices').textContent = formatCurrency(services);
    document.getElementById('posInvoiceExtra').textContent = formatCurrency(extra);
    document.getElementById('posInvoiceDiscount').textContent = formatCurrency(discount);
    document.getElementById('posInvoiceTax').textContent = formatCurrency(tax);

    // Populate items table
    const itemsBody = document.getElementById('posInvoiceItemsTable');
    itemsBody.innerHTML = '';

    transaction.items.forEach(item => {
        const row = document.createElement('tr');
        row.className = 'border-b border-slate-200';
        row.innerHTML = `
            <td class="py-1 text-slate-900 font-medium">${item.name}</td>
            <td class="py-1 text-slate-700">${item.sku || 'N/A'}</td>
            <td class="py-1 text-center text-slate-700">${item.quantity}</td>
            <td class="py-1 text-right text-slate-900">${formatCurrency(item.price)}</td>
            <td class="py-1 text-right text-slate-900 font-semibold">${formatCurrency(item.price * item.quantity)}</td>
        `;
        itemsBody.appendChild(row);
    });

    invoiceModal.classList.remove('hidden');
}

function closeInvoiceModal() {
    const modal = document.getElementById('posInvoiceModal');
    if (!modal) return;
    modal.classList.add('hidden');
}

function deleteTransaction(invoiceNumber) {
    if (!confirm(`Are you sure you want to delete transaction ${invoiceNumber}? This action cannot be undone.`)) {
        return;
    }

    posState.transactionHistory = posState.transactionHistory.filter(t => t.invoice !== invoiceNumber);
    saveTransactionHistory();
    renderTransactionHistory();
}

// Store selected invoices
let selectedInvoices = new Set();

// Bulk delete transactions
function bulkDeleteTransactions() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox:checked');
    const selectedInvoicesList = Array.from(checkboxes).map(cb => cb.dataset.invoice);

    console.log('Selected invoices:', selectedInvoicesList);
    console.log('Total transactions before delete:', posState.transactionHistory.length);

    if (selectedInvoicesList.length === 0) {
        alert('Please select at least one transaction to delete');
        return;
    }

    if (!confirm(`YOU WANT TO DELETE THIS PERMANENTLY?\n\nYou are about to delete ${selectedInvoicesList.length} transaction(s):\n${selectedInvoicesList.join(', ')}\n\nThis action cannot be undone.`)) {
        return;
    }

    posState.transactionHistory = posState.transactionHistory.filter(t => !selectedInvoicesList.includes(t.invoice));

    console.log('Total transactions after delete:', posState.transactionHistory.length);

    saveTransactionHistory();
    renderTransactionHistory();

    // Clear selections
    selectedInvoices.clear();
    document.querySelectorAll('.transaction-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('posSelectAllTransactions').checked = false;
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox:checked');
    const bulkActions = document.getElementById('posTransactionHistoryBulkActions');
    const selectedCount = document.getElementById('posSelectedCount');

    if (checkboxes.length > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.classList.add('hidden');
    }
}

function clearHistoryFilters() {
    const fromInput = document.getElementById('posHistoryFilterFrom');
    const toInput = document.getElementById('posHistoryFilterTo');
    if (fromInput) fromInput.value = '';
    if (toInput) toInput.value = '';
    renderTransactionHistory();
}

function generateQRCode(text) {
    const qrContainer = document.getElementById('posQRCode');
    if (!qrContainer) return;
    qrContainer.innerHTML = '';
    new QRCode(qrContainer, {
        text: text,
        width: 240,
        height: 240,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
}

function showQRPaymentModal(amount) {
    const modal = document.getElementById('posQRPaymentModal');
    if (!modal) return;
    const amountDisplay = document.getElementById('posQRPaymentAmount');
    if (amountDisplay) amountDisplay.textContent = formatCurrency(amount);
    generateQRCode(`instapay|${amount}|KCC Motorcycle|${new Date().toISOString()}`);
    modal.classList.remove('hidden');
}

function closeQRPaymentModal() {
    const modal = document.getElementById('posQRPaymentModal');
    if (!modal) return;
    modal.classList.add('hidden');
}

function completeQRPayment() {
    closeQRPaymentModal();
    populateReceipt();
    const receiptDetails = document.getElementById('receiptInvoiceDetails');
    const viewInvoiceButton = document.getElementById('posViewInvoiceButton');
    const printButton = document.getElementById('posPrintReceiptButton');
    if (receiptDetails) receiptDetails.classList.add('hidden');
    if (viewInvoiceButton) viewInvoiceButton.classList.remove('hidden');
    if (printButton) printButton.classList.add('hidden');
    showReceiptOverlay();
}

function renderCart() {
    const tbody = document.getElementById('posCartBody');
    const emptyMessage = document.getElementById('posEmptyCartMessage');
    const cartFooter = document.getElementById('posCartFooter');

    if (!tbody || !emptyMessage || !cartFooter) return;

    tbody.innerHTML = '';

    if (posState.cart.length === 0) {
        emptyMessage.classList.remove('hidden');
        cartFooter.classList.add('hidden');
        return;
    }

    emptyMessage.classList.add('hidden');
    cartFooter.classList.remove('hidden');

    posState.cart.forEach(item => {
        const row = document.createElement('tr');
        row.className = 'border-b border-slate-200';
        row.innerHTML = `
            <td class="px-3 py-3 text-slate-700 text-xs font-medium">${item.name}</td>
            <td class="px-3 py-3 text-slate-600 text-xs">${item.sku || '-'}</td>
            <td class="px-3 py-3 text-right text-slate-700 text-xs">${formatCurrency(item.unit_price)}</td>
            <td class="px-3 py-3 text-center text-slate-700 text-xs">
                <div class="inline-flex items-center rounded-lg border border-slate-200 overflow-hidden">
                    <button data-action="decrement" data-id="${item.id}" class="px-2 py-1 text-slate-700 hover:bg-slate-100">−</button>
                    <span class="px-3 text-slate-900 text-xs">${item.quantity}</span>
                    <button data-action="increment" data-id="${item.id}" class="px-2 py-1 text-slate-700 hover:bg-slate-100">+</button>
                </div>
            </td>
            <td class="px-3 py-3 text-right text-slate-700 text-xs">${formatCurrency(item.unit_price * item.quantity)}</td>
            <td class="px-3 py-3 text-center text-slate-700 text-xs">
                <button data-action="remove" data-id="${item.id}" class="text-red-600 hover:text-red-800 text-xs font-semibold">Remove</button>
            </td>
        `;
        tbody.appendChild(row);
    });

    updateTotals();
}

function goToCart() {
    const cartSection = document.getElementById('posCartTable');
    if (!cartSection) return;
    cartSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function addProductToCart(product) {
    if (!product || !product.id) return;

    const existing = findCartItem(product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        posState.cart.push({
            id: product.id,
            name: product.name || product.product_name || 'Unnamed Product',
            sku: product.sku,
            category: product.category ?? 'Uncategorized',
            unit_price: Number(product.unit_price || 0),
            quantity: 1,
        });
    }

    renderCart();
    goToCart();
}

function removeCartItem(productId) {
    posState.cart = posState.cart.filter(item => item.id !== productId);
    renderCart();
}

function changeCartQuantity(productId, delta) {
    const item = findCartItem(productId);
    if (!item) return;
    item.quantity = Math.max(1, item.quantity + delta);
    renderCart();
}

function clearCart() {
    posState.cart = [];
    renderCart();
}

async function searchProducts(query = '', page = 1) {
    const productGrid = document.getElementById('posProductGrid');
    if (!productGrid) return;

    posState.productSearchQuery = query;
    posState.productPage = page;

    try {
        productGrid.innerHTML = '<div class="col-span-full rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center text-slate-500">Loading products…</div>';
        const url = new URL(posState.apiProductsUrl, window.location.origin);
        url.searchParams.set('per_page', posState.productPageSize);
        url.searchParams.set('page', page);
        if (query) url.searchParams.set('search', query);

        const response = await fetch(url.toString());
        if (!response.ok) throw new Error(`API error: ${response.status}`);
        const json = await response.json();

        if (!json.data || json.data.length === 0) {
            productGrid.innerHTML = '<div class="col-span-full rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center text-slate-500">' + (query ? 'No products found. Try another keyword or scan the QR code.' : 'No products available. Check your All Stocks inventory.') + '</div>';
            renderProductPagination({ total: 0, per_page: posState.productPageSize, current_page: page, last_page: 0 });
            return;
        }

        posState.productTotalCount = json.pagination?.total || 0;
        productGrid.innerHTML = '';
        json.data.forEach(product => {
            const card = document.createElement('div');
            card.className = 'pos-image-upload-card relative rounded-3xl border border-slate-200 bg-slate-50 p-3 flex flex-col justify-between';
            card.dataset.productId = product.id;
            const stockQty = product.stock_quantity ?? product.stock ?? 'N/A';
            const productName = product.product_name || product.name || 'Unnamed Product';
            const brand = product.brand ? `${product.brand}` : '';
            const compatibility = product.name && product.name !== productName ? product.name : '';

            // Calculate VAT breakdown
            const sellingPrice = Number(product.unit_price || 0);
            const includedVat = sellingPrice * (12 / 112);
            const vatableSales = sellingPrice - includedVat;

            card.innerHTML = `
                <div class="mb-1">
                    <div class="pos-image-preview h-24 w-full overflow-hidden rounded-[10px] bg-slate-200 bg-cover bg-center" style="background-image: url('${product.image || ''}')"></div>
                    <input type="file" accept="image/*" class="pos-image-uploader hidden" data-id="${product.id}" />
                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex-1 pr-2">
                            <h3 class="text-sm font-semibold text-slate-900 line-clamp-2 mb-0">${productName}</h3>
                            ${brand ? `<p class="text-[10px] text-slate-600">${brand}</p>` : ''}
                            ${product.sku ? `<p class="text-[9px] text-slate-500">SKU: ${product.sku}</p>` : ''}
                            ${compatibility ? `<p class="text-[9px] text-slate-500 line-clamp-1">${compatibility}</p>` : ''}
                        </div>
                        <div class="ml-2 flex-shrink-0">
                            <button type="button" aria-label="Upload image" title="Upload image" class="pos-image-upload-trigger inline-flex h-9 w-9 items-center justify-center rounded-[10px] border border-slate-200 bg-white text-slate-700 hover:bg-slate-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 flex flex-col justify-between flex-1">
                    <div class="space-y-1">
                        <p class="text-[11px] text-slate-500 mt-0">Stock: ${stockQty} pcs</p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-slate-900">${formatCurrency(sellingPrice)}</span>
                                <button type="button" class="pos-price-breakdown-toggle group relative inline-flex items-center gap-1 text-[10px] text-slate-500 hover:text-slate-1000 transition" data-product-id="${product.id}" data-vatable="${vatableSales}" data-included-vat="${includedVat}" title="Price Breakdown">
                                    <svg class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                            <button type="button" onclick="posArchiveProduct(${product.id})" class="text-red-600 hover:text-red-700 text-[10px] font-medium flex items-center gap-1" title="Archive Product">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                Archive
                            </button>
                        </div>
                        <div class="pos-price-breakdown hidden mt-2 p-2 bg-slate-100 rounded-lg text-[10px] space-y-1 overflow-hidden transition-all duration-200" data-product-id="${product.id}">
                            <div class="flex justify-between">
                                <span class="text-slate-600">VATable Sales</span>
                                <span class="font-medium text-slate-900">${formatCurrency(vatableSales)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Included VAT (12%)</span>
                                <span class="font-medium text-slate-900">${formatCurrency(includedVat)}</span>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <button type="button" data-id="${product.id}" data-name="${productName}" data-sku="${product.sku || ''}" data-price="${product.unit_price || 0}" class="pos-add-card mt-3 inline-flex h-8 items-center justify-center rounded-[10px] bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Add to Cart</button>
                        </div>
                    </div>
                </div>
            `;
            productGrid.appendChild(card);
        });
        applyProductImagePreviews();
        renderProductPagination(json.pagination);
    } catch (error) {
        console.error('Product search failed', error);
        productGrid.innerHTML = '<div class="col-span-full rounded-3xl border border-slate-200 bg-slate-50 p-8 text-center text-slate-500">Unable to load products. Refresh the page and try again.</div>';
        renderProductPagination({ total: 0, per_page: posState.productPageSize, current_page: page, last_page: 0 });
    }
}

function renderProductPagination(pagination) {
    const paginationContainer = document.getElementById('posProductPagination');
    if (!paginationContainer) return;

    const totalPages = pagination.last_page || 0;
    const currentPage = pagination.current_page || 1;

    paginationContainer.innerHTML = '';

    if (totalPages <= 1) {
        paginationContainer.classList.add('hidden');
        return;
    }

    paginationContainer.classList.remove('hidden');

    const prevButton = document.createElement('button');
    prevButton.type = 'button';
    prevButton.className = 'inline-flex items-center rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed';
    prevButton.textContent = '← Prev';
    prevButton.disabled = currentPage <= 1;
    prevButton.onclick = () => searchProducts(posState.productSearchQuery, currentPage - 1);
    paginationContainer.appendChild(prevButton);

    // Show page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let page = startPage; page <= endPage; page++) {
        const pageButton = document.createElement('button');
        pageButton.type = 'button';
        if (page === currentPage) {
            pageButton.className = 'inline-flex items-center justify-center rounded-[10px] bg-slate-400 text-white w-8 h-8 text-sm font-semibold';
        } else {
            pageButton.className = 'inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-sm font-semibold hover:bg-slate-50';
        }
        pageButton.textContent = page;
        pageButton.onclick = () => searchProducts(posState.productSearchQuery, page);
        paginationContainer.appendChild(pageButton);
    }

    const nextButton = document.createElement('button');
    nextButton.type = 'button';
    nextButton.className = 'inline-flex items-center rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed';
    nextButton.textContent = 'Next →';
    nextButton.disabled = currentPage >= totalPages;
    nextButton.onclick = () => searchProducts(posState.productSearchQuery, currentPage + 1);
    paginationContainer.appendChild(nextButton);
}

async function scanProduct() {
    const scanInput = document.getElementById('posScanInput');
    const scanFeedback = document.getElementById('posScanFeedback');

    if (!scanInput) return;
    const query = scanInput.value.trim();
    if (!query) {
        if (scanFeedback) scanFeedback.textContent = 'Enter a QR code value or SKU to scan.';
        return;
    }

    try {
        const url = new URL(posState.apiProductsUrl, window.location.origin);
        url.searchParams.set('per_page', '1');
        url.searchParams.set('search', query);

        const response = await fetch(url.toString());
        const json = await response.json();

        if (json.data && json.data.length > 0) {
            const product = json.data[0];
            addProductToCart({
                id: product.id,
                name: product.name || product.product_name,
                sku: product.sku || '',
                unit_price: product.unit_price
            });
            scanInput.value = '';
            if (scanFeedback) scanFeedback.textContent = `Added ${product.name} to cart.`;
        } else if (scanFeedback) {
            scanFeedback.textContent = 'Product not found. Please try another barcode or SKU.';
        }
    } catch (error) {
        console.error('Scan failed', error);
        if (scanFeedback) scanFeedback.textContent = 'Scan failed. Please try again.';
    }
}

function updateServiceSelection(event) {
    const serviceId = event.target.value;
    if (event.target.checked) {
        posState.selectedServices.add(serviceId);
    } else {
        posState.selectedServices.delete(serviceId);
    }
    updateTotals();
}

function updateExtraCharge(value) {
    posState.extraCharge = Number(value) || 0;
    updateTotals();
}

function updateDiscount(value) {
    posState.discount = Number(value) || 0;
    updateTotals();
}

function togglePriceBreakdown(productId) {
    const breakdown = document.querySelector(`.pos-price-breakdown[data-product-id="${productId}"]`);
    const toggle = document.querySelector(`.pos-price-breakdown-toggle[data-product-id="${productId}"]`);
    const chevron = toggle?.querySelector('svg');

    if (breakdown) {
        breakdown.classList.toggle('hidden');
        if (chevron) {
            chevron.style.transform = breakdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }
}

function updatePaymentMethod(value) {
    posState.paymentMethod = value;
    document.querySelectorAll('.pos-payment-summary-method').forEach(el => {
        el.textContent = value === 'cash' ? 'Cash' : 'QR PH';
    });
}

function openPaymentModal() {
    if (posState.cart.length === 0) {
        alert('The cart is empty. Add items before proceeding to payment.');
        return;
    }

    const itemsContainer = document.getElementById('posPaymentItems');
    const invoiceLabel = document.getElementById('posPaymentInvoice');
    const dateLabel = document.getElementById('posPaymentDate');
    const subtotalLabel = document.getElementById('posPaymentSubtotal');
    const servicesLabel = document.getElementById('posPaymentServices');
    const extraLabel = document.getElementById('posPaymentExtra');
    const discountLabel = document.getElementById('posPaymentDiscount');
    const taxLabel = document.getElementById('posPaymentTax');
    const totalLabel = document.getElementById('posPaymentTotal');
    const methodRadios = document.getElementsByName('posPaymentModalMethod');

    if (!itemsContainer || !invoiceLabel || !dateLabel || !subtotalLabel || !servicesLabel || !extraLabel || !discountLabel || !taxLabel || !totalLabel) return;

    itemsContainer.innerHTML = '';
    posState.cart.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-3 py-2 text-slate-700">${item.name}</td>
            <td class="px-3 py-2 text-slate-700">${item.sku || 'N/A'}</td>
            <td class="px-3 py-2 text-slate-700">${item.quantity}</td>
            <td class="px-3 py-2 text-right text-slate-700">${formatCurrency(item.unit_price * item.quantity)}</td>
        `;
        itemsContainer.appendChild(row);
    });

    const servicesTotal = Array.from(posState.selectedServices).reduce((sum, serviceId) => {
        const service = posState.services.find(s => s.id === serviceId);
        return service ? sum + service.price : sum;
    }, 0);

    const extra = parseFloat(posState.extraCharge) || 0;
    const discount = parseFloat(posState.discount) || 0;
    const subtotal = posState.cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0);

    // VAT-Inclusive: Total = Subtotal + Services + Extra - Discount
    // VAT is already included in all prices
    const total = Math.max(0, subtotal + servicesTotal + extra - discount);

    // Included VAT = Total × (12 / 112)
    const includedVat = total * (12 / 112);

    const now = new Date();
    invoiceLabel.textContent = `INV-${now.getFullYear()}${String(now.getMonth()+1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}-${now.getHours()}${String(now.getMinutes()).padStart(2,'0')}${String(now.getSeconds()).padStart(2,'0')}`;
    dateLabel.textContent = now.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    subtotalLabel.textContent = formatCurrency(subtotal);
    servicesLabel.textContent = formatCurrency(servicesTotal);
    extraLabel.textContent = formatCurrency(extra);
    discountLabel.textContent = formatCurrency(discount);
    taxLabel.textContent = formatCurrency(includedVat);
    totalLabel.textContent = formatCurrency(total);

    if (methodRadios.length > 0) {
        Array.from(methodRadios).forEach(radio => {
            if (radio.value === posState.paymentMethod) {
                radio.checked = true;
            }
        });
    }

    document.getElementById('posPaymentModal')?.classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('posPaymentModal')?.classList.add('hidden');
}

function showReceiptOverlay() {
    const receiptOverlay = document.getElementById('posReceiptOverlay');
    if (receiptOverlay) receiptOverlay.classList.remove('hidden');
}

function hideReceiptOverlay() {
    const receiptOverlay = document.getElementById('posReceiptOverlay');
    if (receiptOverlay) receiptOverlay.classList.add('hidden');
}

function populateReceipt() {
    const receiptInvoice = document.getElementById('receiptInvoice');
    const receiptDate = document.getElementById('receiptDate');
    const receiptServices = document.getElementById('receiptServices');
    const receiptExtra = document.getElementById('receiptExtra');
    const receiptDiscount = document.getElementById('receiptDiscount');
    const receiptTax = document.getElementById('receiptTax');
    const receiptTotal = document.getElementById('receiptTotal');
    const receiptPaid = document.getElementById('receiptPaid');
    const receiptPaymentMethod = document.getElementById('receiptPaymentMethod');

    if (!receiptInvoice) return;

    const receiptData = posState.lastReceipt || {
        invoiceNumber: document.getElementById('posPaymentInvoice')?.textContent || 'INV-000000',
        date: new Date().toISOString(),
        items: posState.cart.map(item => ({ id: item.id, name: item.name, sku: item.sku || '', qty: item.quantity, price: item.unit_price })),
        subtotal: posState.cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0),
        servicesTotal: Array.from(posState.selectedServices).reduce((sum, serviceId) => {
            const service = posState.services.find(s => s.id === serviceId);
            return service ? sum + service.price : sum;
        }, 0),
        extra: Number(posState.extraCharge || 0),
        discount: Number(posState.discount || 0),
        tax: 0,
        total: 0,
        amountReceived: 0,
        paymentMethod: posState.paymentMethod,
    };

    if (!posState.lastReceipt) {
        // VAT-Inclusive: Total = Subtotal + Services + Extra - Discount
        const subtotalWithExtras = receiptData.subtotal + receiptData.servicesTotal + receiptData.extra - receiptData.discount;
        receiptData.total = Math.max(0, subtotalWithExtras);

        // Included VAT = Total × (12 / 112)
        receiptData.tax = receiptData.total * (12 / 112);

        receiptData.amountReceived = receiptData.total;
    }

    if (receiptInvoice) receiptInvoice.textContent = receiptData.invoiceNumber;
    if (receiptDate) receiptDate.textContent = new Date(receiptData.date).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    if (receiptServices) receiptServices.textContent = formatCurrency(receiptData.servicesTotal);
    if (receiptExtra) receiptExtra.textContent = formatCurrency(receiptData.extra);
    if (receiptDiscount) receiptDiscount.textContent = formatCurrency(receiptData.discount);
    if (receiptTax) receiptTax.textContent = formatCurrency(receiptData.tax);
    if (receiptTotal) receiptTotal.textContent = formatCurrency(receiptData.total);
    if (receiptPaid) receiptPaid.textContent = formatCurrency(receiptData.amountReceived ?? receiptData.total);
    if (receiptPaymentMethod) receiptPaymentMethod.textContent = receiptData.paymentMethod === 'qr' ? 'QR PH' : 'Cash';

    const invoiceNumHeader = document.getElementById('invoiceNumHeader');
    const invoiceDateHeader = document.getElementById('invoiceDateHeader');
    const invoiceItemsTable = document.getElementById('invoiceItemsTable');
    const invoiceSubtotal = document.getElementById('invoiceSubtotal');
    const invoiceDiscount = document.getElementById('invoiceDiscount');
    const invoiceTax = document.getElementById('invoiceTax');
    const invoiceTotalAmount = document.getElementById('invoiceTotalAmount');
    const invoiceAmountReceived = document.getElementById('invoiceAmountReceived');
    const invoiceChange = document.getElementById('invoiceChange');
    const invoicePaymentMethod = document.getElementById('invoicePaymentMethod');

    if (invoiceNumHeader) invoiceNumHeader.textContent = receiptData.invoiceNumber;
    if (invoiceDateHeader) invoiceDateHeader.textContent = new Date(receiptData.date).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

    if (invoiceItemsTable) {
        invoiceItemsTable.innerHTML = '';
        receiptData.items.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'border-b border-slate-200';
            row.innerHTML = `
                <td class="text-slate-700 py-1 text-xs">${item.name}</td>
                <td class="text-slate-700 py-1 text-xs">${item.sku || 'N/A'}</td>
                <td class="text-center text-slate-700 py-1 text-xs">${item.qty}</td>
                <td class="text-right text-slate-700 py-1 text-xs">${formatCurrency(item.price)}</td>
                <td class="text-right text-slate-700 py-1 text-xs">${formatCurrency(item.price * item.qty)}</td>
            `;
            invoiceItemsTable.appendChild(row);
        });
    }

    if (invoiceSubtotal) invoiceSubtotal.textContent = formatCurrency(receiptData.subtotal);
    if (invoiceDiscount) invoiceDiscount.textContent = formatCurrency(receiptData.discount);
    if (invoiceTax) invoiceTax.textContent = formatCurrency(receiptData.tax);
    if (invoiceTotalAmount) invoiceTotalAmount.textContent = formatCurrency(receiptData.total);
    if (invoiceAmountReceived) invoiceAmountReceived.textContent = formatCurrency(receiptData.amountReceived ?? receiptData.total);
    if (invoiceChange) invoiceChange.textContent = formatCurrency(Math.max(0, (receiptData.amountReceived ?? receiptData.total) - receiptData.total));
    if (invoicePaymentMethod) invoicePaymentMethod.textContent = receiptData.paymentMethod === 'qr' ? 'QR PH' : 'Cash';
}

function buildInvoiceHTML(receiptData, cashierName) {
    const invoiceNum   = receiptData.invoiceNumber || 'INV-000000';
    const dateStr      = new Date(receiptData.date).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const subtotal     = receiptData.subtotal    || 0;
    const discount     = receiptData.discount    || 0;
    const tax          = receiptData.tax         || 0;
    const total        = receiptData.total       || 0;
    const amtReceived  = receiptData.amountReceived != null ? receiptData.amountReceived : total;
    const change       = Math.max(0, amtReceived - total);
    const vatableSales = total - tax;
    const payMethod    = receiptData.paymentMethod === 'qr' ? 'QR PH / GCash' : (receiptData.paymentMethod || 'Cash');

    const rowsHTML = (receiptData.items || []).map(item => {
        const skuDisplay = item.sku ? item.sku : '—';
        const lineTotal  = item.price * item.qty;
        return `
            <tr>
                <td class="item-name">${item.name}</td>
                <td class="item-sku">${skuDisplay}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-right">${formatCurrency(item.price)}</td>
                <td class="text-right">${formatCurrency(lineTotal)}</td>
            </tr>`;
    }).join('');

    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice ${invoiceNum}</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  @page { size: A4 portrait; margin: 18mm 16mm; }
  body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

  /* ── Header ── */
  .inv-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 14px; border-bottom: 2.5px solid #1a1a1a; margin-bottom: 20px; }
  .brand-block { display: flex; gap: 14px; align-items: flex-start; }
  .brand-logo { width: 52px; height: 52px; border-radius: 8px; background: #d1fae5; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: #065f46; letter-spacing: 0.04em; flex-shrink: 0; }
  .brand-name { font-size: 14px; font-weight: 800; text-transform: uppercase; line-height: 1.25; color: #0f172a; margin-bottom: 5px; }
  .brand-address { font-size: 10px; color: #475569; line-height: 1.6; }
  .inv-meta { text-align: right; }
  .inv-meta .inv-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
  .inv-meta .inv-value { font-size: 12px; font-weight: 700; color: #0f172a; }
  .inv-meta .inv-badge { display: inline-block; background: #0f172a; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 4px; letter-spacing: 0.04em; margin-bottom: 8px; }

  /* ── Official Receipt label ── */
  .doc-title { text-align: center; margin-bottom: 18px; }
  .doc-title h1 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: #0f172a; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; display: inline-block; padding: 4px 24px; }

  /* ── Items table ── */
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
  .items-table thead tr { background: #0f172a; color: #fff; }
  .items-table thead th { padding: 8px 10px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
  .items-table tbody tr { border-bottom: 1px solid #e2e8f0; }
  .items-table tbody tr:nth-child(even) { background: #f8fafc; }
  .items-table tbody td { padding: 9px 10px; font-size: 11px; vertical-align: top; }
  .item-name { font-weight: 600; color: #0f172a; }
  .item-sku  { font-family: 'Courier New', monospace; font-size: 10px; color: #64748b; }
  .items-table .text-right  { text-align: right; }
  .items-table .text-center { text-align: center; }

  /* ── Summary ── */
  .summary-wrap { display: flex; justify-content: flex-end; margin-bottom: 28px; }
  .summary-box { width: 300px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
  .summary-box .s-row { display: flex; justify-content: space-between; padding: 6px 14px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
  .summary-box .s-row:last-child { border-bottom: none; }
  .summary-box .s-label { color: #475569; }
  .summary-box .s-value { font-weight: 600; color: #0f172a; }
  .summary-box .s-total { background: #0f172a; color: #fff; }
  .summary-box .s-total .s-label { color: #94a3b8; font-weight: 700; font-size: 12px; }
  .summary-box .s-total .s-value { color: #34d399; font-weight: 800; font-size: 13px; }

  /* ── Payment info ── */
  .payment-section { display: flex; gap: 16px; margin-bottom: 28px; }
  .pay-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; }
  .pay-box .pay-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8; margin-bottom: 3px; }
  .pay-box .pay-value { font-size: 13px; font-weight: 700; color: #0f172a; }
  .pay-box .pay-value.change { color: #059669; }

  /* ── Footer ── */
  .inv-footer { text-align: center; margin-top: 20px; padding-top: 14px; border-top: 1px dashed #cbd5e1; }
  .inv-footer p { font-size: 10px; color: #64748b; line-height: 1.7; }
  .inv-footer .thank-you { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }

  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
</head>
<body>

  <!-- Header -->
  <div class="inv-header">
    <div class="brand-block">
      <div class="brand-logo">KCC</div>
      <div>
        <div class="brand-name">KCC Motorcycle Parts<br>&amp; Accessories</div>
        <div class="brand-address">
          129 Motorcycle St., Barangay 123, City, Philippines<br>
          Tel: (02) 1234-56578
        </div>
      </div>
    </div>
    <div class="inv-meta">
      <div class="inv-badge">OFFICIAL RECEIPT</div><br>
      <span class="inv-label">Invoice #</span><br>
      <span class="inv-value">${invoiceNum}</span><br><br>
      <span class="inv-label">Date</span><br>
      <span class="inv-value" style="font-size:11px;">${dateStr}</span><br><br>
      <span class="inv-label">Cashier</span><br>
      <span class="inv-value" style="font-size:11px;">${cashierName}</span>
    </div>
  </div>

  <!-- Items Table -->
  <table class="items-table">
    <thead>
      <tr>
        <th style="text-align:left; width:38%">Item / Description</th>
        <th style="text-align:left; width:22%">SKU</th>
        <th style="text-align:center; width:8%">Qty</th>
        <th style="text-align:right; width:16%">Unit Price</th>
        <th style="text-align:right; width:16%">Amount</th>
      </tr>
    </thead>
    <tbody>
      ${rowsHTML || '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:16px;">No items</td></tr>'}
    </tbody>
  </table>

  <!-- Summary -->
  <div class="summary-wrap">
    <div class="summary-box">
      <div class="s-row"><span class="s-label">Subtotal</span><span class="s-value">${formatCurrency(subtotal)}</span></div>
      <div class="s-row"><span class="s-label">Discount</span><span class="s-value">−${formatCurrency(discount)}</span></div>
      <div class="s-row"><span class="s-label">VATable Sales</span><span class="s-value">${formatCurrency(vatableSales)}</span></div>
      <div class="s-row"><span class="s-label">Included VAT (12%)</span><span class="s-value">${formatCurrency(tax)}</span></div>
      <div class="s-row s-total"><span class="s-label">TOTAL</span><span class="s-value">${formatCurrency(total)}</span></div>
    </div>
  </div>

  <!-- Payment -->
  <div class="payment-section">
    <div class="pay-box">
      <div class="pay-label">Payment Method</div>
      <div class="pay-value">${payMethod}</div>
    </div>
    <div class="pay-box">
      <div class="pay-label">Amount Received</div>
      <div class="pay-value">${formatCurrency(amtReceived)}</div>
    </div>
    <div class="pay-box">
      <div class="pay-label">Change</div>
      <div class="pay-value change">${formatCurrency(change)}</div>
    </div>
  </div>

  <!-- Footer -->
  <div class="inv-footer">
    <div class="thank-you">Thank you for your purchase!</div>
    <p>This serves as your official receipt. Please keep this for your records.</p>
    <p>For concerns, please contact us at Tel: (02) 1234-56578</p>
  </div>

</body>
</html>`;
}

function printReceipt() {
    const receiptData = posState.lastReceipt;
    if (!receiptData) return;
    const cashierName = document.getElementById('receiptCashierName')?.textContent
        || window.POS?.cashier || 'Cashier';
    openPrintWindow(buildInvoiceHTML(receiptData, cashierName));
}

function openPrintWindow(html) {
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); }, 350);
}

function confirmPayment() {
    updatePaymentMethod(document.querySelector('input[name="posPaymentModalMethod"]:checked')?.value || posState.paymentMethod);
    closePaymentModal();

    const invoiceNumber = document.getElementById('posPaymentInvoice')?.textContent || 'INV-000000';
    const now = new Date();
    const subtotal = posState.cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0);
    const servicesTotal = Array.from(posState.selectedServices).reduce((sum, serviceId) => {
        const service = posState.services.find(s => s.id === serviceId);
        return service ? sum + service.price : sum;
    }, 0);
    const extra = Number(posState.extraCharge || 0);
    const discount = Number(posState.discount || 0);

    // VAT-Inclusive: Total = Subtotal + Services + Extra - Discount
    // VAT is already included in all prices
    const transactionTotal = Math.max(0, subtotal + servicesTotal + extra - discount);

    // Included VAT = Total × (12 / 112)
    const tax = transactionTotal * (12 / 112);

    const items = posState.cart.map(item => ({ id: item.id, name: item.name, sku: item.sku || '', qty: item.quantity, price: item.unit_price }));

    posState.lastReceipt = {
        invoiceNumber,
        date: now.toISOString(),
        items,
        subtotal,
        servicesTotal,
        extra,
        discount,
        tax,
        total: transactionTotal,
        paymentMethod: posState.paymentMethod,
    };

    recordTransaction(
        invoiceNumber,
        now.toISOString(),
        transactionTotal,
        posState.paymentMethod,
        items
    );
    renderTransactionHistory();
    populateReceipt();

    if (posState.paymentMethod === 'qr') {
        showQRPaymentModal(transactionTotal);
    } else {
        const receiptDetails = document.getElementById('receiptInvoiceDetails');
        const viewInvoiceButton = document.getElementById('posViewInvoiceButton');
        const printButton = document.getElementById('posPrintReceiptButton');
        if (receiptDetails) receiptDetails.classList.add('hidden');
        if (viewInvoiceButton) viewInvoiceButton.classList.remove('hidden');
        if (printButton) printButton.classList.add('hidden');
        showReceiptOverlay();
    }

    document.querySelectorAll('.pos-service-checkbox').forEach(el => { el.checked = false; });
    posState.selectedServices.clear();
    document.getElementById('posExtraChargeInput').value = '0';
    document.getElementById('posDiscountInput').value = '0';
    const scanFeedback = document.getElementById('posScanFeedback');
    if (scanFeedback) scanFeedback.textContent = '';
    updateTotals();
    clearCart();
}

function viewInvoice() {
    const receiptDetails = document.getElementById('receiptInvoiceDetails');
    const printButton = document.getElementById('posPrintReceiptButton');
    const viewInvoiceButton = document.getElementById('posViewInvoiceButton');
    if (receiptDetails) receiptDetails.classList.remove('hidden');
    if (printButton) printButton.classList.remove('hidden');
    if (viewInvoiceButton) viewInvoiceButton.classList.add('hidden');
}

function setupPosEvents() {
    const scanButton = document.getElementById('posScanButton');
    if (scanButton) scanButton.addEventListener('click', scanProduct);

    const scanInput = document.getElementById('posScanInput');
    if (scanInput) scanInput.addEventListener('keypress', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            scanProduct();
        }
    });

    const searchInput = document.getElementById('posProductSearchInput');
    if (searchInput) {
        let searchTimer;
        searchInput.addEventListener('input', event => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                searchProducts(event.target.value);
            }, 250);
        });
    }

    document.getElementById('posProductGrid')?.addEventListener('click', event => {
        const button = event.target.closest('.pos-add-card');
        if (button) {
            const productId = Number(button.dataset.id);
            const name = button.dataset.name;
            const sku = button.dataset.sku;
            const unitPrice = parseFloat(button.dataset.price) || 0;

            if (!productId) return;
            addProductToCart({ id: productId, name, sku, unit_price: unitPrice });
            return;
        }

        const target = event.target instanceof Element ? event.target : event.target.parentElement;
        if (!target) return;

        const uploadButton = target.closest('.pos-image-upload-trigger');
        if (uploadButton) {
            const card = uploadButton.closest('.pos-image-upload-card');
            const input = card?.querySelector('.pos-image-uploader');
            if (input) input.click();
            return;
        }
    });

    document.getElementById('posProductGrid')?.addEventListener('change', event => {
        const input = event.target.closest('.pos-image-uploader');
        if (!input) return;
        const file = input.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = () => {
            // Compress image before saving
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Resize to max 300x300 to reduce file size
                let width = img.width;
                let height = img.height;
                const maxSize = 300;

                if (width > height) {
                    if (width > maxSize) {
                        height = (height * maxSize) / width;
                        width = maxSize;
                    }
                } else {
                    if (height > maxSize) {
                        width = (width * maxSize) / height;
                        height = maxSize;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                // Compress to lower quality JPEG
                const compressedDataUrl = canvas.toDataURL('image/jpeg', 0.7);

                // Update preview
                const card = input.closest('.pos-image-upload-card');
                const preview = card?.querySelector('.pos-image-preview');
                if (preview) preview.style.backgroundImage = `url('${compressedDataUrl}')`;

                // Save compressed version
                if (input.dataset.id) {
                    saveProductImagePreview(input.dataset.id, compressedDataUrl);
                }
            };
            img.src = reader.result;
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('posCartTable')?.addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const productId = Number(button.dataset.id);
        const action = button.dataset.action;
        if (action === 'increment') changeCartQuantity(productId, 1);
        if (action === 'decrement') changeCartQuantity(productId, -1);
        if (action === 'remove') removeCartItem(productId);
    });

    document.getElementById('posEmptyCartButton')?.addEventListener('click', clearCart);
    document.getElementById('posProceedPaymentButton')?.addEventListener('click', openPaymentModal);
    document.getElementById('posProcessPaymentButton')?.addEventListener('click', openPaymentModal);
    document.getElementById('posPaymentModalClose')?.addEventListener('click', closePaymentModal);
    document.getElementById('posPaymentModalCancel')?.addEventListener('click', closePaymentModal);
    document.getElementById('posPaymentModalConfirm')?.addEventListener('click', confirmPayment);
    document.getElementById('posExtraChargeInput')?.addEventListener('input', event => updateExtraCharge(event.target.value));
    document.getElementById('posDiscountInput')?.addEventListener('input', event => updateDiscount(event.target.value));
    document.querySelectorAll('.pos-payment-method').forEach(radio => {
        radio.addEventListener('change', event => updatePaymentMethod(event.target.value));
    });

    document.querySelectorAll('input[name="posPaymentModalMethod"]').forEach(radio => {
        radio.addEventListener('change', event => updatePaymentMethod(event.target.value));
    });

    document.querySelectorAll('.pos-service-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateServiceSelection);
    });

    document.getElementById('posOpenTransactionHistoryButton')?.addEventListener('click', openTransactionHistory);
    document.getElementById('posCloseTransactionHistoryButton')?.addEventListener('click', closeTransactionHistory);
    document.getElementById('posHistoryFilterApplyButton')?.addEventListener('click', () => { posState.transactionHistoryPage = 1; renderTransactionHistory(); });
    document.getElementById('posHistoryFilterClearButton')?.addEventListener('click', () => { posState.transactionHistoryPage = 1; clearHistoryFilters(); });
    document.getElementById('posHistoryPrevPage')?.addEventListener('click', () => goToTransactionHistoryPage(posState.transactionHistoryPage - 1));
    document.getElementById('posHistoryNextPage')?.addEventListener('click', () => goToTransactionHistoryPage(posState.transactionHistoryPage + 1));
    document.getElementById('posCloseInvoiceModalButton')?.addEventListener('click', closeInvoiceModal);
    document.getElementById('posCloseInvoiceDoneButton')?.addEventListener('click', closeInvoiceModal);
    document.getElementById('posPrintInvoiceButton')?.addEventListener('click', () => {
        const invoiceNum = document.getElementById('posInvoiceNumber')?.textContent || 'INV-000000';
        const tx = posState.transactionHistory.find(t => t.invoice === invoiceNum);
        const cashierName = document.getElementById('posInvoiceCashierName')?.textContent
            || window.POS?.cashier || 'Cashier';
        if (tx) {
            const subtotal = tx.total;
            const tax = subtotal * (12 / 112);
            const receiptData = {
                invoiceNumber: tx.invoice,
                date: tx.createdAt,
                items: (tx.items || []).map(i => ({ name: i.name, sku: i.sku || '', qty: i.qty, price: i.price })),
                subtotal,
                discount: 0,
                tax,
                total: subtotal,
                amountReceived: subtotal,
                paymentMethod: tx.paymentMethod,
            };
            openPrintWindow(buildInvoiceHTML(receiptData, cashierName));
        } else if (posState.lastReceipt) {
            openPrintWindow(buildInvoiceHTML(posState.lastReceipt, cashierName));
        }
    });

    // Transaction history checkboxes
    document.getElementById('posSelectAllTransactions')?.addEventListener('change', (event) => {
        const checkboxes = document.querySelectorAll('.transaction-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = event.target.checked;
            const invoice = cb.dataset.invoice;
            if (event.target.checked) {
                selectedInvoices.add(invoice);
            } else {
                selectedInvoices.delete(invoice);
            }
        });
        updateBulkActions();
    });

    document.getElementById('posTransactionHistoryBody')?.addEventListener('change', (event) => {
        if (event.target.classList.contains('transaction-checkbox')) {
            const invoice = event.target.dataset.invoice;
            if (event.target.checked) {
                selectedInvoices.add(invoice);
            } else {
                selectedInvoices.delete(invoice);
            }
            updateBulkActions();
        }
    });

    // Also add event delegation for dynamically added checkboxes
    document.addEventListener('change', (event) => {
        if (event.target.classList.contains('transaction-checkbox')) {
            const invoice = event.target.dataset.invoice;
            if (event.target.checked) {
                selectedInvoices.add(invoice);
            } else {
                selectedInvoices.delete(invoice);
            }
            updateBulkActions();
        }
    });

    document.getElementById('posBulkDeleteTransactions')?.addEventListener('click', bulkDeleteTransactions);

    document.getElementById('posQRPaymentCompleteButton')?.addEventListener('click', completeQRPayment);
    document.getElementById('posCloseQRPaymentButton')?.addEventListener('click', closeQRPaymentModal);

    document.getElementById('posCloseReceiptButton')?.addEventListener('click', hideReceiptOverlay);
    document.getElementById('posCloseReceiptDoneButton')?.addEventListener('click', hideReceiptOverlay);
    document.getElementById('posPrintReceiptButton')?.addEventListener('click', printReceipt);
    document.getElementById('posViewInvoiceButton')?.addEventListener('click', viewInvoice);

    // Mobile Scanner
    document.getElementById('posOpenScannerButton')?.addEventListener('click', openMobileScanner);
    document.getElementById('posCloseMobileScannerButton')?.addEventListener('click', closeMobileScanner);

    // Desktop QR Scanner
    document.getElementById('posOpenDesktopScannerButton')?.addEventListener('click', openDesktopScanner);
    document.getElementById('posCloseDesktopScannerButton')?.addEventListener('click', closeDesktopScanner);

    // Price Breakdown Toggle (Floating popover)
    let currentFloatingBreakdown = null;
    let currentFloatingCleanup = null;
    function removeFloatingBreakdown() {
        if (currentFloatingBreakdown) {
            currentFloatingBreakdown.remove();
            currentFloatingBreakdown = null;
        }
        if (typeof currentFloatingCleanup === 'function') {
            try { currentFloatingCleanup(); } catch (e) { console.error(e); }
            currentFloatingCleanup = null;
        }
    }

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('.pos-price-breakdown-toggle');
        if (toggle) {
            event.preventDefault();
            event.stopPropagation();
            const productId = toggle.dataset.productId;

            // If the same product's popover is open, close it
            if (currentFloatingBreakdown && currentFloatingBreakdown.dataset.productId === productId) {
                removeFloatingBreakdown();
                return;
            }

            removeFloatingBreakdown();

            const vatable = parseFloat(toggle.dataset.vatable) || 0;
            const includedVat = parseFloat(toggle.dataset.includedVat) || 0;

            const pop = document.createElement('div');
            pop.className = 'pos-floating-breakdown absolute z-50 p-2 bg-white/100 border border-slate-200 rounded-lg text-[10px] text-white space-y-1 overflow-hidden shadow-md';
            pop.dataset.productId = productId;
            pop.innerHTML = `
                <div class="flex justify-between">
                    <span class="text-black/80">VATable Sales</span>
                    <span class="font-medium text-black">${formatCurrency(vatable)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-black/80">Included VAT (12%)</span>
                    <span class="font-medium text-white">${formatCurrency(includedVat)}</span>
                </div>
            `;

            document.body.appendChild(pop);

            function positionPop() {
                if (!toggle || !pop) return;
                const rect = toggle.getBoundingClientRect();
                let left = rect.left + window.scrollX;
                let top = rect.bottom + window.scrollY + 6;
                const addBtn = document.querySelector('.pos-add-card[data-id="' + toggle.dataset.productId + '"]');

                // Reset visibility to measure
                pop.style.left = '0px';
                pop.style.top = '0px';

                const popRect = pop.getBoundingClientRect();

                if (addBtn) {
                    const addRect = addBtn.getBoundingClientRect();
                    left = addRect.left + window.scrollX + (addRect.width / 2) - (popRect.width / 2);
                    top = addRect.top + window.scrollY + (addRect.height / 2) - (popRect.height / 2) - 8;
                }

                // Ensure popover doesn't go off the right edge
                const viewportRight = window.scrollX + document.documentElement.clientWidth;
                const rightOverflow = (left + popRect.width) - viewportRight;
                let computedLeft = left;
                if (rightOverflow > 0) {
                    computedLeft = Math.max(window.scrollX + 8, left - rightOverflow - 8);
                }
                if (computedLeft < window.scrollX + 8) {
                    computedLeft = window.scrollX + 8;
                }

                // Keep popover inside viewport vertically
                if (top < window.scrollY + 8) {
                    top = window.scrollY + 8;
                }
                if (top + popRect.height > window.scrollY + document.documentElement.clientHeight - 8) {
                    top = window.scrollY + document.documentElement.clientHeight - popRect.height - 8;
                }

                pop.style.left = `${computedLeft}px`;
                pop.style.top = `${top}px`;
            }

            // Initial positioning
            positionPop();

            // Reposition on scroll/resize and when window repaints
            const reposition = () => requestAnimationFrame(positionPop);
            window.addEventListener('resize', reposition, { passive: true });
            // capture phase to catch scrolling inside containers
            window.addEventListener('scroll', reposition, { passive: true, capture: true });
            document.addEventListener('scroll', reposition, { passive: true, capture: true });

            currentFloatingCleanup = () => {
                window.removeEventListener('resize', reposition, { passive: true });
                window.removeEventListener('scroll', reposition, { passive: true, capture: true });
                document.removeEventListener('scroll', reposition, { passive: true, capture: true });
            };

            currentFloatingBreakdown = pop;
            return;
        }

        // Clicked outside a toggle; close any open floating breakdown
        if (!event.target.closest('.pos-floating-breakdown')) {
            removeFloatingBreakdown();
        }
    });
}

function initializePos() {
    posState.transactionHistory = loadTransactionHistory();
    loadProductImagePreviews();
    setupPosEvents();
    renderCart();
    searchProducts('');
    applyProductImagePreviews();
    updateInvestmentLabels();

    // Start polling for mobile scans
    startScanPolling();

    // Listen for localStorage changes (same-browser sync)
    window.addEventListener('storage', handleStorageChange);
}

// Expose functions globally for onclick handlers
window.viewTransactionInvoice = viewTransactionInvoice;
window.closeInvoiceModal = closeInvoiceModal;
window.deleteTransaction = deleteTransaction;
window.posArchiveProduct = posArchiveProduct;
window.bulkDeleteTransactions = bulkDeleteTransactions;

async function posArchiveProduct(productId) {
    if (!confirm('Are you sure you want to archive this product? It will be hidden from both POS and All Stocks inventory.')) {
        return;
    }

    try {
        const response = await fetch(`/api/product/${productId}/archive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            }
        });
        const result = await response.json();

        if (result.success) {
            alert('Product archived successfully');
            searchProducts(''); // Refresh product grid
        } else {
            alert('Failed to archive product: ' + result.message);
        }
    } catch (error) {
        console.error('Error archiving product:', error);
        alert('Error archiving product');
    }
}

function updateInvestmentLabels() {
    document.querySelectorAll('.pos-payment-summary-method').forEach(el => {
        el.textContent = posState.paymentMethod === 'cash' ? 'Cash' : 'QR PH';
    });
}

// Mobile Scanner Functions
let scanPollingInterval = null;
let lastScanTimestamp = 0;

function openMobileScanner() {
    const modal = document.getElementById('posMobileScannerModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    initMobileScanner();
}

function closeMobileScanner() {
    const modal = document.getElementById('posMobileScannerModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    stopMobileScanner();
}

let mobileHtml5QrcodeScanner = null;
let mobileScannedItems = [];

function initMobileScanner() {
    if (mobileHtml5QrcodeScanner) {
        mobileHtml5QrcodeScanner.stop().catch(err => console.error(err));
    }

    mobileHtml5QrcodeScanner = new Html5Qrcode("posMobileScannerReader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    mobileHtml5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        onMobileScanSuccess,
        onMobileScanFailure
    ).catch(err => {
        console.error("Mobile scanner error:", err);
        const status = document.getElementById('posMobileScannerStatus');
        if (status) {
            status.textContent = 'Camera access denied or not available';
            status.classList.add('text-red-400');
        }
    });
}

function stopMobileScanner() {
    if (mobileHtml5QrcodeScanner) {
        mobileHtml5QrcodeScanner.stop().catch(err => console.error(err));
        mobileHtml5QrcodeScanner = null;
    }
}

function onMobileScanSuccess(decodedText, decodedResult) {
    playMobileBeep();

    const status = document.getElementById('posMobileScannerStatus');
    if (status) {
        status.textContent = 'Scanned: ' + decodedText;
        status.classList.add('text-green-400');
    }

    setTimeout(() => {
        if (status) {
            status.classList.remove('text-green-400');
            status.textContent = 'Position QR code within the frame';
        }
    }, 2000);

    addToMobileRecentScans(decodedText);
    sendMobileScanToTerminal(decodedText);
}

function onMobileScanFailure(error) {
    // Ignore scan failures
}

function playMobileBeep() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    gainNode.gain.value = 0.1;

    oscillator.start();
    oscillator.stop(audioContext.currentTime + 0.1);
}

function addToMobileRecentScans(code) {
    const timestamp = new Date().toLocaleTimeString();
    mobileScannedItems.unshift({ code, timestamp });

    if (mobileScannedItems.length > 10) {
        mobileScannedItems.pop();
    }

    const container = document.getElementById('posMobileScannerRecent');
    if (!container) return;
    container.innerHTML = mobileScannedItems.map(item => `
        <div class="flex items-center justify-between bg-slate-700 rounded-lg px-3 py-2">
            <div>
                <div class="text-xs font-medium text-white">${item.code}</div>
                <div class="text-[10px] text-slate-400">${item.timestamp}</div>
            </div>
            <span class="text-green-400 text-xs">✓</span>
        </div>
    `).join('');
}

function sendMobileScanToTerminal(code) {
    // Add product to cart
    const scanInput = document.getElementById('posScanInput');
    if (scanInput) {
        scanInput.value = code;
        scanProduct();
    }
}

let desktopScanner = null;

function openDesktopScanner() {
    const modal = document.getElementById('posDesktopScannerModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    // Initialize scanner
    if (!desktopScanner) {
        desktopScanner = new Html5Qrcode("posDesktopScannerReader");
    }

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    desktopScanner.start(
        { facingMode: "environment" },
        config,
        onDesktopScanSuccess,
        onDesktopScanFailure
    ).catch(err => {
        console.error("Desktop scanner error:", err);
        document.getElementById('posDesktopScannerStatus').textContent = 'Camera access denied or not available';
        document.getElementById('posDesktopScannerStatus').classList.add('text-red-400');
    });
}

function closeDesktopScanner() {
    const modal = document.getElementById('posDesktopScannerModal');
    modal.style.display = 'none';

    if (desktopScanner) {
        desktopScanner.stop().catch(err => console.error(err));
    }

    document.getElementById('posDesktopScannerStatus').textContent = 'Position QR code within the frame';
    document.getElementById('posDesktopScannerStatus').classList.remove('text-red-400');
}

function onDesktopScanSuccess(decodedText, decodedResult) {
    // Play notification sound
    playScanNotification();

    // Update status
    document.getElementById('posDesktopScannerStatus').textContent = 'Scanned: ' + decodedText;
    document.getElementById('posDesktopScannerStatus').classList.add('text-green-400');

    setTimeout(() => {
        document.getElementById('posDesktopScannerStatus').classList.remove('text-green-400');
        document.getElementById('posDesktopScannerStatus').textContent = 'Position QR code within the frame';
    }, 2000);

    // Handle the scanned code
    handleScannedCode(decodedText);
}

function onDesktopScanFailure(error) {
    // Ignore scan failures, they're normal
}

function startScanPolling() {
    // Poll server for new scans every 2 seconds
    scanPollingInterval = setInterval(async () => {
        try {
            const response = await fetch('/api/pos/check-scan');
            const data = await response.json();

            if (data.success && data.scan) {
                const scanTimestamp = new Date(data.scan.timestamp).getTime();
                if (scanTimestamp > lastScanTimestamp) {
                    lastScanTimestamp = scanTimestamp;
                    handleScannedCode(data.scan.code);
                }
            }
        } catch (error) {
            console.error('Error polling for scans:', error);
        }
    }, 2000);
}

function handleStorageChange(event) {
    if (event.key === 'pos_scan_data') {
        try {
            const scanData = JSON.parse(event.newValue);
            if (scanData.type === 'qr_scan') {
                handleScannedCode(scanData.code);
            }
        } catch (error) {
            console.error('Error parsing scan data:', error);
        }
    }
}

async function handleScannedCode(code) {
    // Play notification sound
    playScanNotification();

    console.log('Scanned code:', code);

    // Try to parse code as JSON (for QR codes with product data)
    let searchCode = code;
    try {
        const parsed = JSON.parse(code);
        console.log('Parsed QR data:', parsed);
        if (parsed.sku) {
            searchCode = parsed.sku;
            console.log('Using SKU from QR:', searchCode);
        } else if (parsed.product_id) {
            searchCode = parsed.product_id.toString();
            console.log('Using product_id from QR:', searchCode);
        }
    } catch (e) {
        console.log('Not JSON, using original code');
        // Not JSON, use original code
    }

    // Try to find product by SKU or QR code
    try {
        const response = await fetch(posState.apiProductsUrl);
        const result = await response.json();

        // Handle different response structures
        let products = [];
        if (Array.isArray(result)) {
            products = result;
        } else if (result.data && Array.isArray(result.data)) {
            products = result.data;
        } else if (result.products && Array.isArray(result.products)) {
            products = result.products;
        }

        console.log('API response:', result);
        console.log('Loaded products:', products.length);
        console.log('Searching for:', searchCode);

        const product = products.find(p =>
            p.sku === searchCode ||
            p.qr_code === searchCode ||
            p.id.toString() === searchCode
        );

        console.log('Found product:', product);

        if (product) {
            addProductToCart(product);
            showNotification(`Added: ${product.name}`);
        } else {
            showNotification(`Product not found: ${searchCode}`, 'error');
        }
    } catch (error) {
        console.error('Error searching for product:', error);
        showNotification('Error searching for product', 'error');
    }
}

function playScanNotification() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    oscillator.frequency.value = 600;
    oscillator.type = 'sine';
    gainNode.gain.value = 0.15;

    oscillator.start();
    oscillator.stop(audioContext.currentTime + 0.15);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-sm font-medium z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

window.addEventListener('DOMContentLoaded', initializePos);
