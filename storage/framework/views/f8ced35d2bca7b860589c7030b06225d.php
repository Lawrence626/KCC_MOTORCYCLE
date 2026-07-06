<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Received Orders')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Received Orders'))]); ?>
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Received Orders</h1>
                <p class="max-w-2xl text-sm text-slate-500">Track completed deliveries, confirm order receipts, and view inventory impact.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Confirm Receipt</button>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Delivered today</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($deliveredToday)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Orders received and logged today.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Pending confirmation</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($pendingConfirmation)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Awaiting goods inspection or paperwork.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Issues found</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($issuesFound)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Discrepancies requiring follow-up.</p>
            </div>
        </div>

        <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Latest received orders</h2>
                    <p class="text-sm text-slate-500">Recent receipts in a concise table.</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Verified</span>
            </div>

            <form method="GET" action="<?php echo e(route('received.orders')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                    <input name="search" type="search" value="<?php echo e($search ?? ''); ?>" placeholder="Order ID or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                    <select name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="" <?php echo e(empty($status) ? 'selected' : ''); ?>>All statuses</option>
                        <option value="completed" <?php echo e($status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="partially received" <?php echo e($status === 'partially received' ? 'selected' : ''); ?>>Partially Received</option>
                    </select>
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Warehouse</span>
                    <select name="warehouse" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                        <option value="" <?php echo e(empty($warehouse) ? 'selected' : ''); ?>>All warehouses</option>
                        <option value="main" <?php echo e($warehouse === 'main' ? 'selected' : ''); ?>>Main stock</option>
                        <option value="service" <?php echo e($warehouse === 'service' ? 'selected' : ''); ?>>Service bay</option>
                    </select>
                </label>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex w-full justify-center rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Filter</button>
                </div>
            </form>

            <div class="mt-6 overflow-hidden rounded-[26px] border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Received</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold"><?php echo e($order->order_number); ?></td>
                                <td class="px-4 py-3"><?php echo e($order->supplier_name); ?></td>
                                <td class="px-4 py-3"><?php echo e(optional($order->updated_at)->format('M j, Y')); ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                        $statusClass = match($order->status) {
                                            'pending approval' => 'bg-amber-100 text-amber-800',
                                            'approved' => 'bg-sky-100 text-sky-800',
                                            'sent to supplier' => 'bg-blue-100 text-blue-800',
                                            'in transit' => 'bg-sky-100 text-sky-800',
                                            'partially received' => 'bg-amber-100 text-amber-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'rejected' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    ?>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?php echo e($statusClass); ?>">
                                        <?php echo e(ucwords($order->status)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No received orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4">
                <?php echo e($orders->links()); ?>

            </div>
        </section>
    </div>
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
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/purchase_order/received-orders.blade.php ENDPATH**/ ?>