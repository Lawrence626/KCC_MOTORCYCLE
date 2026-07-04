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
<body class="bg-gray-100">
    <div class="flex h-screen relative">
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-toggle" class="md:hidden fixed top-4 left-4 z-40 p-2 text-white bg-slate-800 rounded-lg hover:bg-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Mobile Overlay (backdrop) -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden"></div>

        <!-- Sidebar -->
        <div id="sidebar-wrapper" class="fixed top-0 left-0 w-70 h-screen -translate-x-full md:translate-x-0 transition-transform duration-300 z-30">
            @include('sidebar')
        </div>

        <!-- Floating visible controls (notification + profile) - does not change layout -->
        <div id="floatingVisibleControls" class="fixed top-4 right-6 z-50 flex items-center gap-4 pointer-events-auto">
            @auth
                <button id="floatingNotificationButton" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-[8px] border border-slate-700 bg-gradient-to-b from-black via-slate-950 to-slate-800 text-white transition focus:outline-none" aria-label="Notifications">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a6 6 0 00-6 6v4.586l-1.707 1.707A1 1 0 005 16h14a1 1 0 00.707-1.707L18 12.586V8a6 6 0 00-6-6zm0 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 20z"/></svg>
                </button>

                <div class="relative inline-flex items-center z-50">
                    <button id="floatingProfileButton" type="button" class="inline-flex items-center gap-2 rounded-[8px] border border-slate-700 bg-gradient-to-b from-black via-slate-950 to-slate-800 px-3 py-1.5 text-white transition hover:from-slate-900 hover:via-slate-800 hover:to-slate-700 focus:outline-none">
                        <span class="w-5.5 h-5.5 rounded-full bg-cyan-500 text-white grid place-items-center overflow-hidden text-sm font-semibold">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                            @else
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            @endif
                        </span>
                        <span class="hidden sm:inline-block text-[14px] font-medium text-white">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <svg id="floatingProfileArrow" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5H7z" />
                        </svg>
                    </button>

                    <div id="floatingProfileDropdown" class="absolute right-0 top-full mt-2 w-65 min-h-[100px] rounded-[15px] border border-slate-700/60 bg-gradient-to-b from-[#0b0c10] to-[#20232a] shadow-2xl shadow-black/40 z-[99999] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right">
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
            @endauth
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:ml-[268px] relative z-20 h-screen">
            <!-- Header Container - Fixed at top -->
            <div id="dashboardHeader" class="flex-shrink-0 bg-white border-b-0 px-5 py-2 sticky top-0 z-40 transition-all duration-200" style="border-radius: 10px 0 0 0; border-bottom: 0; border-top: 0; box-shadow: none;">
                @auth
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div class="flex-1"></div>
                        <div class="flex items-center gap-4 mt-2">
                            @if(Request::routeIs('dashboard'))
                                <button class="inline-flex h-9 items-center gap-2 px-3 bg-white border border-gray-300 rounded-[8px] text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    📅 Apr 1, 2026 · Apr 30, 2026
                                </button>
                                <button class="inline-flex h-9 items-center gap-2 px-3 bg-white border border-gray-300 rounded-[8px] text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                    📥 Export Report
                                </button>
                            @endif
                            <button id="headerNotificationButton" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-none border border-slate-700 bg-gradient-to-b from-black via-slate-950 to-slate-800 text-white transition focus:outline-none invisible" aria-label="Notifications">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a6 6 0 00-6 6v4.586l-1.707 1.707A1 1 0 005 16h14a1 1 0 00.707-1.707L18 12.586V8a6 6 0 00-6-6zm0 18a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 20z"/></svg>
                            </button>
                            <div class="relative inline-flex items-center z-50">
                                <button id="headerProfileButton" type="button" class="inline-flex items-center gap-2 rounded-none border border-slate-700 bg-gradient-to-b from-black via-slate-950 to-slate-800 px-3 py-1.5 text-white transition hover:from-slate-900 hover:via-slate-800 hover:to-slate-700 focus:outline-none invisible">
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

            <!-- Content Container - Scrollable -->
            <div id="mainScrollArea" class="flex-1 overflow-y-auto relative">
                <div id="topScrollFade" class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-white/95 via-white/70 to-transparent opacity-0 transition-opacity duration-200"></div>
                <div class="w-full max-w-full min-h-screen">
                    <div class="bg-white border border-slate-200 border-t-0 shadow-sm overflow-hidden" style="border-radius: 0;">
                        <div class="px-8 py-6 space-y-8">
                            {{ $slot }}
                        </div>
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
        const headerNotificationIcon = headerNotificationButton?.querySelector('svg');
        const dashboardHeader = document.getElementById('dashboardHeader');
        const mainScrollArea = document.getElementById('mainScrollArea');
        const topScrollFade = document.getElementById('topScrollFade');
        const floatingNotificationButton = document.getElementById('floatingNotificationButton');
        const floatingProfileButton = document.getElementById('floatingProfileButton');
        const floatingProfileDropdown = document.getElementById('floatingProfileDropdown');
        const floatingProfileArrow = document.getElementById('floatingProfileArrow');

        function closeProfileDropdown() {
            headerProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            headerProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
            headerProfileArrow.classList.remove('text-cyan-400');
            headerProfileArrow.classList.add('text-white');
            headerProfileButton.classList.remove('ring-2', 'ring-cyan-400');
            headerProfileButton.blur();
        }

        function openProfileDropdown() {
            headerProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
            headerProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
            headerProfileArrow.classList.remove('text-white');
            headerProfileArrow.classList.add('text-cyan-400');
            headerProfileButton.classList.add('ring-2', 'ring-cyan-400');
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

            if (headerNotificationButton && headerNotificationIcon) {
                headerNotificationButton.addEventListener('click', function() {
                    const isActive = headerNotificationButton.classList.contains('ring-cyan-400');

                    if (isActive) {
                        headerNotificationButton.classList.remove('ring-2', 'ring-cyan-400');
                        headerNotificationIcon.classList.remove('text-cyan-400');
                        headerNotificationIcon.classList.add('text-white');
                    } else {
                        headerNotificationButton.classList.add('ring-2', 'ring-cyan-400');
                        headerNotificationIcon.classList.remove('text-white');
                        headerNotificationIcon.classList.add('text-cyan-400');
                    }
                });
            }

            // Floating notification button mirrors visual control
            if (floatingNotificationButton) {
                floatingNotificationButton.addEventListener('click', function() {
                    const isActive = floatingNotificationButton.classList.contains('ring-cyan-400');
                    if (isActive) {
                        floatingNotificationButton.classList.remove('ring-2', 'ring-cyan-400');
                        // turn icon white
                        floatingNotificationButton.querySelector('svg')?.classList.remove('text-cyan-400');
                        floatingNotificationButton.querySelector('svg')?.classList.add('text-white');
                    } else {
                        floatingNotificationButton.classList.add('ring-2', 'ring-cyan-400');
                        floatingNotificationButton.querySelector('svg')?.classList.remove('text-white');
                        floatingNotificationButton.querySelector('svg')?.classList.add('text-cyan-400');
                    }
                });
            }

            window.addEventListener('click', function(e) {
                if (!headerProfileDropdown.contains(e.target) && !headerProfileButton.contains(e.target)) {
                    closeProfileDropdown();
                }
            });
        }

        // Floating profile dropdown handlers
        if (floatingProfileButton && floatingProfileDropdown && floatingProfileArrow) {
            floatingProfileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });

            floatingProfileButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = !floatingProfileDropdown.classList.contains('hidden');
                if (isOpen) {
                    floatingProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                    floatingProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                    floatingProfileArrow.classList.remove('text-cyan-400');
                    floatingProfileArrow.classList.add('text-white');
                    floatingProfileButton.classList.remove('ring-2', 'ring-cyan-400');
                    floatingProfileButton.blur();
                } else {
                    floatingProfileDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                    floatingProfileDropdown.classList.add('block', 'opacity-100', 'scale-100');
                    floatingProfileArrow.classList.remove('text-white');
                    floatingProfileArrow.classList.add('text-cyan-400');
                    floatingProfileButton.classList.add('ring-2', 'ring-cyan-400');
                }
            });

            window.addEventListener('click', function(e) {
                if (!floatingProfileDropdown.contains(e.target) && !floatingProfileButton.contains(e.target)) {
                    floatingProfileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
                    floatingProfileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
                    floatingProfileArrow.classList.remove('text-cyan-400');
                    floatingProfileArrow.classList.add('text-white');
                    floatingProfileButton.classList.remove('ring-2', 'ring-cyan-400');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
