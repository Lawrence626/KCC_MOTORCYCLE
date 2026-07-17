<x-layouts.app :title="__('Order Management')">
    <div class="space-y-5">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
 <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Order Management</h1>
                <p class="max-w-2xl text-sm text-slate-500">Monitor and visualize purchase orders across the ordering lifecycle.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:border-[#105f68] hover:text-slate-900">Upload CSV</button>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total orders</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totalOrders) }}</p>
                <p class="mt-2 text-sm text-slate-500">All purchase orders created so far.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">In transit value</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">&#8369;{{ number_format($inTransitTotal, 2) }}</p>
                <p class="mt-2 text-sm text-slate-500">Total value of orders currently in transit.</p>
            </div>
            <div class="relative rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="absolute right-4 top-4">
                    <form id="receivedRangeForm" method="GET" action="{{ route('order.management') }}">
                        <input type="hidden" name="tab" value="{{ $activeTab }}">
                        <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedRange">
                            <span class="sr-only">Received range</span>
                            <input type="hidden" name="received_range" id="receivedRangeInput" value="{{ $receivedRange }}" />
                            <button type="button" id="receivedRangeButton" onclick="toggleDropdown('receivedRangeDropdown')" class="w-32 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-left text-sm text-slate-900 flex items-center justify-between gap-2 focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                                <span>{{ ucfirst($receivedRange) }}</span>
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="receivedRangeDropdown" class="dropdown-menu hidden absolute top-full right-0 z-50 mt-2 w-32 rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                                @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $value => $label)
                                    <button type="button" onclick="selectDropdown(event, 'receivedRangeInput', '{{ $value }}', 'receivedRangeButton', '{{ $label }}', 'receivedRangeDropdown', 'receivedRangeForm')" class="w-full px-4 py-1.5 text-center text-sm {{ $receivedRange === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                                @endforeach
                            </div>
                        </label>
                    </form>
                </div>
                <div>
                    <p class="pr-32 sm:pr-36 text-xs uppercase tracking-[0.2em] text-slate-400">Received {{ $receivedLabel }}</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($receivedCount) }}</p>
                    <p class="mt-2 text-sm text-slate-500 whitespace-nowrap">Completed orders added to inventory.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div id="orderTabs" class="flex items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div class="flex flex-wrap items-center gap-3">
                    <button class="tab-btn rounded-[10px] px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="orders">Purchase Orders</button>
                    <button class="tab-btn rounded-[10px] px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="back_orders">Back Orders</button>
                    <button class="tab-btn rounded-[10px] px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="received">Received Orders</button>
                    <button class="tab-btn rounded-[10px] px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="cancelled">Cancelled Orders</button>
                </div>
                
            </div>

            <div id="orders-tab" class="tab-content min-h-[360px]">
                <form id="ordersForm" method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search orders</span>
                        <input name="orders_search" value="{{ request('orders_search') }}" type="search" placeholder="Receipt no. or supplier" class="mt-2 w-full rounded-[10px] border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-0 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="ordersStatus">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <input type="hidden" name="orders_status" id="ordersStatusInput" value="{{ request('orders_status') }}" />
                        <button type="button" id="ordersStatusButton" onclick="toggleDropdown('ordersStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ request('orders_status') ? ucwords(request('orders_status')) : 'All Status' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="ordersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            @foreach(['all status' => 'All Status', 'pending approval' => 'Pending Approval', 'approved' => 'Approved', 'sent to supplier' => 'Sent To Supplier', 'in transit' => 'In Transit'] as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'ordersStatusInput', '{{ $value }}', 'ordersStatusButton', '{{ $label }}', 'ordersStatusDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('orders_status') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="ordersSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="orders_supplier" id="ordersSupplierInput" value="{{ request('orders_supplier') }}" />
                        <button type="button" id="ordersSupplierButton" onclick="toggleDropdown('ordersSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ request('orders_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="ordersSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'ordersSupplierInput', '', 'ordersSupplierButton', 'All suppliers', 'ordersSupplierDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ empty(request('orders_supplier')) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'ordersSupplierInput', '{{ $supplier->name }}', 'ordersSupplierButton', '{{ $supplier->name }}', 'ordersSupplierDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('orders_supplier') === $supplier->name ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
                <div class="mt-5 flex justify-end">
                    <a href="{{ route('order.create') }}" class="inline-flex items-center gap-2 rounded-[10px] bg-[#105f68] px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-[#105f68]/20 hover:bg-[#0c474e]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Purchase Order
                    </a>
                </div>
                @include('purchase_order.partials.orders-table', ['orders' => $orders, 'emptyMessage' => 'No active purchase orders have been created yet.'])
                <div class="mt-4 px-4">{{ $orders->links() }}</div>
            </div>

            <div id="back_orders-tab" class="tab-content hidden min-h-[360px]">
                <form id="backOrdersForm" method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="back_orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search back orders</span>
                        <input name="back_orders_search" value="{{ request('back_orders_search') }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-0 outline-none" />
                    </label>
                    @php
                        $backOrdersStatuses = [
                            '' => 'All status',
                            'waiting for supplier' => 'Waiting for Supplier'
                        ];
                        $currentBackOrdersStatusLabel = $backOrdersStatuses[request('back_orders_status')] ?? 'All status';
                    @endphp
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="backOrdersStatus">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <input type="hidden" name="back_orders_status" id="backOrdersStatusInput" value="{{ request('back_orders_status') }}" />
                        <button type="button" id="backOrdersStatusButton" onclick="toggleDropdown('backOrdersStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ $currentBackOrdersStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="backOrdersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            @foreach($backOrdersStatuses as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'backOrdersStatusInput', '{{ $value }}', 'backOrdersStatusButton', '{{ $label }}', 'backOrdersStatusDropdown', 'backOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('back_orders_status') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="backOrdersSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="back_orders_supplier" id="backOrdersSupplierInput" value="{{ request('back_orders_supplier') }}" />
                        <button type="button" id="backOrdersSupplierButton" onclick="toggleDropdown('backOrdersSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ request('back_orders_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="backOrdersSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'backOrdersSupplierInput', '', 'backOrdersSupplierButton', 'All suppliers', 'backOrdersSupplierDropdown', 'backOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ empty(request('back_orders_supplier')) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'backOrdersSupplierInput', '{{ $supplier->name }}', 'backOrdersSupplierButton', '{{ $supplier->name }}', 'backOrdersSupplierDropdown', 'backOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('back_orders_supplier') === $supplier->name ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
                @include('purchase_order.partials.back-orders-table', ['backOrders' => $backOrders])
                <div class="mt-4 px-4">{{ $backOrders->links() }}</div>
            </div>

            <div id="received-tab" class="tab-content hidden min-h-[360px]">
                <form id="receivedForm" method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="received">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                        <input name="received_search" value="{{ request('received_search') }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-0 outline-none" />
                    </label>
                    @php
                        $receivedStatuses = [
                            '' => 'All status',
                            'completed' => 'Completed',
                            'partially received' => 'Partially Received'
                        ];
                        $currentReceivedStatusLabel = $receivedStatuses[request('received_status')] ?? 'All status';
                    @endphp
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedStatus">
                        <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                        <input type="hidden" name="received_status" id="receivedStatusInput" value="{{ request('received_status') }}" />
                        <button type="button" id="receivedStatusButton" onclick="toggleDropdown('receivedStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ $currentReceivedStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="receivedStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            @foreach($receivedStatuses as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'receivedStatusInput', '{{ $value }}', 'receivedStatusButton', '{{ $label }}', 'receivedStatusDropdown', 'receivedForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('received_status') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="received_supplier" id="receivedSupplierInput" value="{{ request('received_supplier') }}" />
                        <button type="button" id="receivedSupplierButton" onclick="toggleDropdown('receivedSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ request('received_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="receivedSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'receivedSupplierInput', '', 'receivedSupplierButton', 'All suppliers', 'receivedSupplierDropdown', 'receivedForm')" class="w-full px-4 py-2.5 text-center text-sm {{ empty(request('received_supplier')) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'receivedSupplierInput', '{{ $supplier->name }}', 'receivedSupplierButton', '{{ $supplier->name }}', 'receivedSupplierDropdown', 'receivedForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('received_supplier') === $supplier->name ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
                @include('purchase_order.partials.orders-table', ['orders' => $receivedOrders, 'dateLabel' => 'Received', 'dateType' => 'received', 'emptyMessage' => 'No received purchase orders found.'])
                <div class="mt-4 px-4">{{ $receivedOrders->links() }}</div>
            </div>

            <div id="cancelled-tab" class="tab-content hidden min-h-[360px]">
                <form id="cancelledForm" method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="cancelled">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search cancelled orders</span>
                        <input name="cancelled_search" value="{{ request('cancelled_search') }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-0 outline-none" />
                    </label>
                    @php
                        $cancelledStatuses = [
                            '' => 'All status',
                            'rejected' => 'Rejected',
                            'cancelled' => 'Cancelled'
                        ];
                        $currentCancelledStatusLabel = $cancelledStatuses[request('cancelled_status')] ?? 'All status';
                    @endphp
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="cancelledStatus">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <input type="hidden" name="cancelled_status" id="cancelledStatusInput" value="{{ request('cancelled_status') }}" />
                        <button type="button" id="cancelledStatusButton" onclick="toggleDropdown('cancelledStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ $currentCancelledStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="cancelledStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            @foreach($cancelledStatuses as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'cancelledStatusInput', '{{ $value }}', 'cancelledStatusButton', '{{ $label }}', 'cancelledStatusDropdown', 'cancelledForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('cancelled_status') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="cancelledSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="cancelled_supplier" id="cancelledSupplierInput" value="{{ request('cancelled_supplier') }}" />
                        <button type="button" id="cancelledSupplierButton" onclick="toggleDropdown('cancelledSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#105f68]/20">
                            <span>{{ request('cancelled_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="cancelledSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'cancelledSupplierInput', '', 'cancelledSupplierButton', 'All suppliers', 'cancelledSupplierDropdown', 'cancelledForm')" class="w-full px-4 py-2.5 text-center text-sm {{ empty(request('cancelled_supplier')) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'cancelledSupplierInput', '{{ $supplier->name }}', 'cancelledSupplierButton', '{{ $supplier->name }}', 'cancelledSupplierDropdown', 'cancelledForm')" class="w-full px-4 py-2.5 text-center text-sm {{ request('cancelled_supplier') === $supplier->name ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
                @include('purchase_order.partials.orders-table', ['orders' => $cancelledOrders, 'dateLabel' => 'Created', 'dateType' => 'created', 'emptyMessage' => 'No cancelled purchase orders found.'])
                <div class="mt-4 px-4">{{ $cancelledOrders->links() }}</div>
            </div>
        </div>
    </div>

    <script>
        const activeTab = @json($activeTab);

        function showOrderTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active', 'bg-[#105f68]/10', 'text-[#105f68]'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

            const button = document.querySelector(`.tab-btn[data-tab="${tabName}"]`) || document.querySelector('.tab-btn[data-tab="orders"]');
            const content = document.getElementById((button.dataset.tab || 'orders') + '-tab');

            button.classList.add('active', 'bg-[#105f68]/10', 'text-[#105f68]');
            content.classList.remove('hidden');
        }

        function resetDropdownButtonStyles() {
            document.querySelectorAll('[id$="Button"]').forEach(btn => {
                btn.style.borderColor = '';
                btn.style.borderWidth = '';
                btn.style.boxShadow = '';
            });
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            const button = document.getElementById(id.replace('Dropdown', 'Button'));

            allDropdowns.forEach(d => {
                if (d.id !== id) d.classList.add('hidden');
            });

            resetDropdownButtonStyles();

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                if (button) {
                    button.style.borderColor = '#105f68';
                    button.style.borderWidth = '2px';
                    button.style.boxShadow = 'none';
                }
            } else {
                dropdown.classList.add('hidden');
            }
        }

        function selectDropdown(event, inputId, value, buttonId, label, dropdownId, formId) {
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
                event.stopPropagation();
            }

            document.getElementById(inputId).value = value;
            document.getElementById(buttonId).querySelector('span').textContent = label;
            document.getElementById(dropdownId).classList.add('hidden');
            const button = document.getElementById(buttonId);
            if (button) {
                button.style.borderColor = '';
                button.style.borderWidth = '';
                button.style.boxShadow = '';
            }
            document.getElementById(formId).submit();
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
                resetDropdownButtonStyles();
            }
        });

        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => showOrderTab(button.dataset.tab));
        });

        showOrderTab(activeTab);
    </script>
</x-layouts.app>