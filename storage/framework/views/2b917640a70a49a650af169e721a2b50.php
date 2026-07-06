<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Import Data')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Import Data'))]); ?>
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Import Data</h1>
                <p class="text-xs text-slate-500 mt-0.5">Import offline transactions from CSV or Excel files</p>
            </div>
        </div>

        <!-- Import Form -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Upload File</h2>
            <form action="<?php echo e(route('offline.import.store')); ?>" method="POST" enctype="multipart/form-data" id="importForm">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-slate-700 mb-1">Select File</label>
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" required>
                    <p class="text-xs text-slate-500 mt-1">Supported formats: CSV, Excel (.xlsx, .xls)</p>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Import Data
                    </button>
                    <button type="button" onclick="validateFile()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Validate First
                    </button>
                </div>
            </form>
        </div>

        <!-- Validation Results -->
        <?php if(session('validation_results')): ?>
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Validation Results</h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-xs text-green-600 font-medium">Valid Records</p>
                        <p class="text-2xl font-bold text-green-700"><?php echo e(count(session('validation_results.valid'))); ?></p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-xs text-red-600 font-medium">Invalid Records</p>
                        <p class="text-2xl font-bold text-red-700"><?php echo e(count(session('validation_results.invalid'))); ?></p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <p class="text-xs text-amber-600 font-medium">Duplicate Records</p>
                        <p class="text-2xl font-bold text-amber-700"><?php echo e(count(session('validation_results.duplicates'))); ?></p>
                    </div>
                </div>
                <?php if(!empty(session('validation_results.invalid'))): ?>
                <div class="mt-4">
                    <h3 class="text-xs font-semibold text-slate-900 mb-2">Invalid Records Details</h3>
                    <div class="max-h-60 overflow-y-auto">
                        <?php $__currentLoopData = session('validation_results.invalid'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invalid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-2 bg-red-50 rounded mb-2">
                            <p class="text-xs text-red-700">Row <?php echo e($invalid['index'] + 1); ?>: <?php echo e(implode(', ', $invalid['errors'])); ?></p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Imports -->
        <?php if($pendingImports->count() > 0): ?>
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-amber-50 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Pending Imports (<?php echo e($pendingImports->count()); ?>)</h2>
                <a href="<?php echo e(route('offline.pending.imports')); ?>" class="text-xs text-cyan-600 hover:text-cyan-700 font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">File Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Uploaded By</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Valid</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Invalid</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__currentLoopData = $pendingImports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pending): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium"><?php echo e($pending->file_name); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e($pending->uploadedBy?->name ?? '-'); ?></td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium"><?php echo e($pending->total_records); ?></td>
                            <td class="px-3 py-2 text-right text-green-600 font-medium"><?php echo e($pending->valid_records); ?></td>
                            <td class="px-3 py-2 text-right text-red-600 font-medium"><?php echo e($pending->invalid_records); ?></td>
                            <td class="px-3 py-2">
                                <a href="<?php echo e(route('offline.pending.imports')); ?>" class="text-cyan-600 hover:text-cyan-700 font-medium">Review</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600">Showing <?php echo e($pendingImports->firstItem()); ?> to <?php echo e($pendingImports->lastItem()); ?> of <?php echo e($pendingImports->total()); ?> items</p>
                <?php echo e($pendingImports->links()); ?>

            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Imports -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Recent Imports</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">File Name</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Import Date</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Imported By</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Total</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Imported</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Duplicates</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Failed</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__empty_1 = true; $__currentLoopData = $recentImports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $import): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium"><?php echo e($import->file_name); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e($import->import_date ? $import->import_date->format('M d, Y H:i') : '-'); ?></td>
                            <td class="px-3 py-2 text-slate-600"><?php echo e($import->importedBy?->name ?? '-'); ?></td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium"><?php echo e($import->total_records); ?></td>
                            <td class="px-3 py-2 text-right text-green-600 font-medium"><?php echo e($import->imported_records); ?></td>
                            <td class="px-3 py-2 text-right text-amber-600 font-medium"><?php echo e($import->duplicate_records); ?></td>
                            <td class="px-3 py-2 text-right text-red-600 font-medium"><?php echo e($import->failed_records); ?></td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php if($import->synchronization_status == 'completed'): ?> bg-green-100 text-green-700 <?php elseif($import->synchronization_status == 'failed'): ?> bg-red-100 text-red-700 <?php else: ?> bg-slate-100 text-slate-700 <?php endif; ?>">
                                    <?php echo e(ucfirst($import->synchronization_status)); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-slate-500">No recent imports found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600">Showing <?php echo e($recentImports->firstItem()); ?> to <?php echo e($recentImports->lastItem()); ?> of <?php echo e($recentImports->total()); ?> items</p>
                <?php echo e($recentImports->links()); ?>

            </div>
        </div>

        <!-- Import Information -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h2 class="text-sm font-semibold text-slate-900">Import Information</h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Validation Process</p>
                            <p class="text-xs text-slate-600 mt-1">All records are validated before import. Invalid records are skipped and reported.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Duplicate Detection</p>
                            <p class="text-xs text-slate-600 mt-1">Duplicate purchase orders and inventory movements are automatically detected and skipped.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-slate-900">Sync Status Update</p>
                            <p class="text-xs text-slate-600 mt-1">Successfully imported records are marked as "Synchronized" automatically.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateFile() {
            const fileInput = document.querySelector('input[name="file"]');
            if (fileInput.files.length === 0) {
                alert('Please select a file first.');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            fetch('<?php echo e(route('offline.import.validate')); ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Validation successful!\n\nTotal Records: ${data.total_records}\nValid: ${data.valid_records}\nInvalid: ${data.invalid_records}\nDuplicates: ${data.duplicate_records}`);
                } else {
                    alert('Validation failed: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error validating file: ' + error.message);
            });
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
<?php /**PATH C:\Users\Paulo\OneDrive\Desktop\KCC_MOTORCYCLE\resources\views/offline-reconciliation/import.blade.php ENDPATH**/ ?>