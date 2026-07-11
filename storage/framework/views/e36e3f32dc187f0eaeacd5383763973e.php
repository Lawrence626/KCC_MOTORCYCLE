<?php
    $dateLabel = $dateLabel ?? 'ETA';
    $dateType = $dateType ?? 'eta';
    $emptyMessage = $emptyMessage ?? 'No purchase orders found.';
    $showAction = $showAction ?? true;
?>

<div class="mt-6 overflow-hidden rounded-[10px] border border-slate-200">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
            <tr>
                <th class="px-4 py-3">Order</th>
                <th class="px-4 py-3">Supplier</th>
                <th class="px-4 py-3"><?php echo e($dateLabel); ?></th>
                <th class="px-4 py-3">Status</th>
                <?php if($showAction): ?>
                    <th class="px-4 py-3">Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $statusClass = match($order->status) {
                        'pending approval' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-sky-100 text-sky-800',
                        'sent to supplier' => 'bg-blue-100 text-blue-800',
                        'in transit' => 'bg-sky-100 text-sky-800',
                        'partially received' => 'bg-amber-100 text-amber-800',
                        'completed' => 'bg-emerald-100 text-emerald-800',
                        'archived' => 'bg-slate-100 text-slate-700',
                        'rejected', 'cancelled' => 'bg-rose-100 text-rose-800',
                        default => 'bg-slate-100 text-slate-700',
                    };

                    $dateValue = match($dateType) {
                        'received' => optional($order->completed_at)->format('M j') ?? optional($order->updated_at)->format('M j'),
                        'created' => optional($order->created_at)->format('M j'),
                        default => optional($order->expected_delivery_date)->format('M j') ?? 'TBD',
                    };
                ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold"><?php echo e($order->order_number); ?></td>
                    <td class="px-4 py-3"><?php echo e($order->supplier_name); ?></td>
                    <td class="px-4 py-3"><?php echo e($dateValue); ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?php echo e($statusClass); ?>">
                            <?php echo e(ucwords($order->status)); ?>

                        </span>
                    </td>
                    <?php if($showAction): ?>
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('order.show', $order)); ?>" class="inline-flex rounded-[10px] bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">View Details</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e($showAction ? 5 : 4); ?>" class="px-4 py-6 text-center text-sm text-slate-500"><?php echo e($emptyMessage); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/purchase_order/partials/orders-table.blade.php ENDPATH**/ ?>