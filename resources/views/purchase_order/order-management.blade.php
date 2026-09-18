<x-layouts.app :title="__('Order Management')">
    <style>
        input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
            height: 16px;
            width: 16px;
            margin: 0 8px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' y1='6' x2='6' y2='18'%3E%3C/line%3E%3Cline x1='6' y1='6' x2='18' y2='18'%3E%3C/line%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            cursor: pointer;
            opacity: 0.7;
        }
        input[type="search"]::-webkit-search-cancel-button:hover {
            opacity: 1;
        }
    </style>
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
                <button class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Upload CSV</span>
                </button>
            </div>
        </div>
<div class="grid gap-4 sm:grid-cols-3">
    <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
                <p class="text-black text-xs font-semibold">Total orders</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-black">{{ number_format($totalOrders) }}</p>
                    <p class="text-gray-500 text-[11px] leading-tight mt-1 font-medium whitespace-nowrap">All purchase orders created so far.</p>
                </div>
            </div>
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                <svg class="w-5 h-5 text-[#145a66]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 2a1 1 0 0 0-1 1v1H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7.101A6.5 6.5 0 0 1 12 18.5 6.5 6.5 0 0 1 18.5 12c.352 0 .696.027 1.032.08A2 2 0 0 0 20 10V6a2 2 0 0 0-2-2h-1V3a1 1 0 1 0-2 0v1H9V3a1 1 0 0 0-1-1Zm-2 9h6a1 1 0 1 1 0 2H6a1 1 0 1 1 0-2Zm0-4h10a1 1 0 1 1 0 2H6a1 1 0 1 1 0-2Zm0 8h4.5a1 1 0 1 1 0 2H6a1 1 0 1 1 0-2Z"/>
                    <path d="M18.5 13.5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm2.78 3.72-3.25 3.25a.75.75 0 0 1-1.06 0l-1.25-1.25a.75.75 0 1 1 1.06-1.06l.72.72 2.72-2.72a.75.75 0 1 1 1.06 1.06Z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
                <p class="text-black text-xs font-semibold">In transit value</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-black">&#8369;{{ number_format($inTransitTotal, 2) }}</p>
                    <p class="text-gray-500 text-[11px] leading-tight mt-1 font-medium whitespace-nowrap">Total value of orders currently in transit.</p>
                </div>
            </div>
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: rgba(110, 193, 209, 0.18);">
                <svg class="w-5 h-5 text-[#145a66]" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3.375 4.25A2.125 2.125 0 0 0 1.25 6.375v8.75c0 .966.66 1.777 1.55 2.006a2.626 2.626 0 0 0 5.153-.256h4.11a2.626 2.626 0 0 0 5.13.256A2.001 2.001 0 0 0 18.75 15v-2.62a2 2 0 0 0-.386-1.185l-2.309-3.148A2 2 0 0 0 14.44 7.25H13V6.375A2.125 2.125 0 0 0 10.875 4.25h-7.5ZM13 8.75h1.44l1.965 2.677A.5.5 0 0 1 16 12H13V8.75ZM5.375 14.375a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25Zm9.25 0a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25Z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="relative border border-gray-200 p-4 bg-white shadow-sm" style="border-radius: 20px;">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
                <p class="text-black text-xs font-semibold">Received {{ $receivedLabel }}</p>
                <div class="mt-1">
                    <p class="text-2xl font-bold text-black">{{ number_format($receivedCount) }}</p>
                    <p class="text-gray-500 text-[11px] leading-tight mt-1 font-medium whitespace-nowrap">Completed orders added to inventory.</p>
                </div>
            </div>
            <div class="flex flex-shrink-0 items-end gap-2">
                <form id="receivedRangeForm" method="GET" action="{{ route('order.management') }}">
                    <input type="hidden" name="tab" value="{{ $activeTab }}" />
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedRange">
                        <span class="sr-only">Received range</span>
                        <input type="hidden" name="received_range" id="receivedRangeInput" value="{{ $receivedRange }}" />
                        <button type="button" id="receivedRangeButton" onclick="toggleDropdown('receivedRangeDropdown')" class="w-full min-w-[110px] px-2.5 py-1.5 rounded-[10px] text-xs font-bold text-[#145a66] flex items-center justify-between gap-1.5 transition cursor-pointer focus:outline-none focus:ring-0 shadow-sm" style="background-color: rgba(110, 193, 209, 0.18); border: none;">
                            <svg class="h-4 w-4 text-[#145a66] flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ ucfirst($receivedRange) }}</span>
                            <svg class="w-3.5 h-3.5 text-[#145a66] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="receivedRangeDropdown" class="dropdown-menu hidden absolute top-full left-0 right-0 w-full z-50 mt-1.5 rounded-[10px] border border-slate-200 bg-white shadow-xl p-1 space-y-0.5">
                            @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'receivedRangeInput', '{{ $value }}', 'receivedRangeButton', '{{ $label }}', 'receivedRangeDropdown', 'receivedRangeForm')" class="w-full px-2 py-1 text-center text-xs {{ $receivedRange === $value ? 'font-semibold text-slate-900 bg-gray-200' : 'text-slate-700 hover:bg-slate-100' }} rounded-[6px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>

        <div class="rounded-[15px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            <!-- Section Header Bar (matching All Stocks design) -->
            <div id="orderTabs" class="bg-[#0f172a] px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-800 rounded-t-[15px]">
                @php
                    $tabLabels = [
                        'orders' => 'Purchase Orders',
                        'back_orders' => 'Back Orders',
                        'received' => 'Received Orders',
                        'cancelled' => 'Cancelled Orders',
                    ];
                    $currentTabLabel = $tabLabels[$activeTab ?? 'orders'] ?? 'Purchase Orders';
                @endphp
                {{-- Order tab dropdown card --}}
                <div class="relative" id="orderTabDropdownWrapper">
                    <button type="button" id="orderTabDropdownBtn"
                        onclick="toggleOrderTabDropdown(event)"
                        class="inline-flex items-center gap-2 rounded-[10px] border border-[#59b2c2] bg-[#6EC1D1] px-3 py-1.5 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all duration-200 min-w-[150px] justify-between">
                        <span id="orderTabDropdownLabel">{{ $currentTabLabel }}</span>
                        <svg id="orderTabChevron" class="w-3.5 h-3.5 text-slate-900 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="orderTabDropdown"
                        class="hidden absolute top-full left-0 z-50 mt-1.5 w-full rounded-[12px] border border-slate-700 bg-[#0f172a] shadow-2xl overflow-hidden">
                        <div class="p-1 space-y-0.5">
                            {{-- Hidden proxy buttons for compatibility --}}
                            <button type="button" data-tab="orders" class="tab-btn hidden"></button>
                            <button type="button" data-tab="back_orders" class="tab-btn hidden"></button>
                            <button type="button" data-tab="received" class="tab-btn hidden"></button>
                            <button type="button" data-tab="cancelled" class="tab-btn hidden"></button>

                            <button type="button" onclick="selectOrderTab('orders', 'Purchase Orders')" id="orderTabOpt-orders" class="order-tab-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left cursor-pointer {{ ($activeTab ?? 'orders') === 'orders' ? 'bg-slate-700 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Purchase Orders</button>
                            <button type="button" onclick="selectOrderTab('back_orders', 'Back Orders')" id="orderTabOpt-back_orders" class="order-tab-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left cursor-pointer {{ ($activeTab ?? '') === 'back_orders' ? 'bg-slate-700 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Back Orders</button>
                            <button type="button" onclick="selectOrderTab('received', 'Received Orders')" id="orderTabOpt-received" class="order-tab-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left cursor-pointer {{ ($activeTab ?? '') === 'received' ? 'bg-slate-700 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Received Orders</button>
                            <button type="button" onclick="selectOrderTab('cancelled', 'Cancelled Orders')" id="orderTabOpt-cancelled" class="order-tab-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left cursor-pointer {{ ($activeTab ?? '') === 'cancelled' ? 'bg-slate-700 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">Cancelled Orders</button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('order.create') }}" class="inline-flex items-center gap-2 rounded-[10px] bg-[#6EC1D1] px-4 py-2 text-xs font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition-all flex-shrink-0">
                    <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Purchase Order
                </a>
            </div>

            <div class="p-5">

            <div id="orders-tab" class="tab-content min-h-[360px]">
                <form id="ordersForm" method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search orders</span>
                        <div class="flex items-center gap-2 mt-2">
                            <input name="orders_search" value="{{ request('orders_search') }}" type="search" placeholder="Receipt no. or supplier" class="flex-1 rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            <button type="submit" aria-label="Filter" class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-white text-slate-700 border border-slate-200 transition hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-black/35 flex-shrink-0" style="height: 42px; width: 42px;">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-2.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            </button>
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="ordersStatus">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <input type="hidden" name="orders_status" id="ordersStatusInput" value="{{ request('orders_status') }}" />
                        <button type="button" id="ordersStatusButton" onclick="toggleDropdown('ordersStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ request('orders_status') ? ucwords(request('orders_status')) : 'All Status' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="ordersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            @foreach(['all status' => 'All Status', 'pending approval' => 'Pending Approval', 'approved' => 'Approved', 'sent to supplier' => 'Sent To Supplier', 'in transit' => 'In Transit'] as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'ordersStatusInput', '{{ $value }}', 'ordersStatusButton', '{{ $label }}', 'ordersStatusDropdown', 'ordersForm')" class="w-full px-4 py-2 text-left text-sm {{ request('orders_status') === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="ordersSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="orders_supplier" id="ordersSupplierInput" value="{{ request('orders_supplier') }}" />
                        <button type="button" id="ordersSupplierButton" onclick="toggleDropdown('ordersSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ request('orders_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="ordersSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'ordersSupplierInput', '', 'ordersSupplierButton', 'All suppliers', 'ordersSupplierDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ empty(request('orders_supplier')) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'ordersSupplierInput', '{{ $supplier->name }}', 'ordersSupplierButton', '{{ $supplier->name }}', 'ordersSupplierDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ request('orders_supplier') === $supplier->name ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
                @include('purchase_order.partials.orders-table', ['orders' => $orders, 'emptyMessage' => 'No active purchase orders have been created yet.'])
                <div class="mt-4 px-4">{{ $orders->links() }}</div>
            </div>

            <div id="back_orders-tab" class="tab-content hidden min-h-[360px]">
                <form id="backOrdersForm" method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="back_orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search back orders</span>
                        <div class="flex items-center gap-2 mt-2">
                            <input name="back_orders_search" value="{{ request('back_orders_search') }}" type="search" placeholder="Order ID or supplier" class="flex-1 rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            <button type="submit" aria-label="Filter" class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-white text-slate-700 border border-slate-200 transition hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-black/35 flex-shrink-0" style="height: 42px; width: 42px;">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-2.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            </button>
                        </div>
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
                        <button type="button" id="backOrdersStatusButton" onclick="toggleDropdown('backOrdersStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ $currentBackOrdersStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="backOrdersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            @foreach($backOrdersStatuses as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'backOrdersStatusInput', '{{ $value }}', 'backOrdersStatusButton', '{{ $label }}', 'backOrdersStatusDropdown', 'backOrdersForm')" class="w-full px-4 py-2 text-left text-sm {{ request('back_orders_status') === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="backOrdersSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="back_orders_supplier" id="backOrdersSupplierInput" value="{{ request('back_orders_supplier') }}" />
                        <button type="button" id="backOrdersSupplierButton" onclick="toggleDropdown('backOrdersSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ request('back_orders_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="backOrdersSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'backOrdersSupplierInput', '', 'backOrdersSupplierButton', 'All suppliers', 'backOrdersSupplierDropdown', 'backOrdersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ empty(request('back_orders_supplier')) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'backOrdersSupplierInput', '{{ $supplier->name }}', 'backOrdersSupplierButton', '{{ $supplier->name }}', 'backOrdersSupplierDropdown', 'backOrdersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ request('back_orders_supplier') === $supplier->name ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
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
                        <div class="flex items-center gap-2 mt-2">
                            <input name="received_search" value="{{ request('received_search') }}" type="search" placeholder="Order ID or supplier" class="flex-1 rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            <button type="submit" aria-label="Filter" class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-white text-slate-700 border border-slate-200 transition hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-black/35 flex-shrink-0" style="height: 42px; width: 42px;">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-2.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            </button>
                        </div>
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
                        <button type="button" id="receivedStatusButton" onclick="toggleDropdown('receivedStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ $currentReceivedStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="receivedStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            @foreach($receivedStatuses as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'receivedStatusInput', '{{ $value }}', 'receivedStatusButton', '{{ $label }}', 'receivedStatusDropdown', 'receivedForm')" class="w-full px-4 py-2 text-left text-sm {{ request('received_status') === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="received_supplier" id="receivedSupplierInput" value="{{ request('received_supplier') }}" />
                        <button type="button" id="receivedSupplierButton" onclick="toggleDropdown('receivedSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ request('received_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="receivedSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'receivedSupplierInput', '', 'receivedSupplierButton', 'All suppliers', 'receivedSupplierDropdown', 'receivedForm')" class="w-full px-4 py-2.5 text-left text-sm {{ empty(request('received_supplier')) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'receivedSupplierInput', '{{ $supplier->name }}', 'receivedSupplierButton', '{{ $supplier->name }}', 'receivedSupplierDropdown', 'receivedForm')" class="w-full px-4 py-2.5 text-left text-sm {{ request('received_supplier') === $supplier->name ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
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
                        <div class="flex items-center gap-2 mt-2">
                            <input name="cancelled_search" value="{{ request('cancelled_search') }}" type="search" placeholder="Order ID or supplier" class="flex-1 rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                            <button type="submit" aria-label="Filter" class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-white text-slate-700 border border-slate-200 transition hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-black/35 flex-shrink-0" style="height: 42px; width: 42px;">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-2.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            </button>
                        </div>
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
                        <button type="button" id="cancelledStatusButton" onclick="toggleDropdown('cancelledStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ $currentCancelledStatusLabel }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="cancelledStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            @foreach($cancelledStatuses as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'cancelledStatusInput', '{{ $value }}', 'cancelledStatusButton', '{{ $label }}', 'cancelledStatusDropdown', 'cancelledForm')" class="w-full px-4 py-2 text-left text-sm {{ request('cancelled_status') === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                            @endforeach
                        </div>
                    </label>
                    <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="cancelledSupplier">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="cancelled_supplier" id="cancelledSupplierInput" value="{{ request('cancelled_supplier') }}" />
                        <button type="button" id="cancelledSupplierButton" onclick="toggleDropdown('cancelledSupplierDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                            <span>{{ request('cancelled_supplier') ?: 'All suppliers' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="cancelledSupplierDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[9999] mt-1.5 w-full max-h-48 overflow-y-auto rounded-[10px] border border-slate-200 bg-white shadow-2xl p-2 space-y-1">
                            <button type="button" onclick="selectDropdown(event, 'cancelledSupplierInput', '', 'cancelledSupplierButton', 'All suppliers', 'cancelledSupplierDropdown', 'cancelledForm')" class="w-full px-4 py-2.5 text-left text-sm {{ empty(request('cancelled_supplier')) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'cancelledSupplierInput', '{{ $supplier->name }}', 'cancelledSupplierButton', '{{ $supplier->name }}', 'cancelledSupplierDropdown', 'cancelledForm')" class="w-full px-4 py-2.5 text-left text-sm {{ request('cancelled_supplier') === $supplier->name ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
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

        const ORDER_TAB_LABELS = {
            orders: 'Purchase Orders',
            back_orders: 'Back Orders',
            received: 'Received Orders',
            cancelled: 'Cancelled Orders',
        };

        function showOrderTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

            const tabKey = (tabName in ORDER_TAB_LABELS) ? tabName : 'orders';
            const content = document.getElementById(tabKey + '-tab');
            if (content) {
                content.classList.remove('hidden');
            }

            // sync dropdown trigger label
            const label = document.getElementById('orderTabDropdownLabel');
            if (label) label.textContent = ORDER_TAB_LABELS[tabKey] || 'Purchase Orders';

            // sync dropdown option highlights
            Object.keys(ORDER_TAB_LABELS).forEach(key => {
                const opt = document.getElementById('orderTabOpt-' + key);
                if (!opt) return;
                if (key === tabKey) {
                    opt.className = 'order-tab-dd-opt w-full px-3 py-1 text-sm font-semibold rounded-[8px] transition-colors text-left bg-slate-700 text-white cursor-pointer';
                } else {
                    opt.className = 'order-tab-dd-opt w-full px-3 py-1 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white hover:bg-slate-800 cursor-pointer';
                }
            });
        }

        window.toggleOrderTabDropdown = function (e) {
            if (e) e.stopPropagation();
            const dd = document.getElementById('orderTabDropdown');
            const chevron = document.getElementById('orderTabChevron');
            if (!dd) return;
            const isHidden = dd.classList.contains('hidden');
            dd.classList.toggle('hidden', !isHidden);
            if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
        };

        window.selectOrderTab = function (tabName, labelText) {
            const dd = document.getElementById('orderTabDropdown');
            const chevron = document.getElementById('orderTabChevron');
            if (dd) dd.classList.add('hidden');
            if (chevron) chevron.style.transform = '';
            showOrderTab(tabName);
            setTimeout(resolvePoOrderImages, 50);
        };

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('orderTabDropdownWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const dd = document.getElementById('orderTabDropdown');
                const chevron = document.getElementById('orderTabChevron');
                if (dd) dd.classList.add('hidden');
                if (chevron) chevron.style.transform = '';
            }
        });

        function resetDropdownButtonStyles() {
            document.querySelectorAll('[id$="Button"]').forEach(btn => {
                if (btn.id === 'receivedRangeButton') {
                    btn.style.backgroundColor = 'rgba(110, 193, 209, 0.18)';
                    btn.style.borderColor = '#a2deea';
                    return;
                }
                btn.style.borderColor = '';
                btn.style.borderWidth = '';
                btn.style.boxShadow = '';
                btn.style.backgroundColor = '';
                const chevron = btn.querySelector('.w-4.h-4');
                if (chevron) chevron.style.color = '';
            });
            document.querySelectorAll('[data-dropdown-wrapper]').forEach(w => {
                w.style.zIndex = '';
            });
        }

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            const button = document.getElementById(id.replace('Dropdown', 'Button'));

            allDropdowns.forEach(d => {
                if (d.id !== id) {
                    d.classList.add('hidden');
                    const wrapper = d.closest('[data-dropdown-wrapper]');
                    if (wrapper) wrapper.style.zIndex = '';
                }
            });

            resetDropdownButtonStyles();

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                const wrapper = dropdown.closest('[data-dropdown-wrapper]');
                if (wrapper) wrapper.style.zIndex = '9999';

                if (button && button.id !== 'receivedRangeButton') {
                    button.style.borderColor = 'rgba(0, 0, 0, 0.35)';
                    button.style.borderWidth = '1px';
                    button.style.boxShadow = 'none';
                    button.style.backgroundColor = '#9ca3af !important';
                    const chevron = button.querySelector('.w-4.h-4');
                    if (chevron) chevron.style.color = 'black';
                }
            } else {
                dropdown.classList.add('hidden');
                const wrapper = dropdown.closest('[data-dropdown-wrapper]');
                if (wrapper) wrapper.style.zIndex = '';
            }
        }

        function selectDropdown(event, inputId, value, buttonId, label, dropdownId, formId) {
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
                event.stopPropagation();
            }

            document.getElementById(inputId).value = value;
            document.getElementById(buttonId).querySelector('span').textContent = label;
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.add('hidden');
                const wrapper = dropdown.closest('[data-dropdown-wrapper]');
                if (wrapper) wrapper.style.zIndex = '';
            }
            const button = document.getElementById(buttonId);
            if (button) {
                if (buttonId === 'receivedRangeButton') {
                    button.style.backgroundColor = 'rgba(110, 193, 209, 0.18)';
                    button.style.borderColor = '#a2deea';
                } else {
                    button.style.borderColor = '';
                    button.style.borderWidth = '';
                    button.style.boxShadow = '';
                }
            }
            document.getElementById(formId).submit();
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-menu') && !event.target.closest('[onclick^="toggleDropdown"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => {
                    d.classList.add('hidden');
                    const wrapper = d.closest('[data-dropdown-wrapper]');
                    if (wrapper) wrapper.style.zIndex = '';
                });
                resetDropdownButtonStyles();
            }
        });

        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                showOrderTab(button.dataset.tab);
                setTimeout(resolvePoOrderImages, 50);
            });
        });

        function resolvePoOrderImages() {
            try {
                const stored = localStorage.getItem('posProductImages');
                if (!stored) return;
                const images = JSON.parse(stored);
                const keys = Object.keys(images);

                document.querySelectorAll('.po-order-img-thumb').forEach(container => {
                    const id = container.dataset.id;
                    const sku = container.dataset.sku;
                    const name = container.dataset.name;

                    let imgUrl = null;
                    if (id && images[id]) imgUrl = images[id];
                    else if (sku && images[sku]) imgUrl = images[sku];
                    else if (name && images[name]) imgUrl = images[name];
                    else {
                        if (sku) {
                            const matchSku = keys.find(k => k.toLowerCase() === String(sku).toLowerCase());
                            if (matchSku) imgUrl = images[matchSku];
                        }
                        if (!imgUrl && name) {
                            const matchName = keys.find(k => k.toLowerCase() === String(name).toLowerCase());
                            if (matchName) imgUrl = images[matchName];
                        }
                    }

                    if (imgUrl) {
                        container.innerHTML = '';
                        container.className = 'po-order-img-thumb w-8 h-8 rounded-[6px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center';
                        container.style.backgroundImage = `url('${imgUrl}')`;
                    }
                });
            } catch(e) {
                console.error('Error resolving order product images:', e);
            }
        }

        showOrderTab(activeTab);
        resolvePoOrderImages();
    </script>
    <style>
        #receivedRangeButton {
            background-color: rgba(110, 193, 209, 0.18) !important;
            border: 1px solid transparent !important;
            box-shadow: none !important;
            outline: none !important;
            transition: all 0.2s ease !important;
            -webkit-tap-highlight-color: transparent !important;
        }
        #receivedRangeButton:hover,
        #receivedRangeButton:focus,
        #receivedRangeButton:active,
        #receivedRangeButton:focus-visible {
            border-color: #6EC1D1 !important;
            box-shadow: none !important;
        }
    </style>
</x-layouts.app>