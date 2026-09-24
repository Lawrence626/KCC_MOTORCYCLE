<x-layouts.app :title="__('Archived Suppliers')">
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

    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between pt-2 pb-1 pl-1">
            <div class="pl-3 lg:pl-1">
                <h1 class="text-3xl font-bold text-slate-900">Archived Suppliers</h1>
                <p class="text-gray-600 text-sm mt-1">View and manage archived supplier records.</p>
            </div>
        </div>

    @if(session('success'))
    <div id="success-toast" class="fixed top-4 right-4 z-50 rounded-[10px] border border-[#6EC1D1] bg-teal-50 p-4 text-sm font-medium text-slate-900 shadow-lg">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('success-toast');
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s ease';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3500);
    </script>
@endif

        <div class="rounded-[10px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            @if($archivedSuppliers->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <p class="mt-4 text-base font-semibold text-slate-900">No archived suppliers found.</p>
                    <p class="mt-1 text-sm text-slate-500">Archived supplier records will be listed here when removed from active listing.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="text-xs font-semibold uppercase tracking-wider text-white border-b border-slate-800 bg-[#0f172a]" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3">Supplier Name</th>
                                <th class="px-4 py-3">Contact Person</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Address</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($archivedSuppliers as $supplier)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3.5 font-semibold text-slate-900">{{ $supplier->name }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->contact_person ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->email ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->address ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-right">
                                        <form method="POST" action="{{ route('supplier.assessment.restore', ['supplier' => $supplier->id]) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all">
                                                Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($archivedSuppliers->hasPages())
                    <div class="p-4 flex items-center justify-between border-t border-slate-200">
                        <div class="text-xs text-slate-500">
                            Showing {{ $archivedSuppliers->firstItem() }} to {{ $archivedSuppliers->lastItem() }} of {{ $archivedSuppliers->total() }} results
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($archivedSuppliers->onFirstPage())
                                <span class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Previous</span>
                            @else
                                <a href="{{ $archivedSuppliers->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 transition-all">Previous</a>
                            @endif

                            @foreach($archivedSuppliers->getUrlRange(1, $archivedSuppliers->lastPage()) as $page => $url)
                                @if($page == $archivedSuppliers->currentPage())
                                    <span class="px-3 py-1.5 text-xs font-bold text-slate-900 bg-[#6EC1D1] border border-slate-200 shadow-sm rounded-[10px]">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 transition-all">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($archivedSuppliers->hasMorePages())
                                <a href="{{ $archivedSuppliers->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 transition-all">Next</a>
                            @else
                                <span class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Next</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
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