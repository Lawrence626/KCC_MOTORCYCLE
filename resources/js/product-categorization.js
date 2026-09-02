let products = [
    // PIPE
    { id: 1, name: 'APIDO', category: 'PIPE', sku: 'PIPE-APIDO', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 2, name: 'KVIN', category: 'PIPE', sku: 'PIPE-KVIN', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 3, name: 'TRC', category: 'PIPE', sku: 'PIPE-TRC', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 4, name: 'MVR1', category: 'PIPE', sku: 'PIPE-MVR1', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 5, name: 'MT8 TT', category: 'PIPE', sku: 'PIPE-MT8TT', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 6, name: 'MT8 ST', category: 'PIPE', sku: 'PIPE-MT8ST', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 7, name: 'MT8 V3', category: 'PIPE', sku: 'PIPE-MT8V3', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 8, name: 'MT8 RL', category: 'PIPE', sku: 'PIPE-MT8RL', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 9, name: 'MT8 CNC', category: 'PIPE', sku: 'PIPE-MT8CNC', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 10, name: 'ORBR ST', category: 'PIPE', sku: 'PIPE-ORBRST', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 11, name: 'ORBR V2', category: 'PIPE', sku: 'PIPE-ORBRV2', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 12, name: 'ORBR BT', category: 'PIPE', sku: 'PIPE-ORBRBT', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 13, name: 'HUN', category: 'PIPE', sku: 'PIPE-HUN', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 14, name: 'KENOCHI', category: 'PIPE', sku: 'PIPE-KENOCHI', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 15, name: 'SOLFILI', category: 'PIPE', sku: 'PIPE-SOLFILI', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 16, name: 'NAMBAN GT 125', category: 'PIPE', sku: 'PIPE-NAMBANGT125', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 17, name: 'TSMP', category: 'PIPE', sku: 'PIPE-TSMP', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // SHOCK
    { id: 18, name: 'RCB A3', category: 'SHOCK', sku: 'SHOCK-RCBA3', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 19, name: 'RCB S2', category: 'SHOCK', sku: 'SHOCK-RCBS2', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 20, name: 'BOMX X2', category: 'SHOCK', sku: 'SHOCK-BOMXX2', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 21, name: 'BOMX XSTREET', category: 'SHOCK', sku: 'SHOCK-BOMXXST', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 22, name: 'BOMX BLAZE', category: 'SHOCK', sku: 'SHOCK-BOMXBLAZE', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 23, name: 'BOMX PULSE', category: 'SHOCK', sku: 'SHOCK-BOMXPULSE', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 24, name: 'MUTARRU', category: 'SHOCK', sku: 'SHOCK-MUTARRU', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 25, name: 'SPARK S1', category: 'SHOCK', sku: 'SHOCK-SPARKS1', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // SWING ARM
    { id: 26, name: 'DT10', category: 'SWING ARM', sku: 'SWINGARM-DT10', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 27, name: 'V3', category: 'SWING ARM', sku: 'SWINGARM-V3', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 28, name: 'KBF', category: 'SWING ARM', sku: 'SWINGARM-KBF', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 29, name: 'MINH ANH', category: 'SWING ARM', sku: 'SWINGARM-MINHANH', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // ENGINE SUPPORT
    { id: 30, name: 'P TITANIUM', category: 'ENGINE SUPPORT', sku: 'ENGSUP-PTITANIUM', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 31, name: 'DT10', category: 'ENGINE SUPPORT', sku: 'ENGSUP-DT10', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // SIDE MIRROR
    { id: 32, name: 'H2C', category: 'SIDE MIRROR', sku: 'SIDEMIRROR-H2C', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // TIRE HUGGER
    { id: 33, name: 'YAMAHA', category: 'TIRE HUGGER', sku: 'TIREHUGGER-YAMAHA', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 34, name: 'OEM V1', category: 'TIRE HUGGER', sku: 'TIREHUGGER-OEMV1', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    { id: 35, name: 'OEM V2', category: 'TIRE HUGGER', sku: 'TIREHUGGER-OEMV2', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // MONORACK FRAME
    { id: 36, name: 'DC', category: 'MONORACK FRAME', sku: 'MONORACK-DC', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // QUICK THROTTLE
    { id: 37, name: 'KYTA', category: 'QUICK THROTTLE', sku: 'QTHROTTLE-KYTA', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse A' },
    // TIRE
    { id: 38, name: 'PRIMAAX', category: 'TIRE', sku: 'TIRE-PRIMAAX', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 39, name: 'FDR CHAMPION', category: 'TIRE', sku: 'TIRE-FDRCHAMPION', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 40, name: 'MUTARRU', category: 'TIRE', sku: 'TIRE-MUTARRU', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 41, name: 'QUICK', category: 'TIRE', sku: 'TIRE-QUICK', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 42, name: 'ZENEOS', category: 'TIRE', sku: 'TIRE-ZENEOS', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 43, name: 'PIRELLI', category: 'TIRE', sku: 'TIRE-PIRELLI', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 44, name: 'VEE RUBBER', category: 'TIRE', sku: 'TIRE-VEERUBBER', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 45, name: 'MET ZELLER', category: 'TIRE', sku: 'TIRE-METZELLER', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 46, name: 'CORSA PLATINUM', category: 'TIRE', sku: 'TIRE-CORSAPLATINUM', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 47, name: 'BEAST TIRE', category: 'TIRE', sku: 'TIRE-BEASTTIRE', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 48, name: 'MAXXIS', category: 'TIRE', sku: 'TIRE-MAXXIS', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 49, name: 'APC', category: 'TIRE', sku: 'TIRE-APC', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 50, name: 'ARISUN', category: 'TIRE', sku: 'TIRE-ARISUN', models: [], reorder_level: 10, deleted: false, warehouse: 'Warehouse B' },
    { id: 51, name: 'JOURNEY', category: 'TIRE', sku: 'TIRE-JOURNEY', models: [], reorder_level: 100, deleted: false, warehouse: 'Warehouse B' },
];

// ─── DOM References ───────────────────────────────────────────────────────────
const modal = document.getElementById('productModal');
const overlay = document.getElementById('productOverlay');
const openBtn = document.getElementById('openAddProduct');
const closeBtn = document.getElementById('closeProductModal');
const cancelBtn = document.getElementById('cancelProductModal');
const deleteBtn = document.getElementById('deleteProductBtn');
const form = document.getElementById('productForm');
const productId = document.getElementById('productId');
const productName = document.getElementById('productName');
const productCategory = document.getElementById('productCategory');
const productWarehouse = document.getElementById('productWarehouse');
const productReorderLevel = document.getElementById('productReorderLevel');
const oilVolumeGroup = document.getElementById('oilVolumeGroup');
const productSize = document.getElementById('productSize');
const oilVolumePills = document.getElementById('oilVolumePills');
const isGeneralCheckbox = document.getElementById('isGeneralCheckbox');
const compatibleModelsSection = document.getElementById('compatibleModelsSection');
const productSku = document.getElementById('productSku');
const modalTitle = document.getElementById('modalTitle');
const selectAllCheckbox = document.getElementById('selectAll');
const bulkActionsToolbar = document.getElementById('bulkActionsToolbar');
const selectedCount = document.getElementById('selectedCount');
const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
const clearSelectionBtn = document.getElementById('clearSelectionBtn');
const searchInput = document.getElementById('searchInput');
const clearFilterBtn = document.getElementById('clearFilterBtn');

// Delete List Modal
const deleteListModal = document.getElementById('deleteListModal');
const deleteListOverlay = document.getElementById('deleteListOverlay');
const openDeleteListBtn = document.getElementById('openDeleteList');
const closeDeleteListBtn = document.getElementById('closeDeleteListModal');
const cancelDeleteListBtn = document.getElementById('cancelDeleteList');
const confirmBulkDeleteBtn = document.getElementById('confirmBulkDelete');
const selectAllDeleteChk = document.getElementById('selectAllDelete');
const deleteListTableBody = document.getElementById('deleteListTableBody');
const deleteSelectedCount = document.getElementById('deleteSelectedCount');

// Trash / Restore Modal
const trashModal = document.getElementById('trashModal');
const openTrashBtn = document.getElementById('openTrashBtn');
const closeTrashBtn = document.getElementById('closeTrashModal');
const trashTableBody = document.getElementById('trashTableBody');
const trashCount = document.getElementById('trashCount');
const trashBadge = document.getElementById('trashBadge');
const selectAllTrashChk = document.getElementById('selectAllTrash');
const trashSelectedCount = document.getElementById('trashSelectedCount');
const restoreSelectedBtn = document.getElementById('restoreSelectedBtn');
const permanentDeleteBtn = document.getElementById('permanentDeleteBtn');

// ─── State ────────────────────────────────────────────────────────────────────
let currentEditId = null;
let selectedIds = [];
let deleteSelectedIds = [];
let trashSelectedIds = [];

// ─── Size Slug Helper ─────────────────────────────────────────────────────────
function normalizeSizeSlug(size, category = '', brand = '') {
    if (!size || !size.trim()) return '';
    let s = size.trim();
    if (/^\d+(\.\d+)?$/.test(s)) {
        const val = parseFloat(s);
        s = val >= 10 ? `${val}mL` : `${val}L`;
    }
    const match = s.match(/^(\d+(?:\.\d+)?)\s*(ml|l|liter|liters|litre|litres)$/i);
    if (match) {
        let num = parseFloat(match[1]);
        const unit = match[2].toLowerCase();
        if (unit.startsWith('l')) {
            return `${num}L`;
        }
        if (unit === 'ml') {
            if (num >= 1000 && num % 1000 === 0) {
                return `${num / 1000}L`;
            }
            return `${num}ML`;
        }
    }
    return s.toUpperCase().replace(/\s+/g, '_');
}

// ─── Toggle Oil Volume & General Item UI ──────────────────────────────────────
function toggleOilAndGeneralState() {
    const category = (productCategory?.value || '').toUpperCase();
    const isOilOrFluid = /OIL|FLUID|COOLANT|CLEANER|SEALANT|LUBRICANT/.test(category);

    if (oilVolumeGroup) {
        if (isOilOrFluid) {
            oilVolumeGroup.classList.remove('hidden');
            if (isGeneralCheckbox && !isGeneralCheckbox.dataset.userModified) {
                isGeneralCheckbox.checked = true;
            }
        } else {
            oilVolumeGroup.classList.add('hidden');
        }
    }

    if (compatibleModelsSection && isGeneralCheckbox) {
        if (isGeneralCheckbox.checked) {
            compatibleModelsSection.classList.add('hidden');
            document.querySelectorAll('.motorcycle-checkbox').forEach(cb => cb.checked = false);
        } else {
            compatibleModelsSection.classList.remove('hidden');
        }
    }
}

// ─── Auto-generate SKU helper ─────────────────────────────────────────────────
function autoGenerateSku() {
    const category = productCategory?.value || '';
    const brand = productName?.value || '';
    const size = productSize?.value || '';

    if (!category || !brand) return;

    const descSlug = category.toUpperCase().replace(/[\/\(\)\s]+/g, '_').replace(/^_+|_+$/g, '');
    const brandSlug = brand.toUpperCase().replace(/[\/\(\)\s]+/g, '_').replace(/^_+|_+$/g, '');
    const sizeSlug = normalizeSizeSlug(size, category, brand);

    const sameDescBrand = products.filter(p =>
        !p.deleted &&
        (p.category || '').toUpperCase() === category.toUpperCase() &&
        (p.name || '').toUpperCase() === brand.toUpperCase() &&
        (p.id !== currentEditId)
    );
    const seq = String(sameDescBrand.length + 1).padStart(3, '0');

    if (sizeSlug) {
        productSku.value = `KCC_${descSlug}_${brandSlug}_${sizeSlug}_${seq}`;
    } else {
        productSku.value = `KCC_${descSlug}_${brandSlug}_${seq}`;
    }
}

// ─── Active-product helpers ───────────────────────────────────────────────────
function activeProducts() { return products.filter(p => !p.deleted); }
function deletedProducts() { return products.filter(p => p.deleted); }

// ─── Trash badge ──────────────────────────────────────────────────────────────
function updateTrashBadge() {
    const count = deletedProducts().length;
    if (trashBadge) {
        trashBadge.textContent = count;
        trashBadge.style.display = count > 0 ? 'inline-flex' : 'none';
    }
}

// ─── Main table ───────────────────────────────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const active = activeProducts().filter(product => {
        if (!searchTerm) return true;
        return (
            product.name.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm) ||
            product.sku.toLowerCase().includes(searchTerm)
        );
    });

    if (active.length === 0) {
        tbody.innerHTML = '<tr class="hover:bg-slate-50"><td colspan="8" class="px-6 py-8 text-center text-slate-500">No products found. Add one to get started.</td></tr>';
        return;
    }

    tbody.innerHTML = active.map(product => `
        <tr class="hover:bg-slate-50">
            <td class="px-4 py-3">
                <input type="checkbox" class="product-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" value="${product.id}" ${selectedIds.includes(product.id) ? 'checked' : ''}>
            </td>
            <td class="px-4 py-3 font-semibold text-slate-900">${product.name}</td>
            <td class="px-4 py-3 text-slate-600"><span class="px-2.5 py-0.5 rounded-full bg-[#105f68] text-[#00fff2] text-[11px] font-semibold">${product.category}</span></td>
            <td class="px-4 py-3 text-slate-600 font-mono text-xs">${product.sku}</td>
            <td class="px-4 py-3 text-slate-600 text-xs">${product.warehouse || '-'}</td>
            <td class="px-4 py-3 text-slate-600">
                ${(product.is_general || !product.models || product.models.length === 0 || product.models.includes('Universal'))
                    ? '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs font-semibold">Universal / General</span>'
                    : `<div class="flex flex-wrap gap-1">${product.models.map(m => `<span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-xs">${m}</span>`).join('')}</div>`
                }
            </td>
            <td class="px-4 py-3 text-center font-semibold ${(product.reorder_level ?? 10) >= 100 ? 'text-amber-600' : 'text-slate-700'}">${product.reorder_level ?? 10}</td>
            <td class="px-4 py-3 text-center"><button onclick="openEditProduct(${product.id})" class="text-[#105f68] hover:underline text-xs font-semibold">Edit</button></td>
        </tr>
    `).join('');

    attachCheckboxListeners();
}

// ─── Add/Edit Modal ───────────────────────────────────────────────────────────
function openModal(id = null) {
    currentEditId = id;
    form.reset();
    document.querySelectorAll('.motorcycle-checkbox').forEach(cb => cb.checked = false);

    if (oilVolumePills) {
        oilVolumePills.querySelectorAll('.volume-pill').forEach(p => {
            p.classList.remove('bg-[#00fff2]', 'text-slate-900', 'border-cyan-400');
            p.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
        });
    }

    if (id) {
        const product = products.find(p => p.id === id);
        if (product) {
            productId.value = id;
            productName.value = product.name || '';
            productCategory.value = product.category || '';
            if (productWarehouse) productWarehouse.value = product.warehouse || 'Warehouse A';
            if (productSize) productSize.value = product.size || '';
            productSku.value = product.sku || '';
            if (productReorderLevel) productReorderLevel.value = product.reorder_level ?? 10;
            
            const isGeneral = product.is_general || !product.models || product.models.length === 0 || product.models.includes('Universal');
            if (isGeneralCheckbox) {
                isGeneralCheckbox.checked = isGeneral;
                isGeneralCheckbox.dataset.userModified = 'true';
            }

            if (!isGeneral && product.models) {
                product.models.forEach(model => {
                    const checkbox = document.querySelector(`.motorcycle-checkbox[value="${model}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }

            // Highlight matching volume pill
            if (oilVolumePills && product.size) {
                oilVolumePills.querySelectorAll('.volume-pill').forEach(p => {
                    if (p.dataset.volume.toLowerCase() === product.size.toLowerCase()) {
                        p.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        p.classList.add('bg-[#00fff2]', 'text-slate-900', 'border-cyan-400');
                    }
                });
            }

            modalTitle.textContent = 'Edit Product';
            deleteBtn.classList.remove('hidden');
        }
    } else {
        productId.value = '';
        if (productWarehouse) productWarehouse.value = 'Warehouse A';
        if (productSize) productSize.value = '';
        if (isGeneralCheckbox) {
            isGeneralCheckbox.checked = false;
            isGeneralCheckbox.dataset.userModified = '';
        }
        modalTitle.textContent = 'Add Product';
        deleteBtn.classList.add('hidden');
    }

    toggleOilAndGeneralState();
    modal.style.display = 'flex';
    document.body.classList.add('overflow-hidden');
}

function closeModal() {
    modal.style.display = 'none';
    document.body.classList.remove('overflow-hidden');
}

function openEditProduct(id) { openModal(id); }

function handleFormSubmit(event) {
    event.preventDefault();
    const isGeneral = isGeneralCheckbox ? isGeneralCheckbox.checked : false;
    const selectedModels = isGeneral 
        ? ['Universal'] 
        : Array.from(document.querySelectorAll('.motorcycle-checkbox:checked')).map(cb => cb.value);

    const category = productCategory.value;
    const brand = productName.value;
    const warehouse = productWarehouse ? productWarehouse.value : 'Warehouse A';
    const size = productSize ? productSize.value : '';

    // Slugify: uppercase, spaces → underscores
    const descSlug = category.toUpperCase().replace(/[\/\(\)\s]+/g, '_').replace(/^_+|_+$/g, '');
    const brandSlug = brand.toUpperCase().replace(/[\/\(\)\s]+/g, '_').replace(/^_+|_+$/g, '');
    const sizeSlug = normalizeSizeSlug(size, category, brand);

    if (productId.value) {
        // EDIT: update existing product
        const product = products.find(p => p.id === parseInt(productId.value, 10));
        if (product) {
            product.name = brand;
            product.category = category;
            product.warehouse = warehouse;
            product.size = size;
            product.is_general = isGeneral;
            product.reorder_level = parseInt(productReorderLevel?.value) || 10;
            product.models = selectedModels;

            const sameDescBrand = products.filter(p =>
                !p.deleted &&
                p.category === category &&
                p.name === brand &&
                p.id !== product.id
            );
            const seq = String(sameDescBrand.length + 1).padStart(3, '0');
            if (productSku.value) {
                product.sku = productSku.value;
            } else if (sizeSlug) {
                product.sku = `KCC_${descSlug}_${brandSlug}_${sizeSlug}_${seq}`;
            } else {
                product.sku = `KCC_${descSlug}_${brandSlug}_${seq}`;
            }
        }
    } else {
        // ADD: count non-deleted products with same desc+brand to get next seq
        const sameDescBrand = products.filter(p =>
            !p.deleted &&
            p.category === category &&
            p.name === brand
        );
        const seq = String(sameDescBrand.length + 1).padStart(3, '0');
        let generatedSku = productSku.value;
        if (!generatedSku) {
            if (sizeSlug) {
                generatedSku = `KCC_${descSlug}_${brandSlug}_${sizeSlug}_${seq}`;
            } else {
                generatedSku = `KCC_${descSlug}_${brandSlug}_${seq}`;
            }
        }

        products.push({
            id: Math.max(...products.map(p => p.id), 0) + 1,
            name: brand,
            category,
            warehouse,
            size,
            is_general: isGeneral,
            sku: generatedSku,
            models: selectedModels,
            reorder_level: parseInt(productReorderLevel?.value) || 10,
            deleted: false,
        });
    }

    renderTable();
    closeModal();
}

// Soft-delete a single product from the Edit modal
function handleDelete() {
    if (currentEditId && confirm('Move this product to Trash? You can restore it later.')) {
        const product = products.find(p => p.id === currentEditId);
        if (product) product.deleted = true;
        selectedIds = selectedIds.filter(id => id !== currentEditId);
        updateBulkActions();
        updateTrashBadge();
        renderTable();
        closeModal();
    }
}

// ─── Checkbox / Bulk actions ───────────────────────────────────────────────────
function attachCheckboxListeners() {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const id = parseInt(this.value);
            if (this.checked) {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(s => s !== id);
            }
            updateBulkActions();
        });
    });
}

function updateBulkActions() {
    const count = selectedIds.length;
    if (bulkActionsToolbar) {
        bulkActionsToolbar.style.display = count > 0 ? 'flex' : 'none';
    }
    if (selectedCount) selectedCount.textContent = count;
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = count === activeProducts().length && count > 0;
    }
}

function clearSelection() {
    selectedIds = [];
    updateBulkActions();
    renderTable();
}

// Bulk soft-delete (from the inline toolbar checkboxes)
function handleBulkDelete() {
    if (selectedIds.length === 0) return;
    const names = products.filter(p => selectedIds.includes(p.id)).map(p => p.name).join(', ');
    if (confirm(`Move ${selectedIds.length} product(s) to Trash?\n\n${names}\n\nYou can restore them later.`)) {
        selectedIds.forEach(id => {
            const p = products.find(p => p.id === id);
            if (p) p.deleted = true;
        });
        selectedIds = [];
        updateBulkActions();
        updateTrashBadge();
        renderTable();
    }
}

// ─── Delete List Modal (soft-delete picker) ────────────────────────────────────
function openDeleteListModal() {
    deleteSelectedIds = [];
    renderDeleteListTable();
    deleteListModal.style.display = 'flex';
    document.body.classList.add('overflow-hidden');
}

function closeDeleteListModal() {
    deleteListModal.style.display = 'none';
    document.body.classList.remove('overflow-hidden');
}

function renderDeleteListTable() {
    if (!deleteListTableBody) return;
    const active = activeProducts();
    if (active.length === 0) {
        deleteListTableBody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-sm">No active products.</td></tr>';
        updateDeleteSelectedCount();
        return;
    }
    deleteListTableBody.innerHTML = active.map(product => `
        <tr class="hover:bg-slate-50">
            <td class="px-4 py-2">
                <input type="checkbox" class="delete-checkbox rounded border-slate-300 text-red-500 focus:ring-red-400" value="${product.id}" ${deleteSelectedIds.includes(product.id) ? 'checked' : ''}>
            </td>
            <td class="px-4 py-2 font-medium text-slate-900">${product.name}</td>
            <td class="px-4 py-2 text-slate-600"><span class="px-2.5 py-0.5 rounded-full bg-[#105f68] text-[#00fff2] text-[11px] font-semibold">${product.category}</span></td>
            <td class="px-4 py-2 text-slate-600 font-mono text-xs">${product.sku}</td>
        </tr>
    `).join('');

    attachDeleteCheckboxListeners();
    updateDeleteSelectedCount();
}

function attachDeleteCheckboxListeners() {
    document.querySelectorAll('.delete-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const id = parseInt(this.value);
            if (this.checked) {
                if (!deleteSelectedIds.includes(id)) deleteSelectedIds.push(id);
            } else {
                deleteSelectedIds = deleteSelectedIds.filter(s => s !== id);
            }
            updateDeleteSelectedCount();
        });
    });
}

function updateDeleteSelectedCount() {
    if (deleteSelectedCount) deleteSelectedCount.textContent = deleteSelectedIds.length;
    if (selectAllDeleteChk) {
        selectAllDeleteChk.checked = deleteSelectedIds.length === activeProducts().length && deleteSelectedIds.length > 0;
    }
}

// Soft-delete from Delete List modal
function handleConfirmBulkDelete() {
    if (deleteSelectedIds.length === 0) {
        alert('Please select at least one product to delete.');
        return;
    }
    const names = products.filter(p => deleteSelectedIds.includes(p.id)).map(p => p.name).join(', ');
    if (confirm(`Move ${deleteSelectedIds.length} product(s) to Trash?\n\n${names}\n\nYou can restore them from the Trash.`)) {
        deleteSelectedIds.forEach(id => {
            const p = products.find(p => p.id === id);
            if (p) p.deleted = true;
        });
        // Remove any soft-deleted items from the main selection too
        selectedIds = selectedIds.filter(id => !deleteSelectedIds.includes(id));
        deleteSelectedIds = [];
        updateBulkActions();
        updateTrashBadge();
        closeDeleteListModal();
        renderTable();
    }
}

// ─── Trash / Restore Modal ────────────────────────────────────────────────────
function openTrashModal() {
    trashSelectedIds = [];
    renderTrashTable();
    trashModal.style.display = 'flex';
    document.body.classList.add('overflow-hidden');
}

function closeTrashModal() {
    trashModal.style.display = 'none';
    document.body.classList.remove('overflow-hidden');
}

function renderTrashTable() {
    if (!trashTableBody) return;
    const deleted = deletedProducts();

    if (trashCount) trashCount.textContent = deleted.length;

    if (deleted.length === 0) {
        trashTableBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-10 text-center">
                    <div class="flex flex-col items-center gap-2 text-slate-400">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span class="text-sm">Trash is empty</span>
                    </div>
                </td>
            </tr>`;
        updateTrashActions();
        return;
    }

    trashTableBody.innerHTML = deleted.map(product => `
        <tr class="hover:bg-red-50/40">
            <td class="px-4 py-2">
                <input type="checkbox" class="trash-checkbox rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" value="${product.id}" ${trashSelectedIds.includes(product.id) ? 'checked' : ''}>
            </td>
            <td class="px-4 py-2 font-medium text-slate-700">${product.name}</td>
            <td class="px-4 py-2 text-slate-500"><span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">${product.category}</span></td>
            <td class="px-4 py-2 text-slate-400 font-mono text-xs">${product.sku}</td>
        </tr>
    `).join('');

    attachTrashCheckboxListeners();
    updateTrashActions();
}

function attachTrashCheckboxListeners() {
    document.querySelectorAll('.trash-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const id = parseInt(this.value);
            if (this.checked) {
                if (!trashSelectedIds.includes(id)) trashSelectedIds.push(id);
            } else {
                trashSelectedIds = trashSelectedIds.filter(s => s !== id);
            }
            updateTrashActions();
        });
    });
}

function updateTrashActions() {
    const count = trashSelectedIds.length;
    if (trashSelectedCount) trashSelectedCount.textContent = count;
    if (restoreSelectedBtn) restoreSelectedBtn.disabled = count === 0;
    if (permanentDeleteBtn) permanentDeleteBtn.disabled = count === 0;
    if (selectAllTrashChk) {
        selectAllTrashChk.checked = count === deletedProducts().length && count > 0;
    }
}

// Restore selected from trash
function handleRestoreSelected() {
    if (trashSelectedIds.length === 0) return;
    trashSelectedIds.forEach(id => {
        const p = products.find(p => p.id === id);
        if (p) p.deleted = false;
    });
    trashSelectedIds = [];
    updateTrashBadge();
    renderTable();
    renderTrashTable();
}

// Permanently delete selected from trash
function handlePermanentDelete() {
    if (trashSelectedIds.length === 0) return;
    const names = products.filter(p => trashSelectedIds.includes(p.id)).map(p => p.name).join(', ');
    if (confirm(`Permanently delete ${trashSelectedIds.length} product(s)? This CANNOT be undone.\n\n${names}`)) {
        products = products.filter(p => !trashSelectedIds.includes(p.id));
        trashSelectedIds = [];
        updateTrashBadge();
        renderTable();
        renderTrashTable();
    }
}

// ─── Expose globals ───────────────────────────────────────────────────────────
window.openEditProduct = openEditProduct;
window.clearSelection = clearSelection;

// ─── Event listeners ──────────────────────────────────────────────────────────
if (openBtn) openBtn.addEventListener('click', () => openModal());
if (closeBtn) closeBtn.addEventListener('click', closeModal);
if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
if (overlay) overlay.addEventListener('click', closeModal);
if (form) form.addEventListener('submit', handleFormSubmit);
if (deleteBtn) deleteBtn.addEventListener('click', handleDelete);
if (bulkDeleteBtn) bulkDeleteBtn.addEventListener('click', handleBulkDelete);
if (clearSelectionBtn) clearSelectionBtn.addEventListener('click', clearSelection);

// Category & General & SKU Listeners
if (productCategory) {
    productCategory.addEventListener('change', function() {
        if (isGeneralCheckbox && !isGeneralCheckbox.dataset.userModified) {
            const isOilOrFluid = /OIL|FLUID|COOLANT|CLEANER|SEALANT|LUBRICANT/.test((this.value || '').toUpperCase());
            isGeneralCheckbox.checked = isOilOrFluid;
        }
        toggleOilAndGeneralState();
        autoGenerateSku();
    });
}

if (isGeneralCheckbox) {
    isGeneralCheckbox.addEventListener('change', function() {
        this.dataset.userModified = 'true';
        toggleOilAndGeneralState();
    });
}

if (productName) {
    productName.addEventListener('input', autoGenerateSku);
}

if (productSize) {
    productSize.addEventListener('input', autoGenerateSku);
}

// Volume pills click handler
if (oilVolumePills) {
    oilVolumePills.querySelectorAll('.volume-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            if (productSize) {
                productSize.value = this.dataset.volume;
                oilVolumePills.querySelectorAll('.volume-pill').forEach(p => {
                    p.classList.remove('bg-[#00fff2]', 'text-slate-900', 'border-cyan-400');
                    p.classList.add('bg-white', 'text-slate-700', 'border-slate-200');
                });
                this.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                this.classList.add('bg-[#00fff2]', 'text-slate-900', 'border-cyan-400');
                autoGenerateSku();
            }
        });
    });
}

// Search filter
if (searchInput) {
    searchInput.addEventListener('input', renderTable);
}
if (clearFilterBtn) {
    clearFilterBtn.addEventListener('click', function () {
        if (searchInput) {
            searchInput.value = '';
            renderTable();
        }
    });
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
            const id = parseInt(checkbox.value);
            if (this.checked) {
                if (!selectedIds.includes(id)) selectedIds.push(id);
            } else {
                selectedIds = selectedIds.filter(s => s !== id);
            }
        });
        updateBulkActions();
    });
}

// Delete List modal
if (openDeleteListBtn) openDeleteListBtn.addEventListener('click', openDeleteListModal);
if (closeDeleteListBtn) closeDeleteListBtn.addEventListener('click', closeDeleteListModal);
if (cancelDeleteListBtn) cancelDeleteListBtn.addEventListener('click', closeDeleteListModal);
if (deleteListOverlay) deleteListOverlay.addEventListener('click', closeDeleteListModal);
if (confirmBulkDeleteBtn) confirmBulkDeleteBtn.addEventListener('click', handleConfirmBulkDelete);

if (selectAllDeleteChk) {
    selectAllDeleteChk.addEventListener('change', function () {
        document.querySelectorAll('.delete-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
            const id = parseInt(checkbox.value);
            if (this.checked) {
                if (!deleteSelectedIds.includes(id)) deleteSelectedIds.push(id);
            } else {
                deleteSelectedIds = deleteSelectedIds.filter(s => s !== id);
            }
        });
        updateDeleteSelectedCount();
    });
}

// Trash modal
if (openTrashBtn) openTrashBtn.addEventListener('click', openTrashModal);
if (closeTrashBtn) closeTrashBtn.addEventListener('click', closeTrashModal);
if (restoreSelectedBtn) restoreSelectedBtn.addEventListener('click', handleRestoreSelected);
if (permanentDeleteBtn) permanentDeleteBtn.addEventListener('click', handlePermanentDelete);

if (selectAllTrashChk) {
    selectAllTrashChk.addEventListener('change', function () {
        document.querySelectorAll('.trash-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
            const id = parseInt(checkbox.value);
            if (this.checked) {
                if (!trashSelectedIds.includes(id)) trashSelectedIds.push(id);
            } else {
                trashSelectedIds = trashSelectedIds.filter(s => s !== id);
            }
        });
        updateTrashActions();
    });
}

// Close modals on Escape
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        if (trashModal && trashModal.style.display === 'flex') closeTrashModal();
        else if (deleteListModal && deleteListModal.style.display === 'flex') closeDeleteListModal();
        else if (modal && modal.style.display === 'flex') closeModal();
    }
});

// ─── Init ─────────────────────────────────────────────────────────────────────
renderTable();
updateTrashBadge();
