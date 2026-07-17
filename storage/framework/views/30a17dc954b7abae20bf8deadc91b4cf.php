<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Product Details')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Product Details'))]); ?>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo e(route('product-catalog.index')); ?>" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">← Back to Products</a>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900"><?php echo e($productCatalog->product_description); ?></h1>
                        <?php if($productCatalog->product_name): ?>
                            <p class="text-sm text-slate-500 mt-1"><?php echo e($productCatalog->product_name); ?></p>
                        <?php endif; ?>
                        <p class="text-sm text-slate-500 mt-1"><?php echo e($productCatalog->brand); ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo e($productCatalog->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                        <?php echo e($productCatalog->status); ?>

                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Info -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Product Information</h3>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-slate-500">SKU</dt>
                                    <dd class="font-mono text-slate-900"><?php echo e($productCatalog->sku); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Product Description</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->product_description); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Brand</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->brand); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Status</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->status); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Size</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->size ?? '-'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Color</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->color ?? '-'); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Stock Quantity</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->stock_quantity ?? 0); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Reorder Level</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->reorder_level ?? 10); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Warehouse</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->warehouse ?? '-'); ?></dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Expiration Information - Only for expirable products -->
                        <?php if(in_array(strtoupper($productCatalog->product_description), ['ENGINE OIL', 'BRAKE FLUID (BRAKE OIL)', 'GEAR OIL', 'COOLANT / RADIATOR COOLANT', 'CVT CLEANER', 'TIRE SEALANT'])): ?>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 mb-3">Expiration Information</h3>
                                <dl class="grid grid-cols-2 gap-4 text-sm">
                                    <?php if($productCatalog->manufacturing_date): ?>
                                        <div>
                                            <dt class="text-slate-500">Manufacturing Date</dt>
                                            <dd class="text-slate-900"><?php echo e($productCatalog->manufacturing_date->format('M d, Y')); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($productCatalog->batch_lot_number): ?>
                                        <div>
                                            <dt class="text-slate-500">Batch/Lot Number</dt>
                                            <dd class="text-slate-900"><?php echo e($productCatalog->batch_lot_number); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($productCatalog->expiration_date): ?>
                                        <div>
                                            <dt class="text-slate-500">Expiration Date</dt>
                                            <dd class="text-slate-900"><?php echo e($productCatalog->expiration_date->format('M d, Y')); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        <?php endif; ?>

                        <!-- Description -->
                        <?php if($productCatalog->description): ?>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 mb-3">Description</h3>
                                <p class="text-sm text-slate-600"><?php echo e($productCatalog->description); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Compatible Models -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Compatible Motorcycle Models</h3>
                            <?php if($productCatalog->motorcycleModels->count() > 0): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__currentLoopData = $productCatalog->motorcycleModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm">
                                            <?php echo e($model->full_name); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-slate-500">No compatible models specified</p>
                            <?php endif; ?>
                        </div>

                        <!-- Timestamps -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Timestamps</h3>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-slate-500">Created</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->created_at->format('M d, Y - g:i A')); ?></dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Last Updated</dt>
                                    <dd class="text-slate-900"><?php echo e($productCatalog->updated_at->format('M d, Y - g:i A')); ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Right Column - QR Code -->
                    <div class="lg:col-span-1">
                        <div class="bg-slate-50 rounded-lg p-6 border border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">QR Code</h3>
                            <?php if($productCatalog->qr_code_path): ?>
                                <div class="flex items-center justify-center mb-4">
                                    <img src="<?php echo e(asset('storage/' . $productCatalog->qr_code_path)); ?>" alt="QR Code" class="w-48 h-48 rounded-lg border border-slate-200">
                                </div>
                                <div class="space-y-2">
                                    <a href="<?php echo e(route('product-catalog.download-qr', $productCatalog)); ?>" class="block w-full text-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Download QR
                                    </a>
                                    <a href="<?php echo e(route('product-catalog.print-qr', $productCatalog)); ?>" target="_blank" class="block w-full text-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Print QR
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center justify-center mb-4">
                                    <div class="w-48 h-48 bg-white rounded-lg border border-slate-200 flex items-center justify-center">
                                        <span class="text-sm text-slate-400">No QR Code</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 pt-6 border-t border-slate-200 flex items-center gap-3">
                    <a href="<?php echo e(route('product-catalog.edit', $productCatalog)); ?>" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                        Edit Product
                    </a>
                    <form action="<?php echo e(route('product-catalog.destroy', $productCatalog)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition">
                            Delete Product
                        </button>
                    </form>
                </div>
            </div>
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/product-catalog/show.blade.php ENDPATH**/ ?>