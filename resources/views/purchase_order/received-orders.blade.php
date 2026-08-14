<x-layouts.app :title="__('Received Orders')">
    <div class="space-y-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Received Orders</h1>
                <p class="max-w-2xl text-sm text-slate-500">Track completed deliveries, confirm order receipts, and view inventory impact.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">Confirm Receipt</button>
                <button id="ro-generate-qr" type="button" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Generate QR Codes
                </button>
                <button id="ro-scan-qr" type="button" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Scan QR
                </button>
                <button id="ro-mobile-scanner" type="button" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-black/10 transition-all duration-200">
                    <svg class="h-4 w-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Mobile Scanner
                </button>
            </div>
        </div>

       <div class="grid gap-3 sm:grid-cols-3 items-stretch">
            <div class="rounded-[28px] border border-slate-200 p-5 shadow-sm flex flex-col justify-between h-full" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm text-slate-600 font-medium mb-1">Delivered today</p>
                <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#00fff2] text-black shadow-md shadow-[#00fff2]/40 border border-gray-200">
            <svg class="h-6 w-6 text-black" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
            </svg>
        </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($deliveredToday) }}</p>
                <p class="mt-1 text-sm text-slate-500">Orders received and logged today.</p>
            </div>
            <div class="rounded-[28px] border border-slate-200 p-5 shadow-sm flex flex-col justify-between h-full" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm text-slate-600 font-medium mb-1">Pending confirmation</p>
                    <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#00fff2] text-black shadow-md shadow-[#00fff2]/40 border border-gray-200">
                        <svg class="h-6 w-6 text-black" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($pendingConfirmation) }}</p>
                <p class="mt-1 text-sm text-slate-500">Awaiting goods inspection or paperwork.</p>
            </div>
            <div class="rounded-[28px] border border-slate-200 p-5 shadow-sm flex flex-col justify-between h-full" style="background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm text-slate-600 font-medium mb-1">Issues found</p>
                    <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-[#00fff2] text-black shadow-md shadow-[#00fff2]/40 border border-gray-200">
                        <svg class="h-6 w-6 text-black" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($issuesFound) }}</p>
                <p class="mt-1 text-sm text-slate-500">Discrepancies requiring follow-up.</p>
            </div>
        </div>

        <section class="rounded-[10px] border border-slate-200 bg-white overflow-hidden shadow-sm" style="min-height: calc(100vh - 220px);">
            <!-- Section Header Bar (matching All Stocks design) -->
            <div class="bg-[#0f172a] px-6 py-4 flex items-center justify-between gap-4 border-b border-slate-800 rounded-t-[10px]">
                <div>
                    <h2 class="text-lg font-bold text-white">Latest received orders</h2>
                    <p class="text-xs text-slate-300">Recent receipts in a concise table.</p>
                </div>
                <span class="rounded-full bg-emerald-500/10 border border-emerald-500/20 px-4 py-1.5 text-xs font-semibold text-emerald-400">Verified</span>
            </div>

            <div class="p-5">

            <form id="receivedOrdersForm" method="GET" action="{{ route('received.orders') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                    <div class="flex items-center gap-2 mt-2">
                        <input name="search" type="search" value="{{ $search ?? '' }}" placeholder="Order ID or supplier" class="flex-1 rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" />
                        <button type="submit" aria-label="Filter" class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] bg-white text-slate-700 border border-slate-200 transition hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-black/35 flex-shrink-0" style="height: 42px; width: 42px;">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V18a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-2.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        </button>
                    </div>
                </label>
                <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedOrdersStatus">
                    <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                    <input type="hidden" name="status" id="receivedOrdersStatusInput" value="{{ $status ?? '' }}" />
                    <button type="button" id="receivedOrdersStatusButton" onclick="toggleDropdown('receivedOrdersStatusDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                        <span>{{ !empty($status) ? ucwords($status) : 'All status' }}</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="receivedOrdersStatusDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                        <button type="button" onclick="selectDropdown(event, 'receivedOrdersStatusInput', '', 'receivedOrdersStatusButton', 'All status', 'receivedOrdersStatusDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ empty($status) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All status</button>
                        @foreach(['completed' => 'Completed', 'partially received' => 'Partially Received'] as $value => $label)
                            <button type="button" onclick="selectDropdown(event, 'receivedOrdersStatusInput', '{{ $value }}', 'receivedOrdersStatusButton', '{{ $label }}', 'receivedOrdersStatusDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ ($status ?? '') === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                        @endforeach
                    </div>
                </label>
                <label class="block text-sm text-slate-700 relative" data-dropdown-wrapper="receivedOrdersWarehouse">
                    <span class="text-xs font-semibold text-slate-500">Warehouse</span>
                    <input type="hidden" name="warehouse" id="receivedOrdersWarehouseInput" value="{{ $warehouse ?? '' }}" />
                    <button type="button" id="receivedOrdersWarehouseButton" onclick="toggleDropdown('receivedOrdersWarehouseDropdown')" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35">
                        <span>
                            @php
                                $warehouseLabel = 'All warehouses';
                                if (!empty($warehouse)) {
                                    $warehouseLabel = $warehouse === 'Shop' ? 'Shop (Main Store)' : $warehouse;
                                }
                            @endphp
                            {{ $warehouseLabel }}
                        </span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                    <div id="receivedOrdersWarehouseDropdown" class="dropdown-menu hidden absolute top-full left-0 z-50 mt-2 w-full rounded-[10px] border border-slate-300 bg-white shadow-xl p-3 space-y-1">
                        <button type="button" onclick="selectDropdown(event, 'receivedOrdersWarehouseInput', '', 'receivedOrdersWarehouseButton', 'All warehouses', 'receivedOrdersWarehouseDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ empty($warehouse) ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">All warehouses</button>
                        @foreach(['Shop' => 'Shop (Main Store)', 'Warehouse A' => 'Warehouse A', 'Warehouse B' => 'Warehouse B', 'Warehouse C' => 'Warehouse C'] as $value => $label)
                            <button type="button" onclick="selectDropdown(event, 'receivedOrdersWarehouseInput', '{{ $value }}', 'receivedOrdersWarehouseButton', '{{ $label }}', 'receivedOrdersWarehouseDropdown', 'receivedOrdersForm')" class="w-full px-4 py-2.5 text-center text-sm {{ ($warehouse ?? '') === $value ? 'font-semibold text-slate-900 bg-black/10' : 'text-slate-700 hover:bg-slate-100' }} rounded-[10px]">{{ $label }}</button>
                        @endforeach
                    </div>
                </label>
            </form>

            <div class="mt-6 overflow-hidden rounded-[10px] border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                        <tr>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Received</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                                <td class="px-4 py-3">{{ $order->supplier_name }}</td>
                                <td class="px-4 py-3">{{ optional($order->updated_at)->format('M j, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusClass = match($order->status) {
                                            'pending approval' => 'bg-amber-100 text-amber-800',
                                            'approved' => 'bg-sky-100 text-sky-800',
                                            'sent to supplier' => 'bg-blue-100 text-blue-800',
                                            'in transit' => 'bg-sky-100 text-sky-800',
                                            'partially received' => 'bg-amber-100 text-amber-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'rejected' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                                        {{ ucwords($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No received orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4">
                {{ $orders->links() }}
            </div>
            </div>
        </section>
    </div>

    <script>
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
                    button.style.borderColor = 'rgba(0, 0, 0, 0.35)';
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
    </script>
    <!-- QR Code Generation Modal -->
    <div id="ro-qr-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md px-4 py-6" style="display: none;">
        <div class="w-full max-w-2xl overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Generate QR Codes</h2>
                    <p class="text-sm text-slate-800 font-medium">Enter product names to generate QR codes.</p>
                </div>
                <button onclick="document.getElementById('ro-qr-modal').style.display='none'" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="px-6 py-6 space-y-6">
                <!-- Product Rows -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">New Stock Products</p>
                            <p class="text-xs text-slate-500 mt-1">Enter product names to generate QR codes for multiple items at once.</p>
                        </div>
                        <button type="button" onclick="roAddProductRow()" class="inline-flex items-center justify-center rounded-[10px] border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">+ Add Product</button>
                    </div>
                    <div id="ro-product-rows" class="grid gap-3 max-h-[300px] overflow-y-auto"></div>
                </div>

                <!-- Restock Date Display -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                    <label class="block text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Restock Date (Today)
                    </label>
                    <input type="text" id="ro-qr-restock-date" class="w-full px-4 py-2.5 border border-slate-200 rounded-lg bg-slate-50 text-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 transition" readonly>
                </div>

                <!-- QR Code Preview -->
                <div id="ro-qr-preview" class="space-y-4">
                    <div id="ro-qr-loading" class="hidden text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div>
                        <p class="text-sm text-slate-600">Generating QR codes...</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="ro-qr-pagination" class="hidden flex items-center justify-between pt-4 border-t border-slate-200">
                    <button onclick="roPrevPage()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="ro-prev-page">Previous</button>
                    <span id="ro-page-info" class="text-sm text-slate-600">Page 1 of 1</span>
                    <button onclick="roNextPage()" class="px-4 py-2 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="ro-next-page">Next</button>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button onclick="roGenerateAllQR()" class="flex-1 rounded-[10px] bg-[#00fff2] px-4 py-3 text-sm font-bold text-black hover:bg-[#00e6da] ring-1 ring-slate-300">
                        Generate All QR Codes
                    </button>
                    <button onclick="roPrintAllQR()" class="flex-1 rounded-[10px] bg-[#00fff2] px-4 py-3 text-sm font-bold text-black hover:bg-[#00e6da] ring-1 ring-slate-300">
                        Print All QR Codes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Stock Details Modal -->
    <div id="ro-new-stock-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md px-4 py-6" style="display: none;">
        <div class="w-full max-w-lg overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">New Stock Details</h2>
                    <p class="text-sm text-slate-800 font-medium">Enter product details for warehouse.</p>
                </div>
                <button onclick="roCloseNewStockModal()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="px-6 py-6 space-y-4">
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <p class="text-sm text-slate-600"><strong>Product Name:</strong> <span id="ro-ns-product-name">-</span></p>
                    <p class="text-sm text-slate-600"><strong>SKU:</strong> <span id="ro-ns-sku">-</span></p>
                    <p class="text-sm text-slate-600"><strong>Restock Date:</strong> <span id="ro-ns-restock-date">-</span></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Price</label>
                    <input type="number" id="ro-ns-price" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" placeholder="Enter price" step="0.01">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity</label>
                    <input type="number" id="ro-ns-quantity" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" placeholder="Enter quantity" min="1">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                    <select id="ro-ns-category" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm">
                        <option value="">Select category</option>
                        <option value="Parts">Parts</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Oil">Oil</option>
                        <option value="Tires">Tires</option>
                        <option value="Battery">Battery</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description (Optional)</label>
                    <textarea id="ro-ns-description" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm" rows="2" placeholder="Enter description"></textarea>
                </div>

                <div class="flex gap-3">
                    <button onclick="roCloseNewStockModal()" class="flex-1 rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Cancel</button>
                    <button onclick="roSaveNewStock()" class="flex-1 rounded-[10px] bg-[#00fff2] px-4 py-3 text-sm font-bold text-black hover:bg-[#00e6da] ring-1 ring-slate-300">Save & Add to Warehouse</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Scanner Modal -->
    <div id="ro-scan-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md px-4 py-6" style="display: none;">
        <div class="w-full max-w-[500px] overflow-hidden rounded-[32px] bg-white shadow-[0_40px_120px_rgba(15,23,42,0.18)]">
            <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">QR Code Scanner</h2>
                    <p class="text-sm text-slate-800 font-medium">Scan QR codes to add products.</p>
                </div>
                <button onclick="roCloseScanner()" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="px-6 py-6 flex flex-col">
                <!-- Scanner Section -->
                <div class="flex justify-center">
                    <div id="ro-scanner-reader" class="w-full max-w-[500px] bg-black rounded-xl overflow-hidden min-h-[400px]"></div>
                </div>
                <div id="ro-scanner-status" class="text-center text-sm text-slate-600 mt-2">Position QR code within the frame</div>
                
                <!-- Scanned Items Section -->
                <div id="ro-scanned-items" class="hidden mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-slate-700">Scanned Items (<span id="ro-scan-count">0</span>)</h3>
                        <button onclick="roClearScannedItems()" class="text-xs text-red-500 hover:text-red-700">Clear All</button>
                    </div>
                    <div id="ro-scan-list" class="max-h-48 overflow-y-auto space-y-2"></div>
                    
                    <!-- Pagination -->
                    <div id="ro-scan-pagination" class="hidden flex items-center justify-between mt-3 pt-3 border-t border-slate-200">
                        <button onclick="roScanPrevPage()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="ro-scan-prev-page">Previous</button>
                        <span id="ro-scan-page-info" class="text-xs text-slate-600">Page 1 of 1</span>
                        <button onclick="roScanNextPage()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" id="ro-scan-next-page">Next</button>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="roCloseScanner()" class="rounded-[10px] bg-black/10 px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-black/20">Cancel</button>
                    <button onclick="roProceedToDetails()" class="rounded-[10px] bg-[#00fff2] px-4 py-3 text-sm font-bold text-black hover:bg-[#00e6da] ring-1 ring-slate-300" id="ro-proceed-btn" disabled>Proceed to Details</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const generateQRButton = document.getElementById('ro-generate-qr');
            const scanQRButton = document.getElementById('ro-scan-qr');
            const mobileScannerButton = document.getElementById('ro-mobile-scanner');
            const restockDateInput = document.getElementById('ro-qr-restock-date');

            // Set restock date to today
            const today = new Date().toISOString().split('T')[0];
            if (restockDateInput) {
                restockDateInput.value = today;
            }

            // Initialize with 3 product rows
            if (document.getElementById('ro-product-rows')) {
                for (let i = 0; i < 3; i++) {
                    roAddProductRow();
                }
            }

            if (generateQRButton) {
                generateQRButton.addEventListener('click', function() {
                    document.getElementById('ro-qr-modal').style.display = 'flex';
                });
            }

            if (scanQRButton) {
                scanQRButton.addEventListener('click', function() {
                    document.getElementById('ro-scan-modal').style.display = 'flex';
                    roInitScanner();
                });
            }

            if (mobileScannerButton) {
                mobileScannerButton.addEventListener('click', roOpenMobileScanner);
            }
            
            // Start polling for mobile scanner data
            roStartMobileScannerPolling();

            let roScanner = null;

            function roInitScanner() {
                if (!roScanner) {
                    roScanner = new Html5Qrcode("ro-scanner-reader");
                }
                
                const config = { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                roScanner.start(
                    { facingMode: "environment" },
                    config,
                    roOnScanSuccess,
                    roOnScanFailure
                ).catch(err => {
                    console.error("Received Orders scanner error:", err);
                    document.getElementById('ro-scanner-status').textContent = 'Camera access denied or not available';
                    document.getElementById('ro-scanner-status').classList.add('text-red-400');
                });
            }

            function roCloseScanner() {
                document.getElementById('ro-scan-modal').style.display = 'none';
                if (roScanner) {
                    roScanner.stop().catch(err => console.error(err));
                }
                document.getElementById('ro-scanner-status').textContent = 'Position QR code within the frame';
                document.getElementById('ro-scanner-status').classList.remove('text-red-400');
            }

            function roOnScanSuccess(decodedText, decodedResult) {
                document.getElementById('ro-scanner-status').textContent = 'Scanned: ' + decodedText;
                document.getElementById('ro-scanner-status').classList.add('text-green-400');
                
                setTimeout(() => {
                    document.getElementById('ro-scanner-status').classList.remove('text-green-400');
                    document.getElementById('ro-scanner-status').textContent = 'Position QR code within the frame';
                }, 2000);
                
                roHandleScannedCode(decodedText);
            }

            function roOnScanFailure(error) {
                // Ignore scan failures
            }

            let roGeneratedQRs = [];
            let roCurrentPage = 0;
            const RO_QR_PER_PAGE = 6;
            let roScannedNewStock = null;
            let roScannedItems = [];
            let roScanCurrentPage = 0;
            const RO_SCAN_PER_PAGE = 4;

            async function roHandleScannedCode(code) {
                try {
                    const parsed = JSON.parse(code);
                    const productName = parsed.product_name;
                    const sku = parsed.sku;
                    const restockDate = parsed.restock_date;
                    
                    if (productName && sku && parsed.type === 'new_stock') {
                        const alreadyScanned = roScannedItems.find(item => item.sku === sku);
                        if (alreadyScanned) {
                            alert('This item is already scanned');
                            return;
                        }
                        
                        roScannedItems.push({ productName, sku, restockDate });
                        roUpdateScannedList();
                        alert(`Scanned: ${productName}`);
                    } else {
                        alert('Invalid new stock QR code');
                    }
                } catch (e) {
                    alert('Invalid QR code format');
                }
            }

            function roUpdateScannedList() {
                const scannedItemsDiv = document.getElementById('ro-scanned-items');
                const scanList = document.getElementById('ro-scan-list');
                const scanCount = document.getElementById('ro-scan-count');
                const proceedBtn = document.getElementById('ro-proceed-btn');
                const pagination = document.getElementById('ro-scan-pagination');
                
                if (roScannedItems.length > 0) {
                    scannedItemsDiv.classList.remove('hidden');
                    scanCount.textContent = roScannedItems.length;
                    proceedBtn.disabled = false;
                    
                    if (roScannedItems.length > RO_SCAN_PER_PAGE) {
                        pagination.classList.remove('hidden');
                        pagination.classList.add('flex');
                    } else {
                        pagination.classList.add('hidden');
                        pagination.classList.remove('flex');
                    }
                    
                    const start = roScanCurrentPage * RO_SCAN_PER_PAGE;
                    const end = Math.min(start + RO_SCAN_PER_PAGE, roScannedItems.length);
                    const pageItems = roScannedItems.slice(start, end);
                    
                    scanList.innerHTML = pageItems.map((item, index) => {
                        const actualIndex = start + index;
                        return `
                        <div class="flex items-center justify-between bg-slate-50 rounded-lg p-3 border border-slate-200">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">${item.productName}</p>
                                <p class="text-xs text-slate-600">SKU: ${item.sku}</p>
                                <p class="text-xs text-slate-500">Restock: ${item.restockDate || 'N/A'}</p>
                            </div>
                            <button onclick="roRemoveScannedItem(${actualIndex})" class="text-red-500 hover:text-red-700 p-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    }).join('');
                    
                    const totalPages = Math.ceil(roScannedItems.length / RO_SCAN_PER_PAGE);
                    document.getElementById('ro-scan-page-info').textContent = `Page ${roScanCurrentPage + 1} of ${totalPages}`;
                    document.getElementById('ro-scan-prev-page').disabled = roScanCurrentPage === 0;
                    document.getElementById('ro-scan-next-page').disabled = roScanCurrentPage >= totalPages - 1;
                } else {
                    scannedItemsDiv.classList.add('hidden');
                    proceedBtn.disabled = true;
                    roScanCurrentPage = 0;
                }
            }

            function roScanPrevPage() {
                if (roScanCurrentPage > 0) {
                    roScanCurrentPage--;
                    roUpdateScannedList();
                }
            }

            function roScanNextPage() {
                const totalPages = Math.ceil(roScannedItems.length / RO_SCAN_PER_PAGE);
                if (roScanCurrentPage < totalPages - 1) {
                    roScanCurrentPage++;
                    roUpdateScannedList();
                }
            }

            function roOpenMobileScanner() {
                window.open('{{ route("warehouse.mobile.scanner") }}', 'ReceivedOrdersMobileScanner', 'width=400,height=600');
            }

            function roStartMobileScannerPolling() {
                setInterval(() => {
                    const scannedData = localStorage.getItem('receivedOrdersScannedItems');
                    const timestamp = localStorage.getItem('receivedOrdersScanTimestamp');
                    
                    if (scannedData && timestamp) {
                        const scanTime = parseInt(timestamp);
                        const now = Date.now();
                        
                        if (now - scanTime < 5000) {
                            try {
                                const items = JSON.parse(scannedData);
                                
                                items.forEach(item => {
                                    const alreadyScanned = roScannedItems.find(i => i.sku === item.sku);
                                    if (!alreadyScanned) {
                                        roScannedItems.push({
                                            productName: item.product_name,
                                            sku: item.sku,
                                            restockDate: item.restock_date
                                        });
                                    }
                                });
                                
                                roUpdateScannedList();
                                
                                localStorage.removeItem('receivedOrdersScannedItems');
                                localStorage.removeItem('receivedOrdersScanTimestamp');
                                
                                alert('Received items from mobile scanner');
                            } catch (e) {
                                console.error('Error parsing mobile scanner data:', e);
                            }
                        }
                    }
                }, 1000);
            }

            function roRemoveScannedItem(index) {
                roScannedItems.splice(index, 1);
                const totalPages = Math.ceil(roScannedItems.length / RO_SCAN_PER_PAGE);
                if (roScanCurrentPage >= totalPages && roScanCurrentPage > 0) {
                    roScanCurrentPage = totalPages - 1;
                }
                roUpdateScannedList();
            }

            function roClearScannedItems() {
                roScannedItems = [];
                roScanCurrentPage = 0;
                roUpdateScannedList();
            }

            function roProceedToDetails() {
                if (roScannedItems.length === 0) return;
                
                roCloseScanner();
                
                roScannedNewStock = roScannedItems[0];
                roOpenNewStockModal();
            }

            function roOpenNewStockModal() {
                if (!roScannedNewStock) return;
                
                document.getElementById('ro-ns-product-name').textContent = roScannedNewStock.productName;
                document.getElementById('ro-ns-sku').textContent = roScannedNewStock.sku;
                document.getElementById('ro-ns-restock-date').textContent = roScannedNewStock.restockDate;
                
                document.getElementById('ro-ns-price').value = '';
                document.getElementById('ro-ns-quantity').value = '';
                document.getElementById('ro-ns-category').value = '';
                document.getElementById('ro-ns-description').value = '';
                
                const saveBtn = document.querySelector('#ro-new-stock-modal button[onclick="roSaveNewStock()"]');
                const currentIndex = roScannedItems.findIndex(item => item.sku === roScannedNewStock.sku);
                if (currentIndex < roScannedItems.length - 1) {
                    saveBtn.textContent = `Save & Next (${currentIndex + 1}/${roScannedItems.length})`;
                } else {
                    saveBtn.textContent = 'Save & Add to Warehouse';
                }
                
                document.getElementById('ro-new-stock-modal').style.display = 'flex';
            }

            function roCloseNewStockModal() {
                document.getElementById('ro-new-stock-modal').style.display = 'none';
                roScannedNewStock = null;
            }

            async function roSaveNewStock() {
                if (!roScannedNewStock) return;
                
                const price = parseFloat(document.getElementById('ro-ns-price').value);
                const quantity = parseInt(document.getElementById('ro-ns-quantity').value);
                const category = document.getElementById('ro-ns-category').value;
                const description = document.getElementById('ro-ns-description').value;
                
                if (!price || !quantity || !category) {
                    alert('Please fill in price, quantity, and category');
                    return;
                }
                
                try {
                    const response = await fetch('/stock/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            name: roScannedNewStock.productName,
                            sku: roScannedNewStock.sku,
                            price: price,
                            category: category,
                            description: description,
                            quantity: quantity,
                        }),
                    });
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Failed to create product');
                    }
                    
                    const result = await response.json();
                    const productId = result.id || result.product_id;
                    
                    if (!productId) {
                        throw new Error('No product ID returned');
                    }
                    
                    roScannedNewStock.productId = productId;
                    roScannedNewStock.price = price;
                    roScannedNewStock.quantity = quantity;
                    roScannedNewStock.restockDate = roScannedNewStock.restockDate || restockDateInput.value;
                    
                    const currentIndex = roScannedItems.findIndex(item => item.sku === roScannedNewStock.sku);
                    if (currentIndex < roScannedItems.length - 1) {
                        roScannedNewStock = roScannedItems[currentIndex + 1];
                        roCloseNewStockModal();
                        setTimeout(() => roOpenNewStockModal(), 100);
                        alert('Product saved. Next item...');
                    } else {
                        roCloseNewStockModal();
                        alert('All products created successfully!');
                    }
                    
                } catch (error) {
                    console.error('Error saving new stock:', error);
                    alert(error.message || 'Failed to create product. Please try again.');
                }
            }

            function roAddProductRow() {
                const container = document.getElementById('ro-product-rows');
                if (!container) return;
                
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 bg-slate-50 rounded-lg p-3';
                row.innerHTML = `
                    <input type="text" class="ro-product-name flex-1 rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" placeholder="Enter product name">
                    <div class="ro-sku-display text-xs font-mono font-bold text-slate-700 min-w-[100px]">KCC_</div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                `;
                
                const nameInput = row.querySelector('.ro-product-name');
                const skuDisplay = row.querySelector('.ro-sku-display');
                
                nameInput.addEventListener('input', function() {
                    const name = this.value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
                    skuDisplay.textContent = name ? `KCC_${name}` : 'KCC_';
                });
                
                container.appendChild(row);
            }

            function roGenerateAllQR() {
                const productRows = document.querySelectorAll('#ro-product-rows > div');
                const restockDate = document.getElementById('ro-qr-restock-date').value;
                const previewContainer = document.getElementById('ro-qr-preview');
                
                const products = [];
                productRows.forEach(row => {
                    const nameInput = row.querySelector('.ro-product-name');
                    const name = nameInput.value.trim();
                    if (name) {
                        const sku = `KCC_${name.toUpperCase().replace(/[^A-Z0-9]/g, '')}`;
                        products.push({ name, sku });
                    }
                });
                
                if (products.length === 0) {
                    alert('Please enter at least one product name');
                    return;
                }
                
                previewContainer.innerHTML = '<div id="ro-qr-loading" class="text-center py-12"><div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600 mb-4"></div><p class="text-sm text-slate-600">Generating QR codes...</p></div>';
                
                setTimeout(() => {
                    previewContainer.innerHTML = '';
                    roGeneratedQRs = [];
                    roCurrentPage = 0;
                    
                    products.forEach((product, index) => {
                        const qrData = JSON.stringify({
                            product_name: product.name,
                            sku: product.sku,
                            restock_date: restockDate,
                            type: 'new_stock'
                        });
                        
                        roGeneratedQRs.push({
                            product,
                            qrData,
                            index
                        });
                    });
                    
                    roRenderQRPage();
                }, 500);
            }

            function roRenderQRPage() {
                const previewContainer = document.getElementById('ro-qr-preview');
                previewContainer.innerHTML = '';
                
                const start = roCurrentPage * RO_QR_PER_PAGE;
                const end = Math.min(start + RO_QR_PER_PAGE, roGeneratedQRs.length);
                const pageItems = roGeneratedQRs.slice(start, end);
                
                pageItems.forEach((item) => {
                    const { product, qrData, index } = item;
                    
                    const qrCard = document.createElement('div');
                    qrCard.className = 'border border-slate-200 rounded-lg p-4 bg-white';
                    qrCard.innerHTML = `
                        <div class="flex items-start gap-4">
                            <div id="ro-qr-code-${index}" class="w-24 h-24 flex items-center justify-center bg-white"></div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900 text-sm">${product.name}</h3>
                                <p class="text-xs text-slate-600">SKU: ${product.sku}</p>
                                <p class="text-xs text-slate-600">Restock: ${document.getElementById('ro-qr-restock-date').value}</p>
                                <p class="text-xs text-slate-500 mt-1">Scan to add new stock to system</p>
                            </div>
                        </div>
                    `;
                    
                    previewContainer.appendChild(qrCard);
                    
                    try {
                        const qrElement = document.getElementById(`ro-qr-code-${index}`);
                        new QRCode(qrElement, {
                            text: qrData,
                            width: 96,
                            height: 96,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } catch (error) {
                        console.error('Error generating QR code:', error);
                    }
                });
                
                const totalPages = Math.ceil(roGeneratedQRs.length / RO_QR_PER_PAGE);
                document.getElementById('ro-page-info').textContent = `Page ${roCurrentPage + 1} of ${totalPages}`;
                document.getElementById('ro-prev-page').disabled = roCurrentPage === 0;
                document.getElementById('ro-next-page').disabled = roCurrentPage >= totalPages - 1;
            }

            function roPrevPage() {
                if (roCurrentPage > 0) {
                    roCurrentPage--;
                    roRenderQRPage();
                }
            }

            function roNextPage() {
                const totalPages = Math.ceil(roGeneratedQRs.length / RO_QR_PER_PAGE);
                if (roCurrentPage < totalPages - 1) {
                    roCurrentPage++;
                    roRenderQRPage();
                }
            }

            function roPrintAllQR() {
                if (roGeneratedQRs.length === 0) {
                    alert('Please generate QR codes first');
                    return;
                }
                
                try {
                    const printWindow = window.open('', '_blank');
                    
                    if (!printWindow) {
                        alert('Popup blocked. Please allow popups.');
                        return;
                    }
                    
                    let htmlContent = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>New Stock QR Codes</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    padding: 20px;
                                }
                                .qr-grid {
                                    display: grid;
                                    grid-template-columns: repeat(2, 1fr);
                                    gap: 20px;
                                    margin-top: 20px;
                                }
                                .qr-card {
                                    border: 1px solid #ccc;
                                    padding: 15px;
                                    border-radius: 8px;
                                    text-align: center;
                                    page-break-inside: avoid;
                                }
                                .qr-card img {
                                    max-width: 150px;
                                    border: 1px solid #eee;
                                    padding: 10px;
                                }
                                .qr-card .info {
                                    margin: 10px 0;
                                    font-size: 12px;
                                }
                                @media print {
                                    .qr-grid {
                                        grid-template-columns: repeat(3, 1fr);
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <h2>New Stock QR Codes</h2>
                            <p>Restock Date: ${document.getElementById('ro-qr-restock-date').value}</p>
                            <div class="qr-grid">
                    `;
                    
                    roGeneratedQRs.forEach((item, index) => {
                        const { product, qrData } = item;
                        
                        const tempDiv = document.createElement('div');
                        tempDiv.style.display = 'none';
                        document.body.appendChild(tempDiv);
                        
                        const tempQrElement = document.createElement('div');
                        tempDiv.appendChild(tempQrElement);
                        
                        new QRCode(tempQrElement, {
                            text: qrData,
                            width: 150,
                            height: 150,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        
                        setTimeout(() => {
                            const canvas = tempQrElement.querySelector('canvas');
                            if (canvas) {
                                const dataUrl = canvas.toDataURL('image/png');
                                
                                htmlContent += `
                                    <div class="qr-card">
                                        <div class="info">
                                            <p><strong>${product.name}</strong></p>
                                            <p>${product.sku}</p>
                                            <p>Restock: ${document.getElementById('ro-qr-restock-date').value}</p>
                                        </div>
                                        <img src="${dataUrl}" alt="QR Code">
                                    </div>
                                `;
                            }
                            
                            document.body.removeChild(tempDiv);
                        }, 100);
                    });
                    
                    setTimeout(() => {
                        htmlContent += `
                            </div>
                            <p style="margin-top: 20px; font-size: 12px;">Scan each QR code to add new stock to the system</p>
                        </body>
                        </html>
                        `;
                        
                        printWindow.document.write(htmlContent);
                        printWindow.document.close();
                        setTimeout(() => {
                            printWindow.print();
                        }, 500);
                    }, roGeneratedQRs.length * 150);
                    
                } catch (error) {
                    console.error('Error printing:', error);
                    alert('Error opening print dialog');
                }
            }

            // Expose functions globally
            window.roAddProductRow = roAddProductRow;
            window.roGenerateAllQR = roGenerateAllQR;
            window.roPrintAllQR = roPrintAllQR;
            window.roPrevPage = roPrevPage;
            window.roNextPage = roNextPage;
            window.roCloseScanner = roCloseScanner;
            window.roCloseNewStockModal = roCloseNewStockModal;
            window.roSaveNewStock = roSaveNewStock;
            window.roUpdateScannedList = roUpdateScannedList;
            window.roRemoveScannedItem = roRemoveScannedItem;
            window.roClearScannedItems = roClearScannedItems;
            window.roProceedToDetails = roProceedToDetails;
            window.roScanPrevPage = roScanPrevPage;
            window.roScanNextPage = roScanNextPage;
        });
    </script>
</x-layouts.app>
