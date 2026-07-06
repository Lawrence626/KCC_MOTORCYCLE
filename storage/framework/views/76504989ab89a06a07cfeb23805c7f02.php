<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Order Management')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Order Management'))]); ?>
    <div class="space-y-5">
        <?php if(session('success')): ?>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Order Management</h1>
                <p class="max-w-2xl text-sm text-slate-500">Monitor and visualize purchase orders across the ordering lifecycle.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">Upload CSV</button>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total orders</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($totalOrders)); ?></p>
                <p class="mt-2 text-sm text-slate-500">All purchase orders created so far.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">In transit value</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">&#8369;<?php echo e(number_format($inTransitTotal, 2)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Total value of orders currently in transit.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Received <?php echo e($receivedLabel); ?></p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($receivedCount)); ?></p>
                        <p class="mt-2 text-sm text-slate-500">Completed orders added to inventory.</p>
                    </div>
                    <form method="GET" action="<?php echo e(route('order.management')); ?>" class="w-full sm:w-auto">
                        <input type="hidden" name="tab" value="<?php echo e($activeTab); ?>">
                        <label class="sr-only">Received range</label>
                        <select name="received_range" onchange="this.form.submit()" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option value="daily" <?php echo e($receivedRange === 'daily' ? 'selected' : ''); ?>>Daily</option>
                            <option value="weekly" <?php echo e($receivedRange === 'weekly' ? 'selected' : ''); ?>>Weekly</option>
                            <option value="monthly" <?php echo e($receivedRange === 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                            <option value="yearly" <?php echo e($receivedRange === 'yearly' ? 'selected' : ''); ?>>Yearly</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div id="orderTabs" class="flex flex-wrap items-center gap-3 border-b border-slate-200 pb-4">
                <button class="tab-btn rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="orders">Purchase Orders</button>
                <button class="tab-btn rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="back_orders">Back Orders</button>
                <button class="tab-btn rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="received">Received Orders</button>
                <button class="tab-btn rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="cancelled">Cancelled Orders</button>
            </div>

            <div id="orders-tab" class="tab-content">
                <form method="GET" action="<?php echo e(route('order.management')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search orders</span>
                        <input name="orders_search" value="<?php echo e(request('orders_search')); ?>" type="search" placeholder="Receipt no. or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <select name="orders_status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <?php $__currentLoopData = ['pending approval', 'approved', 'sent to supplier', 'in transit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php echo e(request('orders_status') === $status ? 'selected' : ''); ?>><?php echo e(ucwords($status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="orders_supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->name); ?>" <?php echo e(request('orders_supplier') === $supplier->name ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                </form>
                <div class="mt-5 flex justify-end">
                    <a href="<?php echo e(route('order.create')); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Create Purchase Order</a>
                </div>
                <?php echo $__env->make('purchase_order.partials.orders-table', ['orders' => $orders, 'emptyMessage' => 'No active purchase orders have been created yet.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="mt-4 px-4"><?php echo e($orders->links()); ?></div>
            </div>

            <div id="back_orders-tab" class="tab-content hidden">
                <form method="GET" action="<?php echo e(route('order.management')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="back_orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search back orders</span>
                        <input name="back_orders_search" value="<?php echo e(request('back_orders_search')); ?>" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <select name="back_orders_status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="waiting for supplier" <?php echo e(request('back_orders_status') === 'waiting for supplier' ? 'selected' : ''); ?>>Waiting for Supplier</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="back_orders_supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->name); ?>" <?php echo e(request('back_orders_supplier') === $supplier->name ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                </form>
                <?php echo $__env->make('purchase_order.partials.back-orders-table', ['backOrders' => $backOrders], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="mt-4 px-4"><?php echo e($backOrders->links()); ?></div>
            </div>

            <div id="received-tab" class="tab-content hidden">
                <form method="GET" action="<?php echo e(route('order.management')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="received">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                        <input name="received_search" value="<?php echo e(request('received_search')); ?>" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                        <select name="received_status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="completed" <?php echo e(request('received_status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="partially received" <?php echo e(request('received_status') === 'partially received' ? 'selected' : ''); ?>>Partially Received</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="received_supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->name); ?>" <?php echo e(request('received_supplier') === $supplier->name ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                </form>
                <?php echo $__env->make('purchase_order.partials.orders-table', ['orders' => $receivedOrders, 'dateLabel' => 'Received', 'dateType' => 'received', 'emptyMessage' => 'No received purchase orders found.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="mt-4 px-4"><?php echo e($receivedOrders->links()); ?></div>
            </div>

            <div id="cancelled-tab" class="tab-content hidden">
                <form method="GET" action="<?php echo e(route('order.management')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="cancelled">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search cancelled orders</span>
                        <input name="cancelled_search" value="<?php echo e(request('cancelled_search')); ?>" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <select name="cancelled_status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="rejected" <?php echo e(request('cancelled_status') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                            <option value="cancelled" <?php echo e(request('cancelled_status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="cancelled_supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->name); ?>" <?php echo e(request('cancelled_supplier') === $supplier->name ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                </form>
                <?php echo $__env->make('purchase_order.partials.orders-table', ['orders' => $cancelledOrders, 'dateLabel' => 'Created', 'dateType' => 'created', 'emptyMessage' => 'No cancelled purchase orders found.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="mt-4 px-4"><?php echo e($cancelledOrders->links()); ?></div>
            </div>
        </div>
    </div>

    <script>
        const activeTab = <?php echo json_encode($activeTab, 15, 512) ?>;

        function showOrderTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active', 'bg-emerald-100', 'text-emerald-700'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

            const button = document.querySelector(`.tab-btn[data-tab="${tabName}"]`) || document.querySelector('.tab-btn[data-tab="orders"]');
            const content = document.getElementById((button.dataset.tab || 'orders') + '-tab');

            button.classList.add('active', 'bg-emerald-100', 'text-emerald-700');
            content.classList.remove('hidden');
        }

        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => showOrderTab(button.dataset.tab));
        });

        showOrderTab(activeTab);
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
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/purchase_order/order-management.blade.php ENDPATH**/ ?>