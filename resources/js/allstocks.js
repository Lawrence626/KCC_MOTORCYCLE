// allstocks.js - extracted from blade template
// Expects a global `window.AllStocks` with routes and csrfToken set in the blade.

// Store all products for filtering
let allProducts = [];
let currentFilters = {
    search: '',
    category: '',
    product_name: '',
    brand: '',
    size: '',
    status: '',
    expiry_status: '',
    restock_date: ''
};
let searchTimeout;
let currentEditProduct = null;

function setEditFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    let errorContainer = field.parentElement.querySelector('.edit-field-error');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.className = 'edit-field-error mt-1 text-xs text-red-600 min-h-[1.1rem]';
        field.parentElement.appendChild(errorContainer);
    }
    errorContainer.textContent = message || '';
}

function clearEditProductErrors() {
    document.querySelectorAll('#editProductForm .edit-field-error').forEach(el => {
        el.textContent = '';
    });
}

function populateEditProductForm(product) {
    currentEditProduct = product;
    document.getElementById('editProductId').value = product.id;
    document.getElementById('editName').value = product.name || '';
    document.getElementById('editProductName').value = product.product_name || '';
    document.getElementById('editSku').value = product.sku || '';
    document.getElementById('editBrand').value = product.brand || '';
    document.getElementById('editSize').value = product.size || '';
    document.getElementById('editColor').value = product.color || '';
    document.getElementById('editStockQuantity').value = product.stock_quantity ?? 0;
    document.getElementById('editUnitPrice').value = product.unit_price ?? 0;
    document.getElementById('editSupplier').value = product.supplier_name || '';
    document.getElementById('editCategory').value = product.category || '';
    document.getElementById('editLastRestock').value = product.last_restock_date || '';
    document.getElementById('editExpiryDate').value = product.expiry_date || '';
    document.getElementById('editReorderLevel').value = product.reorder_level ?? 0;
    document.getElementById('editBarcode').value = product.barcode || '';
    document.getElementById('editDescription').value = product.description || '';
    clearEditProductErrors();
}

// Modal functions
function openAddStockModal() {
    document.getElementById('addStockModal').classList.remove('hidden');
    loadProductsForSelect();
}

function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
    const form = document.getElementById('addStockForm');
    if (form) form.reset();
}

function openEditProductModal(productId) {
    const modal = document.getElementById('editProductModal');
    if (!modal) {
        alert('Edit modal not found');
        return;
    }

    modal.classList.remove('hidden');
    const form = document.getElementById('editProductForm');
    if (form) {
        form.reset();
        clearEditProductErrors();
    }
    currentEditProduct = null;

    fetch(`${window.AllStocks.routes.apiProductShowBase}/${productId}`)
        .then(async res => {
            const result = await res.json();
            if (!res.ok) {
                throw new Error(result.message || 'Unable to load product');
            }
            if (result.product) {
                populateEditProductForm(result.product);
            } else {
                showToast('Product not found.', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            showToast('Error loading product data.', 'error');
        });
}

function closeEditProductModal() {
    const modal = document.getElementById('editProductModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    const form = document.getElementById('editProductForm');
    if (form) {
        form.reset();
        clearEditProductErrors();
    }
    currentEditProduct = null;
}

function handleEditClick(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    openEditProductModal(productId);
}

// Make function globally accessible
window.openEditModal = function(productId) {
    openEditProductModal(productId);
};

// Direct edit modal function
window.openEditModalDirect = function(productId) {
    openEditProductModal(productId);
};

// Load products for select dropdown
async function loadProductsForSelect() {
    try {
        const response = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const result = await response.json();

        const select = document.getElementById('productSelect');
        if (!select) return;
        select.innerHTML = '<option value="">Select a product...</option>';

        if (result.data && result.data.length > 0) {
            result.data.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.name} (${product.sku})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Load filter options (Product Name, Brand, Size)
async function loadFilterOptions() {
    try {
        const response = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const result = await response.json();

        let productNames = new Set();
        let brands = new Set();
        let sizes = new Set();

        if (result.data && result.data.length > 0) {
            result.data.forEach(product => {
                if (product.product_name) productNames.add(product.product_name);
                if (product.brand) brands.add(product.brand);
                if (product.size) sizes.add(product.size);
            });
        }

        // Populate Product Name dropdown
        const productNameSelect = document.getElementById('productNameFilter');
        if (productNameSelect) {
            productNames.forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                productNameSelect.appendChild(option);
            });
        }

        // Populate Brand dropdown
        const brandSelect = document.getElementById('brandFilter');
        if (brandSelect) {
            Array.from(brands).sort().forEach(brand => {
                const option = document.createElement('option');
                option.value = brand;
                option.textContent = brand;
                brandSelect.appendChild(option);
            });
        }

        // Populate Size dropdown
        const sizeSelect = document.getElementById('sizeFilter');
        if (sizeSelect) {
            Array.from(sizes).sort().forEach(size => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                sizeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading filter options:', error);
    }
}

// Auto-filter with debounce
function performSearch() {
    currentPage = 1;
    loadProducts(1);
}

function resetFilters() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    ['categoryFilter','productNameFilter','brandFilter','sizeFilter','statusFilter','expiryStatusFilter','restockDateFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    currentFilters = { search: '', category: '', product_name: '', brand: '', size: '', status: '', expiry_status: '', restock_date: '' };
    currentPage = 1;
    loadProducts(1);
}

// Attach UI events (safely when elements exist)
function attachUIEvents() {
    const searchEl = document.getElementById('searchInput');
    if (searchEl) {
        searchEl.addEventListener('input', function(e) {
            currentFilters.search = e.target.value;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    const categoryEl = document.getElementById('categoryFilter');
    if (categoryEl) {
        categoryEl.addEventListener('change', function(e) {
            currentFilters.category = e.target.value;
            renderCategoryChips();
            performSearch();
        });
    }
    const productNameEl = document.getElementById('productNameFilter');
    if (productNameEl) productNameEl.addEventListener('change', function(e){ currentFilters.product_name = e.target.value; performSearch(); });
    const brandEl = document.getElementById('brandFilter');
    if (brandEl) brandEl.addEventListener('change', function(e){ currentFilters.brand = e.target.value; performSearch(); });
    const sizeEl = document.getElementById('sizeFilter');
    if (sizeEl) sizeEl.addEventListener('change', function(e){ currentFilters.size = e.target.value; performSearch(); });
    const statusEl = document.getElementById('statusFilter');
    if (statusEl) statusEl.addEventListener('change', function(e){ currentFilters.status = e.target.value; performSearch(); });
    const expiryStatusEl = document.getElementById('expiryStatusFilter');
    if (expiryStatusEl) expiryStatusEl.addEventListener('change', function(e){ currentFilters.expiry_status = e.target.value; performSearch(); });
    const restockDateEl = document.getElementById('restockDateFilter');
    if (restockDateEl) restockDateEl.addEventListener('change', function(e){ currentFilters.restock_date = e.target.value; performSearch(); });

    const addStockBtn = document.getElementById('addStockBtn');
    if (addStockBtn) addStockBtn.addEventListener('click', openAddStockModal);
    const closeAdd = document.getElementById('closeAddStockModal');
    if (closeAdd) closeAdd.addEventListener('click', closeAddStockModal);
    const cancelAdd = document.getElementById('cancelAddStock');
    if (cancelAdd) cancelAdd.addEventListener('click', closeAddStockModal);

    const addStockModal = document.getElementById('addStockModal');
    if (addStockModal) addStockModal.addEventListener('click', function(e) { if (e.target === this) closeAddStockModal(); });

    // Edit product modal events
    const closeEdit = document.getElementById('closeEditProductModal');
    if (closeEdit) closeEdit.addEventListener('click', closeEditProductModal);
    const cancelEdit = document.getElementById('cancelEditProduct');
    if (cancelEdit) cancelEdit.addEventListener('click', closeEditProductModal);

    const editProductModal = document.getElementById('editProductModal');
    if (editProductModal) editProductModal.addEventListener('click', function(e) { if (e.target === this) closeEditProductModal(); });

    const addStockForm = document.getElementById('addStockForm');
    if (addStockForm) {
        addStockForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const productId = document.getElementById('productSelect')?.value;
            const quantity = document.getElementById('quantityInput')?.value;

            if (!productId || !quantity) {
                alert('Please fill in all required fields');
                return;
            }

            const submitBtn = document.getElementById('submitAddStock');
            if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Adding...'; }

            try {
                const res = await fetch(window.AllStocks.routes.stockAdd, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.AllStocks.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: parseInt(quantity),
                        unit_price: document.getElementById('unitPriceInput')?.value || null,
                        supplier_name: document.getElementById('supplierInput')?.value || null,
                        notes: document.getElementById('notesInput')?.value || null,
                    })
                });

                const result = await res.json();
                if (result.success) {
                    alert('✅ Stock added successfully!');
                    closeAddStockModal();
                    loadStats();
                    loadProducts(currentPage);
                } else {
                    alert('❌ ' + (result.message || 'Failed to add stock'));
                }
            } catch (error) {
                console.error('Error adding stock:', error);
                alert('❌ Error adding stock: ' + error.message);
            } finally {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Add Stock'; }
            }
        });
    }

    // Edit product form submission
    const editProductForm = document.getElementById('editProductForm');
    if (editProductForm) {
        editProductForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const productId = document.getElementById('editProductId')?.value;
            if (!productId) {
                alert('Product ID is required');
                return;
            }

            const submitBtn = document.getElementById('submitEditProduct');
            if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Saving...'; }

            try {
                const payload = {};
                const fieldMap = {
                    name: document.getElementById('editName')?.value,
                    product_name: document.getElementById('editProductName')?.value,
                    sku: document.getElementById('editSku')?.value,
                    brand: document.getElementById('editBrand')?.value,
                    size: document.getElementById('editSize')?.value,
                    color: document.getElementById('editColor')?.value,
                    stock_quantity: parseInt(document.getElementById('editStockQuantity')?.value) || 0,
                    unit_price: parseFloat(document.getElementById('editUnitPrice')?.value) || 0,
                    supplier_name: document.getElementById('editSupplier')?.value,
                    category: document.getElementById('editCategory')?.value,
                    last_restock_date: document.getElementById('editLastRestock')?.value,
                    expiry_date: document.getElementById('editExpiryDate')?.value,
                    reorder_level: parseInt(document.getElementById('editReorderLevel')?.value) || 0,
                    barcode: document.getElementById('editBarcode')?.value,
                    description: document.getElementById('editDescription')?.value,
                };

                Object.entries(fieldMap).forEach(([field, value]) => {
                    const currentValue = currentEditProduct?.[field];
                    const normalizedCurrent = currentValue === null || currentValue === undefined ? '' : String(currentValue);
                    const normalizedValue = value === null || value === undefined ? '' : String(value);
                    if (normalizedCurrent !== normalizedValue) {
                        payload[field] = value;
                    }
                });

                const url = window.AllStocks.routes.productUpdateBase + '/' + productId;
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.AllStocks.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();
                if (result.success) {
                    showToast(result.message || 'Product updated successfully.', 'success');
                    closeEditProductModal();
                    loadStats();
                    loadProducts(currentPage);
                } else {
                    clearEditProductErrors();
                    if (result.errors && typeof result.errors === 'object') {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const inputId = {
                                name: 'editName',
                                product_name: 'editProductName',
                                sku: 'editSku',
                                brand: 'editBrand',
                                size: 'editSize',
                                color: 'editColor',
                                stock_quantity: 'editStockQuantity',
                                unit_price: 'editUnitPrice',
                                supplier_name: 'editSupplier',
                                category: 'editCategory',
                                last_restock_date: 'editLastRestock',
                                expiry_date: 'editExpiryDate',
                                reorder_level: 'editReorderLevel',
                                barcode: 'editBarcode',
                                description: 'editDescription',
                            }[field] || null;
                            if (inputId) {
                                setEditFieldError(inputId, Array.isArray(messages) ? messages[0] : messages);
                            }
                        });
                    }
                    showToast(result.message || 'Failed to update product', 'error');
                }
            } catch (error) {
                console.error('Error updating product:', error);
                showToast('Error updating product: ' + error.message, 'error');
            } finally {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Save Changes'; }
            }
        });
    }

    const importFile = document.getElementById('importFile');
    if (importFile) {
        importFile.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const label = document.querySelector('label[for="importFile"]');
            const originalText = label ? label.textContent : 'Import';
            if (label) { label.textContent = 'Importing...'; label.style.pointerEvents = 'none'; label.style.opacity = '0.6'; }

            try {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', window.AllStocks.csrfToken);

                const response = await fetch(window.AllStocks.routes.stockImport, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                let result;
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) result = await response.json();
                else {
                    const text = await response.text();
                    throw new Error('Server returned non-JSON response:\n' + text.substring(0,200));
                }

                if (result.success) {
                    let message = `✅ ${result.message}`;
                    if (result.debug_total_rows_processed) message += `\nRows processed: ${result.debug_total_rows_processed}`;
                    if (result.errors && result.errors.length > 0) {
                        message += `\n\n⚠️ Errors (${result.errors.length} total):\n${result.errors.slice(0,5).join('\n')}`;
                        if (result.errors.length > 5) message += `\n... and ${result.errors.length - 5} more`;
                    }
                    alert(message);
                    setTimeout(() => { loadStats(); loadProducts(1); }, 300);
                } else {
                    alert(`❌ ${result.message}`);
                }
            } catch (error) {
                alert('❌ Import failed: ' + error.message);
                console.error('Import error:', error);
            } finally {
                if (label) { label.textContent = originalText; label.style.pointerEvents = 'auto'; label.style.opacity = '1'; }
                importFile.value = '';
            }
        });
    }
}

// Load stats from API
async function loadStats() {
    try {
        const response = await fetch(window.AllStocks.routes.apiStats);

        if (!response.ok) {
            const bodyText = await response.text();
            throw new Error(`Stats request failed: ${response.status} ${response.statusText} - ${bodyText}`);
        }

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            const bodyText = await response.text();
            throw new Error(`Stats request returned non-JSON response: ${contentType} - ${bodyText}`);
        }

        const stats = await response.json();

        const totalProductsEl = document.getElementById('stat-total-products');
        const totalValueEl = document.getElementById('stat-total-value');
        const lowStockEl = document.getElementById('stat-low-stock');
        const activeItemsEl = document.getElementById('stat-active-items');
        const outOfStockEl = document.getElementById('stat-out-of-stock');
        const expiringSoonEl = document.getElementById('stat-expiring-soon');
        const expiredEl = document.getElementById('stat-expired');

        if (totalProductsEl) totalProductsEl.textContent = Number(stats.total_items || 0).toLocaleString();
        if (totalValueEl) totalValueEl.textContent = '₱' + (Number(stats.total_value || 0) >= 1000000 ? (Number(stats.total_value) / 1000000).toFixed(1) + 'M' : (Number(stats.total_value) / 1000).toFixed(1) + 'K');
        if (lowStockEl) lowStockEl.textContent = Number(stats.low_stock_count || 0).toLocaleString();
        if (activeItemsEl) activeItemsEl.textContent = Number(stats.active_items || 0).toLocaleString();
        if (outOfStockEl) outOfStockEl.textContent = Number(stats.out_of_stock_count || 0).toLocaleString();
        if (expiringSoonEl) expiringSoonEl.textContent = Number(stats.expiring_soon_count || 0).toLocaleString();
        if (expiredEl) expiredEl.textContent = Number(stats.expired_count || 0).toLocaleString();
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

let movementsData = [];
let movementsPage = 1;
let movementsPerPage = 10;
let movementsDateFilter = '';

async function loadMovements() {
    try {
        if (!window.AllStocks.routes.apiMovements) {
            return;
        }

        const response = await fetch(window.AllStocks.routes.apiMovements);
        const result = await response.json();
        
        movementsData = result.data || [];
        renderMovements();
    } catch (error) {
        console.error('Error loading movements:', error);
    }
}

function renderMovements() {
    const tbody = document.getElementById('movementFeed');
    const paginationInfo = document.getElementById('movementPaginationInfo');
    const prevBtn = document.querySelector('.movement-prev');
    const nextBtn = document.querySelector('.movement-next');
    
    if (!tbody) return;

    // Filter by date range
    let filteredMovements = movementsData;
    if (movementsDateFilter) {
        const now = new Date();
        const today = now.toISOString().split('T')[0];
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        const yesterdayStr = yesterday.toISOString().split('T')[0];
        
        const last7Days = new Date(now);
        last7Days.setDate(last7Days.getDate() - 7);
        const last7DaysStr = last7Days.toISOString().split('T')[0];
        
        const last30Days = new Date(now);
        last30Days.setDate(last30Days.getDate() - 30);
        const last30DaysStr = last30Days.toISOString().split('T')[0];

        filteredMovements = movementsData.filter(entry => {
            const entryDate = new Date(entry.created_at).toISOString().split('T')[0];
            
            switch(movementsDateFilter) {
                case 'today':
                    return entryDate === today;
                case 'yesterday':
                    return entryDate === yesterdayStr;
                case 'last_7_days':
                    return entryDate >= last7DaysStr && entryDate <= today;
                case 'last_30_days':
                    return entryDate >= last30DaysStr && entryDate <= today;
                default:
                    return true;
            }
        });
    }

    // Pagination
    const totalPages = Math.max(1, Math.ceil(filteredMovements.length / movementsPerPage));
    movementsPage = Math.min(Math.max(1, movementsPage), totalPages);
    
    const startIndex = (movementsPage - 1) * movementsPerPage;
    const endIndex = startIndex + movementsPerPage;
    const paginatedMovements = filteredMovements.slice(startIndex, endIndex);

    tbody.innerHTML = '';
    if (paginatedMovements.length > 0) {
        paginatedMovements.forEach(entry => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-2 py-2 text-slate-700">${entry.created_at}</td>
                <td class="px-2 py-2 text-slate-700">${entry.product_name} ${entry.sku ? '(' + entry.sku + ')' : ''}</td>
                <td class="px-2 py-2 text-slate-700 capitalize">${entry.type.replace(/_/g, ' ')}</td>
                <td class="px-2 py-2 text-center text-slate-700">${entry.quantity_change ?? '-'}</td>
                <td class="px-2 py-2 text-slate-700">${entry.notes || entry.supplier_name || '-'}</td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="px-2 py-4 text-center text-slate-500">No movements found</td></tr>';
    }

    // Update pagination info
    if (paginationInfo) {
        paginationInfo.textContent = `Showing ${startIndex + 1}-${Math.min(endIndex, filteredMovements.length)} of ${filteredMovements.length} movements`;
    }

    // Update button states
    if (prevBtn) prevBtn.disabled = movementsPage === 1;
    if (nextBtn) nextBtn.disabled = movementsPage === totalPages;
}

let currentPage = 1;
let perPage = 10;

// Load products from API
async function loadProducts(page = 1) {
    try {
        let url = window.AllStocks.routes.apiProducts + `?page=${page}&per_page=${perPage}`;

        if (currentFilters.search) url += `&search=${encodeURIComponent(currentFilters.search)}`;
        if (currentFilters.category) url += `&category=${encodeURIComponent(currentFilters.category)}`;
        if (currentFilters.product_name) url += `&product_name=${encodeURIComponent(currentFilters.product_name)}`;
        if (currentFilters.brand) url += `&brand=${encodeURIComponent(currentFilters.brand)}`;
        if (currentFilters.size) url += `&size=${encodeURIComponent(currentFilters.size)}`;
        if (currentFilters.status) url += `&status=${encodeURIComponent(currentFilters.status)}`;
        if (currentFilters.expiry_status) url += `&expiry_status=${encodeURIComponent(currentFilters.expiry_status)}`;
        if (currentFilters.restock_date) url += `&restock_date=${encodeURIComponent(currentFilters.restock_date)}`;

        const response = await fetch(url);
        const result = await response.json();

        const tbody = document.querySelector('table tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (result.data && result.data.length > 0) {
            result.data.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-3 py-2 w-6">
                        <input type="checkbox" class="product-checkbox rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" data-product-id="${product.id}" />
                    </td>
                    <td class="px-3 py-2 text-slate-900 font-medium">${product.name}</td>
                    <td class="px-3 py-2 text-slate-600">${product.product_name || 'Uncategorized'}</td>
                    <td class="px-3 py-2 text-slate-600">${`KCC_${(product.sku || product.name || '').replace(/[^A-Za-z0-9\-\+]/g, '')}`}</td>
                    <td class="px-3 py-2 text-slate-600">${product.brand || '-'}</td>
                    <td class="px-3 py-2 text-slate-600">${product.size || '-'}</td>
                    <td class="px-3 py-2 text-slate-600">${product.color || '-'}</td>
                    <td class="px-3 py-2 text-center font-semibold ${product.stock_quantity === 0 ? 'text-red-600' : 'text-slate-900'}">${product.stock_quantity}</td>
                    <td class="px-3 py-2 text-right text-slate-900">
                        <div class="inline-flex items-center justify-end gap-2">
                            <span class="unit-price-text">₱${parseFloat(product.unit_price).toFixed(2)}</span>
                            <input data-product-id="${product.id}" class="unit-price-input hidden w-28 px-2 py-1 rounded border border-slate-200 text-right text-sm" value="${parseFloat(product.unit_price).toFixed(2)}" />
                        </div>
                    </td>
                    <td class="px-3 py-2 text-slate-600">${product.supplier_name || '-'}</td>
                    <td class="px-3 py-2 text-slate-600">${product.last_restock_date || '-'}</td>
                    <td class="px-3 py-2 text-slate-600">${product.expiry_date ? `${product.expiry_date} • ${product.expiry_status_label || 'Status'}` : 'Non-expiring'}</td>
                    <td class="px-3 py-2 text-center">
                        <a href="javascript:void(0)" onclick="event.preventDefault(); openEditModal(${product.id});" class="text-cyan-600 hover:text-cyan-700 text-xs font-medium cursor-pointer z-50 relative">Edit</a>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="13" class="px-3 py-8 text-center text-slate-500">No products found</td></tr>';
        }

        currentPage = page;
        updatePagination(result.pagination);
        attachPriceEditing();
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Update pagination controls
function updatePagination(pagination) {
    const paginationContainer = document.getElementById('paginationControls');
    if (!paginationContainer) {
        console.log('Pagination container not found');
        return;
    }
    
    if (!pagination || !pagination.last_page) {
        console.log('Pagination data not available', pagination);
        return;
    }
    
    let html = `<button onclick="loadProducts(${Math.max(1, currentPage - 1)})" class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">← Prev</button>`;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(pagination.last_page, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        const btnClass = i === currentPage ? 'bg-cyan-600 text-white' : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50';
        html += `<button onclick="loadProducts(${i})" class="px-2 py-1 rounded-lg text-xs font-medium ${btnClass}">${i}</button>`;
    }

    html += `<button onclick="loadProducts(${Math.min(pagination.last_page, currentPage + 1)})" class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">Next →</button>`;

    paginationContainer.innerHTML = html;

    const summary = document.getElementById('paginationInfo');
    if (summary) {
        summary.textContent = `Showing ${pagination.from || 0}-${pagination.to || 0} of ${pagination.total?.toLocaleString() || 0} items`;
    }
}

// Export products CSV with current filters
function exportProducts() {
    const params = new URLSearchParams();
    if (currentFilters.search) params.append('search', currentFilters.search);
    if (currentFilters.category) params.append('category', currentFilters.category);
    if (currentFilters.product_name) params.append('product_name', currentFilters.product_name);
    if (currentFilters.brand) params.append('brand', currentFilters.brand);
    if (currentFilters.size) params.append('size', currentFilters.size);

    const url = window.AllStocks.routes.stockExport + (params.toString() ? ('?' + params.toString()) : '');
    window.location = url;
}

// Inline price editing (event delegation)
function attachPriceEditing() {
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;

    tbody.addEventListener('click', async function(e) {
        const editProductBtn = e.target.closest('.edit-product-trigger');

        // Handle full product edit
        if (editProductBtn) {
            e.preventDefault();
            e.stopPropagation();
            const productId = editProductBtn.dataset.editProductId;
            if (productId) {
                openEditProductModal(productId);
            }
            return;
        }
    });
}

// Also attach directly to document as backup
document.addEventListener('click', function(e) {
    const editProductBtn = e.target.closest('.edit-product-trigger');
    if (editProductBtn) {
        e.preventDefault();
        e.stopPropagation();
        const productId = editProductBtn.dataset.editProductId;
        if (productId) {
            openEditProductModal(productId);
        }
    }
});

// Initialize when DOM is ready
function initializeAllStocksPage() {
    attachUIEvents();
    attachPriceEditing();

    // Load initial data
    loadStats();
    loadProducts(1);
    loadMovements();

    // Load filter options after a short delay
    setTimeout(() => {
        loadFilterOptions();
    }, 500);

    // Wire export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) exportBtn.addEventListener('click', exportProducts);

    // Wire QR code generation button
    const generateQrBtn = document.getElementById('generateQrBtn');
    if (generateQrBtn) {
        generateQrBtn.addEventListener('click', openQRCodeModal);
    }

    const refreshMovementsBtn = document.getElementById('refreshMovementsBtn');
    if (refreshMovementsBtn) refreshMovementsBtn.addEventListener('click', () => {
        loadMovements();
    });

    // Wire movement date filter
    const movementDateFilter = document.getElementById('movementDateFilter');
    if (movementDateFilter) {
        movementDateFilter.addEventListener('change', function() {
            movementsDateFilter = this.value;
            movementsPage = 1;
            renderMovements();
        });
    }

    // Wire movement pagination buttons
    const movementPrevBtn = document.querySelector('.movement-prev');
    const movementNextBtn = document.querySelector('.movement-next');
    
    if (movementPrevBtn) {
        movementPrevBtn.addEventListener('click', function() {
            if (movementsPage > 1) {
                movementsPage--;
                renderMovements();
            }
        });
    }
    
    if (movementNextBtn) {
        movementNextBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(movementsData.length / movementsPerPage);
            if (movementsPage < totalPages) {
                movementsPage++;
                renderMovements();
            }
        });
    }

    // Wire select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAllStocksPage);
} else {
    initializeAllStocksPage();
}




// Export functions to window for inline usage if needed
window.AllStocksMethods = {
    openAddStockModal,
    closeAddStockModal,
    loadProductsForSelect,
    loadFilterOptions,
    performSearch,
    resetFilters,
    loadStats,
    loadProducts,
    openQRCodeModal,
    generateQRCodes,
    printQRCodes
};

// Expose key functions to global scope for onclick handlers
window.loadProducts = loadProducts;
window.performSearch = performSearch;
window.resetFilters = resetFilters;
window.openQRCodeModal = openQRCodeModal;
window.generateQRCodes = generateQRCodes;
window.printQRCodes = printQRCodes;
window.downloadQRCodes = downloadQRCodes;
window.updateRestockDates = updateRestockDates;

// Store current selected products for restock date updates
let currentSelectedProducts = [];

// QR Code Generation Functions
async function openQRCodeModal() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one product to generate QR codes.');
        return;
    }
    
    // Load all products for QR generation
    try {
        const response = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const result = await response.json();
        
        if (result.data && result.data.length > 0) {
            allProducts = result.data;
        }
    } catch (error) {
        console.error('Error loading products:', error);
        alert('Error loading products. Please try again.');
        return;
    }
    
    const selectedProducts = Array.from(selectedCheckboxes).map(cb => {
        const productId = cb.dataset.productId;
        const product = allProducts.find(p => p.id === parseInt(productId));
        return product;
    }).filter(p => p !== undefined);

    if (selectedProducts.length === 0) {
        alert('Please select at least one product to generate QR codes.');
        return;
    }

    document.getElementById('selectedCount').textContent = selectedProducts.length;
    const modal = document.getElementById('qrCodeModal');
    
    // Store selected products for restock date updates
    currentSelectedProducts = selectedProducts;
    
    modal.style.display = 'flex';
    modal.style.visibility = 'visible';
    modal.style.opacity = '1';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.right = '0';
    modal.style.bottom = '0';
    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
    modal.style.zIndex = '99999';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    
    const modalContent = modal.querySelector('div');
    if (modalContent) {
        modalContent.style.display = 'block';
        modalContent.style.visibility = 'visible';
    }
    
    generateQRCodes(selectedProducts);
}

function generateQRCodes(products) {
    const previewContainer = document.getElementById('qrCodePreview');
    const loadingIndicator = document.getElementById('qrLoading');
    
    if (!previewContainer) {
        console.error('Preview container not found');
        return;
    }
    
    // Show loading indicator
    previewContainer.innerHTML = '';
    if (loadingIndicator) {
        previewContainer.appendChild(loadingIndicator);
        loadingIndicator.classList.remove('hidden');
    }

    // Generate QR codes with a small delay to allow UI to update
    setTimeout(() => {
        if (loadingIndicator) loadingIndicator.classList.add('hidden');
        
        products.forEach((product) => {
            // Create QR code data with SKU and restock date
            const qrData = JSON.stringify({
                sku: product.sku,
                restock_date: product.last_restock || 'N/A',
                product_id: product.id
            });

            const qrCard = document.createElement('div');
            qrCard.className = 'border border-slate-200 rounded-lg p-4 bg-white';
            qrCard.innerHTML = `
                <div class="flex items-start gap-4">
                    <div id="qr-${product.id}" class="w-24 h-24 flex items-center justify-center bg-white"></div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-900 text-sm">${product.name}</h3>
                        <p class="text-xs text-slate-600">SKU: ${product.sku}</p>
                        <p class="text-xs text-slate-600">Restock: ${product.last_restock || 'N/A'}</p>
                        <p class="text-xs text-slate-500 mt-1">Scan to add to cart</p>
                    </div>
                </div>
            `;

            previewContainer.appendChild(qrCard);

            // Generate QR code
            try {
                const qrElement = document.getElementById(`qr-${product.id}`);
                if (!qrElement) return;
                
                // Clear any existing content
                qrElement.innerHTML = '';
                
                new QRCode(qrElement, {
                    text: qrData,
                    width: 96,
                    height: 96,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
                
                // Force canvas to be visible
                setTimeout(() => {
                    const canvas = qrElement.querySelector('canvas');
                    if (canvas) {
                        canvas.style.display = 'block';
                        canvas.style.width = '96px';
                        canvas.style.height = '96px';
                    }
                }, 50);
            } catch (error) {
                console.error('Error generating QR code for product:', product.id, error);
            }
        });
    }, 100);
}

function updateRestockDates() {
    const restockDateInput = document.getElementById('restockDateInput');
    const newRestockDate = restockDateInput.value;
    
    if (!newRestockDate) {
        showToast('Please select a restock date.', 'error');
        return;
    }
    
    // Update all selected products with the new restock date
    currentSelectedProducts.forEach(product => {
        product.last_restock = newRestockDate;
    });
    
    // Regenerate QR codes with updated restock date
    generateQRCodes(currentSelectedProducts);
    
    showToast('Restock date updated for all QR codes!', 'success');
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toastMessage.textContent = message;
    
    // Update toast styling based on type
    const toastInner = toast.querySelector('div');
    if (type === 'error') {
        toastInner.classList.remove('border-emerald-500');
        toastInner.classList.add('border-red-500');
        toastInner.querySelector('svg').classList.remove('text-emerald-500');
        toastInner.querySelector('svg').classList.add('text-red-500');
    } else {
        toastInner.classList.remove('border-red-500');
        toastInner.classList.add('border-emerald-500');
        toastInner.querySelector('svg').classList.remove('text-red-500');
        toastInner.querySelector('svg').classList.add('text-emerald-500');
    }
    
    // Show toast
    toast.classList.remove('translate-x-full');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 3000);
}

function downloadQRCodes() {
    const previewContainer = document.getElementById('qrCodePreview');
    
    if (!previewContainer || previewContainer.children.length === 0) {
        alert('No QR codes to download.');
        return;
    }
    
    // Create a canvas to combine all QR codes
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Calculate canvas size
    const qrCards = previewContainer.querySelectorAll('.border');
    const cardWidth = 300;
    const cardHeight = 150;
    const padding = 20;
    const cols = 2;
    const rows = Math.ceil(qrCards.length / cols);
    
    canvas.width = (cardWidth * cols) + (padding * (cols + 1));
    canvas.height = (cardHeight * rows) + (padding * (rows + 1)) + 50; // +50 for title
    
    // White background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Title
    ctx.fillStyle = '#000000';
    ctx.font = 'bold 20px Arial';
    ctx.fillText('Product QR Codes', padding, 30);
    
    let loadedImages = 0;
    const totalImages = qrCards.length;
    
    qrCards.forEach((card, index) => {
        const canvasElement = card.querySelector('canvas');
        if (!canvasElement) return;
        
        const col = index % cols;
        const row = Math.floor(index / cols);
        const x = padding + (col * (cardWidth + padding));
        const y = 50 + padding + (row * (cardHeight + padding));
        
        // Draw card border
        ctx.strokeStyle = '#cccccc';
        ctx.strokeRect(x, y, cardWidth, cardHeight);
        
        // Draw QR code canvas
        ctx.drawImage(canvasElement, x + 10, y + 10, 80, 80);
        
        // Draw text
        const name = card.querySelector('h3').textContent;
        const sku = card.querySelector('p:nth-child(2)').textContent;
        const restock = card.querySelector('p:nth-child(3)').textContent;
        
        ctx.fillStyle = '#000000';
        ctx.font = 'bold 12px Arial';
        ctx.fillText(name.substring(0, 25), x + 100, y + 25);
        
        ctx.font = '10px Arial';
        ctx.fillStyle = '#666666';
        ctx.fillText(sku, x + 100, y + 45);
        ctx.fillText(restock, x + 100, y + 60);
        
        loadedImages++;
    });
    
    if (totalImages === 0) {
        alert('No QR code images found.');
        return;
    }
    
    // Download the combined canvas
    const link = document.createElement('a');
    link.download = 'qr-codes.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
    alert('QR codes downloaded successfully!');
}

function printQRCodes() {
    const previewContainer = document.getElementById('qrCodePreview');
    
    if (!previewContainer || previewContainer.children.length === 0) {
        alert('No QR codes to print.');
        return;
    }
    
    try {
        const printWindow = window.open('', '_blank');
        
        if (!printWindow) {
            alert('Popup blocked. Please allow popups for this site and try again.');
            return;
        }
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Codes Print</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                    }
                    .qr-card {
                        border: 1px solid #ccc;
                        border-radius: 8px;
                        padding: 15px;
                        margin: 10px;
                        display: inline-block;
                        width: 200px;
                        page-break-inside: avoid;
                    }
                    .qr-card canvas {
                        width: 96px;
                        height: 96px;
                    }
                    .product-name {
                        font-weight: bold;
                        font-size: 14px;
                        margin: 5px 0;
                    }
                    .product-info {
                        font-size: 12px;
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <h2>Product QR Codes</h2>
                <div id="print-content"></div>
            </body>
            </html>
        `);

        const printContent = printWindow.document.getElementById('print-content');
        printContent.innerHTML = previewContainer.innerHTML;
        
        printWindow.document.close();
        
        // Wait for content to load before printing
        setTimeout(() => {
            printWindow.print();
        }, 500);
        
    } catch (error) {
        console.error('Error printing:', error);
        alert('Error opening print dialog. Please try again.');
    }
}
