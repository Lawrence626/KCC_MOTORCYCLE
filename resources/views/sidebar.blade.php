<!-- Sidebar -->
<div class="w-full md:w-70 text-white flex flex-col h-screen shadow-2xl
overflow-y-auto overflow-x-hidden sidebar-scroll border-r border-slate-700 rounded-r-[30px]"
 style="background: linear-gradient(to bottom, #090909, #222222, #413f3f);">

    <!-- Logo Section -->
    <a href="{{ route('dashboard') }}" class="px-6 py-5 border-b border-slate-700/50 flex-shrink-0 hover:opacity-80 transition-opacity">
        <div class="flex flex-col items-center justify-center">
            <div class="w-42">
                <img src="{{ asset('images/Logo.png') }}" alt="KCC Logo" class="w-full h-full object-contain" />
            </div>
        </div>
    </a>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto overflow-x-hidden">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400 hover:bg-cyan-500/25' => request()->routeIs('dashboard'), 'hover:bg-slate-700/50 text-slate-300' => !request()->routeIs('dashboard')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            <span>Dashboard</span>
        </a>

        <!-- Inventory Management -->
        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'inventory_clerk'))
        <div class="group space-y-1 @if(request()->routeIs('inventory.monitoring') || request()->routeIs('allstocks') || request()->routeIs('product.categorization') || request()->routeIs('item.disposal') || request()->routeIs('reverse-logistics')) open @endif">
            <button type="button" class="sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700/50 transition cursor-pointer">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" /></svg>
                    <span>Inventory Management</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="sidebar-group-content bg-slate-900/70 px-1 pb-3 rounded-xl">
                <a href="{{ route('inventory.monitoring') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('inventory.monitoring') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Inventory Monitoring</span>
                </a>
                <a href="{{ route('allstocks') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('allstocks') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>All Stocks</span>
                </a>
                <a href="{{ route('product.categorization') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('product.categorization') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Product Categorization</span>
                </a>
                <a href="{{ route('item.disposal') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('item.disposal') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Item Disposal List</span>
                </a>
                <a href="{{ route('reverse-logistics') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('reverse-logistics') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Reverse Logistics</span>
                </a>
            </div>
        </div>
        @elseif(auth()->user() && (auth()->user()->role === 'cashier' || auth()->user()->role === 'warehouse_personnel'))
        <!-- Simplified Inventory Management for Cashier and Warehouse Personnel -->
        <div class="group space-y-1 @if(request()->routeIs('inventory.monitoring') || request()->routeIs('allstocks') || request()->routeIs('product.categorization')) open @endif">
            <button type="button" class="sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700/50 transition cursor-pointer">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" /></svg>
                    <span>Inventory Management</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="sidebar-group-content bg-slate-900/70 px-1 pb-3 rounded-xl">
                <a href="{{ route('inventory.monitoring') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('inventory.monitoring') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Inventory Monitoring</span>
                </a>
                @if(auth()->user()->role === 'warehouse_personnel')
                <a href="{{ route('allstocks') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('allstocks') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>All Stocks</span>
                </a>
                @endif
                <a href="{{ route('product.categorization') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('product.categorization') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Product Categorization</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Point of Sales -->
        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'cashier'))
        <div class="group space-y-1 @if(request()->routeIs('pos.terminal') || request()->routeIs('replacing.items')) open @endif">
            <button type="button" class="sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700/50 transition cursor-pointer">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Point of Sales</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="sidebar-group-content bg-slate-900/70 px-1 pb-3 rounded-xl">
                <a href="{{ route('pos.terminal') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('pos.terminal') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>POS Terminal</span>
                </a>
                <a href="{{ route('replacing.items') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('replacing.items') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Records of Replacing Items</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Purchase Order -->
        @if(auth()->user() && auth()->user()->role === 'admin')
        <div class="group space-y-1 @if(request()->routeIs('order.management') || request()->routeIs('purchase.requests') || request()->routeIs('received.orders')) open @endif">
            <button type="button" class="sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700/50 transition cursor-pointer">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Purchase Order</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="sidebar-group-content bg-slate-900/70 px-1 pb-3 rounded-xl">
                <a href="{{ route('order.management') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('order.management') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Order Management</span>
                </a>
                <a href="{{ route('received.orders') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('received.orders') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Received Orders</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Data Analytics - Full menu for Admin and Inventory Clerk -->
        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'inventory_clerk'))
        <div class="group space-y-1 @if(request()->routeIs('sales.analytics') || request()->routeIs('pricing.module') || request()->routeIs('overstocking.report') || request()->routeIs('out.of.stock')) open @endif">
            <button type="button" class="sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700/50 transition cursor-pointer">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Data Analytics</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="sidebar-group-content bg-slate-900/70 px-1 pb-3 rounded-xl">
                <a href="{{ route('sales.analytics') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('sales.analytics') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Sales Analytics</span>
                </a>
                <a href="{{ route('pricing.module') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('pricing.module') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Pricing Module</span>
                </a>
                <a href="{{ route('overstocking.report') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('overstocking.report') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Overstocking Report</span>
                </a>
                <a href="{{ route('out.of.stock') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('out.of.stock') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Out of Stock Report</span>
                </a>
            </div>
        </div>
        @elseif(auth()->user() && (auth()->user()->role === 'cashier' || auth()->user()->role === 'warehouse_personnel'))
        <!-- Sales Analytics only for Cashier and Warehouse Personnel -->
        <a href="{{ route('sales.analytics') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400 hover:bg-cyan-500/25' => request()->routeIs('sales.analytics'), 'hover:bg-slate-700/50 text-slate-300' => !request()->routeIs('sales.analytics')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Sales Analytics</span>
        </a>
        @endif

        <!-- Warehouse Management -->
        @if(auth()->user() && (auth()->user()->role === 'admin' || auth()->user()->role === 'warehouse_personnel'))
        <a href="{{ route('warehouse.management') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400 hover:bg-cyan-500/25' => request()->routeIs('warehouse.management'), 'hover:bg-slate-700/50 text-slate-300' => !request()->routeIs('warehouse.management')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 008.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <span>Warehouse Management</span>
        </a>
        @endif

        <!-- Supplier Assessment -->
        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('supplier.assessment') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400 hover:bg-cyan-500/25' => request()->routeIs('supplier.assessment'), 'hover:bg-slate-700/50 text-slate-300' => !request()->routeIs('supplier.assessment')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Supplier Assessment</span>
        </a>
        @endif

        <!-- User Management -->
        @if(auth()->user() && auth()->user()->role === 'admin')
        <a href="{{ route('user.management') }}" @class(['sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition', 'bg-cyan-500/15 text-cyan-400 hover:bg-cyan-500/25' => request()->routeIs('user.management'), 'hover:bg-slate-700/50 text-slate-300' => !request()->routeIs('user.management')])>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646zM9 9H5m10 0h-4m7 6a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>User Management</span>
        </a>
        @endif

        <!-- Offline Reconciliation -->
        @if(auth()->user() && auth()->user()->role === 'admin')
        <div class="group space-y-1 @if(request()->routeIs('offline.purchase-orders') || request()->routeIs('offline.inventory-movements') || request()->routeIs('offline.export') || request()->routeIs('offline.import') || request()->routeIs('offline.history') || request()->routeIs('offline.report') || request()->routeIs('offline.pending.imports')) open @endif">
            <button type="button" class="sidebar-group-toggle w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700/50 transition cursor-pointer">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v.01M9 17h12a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zm3-10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Offline Reconciliation</span>
                </span>
                <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div class="sidebar-group-content bg-slate-900/70 px-1 pb-3 rounded-xl">
                <a href="{{ route('offline.purchase-orders') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('offline.purchase-orders') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Offline Purchase Orders</span>
                </a>
                <a href="{{ route('offline.import') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('offline.import') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Import Data</span>
                </a>
                <a href="{{ route('offline.pending.imports') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('offline.pending.imports') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Pending Imports</span>
                </a>
                <a href="{{ route('offline.history') }}" class="sidebar-nav-item flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-700/50">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ request()->routeIs('offline.history') ? 'bg-cyan-400' : 'bg-slate-500' }}"></span>
                    <span>Synchronization History</span>
                </a>
            </div>
        </div>
        @endif



    </nav>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.sidebar-group-toggle').forEach(button => {
            const group = button.closest('.group');
            const content = button.nextElementSibling;
            const svg = button.querySelector('svg:last-child');

            // Ensure the content has transition-friendly inline styles
            content.style.maxHeight = '0px';
            content.style.opacity = '0';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.45s ease, opacity 0.45s ease, transform 0.45s ease';
            svg.style.transition = 'transform 0.45s ease';

            // If server marked group as open, expand it on load
            if (group.classList.contains('open')) {
                content.style.maxHeight = '500px';
                content.style.opacity = '1';
                svg.classList.add('rotate-180');
            }

            // Hover behavior (keeps smooth animation)
            group.addEventListener('mouseenter', function() {
                content.style.maxHeight = '500px';
                content.style.opacity = '1';
                svg.classList.add('rotate-180');
            });

            group.addEventListener('mouseleave', function() {
                // don't collapse if the group was marked open by route
                if (group.classList.contains('open')) return;
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                svg.classList.remove('rotate-180');
            });
        });
    });
</script>
