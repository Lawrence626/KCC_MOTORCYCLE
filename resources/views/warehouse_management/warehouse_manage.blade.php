<x-layouts.app :title="__('Warehouse Management')">
    <style>
        :root {
            --brand: #10b981; /* green accent */
            --brand-dark: #047857;
            --card-bg: #ffffff;
            --muted: #6b7280;
        }
        .wm-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff }
        .wm-card { border: 1px solid rgba(2,6,23,0.04); background: var(--card-bg) }
        .wm-location { background: #f7fdf8; border: 1px dashed rgba(4,120,87,0.06) }
        .product-chip { background: rgba(16,185,129,0.08); border: 1px solid rgba(4,120,87,0.12); color: var(--brand-dark) }
        .warehouse-shelves { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); grid-auto-rows: minmax(220px, auto); }
        @media (max-width: 1024px) { .warehouse-shelves { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 640px) { .warehouse-shelves { grid-template-columns: 1fr; } }
    </style>

    <div class="space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Warehouse Management</h1>
                <p class="mt-2 text-sm text-gray-500">Track and manage storage locations and products across your warehouses.</p>
            </div>
            <div class="flex items-center space-x-3">
                <input id="wm-search" type="search" placeholder="Search product or SKU..." class="px-5 py-3 text-base border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300" />
                <button id="add-shelf-button" type="button" class="inline-flex items-center px-5 py-3 wm-badge rounded-xl text-base font-medium">Add Shelf</button>
            </div>
        </div>

        <div class="mt-4 flex items-center space-x-4">
            <label for="warehouse-select" class="text-base font-medium text-gray-700">Select Warehouse:</label>
            <select id="warehouse-select" class="px-5 py-3 text-base border rounded-xl">
                <option value="all">-- Select --</option>
            </select>
        </div>

        <div class="grid gap-6 mt-4">
            @foreach($warehouses as $index => $wh)
                <div class="wm-card rounded-lg p-4 shadow-sm wh-card" data-index="{{ $index }}" style="display:none;">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-md flex items-center justify-center text-white font-semibold wm-badge">{{ strtoupper(substr($wh['code'], -1)) }}</div>
                                <div>
                                    <div class="text-lg font-semibold text-slate-900">{{ $wh['name'] }}</div>
                                    <div class="text-xs text-gray-500">Code: {{ $wh['code'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="#" class="text-sm text-sky-600">Manage</a>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="map-container border rounded-md p-3 bg-gray-50">
                            <div class="warehouse-shelves grid gap-6" data-index="{{ $index }}"></div>
                            <div class="pagination mt-4 flex items-center justify-between text-sm text-gray-600">
                                <button type="button" class="prev-page px-3 py-2 border rounded-md bg-white" data-index="{{ $index }}">Previous</button>
                                <div class="page-info" data-index="{{ $index }}">Page 1 of 1</div>
                                <button type="button" class="next-page px-3 py-2 border rounded-md bg-white" data-index="{{ $index }}">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const warehouses = @json($warehouses);
            const products = @json($products);
            const PRODUCTS_PER_SHELF = 10;
            const productOptionsHtml = `
                <option value="">Select product</option>
                ${products.map(p => `<option value="${p.id}" data-sku="${p.sku}" data-name="${p.name}" data-price="${p.price}">${p.name} (${p.sku})</option>`).join('')}
            `;
            const select = document.getElementById('warehouse-select');
            warehouses.forEach((w, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.text = w.name;
                select.appendChild(opt);
            });

            const warehousePage = warehouses.map(() => 0);

            select.addEventListener('change', function() {
                showIndex(this.value);
            });

            if (warehouses.length) {
                select.selectedIndex = 1; // Warehouse A
                showIndex(select.value);
            }

            function showIndex(i) {
                document.querySelectorAll('.wh-card').forEach(el => el.style.display = 'none');
                if (i === 'all' || i === null) return;
                const index = parseInt(i, 10);
                const el = document.querySelector('.wh-card[data-index="'+index+'"]');
                if (el) {
                    el.style.display = '';
                    renderWarehousePage(index, warehousePage[index] || 0);
                }
            }

            document.getElementById('modal-close').addEventListener('click', closeModal);
            document.getElementById('modal-cancel').addEventListener('click', closeModal);
            document.getElementById('modal-form').addEventListener('submit', handleModalSave);
            const addShelfButton = document.getElementById('add-shelf-button');
            if (addShelfButton) {
                addShelfButton.addEventListener('click', function() {
                    const currentWarehouse = parseInt(select.value, 10);
                    if (Number.isNaN(currentWarehouse)) return;
                    const nextSlot = warehouses[currentWarehouse].locations.length;
                    showModal('Add Shelf', currentWarehouse, nextSlot);
                });
            }

            document.querySelectorAll('.prev-page').forEach(button => {
                button.addEventListener('click', function() {
                    const warehouseIndex = parseInt(this.dataset.index, 10);
                    warehousePage[warehouseIndex] = Math.max(0, warehousePage[warehouseIndex] - 1);
                    renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
                });
            });

            document.querySelectorAll('.next-page').forEach(button => {
                button.addEventListener('click', function() {
                    const warehouseIndex = parseInt(this.dataset.index, 10);
                    const warehouse = warehouses[warehouseIndex];
                    const totalPages = Math.max(1, Math.ceil(warehouse.locations.length / 9));
                    warehousePage[warehouseIndex] = Math.min(totalPages - 1, warehousePage[warehouseIndex] + 1);
                    renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
                });
            });

            function renderWarehousePage(warehouseIndex, pageIndex = 0) {
                const warehouse = warehouses[warehouseIndex];
                const start = pageIndex * 9;
                const shelvesContainer = document.querySelector(`.warehouse-shelves[data-index="${warehouseIndex}"]`);
                const pageInfo = document.querySelector(`.page-info[data-index="${warehouseIndex}"]`);
                const prevButton = document.querySelector(`.prev-page[data-index="${warehouseIndex}"]`);
                const nextButton = document.querySelector(`.next-page[data-index="${warehouseIndex}"]`);
                const totalPages = Math.max(1, Math.ceil(warehouse.locations.length / 9));

                shelvesContainer.classList.add('grid', 'grid-cols-3', 'gap-6');

                let html = '';
                for (let slot = 1; slot <= 9; slot += 1) {
                    const absoluteIndex = start + slot - 1;
                    const loc = warehouse.locations[absoluteIndex] || null;
                        const slotCount = loc ? loc.products.length : 0;
                    let productsHtml = '';
                    for (let productSlot = 0; productSlot < PRODUCTS_PER_SHELF; productSlot += 1) {
                        const product = loc ? (loc.products[productSlot] || null) : null;
                        if (product) {
                            productsHtml += `<div class="product-chip rounded-xl px-3 py-2 text-sm flex items-center justify-between"><div class="font-medium">${product.name}</div><div class="text-gray-600">${product.qty}</div></div>`;
                        } else {
                            productsHtml += `<div class="product-chip opacity-50 rounded-xl px-3 py-2 text-sm text-gray-500 border border-dashed border-gray-200">Empty slot</div>`;
                        }
                    }
                    const addProductOption = loc && loc.products.length < PRODUCTS_PER_SHELF ? '<option value="add-product">Add Product</option>' : '';
                    html += `
                        <div class="map-unit rounded-2xl shadow-lg relative bg-white border border-gray-200" style="min-height: 220px; padding: 1.25rem;">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-base font-semibold text-slate-900">Shelf ${slot}</div>
                                    <div class="text-sm text-gray-500">${loc ? loc.name : 'Empty slot'}</div>
                                    <div class="text-xs text-gray-400 mt-1">${loc ? `${slotCount}/${PRODUCTS_PER_SHELF} products` : `0/${PRODUCTS_PER_SHELF} products`}</div>
                                </div>
                                <div>
                                    <label class="sr-only" for="action-select-${warehouseIndex}-${slot}">Shelf actions</label>
                                    <select id="action-select-${warehouseIndex}-${slot}" class="action-select text-sm text-slate-700 px-4 py-2 border border-gray-200 rounded-full bg-white hover:bg-gray-50 cursor-pointer appearance-none pr-8" data-index="${warehouseIndex}" data-slot="${absoluteIndex}">
                                        <option value="">Actions</option>
                                        ${loc ? `${addProductOption}<option value="archive-shelf">Archive Shelf</option><option value="delete-shelf">Delete Shelf</option>` : '<option value="add-shelf">Add Shelf</option>'}
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-2 grid-cols-2">
                                ${productsHtml}
                            </div>
                        </div>
                    `;
                }

                shelvesContainer.innerHTML = html;
                pageInfo.textContent = `Page ${pageIndex + 1} of ${totalPages}`;
                prevButton.disabled = pageIndex === 0;
                nextButton.disabled = pageIndex === totalPages - 1;

                shelvesContainer.querySelectorAll('.action-select').forEach(selectEl => {
                    selectEl.addEventListener('change', function() {
                        const action = this.value;
                        const slotIndex = parseInt(this.dataset.slot, 10);
                        if (action === 'add-shelf') {
                            openShelfModal(slotIndex);
                        } else if (action === 'add-product') {
                            openProductModal(slotIndex);
                        } else if (action === 'archive-shelf') {
                            archiveShelf(slotIndex);
                        } else if (action === 'delete-shelf') {
                            deleteShelf(slotIndex);
                        }
                        this.value = '';
                    });
                });
            }

            function openShelfModal(slotIndex) {
                const currentWarehouse = parseInt(select.value, 10);
                if (Number.isNaN(currentWarehouse)) return;
                showModal('Add Shelf', currentWarehouse, slotIndex, 'addShelf');
            }

            function openProductModal(slotIndex) {
                const currentWarehouse = parseInt(select.value, 10);
                if (Number.isNaN(currentWarehouse)) return;
                showModal('Add Product', currentWarehouse, slotIndex, 'addProduct');
            }

            function showModal(title, warehouseIndex, slot, mode = 'addShelf') {
                document.getElementById('modal-title').textContent = title;
                document.getElementById('modal-warehouse-index').value = warehouseIndex;
                document.getElementById('modal-slot').value = slot;
                document.getElementById('modal-mode').value = mode;
                const shelfName = document.getElementById('modal-shelf-name');
                const currentShelf = warehouses[warehouseIndex].locations[slot] || null;

                if (mode === 'addProduct' && currentShelf) {
                    shelfName.value = currentShelf.name;
                    shelfName.readOnly = true;
                    resetProductRows(currentShelf.products);
                } else {
                    shelfName.value = currentShelf ? currentShelf.name : '';
                    shelfName.readOnly = false;
                    resetProductRows([]);
                }

                document.getElementById('modal-backdrop').classList.remove('hidden');
                document.getElementById('modal-backdrop').classList.add('flex');
            }

            async function saveStock(productId, quantity, price, notes) {
                try {
                    const response = await fetch('{{ route('warehouse.management.add_product') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity,
                            unit_price: price,
                            notes,
                        }),
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        alert(result.message || 'Failed to save stock.');
                        return false;
                    }
                    return true;
                } catch (error) {
                    alert('Failed to save stock: ' + error.message);
                    return false;
                }
            }

            function closeModal() {
                document.getElementById('modal-backdrop').classList.add('hidden');
                document.getElementById('modal-backdrop').classList.remove('flex');
            }

            function getProductRows() {
                return Array.from(document.querySelectorAll('.product-row')).map(row => {
                    const select = row.querySelector('.product-select');
                    const productId = select.value ? parseInt(select.value, 10) : null;
                    const sku = row.querySelector('.product-sku').value.trim();
                    const name = select.selectedOptions[0]?.dataset.name || '';
                    const qty = parseInt(row.querySelector('.product-qty').value, 10) || 0;
                    const price = parseFloat(row.querySelector('.product-price').value) || 0;
                    return { product_id: productId, sku, name, qty, price };
                }).filter(p => p.product_id && p.qty > 0);
            }

            function addProductRow(product = {}) {
                const container = document.getElementById('modal-product-rows');
                if (container.querySelectorAll('.product-row').length >= PRODUCTS_PER_SHELF) {
                    return;
                }
                const row = document.createElement('div');
                row.className = 'product-row grid gap-3 md:grid-cols-5 items-end';
                row.innerHTML = `
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-700">Product</label>
                        <select class="product-select mt-1 block w-full border rounded-md px-3 py-2" data-selected="${product.product_id || ''}">
                            ${productOptionsHtml}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700">SKU</label>
                        <input type="text" readonly class="product-sku mt-1 block w-full border rounded-md px-3 py-2 bg-slate-50" value="${product.sku || ''}" placeholder="SKU" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700">Qty</label>
                        <input type="number" min="0" class="product-qty mt-1 block w-full border rounded-md px-3 py-2" value="${product.qty || ''}" placeholder="Qty" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700">Price</label>
                        <input type="number" step="0.01" min="0" class="product-price mt-1 block w-full border rounded-md px-3 py-2" value="${product.price || ''}" placeholder="Price" />
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-product-row text-sm text-red-600 hover:text-red-800">✕</button>
                    </div>
                `;
                container.appendChild(row);

                const selectEl = row.querySelector('.product-select');
                const skuInput = row.querySelector('.product-sku');
                const priceInput = row.querySelector('.product-price');

                selectEl.addEventListener('change', function() {
                    const selected = selectEl.selectedOptions[0];
                    skuInput.value = selected.dataset.sku || '';
                    if (!priceInput.value) {
                        priceInput.value = selected.dataset.price || '';
                    }
                });

                if (product.product_id) {
                    selectEl.value = product.product_id;
                }

                row.querySelector('.remove-product-row').addEventListener('click', () => {
                    row.remove();
                    updateAddRowButtonState();
                });
                updateAddRowButtonState();
            }

            function updateAddRowButtonState() {
                const container = document.getElementById('modal-product-rows');
                const addButton = document.getElementById('modal-add-product-row');
                const rowCount = container.querySelectorAll('.product-row').length;
                addButton.disabled = rowCount >= PRODUCTS_PER_SHELF;
                addButton.classList.toggle('opacity-40', addButton.disabled);
            }

            function resetProductRows(products = []) {
                const container = document.getElementById('modal-product-rows');
                container.innerHTML = '';
                const rows = products.slice(0, PRODUCTS_PER_SHELF);
                rows.forEach(addProductRow);
                for (let i = rows.length; i < PRODUCTS_PER_SHELF; i += 1) {
                    addProductRow();
                }
                updateAddRowButtonState();
            }

            document.getElementById('modal-add-product-row').addEventListener('click', function() {
                addProductRow();
            });

            function archiveShelf(slot) {
                closeModal();
                alert('Archive shelf ' + slot + '. This is a UI mockup; backend persistence is not wired yet.');
            }

            function deleteShelf(slot) {
                closeModal();
                alert('Delete shelf ' + slot + '. This is a UI mockup; backend persistence is not wired yet.');
            }

            async function handleModalSave(event) {
                event.preventDefault();
                const warehouseIndex = parseInt(document.getElementById('modal-warehouse-index').value, 10);
                const slotIndex = parseInt(document.getElementById('modal-slot').value, 10);
                const mode = document.getElementById('modal-mode').value;
                const shelfName = document.getElementById('modal-shelf-name').value.trim() || `New Shelf ${slotIndex + 1}`;
                const rows = getProductRows();
                const product = rows[0] || null;

                if (!Number.isNaN(warehouseIndex) && warehouses[warehouseIndex]) {
                    if (mode === 'addProduct' && warehouses[warehouseIndex].locations[slotIndex]) {
                        const existingShelf = warehouses[warehouseIndex].locations[slotIndex];
                        const productRows = getProductRows().slice(0, PRODUCTS_PER_SHELF);
                        existingShelf.products = productRows;

                        for (const row of productRows) {
                            if (!row.product_id || row.qty <= 0) continue;
                            await saveStock(row.product_id, row.qty, row.price, `Added to shelf ${existingShelf.name}`);
                        }
                        renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
                        closeModal();
                        return;
                    }

                    for (const row of rows) {
                        if (!row.product_id || row.qty <= 0) continue;
                        const saved = await saveStock(row.product_id, row.qty, row.price, `Added to ${shelfName}`);
                        if (!saved) {
                            return;
                        }
                    }

                    const newShelf = {
                        name: shelfName,
                        layout: { x: 1, y: 1, w: 3, h: 2 },
                        products: rows.map(row => ({
                            product_id: row.product_id,
                            sku: row.sku,
                            name: row.name,
                            qty: row.qty,
                            price: row.price,
                        })),
                    };

                    const warehouse = warehouses[warehouseIndex];
                    if (slotIndex >= warehouse.locations.length) {
                        warehouse.locations[slotIndex] = newShelf;
                    } else if (!warehouse.locations[slotIndex]) {
                        warehouse.locations.splice(slotIndex, 0, newShelf);
                    } else {
                        warehouse.locations[slotIndex] = newShelf;
                    }

                    renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
                }

                closeModal();
            }

            // select first warehouse (A) by default
            if (warehouses.length) {
                select.value = 0;
                showIndex(0);
            }
        });
    </script>

    <div id="modal-backdrop" class="fixed inset-0 bg-black/30 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 id="modal-title" class="text-xl font-semibold text-slate-900">Modal Title</h2>
                <button id="modal-close" class="text-slate-500 hover:text-slate-800">✕</button>
            </div>
            <form id="modal-form" class="space-y-4">
                <input type="hidden" id="modal-warehouse-index" />
                <input type="hidden" id="modal-slot" />
                <input type="hidden" id="modal-mode" value="addShelf" />

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Shelf Name</label>
                        <input id="modal-shelf-name" type="text" class="mt-1 block w-full border rounded-md px-3 py-2" placeholder="Enter shelf name" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-slate-700">Products</label>
                            <button type="button" id="modal-add-product-row" class="text-sm text-slate-600 hover:text-slate-900">+ Add product</button>
                        </div>
                        <div id="modal-product-rows" class="space-y-3 mt-3"></div>
                        <p class="mt-2 text-xs text-gray-500">Choose existing inventory items, quantity, and price before saving.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" id="modal-cancel" class="px-4 py-2 text-sm rounded-md border border-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm rounded-md text-white bg-emerald-600">Save</button>
                </div>
            </form>
        </div>
    </div>

        <div class="mt-4 text-sm text-gray-500">
            <strong class="text-slate-800">Note:</strong> This page is now backed by live inventory data from the system. Shelves display actual products and quantities where available.
        </div>
    </div>
</x-layouts.app>
