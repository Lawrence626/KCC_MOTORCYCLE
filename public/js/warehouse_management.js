document.addEventListener('DOMContentLoaded', function () {
    const warehouses = window.WarehouseData?.warehouses || [];
    const products = window.WarehouseData?.products || [];
    const PRODUCTS_PER_SHELF = 10;
    const SHELVES_PER_PAGE = 4;
    const warehousePage = warehouses.map(() => 0);
    let warehouseSearchQuery = '';
    let productDescriptionFilter = '';
    let brandFilter = '';

    const productDescFilter = document.getElementById('wm-product-description-filter');
    const brandFilterEl = document.getElementById('wm-brand-filter');
    const clearFiltersBtn = document.getElementById('wm-clear-filters');
    const searchInput = document.getElementById('wm-search');
    const warehouseSelector = document.getElementById('warehouse-selector');
    const toastContainer = document.getElementById('toast-container');

    // =========================================================================
    // 1. WAREHOUSE PRODUCT, CATEGORY & BRAND EXTRACTION
    // =========================================================================

    /**
     * Get all active products belonging to the specified warehouse's non-archived shelves.
     */
    function getWarehouseProducts(warehouse) {
        if (!warehouse || !Array.isArray(warehouse.locations)) {
            return [];
        }
        const list = [];
        warehouse.locations.forEach(shelf => {
            if (shelf && !shelf.archived && Array.isArray(shelf.products)) {
                shelf.products.forEach(p => {
                    if (p && typeof p === 'object' && (p.name || p.description || p.sku || p.product_name)) {
                        list.push(p);
                    }
                });
            }
        });
        return list;
    }

    /**
     * Extract unique, alphabetically sorted category names existing in the selected warehouse.
     */
    function getWarehouseCategories(warehouse) {
        const prods = getWarehouseProducts(warehouse);
        const map = new Map();
        prods.forEach(p => {
            const cat = (p.description || p.category || p.product_description || p.name || '').trim();
            if (cat && cat !== '—' && cat !== '-' && !map.has(cat.toLowerCase())) {
                map.set(cat.toLowerCase(), cat);
            }
        });
        return Array.from(map.values()).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
    }

    /**
     * Extract unique, alphabetically sorted brand names existing in the selected warehouse,
     * optionally filtered by a selected category.
     */
    function getWarehouseBrands(warehouse, categoryFilter = '') {
        const prods = getWarehouseProducts(warehouse);
        const catLower = (categoryFilter || '').trim().toLowerCase();
        const map = new Map();
        prods.forEach(p => {
            if (catLower) {
                const pCat = (p.description || p.category || p.product_description || p.name || '').trim().toLowerCase();
                if (pCat !== catLower) return;
            }
            const br = (p.brand || '').trim();
            if (br && br !== '—' && br !== '-' && !map.has(br.toLowerCase())) {
                map.set(br.toLowerCase(), br);
            }
        });
        return Array.from(map.values()).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
    }

    /**
     * Update Brand Filter dropdown based strictly on the selected warehouse and category.
     */
    function updateWarehouseBrandDropdown(warehouse, categoryFilter = '') {
        if (!warehouse) return;

        const brands = getWarehouseBrands(warehouse, categoryFilter);
        const brandSelect = document.getElementById('wm-brand-filter');
        const brandMenu = document.getElementById('dd-brand-menu');
        const brandLabel = document.getElementById('dd-brand-label');

        const currentBrandLower = (brandFilter || '').trim().toLowerCase();
        const isBrandValid = brands.some(b => b.toLowerCase() === currentBrandLower);
        if (!isBrandValid) {
            brandFilter = '';
            if (brandLabel) brandLabel.textContent = 'All Brands';
        } else {
            const matchingBrand = brands.find(b => b.toLowerCase() === currentBrandLower);
            if (brandLabel && matchingBrand) brandLabel.textContent = matchingBrand;
        }

        if (brandSelect) {
            brandSelect.innerHTML = '<option value="">All Brands</option>';
            brands.forEach(brand => {
                const opt = document.createElement('option');
                opt.value = brand;
                opt.textContent = brand;
                if (brandFilter && brand.toLowerCase() === brandFilter.toLowerCase()) {
                    opt.selected = true;
                }
                brandSelect.appendChild(opt);
            });
            brandSelect.value = brandFilter;
        }

        if (brandMenu) {
            brandMenu.innerHTML = '';
            const allBrandBtn = document.createElement('button');
            allBrandBtn.type = 'button';
            allBrandBtn.className = 'w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer';
            allBrandBtn.textContent = 'All Brands';
            allBrandBtn.addEventListener('click', () => {
                if (window.selectDropdownOption) {
                    window.selectDropdownOption('wm-brand-filter', 'dd-brand-label', 'dd-brand-menu', '', 'All Brands');
                } else {
                    brandFilter = '';
                    if (brandSelect) { brandSelect.value = ''; brandSelect.dispatchEvent(new Event('change')); }
                    if (brandLabel) brandLabel.textContent = 'All Brands';
                    brandMenu.classList.add('hidden');
                }
            });
            brandMenu.appendChild(allBrandBtn);

            brands.forEach(brand => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer';
                btn.textContent = brand;
                btn.addEventListener('click', () => {
                    if (window.selectDropdownOption) {
                        window.selectDropdownOption('wm-brand-filter', 'dd-brand-label', 'dd-brand-menu', brand, brand);
                    } else {
                        brandFilter = brand;
                        if (brandSelect) { brandSelect.value = brand; brandSelect.dispatchEvent(new Event('change')); }
                        if (brandLabel) brandLabel.textContent = brand;
                        brandMenu.classList.add('hidden');
                    }
                });
                brandMenu.appendChild(btn);
            });
        }
    }

    /**
     * Update Category and Brand dropdowns based strictly on the selected warehouse's products.
     * Resets active filters if their values are not available in the selected warehouse.
     */
    function updateWarehouseFilterDropdowns(warehouse) {
        if (!warehouse) return;

        const categories = getWarehouseCategories(warehouse);

        // -------------------------------------------------------------
        // 1. Update Category Filter Dropdown
        // -------------------------------------------------------------
        const catSelect = document.getElementById('wm-product-description-filter');
        const catMenu = document.getElementById('dd-desc-menu');
        const catLabel = document.getElementById('dd-desc-label');

        const currentCatLower = (productDescriptionFilter || '').trim().toLowerCase();
        const isCatValid = categories.some(c => c.toLowerCase() === currentCatLower);
        if (!isCatValid) {
            productDescriptionFilter = '';
            if (catLabel) catLabel.textContent = 'All Categories';
        } else {
            const matchingCat = categories.find(c => c.toLowerCase() === currentCatLower);
            if (catLabel && matchingCat) catLabel.textContent = matchingCat;
        }

        if (catSelect) {
            catSelect.innerHTML = '<option value="">All Categories</option>';
            categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                if (productDescriptionFilter && cat.toLowerCase() === productDescriptionFilter.toLowerCase()) {
                    opt.selected = true;
                }
                catSelect.appendChild(opt);
            });
            catSelect.value = productDescriptionFilter;
        }

        if (catMenu) {
            catMenu.innerHTML = '';
            const allCatBtn = document.createElement('button');
            allCatBtn.type = 'button';
            allCatBtn.className = 'w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer';
            allCatBtn.textContent = 'All Categories';
            allCatBtn.addEventListener('click', () => {
                if (window.selectDropdownOption) {
                    window.selectDropdownOption('wm-product-description-filter', 'dd-desc-label', 'dd-desc-menu', '', 'All Categories');
                } else {
                    productDescriptionFilter = '';
                    if (catSelect) { catSelect.value = ''; catSelect.dispatchEvent(new Event('change')); }
                    if (catLabel) catLabel.textContent = 'All Categories';
                    catMenu.classList.add('hidden');
                }
            });
            catMenu.appendChild(allCatBtn);

            categories.forEach(cat => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer';
                btn.textContent = cat;
                btn.addEventListener('click', () => {
                    if (window.selectDropdownOption) {
                        window.selectDropdownOption('wm-product-description-filter', 'dd-desc-label', 'dd-desc-menu', cat, cat);
                    } else {
                        productDescriptionFilter = cat;
                        if (catSelect) { catSelect.value = cat; catSelect.dispatchEvent(new Event('change')); }
                        if (catLabel) catLabel.textContent = cat;
                        catMenu.classList.add('hidden');
                    }
                });
                catMenu.appendChild(btn);
            });
        }

        // -------------------------------------------------------------
        // 2. Update Brand Filter Dropdown (Filtered by selected category if any)
        // -------------------------------------------------------------
        updateWarehouseBrandDropdown(warehouse, productDescriptionFilter);
    }

    // =========================================================================
    // 2. FILTERING LOGIC (CATEGORY, BRAND, SEARCH QUERY)
    // =========================================================================

    /**
     * Check if a product satisfies active category, brand, and search filters.
     */
    function productMatchesWarehouseFilters(product, query, productDesc, brand) {
        if (!product) return false;

        const q = (query || '').trim().toLowerCase();
        const cat = (productDesc || '').trim().toLowerCase();
        const br = (brand || '').trim().toLowerCase();

        // 1. Category filter
        if (cat) {
            const prodCat = (product.description || product.category || product.product_description || product.name || '').trim().toLowerCase();
            if (prodCat !== cat) {
                return false;
            }
        }

        // 2. Brand filter
        if (br) {
            const prodBrand = (product.brand || '').trim().toLowerCase();
            if (prodBrand !== br) {
                return false;
            }
        }

        // 3. Search query
        if (q) {
            const name = (product.name || '').toLowerCase();
            const prodName = (product.product_name || '').toLowerCase();
            const desc = (product.description || '').toLowerCase();
            const sku = (product.sku || '').toLowerCase();
            const autoSku = `kcc_${(product.sku || product.name || '').replace(/[^a-z0-9\-\+]/gi, '')}`.toLowerCase();
            const comp = (product.compatible_model || product.compatibility || '').toLowerCase();
            const pBrand = (product.brand || '').toLowerCase();

            const matches = name.includes(q) ||
                prodName.includes(q) ||
                desc.includes(q) ||
                sku.includes(q) ||
                autoSku.includes(q) ||
                comp.includes(q) ||
                pBrand.includes(q);

            if (!matches) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a shelf location should be visible under active filters.
     */
    function shelfMatchesQuery(loc, query, productDesc, brand) {
        if (!loc || loc.archived) {
            return false;
        }

        const q = (query || '').trim().toLowerCase();
        const cat = (productDesc || '').trim().toLowerCase();
        const br = (brand || '').trim().toLowerCase();

        if (!q && !cat && !br) {
            return true;
        }

        if (Array.isArray(loc.products) && loc.products.length > 0) {
            const hasMatchingProduct = loc.products.some(product =>
                productMatchesWarehouseFilters(product, q, cat, br)
            );
            if (hasMatchingProduct) {
                return true;
            }
        }

        // If only search query is active and no category/brand filter is set, allow shelf name match
        if (q && !cat && !br && (loc.name || '').toLowerCase().includes(q)) {
            return true;
        }

        return false;
    }

    function getFilteredShelfSlots(warehouse) {
        if (!warehouse || !Array.isArray(warehouse.locations)) {
            return [];
        }
        const query = warehouseSearchQuery.trim().toLowerCase();
        const productDesc = productDescriptionFilter.trim();
        const brand = brandFilter.trim();
        const allSlots = warehouse.locations.map(loc => ({ loc, slotIndex: loc?.slot_index ?? null }));

        if (!query && !productDesc && !brand) {
            return allSlots;
        }

        return allSlots.filter(item => shelfMatchesQuery(item.loc, query, productDesc, brand));
    }

    // =========================================================================
    // 3. STATS & PRODUCT COUNTS
    // =========================================================================

    function updateWarehouseStats(index) {
        const warehouse = warehouses[index];
        if (!warehouse || !warehouse.locations) return;

        const q = (warehouseSearchQuery || '').trim().toLowerCase();
        const cat = (productDescriptionFilter || '').trim().toLowerCase();
        const br = (brandFilter || '').trim().toLowerCase();
        const isFiltering = Boolean(q || cat || br);

        let totalProducts = 0;
        let matchingProducts = 0;
        let usedSlots = 0;

        warehouse.locations.forEach(location => {
            if (location && !location.archived && Array.isArray(location.products)) {
                location.products.forEach(p => {
                    if (p && (p.name || p.description || p.sku || p.product_name)) {
                        totalProducts++;
                        usedSlots++;
                        if (productMatchesWarehouseFilters(p, q, cat, br)) {
                            matchingProducts++;
                        }
                    }
                });
            }
        });

        const activeShelves = warehouse.locations.filter(l => l && !l.archived);
        const totalSlots = activeShelves.length * PRODUCTS_PER_SHELF;
        const emptySlots = Math.max(0, totalSlots - usedSlots);

        const displayProductCount = isFiltering ? matchingProducts : totalProducts;

        const productsEl = document.getElementById(`warehouseProducts-${index}`);
        const emptyEl = document.getElementById(`warehouseEmptySlots-${index}`);
        const selectedProductsEl = document.getElementById('selectedWarehouseProducts');
        const selectedEmptyEl = document.getElementById('selectedWarehouseEmptySlots');

        if (productsEl) productsEl.textContent = displayProductCount.toString();
        if (emptyEl) emptyEl.textContent = emptySlots.toString();
        if (selectedProductsEl) selectedProductsEl.textContent = displayProductCount.toString();
        if (selectedEmptyEl) selectedEmptyEl.textContent = emptySlots.toString();
    }

    // =========================================================================
    // 4. EVENT LISTENERS FOR FILTERS
    // =========================================================================

    if (productDescFilter) {
        productDescFilter.addEventListener('change', function () {
            productDescriptionFilter = this.value;
            const currentWarehouse = getCurrentWarehouseIndex();
            if (!Number.isNaN(currentWarehouse) && warehouses[currentWarehouse]) {
                updateWarehouseBrandDropdown(warehouses[currentWarehouse], productDescriptionFilter);
                warehousePage[currentWarehouse] = 0;
                renderWarehousePage(currentWarehouse, 0);
                updateWarehouseStats(currentWarehouse);
            }
        });
    }

    if (brandFilterEl) {
        brandFilterEl.addEventListener('change', function () {
            brandFilter = this.value;
            const currentWarehouse = getCurrentWarehouseIndex();
            if (!Number.isNaN(currentWarehouse) && warehouses[currentWarehouse]) {
                warehousePage[currentWarehouse] = 0;
                renderWarehousePage(currentWarehouse, 0);
                updateWarehouseStats(currentWarehouse);
            }
        });
    }

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function () {
            warehouseSearchQuery = '';
            productDescriptionFilter = '';
            brandFilter = '';

            if (searchInput) searchInput.value = '';
            if (productDescFilter) productDescFilter.value = '';
            if (brandFilterEl) brandFilterEl.value = '';

            const catLabel = document.getElementById('dd-desc-label');
            if (catLabel) catLabel.textContent = 'All Categories';

            const brandLabel = document.getElementById('dd-brand-label');
            if (brandLabel) brandLabel.textContent = 'All Brands';

            const currentWarehouse = getCurrentWarehouseIndex();
            if (!Number.isNaN(currentWarehouse) && warehouses[currentWarehouse]) {
                warehousePage[currentWarehouse] = 0;
                updateWarehouseFilterDropdowns(warehouses[currentWarehouse]);
                renderWarehousePage(currentWarehouse, 0);
                updateWarehouseStats(currentWarehouse);
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            warehouseSearchQuery = this.value.trim().toLowerCase();
            const currentWarehouse = getCurrentWarehouseIndex();
            if (!Number.isNaN(currentWarehouse) && warehouses[currentWarehouse]) {
                warehousePage[currentWarehouse] = 0;
                renderWarehousePage(currentWarehouse, 0);
                updateWarehouseStats(currentWarehouse);
            }
        });
    }

    // =========================================================================
    // 5. WAREHOUSE SWITCHING & RENDERING
    // =========================================================================

    function showWarehouse(warehouseId) {
        document.querySelectorAll('.wh-card').forEach(el => {
            el.style.display = 'none';
        });

        const el = document.querySelector(`.wh-card[data-id="${warehouseId}"]`);
        if (el) {
            el.style.display = 'block';
            const warehouseIndex = warehouses.findIndex(wh => wh.id == warehouseId);
            if (warehouseIndex !== -1) {
                const warehouse = warehouses[warehouseIndex];
                updateWarehouseFilterDropdowns(warehouse);
                warehousePage[warehouseIndex] = 0;
                renderWarehousePage(warehouseIndex, 0);
                updateWarehouseStats(warehouseIndex);
            }
        }
    }

    function getCurrentWarehouseIndex() {
        const visibleCard = Array.from(document.querySelectorAll('.wh-card')).find(card => card.style.display !== 'none');
        if (visibleCard) {
            const id = parseInt(visibleCard.dataset.id, 10);
            const idx = warehouses.findIndex(w => w.id === id);
            return idx !== -1 ? idx : 0;
        }
        return 0;
    }

    function dedupeWarehouseLocations(warehouse) {
        if (!warehouse || !Array.isArray(warehouse.locations)) return;
        const byIndex = new Map();
        warehouse.locations.forEach(shelf => {
            if (!shelf || typeof shelf.slot_index !== 'number') return;
            const key = shelf.slot_index;
            const existing = byIndex.get(key);
            if (!existing) {
                byIndex.set(key, shelf);
                return;
            }
            const existingHas = Array.isArray(existing.products) && existing.products.length > 0;
            const shelfHas = Array.isArray(shelf.products) && shelf.products.length > 0;
            if (!existingHas && shelfHas) {
                byIndex.set(key, shelf);
            }
        });
        warehouse.locations = Array.from(byIndex.values()).sort((a, b) => (a.slot_index || 0) - (b.slot_index || 0));
    }

    function findShelfBySlotIndex(warehouse, slotIndex) {
        if (!warehouse || !Array.isArray(warehouse.locations)) {
            return null;
        }
        return warehouse.locations.find(shelf => shelf && shelf.slot_index === slotIndex) || null;
    }

    function findArchivedShelfBySlotIndex(warehouse, slotIndex) {
        if (!warehouse || !Array.isArray(warehouse.archivedShelves)) {
            return null;
        }
        return warehouse.archivedShelves.find(shelf => shelf && shelf.slot_index === slotIndex) || null;
    }

    function getNextShelfIndex(warehouse) {
        if (!warehouse || !Array.isArray(warehouse.locations)) {
            return 0;
        }
        const existingIndexes = warehouse.locations
            .filter(shelf => shelf && typeof shelf.slot_index === 'number')
            .map(shelf => shelf.slot_index);
        return existingIndexes.length ? Math.max(...existingIndexes) + 1 : 0;
    }

    function goToLastWarehousePage(index) {
        const warehouse = warehouses[index];
        if (!warehouse) return;
        const totalPages = Math.max(1, Math.ceil((warehouse.locations || []).length / SHELVES_PER_PAGE));
        warehousePage[index] = totalPages - 1;
    }

    function renderWarehousePage(warehouseIndex, pageIndex = 0) {
        const warehouse = warehouses[warehouseIndex];
        if (!warehouse) return;

        dedupeWarehouseLocations(warehouse);
        const shelfSlots = getFilteredShelfSlots(warehouse);
        const totalPages = Math.max(1, Math.ceil(shelfSlots.length / SHELVES_PER_PAGE));
        warehousePage[warehouseIndex] = Math.min(Math.max(0, pageIndex), totalPages - 1);

        const shelvesContainer = document.querySelector(`.warehouse-shelves[data-id="${warehouse.id}"]`);
        const pageInfo = document.querySelector(`.page-info[data-id="${warehouse.id}"]`);
        const prevButton = document.querySelector(`.prev-page[data-id="${warehouse.id}"]`);
        const nextButton = document.querySelector(`.next-page[data-id="${warehouse.id}"]`);

        if (!shelvesContainer) {
            console.error('Shelves container not found data-id:', warehouse.id);
            return;
        }

        shelvesContainer.classList.add('grid', 'grid-cols-2', 'gap-4', 'items-start');

        const query = warehouseSearchQuery.trim().toLowerCase();
        const productDesc = productDescriptionFilter.trim();
        const brand = brandFilter.trim();
        const isFiltering = Boolean(query || productDesc || brand);

        let html = '';
        const currentPageSlots = shelfSlots.slice(warehousePage[warehouseIndex] * SHELVES_PER_PAGE, warehousePage[warehouseIndex] * SHELVES_PER_PAGE + SHELVES_PER_PAGE);

        if (!currentPageSlots.length) {
            html = `<div class="col-span-2 p-8 rounded-xl border border-dashed text-center text-sm text-slate-500 bg-slate-50">${isFiltering ? 'No products or shelves found matching your filters.' : 'No shelves available in this warehouse.'}</div>`;
        } else {
            currentPageSlots.forEach(item => {
                const slotIndex = item.slotIndex;
                const loc = item.loc || null;
                const locView = (loc && loc.archived) ? null : loc;
                const slotCount = Array.isArray(locView?.products) ? locView.products.length : 0;
                let productsHtml = '';

                const whLetter = (warehouse.code && warehouse.code.length > 0) ? warehouse.code.slice(-1).toUpperCase() : String.fromCharCode(65 + (warehouseIndex % 26));

                for (let productSlot = 0; productSlot < PRODUCTS_PER_SHELF; productSlot += 1) {
                    const binCode = `${whLetter}${productSlot + 1}`;
                    const product = (locView ? (locView.products[productSlot] || null) : null);
                    if (product) {
                        const matches = productMatchesWarehouseFilters(product, query, productDesc, brand);
                        const displayStyle = (isFiltering && !matches) ? 'display: none;' : '';

                        const productName = product.product_name || product.name || '—';
                        const oldBinCode = product.old_bin_code || binCode || 'E-3';
                        const finalTitle = `${productName} - ${oldBinCode}`;

                        const finalBrand = product.brand || '—';
                        const finalCompatible = product.compatible_model || product.compatibility || '—';
                        const finalSku = product.sku || '—';

                        const priceRaw = product.price ?? product.unit_price;
                        const priceText = (priceRaw !== null && priceRaw !== undefined && priceRaw !== '')
                            ? '₱' + Number(priceRaw).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                            : '—';
                        const qty = product.qty ?? product.stock_quantity ?? 0;

                        productsHtml += `<details class="product-chip" style="${displayStyle}">
                            <summary class="chip-header cursor-pointer select-none">
                                <div class="chip-desc flex items-center justify-between">
                                    <span>${finalTitle}</span>
                                    <svg class="w-4 h-4 text-black transition-transform duration-200 details-arrow flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </summary>
                            <div class="chip-body">
                                <div class="chip-row"><span class="chip-label">Compatible:</span><span class="chip-value">${finalCompatible}</span></div>
                                <div class="chip-row"><span class="chip-label">SKU:</span><span class="chip-value sku">${finalSku}</span></div>
                                <div class="chip-row"><span class="chip-label">Price:</span><span class="chip-value price">${priceText}</span></div>
                                <div class="chip-row"><span class="chip-label">Qty:</span><span class="chip-value qty">${qty}</span></div>
                            </div>
                        </details>`;
                    } else {
                        const displayStyle = isFiltering ? 'display: none;' : '';
                        productsHtml += `<div class="product-chip opacity-50 rounded-xl px-3 py-2 text-sm text-gray-500 border border-dashed border-gray-200" style="${displayStyle}">${binCode} - Empty slot</div>`;
                    }
                }

                const rawShelfName = locView?.name || 'Empty shelf';
                const shelfNumber = slotIndex + 1;
                const shelfTitle = rawShelfName.toLowerCase().startsWith('shelf') ? rawShelfName : `Shelf ${shelfNumber} - ${rawShelfName}`;
                const shelfCountText = locView ? `${slotCount}/${PRODUCTS_PER_SHELF} products` : `0/${PRODUCTS_PER_SHELF} products`;

                html += `
                    <div class="map-unit rounded-2xl shadow-sm relative bg-white border border-gray-200" style="padding: 1rem;">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-base font-semibold text-slate-900">${shelfTitle}</div>
                                <div class="text-xs text-gray-400 mt-1">${shelfCountText}</div>
                            </div>
                            <div class="relative inline-block">
                                <button type="button" onclick="toggleShelfActionDropdown(event, '${warehouseIndex}', '${slotIndex}')" class="px-3 py-1.5 text-xs font-semibold rounded-[12px] border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm flex items-center gap-1.5">
                                    <span>Actions</span>
                                    <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div id="shelf-action-menu-${warehouseIndex}-${slotIndex}" class="shelf-action-menu hidden absolute right-0 top-full z-50 mt-1 w-44 rounded-[14px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5">
                                    ${locView ? `
                                        ${locView.products && locView.products.length < PRODUCTS_PER_SHELF ? `<button type="button" onclick="handleShelfAction('${warehouseIndex}', '${slotIndex}', 'add-product')" class="w-full text-center px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">+ Add Product</button>` : ''}
                                        <button type="button" onclick="handleShelfAction('${warehouseIndex}', '${slotIndex}', 'transfer-products')" class="w-full text-center px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">Transfer Products</button>
                                        <button type="button" onclick="handleShelfAction('${warehouseIndex}', '${slotIndex}', 'edit-shelf')" class="w-full text-center px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">Edit Shelf</button>
                                        <button type="button" onclick="handleShelfAction('${warehouseIndex}', '${slotIndex}', 'transfer-shelf')" class="w-full text-center px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">Transfer Shelf</button>
                                        <button type="button" onclick="handleShelfAction('${warehouseIndex}', '${slotIndex}', 'archive-shelf')" class="w-full text-center px-3 py-2 rounded-[8px] text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">Archive Shelf</button>
                                    ` : `
                                        <button type="button" onclick="handleShelfAction('${warehouseIndex}', '${slotIndex}', 'add-shelf')" class="w-full text-center px-3 py-2 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 transition cursor-pointer">+ Add Shelf</button>
                                    `}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid gap-2 grid-cols-2 items-start">
                            ${productsHtml}
                        </div>
                    </div>
                `;
            });
        }

        shelvesContainer.innerHTML = html;
        const currentPageNum = warehousePage[warehouseIndex] + 1;
        if (prevButton) {
            prevButton.innerHTML = '← Prev';
            prevButton.disabled = warehousePage[warehouseIndex] === 0;
            if (prevButton.disabled) {
                prevButton.className = 'prev-page inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-400 opacity-50 cursor-not-allowed';
            } else {
                prevButton.className = 'prev-page inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer';
            }
        }
        if (nextButton) {
            nextButton.innerHTML = 'Next →';
            nextButton.disabled = warehousePage[warehouseIndex] === totalPages - 1;
            if (nextButton.disabled) {
                nextButton.className = 'next-page inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-400 opacity-50 cursor-not-allowed';
            } else {
                nextButton.className = 'next-page inline-flex items-center justify-center h-8 rounded-[10px] border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer';
            }
        }

        const warehouseCard = document.querySelector(`.wh-card[data-id="${warehouse.id}"]`);
        const totalItems = shelfSlots.length;

        const showingInfoContainer = warehouseCard ? warehouseCard.querySelector('.showing-info') : null;
        if (showingInfoContainer) {
            const startItem = totalItems > 0 ? (warehousePage[warehouseIndex] * SHELVES_PER_PAGE) + 1 : 0;
            const endItem = Math.min((warehousePage[warehouseIndex] + 1) * SHELVES_PER_PAGE, totalItems);
            showingInfoContainer.textContent = `Showing ${startItem}-${endItem} of ${totalItems} entries`;
        }

        const pageNumbersContainer = warehouseCard ? warehouseCard.querySelector('.page-numbers') : null;
        if (pageNumbersContainer) {
            let numsHtml = '';
            if (totalPages <= 1) {
                numsHtml = `<span class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">1</span>`;
            } else {
                for (let p = 1; p <= totalPages; p++) {
                    if (p === currentPageNum) {
                        numsHtml += `<span class="inline-flex items-center justify-center rounded-[10px] bg-black/10 text-slate-900 w-8 h-8 text-xs font-semibold">${p}</span>`;
                    } else {
                        numsHtml += `<button type="button" onclick="window.goToWarehousePage(${warehouseIndex}, ${p - 1})" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white text-slate-700 w-8 h-8 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">${p}</button>`;
                    }
                }
            }
            pageNumbersContainer.innerHTML = numsHtml;
        } else if (pageInfo) {
            pageInfo.textContent = `Page ${currentPageNum} of ${totalPages}`;
        }

        updateWarehouseStats(warehouseIndex);
    }

    window.goToWarehousePage = function (warehouseIndex, pageIdx) {
        warehousePage[warehouseIndex] = pageIdx;
        renderWarehousePage(warehouseIndex, pageIdx);
    };

    if (warehouseSelector) {
        warehouseSelector.addEventListener('change', function () {
            const warehouseId = parseInt(this.value, 10);
            const exists = warehouses.some(w => w.id === warehouseId);
            if (!Number.isNaN(warehouseId) && exists) {
                showWarehouse(warehouseId);
            }
        });
    }

    // Initial load for first warehouse
    if (warehouses.length) {
        const initialId = warehouses[0].id;
        if (warehouseSelector) {
            warehouseSelector.value = initialId.toString();
        }
        showWarehouse(initialId);
    }

    // =========================================================================
    // 6. TOAST NOTIFICATIONS
    // =========================================================================

    function showToast(message, type = 'success', options = {}) {
        if (!toastContainer) return;
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const undo = options.undo;
        const inner = document.createElement('div');
        inner.style.display = 'flex';
        inner.style.alignItems = 'center';
        inner.style.gap = '0.8rem';
        const text = document.createElement('span');
        text.textContent = message;
        inner.appendChild(text);

        if (undo && typeof undo.callback === 'function') {
            const undoBtn = document.createElement('button');
            undoBtn.type = 'button';
            undoBtn.className = 'mr-2';
            undoBtn.style.background = 'transparent';
            undoBtn.style.border = '1px solid rgba(255,255,255,0.18)';
            undoBtn.style.padding = '4px 8px';
            undoBtn.style.borderRadius = '6px';
            undoBtn.textContent = undo.label || 'Undo';
            undoBtn.addEventListener('click', function () {
                try { undo.callback(); } catch (e) { console.error(e); }
                if (toast.parentNode) toast.parentNode.removeChild(toast);
                clearTimeout(timeout);
            });
            inner.appendChild(undoBtn);
        }

        const dismiss = document.createElement('button');
        dismiss.type = 'button';
        dismiss.setAttribute('aria-label', 'Dismiss notification');
        dismiss.textContent = '✕';
        dismiss.addEventListener('click', () => { if (toast.parentNode) toast.parentNode.removeChild(toast); clearTimeout(timeout); });

        toast.appendChild(inner);
        toast.appendChild(dismiss);
        toastContainer.appendChild(toast);
        const timeout = setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, options.ttl || 4500);
    }

    // =========================================================================
    // 7. SHELF ACTIONS & MODAL LOGIC
    // =========================================================================

    window.toggleShelfActionDropdown = function (event, warehouseIndex, slotIndex) {
        if (event) event.stopPropagation();
        const menuId = `shelf-action-menu-${warehouseIndex}-${slotIndex}`;
        const targetMenu = document.getElementById(menuId);
        document.querySelectorAll('.shelf-action-menu, [id$="-menu"]').forEach(menu => {
            if (menu !== targetMenu) menu.classList.add('hidden');
        });
        if (targetMenu) targetMenu.classList.toggle('hidden');
    };

    window.handleShelfAction = function (warehouseIndex, slotIndex, action) {
        const sIdx = parseInt(slotIndex, 10);
        const wIdx = parseInt(warehouseIndex, 10);
        const menuId = `shelf-action-menu-${warehouseIndex}-${slotIndex}`;
        const menu = document.getElementById(menuId);
        if (menu) menu.classList.add('hidden');

        if (action === 'add-shelf') {
            openShelfModal(sIdx);
        } else if (action === 'edit-shelf') {
            openEditShelfModal(sIdx);
        } else if (action === 'add-product') {
            openProductModal(sIdx);
        } else if (action === 'archive-shelf') {
            archiveShelf(sIdx);
        } else if (action === 'delete-shelf') {
            deleteShelf(sIdx);
        } else if (action === 'transfer-products') {
            const warehouse = warehouses[wIdx];
            if (warehouse) {
                window.location.href = `/warehouse/transfer/${warehouse.id}/${sIdx}`;
            }
        } else if (action === 'transfer-shelf') {
            const warehouse = warehouses[wIdx];
            if (!warehouse) return;
            const shelf = findShelfBySlotIndex(warehouse, sIdx);
            const shelfName = shelf ? shelf.name : `Shelf ${sIdx + 1}`;

            const modal = document.getElementById('transfer-shelf-modal');
            const sourceWhInput = document.getElementById('transfer-source-warehouse-id');
            const slotInput = document.getElementById('transfer-slot-index');
            const currentShelfInput = document.getElementById('transfer-current-shelf');
            const destSelect = document.getElementById('transfer-destination-warehouse');

            if (!modal) return;

            if (sourceWhInput) sourceWhInput.value = warehouse.id;
            if (slotInput) slotInput.value = sIdx;
            if (currentShelfInput) currentShelfInput.value = `${shelfName} (${warehouse.name})`;

            if (destSelect) {
                destSelect.innerHTML = '<option value="">Select destination warehouse</option>';
                warehouses.forEach(wh => {
                    if (wh.id !== warehouse.id) {
                        const opt = document.createElement('option');
                        opt.value = wh.id;
                        opt.textContent = wh.name;
                        destSelect.appendChild(opt);
                    }
                });
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    };

    function openShelfModal(slotIndex) {
        const currentWarehouse = getCurrentWarehouseIndex();
        if (Number.isNaN(currentWarehouse)) return;
        showModal('Add Shelf', currentWarehouse, slotIndex, 'addShelf');
    }

    function openEditShelfModal(slotIndex) {
        const currentWarehouse = getCurrentWarehouseIndex();
        if (Number.isNaN(currentWarehouse)) return;
        showModal('Edit Shelf', currentWarehouse, slotIndex, 'editShelf');
    }

    function openProductModal(slotIndex) {
        const currentWarehouse = getCurrentWarehouseIndex();
        if (Number.isNaN(currentWarehouse)) return;
        showModal('Add Product', currentWarehouse, slotIndex, 'addProduct');
    }

    function updateModalShelfTemplate(warehouseIndex) {
        const shelfName = document.getElementById('modal-shelf-name');
        const modalSlot = document.getElementById('modal-slot');
        const modalWarehouseIndex = document.getElementById('modal-warehouse-index');
        const warehouse = warehouses[warehouseIndex];
        const nextSlot = getNextShelfIndex(warehouse);
        if (shelfName) {
            shelfName.value = '';
            shelfName.placeholder = 'Enter shelf name';
        }
        if (modalSlot) modalSlot.value = nextSlot;
        if (modalWarehouseIndex) modalWarehouseIndex.value = warehouseIndex;
    }

    function showModal(title, warehouseIndex, slot, mode = 'addShelf') {
        const titleEl = document.getElementById('modal-title');
        const modeEl = document.getElementById('modal-mode');
        const shelfName = document.getElementById('modal-shelf-name');
        const modalWarehouseSelect = document.getElementById('modal-warehouse-select');
        const currentShelf = findShelfBySlotIndex(warehouses[warehouseIndex], slot);

        if (titleEl) titleEl.textContent = title;
        if (modeEl) modeEl.value = mode;

        if (mode === 'addShelf') {
            if (modalWarehouseSelect) {
                modalWarehouseSelect.disabled = false;
                modalWarehouseSelect.value = warehouseIndex;
            }
            updateModalShelfTemplate(warehouseIndex);
            if (shelfName) shelfName.readOnly = false;
            resetProductRows([]);
        } else {
            if (modalWarehouseSelect) {
                modalWarehouseSelect.disabled = true;
                modalWarehouseSelect.value = warehouseIndex;
            }
            const modalWhIdx = document.getElementById('modal-warehouse-index');
            const modalSlotEl = document.getElementById('modal-slot');
            if (modalWhIdx) modalWhIdx.value = warehouseIndex;
            if (modalSlotEl) modalSlotEl.value = slot;

            if (mode === 'addProduct' && currentShelf) {
                if (shelfName) { shelfName.value = currentShelf.name; shelfName.readOnly = true; }
                resetProductRows(currentShelf.products);
            } else if (mode === 'editShelf' && currentShelf) {
                if (shelfName) { shelfName.value = currentShelf.name; shelfName.readOnly = false; }
                resetProductRows(currentShelf.products);
            } else {
                if (shelfName) { shelfName.value = currentShelf ? currentShelf.name : ''; shelfName.readOnly = false; }
                resetProductRows([]);
            }
        }

        const backdrop = document.getElementById('modal-backdrop');
        if (backdrop) {
            backdrop.classList.remove('hidden');
            backdrop.classList.add('flex');
        }
    }

    function closeModal() {
        const backdrop = document.getElementById('modal-backdrop');
        if (backdrop) {
            backdrop.classList.add('hidden');
            backdrop.classList.remove('flex');
        }
    }

    const modalCloseBtn = document.getElementById('modal-close');
    const modalCancelBtn = document.getElementById('modal-cancel');
    const modalForm = document.getElementById('modal-form');
    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);
    if (modalForm) modalForm.addEventListener('submit', handleModalSave);

    window.openShelfModalGlobal = function () {
        const currentWarehouse = getCurrentWarehouseIndex();
        if (Number.isNaN(currentWarehouse) || !warehouses[currentWarehouse]) {
            showModal('Add Shelf', 0, 0, 'addShelf');
            return;
        }
        const warehouse = warehouses[currentWarehouse];
        const nextSlot = getNextShelfIndex(warehouse);
        showModal('Add Shelf', currentWarehouse, nextSlot, 'addShelf');
    };

    const addShelfBtn = document.getElementById('add-shelf-button');
    if (addShelfBtn) {
        addShelfBtn.addEventListener('click', window.openShelfModalGlobal);
    }

    // =========================================================================
    // 7. ADD SHELF MODAL: CASCADING CATEGORY, BRAND & PRODUCT SELECTION WITH AUTO-SKU
    // =========================================================================

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function cleanSlug(str, maxLen = 10) {
        if (!str) return '';
        return str
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '')
            .slice(0, maxLen);
    }

    function generateSkuString(category, brand, modelName) {
        const cSlug = cleanSlug(category, 8) || 'ITEM';
        const bSlug = cleanSlug(brand, 8) || 'GEN';
        const mSlug = cleanSlug(modelName, 14) || '001';
        return `KCC_${cSlug}_${bSlug}_${mSlug}`;
    }

    function getAllCatalogCategories() {
        const catSet = new Set();
        (products || []).forEach(p => {
            const cat = (p.description || p.category || '').trim();
            if (cat && cat !== '—' && cat !== '-') catSet.add(cat);
        });
        return Array.from(catSet).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
    }

    function getAllCatalogBrands() {
        const brandSet = new Set();
        (products || []).forEach(p => {
            const br = (p.brand || '').trim();
            if (br && br !== '—' && br !== '-') brandSet.add(br);
        });
        return Array.from(brandSet).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
    }

    function getBrandsForCatalogCategory(catName) {
        const catLower = (catName || '').trim().toLowerCase();
        if (!catLower) return getAllCatalogBrands();
        const brandSet = new Set();
        (products || []).forEach(p => {
            const pCat = (p.description || p.category || '').trim().toLowerCase();
            if (pCat === catLower) {
                const br = (p.brand || '').trim();
                if (br && br !== '—' && br !== '-') brandSet.add(br);
            }
        });
        if (brandSet.size === 0) return getAllCatalogBrands();
        return Array.from(brandSet).sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
    }

    function getProductsForCatalogCategoryAndBrand(catName, brandName) {
        const catLower = (catName || '').trim().toLowerCase();
        const brandLower = (brandName || '').trim().toLowerCase();
        return (products || []).filter(p => {
            const pCat = (p.description || p.category || '').trim().toLowerCase();
            const pBrand = (p.brand || '').trim().toLowerCase();
            const matchCat = !catLower || pCat === catLower;
            const matchBrand = !brandLower || pBrand === brandLower;
            return matchCat && matchBrand;
        });
    }

    function getProductRows() {
        return Array.from(document.querySelectorAll('.product-row')).map(row => {
            const category = row.querySelector('.product-category')?.value.trim() || '';
            const brand = row.querySelector('.product-brand')?.value.trim() || '';
            const modelName = row.querySelector('.product-model')?.value.trim() || '';
            const sku = row.querySelector('.product-sku')?.value.trim() || '';
            const qty = parseInt(row.querySelector('.product-qty')?.value, 10) || 1;
            const price = parseFloat(row.querySelector('.product-price')?.value) || 0;
            const productId = row.dataset.productId ? parseInt(row.dataset.productId, 10) : null;

            return {
                id: productId,
                product_id: productId,
                category: category || modelName || 'General',
                description: category || modelName || 'General',
                brand: brand || 'Generic',
                name: modelName || category || 'Item',
                compatible_model: modelName || category || 'Item',
                product_name: modelName || category || 'Item',
                sku: sku,
                qty: qty,
                stock_quantity: qty,
                price: price,
            };
        }).filter(p => (p.category || p.name || p.sku) && p.qty > 0);
    }

    function setupCustomCombobox(wrapper, inputEl, menuEl, getOptionsFn, onSelectFn) {
        function renderItems(filterText = '') {
            const query = (filterText || '').trim().toLowerCase();
            const options = getOptionsFn() || [];
            const filtered = query
                ? options.filter(opt => {
                    const label = typeof opt === 'string' ? opt : (opt.name || '');
                    return label.toLowerCase().includes(query);
                })
                : options;

            if (filtered.length === 0) {
                menuEl.innerHTML = `<div class="px-3 py-2 text-[11px] text-slate-400 italic">No exact matches (type to create new)</div>`;
                return;
            }

            menuEl.innerHTML = filtered.slice(0, 50).map(opt => {
                const val = typeof opt === 'string' ? opt : opt.name;
                const extra = typeof opt === 'object' && opt.extra ? `<span class="text-[10px] text-slate-400 font-mono font-normal ml-2">${escapeHtml(opt.extra)}</span>` : '';
                return `<div class="combobox-item px-3 py-1.5 rounded-[8px] text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition cursor-pointer flex items-center justify-between" data-value="${escapeHtml(val)}">
                    <span class="truncate">${escapeHtml(val)}</span>
                    ${extra}
                </div>`;
            }).join('');

            menuEl.querySelectorAll('.combobox-item').forEach(item => {
                item.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const val = item.getAttribute('data-value');
                    inputEl.value = val;
                    menuEl.classList.add('hidden');
                    wrapper.style.zIndex = '';
                    const rowEl = wrapper.closest('.product-row-card');
                    if (rowEl) rowEl.style.zIndex = '';
                    if (onSelectFn) onSelectFn(val);
                });
            });
        }

        function openMenu() {
            document.querySelectorAll('.custom-combobox-menu').forEach(m => {
                if (m !== menuEl) m.classList.add('hidden');
            });
            document.querySelectorAll('.custom-combobox-wrapper').forEach(w => {
                w.style.zIndex = '';
                w.style.position = '';
            });
            document.querySelectorAll('.product-row-card').forEach(r => {
                r.style.zIndex = '';
                r.style.position = 'relative';
            });
            wrapper.style.position = 'relative';
            wrapper.style.zIndex = '100';
            const rowEl = wrapper.closest('.product-row-card');
            if (rowEl) {
                rowEl.style.position = 'relative';
                rowEl.style.zIndex = '90';
            }
            renderItems(inputEl.value);
            menuEl.classList.remove('hidden');
        }

        inputEl.addEventListener('focus', openMenu);
        inputEl.addEventListener('click', (e) => {
            e.stopPropagation();
            openMenu();
        });

        inputEl.addEventListener('input', () => {
            renderItems(inputEl.value);
            menuEl.classList.remove('hidden');
        });
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-combobox-wrapper')) {
            document.querySelectorAll('.custom-combobox-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-combobox-wrapper').forEach(w => {
                w.style.zIndex = '';
                w.style.position = '';
            });
            document.querySelectorAll('.product-row-card').forEach(r => {
                r.style.zIndex = '';
            });
        }
    });

    function addProductRow(product = {}, insertAt) {
        const container = document.getElementById('modal-product-rows');
        if (!container) return;

        const maxCapacity = parseInt(document.getElementById('modal-shelf-capacity')?.value, 10) || PRODUCTS_PER_SHELF;
        if (container.querySelectorAll('.product-row').length >= maxCapacity) {
            return;
        }

        const initialCategory = product.description || product.category || '';
        const initialBrand = product.brand || '';
        const initialModel = product.compatible_model || product.name || product.product_name || '';
        let initialSku = product.sku || '';
        const initialQty = product.qty ?? product.stock_quantity ?? 10;
        const initialPrice = product.price ?? product.unit_price ?? '';

        if (!initialSku && (initialCategory || initialBrand || initialModel)) {
            initialSku = generateSkuString(initialCategory, initialBrand, initialModel);
        }

        const row = document.createElement('div');
        row.className = 'product-row product-row-card relative border border-slate-200 rounded-[18px] p-3.5 bg-slate-50 space-y-2.5';
        if (product.id) {
            row.dataset.productId = product.id;
        }

        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="custom-combobox-wrapper relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                    <div class="relative">
                        <input type="text" class="product-category w-full h-10 rounded-[10px] border border-slate-300 bg-white px-3 py-2 pr-7 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm" placeholder="Select or type category..." autocomplete="off" value="${escapeHtml(initialCategory)}" />
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="custom-combobox-menu hidden absolute left-0 right-0 top-full z-[100] mt-1 max-h-44 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5"></div>
                </div>
                <div class="custom-combobox-wrapper relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Brand</label>
                    <div class="relative">
                        <input type="text" class="product-brand w-full h-10 rounded-[10px] border border-slate-300 bg-white px-3 py-2 pr-7 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm" placeholder="Select or type brand..." autocomplete="off" value="${escapeHtml(initialBrand)}" />
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="custom-combobox-menu hidden absolute left-0 right-0 top-full z-[100] mt-1 max-h-44 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5"></div>
                </div>
                <div class="custom-combobox-wrapper relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Product Name / Model</label>
                    <div class="relative">
                        <input type="text" class="product-model w-full h-10 rounded-[10px] border border-slate-300 bg-white px-3 py-2 pr-7 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm" placeholder="Select or type product name..." autocomplete="off" value="${escapeHtml(initialModel)}" />
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div class="custom-combobox-menu hidden absolute left-0 right-0 top-full z-[100] mt-1 max-h-44 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-xl p-1.5 space-y-0.5"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-[1.5fr_1fr_1.2fr_auto] gap-3 items-end pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">SKU (Auto-Generated)</label>
                    <input type="text" class="product-sku w-full h-10 rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm" value="${escapeHtml(initialSku)}" placeholder="SKU" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Quantity</label>
                    <input type="number" min="1" class="product-qty w-full h-10 rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm" value="${initialQty}" placeholder="Qty" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Unit Price (₱)</label>
                    <input type="number" step="0.01" min="0" class="product-price w-full h-10 rounded-[10px] border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 shadow-sm" value="${initialPrice}" placeholder="0.00" />
                </div>
                <div class="flex justify-end pb-0.5">
                    <button type="button" class="remove-product-row h-10 px-3 py-2 text-xs font-bold text-black hover:text-black hover:bg-slate-200/60 rounded-[10px] transition cursor-pointer inline-flex items-center justify-center border border-transparent">
                        ✕ Remove
                    </button>
                </div>
            </div>
        `;

        const catWrapper = row.querySelectorAll('.custom-combobox-wrapper')[0];
        const catInput = row.querySelector('.product-category');
        const catMenu = catWrapper.querySelector('.custom-combobox-menu');

        const brandWrapper = row.querySelectorAll('.custom-combobox-wrapper')[1];
        const brandInput = row.querySelector('.product-brand');
        const brandMenu = brandWrapper.querySelector('.custom-combobox-menu');

        const modelWrapper = row.querySelectorAll('.custom-combobox-wrapper')[2];
        const modelInput = row.querySelector('.product-model');
        const modelMenu = modelWrapper.querySelector('.custom-combobox-menu');

        const skuInput = row.querySelector('.product-sku');
        const priceInput = row.querySelector('.product-price');
        const qtyInput = row.querySelector('.product-qty');

        if (initialSku && !product.id) {
            // Keep initially generated or passed SKU
        } else if (product.id) {
            skuInput.dataset.manualEdited = 'true';
        }

        function autoGenerateSku(force = false) {
            if (skuInput.dataset.manualEdited && !force) {
                return;
            }
            const currentCat = catInput.value.trim();
            const currentBrand = brandInput.value.trim();
            const currentModel = modelInput.value.trim();

            if (!currentCat && !currentBrand && !currentModel) {
                skuInput.value = '';
                return;
            }

            skuInput.value = generateSkuString(currentCat, currentBrand, currentModel);
        }

        function handleModelChange() {
            const currentModel = modelInput.value.trim().toLowerCase();
            const currentCat = catInput.value.trim().toLowerCase();
            const currentBrand = brandInput.value.trim().toLowerCase();

            if (!currentModel) {
                autoGenerateSku();
                return;
            }

            const match = (products || []).find(p => {
                const pName = (p.compatible_model || p.product_name || p.name || '').trim().toLowerCase();
                const pCat = (p.description || p.category || '').trim().toLowerCase();
                const pBrand = (p.brand || '').trim().toLowerCase();

                if (pName !== currentModel) return false;
                if (currentCat && pCat && pCat !== currentCat) return false;
                if (currentBrand && pBrand && pBrand !== currentBrand) return false;
                return true;
            });

            if (match) {
                if (match.sku) {
                    skuInput.value = match.sku;
                    skuInput.dataset.manualEdited = 'true';
                }
                if (match.price || match.unit_price) {
                    priceInput.value = match.price ?? match.unit_price;
                }
                if (match.id) {
                    row.dataset.productId = match.id;
                }
                if (!catInput.value.trim() && (match.description || match.category)) {
                    catInput.value = match.description || match.category;
                }
                if (!brandInput.value.trim() && match.brand) {
                    brandInput.value = match.brand;
                }
            } else {
                delete row.dataset.productId;
                autoGenerateSku();
            }
        }

        // Setup custom sleek comboboxes
        setupCustomCombobox(catWrapper, catInput, catMenu, () => getAllCatalogCategories(), (val) => {
            autoGenerateSku();
        });

        setupCustomCombobox(brandWrapper, brandInput, brandMenu, () => getBrandsForCatalogCategory(catInput.value), (val) => {
            autoGenerateSku();
        });

        setupCustomCombobox(modelWrapper, modelInput, modelMenu, () => {
            const prods = getProductsForCatalogCategoryAndBrand(catInput.value, brandInput.value);
            const seen = new Set();
            const items = [];
            prods.forEach(p => {
                const name = (p.compatible_model || p.product_name || p.name || '').trim();
                if (name && !seen.has(name.toLowerCase())) {
                    seen.add(name.toLowerCase());
                    items.push({ name: name, extra: p.sku || '' });
                }
            });
            return items;
        }, (val) => {
            handleModelChange();
        });

        // Real-time input typing events
        catInput.addEventListener('input', () => autoGenerateSku());
        brandInput.addEventListener('input', () => autoGenerateSku());
        modelInput.addEventListener('input', () => handleModelChange());

        // Manual SKU edit handling
        skuInput.addEventListener('input', function () {
            if (this.value.trim()) {
                this.dataset.manualEdited = 'true';
            } else {
                delete this.dataset.manualEdited;
                autoGenerateSku(true);
            }
        });

        // Remove row
        row.querySelector('.remove-product-row').addEventListener('click', () => {
            row.remove();
            updateAddRowButtonState();
        });

        const children = container.querySelectorAll('.product-row');
        if (typeof insertAt === 'number' && insertAt >= 0 && insertAt < children.length) {
            container.insertBefore(row, children[insertAt]);
        } else {
            container.appendChild(row);
        }

        updateAddRowButtonState();
    }

    function updateAddRowButtonState() {
        const container = document.getElementById('modal-product-rows');
        const addButton = document.getElementById('modal-add-product-row');
        if (!container || !addButton) return;
        const capacityInput = document.getElementById('modal-shelf-capacity');
        const maxCapacity = capacityInput ? (parseInt(capacityInput.value, 10) || PRODUCTS_PER_SHELF) : PRODUCTS_PER_SHELF;
        const rowCount = container.querySelectorAll('.product-row').length;
        addButton.disabled = rowCount >= maxCapacity;
        addButton.classList.toggle('opacity-40', addButton.disabled);
    }

    function resetProductRows(prodList = []) {
        const container = document.getElementById('modal-product-rows');
        if (!container) return;
        container.innerHTML = '';
        const rows = (prodList || []).slice(0, PRODUCTS_PER_SHELF);
        if (rows.length) {
            rows.forEach(p => addProductRow(p));
        } else {
            addProductRow();
        }
        updateAddRowButtonState();
    }

    // Modal warehouse selector change
    const modalWarehouseSelectEl = document.getElementById('modal-warehouse-select');
    if (modalWarehouseSelectEl) {
        modalWarehouseSelectEl.addEventListener('change', function () {
            const whIdx = parseInt(this.value, 10);
            if (!Number.isNaN(whIdx) && warehouses[whIdx]) {
                updateModalShelfTemplate(whIdx);
                const labelSpan = document.getElementById('modalWarehouseSelectLabel');
                if (labelSpan) labelSpan.textContent = warehouses[whIdx].name;
            }
        });
    }

    const capacityInputEl = document.getElementById('modal-shelf-capacity');
    if (capacityInputEl) {
        capacityInputEl.addEventListener('input', updateAddRowButtonState);
    }

    const addRowBtnEl = document.getElementById('modal-add-product-row');
    if (addRowBtnEl) {
        addRowBtnEl.addEventListener('click', function () {
            addProductRow();
        });
    }

    window.addProductRowGlobal = function () {
        addProductRow();
    };

    async function saveShelfData(shelfData) {
        if (shelfData.warehouse_id === undefined && shelfData.warehouse_index !== undefined && warehouses[shelfData.warehouse_index]) {
            shelfData.warehouse_id = warehouses[shelfData.warehouse_index].id;
        }
        try {
            const response = await fetch(window.WarehouseData.routes.saveShelf, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.WarehouseData.csrfToken,
                },
                body: JSON.stringify(shelfData),
            });
            const responseText = await response.text();
            let result = null;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                if (!response.ok) {
                    alert(responseText || 'Failed to save shelf.');
                    return null;
                }
            }
            if (!response.ok || !result?.success) {
                alert(result?.message || responseText || 'Failed to save shelf.');
                return null;
            }
            return result;
        } catch (error) {
            alert('Failed to save shelf: ' + error.message);
            return null;
        }
    }

    async function handleModalSave(event) {
        event.preventDefault();
        const modalWarehouseSelect = document.getElementById('modal-warehouse-select');
        const warehouseIndex = modalWarehouseSelect && !modalWarehouseSelect.disabled
            ? parseInt(modalWarehouseSelect.value, 10)
            : parseInt(document.getElementById('modal-warehouse-index').value, 10);
        const slotIndex = parseInt(document.getElementById('modal-slot').value, 10);
        const shelfName = document.getElementById('modal-shelf-name')?.value.trim() || `Shelf ${slotIndex + 1}`;
        const capacity = parseInt(document.getElementById('modal-shelf-capacity')?.value, 10) || PRODUCTS_PER_SHELF;
        const rows = getProductRows();

        if (Number.isNaN(warehouseIndex) || !warehouses[warehouseIndex]) {
            alert('Please select a valid warehouse.');
            return;
        }

        const warehouse = warehouses[warehouseIndex];
        const submitBtn = modalForm?.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
        }

        try {
            const result = await saveShelfData({
                warehouse_id: warehouse.id,
                warehouse_index: warehouseIndex,
                slot_index: slotIndex,
                name: shelfName,
                capacity: capacity,
                products: rows,
                archived: false,
            });

            if (!result || !result.success) {
                return;
            }

            const savedShelf = result.shelf;
            warehouse.locations = warehouse.locations || [];
            const existingIdx = warehouse.locations.findIndex(s => s && s.slot_index === slotIndex);
            if (existingIdx !== -1) {
                warehouse.locations[existingIdx] = savedShelf;
            } else {
                warehouse.locations.push(savedShelf);
            }
            dedupeWarehouseLocations(warehouse);

            // Update global product repository with new items
            if (Array.isArray(savedShelf.products)) {
                savedShelf.products.forEach(sp => {
                    if (sp && !products.some(p => p.sku === sp.sku)) {
                        products.push(sp);
                    }
                });
            }

            // Switch view to the target warehouse
            showWarehouse(warehouse.id);
            const whSelect = document.getElementById('warehouse-selector');
            if (whSelect) whSelect.value = warehouse.id;
            const whLabel = document.getElementById('dd-warehouse-label');
            if (whLabel) whLabel.textContent = warehouse.name;

            updateWarehouseFilterDropdowns(warehouse);
            renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex] || 0);
            updateWarehouseStats(warehouseIndex);

            closeModal();
            showToast(`Shelf "${shelfName}" saved to ${warehouse.name} successfully!`, 'success');
        } catch (err) {
            console.error(err);
            alert('An error occurred while saving.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save shelf';
            }
        }
    }

    // =========================================================================
    // 8. RESTORE & TRANSFER SHELF
    // =========================================================================

    window.restoreArchivedShelf = async function (slotIndex, warehouseId) {
        const wId = parseInt(warehouseId, 10);
        const sIdx = parseInt(slotIndex, 10);
        const warehouseIndex = warehouses.findIndex(w => w.id === wId);
        if (warehouseIndex === -1) return;

        const warehouse = warehouses[warehouseIndex];
        const shelf = findArchivedShelfBySlotIndex(warehouse, sIdx);
        if (!shelf) return;

        const saved = await saveShelfData({
            warehouse_id: wId,
            warehouse_index: warehouseIndex,
            slot_index: sIdx,
            name: shelf.name,
            products: shelf.products || [],
            archived: false,
        });
        if (!saved) return;

        shelf.archived = false;
        warehouse.locations = warehouse.locations || [];
        warehouse.locations.push(shelf);
        dedupeWarehouseLocations(warehouse);
        warehouse.archivedShelves = (warehouse.archivedShelves || []).filter(item => item.slot_index !== sIdx);

        showToast(`${shelf.name} restored to ${warehouse.name}.`, 'success');
        if (typeof renderArchivedShelvesList === 'function') {
            renderArchivedShelvesList();
        }
        const currentWarehouse = getCurrentWarehouseIndex();
        if (currentWarehouse === warehouseIndex) {
            updateWarehouseFilterDropdowns(warehouse);
            renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
            updateWarehouseStats(warehouseIndex);
        }
    };

    // Transfer Shelf Modal Form
    const transferShelfModal = document.getElementById('transfer-shelf-modal');
    const cancelTransferShelfBtn = document.getElementById('cancel-transfer-shelf');
    const transferShelfForm = document.getElementById('transfer-shelf-form');

    function closeTransferShelfModal() {
        if (transferShelfModal) {
            transferShelfModal.classList.add('hidden');
            transferShelfModal.classList.remove('flex');
        }
    }

    if (cancelTransferShelfBtn) {
        cancelTransferShelfBtn.addEventListener('click', closeTransferShelfModal);
    }

    if (transferShelfForm) {
        transferShelfForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const sourceWarehouseId = parseInt(document.getElementById('transfer-source-warehouse-id').value, 10);
            const slotIndex = parseInt(document.getElementById('transfer-slot-index').value, 10);
            const destinationWarehouseId = parseInt(document.getElementById('transfer-destination-warehouse').value, 10);

            if (!destinationWarehouseId) {
                showToast('Please select a destination warehouse.', 'error');
                return;
            }

            const submitBtn = transferShelfForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Transferring…';

            try {
                const response = await fetch('/warehouse-management/transfer-shelf', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.WarehouseData.csrfToken,
                    },
                    body: JSON.stringify({
                        source_warehouse_id: sourceWarehouseId,
                        destination_warehouse_id: destinationWarehouseId,
                        slot_index: slotIndex,
                    }),
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    showToast(result.message || 'Transfer failed.', 'error');
                    return;
                }

                const srcWarehouseIdx = warehouses.findIndex(w => w.id === sourceWarehouseId);
                const destWarehouseIdx = warehouses.findIndex(w => w.id === destinationWarehouseId);

                if (srcWarehouseIdx !== -1) {
                    const srcWarehouse = warehouses[srcWarehouseIdx];
                    const shelfToMove = findShelfBySlotIndex(srcWarehouse, slotIndex);

                    if (shelfToMove && destWarehouseIdx !== -1) {
                        const destWarehouse = warehouses[destWarehouseIdx];
                        const newSlot = result.new_slot_index ?? (Math.max(0, ...destWarehouse.locations.map(l => l.slot_index || 0)) + 1);
                        destWarehouse.locations.push({ ...shelfToMove, slot_index: newSlot });
                        dedupeWarehouseLocations(destWarehouse);
                        updateWarehouseFilterDropdowns(destWarehouse);
                    }

                    srcWarehouse.locations = srcWarehouse.locations.filter(l => l.slot_index !== slotIndex);
                    updateWarehouseFilterDropdowns(srcWarehouse);

                    const currentWarehouse = getCurrentWarehouseIndex();
                    if (currentWarehouse === srcWarehouseIdx) {
                        warehousePage[srcWarehouseIdx] = Math.max(0, Math.min(warehousePage[srcWarehouseIdx], Math.ceil(srcWarehouse.locations.length / SHELVES_PER_PAGE) - 1));
                        renderWarehousePage(srcWarehouseIdx, warehousePage[srcWarehouseIdx]);
                        updateWarehouseStats(srcWarehouseIdx);
                    }
                }

                const destName = document.getElementById('transfer-destination-warehouse').selectedOptions[0]?.textContent || 'destination';
                showToast(`Shelf transferred to ${destName} successfully.`, 'success');
                closeTransferShelfModal();

            } catch (err) {
                console.error('Transfer shelf error:', err);
                showToast('An error occurred during transfer.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Warehouse actions (e.g. archive warehouse)
    document.querySelectorAll('.warehouse-action-select').forEach(select => {
        select.addEventListener('change', async function () {
            const action = this.value;
            const warehouseId = this.dataset.id;
            if (action === 'archive-warehouse') {
                if (confirm('Are you sure you want to archive this warehouse?')) {
                    try {
                        const response = await fetch(`/warehouse-management/archive/${warehouseId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.WarehouseData.csrfToken,
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            showToast('Warehouse archived successfully', 'success');
                            setTimeout(() => window.location.reload(), 800);
                        } else {
                            showToast(data.message || 'Failed to archive warehouse', 'error');
                        }
                    } catch (e) {
                        showToast('Failed to archive warehouse', 'error');
                    }
                }
            }
            this.value = '';
        });
    });

    // Pagination button event delegation
    document.querySelectorAll('.prev-page').forEach(button => {
        button.addEventListener('click', function () {
            const warehouseId = parseInt(this.dataset.id, 10);
            const warehouseIndex = warehouses.findIndex(wh => wh.id == warehouseId);
            if (warehouseIndex !== -1) {
                warehousePage[warehouseIndex] = Math.max(0, warehousePage[warehouseIndex] - 1);
                renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
            }
        });
    });

    document.querySelectorAll('.next-page').forEach(button => {
        button.addEventListener('click', function () {
            const warehouseId = parseInt(this.dataset.id, 10);
            const warehouseIndex = warehouses.findIndex(wh => wh.id == warehouseId);
            if (warehouseIndex !== -1) {
                const warehouse = warehouses[warehouseIndex];
                const shelfSlots = getFilteredShelfSlots(warehouse);
                const totalPages = Math.max(1, Math.ceil(shelfSlots.length / SHELVES_PER_PAGE));
                warehousePage[warehouseIndex] = Math.min(totalPages - 1, warehousePage[warehouseIndex] + 1);
                renderWarehousePage(warehouseIndex, warehousePage[warehouseIndex]);
            }
        });
    });

    // =========================================================================
    // 9. QR CODE SCANNING & NEW STOCK HELPERS (PRESERVED)
    // =========================================================================

    const generateQRButton = document.getElementById('wm-generate-qr');
    const scanQRButton = document.getElementById('wm-scan-qr');
    const mobileScannerButton = document.getElementById('wm-mobile-scanner');
    const restockDateInput = document.getElementById('wm-qr-restock-date');

    const today = new Date().toISOString().split('T')[0];
    if (restockDateInput) {
        restockDateInput.value = today;
    }

    if (document.getElementById('wm-product-rows')) {
        for (let i = 0; i < 3; i++) {
            wmAddProductRow();
        }
    }

    if (generateQRButton) {
        generateQRButton.addEventListener('click', function () {
            const qrModal = document.getElementById('wm-qr-modal');
            if (qrModal) qrModal.style.display = 'flex';
        });
    }

    if (scanQRButton) {
        scanQRButton.addEventListener('click', function () {
            const scanModal = document.getElementById('wm-scan-modal');
            if (scanModal) scanModal.style.display = 'flex';
            wmInitScanner();
        });
    }

    if (mobileScannerButton) {
        mobileScannerButton.addEventListener('click', wmOpenMobileScanner);
    }

    wmStartMobileScannerPolling();

    let wmScanner = null;

    function wmInitScanner() {
        if (!wmScanner && typeof Html5Qrcode !== 'undefined') {
            wmScanner = new Html5Qrcode("wm-scanner-reader");
        }

        if (!wmScanner) return;

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        wmScanner.start(
            { facingMode: "environment" },
            config,
            wmOnScanSuccess,
            wmOnScanFailure
        ).catch(err => {
            console.error("Warehouse scanner error:", err);
            const statusEl = document.getElementById('wm-scanner-status');
            if (statusEl) {
                statusEl.textContent = 'Camera access denied or not available';
                statusEl.classList.add('text-red-400');
            }
        });
    }

    function wmCloseScanner() {
        const scanModal = document.getElementById('wm-scan-modal');
        if (scanModal) scanModal.style.display = 'none';
        if (wmScanner) {
            wmScanner.stop().catch(err => console.error(err));
        }
        const statusEl = document.getElementById('wm-scanner-status');
        if (statusEl) {
            statusEl.textContent = 'Position QR code within the frame';
            statusEl.classList.remove('text-red-400');
        }
    }

    function wmOnScanSuccess(decodedText) {
        const statusEl = document.getElementById('wm-scanner-status');
        if (statusEl) {
            statusEl.textContent = 'Scanned: ' + decodedText;
            statusEl.classList.add('text-green-400');
            setTimeout(() => {
                statusEl.classList.remove('text-green-400');
                statusEl.textContent = 'Position QR code within the frame';
            }, 2000);
        }
        wmHandleScannedCode(decodedText);
    }

    function wmOnScanFailure() {
        // Ignore scan failures
    }

    let wmGeneratedQRs = [];
    let wmCurrentPage = 0;
    const WM_QR_PER_PAGE = 6;
    let wmScannedNewStock = null;
    let wmScannedItems = [];
    let wmScanCurrentPage = 0;
    const WM_SCAN_PER_PAGE = 4;

    async function wmHandleScannedCode(code) {
        try {
            const parsed = JSON.parse(code);
            const productName = parsed.product_name;
            const sku = parsed.sku;
            const restockDate = parsed.restock_date;

            if (productName && sku && parsed.type === 'new_stock') {
                const alreadyScanned = wmScannedItems.find(item => item.sku === sku);
                if (alreadyScanned) {
                    showToast('This item is already scanned', 'error');
                    return;
                }
                wmScannedItems.push({ productName, sku, restockDate });
                wmUpdateScannedList();
                showToast(`Scanned: ${productName}`, 'success');
            } else {
                showToast('Invalid new stock QR code', 'error');
            }
        } catch (e) {
            showToast('Invalid QR code format', 'error');
        }
    }

    function wmUpdateScannedList() {
        const scannedItemsDiv = document.getElementById('wm-scanned-items');
        const scanList = document.getElementById('wm-scan-list');
        const scanCount = document.getElementById('wm-scan-count');
        const proceedBtn = document.getElementById('wm-proceed-btn');
        const pagination = document.getElementById('wm-scan-pagination');

        if (!scannedItemsDiv || !scanList) return;

        if (wmScannedItems.length > 0) {
            scannedItemsDiv.classList.remove('hidden');
            if (scanCount) scanCount.textContent = wmScannedItems.length;
            if (proceedBtn) proceedBtn.disabled = false;

            if (pagination) {
                if (wmScannedItems.length > WM_SCAN_PER_PAGE) {
                    pagination.classList.remove('hidden');
                    pagination.classList.add('flex');
                } else {
                    pagination.classList.add('hidden');
                    pagination.classList.remove('flex');
                }
            }

            const start = wmScanCurrentPage * WM_SCAN_PER_PAGE;
            const end = Math.min(start + WM_SCAN_PER_PAGE, wmScannedItems.length);
            const pageItems = wmScannedItems.slice(start, end);

            scanList.innerHTML = pageItems.map((item, index) => {
                const actualIndex = start + index;
                return `
                <div class="flex items-center justify-between bg-slate-50 rounded-lg p-3 border border-slate-200">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">${item.productName}</p>
                        <p class="text-xs text-slate-600">SKU: ${item.sku}</p>
                        <p class="text-xs text-slate-500">Restock: ${item.restockDate || 'N/A'}</p>
                    </div>
                    <button onclick="wmRemoveScannedItem(${actualIndex})" class="text-red-500 hover:text-red-700 p-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            }).join('');

            const totalPages = Math.ceil(wmScannedItems.length / WM_SCAN_PER_PAGE);
            const pageInfo = document.getElementById('wm-scan-page-info');
            const prevPage = document.getElementById('wm-scan-prev-page');
            const nextPage = document.getElementById('wm-scan-next-page');
            if (pageInfo) pageInfo.textContent = `Page ${wmScanCurrentPage + 1} of ${totalPages}`;
            if (prevPage) prevPage.disabled = wmScanCurrentPage === 0;
            if (nextPage) nextPage.disabled = wmScanCurrentPage >= totalPages - 1;
        } else {
            scannedItemsDiv.classList.add('hidden');
            if (proceedBtn) proceedBtn.disabled = true;
            wmScanCurrentPage = 0;
        }
    }

    function wmScanPrevPage() {
        if (wmScanCurrentPage > 0) {
            wmScanCurrentPage--;
            wmUpdateScannedList();
        }
    }

    function wmScanNextPage() {
        const totalPages = Math.ceil(wmScannedItems.length / WM_SCAN_PER_PAGE);
        if (wmScanCurrentPage < totalPages - 1) {
            wmScanCurrentPage++;
            wmUpdateScannedList();
        }
    }

    function wmOpenMobileScanner() {
        if (window.WarehouseData?.routes?.mobileScanner) {
            window.open(window.WarehouseData.routes.mobileScanner, 'WarehouseMobileScanner', 'width=400,height=600');
        }
    }

    function wmStartMobileScannerPolling() {
        setInterval(() => {
            const scannedData = localStorage.getItem('warehouseScannedItems');
            const timestamp = localStorage.getItem('warehouseScanTimestamp');

            if (scannedData && timestamp) {
                const scanTime = parseInt(timestamp, 10);
                const now = Date.now();

                if (now - scanTime < 5000) {
                    try {
                        const items = JSON.parse(scannedData);
                        items.forEach(item => {
                            const alreadyScanned = wmScannedItems.find(i => i.sku === item.sku);
                            if (!alreadyScanned) {
                                wmScannedItems.push({
                                    productName: item.product_name,
                                    sku: item.sku,
                                    restockDate: item.restock_date
                                });
                            }
                        });

                        wmUpdateScannedList();
                        localStorage.removeItem('warehouseScannedItems');
                        localStorage.removeItem('warehouseScanTimestamp');
                        showToast('Received items from mobile scanner', 'success');
                    } catch (e) {
                        console.error('Error parsing mobile scanner data:', e);
                    }
                }
            }
        }, 1000);
    }

    function wmRemoveScannedItem(index) {
        wmScannedItems.splice(index, 1);
        const totalPages = Math.ceil(wmScannedItems.length / WM_SCAN_PER_PAGE);
        if (wmScanCurrentPage >= totalPages && wmScanCurrentPage > 0) {
            wmScanCurrentPage = totalPages - 1;
        }
        wmUpdateScannedList();
    }

    function wmClearScannedItems() {
        wmScannedItems = [];
        wmScanCurrentPage = 0;
        wmUpdateScannedList();
    }

    function wmProceedToDetails() {
        if (wmScannedItems.length === 0) return;
        wmCloseScanner();
        wmScannedNewStock = wmScannedItems[0];
        wmOpenNewStockModal();
    }

    function wmOpenNewStockModal() {
        if (!wmScannedNewStock) return;

        const pName = document.getElementById('wm-ns-product-name');
        const pSku = document.getElementById('wm-ns-sku');
        const pDate = document.getElementById('wm-ns-restock-date');

        if (pName) pName.textContent = wmScannedNewStock.productName;
        if (pSku) pSku.textContent = wmScannedNewStock.sku;
        if (pDate) pDate.textContent = wmScannedNewStock.restockDate;

        const priceEl = document.getElementById('wm-ns-price');
        const qtyEl = document.getElementById('wm-ns-quantity');
        const catEl = document.getElementById('wm-ns-category');
        const descEl = document.getElementById('wm-ns-description');

        if (priceEl) priceEl.value = '';
        if (qtyEl) qtyEl.value = '';
        if (catEl) catEl.value = '';
        if (descEl) descEl.value = '';

        const saveBtn = document.querySelector('#wm-new-stock-modal button[onclick="wmSaveNewStock()"]');
        const currentIndex = wmScannedItems.findIndex(item => item.sku === wmScannedNewStock.sku);
        if (saveBtn) {
            if (currentIndex < wmScannedItems.length - 1) {
                saveBtn.textContent = `Save & Next (${currentIndex + 1}/${wmScannedItems.length})`;
            } else {
                saveBtn.textContent = 'Save & Add to Warehouse';
            }
        }

        const modal = document.getElementById('wm-new-stock-modal');
        if (modal) modal.style.display = 'flex';
    }

    function wmCloseNewStockModal() {
        const modal = document.getElementById('wm-new-stock-modal');
        if (modal) modal.style.display = 'none';
        wmScannedNewStock = null;
    }

    async function wmSaveNewStock() {
        if (!wmScannedNewStock) return;

        const price = parseFloat(document.getElementById('wm-ns-price')?.value);
        const quantity = parseInt(document.getElementById('wm-ns-quantity')?.value, 10);
        const category = document.getElementById('wm-ns-category')?.value;
        const description = document.getElementById('wm-ns-description')?.value || '';

        if (!price || !quantity || !category) {
            showToast('Please fill in price, quantity, and category', 'error');
            return;
        }

        try {
            const response = await fetch('/stock/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.WarehouseData.csrfToken,
                },
                body: JSON.stringify({
                    name: wmScannedNewStock.productName,
                    sku: wmScannedNewStock.sku,
                    price: price,
                    category: category,
                    description: description,
                    quantity: quantity,
                }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to create product');
            }

            const result = await response.json();
            const productId = result.id || result.product_id;

            if (!productId) {
                throw new Error('No product ID returned');
            }

            wmScannedNewStock.productId = productId;
            wmScannedNewStock.price = price;
            wmScannedNewStock.quantity = quantity;
            wmScannedNewStock.restockDate = wmScannedNewStock.restockDate || restockDateInput?.value;

            const currentIndex = wmScannedItems.findIndex(item => item.sku === wmScannedNewStock.sku);
            if (currentIndex < wmScannedItems.length - 1) {
                wmScannedNewStock = wmScannedItems[currentIndex + 1];
                wmCloseNewStockModal();
                setTimeout(() => wmOpenNewStockModal(), 100);
                showToast('Product saved. Next item...', 'success');
            } else {
                wmCloseNewStockModal();
                wmAddAllToWarehouse();
            }

        } catch (error) {
            console.error('Error saving new stock:', error);
            showToast(error.message || 'Failed to create product. Please try again.', 'error');
        }
    }

    async function wmAddAllToWarehouse() {
        const currentWarehouse = getCurrentWarehouseIndex();
        const nextSlot = getNextShelfIndex(warehouses[currentWarehouse]);
        showModal('Add Product', currentWarehouse, nextSlot, 'addProduct');

        setTimeout(() => {
            const container = document.getElementById('modal-product-rows');
            if (!container) return;
            container.innerHTML = '';

            wmScannedItems.forEach(item => {
                if (item.productId) {
                    const row = document.createElement('div');
                    row.className = 'product-row product-row-card';
                    row.innerHTML = `
                        <div class="row-grid grid gap-4 md:grid-cols-[1.8fr_1fr_0.9fr_0.9fr_0.35fr] items-end">
                            <div>
                                <label class="block text-xs font-medium text-slate-700">Product</label>
                                <input type="text" class="product-name mt-1 block w-full px-4 py-3 text-sm" value="${item.productName || ''}" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700">SKU</label>
                                <input type="text" class="product-sku mt-1 block w-full px-4 py-3 text-sm" value="${item.sku || ''}" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700">Qty</label>
                                <input type="number" min="0" class="product-qty mt-1 block w-full px-4 py-3 text-sm" value="${item.quantity || 1}" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700">Price</label>
                                <input type="number" step="0.01" min="0" class="product-price mt-1 block w-full px-4 py-3 text-sm" value="${item.price || 0}" />
                            </div>
                            <div class="flex items-center justify-end">
                                <button type="button" class="remove-product-row text-sm font-semibold" onclick="this.closest('.product-row').remove()">Remove</button>
                            </div>
                        </div>
                    `;
                    container.appendChild(row);
                }
            });

            wmScannedItems = [];
            showToast('All products created. Please save shelf to finalize.', 'success');
        }, 100);
    }

    function wmAddProductRow() {
        const container = document.getElementById('wm-product-rows');
        if (!container) return;

        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 bg-slate-50 rounded-lg p-3';
        row.innerHTML = `
            <input type="text" class="wm-product-name flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Enter product name">
            <div class="wm-sku-display text-xs font-mono font-bold text-slate-700 min-w-[100px]">KCC_</div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

        const nameInput = row.querySelector('.wm-product-name');
        const skuDisplay = row.querySelector('.wm-sku-display');

        nameInput.addEventListener('input', function () {
            const name = this.value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
            skuDisplay.textContent = name ? `KCC_${name}` : 'KCC_';
        });

        container.appendChild(row);
    }

    function wmGenerateAllQR() {
        const productRows = document.querySelectorAll('#wm-product-rows > div');
        const restockDate = document.getElementById('wm-qr-restock-date')?.value || '';
        const previewContainer = document.getElementById('wm-qr-preview');
        if (!previewContainer) return;

        const qrProducts = [];
        productRows.forEach(row => {
            const nameInput = row.querySelector('.wm-product-name');
            const name = nameInput ? nameInput.value.trim() : '';
            if (name) {
                const sku = `KCC_${name.toUpperCase().replace(/[^A-Z0-9]/g, '')}`;
                qrProducts.push({ name, sku });
            }
        });

        if (qrProducts.length === 0) {
            showToast('Please enter at least one product name', 'error');
            return;
        }

        previewContainer.innerHTML = '<div id="wm-qr-loading" class="text-center py-12"><div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div><p class="text-sm text-slate-600">Generating QR codes...</p></div>';

        setTimeout(() => {
            previewContainer.innerHTML = '';
            wmGeneratedQRs = [];
            wmCurrentPage = 0;

            qrProducts.forEach((product, index) => {
                const qrData = JSON.stringify({
                    product_name: product.name,
                    sku: product.sku,
                    restock_date: restockDate,
                    type: 'new_stock'
                });

                wmGeneratedQRs.push({
                    product,
                    qrData,
                    index
                });
            });

            wmRenderQRPage();

            const pagination = document.getElementById('wm-qr-pagination');
            if (pagination) {
                if (wmGeneratedQRs.length > WM_QR_PER_PAGE) {
                    pagination.classList.remove('hidden');
                    pagination.classList.add('flex');
                } else {
                    pagination.classList.add('hidden');
                    pagination.classList.remove('flex');
                }
            }

            showToast(`Generated ${qrProducts.length} QR codes successfully`, 'success');
        }, 500);
    }

    function wmRenderQRPage() {
        const previewContainer = document.getElementById('wm-qr-preview');
        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        const start = wmCurrentPage * WM_QR_PER_PAGE;
        const end = Math.min(start + WM_QR_PER_PAGE, wmGeneratedQRs.length);
        const pageItems = wmGeneratedQRs.slice(start, end);

        pageItems.forEach((item) => {
            const { product, qrData, index } = item;
            const qrCard = document.createElement('div');
            qrCard.className = 'border border-slate-200 rounded-lg p-4 bg-white';
            qrCard.innerHTML = `
                <div class="flex items-start gap-4">
                    <div id="wm-qr-code-${index}" class="w-24 h-24 flex items-center justify-center bg-white"></div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-900 text-sm">${product.name}</h3>
                        <p class="text-xs text-slate-600">SKU: ${product.sku}</p>
                        <p class="text-xs text-slate-600">Restock: ${document.getElementById('wm-qr-restock-date')?.value || ''}</p>
                        <p class="text-xs text-slate-500 mt-1">Scan to add new stock to system</p>
                    </div>
                </div>
            `;
            previewContainer.appendChild(qrCard);

            try {
                const qrElement = document.getElementById(`wm-qr-code-${index}`);
                if (qrElement && typeof QRCode !== 'undefined') {
                    new QRCode(qrElement, {
                        text: qrData,
                        width: 96,
                        height: 96,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            } catch (error) {
                console.error('Error generating QR code:', error);
            }
        });

        const totalPages = Math.ceil(wmGeneratedQRs.length / WM_QR_PER_PAGE);
        const pageInfo = document.getElementById('wm-page-info');
        const prevPage = document.getElementById('wm-prev-page');
        const nextPage = document.getElementById('wm-next-page');
        if (pageInfo) pageInfo.textContent = `Page ${wmCurrentPage + 1} of ${totalPages}`;
        if (prevPage) prevPage.disabled = wmCurrentPage === 0;
        if (nextPage) nextPage.disabled = wmCurrentPage >= totalPages - 1;
    }

    function wmPrevPage() {
        if (wmCurrentPage > 0) {
            wmCurrentPage--;
            wmRenderQRPage();
        }
    }

    function wmNextPage() {
        const totalPages = Math.ceil(wmGeneratedQRs.length / WM_QR_PER_PAGE);
        if (wmCurrentPage < totalPages - 1) {
            wmCurrentPage++;
            wmRenderQRPage();
        }
    }

    function wmPrintAllQR() {
        if (wmGeneratedQRs.length === 0) {
            showToast('Please generate QR codes first', 'error');
            return;
        }

        try {
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                showToast('Popup blocked. Please allow popups.', 'error');
                return;
            }

            let htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>New Stock QR Codes</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .qr-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px; }
                        .qr-card { border: 1px solid #ccc; padding: 15px; border-radius: 8px; text-align: center; page-break-inside: avoid; }
                        .qr-card img { max-width: 150px; border: 1px solid #eee; padding: 10px; }
                        .qr-card .info { margin: 10px 0; font-size: 12px; }
                        @media print { .qr-grid { grid-template-columns: repeat(3, 1fr); } }
                    </style>
                </head>
                <body>
                    <h2>New Stock QR Codes</h2>
                    <p>Restock Date: ${document.getElementById('wm-qr-restock-date')?.value || ''}</p>
                    <div class="qr-grid">
            `;

            wmGeneratedQRs.forEach((item) => {
                const { product, qrData } = item;
                const tempDiv = document.createElement('div');
                tempDiv.style.display = 'none';
                document.body.appendChild(tempDiv);

                const tempQrElement = document.createElement('div');
                tempDiv.appendChild(tempQrElement);

                if (typeof QRCode !== 'undefined') {
                    new QRCode(tempQrElement, {
                        text: qrData,
                        width: 150,
                        height: 150,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }

                setTimeout(() => {
                    const canvas = tempQrElement.querySelector('canvas');
                    if (canvas) {
                        const dataUrl = canvas.toDataURL('image/png');
                        htmlContent += `
                            <div class="qr-card">
                                <div class="info">
                                    <p><strong>${product.name}</strong></p>
                                    <p>${product.sku}</p>
                                    <p>Restock: ${document.getElementById('wm-qr-restock-date')?.value || ''}</p>
                                </div>
                                <img src="${dataUrl}" alt="QR Code">
                            </div>
                        `;
                    }
                    document.body.removeChild(tempDiv);
                }, 100);
            });

            setTimeout(() => {
                htmlContent += `
                    </div>
                    <p style="margin-top: 20px; font-size: 12px;">Scan each QR code to add new stock to the system</p>
                </body>
                </html>
                `;
                printWindow.document.write(htmlContent);
                printWindow.document.close();
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            }, wmGeneratedQRs.length * 150);

        } catch (error) {
            console.error('Error printing:', error);
            showToast('Error opening print dialog', 'error');
        }
    }

    // Global exports
    window.wmAddProductRow = wmAddProductRow;
    window.wmGenerateAllQR = wmGenerateAllQR;
    window.wmPrintAllQR = wmPrintAllQR;
    window.wmPrevPage = wmPrevPage;
    window.wmNextPage = wmNextPage;
    window.wmCloseScanner = wmCloseScanner;
    window.wmCloseNewStockModal = wmCloseNewStockModal;
    window.wmSaveNewStock = wmSaveNewStock;
    window.wmUpdateScannedList = wmUpdateScannedList;
    window.wmRemoveScannedItem = wmRemoveScannedItem;
    window.wmClearScannedItems = wmClearScannedItems;
    window.wmProceedToDetails = wmProceedToDetails;
    window.wmAddAllToWarehouse = wmAddAllToWarehouse;
    window.wmScanPrevPage = wmScanPrevPage;
    window.wmScanNextPage = wmScanNextPage;
});
