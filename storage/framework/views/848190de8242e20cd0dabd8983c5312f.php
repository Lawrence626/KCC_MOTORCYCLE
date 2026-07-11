<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Supplier Assessment')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Supplier Assessment'))]); ?>
    <div class="space-y-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Supplier Assessment</h1>
                <p class="text-sm text-slate-500 mt-1">Track supplier performance, manage supplier records, and inspect products with pricing at a glance.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <button id="openSupplierModal" class="inline-flex items-center justify-center rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700">
                    Add supplier
                </button>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">
                    Active suppliers · <?php echo e(number_format($quickStats['activeSuppliers'])); ?>

                </span>
                <a href="<?php echo e(route('supplier.assessment.archived')); ?>" class="inline-flex items-center rounded-full bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                    Archive list
                </a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div id="success-toast" class="fixed top-4 right-4 z-50 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-lg">
                <?php echo e(session('success')); ?>

            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('success-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.5s ease';
                        setTimeout(() => toast.remove(), 500);
                    }
                }, 3000);
            </script>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <ul class="list-disc pl-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-1 xl:grid-cols-[360px_minmax(0,1fr)]">
                <aside class="space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Supplier search</p>
                                <h2 class="mt-3 text-lg font-semibold text-slate-900">Find the right partner</h2>
                            </div>
                            <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700"><?php echo e(number_format($supplierSummaries->count())); ?> suppliers</span>
                        </div>

                        <div class="mt-5 space-y-4">
                            <input id="supplierSearch" type="search" placeholder="Search supplier, contact, email" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                            <div class="grid gap-3 sm:grid-cols-1">
                                <p class="text-sm text-slate-600">Search by supplier name, contact person, role, email, phone, or address.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Supplier list</p>
                                <h3 class="mt-2 text-lg font-semibold text-slate-900">Select a supplier</h3>
                            </div>
                            <span id="supplierListCount" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?php echo e(number_format($supplierSummaries->count())); ?> total</span>
                        </div>
                        <div id="supplierList" class="space-y-3 max-h-[calc(100vh-34rem)] overflow-y-auto pr-2"></div>
                        <div id="supplierPagination" class="mt-4 flex flex-col items-center gap-2 sm:flex-row sm:justify-between"></div>
                    </div>
                </aside>

                <main class="space-y-4">
                    <div id="supplierDetailPlaceholder" class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center text-slate-500">
                        <p class="text-lg font-semibold text-slate-900">Supplier details will appear here</p>
                        <p class="mt-3 text-sm text-slate-500">Click a supplier from the left panel to see pricing, performance, delivery reliability, and product coverage.</p>
                    </div>

                    <section id="supplierDetailPanel" class="hidden space-y-6">
                        <div class="rounded-3xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Supplier overview</p>
                                    <h2 id="detailSupplierName" class="mt-3 text-2xl md:text-3xl font-semibold text-slate-900"></h2>
                                    <p id="detailSupplierNotes" class="mt-2 text-sm text-slate-500"></p>
                                    <p id="detailSupplierAddress" class="mt-3 text-sm text-slate-500"></p>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-2">
                                    <div class="rounded-3xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Role</p>
                                        <p id="detailSupplierPosition" class="mt-2 font-semibold"></p>
                                    </div>
                                    <div class="rounded-3xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Primary contact</p>
                                        <p id="detailSupplierContact" class="mt-2"></p>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    <button id="detailEditSupplierButton" type="button" class="w-full sm:w-auto rounded-2xl bg-cyan-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">Edit supplier</button>
                                    <button id="detailArchiveSupplierButton" type="button" class="w-full sm:w-auto rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Archive supplier</button>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Performance score</p>
                                    <p id="detailPerformanceScore" class="mt-3 text-3xl font-semibold text-slate-900"></p>
                                </div>
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">On-time delivery</p>
                                    <p id="detailOnTimeRate" class="mt-3 text-3xl font-semibold text-slate-900"></p>
                                </div>
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Order completion</p>
                                    <p id="detailCompletionRate" class="mt-3 text-3xl font-semibold text-slate-900"></p>
                                </div>
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total products</p>
                                    <p id="detailProductCount" class="mt-3 text-3xl font-semibold text-slate-900"></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Performance summary</p>
                                    <h3 class="mt-2 text-lg md:text-xl font-semibold text-slate-900">Delivery and order reliability</h3>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700" id="detailDeliveredCount"></span>
                                    <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600" id="detailOrdersCount"></span>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Latest orders</p>
                                    <div id="detailOrderHistory" class="mt-4 space-y-3"></div>
                                </div>
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Delivery reliability</p>
                                    <div class="mt-4 space-y-3">
                                        <div>
                                            <p class="text-sm text-slate-600">On-time deliveries</p>
                                            <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                                                <div id="detailOnTimeBar" class="h-full rounded-full bg-emerald-500" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-600">Order completion</p>
                                            <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-200">
                                                <div id="detailCompletionBar" class="h-full rounded-full bg-cyan-500" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                            <div class="mb-4 flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Supplier pricing</p>
                                    <h3 class="mt-2 text-lg md:text-xl font-semibold text-slate-900">Product price list</h3>
                                </div>
                                <span id="detailTotalValue" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"></span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-left text-sm text-slate-700">
                                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                                        <tr>
                                            <th class="px-3 py-3 md:px-4">Product</th>
                                            <th class="px-3 py-3 md:px-4">SKU</th>
                                            <th class="px-3 py-3 md:px-4">Category</th>
                                            <th class="px-3 py-3 md:px-4">Stock</th>
                                            <th class="px-3 py-3 md:px-4">Unit price</th>
                                            <th class="px-3 py-3 md:px-4">Restock</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailProductTable" class="divide-y divide-slate-200 bg-white"></tbody>
                                </table>
                            </div>
                            <div id="productPagination" class="mt-4 flex flex-col items-center gap-2 sm:flex-row sm:justify-between"></div>
                        </div>
                    </section>
                </main>
            </div>
        </div>

        <div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 id="supplierModalTitle" class="text-xl font-semibold text-slate-900">Add supplier</h2>
                    <p id="supplierModalSubtitle" class="mt-1 text-sm text-slate-500">Create a supplier record and link products automatically.</p>
                </div>
                <button type="button" id="closeSupplierModal" class="text-slate-400 transition hover:text-slate-700">✕</button>
            </div>

            <form id="supplierForm" method="POST" action="<?php echo e(route('supplier.assessment.store')); ?>" class="space-y-4 px-6 py-6">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="supplierFormMethod" value="POST" />
                <input type="hidden" name="supplier_id" id="supplierId" value="" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">
                        Supplier name
                        <input id="supplierNameInput" name="name" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" required />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Contact person
                        <input id="supplierContactInput" name="contact_person" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">
                        Email address
                        <input id="supplierEmailInput" name="email" type="email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Phone number
                        <input id="supplierPhoneInput" name="phone" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">
                        Contact position
                        <input id="supplierPositionInput" name="contact_position" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Address
                        <input id="supplierAddressInput" name="address" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-1">
                    <label class="block text-sm font-medium text-slate-700">
                        Notes
                        <input id="supplierNotesInput" name="notes" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" id="cancelSupplierModal" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button>
                    <button type="submit" id="supplierModalSubmit" class="rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">Save supplier</button>
                </div>
            </form>
        </div>
    </div>

    <div id="productsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 id="productsModalTitle" class="text-xl font-semibold text-slate-900">Supplier products</h2>
                    <p id="productsModalSubtitle" class="mt-1 text-sm text-slate-500">Review the products, pricing, and stock linked to this supplier.</p>
                </div>
                <button type="button" id="closeProductsModal" class="text-slate-400 transition hover:text-slate-700">✕</button>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Unit price</th>
                                <th class="px-4 py-3">Restock</th>
                            </tr>
                        </thead>
                        <tbody id="productsModalTableBody" class="divide-y divide-slate-200 bg-white"></tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Prices and stock are pulled from current product records.</p>
                    <button type="button" id="closeProductsModalButton" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Close</button>
                </div>
            </div>
        </div>
    </div>

    <form id="archiveSupplierForm" method="POST" class="hidden">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const supplierSummaries = <?php echo json_encode($supplierSummaries, 15, 512) ?>;
            const supplierModal = document.getElementById('supplierModal');
            const productsModal = document.getElementById('productsModal');
            const supplierForm = document.getElementById('supplierForm');
            const supplierModalTitle = document.getElementById('supplierModalTitle');
            const supplierModalSubtitle = document.getElementById('supplierModalSubtitle');
            const supplierFormMethod = document.getElementById('supplierFormMethod');
            const supplierId = document.getElementById('supplierId');
            const supplierNameInput = document.getElementById('supplierNameInput');
            const supplierContactInput = document.getElementById('supplierContactInput');
            const supplierPositionInput = document.getElementById('supplierPositionInput');
            const supplierAddressInput = document.getElementById('supplierAddressInput');
            const supplierEmailInput = document.getElementById('supplierEmailInput');
            const supplierPhoneInput = document.getElementById('supplierPhoneInput');
            const supplierNotesInput = document.getElementById('supplierNotesInput');
            const supplierModalSubmit = document.getElementById('supplierModalSubmit');
            const openSupplierModalButton = document.getElementById('openSupplierModal');
            const closeSupplierModalButton = document.getElementById('closeSupplierModal');
            const cancelSupplierModalButton = document.getElementById('cancelSupplierModal');
            const supplierSearch = document.getElementById('supplierSearch');
            const clearFiltersButton = document.getElementById('clearFilters');
            const productsModalTitle = document.getElementById('productsModalTitle');
            const productsModalSubtitle = document.getElementById('productsModalSubtitle');
            const productsModalTableBody = document.getElementById('productsModalTableBody');
            const closeProductsModal = document.getElementById('closeProductsModal');
            const closeProductsModalButton = document.getElementById('closeProductsModalButton');
            const supplierList = document.getElementById('supplierList');
            const supplierListCount = document.getElementById('supplierListCount');
            const supplierPagination = document.getElementById('supplierPagination');
            const supplierDetailPanel = document.getElementById('supplierDetailPanel');
            const supplierDetailPlaceholder = document.getElementById('supplierDetailPlaceholder');
            const detailSupplierName = document.getElementById('detailSupplierName');
            const detailSupplierNotes = document.getElementById('detailSupplierNotes');
            const detailSupplierPosition = document.getElementById('detailSupplierPosition');
            const detailSupplierAddress = document.getElementById('detailSupplierAddress');
            const detailSupplierContact = document.getElementById('detailSupplierContact');
            const detailPerformanceScore = document.getElementById('detailPerformanceScore');
            const detailOnTimeRate = document.getElementById('detailOnTimeRate');
            const detailCompletionRate = document.getElementById('detailCompletionRate');
            const detailProductCount = document.getElementById('detailProductCount');
            const detailDeliveredCount = document.getElementById('detailDeliveredCount');
            const detailOrdersCount = document.getElementById('detailOrdersCount');
            const detailOrderHistory = document.getElementById('detailOrderHistory');
            const detailOnTimeBar = document.getElementById('detailOnTimeBar');
            const detailCompletionBar = document.getElementById('detailCompletionBar');
            const detailProductTable = document.getElementById('detailProductTable');
            const detailTotalValue = document.getElementById('detailTotalValue');
            const productPagination = document.getElementById('productPagination');
            const detailEditSupplierButton = document.getElementById('detailEditSupplierButton');
            const detailArchiveSupplierButton = document.getElementById('detailArchiveSupplierButton');
            const archiveSupplierForm = document.getElementById('archiveSupplierForm');
            let activeSupplier = null;
            let currentPage = 1;
            const itemsPerPage = 3;
            let currentProductPage = 1;
            const productsPerPage = 10;

            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 rounded-2xl border p-4 text-sm shadow-lg transition-opacity duration-500 ${type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800'}`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }

            function resetSupplierForm() {
                supplierForm.reset();
                supplierForm.action = '<?php echo e(route('supplier.assessment.store')); ?>';
                supplierFormMethod.value = 'POST';
                supplierId.value = '';
                supplierModalTitle.textContent = 'Add supplier';
                supplierModalSubtitle.textContent = 'Create a supplier record and link products automatically.';
                supplierModalSubmit.textContent = 'Save supplier';
            }

            function fillSupplierForm(supplier) {
                supplierModalTitle.textContent = supplier.id ? 'Edit supplier' : 'Add supplier';
                supplierModalSubtitle.textContent = supplier.id
                    ? 'Update the supplier details and product links.'
                    : 'Create a supplier record and keep product links intact.';
                supplierFormMethod.value = supplier.id ? 'PATCH' : 'POST';
                supplierId.value = supplier.id || '';
                supplierNameInput.value = supplier.name || '';
                supplierContactInput.value = supplier.contact_person || '';
                supplierPositionInput.value = supplier.contact_position || '';
                supplierAddressInput.value = supplier.address || '';
                supplierEmailInput.value = supplier.email || '';
                supplierPhoneInput.value = supplier.phone || '';
                supplierNotesInput.value = supplier.notes || '';
                supplierForm.action = supplier.id
                    ? '<?php echo e(url('supplier-assessment/suppliers')); ?>/' + supplier.id
                    : '<?php echo e(route('supplier.assessment.store')); ?>';
                supplierModalSubmit.textContent = supplier.id ? 'Update supplier' : 'Save supplier';
            }

            function renderSupplierList() {
                supplierList.innerHTML = '';
                let visibleCount = 0;
                const filteredSuppliers = [];

                supplierSummaries.forEach(supplier => {
                    const name = supplier.name.toLowerCase();
                    const contact = (supplier.contact_person || '').toLowerCase();
                    const contactPosition = (supplier.contact_position || '').toLowerCase();
                    const email = (supplier.email || supplier.phone || supplier.address || '').toLowerCase();
                    const searchValue = supplierSearch.value.trim().toLowerCase();

                    const matchesSearch = [name, contact, contactPosition, email].some(value => value.includes(searchValue));
                    const isVisible = matchesSearch;

                    if (isVisible) {
                        filteredSuppliers.push(supplier);
                    }
                });

                const totalPages = Math.ceil(filteredSuppliers.length / itemsPerPage);
                currentPage = Math.min(currentPage, totalPages) || 1;
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const paginatedSuppliers = filteredSuppliers.slice(startIndex, endIndex);

                paginatedSuppliers.forEach(supplier => {
                    visibleCount += 1;
                    const card = document.createElement('div');
                    card.dataset.supplierName = supplier.name;
                    card.className = 'supplier-card w-full rounded-3xl border border-slate-200 bg-white p-3 md:p-4 text-left shadow-sm transition hover:border-cyan-300 hover:shadow-md';
                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-2 md:gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">${supplier.name}</p>
                                <p class="mt-1 text-xs text-slate-500 truncate">${supplier.contact_person || 'No contact'} · ${supplier.email || supplier.phone || 'No email'}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 whitespace-nowrap">${supplier.contact_position || 'Supplier'}</span>
                        </div>
                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            <div class="rounded-3xl bg-slate-50 p-2 md:p-3 text-xs text-slate-600">
                                <p class="font-semibold text-slate-900">${supplier.product_count}</p>
                                <p>Products</p>
                            </div>
                            <div class="rounded-3xl bg-slate-50 p-2 md:p-3 text-xs text-slate-600">
                                <p class="font-semibold text-slate-900">${supplier.performance_score}</p>
                                <p>Performance</p>
                            </div>
                        </div>
                    `;
                    supplierList.appendChild(card);
                });

                supplierListCount.textContent = `${visibleCount} shown`;

                // Render pagination
                if (totalPages > 1) {
                    supplierPagination.innerHTML = `
                        <div class="text-sm text-slate-500">
                            Page ${currentPage} of ${totalPages}
                        </div>
                        <div class="flex items-center gap-2 flex-wrap justify-center">
                            <button data-page="${currentPage - 1}" class="pagination-btn px-3 py-1 text-sm ${currentPage === 1 ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:text-slate-900'}" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
                            ${Array.from({length: totalPages}, (_, i) => i + 1).map(page => `
                                <button data-page="${page}" class="pagination-btn px-3 py-1 text-sm ${page === currentPage ? 'font-medium text-white bg-cyan-600 rounded' : 'text-slate-600 hover:text-slate-900'}">${page}</button>
                            `).join('')}
                            <button data-page="${currentPage + 1}" class="pagination-btn px-3 py-1 text-sm ${currentPage === totalPages ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:text-slate-900'}" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
                        </div>
                    `;
                } else {
                    supplierPagination.innerHTML = '';
                }
            }

            window.changePage = function(page) {
                currentPage = page;
                renderSupplierList();
            }

            window.changeProductPage = function(page) {
                currentProductPage = page;
                if (activeSupplier) {
                    setSupplierDetail(activeSupplier.name);
                }
            }

            supplierList.addEventListener('click', event => {
                const card = event.target.closest('.supplier-card');

                if (card) {
                    setSupplierDetail(card.dataset.supplierName);
                }
            });

            detailEditSupplierButton.addEventListener('click', () => {
                if (!activeSupplier) {
                    return;
                }
                fillSupplierForm(activeSupplier);
                openModal(supplierModal);
            });

            detailArchiveSupplierButton.addEventListener('click', () => {
                if (!activeSupplier) {
                    return;
                }
                if (!activeSupplier.id) {
                    showToast('This supplier is not yet saved as a record and cannot be archived. Please add it first.', 'error');
                    return;
                }
                archiveSupplierForm.action = '<?php echo e(url('supplier-assessment/suppliers')); ?>/' + activeSupplier.id;
                if (confirm(`Archive "${activeSupplier.name}"? This will remove it from active supplier listings.`)) {
                    archiveSupplierForm.submit();
                }
            });

            function setSupplierDetail(supplierName) {
                const supplier = supplierSummaries.find(item => item.name === supplierName);
                if (!supplier) {
                    return;
                }

                supplierDetailPlaceholder.classList.add('hidden');
                supplierDetailPanel.classList.remove('hidden');

                // Reset product page when switching to a different supplier
                if (activeSupplier && activeSupplier.name !== supplierName) {
                    currentProductPage = 1;
                }

                detailSupplierName.textContent = supplier.name;
                detailSupplierNotes.textContent = supplier.notes || 'No additional notes provided.';
                detailSupplierPosition.textContent = supplier.contact_position || 'Supplier';
                detailSupplierAddress.textContent = supplier.address ? `📍 ${supplier.address}` : '';
                detailSupplierContact.textContent = supplier.contact_person ? `${supplier.contact_person} · ${supplier.email || supplier.phone || 'No contact info'}` : (supplier.email || supplier.phone || 'No contact info');
                activeSupplier = supplier;
                detailPerformanceScore.textContent = `${supplier.performance_score}/100`;
                detailOnTimeRate.textContent = `${supplier.on_time_rate}%`;
                detailCompletionRate.textContent = `${supplier.completion_rate}%`;
                detailProductCount.textContent = supplier.product_count;
                detailDeliveredCount.textContent = `${supplier.delivered_orders_count} delivered`;
                detailOrdersCount.textContent = `${supplier.orders_count} orders`;
                detailOnTimeBar.style.width = `${supplier.on_time_rate}%`;
                detailCompletionBar.style.width = `${supplier.completion_rate}%`;
                detailTotalValue.textContent = `₱${Number(supplier.total_value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                detailOrderHistory.innerHTML = '';
                if (!supplier.orders.length) {
                    detailOrderHistory.innerHTML = '<div class="rounded-3xl bg-white p-4 text-sm text-slate-500">No order history available for this supplier.</div>';
                } else {
                    supplier.orders.slice(0, 3).forEach(order => {
                        detailOrderHistory.insertAdjacentHTML('beforeend', `
                            <div class="rounded-3xl bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-semibold text-slate-900">${order.order_number}</p>
                                    <span class="text-xs ${order.status.toLowerCase() === 'delivered' ? 'text-emerald-700' : 'text-slate-500'}">${order.status}</span>
                                </div>
                                <div class="mt-2 text-sm text-slate-600">
                                    Expected: ${order.expected_delivery_date || 'Unknown'} · Updated: ${order.updated_at || 'Unknown'}
                                </div>
                                <div class="mt-3 text-sm font-semibold text-slate-900">₱${Number(order.total_amount).toFixed(2)}</div>
                            </div>
                        `);
                    });
                }

                detailProductTable.innerHTML = '';
                if (!supplier.products.length) {
                    detailProductTable.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No product records linked to this supplier.</td></tr>';
                    productPagination.innerHTML = '';
                } else {
                    const totalProductPages = Math.ceil(supplier.products.length / productsPerPage);
                    const productStartIndex = (currentProductPage - 1) * productsPerPage;
                    const productEndIndex = productStartIndex + productsPerPage;
                    const paginatedProducts = supplier.products.slice(productStartIndex, productEndIndex);

                    paginatedProducts.forEach(product => {
                        detailProductTable.insertAdjacentHTML('beforeend', `
                            <tr class="border-b border-slate-200">
                                <td class="px-4 py-4 font-medium text-slate-900">${product.name}</td>
                                <td class="px-4 py-4 text-slate-600">${product.sku}</td>
                                <td class="px-4 py-4 text-slate-600">${product.category || 'Uncategorized'}</td>
                                <td class="px-4 py-4 text-slate-900">${product.stock_quantity}</td>
                                <td class="px-4 py-4 text-slate-900">₱${Number(product.price).toFixed(2)}</td>
                                <td class="px-4 py-4 text-slate-600">${product.last_restock_date || 'N/A'}</td>
                            </tr>
                        `);
                    });

                    // Render product pagination
                    if (totalProductPages > 1) {
                        productPagination.innerHTML = `
                            <div class="text-sm text-slate-500">
                                Page ${currentProductPage} of ${totalProductPages}
                            </div>
                            <div class="flex items-center gap-2 flex-wrap justify-center">
                                <button type="button" onclick="window.changeProductPage(${currentProductPage - 1})" ${currentProductPage === 1 ? 'disabled' : ''} class="px-3 py-1 text-sm ${currentProductPage === 1 ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:text-slate-900'}">Previous</button>
                                ${Array.from({length: totalProductPages}, (_, i) => i + 1).map(page => `
                                    <button type="button" onclick="window.changeProductPage(${page})" class="px-3 py-1 text-sm ${page === currentProductPage ? 'font-medium text-white bg-cyan-600 rounded' : 'text-slate-600 hover:text-slate-900'}">${page}</button>
                                `).join('')}
                                <button type="button" onclick="window.changeProductPage(${currentProductPage + 1})" ${currentProductPage === totalProductPages ? 'disabled' : ''} class="px-3 py-1 text-sm ${currentProductPage === totalProductPages ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:text-slate-900'}">Next</button>
                            </div>
                        `;
                    } else {
                        productPagination.innerHTML = '';
                    }
                }
            }

            function openProductsModal(supplierName) {
                const supplier = supplierSummaries.find(item => item.name === supplierName);
                if (!supplier) {
                    return;
                }

                productsModalTitle.textContent = `Products from ${supplier.name}`;
                productsModalSubtitle.textContent = `${supplier.product_count} product${supplier.product_count === 1 ? '' : 's'} linked to this supplier.`;
                productsModalTableBody.innerHTML = '';

                if (!supplier.products.length) {
                    productsModalTableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No product records are currently linked to this supplier.</td></tr>';
                } else {
                    supplier.products.forEach(product => {
                        productsModalTableBody.insertAdjacentHTML('beforeend', `
                            <tr class="border-b border-slate-200">
                                <td class="px-4 py-4 font-medium text-slate-900">${product.name}</td>
                                <td class="px-4 py-4 text-slate-600">${product.sku}</td>
                                <td class="px-4 py-4 text-slate-600">${product.category}</td>
                                <td class="px-4 py-4 text-slate-900">${product.stock_quantity}</td>
                                <td class="px-4 py-4 text-slate-900">₱${Number(product.price).toFixed(2)}</td>
                                <td class="px-4 py-4 text-slate-600">${product.last_restock_date || 'N/A'}</td>
                            </tr>
                        `);
                    });
                }

                openModal(productsModal);
            }

            openSupplierModalButton.addEventListener('click', () => {
                resetSupplierForm();
                openModal(supplierModal);
            });

            [closeSupplierModalButton, cancelSupplierModalButton].forEach(button => {
                button.addEventListener('click', () => closeModal(supplierModal));
            });


            supplierSearch.addEventListener('input', renderSupplierList);

            // Event delegation for supplier pagination
            supplierPagination.addEventListener('click', (e) => {
                const button = e.target.closest('.pagination-btn');
                if (button) {
                    e.preventDefault();
                    const page = parseInt(button.dataset.page);
                    if (page >= 1 && page <= Math.ceil(supplierSummaries.length / itemsPerPage)) {
                        currentPage = page;
                        renderSupplierList();
                    }
                }
            });

            [closeProductsModal, closeProductsModalButton].forEach(button => {
                button.addEventListener('click', () => closeModal(productsModal));
            });

            renderSupplierList();

            // Auto-select supplier if passed in URL
            const urlParams = new URLSearchParams(window.location.search);
            const selectedSupplier = urlParams.get('selected_supplier');
            if (selectedSupplier) {
                setTimeout(() => {
                    setSupplierDetail(selectedSupplier);
                }, 100);
            }
        </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\Users\Admin\Desktop\WEQW\KCC_MOTORCYCLE\resources\views/supplier_assessment/supplier_asses.blade.php ENDPATH**/ ?>