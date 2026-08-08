<x-layouts.app :title="__('Product Categorization')">
    <style>
        .action-dropdown { position: relative; }
        .dropdown-menu { display: none; position: absolute; right: 0; top: 100%; z-index: 50; min-width: 160px; }
        .dropdown-menu.show { display: block; }
        .qr-thumbnail { width: 40px; height: 40px; object-fit: contain; }
        .modal-overlay { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .motorcycle-group { max-height: 200px; overflow-y: auto; }
        .badge-truncate { max-width: 150px; }
        /* Toast */
        #toastContainer { position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999; display: flex; flex-direction: column; gap: .5rem; pointer-events: none; }
        .toast { display: flex; align-items: center; gap: .75rem; padding: .75rem 1.25rem; border-radius: .75rem; box-shadow: 0 4px 16px rgba(0,0,0,.12); font-size: .875rem; font-weight: 500; pointer-events: all; animation: slideIn .25s ease; }
        .toast.success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
        .toast.error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
        @keyframes slideIn { from { transform:translateX(120%); opacity:0; } to { transform:translateX(0); opacity:1; } }
        @keyframes slideOut { from { transform:translateX(0); opacity:1; } to { transform:translateX(120%); opacity:0; } }
    </style>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Product Categorization</h1>
                    <p class="mt-1 text-xs text-slate-500">Master product catalog with SKU generation, QR codes, and motorcycle compatibility mapping.</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Bulk Actions Toolbar -->
                    <div id="bulkActionsToolbar" class="items-center gap-3 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200" style="display:none">
                        <span class="text-xs text-slate-700 font-medium"><span id="selectedCount">0</span> selected</span>
                        <form action="{{ route('product-catalog.bulk-delete') }}" method="POST"
                              onsubmit="const c=document.querySelectorAll('.product-checkbox:checked').length;return c>0&&confirm(`Move ${c} product(s) to Trash? You can restore them later.`);">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="ids" id="selectedIds">
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">
                                Move to Trash
                            </button>
                        </form>
                        <button onclick="clearSelection()" class="text-xs text-slate-600 hover:text-slate-800 font-medium">Clear</button>
                    </div>
                    <!-- Trash Button -->
                    <button onclick="openTrashModal()" class="relative flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Trash
                        <span id="trashBadge" class="absolute -top-1.5 -right-1.5 hidden items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-xs font-bold">0</span>
                    </button>
                    <a href="{{ route('product-catalog.create') }}" class="rounded-full border border-[#00fff2]/40 bg-[#00fff2] px-4 py-2 text-xs font-semibold text-black hover:bg-[#00e6da] transition shadow-sm">+ Add Product</a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm">
            <form action="{{ route('product-catalog.index') }}" method="GET" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="text-xs text-slate-600 font-semibold mb-1 block">Search</label>
                        <input type="text"
                               name="search"
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="Search description, brand, SKU..."
                               class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-xs text-slate-600 font-semibold mb-1 block">Location</label>
                        <select name="warehouse"
                                id="warehouseFilter"
                                class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="">All Locations</option>
                            <option value="Shop" {{ request('warehouse') == 'Shop' ? 'selected' : '' }}>Shop</option>
                            <option value="Warehouse A" {{ request('warehouse') == 'Warehouse A' ? 'selected' : '' }}>Warehouse A</option>
                            <option value="Warehouse B" {{ request('warehouse') == 'Warehouse B' ? 'selected' : '' }}>Warehouse B</option>
                            <option value="Warehouse C" {{ request('warehouse') == 'Warehouse C' ? 'selected' : '' }}>Warehouse C</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-600 font-semibold mb-1 block">Product Description</label>
                        <select name="product_description"
                                id="productDescriptionFilter"
                                class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="">All Descriptions</option>
                            @foreach($productDescriptions as $description)
                                <option value="{{ $description->name }}"
                                        data-brands="{{ json_encode($description->brands) }}"
                                        {{ request('product_description') == $description->name ? 'selected' : '' }}>
                                    {{ $description->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-600 font-semibold mb-1 block">Brand</label>
                        <select name="brand"
                                id="brandFilter"
                                class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-slate-600 font-semibold mb-1 block">Size</label>
                        <select name="size"
                                id="sizeFilter"
                                class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="">All Sizes</option>
                            <option value="190" {{ request('size') == '190' ? 'selected' : '' }}>190</option>
                            <option value="230" {{ request('size') == '230' ? 'selected' : '' }}>230</option>
                            <option value="260" {{ request('size') == '260' ? 'selected' : '' }}>260</option>
                            <option value="300" {{ request('size') == '300' ? 'selected' : '' }}>300</option>
                            <option value="305" {{ request('size') == '305' ? 'selected' : '' }}>305</option>
                            <option value="320" {{ request('size') == '320' ? 'selected' : '' }}>320</option>
                            <option value="330" {{ request('size') == '330' ? 'selected' : '' }}>330</option>
                            <option value="335" {{ request('size') == '335' ? 'selected' : '' }}>335</option>
                            <option value="365" {{ request('size') == '365' ? 'selected' : '' }}>365</option>
                            <option value="80/80 14" {{ request('size') == '80/80 14' ? 'selected' : '' }}>80/80 14</option>
                            <option value="90/80 14" {{ request('size') == '90/80 14' ? 'selected' : '' }}>90/80 14</option>
                            <option value="100/80 14" {{ request('size') == '100/80 14' ? 'selected' : '' }}>100/80 14</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-[#0f172a] border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2] cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Locations</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product Description</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Brand</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Size</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Color</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Stock</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Reorder Level</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Compatible Models</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Status</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" class="product-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" value="{{ $product->id }}">
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $product->warehouse ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-0.5 rounded-full bg-[#105f68] text-[#00fff2] text-[11px] font-semibold">{{ $product->product_description }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $product->brand }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($product->qr_code_path)
                                                <img src="/storage/{{ $product->qr_code_path }}" alt="QR" class="qr-thumbnail rounded border border-slate-200">
                                            @endif
                                            <span class="font-mono text-xs text-slate-600">{{ $product->sku }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $product->size ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $product->color ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $product->effective_stock_quantity <= $product->effective_reorder_level ? 'bg-red-600 text-white' : 'bg-[#00fff2] text-black' }}">
                                            {{ $product->effective_stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $product->effective_reorder_level }}</td>
                                    <td class="px-4 py-3">
                                        @if($product->product_name)
                                            <div class="text-xs text-slate-900 font-semibold mb-1">{{ $product->product_name }}</div>
                                        @endif
                                        <div class="flex flex-wrap gap-1">
                                            @if($product->motorcycleModels->count() > 0)
                                                @foreach($product->motorcycleModels->take(3) as $model)
                                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-xs badge-truncate">{{ $model->full_name }}</span>
                                                @endforeach
                                                @if($product->motorcycleModels->count() > 3)
                                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-xs">+{{ $product->motorcycleModels->count() - 3 }} more</span>
                                                @endif
                                            @elseif(!$product->product_name)
                                                <span class="text-xs text-slate-400">No compatibility</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $product->status === 'Active' ? 'bg-[#105f68] text-white' : 'bg-red-600 text-white' }}">
                                            {{ $product->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center" onclick="event.stopPropagation()">
                                        <div class="action-dropdown inline-block">
                                            <button onclick="toggleDropdown({{ $product->id }})" class="p-1.5 rounded-lg hover:bg-slate-100 transition">
                                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                </svg>
                                            </button>
                                            <div id="dropdown-{{ $product->id }}" class="dropdown-menu bg-white border border-slate-200 rounded-xl shadow-lg py-1 text-xs">
                                                <a href="{{ route('product-catalog.show', $product) }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">View</a>
                                                <a href="{{ route('product-catalog.edit', $product) }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Edit</a>
                                                <form action="{{ route('product-catalog.destroy', $product) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 font-medium">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="12" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                            </svg>
                                        </div>
                                        <p class="text-slate-500">No products found.</p>
                                        <a href="{{ route('product-catalog.create') }}" class="text-[#105f68] hover:underline text-xs font-semibold">Add your first product</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-between text-xs bg-slate-50">
                    <div class="text-slate-600">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                    </div>
                    <div class="flex items-center gap-2">
                        <select name="per_page" onchange="window.location.href='{{ route('product-catalog.index') }}?per_page='+this.value" class="px-3 py-1.5 rounded-[10px] border border-slate-300 bg-white text-xs">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        {{ $products->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Toggle dropdown menu (global function)
        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== 'dropdown-' + id) {
                    menu.classList.remove('show');
                }
            });
            dropdown.classList.toggle('show');
        }

        // Clear selection (global function)
        function clearSelection() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            const selectAll = document.getElementById('selectAll');

            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            if (selectAll) selectAll.checked = false;

            const bulkActionsToolbar = document.getElementById('bulkActionsToolbar');
            const selectedCount = document.getElementById('selectedCount');
            const selectedIds = document.getElementById('selectedIds');

            if (bulkActionsToolbar) bulkActionsToolbar.style.display = 'none';
            if (selectedCount) selectedCount.textContent = '0';
            if (selectedIds) selectedIds.value = '';
        }

        // Confirm bulk delete with product list (global function)
        function confirmBulkDelete(form) {
            const selectedIds = document.getElementById('selectedIds').value;
            if (!selectedIds) return false;

            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const productNames = [];

            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const brandCell = row.querySelector('td:nth-child(4)');
                const skuCell = row.querySelector('td:nth-child(5)');
                if (brandCell && skuCell) {
                    const brand = brandCell.textContent.trim();
                    const sku = skuCell.textContent.trim();
                    productNames.push(`${brand} (${sku})`);
                }
            });

            const message = `Are you sure you want to delete the following ${checkboxes.length} product(s)?\n\n${productNames.join('\n')}`;

            return confirm(message);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const warehouseFilter = document.getElementById('warehouseFilter');
            const productDescriptionFilter = document.getElementById('productDescriptionFilter');
            const brandFilter = document.getElementById('brandFilter');
            const sizeFilter = document.getElementById('sizeFilter');

            // Auto-submit form when filters change
            function submitForm() {
                filterForm.submit();
            }

            // Search with debounce
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(submitForm, 300);
                });
            }

            // Submit on filter change
            if (warehouseFilter) {
                warehouseFilter.addEventListener('change', submitForm);
            }
            if (productDescriptionFilter) {
                productDescriptionFilter.addEventListener('change', function() {
                    updateBrandDropdown();
                    submitForm();
                });
            }
            if (brandFilter) {
                brandFilter.addEventListener('change', submitForm);
            }
            if (sizeFilter) {
                sizeFilter.addEventListener('change', submitForm);
            }

            // Store all brands from backend (already filtered by product description if selected)
            const allBrandsFromBackend = @json($brands);

            // Function to update brand dropdown based on product description
            function updateBrandDropdown() {
                const selectedOption = productDescriptionFilter.options[productDescriptionFilter.selectedIndex];
                let brands = [];

                if (selectedOption.dataset.brands) {
                    try {
                        brands = JSON.parse(selectedOption.dataset.brands);
                    } catch (e) {
                        console.error('Error parsing brands:', e);
                        brands = [];
                    }
                }

                // Clear brand dropdown
                brandFilter.innerHTML = '';

                // Always add "All Brands" option
                const allBrandsOption = document.createElement('option');
                allBrandsOption.value = '';
                allBrandsOption.textContent = 'All Brands';
                brandFilter.appendChild(allBrandsOption);

                if (productDescriptionFilter.value !== '' && brands.length > 0) {
                    // Add only brands that belong to the selected product description
                    brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        brandFilter.appendChild(option);
                    });
                } else {
                    // If no product description selected, show all brands from backend
                    allBrandsFromBackend.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        brandFilter.appendChild(option);
                    });
                }
            }

            // Initialize brand dropdown on page load
            updateBrandDropdown();

            // Re-select the current brand value if it exists
            const currentBrand = "{{ request('brand') }}";
            if (currentBrand) {
                brandFilter.value = currentBrand;
            }

            productDescriptionFilter.addEventListener('change', function() {
                updateBrandDropdown();
                brandFilter.value = '';
                this.form.submit();
            });

            brandFilter.addEventListener('change', function() {
                this.form.submit();
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.action-dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });

            // Checkbox functionality for bulk selection
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.product-checkbox');
            const bulkActionsToolbar = document.getElementById('bulkActionsToolbar');
            const selectedCount = document.getElementById('selectedCount');
            const selectedIds = document.getElementById('selectedIds');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions();
                });
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    bulkActionsToolbar.style.display = 'flex';
                    selectedCount.textContent = count;

                    const ids = Array.from(checkedBoxes).map(cb => cb.value);
                    selectedIds.value = ids.join(',');
                } else {
                    bulkActionsToolbar.style.display = 'none';
                    selectedCount.textContent = '0';
                    selectedIds.value = '';
                }

                if (selectAll) {
                    selectAll.checked = count === checkboxes.length && count > 0;
                }
            }
        });

        // ── Toast helper ─────────────────────────────────────────────────────
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const icon = type === 'success'
                ? '<svg style="width:18px;height:18px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                : '<svg style="width:18px;height:18px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            toast.innerHTML = icon + `<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOut .3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Show server-side flash messages as toasts
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
        @endif
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
        @endif

        // ── Trash badge ───────────────────────────────────────────────────────
        const TRASH_URL     = '{{ route("product-catalog.trash") }}';
        const RESTORE_URL   = '{{ url("product-catalog") }}';
        const BULK_RESTORE  = '{{ route("product-catalog.bulk-restore") }}';
        const FORCE_DELETE  = '{{ route("product-catalog.force-delete") }}';
        const CSRF          = '{{ csrf_token() }}';

        function updateTrashBadge(count) {
            const badge = document.getElementById('trashBadge');
            if (!badge) return;
            badge.textContent = count;
            badge.classList.toggle('hidden', count === 0);
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        // ── Trash modal ───────────────────────────────────────────────────────
        let trashData = [];
        let trashSelectedIds = [];

        function openTrashModal() {
            trashSelectedIds = [];
            document.getElementById('trashModal').style.display = 'flex';
            document.body.classList.add('overflow-hidden');
            loadTrashItems();
        }

        function closeTrashModal() {
            document.getElementById('trashModal').style.display = 'none';
            document.body.classList.remove('overflow-hidden');
        }

        async function loadTrashItems() {
            const tbody = document.getElementById('trashTableBody');
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-sm">Loading…</td></tr>';
            try {
                const res = await fetch(TRASH_URL);
                trashData = await res.json();
                renderTrashTable();
                updateTrashBadge(trashData.length);
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-red-500 text-sm">Failed to load trash.</td></tr>';
            }
        }

        function renderTrashTable() {
            const tbody = document.getElementById('trashTableBody');
            const countEl = document.getElementById('trashItemCount');
            if (countEl) countEl.textContent = trashData.length;

            if (trashData.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="4" class="px-4 py-10 text-center">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:.5rem;color:#94a3b8">
                            <svg style="width:40px;height:40px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span style="font-size:.875rem">Trash is empty</span>
                        </div>
                    </td></tr>`;
                updateTrashActions();
                return;
            }

            tbody.innerHTML = trashData.map(p => `
                <tr class="hover:bg-red-50/40">
                    <td class="px-4 py-2">
                        <input type="checkbox" class="trash-checkbox rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]" value="${p.id}" ${trashSelectedIds.includes(p.id) ? 'checked' : ''}>
                    </td>
                    <td class="px-4 py-2"><span class="px-2.5 py-0.5 rounded-full bg-[#105f68] text-[#00fff2] text-[11px] font-semibold">${p.product_description}</span></td>
                    <td class="px-4 py-2 font-medium text-slate-700">${p.brand}</td>
                    <td class="px-4 py-2 font-mono text-xs text-slate-400">${p.sku}</td>
                </tr>`).join('');

            document.querySelectorAll('.trash-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = parseInt(this.value);
                    if (this.checked) { if (!trashSelectedIds.includes(id)) trashSelectedIds.push(id); }
                    else { trashSelectedIds = trashSelectedIds.filter(x => x !== id); }
                    updateTrashActions();
                });
            });

            document.getElementById('selectAllTrash').addEventListener('change', function() {
                document.querySelectorAll('.trash-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                    const id = parseInt(cb.value);
                    if (this.checked) { if (!trashSelectedIds.includes(id)) trashSelectedIds.push(id); }
                    else { trashSelectedIds = trashSelectedIds.filter(x => x !== id); }
                });
                updateTrashActions();
            });

            updateTrashActions();
        }

        function updateTrashActions() {
            const count = trashSelectedIds.length;
            const el = document.getElementById('trashSelectedCount');
            if (el) el.textContent = count;
            const restoreBtn = document.getElementById('restoreSelectedBtn');
            const deleteBtn  = document.getElementById('permanentDeleteBtn');
            if (restoreBtn) restoreBtn.disabled = count === 0;
            if (deleteBtn)  deleteBtn.disabled  = count === 0;
        }

        async function restoreSelected() {
            if (trashSelectedIds.length === 0) return;
            const res = await fetch(BULK_RESTORE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF },
                body: new URLSearchParams({ ids: trashSelectedIds.join(',') })
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message, 'success');
                trashSelectedIds = [];
                await loadTrashItems();
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message, 'error');
            }
        }

        async function permanentDeleteSelected() {
            if (trashSelectedIds.length === 0) return;
            if (!confirm(`Permanently delete ${trashSelectedIds.length} product(s)? This CANNOT be undone.`)) return;
            const res = await fetch(FORCE_DELETE, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': CSRF },
                body: new URLSearchParams({ ids: trashSelectedIds.join(',') })
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message, 'success');
                trashSelectedIds = [];
                await loadTrashItems();
            } else {
                showToast(data.message, 'error');
            }
        }

        // Fetch trash count on page load to show badge
        document.addEventListener('DOMContentLoaded', () => {
            fetch(TRASH_URL).then(r => r.json()).then(data => updateTrashBadge(data.length)).catch(() => {});
        });
    </script>

    <!-- Trash / Restore Modal -->
    <div id="trashModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeTrashModal()"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all z-10">
            <div class="px-6 py-5 bg-[#0f172a] relative flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Trash
                        <span class="text-xs font-normal text-slate-300">(<span id="trashItemCount">0</span> items)</span>
                    </h3>
                    <p class="text-xs text-slate-300">Restore items back to the catalog, or permanently delete them.</p>
                </div>
                <button onclick="closeTrashModal()" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">&times;</button>
            </div>

            <div class="p-6">
                <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-[#0f172a] border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white w-10">
                                    <input type="checkbox" id="selectAllTrash" class="rounded border-slate-300 text-[#00fff2] focus:ring-[#00fff2]">
                                </th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Description</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Brand</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
                            </tr>
                        </thead>
                        <tbody id="trashTableBody" class="divide-y divide-slate-100">
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-sm">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-600"><span id="trashSelectedCount">0</span> selected</span>
                    <div class="flex items-center gap-3">
                        <button onclick="closeTrashModal()" class="px-4 py-2 rounded-lg border-2 border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">Close</button>
                        <button id="restoreSelectedBtn"
                            onclick="restoreSelected()"
                            disabled
                            class="px-4 py-2 rounded-lg border border-[#00fff2]/40 bg-[#105f68] text-[#00fff2] text-xs font-semibold hover:bg-[#0d4f57] transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Restore Selected
                        </button>
                        <button id="permanentDeleteBtn"
                            onclick="permanentDeleteSelected()"
                            disabled
                            class="px-4 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Delete Forever
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
