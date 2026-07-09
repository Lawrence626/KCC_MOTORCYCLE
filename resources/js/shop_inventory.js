console.log('Shop Inventory JS loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    // Load shelves on page load
    loadShelves();

    // Add shelf button
    document.getElementById('add-shelf-button').addEventListener('click', function() {
        document.getElementById('add-shelf-modal').classList.remove('hidden');
    });

    // Cancel add shelf
    document.getElementById('cancel-add-shelf').addEventListener('click', function() {
        document.getElementById('add-shelf-modal').classList.add('hidden');
        document.getElementById('add-shelf-form').reset();
    });

    // Add shelf form submission
    document.getElementById('add-shelf-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/shop-inventory/shelf', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                showToast('Shelf created successfully', 'success');
                document.getElementById('add-shelf-modal').classList.add('hidden');
                document.getElementById('add-shelf-form').reset();
                loadShelves();
            } else {
                showToast(result.message || 'Failed to create shelf', 'error');
            }
        } catch (error) {
            showToast('Error creating shelf', 'error');
        }
    });

    // Transfer from warehouse button
    document.getElementById('transfer-from-warehouse').addEventListener('click', function() {
        loadShelvesForSelect('transfer-warehouse-form', 'shop_shelf_id');
        loadWarehouseProducts();
        document.getElementById('transfer-warehouse-modal').classList.remove('hidden');
    });

    // Cancel transfer from warehouse
    document.getElementById('cancel-transfer-warehouse').addEventListener('click', function() {
        document.getElementById('transfer-warehouse-modal').classList.add('hidden');
        document.getElementById('transfer-warehouse-form').reset();
    });

    // Transfer between shelves button
    document.getElementById('transfer-between-shelves').addEventListener('click', function() {
        loadShelvesForSelect('transfer-shelves-form', 'source_shelf_id');
        loadShelvesForSelect('transfer-shelves-form', 'destination_shelf_id');
        document.getElementById('transfer-shelves-modal').classList.remove('hidden');
    });

    // Cancel transfer between shelves
    document.getElementById('cancel-transfer-shelves').addEventListener('click', function() {
        document.getElementById('transfer-shelves-modal').classList.add('hidden');
        document.getElementById('transfer-shelves-form').reset();
    });

    // Source shelf change - load products
    document.querySelector('#transfer-shelves-form select[name="source_shelf_id"]').addEventListener('change', function() {
        const shelfId = this.value;
        if (shelfId) {
            loadShelfProducts(shelfId);
        }
    });

    // Transfer from warehouse form submission
    document.getElementById('transfer-warehouse-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const shelfId = document.querySelector('#transfer-warehouse-form select[name="shop_shelf_id"]').value;
        const selectedProducts = getSelectedWarehouseProducts();

        if (selectedProducts.length === 0) {
            showToast('Please select at least one product to transfer', 'error');
            return;
        }

        const data = {
            shop_shelf_id: shelfId,
            transfers: selectedProducts
        };

        try {
            const response = await fetch('/api/shop-inventory/transfer-from-warehouse', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                showToast('Products transferred successfully', 'success');
                document.getElementById('transfer-warehouse-modal').classList.add('hidden');
                document.getElementById('transfer-warehouse-form').reset();
                loadShelves();
            } else {
                showToast(result.message || 'Transfer failed', 'error');
            }
        } catch (error) {
            showToast('Error transferring products', 'error');
        }
    });

    // Transfer between shelves form submission
    document.getElementById('transfer-shelves-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const sourceShelfId = document.querySelector('#transfer-shelves-form select[name="source_shelf_id"]').value;
        const destShelfId = document.querySelector('#transfer-shelves-form select[name="destination_shelf_id"]').value;
        const selectedProducts = getSelectedShelfProducts();

        if (selectedProducts.length === 0) {
            showToast('Please select at least one product to transfer', 'error');
            return;
        }

        const data = {
            source_shelf_id: sourceShelfId,
            destination_shelf_id: destShelfId,
            transfers: selectedProducts
        };

        try {
            const response = await fetch('/api/shop-inventory/transfer-between-shelves', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                showToast('Products transferred successfully', 'success');
                document.getElementById('transfer-shelves-modal').classList.add('hidden');
                document.getElementById('transfer-shelves-form').reset();
                loadShelves();
            } else {
                showToast(result.message || 'Transfer failed', 'error');
            }
        } catch (error) {
            showToast('Error transferring products', 'error');
        }
    });

    // View history button
    document.getElementById('view-history').addEventListener('click', function() {
        loadHistory();
        document.getElementById('history-modal').classList.remove('hidden');
    });

    // Close history modal
    document.getElementById('close-history').addEventListener('click', function() {
        document.getElementById('history-modal').classList.add('hidden');
    });

    // Pagination
    let currentPage = 0;
    const SHELVES_PER_PAGE = 4;

    document.getElementById('prev-page').addEventListener('click', function() {
        if (currentPage > 0) {
            currentPage--;
            renderShelvesPage();
        }
    });

    document.getElementById('next-page').addEventListener('click', function() {
        const totalPages = Math.ceil(window.shopShelvesData.length / SHELVES_PER_PAGE);
        if (currentPage < totalPages - 1) {
            currentPage++;
            renderShelvesPage();
        }
    });
});

function loadShelves() {
    console.log('Loading shelves...');
    console.log('Fetching from:', '/api/shop-inventory/shelves');
    console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]')?.content);
    
    fetch('/api/shop-inventory/shelves', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Shelves loaded:', data);
            window.shopShelvesData = data.data;
            renderShelvesPage();
        })
        .catch(error => {
            console.error('Error loading shelves:', error);
        });
}

function renderShelvesPage() {
    console.log('Rendering shelves page...');
    const container = document.getElementById('shop-shelves-grid');
    const noShelvesMessage = document.getElementById('no-shelves-message');
    const pageContainer = document.querySelector('.map-container');
    
    console.log('Container found:', !!container);
    console.log('Shelves data:', window.shopShelvesData);
    
    if (!container) {
        console.error('Container not found!');
        return;
    }
    
    if (!window.shopShelvesData || window.shopShelvesData.length === 0) {
        console.log('No shelves data');
        if (pageContainer) pageContainer.style.display = 'none';
        noShelvesMessage.classList.remove('hidden');
        return;
    }

    console.log('Found', window.shopShelvesData.length, 'shelves');
    if (pageContainer) pageContainer.style.display = 'block';
    noShelvesMessage.classList.add('hidden');

    const SHELVES_PER_PAGE = 4;
    if (!window.currentPage) window.currentPage = 0;
    const currentPage = window.currentPage;
    const totalPages = Math.ceil(window.shopShelvesData.length / SHELVES_PER_PAGE);
    
    console.log('Current page:', currentPage, 'Total pages:', totalPages);
    
    const start = currentPage * SHELVES_PER_PAGE;
    const end = start + SHELVES_PER_PAGE;
    const shelvesToShow = window.shopShelvesData.slice(start, end);

    console.log('Shelves to show:', shelvesToShow.length);
    container.innerHTML = '';

    shelvesToShow.forEach((shelf, index) => {
        console.log('Rendering shelf:', shelf.name, 'with', shelf.shop_inventory?.length, 'products');
        const shelfCard = document.createElement('div');
        shelfCard.className = 'map-unit';
        shelfCard.innerHTML = `
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-md flex items-center justify-center text-white font-semibold si-badge text-sm">${shelf.name ? shelf.name.slice(-1).toUpperCase() : 'S'}</div>
                        <div>
                            <div class="text-base font-semibold text-slate-900">${shelf.name || 'Unnamed Shelf'}</div>
                            <div class="text-xs text-gray-500">${shelf.location || 'No location'}</div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold text-emerald-600">${shelf.occupied}/${shelf.capacity}</div>
                    <div class="text-xs text-gray-500">Occupied</div>
                </div>
            </div>
            <div class="si-location p-3 rounded-xl mb-3">
                <div class="text-xs font-medium text-gray-500 mb-2">Available Slots: ${shelf.available}</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: ${(shelf.occupied / shelf.capacity) * 100}%"></div>
                </div>
            </div>
            <div class="space-y-2 max-h-28 overflow-y-auto">
                ${shelf.shop_inventory && shelf.shop_inventory.length > 0 
                    ? shelf.shop_inventory.slice(0, 10).map(item => `
                        <div class="product-chip">
                            <div class="left">
                                <span class="name">${item.product.name}</span>
                                <span class="meta">${item.product.sku || ''}</span>
                            </div>
                            <span class="qty">${item.quantity}</span>
                        </div>
                    `).join('')
                    : '<p class="text-sm text-gray-400">No products on this shelf</p>'
                }
                ${shelf.shop_inventory && shelf.shop_inventory.length > 10 ? `<p class="text-xs text-gray-400 mt-2">+${shelf.shop_inventory.length - 10} more</p>` : ''}
            </div>
        `;
        container.appendChild(shelfCard);
    });

    // Update pagination info
    const pageInfo = document.getElementById('page-info');
    if (pageInfo) {
        pageInfo.textContent = `Page ${currentPage + 1} of ${totalPages}`;
    }
    
    console.log('Rendering complete');
    
    // Force display of container
    if (pageContainer) {
        pageContainer.style.display = 'block';
        console.log('Container display set to block');
    }
}

function loadShelvesForSelect(formId, selectName) {
    fetch('/api/shop-inventory/shelves')
        .then(response => response.json())
        .then(data => {
            const select = document.querySelector(`#${formId} select[name="${selectName}"]`);
            select.innerHTML = '<option value="">Select a shelf</option>';
            data.data.forEach(shelf => {
                const option = document.createElement('option');
                option.value = shelf.id;
                option.textContent = `${shelf.name} (${shelf.occupied}/${shelf.capacity})`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading shelves:', error);
        });
}

function loadWarehouseProducts() {
    fetch('/api/shop-inventory/warehouse-products')
        .then(response => response.json())
        .then(data => {
            renderWarehouseProducts(data.data);
        })
        .catch(error => {
            console.error('Error loading warehouse products:', error);
        });
}

function renderWarehouseProducts(products) {
    const container = document.getElementById('warehouse-products-container');
    container.innerHTML = '';

    products.forEach(product => {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg';
        div.innerHTML = `
            <input type="checkbox" class="warehouse-product-checkbox" data-product-id="${product.id}" data-warehouse-shelf-id="${product.warehouse_shelf_id}">
            <div class="flex-1">
                <div class="font-medium text-sm">${product.name}</div>
                <div class="text-xs text-gray-500">${product.sku || ''} | Warehouse: ${product.warehouse_shelf_name}</div>
            </div>
            <input type="number" class="warehouse-product-qty w-20 px-2 py-1 border rounded" min="1" max="${product.quantity}" value="1" data-product-id="${product.id}">
            <div class="text-sm font-medium text-gray-600">Available: ${product.quantity}</div>
        `;
        container.appendChild(div);
    });
}

function getSelectedWarehouseProducts() {
    const checkboxes = document.querySelectorAll('.warehouse-product-checkbox:checked');
    const transfers = [];

    checkboxes.forEach(checkbox => {
        const productId = checkbox.dataset.productId;
        const warehouseShelfId = checkbox.dataset.warehouseShelfId;
        const qtyInput = document.querySelector(`.warehouse-product-qty[data-product-id="${productId}"]`);
        const quantity = parseInt(qtyInput.value) || 1;

        transfers.push({
            product_id: productId,
            warehouse_shelf_id: warehouseShelfId,
            quantity: quantity
        });
    });

    return transfers;
}

function loadShelfProducts(shelfId) {
    fetch(`/api/shop-inventory/shelves`)
        .then(response => response.json())
        .then(data => {
            const shelf = data.data.find(s => s.id == shelfId);
            if (shelf) {
                renderShelfProducts(shelf.shop_inventory || []);
            }
        })
        .catch(error => {
            console.error('Error loading shelf products:', error);
        });
}

function renderShelfProducts(products) {
    const container = document.getElementById('source-shelf-products-container');
    container.innerHTML = '';

    products.forEach(item => {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg';
        div.innerHTML = `
            <input type="checkbox" class="shelf-product-checkbox" data-product-id="${item.product_id}">
            <div class="flex-1">
                <div class="font-medium text-sm">${item.product.name}</div>
                <div class="text-xs text-gray-500">${item.product.sku || ''}</div>
            </div>
            <input type="number" class="shelf-product-qty w-20 px-2 py-1 border rounded" min="1" max="${item.quantity}" value="1" data-product-id="${item.product_id}">
            <div class="text-sm font-medium text-gray-600">Available: ${item.quantity}</div>
        `;
        container.appendChild(div);
    });
}

function getSelectedShelfProducts() {
    const checkboxes = document.querySelectorAll('.shelf-product-checkbox:checked');
    const transfers = [];

    checkboxes.forEach(checkbox => {
        const productId = checkbox.dataset.productId;
        const qtyInput = document.querySelector(`.shelf-product-qty[data-product-id="${productId}"]`);
        const quantity = parseInt(qtyInput.value) || 1;

        transfers.push({
            product_id: productId,
            quantity: quantity
        });
    });

    return transfers;
}

function loadHistory() {
    fetch('/api/shop-inventory/history')
        .then(response => response.json())
        .then(data => {
            renderHistory(data.data);
        })
        .catch(error => {
            console.error('Error loading history:', error);
        });
}

function renderHistory(history) {
    const container = document.getElementById('history-container');
    container.innerHTML = '';

    if (history.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-400">No history records found</p>';
        return;
    }

    history.forEach(record => {
        const div = document.createElement('div');
        div.className = 'p-3 bg-gray-50 rounded-lg';
        
        const actionLabels = {
            'transfer_in': 'Transfer In',
            'transfer_out': 'Transfer Out',
            'pos_sale': 'POS Sale',
            'restock': 'Restock'
        };

        div.innerHTML = `
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-medium text-sm">${record.product.name}</div>
                    <div class="text-xs text-gray-500">${actionLabels[record.action_type] || record.action_type}</div>
                    <div class="text-xs text-gray-400">${record.notes || ''}</div>
                </div>
                <div class="text-right">
                    <div class="font-medium ${record.quantity_change > 0 ? 'text-emerald-600' : 'text-red-600'}">
                        ${record.quantity_change > 0 ? '+' : ''}${record.quantity_change}
                    </div>
                    <div class="text-xs text-gray-400">${new Date(record.created_at).toLocaleString()}</div>
                </div>
            </div>
        `;
        container.appendChild(div);
    });
}

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">×</button>
    `;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
