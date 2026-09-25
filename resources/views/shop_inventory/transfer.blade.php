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
        .si-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .si-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .product-chip { background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.12); color: var(--brand-dark); font-size: 0.78rem; padding: 0.35rem 0.6rem; border-radius: 0.8rem; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; }
        .product-chip .left { display:flex; flex-direction:column; gap:0.08rem; }
        .product-chip .name { font-weight:600; font-size:0.84rem; color:#0f172a; }
        .product-chip .meta { font-size:0.62rem; color:#475569; }
        .product-chip .qty { font-weight:700; font-size:0.84rem; color:#0f172a; margin-left:0.4rem; min-width:44px; text-align:right; }
        .shelf-option { background: #f8fafc; border: 1px dashed rgba(15,118,110,0.16); transition: all 0.2s ease; }
        .shelf-option:hover { background: rgba(16,185,129,0.04); border-color: rgba(15,118,110,0.3); }
        .shelf-option.selected { background: rgba(16,185,129,0.08); border: 1px solid rgba(15,118,110,0.3); }
        .status-badge { font-size: 0.7rem; padding: 0.25rem 0.6rem; border-radius: 9999px; font-weight: 600; }
        .status-available { background: linear-gradient(90deg, #10b981, #059669); color: white; }
        .status-almost-full { background: linear-gradient(90deg, #f59e0b, #d97706); color: white; }
        .status-limited { background: linear-gradient(90deg, #f97316, #ea580c); color: white; }
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

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Transfer Products</h1>
                <p class="text-xs text-slate-500 mt-0.5">Move products from <strong class="text-emerald-600">{{ is_array($shelf) ? $shelf['name'] : $shelf->name }}</strong> to another shelf</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('shop.inventory') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Shop Inventory
                </a>
                <button type="button" id="confirm-transfer" class="btn-primary rounded-[10px] px-3 py-1.5 text-xs font-semibold opacity-50 cursor-not-allowed" disabled>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirm Transfer
                    </span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Source Shelf Products -->
            <div class="si-card rounded-[28px] p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Source Shelf</h2>
                        <p class="text-xs text-slate-500">{{ is_array($shelf) ? $shelf['name'] : $shelf->name }}</p>
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
                        @foreach($shelfProducts as $productIndex => $product)
                        <div class="product-row-card flex items-center justify-between p-3">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="product-{{ $productIndex }}" class="transfer-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0" data-product-id="{{ $product['product_id'] }}" />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">Product Category: {{ $product['description'] ?? $product['name'] }}</p>
                                    <p class="text-xs text-slate-500">SKU: {{ $product['sku'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-slate-700">QTY: {{ $product['qty'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Destination Shelf Selection -->
            <div class="si-card rounded-[28px] p-6">
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
                        @foreach($availableShelves as $availableShelf)
                        <label class="shelf-option flex items-center gap-3 p-4 rounded-xl cursor-pointer">
                            <input type="radio" name="destination_shelf" value="{{ $availableShelf['id'] }}" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0" required />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $availableShelf['name'] }}</p>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $availableShelf['current_occupancy'] }} / {{ $shelfCapacity }} occupied
                                    <span class="text-emerald-600 font-medium">• {{ $availableShelf['available_space'] }} slots available</span>
                                </p>
                            </div>
                            <span class="status-badge {{ $availableShelf['status'] === 'Available' ? 'status-available' : ($availableShelf['status'] === 'Almost Full' ? 'status-almost-full' : 'status-limited') }}">
                                {{ $availableShelf['status'] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <form id="transfer-form" action="{{ route('shop.inventory.execute_transfer') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="source_shelf_id" value="{{ is_array($shelf) ? $shelf['id'] : $shelf->id }}" />
        <input type="hidden" name="destination_shelf_id" id="form-destination-shelf" />
        <div id="form-transfers"></div>
    </form>

    <!-- Debug info -->
    <div style="display:none;" id="debug-info">
        Shelf ID: {{ is_array($shelf) ? $shelf['id'] : $shelf->id }}
        Shelf Name: {{ is_array($shelf) ? $shelf['name'] : $shelf->name }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.transfer-checkbox');
            const destinationRadios = document.querySelectorAll('input[name="destination_shelf"]');
            const confirmButton = document.getElementById('confirm-transfer');
            const transferForm = document.getElementById('transfer-form');
            const formDestinationShelf = document.getElementById('form-destination-shelf');
            const formTransfers = document.getElementById('form-transfers');
            const toastContainer = document.getElementById('toast-container');

            const shelfProducts = @json($shelfProducts ?? []);
            console.log('Shelf products loaded:', shelfProducts);
            console.log('Shelf products IDs:', shelfProducts.map(p => p.product_id));

            // Debug: Check the hidden form field
            const sourceShelfIdField = transferForm.querySelector('input[name="source_shelf_id"]');
            console.log('Hidden source_shelf_id field value:', sourceShelfIdField.value);
            console.log('Debug info div:', document.getElementById('debug-info').textContent);

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
                setTimeout(() => toast.remove(), 3000);
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const productId = parseInt(this.dataset.productId);
                    console.log('Checkbox changed - Product ID from dataset:', productId);
                    console.log('Checkbox dataset:', this.dataset);
                    if (this.checked) {
                        selectedProducts.push(productId);
                        console.log('Added to selectedProducts:', productId);
                    } else {
                        selectedProducts = selectedProducts.filter(id => id !== productId);
                        console.log('Removed from selectedProducts:', productId);
                    }
                    console.log('Current selectedProducts:', selectedProducts);
                    updateConfirmButton();
                });
            });

            destinationRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    selectedDestination = this.value;
                    updateConfirmButton();
                });
            });

            function updateConfirmButton() {
                const availableShelves = @json($availableShelves);
                const destShelf = selectedDestination ? availableShelves.find(s => s.id == selectedDestination) : null;

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

                formDestinationShelf.value = selectedDestination;

                console.log('Selected product IDs:', selectedProducts);
                console.log('Finding products in shelfProducts...');
                console.log('shelfProducts array:', shelfProducts);

                const transfers = selectedProducts.map(productId => {
                    const product = shelfProducts.find(p => p.product_id === productId);
                    console.log('Looking for product ID:', productId, 'Found:', product);
                    if (!product) {
                        console.error('Product not found in shelfProducts for ID:', productId);
                        return null;
                    }
                    const transferItem = {
                        product_id: parseInt(product.product_id),
                        product_name: product.name,
                        sku: product.sku,
                        current_qty: parseInt(product.qty),
                        transfer_qty: parseInt(product.qty),
                        unit_price: parseFloat(product.price)
                    };
                    console.log('Transfer item created:', transferItem);
                    return transferItem;
                }).filter(p => p !== null);

                console.log('Final transfers array:', transfers);

                const transferData = {
                    source_shelf_id: parseInt(transferForm.querySelector('input[name="source_shelf_id"]').value),
                    destination_shelf_id: parseInt(selectedDestination),
                    transfers: transfers
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
                            window.location.href = '{{ route("shop.inventory") }}';
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
                    showToast('Transfer failed. Please try again.', 'error');
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
