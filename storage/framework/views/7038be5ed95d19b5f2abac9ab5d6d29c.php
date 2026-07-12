<div class="mt-6 overflow-hidden rounded-[10px] border border-slate-200">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
            <tr>
                <th class="px-4 py-3">PO No.</th>
                <th class="px-4 py-3">Supplier</th>
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Ordered</th>
                <th class="px-4 py-3">Received</th>
                <th class="px-4 py-3">Remaining</th>
                <th class="px-4 py-3">Order Date</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-slate-700">
            <?php $__empty_1 = true; $__currentLoopData = $backOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $purchaseOrder = $item->purchaseOrder;
                    $receivedQuantity = $item->received_quantity ?? 0;
                    $remainingQuantity = max(0, $item->quantity - $receivedQuantity);
                ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-semibold"><?php echo e($purchaseOrder->order_number); ?></td>
                    <td class="px-4 py-3"><?php echo e($purchaseOrder->supplier_name); ?></td>
                    <td class="px-4 py-3"><?php echo e($item->product_name); ?></td>
                    <td class="px-4 py-3"><?php echo e($item->quantity); ?></td>
                    <td class="px-4 py-3"><?php echo e($receivedQuantity); ?></td>
                    <td class="px-4 py-3"><?php echo e($remainingQuantity); ?></td>
                    <td class="px-4 py-3"><?php echo e($purchaseOrder->created_at->format('M j')); ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">Waiting for Supplier</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="<?php echo e(route('order.show', $purchaseOrder)); ?>" class="inline-flex rounded-[10px] bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-200">View Details</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-sm text-slate-500">No back ordered items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/purchase_order/partials/back-orders-table.blade.php ENDPATH**/ ?>