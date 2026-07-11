<div class="w-full md:w-70 text-white flex flex-col h-screen shadow-2xl border-r border-slate-700 "
     style="background: linear-gradient( #000000, #2b2b2b); position: relative;">

    <a href="{{ route('dashboard') }}" class="px-6 py-5 flex-shrink-0">
        <div class="flex flex-col items-center justify-center">
            <div class="w-48">
                <img src="{{ asset('images/Logo.png') }}" alt="KCC Logo" class="w-full h-auto object-contain" />
            </div>
        </div>
    </a>

    <div class="px-4 pt-2 pb-3 flex-shrink-0">
        <div class="relative w-[93%]">
            <svg class="w-4.5 h-4.5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="7" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
            <input type="text" placeholder="Search" class="w-full bg-[#1a1a1a] text-white placeholder-slate-400 text-sm pl-11 pr-4 py-2 rounded-[10px] border border-white/10 focus:outline-none focus:border-cyan-400/50" />
        </div>
    </div>


    <nav class="flex-1 py-4 space-y-2 overflow-y-auto overflow-x-visible sidebar-scroll">

        <a href="{{ route('dashboard') }}" @class(['sidebar-nav-item flex items-center gap-3 pl-6 pr-4 py-3 text-sm font-medium transition border-l-[3px]', 'bg-cyan-500/5 text-cyan-400 border-cyan-400' => request()->routeIs('dashboard'), 'text-slate-300 border-transparent' => !request()->routeIs('dashboard')])>
            <svg class="w-5.5 h-5.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M4 11.5 L12 4 L20 11.5 V20 H14 V14 H10 V20 H4 Z" />
            </svg>
            <span>Dashboard</span>
        </a>

        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'inventory_clerk'))
        @php
            $isInventoryActive = request()->routeIs('inventory.monitoring') || request()->routeIs('allstocks') || request()->routeIs('product.categorization') || request()->routeIs('item.disposal') || request()->routeIs('reverse-logistics');
        @endphp
        <div class="group space-y-1 @if($isInventoryActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 pl-6 pr-4 py-3 text-sm font-medium cursor-pointer text-slate-300 border-l-[3px] border-transparent'])>
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <rect x="3" y="7" width="18" height="11" rx="1.2" />
                        <rect x="8" y="3.8" width="8" height="2" rx="0.6" />
                        <rect x="4.5" y="10.4" width="15" height="1.2" rx="0.4" />
                        <rect x="6.2" y="11.6" width="1.6" height="1.8" rx="0.3" />
                        <rect x="16.2" y="11.6" width="1.6" height="1.8" rx="0.3" />
                    </svg>
                    <span>Inventory Management</span>
                </span>
                <svg class="w-4 h-4 sidebar-arrow text-slate-400 mr-0 flex-shrink-0 arrow-left" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 10l6 6 6-6H6z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated overflow-hidden ml-6 pl-4 border-l-2 border-cyan-400/40 space-y-0.5 mt-1">
                <a href="{{ route('inventory.monitoring') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('inventory.monitoring'), 'text-slate-400' => !request()->routeIs('inventory.monitoring')])>
                    <span>Inventory Monitoring</span>
                </a>
                <a href="{{ route('allstocks') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('allstocks'), 'text-slate-400' => !request()->routeIs('allstocks')])>
                    <span>All Stocks</span>
                </a>
                <a href="{{ route('product.categorization') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('product.categorization'), 'text-slate-400' => !request()->routeIs('product.categorization')])>
                    <span>Product Categorization</span>
                </a>
                <a href="{{ route('item.disposal') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('item.disposal'), 'text-slate-400' => !request()->routeIs('item.disposal')])>
                    <span>Item Disposal List</span>
                </a>
                <a href="{{ route('reverse-logistics') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('reverse-logistics'), 'text-slate-400' => !request()->routeIs('reverse-logistics')])>
                    <span>Reverse Logistics</span>
                </a>
            </div>
        </div>

        @elseif(auth()->user() && (auth()->user()->role === 'cashier' || auth()->user()->role === 'warehouse_personnel'))
        @php
            $isInventoryStaffActive = request()->routeIs('inventory.monitoring') || request()->routeIs('allstocks') || request()->routeIs('product.categorization');
        @endphp
        <div class="group space-y-1 @if($isInventoryStaffActive) open @endif">
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 pl-6 pr-4 py-3 text-sm font-medium cursor-pointer text-slate-300 border-l-[3px] border-transparent'])>
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <rect x="2.5" y="6.5" width="19" height="12" rx="1.4" />
                        <rect x="8.2" y="3.5" width="7.6" height="2.6" rx="0.6" />
                        <rect x="4.6" y="10.2" width="14.8" height="1.4" rx="0.4" />
                        <rect x="6.4" y="11.8" width="1.8" height="2" rx="0.3" />
                        <rect x="15.8" y="11.8" width="1.8" height="2" rx="0.3" />
                    </svg>
                    <span>Inventory Management</span>
                </span>
                <svg class="w-4 h-4 sidebar-arrow text-slate-400 mr-0 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 10l6 6 6-6H6z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated overflow-hidden ml-6 pl-4 border-l-2 border-cyan-400/40 space-y-0.5 mt-1">
                <a href="{{ route('inventory.monitoring') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('inventory.monitoring'), 'text-slate-400' => !request()->routeIs('inventory.monitoring')])>
                    <span>Inventory Monitoring</span>
                </a>
                @if(auth()->user()->role === 'warehouse_personnel')
                <a href="{{ route('allstocks') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('allstocks'), 'text-slate-400' => !request()->routeIs('allstocks')])>
                    <span>All Stocks</span>
                </a>
                @endif
                <a href="{{ route('product.categorization') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('product.categorization'), 'text-slate-400' => !request()->routeIs('product.categorization')])>
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
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 pl-6 pr-4 py-3 text-sm font-medium cursor-pointer text-slate-300 border-l-[3px] border-transparent'])>
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 5.29l-.31-1.163a1.875 1.875 0 0 0-1.81-1.377H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                    </svg>
                    <span>Point of Sales</span>
                </span>
                <svg class="w-4 h-4 sidebar-arrow text-slate-400 mr-0 flex-shrink-0 arrow-left" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 10l6 6 6-6H6z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated overflow-hidden ml-6 pl-4 border-l-2 border-cyan-400/40 space-y-0.5 mt-1">
                <a href="{{ route('pos.terminal') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('pos.terminal'), 'text-slate-400' => !request()->routeIs('pos.terminal')])>
                    <span>POS Terminal</span>
                </a>
                <a href="{{ route('replacing.items') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('replacing.items'), 'text-slate-400' => !request()->routeIs('replacing.items')])>
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
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 pl-6 pr-4 py-3 text-sm font-medium cursor-pointer text-slate-300 border-l-[3px] border-transparent'])>
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875h1.875A3.75 3.75 0 0 1 21 12.75v3.375C21 17.161 20.16 18 19.125 18h-9.75A1.875 1.875 0 0 1 7.5 16.125V3.375Z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M15 5.25a5.23 5.23 0 0 0-1.279-3.434 9.768 9.768 0 0 1 6.963 6.963A5.23 5.23 0 0 0 17.25 7.5h-1.875A.375.375 0 0 1 15 7.125V5.25ZM4.875 6H6v10.125A3.375 3.375 0 0 0 9.375 19.5H16.5v1.125c0 1.036-.84 1.875-1.875 1.875H4.875A1.875 1.875 0 0 1 3 20.625V7.875C3 6.839 3.84 6 4.875 6Z" clip-rule="evenodd" />
                    </svg>
                    <span>Purchase Order</span>
                </span>
                <svg class="w-4 h-4 sidebar-arrow text-slate-400 mr-0 flex-shrink-0 arrow-left" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 10l6 6 6-6H6z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated overflow-hidden ml-6 pl-4 border-l-2 border-cyan-400/40 space-y-0.5 mt-1">
                <a href="{{ route('order.management') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('order.management'), 'text-slate-400' => !request()->routeIs('order.management')])>
                    <span>Order Management</span>
                </a>
                <a href="{{ route('received.orders') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('received.orders'), 'text-slate-400' => !request()->routeIs('received.orders')])>
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
            <button type="button" @class(['sidebar-group-toggle w-full flex items-center justify-between gap-3 pl-6 pr-4 py-3 text-sm font-medium cursor-pointer text-slate-300 border-l-[3px] border-transparent'])>
                <span class="flex items-center gap-3">
                    <svg class="w-4.5 h-4.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
                    </svg>
                    <span>Data Analytics</span>
                </span>
                <svg class="w-4 h-4 sidebar-arrow text-slate-400 mr-0 flex-shrink-0 arrow-left-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6 10l6 6 6-6H6z" />
                </svg>
            </button>
            <div class="sidebar-group-content flyout-animated overflow-hidden ml-6 pl-4 border-l-2 border-cyan-400/40 space-y-0.5 mt-1">
                <a href="{{ route('sales.analytics') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('sales.analytics'), 'text-slate-400' => !request()->routeIs('sales.analytics')])>
                    <span>Sales Analytics</span>
                </a>
                <a href="{{ route('pricing.module') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('pricing.module'), 'text-slate-400' => !request()->routeIs('pricing.module')])>
                    <span>Pricing Module</span>
                </a>
                <a href="{{ route('overstocking.report') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('overstocking.report'), 'text-slate-400' => !request()->routeIs('overstocking.report')])>
                    <span>Overstocking Report</span>
                </a>
                <a href="{{ route('out.of.stock') }}" @class(['sidebar-nav-item flex items-center py-2 text-sm font-medium transition w-full', 'text-cyan-300 font-semibold' => request()->routeIs('out.of.stock'), 'text-slate-400' => !request()->routeIs('out.of.stock')])>
                    <span>Out of Stock Report</span>
                </a>
            </div>
        </div>
        @elseif(auth()->user() && (auth()->user()->role === 'cashier' || auth()->user()->role === 'warehouse_personnel'))
        <a href="{{ route('sales.analytics') }}" @class(['sidebar-nav-item flex items-center gap-3 pl-6 pr-4 py-3 text-sm font-medium transition border-l-[3px]', 'bg-cyan-500/5 text-cyan-400 border-cyan-400' => request()->routeIs('sales.analytics'), 'text-slate-300 border-transparent' => !request()->routeIs('sales.analytics')])>
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
                    </svg>
            <span>Sales Analytics</span>
        </a>
        @endif

        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'warehouse_personnel'))
        <a href="{{ route('warehouse.management') }}" @class(['sidebar-nav-item flex items-center gap-3 pl-6 pr-4 py-3 text-sm font-medium transition border-l-[3px]', 'bg-cyan-500/5 text-cyan-400 border-cyan-400' => request()->routeIs('warehouse.management'), 'text-slate-300 border-transparent' => !request()->routeIs('warehouse.management')])>
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
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
                <rect x="1.6" y="17.1" width="20.8" height="0.5" />
            </svg>
            <span>Warehouse Management</span>
        </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('supplier.assessment') }}" @class(['sidebar-nav-item flex items-center gap-3 pl-6 pr-4 py-3 text-sm font-medium transition border-l-[3px]', 'bg-cyan-500/5 text-cyan-400 border-cyan-400' => request()->routeIs('supplier.assessment'), 'text-slate-300 border-transparent' => !request()->routeIs('supplier.assessment')])>
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" />
            </svg>
            <span>Supplier Assessment</span>
        </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('user.management') }}" @class(['sidebar-nav-item flex items-center gap-3 pl-6 pr-4 py-3 text-sm font-medium transition border-l-[3px]', 'bg-cyan-500/5 text-cyan-400 border-cyan-400' => request()->routeIs('user.management'), 'text-slate-300 border-transparent' => !request()->routeIs('user.management')])>
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.671 0 4.842-2.171 4.842-4.842S14.671 2.316 12 2.316 7.158 4.487 7.158 7.158 9.329 12 12 12zm0 2.526c-3.198 0-9.6 1.604-9.6 4.8v2.4h19.2v-2.4c0-3.196-6.402-4.8-9.6-4.8z" />
            </svg>
            <span>User Management</span>
        </a>
        @endif

        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('offline.reconciliation') }}" @class(['sidebar-nav-item flex items-center gap-3 pl-6 pr-4 py-3 text-sm font-medium transition border-l-[3px]', 'bg-cyan-500/5 text-cyan-400 border-cyan-400' => request()->routeIs('offline.reconciliation'), 'text-slate-300 border-transparent' => !request()->routeIs('offline.reconciliation')])>
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <defs>
                    <mask id="arc-mask">
                        <rect x="0" y="0" width="24" height="24" fill="white" />
                        <rect x="9" y="0" width="6" height="24" fill="black" />
                    </mask>
                </defs>
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" mask="url(#arc-mask)" />
                <circle cx="12" cy="12" r="6" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" mask="url(#arc-mask)" />
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" mask="url(#arc-mask)" />
                <circle cx="12" cy="12" r="1.6" fill="currentColor" />
            </svg>
            <span>Offline Reconciliation</span>
        </a>
        @endif

    </nav>
</div>

<style>
    .dropdown-active {
        visibility: visible !important;
        opacity: 1 !important;
        max-height: 400px !important;
        scale: 100% !important;
    }

    /* FIX: dating "transition: none !important" ang pumapatay sa lahat ng
       transition (kasama ang color), kaya biglaan/naiiwan yung font kapag
       nag-a-animate yung cyan wipe-in box. Ngayon, may sarili nang
       color/background-color transition na parehong 0.5s at parehong
       easing curve gaya ng wipeInRight animation, para magkasabay sila. */
    .sidebar-group-toggle {
        transition: color 0.5s cubic-bezier(0.65, 0, 0.35, 1),
                    background-color 0.5s cubic-bezier(0.65, 0, 0.35, 1);
    }

    .sidebar-group-toggle:hover {
        transform: none !important;
    }

    nav > a.sidebar-nav-item:hover {
        transform: none !important;
    }

    /* FIX: dagdag na color transition para sa direct nav links (Dashboard,
       Warehouse Management, atbp.) para pareho ring sabay ang font sa
       background wipe animation kapag na-click. */
    .sidebar-nav-item,
    .sidebar-group-toggle {
        transition: color 0.2s ease, background-color 0.2s ease;
    }

    /* FIX (equal hover/click box size): dati iba-iba ang row height ng bawat
       item dahil magkakaiba ang laki ng icons (w-5.5, w-5, w-4.5). Ngayon,
       fixed ang height ng lahat ng nav item / group toggle (parehong may
       submodule at wala), kaya pareho na rin lagi ang laki ng hover/click
       box sa lahat ng button, kahit anong laki ng icon sa loob. */
    nav > a.sidebar-nav-item,
    .sidebar-group-toggle {
        height: 48px;
        box-sizing: border-box;
    }

    .sidebar-group-content,
    .flyout-animated {
        display: none;
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        visibility: hidden;
        transform: translateY(-4px);
        transition: max-height 0.2s ease, opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    }

    .group.open .sidebar-group-content,
    .group.open .flyout-animated {
        display: block;
        opacity: 1;
        max-height: 999px;
        visibility: visible;
        transform: translateY(0);
    }

    .sidebar-nav-item.active {
        color: #7dd3fc;
        font-weight: 600;
    }

    /* Arrow: points up by default, animates to point down when submenu is open.
       FIX: dinagdagan ng color transition (parehong 0.5s/easing) para
       sabay ring mag-color-change ang arrow kasabay ng box wipe. */
    .sidebar-arrow {
        transition: transform 0.25s ease, color 0.5s cubic-bezier(0.65, 0, 0.35, 1);
        transform: rotate(180deg);
    }

    .group.open .sidebar-arrow {
        transform: rotate(0deg);
    }

    /* ============================================
       HOVER EFFECT (15px radius, shortened box, NOT sagad)
       - Plain hover: box appears INSTANTLY, no animation (transition: none)
       - No wipe-out animation on click anymore (removed)
       ============================================ */

    .sidebar-nav-item,
    .sidebar-group-toggle {
        position: relative;
        overflow: hidden;
        z-index: 0;
        isolation: isolate;
        transition: transform 0.2s ease, color 0.2s ease, background-color 0.2s ease;
    }

    .sidebar-nav-item::before,
    .sidebar-group-toggle::before {
        content: "";
        position: absolute;
        top: 5px;
        bottom: 5px;
        left: 12px;
        right: 18px;
        border-radius: 16px;
        background: rgba(123, 123, 123, 0.35);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.2s ease, opacity 0.2s ease;
        opacity: 0;
        z-index: -1;
        pointer-events: none;
    }

    /* Plain hover: smooth fade/scale on hover */
    .sidebar-nav-item:hover::before,
    .sidebar-group-toggle:hover::before {
        transform: scaleX(1);
        opacity: 1;
    }

    /* Walang hover BOX ang mga sub-menu items (Inventory Monitoring, All Stocks, etc.) */
    .sidebar-group-content .sidebar-nav-item::before {
        display: none;
    }

    /* Submenu font: pag hinover, magiging white lang ang kulay
       (kakulay ng font sa sidebar) - simple color change lang, walang
       ibang effect */
    .sidebar-group-content .sidebar-nav-item:hover {
        color: #ffffff;
        transform: translateY(-2px) scale(1.01);
    }

    .sidebar-nav-item:hover,
    .sidebar-group-toggle:hover {
        transform: translateY(-2px) scale(1.01);
    }

    /* Once already active/clicked, no more hover box (it already has its own
       persistent solid-cyan highlight below, so the extra gray hover box is
       not needed) */
    .sidebar-nav-item.bg-cyan-500\/5::before,
    .sidebar-group-toggle.bg-cyan-500\/5::before,
    .sidebar-group-content .sidebar-nav-item.text-cyan-300::before {
        display: none;
    }

    /* Remove the left border strip entirely — the rounded cyan box below
       is the only active indicator now, no more vertical line on the left */
    .sidebar-nav-item,
    .sidebar-group-toggle {
        border-left: none !important;
    }

    /* ============================================
       CLICKED / ACTIVE STATE
       Same box size/shape as the hover box (top/bottom 4px, left/right 10px,
       15px radius) but filled solid cyan, with black semi-bold text so it's
       easy to read and doesn't strain the eyes.
       Applied ONLY to main buttons / direct nav items (bg-cyan-500/5).
       Submenu items (text-cyan-300) are NOT affected.
       ============================================ */

    .sidebar-nav-item.bg-cyan-500\/5,
    .sidebar-group-toggle.bg-cyan-500\/5 {
        background-color: transparent !important;
        color: #000000 !important;
        font-weight: 600 !important;
    }

    .sidebar-nav-item.bg-cyan-500\/5 .sidebar-arrow,
    .sidebar-group-toggle.bg-cyan-500\/5 .sidebar-arrow {
        color: #000000 !important;
    }

    .sidebar-nav-item.bg-cyan-500\/5::after,
    .sidebar-group-toggle.bg-cyan-500\/5::after {
        content: "";
        position: absolute;
        top: 5px;
        bottom: 5px;
        left: 12px;
        right: 18px;
        border-radius: 16px;
        background: #00ddd2;
        transform: scaleX(1);
        transform-origin: left;
        animation: wipeInRight 0.5s cubic-bezier(0.65, 0, 0.35, 1);
        z-index: -1;
        pointer-events: none;
    }

    @keyframes wipeInRight {
        0% {
            transform: scaleX(0);
        }
        100% {
            transform: scaleX(1);
        }
    }

/* ===========================
   ARROW ALIGNMENT FIX
   =========================== */
.sidebar-group-toggle{
    padding-right:20px !important;
}
.sidebar-group-toggle .sidebar-arrow{
    margin-right:8px !important;
    flex-shrink:0;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const allGroups = document.querySelectorAll('.group');
    const allDirectNavItems = document.querySelectorAll('nav > a.sidebar-nav-item');

    function clearAllHighlights() {
        allGroups.forEach(group => {
            const button = group.querySelector('.sidebar-group-toggle');
            const arrow = group.querySelector('.sidebar-arrow');
            if (!button) return;


            button.classList.remove('bg-cyan-500/5', 'text-cyan-400', 'shadow-sm', 'border-cyan-400');
            button.classList.add('text-slate-300', 'border-transparent');

            if (arrow) {
                arrow.classList.remove('text-cyan-400');
                arrow.classList.add('text-slate-400');
            }
        });
    }

    function clearDirectNavHighlights() {
        allDirectNavItems.forEach(item => {
            item.classList.remove('bg-cyan-500/5', 'text-cyan-400', 'border-cyan-400');
            item.classList.add('text-slate-300', 'border-transparent');
        });
    }

    function highlightParentButton(group) {
        const button = group.querySelector('.sidebar-group-toggle');
        const arrow = group.querySelector('.sidebar-arrow');
        if (!button) return;

        button.classList.remove('text-slate-300', 'border-transparent');
        button.classList.add('bg-cyan-500/5', 'text-cyan-400', 'border-cyan-400');

        if (arrow) {
            arrow.classList.remove('text-slate-400');
            arrow.classList.add('text-cyan-400');
        }
    }

    function closeAllMenus() {
        allGroups.forEach(g => g.classList.remove('open'));
    }

    function hasActiveSubmodule(group) {
        const activeSubmodule = group.querySelector('.sidebar-group-content .sidebar-nav-item.text-cyan-300');
        return activeSubmodule !== null;
    }

    allGroups.forEach(group => {
        if (hasActiveSubmodule(group)) {
            highlightParentButton(group);
            group.classList.add('open');
        }
    });

    allGroups.forEach(group => {
        const button = group.querySelector('.sidebar-group-toggle');
        const content = group.querySelector('.sidebar-group-content');

        if (!button || !content) return;

        button.addEventListener('click', function(event) {
            event.preventDefault();
            const isOpen = group.classList.contains('open');

            clearAllHighlights();
            clearDirectNavHighlights();
            closeAllMenus();

            allGroups.forEach(g => {
                if (hasActiveSubmodule(g)) {
                    highlightParentButton(g);
                }
            });

            if (!isOpen) {
                group.classList.add('open');
                highlightParentButton(group);
            }

            document.querySelectorAll('.sidebar-group-content .sidebar-nav-item.active').forEach(activeItem => {
                activeItem.classList.remove('active');
            });
        });
    });

    const allSubmodules = document.querySelectorAll('.sidebar-group-content .sidebar-nav-item');
    allSubmodules.forEach(item => {
        item.addEventListener('click', function() {
            allSubmodules.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            const parentGroup = this.closest('.group');
            if (parentGroup) {
                clearAllHighlights();
                clearDirectNavHighlights();
                highlightParentButton(parentGroup);
            }
        });
    });

    allDirectNavItems.forEach(item => {
        item.addEventListener('click', function() {
            clearAllHighlights();
            closeAllMenus();
        });
    });
});
</script>