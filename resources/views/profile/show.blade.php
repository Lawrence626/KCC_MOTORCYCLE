<x-layouts.app :title="__('My Profile')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">My Profile</h1>
                <p class="text-sm text-slate-500 mt-1">Manage your personal information and account settings</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <div id="avatarLarge" class="relative w-32 h-32 rounded-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 grid place-items-center text-3xl font-bold text-slate-400 border-4 border-slate-100 shadow-lg">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover w-full h-full" />
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                        @endif
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-slate-900">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ auth()->user()->email }}</p>
                    <div class="mt-3 inline-flex items-center rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">
                        {{ ucfirst(auth()->user()->role ?? 'user') }}
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                        <span class="text-sm text-slate-500">Member Since</span>
                        <span class="text-sm font-semibold text-slate-900">{{ auth()->user()->created_at?->format('M d, Y') ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50">
                        <span class="text-sm text-slate-500">Account Status</span>
                        <span class="text-sm font-semibold text-emerald-600">Active</span>
                    </div>
                </div>

                <div class="mt-6">
                    <button id="openEditProfile" class="w-full rounded-2xl bg-gradient-to-r from-cyan-600 to-cyan-500 px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg hover:from-cyan-700 hover:to-cyan-600">
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Details Card -->
            <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-slate-900">Personal Information</h2>
                    <p class="text-sm text-slate-500 mt-1">Your account details and contact information</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Full Name</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Email Address</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Contact Number</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->contact ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Address</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->address ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Age</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->age ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Gender</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">{{ auth()->user()->gender ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="editOverlay" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 p-8 z-10">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h3 class="text-2xl font-semibold text-slate-900">Edit Profile</h3>
                <p class="text-sm text-slate-500 mt-1">Update your personal information and profile picture</p>
            </div>
            <button id="closeEditProfile" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Avatar Upload Section -->
            <div class="flex flex-col items-center justify-center p-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 hover:bg-slate-100 transition">
                <div id="avatarPreview" class="relative w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 grid place-items-center text-2xl font-semibold text-slate-400 border-4 border-white shadow-lg mb-4">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover w-full h-full" />
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                    @endif
                </div>
                <label class="cursor-pointer">
                    <span class="text-sm font-medium text-cyan-600 hover:text-cyan-700">Change Profile Photo</span>
                    <input type="file" name="avatar" accept="image/*" class="hidden" />
                </label>
                <p class="text-xs text-slate-400 mt-2">JPG, PNG or GIF. Max 5MB.</p>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <input name="email" value="{{ old('email', auth()->user()->email) }}" type="email" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Contact Number</label>
                    <input name="contact" value="{{ old('contact', auth()->user()->contact) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Address</label>
                    <input name="address" value="{{ old('address', auth()->user()->address) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Age</label>
                    <input name="age" value="{{ old('age', auth()->user()->age) }}" type="number" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Gender</label>
                    <select name="gender" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition">
                        <option value="">Prefer not to say</option>
                        <option value="Male" {{ old('gender', auth()->user()->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', auth()->user()->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', auth()->user()->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <!-- Password Section -->
            <div class="border-t border-slate-200 pt-6">
                <h4 class="text-sm font-semibold text-slate-900 mb-4">Change Password</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                        <input name="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" placeholder="Leave blank to keep current" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                        <input name="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 transition" placeholder="Leave blank to keep current" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="flex-1 rounded-2xl bg-gradient-to-r from-cyan-600 to-cyan-500 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg hover:from-cyan-700 hover:to-cyan-600">
                    Save Changes
                </button>
                <button type="button" id="cancelEditProfile" class="rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal open/close
    const openBtn = document.getElementById('openEditProfile');
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
</script>
</x-layouts.app>

