<x-layouts.app :title="__('Supplier Assessment')">
    <div class="space-y-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Supplier Assessment</h1>
                <p class="text-sm text-slate-500 mt-1">Track supplier performance, manage supplier records, and inspect products with pricing at a glance.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <button id="openSupplierModal" class="inline-flex items-center justify-center rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700">
                    + Add Supplier
                </button>
                <a href="{{ route('supplier.assessment') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    Refresh
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Suppliers</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($quickStats['totalSuppliers']) }}</p>
                <p class="mt-2 text-sm text-slate-500">Total suppliers tracked from inventory and supplier records.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Active suppliers</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($quickStats['activeSuppliers']) }}</p>
                <p class="mt-2 text-sm text-slate-500">Suppliers currently marked as active.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Products connected</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($quickStats['trackedProducts']) }}</p>
                <p class="mt-2 text-sm text-slate-500">Products currently linked with supplier names.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Supplier stock value</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">₱{{ number_format($quickStats['stockValue'], 2) }}</p>
                <p class="mt-2 text-sm text-slate-500">Combined value of supplier-stocked products.</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 md:grid-cols-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Search</label>
                    <input id="supplierSearch" type="search" placeholder="Supplier name, contact or email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</label>
                    <select id="supplierStatusFilter" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Has supplier record</label>
                    <select id="supplierRecordFilter" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100">
                        <option value="">All suppliers</option>
                        <option value="recorded">Registered</option>
                        <option value="unrecorded">Unregistered</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="clearFilters" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Clear filters</button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                        <tr>
                            <th class="px-4 py-4">Supplier</th>
                            <th class="px-4 py-4">Contact</th>
                            <th class="px-4 py-4">Products</th>
                            <th class="px-4 py-4">Min Price</th>
                            <th class="px-4 py-4">Avg Price</th>
                            <th class="px-4 py-4">Max Price</th>
                            <th class="px-4 py-4">Stock Value</th>
                            <th class="px-4 py-4">Last Restock</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-4 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTableBody" class="divide-y divide-slate-200 bg-white">
                        @foreach($supplierSummaries as $supplier)
                            <tr class="supplier-row" data-name="{{ strtolower($supplier->name) }}" data-contact="{{ strtolower($supplier->contact_person ?? '') }}" data-email="{{ strtolower($supplier->email ?? '') }}" data-status="{{ $supplier->status }}" data-recorded="{{ $supplier->has_record ? 'recorded' : 'unrecorded' }}">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-900">{{ $supplier->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $supplier->notes ?? 'No description available' }}</div>
                                </td>
                                <td class="px-4 py-4 space-y-1 text-slate-700">
                                    <div>{{ $supplier->contact_person ?? '—' }}</div>
                                    <div class="text-xs text-slate-500">{{ $supplier->email ?? $supplier->phone ?? 'No contact' }}</div>
                                </td>
                                <td class="px-4 py-4 font-semibold text-slate-900">{{ $supplier->product_count }}</td>
                                <td class="px-4 py-4">₱{{ number_format($supplier->min_price, 2) }}</td>
                                <td class="px-4 py-4">₱{{ number_format($supplier->avg_price, 2) }}</td>
                                <td class="px-4 py-4">₱{{ number_format($supplier->max_price, 2) }}</td>
                                <td class="px-4 py-4">₱{{ number_format($supplier->total_value, 2) }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $supplier->last_restock_date ?? 'No restock' }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-2xl px-3 py-1 text-xs font-semibold {{ $supplier->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="supplier-products-button inline-flex items-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50" data-supplier-name="{{ $supplier->name }}">
                                            View product prices
                                        </button>
                                        @if($supplier->has_record)
                                            <button type="button" class="supplier-edit-button inline-flex items-center rounded-2xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-xs font-semibold text-cyan-700 transition hover:bg-cyan-100" data-supplier='{{ json_encode(["id" => $supplier->id, "name" => $supplier->name, "contact_person" => $supplier->contact_person, "email" => $supplier->email, "phone" => $supplier->phone, "status" => $supplier->status, "notes" => $supplier->notes]) }}'>
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('supplier.assessment.destroy', ['supplier' => $supplier->id]) }}" class="inline-block" onsubmit="return confirm('Delete supplier and clear linked products?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-2xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                            </form>
                                        @else
                                            <button type="button" class="supplier-create-button inline-flex items-center rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50" data-supplier-name="{{ $supplier->name }}">
                                                Register
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="supplierModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 id="supplierModalTitle" class="text-xl font-semibold text-slate-900">Add supplier</h2>
                    <p id="supplierModalSubtitle" class="mt-1 text-sm text-slate-500">Create a supplier record and link products automatically.</p>
                </div>
                <button type="button" id="closeSupplierModal" class="text-slate-400 transition hover:text-slate-700">✕</button>
            </div>

            <form id="supplierForm" method="POST" action="{{ route('supplier.assessment.store') }}" class="space-y-4 px-6 py-6">
                @csrf
                <input type="hidden" name="_method" id="supplierFormMethod" value="POST" />
                <input type="hidden" name="supplier_id" id="supplierId" value="" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">
                        Supplier name
                        <input id="supplierNameInput" name="name" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" required />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Contact person
                        <input id="supplierContactInput" name="contact_person" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">
                        Email address
                        <input id="supplierEmailInput" name="email" type="email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Phone number
                        <input id="supplierPhoneInput" name="phone" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-medium text-slate-700">
                        Status
                        <select id="supplierStatusInput" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-slate-700">
                        Notes
                        <input id="supplierNotesInput" name="notes" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100" />
                    </label>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button type="button" id="cancelSupplierModal" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</button>
                    <button type="submit" id="supplierModalSubmit" class="rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">Save supplier</button>
                </div>
            </form>
        </div>
    </div>

    <div id="productsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
        <div class="w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                <div>
                    <h2 id="productsModalTitle" class="text-xl font-semibold text-slate-900">Supplier products</h2>
                    <p id="productsModalSubtitle" class="mt-1 text-sm text-slate-500">Review the products, pricing, and stock linked to this supplier.</p>
                </div>
                <button type="button" id="closeProductsModal" class="text-slate-400 transition hover:text-slate-700">✕</button>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Unit price</th>
                                <th class="px-4 py-3">Restock</th>
                            </tr>
                        </thead>
                        <tbody id="productsModalTableBody" class="divide-y divide-slate-200 bg-white"></tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Prices and stock are pulled from current product records.</p>
                    <button type="button" id="closeProductsModalButton" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const supplierSummaries = @json($supplierSummaries);
            const supplierModal = document.getElementById('supplierModal');
            const productsModal = document.getElementById('productsModal');
            const supplierForm = document.getElementById('supplierForm');
            const supplierModalTitle = document.getElementById('supplierModalTitle');
            const supplierModalSubtitle = document.getElementById('supplierModalSubtitle');
            const supplierFormMethod = document.getElementById('supplierFormMethod');
            const supplierId = document.getElementById('supplierId');
            const supplierNameInput = document.getElementById('supplierNameInput');
            const supplierContactInput = document.getElementById('supplierContactInput');
            const supplierEmailInput = document.getElementById('supplierEmailInput');
            const supplierPhoneInput = document.getElementById('supplierPhoneInput');
            const supplierStatusInput = document.getElementById('supplierStatusInput');
            const supplierNotesInput = document.getElementById('supplierNotesInput');
            const supplierModalSubmit = document.getElementById('supplierModalSubmit');
            const openSupplierModalButton = document.getElementById('openSupplierModal');
            const closeSupplierModalButton = document.getElementById('closeSupplierModal');
            const cancelSupplierModalButton = document.getElementById('cancelSupplierModal');
            const supplierSearch = document.getElementById('supplierSearch');
            const supplierStatusFilter = document.getElementById('supplierStatusFilter');
            const supplierRecordFilter = document.getElementById('supplierRecordFilter');
            const clearFiltersButton = document.getElementById('clearFilters');
            const productsModalTitle = document.getElementById('productsModalTitle');
            const productsModalSubtitle = document.getElementById('productsModalSubtitle');
            const productsModalTableBody = document.getElementById('productsModalTableBody');
            const closeProductsModal = document.getElementById('closeProductsModal');
            const closeProductsModalButton = document.getElementById('closeProductsModalButton');

            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function resetSupplierForm() {
                supplierForm.reset();
                supplierForm.action = '{{ route('supplier.assessment.store') }}';
                supplierFormMethod.value = 'POST';
                supplierId.value = '';
                supplierModalTitle.textContent = 'Add supplier';
                supplierModalSubtitle.textContent = 'Create a supplier record and link products automatically.';
                supplierModalSubmit.textContent = 'Save supplier';
            }

            function fillSupplierForm(supplier) {
                supplierModalTitle.textContent = 'Edit supplier';
                supplierModalSubtitle.textContent = 'Update the supplier details and product links.';
                supplierFormMethod.value = 'PATCH';
                supplierId.value = supplier.id;
                supplierNameInput.value = supplier.name || '';
                supplierContactInput.value = supplier.contact_person || '';
                supplierEmailInput.value = supplier.email || '';
                supplierPhoneInput.value = supplier.phone || '';
                supplierStatusInput.value = supplier.status || 'active';
                supplierNotesInput.value = supplier.notes || '';
                supplierForm.action = '{{ url('supplier-assessment/suppliers') }}/' + supplier.id;
                supplierModalSubmit.textContent = 'Update supplier';
            }

            function filterSupplierRows() {
                const searchValue = supplierSearch.value.trim().toLowerCase();
                const selectedStatus = supplierStatusFilter.value;
                const selectedRecord = supplierRecordFilter.value;

                document.querySelectorAll('.supplier-row').forEach(row => {
                    const name = row.dataset.name || '';
                    const contact = row.dataset.contact || '';
                    const email = row.dataset.email || '';
                    const status = row.dataset.status || '';
                    const recorded = row.dataset.recorded || '';

                    const matchesSearch = [name, contact, email].some(value => value.includes(searchValue));
                    const matchesStatus = selectedStatus ? status === selectedStatus : true;
                    const matchesRecord = selectedRecord ? recorded === selectedRecord : true;

                    row.classList.toggle('hidden', !(matchesSearch && matchesStatus && matchesRecord));
                });
            }

            function openProductsModal(supplierName) {
                const supplier = supplierSummaries.find(item => item.name === supplierName);
                if (!supplier) {
                    return;
                }

                productsModalTitle.textContent = `Products from ${supplier.name}`;
                productsModalSubtitle.textContent = `${supplier.product_count} product${supplier.product_count === 1 ? '' : 's'} linked to this supplier.`;
                productsModalTableBody.innerHTML = '';

                if (!supplier.products.length) {
                    productsModalTableBody.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No product records are currently linked to this supplier.</td></tr>';
                } else {
                    supplier.products.forEach(product => {
                        productsModalTableBody.insertAdjacentHTML('beforeend', `
                            <tr class="border-b border-slate-200">
                                <td class="px-4 py-4 font-medium text-slate-900">${product.name}</td>
                                <td class="px-4 py-4 text-slate-600">${product.sku}</td>
                                <td class="px-4 py-4 text-slate-600">${product.category}</td>
                                <td class="px-4 py-4 text-slate-900">${product.stock_quantity}</td>
                                <td class="px-4 py-4 text-slate-900">₱${Number(product.price).toFixed(2)}</td>
                                <td class="px-4 py-4 text-slate-600">${product.last_restock_date || 'N/A'}</td>
                            </tr>
                        `);
                    });
                }

                openModal(productsModal);
            }

            openSupplierModalButton.addEventListener('click', () => {
                resetSupplierForm();
                openModal(supplierModal);
            });

            [closeSupplierModalButton, cancelSupplierModalButton].forEach(button => {
                button.addEventListener('click', () => closeModal(supplierModal));
            });

            document.querySelectorAll('.supplier-edit-button').forEach(button => {
                button.addEventListener('click', event => {
                    const supplier = JSON.parse(event.currentTarget.dataset.supplier);
                    fillSupplierForm(supplier);
                    openModal(supplierModal);
                });
            });

            document.querySelectorAll('.supplier-create-button').forEach(button => {
                button.addEventListener('click', event => {
                    const name = event.currentTarget.dataset.supplierName;
                    resetSupplierForm();
                    supplierNameInput.value = name;
                    openModal(supplierModal);
                });
            });

            document.querySelectorAll('.supplier-products-button').forEach(button => {
                button.addEventListener('click', event => {
                    openProductsModal(event.currentTarget.dataset.supplierName);
                });
            });

            [supplierSearch, supplierStatusFilter, supplierRecordFilter].forEach(control => {
                control.addEventListener('input', filterSupplierRows);
            });

            clearFiltersButton.addEventListener('click', event => {
                event.preventDefault();
                supplierSearch.value = '';
                supplierStatusFilter.value = '';
                supplierRecordFilter.value = '';
                filterSupplierRows();
            });

            [closeProductsModal, closeProductsModalButton].forEach(button => {
                button.addEventListener('click', () => closeModal(productsModal));
            });
        </script>
    @endpush
</x-layouts.app>
