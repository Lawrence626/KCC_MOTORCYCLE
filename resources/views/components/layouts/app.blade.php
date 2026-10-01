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
<body class="h-dvh m-0 overflow-hidden" style="background: linear-gradient(to bottom, #000000, #2b2b2b);">
    @php
        $isCashier = auth()->check() && auth()->user()->role === 'cashier';
        $isInventoryClerk = auth()->check() && auth()->user()->role === 'inventory_clerk';
        $isWarehousePersonnel = auth()->check() && auth()->user()->role === 'warehouse_personnel';
        $hasTopNavbar = false;
    @endphp
    <div class="flex h-full relative" style="background: linear-gradient(to bottom, #000000, #2b2b2b);">
        @if(!$hasTopNavbar)
            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-toggle" class="md:hidden fixed top-4 left-4 z-50 p-2 text-white bg-slate-800 rounded-lg hover:bg-slate-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Mobile Overlay (backdrop) -->
            <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

            <!-- Sidebar -->
            <div id="sidebar-wrapper" class="fixed top-0 left-0 w-[270px] h-full -translate-x-full md:translate-x-0 transition-transform duration-300 z-50 md:z-30">
                @include('sidebar')
            </div>
        @endif

        <!-- Main Content -->
        <div class="flex-1 min-w-0 flex flex-col {{ $hasTopNavbar ? 'md:ml-0 bg-white' : 'md:ml-[270px] bg-white rounded-tl-[15px] rounded-bl-[15px] overflow-hidden' }} relative z-40 h-full" style="border-top-left-radius: 15px; border-bottom-left-radius: 15px;">
            @if(!$hasTopNavbar && auth()->check())
                <div id="globalHeader" class="flex-shrink-0 bg-white border-b border-slate-200 px-4 sm:px-6 py-2.5 sticky top-0 z-[999] flex items-center justify-between gap-4 rounded-tl-[15px]" style="border-top-left-radius: 15px;">
                    <div class="flex-1 min-w-0">
                        <x-breadcrumb />
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @if(!in_array(auth()->user()->role, ['cashier', 'warehouse_personnel']))
                        <div class="relative" id="notification-bell-wrapper">
                            <button
                                type="button"
                                id="notification-bell-btn"
                                class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
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

                            <div
                                id="notification-panel"
                                class="hidden absolute right-0 top-full mt-2 w-[320px] rounded-xl bg-white border border-slate-200 shadow-2xl z-[9999] flex flex-col"
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
                                <div id="notification-list" class="flex-1 overflow-y-auto"></div>
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

                        <div class="relative inline-flex items-center text-left">
                            <button type="button" id="dashboardProfileButton" class="inline-flex items-center gap-2 rounded-[18px] px-2.5 py-1.5 text-left bg-transparent border-none hover:bg-transparent transition-all focus:outline-none cursor-pointer group" aria-label="Open profile menu">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-900 grid place-items-center text-sm font-semibold overflow-hidden border border-slate-300 flex-shrink-0">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}?v={{ auth()->user()->updated_at?->timestamp }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                                    @else
                                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                    @endif
                                </span>
                                <div class="flex flex-col leading-tight text-left min-w-0">
                                    <span class="text-xs font-semibold text-slate-900 max-w-[130px] truncate">{{ auth()->user()->name ?? 'Admin' }}</span>
                                    <span class="text-[11px] text-slate-500 max-w-[150px] truncate">{{ auth()->user()->email ?? '' }}</span>
                                </div>
                                <svg id="dashboardProfileArrow" class="w-5 h-5 text-slate-500 transition-transform duration-200 group-hover:text-slate-700" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 10l5 5 5-5H7z"/>
                                </svg>
                            </button>

                            <div id="dashboardProfileDropdown" class="absolute right-0 top-full mt-2 rounded-[15px] bg-[#111111] shadow-xl shadow-black/30 z-[9999] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right border border-slate-800" style="min-width: 220px;">
                                <div class="px-4 py-4 border-b border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <span class="w-11 h-11 rounded-full bg-slate-100 text-slate-900 grid place-items-center overflow-hidden text-base font-semibold flex-shrink-0 border border-slate-300">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="text-[13px] font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2.5">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-[0.1em] bg-slate-800 text-cyan-400 border border-slate-700">
                                            {{ (auth()->user()->role ?? 'user') === 'admin' ? 'Administrator' : ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-0.5 px-2 py-2">
                                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                                        <span class="w-7 h-7 grid place-items-center rounded-full bg-slate-800 flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </span>
                                        <span>View Profile</span>
                                    </a>
                                    <a href="{{ route('settings.general') }}" class="flex items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                                        <span class="w-7 h-7 grid place-items-center rounded-full bg-slate-800 flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </span>
                                        <span>Settings</span>
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-3 rounded-[10px] px-2.5 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                                            <span class="w-7 h-7 grid place-items-center rounded-full bg-slate-800 flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            </span>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Content Container - Scrollable -->
            @php
                $flush = $flush ?? false;
                $stretch = $stretch ?? true;
            @endphp
<div id="mainScrollArea" class="flex-1 min-w-0 min-h-0 overflow-y-auto relative flex flex-col bg-white rounded-bl-[15px]" style="border-bottom-left-radius: 15px;">
                @if($hasTopNavbar)
                    @include('partials.cashier-navbar')
                @endif
                <div id="topScrollFade" class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-white/95 via-white/70 to-transparent opacity-0 transition-opacity duration-200"></div>
                <div class="w-full min-w-0 max-w-full flex-1 {{ $stretch ? 'flex flex-col' : '' }}">
                    @if(!empty($header))
                        <div id="modulePageHeader" class="px-4 sm:px-8 pt-5 pb-2">
                            {{ $header }}
                        </div>
                    @endif
                    @if($flush)
                        <div class="pt-0">
                            {{ $slot }}
                        </div>
                    @else
                        <div class="bg-white w-full flex-1 px-4 sm:px-8 {{ !empty($header) ? 'pt-2 pb-4 sm:pb-6' : 'py-4 sm:py-6' }} space-y-8 min-w-0 min-h-0 {{ $stretch ? 'flex flex-col' : '' }}" style="border-bottom-left-radius: 15px;">
                            <div class="space-y-8 min-w-0 {{ $stretch ? 'flex-1' : 'h-full' }} pt-0">
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

            // Dropdown & Action Elements
            const headerProfileButton = document.getElementById('headerProfileButton');
            const headerProfileDropdown = document.getElementById('headerProfileDropdown');
            const headerProfileArrow = document.getElementById('headerProfileArrow');
            const headerNotificationButton = document.getElementById('headerNotificationButton');
            const headerNotificationDropdown = document.getElementById('headerNotificationDropdown');
            const headerNotificationClose = document.getElementById('headerNotificationClose');
            const dashboardProfileButton = document.getElementById('dashboardProfileButton');
            const dashboardProfileTrigger = document.getElementById('dashboardProfileTrigger');
            const dashboardProfileDropdown = document.getElementById('dashboardProfileDropdown');
            const dashboardProfileArrow = document.getElementById('dashboardProfileArrow');

            function dismissAllNotifications() {
                var notifDropdown = document.getElementById('headerNotificationDropdown');
                if (notifDropdown) closeNotificationDropdown();
                var notifPanel = document.getElementById('notification-panel');
                if (notifPanel) notifPanel.classList.add('hidden');
                var toastContainer = document.getElementById('inventory-toast-container');
                if (toastContainer) toastContainer.innerHTML = '';
                document.querySelectorAll('[data-toast-notification]').forEach(function(t) {
                    t.remove();
                });
            }

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
                dismissAllNotifications();
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

            // Header profile dropdown event listeners
            if (headerProfileButton && headerProfileDropdown) {
                headerProfileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                headerProfileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dismissAllNotifications();
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

            // Header notification dropdown event listeners
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

            // Dashboard / Cashier profile dropdown handlers
            if (dashboardProfileButton && dashboardProfileDropdown) {
                dashboardProfileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

                function toggleDashboardProfileDropdown(e) {
                    if (e) e.stopPropagation();
                    dismissAllNotifications();

                    // Close Sales Range dropdown if open
                    const salesRangeDd = document.getElementById('salesRangeDropdown');
                    const salesRangeChevron = document.getElementById('salesRangeChevron');
                    if (salesRangeDd && !salesRangeDd.classList.contains('hidden')) {
                        salesRangeDd.classList.add('hidden');
                        if (salesRangeChevron) salesRangeChevron.style.transform = '';
                    }

                    // Close Sales Trend dropdown (in sales analytics) if open
                    const salesTrendDd = document.getElementById('salesTrendRangeDropdown');
                    const salesTrendChevron = document.getElementById('salesTrendRangeChevron');
                    if (salesTrendDd && !salesTrendDd.classList.contains('hidden')) {
                        salesTrendDd.classList.add('hidden');
                        if (salesTrendChevron) salesTrendChevron.style.transform = '';
                    }

                    // Close Order Tab dropdown (in order management) if open
                    const orderTabDd = document.getElementById('orderTabDropdown');
                    const orderTabChevron = document.getElementById('orderTabChevron');
                    if (orderTabDd && !orderTabDd.classList.contains('hidden')) {
                        orderTabDd.classList.add('hidden');
                        if (orderTabChevron) orderTabChevron.style.transform = '';
                    }

                    const isOpen = !dashboardProfileDropdown.classList.contains('hidden');
                    if (isOpen) {
                        dashboardProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                        dashboardProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                        dashboardProfileButton.blur();
                        if (dashboardProfileTrigger) dashboardProfileTrigger.setAttribute('aria-expanded', 'false');
                    } else {
                        dashboardProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                        dashboardProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
                        if (dashboardProfileTrigger) dashboardProfileTrigger.setAttribute('aria-expanded', 'true');
                    }
                }

                dashboardProfileButton.addEventListener('click', toggleDashboardProfileDropdown);

                if (dashboardProfileTrigger) {
                    dashboardProfileTrigger.addEventListener('click', toggleDashboardProfileDropdown);
                    dashboardProfileTrigger.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleDashboardProfileDropdown(e);
                        }
                    });
                }

                window.addEventListener('click', function(e) {
                    const triggerClicked = dashboardProfileTrigger && dashboardProfileTrigger.contains(e.target);
                    const buttonClicked = dashboardProfileButton && dashboardProfileButton.contains(e.target);
                    if (!dashboardProfileDropdown.contains(e.target) && !triggerClicked && !buttonClicked) {
                        dashboardProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                        dashboardProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                        dashboardProfileButton.blur();
                        if (dashboardProfileTrigger) dashboardProfileTrigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Notification panel toggle function
            window.toggleNotificationPanel = function(event) {
                event.stopPropagation();
                const panel = document.getElementById('notification-panel');
                const dropdown = document.getElementById('dashboardProfileDropdown');

                if (panel) {
                    const isHidden = panel.classList.contains('hidden');
                    if (isHidden) {
                        panel.classList.remove('hidden');
                        if (dropdown) {
                            dropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                            dropdown.classList.remove('block', 'opacity-100', 'scale-100');
                        }
                    } else {
                        panel.classList.add('hidden');
                    }
                }
            };

            // Mark all notifications as read
            window.markAllNotificationsRead = function() {
                console.log('Mark all notifications as read');
            };

            // Open all notifications modal
            window.openAllNotificationsModal = function() {
                console.log('Open all notifications modal');
            };

            // Close notification panel when clicking outside
            window.addEventListener('click', function(e) {
                const panel = document.getElementById('notification-panel');
                const bellBtn = document.getElementById('notification-bell-btn');
                if (panel && !panel.classList.contains('hidden') && !panel.contains(e.target) && !bellBtn.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });
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
                var userRole = @json(auth()->user()->role ?? '');
                if (userRole !== 'admin' && userRole !== 'inventory_clerk') return;

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
                        ? '<svg class="w-4 h-4 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                        : '<svg class="w-4 h-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
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
                            '<div class="mt-0.5 flex-shrink-0 w-8 h-8 flex items-center justify-center" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">' +
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
                        ? '<svg class="w-4 h-4 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                        : '<svg class="w-4 h-4 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                    var iconBgStyle = isCritical
                        ? 'background: linear-gradient(135deg, rgba(239, 68, 68, 0.06) 0%, rgba(239, 68, 68, 0.10) 100%); border: 1px solid rgba(239, 68, 68, 0.20); border-radius: 10px;'
                        : 'background: linear-gradient(135deg, rgba(245, 158, 11, 0.06) 0%, rgba(245, 158, 11, 0.10) 100%); border: 1px solid rgba(245, 158, 11, 0.20); border-radius: 10px;';
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
                            '<div class="flex-shrink-0 w-8 h-8 flex items-center justify-center" style="' + iconBgStyle + '">' +
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