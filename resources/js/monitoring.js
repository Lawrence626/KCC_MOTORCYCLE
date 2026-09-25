let allProducts = [];
let currentFilters = {
    search: '',
    status: '',
    expiry_status: '',
    restock_date: ''
};
let searchTimeout;

function formatDate(dateStr) {
    if (!dateStr || dateStr === '-') return '-';
    try {
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return dateStr;
    }
}

function getMonitoringProductImage(p) {
    if (!p) return null;
    if (p.image) return p.image;
    try {
        const stored = localStorage.getItem('posProductImages');
        if (stored) {
            const images = JSON.parse(stored);
            const productId = p.id || p.product_id;
            if (productId && images[productId]) return images[productId];
            if (p.sku && images[p.sku]) return images[p.sku];

            const keys = Object.keys(images);
            if (p.sku) {
                const matchSku = keys.find(k => k.toLowerCase() === String(p.sku).toLowerCase());
                if (matchSku) return images[matchSku];
            }
        }
    } catch (e) {}
    return null;
}

function renderMonitoringProductImageHtml(p) {
    const imageUrl = getMonitoringProductImage(p);
    return imageUrl
        ? `<div class="w-8 h-8 rounded-[6px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image: url('${imageUrl}');"></div>`
        : `<div class="w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
           </div>`;
}
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

function getAvailableSuppliers() {
    if (window.AllStocks && Array.isArray(window.AllStocks.suppliers) && window.AllStocks.suppliers.length > 0) {
        return Promise.resolve(window.AllStocks.suppliers);
    }
    const url = window.AllStocks?.routes?.apiSuppliers || '/api/suppliers';
    return fetch(url)
        .then(res => res.json())
        .then(data => {
            const list = data.suppliers || [];
            if (window.AllStocks) window.AllStocks.suppliers = list;
            return list;
        })
        .catch(err => {
            console.error('Error fetching suppliers:', err);
            return [];
        });
}

function updateEditSuppliersDisplay() {
    const displaySpan = document.getElementById('editSuppliersDisplay');
    const button = document.getElementById('editSuppliersButton');
    const hiddenInput = document.getElementById('editSupplier');
    const checkboxes = Array.from(document.querySelectorAll('#editSuppliersList input.supplier-checkbox:checked'));

    const selectedNames = checkboxes.map(cb => cb.dataset.name);

    if (hiddenInput) {
        hiddenInput.value = selectedNames.join(', ');
    }

    if (!displaySpan || !button) return;

    if (selectedNames.length === 0) {
        displaySpan.textContent = 'Select suppliers...';
        displaySpan.className = 'truncate text-slate-400';
        button.title = '';
    } else if (selectedNames.length === 1) {
        displaySpan.textContent = selectedNames[0];
        displaySpan.className = 'truncate text-slate-900 font-medium';
        button.title = selectedNames[0];
    } else if (selectedNames.length === 2) {
        displaySpan.textContent = selectedNames.join(', ');
        displaySpan.className = 'truncate text-slate-900 font-medium';
        button.title = selectedNames.join(', ');
    } else {
        displaySpan.textContent = `${selectedNames.length} suppliers selected`;
        displaySpan.className = 'truncate text-slate-900 font-medium';
        button.title = selectedNames.join(', ');
    }
}

function renderEditSuppliersDropdown(selectedIds = []) {
    const listContainer = document.getElementById('editSuppliersList');
    if (!listContainer) return;

    const suppliers = window.AllStocks?.suppliers || [];
    if (!suppliers.length) {
        getAvailableSuppliers().then(fetched => {
            renderEditSuppliersDropdown(selectedIds);
        });
        listContainer.innerHTML = '<div class="p-3 text-left text-xs text-slate-500">Loading suppliers...</div>';
        return;
    }

    const selectedSet = new Set(selectedIds.map(id => String(id)));

    listContainer.innerHTML = suppliers.map(s => {
        const isChecked = selectedSet.has(String(s.id));
        return `
            <label class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-800 hover:bg-slate-100 rounded-[8px] cursor-pointer select-none transition">
                <input type="checkbox" value="${s.id}" data-name="${s.name}" class="supplier-checkbox rounded border-slate-300 text-cyan-500 focus:ring-cyan-400/20 w-4 h-4 cursor-pointer accent-[#00fff2]" ${isChecked ? 'checked' : ''} />
                <span class="truncate font-medium text-slate-900">${s.name}</span>
            </label>
        `;
    }).join('');

    listContainer.querySelectorAll('input.supplier-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            updateEditSuppliersDisplay();
        });
    });

    updateEditSuppliersDisplay();
}

function toggleEditSuppliersDropdown(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const dropdown = document.getElementById('editSuppliersDropdown');
    const arrow = document.getElementById('editSuppliersArrow');
    if (!dropdown) return;

    document.querySelectorAll('#editProductModal .dropdown-menu').forEach(d => {
        if (d !== dropdown) d.classList.add('hidden');
    });

    const isOpening = dropdown.classList.contains('hidden');
    if (isOpening) {
        dropdown.classList.remove('hidden');
        if (arrow) arrow.classList.add('rotate-180');
    } else {
        dropdown.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }
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

    // Suppliers population
    let initialSupplierIds = [];
    if (product.suppliers && Array.isArray(product.suppliers) && product.suppliers.length > 0) {
        initialSupplierIds = product.suppliers.map(s => s.id);
    } else if (product.supplier_name) {
        const names = product.supplier_name.split(',').map(n => n.trim().toLowerCase());
        const available = window.AllStocks?.suppliers || [];
        initialSupplierIds = available
            .filter(s => names.includes(s.name.trim().toLowerCase()))
            .map(s => s.id);
    }

    currentEditProduct._supplier_ids = initialSupplierIds.slice();
    renderEditSuppliersDropdown(initialSupplierIds);

    document.getElementById('editCategory').value = product.category || '';
    document.getElementById('editLastRestock').value = product.last_restock_date ? String(product.last_restock_date).split('T')[0] : '';
    document.getElementById('editExpiryDate').value = product.expiry_date ? String(product.expiry_date).split('T')[0] : '';

    // Sync custom category dropdown button text
    const categoryMap = {
        'engine_oil': 'Engine Oil',
        'lubricants': 'Lubricants',
        'battery': 'Battery',
        'spark_plug': 'Spark Plug',
        'brake_pads': 'Brake Pads',
        'tires': 'Tires',
        'filters': 'Filters',
        'accessories': 'Accessories'
    };
    const catBtn = document.getElementById('editCategoryButton');
    if (catBtn) {
        const catSpan = catBtn.querySelector('span');
        if (catSpan) {
            catSpan.textContent = categoryMap[product.category] || product.category || 'Select Category';
        }
    }
    document.getElementById('editReorderLevel').value = product.reorder_level ?? 0;
    document.getElementById('editBarcode').value = product.barcode || '';
    document.getElementById('editDescription').value = product.description || '';
    clearEditProductErrors();
}

// Movements pagination
let movementsData = [];
let movementsPage = 1;
let movementsPerPage = 10;
let movementsDateFilter = '';

// Product pagination
let currentPage = 1;
let perPage = 10;

// Modal functions
function openEditProductModal(productId) {
    const modal = document.getElementById('editProductModal');
    if (!modal) {
        alert('Edit modal not found');
        return;
    }

    modal.classList.remove('hidden');
    modal.style.display = 'flex';

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
                alert('Product not found');
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            alert('Error loading product data');
        });
}

function closeEditProductModal() {
    const modal = document.getElementById('editProductModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    const form = document.getElementById('editProductForm');
    if (form) {
        form.reset();
        clearEditProductErrors();
    }
    const suppliersDropdown = document.getElementById('editSuppliersDropdown');
    if (suppliersDropdown) suppliersDropdown.classList.add('hidden');
    const arrow = document.getElementById('editSuppliersArrow');
    if (arrow) arrow.classList.remove('rotate-180');
    currentEditProduct = null;
}

// Make function globally accessible
window.openEditModal = function (productId) {
    openEditProductModal(productId);
};

async function archiveProduct(productId) {
    if (!confirm('Are you sure you want to archive this product? It will be hidden from the main inventory.')) {
        return;
    }

    try {
        const response = await fetch(`/api/product/${productId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.AllStocks.csrfToken,
                'Content-Type': 'application/json'
            }
        });
        const result = await response.json();

        if (result.success) {
            alert('Product archived successfully');
            loadStats();
            loadProducts(currentPage);
        } else {
            alert('Failed to archive product: ' + result.message);
        }
    } catch (error) {
        console.error('Error archiving product:', error);
        alert('Error archiving product');
    }
}
window.archiveProduct = archiveProduct;

// Auto-filter with debounce
function performSearch() {
    currentPage = 1;
    loadProducts(1);
}

function resetFilters() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    ['statusFilter', 'expiryStatusFilter', 'restockDateFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    currentFilters = { search: '', status: '', expiry_status: '', restock_date: '' };
    currentPage = 1;
    loadProducts(1);
}

// Attach UI events
function attachUIEvents() {
    const searchEl = document.getElementById('searchInput');
    if (searchEl) {
        searchEl.addEventListener('input', function (e) {
            currentFilters.search = e.target.value;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    const statusEl = document.getElementById('statusFilter');
    if (statusEl) statusEl.addEventListener('change', function (e) { currentFilters.status = e.target.value; performSearch(); });
    const expiryStatusEl = document.getElementById('expiryStatusFilter');
    if (expiryStatusEl) expiryStatusEl.addEventListener('change', function (e) { currentFilters.expiry_status = e.target.value; performSearch(); });
    const restockDateEl = document.getElementById('restockDateFilter');
    if (restockDateEl) restockDateEl.addEventListener('change', function (e) { currentFilters.restock_date = e.target.value; performSearch(); });

    // Edit product modal events
    const closeEdit = document.getElementById('closeEditProductModal');
    if (closeEdit) closeEdit.addEventListener('click', closeEditProductModal);
    const cancelEdit = document.getElementById('cancelEditProduct');
    if (cancelEdit) cancelEdit.addEventListener('click', closeEditProductModal);

    const editSuppliersBtn = document.getElementById('editSuppliersButton');
    if (editSuppliersBtn) editSuppliersBtn.addEventListener('click', toggleEditSuppliersDropdown);
    const editSuppliersDropdown = document.getElementById('editSuppliersDropdown');
    if (editSuppliersDropdown) editSuppliersDropdown.addEventListener('click', function (e) { e.stopPropagation(); });

    const editProductModal = document.getElementById('editProductModal');
    if (editProductModal) editProductModal.addEventListener('click', function (e) { if (e.target === this) closeEditProductModal(); });

    // Edit product form submission
    const editProductForm = document.getElementById('editProductForm');
    if (editProductForm) {
        editProductForm.addEventListener('submit', async function (e) {
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

                // Check if supplier_ids changed
                const currentCheckedSupplierIds = Array.from(
                    document.querySelectorAll('#editSuppliersList input.supplier-checkbox:checked')
                ).map(cb => parseInt(cb.value));

                const prevSupplierIds = (currentEditProduct?._supplier_ids || []).slice().map(Number).sort();
                const newSupplierIds = currentCheckedSupplierIds.slice().map(Number).sort();
                const suppliersChanged = JSON.stringify(prevSupplierIds) !== JSON.stringify(newSupplierIds);

                if (suppliersChanged) {
                    payload.supplier_ids = currentCheckedSupplierIds;
                }

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
                    alert(result.message || 'Product updated successfully.');
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
                    alert(result.message || 'Failed to update product');
                }
            } catch (error) {
                console.error('Error updating product:', error);
                alert('Error updating product: ' + error.message);
            } finally {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Save Changes'; }
            }
        });
    }

    // Wire select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Wire movement date filter
    const movementDateFilter = document.getElementById('movementDateFilter');
    if (movementDateFilter) {
        movementDateFilter.addEventListener('change', function () {
            movementsDateFilter = this.value;
            movementsPage = 1;
            renderMovements();
        });
    }

    // Wire movement pagination buttons
    const movementPrevBtn = document.querySelector('.movement-prev');
    const movementNextBtn = document.querySelector('.movement-next');

    if (movementPrevBtn) {
        movementPrevBtn.addEventListener('click', function () {
            if (movementsPage > 1) {
                movementsPage--;
                renderMovements();
            }
        });
    }

    if (movementNextBtn) {
        movementNextBtn.addEventListener('click', function () {
            const totalPages = Math.ceil(movementsData.length / movementsPerPage);
            if (movementsPage < totalPages) {
                movementsPage++;
                renderMovements();
            }
        });
    }

    // Wire refresh movements button
    const refreshMovementsBtn = document.getElementById('refreshMovementsBtn');
    if (refreshMovementsBtn) {
        refreshMovementsBtn.addEventListener('click', () => {
            loadMovements();
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
        const activeItemsEl = document.getElementById('stat-active-items');
        const lowStockEl = document.getElementById('stat-low-stock');
        const outOfStockEl = document.getElementById('stat-out-of-stock');
        const expiringSoonEl = document.getElementById('stat-expiring-soon');
        const expiredEl = document.getElementById('stat-expired');

        if (totalProductsEl) totalProductsEl.textContent = Number(stats.total_items || 0).toLocaleString();
        if (activeItemsEl) activeItemsEl.textContent = Number(stats.active_items || 0).toLocaleString();
        if (lowStockEl) lowStockEl.textContent = Number(stats.low_stock_count || 0).toLocaleString();
        if (outOfStockEl) outOfStockEl.textContent = Number(stats.out_of_stock_count || 0).toLocaleString();
        if (expiringSoonEl) expiringSoonEl.textContent = Number(stats.expiring_soon_count || 0).toLocaleString();
        if (expiredEl) expiredEl.textContent = Number(stats.expired_count || 0).toLocaleString();
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

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

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
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

            switch (movementsDateFilter) {
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
            row.className = 'hover:bg-slate-50/80 transition-colors border-b border-slate-100/80';
            const prodName = entry.product_name || '-';
            const detailStr = entry.notes || entry.supplier_name || '-';
            row.innerHTML = `
                <td class="w-[18%] px-3 py-2 text-slate-600 align-middle truncate">${escapeHtml(entry.created_at)}</td>
                <td class="w-[32%] px-3 py-2 text-slate-700 align-middle overflow-hidden">
                    <div class="flex items-center gap-2 min-w-0" title="${escapeHtml(prodName)}">
                        ${renderMonitoringProductImageHtml(entry)}
                        <div class="min-w-0 flex-1 overflow-hidden">
                            <div class="font-medium text-slate-900 truncate">${escapeHtml(prodName)}</div>
                            ${entry.sku ? `<div class="text-[10px] text-slate-400 font-mono truncate">${escapeHtml(entry.sku)}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td class="w-[15%] px-3 py-2 text-slate-700 capitalize align-middle font-medium overflow-hidden">
                    <span class="truncate block">${escapeHtml(entry.type ? entry.type.replace(/_/g, ' ') : '-')}</span>
                </td>
                <td class="w-[10%] px-3 py-2 text-center text-slate-900 font-semibold align-middle">${entry.quantity_change ?? '-'}</td>
                <td class="w-[25%] px-3 py-2 text-slate-600 align-middle overflow-hidden">
                    <span class="truncate block" title="${escapeHtml(detailStr)}">${escapeHtml(detailStr)}</span>
                </td>
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

// Load products from API
async function loadProducts(page = 1) {
    try {
        let url = window.AllStocks.routes.apiProducts + `?page=${page}&per_page=${perPage}`;

        if (currentFilters.search) url += `&search=${encodeURIComponent(currentFilters.search)}`;
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
                row.className = 'hover:bg-slate-50/80 transition-colors border-b border-slate-100/80';
                
                const compatName = product.name || '-';
                const prodName = product.product_name || 'Uncategorized';
                const skuStr = `KCC_${(product.sku || product.name || '').replace(/[^A-Za-z0-9\-\+]/g, '')}`;
                const brandStr = product.brand || '-';
                const sizeStr = product.size || '-';
                const colorStr = product.color || '-';
                const supplierStr = product.supplier_name || '-';
                const expiryStr = product.expiry_date ? `${formatDate(product.expiry_date)}` : 'Non-expiring';
                const stockQty = product.stock_quantity ?? 0;
                const priceFormatted = `₱${parseFloat(product.unit_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                row.innerHTML = `
                    <td class="w-[3.5%] px-2 py-2.5 text-center align-middle">
                        <input type="checkbox" class="product-checkbox rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" data-product-id="${product.id}" />
                    </td>
                    <td class="w-[15%] px-2.5 py-2.5 text-left align-middle overflow-hidden">
                        <div class="flex items-center gap-2 min-w-0" title="${escapeHtml(compatName)}">
                            ${renderMonitoringProductImageHtml(product)}
                            <span class="truncate font-semibold text-slate-900 text-[11px]">${escapeHtml(compatName)}</span>
                        </div>
                    </td>
                    <td class="w-[10%] px-2 py-2.5 text-left text-slate-600 align-middle overflow-hidden">
                        <span class="truncate block text-[11px]" title="${escapeHtml(prodName)}">${escapeHtml(prodName)}</span>
                    </td>
                    <td class="w-[12%] px-2 py-2.5 text-left text-slate-600 font-mono text-[10px] align-middle overflow-hidden">
                        <span class="truncate block" title="${escapeHtml(skuStr)}">${escapeHtml(skuStr)}</span>
                    </td>
                    <td class="w-[8%] px-2 py-2.5 text-left text-slate-600 align-middle overflow-hidden">
                        <span class="truncate block text-[11px]" title="${escapeHtml(brandStr)}">${escapeHtml(brandStr)}</span>
                    </td>
                    <td class="w-[5%] px-1.5 py-2.5 text-center text-slate-600 align-middle overflow-hidden">
                        <span class="truncate block text-[11px]" title="${escapeHtml(sizeStr)}">${escapeHtml(sizeStr)}</span>
                    </td>
                    <td class="w-[5%] px-1.5 py-2.5 text-center text-slate-600 align-middle overflow-hidden">
                        <span class="truncate block text-[11px]" title="${escapeHtml(colorStr)}">${escapeHtml(colorStr)}</span>
                    </td>
                    <td class="w-[6%] px-2 py-2.5 text-center font-bold text-[11px] align-middle ${stockQty === 0 ? 'text-red-600' : (stockQty <= 10 ? 'text-amber-600' : 'text-slate-900')}">
                        ${stockQty}
                    </td>
                    <td class="w-[9%] px-2 py-2.5 text-right font-semibold text-slate-900 text-[11px] align-middle">
                        ${priceFormatted}
                    </td>
                    <td class="w-[11%] px-2 py-2.5 text-left text-slate-600 align-middle overflow-hidden">
                        <span class="truncate block text-[11px]" title="${escapeHtml(supplierStr)}">${escapeHtml(supplierStr)}</span>
                    </td>
                    <td class="w-[8.5%] px-2 py-2.5 text-left text-slate-600 text-[11px] align-middle overflow-hidden">
                        <span class="truncate block">${formatDate(product.last_restock_date)}</span>
                    </td>
                    <td class="w-[9%] px-2 py-2.5 text-left text-slate-600 text-[11px] align-middle overflow-hidden">
                        <span class="truncate block ${product.expiry_date && new Date(product.expiry_date) < new Date() ? 'text-red-600 font-semibold' : ''}" title="${escapeHtml(expiryStr)}">${escapeHtml(expiryStr)}</span>
                    </td>
                    <td class="w-[8%] px-2 py-2.5 text-center align-middle text-[10px] font-medium" onclick="event.stopPropagation()">
                        <div class="inline-flex items-center gap-1 justify-center">
                            <button type="button" onclick="event.stopPropagation(); openEditModal(${product.id});" class="text-slate-700 hover:text-black p-1 rounded hover:bg-slate-100 transition" title="Edit Product">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); archiveProduct(${product.id});" class="rounded-[6px] border border-slate-200 px-1.5 py-0.5 text-[9.5px] font-semibold transition-all bg-white text-slate-700 hover:bg-slate-100 hover:text-red-600">Archive</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="13" class="px-4 py-8 text-center text-slate-500">No products found</td></tr>';
        }

        currentPage = page;
        updatePagination(result.pagination);
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

    const totalPages = pagination.last_page;
    let html = `<button type="button" onclick="loadProducts(${Math.max(1, currentPage - 1)})" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage <= 1 ? 'disabled' : ''}>← Prev</button>`;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
    startPage = Math.max(1, startPage);

    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `<button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${i}</button>`;
        } else {
            html += `<button type="button" onclick="loadProducts(${i})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50">${i}</button>`;
        }
    }

    html += `<button type="button" onclick="loadProducts(${Math.min(totalPages, currentPage + 1)})" class="rounded-[10px] border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed" ${currentPage >= totalPages ? 'disabled' : ''}>Next →</button>`;

    paginationContainer.innerHTML = html;

    const summary = document.getElementById('paginationInfo');
    if (summary) {
        summary.textContent = `Showing ${pagination.from || 0}-${pagination.to || 0} of ${pagination.total?.toLocaleString() || 0} items`;
    }
}

// Initialize when DOM is ready
function initializeMonitoringPage() {
    attachUIEvents();

    // Load initial data
    loadStats();
    loadProducts(1);
    loadMovements();
    getAvailableSuppliers().then(() => renderEditSuppliersDropdown());
}

// Expose key functions to global scope for onclick handlers
window.loadProducts = loadProducts;
window.performSearch = performSearch;
window.resetFilters = resetFilters;
window.openEditModal = openEditModal;
window.toggleEditSuppliersDropdown = toggleEditSuppliersDropdown;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMonitoringPage);
} else {
    initializeMonitoringPage();
}
