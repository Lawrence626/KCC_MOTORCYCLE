<div class="w-full md:w-70 text-white flex flex-col h-screen shadow-2xl border-r border-slate-700 rounded-r-[10px]"
     style="background: linear-gradient(to bottom,#000000, #111111, #353535); position: relative; z-index: 9999999 !important;">

    <a href="{{ route('dashboard') }}" class="px-6 py-5 border-b border-slate-700/50 flex-shrink-0 hover:opacity-80 transition-opacity">
        <div class="flex flex-col items-center justify-center">
            <div class="w-42">
                <img src="{{ asset('images/Logo.png') }}" alt="KCC Logo" class="w-full h-full object-contain" />
            </div>
        </div>
    </a>

    <div class="px-6 py-4 border-b border-slate-700/30 flex items-center justify-between bg-black/15 relative">
        <button type="button" id="profileButton" class="w-full flex items-center justify-between cursor-pointer hover:opacity-80 transition-opacitytransition-colors duration-300">
            <div class="flex items-center gap-3">
                @php
                    $initial = strtoupper(substr(auth()->user()->name ?? 'A', 0, 1));
                    $bgColor = 'bg-cyan-500';
                @endphp
                <div class="w-8 h-8 rounded-full {{ $bgColor }} flex items-center justify-center overflow-hidden border border-slate-600 shadow-inner flex-shrink-0 font-semibold text-white text-sm">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                    @else
                        {{ $initial }}
                    @endif
                </div>
                <span class="text-sm font-medium text-slate-200 tracking-wide">{{ auth()->user()->name ?? 'Admin' }}</span>
            </div>
            <!-- Default: pointing UP (rotate-180). Click turns it DOWN. -->
            <svg class="w-5 h-5 text-slate-400 rotate-180 transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7 10l5 5 5-5H7z" />
            </svg>
        </button>

        <div id="profileDropdown" 
             class="absolute top-full left-1 right-3 mt-2 border border-slate-900/70 rounded-lg shadow-2xl z-50 
                    invisible opacity-0 max-h-0 scale-95 overflow-hidden origin-top transition-all duration-300 ease-in-out" 
             style="background: linear-gradient(to bottom,  #000000, #131313)">
            <div class="px-4 py-3 border-b border-slate-700/40">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full {{ $bgColor }} flex items-center justify-center overflow-hidden border border-slate-600 font-semibold text-white">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
                        @else
                            {{ $initial }}
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-200">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="text-xs text-slate-400">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
                <div class="px-0 py-2">
                    <span class="inline-block bg-cyan-500/20 text-cyan-400 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-cyan-500/30">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'user')) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>View Profile</span>
            </a>
            <a href="{{ route('settings.general') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:text-white hover:bg-slate-700/50 transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Settings</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-700/40">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-slate-700/50 transition">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto overflow-x-visible sidebar-scroll">

        <a href="{{ route('dashboard') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400' => request()->routeIs('dashboard'), 'hover:bg-[#242b35] text-slate-300' => !request()->routeIs('dashboard')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3L4 10v11h5v-6h6v6h5V10L12 3z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'inventory_clerk'))
        @php
            $isInventoryActive = request()->routeIs('inventory.monitoring') || request()->routeIs('allstocks') || request()->routeIs('product.categorization') || request()->routeIs('item.disposal') || request()->routeIs('reverse-logistics');
        @endphp
        <div class="group space-y-1 @if($isInventoryActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium transition cursor-pointer', 'bg-cyan-500/15 text-cyan-400 shadow-sm' => $isInventoryActive, 'text-slate-300 hover:bg-[#242b35]' => !$isInventoryActive])>
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 26 26">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7V5a2 2 0 012-2h12a2 2 0 012 2v2M9 12h6" />
                    </svg>
                    <span>Inventory Management</span>
                </span>
                <svg class="w-5 h-5 sidebar-arrow {{ $isInventoryActive ? 'text-cyan-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 8l5 4-5 4V8z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated hidden fixed md:w-64 border border-slate-700/60 p-1.5 rounded-xl shadow-2xl z-[99999] overflow-hidden space-y-0.5 bg-gradient-to-b from-[#0b0c10] to-[#20232a]">
                <a href="{{ route('inventory.monitoring') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('inventory.monitoring'), 'text-slate-300 hover:text-white' => !request()->routeIs('inventory.monitoring')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('inventory.monitoring') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Inventory Monitoring</span>
                </a>
                <a href="{{ route('allstocks') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('allstocks'), 'text-slate-300 hover:text-white' => !request()->routeIs('allstocks')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('allstocks') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>All Stocks</span>
                </a>
                <a href="{{ route('product.categorization') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('product.categorization'), 'text-slate-300 hover:text-white' => !request()->routeIs('product.categorization')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('product.categorization') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Product Categorization</span>
                </a>
                <a href="{{ route('item.disposal') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('item.disposal'), 'text-slate-300 hover:text-white' => !request()->routeIs('item.disposal')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('item.disposal') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Item Disposal List</span>
                </a>
                <a href="{{ route('reverse-logistics') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('reverse-logistics'), 'text-slate-300 hover:text-white' => !request()->routeIs('reverse-logistics')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('reverse-logistics') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Reverse Logistics</span>
                </a>
            </div>
        </div>

        @elseif(auth()->user() && (auth()->user()->role === 'cashier' || auth()->user()->role === 'warehouse_personnel'))
        @php
            $isInventoryStaffActive = request()->routeIs('inventory.monitoring') || request()->routeIs('allstocks') || request()->routeIs('product.categorization');
        @endphp
        <div class="group space-y-1 @if($isInventoryStaffActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium transition cursor-pointer', 'bg-cyan-500/15 text-cyan-400 shadow-sm' => $isInventoryStaffActive, 'text-slate-300 hover:bg-[#242b35]' => !$isInventoryStaffActive])>
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4M4 7a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2M4 7V5a2 2 0 012-2h12a2 2 0 012 2v2M9 12h6" />
                    </svg>
                    <span>Inventory Management</span>
                </span>
                <svg class="w-5 h-5 sidebar-arrow {{ $isInventoryStaffActive ? 'text-cyan-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 8l5 4-5 4V8z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated hidden fixed md:w-64 border border-slate-700/60 p-1.5 rounded-xl shadow-2xl z-[99999] overflow-hidden space-y-0.5 bg-gradient-to-b from-[#0b0c10] to-[#20232a]">
                <a href="{{ route('inventory.monitoring') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('inventory.monitoring'), 'text-slate-300 hover:text-white' => !request()->routeIs('inventory.monitoring')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('inventory.monitoring') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Inventory Monitoring</span>
                </a>
                @if(auth()->user()->role === 'warehouse_personnel')
                <a href="{{ route('allstocks') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('allstocks'), 'text-slate-300 hover:text-white' => !request()->routeIs('allstocks')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('allstocks') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>All Stocks</span>
                </a>
                @endif
                <a href="{{ route('product.categorization') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('product.categorization'), 'text-slate-300 hover:text-white' => !request()->routeIs('product.categorization')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('product.categorization') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Product Categorization</span>
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'cashier'))
        @php
            $isPosActive = request()->routeIs('pos.terminal') || request()->routeIs('replacing.items');
        @endphp
        <div class="group space-y-1 @if($isPosActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium transition cursor-pointer', 'bg-cyan-500/15 text-cyan-400 shadow-sm' => $isPosActive, 'text-slate-300 hover:bg-[#242b35]' => !$isPosActive])>
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Point of Sales</span>
                </span>
                <svg class="w-5 h-5 sidebar-arrow {{ $isPosActive ? 'text-cyan-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 8l5 4-5 4V8z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated hidden fixed md:w-64 border border-slate-700/60 p-1.5 rounded-xl shadow-2xl z-[99999] overflow-hidden space-y-0.5 bg-gradient-to-b from-[#0b0c10] to-[#20232a]">
                <a href="{{ route('pos.terminal') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('pos.terminal'), 'text-slate-300 hover:text-white' => !request()->routeIs('pos.terminal')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('pos.terminal') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>POS Terminal</span>
                </a>
                <a href="{{ route('replacing.items') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('replacing.items'), 'text-slate-300 hover:text-white' => !request()->routeIs('replacing.items')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('replacing.items') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Records of Replacing Items</span>
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        @php
            $isPoActive = request()->routeIs('order.management') || request()->routeIs('purchase.requests') || request()->routeIs('received.orders');
        @endphp
        <div class="group space-y-1 @if($isPoActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium transition cursor-pointer', 'bg-cyan-500/15 text-cyan-400 shadow-sm' => $isPoActive, 'text-slate-300 hover:bg-[#242b35]' => !$isPoActive])>
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <rect x="4" y="3" width="16" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="8" x2="16" y2="8" stroke-linecap="round"/>
                        <line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/>
                        <line x1="8" y1="16" x2="16" y2="16" stroke-linecap="round"/>
                    </svg>
                    <span>Purchase Order</span>
                </span>
                <svg class="w-5 h-5 sidebar-arrow {{ $isPoActive ? 'text-cyan-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 8l5 4-5 4V8z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated hidden fixed md:w-64 border border-slate-700/60 p-1.5 rounded-xl shadow-2xl z-[99999] overflow-hidden space-y-0.5 bg-gradient-to-b from-[#0b0c10] to-[#20232a]">
                <a href="{{ route('order.management') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('order.management'), 'text-slate-300 hover:text-white' => !request()->routeIs('order.management')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('order.management') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Order Management</span>
                </a>
                <a href="{{ route('received.orders') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('received.orders'), 'text-slate-300 hover:text-white' => !request()->routeIs('received.orders')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('received.orders') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Received Orders</span>
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'inventory_clerk'))
        @php
            $isAnalyticsActive = request()->routeIs('sales.analytics') || request()->routeIs('pricing.module') || request()->routeIs('overstocking.report') || request()->routeIs('out.of.stock');
        @endphp
        <div class="group space-y-1 @if($isAnalyticsActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium transition cursor-pointer', 'bg-cyan-500/15 text-cyan-400 shadow-sm' => $isAnalyticsActive, 'text-slate-300 hover:bg-[#242b35]' => !$isAnalyticsActive])>
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Data Analytics</span>
                </span>
                <svg class="w-5 h-5 sidebar-arrow {{ $isAnalyticsActive ? 'text-cyan-400' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 8l5 4-5 4V8z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated hidden fixed md:w-64 border border-slate-700/60 p-1.5 rounded-xl shadow-2xl z-[99999] overflow-hidden space-y-0.5 bg-gradient-to-b from-[#0b0c10] to-[#20232a]">
                <a href="{{ route('sales.analytics') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('sales.analytics'), 'text-slate-300 hover:text-white' => !request()->routeIs('sales.analytics')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('sales.analytics') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Sales Analytics</span>
                </a>
                <a href="{{ route('pricing.module') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('pricing.module'), 'text-slate-300 hover:text-white' => !request()->routeIs('pricing.module')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('pricing.module') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Pricing Module</span>
                </a>
                <a href="{{ route('overstocking.report') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('overstocking.report'), 'text-slate-300 hover:text-white' => !request()->routeIs('overstocking.report')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('overstocking.report') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Overstocking Report</span>
                </a>
                <a href="{{ route('out.of.stock') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition rounded-lg w-full', 'text-cyan-400 font-semibold bg-slate-800/40' => request()->routeIs('out.of.stock'), 'text-slate-300 hover:text-white' => !request()->routeIs('out.of.stock')])>
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('out.of.stock') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Out of Stock Report</span>
                </a>
            </div>
        </div>
        @elseif(auth()->user() && (auth()->user()->role === 'cashier' || auth()->user()->role === 'warehouse_personnel'))
        <a href="{{ route('sales.analytics') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400' => request()->routeIs('sales.analytics'), 'hover:bg-[#242b35] text-slate-300' => !request()->routeIs('sales.analytics')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Sales Analytics</span>
        </a>
        @endif

        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'warehouse_personnel'))
        <a href="{{ route('warehouse.management') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400' => request()->routeIs('warehouse.management'), 'hover:bg-[#242b35] text-slate-300' => !request()->routeIs('warehouse.management')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Warehouse Management</span>
        </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('supplier.assessment') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400' => request()->routeIs('supplier.assessment'), 'hover:bg-[#242b35] text-slate-300' => !request()->routeIs('supplier.assessment')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Supplier Assessment</span>
        </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('user.management') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400' => request()->routeIs('user.management'), 'hover:bg-[#242b35] text-slate-300' => !request()->routeIs('user.management')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>User Management</span>
        </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('offline.reconciliation') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400' => request()->routeIs('offline.reconciliation'), 'hover:bg-[#242b35] text-slate-300' => !request()->routeIs('offline.reconciliation')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a1 1 0 100-2 1 1 0 000 2zm0 0a4 4 0 100 8 4 4 0 000-8zm0 0V3m0 0L9 6m3-3l3 3" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.071 4.929a10 10 0 00-14.142 0M16.243 7.757a6 6 0 00-8.486 0" />
            </svg>
            <span>Offline Reconciliation</span>
        </a>

        @endif

    </nav>
</div>

<style>
    /* Profile Dropdown Active Sliding Class */
    .dropdown-active {
        visibility: visible !important;
        opacity: 1 !important;
        max-height: 400px !important;
        scale: 100% !important;
    }

    /* Flyout Horizontal Transition Settings */
    .flyout-animated {
        display: block !important; 
        transform: translateX(-15px);
        opacity: 0;
        visibility: hidden;
        transition: transform 0.25s ease-out, opacity 0.25s ease-out, visibility 0.25s ease-out;
        z-index: 999999999 !important;
    }

    .sidebar-group-content {
        z-index: 999999999 !important;
    }

    .group:hover .flyout-animated {
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
    }

    @media (min-width: 768px) {
        .sidebar-group-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: -20px;
            width: 25px; 
            height: 100%;
            background: transparent;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Profile Dropdown Logic ---
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileArrow = profileButton ? profileButton.querySelector('svg') : null;

    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            const willOpen = !profileDropdown.classList.contains('dropdown-active');

            profileDropdown.classList.toggle('dropdown-active', willOpen);

            if (profileArrow) {
                profileArrow.classList.toggle('rotate-180', !willOpen);
                profileArrow.classList.toggle('text-cyan-400', willOpen);
                profileArrow.classList.toggle('text-slate-400', !willOpen);
            }
        });

        window.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileButton.contains(e.target)) {
                profileDropdown.classList.remove('dropdown-active');
                profileArrow?.classList.add('rotate-180');
                profileArrow?.classList.remove('text-cyan-400');
                profileArrow?.classList.add('text-slate-400');
            }
        });
    }

    // --- Sidebar Navigation Logic ---
    const allGroups = document.querySelectorAll('.group');

    allGroups.forEach(group => {
        const button = group.querySelector('.sidebar-group-toggle');
        const content = group.querySelector('.sidebar-group-content');

        if (!button || !content) return;

        // No click behavior on button or arrow — hover only, active route state defines styling.
        let hideTimeout;

        const showContent = () => {
            clearTimeout(hideTimeout);
            allGroups.forEach(g => {
                if (g !== group) g.querySelector('.sidebar-group-content')?.classList.add('hidden');
            });
            content.classList.remove('hidden');
            const rect = button.getBoundingClientRect();
            content.style.top = `${rect.top}px`;
            content.style.left = `${rect.right - 6}px`;
        };

        const hideContent = () => {
            clearTimeout(hideTimeout);
            hideTimeout = setTimeout(() => {
                content.classList.add('hidden');
            }, 100);
        };

        group.addEventListener('mouseenter', showContent);
        group.addEventListener('mouseleave', hideContent);
        content.addEventListener('mouseenter', showContent);
        content.addEventListener('mouseleave', hideContent);
    });
});
</script>