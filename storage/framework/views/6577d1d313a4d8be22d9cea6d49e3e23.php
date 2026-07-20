<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Add Product')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Add Product'))]); ?>
    <style>
        .motorcycle-group { max-height: 300px; overflow-y: auto; }
        .motorcycle-group::-webkit-scrollbar { width: 6px; }
        .motorcycle-group::-webkit-scrollbar-track { background: #f1f5f9; }
        .motorcycle-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .qr-preview { width: 150px; height: 150px; }
    </style>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="<?php echo e(route('product-catalog.index')); ?>" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">← Back to Products</a>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Add New Product</h1>
                <p class="text-sm text-slate-500 mt-1">Create a new product with SKU generation, QR code, and motorcycle compatibility.</p>
            </div>

            <form action="<?php echo e(route('product-catalog.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Warehouse</label>
                            <input type="text" 
                                   name="warehouse" 
                                   value="<?php echo e(old('warehouse')); ?>" 
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
                            <div class="flex gap-2">
                                <select name="product_description" 
                                        id="productDescriptionSelect"
                                        class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                                        required>
                                    <option value="">Select product description</option>
                                    <?php $__currentLoopData = $productDescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $description): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($description->name); ?>" 
                                                data-brands="<?php echo e(json_encode($description->brands)); ?>"
                                                <?php echo e(old('product_description') == $description->name ? 'selected' : ''); ?>>
                                            <?php echo e($description->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <button type="button" 
                                        id="addNewProductDescBtn"
                                        class="px-3 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                                    + New
                                </button>
                            </div>
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
                                <option value="">Select product description first</option>
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
                                   value="<?php echo e(old('product_name')); ?>" 
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
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Size (Optional)</label>
                                <input type="text" 
                                       name="size" 
                                       value="<?php echo e(old('size')); ?>" 
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
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Color (Optional)</label>
                                <input type="text" 
                                       name="color" 
                                       value="<?php echo e(old('color')); ?>" 
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
                                       value="<?php echo e(old('stock_quantity') ?? 0); ?>" 
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
                                       value="<?php echo e(old('reorder_level') ?? 10); ?>" 
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
                                       value="<?php echo e(old('sku')); ?>" 
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
                                      class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"><?php echo e(old('description')); ?></textarea>
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
                                       value="<?php echo e(old('manufacturing_date')); ?>" 
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
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Batch/Lot Number (Optional)</label>
                                <input type="text" 
                                       name="batch_lot_number" 
                                       value="<?php echo e(old('batch_lot_number')); ?>" 
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
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Expiration Date (Optional)</label>
                                <input type="date" 
                                       name="expiration_date" 
                                       value="<?php echo e(old('expiration_date')); ?>" 
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
                                <option value="Active" <?php echo e(old('status', 'Active') == 'Active' ? 'selected' : ''); ?>>Active</option>
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
                            <label class="text-sm font-medium text-slate-700 mb-2 block">Compatible Motorcycle Models <span class="text-red-500">*</span></label>
                            <div class="border border-slate-200 rounded-lg p-4 motorcycle-group">
                                <?php $__currentLoopData = $motorcycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand => $models): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-2"><?php echo e($brand); ?></h4>
                                        <div class="space-y-2">
                                            <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 p-1 rounded">
                                                    <input type="checkbox" 
                                                           name="motorcycle_models[]" 
                                                           value="<?php echo e($model->id); ?>" 
                                                           class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                                    <span class="text-sm text-slate-700"><?php echo e($model->full_name); ?></span>
                                                </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                    <!-- Right Column - QR Preview -->
                    <div class="lg:col-span-1">
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">QR Code Preview</h3>
                            <div class="flex items-center justify-center mb-4">
                                <div id="qrPreview" class="qr-preview bg-white rounded-lg border border-slate-200 flex items-center justify-center">
                                    <span class="text-xs text-slate-400">Enter SKU to preview</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 text-center">QR code will be automatically generated when you save the product.</p>
                        </div>
                    </div>                </div>

                <div class="mt-6 flex items-center gap-3 pt-6 border-t border-slate-200">
                    <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold hover:from-cyan-700 hover:to-cyan-600 transition">
                        Save Product
                    </button>
                    <a href="<?php echo e(route('product-catalog.index')); ?>" class="px-6 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Add New Product Description Modal -->
    <div id="addProductDescModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 sm:max-w-md overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-500">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Add New Product Description</h3>
                        <button id="closeProductDescModal" class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <form id="addProductDescForm" class="p-6 space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Product Description Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="newProductDescName" 
                               name="name" 
                               placeholder="e.g., Brake Pads" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Brand <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="newProductDescBrand" 
                               name="brand" 
                               placeholder="e.g., Bosch" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                               required>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" id="cancelProductDesc" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                            Add Description
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productDescriptionSelect = document.getElementById('productDescriptionSelect');
            const brandSelect = document.getElementById('brandSelect');
            const skuInput = document.getElementById('skuInput');
            const generateSkuBtn = document.getElementById('generateSkuBtn');
            const qrPreview = document.getElementById('qrPreview');
            const expirationFields = document.getElementById('expirationFields');
            const addNewProductDescBtn = document.getElementById('addNewProductDescBtn');
            const addProductDescModal = document.getElementById('addProductDescModal');
            const closeProductDescModal = document.getElementById('closeProductDescModal');
            const cancelProductDesc = document.getElementById('cancelProductDesc');
            const addProductDescForm = document.getElementById('addProductDescForm');
            const newProductDescName = document.getElementById('newProductDescName');
            const newProductDescBrand = document.getElementById('newProductDescBrand');

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
                
                // Reset brand selection
                brandSelect.value = '';
                
                // Toggle expiration fields
                toggleExpirationFields();
            });

            // Generate SKU helper
            async function generateSku() {
                const productDescription = productDescriptionSelect.value;
                const brand = brandSelect.value;

                if (!productDescription) {
                    alert('Please select a product description first.');
                    return;
                }
                if (!brand) {
                    alert('Please select a brand first.');
                    return;
                }

                try {
                    const url = `<?php echo e(route('product-catalog.generate-sku')); ?>?product_description=${encodeURIComponent(productDescription)}&brand=${encodeURIComponent(brand)}`;
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data.sku) {
                        skuInput.value = data.sku;
                        updateQrPreview(data.sku);
                    } else if (data.error) {
                        alert(data.error);
                    }
                } catch (error) {
                    console.error('Error generating SKU:', error);
                    alert('Failed to generate SKU. Please try again.');
                }
            }

            // Auto-generate SKU when brand changes (if description is already selected)
            brandSelect.addEventListener('change', function() {
                if (productDescriptionSelect.value && this.value) {
                    generateSku();
                }
            });

            // Generate SKU button
            generateSkuBtn.addEventListener('click', generateSku);

            // Update QR preview on SKU change
            skuInput.addEventListener('input', function() {
                updateQrPreview(this.value);
            });

            function updateQrPreview(sku) {
                if (!sku) {
                    qrPreview.innerHTML = '<span class="text-xs text-slate-400">Enter SKU to preview</span>';
                    return;
                }
                
                // Use QRCode.js for preview
                qrPreview.innerHTML = '';
                const qr = new QRCode(qrPreview, {
                    text: sku,
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            // Add New Product Description Modal
            addNewProductDescBtn.addEventListener('click', function() {
                addProductDescModal.classList.remove('hidden');
                newProductDescName.focus();
            });

            closeProductDescModal.addEventListener('click', function() {
                addProductDescModal.classList.add('hidden');
                addProductDescForm.reset();
            });

            cancelProductDesc.addEventListener('click', function() {
                addProductDescModal.classList.add('hidden');
                addProductDescForm.reset();
            });

            addProductDescForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const name = newProductDescName.value.trim();
                const brand = newProductDescBrand.value.trim();
                
                if (!name || !brand) {
                    alert('Please fill in all fields');
                    return;
                }

                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    formData.append('name', name);
                    formData.append('brand', brand);

                    const response = await fetch('<?php echo e(route('product-descriptions.store')); ?>', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Add new option to product description select
                        const option = document.createElement('option');
                        option.value = data.product_description.name;
                        option.textContent = data.product_description.name;
                        option.dataset.brands = JSON.stringify(data.product_description.brands);
                        productDescriptionSelect.appendChild(option);
                        
                        // Select the new option
                        productDescriptionSelect.value = data.product_description.name;
                        
                        // Update brand dropdown with the new brand
                        brandSelect.innerHTML = '<option value="">Select brand</option>';
                        data.product_description.brands.forEach(b => {
                            const brandOption = document.createElement('option');
                            brandOption.value = b;
                            brandOption.textContent = b;
                            brandSelect.appendChild(brandOption);
                        });
                        
                        // Select the new brand
                        brandSelect.value = brand;
                        
                        // Close modal and reset form
                        addProductDescModal.classList.add('hidden');
                        addProductDescForm.reset();
                        
                        // Trigger brand change to auto-generate SKU
                        brandSelect.dispatchEvent(new Event('change'));
                        
                        alert('Product description added successfully!');
                    } else {
                        alert(data.error || 'Failed to add product description');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to add product description. Please try again.');
                }
            });
        });
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/qrcode.js']); ?>
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/product-catalog/create.blade.php ENDPATH**/ ?>