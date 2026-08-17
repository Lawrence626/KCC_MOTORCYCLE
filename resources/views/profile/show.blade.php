<x-layouts.app :title="__('My Profile')">
    <div class="space-y-6">
        <div class="pl-3 lg:pl-2 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">My Profile</h1>
                <p class="text-gray-600 text-sm mt-1">Manage your personal information and account settings</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200 flex-shrink-0">
                <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                BACK TO DASHBOARD
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Profile Card -->
            <div class="border border-gray-200 p-6" style="border-radius: 20px; background-color: #ffffff;">
                <div class="flex flex-col items-center text-center">
                    <div id="avatarLarge" class="relative w-32 h-32 rounded-full overflow-hidden bg-gray-200 grid place-items-center text-3xl font-bold text-black shadow-sm" style="border: 1px solid #e5e7eb;">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover w-full h-full" />
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                        @endif
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-black">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->email }}</p>
                    <div class="mt-3 inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider" style="color: #36ADA3;">
                        {{ auth()->user()->role === 'admin' ? 'Administrator' : ucfirst(auth()->user()->role ?? 'user') }}
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between p-3" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                        <span class="text-sm text-gray-500">Member Since</span>
                        <span class="text-sm font-semibold text-black">{{ auth()->user()->created_at?->format('M d, Y') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                        <span class="text-sm text-gray-500">Account Status</span>
                        <span class="text-sm font-semibold" style="color: #0aada5;">Active</span>
                    </div>
                </div>

                <div class="mt-6">
                    <button id="openEditProfile" type="button" class="w-full px-4 py-3 text-sm font-semibold text-black transition" style="border-radius: 15px; background-color: #00fff2ff;" onmouseover="this.style.backgroundColor='#00e6da'" onmouseout="this.style.backgroundColor='#00fff2ff'">
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Details Card -->
            <div class="lg:col-span-2 border border-gray-200 overflow-hidden shadow-sm" style="border-radius: 20px; background-color: #ffffff;">
                <div class="bg-[#0f172a] border-b border-slate-800 px-6 py-5">
                    <h2 class="text-xl font-bold text-white">Personal Information</h2>
                    <p class="text-sm text-slate-300 mt-0.5">Your account details and contact information</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="p-4" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                            <p class="text-xs font-semibold text-slate-500">Full Name</p>
                            <p class="mt-2 text-base font-semibold text-black">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="p-4" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                            <p class="text-xs font-semibold text-slate-500">Email Address</p>
                            <p class="mt-2 text-base font-semibold text-black">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="p-4" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                            <p class="text-xs font-semibold text-slate-500">Contact Number</p>
                            <p class="mt-2 text-base font-semibold text-black">{{ auth()->user()->contact ?? '-' }}</p>
                        </div>
                        <div class="p-4" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                            <p class="text-xs font-semibold text-slate-500">Address</p>
                            <p class="mt-2 text-base font-semibold text-black">{{ auth()->user()->address ?? '-' }}</p>
                        </div>
                        <div class="p-4" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                            <p class="text-xs font-semibold text-slate-500">Age</p>
                            <p class="mt-2 text-base font-semibold text-black">{{ auth()->user()->age ?? '-' }}</p>
                        </div>
                        <div class="p-4" style="border-radius: 15px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                            <p class="text-xs font-semibold text-slate-500">Gender</p>
                            <p class="mt-2 text-base font-semibold text-black">{{ auth()->user()->gender ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Profile Modal (styled to match the POS "Process Payment" modal) -->
<div id="editProfileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
    <div id="editOverlay" class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl"></div>

    <div id="editProfileModalPanel" class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)] z-10 flex flex-col">
        <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
            <div>
                <h2 class="text-xl font-bold text-black">Edit Profile</h2>
                <p class="text-sm text-slate-800 font-medium">Update your personal information and profile picture.</p>
            </div>
            <button id="closeEditProfile" type="button" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="editProfileModalBody" class="overflow-y-auto flex-1 min-h-0" style="scrollbar-width: none; -ms-overflow-style: none;">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] px-6 py-6">
                {{-- Left column: avatar + personal details --}}
                <div class="space-y-5">
                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <div class="flex flex-col items-center text-center gap-3">
                            <div id="avatarPreview" class="relative w-24 h-24 flex-shrink-0 rounded-full overflow-hidden bg-gray-200 grid place-items-center text-3xl font-semibold text-black border border-slate-200">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover w-full h-full" />
                                @else
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                                @endif
                            </div>
                            <div>
                                <label class="cursor-pointer">
                                    <span class="inline-flex items-center rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-50 transition">Change Photo</span>
                                    <input type="file" name="avatar" accept="image/*" class="hidden" />
                                </label>
                                <p class="text-xs text-slate-500 mt-1.5">JPG, PNG or GIF. Max 5MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">Personal Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Full Name</label>
                                <input name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Email Address</label>
                                <input name="email" value="{{ old('email', auth()->user()->email) }}" type="email" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Age</label>
                                <input name="age" value="{{ old('age', auth()->user()->age) }}" type="number" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div class="relative" data-dropdown-wrapper="editProfileGender">
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Gender</label>
                                @php $currentGender = old('gender', auth()->user()->gender); @endphp
                                <input type="hidden" name="gender" id="editProfileGenderInput" value="{{ $currentGender }}" />
                                <button type="button" id="editProfileGenderButton" onclick="toggleEditProfileDropdown('editProfileGenderDropdown')" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                                    <span class="text-left">{{ $currentGender ?: 'Other' }}</span>
                                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <div id="editProfileGenderDropdown" class="dropdown-menu hidden fixed z-[9999] rounded-[10px] border border-slate-200 bg-white shadow-lg p-1.5 space-y-0.5">
                                    @foreach(['Male' => 'Male', 'Female' => 'Female'] as $value => $label)
                                        <button type="button" onclick="selectEditProfileDropdown(event, 'editProfileGenderInput', '{{ $value }}', 'editProfileGenderButton', '{{ $label }}', 'editProfileGenderDropdown')" class="w-full px-3 py-1.5 text-center text-xs {{ $currentGender === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[8px]">{{ $label }}</button>
                                    @endforeach
                                    <button type="button" onclick="selectEditProfileDropdown(event, 'editProfileGenderInput', '', 'editProfileGenderButton', 'Other', 'editProfileGenderDropdown')" class="w-full px-3 py-1.5 text-center text-xs {{ empty($currentGender) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[8px]">Other</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Address</label>
                                <input name="address" value="{{ old('address', auth()->user()->address) }}" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Contact Number</label>
                                <input name="contact" value="{{ old('contact', auth()->user()->contact) }}" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right column: password + notes + actions (mirrors payment method / notes / confirm-cancel layout) --}}
                <div class="space-y-5">
                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">Change Password</h3>
                        <div class="grid gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">New Password</label>
                                <input name="password" type="password" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="Leave blank to keep current" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Confirm Password</label>
                                <input name="password_confirmation" type="password" class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="Leave blank to keep current" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[28px] border border-slate-200 p-4">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">Note</h3>
                        <p class="text-sm text-slate-500">Changes apply immediately after saving. Leave the password fields blank to keep your current password.</p>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" id="cancelEditProfile" class="flex-1 rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Cancel</button>
                        <button type="submit" class="flex-1 rounded-[10px] bg-[#00fff2] px-4 py-3 text-sm font-bold text-black hover:bg-[#00e6da] ring-1 ring-slate-300">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<style>
    #editProfileModalBody::-webkit-scrollbar {
        display: none;
    }
</style>
<script>
    // Modal open/close
    const openBtn = document.getElementById('openEditProfile');
    const openBtnMobile = document.getElementById('openEditProfileMobile');
    const modal = document.getElementById('editProfileModal');
    const closeBtn = document.getElementById('closeEditProfile');
    const cancelBtn = document.getElementById('cancelEditProfile');
    const overlay = document.getElementById('editOverlay');

    function openModal(){
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        const first = modal.querySelector('input[name="name"]');
        if (first) first.focus();
    }
    function closeModal(){
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (openBtnMobile) openBtnMobile.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    // avatar preview for both profile and modal
    document.querySelectorAll('input[type=file][name=avatar]').forEach(input => {
        input.addEventListener('change', function(e){
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev){
                const large = document.getElementById('avatarLarge');
                const preview = document.getElementById('avatarPreview');
                if (large) large.innerHTML = `<img src="${ev.target.result}" class="object-cover w-full h-full" />`;
                if (preview) preview.innerHTML = `<img src="${ev.target.result}" class="object-cover w-full h-full" />`;
            }
            reader.readAsDataURL(file);
        });
    });

    // Gender dropdown (styled to match the Received Orders filter dropdowns)
    function positionEditProfileDropdown(id) {
        const dropdown = document.getElementById(id);
        const button = document.getElementById('editProfileGenderButton');
        if (!dropdown || !button) return;
        const rect = button.getBoundingClientRect();
        const dropdownHeight = dropdown.offsetHeight || 160;
        const spaceBelow = window.innerHeight - rect.bottom;
        const openUpward = spaceBelow < dropdownHeight && rect.top > dropdownHeight;

        dropdown.style.left = rect.left + 'px';
        dropdown.style.width = rect.width + 'px';
        if (openUpward) {
            dropdown.style.top = (rect.top - dropdownHeight - 6) + 'px';
        } else {
            dropdown.style.top = (rect.bottom + 6) + 'px';
        }
    }

    function toggleEditProfileDropdown(id) {
        const dropdown = document.getElementById(id);
        if (!dropdown) return;
        const isHidden = dropdown.classList.contains('hidden');
        document.querySelectorAll('#editProfileModal .dropdown-menu').forEach(d => d.classList.add('hidden'));
        if (isHidden) {
            dropdown.classList.remove('hidden');
            positionEditProfileDropdown(id);
        }
    }

    window.addEventListener('resize', () => positionEditProfileDropdown('editProfileGenderDropdown'));
    document.getElementById('editProfileModalBody')?.addEventListener('scroll', () => {
        const dd = document.getElementById('editProfileGenderDropdown');
        if (dd && !dd.classList.contains('hidden')) positionEditProfileDropdown('editProfileGenderDropdown');
    });

    function selectEditProfileDropdown(event, inputId, value, buttonId, label, dropdownId) {
        if (event) { event.preventDefault(); event.stopPropagation(); }
        document.getElementById(inputId).value = value;
        document.getElementById(buttonId).querySelector('span').textContent = label;
        document.getElementById(dropdownId).classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown-wrapper="editProfileGender"]')) {
            const dd = document.getElementById('editProfileGenderDropdown');
            if (dd) dd.classList.add('hidden');
        }
    });
</script>
</x-layouts.app>