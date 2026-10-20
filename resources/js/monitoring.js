// monitoring.js - Consolidated Inventory Monitoring & All Stocks
// Expects global window.AllStocks with routes and csrfToken set in the blade view.

let allProducts = [];
let currentFilters = {
    search: '',
    warehouse: '',
    category: '',
    product_name: '',
    brand: '',
    size: '',
    status: '',
    expiry_status: '',
    date_of_stock: '',
    restock_date: ''
};
let searchTimeout;
let currentEditProduct = null;
let currentSelectedProducts = [];
let currentPage = 1;
let perPage = 10;

// Movements data and pagination
let movementsData = [];
let movementsPage = 1;
let movementsPerPage = 10;
let movementsDateFilter = '';

// Date formatters
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

function formatDateWithTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';

    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const year = String(date.getFullYear()).slice(-2);
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${month}/${day}/${year} <span class="text-slate-400">${hours}:${minutes}</span>`;
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

// Product image resolution
function getMonitoringProductImage(p) {
    if (!p) return null;
    if (window.resolveProductImage) {
        const resolved = window.resolveProductImage(p);
        if (resolved) return resolved;
    }
    if (p.image) return p.image;
    try {
        const stored = localStorage.getItem('posProductImages');
        if (stored) {
            const images = JSON.parse(stored);
            const productId = p.id || p.product_id;
            if (productId && images[productId]) return images[productId];
            if (p.sku && images[p.sku]) return images[p.sku];
            if (p.name && images[p.name]) return images[p.name];
            if (p.product_name && images[p.product_name]) return images[p.product_name];

            const keys = Object.keys(images);
            if (p.sku) {
                const matchSku = keys.find(k => k.toLowerCase() === String(p.sku).toLowerCase());
                if (matchSku) return images[matchSku];
            }
            if (p.name) {
                const matchName = keys.find(k => k.toLowerCase() === String(p.name).toLowerCase());
                if (matchName) return images[matchName];
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

// Warehouse badge and location summary
function getWarehouseBadge(warehouse) {
    const isShop = String(warehouse).toLowerCase() === 'shop';
    const colorClasses = isShop
        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
        : 'bg-slate-100 text-slate-800 border-slate-200';
    return `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold ${colorClasses} border">${escapeHtml(warehouse)}</span>`;
}

function getLocationSummary(product) {
    let locations = [];
    if (product.warehouse_locations && Array.isArray(product.warehouse_locations) && product.warehouse_locations.length > 0) {
        locations = product.warehouse_locations;
    } else if (product.warehouse) {
        locations = [product.warehouse];
    }

    if (locations.length === 0) {
        return getWarehouseBadge('Shop');
    }

    const uniqueLocations = [...new Set(locations)];
    return uniqueLocations.map(wh => getWarehouseBadge(wh)).join(' ');
}

// Toast helper
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast || !toastMessage) {
        console.log(`[Toast ${type}]: ${message}`);
        return;
    }

    toastMessage.textContent = message;

    const toastInner = toast.querySelector('div');
    if (toastInner) {
        const svg = toastInner.querySelector('svg');
        if (type === 'error') {
            toastInner.classList.remove('border-emerald-500');
            toastInner.classList.add('border-red-500');
            if (svg) {
                svg.classList.remove('text-emerald-500');
                svg.classList.add('text-red-500');
            }
        } else {
            toastInner.classList.remove('border-red-500');
            toastInner.classList.add('border-emerald-500');
            if (svg) {
                svg.classList.remove('text-red-500');
                svg.classList.add('text-emerald-500');
            }
        }
    }

    toast.classList.remove('translate-x-full');
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 3000);
}

// Edit field validation helpers
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

// Available suppliers handling
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
        getAvailableSuppliers().then(() => {
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
                <input type="checkbox" value="${s.id}" data-name="${escapeHtml(s.name)}" class="supplier-checkbox rounded border-slate-300 text-cyan-500 focus:ring-cyan-400/20 w-4 h-4 cursor-pointer accent-[#6EC1D1]" ${isChecked ? 'checked' : ''} />
                <span class="truncate font-medium text-slate-900">${escapeHtml(s.name)}</span>
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

    const categoryVal = product.category || '';
    document.getElementById('editCategory').value = categoryVal;
    const catBtn = document.getElementById('editCategoryButton');
    if (catBtn) {
        const catSpan = catBtn.querySelector('span');
        if (catSpan) {
            catSpan.textContent = categoryVal || 'Select category';
        }
    }

    document.getElementById('editLastRestock').value = product.last_restock_date ? String(product.last_restock_date).split('T')[0] : '';
    document.getElementById('editExpiryDate').value = product.expiry_date ? String(product.expiry_date).split('T')[0] : '';
    document.getElementById('editReorderLevel').value = product.reorder_level ?? 10;
    document.getElementById('editBarcode').value = product.barcode || '';
    document.getElementById('editDescription').value = product.description || '';
    clearEditProductErrors();
}

// Modal functions
function openAddStockModal() {
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
    const modal = document.getElementById('addStockModal');
    if (modal) modal.classList.remove('hidden');
    loadProductsForSelect();
}

function closeAddStockModal() {
    const modal = document.getElementById('addStockModal');
    if (modal) modal.classList.add('hidden');
    const form = document.getElementById('addStockForm');
    if (form) form.reset();
    const display = document.getElementById('addStockProductDisplay');
    if (display) display.textContent = 'Select a product...';
    const dropdown = document.getElementById('addStockProductDropdown');
    if (dropdown) dropdown.classList.add('hidden');
}

function openEditProductModal(productId) {
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
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
    if (modal) modal.classList.add('hidden');
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

// Make modal functions globally accessible
window.openAddStockModal = openAddStockModal;
window.closeAddStockModal = closeAddStockModal;
window.openEditModal = openEditProductModal;
window.closeEditProductModal = closeEditProductModal;

// Archive product function
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
            showToast('Product archived successfully', 'success');
            loadStats();
            loadProducts(currentPage);
        } else {
            showToast('Failed to archive product: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error archiving product:', error);
        showToast('Error archiving product', 'error');
    }
}
window.archiveProduct = archiveProduct;

// Dropdown option select helper
window.selectDropdownOption = function (inputId, value, text, dropdownId, event) {
    if (event) {
        if (event.preventDefault) event.preventDefault();
        if (event.stopPropagation) event.stopPropagation();
    }
    const input = document.getElementById(inputId);
    if (input) {
        input.value = value;
        const wrapper = input.closest('[data-dropdown-wrapper]');
        if (wrapper) {
            const btnSpan = wrapper.querySelector('button span');
            if (btnSpan) {
                btnSpan.textContent = text;
            }
        }
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    const display = document.getElementById('addStockProductDisplay');
    if (display && inputId === 'productSelect') {
        display.textContent = text;
    }
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.add('hidden');
        const wrapper = dropdown.closest('[data-dropdown-wrapper]');
        if (wrapper) wrapper.style.zIndex = '';
    }
};

// Load products for Add Stock select dropdown
async function loadProductsForSelect() {
    try {
        const response = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const result = await response.json();

        const select = document.getElementById('productSelect');
        const dropdown = document.getElementById('addStockProductDropdown');

        if (select) select.innerHTML = '<option value="">Select a product...</option>';

        if (dropdown) {
            dropdown.innerHTML = '';

            if (result.data && result.data.length > 0) {
                result.data.forEach(product => {
                    const prodName = product.product_name || product.name || 'Unnamed Product';
                    const label = `${prodName} (${product.sku || 'No SKU'})`;

                    if (select) {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = label;
                        select.appendChild(option);
                    }

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full px-3 py-1.5 text-left text-xs font-medium text-slate-700 hover:bg-slate-100 rounded-[8px] transition cursor-pointer';
                    btn.textContent = label;
                    btn.onclick = (e) => selectDropdownOption('productSelect', product.id, label, 'addStockProductDropdown', e);
                    dropdown.appendChild(btn);
                });
            } else {
                dropdown.innerHTML = '<div class="p-3 text-left text-xs text-slate-500">No products available</div>';
            }
        }
    } catch (error) {
        console.error('Error loading products for select:', error);
    }
}

// Load filter options (Product Name, Brand, Size)
async function loadFilterOptions() {
    try {
        let productNames = new Set();
        let brands = new Set();
        let sizes = new Set();

        window.productNameToBrands = {};

        if (window.AllStocks?.routes?.apiProductDescriptions) {
            try {
                const descriptionsResponse = await fetch(window.AllStocks.routes.apiProductDescriptions);
                const descriptionsResult = await descriptionsResponse.json();
                if (descriptionsResult && Array.isArray(descriptionsResult)) {
                    descriptionsResult.forEach(description => {
                        if (description.name) {
                            window.productNameToBrands[description.name] = new Set(description.brands || []);
                            (description.brands || []).forEach(brand => { if (brand) brands.add(brand); });
                            productNames.add(description.name);
                        }
                    });
                }
            } catch (e) {
                console.warn('Could not load product descriptions:', e);
            }
        }

        const productsResponse = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const productsResult = await productsResponse.json();

        if (productsResult.data && productsResult.data.length > 0) {
            productsResult.data.forEach(product => {
                if (product.size) sizes.add(product.size);
                if (product.brand) brands.add(product.brand);
                const name = product.product_name || product.name;
                if (name) productNames.add(name);
            });
        }

        window.allAvailableBrands = Array.from(brands);

        // Populate Product Name dropdown
        const productNameDropdown = document.getElementById('productNameFilterDropdown');
        if (productNameDropdown) {
            productNameDropdown.innerHTML = '';
            const defaultBtn = document.createElement('button');
            defaultBtn.type = 'button';
            defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
            defaultBtn.textContent = 'All Products';
            defaultBtn.onclick = (e) => selectDropdownOption('productNameFilter', '', 'All Products', 'productNameFilterDropdown', e);
            productNameDropdown.appendChild(defaultBtn);

            Array.from(productNames).sort().forEach(name => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
                btn.textContent = name;
                btn.onclick = (e) => selectDropdownOption('productNameFilter', name, name, 'productNameFilterDropdown', e);
                productNameDropdown.appendChild(btn);
            });
        }

        // Populate Brand dropdown
        const brandDropdown = document.getElementById('brandFilterDropdown');
        if (brandDropdown) {
            brandDropdown.innerHTML = '';
            const defaultBtn = document.createElement('button');
            defaultBtn.type = 'button';
            defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
            defaultBtn.textContent = 'All Brands';
            defaultBtn.onclick = (e) => selectDropdownOption('brandFilter', '', 'All Brands', 'brandFilterDropdown', e);
            brandDropdown.appendChild(defaultBtn);

            Array.from(brands).sort().forEach(brand => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
                btn.textContent = brand;
                btn.onclick = (e) => selectDropdownOption('brandFilter', brand, brand, 'brandFilterDropdown', e);
                brandDropdown.appendChild(btn);
            });
        }

        // Populate Size dropdown
        const sizeDropdown = document.getElementById('sizeFilterDropdown');
        if (sizeDropdown) {
            sizeDropdown.innerHTML = '';
            const defaultBtn = document.createElement('button');
            defaultBtn.type = 'button';
            defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
            defaultBtn.textContent = 'All Sizes';
            defaultBtn.onclick = (e) => selectDropdownOption('sizeFilter', '', 'All Sizes', 'sizeFilterDropdown', e);
            sizeDropdown.appendChild(defaultBtn);

            Array.from(sizes).sort().forEach(size => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
                btn.textContent = size;
                btn.onclick = (e) => selectDropdownOption('sizeFilter', size, size, 'sizeFilterDropdown', e);
                sizeDropdown.appendChild(btn);
            });
        }
    } catch (error) {
        console.error('Error loading filter options:', error);
    }
}

function updateBrandDropdown() {
    const productNameFilter = document.getElementById('productNameFilter');
    const brandDropdown = document.getElementById('brandFilterDropdown');

    if (!brandDropdown) return;

    const selectedProductName = productNameFilter ? productNameFilter.value : '';
    let brandsToShow = [];

    if (selectedProductName && window.productNameToBrands && window.productNameToBrands[selectedProductName] && window.productNameToBrands[selectedProductName].size > 0) {
        brandsToShow = Array.from(window.productNameToBrands[selectedProductName]);
    } else if (window.allAvailableBrands && window.allAvailableBrands.length > 0) {
        brandsToShow = Array.from(window.allAvailableBrands);
    }

    if (brandsToShow.length > 0) {
        brandDropdown.innerHTML = '';

        const defaultBtn = document.createElement('button');
        defaultBtn.type = 'button';
        defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
        defaultBtn.textContent = 'All Brands';
        defaultBtn.onclick = (e) => selectDropdownOption('brandFilter', '', 'All Brands', 'brandFilterDropdown', e);
        brandDropdown.appendChild(defaultBtn);

        brandsToShow.sort().forEach(brand => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition cursor-pointer';
            btn.textContent = brand;
            btn.onclick = (e) => selectDropdownOption('brandFilter', brand, brand, 'brandFilterDropdown', e);
            brandDropdown.appendChild(btn);
        });
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

    const filterDefaults = {
        'warehouseFilter': 'All Locations',
        'categoryFilter': 'All Categories',
        'productNameFilter': 'All Products',
        'brandFilter': 'All Brands',
        'statusFilter': 'All Status',
        'expiryStatusFilter': 'All Expiry',
        'sizeFilter': 'All Sizes',
        'dateOfStockFilter': '',
        'restockDateFilter': ''
    };

    Object.entries(filterDefaults).forEach(([id, defaultText]) => {
        const el = document.getElementById(id);
        if (el) {
            el.value = '';
            const wrapper = el.closest('[data-dropdown-wrapper]');
            if (wrapper) {
                const btnSpan = wrapper.querySelector('button span');
                if (btnSpan) btnSpan.textContent = defaultText;
            }
        }
    });

    currentFilters = {
        search: '',
        warehouse: '',
        category: '',
        product_name: '',
        brand: '',
        size: '',
        status: '',
        expiry_status: '',
        date_of_stock: '',
        restock_date: ''
    };

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

    const warehouseEl = document.getElementById('warehouseFilter');
    if (warehouseEl) warehouseEl.addEventListener('change', function (e) { currentFilters.warehouse = e.target.value; performSearch(); });

    const categoryEl = document.getElementById('categoryFilter');
    if (categoryEl) categoryEl.addEventListener('change', function (e) { currentFilters.category = e.target.value; performSearch(); });

    const productNameEl = document.getElementById('productNameFilter');
    if (productNameEl) productNameEl.addEventListener('change', function (e) {
        updateBrandDropdown();
        currentFilters.product_name = e.target.value;
        currentFilters.brand = '';
        const brandInput = document.getElementById('brandFilter');
        if (brandInput) {
            brandInput.value = '';
            const wrapper = brandInput.closest('[data-dropdown-wrapper]');
            if (wrapper) {
                const span = wrapper.querySelector('button span');
                if (span) span.textContent = 'All Brands';
            }
        }
        performSearch();
    });

    const brandEl = document.getElementById('brandFilter');
    if (brandEl) brandEl.addEventListener('change', function (e) { currentFilters.brand = e.target.value; performSearch(); });

    const sizeEl = document.getElementById('sizeFilter');
    if (sizeEl) sizeEl.addEventListener('change', function (e) { currentFilters.size = e.target.value; performSearch(); });

    const statusEl = document.getElementById('statusFilter');
    if (statusEl) statusEl.addEventListener('change', function (e) { currentFilters.status = e.target.value; performSearch(); });

    const expiryStatusEl = document.getElementById('expiryStatusFilter');
    if (expiryStatusEl) expiryStatusEl.addEventListener('change', function (e) { currentFilters.expiry_status = e.target.value; performSearch(); });

    const dateOfStockEl = document.getElementById('dateOfStockFilter');
    if (dateOfStockEl) dateOfStockEl.addEventListener('change', function (e) { currentFilters.date_of_stock = e.target.value; performSearch(); });

    const restockDateEl = document.getElementById('restockDateFilter');
    if (restockDateEl) restockDateEl.addEventListener('change', function (e) { currentFilters.restock_date = e.target.value; performSearch(); });

    // Add Stock button
    const addStockBtn = document.getElementById('addStockBtn');
    if (addStockBtn) addStockBtn.addEventListener('click', openAddStockModal);

    // Add stock form submission
    const addStockForm = document.getElementById('addStockForm');
    if (addStockForm) {
        addStockForm.addEventListener('submit', async function (e) {
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
                    showToast('Stock added successfully!', 'success');
                    closeAddStockModal();
                    loadStats();
                    loadProducts(currentPage);
                    loadMovements();
                } else {
                    alert('Failed to add stock: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error adding stock:', error);
                alert('Error adding stock: ' + error.message);
            } finally {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Add Stock'; }
            }
        });
    }

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
                    showToast(result.message || 'Product updated successfully.', 'success');
                    closeEditProductModal();
                    loadStats();
                    loadProducts(currentPage);
                    loadMovements();
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

    // Edit suppliers dropdown triggers
    const editSuppliersBtn = document.getElementById('editSuppliersButton');
    if (editSuppliersBtn) editSuppliersBtn.addEventListener('click', toggleEditSuppliersDropdown);
    const editSuppliersDropdown = document.getElementById('editSuppliersDropdown');
    if (editSuppliersDropdown) editSuppliersDropdown.addEventListener('click', function (e) { e.stopPropagation(); });

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) exportBtn.addEventListener('click', exportProducts);

    // QR Code button
    const generateQrBtn = document.getElementById('generateQrBtn');
    if (generateQrBtn) generateQrBtn.addEventListener('click', openQRCodeModal);

    // Movement date filter
    const movementDateFilter = document.getElementById('movementDateFilter');
    if (movementDateFilter) {
        movementDateFilter.addEventListener('change', function () {
            movementsDateFilter = this.value;
            movementsPage = 1;
            renderMovements();
        });
    }

    // Movement pagination buttons
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

    // Refresh movements button
    const refreshMovementsBtn = document.getElementById('refreshMovementsBtn');
    if (refreshMovementsBtn) {
        refreshMovementsBtn.addEventListener('click', () => {
            loadMovements();
        });
    }

    // Add Product Description Modal functionality
    const addNewProductDescBtn = document.getElementById('addNewProductDescBtn');
    const addProductDescModal = document.getElementById('addProductDescModal');
    const addProductDescForm = document.getElementById('addProductDescForm');
    const newProductDescName = document.getElementById('newProductDescName');
    const newProductDescBrand = document.getElementById('newProductDescBrand');

    if (addNewProductDescBtn && addProductDescModal) {
        addNewProductDescBtn.addEventListener('click', function () {
            addProductDescModal.classList.remove('hidden');
            if (newProductDescName) newProductDescName.focus();
        });

        if (addProductDescForm) {
            addProductDescForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const name = newProductDescName.value.trim();
                const brand = newProductDescBrand.value.trim();

                if (!name || !brand) {
                    alert('Please fill in all fields');
                    return;
                }

                try {
                    const formData = new FormData();
                    formData.append('_token', window.AllStocks.csrfToken);
                    formData.append('name', name);
                    formData.append('brand', brand);

                    const response = await fetch('/product-descriptions', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Product category added successfully!', 'success');
                        addProductDescModal.classList.add('hidden');
                        addProductDescForm.reset();
                        loadFilterOptions();
                    } else {
                        alert(data.error || 'Failed to add product category');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to add product category. Please try again.');
                }
            });
        }
    }
}

// Bulk Actions Toolbar
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    const toolbar = document.getElementById('bulkActionsToolbar');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        if (toolbar) toolbar.classList.remove('hidden');
        if (selectedCount) selectedCount.textContent = checkboxes.length;
    } else {
        if (toolbar) toolbar.classList.add('hidden');
        if (selectedCount) selectedCount.textContent = '0';
    }
}

function clearSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const selectAll = document.getElementById('selectAllCheckbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    if (selectAll) selectAll.checked = false;
    updateBulkActions();
}
window.clearSelection = clearSelection;

// Load stats from API
async function loadStats() {
    try {
        const response = await fetch(window.AllStocks.routes.apiStats);
        if (!response.ok) return;

        const stats = await response.json();

        const totalProductsEl = document.getElementById('stat-total-products');
        const totalValueEl = document.getElementById('stat-total-value');
        const lowStockEl = document.getElementById('stat-low-stock');
        const expiringSoonEl = document.getElementById('stat-expiring-soon');

        if (totalProductsEl) totalProductsEl.textContent = Number(stats.total_items || 0).toLocaleString();
        if (totalValueEl) totalValueEl.textContent = '₱' + Number(stats.total_value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (lowStockEl) lowStockEl.textContent = Number(stats.low_stock_count || 0).toLocaleString();
        if (expiringSoonEl) expiringSoonEl.textContent = Number(stats.expiring_soon_count || 0).toLocaleString();
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load movements from API
async function loadMovements() {
    try {
        if (!window.AllStocks.routes.apiMovements) return;

        const response = await fetch(window.AllStocks.routes.apiMovements);
        const result = await response.json();

        movementsData = result.data || [];
        renderMovements();
    } catch (error) {
        console.error('Error loading movements:', error);
    }
}

window.goToMovementPage = function (page) {
    movementsPage = page;
    renderMovements();
};

function renderMovements() {
    const tbody = document.getElementById('movementFeed');
    const paginationInfo = document.getElementById('movementPaginationInfo');

    if (!tbody) return;

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
                case 'today': return entryDate === today;
                case 'yesterday': return entryDate === yesterdayStr;
                case 'last_7_days': return entryDate >= last7DaysStr && entryDate <= today;
                case 'last_30_days': return entryDate >= last30DaysStr && entryDate <= today;
                default: return true;
            }
        });
    }

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

    const totalMovements = filteredMovements.length;
    if (paginationInfo) {
        if (totalMovements === 0) {
            paginationInfo.textContent = 'Showing 0 of 0 movements';
        } else {
            paginationInfo.textContent = `Showing ${startIndex + 1}-${Math.min(endIndex, totalMovements)} of ${totalMovements} movements`;
        }
    }

    const paginationControls = document.getElementById('movementPaginationControls');
    if (paginationControls) {
        let html = `<button type="button" onclick="goToMovementPage(${movementsPage - 1})" class="movement-prev rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer" ${movementsPage <= 1 ? 'disabled' : ''}>← Prev</button>`;

        let startPage = Math.max(1, movementsPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
        startPage = Math.max(1, startPage);

        for (let i = startPage; i <= endPage; i++) {
            if (i === movementsPage) {
                html += `<button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${i}</button>`;
            } else {
                html += `<button type="button" onclick="goToMovementPage(${i})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">${i}</button>`;
            }
        }

        html += `<button type="button" onclick="goToMovementPage(${movementsPage + 1})" class="movement-next rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer" ${movementsPage >= totalPages ? 'disabled' : ''}>Next →</button>`;
        paginationControls.innerHTML = html;
    }
}

// Load products from API
async function loadProducts(page = 1) {
    try {
        let url = window.AllStocks.routes.apiProducts + `?page=${page}&per_page=${perPage}`;

        if (currentFilters.search) url += `&search=${encodeURIComponent(currentFilters.search)}`;
        if (currentFilters.warehouse) url += `&warehouse=${encodeURIComponent(currentFilters.warehouse)}`;
        if (currentFilters.category) url += `&category=${encodeURIComponent(currentFilters.category)}`;
        if (currentFilters.product_name) url += `&product_name=${encodeURIComponent(currentFilters.product_name)}`;
        if (currentFilters.brand) url += `&brand=${encodeURIComponent(currentFilters.brand)}`;
        if (currentFilters.size) url += `&size=${encodeURIComponent(currentFilters.size)}`;
        if (currentFilters.status) url += `&status=${encodeURIComponent(currentFilters.status)}`;
        if (currentFilters.expiry_status) url += `&expiry_status=${encodeURIComponent(currentFilters.expiry_status)}`;
        if (currentFilters.date_of_stock) url += `&date_of_stock=${encodeURIComponent(currentFilters.date_of_stock)}`;
        if (currentFilters.restock_date) url += `&restock_date=${encodeURIComponent(currentFilters.restock_date)}`;

        const response = await fetch(url);
        const result = await response.json();

        const tbody = document.getElementById('productsTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (result.data && result.data.length > 0) {
            allProducts = result.data;
            result.data.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'table-row-hover transition cursor-pointer border-b border-slate-100/80';

                const stockLevel = product.stock_quantity ?? 0;
                const reorderLevel = product.reorder_level ?? 10;
                const maxStock = 50;
                const isOutOfStock = stockLevel <= 0;
                const isLowStock = !isOutOfStock && stockLevel <= reorderLevel;
                const isOverStock = stockLevel > maxStock;

                const unitPrice = parseFloat(product.unit_price || 0);
                const vatAmount = unitPrice * 0.12;
                const priceWithoutVat = unitPrice - vatAmount;

                // Status badge
                let statusBadge = '';
                if (product.is_archived) {
                    statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-800">Archived</span>';
                } else if (!product.is_active) {
                    statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800">Inactive</span>';
                } else if (isOutOfStock) {
                    statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800">No Stock</span>';
                } else if (isLowStock) {
                    statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-100 text-orange-800">Low Stock</span>';
                } else if (isOverStock) {
                    statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-800">Overstock</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">Available</span>';
                }

                const compatName = product.name || 'Universal';
                const prodName = product.product_name || 'Accessories';
                const skuStr = product.sku || 'N/A';
                const brandStr = product.brand || '-';
                const categoryStr = product.category || '-';
                const supplierStr = product.supplier_name || '-';
                const expiryStr = product.expiry_date ? formatDate(product.expiry_date) : 'Non-expiring';
                const isExpired = product.expiry_date && new Date(product.expiry_date) < new Date();

                row.innerHTML = `
                    <td class="w-10 px-3 py-2.5 text-center sticky-first-col bg-white" onclick="event.stopPropagation()">
                        <input type="checkbox" class="product-checkbox rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" data-product-id="${product.id}" />
                    </td>
                    <td class="px-3 py-2.5 text-left align-middle overflow-hidden">
                        <div class="flex items-center gap-2.5 min-w-0" title="${escapeHtml(compatName)}">
                            <div class="inline-qr-code shrink-0" data-sku="${escapeHtml(skuStr)}" style="width: 32px; height: 32px;"></div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900 truncate text-[11px]">${escapeHtml(prodName)}</div>
                                <div class="text-[10px] text-slate-500 truncate">${escapeHtml(compatName)}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-left align-middle overflow-hidden">
                        <div class="font-mono text-slate-900 font-semibold text-[10px] truncate">${escapeHtml(skuStr)}</div>
                        <div class="text-[10px] text-slate-500 truncate">${escapeHtml(brandStr)}</div>
                    </td>
                    <td class="px-3 py-2.5 text-left text-slate-700 text-[11px] align-middle overflow-hidden">
                        <span class="truncate block">${escapeHtml(categoryStr)}</span>
                    </td>
                    <td class="px-3 py-2.5 text-left align-middle overflow-hidden">
                        <div class="flex flex-wrap gap-1">
                            ${getLocationSummary(product)}
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-center align-middle">
                        <div class="font-semibold text-[11px] ${stockLevel <= 0 ? 'text-red-600' : (stockLevel <= reorderLevel ? 'text-orange-600' : 'text-slate-900')}">
                            ${stockLevel} / ${maxStock}
                        </div>
                        <div class="w-16 bg-slate-100 rounded-full h-1.5 mt-1 mx-auto overflow-hidden">
                            <div class="${isOutOfStock ? 'bg-red-500' : (isLowStock ? 'bg-orange-500' : (isOverStock ? 'bg-purple-500' : 'bg-emerald-500'))} h-full rounded-full" style="width: ${Math.min(100, (stockLevel / maxStock) * 100)}%"></div>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 text-right font-semibold text-slate-900 text-[11px] align-middle" title="Net: ₱${priceWithoutVat.toFixed(2)} | VAT: ₱${vatAmount.toFixed(2)}">
                        ₱${unitPrice.toFixed(2)}
                    </td>
                    <td class="px-3 py-2.5 text-left text-slate-600 text-[11px] align-middle overflow-hidden">
                        <span class="truncate block max-w-[120px]" title="${escapeHtml(supplierStr)}">${escapeHtml(supplierStr)}</span>
                    </td>
                    <td class="px-3 py-2.5 text-left text-slate-600 text-[11px] align-middle overflow-hidden">
                        <span class="truncate block">${formatDate(product.last_restock_date)}</span>
                    </td>
                    <td class="px-3 py-2.5 text-left text-slate-600 text-[11px] align-middle overflow-hidden">
                        <span class="truncate block ${isExpired ? 'text-red-600 font-semibold' : ''}" title="${escapeHtml(expiryStr)}">${escapeHtml(expiryStr)}</span>
                    </td>
                    <td class="px-3 py-2.5 text-center align-middle whitespace-nowrap">
                        ${statusBadge}
                    </td>
                    <td class="px-3 py-2.5 text-center align-middle whitespace-nowrap text-[10px]" onclick="event.stopPropagation()">
                        <div class="inline-flex items-center gap-1.5 justify-center">
                            <button type="button" onclick="event.stopPropagation(); openViewDetailsById(${product.id});" class="text-slate-500 hover:text-slate-900 p-1 rounded hover:bg-slate-100 transition inline-flex items-center justify-center shrink-0 cursor-pointer" title="View Details">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); openEditModal(${product.id});" class="text-slate-500 hover:text-slate-900 p-1 rounded hover:bg-slate-100 transition inline-flex items-center justify-center shrink-0 cursor-pointer" title="Edit Product">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); archiveProduct(${product.id});" class="rounded-[5px] border border-slate-200 bg-white px-1.5 py-0.5 text-[9.5px] font-semibold text-slate-700 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition shadow-sm whitespace-nowrap shrink-0 cursor-pointer">Archive</button>
                        </div>
                    </td>
                `;

                row.onclick = () => {
                    openViewDetailsModal(product);
                };

                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="12" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                            </div>
                            <p class="text-slate-600 font-medium">No inventory records found.</p>
                            <button type="button" onclick="openAddStockModal()" class="px-4 py-2 rounded-lg bg-[#6EC1D1] text-slate-900 font-semibold hover:bg-[#59b2c2] transition text-xs cursor-pointer">
                                Add First Stock
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Render inline QR codes
        document.querySelectorAll('.inline-qr-code:not(.rendered)').forEach(container => {
            const sku = container.dataset.sku;
            if (sku && typeof QRCode !== 'undefined') {
                new QRCode(container, {
                    text: sku,
                    width: 32,
                    height: 32,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });
            }
            container.classList.add('rendered');
        });

        currentPage = page;
        updatePagination(result.pagination);
        attachCheckboxListeners();
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

function openViewDetailsById(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (product) {
        openViewDetailsModal(product);
    } else {
        fetch(`${window.AllStocks.routes.apiProductShowBase}/${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.product) openViewDetailsModal(data.product);
            });
    }
}
window.openViewDetailsById = openViewDetailsById;

// Update pagination controls
function updatePagination(pagination) {
    const paginationContainer = document.getElementById('paginationControls');
    const summary = document.getElementById('paginationInfo');

    if (summary) {
        summary.textContent = `Showing ${pagination?.from || 0}-${pagination?.to || 0} of ${pagination?.total?.toLocaleString() || 0} items`;
    }

    if (!paginationContainer || !pagination || !pagination.last_page) return;

    const totalPages = pagination.last_page;
    let html = `<button type="button" onclick="loadProducts(${Math.max(1, currentPage - 1)})" class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer" ${currentPage <= 1 ? 'disabled' : ''}>← Prev</button>`;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
    startPage = Math.max(1, startPage);

    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `<button type="button" class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${i}</button>`;
        } else {
            html += `<button type="button" onclick="loadProducts(${i})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">${i}</button>`;
        }
    }

    html += `<button type="button" onclick="loadProducts(${Math.min(totalPages, currentPage + 1)})" class="rounded-[10px] border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer" ${currentPage >= totalPages ? 'disabled' : ''}>Next →</button>`;
    paginationContainer.innerHTML = html;
}

// Checkbox listeners for bulk selection
function attachCheckboxListeners() {
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.product-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
}

// Export products CSV with current filters
function exportProducts() {
    const params = new URLSearchParams();
    if (currentFilters.search) params.append('search', currentFilters.search);
    if (currentFilters.warehouse) params.append('warehouse', currentFilters.warehouse);
    if (currentFilters.category) params.append('category', currentFilters.category);
    if (currentFilters.product_name) params.append('product_name', currentFilters.product_name);
    if (currentFilters.brand) params.append('brand', currentFilters.brand);
    if (currentFilters.size) params.append('size', currentFilters.size);

    const url = window.AllStocks.routes.stockExport + (params.toString() ? ('?' + params.toString()) : '');
    window.location = url;
}
window.exportProducts = exportProducts;

// QR Code Generation Functions
async function openQRCodeModal() {
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one product using the checkboxes to generate QR codes.');
        return;
    }

    let productsList = allProducts;
    if (productsList.length === 0) {
        try {
            const response = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
            const result = await response.json();
            if (result.data) productsList = result.data;
        } catch (error) {
            console.error('Error loading products for QR:', error);
        }
    }

    const selectedProducts = Array.from(selectedCheckboxes).map(cb => {
        const productId = parseInt(cb.dataset.productId);
        return productsList.find(p => p.id === productId);
    }).filter(Boolean);

    if (selectedProducts.length === 0) {
        alert('Please select at least one product to generate QR codes.');
        return;
    }

    document.getElementById('qrSelectedCount').textContent = selectedProducts.length;
    const modal = document.getElementById('qrCodeModal');
    currentSelectedProducts = selectedProducts;

    modal.style.display = 'flex';
    generateQRCodes(selectedProducts);
}
window.openQRCodeModal = openQRCodeModal;

function generateQRCodes(products) {
    const previewContainer = document.getElementById('qrCodePreview');
    const loadingIndicator = document.getElementById('qrLoading');

    if (!previewContainer) return;

    previewContainer.innerHTML = '';
    if (loadingIndicator) {
        previewContainer.appendChild(loadingIndicator);
        loadingIndicator.classList.remove('hidden');
    }

    setTimeout(() => {
        if (loadingIndicator) loadingIndicator.classList.add('hidden');

        products.forEach((product) => {
            const qrData = JSON.stringify({
                sku: product.sku || '',
                restock_date: product.last_restock_date || 'N/A',
                product_id: product.id,
                name: product.product_name || product.name || '',
                price: Number(product.unit_price || 0)
            });

            const qrCard = document.createElement('div');
            qrCard.className = 'qr-product-card border border-slate-200 rounded-xl p-3.5 bg-white shadow-sm transition-all hover:border-slate-300';
            qrCard.dataset.productId = product.id;
            qrCard.dataset.name = product.product_name || product.name || 'N/A';
            qrCard.dataset.sku = product.sku || 'N/A';
            qrCard.dataset.price = Number(product.unit_price || 0).toFixed(2);
            qrCard.dataset.stock = product.stock_quantity ?? 0;

            qrCard.innerHTML = `
                <div class="flex items-center gap-4">
                    <div id="qr-${product.id}" class="qr-code-holder w-28 h-28 shrink-0 flex items-center justify-center bg-white p-1.5 border border-slate-200 rounded-xl shadow-inner overflow-hidden"></div>
                    <div class="flex-1 min-w-0">
                        <h3 class="qr-product-name font-bold text-slate-900 text-sm leading-snug mb-1 truncate" title="${product.product_name || product.name || 'N/A'}">${product.product_name || product.name || 'N/A'}</h3>
                        <p class="qr-product-sku text-xs text-slate-700 font-medium">SKU: <span class="font-mono font-bold text-slate-900">${product.sku || 'N/A'}</span></p>
                        <p class="qr-product-price text-xs text-slate-600 mt-0.5">Price: <span class="font-semibold text-emerald-700">₱${Number(product.unit_price || 0).toFixed(2)}</span></p>
                        <p class="qr-product-stock text-xs text-slate-500 mt-0.5">Stock: ${product.stock_quantity ?? 0} units</p>
                        <div class="mt-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Ready to scan for POS
                        </div>
                    </div>
                </div>
            `;

            previewContainer.appendChild(qrCard);

            try {
                const qrElement = document.getElementById(`qr-${product.id}`);
                if (!qrElement) return;

                qrElement.innerHTML = '';
                if (typeof QRCode !== 'undefined') {
                    new QRCode(qrElement, {
                        text: qrData,
                        width: 120,
                        height: 120,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });

                    setTimeout(() => {
                        const canvas = qrElement.querySelector('canvas');
                        const img = qrElement.querySelector('img');
                        if (canvas && img) {
                            img.style.display = 'none';
                            canvas.style.display = 'block';
                            canvas.style.width = '100px';
                            canvas.style.height = '100px';
                        }
                    }, 40);
                } else {
                    qrElement.innerHTML = '<div class="text-[10px] text-red-500 text-center">QR Library missing</div>';
                }
            } catch (error) {
                console.error('Error generating QR code for product:', product.id, error);
            }
        });
    }, 100);
}

function getQrImageSrc(productId) {
    const qrElement = document.getElementById(`qr-${productId}`);
    if (!qrElement) return null;
    const canvas = qrElement.querySelector('canvas');
    if (canvas) {
        try {
            return canvas.toDataURL('image/png');
        } catch (e) {}
    }
    const img = qrElement.querySelector('img');
    if (img && img.src && (img.src.startsWith('data:') || img.src.startsWith('http') || img.src.startsWith('blob:'))) {
        return img.src;
    }
    return null;
}

function drawCanvasRoundRect(ctx, x, y, width, height, radius, fill, stroke) {
    if (typeof radius === 'number') {
        radius = { tl: radius, tr: radius, br: radius, bl: radius };
    }
    ctx.beginPath();
    ctx.moveTo(x + radius.tl, y);
    ctx.lineTo(x + width - radius.tr, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
    ctx.lineTo(x + width, y + height - radius.br);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
    ctx.lineTo(x + radius.bl, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
    ctx.lineTo(x, y + radius.tl);
    ctx.quadraticCurveTo(x, y, x + radius.tl, y);
    ctx.closePath();
    if (fill) ctx.fill();
    if (stroke) ctx.stroke();
}

async function downloadQRCodes() {
    const previewContainer = document.getElementById('qrCodePreview');

    if (!previewContainer || !currentSelectedProducts || currentSelectedProducts.length === 0) {
        showToast('No QR codes available to download.', 'error');
        return;
    }

    try {
        const items = [];
        for (const product of currentSelectedProducts) {
            const dataUrl = getQrImageSrc(product.id);
            items.push({
                id: product.id,
                name: product.product_name || product.name || 'Unnamed Product',
                sku: product.sku || 'N/A',
                price: Number(product.unit_price || 0).toFixed(2),
                stock: product.stock_quantity ?? 0,
                qrDataUrl: dataUrl
            });
        }

        if (items.length === 0) {
            showToast('No QR codes found to download.', 'error');
            return;
        }

        const imagePromises = items.map(item => {
            return new Promise((resolve) => {
                if (!item.qrDataUrl) {
                    resolve({ item, img: null });
                    return;
                }
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve({ item, img });
                img.onerror = () => resolve({ item, img: null });
                img.src = item.qrDataUrl;
            });
        });

        const loadedResults = await Promise.all(imagePromises);

        const scale = 2;
        const cardWidth = 360 * scale;
        const cardHeight = 150 * scale;
        const padding = 20 * scale;
        const cols = items.length === 1 ? 1 : 2;
        const rows = Math.ceil(items.length / cols);
        const headerHeight = 60 * scale;

        const canvas = document.createElement('canvas');
        canvas.width = (cardWidth * cols) + (padding * (cols + 1));
        canvas.height = headerHeight + (cardHeight * rows) + (padding * (rows + 1));

        const ctx = canvas.getContext('2d');

        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = '#105f68';
        ctx.fillRect(0, 0, canvas.width, headerHeight);

        ctx.fillStyle = '#ffffff';
        ctx.font = `bold ${18 * scale}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`;
        ctx.fillText('KCC MOTORCYCLE - PRODUCT QR STICKERS', padding, 36 * scale);

        ctx.fillStyle = '#e2e8f0';
        ctx.font = `${10 * scale}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`;
        ctx.fillText(`Total Items: ${items.length} • Generated: ${new Date().toLocaleDateString()}`, padding, 50 * scale);

        loadedResults.forEach(({ item, img }, index) => {
            const col = index % cols;
            const row = Math.floor(index / cols);
            const x = padding + (col * (cardWidth + padding));
            const y = headerHeight + padding + (row * (cardHeight + padding));

            ctx.fillStyle = '#ffffff';
            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 1.5 * scale;
            drawCanvasRoundRect(ctx, x, y, cardWidth, cardHeight, 12 * scale, true, true);

            const qrBoxSize = 110 * scale;
            const qrX = x + (16 * scale);
            const qrY = y + (20 * scale);

            ctx.fillStyle = '#ffffff';
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 1 * scale;
            drawCanvasRoundRect(ctx, qrX, qrY, qrBoxSize, qrBoxSize, 8 * scale, true, true);

            if (img) {
                ctx.drawImage(img, qrX + (5 * scale), qrY + (5 * scale), qrBoxSize - (10 * scale), qrBoxSize - (10 * scale));
            }

            const textX = qrX + qrBoxSize + (16 * scale);
            const maxTextWidth = cardWidth - (qrBoxSize + (48 * scale));

            ctx.fillStyle = '#0f172a';
            ctx.font = `bold ${14 * scale}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`;
            let displayName = item.name;
            if (displayName.length > 24) displayName = displayName.substring(0, 22) + '...';
            ctx.fillText(displayName, textX, y + (36 * scale), maxTextWidth);

            ctx.fillStyle = '#334155';
            ctx.font = `bold ${11 * scale}px monospace, monospace`;
            ctx.fillText(`SKU: ${item.sku}`, textX, y + (58 * scale), maxTextWidth);

            ctx.fillStyle = '#047857';
            ctx.font = `bold ${13 * scale}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`;
            ctx.fillText(`Price: ₱${item.price}`, textX, y + (80 * scale), maxTextWidth);

            ctx.fillStyle = '#64748b';
            ctx.font = `${10 * scale}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`;
            ctx.fillText(`Stock: ${item.stock} units`, textX, y + (100 * scale), maxTextWidth);

            const badgeY = y + (114 * scale);
            ctx.fillStyle = '#ecfdf5';
            ctx.strokeStyle = '#a7f3d0';
            ctx.lineWidth = 1 * scale;
            drawCanvasRoundRect(ctx, textX, badgeY, 90 * scale, 18 * scale, 9 * scale, true, true);

            ctx.fillStyle = '#047857';
            ctx.font = `bold ${9 * scale}px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif`;
            ctx.fillText('✓ POS Ready', textX + (12 * scale), badgeY + (12 * scale));
        });

        const downloadLink = document.createElement('a');
        downloadLink.download = `kcc-qr-codes-${Date.now()}.png`;
        downloadLink.href = canvas.toDataURL('image/png');
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);

        showToast('QR code image downloaded successfully!', 'success');
    } catch (error) {
        console.error('Error in downloadQRCodes:', error);
        showToast('Failed to download QR code image.', 'error');
    }
}
window.downloadQRCodes = downloadQRCodes;

function printQRCodes() {
    const previewContainer = document.getElementById('qrCodePreview');

    if (!previewContainer || !currentSelectedProducts || currentSelectedProducts.length === 0) {
        showToast('No QR codes available to print.', 'error');
        return;
    }

    try {
        const items = [];
        for (const product of currentSelectedProducts) {
            const dataUrl = getQrImageSrc(product.id);
            items.push({
                id: product.id,
                name: product.product_name || product.name || 'Unnamed Product',
                sku: product.sku || 'N/A',
                price: Number(product.unit_price || 0).toFixed(2),
                stock: product.stock_quantity ?? 0,
                qrImage: dataUrl || ''
            });
        }

        const validItems = items.filter(i => Boolean(i.qrImage));
        if (validItems.length === 0) {
            showToast('Please wait a moment for QR codes to finish rendering.', 'error');
            return;
        }

        const printHtml = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Print QR Codes - KCC Motorcycle</title>
                <style>
                    @page { size: auto; margin: 10mm; }
                    * { box-sizing: border-box; margin: 0; padding: 0; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #ffffff; color: #0f172a; padding: 12px; }
                    .header { margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #105f68; display: flex; justify-content: space-between; align-items: center; }
                    .header h1 { font-size: 16px; font-weight: 800; color: #0f172a; }
                    .header p { font-size: 11px; color: #64748b; }
                    .qr-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
                    @media print { body { padding: 0; } .qr-card { page-break-inside: avoid; break-inside: avoid; } }
                    .qr-card { border: 1.5px solid #cbd5e1; border-radius: 12px; padding: 12px; background: #ffffff; display: flex; align-items: center; gap: 12px; }
                    .qr-img-box { width: 90px; height: 90px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: 8px; padding: 4px; background: #ffffff; }
                    .qr-img-box img { width: 100%; height: 100%; object-fit: contain; display: block; }
                    .qr-info { flex: 1; min-width: 0; }
                    .qr-title { font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.25; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                    .qr-sku { font-size: 11px; font-family: monospace; font-weight: 700; color: #1e293b; margin-bottom: 3px; }
                    .qr-price { font-size: 12px; font-weight: 700; color: #047857; margin-bottom: 2px; }
                    .qr-stock { font-size: 10px; color: #64748b; }
                    .badge { display: inline-block; margin-top: 4px; padding: 2px 6px; border-radius: 9999px; background: #ecfdf5; color: #047857; font-size: 9px; font-weight: 600; border: 1px solid #a7f3d0; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div>
                        <h1>KCC MOTORCYCLE - PRODUCT QR STICKERS</h1>
                        <p>Total Items: ${validItems.length} • Date: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="qr-grid">
                    ${validItems.map(item => `
                        <div class="qr-card">
                            <div class="qr-img-box">
                                <img src="${item.qrImage}" alt="${item.sku}">
                            </div>
                            <div class="qr-info">
                                <div class="qr-title" title="${item.name}">${item.name}</div>
                                <div class="qr-sku">SKU: ${item.sku}</div>
                                <div class="qr-price">₱${item.price}</div>
                                <div class="qr-stock">Stock: ${item.stock} units</div>
                                <div class="badge">✓ POS Ready</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </body>
            </html>
        `;

        let printFrame = document.getElementById('pos_qr_print_frame');
        if (printFrame) printFrame.remove();

        printFrame = document.createElement('iframe');
        printFrame.id = 'pos_qr_print_frame';
        printFrame.style.position = 'fixed';
        printFrame.style.top = '-9999px';
        printFrame.style.left = '-9999px';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = 'none';
        document.body.appendChild(printFrame);

        const frameDoc = printFrame.contentWindow.document;
        frameDoc.open();
        frameDoc.write(printHtml);
        frameDoc.close();

        setTimeout(() => {
            try {
                printFrame.contentWindow.focus();
                printFrame.contentWindow.print();
                showToast('Print dialog opened.', 'success');
            } catch (err) {
                const win = window.open('', '_blank');
                if (win) {
                    win.document.open();
                    win.document.write(printHtml);
                    win.document.close();
                    setTimeout(() => { win.focus(); win.print(); }, 400);
                } else {
                    showToast('Popup blocked. Please allow popups to print.', 'error');
                }
            }
        }, 300);
    } catch (error) {
        console.error('Error in printQRCodes:', error);
        showToast('Error opening print preview.', 'error');
    }
}
window.printQRCodes = printQRCodes;

// View Details Modal Logic
window.openViewDetailsModal = function (product) {
    document.getElementById('vdProductTitle').textContent = product.product_name || product.name || 'Product Details';
    document.getElementById('vdSku').textContent = product.sku || 'N/A';

    const barcodeVal = product.barcode;
    const vdBarcodeSvg = document.getElementById('vdBarcode');
    const vdBarcodeText = document.getElementById('vdBarcodeText');
    if (barcodeVal && barcodeVal !== 'N/A') {
        vdBarcodeText.classList.add('hidden');
        vdBarcodeSvg.classList.remove('hidden');
        try {
            JsBarcode(vdBarcodeSvg, barcodeVal, {
                format: "CODE128",
                width: 1.5,
                height: 40,
                displayValue: true,
                margin: 0,
                fontSize: 12
            });
        } catch (e) {
            vdBarcodeSvg.classList.add('hidden');
            vdBarcodeText.classList.remove('hidden');
            vdBarcodeText.textContent = barcodeVal;
        }
    } else {
        vdBarcodeSvg.classList.add('hidden');
        vdBarcodeText.classList.remove('hidden');
        vdBarcodeText.textContent = 'N/A';
    }
    document.getElementById('vdBrand').textContent = product.brand || 'N/A';
    document.getElementById('vdSupplier').textContent = product.supplier_name || 'N/A';
    document.getElementById('vdSize').textContent = product.size || 'N/A';
    document.getElementById('vdColor').textContent = product.color || 'N/A';

    let models = product.compatible_models || product.compatibility || product.name || 'N/A';
    if (Array.isArray(models)) {
        models = models.join(', ');
    } else if (typeof models === 'string') {
        try {
            const parsed = JSON.parse(models);
            if (Array.isArray(parsed)) models = parsed.join(', ');
        } catch (e) { }
    }
    document.getElementById('vdCompatibleModels').textContent = models;

    document.getElementById('vdReorderLevel').textContent = product.reorder_level ?? 10;

    const unitPrice = parseFloat(product.unit_price || 0);
    const vatAmount = unitPrice * 0.12;
    const priceWithoutVat = unitPrice - vatAmount;
    document.getElementById('vdVat').textContent = `₱${unitPrice.toFixed(2)} (VAT: ₱${vatAmount.toFixed(2)} | Net: ₱${priceWithoutVat.toFixed(2)})`;

    document.getElementById('vdDateOfStock').innerHTML = product.last_restock_date ? formatDateWithTime(product.last_restock_date) : '-';
    document.getElementById('vdExpirationDate').innerHTML = product.expiry_date ? formatDateWithTime(product.expiry_date) : '-';

    document.getElementById('viewDetailsModal').classList.remove('hidden');
};

// Initialize when DOM is ready
function initializeMonitoringPage() {
    attachUIEvents();

    loadStats();
    loadProducts(1);
    loadMovements();
    getAvailableSuppliers().then(() => renderEditSuppliersDropdown());

    setTimeout(() => {
        loadFilterOptions();
    }, 400);
}

// Global functions exposure
window.loadProducts = loadProducts;
window.performSearch = performSearch;
window.resetFilters = resetFilters;
window.toggleEditSuppliersDropdown = toggleEditSuppliersDropdown;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMonitoringPage);
} else {
    initializeMonitoringPage();
}
