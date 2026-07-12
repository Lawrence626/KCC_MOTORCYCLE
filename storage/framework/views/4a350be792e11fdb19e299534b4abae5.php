<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Archived Shelves')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Archived Shelves'))]); ?>
    <style>
        :root {
            --brand: #0f766e;
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #6b7280;
            --border: rgba(148,163,184,0.2);
        }
        .si-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .si-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .product-chip { background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.12); color: var(--brand-dark); font-size: 0.78rem; padding: 0.35rem 0.6rem; border-radius: 0.8rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; }
        .product-chip .left { display:flex; flex-direction:column; gap:0.08rem; }
        .product-chip .name { font-weight:600; font-size:0.84rem; color:#0f172a; }
        .product-chip .meta { font-size:0.62rem; color:#475569; }
        .product-chip .qty { font-weight:700; font-size:0.84rem; color:#0f172a; margin-left:0.4rem; min-width:44px; text-align:right; }
        .btn-primary { background: linear-gradient(90deg, var(--brand), var(--brand-dark)); color: #fff; box-shadow: 0 4px 15px rgba(15,118,110,0.25); transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.35); }
        .btn-secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); transition: all 0.2s ease; }
        .btn-secondary:hover { background: #f1f5f9; border-color: rgba(148,163,184,0.5); }
        .btn-danger { background: linear-gradient(90deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 15px rgba(239,68,68,0.25); transition: all 0.2s ease; }
        .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(239,68,68,0.35); }
        .btn-success { background: linear-gradient(90deg, #10b981, #059669); color: #fff; box-shadow: 0 4px 15px rgba(16,185,129,0.25); transition: all 0.2s ease; }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,0.35); }
    </style>

    <div class="space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Archived Shelves</h1>
                <p class="mt-2 text-sm text-gray-500">View and manage archived shop shelves.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('shop.inventory')); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Shop Inventory
                </a>
            </div>
        </div>

        <?php if($archivedShelves->count() > 0): ?>
            <div class="grid gap-6 mt-4">
                <?php $__currentLoopData = $archivedShelves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shelf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="si-card rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-md flex items-center justify-center text-white font-semibold bg-gray-400 text-sm"><?php echo e(strtoupper(substr($shelf->name, -1))); ?></div>
                                <div>
                                    <div class="text-base font-semibold text-slate-900"><?php echo e($shelf->name); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($shelf->location ?? 'No location'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="restoreShelf(<?php echo e($shelf->id); ?>)" class="btn-success px-4 py-2 rounded-xl text-sm font-medium">
                                Restore
                            </button>
                            <button onclick="deleteShelf(<?php echo e($shelf->id); ?>)" class="btn-danger px-4 py-2 rounded-xl text-sm font-medium">
                                Delete Permanently
                            </button>
                        </div>
                    </div>
                    
                    <?php if($shelf->shop_inventory && $shelf->shop_inventory->count() > 0): ?>
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-medium text-gray-500">Products (<?php echo e($shelf->occupied); ?> items):</p>
                        <div class="grid grid-cols-2 gap-2">
                            <?php $__currentLoopData = $shelf->shop_inventory->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="product-chip">
                                <div class="left">
                                    <span class="name"><?php echo e($item->product->name ?? 'Unknown Product'); ?></span>
                                    <span class="meta"><?php echo e($item->product->sku ?? ''); ?></span>
                                </div>
                                <span class="qty"><?php echo e($item->quantity); ?></span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($shelf->shop_inventory->count() > 10): ?>
                        <p class="text-xs text-gray-400 mt-2">+<?php echo e($shelf->shop_inventory->count() - 10); ?> more</p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <p class="mt-4 text-sm text-gray-400">No products on this shelf</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="si-card rounded-lg p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">No Archived Shelves</h3>
                <p class="text-sm text-gray-500">You haven't archived any shelves yet.</p>
                <a href="<?php echo e(route('shop.inventory')); ?>" class="inline-flex items-center gap-2 mt-4 text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                    Go to Shop Inventory
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        async function restoreShelf(shelfId) {
            if (!confirm('Are you sure you want to restore this shelf?')) {
                return;
            }

            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Shelf restored successfully');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to restore shelf');
                }
            } catch (error) {
                alert('Error restoring shelf');
            }
        }

        async function deleteShelf(shelfId) {
            if (!confirm('Are you sure you want to permanently delete this shelf? This action cannot be undone and will delete all associated products.')) {
                return;
            }

            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}/permanent`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Shelf deleted permanently');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to delete shelf');
                }
            } catch (error) {
                alert('Error deleting shelf');
            }
        }
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/shop_inventory/archived.blade.php ENDPATH**/ ?>