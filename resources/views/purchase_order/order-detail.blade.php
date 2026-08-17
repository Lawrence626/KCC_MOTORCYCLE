<x-layouts.app :title="__('Purchase Order')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Purchase Order {{ $purchaseOrder->order_number }}</h1>
                <p class="max-w-2xl text-sm text-slate-500">Review full purchase order details and manage the lifecycle.</p>
            </div>
            <a href="{{ route('order.management') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900">Back to Orders</a>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">{{ session('warning') }}</div>
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

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Supplier</p>
                <p class="mt-3 text-xl font-semibold text-slate-900">{{ $purchaseOrder->supplier_name }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ optional($purchaseOrder->supplier)->email ?? 'No supplier email on file' }}</p>
                <p class="text-sm text-slate-500">{{ optional($purchaseOrder->supplier)->phone ?? 'No phone available' }}</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Order details</p>
                <div class="mt-3 space-y-2 text-sm text-slate-700">
                    <p><span class="font-semibold">Status:</span> {{ ucwords($purchaseOrder->status) }}</p>
                    <p><span class="font-semibold">Created:</span> {{ $purchaseOrder->created_at->format('M j, Y') }}</p>
                    <p><span class="font-semibold">ETA:</span> {{ optional($purchaseOrder->estimated_delivery_date ?? $purchaseOrder->expected_delivery_date)->format('M j, Y') ?? 'TBD' }}</p>
                    <p><span class="font-semibold">Order total:</span> ₱{{ number_format($purchaseOrder->total_amount, 2) }}</p>
                </div>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Timeline</p>
                <div class="mt-3 space-y-2 text-sm text-slate-700">
                    @if($purchaseOrder->approved_at)
                        <p><span class="font-semibold">Approved:</span> {{ $purchaseOrder->approved_at->format('M j, Y H:i') }}</p>
                    @endif
                    @if($purchaseOrder->sent_to_supplier_at)
                        <p><span class="font-semibold">Sent:</span> {{ $purchaseOrder->sent_to_supplier_at->format('M j, Y H:i') }}</p>
                    @endif
                    @if($purchaseOrder->in_transit_at)
                        <p><span class="font-semibold">In transit:</span> {{ $purchaseOrder->in_transit_at->format('M j, Y H:i') }}</p>
                    @endif
                    @if($purchaseOrder->completed_at)
                        <p><span class="font-semibold">Completed:</span> {{ $purchaseOrder->completed_at->format('M j, Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estimated Delivery Date Card --}}
        @php
            $estDate = $purchaseOrder->estimated_delivery_date;
            $isCompleted = in_array($purchaseOrder->status, ['completed', 'archived']);
            $daysRemaining = $estDate ? (int) now()->startOfDay()->diffInDays($estDate->startOfDay(), false) : null;

            if ($estDate === null) {
                $estColor = 'slate';
                $estBg = 'bg-slate-50 border-slate-200';
                $estBadgeBg = 'bg-slate-100 text-slate-600';
                $estLabel = 'Not yet provided';
                $estIcon = '⏳';
            } elseif ($isCompleted) {
                $estColor = 'emerald';
                $estBg = 'bg-emerald-50 border-emerald-200';
                $estBadgeBg = 'bg-emerald-100 text-emerald-700';
                $estLabel = 'Delivered';
                $estIcon = '✅';
            } elseif ($daysRemaining < 0) {
                $estColor = 'rose';
                $estBg = 'bg-rose-50 border-rose-200';
                $estBadgeBg = 'bg-rose-100 text-rose-700';
                $estLabel = abs($daysRemaining) . ' ' . Str::plural('day', abs($daysRemaining)) . ' overdue';
                $estIcon = '🔴';
            } elseif ($daysRemaining <= 2) {
                $estColor = 'amber';
                $estBg = 'bg-amber-50 border-amber-200';
                $estBadgeBg = 'bg-amber-100 text-amber-700';
                $estLabel = $daysRemaining === 0 ? 'Due today' : 'Arriving in ' . $daysRemaining . ' ' . Str::plural('day', $daysRemaining);
                $estIcon = '🟡';
            } else {
                $estColor = 'emerald';
                $estBg = 'bg-emerald-50 border-emerald-200';
                $estBadgeBg = 'bg-emerald-100 text-emerald-700';
                $estLabel = 'Arriving in ' . $daysRemaining . ' ' . Str::plural('day', $daysRemaining);
                $estIcon = '🟢';
            }
        @endphp
        <div class="rounded-[26px] border {{ $estBg }} p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Estimated Delivery Date</p>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $estIcon }}</span>
                        <div>
                            <p class="text-xl font-semibold text-slate-900">
                                {{ $estDate ? $estDate->format('M j, Y') : 'Not yet provided' }}
                            </p>
                            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $estBadgeBg }}">
                                {{ $estLabel }}
                            </span>
                        </div>
                    </div>
                </div>
                @if(auth()->user() && auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('order.update_estimated_delivery', $purchaseOrder) }}" class="flex items-end gap-2">
                        @csrf
                        @method('PUT')
                        <label class="block text-sm">
                            <span class="text-xs font-semibold text-slate-500">{{ $estDate ? 'Update date' : 'Set date' }}</span>
                            <input type="date" name="estimated_delivery_date"
                                   value="{{ $estDate ? $estDate->format('Y-m-d') : '' }}"
                                   required
                                   class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100" />
                        </label>
                        <button type="submit" class="rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">
                            {{ $estDate ? 'Update' : 'Save' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Items</h2>
                    <p class="text-sm text-slate-500">Verify quantities and received inventory.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($purchaseOrder->status === 'pending approval')
                        <form method="POST" action="{{ route('order.approve', $purchaseOrder) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('order.reject', $purchaseOrder) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl border border-rose-200 bg-white px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-50">Reject</button>
                        </form>
                    @elseif($purchaseOrder->status === 'approved')
                        <form method="POST" action="{{ route('order.send', $purchaseOrder) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-cyan-600 px-4 py-3 text-sm font-semibold text-white hover:bg-cyan-700">Send to Supplier</button>
                        </form>
                    @elseif($purchaseOrder->status === 'sent to supplier')
                        <form method="POST" action="{{ route('order.in_transit', $purchaseOrder) }}">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white hover:bg-sky-700">Mark In Transit</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-700 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Product</th>
                            <th class="px-4 py-3 text-left font-semibold">SKU</th>
                            <th class="px-4 py-3 text-left font-semibold">Qty ordered</th>
                            <th class="px-4 py-3 text-left font-semibold">Received</th>
                            <th class="px-4 py-3 text-left font-semibold">Unit price</th>
                            <th class="px-4 py-3 text-left font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @foreach($purchaseOrder->items as $item)
                            <tr class="hover:bg-white">
                                <td class="px-4 py-3">{{ $item->product_name }}</td>
                                <td class="px-4 py-3">{{ $item->sku }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3">{{ $item->received_quantity ?? 0 }}</td>
                                <td class="px-4 py-3">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3">₱{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(in_array($purchaseOrder->status, ['in transit', 'partially received'], true))
                <form method="POST" action="{{ route('order.receive', $purchaseOrder) }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-sm font-semibold text-slate-700">Receive Order</h3>
                        <p class="mt-1 text-sm text-slate-500">Confirm received quantities (inventory will NOT be updated yet).</p>

                        <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-slate-100 text-slate-700 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">Product</th>
                                        <th class="px-4 py-3 text-left font-semibold">Ordered</th>
                                        <th class="px-4 py-3 text-left font-semibold">Received</th>
                                        <th class="px-4 py-3 text-left font-semibold">Remaining</th>
                                        <th class="px-4 py-3 text-left font-semibold">Supplier Cost/Unit</th>
                                        <th class="px-4 py-3 text-left font-semibold">Receive quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-700">
                                    @foreach($purchaseOrder->items as $item)
                                        @php
                                            $remainingQuantity = max(0, $item->quantity - ($item->received_quantity ?? 0));
                                        @endphp
                                        <tr class="hover:bg-white">
                                            <td class="px-4 py-3">{{ $item->product_name }}</td>
                                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                                            <td class="px-4 py-3">{{ $item->received_quantity ?? 0 }}</td>
                                            <td class="px-4 py-3">{{ $remainingQuantity }}</td>
                                            <td class="px-4 py-3">
                                                <input name="items[{{ $item->id }}][unit_price]" type="number" step="0.01" min="0" value="{{ (float) $item->unit_price }}" class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" placeholder="0.00" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input name="items[{{ $item->id }}][item_id]" type="hidden" value="{{ $item->id }}" />
                                                <input name="items[{{ $item->id }}][received_quantity]" type="number" min="0" max="{{ $remainingQuantity }}" value="{{ $remainingQuantity }}" class="w-24 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Record Receipt</button>
                    </div>
                </form>
            @endif

            @if(in_array($purchaseOrder->status, ['awaiting confirmation', 'partially received'], true))
                <form method="POST" action="{{ route('order.confirm_receive', $purchaseOrder) }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="rounded-[26px] border border-emerald-200 bg-emerald-50 p-4">
                        <h3 class="text-sm font-semibold text-emerald-700">Confirm & Add to Inventory</h3>
                        <p class="mt-1 text-sm text-emerald-600">Select warehouse and shelf location, then confirm to update inventory.</p>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Warehouse</label>
                                <select name="warehouse" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none">
                                    <option value="Shop">Shop (Main Store)</option>
                                    <option value="Warehouse A">Warehouse A</option>
                                    <option value="Warehouse B">Warehouse B</option>
                                    <option value="Warehouse C">Warehouse C</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Shelf (Optional)</label>
                                <select name="shelf_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none">
                                    <option value="">No specific shelf</option>
                                    @php
                                        $warehouseName = 'Warehouse A'; // Default to Warehouse A for shelf options
                                        $shelves = \App\Models\WarehouseShelf::whereHas('warehouse', function($q) use ($warehouseName) {
                                            $q->where('name', $warehouseName);
                                        })->where('archived', false)->get();
                                    @endphp
                                    @foreach($shelves as $shelf)
                                        <option value="{{ $shelf->id }}">{{ $shelf->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Confirm & Add to Inventory</button>
                    </div>
                </form>
            @endif
        </div>

        @if($purchaseOrder->notes)
            <div class="rounded-[26px] border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Notes</h2>
                <p class="mt-3 text-sm text-slate-700">{{ $purchaseOrder->notes }}</p>
            </div>
        @endif
    </div>
</x-layouts.app>
