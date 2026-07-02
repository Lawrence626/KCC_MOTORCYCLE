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
    apiProductsUrl: window.POS?.routes?.apiProducts || '/api/products',
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
    const transaction = {
        invoice,
        date,
        total,
        paymentMethod,
        items,
        createdAt: new Date().toISOString(),
    };
    posState.transactionHistory.unshift(transaction);
    saveTransactionHistory();
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
        row.innerHTML = `
            <td class="px-3 py-4 text-slate-700 font-medium">${transaction.invoice}</td>
            <td class="px-3 py-4 text-slate-700">${formatDateForHistory(transaction.createdAt)}</td>
            <td class="px-3 py-4 text-slate-700">${transaction.paymentMethod === 'cash' ? 'Cash' : 'QR PH'}</td>
            <td class="px-3 py-4 text-center text-slate-700">${transaction.items.length}</td>
            <td class="px-3 py-4 text-right text-slate-900 font-semibold">${formatCurrency(transaction.total)}</td>
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
            <td class="px-3 py-3 text-slate-700 text-sm font-medium">${item.name}</td>
            <td class="px-3 py-3 text-slate-600 text-sm">${item.sku || '-'}</td>
            <td class="px-3 py-3 text-right text-slate-700 text-sm">${formatCurrency(item.unit_price)}</td>
            <td class="px-3 py-3 text-center text-slate-700 text-sm">
                <div class="inline-flex items-center rounded-lg border border-slate-200 overflow-hidden">
                    <button data-action="decrement" data-id="${item.id}" class="px-2 py-1 text-slate-700 hover:bg-slate-100">−</button>
                    <span class="px-3 text-slate-900 text-sm">${item.quantity}</span>
                    <button data-action="increment" data-id="${item.id}" class="px-2 py-1 text-slate-700 hover:bg-slate-100">+</button>
                </div>
            </td>
            <td class="px-3 py-3 text-right text-slate-700 text-sm">${formatCurrency(item.unit_price * item.quantity)}</td>
            <td class="px-3 py-3 text-center text-slate-700 text-sm">
                <button data-action="remove" data-id="${item.id}" class="text-red-600 hover:text-red-800 text-sm font-semibold">Remove</button>
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
            card.className = 'pos-image-upload-card relative rounded-3xl border border-slate-200 bg-slate-50 p-3 shadow-sm flex flex-col justify-between';
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
                <div class="mb-3">
                    <div class="pos-image-preview h-24 w-full overflow-hidden rounded-3xl bg-slate-200 bg-cover bg-center" style="background-image: url('${product.image || ''}')"></div>
                    <input type="file" accept="image/*" class="pos-image-uploader hidden" data-id="${product.id}" />
                    <button type="button" class="pos-image-upload-trigger mt-2 inline-flex items-center rounded-full border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-100">Upload Image</button>
                </div>
                <div class="space-y-1">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 line-clamp-2">${productName}</h3>
                        ${brand ? `<p class="text-[10px] text-slate-600">${brand}</p>` : ''}
                        ${compatibility ? `<p class="text-[9px] text-slate-500 line-clamp-1">${compatibility}</p>` : ''}
                        <p class="text-[11px] text-slate-500 mt-1">Stock: ${stockQty} pcs</p>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-slate-900">${formatCurrency(sellingPrice)}</span>
                            <button type="button" class="pos-price-breakdown-toggle mt-1 flex items-center gap-1 text-[10px] text-slate-500 hover:text-slate-700 transition" data-product-id="${product.id}">
                                <svg class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                Price Breakdown
                            </button>
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
                        </div>
                        <button type="button" data-id="${product.id}" data-name="${productName}" data-sku="${product.sku || ''}" data-price="${product.unit_price || 0}" class="pos-add-card inline-flex h-8 rounded-2xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Add to Cart</button>
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
    prevButton.className = 'inline-flex items-center rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed';
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
            pageButton.className = 'inline-flex items-center justify-center rounded-full bg-teal-500 text-white w-8 h-8 text-sm font-semibold';
        } else {
            pageButton.className = 'inline-flex items-center justify-center rounded-full border border-slate-300 bg-white text-slate-700 w-8 h-8 text-sm font-semibold hover:bg-slate-50';
        }
        pageButton.textContent = page;
        pageButton.onclick = () => searchProducts(posState.productSearchQuery, page);
        paginationContainer.appendChild(pageButton);
    }

    const nextButton = document.createElement('button');
    nextButton.type = 'button';
    nextButton.className = 'inline-flex items-center rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed';
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
            addProductToCart(json.data[0]);
            scanInput.value = '';
            if (scanFeedback) scanFeedback.textContent = `Added ${json.data[0].name} to cart.`;
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
        items: posState.cart.map(item => ({ id: item.id, name: item.name, qty: item.quantity, price: item.unit_price })),
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
    if (invoiceAmountReceived) invoiceAmountReceived.textContent = formatCurrency(receiptData.total);
    if (invoiceChange) invoiceChange.textContent = formatCurrency(0);
    if (invoicePaymentMethod) invoicePaymentMethod.textContent = receiptData.paymentMethod === 'qr' ? 'QR PH' : 'Cash';
}

function printReceipt() {
    const printStyles = `
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: white; }
            .receipt-container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
            .receipt-header { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
            .header-left { display: flex; gap: 12px; align-items: flex-start; }
            .header-left-logo { width: 60px; height: 60px; background: #e8f5e9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #2d6a4f; font-size: 12px; }
            .header-left-text h2 { font-size: 14px; font-weight: bold; margin-bottom: 4px; }
            .header-left-text p { font-size: 11px; color: #666; line-height: 1.5; }
            .header-right { text-align: right; }
            .header-right p { font-size: 12px; margin-bottom: 4px; }
            .header-right .label { color: #666; font-weight: 600; }
            .header-right .value { font-weight: bold; }

            .items-section { margin: 30px 0; }
            .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .items-table thead { background: #f5f5f5; }
            .items-table th { padding: 10px; text-align: left; font-weight: 600; font-size: 11px; border-bottom: 2px solid #000; text-transform: uppercase; letter-spacing: 0.5px; }
            .items-table td { padding: 12px 10px; border-bottom: 1px solid #ddd; font-size: 12px; }
            .items-table .text-right { text-align: right; }

            .summary-section { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 30px; }
            .summary-left { }
            .summary-right { text-align: right; }
            .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 12px; }
            .summary-row .label { color: #666; }
            .summary-row.total { font-weight: bold; font-size: 14px; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 0; margin: 12px 0; }
            .summary-row.amount-received .label { font-weight: 600; }
            .summary-row.change .value { color: #2d6a4f; font-weight: bold; }

            @media print {
                body { margin: 0; padding: 0; }
                .receipt-container { max-width: 100%; padding: 20px; }
            }
        </style>
    `;

    const invoiceNum = document.getElementById('receiptInvoice')?.textContent || 'INV-000000';
    const receiptDateText = document.getElementById('receiptDate')?.textContent || new Date().toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    const items = posState.cart.map(item => ({
        name: item.name,
        qty: item.quantity,
        price: formatCurrency(item.unit_price),
        total: formatCurrency(item.unit_price * item.quantity)
    }));

    const subtotal = document.getElementById('posPaymentSubtotal')?.textContent || '₱0.00';
    const services = document.getElementById('receiptServices')?.textContent || '₱0.00';
    const extra = document.getElementById('receiptExtra')?.textContent || '₱0.00';
    const discount = document.getElementById('receiptDiscount')?.textContent || '₱0.00';
    const tax = document.getElementById('receiptTax')?.textContent || '₱0.00';
    const total = document.getElementById('receiptTotal')?.textContent || '₱0.00';
    
    // Calculate VATable Sales from total and included VAT
    const totalValue = parseFloat(total.replace('₱', '').replace(',', '')) || 0;
    const taxValue = parseFloat(tax.replace('₱', '').replace(',', '')) || 0;
    const vatableSales = totalValue - taxValue;
    
    const paid = document.getElementById('receiptPaid')?.textContent || '₱0.00';
    const paymentMethod = document.getElementById('receiptPaymentMethod')?.textContent || 'Cash';

    const itemsHTML = items.map(item => `
        <tr>
            <td>${item.name}</td>
            <td style="text-align: center;">${item.qty}</td>
            <td class="text-right">${item.price}</td>
            <td class="text-right">${item.total}</td>
        </tr>
    `).join('');

    const receiptHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Receipt</title>
            ${printStyles}
        </head>
        <body>
            <div class="receipt-container">
                <div class="receipt-header">
                    <div class="header-left">
                        <div class="header-left-logo">KCC</div>
                        <div class="header-left-text">
                            <h2>MOTORCYCLE PARTS<br/>AND ACCESSORIES</h2>
                            <p>129 Motorcycle St., Barangay 123<br/>City, Philippines<br/>Tel: (02) 1234-56578</p>
                        </div>
                    </div>
                    <div class="header-right">
                        <p><span class="label">Invoice #:</span> <span class="value">${invoiceNum}</span></p>
                        <p><span class="label">Date:</span> <span class="value">${receiptDateText}</span></p>
                        <p><span class="label">Cashier:</span> <span class="value">Admin</span></p>
                    </div>
                </div>

                <div class="items-section">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="text-align: center;">Qty</th>
                                <th class="text-right">Price</th>
                                <th class="text-right" style="width: 15%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHTML}
                        </tbody>
                    </table>
                </div>

                <div class="summary-section">
                    <div class="summary-left">
                        <div class="summary-row">
                            <span class="label">Subtotal</span>
                            <span class="value">${subtotal}</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Discount</span>
                            <span class="value">${discount}</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Included VAT (12%)</span>
                            <span class="value">${tax}</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">VATable Sales</span>
                            <span class="value">${formatCurrency(vatableSales)}</span>
                        </div>
                        <div class="summary-row total">
                            <span class="label">TOTAL</span>
                            <span class="value">${total}</span>
                        </div>
                    </div>
                    <div class="summary-right">
                        <div class="summary-row amount-received">
                            <span class="label">Amount Received</span>
                            <span class="value">${paid}</span>
                        </div>
                        <div class="summary-row change">
                            <span class="label">Change</span>
                            <span class="value">₱0.00</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Payment Method</span>
                            <span class="value">${paymentMethod}</span>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
    `;

    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    printWindow.document.write(receiptHTML);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
    }, 250);
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
    
    const items = posState.cart.map(item => ({ id: item.id, name: item.name, qty: item.quantity, price: item.unit_price }));

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

    document.getElementById('posQRPaymentCompleteButton')?.addEventListener('click', completeQRPayment);
    document.getElementById('posCloseQRPaymentButton')?.addEventListener('click', closeQRPaymentModal);

    document.getElementById('posCloseReceiptButton')?.addEventListener('click', hideReceiptOverlay);
    document.getElementById('posCloseReceiptDoneButton')?.addEventListener('click', hideReceiptOverlay);
    document.getElementById('posPrintReceiptButton')?.addEventListener('click', printReceipt);
    document.getElementById('posViewInvoiceButton')?.addEventListener('click', viewInvoice);

    // Mobile Scanner
    document.getElementById('posOpenScannerButton')?.addEventListener('click', openMobileScanner);
    
    // Desktop QR Scanner
    document.getElementById('posOpenDesktopScannerButton')?.addEventListener('click', openDesktopScanner);
    document.getElementById('posCloseDesktopScannerButton')?.addEventListener('click', closeDesktopScanner);

    // Price Breakdown Toggle (Event Delegation)
    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('.pos-price-breakdown-toggle');
        if (toggle) {
            const productId = toggle.dataset.productId;
            togglePriceBreakdown(productId);
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

function updateInvestmentLabels() {
    document.querySelectorAll('.pos-payment-summary-method').forEach(el => {
        el.textContent = posState.paymentMethod === 'cash' ? 'Cash' : 'QR PH';
    });
}

// Mobile Scanner Functions
let scanPollingInterval = null;
let lastScanTimestamp = 0;

function openMobileScanner() {
    const scannerUrl = window.POS?.routes?.mobileScanner || '/pos/mobile-scanner';
    window.open(scannerUrl, '_blank');
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
