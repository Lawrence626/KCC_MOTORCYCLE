<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => __('User Management')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('User Management'))]); ?>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <?php if(!($showArchived ?? false)): ?>
                <button id="openAddUserModal" class="inline-flex items-center px-4 py-2 bg-teal-500 text-white rounded-lg shadow-sm hover:bg-teal-600">+ Add User</button>
            <?php else: ?>
                <a href="<?php echo e(route('user.management')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Back to Active</a>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div id="pageSuccessAlert" class="rounded-3xl border border-teal-200 bg-teal-50 p-4 text-teal-900 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold">Success</p>
                        <p class="text-sm text-teal-800"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
            $managementRoute = ($showArchived ?? false)
                ? route('user.management.archived')
                : route('user.management');
        ?>

        <!-- Add User Modal -->
        <div id="addUserModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-3xl overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-teal-600 font-semibold">Add User</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">Create new user</h3>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600 text-2xl leading-none" id="closeAddUserModal">×</button>
                </div>
                <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <form action="<?php echo e(route('users.store')); ?>" method="POST" autocomplete="off">
                        <?php echo csrf_field(); ?>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Name</label>
                                <input name="name" value="<?php echo e(old('name')); ?>" required autocomplete="name" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Email</label>
                                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="username" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Role</label>
                                <select name="role" autocomplete="off" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                                    <option value="admin">Administrator</option>
                                    <option value="cashier">Cashier</option>
                                    <option value="inventory_clerk">Inventory Clerk</option>
                                    <option value="warehouse_personnel">Warehouse Personnel</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Contact</label>
                                <input name="contact" value="<?php echo e(old('contact')); ?>" autocomplete="tel" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Full Address</label>
                                <input name="address" value="<?php echo e(old('address')); ?>" autocomplete="street-address" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Age</label>
                                <input name="age" type="number" value="<?php echo e(old('age')); ?>" autocomplete="off" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Gender</label>
                                <select name="gender" autocomplete="off" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                                    <option value="">--</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Password</label>
                                <div class="relative">
                                    <input name="password" id="add_password" type="password" autocomplete="new-password" required class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                                    <button type="button" class="toggle-password absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="#add_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <div id="add_password_requirements" class="text-[11px] space-y-0.5 mt-1 p-2 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="font-semibold text-red-700 text-xs">Password must contain:</div>
                                    <div class="flex items-center gap-1" data-requirement="lowercase">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Lowercase letter (a-z)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="uppercase">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Uppercase letter (A-Z)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="number">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Number (0-9)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="special">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Special char (@ $ ! % * # ?)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="length">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">12+ characters</span>
                                    </div>
                                </div>
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Confirm Password</label>
                                <div class="relative">
                                    <input name="password_confirmation" id="add_password_confirm" type="password" autocomplete="new-password" required class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                                    <button type="button" class="toggle-password absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="#add_password_confirm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <div id="add_password_match" class="text-xs hidden mt-1 p-2 rounded-lg"></div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:justify-end">
                            <button type="button" id="cancelAddUser" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button>
                            <button type="submit" id="addUserSubmitBtn" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-3xl overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 bg-slate-900 text-white">
                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-300 font-semibold">Edit User</p>
                        <h3 class="mt-1 text-lg font-semibold">Update user details</h3>
                    </div>
                    <button class="text-slate-300 hover:text-white text-2xl leading-none" id="closeEditUserModal">×</button>
                </div>
                <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(90vh-100px)]">
                    <form method="POST" id="editUserForm" autocomplete="off">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Name</label>
                                <input name="name" id="edit_name" required autocomplete="name" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Email</label>
                                <input type="email" name="email" id="edit_email" required autocomplete="username" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Role</label>
                                <select name="role" id="edit_role" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                                    <option value="admin">Administrator</option>
                                    <option value="cashier">Cashier</option>
                                    <option value="inventory_clerk">Clerks</option>
                                    <option value="warehouse_personnel">Warehouse Personnel</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Contact</label>
                                <input name="contact" id="edit_contact" autocomplete="tel" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Full Address</label>
                                <input name="address" id="edit_address" autocomplete="street-address" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Age</label>
                                <input name="age" id="edit_age" type="number" autocomplete="off" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Gender</label>
                                <select name="gender" id="edit_gender" autocomplete="off" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-xs text-slate-900 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                                    <option value="">--</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Password</label>
                                <div class="relative">
                                    <input name="password" id="edit_password" type="password" placeholder="Leave blank if you don't change your password" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                                    <button type="button" class="toggle-password absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="#edit_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5,12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1">Just fill up to change your password</p>
                                <div id="edit_password_requirements" class="text-[11px] space-y-0.5 mt-1 p-2 bg-red-50 border border-red-200 rounded-lg hidden">
                                    <div class="font-semibold text-red-700 text-xs">Password must contain:</div>
                                    <div class="flex items-center gap-1" data-requirement="lowercase">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Lowercase (a-z)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="uppercase">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Uppercase (A-Z)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="number">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Number (0-9)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="special">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">Special char (@ $ ! %)</span>
                                    </div>
                                    <div class="flex items-center gap-1" data-requirement="length">
                                        <span class="text-red-500 text-xs">✕</span>
                                        <span class="text-red-600 text-[10px]">12+ characters</span>
                                    </div>
                                </div>
                                <div class="relative mt-3" id="edit_password_confirm_wrapper" style="display:none;">
                                    <label class="block text-xs font-medium text-slate-700 mb-1">Confirm Password</label>
                                    <input name="password_confirmation" id="edit_password_confirm" type="password" placeholder="Re-enter password" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200" />
                                    <button type="button" class="toggle-password absolute right-2 top-[33px] text-slate-400 hover:text-slate-600" data-target="#edit_password_confirm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:justify-end">
                            <button type="button" id="cancelEditUser" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-700">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="userListWrapper" class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="mb-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <?php
                    $hasQ = request('q');
                    $hasRole = request('role');
                    $queryString = [];
                    if ($hasRole) {
                        $queryString['role'] = request('role');
                    }
                    if ($hasQ) {
                        $queryString['q'] = request('q');
                    }
                    $archiveQuery = $queryString ? '?'.http_build_query($queryString) : '';
                ?>

                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('user.management')); ?><?php echo e($hasQ ? '?q=' . urlencode(request('q')) : ''); ?>" class="px-3 py-1 rounded-md <?php echo e(!($showArchived ?? false) && !$hasRole ? 'bg-teal-500 text-white' : 'border'); ?>">All</a>
                    <a href="<?php echo e(route('user.management')); ?>?role=admin<?php echo e($hasQ ? '&q=' . urlencode(request('q')) : ''); ?>" class="px-3 py-1 rounded-md <?php echo e(!($showArchived ?? false) && request('role') === 'admin' ? 'bg-teal-500 text-white' : 'border'); ?>">Administrator</a>
                    <a href="<?php echo e(route('user.management')); ?>?role=cashier<?php echo e($hasQ ? '&q=' . urlencode(request('q')) : ''); ?>" class="px-3 py-1 rounded-md <?php echo e(!($showArchived ?? false) && request('role') === 'cashier' ? 'bg-teal-500 text-white' : 'border'); ?>">Cashier</a>
                    <a href="<?php echo e(route('user.management')); ?>?role=inventory_clerk<?php echo e($hasQ ? '&q=' . urlencode(request('q')) : ''); ?>" class="px-3 py-1 rounded-md <?php echo e(!($showArchived ?? false) && request('role') === 'inventory_clerk' ? 'bg-teal-500 text-white' : 'border'); ?>">Inventory Clerk</a>
                    <a href="<?php echo e(route('user.management')); ?>?role=warehouse_personnel<?php echo e($hasQ ? '&q=' . urlencode(request('q')) : ''); ?>" class="px-3 py-1 rounded-md <?php echo e(!($showArchived ?? false) && request('role') === 'warehouse_personnel' ? 'bg-teal-500 text-white' : 'border'); ?>">Warehouse</a>
                    <a href="<?php echo e(route('user.management.archived')); ?><?php echo e($archiveQuery); ?>" class="px-3 py-1 rounded-md <?php echo e(($showArchived ?? false) ? 'bg-gray-700 text-white' : 'border'); ?>">Archived</a>
                </div>

                <div class="flex items-center">
                    <form method="GET" action="<?php echo e($managementRoute); ?>" class="relative" id="userSearchForm">
                        <?php if(request('role')): ?>
                            <input type="hidden" name="role" value="<?php echo e(request('role')); ?>">
                        <?php endif; ?>
                        <input id="userSearchInput" type="search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search user" class="border rounded-full px-4 py-2 w-72 focus:outline-none focus:ring-2 focus:ring-teal-300" autocomplete="off" />
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Name</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Role</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Email</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Contact</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Full Address</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Age</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Gender</th>
                            <th class="px-3 py-2 text-left text-[10px] font-semibold text-gray-500">Status</th>
                            <th class="px-3 py-2 text-center text-[10px] font-semibold text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-xs">
                        <?php $__empty_1 = true; $__currentLoopData = $users ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 align-top whitespace-normal max-w-[140px]">
                                <div class="flex items-start gap-1.5">
                                    <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-[10px] flex-shrink-0"><?php echo e(strtoupper(substr($user->name,0,1))); ?></div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-900 text-xs truncate"><?php echo e($user->name); ?></div>
                                        <div class="text-[10px] text-gray-500 truncate"><?php echo e($user->email); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-2 py-2 align-top whitespace-nowrap">
                                <form method="POST" action="<?php echo e(route('users.update_role', $user->id ?? 0)); ?>" class="inline-flex items-center role-form" data-user-id="<?php echo e($user->id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <select name="role" class="w-20 rounded-md border border-slate-300 bg-slate-100 px-1.5 py-1 text-[10px] text-slate-900" onchange="this.closest('form').submit()">
                                        <?php
                                            $roles = ['admin' => 'Admin','cashier' => 'Cashier','inventory_clerk' => 'Clerk','warehouse_personnel' => 'Warehouse'];
                                        ?>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php if($user->role === $value): ?> selected <?php endif; ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </form>
                            </td>

                            <td class="px-2 py-2 align-top whitespace-normal max-w-[160px] break-words text-[10px] text-gray-700"><?php echo e($user->email); ?></td>
                            <td class="px-2 py-2 align-top whitespace-normal max-w-[100px] break-words text-[10px] text-gray-700"><?php echo e($user->contact ?? '-'); ?></td>
                            <td class="px-2 py-2 align-top whitespace-normal max-w-[160px] break-words text-[10px] text-gray-700"><?php echo e($user->address ?? '-'); ?></td>
                            <td class="px-2 py-2 align-top whitespace-nowrap text-[10px] text-gray-700"><?php echo e($user->age ?? '-'); ?></td>
                            <td class="px-2 py-2 align-top whitespace-nowrap text-[10px] text-gray-700"><?php echo e($user->gender ?? '-'); ?></td>

                            <td class="px-2 py-2 align-top whitespace-nowrap">
                                <?php if($user->is_active ?? true): ?>
                                    <span class="inline-flex items-center rounded-full bg-teal-100 px-1.5 py-0.5 text-[10px] font-semibold text-teal-800">Active</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold text-gray-800">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-2 py-2 text-center align-top whitespace-nowrap text-[10px] font-medium">
                                <button type="button" class="text-gray-600 hover:text-gray-900 mr-1.5 editUserBtn text-sm" data-user='<?php echo json_encode($user, 15, 512) ?>'>✏️</button>
                                <form action="<?php echo e(route('users.update_status', $user->id ?? 0)); ?>" method="POST" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="rounded px-1.5 py-0.5 text-[10px] <?php echo e(($user->is_active ?? true) ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>"><?php echo e(($user->is_active ?? true) ? 'Archive' : 'Unarchive'); ?></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-gray-500">No users found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php echo e($users->links() ?? ''); ?>

            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/user_management.js'); ?>
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
<?php /**PATH C:\Users\ilano\Herd\KCC_MOTORCYCLE\KCC_MOTORCYCLE\resources\views/user_management/user_manage.blade.php ENDPATH**/ ?>