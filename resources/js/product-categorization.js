let products = [];

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
const productSku = document.getElementById('productSku');
const modalTitle = document.getElementById('modalTitle');
let currentEditId = null;

function openModal(id = null) {
    currentEditId = id;
    form.reset();
    document.querySelectorAll('.motorcycle-checkbox').forEach(cb => cb.checked = false);

    if (id) {
        const product = products.find(p => p.id === id);
        if (product) {
            productId.value = id;
            productName.value = product.name;
            productCategory.value = product.category;
            productSku.value = product.sku;
            product.models.forEach(model => {
                const checkbox = document.querySelector(`.motorcycle-checkbox[value="${model}"]`);
                if (checkbox) checkbox.checked = true;
            });
            modalTitle.textContent = 'Edit Product';
            deleteBtn.classList.remove('hidden');
        }
    } else {
        productId.value = '';
        modalTitle.textContent = 'Add Product';
        deleteBtn.classList.add('hidden');
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeModal() {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function renderTable() {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    if (products.length === 0) {
        tbody.innerHTML = '<tr class="hover:bg-slate-50"><td colspan="5" class="px-6 py-8 text-center text-slate-500">No products found. Add one to get started.</td></tr>';
        return;
    }

    tbody.innerHTML = products.map(product => `
        <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-900">${product.name}</td>
            <td class="px-6 py-3 text-slate-600"><span class="px-2 py-1 rounded-full bg-cyan-100 text-cyan-700 text-xs font-medium">${product.category}</span></td>
            <td class="px-6 py-3 text-slate-600 font-mono text-xs">${product.sku}</td>
            <td class="px-6 py-3 text-slate-600"><div class="flex flex-wrap gap-1">${product.models.map(m => `<span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs">${m}</span>`).join('')}</div></td>
            <td class="px-6 py-3 text-center"><button onclick="openEditProduct(${product.id})" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">Edit</button></td>
        </tr>
    `).join('');
}

function openEditProduct(id) {
    openModal(id);
}

function handleFormSubmit(event) {
    event.preventDefault();
    const selectedModels = Array.from(document.querySelectorAll('.motorcycle-checkbox:checked')).map(cb => cb.value);

    if (productId.value) {
        const product = products.find(p => p.id === parseInt(productId.value, 10));
        if (product) {
            product.name = productName.value;
            product.category = productCategory.value;
            product.sku = productSku.value;
            product.models = selectedModels;
        }
    } else {
        products.push({
            id: Math.max(...products.map(p => p.id), 0) + 1,
            name: productName.value,
            category: productCategory.value,
            sku: productSku.value,
            models: selectedModels,
        });
    }

    renderTable();
    closeModal();
}

function handleDelete() {
    if (currentEditId && confirm('Are you sure you want to delete this product?')) {
        products = products.filter(p => p.id !== currentEditId);
        renderTable();
        closeModal();
    }
}

window.openEditProduct = openEditProduct;

if (openBtn) openBtn.addEventListener('click', () => openModal());
if (closeBtn) closeBtn.addEventListener('click', closeModal);
if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
if (overlay) overlay.addEventListener('click', closeModal);
if (form) form.addEventListener('submit', handleFormSubmit);
if (deleteBtn) deleteBtn.addEventListener('click', handleDelete);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
        closeModal();
    }
});

renderTable();
