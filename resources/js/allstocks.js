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
    if (categoryEl) categoryEl.addEventListener('change', function(e){ currentFilters.category = e.target.value; performSearch(); });
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

async function loadMovements() {
    try {
        if (!window.AllStocks.routes.apiMovements) {
            return;
        }

        const response = await fetch(window.AllStocks.routes.apiMovements);
        const result = await response.json();
        const tbody = document.getElementById('movementFeed');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (result.data && result.data.length > 0) {
            result.data.forEach(entry => {
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
            tbody.innerHTML = '<tr><td colspan="5" class="px-2 py-4 text-center text-slate-500">No recent movements</td></tr>';
        }
    } catch (error) {
        console.error('Error loading movements:', error);
    }
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
                        <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 cursor-pointer" />
                    </td>
                    <td class="px-3 py-2 text-slate-900 font-medium">${product.name}</td>
                    <td class="px-3 py-2 text-slate-600">${product.product_name || 'Uncategorized'}</td>
                    <td class="px-3 py-2 text-slate-600">${product.sku}</td>
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
                        <div class="inline-flex items-center gap-2">
                            <button class="edit-price-btn text-cyan-600 hover:text-cyan-700 text-xs font-medium">Edit</button>
                            <button class="cancel-price-btn hidden text-slate-500 hover:text-slate-700 text-xs">Cancel</button>
                        </div>
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
    const paginationContainer = document.querySelector('.flex.gap-1');
    if (paginationContainer) {
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

        const summary = paginationContainer.parentNode.querySelector('#paginationInfo');
        if (summary) {
            summary.textContent = `Showing ${(currentPage - 1) * perPage + 1} to ${Math.min(currentPage * perPage, pagination.total)} of ${pagination.total} items`;
        }
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
        const editBtn = e.target.closest('.edit-price-btn');
        const cancelBtn = e.target.closest('.cancel-price-btn');

        if (editBtn) {
            const row = editBtn.closest('tr');
            const priceSpan = row.querySelector('.unit-price-text');
            const priceInput = row.querySelector('.unit-price-input');
            const cancel = row.querySelector('.cancel-price-btn');

            if (editBtn.dataset.mode !== 'editing') {
                priceSpan.classList.add('hidden');
                priceInput.classList.remove('hidden');
                priceInput.focus();
                editBtn.textContent = 'Save';
                editBtn.dataset.mode = 'editing';
                cancel.classList.remove('hidden');
                return;
            }

            const newVal = parseFloat(priceInput.value);
            if (isNaN(newVal) || newVal < 0) {
                alert('Please enter a valid non-negative price.');
                return;
            }

            editBtn.disabled = true;
            cancel.disabled = true;
            const productId = priceInput.dataset.productId;

            try {
                const token = window.AllStocks.csrfToken || '';
                const basePriceUrl = window.AllStocks.routes.apiUpdatePriceBase || (window.AllStocks.baseUrl + '/api/product');
                const url = `${basePriceUrl}/${productId}/price`;
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ unit_price: newVal })
                });

                const result = await res.json();
                if (res.ok && result.success) {
                    priceSpan.textContent = '₱' + newVal.toFixed(2);
                    priceSpan.classList.remove('hidden');
                    priceInput.classList.add('hidden');
                    editBtn.textContent = 'Edit';
                    editBtn.dataset.mode = '';
                    cancel.classList.add('hidden');
                } else {
                    console.error('Price update failed', result);
                    alert('Failed to update price: ' + (result.message || 'Unknown error'));
                }
            } catch (err) {
                console.error('Price update network error:', err);
                alert('Network error while updating price.');
            } finally {
                editBtn.disabled = false;
                cancel.disabled = false;
            }
        }

        if (cancelBtn) {
            const row = cancelBtn.closest('tr');
            const priceSpan = row.querySelector('.unit-price-text');
            const priceInput = row.querySelector('.unit-price-input');
            const edit = row.querySelector('.edit-price-btn');

            priceInput.value = priceSpan.textContent.replace(/[^0-9.]/g, '') || '0.00';
            priceSpan.classList.remove('hidden');
            priceInput.classList.add('hidden');
            edit.textContent = 'Edit';
            edit.dataset.mode = '';
            cancelBtn.classList.add('hidden');
        }
    });
}

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

    const refreshMovementsBtn = document.getElementById('refreshMovementsBtn');
    if (refreshMovementsBtn) refreshMovementsBtn.addEventListener('click', () => {
        loadMovements();
    });
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
    loadProducts
};

// Expose key functions to global scope for onclick handlers
window.loadProducts = loadProducts;
window.performSearch = performSearch;
window.resetFilters = resetFilters;
