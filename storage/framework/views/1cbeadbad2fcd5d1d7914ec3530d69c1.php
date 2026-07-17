<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Item Disposal')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Item Disposal'))]); ?>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Item Disposal List</h1>
                <p class="text-sm text-slate-500 mt-1">Automatically identifies expired, damaged, or recalled inventory items requiring disposal.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div onclick="openItemsByStatus('all')" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm cursor-pointer hover:border-cyan-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Items Identified</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo e($stats['total']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>
            <div onclick="openItemsByStatus('Pending')" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm cursor-pointer hover:border-orange-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Pending Review</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2"><?php echo e($stats['pending']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div onclick="openItemsByStatus('Approved')" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm cursor-pointer hover:border-cyan-400 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Approved for Disposal</p>
                        <p class="text-3xl font-bold text-cyan-600 mt-2"><?php echo e($stats['approved']); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-cyan-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="p-6 border-b border-slate-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-900">Identified Disposal Items</h2>
                    <div class="flex items-center gap-3">
                        <select id="statusFilter" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:border-cyan-400">
                            <option value="all" <?php echo e(request('status') == 'all' || !request('status') ? 'selected' : ''); ?>>All Status</option>
                            <option value="Pending" <?php echo e(request('status') == 'Pending' ? 'selected' : ''); ?>>Pending Review</option>
                            <option value="Approved" <?php echo e(request('status') == 'Approved' ? 'selected' : ''); ?>>Approved</option>
                        </select>
                        <input type="text" id="searchInput" placeholder="Search items..." value="<?php echo e(request('search')); ?>" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 focus:outline-none focus:border-cyan-400 w-64" />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Product Image</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Item Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">SKU</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Category</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Total Stock</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Shop Qty</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Wh Qty</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Expiration Date</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Days Expired</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Reason</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Status</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-900">Date Identified</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php if($products->count() > 0): ?>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $shopQty = 0;
                                    $whQty = 0;
                                    if(isset($product->warehouseStocks)) {
                                        foreach($product->warehouseStocks as $stock) {
                                            if ($stock->warehouse === 'SHOP') {
                                                $shopQty += $stock->quantity;
                                            } else {
                                                $whQty += $stock->quantity;
                                            }
                                        }
                                    }
                                ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">
                                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-900"><?php echo e($product->name); ?></td>
                                    <td class="px-6 py-4 text-slate-600"><?php echo e($product->sku ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-slate-600"><?php echo e($product->category ?? '-'); ?></td>
                                    <td class="px-6 py-4 font-bold text-slate-800"><?php echo e($product->stock_quantity); ?></td>
                                    <td class="px-6 py-4 font-semibold text-blue-600"><?php echo e($shopQty); ?></td>
                                    <td class="px-6 py-4 font-semibold text-orange-600"><?php echo e($whQty); ?></td>
                                    <td class="px-6 py-4 text-slate-600"><?php echo e($product->expiry_date?->format('M d, Y') ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <?php if($product->expiry_date && $product->expiry_date->isPast()): ?>
                                            <?php echo e($product->expiry_date->diffInDays(now())); ?> days
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600"><?php echo e($product->disposal_reason ?? '-'); ?></td>
                                    <td class="px-6 py-4">
                                        <?php if($product->disposal_status === 'Pending'): ?>
                                            <span onclick="openItemModal(<?php echo e($product->id); ?>)" class="cursor-pointer inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700 hover:bg-orange-100">Pending Review</span>
                                        <?php elseif($product->disposal_status === 'Approved'): ?>
                                            <span onclick="openItemModal(<?php echo e($product->id); ?>)" class="cursor-pointer inline-flex items-center rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700 hover:bg-cyan-100">Approved</span>
                                        <?php elseif($product->disposal_status === 'Disposed'): ?>
                                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Disposed</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600"><?php echo e($product->disposal_date_identified?->format('M d, Y') ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="relative inline-block">
                                            <button onclick="toggleDropdown(<?php echo e($product->id); ?>)" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>
                                            <div id="dropdown-<?php echo e($product->id); ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10">
                                                <?php if($product->disposal_status === 'Pending'): ?>
                                                    <form method="POST" action="<?php echo e(route('item.disposal.approve', $product->id)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Approve
                                                        </button>
                                                    </form>
                                                <?php elseif($product->disposal_status === 'Approved'): ?>
                                                    <form method="POST" action="<?php echo e(route('item.disposal.dispose', $product->id)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Mark as Disposed
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View Details
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="px-6 py-16">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-semibold text-slate-900">No items currently require disposal.</p>
                                        <p class="text-sm text-slate-500 mt-1">The system will automatically identify expired or damaged inventory.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($products->hasPages()): ?>
                <div class="px- py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-600">Showing <?php echo e($products->firstItem()); ?> to <?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?> results</p>
                    <?php echo e($products->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div id="itemModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-[9999] flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 md:mx-0 sm:max-w-lg md:max-w-2xl lg:max-w-3xl overflow-hidden transform transition-all max-h-[90vh] relative z-[10000]">
            <div class="px-6 py-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 relative">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-1">Item Details</h2>
                        <p class="text-xs text-slate-300 mt-1">View complete item information</p>
                    </div>
                    <button onclick="document.getElementById('itemModal').classList.add('hidden')" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div id="itemModalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        const productsData = <?php echo json_encode($products->items(), 15, 512) ?>;

        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            
            allDropdowns.forEach(d => {
                if (d.id !== dropdown.id) {
                    d.classList.add('hidden');
                }
            });
            
            dropdown.classList.toggle('hidden');
        }

        function openItemsByStatus(status) {
            const modal = document.getElementById('itemModal');
            const content = document.getElementById('itemModalContent');
            
            let filteredProducts = productsData;
            if (status !== 'all') {
                filteredProducts = productsData.filter(p => p.disposal_status === status);
            }
            
            if (filteredProducts.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-slate-500">No items found for this status.</p>
                    </div>
                `;
            } else {
                let itemsHtml = filteredProducts.map(product => `
                    <div class="border border-slate-200 rounded-xl p-4 mb-3 hover:bg-slate-50 cursor-pointer" onclick="openItemModal(${product.id})">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-slate-900">${product.name || '-'}</p>
                                <p class="text-sm text-slate-600">${product.sku || '-'}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ${
                                product.disposal_status === 'Pending' ? 'bg-orange-50 text-orange-700' : 
                                product.disposal_status === 'Approved' ? 'bg-cyan-50 text-cyan-700' : 
                                'bg-slate-100 text-slate-600'
                            }">${product.disposal_status || 'None'}</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-500">
                            Stock: ${product.stock_quantity || 0} • Exp: ${product.expiry_date || 'N/A'}
                        </div>
                    </div>
                `).join('');
                
                content.innerHTML = `
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-slate-900">${status === 'all' ? 'All Items' : status + ' Items'} (${filteredProducts.length})</h3>
                        <p class="text-sm text-slate-500">Click on an item to view details</p>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto">
                        ${itemsHtml}
                    </div>
                `;
            }
            
            modal.classList.remove('hidden');
        }

        function openItemModal(productId) {
            const product = productsData.find(p => p.id === productId);
            if (!product) return;

            const modal = document.getElementById('itemModal');
            const content = document.getElementById('itemModalContent');
            
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Product Name</label>
                            <p class="text-sm font-medium text-slate-900 mt-1">${product.name || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">SKU</label>
                            <p class="text-sm text-slate-600 mt-1">${product.sku || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</label>
                            <p class="text-sm text-slate-600 mt-1">${product.category || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Brand</label>
                            <p class="text-sm text-slate-600 mt-1">${product.brand || '-'}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Stock</label>
                            <p class="text-sm font-medium text-slate-900 mt-1">${product.stock_quantity || 0}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Price</label>
                            <p class="text-sm text-slate-600 mt-1">₱${parseFloat(product.unit_price || 0).toFixed(2)}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Expiration Date</label>
                            <p class="text-sm text-slate-600 mt-1">${product.expiry_date || 'Non-expiring'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Days Expired</label>
                            <p class="text-sm text-slate-600 mt-1">${product.expiry_date ? product.days_expired + ' days' : '-'}</p>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Disposal Reason</label>
                            <p class="text-sm text-slate-600 mt-1">${product.disposal_reason || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Disposal Status</label>
                            <p class="text-sm font-medium text-slate-900 mt-1">${product.disposal_status || 'None'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Date Identified</label>
                            <p class="text-sm text-slate-600 mt-1">${product.disposal_date_identified || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Supplier</label>
                            <p class="text-sm text-slate-600 mt-1">${product.supplier_name || '-'}</p>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleDropdown"]') && !e.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });
            }
        });

        // Status filter
        document.getElementById('statusFilter').addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('status', this.value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        });

        // Search input
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const url = new URL(window.location);
                url.searchParams.set('search', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            }
        });
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/inventory/item-disposal.blade.php ENDPATH**/ ?>