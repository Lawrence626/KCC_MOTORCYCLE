<x-layouts.app :title="__('Transfer Products')">
    <style>
        :root {
            --brand: #0f766e;
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #6b7280;
            --border: rgba(148,163,184,0.2);
        }
        .wm-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .wm-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .product-chip { background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.12); color: var(--brand-dark); font-size: 0.78rem; padding: 0.35rem 0.6rem; border-radius: 0.8rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; }
        .product-chip .left { display:flex; flex-direction:column; gap:0.08rem; }
        .product-chip .name { font-weight:600; font-size:0.84rem; color:#0f172a; }
        .product-chip .meta { font-size:0.62rem; color:#475569; }
        .product-chip .qty-badge { font-weight:700; font-size:0.74rem; color:#0f172a; background: rgba(15,118,110,0.1); padding: 0.2rem 0.5rem; border-radius: 0.5rem; }
        .shelf-option { background: #f8fafc; border: 1px dashed rgba(15,118,110,0.16); transition: all 0.2s ease; }
        .shelf-option:hover { background: rgba(16,185,129,0.04); border-color: rgba(15,118,110,0.3); }
        .shelf-option.selected { background: rgba(16,185,129,0.08); border: 1px solid rgba(15,118,110,0.3); }
        .status-badge { font-size: 0.7rem; padding: 0.25rem 0.6rem; border-radius: 9999px; font-weight: 600; }
        .status-available { background: linear-gradient(90deg, #10b981, #059669); color: white; }
        .status-almost-full { background: linear-gradient(90deg, #f59e0b, #d97706); color: white; }
        .status-limited { background: linear-gradient(90deg, #f97316, #ea580c); color: white; }
        .quantity-input { border: 1px solid rgba(148,163,184,0.35); background: #f8fafc; border-radius: 0.85rem; transition: all 0.2s ease; }
        .quantity-input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(15,118,110,0.1); outline: none; }
        .summary-card { background: linear-gradient(135deg, rgba(16,185,129,0.05), rgba(15,118,110,0.08)); border: 1px solid rgba(15,118,110,0.2); }
        .btn-primary { background: linear-gradient(90deg, var(--brand), var(--brand-dark)); color: #fff; box-shadow: 0 4px 15px rgba(15,118,110,0.25); transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.35); }
        .btn-secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); transition: all 0.2s ease; }
        .btn-secondary:hover { background: #f1f5f9; border-color: rgba(148,163,184,0.5); }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.85rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f766e; color: #fff; border-radius: 1rem; box-shadow: 0 18px 50px rgba(15,23,42,0.18); padding: 0.85rem 1rem; font-size: 0.95rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f766e; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 1rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="space-y-6">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

        <div class="flex items-start justify-between gap-2">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Transfer Products</h1>
                <p class="mt-2 text-sm text-gray-500">Move products from <strong class="text-emerald-600">{{ is_array($shelf) ? ($shelf['name'] ?? 'Unknown') : ($shelf->name ?? 'Unknown') }}</strong> to another shelf</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('warehouse.management') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm hover:border-emerald-500 hover:text-slate-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Warehouse
                </a>
                <button type="button" id="confirm-transfer" class="btn-primary rounded-2xl px-6 py-3 text-sm font-semibold opacity-50 cursor-not-allowed" disabled>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirm Transfer
                    </span>
                </button>
            </div>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Source Shelf Products -->
            <div class="wm-card rounded-[28px] p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Source Shelf</h2>
                        <p class="text-xs text-slate-500">{{ is_array($shelf) ? ($shelf['name'] ?? 'Unknown') : ($shelf->name ?? 'Unknown') }}</p>
                    </div>
                </div>

                @if(empty($shelfProducts))
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-sm text-gray-500">No products on this shelf</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @php $productIndex = 0; @endphp
                        @foreach($shelfProducts as $product)
                            <div class="product-chip">
                                <div class="left">
                                    <span class="name">Product Category: {{ $product['description'] ?? $product['name'] }}</span>
                                    <span class="meta">SKU: {{ $product['sku'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="qty-badge">Qty: {{ $product['qty'] }}</span>
                                    <input type="checkbox" id="product-{{ $productIndex }}" class="transfer-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0" data-index="{{ $productIndex }}" />
                                </div>
                            </div>
                            @php $productIndex++; @endphp
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Destination Shelf -->
            <div class="wm-card rounded-[28px] p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Destination Shelf</h2>
                        <p class="text-xs text-slate-500">Select a shelf with available space</p>
                    </div>
                </div>

                @if(empty($availableShelves))
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm text-gray-500">No shelves with available space</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($availableShelves as $shelf)
                            <label class="shelf-option flex items-center gap-3 p-4 rounded-xl cursor-pointer">
                                <input type="radio" name="destination_slot" value="{{ $shelf['slot_index'] }}" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0" required />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">{{ $shelf['name'] }}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $shelf['current_occupancy'] }} / {{ $shelfCapacity }} occupied
                                        <span class="text-emerald-600 font-medium">• {{ $shelf['available_space'] }} slots available</span>
                                    </p>
                                </div>
                                <span class="status-badge {{ $shelf['status'] === 'Available' ? 'status-available' : ($shelf['status'] === 'Almost Full' ? 'status-almost-full' : 'status-limited') }}">
                                    {{ $shelf['status'] }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <form id="transfer-form" action="{{ route('warehouse.transfer.execute') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="warehouse_id" value="{{ $warehouseId }}" />
        <input type="hidden" name="source_slot_index" value="{{ $slotIndex }}" />
        <input type="hidden" name="destination_slot_index" id="form-destination-slot" />
        <div id="form-transfers"></div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.transfer-checkbox');
            const destinationRadios = document.querySelectorAll('input[name="destination_slot"]');
            const confirmButton = document.getElementById('confirm-transfer');
            const transferForm = document.getElementById('transfer-form');
            const formDestinationSlot = document.getElementById('form-destination-slot');
            const formTransfers = document.getElementById('form-transfers');
            const toastContainer = document.getElementById('toast-container');

            const shelfProducts = @json($shelfProducts ?? []);
            console.log('Shelf products loaded:', shelfProducts);
            console.log('Warehouse ID:', {{ $warehouseId }});
            console.log('Slot index:', {{ $slotIndex }});

            let selectedProducts = [];
            let selectedDestination = null;

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.innerHTML = `
                    <span>${message}</span>
                    <button onclick="this.parentElement.remove()">&times;</button>
                `;
                toastContainer.appendChild(toast);
                setTimeout(() => toast.remove(), 5000);
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const index = this.dataset.index;
                    if (this.checked) {
                        selectedProducts.push(index);
                    } else {
                        selectedProducts = selectedProducts.filter(i => i !== index);
                    }
                    updateConfirmButton();
                });
            });

            destinationRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    selectedDestination = this.value;
                    document.querySelectorAll('.shelf-option').forEach(opt => opt.classList.remove('selected'));
                    this.closest('.shelf-option').classList.add('selected');
                    updateConfirmButton();
                });
            });

            function updateConfirmButton() {
                const availableShelves = @json($availableShelves);
                const destShelf = selectedDestination ? availableShelves.find(s => s.slot_index == selectedDestination) : null;

                // Shelf capacity is based on number of products, not quantity
                const numberOfProductsToTransfer = selectedProducts.length;
                const availableAfterTransfer = destShelf ? destShelf.available_space - numberOfProductsToTransfer : 0;

                if (selectedProducts.length === 0 || !selectedDestination || availableAfterTransfer < 0) {
                    confirmButton.disabled = true;
                    confirmButton.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    confirmButton.disabled = false;
                    confirmButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            confirmButton.addEventListener('click', async function() {
                if (selectedProducts.length === 0 || !selectedDestination) return;

                confirmButton.disabled = true;
                confirmButton.textContent = 'Transferring...';

                // Set the destination slot index in the hidden form
                formDestinationSlot.value = selectedDestination;

                console.log('Selected product indices:', selectedProducts);
                console.log('shelfProducts array:', shelfProducts);

                const transferData = {
                    warehouse_id: formDestinationSlot.closest('form').querySelector('input[name="warehouse_id"]').value,
                    source_slot_index: formDestinationSlot.closest('form').querySelector('input[name="source_slot_index"]').value,
                    destination_slot_index: selectedDestination,
                    transfers: selectedProducts.map(index => {
                        const product = shelfProducts[index];
                        console.log('Product at index:', index, product);
                        return {
                            product_id: parseInt(product.product_id ?? index),
                            product_name: product.name,
                            sku: product.sku,
                            current_qty: parseInt(product.qty),
                            transfer_qty: parseInt(product.qty),
                            unit_price: parseFloat(product.price)
                        };
                    })
                };

                console.log('Sending transfer data:', transferData);

                try {
                    const response = await fetch(transferForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(transferData)
                    });

                    const data = await response.json();
                    console.log('Server response:', data);

                    if (data.success) {
                        showToast('Products transferred successfully!', 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("warehouse.management") }}';
                        }, 1500);
                    } else {
                        showToast(data.message || 'Transfer failed', 'error');
                        confirmButton.disabled = false;
                        confirmButton.innerHTML = `
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Confirm Transfer
                            </span>
                        `;
                    }
                } catch (error) {
                    console.error('Transfer error:', error);
                    showToast('An error occurred during transfer', 'error');
                    confirmButton.disabled = false;
                    confirmButton.innerHTML = `
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirm Transfer
                        </span>
                    `;
                }
            });
        });
    </script>
</x-layouts.app>
