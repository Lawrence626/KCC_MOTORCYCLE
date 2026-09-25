<x-layouts.app :title="__('Archived Shelves')">
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
        .product-chip { background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.12); color: var(--brand-dark); font-size: 0.78rem; padding: 0.35rem 0.6rem; border-radius: 0.8rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; }
        .product-chip .left { display:flex; flex-direction:column; gap:0.08rem; }
        .product-chip .name { font-weight:600; font-size:0.84rem; color:#0f172a; }
        .product-chip .meta { font-size:0.62rem; color:#475569; }
        .product-chip .qty { font-weight:700; font-size:0.84rem; color:#0f172a; margin-left:0.4rem; min-width:44px; text-align:right; }
        .btn-primary { background: linear-gradient(90deg, var(--brand), var(--brand-dark)); color: #fff; box-shadow: 0 4px 15px rgba(15,118,110,0.25); transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.35); }
        .btn-secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); transition: all 0.2s ease; }
        .btn-secondary:hover { background: #f1f5f9; border-color: rgba(148,163,184,0.5); }
        .btn-danger { background: linear-gradient(90deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 15px rgba(239,68,68,0.25); transition: all 0.2s ease; }
        .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(239,68,68,0.35); }
        .btn-success { background: linear-gradient(90deg, #10b981, #059669); color: #fff; box-shadow: 0 4px 15px rgba(16,185,129,0.25); transition: all 0.2s ease; }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,0.35); }
    </style>


    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Archived Shelves</h1>
                <p class="text-xs text-slate-500 mt-0.5">View and manage archived shop shelves.</p>
            </div>
            <a href="{{ route('shop.inventory') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 focus:outline-none transition-all duration-200 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Shop Inventory
            </a>

        </div>
    </x-slot>

    <div class="space-y-4">

        @if($archivedShelves->count() > 0)
            <div class="grid gap-6 mt-4">
                @foreach($archivedShelves as $shelf)
                <div class="si-card rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-md flex items-center justify-center text-white font-semibold bg-gray-400 text-sm">{{ strtoupper(substr($shelf->name, -1)) }}</div>
                                <div>
                                    <div class="text-base font-semibold text-slate-900">{{ $shelf->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $shelf->location ?? 'No location' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="restoreShelf({{ $shelf->id }})" class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-4 py-2 text-xs font-bold text-black hover:bg-[#59b2c2] ring-1 ring-slate-300 transition cursor-pointer">
                                Restore
                            </button>
                            <button onclick="deleteShelf({{ $shelf->id }})" class="inline-flex items-center gap-1.5 rounded-[10px] bg-rose-50 border border-rose-200/80 px-4 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition cursor-pointer">
                                Delete Permanently
                            </button>
                        </div>
                    </div>
                    
                    @if($shelf->shop_inventory && $shelf->shop_inventory->count() > 0)
                    <div class="mt-4 space-y-2">
                        <p class="text-xs font-medium text-gray-500">Products ({{ $shelf->occupied }} items):</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($shelf->shop_inventory->take(10) as $item)
                            <div class="product-chip">
                                <div class="left">
                                    <span class="name">{{ $item->product->name ?? 'Unknown Product' }}</span>
                                    <span class="meta">{{ $item->product->sku ?? '' }}</span>
                                </div>
                                <span class="qty">{{ $item->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                        @if($shelf->shop_inventory->count() > 10)
                        <p class="text-xs text-gray-400 mt-2">+{{ $shelf->shop_inventory->count() - 10 }} more</p>
                        @endif
                    </div>
                    @else
                    <p class="mt-4 text-sm text-gray-400">No products on this shelf</p>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <div class="si-card rounded-lg p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">No Archived Shelves</h3>
                <p class="text-sm text-gray-500">You haven't archived any shelves yet.</p>
                <a href="{{ route('shop.inventory') }}" class="inline-flex items-center gap-2 mt-4 text-black hover:text-slate-700 text-sm font-semibold">
                    Go to Shop Inventory
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>

    <script>
        async function restoreShelf(shelfId) {
            if (!confirm('Are you sure you want to restore this shelf?')) {
                return;
            }

            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Shelf restored successfully');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to restore shelf');
                }
            } catch (error) {
                alert('Error restoring shelf');
            }
        }

        async function deleteShelf(shelfId) {
            if (!confirm('Are you sure you want to permanently delete this shelf? This action cannot be undone and will delete all associated products.')) {
                return;
            }

            try {
                const response = await fetch(`/shop-inventory/shelf/${shelfId}/permanent`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('Shelf deleted permanently');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to delete shelf');
                }
            } catch (error) {
                alert('Error deleting shelf');
            }
        }

        // Notification Panel Toggle
        function toggleNotificationPanel(event) {
            event.stopPropagation();
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            if (panel) {
                const isHidden = panel.classList.contains('hidden');
                if (isHidden) {
                    panel.classList.remove('hidden');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                        profileDropdown.classList.remove('opacity-100', 'scale-100');
                        profileDropdown.classList.add('opacity-0', 'scale-95');
                    }
                } else {
                    panel.classList.add('hidden');
                }
            }
        }

        // Helper functions
        function markAllNotificationsRead() {
            // Placeholder for marking all notifications as read
            console.log('Mark all notifications as read');
        }

        function openAllNotificationsModal() {
            // Placeholder for opening all notifications modal
            console.log('Open all notifications modal');
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', function(event) {
            const panel = document.getElementById('notification-panel');
            const profileDropdown = document.getElementById('dashboardProfileDropdown');
            const notificationBell = document.getElementById('notification-bell-btn');
            const profileButton = document.getElementById('dashboardProfileButton');

            if (panel && !panel.contains(event.target) && notificationBell && !notificationBell.contains(event.target)) {
                panel.classList.add('hidden');
            }

            if (profileDropdown && !profileDropdown.contains(event.target) && profileButton && !profileButton.contains(event.target)) {
                profileDropdown.classList.add('hidden');
                profileDropdown.classList.remove('opacity-100', 'scale-100');
                profileDropdown.classList.add('opacity-0', 'scale-95');
            }
        });
    </script>
</x-layouts.app>
