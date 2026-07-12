<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Received Orders')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Received Orders'))]); ?>
    <div class="space-y-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Received Orders</h1>
                <p class="max-w-2xl text-sm text-slate-500">Track completed deliveries, confirm order receipts, and view inventory impact.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-[10px] bg-[#105f68] px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-[#105f68]/20 hover:bg-[#0c474e]">Confirm Receipt</button>
        </div>

        <div class="grid gap-3 sm:grid-cols-3 items-stretch">
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between h-full">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Delivered today</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($deliveredToday)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Orders received and logged today.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between h-full">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Pending confirmation</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($pendingConfirmation)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Awaiting goods inspection or paperwork.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between h-full">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Issues found</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900"><?php echo e(number_format($issuesFound)); ?></p>
                <p class="mt-2 text-sm text-slate-500">Discrepancies requiring follow-up.</p>
            </div>
        </div>

        <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm" style="min-height: calc(100vh - 220px);">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Latest received orders</h2>
                    <p class="text-sm text-slate-500">Recent receipts in a concise table.</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Verified</span>
            </div>

            <form id="receivedOrdersForm" method="GET" action="<?php echo e(route('received.orders')); ?>" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                    <input name="search" type="search" value="<?php echo e($search ?? ''); ?>" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                </label>
                <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedOrdersStatus">
                    <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                    <input type="hidden" name="status" id="receivedOrdersStatusInput" value="<?php echo e($status ?? ''); ?>" />
                    <button type="button" id="receivedOrdersStatusButton" onclick="toggleDropdown('receivedOrdersStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                        <span><?php echo e(!empty($status) ? ucwords($status) : 'All status'); ?></span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="receivedOrdersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                        <button type="button" onclick="selectDropdown(event, 'receivedOrdersStatusInput', '', 'receivedOrdersStatusButton', 'All status', 'receivedOrdersStatusDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm <?php echo e(empty($status) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100'); ?> rounded-[10px]">All status</button>
                        <?php $__currentLoopData = ['completed' => 'Completed', 'partially received' => 'Partially Received']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" onclick="selectDropdown(event, 'receivedOrdersStatusInput', '<?php echo e($value); ?>', 'receivedOrdersStatusButton', '<?php echo e($label); ?>', 'receivedOrdersStatusDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm <?php echo e(($status ?? '') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100'); ?> rounded-[10px]"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </label>
                <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedOrdersWarehouse">
                    <span class="text-xs font-semibold text-slate-500">Warehouse</span>
                    <input type="hidden" name="warehouse" id="receivedOrdersWarehouseInput" value="<?php echo e($warehouse ?? ''); ?>" />
                    <button type="button" id="receivedOrdersWarehouseButton" onclick="toggleDropdown('receivedOrdersWarehouseDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                        <span><?php echo e(!empty($warehouse) ? ($warehouse === 'main' ? 'Main stock' : 'Service bay') : 'All warehouses'); ?></span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="receivedOrdersWarehouseDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                        <button type="button" onclick="selectDropdown(event, 'receivedOrdersWarehouseInput', '', 'receivedOrdersWarehouseButton', 'All warehouses', 'receivedOrdersWarehouseDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm <?php echo e(empty($warehouse) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100'); ?> rounded-[10px]">All warehouses</button>
                        <?php $__currentLoopData = ['main' => 'Main stock', 'service' => 'Service bay']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" onclick="selectDropdown(event, 'receivedOrdersWarehouseInput', '<?php echo e($value); ?>', 'receivedOrdersWarehouseButton', '<?php echo e($label); ?>', 'receivedOrdersWarehouseDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm <?php echo e(($warehouse ?? '') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100'); ?> rounded-[10px]"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </label>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex w-full justify-center rounded-[10px] bg-[#105f68] px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-[#105f68]/20 hover:bg-[#0c474e]">Filter</button>
                </div>
            </form>

            <div class="mt-6 overflow-hidden rounded-[10px] border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Received</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold"><?php echo e($order->order_number); ?></td>
                                <td class="px-4 py-3"><?php echo e($order->supplier_name); ?></td>
                                <td class="px-4 py-3"><?php echo e(optional($order->updated_at)->format('M j, Y')); ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                        $statusClass = match($order->status) {
                                            'pending approval' => 'bg-amber-100 text-amber-800',
                                            'approved' => 'bg-sky-100 text-sky-800',
                                            'sent to supplier' => 'bg-blue-100 text-blue-800',
                                            'in transit' => 'bg-sky-100 text-sky-800',
                                            'partially received' => 'bg-amber-100 text-amber-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'rejected' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    ?>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold <?php echo e($statusClass); ?>">
                                        <?php echo e(ucwords($order->status)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No received orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4">
                <?php echo e($orders->links()); ?>

            </div>
        </section>
    </div>

    <script>
        function resetDropdownButtonStyles() {
            document.querySelectorAll('[id$="Button"]').forEach(btn => {
                btn.style.borderColor = '';
                btn.style.borderWidth = '';
                btn.style.boxShadow = '';
            });
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            const button = document.getElementById(id.replace('Dropdown', 'Button'));

            allDropdowns.forEach(d => {
                if (d.id !== id) d.classList.add('hidden');
            });

            resetDropdownButtonStyles();

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                if (button) {
                    button.style.borderColor = '#105f68';
                    button.style.borderWidth = '2px';
                    button.style.boxShadow = 'none';
                }
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function selectDropdown(event, inputId, value, buttonId, label, dropdownId, formId) {
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
                event.stopPropagation();
            }

            document.getElementById(inputId).value = value;
            document.getElementById(buttonId).querySelector('span').textContent = label;
            document.getElementById(dropdownId).classList.add('hidden');
            const button = document.getElementById(buttonId);
            if (button) {
                button.style.borderColor = '';
                button.style.borderWidth = '';
                button.style.boxShadow = '';
            }
            document.getElementById(formId).submit();
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
                resetDropdownButtonStyles();
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
<?php endif; ?><?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/purchase_order/received-orders.blade.php ENDPATH**/ ?>