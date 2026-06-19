<x-layouts.app :title="__('Order Management')">
    <div class="space-y-5">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <strong class="block font-semibold">Please fix the following:</strong>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Order Management</h1>
                <p class="max-w-2xl text-sm text-slate-500">Create and manage purchase orders directly for restocking new products.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button id="createOrderButton" type="button" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Create Purchase Order</button>
                <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">Upload CSV</button>
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
                <p class="mt-3 text-3xl font-semibold text-slate-900">₱{{ number_format($inTransitTotal, 2) }}</p>
                <p class="mt-2 text-sm text-slate-500">Total value of orders currently in transit.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Received {{ $receivedLabel }}</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($receivedCount) }}</p>
                        <p class="mt-2 text-sm text-slate-500">Completed orders added to inventory.</p>
                    </div>
                    <form method="GET" action="{{ route('order.management') }}" class="w-full sm:w-auto">
                        <label class="sr-only">Received range</label>
                        <select name="received_range" onchange="this.form.submit()" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option value="daily" {{ $receivedRange === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $receivedRange === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $receivedRange === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ $receivedRange === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div id="orderTabs" class="flex items-center gap-3 border-b border-slate-200 pb-4">
                <button class="tab-btn active rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="orders">Purchase Orders</button>
                <button class="tab-btn rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100" data-tab="received">Received</button>
            </div>

            <!-- Orders Tab -->
            <div id="orders-tab" class="tab-content">
                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search orders</span>
                        <input type="search" placeholder="Receipt no. or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Status</span>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option>All statuses</option>
                            <option>Pending</option>
                            <option>In transit</option>
                            <option>Delivered</option>
                            <option>Cancelled</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option>All suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="mt-6 overflow-hidden rounded-[26px] border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                            <tr>
                                <th class="px-4 py-3">Order</th>
                                <th class="px-4 py-3">Supplier</th>
                                <th class="px-4 py-3">ETA</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-700">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3">{{ $order->supplier_name }}</td>
                                    <td class="px-4 py-3">{{ optional($order->expected_delivery_date)->format('M j') ?? 'TBD' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($order->status === 'in transit' ? 'bg-sky-100 text-sky-800' : ($order->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800')) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No purchase orders have been created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Purchase Order Modal -->
            <div id="orderModal" class="fixed inset-0 z-50 hidden flex items-center justify-center overflow-y-auto bg-slate-900/40 px-4 py-10">
                <div class="w-full max-w-3xl max-h-[80vh] overflow-y-auto rounded-[28px] bg-white p-6 shadow-2xl">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-slate-900">Create Purchase Order</h2>
                            <p class="mt-1 text-sm text-slate-500">Choose supplier and select low-stock products to restock.</p>
                        </div>
                        <button id="closeModal" type="button" class="rounded-full bg-slate-100 p-3 text-slate-600 hover:bg-slate-200">Close</button>
                    </div>

                    <form action="{{ route('order.store') }}" method="POST" class="mt-6 space-y-6">
                        @csrf
                        <div class="grid gap-4 lg:grid-cols-2">
                            <label class="block text-sm text-slate-700">
                                <span class="text-xs font-semibold text-slate-500">Supplier</span>
                                <select name="supplier_id" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                                    <option value="">Select supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block text-sm text-slate-700">
                                <span class="text-xs font-semibold text-slate-500">Expected delivery date</span>
                                <input name="expected_delivery_date" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                            </label>
                        </div>

                        <label class="block text-sm text-slate-700">
                            <span class="text-xs font-semibold text-slate-500">Notes</span>
                            <textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none"></textarea>
                        </label>

                        <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-4">
                            <h3 class="text-sm font-semibold text-slate-700">Low stock products</h3>
                            <p class="mt-1 text-sm text-slate-500">Select the items to include in the order.</p>

                            <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                                <table class="min-w-full text-left text-sm">
                                    <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                                        <tr>
                                            <th class="px-4 py-3">Select</th>
                                            <th class="px-4 py-3">Product</th>
                                            <th class="px-4 py-3">Supplier</th>
                                            <th class="px-4 py-3">SKU</th>
                                            <th class="px-4 py-3">Stock</th>
                                            <th class="px-4 py-3">Reorder</th>
                                            <th class="px-4 py-3">Qty to order</th>
                                            <th class="px-4 py-3">Unit price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 text-slate-700">
                                        @foreach($lowStockProducts as $product)
                                            <tr class="hover:bg-white" data-supplier-name="{{ strtolower($product->supplier_name) }}">
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" name="products[{{ $loop->index }}][selected]" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                                    <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $product->id }}" />
                                                    <input type="hidden" name="products[{{ $loop->index }}][product_name]" value="{{ $product->product_name ?? $product->name }}" />
                                                    <input type="hidden" name="products[{{ $loop->index }}][sku]" value="{{ $product->sku }}" />
                                                </td>
                                                <td class="px-4 py-3">{{ $product->product_name ?? $product->name }}</td>
                                                <td class="px-4 py-3">{{ $product->supplier_name }}</td>
                                                <td class="px-4 py-3">{{ $product->sku }}</td>
                                                <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                                                <td class="px-4 py-3">{{ $product->reorder_level }}</td>
                                                <td class="px-4 py-3">
                                                    <input name="products[{{ $loop->index }}][quantity]" type="number" min="1" value="{{ max(1, $product->reorder_level - $product->stock_quantity) }}" class="w-20 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input name="products[{{ $loop->index }}][unit_price]" type="number" step="0.01" min="0" value="{{ $product->unit_price }}" class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 px-4">
                                {{ $lowStockProducts->links() }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <button type="button" id="cancelOrder" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Submit order</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Received Tab -->
            <div id="received-tab" class="tab-content hidden">
                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Search deliveries</span>
                        <input type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Receipt status</span>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option>All statuses</option>
                            <option>Confirmed</option>
                            <option>Pending</option>
                            <option>Issue</option>
                        </select>
                    </label>
                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Warehouse</span>
                        <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none">
                            <option>All warehouses</option>
                            <option>Main stock</option>
                            <option>Service bay</option>
                        </select>
                    </label>
                </div>

                <div class="mt-6 overflow-hidden rounded-[26px] border border-slate-200">
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
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold">RO-3309</td>
                                <td class="px-4 py-3">PhilMoto</td>
                                <td class="px-4 py-3">Jun 17</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">Confirmed</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold">RO-3305</td>
                                <td class="px-4 py-3">Supreme Parts</td>
                                <td class="px-4 py-3">Jun 16</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">Pending</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold">RO-3301</td>
                                <td class="px-4 py-3">Team Ride</td>
                                <td class="px-4 py-3">Jun 15</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-semibold text-rose-800">Issue</span></td>
                            </tr>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold">RO-3298</td>
                                <td class="px-4 py-3">PhilMoto</td>
                                <td class="px-4 py-3">Jun 14</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">Confirmed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active', 'bg-emerald-100', 'text-emerald-700'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));

                button.classList.add('active', 'bg-emerald-100', 'text-emerald-700');
                document.getElementById(tabName + '-tab').classList.remove('hidden');
            });
        });

        const orderModal = document.getElementById('orderModal');
        const createOrderButton = document.getElementById('createOrderButton');
        const closeModal = document.getElementById('closeModal');
        const cancelOrder = document.getElementById('cancelOrder');
        const supplierSelect = document.querySelector('select[name="supplier_id"]');
        const productRows = document.querySelectorAll('#orderModal table tbody tr');
        const supplierNames = @json($suppliers->pluck('name', 'id'));

        const openModal = () => orderModal.classList.remove('hidden');
        const closeModalAction = () => orderModal.classList.add('hidden');

        const updateProductVisibility = () => {
            if (!supplierSelect) {
                return;
            }
            const selectedSupplierId = supplierSelect.value;
            const selectedSupplierName = selectedSupplierId ? supplierNames[selectedSupplierId] || '' : '';

            productRows.forEach(row => {
                const rowSupplier = row.dataset.supplierName || '';
                if (!selectedSupplierName || rowSupplier.toLowerCase() === selectedSupplierName.toLowerCase()) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                    const checkbox = row.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            });
        };

        createOrderButton.addEventListener('click', () => {
            openModal();
            updateProductVisibility();
        });
        closeModal.addEventListener('click', closeModalAction);
        cancelOrder.addEventListener('click', closeModalAction);

        if (supplierSelect) {
            supplierSelect.addEventListener('change', updateProductVisibility);
        }

        if (@json($errors->any())) {
            openModal();
        }
    </script>
</x-layouts.app>
