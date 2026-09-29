<!-- Cashier Top Full-Width Navigation Header (Full Screen Width Edge-to-Edge) -->
<div id="cashierNavbarWrapper" class="w-full z-50">
    <div class="w-full text-slate-800 px-4 sm:px-8 py-2.5 flex items-center justify-between relative min-h-[64px]">
    <!-- Left: Brand Logo (Separated on Left Side) -->
    <div class="flex items-center">
        <a href="{{ route('dashboard') }}" class="flex items-center group">
            <img src="{{ asset('images/Logo.png') }}?v={{ time() }}" alt="KCC Logo" class="h-14 sm:h-16 w-auto object-contain transition-transform group-hover:scale-105" />
        </a>
    </div>

    <!-- Center: Navigation Tabs (Pill Container Centered in Middle) -->
    @php
        $isPosActive = request()->routeIs('pos.terminal') || request()->routeIs('replacing.items') || request()->is('pos*') || request()->is('replacing*') || request()->is('replacing-items*');
        $isAnalyticsActive = request()->routeIs('sales.analytics') || request()->routeIs('pricing.module') || request()->routeIs('overstocking.report') || request()->routeIs('out.of.stock') || request()->routeIs('dss.dead-stock*');
    @endphp
    <nav class="hidden md:flex items-center gap-1.5 border border-slate-700/60 rounded-full p-1 px-2 shadow-md mx-auto whitespace-nowrap flex-shrink-0" style="background: linear-gradient(90deg, #000000, #2b2b2b);">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           @class([
               'rounded-full px-5 py-2 text-sm transition-all duration-200 flex items-center gap-2 whitespace-nowrap flex-shrink-0',
               'bg-[#00ddd2] text-black font-semibold shadow-md' => request()->routeIs('dashboard'),
               'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium' => !request()->routeIs('dashboard')
           ])>
            <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                <rect width="7" height="7" x="3" y="3" rx="1.5" />
                <rect width="7" height="7" x="14" y="3" rx="1.5" />
                <rect width="7" height="7" x="3" y="14" rx="1.5" />
                <rect width="7" height="7" x="14" y="14" rx="1.5" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Point of Sales Dropdown (Cashier / Admin) -->
        @if(!auth()->check() || auth()->user()->role === 'cashier' || auth()->user()->role === 'admin')
        <div class="relative" id="posDropdownContainer">
            <button type="button" 
                    id="posDropdownBtn"
                    onclick="togglePosDropdown(event)"
                    @class([
                        'rounded-full px-5 py-2 text-sm transition-all duration-200 flex items-center gap-2 cursor-pointer whitespace-nowrap flex-shrink-0',
                        'bg-[#00ddd2] text-black font-semibold shadow-md' => $isPosActive,
                        'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium' => !$isPosActive
                    ])>
                <svg id="posDropdownIcon" class="w-4 h-4 {{ $isPosActive ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 0.674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 5.29l-.31-1.163a1.875 1.875 0 0 0-1.81-1.377H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                </svg>
                <span>Point of Sales</span>
                <svg id="posDropdownArrow" class="w-3.5 h-3.5 {{ $isPosActive ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5H7z" />
                </svg>
            </button>
            
            <div id="posDropdownMenu" class="hidden absolute left-0 top-full mt-2 w-max min-w-[250px] rounded-[15px] shadow-2xl border border-slate-700/80 p-2 z-50 transition-all duration-200 origin-top-left text-white" style="background: linear-gradient(135deg, #000000, #2b2b2b);">
                <a href="{{ route('pos.terminal') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('pos.terminal'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('pos.terminal')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('pos.terminal'),
                        'bg-slate-400' => !request()->routeIs('pos.terminal')
                    ])></span>
                    <span>POS Terminal</span>
                </a>
                <a href="{{ route('replacing.items') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full mt-1 whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('replacing.items'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('replacing.items')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('replacing.items'),
                        'bg-slate-400' => !request()->routeIs('replacing.items')
                    ])></span>
                    <span>Records of Replacing Items</span>
                </a>
            </div>
        </div>
        @endif



        <!-- Data Analytics (Dropdown for Inventory Clerk / Admin) vs Sales Analytics (Cashier / Warehouse Personnel) -->
        @if(auth()->check() && (auth()->user()->role === 'inventory_clerk' || auth()->user()->role === 'admin'))
        <div class="relative" id="analyticsDropdownContainer">
            <button type="button" 
                    id="analyticsDropdownBtn"
                    onclick="toggleAnalyticsDropdown(event)"
                    @class([
                        'rounded-full px-5 py-2 text-sm transition-all duration-200 flex items-center gap-2 cursor-pointer',
                        'bg-[#00ddd2] text-black font-semibold shadow-md' => $isAnalyticsActive,
                        'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium' => !$isAnalyticsActive
                    ])>
                <svg id="analyticsDropdownIcon" class="w-4 h-4 {{ $isAnalyticsActive ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
                </svg>
                <span>Data Analytics</span>
                <svg id="analyticsDropdownArrow" class="w-3.5 h-3.5 {{ $isAnalyticsActive ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5H7z" />
                </svg>
            </button>

            <div id="analyticsDropdownMenu" class="hidden absolute left-0 top-full mt-2 w-max min-w-[250px] rounded-[15px] shadow-2xl border border-slate-700/80 p-2 z-50 transition-all duration-200 origin-top-left text-white" style="background: linear-gradient(135deg, #000000, #2b2b2b);">
                <a href="{{ route('sales.analytics') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('sales.analytics'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('sales.analytics')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('sales.analytics'),
                        'bg-slate-400' => !request()->routeIs('sales.analytics')
                    ])></span>
                    <span>Sales Analytics</span>
                </a>
                <a href="{{ route('pricing.module') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full mt-1 whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('pricing.module'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('pricing.module')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('pricing.module'),
                        'bg-slate-400' => !request()->routeIs('pricing.module')
                    ])></span>
                    <span>Pricing Module</span>
                </a>
                <a href="{{ route('overstocking.report') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full mt-1 whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('overstocking.report'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('overstocking.report')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('overstocking.report'),
                        'bg-slate-400' => !request()->routeIs('overstocking.report')
                    ])></span>
                    <span>Overstocking Report</span>
                </a>
                <a href="{{ route('out.of.stock') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full mt-1 whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('out.of.stock'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('out.of.stock')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('out.of.stock'),
                        'bg-slate-400' => !request()->routeIs('out.of.stock')
                    ])></span>
                    <span>Out of Stock Report</span>
                </a>
                <a href="{{ route('dss.dead-stock.index') }}" 
                   @class([
                       'flex items-center gap-3 px-4 py-2 text-sm transition-all duration-200 rounded-full mt-1 whitespace-nowrap',
                       'font-semibold text-white bg-slate-700/70 shadow-xs' => request()->routeIs('dss.dead-stock*'),
                       'text-slate-300 hover:bg-slate-700/60 hover:text-white font-medium' => !request()->routeIs('dss.dead-stock*')
                   ])>
                    <span @class([
                        'w-2 h-2 rounded-full transition-colors flex-shrink-0',
                        'bg-[#00ddd2]' => request()->routeIs('dss.dead-stock*'),
                        'bg-slate-400' => !request()->routeIs('dss.dead-stock*')
                    ])></span>
                    <span>Dead Stock Analysis</span>
                </a>
            </div>
        </div>
        @else
        <a href="{{ route('sales.analytics') }}" 
           @class([
               'rounded-full px-5 py-2 text-sm transition-all duration-200 flex items-center gap-2',
               'bg-[#00ddd2] text-black font-semibold shadow-md' => request()->routeIs('sales.analytics'),
               'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium' => !request()->routeIs('sales.analytics')
           ])>
            <svg class="w-4 h-4 {{ request()->routeIs('sales.analytics') ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
            </svg>
            <span>Sales Analytics</span>
        </a>
        @endif

        @if(auth()->check() && (auth()->user()->role === 'warehouse_personnel' || auth()->user()->role === 'admin'))
        <!-- Warehouse Management -->
        <a href="{{ route('warehouse.management') }}" 
           @class([
               'rounded-full px-5 py-2 text-sm transition-all duration-200 flex items-center gap-2',
               'bg-[#00ddd2] text-black font-semibold shadow-md' => request()->routeIs('warehouse.management'),
               'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium' => !request()->routeIs('warehouse.management')
           ])>
            <svg class="w-5 h-5 flex-shrink-0 align-middle translate-y-[1.5px] {{ request()->routeIs('warehouse.management') ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                <polygon points="2.2,9 12,3.2 21.8,9" />
                <rect x="2" y="9.2" width="20" height="1" />
                <rect x="5.4" y="10.5" width="1.8" height="5.4" />
                <rect x="9.2" y="10.5" width="1.8" height="5.4" />
                <rect x="13" y="10.5" width="1.8" height="5.4" />
                <rect x="16.8" y="10.5" width="1.8" height="5.4" />
                <rect x="5" y="15.6" width="2.6" height="0.6" />
                <rect x="8.8" y="15.6" width="2.6" height="0.6" />
                <rect x="12.6" y="15.6" width="2.6" height="0.6" />
                <rect x="16.4" y="15.6" width="2.6" height="0.6" />
                <rect x="2" y="16.4" width="20" height="0.5" />
                <rect x="1.6" y="17.1" width="20.8" height="0.5" />
            </svg>
            <span>Warehouse Management</span>
        </a>
        @endif

        @if(auth()->check() && (auth()->user()->role === 'inventory_clerk' || auth()->user()->role === 'admin'))
        <!-- Shop Inventory Items -->
        <a href="{{ route('shop.inventory') }}" 
           @class([
               'rounded-full px-5 py-2 text-sm transition-all duration-200 flex items-center gap-2',
               'bg-[#00ddd2] text-black font-semibold shadow-md' => request()->routeIs('shop.inventory'),
               'text-slate-300 hover:text-white hover:bg-slate-800/60 font-medium' => !request()->routeIs('shop.inventory')
           ])>
            <svg class="w-4 h-4 {{ request()->routeIs('shop.inventory') ? 'text-black' : 'text-slate-300' }}" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 11.5 L12 4 L20 11.5 V20 H14 V14 H10 V20 H4 Z" />
            </svg>
            <span>Shop Inventory Items</span>
        </a>
        @endif
    </nav>

    <!-- Right: Quick Actions (Profile Dropdown & Notification Bell) -->
    <div class="flex items-center gap-3 md:absolute right-4 sm:right-8">

        <!-- Notification Bell Trigger (Dark Capsule Background Matching Buttons) -->
        @if(auth()->check() && (auth()->user()->role === 'inventory_clerk' || auth()->user()->role === 'admin'))
        <div class="relative inline-flex items-center z-50" id="notification-bell-wrapper">
            <button
                type="button"
                id="notification-bell-btn"
                class="relative inline-flex h-[38px] w-[38px] items-center justify-center text-slate-300 hover:text-white rounded-full border border-slate-700/60 shadow-md transition focus:outline-none cursor-pointer"
                style="background: linear-gradient(90deg, #000000, #2b2b2b);"
                aria-label="Notifications"
                onclick="toggleNotificationPanel(event)"
            >
                <svg class="w-5 h-5 text-slate-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span
                    id="notification-badge"
                    class="hidden"
                    style="display: none !important;"
                ></span>
            </button>

            {{-- Notification Dropdown Panel --}}
            <div
                id="notification-panel"
                class="hidden absolute right-0 top-full mt-3 w-[320px] rounded-xl bg-white border border-slate-200 shadow-2xl z-[9999] flex flex-col"
                style="max-height: 350px;"
            >
                <div class="sticky top-0 z-10 flex items-center justify-between px-4 py-3 border-b border-slate-800 rounded-t-xl bg-[#0f172a]" style="background-color: #0f172a;">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-white">Notifications</span>
                        <span id="notif-center-unread-badge" class="hidden" style="display: none !important;">0</span>
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

        <!-- Profile Dropdown Trigger (Dark Capsule Background Matching Buttons) -->
        <div class="relative inline-flex items-center text-left">
            <button type="button" id="dashboardProfileButton" class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full border border-slate-700/60 text-left shadow-md transition focus:outline-none cursor-pointer group" style="background: linear-gradient(90deg, #000000, #2b2b2b);" aria-label="Open profile menu">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-slate-800 grid place-items-center text-xs font-semibold overflow-hidden border border-slate-300">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                    @endif
                </span>
                <div class="hidden sm:flex flex-col leading-tight text-left">
                    <span class="text-sm font-semibold text-white max-w-[120px] truncate">{{ auth()->user()->name ?? 'Cashier' }}</span>
                    <span class="text-[11px] text-slate-200 max-w-[130px] truncate">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <svg id="dashboardProfileArrow" class="w-4 h-4 text-slate-400 transition-colors duration-200 group-hover:text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z"/></svg>
            </button>

            {{-- Profile Dropdown Card (White Theme) --}}
            <div id="dashboardProfileDropdown" class="absolute right-0 top-full mt-2.5 w-68 rounded-[18px] border border-slate-200 bg-white shadow-xl z-[9999] hidden opacity-0 transform scale-95 transition-all duration-200 origin-top-right overflow-hidden">
                
                <!-- User Info Header -->
                <div class="px-4.5 py-4 border-b border-slate-100 bg-white">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-full bg-slate-100 text-slate-800 grid place-items-center overflow-hidden text-base font-bold border border-slate-200 shadow-sm flex-shrink-0">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                            @else
                                {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="text-[14px] font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Cashier' }}</div>
                            <div class="text-[11px] text-slate-500 font-medium truncate mt-0.5">{{ auth()->user()->email ?? '' }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="inline-flex items-center rounded-full border border-cyan-200 bg-cyan-50 px-2.5 py-0.5 text-[11px] font-bold tracking-wider text-cyan-800 uppercase">
                            {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                        </span>
                    </div>
                </div>

                <!-- Action Links -->
                <div class="flex flex-col gap-1 p-2">
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-all duration-150 group">
                        <span class="w-7 h-7 grid place-items-center rounded-lg bg-slate-100 text-slate-600 group-hover:text-cyan-600 transition-colors border border-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.879 6.196 9 9 0 015.12 17.804z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span>View Profile</span>
                    </a>

                    <a href="{{ route('settings.general') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-all duration-150 group">
                        <span class="w-7 h-7 grid place-items-center rounded-lg bg-slate-100 text-slate-600 group-hover:text-cyan-600 transition-colors border border-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span>Settings</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-all duration-150 group cursor-pointer">
                            <span class="w-7 h-7 grid place-items-center rounded-lg bg-slate-100 text-slate-600 group-hover:text-slate-900 transition-colors border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button id="cashier-mobile-toggle" type="button" class="md:hidden w-9 h-9 rounded-full bg-slate-100 border border-slate-200 text-slate-700 hover:bg-slate-200 flex items-center justify-center focus:outline-none cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</div>
</div>

<!-- Mobile Dropdown Navigation Menu for Cashier -->
<div id="cashier-mobile-menu" class="hidden md:hidden mx-3 mb-4 rounded-2xl bg-[#1a1a1a] shadow-xl border border-slate-700 p-4 transition-all duration-200 text-white">
    <div class="flex flex-col gap-2">
        <a href="{{ route('dashboard') }}" 
           @class([
               'px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-3',
               'bg-[#00ddd2] text-black font-semibold' => request()->routeIs('dashboard'),
               'text-slate-300 hover:bg-slate-800 hover:text-white' => !request()->routeIs('dashboard')
           ])>
            <span>Dashboard</span>
        </a>

        <div class="border-t border-slate-800 my-1"></div>
        <div class="px-4 py-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Point of Sales</div>
        <a href="{{ route('pos.terminal') }}" 
           @class([
               'px-4 py-2 rounded-xl text-sm font-medium transition pl-6 flex items-center gap-2',
               'text-[#00ddd2] font-semibold' => request()->routeIs('pos.terminal'),
               'text-slate-300 hover:bg-slate-800 hover:text-white' => !request()->routeIs('pos.terminal')
           ])>
            <span>• POS Terminal</span>
        </a>
        <a href="{{ route('replacing.items') }}" 
           @class([
               'px-4 py-2 rounded-xl text-sm font-medium transition pl-6 flex items-center gap-2',
               'text-[#00ddd2] font-semibold' => request()->routeIs('replacing.items'),
               'text-slate-300 hover:bg-slate-800 hover:text-white' => !request()->routeIs('replacing.items')
           ])>
            <span>• Records of Replacing Items</span>
        </a>

        <div class="border-t border-slate-800 my-1"></div>
        <a href="{{ route('sales.analytics') }}" 
           @class([
               'px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-3',
               'bg-[#00ddd2] text-black font-semibold' => request()->routeIs('sales.analytics'),
               'text-slate-300 hover:bg-slate-800 hover:text-white' => !request()->routeIs('sales.analytics')
           ])>
            <span>Sales Analytics</span>
        </a>
    </div>
</div>

<script>
    const isPosRouteActive = @json((bool)$isPosActive);
    const isAnalyticsRouteActive = @json(request()->routeIs('sales.analytics') || request()->routeIs('pricing.module') || request()->routeIs('overstocking.report') || request()->routeIs('out.of.stock') || request()->routeIs('dss.dead-stock*'));

    function setBtnActive(btn, arrow, svgIcon, isActive) {
        if (!btn) return;
        if (isActive) {
            btn.classList.add('bg-[#00ddd2]', 'text-black', 'font-semibold', 'shadow-md');
            btn.classList.remove('text-slate-300', 'hover:text-white', 'hover:bg-slate-800/60', 'font-medium');
            if (arrow) {
                arrow.classList.add('text-black');
                arrow.classList.remove('text-slate-300');
            }
            if (svgIcon) {
                svgIcon.classList.add('text-black');
                svgIcon.classList.remove('text-slate-300');
            }
        } else {
            btn.classList.remove('bg-[#00ddd2]', 'text-black', 'font-semibold', 'shadow-md');
            btn.classList.add('text-slate-300', 'hover:text-white', 'hover:bg-slate-800/60', 'font-medium');
            if (arrow) {
                arrow.classList.remove('text-black');
                arrow.classList.add('text-slate-300');
            }
            if (svgIcon) {
                svgIcon.classList.remove('text-black');
                svgIcon.classList.add('text-slate-300');
            }
        }
    }

    function togglePosDropdown(event) {
        if (event) event.stopPropagation();
        const posMenu = document.getElementById('posDropdownMenu');
        const analyticsMenu = document.getElementById('analyticsDropdownMenu');
        const posBtn = document.getElementById('posDropdownBtn');
        const analyticsBtn = document.getElementById('analyticsDropdownBtn');
        const posArrow = document.getElementById('posDropdownArrow');
        const analyticsArrow = document.getElementById('analyticsDropdownArrow');
        const posIcon = document.getElementById('posDropdownIcon');
        const analyticsIcon = document.getElementById('analyticsDropdownIcon');

        if (analyticsMenu) analyticsMenu.classList.add('hidden');
        setBtnActive(analyticsBtn, analyticsArrow, analyticsIcon, isAnalyticsRouteActive);

        if (posMenu) {
            const isOpening = posMenu.classList.contains('hidden');
            posMenu.classList.toggle('hidden');
            setBtnActive(posBtn, posArrow, posIcon, isOpening || isPosRouteActive);
        }
    }

    function toggleAnalyticsDropdown(event) {
        if (event) event.stopPropagation();
        const posMenu = document.getElementById('posDropdownMenu');
        const analyticsMenu = document.getElementById('analyticsDropdownMenu');
        const posBtn = document.getElementById('posDropdownBtn');
        const analyticsBtn = document.getElementById('analyticsDropdownBtn');
        const posArrow = document.getElementById('posDropdownArrow');
        const analyticsArrow = document.getElementById('analyticsDropdownArrow');
        const posIcon = document.getElementById('posDropdownIcon');
        const analyticsIcon = document.getElementById('analyticsDropdownIcon');

        if (posMenu) posMenu.classList.add('hidden');
        setBtnActive(posBtn, posArrow, posIcon, isPosRouteActive);

        if (analyticsMenu) {
            const isOpening = analyticsMenu.classList.contains('hidden');
            analyticsMenu.classList.toggle('hidden');
            setBtnActive(analyticsBtn, analyticsArrow, analyticsIcon, isOpening || isAnalyticsRouteActive);
        }
    }

    document.addEventListener('click', function(event) {
        const posMenu = document.getElementById('posDropdownMenu');
        const analyticsMenu = document.getElementById('analyticsDropdownMenu');
        const posContainer = document.getElementById('posDropdownContainer');
        const analyticsContainer = document.getElementById('analyticsDropdownContainer');
        const posBtn = document.getElementById('posDropdownBtn');
        const analyticsBtn = document.getElementById('analyticsDropdownBtn');
        const posArrow = document.getElementById('posDropdownArrow');
        const analyticsArrow = document.getElementById('analyticsDropdownArrow');
        const posIcon = document.getElementById('posDropdownIcon');
        const analyticsIcon = document.getElementById('analyticsDropdownIcon');

        if (posMenu && posContainer && !posContainer.contains(event.target)) {
            posMenu.classList.add('hidden');
            setBtnActive(posBtn, posArrow, posIcon, isPosRouteActive);
        }
        if (analyticsMenu && analyticsContainer && !analyticsContainer.contains(event.target)) {
            analyticsMenu.classList.add('hidden');
            setBtnActive(analyticsBtn, analyticsArrow, analyticsIcon, isAnalyticsRouteActive);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const cashierMobileToggle = document.getElementById('cashier-mobile-toggle');
        const cashierMobileMenu = document.getElementById('cashier-mobile-menu');

        if (cashierMobileToggle && cashierMobileMenu) {
            cashierMobileToggle.addEventListener('click', function() {
                cashierMobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
