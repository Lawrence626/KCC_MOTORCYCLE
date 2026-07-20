<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Edit Product')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Edit Product'))]); ?>
    <style>
        .motorcycle-group { max-height: 300px; overflow-y: auto; }
        .motorcycle-group::-webkit-scrollbar { width: 6px; }
        .motorcycle-group::-webkit-scrollbar-track { background: #f1f5f9; }
        .motorcycle-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .qr-thumbnail { width: 150px; height: 150px; }
    </style>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo e(route('product-catalog.index')); ?>" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">← Back to Products</a>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Edit Product</h1>
                <p class="text-sm text-slate-500 mt-1">Update product details, SKU, and motorcycle compatibility.</p>
            </div>

            <form action="<?php echo e(route('product-catalog.update', $productCatalog)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Warehouse</label>
                            <input type="text" 
                                   name="warehouse" 
                                   value="<?php echo e(old('warehouse', $productCatalog->warehouse)); ?>" 
                                   placeholder="e.g., Warehouse A" 
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <?php $__errorArgs = ['warehouse'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Product Description <span class="text-red-500">*</span></label>
                            <select name="product_description" 
                                    id="productDescriptionSelect"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                                    required>
                                <option value="">Select product description</option>
                                <?php $__currentLoopData = $productDescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $description): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($description->name); ?>" 
                                            data-brands="<?php echo e(json_encode($description->brands)); ?>"
                                            <?php echo e(old('product_description', $productCatalog->product_description) == $description->name ? 'selected' : ''); ?>>
                                        <?php echo e($description->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['product_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Brand <span class="text-red-500">*</span></label>
                            <select name="brand"
                                    id="brandSelect"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                    required>
                                <option value="">Select brand</option>
                            </select>
                            <?php $__errorArgs = ['brand'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Product Name (Optional)</label>
                            <input type="text" 
                                   name="product_name" 
                                   value="<?php echo e(old('product_name', $productCatalog->product_name)); ?>" 
                                   placeholder="e.g., Additional product name" 
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            <?php $__errorArgs = ['product_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Size</label>
                                <input type="text" 
                                       name="size" 
                                       value="<?php echo e(old('size', $productCatalog->size)); ?>" 
                                       placeholder="e.g., L, XL, 14 inch" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Color</label>
                                <input type="text" 
                                       name="color" 
                                       value="<?php echo e(old('color', $productCatalog->color)); ?>" 
                                       placeholder="e.g., Black, Red, Blue" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Stock Quantity</label>
                                <input type="number" 
                                       name="stock_quantity" 
                                       value="<?php echo e(old('stock_quantity', $productCatalog->stock_quantity ?? 0)); ?>" 
                                       min="0" 
                                       placeholder="0" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['stock_quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Reorder Level</label>
                                <input type="number" 
                                       name="reorder_level" 
                                       value="<?php echo e(old('reorder_level', $productCatalog->reorder_level ?? 10)); ?>" 
                                       min="0" 
                                       placeholder="10" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['reorder_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">SKU <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="sku" 
                                       id="skuInput"
                                       value="<?php echo e(old('sku', $productCatalog->sku)); ?>" 
                                       placeholder="Auto-generated or enter manually" 
                                       class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono" 
                                       required>
                                <button type="button" 
                                        id="generateSkuBtn"
                                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition">
                                    Generate SKU
                                </button>
                            </div>
                            <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Description</label>
                            <textarea name="description" 
                                      rows="3" 
                                      placeholder="Product description (optional)" 
                                      class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"><?php echo e(old('description', $productCatalog->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Expiration Fields - Only for expirable products -->
                        <div id="expirationFields" class="hidden space-y-4 bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <h4 class="text-sm font-semibold text-amber-900 mb-2">Expiration Information</h4>
                            
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Manufacturing Date (Optional)</label>
                                <input type="date" 
                                       name="manufacturing_date" 
                                       value="<?php echo e(old('manufacturing_date', $productCatalog->manufacturing_date ? $productCatalog->manufacturing_date->format('Y-m-d') : '')); ?>" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['manufacturing_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Batch/Lot Number <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="batch_lot_number" 
                                       value="<?php echo e(old('batch_lot_number', $productCatalog->batch_lot_number)); ?>" 
                                       placeholder="e.g., LOT-2024-001" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['batch_lot_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Expiration Date <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       name="expiration_date" 
                                       value="<?php echo e(old('expiration_date', $productCatalog->expiration_date ? $productCatalog->expiration_date->format('Y-m-d') : '')); ?>" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                <?php $__errorArgs = ['expiration_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                                <option value="Active" <?php echo e(old('status', $productCatalog->status) == 'Active' ? 'selected' : ''); ?>>Active</option>
                                <option value="Inactive" <?php echo e(old('status') == 'Inactive' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-2 block">Compatible Motorcycle Models</label>
                            <div class="border border-slate-200 rounded-lg p-4">
                                <!-- Existing Compatible Models -->
                                <div class="mb-4">
                                    <p class="text-xs font-medium text-slate-600 mb-2">Currently Compatible:</p>
                                    <div id="existingCompatibleModels" class="space-y-2">
                                        <?php if(isset($productCatalog->motorcycleModels) && count($productCatalog->motorcycleModels) > 0): ?>
                                            <?php $__currentLoopData = $productCatalog->motorcycleModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-200" data-model-id="<?php echo e($model->id); ?>">
                                                    <span class="text-sm text-slate-700"><?php echo e($model->brand); ?> <?php echo e($model->model_name); ?></span>
                                                    <button type="button" class="remove-compatible-btn text-red-600 hover:text-red-700 text-sm font-medium" data-model-id="<?php echo e($model->id); ?>">
                                                        Remove
                                                    </button>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <p class="text-xs text-slate-500 italic">No compatible models set</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Add New Compatible Model -->
                                <div class="border-t border-slate-200 pt-4">
                                    <p class="text-xs font-medium text-slate-600 mb-2">Add New Compatible Model:</p>
                                    <div class="flex gap-2">
                                        <input type="text" 
                                               id="addCompatibleModelInput" 
                                               placeholder="Enter motorcycle model name..." 
                                               class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                        <button type="button" id="addCompatibleBtn" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                                            Add
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Hidden input to store selected model IDs -->
                                <input type="hidden" name="motorcycle_models" id="motorcycleModelsInput" value="<?php echo e(isset($productCatalog->motorcycleModels) ? $productCatalog->motorcycleModels->pluck('id')->implode(',') : ''); ?>">
                                
                                <p class="text-xs text-slate-500 mt-2">Type a motorcycle model name and click Add to include it as compatible.</p>
                            </div>
                            <?php $__errorArgs = ['motorcycle_models'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Right Column - QR Code -->
                    <div class="lg:col-span-1">
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">QR Code</h3>
                            <?php if($productCatalog->qr_code_path): ?>
                                <div class="flex items-center justify-center mb-4">
                                    <img src="<?php echo e(asset('storage/' . $productCatalog->qr_code_path)); ?>" alt="QR Code" class="qr-thumbnail rounded-lg border border-slate-200">
                                </div>
                                <div class="space-y-2">
                                    <a href="<?php echo e(route('product-catalog.download-qr', $productCatalog)); ?>" class="block w-full text-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Download QR
                                    </a>
                                    <a href="<?php echo e(route('product-catalog.print-qr', $productCatalog)); ?>" target="_blank" class="block w-full text-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Print QR
                                    </a>
                                    <form action="<?php echo e(route('product-catalog.regenerate-qr', $productCatalog)); ?>" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to regenerate the QR code?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('POST'); ?>
                                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                            Regenerate QR
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center justify-center mb-4">
                                    <div class="w-32 h-32 bg-white rounded-lg border border-slate-200 flex items-center justify-center">
                                        <span class="text-xs text-slate-400">No QR Code</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 text-center">QR code will be generated when you save the product.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3 pt-6 border-t border-slate-200">
                    <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold hover:from-cyan-700 hover:to-cyan-600 transition">
                        Update Product
                    </button>
                    <a href="<?php echo e(route('product-catalog.index')); ?>" class="px-6 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productDescriptionSelect = document.getElementById('productDescriptionSelect');
            const brandSelect = document.getElementById('brandSelect');
            const skuInput = document.getElementById('skuInput');
            const generateSkuBtn = document.getElementById('generateSkuBtn');
            const currentBrand = "<?php echo e($productCatalog->brand); ?>";
            const expirationFields = document.getElementById('expirationFields');
            const addCompatibleModelInput = document.getElementById('addCompatibleModelInput');
            const addCompatibleBtn = document.getElementById('addCompatibleBtn');
            const existingCompatibleModels = document.getElementById('existingCompatibleModels');
            const motorcycleModelsInput = document.getElementById('motorcycleModelsInput');

            // Expirable product descriptions
            const expirableDescriptions = [
                'ENGINE OIL',
                'BRAKE FLUID (BRAKE OIL)',
                'GEAR OIL',
                'COOLANT / RADIATOR COOLANT',
                'CVT CLEANER',
                'TIRE SEALANT'
            ];

            // Function to show/hide expiration fields
            function toggleExpirationFields() {
                const selectedDescription = productDescriptionSelect.value.toUpperCase();
                if (expirableDescriptions.includes(selectedDescription)) {
                    expirationFields.classList.remove('hidden');
                } else {
                    expirationFields.classList.add('hidden');
                }
            }

            // Initialize brand dropdown based on current product description
            function initializeBrandDropdown() {
                const selectedOption = productDescriptionSelect.options[productDescriptionSelect.selectedIndex];
                const brands = selectedOption.dataset.brands ? JSON.parse(selectedOption.dataset.brands) : [];
                
                // Clear brand dropdown
                brandSelect.innerHTML = '<option value="">Select brand</option>';
                
                if (brands.length > 0) {
                    brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        if (brand === currentBrand) {
                            option.selected = true;
                        }
                        brandSelect.appendChild(option);
                    });
                } else {
                    brandSelect.innerHTML = '<option value="">No brands available</option>';
                }
            }

            // Initialize on page load
            initializeBrandDropdown();
            toggleExpirationFields();

            // Update brand dropdown when product description changes
            productDescriptionSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const brands = selectedOption.dataset.brands ? JSON.parse(selectedOption.dataset.brands) : [];
                
                // Clear brand dropdown
                brandSelect.innerHTML = '<option value="">Select brand</option>';
                
                if (brands.length > 0) {
                    brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        brandSelect.appendChild(option);
                    });
                } else {
                    brandSelect.innerHTML = '<option value="">No brands available</option>';
                }
                
                // Reset brand selection if current brand is not in new list
                if (brands.length > 0 && !brands.includes(currentBrand)) {
                    brandSelect.value = '';
                }
                
                // Toggle expiration fields
                toggleExpirationFields();
            });

            // Generate SKU
            generateSkuBtn.addEventListener('click', async function() {
                const productDescription = productDescriptionSelect.value;
                if (!productDescription) {
                    alert('Please select a product description first');
                    return;
                }

                try {
                    const response = await fetch(`<?php echo e(route('product-catalog.generate-sku')); ?>?product_description=${encodeURIComponent(productDescription)}`);
                    const data = await response.json();
                    if (data.sku) {
                        skuInput.value = data.sku;
                    } else if (data.error) {
                        alert(data.error);
                    }
                } catch (error) {
                    console.error('Error generating SKU:', error);
                    alert('Failed to generate SKU');
                }
            });

            // Add compatible model from text input
            addCompatibleBtn.addEventListener('click', function() {
                const modelName = addCompatibleModelInput.value.trim();

                if (!modelName) {
                    alert('Please enter a motorcycle model name');
                    return;
                }

                // Check if already added
                const existingModels = existingCompatibleModels.querySelectorAll('[data-model-name]');
                for (let model of existingModels) {
                    if (model.dataset.modelName.toLowerCase() === modelName.toLowerCase()) {
                        alert('This model is already added');
                        return;
                    }
                }

                // Add to existing list
                const modelDiv = document.createElement('div');
                modelDiv.className = 'flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-200';
                modelDiv.dataset.modelName = modelName;
                modelDiv.dataset.isNew = 'true';
                modelDiv.innerHTML = `
                    <span class="text-sm text-slate-700">${modelName}</span>
                    <button type="button" class="remove-compatible-btn text-red-600 hover:text-red-700 text-sm font-medium" data-model-name="${modelName}">
                        Remove
                    </button>
                `;
                existingCompatibleModels.appendChild(modelDiv);

                // Update hidden input
                updateMotorcycleModelsInput();

                // Clear input
                addCompatibleModelInput.value = '';
            });

            // Remove compatible model (using event delegation)
            existingCompatibleModels.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-compatible-btn')) {
                    const modelDiv = e.target.closest('[data-model-id]') || e.target.closest('[data-model-name]');
                    if (modelDiv) {
                        modelDiv.remove();
                        updateMotorcycleModelsInput();
                    }
                }
            });

            // Update hidden input with selected model IDs and names
            function updateMotorcycleModelsInput() {
                const modelIds = [];
                const modelNames = [];
                
                existingCompatibleModels.querySelectorAll('[data-model-id]').forEach(div => {
                    modelIds.push(div.dataset.modelId);
                });
                
                existingCompatibleModels.querySelectorAll('[data-is-new="true"]').forEach(div => {
                    modelNames.push(div.dataset.modelName);
                });
                
                // Combine existing IDs and new names
                const combined = [...modelIds, ...modelNames];
                motorcycleModelsInput.value = combined.join(',');
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/product-catalog/edit.blade.php ENDPATH**/ ?>