// allstocks.js - extracted from blade template
// Expects a global `window.AllStocks` with routes and csrfToken set in the blade.

// Store all products for filtering
let allProducts = [];
let currentFilters = {
    search: '',
    warehouse: '',
    product_name: '',
    brand: '',
    size: '',
    status: '',
    expiry_status: '',
    date_of_stock: ''
};
let searchTimeout;
let currentEditProduct = null;

// Get warehouse badge with color
function getWarehouseBadge(warehouse) {
    const badges = {
        'Shop': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Shop</span>',
        'Warehouse A': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Warehouse A</span>',
        'Warehouse B': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Warehouse B</span>',
        'Warehouse C': '<span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Warehouse C</span>',
    };
    return badges[warehouse] || `<span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">${warehouse}</span>`;
}

// Get location summary from warehouse stocks
function getLocationSummary(product) {
    if (!product.warehouse_stocks || product.warehouse_stocks.length === 0) {
        return '<span class="text-red-500">No Location</span>';
    }

    const locations = product.warehouse_stocks
        .filter(stock => stock.quantity > 0)
        .map(stock => {
            const badge = getWarehouseBadge(stock.warehouse);
            return `${badge} (${stock.quantity})`;
        });

    if (locations.length === 0) {
        return '<span class="text-red-500">No Stock</span>';
    }

    return locations.join('<br>');
}

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

    const categoryVal = product.category || '';
    document.getElementById('editCategory').value = categoryVal;
    const categoryBtn = document.getElementById('editCategoryButton');
    if (categoryBtn) {
        const categoryLabels = {
            'engine_oil': 'Engine Oil',
            'battery': 'Battery',
            'spark_plug': 'Spark Plug',
            'brake_pads': 'Brake Pads',
            'tires': 'Tires',
            'filters': 'Filters',
            'lubricants': 'Lubricants',
            'accessories': 'Accessories'
        };
        const span = categoryBtn.querySelector('span');
        if (span) {
            span.textContent = categoryLabels[categoryVal] || categoryVal || 'Select category';
        }
    }

    document.getElementById('editLastRestock').value = product.last_restock_date ? String(product.last_restock_date).split('T')[0] : '';
    document.getElementById('editExpiryDate').value = product.expiry_date ? String(product.expiry_date).split('T')[0] : '';
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
    const display = document.getElementById('addStockProductDisplay');
    if (display) display.textContent = 'Select a product...';
    const dropdown = document.getElementById('addStockProductDropdown');
    if (dropdown) dropdown.classList.add('hidden');
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
window.openEditModal = function (productId) {
    openEditProductModal(productId);
};

// Direct edit modal function
window.openEditModalDirect = function (productId) {
    openEditProductModal(productId);
};

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

// Make archiveProduct globally accessible
window.archiveProduct = archiveProduct;

window.selectDropdownOption = function (inputId, value, text, dropdownId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const input = document.getElementById(inputId);
    if (input) {
        input.value = value;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    const display = document.getElementById('addStockProductDisplay');
    if (display) {
        display.textContent = text;
    }
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.add('hidden');
    }
};

// Load products for select dropdown
async function loadProductsForSelect() {
    try {
        const response = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const result = await response.json();

        const select = document.getElementById('productSelect');
        const dropdown = document.getElementById('addStockProductDropdown');

        if (select) {
            select.innerHTML = '<option value="">Select a product...</option>';
        }

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
                    btn.className = 'w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
                    btn.textContent = label;
                    btn.onclick = (e) => selectDropdownOption('productSelect', product.id, label, 'addStockProductDropdown', e);
                    dropdown.appendChild(btn);
                });
            } else {
                dropdown.innerHTML = '<div class="p-3 text-left text-xs text-slate-500">No products available</div>';
            }
        }
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Load filter options (Product Name, Brand, Size)
async function loadFilterOptions() {
    try {
        // Load product descriptions from Product Categorization module
        const descriptionsResponse = await fetch(window.AllStocks.routes.apiProductDescriptions);
        const descriptionsResult = await descriptionsResponse.json();

        // Load products for brands and sizes
        const productsResponse = await fetch(window.AllStocks.routes.apiProducts + '?per_page=1000');
        const productsResult = await productsResponse.json();

        let brands = new Set();
        let sizes = new Set();

        // Store product name to brands mapping from Product Categorization
        window.productNameToBrands = {};

        let productNames = new Set();

        if (descriptionsResult && descriptionsResult.length > 0) {
            descriptionsResult.forEach(description => {
                window.productNameToBrands[description.name] = new Set(description.brands || []);
                description.brands.forEach(brand => brands.add(brand));
                productNames.add(description.name);
            });
        }

        if (productsResult.data && productsResult.data.length > 0) {
            productsResult.data.forEach(product => {
                if (product.size) sizes.add(product.size);
                if (product.brand) brands.add(product.brand);
                const name = product.product_name || product.name;
                if (name) productNames.add(name);
            });
        }

        // Populate Product Name dropdown
        const productNameSelect = document.getElementById('productNameFilter');
        const productNameDropdown = document.getElementById('productNameFilterDropdown');
        if (productNameDropdown) {
            productNameDropdown.innerHTML = '';
            const defaultBtn = document.createElement('button');
            defaultBtn.type = 'button';
            defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
            defaultBtn.textContent = 'All Products';
            defaultBtn.onclick = (e) => selectDropdownOption('productNameFilter', '', 'All Products', 'productNameFilterDropdown', e);
            productNameDropdown.appendChild(defaultBtn);

            Array.from(productNames).sort().forEach(name => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
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
            defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
            defaultBtn.textContent = 'All Brands';
            defaultBtn.onclick = (e) => selectDropdownOption('brandFilter', '', 'All Brands', 'brandFilterDropdown', event);
            brandDropdown.appendChild(defaultBtn);

            Array.from(brands).sort().forEach(brand => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
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
            defaultBtn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
            defaultBtn.textContent = 'All Sizes';
            defaultBtn.onclick = (e) => selectDropdownOption('sizeFilter', '', 'All Sizes', 'sizeFilterDropdown', e);
            sizeDropdown.appendChild(defaultBtn);

            Array.from(sizes).sort().forEach(size => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition';
                btn.textContent = size;
                btn.onclick = (e) => selectDropdownOption('sizeFilter', size, size, 'sizeFilterDropdown', e);
                sizeDropdown.appendChild(btn);
            });
        }
    } catch (error) {
        console.error('Error loading filter options:', error);
    }
}

// Update brand dropdown based on selected product description
function updateBrandDropdown() {
    const productNameFilter = document.getElementById('productNameFilter');
    const brandFilter = document.getElementById('brandFilter');

    if (!productNameFilter || !brandFilter) return;

    const selectedOption = productNameFilter.options[productNameFilter.selectedIndex];
    let brands = [];

    if (selectedOption.dataset.brands) {
        try {
            brands = JSON.parse(selectedOption.dataset.brands);
        } catch (e) {
            console.error('Error parsing brands:', e);
            brands = [];
        }
    }

    // Clear brand dropdown
    brandFilter.innerHTML = '';

    // Always add "All Brands" option
    const allBrandsOption = document.createElement('option');
    allBrandsOption.value = '';
    allBrandsOption.textContent = 'All Brands';
    brandFilter.appendChild(allBrandsOption);

    if (productNameFilter.value !== '' && brands.length > 0) {
        // Add only brands that belong to the selected product description
        brands.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand;
            option.textContent = brand;
            brandFilter.appendChild(option);
        });
    } else {
        // If no product description selected, show all brands
        if (window.originalBrandOptions) {
            window.originalBrandOptions.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand;
                option.textContent = brand;
                brandFilter.appendChild(option);
            });
        }
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
    ['warehouseFilter', 'productNameFilter', 'brandFilter', 'sizeFilter', 'statusFilter', 'expiryStatusFilter', 'dateOfStockFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    currentFilters = { search: '', warehouse: '', product_name: '', brand: '', size: '', status: '', expiry_status: '', date_of_stock: '' };
    currentPage = 1;
    loadProducts(1);
}

// Attach UI events (safely when elements exist)
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
    if (categoryEl) {
        categoryEl.addEventListener('change', function (e) {
            currentFilters.category = e.target.value;
            renderCategoryChips();
            performSearch();
        });
    }
    const productNameEl = document.getElementById('productNameFilter');
    if (productNameEl) productNameEl.addEventListener('change', function (e) {
        updateBrandDropdown();
        currentFilters.product_name = e.target.value;
        currentFilters.brand = ''; // Reset brand when product name changes
        document.getElementById('brandFilter').value = '';
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

    const addStockBtn = document.getElementById('addStockBtn');
    if (addStockBtn) addStockBtn.addEventListener('click', openAddStockModal);
    const closeAdd = document.getElementById('closeAddStockModal');
    if (closeAdd) closeAdd.addEventListener('click', closeAddStockModal);
    const closeAddBackdrop = document.getElementById('closeAddStockModalBackdrop');
    if (closeAddBackdrop) closeAddBackdrop.addEventListener('click', closeAddStockModal);
    const cancelAdd = document.getElementById('cancelAddStock');
    if (cancelAdd) cancelAdd.addEventListener('click', closeAddStockModal);

    const addStockModal = document.getElementById('addStockModal');
    if (addStockModal) addStockModal.addEventListener('click', function (e) { if (e.target === this) closeAddStockModal(); });

    // Edit product modal events
    const closeEdit = document.getElementById('closeEditProductModal');
    if (closeEdit) closeEdit.addEventListener('click', closeEditProductModal);
    const cancelEdit = document.getElementById('cancelEditProduct');
    if (cancelEdit) cancelEdit.addEventListener('click', closeEditProductModal);

    const editProductModal = document.getElementById('editProductModal');
    if (editProductModal) editProductModal.addEventListener('click', function (e) { if (e.target === this) closeEditProductModal(); });

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
        importFile.addEventListener('change', async function (e) {
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
                    throw new Error('Server returned non-JSON response:\n' + text.substring(0, 200));
                }

                if (result.success) {
                    let message = `✅ ${result.message}`;
                    if (result.debug_total_rows_processed) message += `\nRows processed: ${result.debug_total_rows_processed}`;
                    if (result.errors && result.errors.length > 0) {
                        message += `\n\n⚠️ Errors (${result.errors.length} total):\n${result.errors.slice(0, 5).join('\n')}`;
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
        const expiringSoonEl = document.getElementById('stat-expiring-soon');
        const shopEl = document.getElementById('stat-shop');
        const warehouseAEl = document.getElementById('stat-warehouse-a');
        const warehouseBEl = document.getElementById('stat-warehouse-b');
        const warehouseCEl = document.getElementById('stat-warehouse-c');

        if (totalProductsEl) totalProductsEl.textContent = Number(stats.total_items || 0).toLocaleString();
        if (totalValueEl) totalValueEl.textContent = '₱' + Number(stats.total_value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (lowStockEl) lowStockEl.textContent = Number(stats.low_stock_count || 0).toLocaleString();
        if (expiringSoonEl) expiringSoonEl.textContent = Number(stats.expiring_soon_count || 0).toLocaleString();

        // Warehouse breakdown stats
        if (shopEl) shopEl.textContent = Number(stats.shop_count || 0).toLocaleString();
        if (warehouseAEl) warehouseAEl.textContent = Number(stats.warehouse_a_count || 0).toLocaleString();
        if (warehouseBEl) warehouseBEl.textContent = Number(stats.warehouse_b_count || 0).toLocaleString();
        if (warehouseCEl) warehouseCEl.textContent = Number(stats.warehouse_c_count || 0).toLocaleString();
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
        if (currentFilters.warehouse) url += `&warehouse=${encodeURIComponent(currentFilters.warehouse)}`;
        if (currentFilters.product_name) url += `&product_name=${encodeURIComponent(currentFilters.product_name)}`;
        if (currentFilters.brand) url += `&brand=${encodeURIComponent(currentFilters.brand)}`;
        if (currentFilters.size) url += `&size=${encodeURIComponent(currentFilters.size)}`;
        if (currentFilters.status) url += `&status=${encodeURIComponent(currentFilters.status)}`;
        if (currentFilters.expiry_status) url += `&expiry_status=${encodeURIComponent(currentFilters.expiry_status)}`;
        if (currentFilters.date_of_stock) url += `&date_of_stock=${encodeURIComponent(currentFilters.date_of_stock)}`;

        const response = await fetch(url);
        const result = await response.json();

        const tbody = document.getElementById('productsTableBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        if (result.data && result.data.length > 0) {
            result.data.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'table-row-hover transition cursor-pointer';
                row.onclick = () => toggleRow(product.id);

                const stockLevel = product.stock_quantity || 0;
                const reorderLevel = product.reorder_level || 10;
                const isLowStock = stockLevel <= reorderLevel && stockLevel > 0;
                const isOutOfStock = stockLevel <= 0;

                // Calculate VAT breakdown (assuming 12% VAT)
                const unitPrice = parseFloat(product.unit_price || 0);
                const vatAmount = unitPrice * 0.12;
                const priceWithoutVat = unitPrice - vatAmount;

                // Status badge
                let statusBadge = '';
                if (product.is_archived) {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Archived</span>';
                } else if (!product.is_active) {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>';
                } else if (isOutOfStock) {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">No Stock</span>';
                } else if (isLowStock) {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Low Stock</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Available</span>';
                }

                row.innerHTML = `
                    <td class="px-4 py-3 sticky-first-col bg-white" onclick="event.stopPropagation()">
                        <input type="checkbox" class="product-checkbox rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" data-product-id="${product.id}" />
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="inline-qr-code shrink-0" data-sku="${product.sku || ''}" style="width: 32px; height: 32px;"></div>
                            <div>
                                <div class="font-semibold text-slate-900">${product.product_name || product.name || 'N/A'}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-1">
                                    <span class="font-mono text-slate-600 font-medium">${product.sku || 'N/A'}</span>
                                    <span>&bull;</span>
                                    <span>${product.brand || '-'}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-700">${product.category || '-'}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1 text-sm font-medium text-slate-900">
                            ${getLocationSummary(product)}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-slate-900">${stockLevel} / ${reorderLevel}</div>
                        <div class="w-24 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                            <div class="${isLowStock || isOutOfStock ? 'bg-orange-500' : 'bg-emerald-500'} h-full rounded-full" style="width: ${Math.min(100, (stockLevel / Math.max(1, reorderLevel)) * 100)}%"></div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-semibold text-slate-900">₱${unitPrice.toFixed(2)}</div>
                    </td>
                    <td class="px-4 py-3 text-center">${statusBadge}</td>
                    <td class="px-4 py-3 text-center align-middle whitespace-nowrap text-[10px] font-medium" onclick="event.stopPropagation()">
                        <div class="inline-flex items-center gap-1.5 justify-center">
                            <button type="button" onclick="event.stopPropagation(); openEditModal(${product.id});" class="text-black hover:text-slate-900 inline-flex items-center p-1" title="Edit">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); archiveProduct(${product.id});" class="rounded-[8px] border border-slate-200 px-2 py-1 text-[10px] font-semibold transition-all bg-white text-slate-700 hover:bg-black/10">Archive</button>
                        </div>
                    </td>
                `;
                row.onclick = () => {
                    const stringifiedProduct = JSON.stringify(product).replace(/'/g, "\\'");
                    openViewDetailsModal(JSON.parse(stringifiedProduct));
                };
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-lg">No inventory records found.</p>
                            <button onclick="openAddStockModal()" class="px-6 py-2.5 rounded-lg bg-cyan-600 text-white font-semibold hover:bg-cyan-700 transition">
                                Add First Stock
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        document.querySelectorAll('.inline-qr-code:not(.rendered)').forEach(container => {
            const sku = container.dataset.sku;
            if (sku && typeof QRCode !== 'undefined') {
                new QRCode(container, {
                    text: sku,
                    width: 36,
                    height: 36,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });
            }
            container.classList.add('rendered');
        });

        currentPage = page;
        updatePagination(result.pagination);
        updatePaginationDisplay(result.pagination);
        attachCheckboxListeners();
        attachDropdownListeners();
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Update pagination display
function updatePaginationDisplay(pagination) {
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');
    const totalItems = document.getElementById('totalItems');

    if (showingFrom) showingFrom.textContent = pagination.from || 0;
    if (showingTo) showingTo.textContent = pagination.to || 0;
    if (totalItems) totalItems.textContent = pagination.total?.toLocaleString() || 0;
}

// Attach checkbox listeners for bulk selection
function attachCheckboxListeners() {
    const selectAll = document.getElementById('selectAll');
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

// Attach dropdown listeners
function attachDropdownListeners() {
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.action-dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
}

// Update bulk actions toolbar
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    const toolbar = document.getElementById('bulkActionsToolbar');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        toolbar.classList.remove('hidden');
        selectedCount.textContent = checkboxes.length;
    } else {
        toolbar.classList.add('hidden');
        selectedCount.textContent = '0';
    }
}

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const selectAll = document.getElementById('selectAll');

    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    if (selectAll) selectAll.checked = false;
    updateBulkActions();
}

// Toggle dropdown menu
function toggleDropdown(id, event) {
    if (event) {
        if (event.preventDefault) event.preventDefault();
        if (event.stopPropagation) event.stopPropagation();
    }

    let dropdown = null;
    if (typeof id === 'string') {
        dropdown = document.getElementById(id);
    }
    if (!dropdown) {
        dropdown = document.getElementById('dropdown-' + id);
    }
    if (!dropdown) return;

    const currentWrapper = dropdown.closest('[data-dropdown-wrapper]');

    // Hide any open calendar popups
    document.querySelectorAll('.custom-calendar-card').forEach(c => c.classList.add('hidden'));

    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown) {
            menu.classList.add('hidden');
            menu.classList.remove('show');
            const w = menu.closest('[data-dropdown-wrapper]');
            if (w) w.style.zIndex = '';
        }
    });

    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        dropdown.classList.add('show');
        if (currentWrapper) currentWrapper.style.zIndex = '100';
    } else if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
        dropdown.classList.add('hidden');
        if (currentWrapper) currentWrapper.style.zIndex = '';
    } else {
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            if (currentWrapper) currentWrapper.style.zIndex = '100';
        } else {
            if (currentWrapper) currentWrapper.style.zIndex = '';
        }
    }
}

// Toggle row expansion
function toggleRow(id) {
    const expandedRow = document.getElementById('expanded-row-' + id);
    if (expandedRow) {
        expandedRow.classList.toggle('hidden');
    }
}

// Format date to MM/DD/YY with time
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

    let html = `<button onclick="loadProducts(${Math.max(1, currentPage - 1)})" ${currentPage === 1 ? 'disabled' : ''} class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">← Prev</button>`;

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(pagination.last_page, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        const btnClass = i === currentPage ? 'bg-black/10 text-slate-900 font-semibold' : 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 font-semibold';
        html += `<button onclick="loadProducts(${i})" class="inline-flex items-center justify-center rounded-[10px] w-8 h-8 text-xs ${btnClass}">${i}</button>`;
    }

    html += `<button onclick="loadProducts(${Math.min(pagination.last_page, currentPage + 1)})" ${currentPage === pagination.last_page ? 'disabled' : ''} class="rounded-[10px] border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next →</button>`;

    paginationContainer.innerHTML = html;

    const summary = document.getElementById('paginationInfo');
    if (summary) {
        summary.textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total?.toLocaleString() || 0} products`;
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

    tbody.addEventListener('click', async function (e) {
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
document.addEventListener('click', function (e) {
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
window.clearSelection = clearSelection;
window.toggleDropdown = toggleDropdown;
window.toggleRow = toggleRow;

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

    document.getElementById('qrSelectedCount').textContent = selectedProducts.length;
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
                    <div id="qr-${product.id}" class="w-24 h-24 shrink-0 flex-none flex items-center justify-center bg-white overflow-hidden rounded-md"></div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-slate-900 text-sm truncate" title="${product.product_name || product.name || 'N/A'}">${product.product_name || product.name || 'N/A'}</h3>
                        <p class="text-xs text-slate-600 truncate" title="${product.sku}">SKU: ${product.sku}</p>
                        <p class="text-xs text-slate-600">Restock: ${product.last_restock_date ? window.formatDateWithTime ? window.formatDateWithTime(product.last_restock_date) : product.last_restock_date : 'N/A'}</p>
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

                // Force canvas and image to be visible
                setTimeout(() => {
                    const canvas = qrElement.querySelector('canvas');
                    if (canvas) {
                        canvas.style.display = 'block';
                        canvas.style.width = '96px';
                        canvas.style.height = '96px';
                    }
                    const img = qrElement.querySelector('img');
                    if (img) {
                        img.style.display = 'block';
                        img.style.width = '96px';
                        img.style.height = '96px';
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

// Add New Product Description Modal functionality
document.addEventListener('DOMContentLoaded', function () {
    const addNewProductDescBtn = document.getElementById('addNewProductDescBtn');
    const addProductDescModal = document.getElementById('addProductDescModal');
    const closeProductDescModal = document.getElementById('closeProductDescModal');
    const cancelProductDesc = document.getElementById('cancelProductDesc');
    const addProductDescForm = document.getElementById('addProductDescForm');
    const newProductDescName = document.getElementById('newProductDescName');
    const newProductDescBrand = document.getElementById('newProductDescBrand');
    const productNameFilter = document.getElementById('productNameFilter');

    if (addNewProductDescBtn && addProductDescModal) {
        // Open modal
        addNewProductDescBtn.addEventListener('click', function () {
            addProductDescModal.classList.remove('hidden');
            newProductDescName.focus();
        });

        // Close modal
        closeProductDescModal.addEventListener('click', function () {
            addProductDescModal.classList.add('hidden');
            addProductDescForm.reset();
        });

        cancelProductDesc.addEventListener('click', function () {
            addProductDescModal.classList.add('hidden');
            addProductDescForm.reset();
        });

        // Handle form submission
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
                    // Add new option to product description filter
                    const option = document.createElement('option');
                    option.value = data.product_description.name;
                    option.textContent = data.product_description.name;
                    productNameFilter.appendChild(option);

                    // Select the new option
                    productNameFilter.value = data.product_description.name;

                    // Trigger filter change to refresh the table
                    productNameFilter.dispatchEvent(new Event('change'));

                    // Close modal and reset form
                    addProductDescModal.classList.add('hidden');
                    addProductDescForm.reset();

                    alert('Product description added successfully!');
                } else {
                    alert(data.error || 'Failed to add product description');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to add product description. Please try again.');
            }
        });
    }
});

// Modal Logic for View Details
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

    document.getElementById('vdReorderLevel').textContent = product.reorder_level || 'N/A';

    const unitPrice = parseFloat(product.unit_price || 0);
    const vatAmount = unitPrice * 0.12;
    const priceWithoutVat = unitPrice - vatAmount;
    document.getElementById('vdVat').textContent = `₱${unitPrice.toFixed(2)} (VAT: ₱${vatAmount.toFixed(2)} | Net: ₱${priceWithoutVat.toFixed(2)})`;

    document.getElementById('vdDateOfStock').innerHTML = product.last_restock_date ? formatDateWithTime(product.last_restock_date) : '-';
    document.getElementById('vdExpirationDate').innerHTML = product.expiry_date ? formatDateWithTime(product.expiry_date) : '-';

    document.getElementById('viewDetailsModal').classList.remove('hidden');
};
