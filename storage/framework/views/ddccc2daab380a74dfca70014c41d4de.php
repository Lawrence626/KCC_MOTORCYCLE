<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Create Purchase Order')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Create Purchase Order'))]); ?>
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Create Purchase Order</h1>
                <p class="max-w-2xl text-sm text-slate-500">Choose supplier and select low-stock products to restock.</p>
            </div>
            <a href="<?php echo e(route('order.management')); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900">Back to orders</a>
        </div>

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

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <form action="<?php echo e(route('order.store')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>
                <div class="grid gap-4 lg:grid-cols-2">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="supplier_id" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option value="">Select supplier</option>
                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supplier->id); ?>" <?php echo e(old('supplier_id') == $supplier->id ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>

                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Expected delivery date</span>
                        <input name="expected_delivery_date" value="<?php echo e(old('expected_delivery_date')); ?>" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                </div>

                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Notes</span>
                    <textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none"><?php echo e(old('notes')); ?></textarea>
                </label>

                <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-4">
                    <?php if(!empty($selectedProductIds)): ?>
                        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            The low-stock alert preselected these products for replenishment.
                        </div>
                    <?php endif; ?>
                    <h3 class="text-sm font-semibold text-slate-700">Low stock products</h3>
                    <p class="mt-1 text-sm text-slate-500">Select the items to include in the order.</p>

                    <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                                <tr>
                                    <th class="px-4 py-3">Select</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Supplier</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3">Reorder</th>
                                    <th class="px-4 py-3">Qty to order</th>
                                    <th class="px-4 py-3">Unit price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-700">
                                <?php $__empty_1 = true; $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-white">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="products[<?php echo e($loop->index); ?>][selected]" value="1" <?php echo e(in_array($product->id, $selectedProductIds, true) ? 'checked' : ''); ?> class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                            <input type="hidden" name="products[<?php echo e($loop->index); ?>][product_id]" value="<?php echo e($product->id); ?>" />
                                            <input type="hidden" name="products[<?php echo e($loop->index); ?>][product_name]" value="<?php echo e($product->product_name ?? $product->name); ?>" />
                                            <input type="hidden" name="products[<?php echo e($loop->index); ?>][sku]" value="<?php echo e($product->sku); ?>" />
                                        </td>
                                        <td class="px-4 py-3"><?php echo e($product->product_name ?? $product->name); ?></td>
                                        <td class="px-4 py-3"><?php echo e($product->supplier_name); ?></td>
                                        <td class="px-4 py-3"><?php echo e($product->sku); ?></td>
                                        <td class="px-4 py-3"><?php echo e($product->stock_quantity); ?></td>
                                        <td class="px-4 py-3"><?php echo e($product->reorder_level); ?></td>
                                        <td class="px-4 py-3">
                                            <input name="products[<?php echo e($loop->index); ?>][quantity]" type="number" min="1" value="<?php echo e(old('products.' . $loop->index . '.quantity', max(1, $product->reorder_level - $product->stock_quantity))); ?>" class="w-20 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input name="products[<?php echo e($loop->index); ?>][unit_price]" type="number" step="0.01" min="0" value="<?php echo e(old('products.' . $loop->index . '.unit_price', $product->unit_price)); ?>" class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">No low-stock products found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4"><?php echo e($lowStockProducts->links()); ?></div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <a href="<?php echo e(route('order.management')); ?>" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Submit order</button>
                </div>
            </form>
        </div>
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
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/purchase_order/create.blade.php ENDPATH**/ ?>