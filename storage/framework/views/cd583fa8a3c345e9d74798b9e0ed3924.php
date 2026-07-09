<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Purchase Order')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Purchase Order'))]); ?>
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Purchase Order <?php echo e($purchaseOrder->order_number); ?></h1>
                <p class="max-w-2xl text-sm text-slate-500">Review full purchase order details and manage the lifecycle.</p>
            </div>
            <a href="<?php echo e(route('order.management')); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900">Back to Orders</a>
        </div>

        <?php if(session('success')): ?>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('warning')): ?>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700"><?php echo e(session('warning')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <strong class="block font-semibold">Please fix the following:</strong>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Supplier</p>
                <p class="mt-3 text-xl font-semibold text-slate-900"><?php echo e($purchaseOrder->supplier_name); ?></p>
                <p class="mt-2 text-sm text-slate-500"><?php echo e(optional($purchaseOrder->supplier)->email ?? 'No supplier email on file'); ?></p>
                <p class="text-sm text-slate-500"><?php echo e(optional($purchaseOrder->supplier)->phone ?? 'No phone available'); ?></p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Order details</p>
                <div class="mt-3 space-y-2 text-sm text-slate-700">
                    <p><span class="font-semibold">Status:</span> <?php echo e(ucwords($purchaseOrder->status)); ?></p>
                    <p><span class="font-semibold">Created:</span> <?php echo e($purchaseOrder->created_at->format('M j, Y')); ?></p>
                    <p><span class="font-semibold">ETA:</span> <?php echo e(optional($purchaseOrder->expected_delivery_date)->format('M j, Y') ?? 'TBD'); ?></p>
                    <p><span class="font-semibold">Order total:</span> ₱<?php echo e(number_format($purchaseOrder->total_amount, 2)); ?></p>
                </div>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Timeline</p>
                <div class="mt-3 space-y-2 text-sm text-slate-700">
                    <?php if($purchaseOrder->approved_at): ?>
                        <p><span class="font-semibold">Approved:</span> <?php echo e($purchaseOrder->approved_at->format('M j, Y H:i')); ?></p>
                    <?php endif; ?>
                    <?php if($purchaseOrder->sent_to_supplier_at): ?>
                        <p><span class="font-semibold">Sent:</span> <?php echo e($purchaseOrder->sent_to_supplier_at->format('M j, Y H:i')); ?></p>
                    <?php endif; ?>
                    <?php if($purchaseOrder->in_transit_at): ?>
                        <p><span class="font-semibold">In transit:</span> <?php echo e($purchaseOrder->in_transit_at->format('M j, Y H:i')); ?></p>
                    <?php endif; ?>
                    <?php if($purchaseOrder->completed_at): ?>
                        <p><span class="font-semibold">Completed:</span> <?php echo e($purchaseOrder->completed_at->format('M j, Y H:i')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Items</h2>
                    <p class="text-sm text-slate-500">Verify quantities and received inventory.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if($purchaseOrder->status === 'pending approval'): ?>
                        <form method="POST" action="<?php echo e(route('order.approve', $purchaseOrder)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Approve</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('order.reject', $purchaseOrder)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-2xl border border-rose-200 bg-white px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-50">Reject</button>
                        </form>
                    <?php elseif($purchaseOrder->status === 'approved'): ?>
                        <form method="POST" action="<?php echo e(route('order.send', $purchaseOrder)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-2xl bg-cyan-600 px-4 py-3 text-sm font-semibold text-white hover:bg-cyan-700">Send to Supplier</button>
                        </form>
                    <?php elseif($purchaseOrder->status === 'sent to supplier'): ?>
                        <form method="POST" action="<?php echo e(route('order.in_transit', $purchaseOrder)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-2xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-700">Mark In Transit</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Qty ordered</th>
                            <th class="px-4 py-3">Received</th>
                            <th class="px-4 py-3">Unit price</th>
                            <th class="px-4 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        <?php $__currentLoopData = $purchaseOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-white">
                                <td class="px-4 py-3"><?php echo e($item->product_name); ?></td>
                                <td class="px-4 py-3"><?php echo e($item->sku); ?></td>
                                <td class="px-4 py-3"><?php echo e($item->quantity); ?></td>
                                <td class="px-4 py-3"><?php echo e($item->received_quantity ?? 0); ?></td>
                                <td class="px-4 py-3">₱<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                <td class="px-4 py-3">₱<?php echo e(number_format($item->total_price, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php if(in_array($purchaseOrder->status, ['in transit', 'partially received'], true)): ?>
                <form method="POST" action="<?php echo e(route('order.receive', $purchaseOrder)); ?>" class="mt-6 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-semibold text-slate-700">Receive Order</h3>
                        <p class="mt-1 text-sm text-slate-500">Confirm received quantities and update inventory.</p>

                        <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-4 py-3">Ordered</th>
                                        <th class="px-4 py-3">Received</th>
                                        <th class="px-4 py-3">Remaining</th>
                                        <th class="px-4 py-3">Supplier Cost/Unit</th>
                                        <th class="px-4 py-3">Receive quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-700">
                                    <?php $__currentLoopData = $purchaseOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $remainingQuantity = max(0, $item->quantity - ($item->received_quantity ?? 0));
                                        ?>
                                        <tr class="hover:bg-white">
                                            <td class="px-4 py-3"><?php echo e($item->product_name); ?></td>
                                            <td class="px-4 py-3"><?php echo e($item->quantity); ?></td>
                                            <td class="px-4 py-3"><?php echo e($item->received_quantity ?? 0); ?></td>
                                            <td class="px-4 py-3"><?php echo e($remainingQuantity); ?></td>
                                            <td class="px-4 py-3">
                                                <input name="items[<?php echo e($item->id); ?>][unit_price]" type="number" step="0.01" min="0" value="<?php echo e((float) $item->unit_price); ?>" class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" placeholder="0.00" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input name="items[<?php echo e($item->id); ?>][item_id]" type="hidden" value="<?php echo e($item->id); ?>" />
                                                <input name="items[<?php echo e($item->id); ?>][received_quantity]" type="number" min="0" max="<?php echo e($remainingQuantity); ?>" value="<?php echo e($remainingQuantity); ?>" class="w-24 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Receive Order</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <?php if($purchaseOrder->notes): ?>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Notes</h2>
                <p class="mt-3 text-sm text-slate-700"><?php echo e($purchaseOrder->notes); ?></p>
            </div>
        <?php endif; ?>
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
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/purchase_order/order-detail.blade.php ENDPATH**/ ?>