const posState = {
    cart: [],
    services: [
        { id: 'installation', label: 'Parts Installation', price: 120.00 },
        { id: 'tuneup', label: 'Engine Tune-up', price: 250.00 },
        { id: 'brake_adjust', label: 'Brake Adjustment', price: 180.00 },
    ],
    selectedServices: new Set(),
    expandedCartItems: new Set(),
    extraCharge: 0,
    discount: 0,
    hasAutoDiscount: false,
    paymentMethod: 'cash',
    productImages: {},
    lastReceipt: null,
    transactionHistory: [],
    transactionHistoryPage: 1,
    transactionHistoryPageSize: 10,
    apiProductsUrl: window.POS?.routes?.apiProducts || '/api/shop-inventory/products',
    productPage: 1,
    productPageSize: 9,
    productTotalCount: 0,
    productSearchQuery: '',
    selectedCategory: 'All',
    selectedBrand: 'All',
    categories: ['All', 'Exhaust', 'Tires', 'Brakes', 'Oils', 'Accessories'],
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

async function loadPOSCategories() {
    try {
        const response = await fetch('/product-descriptions');
        const descriptions = await response.json();

        // Use all product descriptions as categories
        posState.categories = ['All', ...descriptions.map(desc => desc.name)];

        // Store descriptions for filtering
        posState.productDescriptions = descriptions;

        // Render category dropdown
        renderCategoryDropdown();
        // Initialize brand dropdown
        updateBrandDropdown();
    } catch (error) {
        console.error('Failed to load POS categories:', error);
        // Use default categories if API fails
        posState.categories = ['All'];
        renderCategoryDropdown();
        updateBrandDropdown();
    }
}

function renderCategoryDropdown() {
    const select = document.getElementById('posCategorySelect');
    if (!select) return;

    select.innerHTML = posState.categories.map(category => {
        const isSelected = category === posState.selectedCategory;
        const label = (category === 'All' || category === 'all') ? 'All Categories' : category;
        return `<option value="${category}" ${isSelected ? 'selected' : ''}>${label}</option>`;
    }).join('');

    // Add event listener
    select.addEventListener('change', () => {
        posState.selectedCategory = select.value;
        posState.selectedBrand = 'All'; // Reset brand when category changes
        updateBrandDropdown();
        searchProducts(posState.productSearchQuery, 1);
    });
}

function updateBrandDropdown() {
    const select = document.getElementById('posBrandSelect');
    if (!select) return;

    let brands = ['All'];

    if (posState.selectedCategory !== 'All' && posState.productDescriptions) {
        // Show brands for selected product description
        const selectedDesc = posState.productDescriptions.find(desc => desc.name === posState.selectedCategory);
        if (selectedDesc && selectedDesc.brands) {
            brands = ['All', ...selectedDesc.brands];
        }
    } else if (posState.productDescriptions) {
        // Show all unique brands from all product descriptions when "All" is selected
        const allBrands = new Set();
        posState.productDescriptions.forEach(desc => {
            if (desc.brands) {
                desc.brands.forEach(brand => allBrands.add(brand));
            }
        });
        brands = ['All', ...Array.from(allBrands).sort()];
    }

    select.innerHTML = brands.map(brand => {
        const isSelected = brand === posState.selectedBrand;
        const label = (brand === 'All' || brand === 'all') ? 'All Brands' : brand;
        return `<option value="${brand}" ${isSelected ? 'selected' : ''}>${label}</option>`;
    }).join('');

    // Add event listener
    select.addEventListener('change', () => {
        posState.selectedBrand = select.value;
        searchProducts(posState.productSearchQuery, 1);
    });
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
            product_description: cartItem?.product_description || '',
            brand: cartItem?.brand || '',
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
        tax: total * (12 / 112),
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

function recalculateAutoDiscount() {
    let autoDiscount = 0;
    let hasDeadStockDiscount = false;

    posState.cart.forEach(item => {
        const dType = item.discount_type;
        const dVal = Number(item.discount_value || 0);
        if (dVal > 0) {
            hasDeadStockDiscount = true;
            if (dType === 'percentage') {
                autoDiscount += (item.unit_price * (dVal / 100)) * item.quantity;
            } else if (dType === 'fixed') {
                autoDiscount += dVal * item.quantity;
            }
        }
    });

    const discountInput = document.getElementById('posDiscountInput');
    const removeBtn = document.getElementById('posRemoveDiscountBtn');
    if (removeBtn) {
        if (hasDeadStockDiscount) {
            removeBtn.classList.remove('hidden');
        } else {
            removeBtn.classList.add('hidden');
        }
    }

    if (hasDeadStockDiscount) {
        if (discountInput) {
            discountInput.value = autoDiscount.toFixed(2);
        }
        posState.discount = autoDiscount;
        posState.hasAutoDiscount = true;
    } else if (posState.hasAutoDiscount) {
        if (discountInput) {
            discountInput.value = '0.00';
        }
        posState.discount = 0;
        posState.hasAutoDiscount = false;
    }
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

function getProductDefaultImage(product) {
    if (!product) return '';
    const brand = String(product.brand || '').trim().toUpperCase();
    const desc = String(product.product_description || product.category || '').trim().toUpperCase();
    const name = String(product.product_name || product.name || '').trim().toUpperCase();
    const compat = String(product.compatibility || '').trim().toUpperCase();
    const sku = String(product.sku || '').trim().toUpperCase();

    // Check for Apido Brand / Pipe
    const isApido = brand.includes('APIDO') || name.includes('APIDO') || sku.includes('APIDO');
    const isPipe = desc.includes('PIPE') || name.includes('PIPE') || compat.includes('PIPE') || sku.includes('PIPE') ||
                   desc.includes('EXHAUST') || name.includes('EXHAUST') || compat.includes('EXHAUST') || sku.includes('EXHAUST') ||
                   desc.includes('MUFFLER') || name.includes('MUFFLER') || compat.includes('MUFFLER') || sku.includes('MUFFLER');

    if (isApido || (isPipe && brand.includes('APIDO'))) {
        const apidoImages = [
            '/images/products/apido_pipe_1.png',
            '/images/products/apido_pipe_2.png',
            '/images/products/apido_pipe_3.png'
        ];
        // Hash product identity to consistently distribute varied images across different products
        const seedStr = String(product.id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return apidoImages[Math.abs(hash) % apidoImages.length];
    }

    // Check for KVIN Brand / Pipe
    const isKvin = brand.includes('KVIN') || brand.includes('K-VIN') || brand.includes('K VIN') ||
                   name.includes('KVIN') || name.includes('K-VIN') || name.includes('K VIN') ||
                   sku.includes('KVIN') || sku.includes('K-VIN');

    if (isKvin) {
        const kvinImages = [
            '/images/products/kvin_pipe_1.png',
            '/images/products/kvin_pipe_2.png'
        ];
        // Hash product identity to consistently distribute varied images across different products
        const seedStr = String(product.id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return kvinImages[Math.abs(hash) % kvinImages.length];
    }

    // Check for TRC Brand / Pipe
    const isTrc = brand === 'TRC' || brand.includes('TRC') || name.includes('TRC') || sku.includes('TRC');

    if (isTrc) {
        const trcImages = [
            '/images/products/trc_pipe_1.png',
            '/images/products/trc_pipe_2.png',
            '/images/products/trc_pipe_3.png'
        ];
        // Hash product identity to consistently distribute varied images across different products
        const seedStr = String(product.id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return trcImages[Math.abs(hash) % trcImages.length];
    }

    // Check for MT8 Brand / Pipe
    const isMt8 = brand === 'MT8' || brand.includes('MT8') || brand.includes('MT-8') || brand.includes('MT 8') ||
                  name.includes('MT8') || name.includes('MT-8') || name.includes('MT 8') ||
                  sku.includes('MT8') || sku.includes('MT-8');

    if (isMt8) {
        const mt8Images = [
            '/images/products/mt8_pipe_1.png',
            '/images/products/mt8_pipe_2.png',
            '/images/products/mt8_pipe_3.png'
        ];
        // Hash product identity to consistently distribute varied images across different products
        const seedStr = String(product.id || '') + String(product.name || product.product_name || product.sku || '');
        let hash = 0;
        for (let i = 0; i < seedStr.length; i++) {
            hash = (hash * 31 + seedStr.charCodeAt(i)) | 0;
        }
        return mt8Images[Math.abs(hash) % mt8Images.length];
    }

    return '';
}

function saveProductImagePreview(cardId, dataUrl) {
    // Store image ONLY by product ID to prevent cross-product image conflicts.
    // Do NOT store by SKU or name since multiple products can share the same values.
    const key = String(cardId);
    try {
        posState.productImages[key] = dataUrl;
        const serialized = JSON.stringify(posState.productImages);
        const sizeInMB = new Blob([serialized]).size / (1024 * 1024);
        console.log(`Saving ${Object.keys(posState.productImages).length} images, total size: ${sizeInMB.toFixed(2)}MB`);

        localStorage.setItem('posProductImages', JSON.stringify(posState.productImages));
        console.log('✓ Saved product image for card:', key);
    } catch (error) {
        console.error('Failed to save product image preview:', error.message);
        if (error.name === 'QuotaExceededError') {
            console.warn('localStorage quota exceeded. Clearing old images and retrying...');
            posState.productImages = {};
            try {
                localStorage.setItem('posProductImages', JSON.stringify({}));
                posState.productImages[key] = dataUrl;
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
        // One-time migration: clear all old uploaded images to fix cross-product image conflicts.
        // Remove this block once the migration has run (after first page load).
        if (!localStorage.getItem('posProductImages_v2')) {
            localStorage.removeItem('posProductImages');
            localStorage.setItem('posProductImages_v2', '1');
            posState.productImages = {};
            console.log('🔄 Cleared all old product images (one-time migration)');
            return;
        }

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
        const placeholder = card.querySelector('.pos-image-placeholder');
        if (preview && dataUrl) {
            preview.style.backgroundImage = `url('${dataUrl}')`;
            preview.classList.remove('hidden');
        }
        if (placeholder && dataUrl) {
            placeholder.classList.add('hidden');
        }
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
    const pageNumbersContainer = document.getElementById('posHistoryPageNumbers');
    if (!body || !empty || !pagination || !pageInfo || !prevButton || !nextButton) return;

    const fromDate = document.getElementById('posHistoryFilterFrom')?.value;
    const toDate = document.getElementById('posHistoryFilterTo')?.value;
    const transactions = filterTransactionHistory(fromDate, toDate);
    const pageSize = posState.transactionHistoryPageSize || 10;
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
            <td class="px-4 py-4 text-right text-slate-900 font-semibold whitespace-nowrap">${formatCurrency(transaction.total)}</td>
            <td class="pl-8 pr-4 py-4 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-3">
                    <button onclick="viewTransactionInvoice('${transaction.invoice}')" class="font-semibold text-xs text-black hover:underline hover:decoration-black transition cursor-pointer">View</button>
                    <button onclick="deleteTransaction('${transaction.invoice}')" class="p-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition cursor-pointer inline-flex items-center justify-center" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </td>
        `;
        body.appendChild(row);
    });

    const startDisplay = startIndex + 1;
    const endDisplay = Math.min(endIndex, transactions.length);
    pageInfo.textContent = `Showing ${startDisplay}-${endDisplay} of ${transactions.length}`;
    prevButton.disabled = posState.transactionHistoryPage <= 1;
    nextButton.disabled = posState.transactionHistoryPage >= totalPages;

    if (pageNumbersContainer) {
        let numsHtml = '';
        const maxVisiblePages = 5;
        let startPage = Math.max(1, posState.transactionHistoryPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        for (let page = startPage; page <= endPage; page++) {
            if (page === posState.transactionHistoryPage) {
                numsHtml += `<button type="button" disabled class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${page}</button>`;
            } else {
                numsHtml += `<button type="button" onclick="goToTransactionHistoryPage(${page})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">${page}</button>`;
            }
        }
        pageNumbersContainer.innerHTML = numsHtml;
    }
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
        const maxStock = Number(item.stock_quantity ?? 0);
        const isAtMaxStock = item.quantity >= maxStock;
        const isAtMin = item.quantity <= 1;
        const isExceeding = item.quantity > maxStock;
        const isOutOfStock = maxStock <= 0;
        const isExpanded = posState.expandedCartItems.has(item.id);

        let stockInfoHtml = '';
        if (isOutOfStock) {
            stockInfoHtml = `<div class="text-[10px] text-red-600 font-semibold mt-0.5"><span class="font-semibold text-black">STOCK:</span> 0 pcs <span class="font-bold text-red-700">(Out of Stock)</span></div>`;
        } else if (isExceeding) {
            stockInfoHtml = `<div class="text-[10px] text-red-600 font-semibold mt-0.5"><span class="font-semibold text-black">STOCK:</span> ${maxStock} pcs <span class="font-bold text-red-700">(Exceeds stock by ${item.quantity - maxStock}!)</span></div>`;
        } else {
            stockInfoHtml = `<div class="text-[10px] text-slate-700 mt-0.5"><span class="font-semibold text-black">STOCK:</span> ${maxStock} pcs</div>`;
        }

        let discountBadge = '';
        const itemDiscountVal = Number(item.discount_value || 0);
        if (itemDiscountVal > 0) {
            const discountLabel = item.discount_type === 'percentage'
                ? `${itemDiscountVal}% OFF`
                : `₱${itemDiscountVal.toFixed(2)} OFF`;
            discountBadge = `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300 ml-1.5" title="Dead stock discount applied">🏷️ ${discountLabel}</span>`;
        }

        const productDisplayName = item.name || item.product_name || 'Product';
        const compactProductName = productDisplayName.length > 18 ? `${productDisplayName.slice(0, 18)}...` : productDisplayName;
        const compactSkuText = item.sku ? `SKU: ${item.sku}` : 'SKU: N/A';

        const row = document.createElement('tr');
        row.className = `border-b border-slate-200 ${isExceeding || isOutOfStock ? 'bg-red-50/50' : ''}`;
        row.innerHTML = `
            <td colspan="5" class="px-2 py-2 align-top">
                <div class="pos-cart-item-panel rounded-[12px] border ${isExceeding || isOutOfStock ? 'border-red-200 bg-red-50/40' : 'border-slate-200 bg-white'} shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[12px] font-bold leading-snug text-slate-900">${compactProductName}</span>
                                ${discountBadge}
                            </div>
                            <div class="mt-0.5 text-[9.5px] text-slate-500">${compactSkuText}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[11px] font-semibold text-slate-800">${formatCurrency(item.unit_price * item.quantity)}</span>
                            <button type="button" data-action="toggle-details" data-id="${item.id}" class="flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-600 transition hover:bg-slate-100" aria-label="Toggle item details" aria-expanded="${isExpanded ? 'true' : 'false'}">
                                <svg class="h-3.5 w-3.5 transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M5 7.5L10 12.5L15 7.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="pos-cart-item-details ${isExpanded ? '' : 'hidden'} border-t border-slate-200 bg-slate-50/60 px-3 py-2.5 text-[10px] text-slate-700">
                        ${item.product_description ? `<div class="leading-[1.5]"><span class="font-semibold text-black">PRODUCT DESCRIPTION:</span> ${item.product_description}</div>` : ''}
                        ${item.brand ? `<div class="mt-1 leading-[1.5]"><span class="font-semibold text-black">BRAND:</span> ${item.brand}</div>` : ''}
                        ${item.compatibility ? `<div class="mt-1 leading-[1.5]"><span class="font-semibold text-black">COMPATIBLE:</span> ${item.compatibility}</div>` : ''}
                        ${stockInfoHtml}

                        <div class="mt-2 flex items-center justify-between gap-3">
                            <div class="inline-flex items-center rounded-lg border ${isExceeding || isOutOfStock ? 'border-red-400 ring-1 ring-red-400 bg-white' : 'border-slate-200'} overflow-hidden">
                                <button data-action="decrement" data-id="${item.id}" ${isAtMin ? 'disabled' : ''} class="px-2 py-1 text-slate-700 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent" title="${isAtMin ? 'Minimum quantity is 1' : 'Decrease quantity'}">−</button>
                                <span class="px-3 text-slate-900 text-[10px] font-semibold ${isExceeding || isOutOfStock ? 'text-red-600' : ''}">${item.quantity}</span>
                                <button data-action="increment" data-id="${item.id}" ${isAtMaxStock ? 'disabled' : ''} class="px-2 py-1 text-slate-700 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent" title="${isAtMaxStock ? `Maximum stock reached (${maxStock} available)` : 'Increase quantity'}">+</button>
                            </div>
                            <button data-action="remove" data-id="${item.id}" class="text-red-600 hover:text-red-800 text-[10px] font-semibold">Remove</button>
                        </div>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    recalculateAutoDiscount();
    updateTotals();
}

function goToCart() {
    const cartSection = document.getElementById('posCartTable');
    if (!cartSection) return;
    cartSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function addProductToCart(product) {
    if (!product || !product.id) return false;

    let availableStock = Number(product.stock_quantity ?? product.stock ?? 0);
    if (isNaN(availableStock)) availableStock = 0;

    const productName = product.name || product.product_name || 'Product';

    if (availableStock <= 0) {
        showNotification(`Cannot add item. ${productName} is out of stock.`, 'error');
        return false;
    }

    const existing = findCartItem(product.id);
    if (existing) {
        existing.stock_quantity = availableStock;
        if (product.discount_type !== undefined) existing.discount_type = product.discount_type;
        if (product.discount_value !== undefined) existing.discount_value = Number(product.discount_value || 0);
        if (existing.quantity + 1 > availableStock) {
            showNotification(`Insufficient stock. Only ${availableStock} item(s) available.`, 'error');
            return false;
        }
        existing.quantity += 1;
        showNotification(`Added ${existing.name} (Qty: ${existing.quantity})`, 'success');
    } else {
        if (1 > availableStock) {
            showNotification(`Insufficient stock. Only ${availableStock} item(s) available.`, 'error');
            return false;
        }
        posState.cart.push({
            id: product.id,
            name: productName,
            sku: product.sku || '',
            product_description: (product.product_description || product.category) ?? 'Uncategorized',
            brand: product.brand || '',
            compatibility: product.compatibility || '',
            category: product.category ?? 'Uncategorized',
            stock_quantity: availableStock,
            unit_price: Number(product.unit_price || 0),
            discount_type: product.discount_type || null,
            discount_value: Number(product.discount_value || 0),
            quantity: 1,
        });
        showNotification(`Added ${productName} to cart`, 'success');
    }

    renderCart();
    goToCart();
    return true;
}

function removeCartItem(productId) {
    posState.cart = posState.cart.filter(item => item.id !== productId);
    renderCart();
}

function changeCartQuantity(productId, delta) {
    const item = findCartItem(productId);
    if (!item) return;

    const maxStock = Number(item.stock_quantity ?? 0);

    if (delta > 0) {
        if (item.quantity + delta > maxStock) {
            showNotification(`Insufficient stock. Only ${maxStock} item(s) available.`, 'error');
            return;
        }
        item.quantity += delta;
    } else if (delta < 0) {
        if (item.quantity <= 1) {
            return;
        }
        item.quantity = Math.max(1, item.quantity + delta);
    }

    renderCart();
}

function clearCart() {
    posState.cart = [];
    posState.discount = 0;
    posState.hasAutoDiscount = false;
    const discountInput = document.getElementById('posDiscountInput');
    if (discountInput) discountInput.value = '0.00';
    const removeBtn = document.getElementById('posRemoveDiscountBtn');
    if (removeBtn) removeBtn.classList.add('hidden');
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

        // Add product_description filter if not "All"
        if (posState.selectedCategory && posState.selectedCategory !== 'All') {
            url.searchParams.set('product_name', posState.selectedCategory);
        }

        // Add brand filter if not "All"
        if (posState.selectedBrand && posState.selectedBrand !== 'All') {
            url.searchParams.set('brand', posState.selectedBrand);
        }

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
            const stockQty = Number(product.stock_quantity ?? product.stock ?? 0);
            const isOutOfStock = stockQty <= 0;
            const productName = product.product_name || product.name || 'Unnamed Product';
            const brand = product.brand ? `${product.brand}` : '';
            const compatibility = product.name && product.name !== productName ? product.name : (product.compatibility || '');

            card.dataset.productId = product.id;
            card.dataset.sku = product.sku || '';
            card.dataset.name = productName;

            // Calculate VAT breakdown
            const sellingPrice = Number(product.unit_price || 0);
            const includedVat = sellingPrice * (12 / 112);
            const vatableSales = sellingPrice - includedVat;

            // Retrieve image: custom uploaded by product ID -> product DB image -> brand default image -> empty
            const defaultImage = getProductDefaultImage(product);
            const cardImage = posState.productImages[String(product.id)] || product.image || defaultImage || '';

            card.innerHTML = `
                <div class="flex-shrink-0">
                    <div class="pos-image-container relative h-24 w-full overflow-hidden rounded-[10px] bg-slate-100/90 border border-slate-200/80 flex items-center justify-center">
                        <div class="pos-image-preview absolute inset-0 bg-cover bg-center ${cardImage ? '' : 'hidden'}" style="${cardImage ? `background-image: url('${cardImage}');` : ''}"></div>
                        <div class="pos-image-placeholder flex items-center justify-center text-slate-300 ${cardImage ? 'hidden' : ''}">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <input type="file" accept="image/*" class="pos-image-uploader hidden" data-id="${product.id}" />
                    <div class="mt-2 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900 line-clamp-2">${productName}</h3>
                        <button type="button" aria-label="Upload image" title="Upload image" class="pos-image-upload-trigger ml-2 flex-shrink-0 inline-flex h-8 w-8 items-center justify-center rounded-[10px] border border-slate-200 bg-white text-slate-700 hover:bg-slate-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 flex flex-col justify-start pt-1 pb-2 space-y-0.5">
                    ${product.product_description ? `<p class="text-[9px] text-slate-700"><span class="font-semibold text-black">PRODUCT DESCRIPTION:</span> ${product.product_description}</p>` : ''}
                    ${brand ? `<p class="text-[9px] text-slate-700"><span class="font-semibold text-black">BRAND:</span> ${brand}</p>` : ''}
                    ${compatibility ? `<p class="text-[9px] text-slate-700"><span class="font-semibold text-black">COMPATIBLE:</span> ${compatibility}</p>` : ''}
                    ${product.sku ? `<p class="text-[9px] text-slate-700"><span class="font-semibold text-black">SKU:</span> ${product.sku}</p>` : ''}
                    ${isOutOfStock
                    ? `<p class="text-[9px] text-red-600 font-semibold"><span class="font-semibold text-black">STOCK:</span> 0 pcs <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold bg-red-100 text-red-700">Out of Stock</span></p>`
                    : `<p class="text-[9px] text-slate-700"><span class="font-semibold text-black">STOCK:</span> ${stockQty} pcs</p>`
                }
                </div>
                <div class="flex-shrink-0 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-900">${formatCurrency(sellingPrice)}</span>
                            ${Number(product.discount_value || 0) > 0 ? `<span class="pos-discount-badge inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200" title="Dead stock discount">🏷️ ${product.discount_type === 'percentage' ? `${product.discount_value}% OFF` : `₱${Number(product.discount_value).toFixed(2)} OFF`}</span>` : ''}
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
                        ${isOutOfStock
                    ? `<button type="button" disabled class="pos-add-card mt-3 inline-flex h-8 items-center justify-center rounded-[10px] bg-slate-200 px-4 text-xs font-bold text-slate-400 cursor-not-allowed shadow-none tracking-wide">Out of Stock</button>`
                    : `<button type="button" data-id="${product.id}" data-name="${productName}" data-sku="${product.sku || ''}" data-price="${product.unit_price || 0}" data-stock="${stockQty}" data-product-description="${product.product_description || product.category || ''}" data-brand="${brand}" data-compatibility="${compatibility}" data-category="${product.category || ''}" data-discount-type="${product.discount_type || ''}" data-discount-value="${product.discount_value || 0}" class="pos-add-card mt-3 inline-flex h-8 items-center justify-center rounded-[10px] bg-[#6EC1D1] px-4 text-xs font-bold text-black shadow-sm hover:bg-[#59b2c2] transition-all duration-200 tracking-wide">Add to Cart</button>`
                }
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
            pageButton.className = 'inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-sm font-semibold';
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
        if (scanFeedback) {
            scanFeedback.textContent = 'Enter a QR code value or SKU to scan.';
            scanFeedback.className = 'text-xs text-slate-500 mt-1';
        }
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
            const stock = Number(product.stock_quantity ?? product.stock ?? 0);
            const pName = product.product_name || product.name || 'Product';

            if (stock <= 0) {
                if (scanFeedback) {
                    scanFeedback.textContent = `Product "${pName}" is out of stock (0 pcs).`;
                    scanFeedback.className = 'text-xs text-red-600 font-semibold mt-1';
                }
                showNotification(`Cannot add item. ${pName} is out of stock.`, 'error');
                return;
            }

            const added = addProductToCart({
                id: product.id,
                name: pName,
                sku: product.sku || '',
                product_description: product.product_description || product.category || '',
                brand: product.brand || '',
                compatibility: product.compatibility || '',
                category: product.category || 'Uncategorized',
                stock_quantity: stock,
                unit_price: Number(product.unit_price || 0),
                discount_type: product.discount_type || null,
                discount_value: Number(product.discount_value || 0)
            });

            if (added) {
                scanInput.value = '';
                if (scanFeedback) {
                    scanFeedback.textContent = `Added ${pName} to cart.`;
                    scanFeedback.className = 'text-xs text-green-600 font-semibold mt-1';
                }
            } else if (scanFeedback) {
                scanFeedback.textContent = `Could not add ${pName} due to stock limit.`;
                scanFeedback.className = 'text-xs text-red-600 font-semibold mt-1';
            }
        } else if (scanFeedback) {
            scanFeedback.textContent = 'Product not found. Please try another barcode or SKU.';
            scanFeedback.className = 'text-xs text-red-600 font-semibold mt-1';
        }
    } catch (error) {
        console.error('Scan failed', error);
        if (scanFeedback) {
            scanFeedback.textContent = 'Scan failed. Please try again.';
            scanFeedback.className = 'text-xs text-red-600 font-semibold mt-1';
        }
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
    posState.hasAutoDiscount = false;
    updateTotals();
}

async function removeAppliedDiscount() {
    const discountedItems = posState.cart.filter(item => Number(item.discount_value || 0) > 0);
    if (discountedItems.length === 0) return;

    const productIds = discountedItems.map(item => item.id);
    const removeBtn = document.getElementById('posRemoveDiscountBtn');
    const originalText = removeBtn ? removeBtn.textContent : 'Remove';

    if (removeBtn) {
        removeBtn.disabled = true;
        removeBtn.textContent = 'Removing...';
    }

    try {
        const response = await fetch('/api/pos/remove-product-discount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ product_ids: productIds }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Update cart items in place
            discountedItems.forEach(item => {
                item.discount_type = null;
                item.discount_value = 0;
            });

            // Update product cards in the product grid DOM
            productIds.forEach(id => {
                const card = document.querySelector(`.pos-image-upload-card[data-product-id="${id}"]`);
                if (card) {
                    const badge = card.querySelector('.pos-discount-badge');
                    if (badge) badge.remove();

                    const btn = card.querySelector('.pos-add-card');
                    if (btn) {
                        btn.dataset.discountType = '';
                        btn.dataset.discountValue = '0';
                    }
                }
            });

            posState.discount = 0;
            posState.hasAutoDiscount = false;
            const discountInput = document.getElementById('posDiscountInput');
            if (discountInput) discountInput.value = '0.00';

            renderCart();
            updateTotals();
            showNotification('Discount permanently removed from product.', 'success');
        } else {
            showNotification(data.message || 'Failed to remove discount.', 'error');
        }
    } catch (error) {
        console.error('Error removing discount:', error);
        showNotification('Error connecting to server to remove discount.', 'error');
    } finally {
        if (removeBtn) {
            removeBtn.disabled = false;
            removeBtn.textContent = originalText;
        }
    }
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

async function revalidateCartStock() {
    if (posState.cart.length === 0) {
        return { valid: true, items: [], errors: [] };
    }

    const payload = {
        items: posState.cart.map(item => ({
            id: item.id,
            quantity: item.quantity,
        })),
    };

    try {
        const response = await fetch('/api/pos/validate-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const errData = await response.json().catch(() => ({}));
            throw new Error(errData.message || `Validation failed (${response.status})`);
        }

        const data = await response.json();

        // Update local cart item stock_quantity with latest DB values
        if (data.items && Array.isArray(data.items)) {
            data.items.forEach(serverItem => {
                const cartItem = findCartItem(serverItem.id);
                if (cartItem) {
                    cartItem.stock_quantity = serverItem.current_stock;
                }
            });
            renderCart();
        }

        return data;
    } catch (error) {
        console.error('Error validating cart stock:', error);
        return {
            valid: false,
            errors: [error.message || 'Unable to verify stock with server.'],
            items: [],
        };
    }
}

function updateChangeCalculation() {
    const tenderedInput = document.getElementById('posAmountTenderedInput');
    const changeDisplay = document.getElementById('posChangeDisplayAmount');
    const warningEl = document.getElementById('posInsufficientWarning');
    const warningText = document.getElementById('posInsufficientText');
    const confirmBtn = document.getElementById('posPaymentModalConfirm');
    const cashSection = document.getElementById('posCashPaymentDetails');
    const selectedMethod = document.querySelector('input[name="posPaymentModalMethod"]:checked')?.value || posState.paymentMethod;

    if (!confirmBtn) return;

    const total = posState.currentPaymentTotal ?? 0;

    if (selectedMethod === 'qr') {
        if (cashSection) cashSection.classList.add('hidden');
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return;
    }

    if (cashSection) cashSection.classList.remove('hidden');
    if (!tenderedInput || !changeDisplay) return;

    const rawVal = tenderedInput.value.trim();
    if (rawVal === '') {
        changeDisplay.textContent = '₱0.00';
        if (warningEl) warningEl.classList.add('hidden');
        confirmBtn.disabled = true;
        confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
        return;
    }

    const tendered = parseFloat(rawVal) || 0;
    const diff = tendered - total;

    if (diff < -0.001) {
        const short = Math.abs(diff);
        changeDisplay.textContent = '₱0.00';
        if (warningEl) {
            warningEl.classList.remove('hidden');
            if (warningText) warningText.textContent = `Insufficient amount (Kulang ng ${formatCurrency(short)})`;
        }
        confirmBtn.disabled = true;
        confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        const change = Math.max(0, diff);
        changeDisplay.textContent = formatCurrency(change);
        if (warningEl) warningEl.classList.add('hidden');
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

async function openPaymentModal() {
    // Dismiss any visible "Added to cart" toast notifications
    dismissAllNotifications();

    if (posState.cart.length === 0) {
        alert('The cart is empty. Add items before proceeding to payment.');
        return;
    }

    const proceedBtn = document.getElementById('posProceedPaymentButton');
    const originalText = proceedBtn ? proceedBtn.textContent : '';
    if (proceedBtn) {
        proceedBtn.disabled = true;
        proceedBtn.textContent = 'Verifying stock...';
    }

    try {
        const validation = await revalidateCartStock();
        if (!validation.valid) {
            const errorMsg = validation.errors && validation.errors.length > 0
                ? validation.errors.join('\n')
                : 'One or more items have insufficient stock.';
            alert(`Cannot proceed to payment.\n\n${errorMsg}\n\nPlease adjust the cart quantities to match available stock.`);
            return;
        }
    } finally {
        if (proceedBtn) {
            proceedBtn.disabled = false;
            proceedBtn.textContent = originalText;
        }
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
    posState.currentPaymentTotal = total;

    // Included VAT = Total × (12 / 112)
    const includedVat = total * (12 / 112);

    const now = new Date();
    invoiceLabel.textContent = `INV-${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}-${now.getHours()}${String(now.getMinutes()).padStart(2, '0')}${String(now.getSeconds()).padStart(2, '0')}`;
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

    const tenderedInput = document.getElementById('posAmountTenderedInput');
    if (tenderedInput) {
        tenderedInput.value = '';
    }

    updateChangeCalculation();

    document.getElementById('posPaymentModal')?.classList.remove('hidden');

    setTimeout(() => {
        if (tenderedInput && (document.querySelector('input[name="posPaymentModalMethod"]:checked')?.value || posState.paymentMethod) === 'cash') {
            tenderedInput.focus();
        }
    }, 150);
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
    const receiptChange = document.getElementById('receiptChange');
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
        change: 0,
        paymentMethod: posState.paymentMethod,
    };

    if (!posState.lastReceipt) {
        // VAT-Inclusive: Total = Subtotal + Services + Extra - Discount
        const subtotalWithExtras = receiptData.subtotal + receiptData.servicesTotal + receiptData.extra - receiptData.discount;
        receiptData.total = Math.max(0, subtotalWithExtras);

        // Included VAT = Total × (12 / 112)
        receiptData.tax = receiptData.total * (12 / 112);

        receiptData.amountReceived = receiptData.total;
        receiptData.change = 0;
    }

    if (receiptInvoice) receiptInvoice.textContent = receiptData.invoiceNumber;
    if (receiptDate) receiptDate.textContent = new Date(receiptData.date).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    if (receiptServices) receiptServices.textContent = formatCurrency(receiptData.servicesTotal);
    if (receiptExtra) receiptExtra.textContent = formatCurrency(receiptData.extra);
    if (receiptDiscount) receiptDiscount.textContent = formatCurrency(receiptData.discount);
    if (receiptTax) receiptTax.textContent = formatCurrency(receiptData.tax);
    if (receiptTotal) receiptTotal.textContent = formatCurrency(receiptData.total);
    if (receiptPaid) receiptPaid.textContent = formatCurrency(receiptData.amountReceived ?? receiptData.total);
    if (receiptChange) receiptChange.textContent = formatCurrency(receiptData.change ?? Math.max(0, (receiptData.amountReceived ?? receiptData.total) - receiptData.total));
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
    if (invoiceChange) invoiceChange.textContent = formatCurrency(receiptData.change ?? Math.max(0, (receiptData.amountReceived ?? receiptData.total) - receiptData.total));
    if (invoicePaymentMethod) invoicePaymentMethod.textContent = receiptData.paymentMethod === 'qr' ? 'QR PH' : 'Cash';
}


/* =========================================================================
   PRINT RECEIPT
   Clean, well-designed receipt card layout (inspired by Receiptify-style
   receipts): centered store header, boxed invoice/date, numbered item
   rows, dashed dividers, totals block, payment info, thank-you line, and
   a barcode strip with a small torn-edge accent at the top of the paper.
   Data is read straight from posState.lastReceipt (the snapshot saved at
   the moment payment was confirmed) so the printed receipt always shows
   the correct items/total even after the cart has been cleared.
   Only this visual template changed — all payment/process logic above
   (confirmPayment, populateReceipt, totals math, etc.) is untouched.
   ========================================================================= */
function printReceipt() {
    const printStyles = `
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                background: #eceef1;
                font-family: 'Courier New', Courier, monospace;
                color: #1a1a1a;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 30px 0;
            }
            .receipt-wrapper { width: 100mm; max-width: 100mm; margin: 0 auto; padding: 10px 8px 20px; }

            .receipt-paper {
                position: relative;
                background: #ffffff;
                padding: 26px 20px 22px;
                border-radius: 4px;
                box-shadow: 0 10px 28px rgba(15,23,42,0.16), 0 2px 6px rgba(15,23,42,0.08);
                clip-path: polygon(
                    0% 0%, 4% 1.4%, 8% 0%, 12% 1.4%, 16% 0%, 20% 1.4%, 24% 0%, 28% 1.4%,
                    32% 0%, 36% 1.4%, 40% 0%, 44% 1.4%, 48% 0%, 52% 1.4%, 56% 0%, 60% 1.4%,
                    64% 0%, 68% 1.4%, 72% 0%, 76% 1.4%, 80% 0%, 84% 1.4%, 88% 0%, 92% 1.4%,
                    96% 0%, 100% 1.4%, 100% 100%, 0% 100%
                );
            }

            .receipt-header { text-align: center; margin-bottom: 4px; }
            .receipt-header h1 { font-size: 21px; letter-spacing: 0.34em; font-weight: 700; }
            .receipt-header .receipt-subtitle { font-size: 9px; letter-spacing: 0.26em; color: #8a8f98; margin-top: 6px; text-transform: uppercase; }

            .receipt-order-box { text-align: center; margin: 16px 0 2px; }
            .receipt-order-box .order-line { font-size: 11.5px; font-weight: 700; letter-spacing: 0.03em; }
            .receipt-order-box .order-sub { font-size: 9.5px; color: #8a8f98; margin-top: 3px; letter-spacing: 0.04em; }

            .receipt-divider { border-top: 1.5px dashed #c7cad0; margin: 14px 0; }

            .receipt-meta-line { display: flex; justify-content: space-between; font-size: 10.5px; color: #374151; padding: 2px 0; }
            .receipt-meta-line .label { color: #8a8f98; }

            .receipt-items table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
            .receipt-items thead th { text-align: left; font-size: 9px; letter-spacing: 0.08em; color: #8a8f98; padding-bottom: 8px; text-transform: uppercase; }
            .receipt-items thead th.qty { width: 26px; }
            .receipt-items thead th.amt { text-align: right; }
            .receipt-items tbody td { padding: 5px 0; vertical-align: top; color: #1a1a1a; line-height: 1.4; }
            .receipt-items tbody td.idx { color: #b0b4bb; width: 20px; }
            .receipt-items tbody td.item-name { padding-right: 8px; }
            .receipt-items tbody td.item-name .qty-tag { color: #8a8f98; font-size: 9.5px; }
            .receipt-items tbody td.amt { text-align: right; white-space: nowrap; font-weight: 600; }

            .receipt-summary { font-size: 10.5px; margin-top: 4px; }
            .receipt-summary .row { display: flex; justify-content: space-between; padding: 3px 0; color: #374151; }
            .receipt-summary .row.grand { font-weight: 700; font-size: 13.5px; color: #111827; border-top: 1.5px dashed #c7cad0; margin-top: 8px; padding-top: 10px; }

            .receipt-payment { font-size: 10.5px; color: #374151; margin-top: 12px; }
            .receipt-payment .row { display: flex; justify-content: space-between; padding: 2px 0; }
            .receipt-payment .label { color: #8a8f98; }

            .receipt-thankyou { text-align: center; font-weight: 700; letter-spacing: 0.2em; font-size: 11px; margin: 20px 0 16px; text-transform: uppercase; }

            @page { size: 100mm auto; margin: 5mm; }
            @media print {
                body { background: #fff; display: block; padding: 0; }
                .receipt-wrapper { width: 100mm; margin: 0 auto; padding: 0; }
                .receipt-paper { box-shadow: none; border-radius: 0; }
            }
        </style>
    `;

    // Pull the exact snapshot recorded at the moment payment was confirmed —
    // this is the single source of truth, so it stays correct even after
    // the cart/inputs are reset for the next sale.
    const receiptData = posState.lastReceipt;

    const invoiceNum = receiptData?.invoiceNumber || document.getElementById('receiptInvoice')?.textContent || 'INV-000000';
    const receiptDateText = receiptData?.date
        ? new Date(receiptData.date).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
        : (document.getElementById('receiptDate')?.textContent || new Date().toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' }));

    const items = (receiptData?.items || []).map(item => ({
        name: item.name,
        qty: item.qty,
        total: formatCurrency(item.price * item.qty),
    }));

    const itemCount = items.reduce((sum, item) => sum + item.qty, 0);

    const subtotal = formatCurrency(receiptData?.subtotal || 0);
    const services = formatCurrency(receiptData?.servicesTotal || 0);
    const extra = formatCurrency(receiptData?.extra || 0);
    const discount = formatCurrency(receiptData?.discount || 0);
    const tax = formatCurrency(receiptData?.tax || 0);
    const total = formatCurrency(receiptData?.total || 0);
    const rawPaid = receiptData?.amountReceived != null ? parseFloat(receiptData.amountReceived) : (receiptData?.amount_paid != null ? parseFloat(receiptData.amount_paid) : (receiptData?.total || 0));
    const rawTotal = receiptData?.total != null ? parseFloat(receiptData.total) : 0;
    const rawChange = receiptData?.change != null ? parseFloat(receiptData.change) : (receiptData?.change_amount != null ? parseFloat(receiptData.change_amount) : Math.max(0, rawPaid - rawTotal));

    const paid = formatCurrency(rawPaid);
    const change = formatCurrency(rawChange);
    const paymentMethod = (receiptData?.paymentMethod === 'qr' || receiptData?.payment_method === 'qr') ? 'QR PH' : 'Cash';

    const itemsHTML = items.map((item, index) => `
        <tr>
            <td class="idx">${String(index + 1).padStart(2, '0')}</td>
            <td class="item-name">${item.name} <span class="qty-tag">x${item.qty}</span></td>
            <td class="amt">${item.total}</td>
        </tr>
    `).join('');

    const receiptHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Receipt ${invoiceNum}</title>
            ${printStyles}
        </head>
        <body>
            <div class="receipt-wrapper">
                <div class="receipt-paper">

                    <div class="receipt-header">
                        <h1>KCC</h1>
                        <p class="receipt-subtitle">Motorcycle Parts &amp; Accessories</p>
                    </div>

                    <div class="receipt-order-box">
                        <div class="order-line">INVOICE #${invoiceNum}</div>
                        <div class="order-sub">${receiptDateText}</div>
                    </div>

                    <div class="receipt-divider"></div>

                    <div class="receipt-meta-line"><span class="label">Cashier</span><span>Administrator</span></div>

                    <div class="receipt-divider"></div>

                    <div class="receipt-items">
                        <table>
                            <thead>
                                <tr>
                                    <th class="qty">#</th>
                                    <th>Item</th>
                                    <th class="amt">Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHTML || '<tr><td colspan="3" style="color:#9ca3af;padding:8px 0;">No items</td></tr>'}
                            </tbody>
                        </table>
                    </div>

                    <div class="receipt-divider"></div>

                    <div class="receipt-summary">
                        <div class="row"><span>Item Count</span><span>${itemCount}</span></div>
                        <div class="row"><span>Subtotal</span><span>${subtotal}</span></div>
                        <div class="row"><span>Services</span><span>${services}</span></div>
                        <div class="row"><span>Extra Charges</span><span>${extra}</span></div>
                        <div class="row"><span>Discount</span><span>-${discount}</span></div>
                        <div class="row"><span>Included VAT (12%)</span><span>${tax}</span></div>
                        <div class="row grand"><span>TOTAL</span><span>${total}</span></div>
                    </div>

                    <div class="receipt-divider"></div>

                    <div class="receipt-payment">
                        <div class="row"><span class="label">Amount Paid</span><span>${paid}</span></div>
                        <div class="row"><span class="label">Change (Sukli)</span><span style="font-weight:700; color:#059669;">${change}</span></div>
                        <div class="row"><span class="label">Payment Method</span><span>${paymentMethod}</span></div>
                    </div>

                    <div class="receipt-thankyou">Thank You!</div>

                </div>
            </div>
        </body>
        </html>
    `;

    openPrintWindow(receiptHTML);
}

function buildInvoiceHTML(receiptData, cashierName) {
    const invoiceNum = receiptData.invoiceNumber || receiptData.invoice_number || 'INV-000000';
    const rawDate = receiptData.date || receiptData.created_at || new Date();
    const dateStr = new Date(rawDate).toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const subtotal = receiptData.subtotal != null ? parseFloat(receiptData.subtotal) : 0;
    const discount = receiptData.discount != null ? parseFloat(receiptData.discount) : 0;
    const tax = receiptData.tax != null ? parseFloat(receiptData.tax) : 0;
    const total = receiptData.total != null ? parseFloat(receiptData.total) : 0;
    const amtReceived = receiptData.amountReceived != null ? parseFloat(receiptData.amountReceived) : (receiptData.amount_paid != null ? parseFloat(receiptData.amount_paid) : total);
    const change = Math.max(0, amtReceived - total);
    const vatableSales = total - tax;
    const payMethod = (receiptData.paymentMethod === 'qr' || receiptData.payment_method === 'qr') ? 'QR PH / GCash' : (receiptData.paymentMethod || receiptData.payment_method || 'Cash');

    const items = receiptData.items || receiptData.details || [];
    const rowsHTML = items.map(item => {
        const qty = item.qty != null ? item.qty : (item.quantity != null ? item.quantity : 1);
        const price = item.price != null ? parseFloat(item.price) : (item.unit_price != null ? parseFloat(item.unit_price) : 0);
        const lineTotal = item.subtotal != null ? parseFloat(item.subtotal) : (price * qty);
        const skuDisplay = item.sku ? item.sku : '—';
        const itemName = item.name || item.product_name || 'Item';
        return `
            <tr>
                <td class="item-name">${itemName}</td>
                <td class="item-sku">${skuDisplay}</td>
                <td class="text-center">${qty}</td>
                <td class="text-right">${formatCurrency(price)}</td>
                <td class="text-right font-bold">${formatCurrency(lineTotal)}</td>
            </tr>`;
    }).join('');

    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice ${invoiceNum}</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  @page { size: A4 portrait; margin: 12mm 12mm; }
  body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    font-size: 11px;
    color: #0f172a;
    background: #f1f5f9;
    padding: 32px 16px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
  }

  .invoice-card {
    width: 100%;
    max-width: 680px;
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    padding: 36px 40px;
    box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.08);
  }

  /* ── Header ── */
  .inv-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 18px; border-bottom: 1.5px solid #cbd5e1; margin-bottom: 24px; }
  .brand-block { display: flex; gap: 14px; align-items: center; }
  .brand-logo { width: 48px; height: 48px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 900; color: #0f172a; letter-spacing: 0.04em; flex-shrink: 0; }
  .brand-name { font-size: 14px; font-weight: 800; text-transform: uppercase; line-height: 1.25; color: #0f172a; margin-bottom: 3px; }
  .brand-address { font-size: 10.5px; color: #64748b; line-height: 1.5; }
  .inv-meta { text-align: right; }
  .inv-meta .inv-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
  .inv-meta .inv-value { font-size: 12px; font-weight: 700; color: #0f172a; }
  .inv-meta .inv-badge { font-size: 13px; font-weight: 800; color: #0f172a; letter-spacing: 0.08em; margin-bottom: 8px; }

  /* ── Items table ── */
  .items-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 24px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
  .items-table thead tr { background: #0f172a; color: #ffffff; }
  .items-table thead th { padding: 10px 14px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #ffffff; }
  .items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
  .items-table tbody tr:last-child { border-bottom: none; }
  .items-table tbody tr:nth-child(even) { background: #f8fafc; }
  .items-table tbody td { padding: 11px 14px; font-size: 11px; vertical-align: middle; }
  .item-name { font-weight: 700; color: #0f172a; }
  .item-sku { font-family: 'Courier New', monospace; font-size: 10px; color: #64748b; }
  .font-bold { font-weight: 700; }
  .items-table .text-right { text-align: right; }
  .items-table .text-center { text-align: center; }

  /* ── Summary ── */
  .summary-wrap { display: flex; justify-content: flex-end; margin-bottom: 24px; }
  .summary-box { width: 310px; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; background: #ffffff; }
  .summary-box .s-row { display: flex; justify-content: space-between; padding: 8px 16px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
  .summary-box .s-row:last-child { border-bottom: none; }
  .summary-box .s-label { color: #64748b; font-weight: 500; }
  .summary-box .s-value { font-weight: 700; color: #0f172a; }
  .summary-box .s-total { background: #ffffff; border-t: 1.5px solid #cbd5e1; color: #0f172a; padding: 10px 16px; }
  .summary-box .s-total .s-label { color: #0f172a; font-weight: 800; font-size: 12px; }
  .summary-box .s-total .s-value { color: #0f172a; font-weight: 900; font-size: 15px; }

  /* ── Payment info ── */
  .payment-section { display: flex; gap: 14px; margin-bottom: 28px; }
  .pay-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; background: #f8fafc; }
  .pay-box .pay-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.07em; color: #64748b; margin-bottom: 4px; font-weight: 600; }
  .pay-box .pay-value { font-size: 14px; font-weight: 800; color: #0f172a; }
  .pay-box .pay-value.change { color: #059669; }

  /* ── Footer ── */
  .inv-footer { text-align: center; padding-top: 18px; border-top: 1px dashed #cbd5e1; }
  .inv-footer p { font-size: 10px; color: #64748b; line-height: 1.6; }
  .inv-footer .thank-you { font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }

  @media print {
    body { background: #ffffff !important; padding: 0 !important; display: block !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .invoice-card { max-width: 100% !important; border: none !important; box-shadow: none !important; padding: 0 !important; border-radius: 0 !important; }
  }
</style>
</head>
<body>

  <div class="invoice-card">
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
          <th style="text-align:left; width:36%">Item / Description</th>
          <th style="text-align:left; width:24%">SKU</th>
          <th style="text-align:center; width:10%">Qty</th>
          <th style="text-align:right; width:15%">Unit Price</th>
          <th style="text-align:right; width:15%">Amount</th>
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

    <div class="inv-footer">
      <div class="thank-you">Thank you for your purchase!</div>
      <p>This serves as your official receipt. Please keep this for your records.</p>
      <p>For concerns, please contact us at Tel: (02) 1234-56578</p>
    </div>
  </div>

</body>
</html>`;
}

function printInvoice() {
    const receiptData = posState.lastReceipt || {
        invoiceNumber: document.getElementById('receiptInvoice')?.textContent || 'INV-000000',
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
        change: 0,
        paymentMethod: posState.paymentMethod,
    };
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

async function confirmPayment() {
    const confirmBtn = document.getElementById('posPaymentModalConfirm');
    const originalText = confirmBtn ? confirmBtn.textContent : 'Confirm Payment';

    const selectedMethod = document.querySelector('input[name="posPaymentModalMethod"]:checked')?.value || posState.paymentMethod;
    updatePaymentMethod(selectedMethod);

    const invoiceNumber = document.getElementById('posPaymentInvoice')?.textContent || 'INV-000000';
    const now = new Date();
    const subtotal = posState.cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0);
    const servicesTotal = Array.from(posState.selectedServices).reduce((sum, serviceId) => {
        const service = posState.services.find(s => s.id === serviceId);
        return service ? sum + service.price : sum;
    }, 0);
    const extra = Number(posState.extraCharge || 0);
    const discount = Number(posState.discount || 0);

    const transactionTotal = Math.max(0, subtotal + servicesTotal + extra - discount);
    const tax = transactionTotal * (12 / 112);

    let amountPaid = transactionTotal;
    let changeAmount = 0;

    if (selectedMethod === 'cash') {
        const tenderedInput = document.getElementById('posAmountTenderedInput');
        const rawVal = tenderedInput ? tenderedInput.value.trim() : '';
        if (!rawVal) {
            alert('Please enter the cash amount paid by the customer.');
            tenderedInput?.focus();
            return;
        }
        const val = parseFloat(rawVal) || 0;
        if (val < transactionTotal - 0.001) {
            const short = transactionTotal - val;
            alert(`Insufficient cash payment.\n\nTotal Due: ${formatCurrency(transactionTotal)}\nAmount Paid: ${formatCurrency(val)}\nShortage: ${formatCurrency(short)}`);
            tenderedInput?.focus();
            return;
        }
        amountPaid = val;
        changeAmount = Math.max(0, val - transactionTotal);
    }

    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';
    }

    const items = posState.cart.map(item => ({
        id: item.id,
        name: item.name,
        sku: item.sku || '',
        qty: item.quantity,
        price: item.unit_price,
    }));

    const transactionItems = posState.cart.map(item => ({
        id: item.id,
        name: item.name,
        sku: item.sku || '',
        compatibility: item.compatibility || '',
        quantity: item.quantity,
        unit_price: item.unit_price,
        category: item.category || 'Uncategorized',
    }));

    const payload = {
        invoice_number: invoiceNumber,
        items: transactionItems,
        subtotal: subtotal,
        services_total: servicesTotal,
        extra_charge: extra,
        discount: discount,
        tax: tax,
        total_amount: transactionTotal,
        amount_paid: amountPaid,
        change_amount: changeAmount,
        payment_method: selectedMethod === 'qr' ? 'qr' : 'cash',
    };

    try {
        const response = await fetch('/api/pos/transactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data.success) {
            const errorMsg = data.message || 'Transaction failed. Please check stock availability.';
            alert(`Payment Failed: Insufficient Stock or System Error\n\n${errorMsg}`);
            await revalidateCartStock();
            searchProducts(posState.productSearchQuery, posState.productPage);
            return;
        }

        closePaymentModal();

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
            amountReceived: amountPaid,
            change: changeAmount,
            paymentMethod: selectedMethod,
        };

        recordTransaction(
            invoiceNumber,
            now.toISOString(),
            transactionTotal,
            selectedMethod,
            items
        );

        renderTransactionHistory();
        populateReceipt();

        if (selectedMethod === 'qr') {
            showQRPaymentModal(transactionTotal);
        } else {
            const receiptDetails = document.getElementById('receiptInvoiceDetails');
            const viewInvoiceButton = document.getElementById('posViewInvoiceButton');
            const printButton = document.getElementById('posPrintReceiptButton');
            if (receiptDetails) receiptDetails.classList.add('hidden');
            if (viewInvoiceButton) viewInvoiceButton.classList.remove('hidden');
            if (printButton) printButton.classList.remove('hidden');
            showReceiptOverlay();
        }

        document.querySelectorAll('.pos-service-checkbox').forEach(el => { el.checked = false; });
        posState.selectedServices.clear();
        const extraInp = document.getElementById('posExtraChargeInput');
        if (extraInp) extraInp.value = '0';
        const discInp = document.getElementById('posDiscountInput');
        if (discInp) discInp.value = '0';
        const scanFeedback = document.getElementById('posScanFeedback');
        if (scanFeedback) scanFeedback.textContent = '';
        updateTotals();
        clearCart();
        searchProducts(posState.productSearchQuery, posState.productPage);

    } catch (error) {
        console.error('Error processing transaction:', error);
        alert(`Transaction Error: ${error.message || 'Unable to connect to server.'}`);
    } finally {
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = originalText;
        }
    }
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
    // Category filter buttons
    const categoryButtons = document.querySelectorAll('.pos-category-button');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function () {
            const category = this.textContent.trim();
            posState.selectedCategory = category;
            posState.productPage = 1; // Reset to first page

            // Update button styling
            categoryButtons.forEach(btn => {
                btn.classList.remove('bg-emerald-600', 'text-white', 'shadow-sm');
                btn.classList.add('border', 'border-slate-200', 'bg-white', 'text-slate-700', 'hover:border-emerald-500');
            });
            this.classList.remove('border', 'border-slate-200', 'bg-white', 'text-slate-700', 'hover:border-emerald-500');
            this.classList.add('bg-emerald-600', 'text-white', 'shadow-sm');

            // Trigger search with category filter
            searchProducts(posState.productSearchQuery, 1);
        });
    });

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
            if (button.disabled) {
                showNotification('Product is out of stock', 'error');
                return;
            }
            const productId = Number(button.dataset.id);
            const name = button.dataset.name;
            const sku = button.dataset.sku;
            const unitPrice = parseFloat(button.dataset.price) || 0;
            const stockQty = Number(button.dataset.stock ?? 0);
            const productDescription = button.dataset.productDescription || '';
            const brand = button.dataset.brand || '';
            const compatibility = button.dataset.compatibility || '';
            const category = button.dataset.category || '';
            const discountType = button.dataset.discountType || null;
            const discountValue = parseFloat(button.dataset.discountValue) || 0;

            if (!productId) return;
            addProductToCart({
                id: productId,
                name,
                sku,
                unit_price: unitPrice,
                stock_quantity: stockQty,
                product_description: productDescription,
                brand,
                compatibility,
                category,
                discount_type: discountType,
                discount_value: discountValue
            });
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
                const placeholder = card?.querySelector('.pos-image-placeholder');
                if (preview) {
                    preview.style.backgroundImage = `url('${compressedDataUrl}')`;
                    preview.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }

                // Save compressed version — keyed only by product ID to avoid cross-product conflicts
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

        if (action === 'toggle-details') {
            if (posState.expandedCartItems.has(productId)) {
                posState.expandedCartItems.delete(productId);
            } else {
                posState.expandedCartItems.add(productId);
            }
            renderCart();
            return;
        }

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
    document.getElementById('posAmountTenderedInput')?.addEventListener('input', updateChangeCalculation);
    document.querySelectorAll('.pos-quick-cash-pill').forEach(btn => {
        btn.addEventListener('click', function () {
            const tenderedInput = document.getElementById('posAmountTenderedInput');
            if (!tenderedInput) return;
            const total = posState.currentPaymentTotal ?? 0;
            if (this.dataset.mode === 'exact') {
                tenderedInput.value = total > 0 ? (total % 1 === 0 ? total : total.toFixed(2)) : 0;
            } else if (this.dataset.amount) {
                tenderedInput.value = this.dataset.amount;
            }
            updateChangeCalculation();
            tenderedInput.focus();
        });
    });
    document.getElementById('posExtraChargeInput')?.addEventListener('input', event => updateExtraCharge(event.target.value));
    document.getElementById('posDiscountInput')?.addEventListener('input', event => updateDiscount(event.target.value));
    document.getElementById('posRemoveDiscountBtn')?.addEventListener('click', removeAppliedDiscount);
    document.querySelectorAll('.pos-payment-method').forEach(radio => {
        radio.addEventListener('change', event => updatePaymentMethod(event.target.value));
    });

    document.querySelectorAll('input[name="posPaymentModalMethod"]').forEach(radio => {
        radio.addEventListener('change', event => {
            updatePaymentMethod(event.target.value);
            updateChangeCalculation();
        });
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
    if (window.POS?.routes?.apiProducts) {
        posState.apiProductsUrl = window.POS.routes.apiProducts;
    }
    posState.transactionHistory = loadTransactionHistory();
    loadProductImagePreviews();
    setupPosEvents();
    renderCart();
    loadPOSCategories(); // Load categories dynamically
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
window.searchProducts = searchProducts;

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
            const stock = Number(product.stock_quantity ?? product.stock ?? 0);
            const pName = product.product_name || product.name || 'Product';
            if (stock <= 0) {
                showNotification(`Cannot add item. ${pName} is out of stock.`, 'error');
                return;
            }
            addProductToCart({
                id: product.id,
                name: pName,
                sku: product.sku || '',
                product_description: product.product_description || product.category || '',
                brand: product.brand || '',
                compatibility: product.compatibility || '',
                category: product.category || 'Uncategorized',
                stock_quantity: stock,
                unit_price: Number(product.unit_price || 0),
                discount_type: product.discount_type || null,
                discount_value: Number(product.discount_value || 0)
            });
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
    notification.className = 'pos-toast-notification fixed top-4 right-8 z-50 rounded-[10px] border p-4 text-sm font-medium shadow-lg transition-all duration-300';
    if (type === 'success') {
        notification.style.backgroundColor = '#e6fffe';
        notification.style.borderColor = '#6EC1D1';
        notification.style.borderWidth = '1px';
        notification.style.borderStyle = 'solid';
        notification.style.color = '#0f172a';
        notification.style.borderRadius = '10px';
        notification.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
    } else {
        notification.style.backgroundColor = '#fff1f2';
        notification.style.borderColor = '#fecdd3';
        notification.style.borderWidth = '1px';
        notification.style.borderStyle = 'solid';
        notification.style.color = '#9f1239';
        notification.style.borderRadius = '10px';
        notification.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
    }
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s ease';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

function dismissAllNotifications() {
    document.querySelectorAll('.pos-toast-notification').forEach(el => {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.3s ease';
        setTimeout(() => el.remove(), 300);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePos);
} else {
    initializePos();
}
