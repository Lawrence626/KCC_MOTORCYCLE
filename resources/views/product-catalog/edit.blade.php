<x-layouts.app :title="__('Edit Product')">
    <style>
        .motorcycle-group { max-height: 300px; overflow-y: auto; }
        .motorcycle-group::-webkit-scrollbar { width: 6px; }
        .motorcycle-group::-webkit-scrollbar-track { background: #f1f5f9; }
        .motorcycle-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .qr-thumbnail { width: 180px; height: 180px; }
    </style>

    <div class="space-y-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-1 py-1 mb-2">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Edit Product</h1>
                <p class="text-sm text-slate-500 mt-1">Update product details, SKU, and motorcycle compatibility.</p>
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
            <form action="{{ route('product-catalog.update', $productCatalog) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column - Form Fields -->
                        <div class="lg:col-span-2 space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Warehouse</label>
                                <input type="text" 
                                       name="warehouse" 
                                       value="{{ old('warehouse', $productCatalog->warehouse) }}" 
                                       placeholder="e.g., Warehouse A" 
                                       class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                @error('warehouse')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Product Description <span class="text-red-500">*</span></label>
                                <select name="product_description" 
                                        id="productDescriptionSelect"
                                        class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm" 
                                        required>
                                    <option value="">Select product description</option>
                                    @foreach($productDescriptions as $description)
                                        <option value="{{ $description->name }}" 
                                                data-brands="{{ json_encode($description->brands) }}"
                                                {{ old('product_description', $productCatalog->product_description) == $description->name ? 'selected' : '' }}>
                                            {{ $description->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    <option value="">Select brand</option>
                                </select>
                                @error('brand')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Product Name (Optional)</label>
                                <input type="text" 
                                       name="product_name" 
                                       value="{{ old('product_name', $productCatalog->product_name) }}" 
                                       placeholder="e.g., Additional product name" 
                                       class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                @error('product_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Size</label>
                                    <input type="text" 
                                           name="size" 
                                           value="{{ old('size', $productCatalog->size) }}" 
                                           placeholder="e.g., L, XL, 14 inch" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('size')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Color</label>
                                    <input type="text" 
                                           name="color" 
                                           value="{{ old('color', $productCatalog->color) }}" 
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
                                           value="{{ old('stock_quantity', $productCatalog->stock_quantity ?? 0) }}" 
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
                                           value="{{ old('reorder_level', $productCatalog->reorder_level ?? 10) }}" 
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
                                           value="{{ old('sku', $productCatalog->sku) }}" 
                                           placeholder="Auto-generated or enter manually" 
                                           class="flex-1 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#6EC1D1] focus:border-transparent font-mono transition" 
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
                                          class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">{{ old('description', $productCatalog->description) }}</textarea>
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
                                           value="{{ old('manufacturing_date', $productCatalog->manufacturing_date ? $productCatalog->manufacturing_date->format('Y-m-d') : '') }}" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('manufacturing_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Batch/Lot Number <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           name="batch_lot_number" 
                                           value="{{ old('batch_lot_number', $productCatalog->batch_lot_number) }}" 
                                           placeholder="e.g., LOT-2024-001" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('batch_lot_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Expiration Date <span class="text-red-500">*</span></label>
                                    <input type="date" 
                                           name="expiration_date" 
                                           value="{{ old('expiration_date', $productCatalog->expiration_date ? $productCatalog->expiration_date->format('Y-m-d') : '') }}" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                    @error('expiration_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="statusSelect" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm h-10" required>
                                    <option value="Active" {{ old('status', $productCatalog->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-2 block">Compatible Motorcycle Models</label>
                                <div class="border border-slate-300 rounded-[12px] p-4">
                                    <!-- Existing Compatible Models -->
                                    <div class="mb-4">
                                        <p class="text-xs font-semibold text-slate-600 mb-2">Currently Compatible:</p>
                                        <div id="existingCompatibleModels" class="space-y-2">
                                            @if(isset($productCatalog->motorcycleModels) && count($productCatalog->motorcycleModels) > 0)
                                                @foreach($productCatalog->motorcycleModels as $model)
                                                    <div class="flex items-center justify-between p-2 bg-slate-50 rounded-[10px] border border-slate-200" data-model-id="{{ $model->id }}">
                                                        <span class="text-xs text-slate-700 font-medium">{{ $model->brand }} {{ $model->model_name }}</span>
                                                        <button type="button" class="remove-compatible-btn text-red-500 hover:text-red-700 text-xs font-semibold" data-model-id="{{ $model->id }}">
                                                            Remove
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-xs text-slate-400 italic">No compatible models set</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Add New Compatible Model -->
                                    <div class="border-t border-slate-200 pt-4">
                                        <p class="text-xs font-semibold text-slate-600 mb-2">Add New Compatible Model:</p>
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                   id="addCompatibleModelInput" 
                                                   placeholder="Enter motorcycle model name..." 
                                                   class="flex-1 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm">
                                            <button type="button" id="addCompatibleBtn" class="px-4 py-2 rounded-[12px] bg-[#0f172a] text-white text-xs font-semibold hover:bg-[#1e293b] transition">
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden input to store selected model IDs -->
                                    <input type="hidden" name="motorcycle_models" id="motorcycleModelsInput" value="{{ isset($productCatalog->motorcycleModels) ? $productCatalog->motorcycleModels->pluck('id')->implode(',') : '' }}">
                                    
                                    <p class="text-xs text-slate-400 mt-2">Type a motorcycle model name and click Add to include it as compatible.</p>
                                </div>
                                @error('motorcycle_models')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column - QR Code -->
                        <div class="lg:col-span-1">
                            <div class="bg-[#0f172a] rounded-[12px] p-4 border border-slate-800 shadow-sm">
                                <h3 class="text-xs font-bold text-white mb-3">QR Code</h3>
                                @if($productCatalog->qr_code_path)
                                    <div class="flex items-center justify-center mb-4">
                                        <img src="{{ asset('storage/' . $productCatalog->qr_code_path) }}" alt="QR Code" class="qr-thumbnail rounded-[12px] border border-slate-700 bg-white">
                                    </div>
                                    <div class="space-y-2">
                                        <a href="{{ route('product-catalog.download-qr', $productCatalog) }}" class="block w-full text-center px-4 py-2 rounded-[10px] bg-slate-800 border border-slate-700 text-white text-xs font-semibold hover:bg-slate-700 transition">
                                            Download QR
                                        </a>
                                        <a href="{{ route('product-catalog.print-qr', $productCatalog) }}" target="_blank" class="block w-full text-center px-4 py-2 rounded-[10px] bg-slate-800 border border-slate-700 text-white text-xs font-semibold hover:bg-slate-700 transition">
                                            Print QR
                                        </a>
                                        <form action="{{ route('product-catalog.regenerate-qr', $productCatalog) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to regenerate the QR code?');">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="w-full px-4 py-2 rounded-[10px] bg-slate-800 border border-slate-700 text-white text-xs font-semibold hover:bg-slate-700 transition">
                                                Regenerate QR
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center mb-4">
                                        <div class="w-32 h-32 bg-white rounded-[12px] border border-slate-200 flex items-center justify-center">
                                            <span class="text-xs text-slate-400">No QR Code</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 text-center">QR code will be generated when you save the product.</p>
                                @endif
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
                        Update Product
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
            const currentBrand = "{{ $productCatalog->brand }}";
            const expirationFields = document.getElementById('expirationFields');
            const addCompatibleModelInput = document.getElementById('addCompatibleModelInput');
            const addCompatibleBtn = document.getElementById('addCompatibleBtn');
            const existingCompatibleModels = document.getElementById('existingCompatibleModels');
            const motorcycleModelsInput = document.getElementById('motorcycleModelsInput');

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

            // Initialize brand dropdown based on current product description
            function initializeBrandDropdown() {
                const selectedOption = productDescriptionSelect.options[productDescriptionSelect.selectedIndex];
                const brands = selectedOption.dataset.brands ? JSON.parse(selectedOption.dataset.brands) : [];
                
                // Clear brand dropdown
                brandSelect.innerHTML = '<option value="">Select brand</option>';
                
                if (brands.length > 0) {
                    brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        if (brand === currentBrand) {
                            option.selected = true;
                        }
                        brandSelect.appendChild(option);
                    });
                } else {
                    brandSelect.innerHTML = '<option value="">No brands available</option>';
                }
            }

            // Initialize on page load
            initializeBrandDropdown();
            toggleExpirationFields();

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
                
                // Reset brand selection if current brand is not in new list
                if (brands.length > 0 && !brands.includes(currentBrand)) {
                    brandSelect.value = '';
                }
                
                // Toggle expiration fields
                toggleExpirationFields();
            });

            // Generate SKU
            generateSkuBtn.addEventListener('click', async function() {
                const productDescription = productDescriptionSelect.value;
                if (!productDescription) {
                    alert('Please select a product description first');
                    return;
                }

                try {
                    const response = await fetch(`{{ route('product-catalog.generate-sku') }}?product_description=${encodeURIComponent(productDescription)}`);
                    const data = await response.json();
                    if (data.sku) {
                        skuInput.value = data.sku;
                    } else if (data.error) {
                        alert(data.error);
                    }
                } catch (error) {
                    console.error('Error generating SKU:', error);
                    alert('Failed to generate SKU');
                }
            });

            // Add compatible model from text input
            addCompatibleBtn.addEventListener('click', function() {
                const modelName = addCompatibleModelInput.value.trim();

                if (!modelName) {
                    alert('Please enter a motorcycle model name');
                    return;
                }

                // Check if already added
                const existingModels = existingCompatibleModels.querySelectorAll('[data-model-name]');
                for (let model of existingModels) {
                    if (model.dataset.modelName.toLowerCase() === modelName.toLowerCase()) {
                        alert('This model is already added');
                        return;
                    }
                }

                // Add to existing list
                const modelDiv = document.createElement('div');
                modelDiv.className = 'flex items-center justify-between p-2 bg-slate-50 rounded-[10px] border border-slate-200';
                modelDiv.dataset.modelName = modelName;
                modelDiv.dataset.isNew = 'true';
                modelDiv.innerHTML = `
                    <span class="text-xs text-slate-700 font-medium">${modelName}</span>
                    <button type="button" class="remove-compatible-btn text-red-500 hover:text-red-700 text-xs font-semibold" data-model-name="${modelName}">
                        Remove
                    </button>
                `;
                existingCompatibleModels.appendChild(modelDiv);

                // Update hidden input
                updateMotorcycleModelsInput();

                // Clear input
                addCompatibleModelInput.value = '';
            });

            // Remove compatible model (using event delegation)
            existingCompatibleModels.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-compatible-btn')) {
                    const modelDiv = e.target.closest('[data-model-id]') || e.target.closest('[data-model-name]');
                    if (modelDiv) {
                        modelDiv.remove();
                        updateMotorcycleModelsInput();
                    }
                }
            });

            // Update hidden input with selected model IDs and names
            function updateMotorcycleModelsInput() {
                const modelIds = [];
                const modelNames = [];
                
                existingCompatibleModels.querySelectorAll('[data-model-id]').forEach(div => {
                    modelIds.push(div.dataset.modelId);
                });
                
                existingCompatibleModels.querySelectorAll('[data-is-new="true"]').forEach(div => {
                    modelNames.push(div.dataset.modelName);
                });
                
                // Combine existing IDs and new names
                const combined = [...modelIds, ...modelNames];
                motorcycleModelsInput.value = combined.join(',');
            }
        });
    </script>
</x-layouts.app>
