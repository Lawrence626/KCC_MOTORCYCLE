<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KCC Motorcycle' }}</title>
    @include('partials.head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 h-dvh m-0 overflow-hidden">
    @php
        $isCashier = auth()->check() && auth()->user()->role === 'cashier';
        $isInventoryClerk = auth()->check() && auth()->user()->role === 'inventory_clerk';
        $isWarehousePersonnel = auth()->check() && auth()->user()->role === 'warehouse_personnel';
        $hasTopNavbar = false;
    @endphp
    <div class="flex h-full relative">
        @if(!$hasTopNavbar)
            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="md:hidden fixed top-4 left-4 z-40 p-2 text-white bg-slate-800 rounded-lg hover:bg-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Mobile Overlay (backdrop) -->
            <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

            <!-- Sidebar -->
            <div id="sidebar-wrapper" class="fixed top-0 left-0 w-70 h-full -translate-x-full md:translate-x-0 transition-transform duration-300 z-30">
                @include('sidebar')
            </div>
        @endif

        <!-- Main Content -->
        <div class="flex-1 min-w-0 flex flex-col {{ $hasTopNavbar ? 'md:ml-0 bg-white' : 'md:ml-[270px] bg-white rounded-tl-[12px] rounded-bl-[12px]' }} relative z-40 h-full overflow-hidden">
            @if(!$hasTopNavbar)
                <!-- Header Container - Optional if a header slot is provided -->
                @if(!empty($header))
            <div id="dashboardHeader" class="flex-shrink-0 bg-white border-b border-slate-200 px-5 py-2 sticky top-0 z-10 transition-all duration-200 shadow-none rounded-tl-[12px]">
                @auth
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div class="flex-1 min-w-0">
                            {{ $header }}
                        </div>
                        <div class="flex items-center gap-3 md:mt-2 mt-2">
                            <!-- Theme Toggle Button -->
                            <div class="relative inline-flex items-center z-50">
                                <button id="headerThemeToggle" type="button" class="relative inline-flex h-9 w-9 items-center justify-center border border-slate-700 text-white transition hover:border-cyan-500 focus:outline-none" style="border-radius: 20px; background-color: #0f0f0f;" aria-label="Toggle Dark Mode" title="Toggle theme">
                                    <!-- Sun Icon (shows in dark mode) -->
                                    <svg id="themeIconSun" class="w-5 h-5 text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <!-- Moon Icon (shows in light mode) -->
                                    <svg id="themeIconMoon" class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="relative inline-flex items-center z-50">
                                <button id="headerNotificationButton" type="button" class="relative inline-flex h-9 w-9 items-center justify-center border border-slate-700 text-white transition focus:outline-none cursor-pointer" style="border-radius: 20px; background-color: #0f0f0f;" aria-label="Notifications">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a6 6 0 00-6 6v4.586l-1.707 1.707A1 1 0 005 16h14a1 1 0 00.707-1.707L18 12.586V8a6 6 0 00-6-6zm0 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 20z"/></svg>
                                    <span id="headerNotificationBadge" class="absolute -top-1 -right-1 hidden rounded-full bg-rose-600 text-[10px] font-bold text-white text-center" style="min-width: 18px; height: 18px; padding: 0 4px; display: none; align-items: center; justify-content: center; line-height: 1; text-align: center;">0</span>
                                </button>
                                <div id="headerNotificationDropdown" class="absolute right-0 top-full z-[99999] mt-2 w-[24rem] overflow-hidden rounded-[15px] border border-slate-700/60 bg-gradient-to-b from-[#0b0c10] to-[#20232a] shadow-2xl shadow-black/40 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                                    <div class="px-4 py-4 border-b border-slate-800 bg-[#0f172a]" style="background-color: #0f172a;">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-bold text-white">Inventory Notifications</p>
                                                <p class="text-xs text-slate-300">Recent stock alerts and reminders.</p>
                                            </div>
                                            <button id="headerNotificationClose" type="button" class="text-slate-400 transition hover:text-white font-bold text-lg" aria-label="Close notifications">×</button>
                                        </div>
                                    </div>
                                    <div id="headerNotificationList" class="max-h-80 overflow-y-auto">
                                        <div class="p-4 text-sm text-slate-400">Loading notifications…</div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative inline-flex items-center z-50">
                                <button id="headerProfileButton" type="button" class="inline-flex h-9 items-center gap-2 border border-slate-700 px-3 text-white transition focus:outline-none cursor-pointer" style="border-radius: 20px; background-color: #0f0f0f;">
                                    <span class="w-5.5 h-5.5 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-sm font-semibold">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}?v={{ auth()->user()->updated_at?->timestamp }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                                        @else
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </span>
                                    <span class="hidden sm:inline-block text-[14px] font-medium text-white">{{ auth()->user()->name ?? 'User' }}</span>
                                    <svg id="headerProfileArrow" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7 10l5 5 5-5H7z" />
                                    </svg>
                                </button>

                                <div id="headerProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] border border-slate-700/60 bg-gradient-to-b from-[#0b0c10] to-[#20232a] shadow-2xl shadow-black/40 z-[99999] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                                    <div class="px-4 py-4 border-b border-slate-800/60">
                                        <div class="flex items-center gap-3">
                                            <span class="w-12 h-12 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-lg font-semibold">
                                                @if(auth()->user()->avatar)
                                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}?v={{ auth()->user()->updated_at?->timestamp }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                                                @else
                                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                                @endif
                                            </span>
                                            <div>
                                                <div class="text-[13px] font-semibold text-white">{{ auth()->user()->name ?? 'Admin' }}</div>
                                                <div class="text-[12px] text-slate-400">{{ auth()->user()->email ?? '' }}</div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                                <span class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-2 py-0.5 text-xs font-semibold uppercase tracking-wider text-cyan-300">
                                                {{ (auth()->user()->role ?? 'user') === 'admin' ? 'Administrator' : ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1 px-2 py-2">
                                        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                                            <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </span>
                                            <span>View Profile</span>
                                        </a>
                                        <a href="{{ route('settings.general') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                                            <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </span>
                                            <span>Settings</span>
                                        </a>
                                        <button type="button" onclick="window.toggleTheme()" class="flex w-full items-center justify-between rounded-[10px] px-2.5 py-2.5 text-sm text-slate-100 hover:bg-slate-800 transition">
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-slate-300">
                                                    <svg id="dropdownThemeIcon" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                                    </svg>
                                                </span>
                                                <span id="dropdownThemeLabel">Dark Mode</span>
                                            </div>
                                            <span id="dropdownThemeStatus" class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-medium">Off</span>
                                        </button>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm text-rose-300 hover:bg-slate-800 transition">
                                                <span class="w-6 h-6 grid place-items-center rounded-full bg-slate-950 text-rose-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                </span>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
            @endif
            @endif

            <!-- Content Container - Scrollable -->
            @php
                $flush = $flush ?? false;
                $stretch = $stretch ?? true;
            @endphp
            <div id="mainScrollArea" class="flex-1 min-w-0 min-h-0 overflow-y-auto relative flex flex-col z-40 bg-white rounded-bl-[12px]">
                @if($hasTopNavbar)
                    @include('partials.cashier-navbar')
                @endif
                <div id="topScrollFade" class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-white/95 via-white/70 to-transparent opacity-0 transition-opacity duration-200"></div>
                <div class="w-full min-w-0 max-w-full flex-1 {{ $stretch ? 'flex flex-col' : '' }}">
                    @if($flush)
                        {{ $slot }}
                    @else
                        <div class="bg-white w-full flex-1 px-4 sm:px-8 py-4 sm:py-6 space-y-8 overflow-hidden min-w-0 min-h-0 {{ $stretch ? 'flex flex-col' : '' }}">
                            <div class="space-y-8 min-w-0 {{ $stretch ? 'flex-1' : 'h-full' }}">
                                {{ $slot }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.admin-purchase-order-toasts')

    <script>
        (function() {
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const sidebarWrapper = document.getElementById('sidebar-wrapper');
            const mobileOverlay = document.getElementById('mobile-overlay');

            // Toggle sidebar on mobile
            if (menuToggle && sidebarWrapper && mobileOverlay) {
                menuToggle.addEventListener('click', function() {
                    sidebarWrapper.classList.toggle('-translate-x-full');
                    mobileOverlay.classList.toggle('hidden');
                });

                // Close sidebar when clicking overlay
                mobileOverlay.addEventListener('click', function() {
                    sidebarWrapper.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                });

                // Close sidebar when clicking a link
                const links = sidebarWrapper.querySelectorAll('a');
                links.forEach(link => {
                    link.addEventListener('click', function() {
                        sidebarWrapper.classList.add('-translate-x-full');
                        mobileOverlay.classList.add('hidden');
                    });
                });
            }

            // Header profile dropdown
            const headerProfileButton = document.getElementById('headerProfileButton');
            const headerProfileDropdown = document.getElementById('headerProfileDropdown');
            const headerProfileArrow = document.getElementById('headerProfileArrow');
            const headerNotificationButton = document.getElementById('headerNotificationButton');
            const headerNotificationDropdown = document.getElementById('headerNotificationDropdown');
            const headerNotificationClose = document.getElementById('headerNotificationClose');
            const dashboardProfileButton = document.getElementById('dashboardProfileButton');
            const dashboardProfileDropdown = document.getElementById('dashboardProfileDropdown');
            const dashboardProfileArrow = document.getElementById('dashboardProfileArrow');

            function closeProfileDropdown() {
                if (!headerProfileDropdown) return;
                headerProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                headerProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                if (headerProfileArrow) {
                    headerProfileArrow.classList.remove('text-cyan-400');
                    headerProfileArrow.classList.add('text-white');
                }
                if (headerProfileButton) headerProfileButton.blur();
            }

            function openProfileDropdown() {
                if (!headerProfileDropdown) return;
                headerProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                headerProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
                if (headerProfileArrow) {
                    headerProfileArrow.classList.remove('text-white');
                    headerProfileArrow.classList.add('text-cyan-400');
                }
            }

            function closeNotificationDropdown() {
                if (!headerNotificationDropdown) return;
                headerNotificationDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                headerNotificationDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                if (headerNotificationButton) headerNotificationButton.blur();
            }

            function openNotificationDropdown() {
                if (!headerNotificationDropdown) return;
                headerNotificationDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                headerNotificationDropdown.classList.add('block', 'opacity-100', 'scale-100');
            }

            if (headerProfileButton && headerProfileDropdown) {
                headerProfileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                headerProfileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = !headerProfileDropdown.classList.contains('hidden');
                    if (isOpen) {
                        closeProfileDropdown();
                    } else {
                        openProfileDropdown();
                    }
                });

                window.addEventListener('click', function(e) {
                    if (!headerProfileDropdown.contains(e.target) && !headerProfileButton.contains(e.target)) {
                        closeProfileDropdown();
                    }
                });
            }

            if (headerNotificationButton && headerNotificationDropdown) {
                headerNotificationDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                headerNotificationButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = !headerNotificationDropdown.classList.contains('hidden');
                    if (isOpen) {
                        closeNotificationDropdown();
                    } else {
                        openNotificationDropdown();
                    }
                });

                if (headerNotificationClose) {
                    headerNotificationClose.addEventListener('click', function() {
                        closeNotificationDropdown();
                    });
                }

                window.addEventListener('click', function(e) {
                    if (!headerNotificationDropdown.contains(e.target) && !headerNotificationButton.contains(e.target)) {
                        closeNotificationDropdown();
                    }
                });
            }

            // Dashboard profile dropdown handlers
            if (dashboardProfileButton && dashboardProfileDropdown) {
                dashboardProfileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

                dashboardProfileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = !dashboardProfileDropdown.classList.contains('hidden');
                    if (isOpen) {
                        dashboardProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                        dashboardProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                        dashboardProfileButton.blur();
                    } else {
                        dashboardProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                        dashboardProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
                    }
                });

                window.addEventListener('click', function(e) {
                    if (!dashboardProfileDropdown.contains(e.target) && !dashboardProfileButton.contains(e.target)) {
                        dashboardProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                        dashboardProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                        dashboardProfileButton.blur();
                    }
                });
            }
        })();
    </script>

    <script>
        // ── Global Header Notification Bell (API-driven) ────────────
        (function() {
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            var token = csrfToken ? csrfToken.getAttribute('content') : '';

            function _timeAgo(dateStr) {
                if (!dateStr) return '';
                var now = new Date();
                var date = new Date(dateStr);
                var diffSec = Math.floor((now - date) / 1000);
                if (diffSec < 60) return 'Just now';
                var diffMin = Math.floor(diffSec / 60);
                if (diffMin < 60) return diffMin + (diffMin === 1 ? ' minute ago' : ' minutes ago');
                var diffHr = Math.floor(diffMin / 60);
                if (diffHr < 24) return diffHr + (diffHr === 1 ? ' hour ago' : ' hours ago');
                var diffDay = Math.floor(diffHr / 24);
                if (diffDay < 7) return diffDay + (diffDay === 1 ? ' day ago' : ' days ago');
                return date.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
            }

            function loadHeaderNotifications() {
                fetch('/api/inventory-notifications?limit=20', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    renderHeaderNotifications(data.notifications || [], data.unread_count || 0);
                })
                .catch(function() {});
            }

            function renderHeaderNotifications(notifications, unreadCount) {
                var badge = document.getElementById('headerNotificationBadge');
                var list = document.getElementById('headerNotificationList');
                if (!list) return;

                if (badge) {
                    if (unreadCount > 0) {
                        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                        badge.classList.remove('hidden');
                        badge.style.display = 'inline-flex';
                    } else {
                        badge.classList.add('hidden');
                        badge.style.display = 'none';
                    }
                }

                if (!notifications || notifications.length === 0) {
                    list.innerHTML = '<div class="px-4 py-8 text-center"><svg class="w-8 h-8 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg><p class="text-xs text-slate-500">No inventory notifications.</p></div>';
                    return;
                }

                list.innerHTML = notifications.map(function(n) {
                    var isCritical = n.notification_type === 'out_of_stock';
                    var iconSVG = isCritical 
                        ? '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" fill="#dc2626"></path><line x1="12" y1="9" x2="12" y2="13" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"></line><circle cx="12" cy="16.5" r="1.1" fill="#ffffff"></circle></svg>'
                        : '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9.5" fill="#d97706"></circle><line x1="12" y1="7.5" x2="12" y2="12.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"></line><circle cx="12" cy="16" r="1.1" fill="#ffffff"></circle></svg>';
                    var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                    var ago = _timeAgo(n.created_at);
                    var stockText = isCritical ? '0 remaining' : (n.current_stock || 0) + ' remaining';
                    var isUnread = n.status === 'unread';
                    var bgClass = isUnread ? '' : (n.status === 'resolved' ? 'opacity-60' : '');
                    var resolvedMark = n.status === 'resolved'
                        ? '<span class="text-[10px] text-emerald-400 font-medium">✓ Resolved</span>'
                        : '';
                    var orderBtn = n.status !== 'resolved'
                        ? '<a href="' + (n.order_url || '/purchase-order/create') + '" class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[10px] font-semibold text-white transition hover:bg-emerald-700">Order Now</a>'
                        : resolvedMark;

                    return '<div class="border-b border-slate-800/40 px-4 py-3 last:border-b-0 ' + bgClass + '" data-header-notif-id="' + n.id + '">' +
                        '<div class="flex items-start gap-3">' +
                            '<div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-[10px] flex items-center justify-center" style="background-color: rgba(110, 193, 209, 0.18);">' +
                                iconSVG +
                            '</div>' +
                            '<div class="flex-1 min-w-0">' +
                                '<div class="flex items-center justify-between">' +
                                    '<span class="text-[9px] font-bold uppercase tracking-wider text-slate-700">' + typeLabel + '</span>' +
                                    (isUnread ? '<span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>' : '') +
                                '</div>' +
                                '<p class="text-[13px] font-semibold text-white mt-0.5 truncate">' + (n.product_name || 'Product') + '</p>' +
                                '<p class="text-[10px] text-slate-400 mt-0.5">SKU: ' + (n.sku || '') + ' · ' + stockText + '</p>' +
                                '<div class="flex items-center justify-between mt-2">' +
                                    '<span class="text-[10px] ' + (isUnread ? 'font-bold text-white' : 'text-slate-500') + '">' + ago + '</span>' +
                                    orderBtn +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                }).join('');
            }

            // Load on open
            var hBtn = document.getElementById('headerNotificationButton');
            if (hBtn) {
                hBtn.addEventListener('click', function() {
                    var dropdown = document.getElementById('headerNotificationDropdown');
                    if (dropdown && dropdown.classList.contains('hidden')) {
                        loadHeaderNotifications();
                    }
                });
            }

            // Initial badge load
            fetch('/api/inventory-notifications/unread-count', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var badge = document.getElementById('headerNotificationBadge');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                        badge.classList.remove('hidden');
                        badge.style.display = 'inline-flex';
                    } else {
                        badge.classList.add('hidden');
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(function() {});
        })();

        // Teleport all fixed modals directly to document.body so that their backdrop blur
        // covers 100% of the screen without bottom white gaps or container clipping
        (function() {
            function teleportModals() {
                document.querySelectorAll('.fixed.inset-0').forEach(function(el) {
                    if (el.id !== 'mobile-overlay' && !el.closest('#sidebar-wrapper') && el.parentElement !== document.body) {
                        document.body.appendChild(el);
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', teleportModals);
            } else {
                teleportModals();
            }

            setTimeout(teleportModals, 100);
            setTimeout(teleportModals, 400);
            setTimeout(teleportModals, 1000);

            // Listen for any modal openings or dynamic modal creations
            var modalObserver = new MutationObserver(function() {
                teleportModals();
            });
            if (document.body) {
                modalObserver.observe(document.body, { childList: true, subtree: true });
            }
        })();

        // Global Theme Management
        (function() {
            function updateThemeUI(isDark) {
                const sunIcon = document.getElementById('themeIconSun');
                const moonIcon = document.getElementById('themeIconMoon');
                const dropdownStatus = document.getElementById('dropdownThemeStatus');
                const dropdownLabel = document.getElementById('dropdownThemeLabel');
                const dropdownIcon = document.getElementById('dropdownThemeIcon');

                if (sunIcon && moonIcon) {
                    if (isDark) {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }

                if (dropdownStatus) {
                    dropdownStatus.textContent = isDark ? 'On' : 'Off';
                    dropdownStatus.className = isDark 
                        ? 'text-xs px-2 py-0.5 rounded-full bg-cyan-950/80 text-cyan-400 font-medium border border-cyan-500/30' 
                        : 'text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-medium';
                }
                if (dropdownLabel) {
                    dropdownLabel.textContent = isDark ? 'Light Mode' : 'Dark Mode';
                }
                if (dropdownIcon) {
                    dropdownIcon.innerHTML = isDark
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />';
                }
            }

            window.toggleTheme = function() {
                const isDark = document.documentElement.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';
                if (newTheme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                try {
                    localStorage.setItem('theme', newTheme);
                } catch (e) {}
                updateThemeUI(newTheme === 'dark');
                window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
            };

            window.setTheme = function(theme) {
                let isDark = false;
                if (theme === 'system') {
                    try { localStorage.removeItem('theme'); } catch(e) {}
                    isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                } else {
                    try { localStorage.setItem('theme', theme); } catch(e) {}
                    isDark = (theme === 'dark');
                }

                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                updateThemeUI(isDark);
                window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: isDark ? 'dark' : 'light' } }));
            };

            document.addEventListener('DOMContentLoaded', function() {
                const isDark = document.documentElement.classList.contains('dark');
                updateThemeUI(isDark);

                const btn = document.getElementById('headerThemeToggle');
                if (btn) {
                    btn.addEventListener('click', window.toggleTheme);
                }
            });
        })();

        // ══════════════════════════════════════════════════════════════
        // ── GLOBAL NOTIFICATION PANEL HANDLERS ───────────────────────
        // ══════════════════════════════════════════════════════════════
        (function() {
            window.escHtml = window.escHtml || function(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            window.timeAgo = window.timeAgo || function(dateStr) {
                if (!dateStr) return '';
                var now = new Date();
                var date = new Date(dateStr);
                var diffSec = Math.floor((now - date) / 1000);
                if (diffSec < 60) return 'Just now';
                var diffMin = Math.floor(diffSec / 60);
                if (diffMin < 60) return diffMin + (diffMin === 1 ? ' min ago' : ' mins ago');
                var diffHr = Math.floor(diffMin / 60);
                if (diffHr < 24) return diffHr + (diffHr === 1 ? ' hr ago' : ' hrs ago');
                var diffDay = Math.floor(diffHr / 24);
                if (diffDay < 7) return diffDay + (diffDay === 1 ? ' day ago' : ' days ago');
                return date.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
            };

            window.loadNotificationCenter = function() {
                fetch('/api/inventory-notifications?limit=30', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    window.renderNotificationCenter(data.notifications || [], data.unread_count || 0);
                })
                .catch(function(err) { console.error('Failed to load notifications:', err); });
            };

            window.renderNotificationCenter = function(notifications, unreadCount) {
                var panel = document.getElementById('notification-list');
                var empty = document.getElementById('notification-empty');
                var badge = document.getElementById('notification-badge');
                var centerBadge = document.getElementById('notif-center-unread-badge');
                if (!panel) return;

                if (badge) {
                    if (unreadCount > 0) {
                        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                if (centerBadge) {
                    if (unreadCount > 0) {
                        centerBadge.textContent = unreadCount;
                        centerBadge.classList.remove('hidden');
                        centerBadge.style.display = 'inline-flex';
                        centerBadge.style.alignItems = 'center';
                        centerBadge.style.justifyContent = 'center';
                        if (unreadCount > 9) {
                            centerBadge.style.width = 'auto';
                            centerBadge.style.padding = '0 6px';
                        } else {
                            centerBadge.style.width = '20px';
                            centerBadge.style.padding = '0';
                        }
                    } else {
                        centerBadge.classList.add('hidden');
                        centerBadge.style.display = 'none';
                    }
                }

                panel.innerHTML = '';
                if (!notifications || notifications.length === 0) {
                    if (empty) empty.classList.remove('hidden');
                    return;
                }
                if (empty) empty.classList.add('hidden');

                notifications.forEach(function(n) {
                    var isCritical = n.notification_type === 'out_of_stock';
                    var iconSVG = isCritical 
                        ? '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" fill="#dc2626"></path><line x1="12" y1="9" x2="12" y2="13" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"></line><circle cx="12" cy="16.5" r="1.1" fill="#ffffff"></circle></svg>'
                        : '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9.5" fill="#d97706"></circle><line x1="12" y1="7.5" x2="12" y2="12.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"></line><circle cx="12" cy="16" r="1.1" fill="#ffffff"></circle></svg>';
                    var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                    var statusClass = 'notif-item notif-item-' + n.status;
                    var ago = window.timeAgo(n.created_at);
                    var stockText = isCritical ? '0 left' : n.current_stock + ' remaining';
                    var shortSku = (n.sku || '').length > 15 ? (n.sku || '').substring(0, 15) + '...' : (n.sku || '');

                    var item = document.createElement('div');
                    item.className = statusClass + ' px-4 py-3 cursor-pointer border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition-colors';
                    item.setAttribute('data-notif-id', n.id);

                    item.innerHTML =
                        '<div class="flex items-start gap-3">' +
                            '<div class="flex-shrink-0 w-8 h-8 rounded-[10px] flex items-center justify-center" style="background-color: rgba(110, 193, 209, 0.18);">' +
                                iconSVG +
                            '</div>' +
                            '<div class="flex-1 min-w-0">' +
                                '<div class="flex items-center justify-between mb-0.5">' +
                                    '<div class="flex items-center gap-1.5">' +
                                        '<span class="text-[10px] font-bold uppercase tracking-wider text-slate-700">' + typeLabel + '</span>' +
                                        (n.status === 'unread' ? '<span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>' : '') +
                                    '</div>' +
                                    '<span class="text-[10px] text-slate-400">' + window.escHtml(ago) + '</span>' +
                                '</div>' +
                                '<p class="text-[13px] font-semibold text-slate-900 truncate mb-1">' + window.escHtml(n.product_name) + '</p>' +
                                '<div class="flex items-center justify-between">' +
                                    '<div class="flex items-center gap-1.5 text-[11px] text-slate-500 whitespace-nowrap">' +
                                        '<span class="truncate max-w-[80px]">' + window.escHtml(shortSku) + '</span>' +
                                        '<span>&middot;</span>' +
                                        '<span class="font-medium whitespace-nowrap text-slate-600">' + stockText + '</span>' +
                                    '</div>' +
                                    (n.status !== 'resolved'
                                        ? '<a href="' + window.escHtml(n.order_url || '/purchase-order/create') + '" class="px-2.5 py-1 rounded bg-[#00ddd2] text-black text-[11px] font-semibold hover:bg-[#00c7bc] transition-colors flex-shrink-0" onclick="event.stopPropagation();">Order</a>'
                                        : '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-600 flex-shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Resolved</span>'
                                    ) +
                                '</div>' +
                            '</div>' +
                        '</div>';

                    if (n.status === 'unread') {
                        item.addEventListener('click', function() {
                            window.markNotificationRead(n.id);
                        });
                    }

                    panel.appendChild(item);
                });
            };

            window.markNotificationRead = function(notifId) {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fetch('/api/inventory-notifications/' + notifId + '/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(function() { window.loadNotificationCenter(); })
                .catch(function(err) { console.error('Mark read failed:', err); });
            };

            window.markAllNotificationsRead = function() {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fetch('/api/inventory-notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(function() { window.loadNotificationCenter(); })
                .catch(function(err) {
                    console.error('Mark all read failed:', err);
                    fetch('/api/inventory-notifications?limit=100', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var ids = (data.notifications || []).filter(function(n) { return n.status === 'unread'; }).map(function(n) { return n.id; });
                        return Promise.all(ids.map(function(id) {
                            return fetch('/api/inventory-notifications/' + id + '/read', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            });
                        }));
                    })
                    .then(function() { window.loadNotificationCenter(); });
                });
            };

            window.toggleNotificationPanel = function(e) {
                if (e) e.stopPropagation();
                var panel = document.getElementById('notification-panel');
                if (!panel) return;

                // Close profile dropdown first if open
                var profileDropdown = document.getElementById('dashboardProfileDropdown');
                if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
                    profileDropdown.classList.add('hidden');
                    profileDropdown.classList.add('opacity-0', 'scale-95');
                }

                var isHidden = panel.classList.contains('hidden');
                panel.classList.toggle('hidden');
                if (isHidden) window.loadNotificationCenter();
            };

            // Close notification panel when clicking outside
            document.addEventListener('click', function(e) {
                var wrapper = document.getElementById('notification-bell-wrapper');
                var panel = document.getElementById('notification-panel');
                if (panel && wrapper && !wrapper.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });

            // Load notification center badge on DOM load
            document.addEventListener('DOMContentLoaded', function() {
                if (document.getElementById('notification-bell-wrapper')) {
                    window.loadNotificationCenter();
                }
            });
        })();

        // Universal Auto-Dismiss for Success Alerts and Flash Messages across pages
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const autoDismissSelectors = [
                    '#success-toast',
                    '#pageSuccessAlert',
                    '#orderManagementSuccessAlert',
                    '#orderDetailSuccessAlert',
                    '#deadStockSuccessAlert',
                    '#deadStockShowSuccessAlert',
                    '#profileSuccessAlert',
                    '.alert-success'
                ];
                
                autoDismissSelectors.forEach(function(selector) {
                    document.querySelectorAll(selector).forEach(function(el) {
                        if (el && !el.dataset.dismissing) {
                            el.dataset.dismissing = 'true';
                            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(-10px)';
                            setTimeout(function() {
                                if (el.parentNode) {
                                    el.remove();
                                }
                            }, 400);
                        }
                    });
                });
            }, 3500);
        });
    </script>

    @stack('scripts')
</body>
</html>