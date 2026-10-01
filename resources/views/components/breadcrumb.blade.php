@php
    $breadcrumbItems = [];

    // Get current route and generate breadcrumb based on sidebar hierarchy
    $currentRoute = request()->route() ? request()->route()->getName() : '';

    // Dashboard is always the root
    $breadcrumbItems[] = [
        'name' => 'Dashboard',
        'url' => route('dashboard'),
        'active' => $currentRoute === 'dashboard'
    ];

    // Determine breadcrumb based on current route
    if ($currentRoute === 'settings.general') {
        $breadcrumbItems[] = ['name' => 'Profile & Settings', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'profile.show' && request()->query('from') === 'settings') {
        $breadcrumbItems[] = ['name' => 'Profile & Settings', 'url' => route('settings.general'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'My Profile', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'profile.show') {
        $breadcrumbItems[] = ['name' => 'My Profile', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'inventory.monitoring') {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Inventory Monitoring', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'allstocks') {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'All Stocks', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'product.categorization') {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Product Categorization', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'item.disposal') {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Item Disposal List', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'reverse-logistics') {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Reverse Logistics', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'archived') {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'All Stocks', 'url' => route('allstocks'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Archived Items', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'pos.archived') {
        $breadcrumbItems[] = ['name' => 'Point of Sales', 'url' => route('pos.terminal'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'POS Terminal', 'url' => route('pos.terminal'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Archived Items', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'pos.terminal') {
        $breadcrumbItems[] = ['name' => 'Point of Sales', 'url' => route('pos.terminal'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'POS Terminal', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'replacing.items') {
        $breadcrumbItems[] = ['name' => 'Point of Sales', 'url' => route('pos.terminal'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Records of Replacing Items', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'order.management') {
        $breadcrumbItems[] = ['name' => 'Purchase Order', 'url' => route('order.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Order Management', 'url' => null, 'active' => true];
    } elseif (str_starts_with($currentRoute, 'order.create')) {
        $breadcrumbItems[] = ['name' => 'Purchase Order', 'url' => route('order.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Order Management', 'url' => route('order.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Create Purchase Order', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'received.orders') {
        $breadcrumbItems[] = ['name' => 'Purchase Order', 'url' => route('order.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Received Orders', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'order.create') {
        $breadcrumbItems[] = ['name' => 'Purchase Order', 'url' => route('order.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Order Management', 'url' => route('order.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Create Purchase Order', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'sales.analytics') {
        $breadcrumbItems[] = ['name' => 'Data Analytics', 'url' => route('sales.analytics'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Sales Analytics', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'pricing.module') {
        $breadcrumbItems[] = ['name' => 'Data Analytics', 'url' => route('sales.analytics'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Pricing Module', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'overstocking.report') {
        $breadcrumbItems[] = ['name' => 'Data Analytics', 'url' => route('sales.analytics'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Overstocking Report', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'out.of.stock') {
        $breadcrumbItems[] = ['name' => 'Data Analytics', 'url' => route('sales.analytics'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Out of Stock Report', 'url' => null, 'active' => true];
    } elseif (str_starts_with($currentRoute, 'dss.dead-stock')) {
        $breadcrumbItems[] = ['name' => 'Data Analytics', 'url' => route('sales.analytics'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Dead Stock Analysis', 'url' => null, 'active' => true];
    } elseif (str_starts_with($currentRoute, 'warehouse')) {
        $breadcrumbItems[] = ['name' => 'Warehouse Management', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'shop.inventory') {
        $breadcrumbItems[] = ['name' => 'Shop Inventory Items', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'shop.inventory.archived') {
        $breadcrumbItems[] = ['name' => 'Shop Inventory Items', 'url' => route('shop.inventory'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Archived Shelves', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'supplier.assessment') {
        $breadcrumbItems[] = ['name' => 'Supplier Assessment', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'supplier.assessment.archived') {
        $breadcrumbItems[] = ['name' => 'Supplier Assessment', 'url' => route('supplier.assessment'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Archived Suppliers', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'user.management') {
        $breadcrumbItems[] = ['name' => 'User Management', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'user.management.archived') {
        $breadcrumbItems[] = ['name' => 'User Management', 'url' => route('user.management'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Archived Users', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.reconciliation') {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.purchase-orders') {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => route('offline.reconciliation'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Purchase Orders', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.export') {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => route('offline.reconciliation'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Export Data', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.import' || str_starts_with($currentRoute, 'offline.import.')) {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => route('offline.reconciliation'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Import Data', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.pending.imports' || str_starts_with($currentRoute, 'offline.pending.')) {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => route('offline.reconciliation'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Pending Imports', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.history') {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => route('offline.reconciliation'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Sync History', 'url' => null, 'active' => true];
    } elseif ($currentRoute === 'offline.report') {
        $breadcrumbItems[] = ['name' => 'Offline Reconciliation', 'url' => route('offline.reconciliation'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Reconciliation Report', 'url' => null, 'active' => true];
    } elseif (str_starts_with($currentRoute, 'product-catalog')) {
        $breadcrumbItems[] = ['name' => 'Inventory Management', 'url' => route('inventory.monitoring'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Product Categorization', 'url' => route('product.categorization'), 'active' => false];
        $breadcrumbItems[] = ['name' => 'Add New Product', 'url' => null, 'active' => true];
    }
@endphp

@php
    $activeBreadcrumbClasses = 'bg-slate-200/45 text-slate-800 font-semibold';
    $inactiveBreadcrumbClasses = 'text-slate-500 hover:bg-slate-100 hover:text-slate-700';
@endphp

<nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-1.5 text-sm">
    @foreach($breadcrumbItems as $index => $item)
        @if($index > 0)
            <span class="text-slate-400 text-xs select-none">›</span>
        @endif

        @if($item['url'])
            <a href="{{ $item['url'] }}"
               class="inline-flex items-center rounded-md px-1.5 py-0.5 transition {{ $item['active'] ? $activeBreadcrumbClasses : $inactiveBreadcrumbClasses }}">
                {{ $item['name'] }}
            </a>
        @else
            <span class="inline-flex items-center rounded-md px-2 py-1 {{ $item['active'] ? $activeBreadcrumbClasses : 'bg-slate-100 text-slate-800 font-semibold' }}">
                {{ $item['name'] }}
            </span>
        @endif
    @endforeach
</nav>
