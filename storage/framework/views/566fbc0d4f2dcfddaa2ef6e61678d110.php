<?php if($paginator->hasPages()): ?>
    <div class="border-t border-slate-200 px-6 py-4 flex items-center justify-between">
        <p class="text-sm text-slate-600">Showing <?php echo e($paginator->firstItem() ?? 0); ?> - <?php echo e($paginator->lastItem() ?? 0); ?> of <?php echo e($paginator->total()); ?> entries</p>
        <nav role="navigation" aria-label="Pagination Navigation">
            <ul class="inline-flex items-center gap-2">
                
                <?php if($paginator->onFirstPage()): ?>
                    <li>
                        <span class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-400">&lsaquo;</span>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">&lsaquo;</a>
                    </li>
                <?php endif; ?>

                
                <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if(is_string($element)): ?>
                        <li><span class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-400"><?php echo e($element); ?></span></li>
                    <?php endif; ?>

                    
                    <?php if(is_array($element)): ?>
                        <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $paginator->currentPage()): ?>
                                    <li aria-current="page"><span class="inline-flex items-center px-3 py-1 rounded-[10px] bg-slate-400 text-white font-semibold shadow-sm"><?php echo e($page); ?></span></li>
                                <?php else: ?>
                                    <li><a href="<?php echo e($url); ?>" class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-600 hover:bg-slate-50"><?php echo e($page); ?></a></li>
                                <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($paginator->hasMorePages()): ?>
                    <li>
                        <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">&rsaquo;</a>
                    </li>
                <?php else: ?>
                    <li>
                        <span class="inline-flex items-center px-3 py-1 rounded-[10px] bg-white border border-slate-200 text-slate-400">&rsaquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/vendor/pagination/tailwind.blade.php ENDPATH**/ ?>