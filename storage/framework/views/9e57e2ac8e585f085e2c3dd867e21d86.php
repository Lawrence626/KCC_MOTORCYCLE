<div style="font-family:Arial,Helvetica,sans-serif;color:#111;line-height:1.5;">
    <h1 style="font-size:20px;margin-bottom:16px;">Purchase Order <?php echo e($purchaseOrder->order_number); ?></h1>
    <p style="margin-bottom:8px;">Hello <?php echo e($purchaseOrder->supplier_name); ?>,</p>
    <p style="margin-bottom:16px;">Please find the purchase order details below for your attention.</p>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;margin-bottom:16px;">
        <tr style="background:#f4f4f4;font-weight:bold;">
            <td>Order number</td>
            <td><?php echo e($purchaseOrder->order_number); ?></td>
        </tr>
        <tr>
            <td>Expected delivery date</td>
            <td><?php echo e(optional($purchaseOrder->expected_delivery_date)->format('M j, Y') ?? 'TBD'); ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td><?php echo e(ucfirst($purchaseOrder->status)); ?></td>
        </tr>
        <tr>
            <td>Total amount</td>
            <td>₱<?php echo e(number_format($purchaseOrder->total_amount, 2)); ?></td>
        </tr>
    </table>

    <h2 style="font-size:16px;margin-bottom:12px;">Order items</h2>
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;margin-bottom:24px;">
        <thead style="background:#f4f4f4;font-weight:bold;">
            <tr>
                <td>Product</td>
                <td>SKU</td>
                <td>Quantity</td>
                <td>Unit price</td>
                <td>Total</td>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $purchaseOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->product_name); ?></td>
                    <td><?php echo e($item->sku); ?></td>
                    <td><?php echo e($item->quantity); ?></td>
                    <td>₱<?php echo e(number_format($item->unit_price, 2)); ?></td>
                    <td>₱<?php echo e(number_format($item->total_price, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php if($purchaseOrder->notes): ?>
        <p style="margin-bottom:16px;"><strong>Notes:</strong> <?php echo e($purchaseOrder->notes); ?></p>
    <?php endif; ?>

    <p style="margin-bottom:0;">Thank you,<br>The Purchase Order Team</p>
</div>
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/emails/purchase_orders/sent.blade.php ENDPATH**/ ?>