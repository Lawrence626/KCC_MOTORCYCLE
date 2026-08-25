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
    <div class="flex h-full relative">
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

        <!-- Main Content -->
        <div class="flex-1 min-w-0 flex flex-col md:ml-[270px] relative z-40 h-full">
            <!-- Header Container - Optional if a header slot is provided -->
            @if(!empty($header))
            <div id="dashboardHeader" class="flex-shrink-0 bg-white border border-slate-300 border-b-0 px-5 py-2 sticky top-0 z-10 transition-all duration-200 rounded-tl-[10px] rounded-tr-none shadow-none">
                @auth
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div class="flex-1">
                            {{ $header }}
                        </div>
                        <div class="flex items-center gap-3 md:mt-2 mt-2">
                            <div class="relative inline-flex items-center z-50">
                                <button id="headerNotificationButton" type="button" class="relative inline-flex h-9 w-9 items-center justify-center border border-slate-700 text-white transition focus:outline-none" style="border-radius: 20px; background-color: #0f0f0f;" aria-label="Notifications">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a6 6 0 00-6 6v4.586l-1.707 1.707A1 1 0 005 16h14a1 1 0 00.707-1.707L18 12.586V8a6 6 0 00-6-6zm0 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 20z"/></svg>
                                    <span id="headerNotificationBadge" class="absolute -top-1 -right-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-600 px-1.5 text-[10px] font-semibold text-white hidden">0</span>
                                </button>
                                <div id="headerNotificationDropdown" class="absolute right-0 top-full z-[99999] mt-2 w-[24rem] overflow-hidden rounded-[15px] border border-slate-700/60 bg-gradient-to-b from-[#0b0c10] to-[#20232a] shadow-2xl shadow-black/40 hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                                    <div class="px-4 py-4 border-b border-slate-800/60">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-white">Inventory Notifications</p>
                                                <p class="text-xs text-slate-400">Recent stock alerts and reminders.</p>
                                            </div>
                                            <button id="headerNotificationClose" type="button" class="text-slate-400 transition hover:text-slate-200" aria-label="Close notifications">×</button>
                                        </div>
                                    </div>
                                    <div id="headerNotificationList" class="max-h-80 overflow-y-auto">
                                        <div class="p-4 text-sm text-slate-400">Loading notifications…</div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative inline-flex items-center z-50">
                                <button id="headerProfileButton" type="button" class="inline-flex h-9 items-center gap-2 border border-slate-700 px-3 text-white transition focus:outline-none" style="border-radius: 20px; background-color: #0f0f0f;">
                                    <span class="w-5.5 h-5.5 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-sm font-semibold">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                                        @else
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        @endif
                                    </span>
                                    <span class="hidden sm:inline-block text-[14px] font-medium text-white">{{ auth()->user()->name ?? 'Admin' }}</span>
                                    <svg id="headerProfileArrow" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7 10l5 5 5-5H7z" />
                                    </svg>
                                </button>

                                <div id="headerProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] border border-slate-700/60 bg-gradient-to-b from-[#0b0c10] to-[#20232a] shadow-2xl shadow-black/40 z-[99999] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
                                    <div class="px-4 py-4 border-b border-slate-800/60">
                                        <div class="flex items-center gap-3">
                                            <span class="w-12 h-12 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-lg font-semibold">
                                                @if(auth()->user()->avatar)
                                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
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
                                            <span class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-cyan-300">
                                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
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

            <!-- Content Container - Scrollable -->
            @php
                $flush = $flush ?? false;
                $stretch = $stretch ?? true;
            @endphp
            <div id="mainScrollArea" class="flex-1 min-w-0 min-h-0 overflow-y-auto relative flex flex-col z-40">
                <div id="topScrollFade" class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-white/95 via-white/70 to-transparent opacity-0 transition-opacity duration-200"></div>
                <div class="w-full min-w-0 max-w-full flex-1 {{ $stretch ? 'flex flex-col' : '' }}">
                    @if($flush)
                        {{ $slot }}
                    @else
                        <div class="bg-white border border-slate-300 border-t-0 shadow-sm overflow-hidden flex-1 min-w-0 min-h-0 rounded-[10px] {{ $stretch ? 'flex flex-col' : '' }}">
                            <div class="px-8 py-6 space-y-8 min-w-0 {{ $stretch ? 'flex-1' : 'h-full' }}">
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
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebarWrapper = document.getElementById('sidebar-wrapper');
        const overlay = document.getElementById('mobile-overlay');

        // Toggle sidebar on mobile
        menuToggle.addEventListener('click', function() {
            sidebarWrapper.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function() {
            sidebarWrapper.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Close sidebar when clicking a link
        const links = sidebarWrapper.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                sidebarWrapper.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        });

        // Header profile dropdown
        const headerProfileButton = document.getElementById('headerProfileButton');
        const headerProfileDropdown = document.getElementById('headerProfileDropdown');
        const headerProfileArrow = document.getElementById('headerProfileArrow');
        const headerNotificationButton = document.getElementById('headerNotificationButton');
        const headerNotificationDropdown = document.getElementById('headerNotificationDropdown');
        const headerNotificationClose = document.getElementById('headerNotificationClose');
        const dashboardHeader = document.getElementById('dashboardHeader');
        const mainScrollArea = document.getElementById('mainScrollArea');
        const topScrollFade = document.getElementById('topScrollFade');
        const dashboardProfileButton = document.getElementById('dashboardProfileButton');
        const dashboardProfileDropdown = document.getElementById('dashboardProfileDropdown');
        const dashboardProfileArrow = document.getElementById('dashboardProfileArrow');

        function closeProfileDropdown() {
            headerProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            headerProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
            headerProfileArrow.classList.remove('text-cyan-400');
            headerProfileArrow.classList.add('text-white');
            headerProfileButton.blur();
        }

        function openProfileDropdown() {
            headerProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
            headerProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
            headerProfileArrow.classList.remove('text-white');
            headerProfileArrow.classList.add('text-cyan-400');
        }

        function closeNotificationDropdown() {
            headerNotificationDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            headerNotificationDropdown.classList.remove('block', 'opacity-100', 'scale-100');
            headerNotificationButton.blur();
        }

        function openNotificationDropdown() {
            headerNotificationDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
            headerNotificationDropdown.classList.add('block', 'opacity-100', 'scale-100');
        }

        if (headerProfileButton && headerProfileDropdown && headerProfileArrow) {
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

        // Floating profile dropdown handlers

        // Dashboard profile dropdown handlers
        if (dashboardProfileButton && dashboardProfileDropdown && dashboardProfileArrow) {
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
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                if (!notifications || notifications.length === 0) {
                    list.innerHTML = '<div class="px-4 py-8 text-center"><svg class="w-8 h-8 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg><p class="text-xs text-slate-500">No inventory notifications.</p></div>';
                    return;
                }

                list.innerHTML = notifications.map(function(n) {
                    var isCritical = n.notification_type === 'out_of_stock';
                    var emoji = isCritical ? '🔴' : '🟠';
                    var typeLabel = isCritical ? 'Out of Stock' : 'Low Stock';
                    var ago = _timeAgo(n.created_at);
                    var stockText = isCritical ? '0 remaining' : (n.current_stock || 0) + ' remaining';
                    var isUnread = n.status === 'unread';
                    var bgClass = isUnread ? 'bg-blue-500/5' : (n.status === 'resolved' ? 'opacity-60' : '');
                    var resolvedMark = n.status === 'resolved'
                        ? '<span class="text-[10px] text-emerald-400 font-medium">✓ Resolved</span>'
                        : '';
                    var orderBtn = n.status !== 'resolved'
                        ? '<a href="' + (n.order_url || '/purchase-order/create') + '" class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[10px] font-semibold text-white transition hover:bg-emerald-700">Order Now</a>'
                        : resolvedMark;

                    return '<div class="border-b border-slate-800/40 px-4 py-3 last:border-b-0 ' + bgClass + '" data-header-notif-id="' + n.id + '">' +
                        '<div class="flex items-start gap-3">' +
                            '<div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center" style="background:' + (isCritical ? 'rgba(239,68,68,0.15)' : 'rgba(249,115,22,0.15)') + ';">' +
                                '<span style="font-size:14px;">' + emoji + '</span>' +
                            '</div>' +
                            '<div class="flex-1 min-w-0">' +
                                '<div class="flex items-center justify-between">' +
                                    '<span class="text-[9px] font-bold uppercase tracking-wider ' + (isCritical ? 'text-red-400' : 'text-orange-400') + '">' + typeLabel + '</span>' +
                                    (isUnread ? '<span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>' : '') +
                                '</div>' +
                                '<p class="text-[13px] font-semibold text-white mt-0.5 truncate">' + (n.product_name || 'Product') + '</p>' +
                                '<p class="text-[10px] text-slate-400 mt-0.5">SKU: ' + (n.sku || '') + ' · ' + stockText + '</p>' +
                                '<div class="flex items-center justify-between mt-2">' +
                                    '<span class="text-[10px] text-slate-500">' + ago + '</span>' +
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
                if (badge && data.unread_count > 0) {
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.classList.remove('hidden');
                }
            })
            .catch(function() {});
        })();
    </script>

    @stack('scripts')
</body>
</html>