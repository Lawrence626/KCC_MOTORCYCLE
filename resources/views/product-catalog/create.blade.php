<x-layouts.app :title="__('Add Product')">
    <style>
        .motorcycle-group { max-height: 300px; overflow-y: auto; }
        .motorcycle-group::-webkit-scrollbar { width: 6px; }
        .motorcycle-group::-webkit-scrollbar-track { background: #f1f5f9; }
        .motorcycle-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .qr-preview { width: 180px; height: 180px; }
    </style>

    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-1 py-1 mb-2">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Add New Product</h1>
                <p class="text-sm text-slate-500 mt-1">Create a new product with SKU generation, QR code, and motorcycle compatibility.</p>
            </div>
            <div>
                <a href="{{ route('product-catalog.index') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Products
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-[20px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <form action="{{ route('product-catalog.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column - Form Fields -->
                        <div class="lg:col-span-2 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Warehouse</label>
                                <input type="text" 
                                       name="warehouse" 
                                       value="{{ old('warehouse') }}" 
                                       placeholder="e.g., Warehouse A" 
                                       class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                @error('warehouse')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Product Description <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <select name="product_description" 
                                            id="productDescriptionSelect"
                                            class="flex-1 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm" 
                                            required>
                                        <option value="">Select product description</option>
                                        @foreach($productDescriptions as $description)
                                            <option value="{{ $description->name }}" 
                                                    data-brands="{{ json_encode($description->brands) }}"
                                                    {{ old('product_description') == $description->name ? 'selected' : '' }}>
                                                {{ $description->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" 
                                            id="addNewProductDescBtn"
                                            class="shrink-0 h-10 px-4 rounded-[12px] bg-[#6EC1D1] text-slate-900 text-xs font-bold hover:bg-[#59b2c2] transition shadow-sm inline-flex items-center justify-center text-center leading-none whitespace-nowrap gap-1">
                                        <span class="text-sm font-black">+</span><span>New</span>
                                    </button>
                                </div>
                                @error('product_description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Brand <span class="text-red-500">*</span></label>
                                <select name="brand" 
                                        id="brandSelect"
                                        class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm" 
                                        required>
                                    <option value="">Select product description first</option>
                                </select>
                                @error('brand')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Product Name (Optional)</label>
                                <input type="text" 
                                       name="product_name" 
                                       value="{{ old('product_name') }}" 
                                       placeholder="e.g., Additional product name" 
                                       class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                @error('product_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Size (Optional)</label>
                                    <input type="text" 
                                           name="size" 
                                           value="{{ old('size') }}" 
                                           placeholder="e.g., L, XL, 14 inch" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('size')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Color (Optional)</label>
                                    <input type="text" 
                                           name="color" 
                                           value="{{ old('color') }}" 
                                           placeholder="e.g., Black, Red, Blue" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('color')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Stock Quantity</label>
                                    <input type="number" 
                                           name="stock_quantity" 
                                           value="{{ old('stock_quantity') ?? 0 }}" 
                                           min="0" 
                                           placeholder="0" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('stock_quantity')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Reorder Level</label>
                                    <input type="number" 
                                           name="reorder_level" 
                                           value="{{ old('reorder_level') ?? 10 }}" 
                                           min="0" 
                                           placeholder="10" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('reorder_level')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">SKU <span class="text-red-500">*</span></label>
                                <div class="flex gap-2">
                                    <input type="text" 
                                           name="sku" 
                                           id="skuInput"
                                           value="{{ old('sku') }}" 
                                           placeholder="Auto-generated or enter manually" 
                                           class="flex-1 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 font-mono transition shadow-sm" 
                                           required>
                                    <button type="button" 
                                            id="generateSkuBtn"
                                            class="shrink-0 h-10 px-4 rounded-[12px] bg-[#0f172a] text-white text-xs font-semibold hover:bg-[#1e293b] transition shadow-sm flex items-center justify-center whitespace-nowrap">
                                        Generate SKU
                                    </button>
                                </div>
                                @error('sku')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Description</label>
                                <textarea name="description" 
                                          rows="3" 
                                          placeholder="Product description (optional)" 
                                          class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expiration Fields - Only for expirable products -->
                            <div id="expirationFields" class="hidden space-y-4 bg-amber-50/50 p-4 rounded-[16px] border border-amber-200">
                                <h4 class="text-xs font-bold text-amber-900 mb-2">Expiration Information</h4>
                                
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Manufacturing Date (Optional)</label>
                                    <input type="date" 
                                           name="manufacturing_date" 
                                           value="{{ old('manufacturing_date') }}" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('manufacturing_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Batch/Lot Number (Optional)</label>
                                    <input type="text" 
                                           name="batch_lot_number" 
                                           value="{{ old('batch_lot_number') }}" 
                                           placeholder="e.g., LOT-2024-001" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('batch_lot_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Expiration Date (Optional)</label>
                                    <input type="date" 
                                           name="expiration_date" 
                                           value="{{ old('expiration_date') }}" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('expiration_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="statusSelect" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm h-10" required>
                                    <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-2 block">Compatible Motorcycle Models <span class="text-red-500">*</span></label>
                                <div class="border border-slate-300 rounded-[12px] p-4 motorcycle-group">
                                    @foreach($motorcycles as $brand => $models)
                                        <div class="mb-4">
                                            <h4 class="text-xs font-bold text-slate-900 mb-2">{{ $brand }}</h4>
                                            <div class="space-y-2">
                                                @foreach($models as $model)
                                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 p-1 rounded-lg">
                                                        <input type="checkbox" 
                                                               name="motorcycle_models[]" 
                                                               value="{{ $model->id }}" 
                                                               class="rounded border-slate-300 text-[#6EC1D1] focus:ring-[#6EC1D1]">
                                                        <span class="text-xs text-slate-700">{{ $model->full_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('motorcycle_models')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column - QR Preview -->
                        <div class="lg:col-span-1">
                            <div class="bg-[#0f172a] rounded-[12px] p-4 border border-slate-800 shadow-sm">
                                <h3 class="text-xs font-bold text-white mb-3">QR Code Preview</h3>
                                <div class="flex items-center justify-center mb-4">
                                    <div id="qrPreview" class="qr-preview bg-white rounded-[12px] border border-slate-700 flex items-center justify-center">
                                        <span class="text-xs text-slate-400">Enter SKU to preview</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-300 text-center">QR code will be automatically generated when you save the product.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-start gap-3">
                    <a href="{{ route('product-catalog.index') }}" class="rounded-[10px] bg-black/10 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-black/20 transition">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-4 py-2 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add New Product Description Modal (Matching Add User Modal design) -->
    <div id="addProductDescModal" class="hidden fixed inset-0 z-[10000] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" id="closeProductDescModalBackdrop"></div>
        <div class="relative w-full max-w-md bg-white rounded-[28px] border border-slate-200 shadow-[0_30px_80px_rgba(15,23,42,0.18)] overflow-hidden transform transition-all z-10">
            <!-- Header (matching Add User Modal style) -->
            <div class="flex items-center justify-between border-b border-[#6EC1D1] bg-[#6EC1D1] px-6 py-5">
                <div>
                    <h3 class="text-xl font-bold text-black">Add New Product Description</h3>
                    <p class="text-sm text-slate-900 font-medium mt-0.5">Enter description name and default brand.</p>
                </div>
                <button type="button" id="closeProductDescModal" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="addProductDescForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Product Description Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="newProductDescName" 
                           name="name" 
                           placeholder="e.g., Brake Pads" 
                           class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" 
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Brand <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="newProductDescBrand" 
                           name="brand" 
                           placeholder="e.g., Bosch" 
                           class="w-full rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 placeholder:text-slate-400 shadow-sm hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35" 
                           required>
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" id="cancelProductDesc" class="rounded-[10px] bg-black/10 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-black/20 transition flex-1">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-[10px] bg-[#6EC1D1] px-4 py-2 text-sm font-bold text-slate-900 shadow-sm hover:bg-[#59b2c2] transition flex-1">
                        Add Description
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupCustomSelectDropdown(selectId, placeholder) {
                const select = document.getElementById(selectId);
                if (!select || select.dataset.customized === 'true') return;
                select.dataset.customized = 'true';
                select.classList.add('hidden');

                const wrapper = select.parentElement;
                wrapper.classList.add('relative', 'flex-1');

                const button = document.createElement('button');
                button.type = 'button';
                button.id = selectId + 'Button';
                button.className = 'custom-select-button w-full h-10 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm';
                button.innerHTML = `
                    <span class="custom-select-label truncate"></span>
                    <svg class="w-4 h-4 text-slate-500 shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/>
                    </svg>
                `;
                wrapper.insertBefore(button, select);

                const panel = document.createElement('div');
                panel.className = 'custom-select-panel hidden absolute left-0 top-full z-[999] mt-2 w-full max-h-60 overflow-y-auto rounded-[12px] border border-slate-200 bg-white p-3 space-y-1 shadow-xl';
                wrapper.appendChild(panel);

                const labelSpan = button.querySelector('.custom-select-label');

                function renderOptions() {
                    panel.innerHTML = '';
                    Array.from(select.options).forEach((opt) => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.dataset.value = opt.value;
                        const isSelected = opt.value === select.value;
                        item.className = 'custom-select-item w-full rounded-[10px] px-3 py-2 text-left text-xs transition-colors duration-100 ' +
                            (isSelected
                                ? 'bg-slate-100 font-semibold text-slate-900'
                                : 'text-slate-700 hover:bg-slate-100');
                        item.textContent = opt.textContent;
                        item.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (select.value !== opt.value) {
                                select.value = opt.value;
                                select.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            updateButtonLabel();
                            closePanel();
                        });
                        panel.appendChild(item);
                    });
                }

                function updateButtonLabel() {
                    const selectedOption = select.options[select.selectedIndex];
                    labelSpan.textContent = selectedOption ? selectedOption.textContent : (placeholder || '');
                }

                function openPanel() {
                    document.querySelectorAll('.custom-select-panel').forEach((p) => {
                        if (p !== panel) p.classList.add('hidden');
                    });
                    renderOptions();
                    panel.classList.remove('hidden');
                }

                function closePanel() {
                    panel.classList.add('hidden');
                }

                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (panel.classList.contains('hidden')) {
                        openPanel();
                    } else {
                        closePanel();
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) closePanel();
                });

                const observer = new MutationObserver(() => {
                    updateButtonLabel();
                    if (!panel.classList.contains('hidden')) renderOptions();
                });
                observer.observe(select, { childList: true, subtree: true, attributes: true });

                updateButtonLabel();
            }

            setupCustomSelectDropdown('productDescriptionSelect', 'Select product description');
            setupCustomSelectDropdown('brandSelect', 'Select brand');
            setupCustomSelectDropdown('statusSelect', 'Active');

            const productDescriptionSelect = document.getElementById('productDescriptionSelect');
            const brandSelect = document.getElementById('brandSelect');
            const skuInput = document.getElementById('skuInput');
            const generateSkuBtn = document.getElementById('generateSkuBtn');
            const qrPreview = document.getElementById('qrPreview');
            const expirationFields = document.getElementById('expirationFields');
            const addNewProductDescBtn = document.getElementById('addNewProductDescBtn');
            const addProductDescModal = document.getElementById('addProductDescModal');
            const closeProductDescModal = document.getElementById('closeProductDescModal');
            const cancelProductDesc = document.getElementById('cancelProductDesc');
            const addProductDescForm = document.getElementById('addProductDescForm');
            const newProductDescName = document.getElementById('newProductDescName');
            const newProductDescBrand = document.getElementById('newProductDescBrand');

            // Expirable product descriptions
            const expirableDescriptions = [
                'ENGINE OIL',
                'BRAKE FLUID (BRAKE OIL)',
                'GEAR OIL',
                'COOLANT / RADIATOR COOLANT',
                'CVT CLEANER',
                'TIRE SEALANT'
            ];

            // Function to show/hide expiration fields
            function toggleExpirationFields() {
                const selectedDescription = productDescriptionSelect.value.toUpperCase();
                if (expirableDescriptions.includes(selectedDescription)) {
                    expirationFields.classList.remove('hidden');
                } else {
                    expirationFields.classList.add('hidden');
                }
            }

            // Update brand dropdown when product description changes
            productDescriptionSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const brands = selectedOption.dataset.brands ? JSON.parse(selectedOption.dataset.brands) : [];
                
                // Clear brand dropdown
                brandSelect.innerHTML = '<option value="">Select brand</option>';
                
                if (brands.length > 0) {
                    brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        brandSelect.appendChild(option);
                    });
                } else {
                    brandSelect.innerHTML = '<option value="">No brands available</option>';
                }
                
                // Reset brand selection
                brandSelect.value = '';
                
                // Toggle expiration fields
                toggleExpirationFields();
            });

            // Generate SKU helper
            async function generateSku() {
                const productDescription = productDescriptionSelect.value;
                const brand = brandSelect.value;

                if (!productDescription) {
                    alert('Please select a product description first.');
                    return;
                }
                if (!brand) {
                    alert('Please select a brand first.');
                    return;
                }

                try {
                    const url = `{{ route('product-catalog.generate-sku') }}?product_description=${encodeURIComponent(productDescription)}&brand=${encodeURIComponent(brand)}`;
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data.sku) {
                        skuInput.value = data.sku;
                        updateQrPreview(data.sku);
                    } else if (data.error) {
                        alert(data.error);
                    }
                } catch (error) {
                    console.error('Error generating SKU:', error);
                    alert('Failed to generate SKU. Please try again.');
                }
            }

            // Auto-generate SKU when brand changes (if description is already selected)
            brandSelect.addEventListener('change', function() {
                if (productDescriptionSelect.value && this.value) {
                    generateSku();
                }
            });

            // Generate SKU button
            generateSkuBtn.addEventListener('click', generateSku);

            // Update QR preview on SKU change
            skuInput.addEventListener('input', function() {
                updateQrPreview(this.value);
            });

            function updateQrPreview(sku) {
                if (!sku) {
                    qrPreview.innerHTML = '<span class="text-xs text-slate-400">Enter SKU to preview</span>';
                    return;
                }
                
                // Use QRCode.js for preview
                qrPreview.innerHTML = '';
                const qr = new QRCode(qrPreview, {
                    text: sku,
                    width: 180,
                    height: 180,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            // Add New Product Description Modal
            addNewProductDescBtn.addEventListener('click', function() {
                document.querySelectorAll('.custom-select-panel').forEach(p => p.classList.add('hidden'));
                addProductDescModal.classList.remove('hidden');
                newProductDescName.focus();
            });

            const closeProductDescModalBackdrop = document.getElementById('closeProductDescModalBackdrop');
            if (closeProductDescModalBackdrop) {
                closeProductDescModalBackdrop.addEventListener('click', function() {
                    addProductDescModal.classList.add('hidden');
                    addProductDescForm.reset();
                });
            }

            closeProductDescModal.addEventListener('click', function() {
                addProductDescModal.classList.add('hidden');
                addProductDescForm.reset();
            });

            cancelProductDesc.addEventListener('click', function() {
                addProductDescModal.classList.add('hidden');
                addProductDescForm.reset();
            });

            addProductDescForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const name = newProductDescName.value.trim();
                const brand = newProductDescBrand.value.trim();
                
                if (!name || !brand) {
                    alert('Please fill in all fields');
                    return;
                }

                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    formData.append('name', name);
                    formData.append('brand', brand);

                    const response = await fetch('{{ route('product-descriptions.store') }}', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Add new option to product description select
                        const option = document.createElement('option');
                        option.value = data.product_description.name;
                        option.textContent = data.product_description.name;
                        option.dataset.brands = JSON.stringify(data.product_description.brands);
                        productDescriptionSelect.appendChild(option);
                        
                        // Select the new option
                        productDescriptionSelect.value = data.product_description.name;
                        
                        // Update brand dropdown with the new brand
                        brandSelect.innerHTML = '<option value="">Select brand</option>';
                        data.product_description.brands.forEach(b => {
                            const brandOption = document.createElement('option');
                            brandOption.value = b;
                            brandOption.textContent = b;
                            brandSelect.appendChild(brandOption);
                        });
                        
                        // Select the new brand
                        brandSelect.value = brand;
                        
                        // Close modal and reset form
                        addProductDescModal.classList.add('hidden');
                        addProductDescForm.reset();
                        
                        // Trigger brand change to auto-generate SKU
                        brandSelect.dispatchEvent(new Event('change'));
                        
                        alert('Product description added successfully!');
                    } else {
                        alert(data.error || 'Failed to add product description');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to add product description. Please try again.');
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</x-layouts.app>
