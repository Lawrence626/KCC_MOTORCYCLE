<?php
    $notifications = [];
    if (auth()->check() && auth()->user()->role === 'admin') {
        $notifications = Cache::pull('admin_purchase_order_notifications:' . auth()->id(), []);
        if (! is_array($notifications)) {
            $notifications = [];
        }
    }
?>

<?php if(!empty($notifications)): ?>
    <div class="fixed right-4 top-4 z-[60] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3" aria-live="polite">
        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-amber-200 bg-white p-4 shadow-xl shadow-slate-900/10" data-toast-notification="<?php echo e($notification['id'] ?? ''); ?>">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">New Purchase Order Submitted – Approval Required.</p>
                        <p class="mt-1 text-sm text-slate-600">Order <?php echo e($notification['order_number'] ?? 'Unknown'); ?> is waiting for your review.</p>
                    </div>
                    <button type="button" class="text-slate-400 transition hover:text-slate-700" data-dismiss-toast aria-label="Dismiss notification">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 flex items-center justify-end gap-2">
                    <a href="<?php echo e($notification['url'] ?? route('order.management')); ?>" class="rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">View Order</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toasts = Array.from(document.querySelectorAll('[data-toast-notification]'));

            const dismissToast = (toast) => {
                toast.remove();
            };

            toasts.forEach((toast) => {
                const dismissButton = toast.querySelector('[data-dismiss-toast]');
                if (dismissButton) {
                    dismissButton.addEventListener('click', () => dismissToast(toast));
                }

                setTimeout(() => dismissToast(toast), 7000);
            });
        });
    </script>
<?php endif; ?>
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/partials/admin-purchase-order-toasts.blade.php ENDPATH**/ ?>