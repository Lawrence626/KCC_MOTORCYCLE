<x-layouts.app :title="__('Product Categorization')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Product Categorization</h1>
                <p class="text-xs text-slate-500 mt-1">Add, update, and delete product categories. Set SKU (QR code) and filter compatibility for each motorcycle.</p>
            </div>
            <button id="openAddProduct" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">+ Add Product</button>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Product Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Category</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">SKU (QR Code)</th>
                            <th class="px-6 py-3 text-left font-semibold text-slate-900">Compatible Models</th>
                            <th class="px-6 py-3 text-center font-semibold text-slate-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="divide-y divide-slate-200">
                        <!-- Products will be loaded here -->
                        <tr class="hover:bg-slate-50">
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No products found. Add one to get started.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div id="productOverlay" class="absolute inset-0 bg-black/40"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl border border-slate-200 p-6 z-10">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="modalTitle" class="text-lg font-semibold text-slate-900">Add Product</h3>
                    <p class="text-xs text-slate-500">Set product category, SKU, and motorcycle compatibility</p>
                </div>
                <button id="closeProductModal" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form id="productForm">
                <input type="hidden" id="productId" />
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Product Name</label>
                        <input type="text" id="productName" placeholder="e.g., Chain Sprocket" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 font-medium">Category</label>
                        <select id="productCategory" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required>
                            <option value="">Select category</option>
                            <option value="Engine">Engine</option>
                            <option value="Transmission">Transmission</option>
                            <option value="Brakes">Brakes</option>
                            <option value="Suspension">Suspension</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Body Parts">Body Parts</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-slate-500 font-medium">SKU / QR Code</label>
                        <input type="text" id="productSku" placeholder="e.g., SKU-2024-001 or scan QR code" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm mt-1" required />
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-slate-500 font-medium mb-2 block">Compatible Motorcycle Models</label>
                        <div id="motorcycleList" class="space-y-2 max-h-40 overflow-y-auto">
                            <!-- Motorcycle checkboxes will load here -->
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="CB150" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">CB150</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="Wave110" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">Wave 110</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="ADV160" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">ADV 160</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" value="XRE300" class="motorcycle-checkbox rounded border-slate-300" />
                                <span class="text-sm">XRE 300</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">Save Product</button>
                    <button type="button" id="cancelProductModal" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Cancel</button>
                    <button type="button" id="deleteProductBtn" class="ml-auto px-4 py-2 rounded-lg border border-red-200 text-sm text-red-600 hover:bg-red-50 hidden">Delete Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Empty list - fetch from API in production
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
            if (products.length === 0) {
                tbody.innerHTML = '<tr class="hover:bg-slate-50"><td colspan="5" class="px-6 py-8 text-center text-slate-500">No products found. Add one to get started.</td></tr>';
                return;
            }
            tbody.innerHTML = products.map(product => `
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium text-slate-900">${product.name}</td>
                    <td class="px-6 py-3 text-slate-600"><span class="px-2 py-1 rounded-full bg-cyan-100 text-cyan-700 text-xs font-medium">${product.category}</span></td>
                    <td class="px-6 py-3 text-slate-600 font-mono text-xs">${product.sku}</td>
                    <td class="px-6 py-3 text-slate-600">
                        <div class="flex flex-wrap gap-1">
                            ${product.models.map(m => `<span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs">${m}</span>`).join('')}
                        </div>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <button onclick="openEditProduct(${product.id})" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">Edit</button>
                    </td>
                </tr>
            `).join('');
        }

        function openEditProduct(id) {
            openModal(id);
        }

        openBtn.addEventListener('click', () => openModal());
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const selectedModels = Array.from(document.querySelectorAll('.motorcycle-checkbox:checked')).map(cb => cb.value);

            if (productId.value) {
                const product = products.find(p => p.id === parseInt(productId.value));
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
                    models: selectedModels
                });
            }
            renderTable();
            closeModal();
        });

        deleteBtn.addEventListener('click', () => {
            if (currentEditId && confirm('Are you sure you want to delete this product?')) {
                products = products.filter(p => p.id !== currentEditId);
                renderTable();
                closeModal();
            }
        });

        // Initial render
        renderTable();
    </script>
    @vite('resources/js/product-categorization.js')
</x-layouts.app>
