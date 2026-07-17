<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('Warehouse Management')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Warehouse Management'))]); ?>
    <style>
        :root {
            --brand: #0f766e; /* professional teal */
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #6b7280;
            --border: rgba(148,163,184,0.2);
        }
        .wm-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .wm-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .wm-location { background: #f8fafc; border: 1px dashed rgba(15,118,110,0.16); }
        .product-chip { background: #ffffff; border: 1px solid rgba(16,185,129,0.18); color: var(--brand-dark); border-radius: 0.9rem; display:flex; flex-direction:column; gap:0; overflow:hidden; box-shadow: 0 2px 8px rgba(15,118,110,0.06); }
        .product-chip .chip-header { background: linear-gradient(90deg, #ecfdf5, #d1fae5); padding: 0.45rem 0.75rem; border-bottom: 1px solid rgba(16,185,129,0.14); }
        .product-chip .chip-body { padding: 0.5rem 0.75rem; display:flex; flex-direction:column; gap:0.18rem; }
        .product-chip .chip-row { display:flex; align-items:baseline; gap:0.3rem; font-size:0.72rem; }
        .product-chip .chip-label { color:#64748b; font-weight:600; min-width:4.8rem; flex-shrink:0; }
        .product-chip .chip-value { color:#0f172a; font-weight:500; word-break:break-word; }
        .product-chip .chip-value.sku { font-family: monospace; font-size:0.68rem; color:#0f766e; font-weight:700; }
        .product-chip .chip-value.price { color:#065f46; font-weight:700; }
        .product-chip .chip-value.qty { color:#1e40af; font-weight:700; }
        .product-chip .chip-desc { font-size:0.78rem; font-weight:700; color:#0f172a; }
        .warehouse-shelves { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: minmax(220px, auto); }
        .map-unit { min-height: 220px; }
        .modal-panel { width: min(100%, 960px); border-radius: 1.5rem; background: #ffffff; box-shadow: 0 28px 80px rgba(15,23,42,0.18); }
        .modal-field { border: 1px solid rgba(148,163,184,0.35); background: #f8fafc; border-radius: 0.85rem; }
        .modal-field input,
        .modal-field select { border: none; background: transparent; outline: none; }
        .modal-field label { color: #334155; }
        .product-row-card { background: #f8fafc; border: 1px solid rgba(148,163,184,0.2); border-radius: 1rem; padding: 0.85rem; }
        .product-row-card .row-grid { gap: 0.75rem; }
        .product-row-card .product-sku,
        .product-row-card .product-brand,
        .product-row-card .product-compatible,
        .product-row-card .product-qty,
        .product-row-card .product-price,
        .product-row-card .product-select { background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 0.85rem; }
        .product-row-card .product-sku { background: #f1f5f9; }
        .product-row-card .product-name {
            background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 0.85rem; padding: 0.75rem;
        }
        .product-row-card .product-select,
        .product-row-card .product-sku,
        .product-row-card .product-brand,
        .product-row-card .product-compatible,
        .product-row-card .product-qty,
        .product-row-card .product-price { padding: 0.75rem; }
        .remove-product-row { color: #ef4444; transition: color 0.2s ease; }
        .remove-product-row:hover { color: #b91c1c; }
        .modal-actions { border-top: 1px solid rgba(148,163,184,0.25); padding-top: 0.85rem; }
        .modal-footer-button { border-radius: 0.85rem; padding: 0.75rem 1.2rem; font-weight: 600; }
        .modal-footer-button.primary { background: var(--brand); color: #fff; }
        .modal-footer-button.secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); }
        .product-row-card label { font-size: 0.72rem; }
        .product-row-card .remove-product-row { font-size: 0.85rem; }
        .modal-panel { max-height: 95vh; overflow: auto; }
        #modal-product-rows { max-height: 560px; overflow-y: auto; }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.85rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f766e; color: #fff; border-radius: 1rem; box-shadow: 0 18px 50px rgba(15,23,42,0.18); padding: 0.85rem 1rem; font-size: 0.95rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f766e; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 1rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 1024px) { .warehouse-shelves { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 770px) {
            .warehouse-shelves { grid-template-columns: 1fr; }
            .product-row-card { padding: 0.85rem; }
            .map-unit { min-height: 200px; }
        }
    </style>

    <div class="space-y-6">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Warehouse Management</h1>
                <p class="mt-2 text-sm text-gray-500">Track and manage storage locations and products across your warehouses.</p>
            </div>
                <div class="flex items-center space-x-3">
                <input id="wm-search" type="search" placeholder="Search product or SKU..." class="px-5 py-3 text-base border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300" />
                <button id="view-archived-shelves" type="button" class="px-3 py-2 border rounded-xl text-sm">Archived Shelves</button>
                <button id="view-archived-warehouses" type="button" class="px-3 py-2 border rounded-xl text-sm">Archived Warehouses</button>
                <button id="add-warehouse-button" type="button" class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl text-sm font-medium shadow-md hover:from-blue-700 hover:to-blue-800 transition">Add Warehouse</button>
                <button id="add-shelf-button" type="button" class="inline-flex items-center px-3 py-2 wm-badge rounded-xl text-sm font-medium">Add Shelf</button>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <label class="text-sm font-medium text-slate-700">Select a warehouse:</label>
            <select id="warehouse-selector" class="px-4 py-3 border rounded-xl bg-white text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($wh['id']); ?>"><?php echo e($wh['name']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700">Product Description:</label>
                <select id="wm-product-description-filter" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                    <option value="">All Descriptions</option>
                    <?php
                        $descriptions = \App\Models\ProductDescription::where('is_active', true)->orderBy('name')->get();
                        foreach($descriptions as $desc):
                    ?>
                        <option value="<?php echo e($desc->name); ?>"><?php echo e($desc->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700">Brand:</label>
                <select id="wm-brand-filter" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                    <option value="">All Brands</option>
                    <?php
                        $brands = \App\Models\Product::where('is_archived', false)
                            ->whereNotNull('brand')
                            ->where('brand', '!=', '')
                            ->distinct()
                            ->orderBy('brand')
                            ->pluck('brand')
                            ->toArray();
                        foreach($brands as $brand):
                    ?>
                        <option value="<?php echo e($brand); ?>"><?php echo e($brand); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button id="wm-clear-filters" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition">
                Clear Filters
            </button>
            <div class="flex items-center gap-3">
                <div class="rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                    <p class="uppercase tracking-[0.18em] text-xs text-slate-400">Products</p>
                    <p id="selectedWarehouseProducts" class="mt-1 text-lg font-semibold text-slate-900">0</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                    <p class="uppercase tracking-[0.18em] text-xs text-slate-400">Empty Slots</p>
                    <p id="selectedWarehouseEmptySlots" class="mt-1 text-lg font-semibold text-slate-900">0</p>
                </div>
            </div>
        </div>


        <div class="grid gap-6 mt-4">

        
        <div id="pending-arrivals-panel" class="wm-card rounded-xl border-l-4 border-amber-400 bg-amber-50 overflow-hidden">
            
            <button
                type="button"
                id="pending-arrivals-toggle"
                class="w-full flex items-center justify-between px-5 py-3 hover:bg-amber-100/60 transition"
                aria-expanded="<?php echo e(count($pendingArrivals) > 0 ? 'true' : 'false'); ?>"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-amber-900">New Stock: Pending Warehouse Assignment</span>
                    <span id="pending-arrivals-count" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?php echo e(count($pendingArrivals) > 0 ? 'bg-amber-300 text-amber-900' : 'bg-slate-100 text-slate-500'); ?>">
                        <?php echo e(count($pendingArrivals)); ?> pending
                    </span>
                </div>
                <svg id="pending-arrivals-chevron" class="w-4 h-4 text-amber-600 transition-transform duration-200 <?php echo e(count($pendingArrivals) > 0 ? 'rotate-180' : ''); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            
            <div id="pending-arrivals-body" class="<?php echo e(count($pendingArrivals) > 0 ? '' : 'hidden'); ?> px-5 pb-5">
                <p class="text-xs text-amber-700 mb-3">Items that arrived from confirmed purchase orders are listed here. Assign each one to a warehouse before shelving.</p>

                <?php if(count($pendingArrivals) > 0): ?>
                <div class="overflow-x-auto rounded-lg border border-amber-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-100 text-sm" id="pending-arrivals-table">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">SKU</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">PO #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Arrived</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="pending-arrivals-tbody">
                            <?php $__currentLoopData = $pendingArrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arrival): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr id="arrival-row-<?php echo e($arrival['id']); ?>" class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($arrival['product_name']); ?></td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-600"><?php echo e($arrival['sku'] ?? 'â€”'); ?></td>
                                <td class="px-4 py-3 text-center font-semibold text-slate-800"><?php echo e($arrival['quantity']); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($arrival['purchase_order_number'] ?? 'â€”'); ?></td>
                                <td class="px-4 py-3 text-slate-600"><?php echo e($arrival['supplier_name'] ?? 'â€”'); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($arrival['arrived_at']); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        class="assign-arrival-btn inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition shadow-sm"
                                        data-id="<?php echo e($arrival['id']); ?>"
                                        data-name="<?php echo e($arrival['product_name']); ?>"
                                        data-qty="<?php echo e($arrival['quantity']); ?>"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        Assign
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="flex flex-col items-center justify-center rounded-lg border border-dashed border-amber-200 bg-white py-8 text-center">
                    <svg class="w-8 h-8 text-amber-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold text-slate-500">All caught up!</p>
                    <p class="text-xs text-slate-400 mt-1">New stocks from confirmed purchase orders will appear here.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        

            <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="wm-card rounded-lg p-4 shadow-sm wh-card" data-id="<?php echo e($wh['id']); ?>" style="display:none;">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-md flex items-center justify-center text-white font-semibold wm-badge"><?php echo e(strtoupper(substr($wh['code'], -1))); ?></div>
                                <div>
                                    <div class="text-lg font-semibold text-slate-900"><?php echo e($wh['name']); ?></div>
                                    <div class="text-xs text-gray-500">Code: <?php echo e($wh['code']); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <select class="warehouse-action-select text-sm text-slate-700 px-4 py-2 border border-gray-200 rounded-full bg-white hover:bg-gray-50 cursor-pointer appearance-none pr-8" data-id="<?php echo e($wh['id']); ?>">
                                <option value="">Actions</option>
                                <option value="archive-warehouse">Archive Warehouse</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="map-container border rounded-md p-3 bg-gray-50">
                            <div class="warehouse-shelves grid gap-6" data-id="<?php echo e($wh['id']); ?>"></div>
                            <div class="pagination mt-4 flex items-center justify-between text-sm text-gray-600">
                                <button type="button" class="prev-page px-3 py-2 border rounded-md bg-white" data-id="<?php echo e($wh['id']); ?>">Previous</button>
                                <div class="page-info" data-id="<?php echo e($wh['id']); ?>">Page 1 of 1</div>
                                <button type="button" class="next-page px-3 py-2 border rounded-md bg-white" data-id="<?php echo e($wh['id']); ?>">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    </div>

    <script>
        window.WarehouseData = {
            warehouses: <?php echo json_encode($warehouses, 15, 512) ?>,
            products: <?php echo json_encode($products, 15, 512) ?>,
            routes: {
                addProduct: '<?php echo e(route('warehouse.management.add_product')); ?>',
                saveShelf: '<?php echo e(route('warehouse.management.save_shelf')); ?>',
                mobileScanner: '<?php echo e(route("warehouse.mobile.scanner")); ?>'
            },
            csrfToken: '<?php echo e(csrf_token()); ?>'
        };

    </script>

    <div id="modal-backdrop" class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50 px-4 py-8">
        <div class="modal-panel p-6 max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 id="modal-title" class="text-2xl font-semibold text-slate-900">Modal Title</h2>
                <button id="modal-close" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition hover:bg-slate-200">✕</button>
            </div>

            <form id="modal-form" class="space-y-6">
                <input type="hidden" id="modal-warehouse-index" />
                <input type="hidden" id="modal-slot" />
                <input type="hidden" id="modal-mode" value="addShelf" />

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_1fr]">
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Warehouse</label>
                            <select id="modal-warehouse-select" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900">
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($index); ?>"><?php echo e($wh['name']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Shelf Name</label>
                            <input id="modal-shelf-name" type="text" class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300" placeholder="Enter shelf name" />
                        </div>
                        <div class="modal-field p-4">
                            <label class="block text-sm font-semibold text-slate-800 mb-2">Shelf Capacity</label>
                            <input id="modal-shelf-capacity" type="number" min="1" value="10"
                                   class="block w-full px-4 py-3 text-sm text-slate-900 rounded-md border border-slate-300"
                                   placeholder="Enter shelf capacity" />
                            <p class="text-xs text-slate-400 mt-1">ðŸ’¡ You can increase this to allow more products per shelf. Default is 10.</p>
                        </div>
                    </div>

                    <div class="modal-field p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">Products</p>
                                <p class="text-xs text-slate-500 mt-1">Choose existing inventory items, quantity, and price before saving.</p>
                            </div>
                            <button type="button" id="modal-add-product-row" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add product</button>
                        </div>
                        <div id="modal-product-rows" class="grid gap-3 max-h-[540px] overflow-y-auto"></div>
                    </div>
                </div>

                <div class="modal-actions flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" id="modal-cancel" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Save shelf</button>
                </div>
            </form>
        </div>
    </div>

    <div id="archived-backdrop" class="fixed inset-0 bg-black/30 hidden items-center justify-center z-50 px-4 py-8">
        <div class="modal-panel p-6 max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Archived Shelves</h3>
                <button id="archived-close" class="text-slate-600">âœ•</button>
            </div>
            <div id="archived-list" class="space-y-2 max-h-64 overflow-y-auto border rounded-md p-3 bg-surface"></div>

            <div class="archived-pagination mt-4 flex items-center justify-center gap-3">
                <button id="archived-prev" class="px-3 py-1 rounded-md border text-sm">Previous</button>
                <div id="archived-page-info" class="text-sm text-slate-600">Page 1 of 1</div>
                <button id="archived-next" class="px-3 py-1 rounded-md border text-sm">Next</button>
            </div>

            <div class="mt-4 text-right">
                <button id="archived-done" class="px-4 py-2 rounded-md bg-slate-100">Close</button>
            </div>
        </div>
    </div>

        <div class="mt-4 text-sm text-gray-500">
            <strong class="text-slate-800">Note:</strong> This page is now backed by live inventory data from the system. Shelves display actual products and quantities where available.
        </div>
    </div>

    <!-- QR Code Generation Modal -->
    <div id="wm-qr-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-emerald-600 to-cyan-600 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Generate QR Codes</h2>
                    </div>
                    <button onclick="document.getElementById('wm-qr-modal').style.display='none'" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Product Rows -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">New Stock Products</p>
                            <p class="text-xs text-slate-500 mt-1">Enter product names to generate QR codes for multiple items at once.</p>
                        </div>
                        <button type="button" onclick="wmAddProductRow()" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add Product</button>
                    </div>
                    <div id="wm-product-rows" class="grid gap-3 max-h-[300px] overflow-y-auto"></div>
                </div>

                <!-- Restock Date Display (Fixed to today) -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                    <label class="block text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Restock Date (Today)
                    </label>
                    <input type="text" id="wm-qr-restock-date" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-slate-50 text-sm transition" readonly>
                </div>

                <!-- QR Code Preview -->
                <div id="wm-qr-preview" class="space-y-4">
                    <div id="wm-qr-loading" class="hidden text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div>
                        <p class="text-sm text-slate-600">Generating QR codes...</p>
                    </div>
                    <!-- QR codes will be generated here -->
                </div>

                <!-- Pagination -->
                <div id="wm-qr-pagination" class="hidden flex items-center justify-between pt-4 border-t border-slate-200">
                    <button onclick="wmPrevPage()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-prev-page">Previous</button>
                    <span id="wm-page-info" class="text-sm text-slate-600">Page 1 of 1</span>
                    <button onclick="wmNextPage()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-next-page">Next</button>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button onclick="wmGenerateAllQR()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold hover:from-emerald-700 hover:to-emerald-800 transition shadow-md">
                        Generate All QR Codes
                    </button>
                    <button onclick="wmPrintAllQR()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-slate-600 to-slate-700 text-white font-semibold hover:from-slate-700 hover:to-slate-800 transition shadow-md">
                        Print All QR Codes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Stock Details Modal -->
    <div id="wm-new-stock-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
            <div class="bg-gradient-to-r from-emerald-600 to-cyan-600 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">New Stock Details</h2>
                    </div>
                    <button onclick="wmCloseNewStockModal()" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <p class="text-sm text-slate-600"><strong>Product Name:</strong> <span id="wm-ns-product-name">-</span></p>
                    <p class="text-sm text-slate-600"><strong>SKU:</strong> <span id="wm-ns-sku">-</span></p>
                    <p class="text-sm text-slate-600"><strong>Restock Date:</strong> <span id="wm-ns-restock-date">-</span></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Price</label>
                    <input type="number" id="wm-ns-price" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" placeholder="Enter price" step="0.01">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity</label>
                    <input type="number" id="wm-ns-quantity" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" placeholder="Enter quantity" min="1">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                    <select id="wm-ns-category" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                        <option value="">Select category</option>
                        <option value="Parts">Parts</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Oil">Oil</option>
                        <option value="Tires">Tires</option>
                        <option value="Battery">Battery</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description (Optional)</label>
                    <textarea id="wm-ns-description" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" rows="2" placeholder="Enter description"></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button onclick="wmCloseNewStockModal()" class="flex-1 px-4 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                    <button onclick="wmSaveNewStock()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold hover:from-emerald-700 hover:to-emerald-800 transition shadow-md">Save & Add to Warehouse</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Scanner Modal -->
    <div id="wm-scan-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4">
            <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-lg p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">QR Code Scanner</h2>
                    </div>
                    <button onclick="wmCloseScanner()" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-col md:flex-row p-6 gap-6">
                <!-- Scanner Section -->
                <div class="flex-1">
                    <div id="wm-scanner-reader" class="w-full bg-black rounded-xl overflow-hidden min-h-[400px]"></div>
                    <div id="wm-scanner-status" class="text-center text-sm text-slate-600 mt-2">Position QR code within the frame</div>
                </div>

                <!-- Scanned Items Section -->
                <div class="w-full md:w-80">
                    <div id="wm-scanned-items" class="hidden">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Scanned Items (<span id="wm-scan-count">0</span>)</h3>
                            <button onclick="wmClearScannedItems()" class="text-xs text-red-500 hover:text-red-700">Clear All</button>
                        </div>
                        <div id="wm-scan-list" class="max-h-48 overflow-y-auto space-y-2"></div>

                        <!-- Pagination -->
                        <div id="wm-scan-pagination" class="hidden flex items-center justify-between mt-3 pt-3 border-t border-slate-200">
                            <button onclick="wmScanPrevPage()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-scan-prev-page">Previous</button>
                            <span id="wm-scan-page-info" class="text-xs text-slate-600">Page 1 of 1</span>
                            <button onclick="wmScanNextPage()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="wm-scan-next-page">Next</button>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button onclick="wmCloseScanner()" class="flex-1 px-4 py-3 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">Cancel</button>
                        <button onclick="wmProceedToDetails()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold hover:from-emerald-700 hover:to-emerald-800 transition shadow-md" id="wm-proceed-btn" disabled>Proceed to Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Add Warehouse Modal -->
    <div id="add-warehouse-modal" class="fixed inset-0 bg-black/50 z-[100000002] hidden flex items-center justify-center">
        <div class="modal-panel p-8 max-w-md">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Add New Warehouse</h2>
            <form id="add-warehouse-form" class="space-y-4">
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Warehouse Name</label>
                    <input type="text" id="warehouse-name" name="name" required class="w-full px-4 py-3" placeholder="Enter warehouse name">
                </div>
                <div class="modal-actions flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel-add-warehouse" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Create Warehouse</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transfer Shelf Modal -->
    <div id="transfer-shelf-modal" class="fixed inset-0 bg-black/50 z-[100000002] hidden flex items-center justify-center">
        <div class="modal-panel p-8 max-w-md">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Transfer Shelf</h2>
            <form id="transfer-shelf-form" class="space-y-4">
                <input type="hidden" id="transfer-source-warehouse-id">
                <input type="hidden" id="transfer-slot-index">
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Current Shelf</label>
                    <input type="text" id="transfer-current-shelf" class="w-full px-4 py-3" readonly>
                </div>
                <div class="modal-field">
                    <label class="block text-sm font-medium mb-2">Destination Warehouse</label>
                    <select id="transfer-destination-warehouse" class="w-full px-4 py-3" required>
                        <option value="">Select destination warehouse</option>
                    </select>
                </div>
                <div class="modal-actions flex justify-end gap-3 mt-6">
                    <button type="button" id="cancel-transfer-shelf" class="modal-footer-button secondary">Cancel</button>
                    <button type="submit" class="modal-footer-button primary">Transfer Shelf</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Stock Arrival Modal -->
    <div id="assign-arrival-modal" class="fixed inset-0 bg-black/50 z-[100000003] hidden items-center justify-center">
        <div class="modal-panel p-8 max-w-md w-full">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900">Assign New Stock to Warehouse</h2>
                <button type="button" id="close-assign-arrival" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200">âœ•</button>
            </div>

            <div id="assign-arrival-info" class="mb-5 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm">
                <p class="font-semibold text-amber-900" id="assign-arrival-product-name">â€”</p>
                <p class="text-amber-700 mt-0.5">Qty: <span id="assign-arrival-qty" class="font-semibold">â€”</span></p>
            </div>

            <form id="assign-arrival-form" class="space-y-4">
                <input type="hidden" id="assign-arrival-id">

                <div class="modal-field p-4">
                    <label class="block text-sm font-semibold text-slate-800 mb-2">Destination Warehouse <span class="text-red-500">*</span></label>
                    <select id="assign-arrival-warehouse" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900" required>
                        <option value="">Select warehouse</option>
                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wh['id']); ?>"><?php echo e($wh['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="modal-field p-4">
                    <label class="block text-sm font-semibold text-slate-800 mb-2">Date Assigned</label>
                    <input type="date" id="assign-arrival-date" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900">
                </div>

                <div class="modal-field p-4">
                    <label class="block text-sm font-semibold text-slate-800 mb-2">Note <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea id="assign-arrival-note" rows="3" class="block w-full rounded-md border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900" placeholder="e.g. Placed in Shelf A-2 area..."></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200">
                    <button type="button" id="cancel-assign-arrival" class="modal-footer-button secondary flex-1">Cancel</button>
                    <button type="submit" class="modal-footer-button primary flex-1">Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Collapsible toggle for Pending Arrivals panel
        (function () {
            const toggleBtn = document.getElementById('pending-arrivals-toggle');
            const body = document.getElementById('pending-arrivals-body');
            const chevron = document.getElementById('pending-arrivals-chevron');
            if (!toggleBtn || !body) return;

            toggleBtn.addEventListener('click', function () {
                const isOpen = !body.classList.contains('hidden');
                body.classList.toggle('hidden', isOpen);
                chevron.classList.toggle('rotate-180', !isOpen);
                toggleBtn.setAttribute('aria-expanded', String(!isOpen));
            });
        })();
    </script>

    <script>
        // Assign Stock Arrival logic
        (function () {
            const modal = document.getElementById('assign-arrival-modal');
            const form = document.getElementById('assign-arrival-form');
            const dateInput = document.getElementById('assign-arrival-date');

            // Default date to today
            if (dateInput) {
                dateInput.value = new Date().toISOString().split('T')[0];
            }

            function openModal(id, name, qty) {
                document.getElementById('assign-arrival-id').value = id;
                document.getElementById('assign-arrival-product-name').textContent = name;
                document.getElementById('assign-arrival-qty').textContent = qty;
                document.getElementById('assign-arrival-warehouse').value = '';
                document.getElementById('assign-arrival-note').value = '';
                if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.getElementById('close-assign-arrival').addEventListener('click', closeModal);
            document.getElementById('cancel-assign-arrival').addEventListener('click', closeModal);

            document.querySelectorAll('.assign-arrival-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    openModal(this.dataset.id, this.dataset.name, this.dataset.qty);
                });
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const id = document.getElementById('assign-arrival-id').value;
                const warehouseId = document.getElementById('assign-arrival-warehouse').value;
                const note = document.getElementById('assign-arrival-note').value.trim();
                const assignedDate = dateInput ? dateInput.value : '';

                if (!warehouseId) {
                    alert('Please select a destination warehouse.');
                    return;
                }

                const submitBtn = form.querySelector('[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Savingâ€¦';

                try {
                    const response = await fetch(`/warehouse-management/stock-arrival/${id}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            warehouse_id: parseInt(warehouseId, 10),
                            note: note || null,
                            assigned_date: assignedDate || null,
                        }),
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Remove row from table
                        const row = document.getElementById(`arrival-row-${id}`);
                        if (row) row.remove();

                        // Update badge count
                        const tbody = document.getElementById('pending-arrivals-tbody');
                        const remaining = tbody ? tbody.querySelectorAll('tr').length : 0;
                        const badge = document.getElementById('pending-arrivals-count');
                        if (badge) badge.textContent = `${remaining} pending`;

                        // Hide entire panel if no rows left
                        if (remaining === 0) {
                            const panel = document.getElementById('pending-arrivals-panel');
                            if (panel) panel.remove();
                        }

                        closeModal();

                        // Show toast via existing warehouse toast function (if available)
                        if (typeof showToast === 'function') {
                            showToast('Stock assigned to warehouse successfully!', 'success');
                        }
                    } else {
                        alert(result.message || 'Failed to assign stock. Please try again.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('An error occurred. Please try again.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Confirm Assignment';
                }
            });
        })();
    </script>

    <script src="<?php echo e(asset('js/warehouse_management.js')); ?>?v=<?php echo e(time()); ?>"></script>
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/warehouse_management/warehouse_manage.blade.php ENDPATH**/ ?>