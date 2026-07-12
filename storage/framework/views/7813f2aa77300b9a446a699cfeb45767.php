<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Archived Items')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Archived Items'))]); ?>
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Archived Items</h1>
                <p class="text-xs text-slate-500 mt-0.5">Archived inventory items. Restore or permanently delete</p>
            </div>
            <div class="flex gap-2 items-center">
                <a href="<?php echo e(url()->previous() ?: route('allstocks')); ?>" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    BACK TO POS
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Archived Items</p>
                <p id="archivedCount" class="text-lg font-bold text-slate-900">0</p>
                <p class="text-xs text-slate-500 mt-0.5">Total archived</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Archive Value</p>
                <p id="archiveValue" class="text-lg font-bold text-slate-900">₱0</p>
                <p class="text-xs text-slate-500 mt-0.5">Total value</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-2.5 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Avg Price</p>
                <p id="avgPrice" class="text-lg font-bold text-slate-900">₱0</p>
                <p class="text-xs text-slate-500 mt-0.5">Per item</p>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg border border-slate-200 p-2.5 shadow-sm space-y-2">
            <input id="searchInput" type="search" placeholder="Search by product name, SKU, or barcode..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />

            <div class="flex gap-2 items-end">
                <div class="flex-1">
                    <label class="block text-slate-600 font-medium mb-0.5 text-xs">Category</label>
                    <select id="categoryFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option value="engine_oil">Engine Oil</option>
                        <option value="battery">Battery</option>
                        <option value="spark_plug">Spark Plug</option>
                        <option value="brake_pads">Brake Pads</option>
                        <option value="tires">Tires</option>
                        <option value="filters">Filters</option>
                        <option value="lubricants">Lubricants</option>
                        <option value="accessories">Accessories</option>
                    </select>
                </div>
                <button id="applyFilter" class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 transition whitespace-nowrap">Apply</button>
                <button id="resetFilter" class="px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-600 hover:bg-slate-50 transition whitespace-nowrap">Reset</button>
            </div>
        </div>

        <!-- Archived Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Product</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">SKU</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Category</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 uppercase tracking-wide">Stock</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Unit Price</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total Value</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Archived Date</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="archivedTableBody" class="divide-y divide-slate-200">
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500">Loading archived items...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer - Pagination -->
            <div id="pagination" class="hidden px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <span>Showing</span>
                    <select id="perPage" class="px-2 py-1 rounded border border-slate-300 bg-white text-xs">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span id="showingText">of 0 items</span>
                </div>
                <div class="flex gap-1">
                    <button id="prevPage" class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50">← Prev</button>
                    <button id="nextPage" class="px-2 py-1 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50">Next →</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let perPage = 10;
        let totalPages = 1;

        async function loadArchivedProducts() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            
            const url = new URL('/api/products/archived', window.location.origin);
            url.searchParams.set('page', currentPage);
            url.searchParams.set('per_page', perPage);
            if (search) url.searchParams.set('search', search);
            if (category) url.searchParams.set('category', category);

            try {
                const response = await fetch(url);
                const result = await response.json();
                
                console.log('Archived products response:', result);
                
                if (result.success) {
                    renderTable(result.data);
                    updatePagination(result.pagination);
                    updateStats(result.data);
                } else {
                    console.error('API returned error:', result.message);
                    document.getElementById('archivedTableBody').innerHTML = '<tr><td colspan="8" class="px-3 py-8 text-center text-red-500">Error loading archived items: ' + (result.message || 'Unknown error') + '</td></tr>';
                }
            } catch (error) {
                console.error('Error loading archived products:', error);
                document.getElementById('archivedTableBody').innerHTML = '<tr><td colspan="8" class="px-3 py-8 text-center text-red-500">Error loading archived items. Please check console for details.</td></tr>';
            }
        }

        function renderTable(products) {
            const tbody = document.getElementById('archivedTableBody');
            tbody.innerHTML = '';

            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-8 text-center text-slate-500">No archived items found</td></tr>';
                return;
            }

            products.forEach(product => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50 transition opacity-70';
                const unitPrice = parseFloat(product.unit_price) || 0;
                const totalValue = (product.stock_quantity || 0) * unitPrice;
                const archivedDate = product.updated_at ? new Date(product.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
                
                row.innerHTML = `
                    <td class="px-3 py-2">
                        <div>
                            <p class="font-medium text-slate-900">${product.product_name || product.name || 'Unnamed'}</p>
                            <p class="text-xs text-slate-500">${product.description || ''}</p>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-slate-600">${product.sku || 'N/A'}</td>
                    <td class="px-3 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">${product.category || 'Uncategorized'}</span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex flex-col items-center">
                            <span class="font-semibold text-slate-900">${product.stock_quantity || 0}</span>
                            <span class="text-xs text-slate-500">units</span>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-right font-medium text-slate-900">₱${unitPrice.toFixed(2)}</td>
                    <td class="px-3 py-2 text-right font-medium text-slate-900">₱${totalValue.toFixed(2)}</td>
                    <td class="px-3 py-2 text-slate-600">${archivedDate}</td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex gap-1 justify-center">
                            <button onclick="restoreProduct(${product.id})" class="p-1 text-cyan-600 hover:bg-cyan-50 rounded transition" title="Restore">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <button onclick="permanentDeleteProduct(${product.id})" class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Permanently Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function updatePagination(pagination) {
            totalPages = pagination.last_page;
            currentPage = pagination.current_page;
            
            document.getElementById('showingText').textContent = `of ${pagination.total} items`;
            document.getElementById('prevPage').disabled = currentPage <= 1;
            document.getElementById('nextPage').disabled = currentPage >= totalPages;
            
            const paginationDiv = document.getElementById('pagination');
            if (pagination.total > 0) {
                paginationDiv.classList.remove('hidden');
            } else {
                paginationDiv.classList.add('hidden');
            }
        }

        function updateStats(products) {
            const count = products.length;
            const totalValue = products.reduce((sum, p) => sum + ((p.stock_quantity || 0) * (p.unit_price || 0)), 0);
            const avgPrice = count > 0 ? totalValue / count : 0;
            
            document.getElementById('archivedCount').textContent = count;
            document.getElementById('archiveValue').textContent = `₱${totalValue.toFixed(2)}`;
            document.getElementById('avgPrice').textContent = `₱${avgPrice.toFixed(2)}`;
        }

        async function restoreProduct(id) {
            if (!confirm('Are you sure you want to restore this product?')) return;
            
            try {
                const response = await fetch(`/api/product/${id}/restore`, { method: 'POST' });
                const result = await response.json();
                
                if (result.success) {
                    alert('Product restored successfully');
                    loadArchivedProducts();
                } else {
                    alert('Failed to restore product: ' + result.message);
                }
            } catch (error) {
                console.error('Error restoring product:', error);
                alert('Error restoring product');
            }
        }

        async function permanentDeleteProduct(id) {
            if (!confirm('Are you sure you want to permanently delete this product? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`/api/product/${id}/permanent`, { method: 'DELETE' });
                const result = await response.json();
                
                if (result.success) {
                    alert('Product permanently deleted');
                    loadArchivedProducts();
                } else {
                    alert('Failed to delete product: ' + result.message);
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Error deleting product');
            }
        }

        // Event listeners
        document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; loadArchivedProducts(); });
        document.getElementById('resetFilter').addEventListener('click', () => {
            document.getElementById('searchInput').value = '';
            document.getElementById('categoryFilter').value = '';
            currentPage = 1;
            loadArchivedProducts();
        });
        document.getElementById('prevPage').addEventListener('click', () => { if (currentPage > 1) { currentPage--; loadArchivedProducts(); } });
        document.getElementById('nextPage').addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; loadArchivedProducts(); } });
        document.getElementById('perPage').addEventListener('change', (e) => { perPage = parseInt(e.target.value); currentPage = 1; loadArchivedProducts(); });

        // Load on page load
        loadArchivedProducts();
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/inventory/archived.blade.php ENDPATH**/ ?>