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
                    @php
                        $deliveryDate = $purchaseOrder->estimated_delivery_date ?? $purchaseOrder->expected_delivery_date;
                    @endphp
                    <p><span class="font-semibold">ETA:</span> {{ $deliveryDate ? $deliveryDate->format('M j, Y') : 'Not yet provided' }}</p>
                    @php
                        $calculatedTotal = $purchaseOrder->items->sum(function($item) use ($purchaseOrder) {
                            $unitPrice = (float) $item->unit_price;
                            if ($unitPrice <= 0 && $item->product) {
                                $supplierCost = \App\Models\SupplierPriceHistory::where('supplier_id', $purchaseOrder->supplier_id)
                                    ->where('product_id', $item->product_id)
                                    ->latest('id')
                                    ->value('supplier_cost');
                                $unitPrice = (float) ($supplierCost ?? $item->product->unit_price ?? 0);
                            }
                            return (float) $item->quantity * $unitPrice;
                        });
                        $orderTotal = (float) $purchaseOrder->total_amount > 0 ? (float) $purchaseOrder->total_amount : $calculatedTotal;
                    @endphp
                    <p><span class="font-semibold">Order total:</span> ₱{{ number_format($orderTotal, 2) }}</p>
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
            $estDate = $purchaseOrder->estimated_delivery_date ?? $purchaseOrder->expected_delivery_date;
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

            $isReceivingStage = in_array($purchaseOrder->status, ['in transit', 'partially received', 'awaiting confirmation', 'completed', 'delivered'], true);
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
                    <p class="text-sm text-slate-500">
                        {{ $isReceivingStage ? 'Verify quantities and received inventory.' : 'Verify items, ordered quantities, and supplier pricing.' }}
                    </p>
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
                            @if($isReceivingStage)
                                <th class="px-4 py-3 text-left font-semibold">Received</th>
                                <th class="px-4 py-3 text-left font-semibold">Defective</th>
                                <th class="px-4 py-3 text-left font-semibold">Accepted</th>
                            @endif
                            <th class="px-4 py-3 text-left font-semibold">Supplier Cost/Unit</th>
                            <th class="px-4 py-3 text-left font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @foreach($purchaseOrder->items as $item)
                            @php
                                $unitPrice = (float) $item->unit_price;
                                if ($unitPrice <= 0 && $item->product) {
                                    $supplierCost = \App\Models\SupplierPriceHistory::where('supplier_id', $purchaseOrder->supplier_id)
                                        ->where('product_id', $item->product_id)
                                        ->latest('id')
                                        ->value('supplier_cost');
                                    $unitPrice = (float) ($supplierCost ?? $item->product->unit_price ?? 0);
                                }
                                $totalPrice = (float) $item->total_price;
                                if ($totalPrice <= 0 && $unitPrice > 0) {
                                    $totalPrice = (float) $item->quantity * $unitPrice;
                                }
                            @endphp
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="po-detail-img-thumb w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300"
                                             data-id="{{ $item->product_id ?? '' }}"
                                             data-sku="{{ $item->sku ?? ($item->product?->sku ?? '') }}"
                                             data-name="{{ $item->product_name ?? '' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-slate-900 truncate">{{ $item->product_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $item->sku }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $item->quantity }}</td>
                                @if($isReceivingStage)
                                    <td class="px-4 py-3">{{ $item->received_quantity ?? 0 }}</td>
                                    <td class="px-4 py-3">
                                        @if(($item->defective_quantity ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 border border-rose-200" title="{{ $item->defect_reason ?? 'Defective' }}">
                                                {{ $item->defective_quantity }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">0</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(($item->accepted_quantity ?? 0) > 0 || ($item->received_quantity ?? 0) > 0)
                                            <span class="font-semibold text-emerald-700">{{ $item->accepted_quantity ?? max(0, ($item->received_quantity ?? 0) - ($item->defective_quantity ?? 0)) }}</span>
                                        @else
                                            <span class="text-slate-400">0</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="px-4 py-3">₱{{ number_format($unitPrice, 2) }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">₱{{ number_format($totalPrice, 2) }}</td>
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
                                        <th class="px-4 py-3 text-left font-semibold">Receive Quantity</th>
                                        <th class="px-4 py-3 text-left font-semibold">Defective Qty</th>
                                        <th class="px-4 py-3 text-left font-semibold">Accepted Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-slate-700">
                                    @foreach($purchaseOrder->items as $item)
                                        @php
                                            $remainingQuantity = max(0, $item->quantity - ($item->received_quantity ?? 0));
                                            $receiveUnitPrice = (float) $item->unit_price;
                                            if ($receiveUnitPrice <= 0 && $item->product) {
                                                $supplierCost = \App\Models\SupplierPriceHistory::where('supplier_id', $purchaseOrder->supplier_id)
                                                    ->where('product_id', $item->product_id)
                                                    ->latest('id')
                                                    ->value('supplier_cost');
                                                $receiveUnitPrice = (float) ($supplierCost ?? $item->product->unit_price ?? 0);
                                            }
                                        @endphp
                                        <tr class="receive-item-row hover:bg-white" data-item-id="{{ $item->id }}" data-ordered="{{ $item->quantity }}" data-already-received="{{ $item->received_quantity ?? 0 }}">
                                            <td class="px-4 py-3 font-medium text-slate-900">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="po-detail-img-thumb w-8 h-8 rounded-[6px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-300"
                                                         data-id="{{ $item->product_id ?? '' }}"
                                                         data-sku="{{ $item->sku ?? ($item->product?->sku ?? '') }}"
                                                         data-name="{{ $item->product_name ?? '' }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-medium text-slate-900 truncate">{{ $item->product_name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                                            <td class="px-4 py-3">{{ $item->received_quantity ?? 0 }}</td>
                                            <td class="px-4 py-3 font-semibold text-slate-700">
                                                <span id="remaining-{{ $item->id }}">{{ $remainingQuantity }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input name="items[{{ $item->id }}][unit_price]" type="number" step="0.01" min="0" value="{{ $receiveUnitPrice }}" class="w-28 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" placeholder="0.00" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input name="items[{{ $item->id }}][item_id]" type="hidden" value="{{ $item->id }}" />
                                                <input name="items[{{ $item->id }}][received_quantity]" type="number" min="0" max="{{ $remainingQuantity }}" value="{{ $remainingQuantity }}" class="receive-qty-input w-24 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-slate-400" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input name="items[{{ $item->id }}][defective_quantity]" type="number" min="0" max="{{ $remainingQuantity }}" value="0" class="defective-qty-input w-24 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-rose-300" />
                                            </td>
                                            <td class="px-4 py-3">
                                                <input name="items[{{ $item->id }}][accepted_quantity]" type="number" readonly value="{{ $remainingQuantity }}" class="accepted-qty-input w-24 rounded-2xl border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-sm font-semibold text-emerald-700 outline-none cursor-not-allowed" />
                                            </td>
                                        </tr>
                                        <tr id="defect-row-{{ $item->id }}" class="defect-reason-row hidden bg-rose-50/40">
                                            <td colspan="8" class="px-4 py-2.5">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs font-semibold text-rose-800 whitespace-nowrap">
                                                        Defect Reason / Remarks <span class="text-rose-500">*</span>:
                                                    </span>
                                                    <input name="items[{{ $item->id }}][defect_reason]" id="defect-reason-{{ $item->id }}" type="text" placeholder="Specify defect reason (e.g., Damaged casing, Broken seal, Manufacturing defect)..." class="defect-reason-input flex-1 rounded-xl border border-rose-200 bg-white px-3 py-1.5 text-xs text-slate-900 outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400" />
                                                </div>
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

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const rows = document.querySelectorAll('.receive-item-row');

                        rows.forEach(row => {
                            const itemId = row.dataset.itemId;
                            const ordered = parseInt(row.dataset.ordered || '0', 10);
                            const alreadyReceived = parseInt(row.dataset.alreadyReceived || '0', 10);
                            const receiveInput = row.querySelector('.receive-qty-input');
                            const defectiveInput = row.querySelector('.defective-qty-input');
                            const acceptedInput = row.querySelector('.accepted-qty-input');
                            const defectRow = document.getElementById(`defect-row-${itemId}`);
                            const defectReasonInput = document.getElementById(`defect-reason-${itemId}`);

                            function recalculate() {
                                const maxAvailable = Math.max(0, ordered - alreadyReceived);
                                let receiveQty = parseInt(receiveInput.value || '0', 10);
                                if (isNaN(receiveQty) || receiveQty < 0) {
                                    receiveQty = 0;
                                    receiveInput.value = 0;
                                }
                                if (receiveQty > maxAvailable) {
                                    receiveQty = maxAvailable;
                                    receiveInput.value = maxAvailable;
                                }

                                // Defective quantity must be 0 or greater and cannot exceed receive quantity
                                defectiveInput.max = receiveQty;
                                let defectiveQty = parseInt(defectiveInput.value || '0', 10);
                                if (isNaN(defectiveQty) || defectiveQty < 0) {
                                    defectiveQty = 0;
                                    defectiveInput.value = 0;
                                }
                                if (defectiveQty > receiveQty) {
                                    defectiveQty = receiveQty;
                                    defectiveInput.value = receiveQty;
                                }

                                // Accepted Qty is automatically calculated as Receive Qty - Defective Qty
                                const acceptedQty = Math.max(0, receiveQty - defectiveQty);
                                if (acceptedInput) {
                                    acceptedInput.value = acceptedQty;
                                }

                                // If Defective Qty > 0, show and require defect reason
                                if (defectiveQty > 0) {
                                    if (defectRow) defectRow.classList.remove('hidden');
                                    if (defectReasonInput) {
                                        defectReasonInput.required = true;
                                    }
                                } else {
                                    if (defectRow) defectRow.classList.add('hidden');
                                    if (defectReasonInput) {
                                        defectReasonInput.required = false;
                                    }
                                }
                            }

                            if (receiveInput) {
                                receiveInput.addEventListener('input', recalculate);
                            }
                            if (defectiveInput) {
                                defectiveInput.addEventListener('input', recalculate);
                            }

                            // Run initial recalculation
                            recalculate();
                        });
                    });
                </script>
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

        {{-- Defective Products & Supplier Resolution Section --}}
        @if($purchaseOrder->defectiveReturnRequests->isNotEmpty())
            <div class="rounded-[26px] border border-rose-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <h2 class="text-lg font-bold text-slate-900">Defective Products & Supplier Resolution</h2>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Track defect records, supplier resolution decisions, and replacement fulfillment.</p>
                    </div>
                    <span class="rounded-full bg-rose-50 border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-700">
                        {{ $purchaseOrder->defectiveReturnRequests->count() }} Defective Item{{ $purchaseOrder->defectiveReturnRequests->count() > 1 ? 's' : '' }}
                    </span>
                </div>

                <div class="overflow-x-auto rounded-[12px] border border-slate-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#0f172a] text-white text-[11px] font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3 text-center">Defective Qty</th>
                                <th class="px-4 py-3">Defect Reason</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3">Resolution Details</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($purchaseOrder->defectiveReturnRequests as $req)
                                @php
                                    $statusBadge = match($req->status) {
                                        'Pending Supplier Response' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Replacement Approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Refund Approved' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Awaiting Replacement' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $req->product_name }}</td>
                                    <td class="px-4 py-3 text-xs text-slate-600">{{ $req->sku ?: 'N/A' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-rose-600">{{ $req->defective_quantity }}</td>
                                            <td class="px-4 py-3 text-xs text-slate-700">{{ $req->defect_reason ?: 'None specified' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $statusBadge }}">
                                            {{ $req->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-600">
                                        @if($req->resolution === 'Replacement')
                                            <div class="font-semibold text-blue-900 flex items-center gap-1.5">
                                                <span>Replacement PO:</span>
                                                @if($req->replacement_purchase_order_id)
                                                    <a href="{{ route('order.show', $req->replacement_purchase_order_id) }}" class="font-mono text-blue-600 underline font-bold hover:text-blue-800">
                                                        {{ $req->replacement_order_number }}
                                                    </a>
                                                @else
                                                    <span class="font-mono text-slate-900">{{ $req->replacement_order_number }}</span>
                                                @endif
                                            </div>
                                            <div class="mt-0.5 text-xs text-slate-700">
                                                @if($req->expected_replacement_date)
                                                    Expected replacement: <span class="font-semibold text-slate-900">{{ $req->expected_replacement_date->format('M j, Y') }}</span>
                                                @else
                                                    <span class="text-amber-700 font-medium">Delivery date not provided</span>
                                                @endif
                                            </div>
                                            @php
                                                $rpo = $req->replacementPurchaseOrder;
                                            @endphp
                                            @if($req->status === 'Completed' || ($rpo && $rpo->status === 'completed'))
                                                <div class="mt-0.5 text-[11px] text-emerald-700 font-medium">✓ Received {{ $req->replacement_received_quantity ?: ($rpo ? $rpo->items->sum('accepted_quantity') : $req->defective_quantity) }} units on {{ optional($req->replacement_received_at ?? ($rpo ? $rpo->completed_at : null))->format('M j, Y') }}</div>
                                            @elseif($rpo)
                                                <div class="mt-0.5 text-[11px] text-blue-600 font-medium">PO Status: <span class="font-semibold capitalize">{{ $rpo->status }}</span></div>
                                            @else
                                                <div class="mt-0.5 text-[11px] text-blue-600 font-medium">Awaiting delivery from supplier</div>
                                            @endif
                                            @if($req->resolution_notes)
                                                <div class="mt-0.5 text-[11px] text-slate-500">{{ $req->resolution_notes }}</div>
                                            @endif
                                        @elseif($req->resolution === 'Refund/Credit')
                                            <div class="font-semibold text-purple-900">Refund/Credit Approved</div>
                                            @if($req->resolution_notes)
                                                <div class="text-[11px] text-slate-500">{{ $req->resolution_notes }}</div>
                                            @endif
                                        @elseif($req->resolution === 'No Replacement')
                                            <div class="font-semibold text-slate-700">No Replacement Needed</div>
                                            @if($req->resolution_notes)
                                                <div class="text-[11px] text-slate-500">{{ $req->resolution_notes }}</div>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic">Awaiting supplier confirmation</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($req->status === 'Pending Supplier Response')
                                            <button type="button" onclick="openResolveModal('{{ $req->id }}', '{{ addslashes($req->product_name) }}', '{{ $req->defective_quantity }}')" class="inline-flex items-center gap-1.5 rounded-[10px] bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 shadow-sm">
                                                <span>Record Resolution</span>
                                            </button>
                                        @elseif($req->resolution === 'Replacement' && $req->replacement_purchase_order_id)
                                            <a href="{{ route('order.show', $req->replacement_purchase_order_id) }}" class="inline-flex items-center gap-1.5 rounded-[10px] {{ $req->status === 'Completed' ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm' }} px-3 py-1.5 text-xs font-semibold">
                                                <span>{{ $req->status === 'Completed' ? 'View Replacement PO' : 'Process Replacement PO' }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @else
                                            <span class="text-xs font-medium text-emerald-600 inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Closed
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Record Supplier Resolution Modal --}}
    <div id="resolveModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-2xl">
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Record Supplier Resolution</h3>
                    <p id="resolveModalSubtitle" class="text-xs text-slate-500 mt-0.5"></p>
                </div>
                <button type="button" onclick="closeResolveModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="resolveForm" method="POST" action="">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Supplier Resolution <span class="text-rose-500">*</span></label>
                        <select name="resolution" id="resolveResolution" onchange="toggleExpectedDateField()" required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400">
                            <option value="Replacement">Replacement (Supplier will send replacement units)</option>
                            <option value="Refund/Credit">Refund / Credit Note (Supplier will refund/deduct amount)</option>
                            <option value="No Replacement">No Replacement (Discarded / Disposed without replacement)</option>
                        </select>
                    </div>
                    <div id="expectedReplacementDateWrapper">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Expected Replacement Delivery Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="expected_replacement_date" id="resolveExpectedDate" required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400" />
                        <p class="text-[11px] text-slate-500 mt-1">Enter the delivery date provided by the supplier.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Resolution Notes / Remarks (Optional)</label>
                        <textarea name="resolution_notes" id="resolveNotes" rows="3" placeholder="Enter supplier RMA / tracking info or resolution details..." class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none focus:border-slate-400 focus:ring-1 focus:ring-slate-400"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50 px-6 py-3.5">
                    <button type="button" onclick="closeResolveModal()" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">Save Resolution</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Receive Replacement Modal --}}
    <div id="receiveReplacementModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-lg overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-2xl">
            <div class="border-b border-emerald-100 bg-emerald-50/70 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Receive Replacement Stock</h3>
                    <p id="receiveReplacementSubtitle" class="text-xs text-slate-500 mt-0.5"></p>
                </div>
                <button type="button" onclick="closeReceiveReplacementModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="receiveReplacementForm" method="POST" action="">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Replacement Quantity Received <span class="text-rose-500">*</span></label>
                        <input name="replacement_quantity" id="receiveReplacementQty" type="number" min="1" required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Destination Warehouse <span class="text-rose-500">*</span></label>
                            <select name="warehouse" required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <option value="Shop">Shop (Main Store)</option>
                                <option value="Warehouse A">Warehouse A</option>
                                <option value="Warehouse B">Warehouse B</option>
                                <option value="Warehouse C">Warehouse C</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Shelf Location (Optional)</label>
                            <select name="shelf_id" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <option value="">No specific shelf</option>
                                @php
                                    $shelves = \App\Models\WarehouseShelf::where('archived', false)->get();
                                @endphp
                                @foreach($shelves as $shelf)
                                    <option value="{{ $shelf->id }}">{{ $shelf->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 border-t border-slate-100 bg-slate-50 px-6 py-3.5">
                    <button type="button" onclick="closeReceiveReplacementModal()" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm shadow-emerald-600/20">Add to Inventory & Complete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleExpectedDateField() {
            const resSelect = document.getElementById('resolveResolution');
            const dateWrapper = document.getElementById('expectedReplacementDateWrapper');
            const dateInput = document.getElementById('resolveExpectedDate');
            if (resSelect.value === 'Replacement') {
                dateWrapper.classList.remove('hidden');
                dateInput.required = true;
            } else {
                dateWrapper.classList.add('hidden');
                dateInput.required = false;
                dateInput.value = '';
            }
        }

        function openResolveModal(reqId, productName, defQty) {
            const form = document.getElementById('resolveForm');
            form.action = `{{ url('purchase-order/' . $purchaseOrder->id . '/defective-request') }}/${reqId}/resolve`;
            document.getElementById('resolveModalSubtitle').textContent = `${productName} · ${defQty} defective unit(s)`;
            const modal = document.getElementById('resolveModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            toggleExpectedDateField();
        }

        function closeResolveModal() {
            const modal = document.getElementById('resolveModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openReceiveReplacementModal(reqId, productName, defQty, rboNumber) {
            const form = document.getElementById('receiveReplacementForm');
            form.action = `{{ url('purchase-order/' . $purchaseOrder->id . '/defective-request') }}/${reqId}/receive-replacement`;
            document.getElementById('receiveReplacementSubtitle').textContent = `${productName} · Ref: ${rboNumber || 'N/A'}`;
            const qtyInput = document.getElementById('receiveReplacementQty');
            qtyInput.value = defQty;
            qtyInput.max = defQty;
            const modal = document.getElementById('receiveReplacementModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeReceiveReplacementModal() {
            const modal = document.getElementById('receiveReplacementModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function resolvePoDetailImages() {
            try {
                const stored = localStorage.getItem('posProductImages');
                if (!stored) return;
                const images = JSON.parse(stored);
                const keys = Object.keys(images);

                document.querySelectorAll('.po-detail-img-thumb').forEach(container => {
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
                        container.className = 'po-detail-img-thumb w-8 h-8 rounded-[6px] bg-slate-100 border border-slate-200/80 flex-shrink-0 bg-cover bg-center';
                        container.style.backgroundImage = `url('${imgUrl}')`;
                    }
                });
            } catch(e) {
                console.error('Error resolving order detail product images:', e);
            }
        }
        document.addEventListener('DOMContentLoaded', resolvePoDetailImages);
    </script>
</x-layouts.app>
