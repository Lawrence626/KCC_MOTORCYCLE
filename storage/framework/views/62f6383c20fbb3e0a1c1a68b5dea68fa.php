<div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm mb-4">
    <div class="flex flex-wrap gap-2">
        <?php
            $offlineMenuLinks = [
                ['route' => 'offline.purchase-orders', 'label' => 'Purchase Orders'],
                ['route' => 'offline.export', 'label' => 'Export Data'],
                ['route' => 'offline.import', 'label' => 'Import Data'],
                ['route' => 'offline.pending.imports', 'label' => 'Pending Imports'],
                ['route' => 'offline.history', 'label' => 'Sync History'],
            ];
        ?>

        <?php $__currentLoopData = $offlineMenuLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                   'px-4 py-2 rounded-lg text-sm font-medium transition',
                   'bg-cyan-600 text-white' => request()->routeIs($item['route']),
                   'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' => !request()->routeIs($item['route']),
               ]); ?>">
                <?php echo e($item['label']); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/partials/offline-submenu.blade.php ENDPATH**/ ?>