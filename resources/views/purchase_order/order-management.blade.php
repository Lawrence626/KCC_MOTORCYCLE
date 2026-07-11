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
                            <span>{{ request('orders_status') ? ucwords(request('orders_status')) : 'All statuses' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                        <div id="ordersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                            @foreach(['pending approval' => 'Pending Approval', 'approved' => 'Approved', 'sent to supplier' => 'Sent To Supplier', 'in transit' => 'In Transit'] as $value => $label)
                                <button type="button" onclick="selectDropdown(event, 'ordersStatusInput', '{{ $value }}', 'ordersStatusButton', '{{ $label }}', 'ordersStatusDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ request('orders_status') === $value ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
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
                            <button type="button" onclick="selectDropdown(event, 'ordersSupplierInput', '', 'ordersSupplierButton', 'All suppliers', 'ordersSupplierDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ empty(request('orders_supplier')) ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All suppliers</button>
                            @foreach($suppliers as $supplier)
                                <button type="button" onclick="selectDropdown(event, 'ordersSupplierInput', '{{ $supplier->name }}', 'ordersSupplierButton', '{{ $supplier->name }}', 'ordersSupplierDropdown', 'ordersForm')" class="w-full px-4 py-2.5 text-left text-sm {{ request('orders_supplier') === $supplier->name ? 'font-semibold text-[#105f68] bg-[#105f68]/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $supplier->name }}</button>
                            @endforeach
                        </div>
                    </label>
                </form>
                <div class="mt-5 flex justify-end">
                    <a href="{{ route('order.create') }}" class="inline-flex items-center gap-2 rounded-[10px] bg-[#105f68] px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-[#105f68]/20 hover:bg-[#0c474e]">Create Purchase Order</a>
                </div>
                @include('purchase_order.partials.orders-table', ['orders' => $orders, 'emptyMessage' => 'No active purchase orders have been created yet.'])
                <div class="mt-4 px-4">{{ $orders->links() }}</div>
            </div>

            <div id="back_orders-tab" class="tab-content hidden min-h-[360px]">
                <form method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="back_orders">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search back orders</span>
                        <input name="back_orders_search" value="{{ request('back_orders_search') }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <select name="back_orders_status" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="waiting for supplier" {{ request('back_orders_status') === 'waiting for supplier' ? 'selected' : '' }}>Waiting for Supplier</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="back_orders_supplier" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->name }}" {{ request('back_orders_supplier') === $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </form>
                @include('purchase_order.partials.back-orders-table', ['backOrders' => $backOrders])
                <div class="mt-4 px-4">{{ $backOrders->links() }}</div>
            </div>

            <div id="received-tab" class="tab-content hidden min-h-[360px]">
                <form method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="received">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                        <input name="received_search" value="{{ request('received_search') }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                        <select name="received_status" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="completed" {{ request('received_status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="partially received" {{ request('received_status') === 'partially received' ? 'selected' : '' }}>Partially Received</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="received_supplier" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->name }}" {{ request('received_supplier') === $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </form>
                @include('purchase_order.partials.orders-table', ['orders' => $receivedOrders, 'dateLabel' => 'Received', 'dateType' => 'received', 'emptyMessage' => 'No received purchase orders found.'])
                <div class="mt-4 px-4">{{ $receivedOrders->links() }}</div>
            </div>

            <div id="cancelled-tab" class="tab-content hidden min-h-[360px]">
                <form method="GET" action="{{ route('order.management') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="tab" value="cancelled">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search cancelled orders</span>
                        <input name="cancelled_search" value="{{ request('cancelled_search') }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <select name="cancelled_status" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                            <option value="">All statuses</option>
                            <option value="rejected" {{ request('cancelled_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ request('cancelled_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select name="cancelled_supplier" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                            <option value="">All suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->name }}" {{ request('cancelled_supplier') === $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
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