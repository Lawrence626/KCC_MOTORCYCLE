<x-layouts.app :title="__('My Profile')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
                <p class="text-xs text-slate-500 mt-1">Your personal information and profile picture</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <div id="avatarLarge" class="w-28 h-28 rounded-full overflow-hidden bg-slate-100 grid place-items-center text-2xl font-bold">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover w-full h-full" />
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                        @endif
                    </div>
                    <h3 class="mt-3 text-base font-semibold text-slate-900">{{ auth()->user()->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ auth()->user()->email }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ ucfirst(auth()->user()->role ?? 'user') }}</p>
                </div>

                <div class="mt-6 flex justify-center">
                    <button id="openEditProfile" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">Edit Profile</button>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-500">Full Name</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Contact</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->contact ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Address</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Age</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->age ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Gender</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->gender ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Role</p>
                        <p class="text-sm text-slate-900">{{ ucfirst(auth()->user()->role ?? 'user') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Member Since</p>
                        <p class="text-sm text-slate-900">{{ auth()->user()->created_at?->format('M d, Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="editOverlay" class="absolute inset-0 bg-black/40"></div>

    <div class="relative w-full max-w-xl bg-white rounded-xl shadow-xl border border-slate-200 p-6 z-10">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Edit Profile</h3>
                <p class="text-xs text-slate-500">Update your personal information</p>
            </div>
            <button id="closeEditProfile" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1 flex flex-col items-center">
                    <div id="avatarPreview" class="w-24 h-24 rounded-full overflow-hidden bg-slate-100 grid place-items-center text-xl font-semibold mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover w-full h-full" />
                        @else
                            {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                        @endif
                    </div>
                    <label class="text-xs text-slate-500 mb-2">Change Photo</label>
                    <input type="file" name="avatar" accept="image/*" class="text-sm" />
                </div>

                <div class="col-span-2">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500">Full Name</label>
                            <input name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" required />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Email</label>
                            <input name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" required />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Contact</label>
                            <input name="contact" value="{{ old('contact', auth()->user()->contact) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Address</label>
                            <input name="address" value="{{ old('address', auth()->user()->address) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Age</label>
                            <input name="age" value="{{ old('age', auth()->user()->age) }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Gender</label>
                            <select name="gender" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                                <option value="">Prefer not to say</option>
                                <option value="Male" {{ old('gender', auth()->user()->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', auth()->user()->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', auth()->user()->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500">New Password</label>
                            <input name="password" type="password" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Confirm Password</label>
                            <input name="password_confirmation" type="password" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" />
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold">Save changes</button>
                        <button type="button" id="cancelEditProfile" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-700">Cancel</button>
                    </div>
                </div>
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
