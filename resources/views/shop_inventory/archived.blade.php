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

    <div id="dashboard-root" class="space-y-1.5">
        <!-- Fixed Header Strip (notification + profile only, radius on top-left only) -->
        <div id="dashboardStickyHeader" class="fixed top-0 left-0 right-0 md:left-[270px] z-[100000005] bg-white/95 backdrop-blur-md px-0 pt-3 sm:pt-4 pb-2 transition-shadow border-b border-slate-100" style="border-top-left-radius: 10px;">
            <div class="flex items-center justify-end">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center pr-4">
                    @if(auth()->check())
                        @if(!in_array(auth()->user()->role, ['cashier', 'warehouse_personnel']))
                        <div class="relative" id="notification-bell-wrapper">
                            <button
                                type="button"
                                id="notification-bell-btn"
                                class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                aria-label="Notifications"
                                onclick="toggleNotificationPanel(event)"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span
                                    id="notification-badge"
                                    class="absolute -top-0.5 -right-0.5 hidden rounded-full bg-red-500 text-[10px] font-bold text-white text-center"
                                    style="min-width: 18px; height: 18px; padding: 0 4px; display: none; align-items: center; justify-content: center; line-height: 1; text-align: center;"
                                ></span>
                            </button>

                            {{-- Notification Dropdown Panel --}}
                            <div
                                id="notification-panel"
                                class="hidden absolute right-0 top-full mt-2 w-[320px] rounded-xl bg-white border border-slate-200 shadow-2xl z-[100000006] flex flex-col"
                                style="max-height: 350px;"
                            >
                                <div class="sticky top-0 z-10 flex items-center justify-between px-4 py-3 border-b border-slate-800 rounded-t-xl bg-[#0f172a]" style="background-color: #0f172a;">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-white">Notifications</span>
                                        <span id="notif-center-unread-badge" class="hidden inline-flex items-center justify-center rounded-full text-[11px] font-bold text-white text-center" style="background-color: #ef4444; width: 20px; height: 20px; padding: 0; display: none; align-items: center; justify-content: center; line-height: 1; text-align: center; border-radius: 50%; box-sizing: border-box;">0</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button
                                            type="button"
                                            onclick="markAllNotificationsRead()"
                                            class="text-xs font-semibold text-slate-300 hover:text-white transition-colors"
                                        >Mark all as read</button>
                                    </div>
                                </div>
                                <div id="notification-list" class="flex-1 overflow-y-auto">
                                    {{-- Notifications rendered by JS --}}
                                </div>
                                <div id="notification-empty" class="hidden px-4 py-8 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#ccfbf1] mb-2">
                                        <svg class="w-5 h-5 text-[#0f766e]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800">All caught up</p>
                                    <p class="text-xs text-slate-500 mt-1">No new inventory alerts.</p>
                                </div>
                                <div class="sticky bottom-0 z-10 px-4 py-3 bg-slate-50 border-t border-slate-100 text-center rounded-b-xl">
                                    <button type="button" onclick="openAllNotificationsModal()" class="text-xs font-semibold text-slate-700 hover:text-slate-900 transition-colors">View All Notifications</button>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="relative inline-flex items-center gap-1.5 rounded-[20px] px-3 py-2 text-left">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-black grid place-items-center text-lg font-semibold overflow-hidden">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </span>
                            <div class="flex flex-col leading-tight">
                                <span class="text-sm font-semibold text-black">{{ auth()->user()->name ?? 'Admin' }}</span>
                                <span class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</span>
                            </div>
                            <button type="button" id="dashboardProfileButton" class="inline-flex h-7 w-7 items-center justify-center rounded-[12px] bg-transparent text-[#0f0f0f] transition-colors duration-200 focus:outline-none hover:bg-transparent focus:bg-transparent active:bg-transparent hover:text-slate-400 border-none cursor-pointer" style="background: transparent !important; border: none !important; box-shadow: none !important;" aria-label="Open profile menu">
                                <svg id="dashboardProfileArrow" class="w-5 h-5 text-current transition-colors duration-200" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z"/></svg>
                            </button>

                            <div id="dashboardProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] bg-[#0f0f0f] shadow-2xl shadow-black/20 z-[100000006] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right" style="color: #ffffff;">
                                <div class="px-4 py-4 border-b border-slate-700/60">
                                    <div class="flex items-center gap-3">
                                        <span class="w-12 h-12 rounded-full bg-gray-200 text-black grid place-items-center overflow-hidden text-lg font-semibold">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="text-[13px] font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</div>
                                            <div class="text-[12px] text-gray-400">{{ auth()->user()->email ?? '' }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="inline-flex items-center rounded-full border border-gray-600/30 bg-gray-800 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em]" style="color: #32FFFD;">
                                            {{ (auth()->user()->role ?? 'user') === 'admin' ? 'Administrator' : ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 px-2 py-2">
                                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition">
                                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </span>
                                        <span>View Profile</span>
                                    </a>
                                    <a href="{{ route('settings.general') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition">
                                        <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </span>
                                        <span>Settings</span>
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition">
                                            <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-700 text-white">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            </span>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between pl-3 lg:pl-2 pr-4 pt-2 pb-1 pl-1">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Archived Shelves</h1>
                <p class="mt-1 text-sm text-slate-500 font-medium">View and manage archived shop shelves.</p>
            </div>
        </div>

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
