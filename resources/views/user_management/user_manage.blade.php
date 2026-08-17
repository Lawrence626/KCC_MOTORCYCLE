<x-layouts.app :title="__('User Management')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="pl-3 lg:pl-1">
                <h1 class="text-3xl font-bold text-slate-900">User Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage user accounts, roles, and access across the system.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if($showArchived ?? false)
                    <a href="{{ route('user.management') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                        <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Active
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div id="pageSuccessAlert" class="rounded-[10px] border border-[#00fff2] bg-[#e6fffe] p-4 text-sm font-medium text-slate-900 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @php
            $managementRoute = ($showArchived ?? false)
                ? route('user.management.archived')
                : route('user.management');
        @endphp

        <!-- Add User Modal -->
        <div id="addUserModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                    <div>
                        <h2 class="text-xl font-bold text-black">Add User</h2>
                        <p class="text-sm text-slate-800 font-medium">Create new user account and assign role.</p>
                    </div>
                    <button type="button" id="closeAddUserModal" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Name</label>
                                <input name="name" value="{{ old('name') }}" required autocomplete="name" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1 relative" data-dropdown-wrapper="userRole">
                                <label class="block text-xs font-medium text-slate-700">Role</label>
                                <input type="hidden" name="role" id="userRoleInput" value="{{ old('role', 'admin') }}" />
                                <button type="button" id="userRoleButton" onclick="toggleDropdown('userRoleDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                                    <span>{{ old('role', 'admin') === 'admin' ? 'Administrator' : (old('role', 'cashier') === 'cashier' ? 'Cashier' : (old('role', 'inventory_clerk') === 'inventory_clerk' ? 'Inventory Clerk' : 'Warehouse Personnel')) }}</span>
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <div id="userRoleDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                                    <button type="button" onclick="selectDropdown(event, 'userRoleInput', 'admin', 'userRoleButton', 'Administrator', 'userRoleDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('role', 'admin') === 'admin' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Administrator</button>
                                    <button type="button" onclick="selectDropdown(event, 'userRoleInput', 'cashier', 'userRoleButton', 'Cashier', 'userRoleDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('role', 'admin') === 'cashier' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Cashier</button>
                                    <button type="button" onclick="selectDropdown(event, 'userRoleInput', 'inventory_clerk', 'userRoleButton', 'Inventory Clerk', 'userRoleDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('role', 'admin') === 'inventory_clerk' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Inventory Clerk</button>
                                    <button type="button" onclick="selectDropdown(event, 'userRoleInput', 'warehouse_personnel', 'userRoleButton', 'Warehouse Personnel', 'userRoleDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('role', 'admin') === 'warehouse_personnel' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Warehouse Personnel</button>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Contact</label>
                                <input name="contact" value="{{ old('contact') }}" autocomplete="tel" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Full Address</label>
                                <input name="address" value="{{ old('address') }}" autocomplete="street-address" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Age</label>
                                <input name="age" type="number" value="{{ old('age') }}" autocomplete="off" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1 relative" data-dropdown-wrapper="userGender">
                                <label class="block text-xs font-medium text-slate-700">Gender</label>
                                <input type="hidden" name="gender" id="userGenderInput" value="{{ old('gender', '') }}" />
                                <button type="button" id="userGenderButton" onclick="toggleDropdown('userGenderDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                                    <span>{{ old('gender', '') === '' ? '--' : old('gender', '') }}</span>
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <div id="userGenderDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                                    <button type="button" onclick="selectDropdown(event, 'userGenderInput', 'Male', 'userGenderButton', 'Male', 'userGenderDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('gender', '') === 'Male' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Male</button>
                                    <button type="button" onclick="selectDropdown(event, 'userGenderInput', 'Female', 'userGenderButton', 'Female', 'userGenderDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('gender', '') === 'Female' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Female</button>
                                    <button type="button" onclick="selectDropdown(event, 'userGenderInput', 'Other', 'userGenderButton', 'Other', 'userGenderDropdown')" class="w-full px-4 py-2.5 text-center text-sm {{ old('gender', '') === 'Other' ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">Other</button>
                                </div>
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Password</label>
                                <div class="relative">
                                    <input name="password" id="add_password" type="password" autocomplete="new-password" required class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                                    <button type="button" class="toggle-password absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="#add_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <div id="add_password_requirements" class="text-[11px] space-y-0.5 mt-1 p-2 bg-white border border-slate-200 rounded-[10px] hidden">
                                    <div class="font-semibold text-slate-900 text-xs">Password must contain:</div>
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
                                    <input name="password_confirmation" id="add_password_confirm" type="password" autocomplete="new-password" required class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                                    <button type="button" class="toggle-password absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="#add_password_confirm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <div id="add_password_match" class="text-xs hidden mt-1 p-2 rounded-[10px]"></div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:justify-end">
                            <button type="button" id="cancelAddUser" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200">Cancel</button>
                            <button type="submit" id="addUserSubmitBtn" class="inline-flex items-center justify-center rounded-[10px] bg-[#00FFF2] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#00D9CC] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-4">
            <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" data-action="close-modal"></div>
            <div class="relative w-full max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                    <div>
                        <h2 class="text-xl font-bold text-black">Edit User</h2>
                        <p class="text-sm text-slate-800 font-medium">Update user details and role.</p>
                    </div>
                    <button type="button" id="closeEditUserModal" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 sm:p-5 overflow-y-auto max-h-[calc(90vh-100px)]">
                    <form method="POST" id="editUserForm" autocomplete="off">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Name</label>
                                <input name="name" id="edit_name" required autocomplete="name" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Email</label>
                                <input type="email" name="email" id="edit_email" required autocomplete="username" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1 relative" data-dropdown-wrapper="editUserRole">
                                <label class="block text-xs font-medium text-slate-700">Role</label>
                                <input type="hidden" name="role" id="edit_role_input" value="admin" />
                                <button type="button" id="edit_role_button" onclick="toggleDropdown('edit_role_dropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                                    <span id="edit_role_display">Administrator</span>
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <div id="edit_role_dropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                                    <button type="button" onclick="selectDropdown(event, 'edit_role_input', 'admin', 'edit_role_button', 'Administrator', 'edit_role_dropdown')" class="w-full px-4 py-2.5 text-center text-sm font-semibold text-slate-900 bg-black/10 rounded-[10px]">Administrator</button>
                                    <button type="button" onclick="selectDropdown(event, 'edit_role_input', 'cashier', 'edit_role_button', 'Cashier', 'edit_role_dropdown')" class="w-full px-4 py-2.5 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Cashier</button>
                                    <button type="button" onclick="selectDropdown(event, 'edit_role_input', 'inventory_clerk', 'edit_role_button', 'Inventory Clerk', 'edit_role_dropdown')" class="w-full px-4 py-2.5 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Inventory Clerk</button>
                                    <button type="button" onclick="selectDropdown(event, 'edit_role_input', 'warehouse_personnel', 'edit_role_button', 'Warehouse Personnel', 'edit_role_dropdown')" class="w-full px-4 py-2.5 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Warehouse Personnel</button>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Contact</label>
                                <input name="contact" id="edit_contact" autocomplete="tel" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Full Address</label>
                                <input name="address" id="edit_address" autocomplete="street-address" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Age</label>
                                <input name="age" id="edit_age" type="number" autocomplete="off" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="space-y-1 relative" data-dropdown-wrapper="editUserGender">
                                <label class="block text-xs font-medium text-slate-700">Gender</label>
                                <input type="hidden" name="gender" id="edit_gender_input" value="" />
                                <button type="button" id="edit_gender_button" onclick="toggleDropdown('edit_gender_dropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                                    <span id="edit_gender_display">--</span>
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                    </svg>
                                </button>
                                <div id="edit_gender_dropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                                    <button type="button" onclick="selectDropdown(event, 'edit_gender_input', 'Male', 'edit_gender_button', 'Male', 'edit_gender_dropdown')" class="w-full px-4 py-2.5 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Male</button>
                                    <button type="button" onclick="selectDropdown(event, 'edit_gender_input', 'Female', 'edit_gender_button', 'Female', 'edit_gender_dropdown')" class="w-full px-4 py-2.5 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Female</button>
                                    <button type="button" onclick="selectDropdown(event, 'edit_gender_input', 'Other', 'edit_gender_button', 'Other', 'edit_gender_dropdown')" class="w-full px-4 py-2.5 text-center text-sm text-slate-700 hover:bg-slate-100 rounded-[10px]">Other</button>
                                </div>
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-medium text-slate-700">Password</label>
                                <div class="relative">
                                    <input name="password" id="edit_password" type="password" placeholder="Leave blank if you don't change your password" autocomplete="new-password" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                                    <button type="button" class="toggle-password absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="#edit_password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.488m.518-.59A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.074 5.123M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" /></svg>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1">Just fill up to change your password</p>
                                <div id="edit_password_requirements" class="text-[11px] space-y-0.5 mt-1 p-2 bg-white border border-slate-200 rounded-[10px] hidden">
                                    <div class="font-semibold text-slate-900 text-xs">Password must contain:</div>
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
                                    <input name="password_confirmation" id="edit_password_confirm" type="password" placeholder="Re-enter password" autocomplete="new-password" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 pr-8 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                                    <button type="button" class="toggle-password absolute right-2 top-[33px] text-slate-400 hover:text-slate-600" data-target="#edit_password_confirm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:justify-end">
                            <button type="button" id="cancelEditUser" class="inline-flex items-center justify-center rounded-[10px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-black/10 transition-all duration-200">Cancel</button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-[10px] bg-[#00FFF2] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#00D9CC] transition-all duration-200">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="rounded-[10px] border border-slate-200 bg-white p-5 shadow-sm">
            <div id="userListWrapper">
            <div class="mb-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                @php
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
                @endphp

                @unless($showArchived ?? false)
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('user.management') }}{{ $hasQ ? '?q=' . urlencode(request('q')) : '' }}" class="rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all {{ !$hasRole && !($showArchived ?? false) ? 'bg-[#0f172a] text-white border-[#0f172a] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">All</a>
                    <a href="{{ route('user.management') }}?role=admin{{ $hasQ ? '&q=' . urlencode(request('q')) : '' }}" class="rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all {{ request('role') === 'admin' ? 'bg-[#0f172a] text-white border-[#0f172a] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Administrator</a>
                    <a href="{{ route('user.management') }}?role=cashier{{ $hasQ ? '&q=' . urlencode(request('q')) : '' }}" class="rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all {{ request('role') === 'cashier' ? 'bg-[#0f172a] text-white border-[#0f172a] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Cashier</a>
                    <a href="{{ route('user.management') }}?role=inventory_clerk{{ $hasQ ? '&q=' . urlencode(request('q')) : '' }}" class="rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all {{ request('role') === 'inventory_clerk' ? 'bg-[#0f172a] text-white border-[#0f172a] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Inventory Clerk</a>
                    <a href="{{ route('user.management') }}?role=warehouse_personnel{{ $hasQ ? '&q=' . urlencode(request('q')) : '' }}" class="rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all {{ request('role') === 'warehouse_personnel' ? 'bg-[#0f172a] text-white border-[#0f172a] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Warehouse</a>
                    <a href="{{ route('user.management.archived') }}{{ $archiveQuery }}" class="rounded-[10px] border px-4 py-2 text-sm font-semibold transition-all {{ ($showArchived ?? false) ? 'bg-[#0f172a] text-white border-[#0f172a] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">Archived</a>
                </div>
                @endunless

                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ $managementRoute }}" class="relative" id="userSearchForm">
                        @if(request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif
                        <input id="userSearchInput" type="search" name="q" value="{{ request('q') }}" placeholder="Search user" class="rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-2.5 w-72 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" autocomplete="off" />
                    </form>
                    @if(!($showArchived ?? false))
                        <button id="openAddUserModal" class="inline-flex items-center gap-2 rounded-[10px] bg-[#00FFF2] px-4 py-2.5 text-sm font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#00D9CC] transition-all duration-200">
                            <svg class="h-4 w-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add User
                        </button>
                    @endif
                </div>
            </div>

            @php
                $roles = ['admin' => 'Admin','cashier' => 'Cashier','inventory_clerk' => 'Clerk','warehouse_personnel' => 'Warehouse'];
            @endphp

            <div class="overflow-x-auto rounded-[10px] border border-slate-200">
                <table class="w-full divide-y divide-slate-200 table-auto">
                    <thead class="border-b border-slate-200 bg-[#0f172a] text-xs uppercase tracking-wider text-white">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold text-white">Name</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Role</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Email</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Contact</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Full Address</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Age</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Gender</th>
                            <th class="px-3 py-3 text-left font-semibold text-white">Status</th>
                            <th class="px-3 py-3 text-center font-semibold text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200 text-xs">
                        @forelse($users ?? [] as $user)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-2 py-2 align-top whitespace-normal max-w-[140px]">
                                <div class="flex items-start gap-1.5">
                                    <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-[10px] flex-shrink-0">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-900 text-xs truncate">{{ $user->name }}</div>
                                        <div class="text-[10px] text-slate-500 truncate">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role dropdown: styled to match the Order Management filter-dropdown pattern.
                                 Uses a single shared fixed-position panel (see bottom of page) instead of an
                                 absolutely-positioned menu inside this cell, so it never gets clipped by the
                                 table's overflow-x-auto wrapper. -->
                            <td class="px-2 py-2 align-top whitespace-nowrap">
                                <form method="POST" action="{{ route('users.update_role', $user->id ?? 0) }}" class="inline-flex items-center role-form" data-user-id="{{ $user->id }}" id="roleForm-{{ $user->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="role" id="roleInput-{{ $user->id }}" value="{{ $user->role }}">
                                    <button type="button"
                                        class="roleDropdownBtn w-20 rounded-[8px] border border-slate-200 ring-1 ring-gray-200 bg-slate-50 px-1.5 py-1 text-left text-[10px] text-slate-900 flex items-center justify-between gap-1 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35"
                                        data-form-id="roleForm-{{ $user->id }}"
                                        data-input-id="roleInput-{{ $user->id }}"
                                        data-current="{{ $user->role }}">
                                        <span>{{ $roles[$user->role] ?? ucfirst($user->role) }}</span>
                                        <svg class="w-3 h-3 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                        </svg>
                                    </button>
                                </form>
                            </td>

                            <td class="px-2 py-2 align-top whitespace-normal max-w-[160px] break-words text-[10px] text-slate-600">{{ $user->email }}</td>
                            <td class="px-2 py-2 align-top whitespace-normal max-w-[100px] break-words text-[10px] text-slate-600">{{ $user->contact ?? '-' }}</td>
                            <td class="px-2 py-2 align-top whitespace-normal max-w-[160px] break-words text-[10px] text-slate-600">{{ $user->address ?? '-' }}</td>
                            <td class="px-2 py-2 align-top whitespace-nowrap text-[10px] text-slate-600">{{ $user->age ?? '-' }}</td>
                            <td class="px-2 py-2 align-top whitespace-nowrap text-[10px] text-slate-600">{{ $user->gender ?? '-' }}</td>

                            <td class="px-2 py-2 align-top whitespace-nowrap">
                                @if($user->is_active ?? true)
                                    <span class="inline-flex items-center rounded-[8px] bg-[#e6fffe] border border-[#00fff2] px-1.5 py-0.5 text-[10px] font-semibold text-slate-900">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-[8px] bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700">Inactive</span>
                                @endif
                            </td>

                            <td class="px-2 py-2 text-center align-middle whitespace-nowrap text-[10px] font-medium">
                                <div class="inline-flex items-center gap-1.5">
                                <button type="button" class="text-black hover:text-slate-900 editUserBtn inline-flex items-center" data-user='@json($user)'>
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <form action="{{ route('users.update_status', $user->id ?? 0) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-[8px] border border-slate-200 px-2 py-1 text-[10px] font-semibold transition-all bg-white text-slate-700 hover:bg-black/10">{{ ($user->is_active ?? true) ? 'Archive' : 'Unarchive' }}</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-slate-500">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() ?? '' }}
            </div>
            </div>
        </div>
    </div>

    <!-- Shared floating Role dropdown panel. Rendered once, positioned with JS (position: fixed)
         next to whichever row's button was clicked. Using position: fixed here means it is never
         clipped by the table's overflow-x-auto wrapper (the bug seen when each row had its own
         absolutely-positioned menu nested inside that scroll container). -->
    <div id="sharedRoleDropdown" class="hidden fixed z-[9999] rounded-[10px] border border-slate-200/50 bg-white shadow-xl p-2 space-y-1"></div>

    <script>
        const USER_ROLES = @json($roles);

        function closeRoleDropdown() {
            const dd = document.getElementById('sharedRoleDropdown');
            dd.classList.add('hidden');
            dd.dataset.forId = '';
        }

        function positionRoleDropdown(button) {
            const dd = document.getElementById('sharedRoleDropdown');
            const rect = button.getBoundingClientRect();
            const ddWidth = rect.width;
            let left = rect.left;
            if (left + ddWidth > window.innerWidth - 8) {
                left = window.innerWidth - ddWidth - 8;
            }
            dd.style.width = ddWidth + 'px';
            dd.style.top = (rect.bottom + 4) + 'px';
            dd.style.left = left + 'px';
        }

        function openRoleDropdown(button) {
            const dd = document.getElementById('sharedRoleDropdown');
            const wasOpenForThisButton = dd.dataset.forId === button.dataset.formId && !dd.classList.contains('hidden');

            closeRoleDropdown();
            if (wasOpenForThisButton) return;

            const current = button.dataset.current;
            const inputId = button.dataset.inputId;
            const formId = button.dataset.formId;

            dd.innerHTML = '';
            Object.keys(USER_ROLES).forEach(value => {
                const label = USER_ROLES[value];
                const opt = document.createElement('button');
                opt.type = 'button';
                opt.textContent = label;
                opt.className = 'w-full px-2 py-1 text-center text-[10px] rounded-[8px] ' +
                    (current === value ? 'font-semibold text-slate-900 bg-gray-200' : 'text-slate-700 hover:bg-slate-100');
                opt.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    document.getElementById(inputId).value = value;
                    button.querySelector('span').textContent = label;
                    closeRoleDropdown();
                    document.getElementById(formId).submit();
                });
                dd.appendChild(opt);
            });

            dd.dataset.forId = formId;
            dd.classList.remove('hidden');
            positionRoleDropdown(button);
        }

        document.querySelectorAll('.roleDropdownBtn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                openRoleDropdown(this);
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#sharedRoleDropdown') && !e.target.closest('.roleDropdownBtn')) {
                closeRoleDropdown();
            }
        });
        window.addEventListener('scroll', closeRoleDropdown, true);
        window.addEventListener('resize', closeRoleDropdown);
    </script>

    @push('scripts')
    @vite('resources/js/user_management.js')
    @endpush

</x-layouts.app>