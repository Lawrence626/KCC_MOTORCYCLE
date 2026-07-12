<x-layouts.app :title="__('Create Purchase Order')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Create Purchase Order</h1>
                <p class="max-w-2xl text-sm text-slate-500">Choose supplier and select low-stock products to restock.</p>
            </div>
            <a href="{{ route('order.management') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:border-[#105f68] hover:text-slate-900">Back to orders</a>
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

            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <form action="{{ route('order.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Supplier</span>
                        <input type="hidden" name="supplier_id" id="supplierInput" value="{{ old('supplier_id') }}" required />
                        <div class="relative mt-2">
                            <button type="button" id="supplierDropdownBtn" onclick="toggleSupplierDropdown()" class="w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-left text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none flex items-center justify-between">
                                <span id="supplierLabel">Select supplier</span>
                                <svg class="w-4 h-4 text-slate-500" id="supplierChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div id="supplierDropdown" class="hidden absolute top-full mt-2 left-0 w-full bg-white border border-slate-300 rounded-lg shadow-xl z-50 p-3 space-y-1 max-h-60 overflow-y-auto">
                                <button type="button" onclick="selectSupplier('', 'Select supplier')" class="w-full px-4 py-2 text-left text-sm text-slate-400 hover:bg-slate-100 rounded-[10px]">Select supplier</button>
                                @foreach($suppliers as $supplier)
                                    <button type="button" onclick="selectSupplier('{{ $supplier->id }}', '{{ addslashes($supplier->name) }}')" class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 rounded-[10px] supplier-option" data-value="{{ $supplier->id }}">{{ $supplier->name }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Expected delivery date</span>
                        <input name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" type="date" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                    </label>
                </div>

                    <label class="block text-sm text-slate-700">
                        <span class="text-xs font-semibold text-slate-500">Notes</span>
                        <textarea name="notes" rows="3" class="mt-2 w-full h-24 resize-none rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none">{{ old('notes') }}</textarea>
                    </label>

                <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700">Low stock products</h3>
                            <p class="mt-1 text-sm text-slate-500">Select the items to include in the order.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" data-filter="all" class="movement-filter-button rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-all duration-200 hover:text-slate-900 hover:shadow-inner">All</button>
                            <button type="button" data-filter="fast_moving" class="movement-filter-button rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-all duration-200 hover:text-slate-900 hover:shadow-inner">Fast moving</button>
                            <button type="button" data-filter="slow_moving" class="movement-filter-button rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-all duration-200 hover:text-slate-900 hover:shadow-inner">Slow moving</button>
                            <button type="button" data-filter="special_order" class="movement-filter-button rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-all duration-200 hover:text-slate-900 hover:shadow-inner">Special order</button>
                        </div>
                    </div>

                    <div class="mt-4 overflow-hidden rounded-[10px] border border-slate-200">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                                <tr>
                                    <th class="px-4 py-3">Select</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Movement</th>
                                    <th class="px-4 py-3">Supplier</th>
                                    <th class="px-4 py-3">SKU</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3">Reorder</th>
                                    <th class="px-4 py-3">Qty to order</th>
                                    <th class="px-4 py-3">Unit price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-700">
                                @forelse($lowStockProducts as $product)
                                    <tr class="hover:bg-white movement-row" data-movement="{{ $product->movement_category ?? 'special_order' }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="products[{{ $loop->index }}][selected]" value="1" class="h-4 w-4 rounded border-slate-300 text-[#105f68] focus:ring-[#105f68]" />
                                            <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $product->id }}" />
                                            <input type="hidden" name="products[{{ $loop->index }}][product_name]" value="{{ $product->product_name ?? $product->name }}" />
                                            <input type="hidden" name="products[{{ $loop->index }}][sku]" value="{{ $product->sku }}" />
                                        </td>
                                        <td class="px-4 py-3">{{ $product->product_name ?? $product->name }}</td>
                                        <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_', ' ', $product->movement_category ?? 'special_order') }}</td>
                                        <td class="px-4 py-3">{{ $product->supplier_name }}</td>
                                        <td class="px-4 py-3">{{ $product->sku }}</td>
                                        <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                                        <td class="px-4 py-3">{{ $product->reorder_level }}</td>
                                        <td class="px-4 py-3">
                                            <input name="products[{{ $loop->index }}][quantity]" type="number" min="1" value="{{ max(1, $product->reorder_level - $product->stock_quantity) }}" class="w-20 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input name="products[{{ $loop->index }}][unit_price]" type="number" step="0.01" min="0" value="{{ $product->unit_price }}" class="w-28 rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-6 text-center text-sm text-slate-500">No low-stock products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">{{ $lowStockProducts->links() }}</div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('order.management') }}" class="rounded-[10px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-[#105f68] hover:text-slate-900">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] bg-[#105f68] px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-[#105f68]/20 hover:bg-[#0c474e]">Submit order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.movement-filter-button');
            const currentFilter = '{{ $currentFilter ?? 'all' }}';

            function setActiveButton(selectedButton) {
                buttons.forEach(button => {
                    const isActive = button === selectedButton;

                    button.classList.toggle('bg-[#105f68]', isActive);
                    button.classList.toggle('bg-white', !isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('text-slate-700', !isActive);
                    button.classList.toggle('border-slate-200', !isActive);
                    button.classList.toggle('border-[#105f68]', isActive);
                });
            }

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const filter = this.dataset.filter;
                    // Reload page with filter parameter
                    const url = new URL(window.location);
                    url.searchParams.set('movement', filter);
                    url.searchParams.set('page', '1'); // Reset to page 1 when filter changes
                    window.location.href = url.toString();
                });
            });

            // Set active button based on current filter
            if (buttons.length) {
                const activeButton = Array.from(buttons).find(btn => btn.dataset.filter === currentFilter);
                if (activeButton) {
                    setActiveButton(activeButton);
                } else {
                    setActiveButton(buttons[0]);
                }
            }
        });

        // Supplier custom dropdown
        function toggleSupplierDropdown() {
            const dd = document.getElementById('supplierDropdown');
            dd.classList.toggle('hidden');
        }

        function selectSupplier(value, label) {
            document.getElementById('supplierInput').value = value;
            document.getElementById('supplierLabel').textContent = label;
            document.getElementById('supplierLabel').classList.toggle('text-slate-400', !value);
            document.getElementById('supplierLabel').classList.toggle('text-slate-900', !!value);
            document.getElementById('supplierDropdown').classList.add('hidden');

            // Highlight active option
            document.querySelectorAll('.supplier-option').forEach(btn => {
                const isActive = btn.dataset.value === value;
                btn.classList.toggle('bg-[#105f68]/10', isActive);
                btn.classList.toggle('text-[#105f68]', isActive);
                btn.classList.toggle('font-semibold', isActive);
                btn.classList.toggle('text-slate-700', !isActive);
            });
        }

        // Click outside to close
        document.addEventListener('click', function(e) {
            const dd = document.getElementById('supplierDropdown');
            const btn = document.getElementById('supplierDropdownBtn');
            if (dd && btn && !dd.contains(e.target) && !btn.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });

        // Set initial selection if old value exists
        document.addEventListener('DOMContentLoaded', function() {
            const oldVal = document.getElementById('supplierInput').value;
            if (oldVal) {
                const opt = document.querySelector('.supplier-option[data-value="' + oldVal + '"]');
                if (opt) {
                    selectSupplier(oldVal, opt.textContent.trim());
                }
            }
        });
    </script>
</x-layouts.app>
