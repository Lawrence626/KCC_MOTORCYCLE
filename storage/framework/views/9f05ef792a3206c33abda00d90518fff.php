<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Offline Purchase Orders')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Offline Purchase Orders'))]); ?>
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Offline Purchase Orders</h1>
                <p class="text-xs text-slate-500 mt-0.5">Generate orders locally during internet outages</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="offline-indicator" class="hidden"></div>
                <button onclick="toggleArchiveList()" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    Archive (<span id="archive-count">0</span>)
                </button>
            </div>
        </div>

        <!-- Archive List Modal -->
        <div id="archiveModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Archived Orders</h3>
                    <button onclick="toggleArchiveList()" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto max-h-[60vh]">
                    <div id="archiveList">
                        <p class="text-sm text-slate-500 text-center py-4">No archived orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Offline Status Banner -->
        <div id="offline-banner" class="bg-amber-50 border border-amber-200 rounded-lg p-4 hidden">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-900">You are currently offline</p>
                    <p class="text-xs text-amber-700">Orders will be saved locally. Export to CSV when ready to sync.</p>
                </div>
            </div>
        </div>

        <!-- Order Form -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Create Purchase Order (Offline)</h2>
            <form id="localOrderForm">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Order Number</label>
                        <input type="text" name="order_number" id="order_number" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" readonly>
                        <p class="text-xs text-slate-500 mt-1">Auto-generated</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                            <option value="">Select Supplier</option>
                            <?php $__currentLoopData = $suppliers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" id="expected_delivery_date" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount" step="0.01" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-medium text-slate-700 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"></textarea>
                </div>
                <div class="mt-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Save Order Locally
                    </button>
                    <button type="button" onclick="exportLocalOrders()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export to CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Local Orders</p>
                <p class="text-2xl font-bold text-amber-600" id="local-count">0</p>
                <p class="text-xs text-slate-500 mt-0.5">Saved locally</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Pending Sync</p>
                <p class="text-2xl font-bold text-amber-600"><?php echo e($purchaseOrders->where('sync_status', 'pending_sync')->count()); ?></p>
                <p class="text-xs text-slate-500 mt-0.5">Database orders</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Exported</p>
                <p class="text-2xl font-bold text-blue-600"><?php echo e($purchaseOrders->where('sync_status', 'exported')->count()); ?></p>
                <p class="text-xs text-slate-500 mt-0.5">Ready for import</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Synchronized</p>
                <p class="text-2xl font-bold text-green-600"><?php echo e($purchaseOrders->where('sync_status', 'synchronized')->count()); ?></p>
                <p class="text-xs text-slate-500 mt-0.5">Successfully synced</p>
            </div>
        </div>

        <!-- Local Orders Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Local Orders (Saved in Browser)</h2>
                <span class="text-xs text-slate-500" id="pending-count">0 orders</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Order Number</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Supplier</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Date</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200" id="localOrdersTable">
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500">No local orders</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Local Orders -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600" id="localOrdersPaginationInfo">Showing 0 of 0 items</p>
                <div class="flex gap-1" id="localOrdersPagination">
                    <button onclick="changeLocalOrdersPage(-1)" class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50" id="prevPageBtn">Previous</button>
                    <button onclick="changeLocalOrdersPage(1)" class="px-2 py-1 rounded border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50" id="nextPageBtn">Next</button>
                </div>
            </div>
        </div>

        <!-- Database Orders Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Database Orders (Synced)</h2>
                <div class="flex gap-2">
                    <select class="px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500">
                        <option value="">All Sync Status</option>
                        <option value="pending_sync">Pending Sync</option>
                        <option value="exported">Exported</option>
                        <option value="imported">Imported</option>
                        <option value="synchronized">Synchronized</option>
                        <option value="duplicate">Duplicate</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Order Number</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Supplier</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Sync Status</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total Amount</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Expected Delivery</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__empty_1 = true; $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium"><?php echo e($po->order_number); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e($po->supplier_name); ?></td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php if($po->status == 'pending'): ?> bg-amber-100 text-amber-700 <?php elseif($po->status == 'approved'): ?> bg-blue-100 text-blue-700 <?php elseif($po->status == 'received'): ?> bg-green-100 text-green-700 <?php else: ?> bg-slate-100 text-slate-700 <?php endif; ?>">
                                    <?php echo e(ucfirst($po->status)); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php if($po->sync_status == 'pending_sync'): ?> bg-amber-100 text-amber-700 <?php elseif($po->sync_status == 'exported'): ?> bg-blue-100 text-blue-700 <?php elseif($po->sync_status == 'synchronized'): ?> bg-green-100 text-green-700 <?php elseif($po->sync_status == 'duplicate'): ?> bg-red-100 text-red-700 <?php elseif($po->sync_status == 'failed'): ?> bg-red-100 text-red-700 <?php else: ?> bg-slate-100 text-slate-700 <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', ucfirst($po->sync_status))); ?>

                                </span>
                            </td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium">₱<?php echo e(number_format($po->total_amount, 2)); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e($po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : '-'); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e($po->created_at->format('M d, Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No database orders found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600">Showing <?php echo e($purchaseOrders->firstItem()); ?> to <?php echo e($purchaseOrders->lastItem()); ?> of <?php echo e($purchaseOrders->total()); ?> items</p>
                <?php echo e($purchaseOrders->links()); ?>

            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('js/offline-manager.js')); ?>"></script>
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for offline manager to initialize
            setTimeout(() => {
                if (offlineManager && offlineManager.db) {
                    updateOfflineBanner();
                    loadLocalOrders();
                    generateOrderNumber();
                } else {
                    console.error('Offline manager not initialized');
                }
            }, 500);
            
            // Form submission
            document.getElementById('localOrderForm').addEventListener('submit', handleOrderSubmit);
        });

        function generateOrderNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
            const orderNumber = `PO-${year}${month}${day}-${random}`;
            document.getElementById('order_number').value = orderNumber;
        }

        function updateOfflineBanner() {
            const banner = document.getElementById('offline-banner');
            if (offlineManager && offlineManager.isOffline()) {
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }
        }

        async function handleOrderSubmit(e) {
            e.preventDefault();
            
            // Prevent multiple submissions
            const submitButton = e.target.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
            
            const orderData = {
                order_number: document.getElementById('order_number').value,
                supplier_id: document.getElementById('supplier_id').value,
                supplier_name: document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].text,
                expected_delivery_date: document.getElementById('expected_delivery_date').value,
                total_amount: parseFloat(document.getElementById('total_amount').value),
                notes: document.getElementById('notes').value,
                status: 'pending',
                sync_status: 'pending_sync'
            };

            try {
                // Always save to IndexedDB
                await offlineManager.savePendingOrder(orderData);
                alert('Order saved locally. Export to CSV when ready to sync.');
                
                // Reset form and reload local orders
                document.getElementById('localOrderForm').reset();
                generateOrderNumber(); // Generate new order number
                loadLocalOrders();
                
            } catch (error) {
                console.error('Error saving order:', error);
                if (error.message.includes('already exists')) {
                    alert('Order with this number already exists locally.');
                } else {
                    alert('Failed to save order. Please try again.');
                }
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = `
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Save Order Locally
                `;
            }
        }

        let localOrdersCurrentPage = 1;
        const localOrdersPerPage = 5;
        let allLocalOrders = [];

        async function loadLocalOrders() {
            if (!offlineManager || !offlineManager.db) {
                console.error('Offline manager or database not ready');
                return;
            }
            
            try {
                const orders = await offlineManager.getAllFromStore('pending_orders');
                allLocalOrders = orders;
                const tbody = document.getElementById('localOrdersTable');
                const countElement = document.getElementById('pending-count');
                const localCountElement = document.getElementById('local-count');
                
                const count = orders.length || 0;
                countElement.textContent = `${count} orders`;
                localCountElement.textContent = count;
                
                if (count === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-8 text-center text-slate-500">No local orders</td></tr>';
                    updateLocalOrdersPagination();
                    return;
                }

                renderLocalOrdersPage();
            } catch (error) {
                console.error('Error loading local orders:', error);
            }
        }

        function renderLocalOrdersPage() {
            const tbody = document.getElementById('localOrdersTable');
            const startIndex = (localOrdersCurrentPage - 1) * localOrdersPerPage;
            const endIndex = startIndex + localOrdersPerPage;
            const pageOrders = allLocalOrders.slice(startIndex, endIndex);
            
            if (pageOrders.length === 0 && allLocalOrders.length > 0) {
                localOrdersCurrentPage = 1;
                renderLocalOrdersPage();
                return;
            }

            tbody.innerHTML = pageOrders.map(order => `
                <tr>
                    <td class="px-3 py-2 text-slate-900 font-medium">${order.order_number}</td>
                    <td class="px-3 py-2 text-slate-600">${order.supplier_name}</td>
                    <td class="px-3 py-2 text-right text-slate-900 font-medium">₱${order.total_amount.toFixed(2)}</td>
                    <td class="px-3 py-2 text-slate-600">${new Date(order.timestamp).toLocaleDateString()}</td>
                    <td class="px-3 py-2">
                        <button onclick="archiveOrder(${order.id})" class="text-amber-600 hover:text-amber-700 font-medium">Archive</button>
                    </td>
                </tr>
            `).join('');
            
            updateLocalOrdersPagination();
        }

        function updateLocalOrdersPagination() {
            const totalPages = Math.ceil(allLocalOrders.length / localOrdersPerPage);
            const startItem = allLocalOrders.length === 0 ? 0 : (localOrdersCurrentPage - 1) * localOrdersPerPage + 1;
            const endItem = Math.min(localOrdersCurrentPage * localOrdersPerPage, allLocalOrders.length);
            
            document.getElementById('localOrdersPaginationInfo').textContent = 
                `Showing ${startItem}-${endItem} of ${allLocalOrders.length} items`;
            
            document.getElementById('prevPageBtn').disabled = localOrdersCurrentPage <= 1;
            document.getElementById('nextPageBtn').disabled = localOrdersCurrentPage >= totalPages;
        }

        function changeLocalOrdersPage(delta) {
            const totalPages = Math.ceil(allLocalOrders.length / localOrdersPerPage);
            const newPage = localOrdersCurrentPage + delta;
            
            if (newPage >= 1 && newPage <= totalPages) {
                localOrdersCurrentPage = newPage;
                renderLocalOrdersPage();
            }
        }

        async function deleteOrder(id) {
            if (!confirm('Are you sure you want to delete this order?')) return;
            
            const transaction = offlineManager.db.transaction(['pending_orders'], 'readwrite');
            const store = transaction.objectStore('pending_orders');
            store.delete(id);
            
            transaction.oncomplete = () => {
                loadLocalOrders();
            };
        }

        async function archiveOrder(id) {
            if (!confirm('Are you sure you want to archive this order?')) return;
            
            try {
                // Get the order first
                const order = await offlineManager.getOrderById('pending_orders', id);
                
                // Add to archived_orders store
                const transaction = offlineManager.db.transaction(['archived_orders'], 'readwrite');
                const archiveStore = transaction.objectStore('archived_orders');
                await new Promise((resolve, reject) => {
                    const request = archiveStore.add({
                        ...order,
                        archived_at: new Date().toISOString()
                    });
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => reject(request.error);
                });
                
                // Remove from pending_orders
                const pendingStore = transaction.objectStore('pending_orders');
                await new Promise((resolve, reject) => {
                    const request = pendingStore.delete(id);
                    request.onsuccess = () => resolve();
                    request.onerror = () => reject(request.error);
                });
                
                alert('Order archived successfully.');
                loadLocalOrders();
                loadArchiveList();
            } catch (error) {
                console.error('Error archiving order:', error);
                alert('Failed to archive order.');
            }
        }

        function toggleArchiveList() {
            const modal = document.getElementById('archiveModal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                loadArchiveList();
            }
        }

        async function loadArchiveList() {
            if (!offlineManager || !offlineManager.db) return;
            
            try {
                const archivedOrders = await offlineManager.getAllFromStore('archived_orders');
                const archiveList = document.getElementById('archiveList');
                const archiveCount = document.getElementById('archive-count');
                
                archiveCount.textContent = archivedOrders.length;
                
                if (archivedOrders.length === 0) {
                    archiveList.innerHTML = '<p class="text-sm text-slate-500 text-center py-4">No archived orders</p>';
                    return;
                }

                archiveList.innerHTML = `
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Order Number</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Supplier</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700">Total</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Archived</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            ${archivedOrders.map(order => `
                                <tr>
                                    <td class="px-3 py-2 text-slate-900 font-medium">${order.order_number}</td>
                                    <td class="px-3 py-2 text-slate-600">${order.supplier_name}</td>
                                    <td class="px-3 py-2 text-right text-slate-900 font-medium">₱${order.total_amount.toFixed(2)}</td>
                                    <td class="px-3 py-2 text-slate-600">${new Date(order.archived_at).toLocaleDateString()}</td>
                                    <td class="px-3 py-2">
                                        <button onclick="restoreOrder(${order.id})" class="text-cyan-600 hover:text-cyan-700 font-medium">Restore</button>
                                        <button onclick="deleteArchivedOrder(${order.id})" class="text-red-600 hover:text-red-700 font-medium ml-2">Delete</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } catch (error) {
                console.error('Error loading archive list:', error);
            }
        }

        async function restoreOrder(id) {
            if (!confirm('Are you sure you want to restore this order?')) return;
            
            try {
                // Get the archived order
                const order = await offlineManager.getOrderById('archived_orders', id);
                
                // Remove archived_at field
                const { archived_at, ...orderData } = order;
                
                // Add back to pending_orders
                const transaction = offlineManager.db.transaction(['pending_orders', 'archived_orders'], 'readwrite');
                const pendingStore = transaction.objectStore('pending_orders');
                await new Promise((resolve, reject) => {
                    const request = pendingStore.add(orderData);
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => reject(request.error);
                });
                
                // Remove from archived_orders
                const archiveStore = transaction.objectStore('archived_orders');
                await new Promise((resolve, reject) => {
                    const request = archiveStore.delete(id);
                    request.onsuccess = () => resolve();
                    request.onerror = () => reject(request.error);
                });
                
                alert('Order restored successfully.');
                loadLocalOrders();
                loadArchiveList();
            } catch (error) {
                console.error('Error restoring order:', error);
                alert('Failed to restore order.');
            }
        }

        async function deleteArchivedOrder(id) {
            if (!confirm('Are you sure you want to permanently delete this order?')) return;
            
            const transaction = offlineManager.db.transaction(['archived_orders'], 'readwrite');
            const store = transaction.objectStore('archived_orders');
            store.delete(id);
            
            transaction.oncomplete = () => {
                loadArchiveList();
            };
        }

        async function exportLocalOrders() {
            const orders = offlineManager.getPendingOrders();
            
            if (orders.length === 0) {
                alert('No local orders to export.');
                return;
            }

            // Convert to CSV
            const headers = ['type', 'order_number', 'supplier_id', 'supplier_name', 'status', 'expected_delivery_date', 'notes', 'total_amount', 'created_at'];
            const csvContent = [
                headers.join(','),
                ...orders.map(order => [
                    'purchase_order',
                    order.order_number,
                    order.supplier_id,
                    order.supplier_name,
                    order.status,
                    order.expected_delivery_date,
                    order.notes,
                    order.total_amount,
                    order.timestamp
                ].join(','))
            ].join('\n');

            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `offline_orders_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
            
            alert('CSV exported successfully. Upload to Import Data page for admin approval.');
        }

        // Listen for offline/online changes
        window.addEventListener('online', updateOfflineBanner);
        window.addEventListener('offline', updateOfflineBanner);
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
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/offline-reconciliation/purchase-orders.blade.php ENDPATH**/ ?>