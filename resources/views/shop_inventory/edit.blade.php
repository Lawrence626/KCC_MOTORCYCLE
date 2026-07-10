<x-layouts.app :title="__('Edit Shelf')">
    <style>
        :root {
            --brand: #0f766e;
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #6b7280;
            --border: rgba(148,163,184,0.2);
        }
        .si-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .si-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .btn-primary { background: linear-gradient(90deg, var(--brand), var(--brand-dark)); color: #fff; box-shadow: 0 4px 15px rgba(15,118,110,0.25); transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.35); }
        .btn-secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); transition: all 0.2s ease; }
        .btn-secondary:hover { background: #f1f5f9; border-color: rgba(148,163,184,0.5); }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.85rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f766e; color: #fff; border-radius: 1rem; box-shadow: 0 18px 50px rgba(15,23,42,0.18); padding: 0.85rem 1rem; font-size: 0.95rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f766e; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 1rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="space-y-6">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
        
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Edit Shelf</h1>
                <p class="mt-2 text-sm text-gray-500">Update shelf information for <strong class="text-emerald-600">{{ $shelf->name }}</strong></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.inventory') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Shop Inventory
                </a>
            </div>
        </div>

        <div class="si-card rounded-lg p-6 shadow-sm">
            <form id="edit-shelf-form" action="{{ route('shop.inventory.update_shelf', $shelf->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                @if($errors->any())
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 mb-6">
                        <strong class="block font-semibold">Please fix the following:</strong>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name" value="{{ $shelf->name }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" id="location" name="location" value="{{ $shelf->location ?? '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                        <input type="number" id="capacity" name="capacity" value="{{ $shelf->capacity }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500" required min="1" max="100">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $shelf->description ?? '' }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('shop.inventory') }}" class="btn-secondary px-6 py-2 rounded-xl text-sm font-semibold">Cancel</a>
                    <button type="submit" id="save-button" class="btn-primary px-6 py-2 rounded-xl text-sm font-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()">✕</button>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        document.getElementById('edit-shelf-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const saveButton = document.getElementById('save-button');
            const originalText = saveButton.textContent;
            
            saveButton.disabled = true;
            saveButton.textContent = 'Saving...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message || 'Shelf updated successfully', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("shop.inventory") }}';
                    }, 1000);
                } else {
                    showToast(data.message || 'Failed to update shelf', 'error');
                    saveButton.disabled = false;
                    saveButton.textContent = originalText;
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
                saveButton.disabled = false;
                saveButton.textContent = originalText;
            }
        });
    </script>
</x-layouts.app>
