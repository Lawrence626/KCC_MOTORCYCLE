<x-layouts.app :title="__('Edit Product')">
    <style>
        .motorcycle-group { max-height: 300px; overflow-y: auto; }
        .motorcycle-group::-webkit-scrollbar { width: 6px; }
        .motorcycle-group::-webkit-scrollbar-track { background: #f1f5f9; }
        .motorcycle-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .qr-thumbnail { width: 150px; height: 150px; }
    </style>

    <div class="max-w-4xl mx-auto space-y-4">
        <div class="mb-2">
            <a href="{{ route('product-catalog.index') }}" class="inline-flex items-center gap-1.5 text-[#105f68] hover:underline text-xs font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Products
            </a>
        </div>

        <!-- Page Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Edit Product</h1>
                    <p class="mt-1 text-xs text-slate-500">Update product details, SKU, and motorcycle compatibility.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-[12px] bg-[#00fff2]/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                </div>
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
                                       class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
                                @error('warehouse')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Product Description <span class="text-red-500">*</span></label>
                                <select name="product_description" 
                                        id="productDescriptionSelect"
                                        class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition" 
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
                                        class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition"
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
                                       class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                                           class="flex-1 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent font-mono transition" 
                                           required>
                                    <button type="button" 
                                            id="generateSkuBtn"
                                            class="px-4 py-2 rounded-[12px] bg-slate-100 border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
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
                                          class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">{{ old('description', $productCatalog->description) }}</textarea>
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
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
                                    @error('batch_lot_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1 block">Expiration Date <span class="text-red-500">*</span></label>
                                    <input type="date" 
                                           name="expiration_date" 
                                           value="{{ old('expiration_date', $productCatalog->expiration_date ? $productCatalog->expiration_date->format('Y-m-d') : '') }}" 
                                           class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
                                    @error('expiration_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-1 block">Status <span class="text-red-500">*</span></label>
                                <select name="status" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition" required>
                                    <option value="Active" {{ old('status', $productCatalog->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600 mb-2 block">Compatible Motorcycle Models</label>
                                <div class="border border-slate-300 rounded-[16px] p-4">
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
                                                   class="flex-1 px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent transition">
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
                            <div class="bg-slate-50 rounded-[18px] p-4 border border-slate-200">
                                <h3 class="text-xs font-bold text-slate-900 mb-3">QR Code</h3>
                                @if($productCatalog->qr_code_path)
                                    <div class="flex items-center justify-center mb-4">
                                        <img src="{{ asset('storage/' . $productCatalog->qr_code_path) }}" alt="QR Code" class="qr-thumbnail rounded-[12px] border border-slate-200">
                                    </div>
                                    <div class="space-y-2">
                                        <a href="{{ route('product-catalog.download-qr', $productCatalog) }}" class="block w-full text-center px-4 py-2 rounded-[10px] bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition">
                                            Download QR
                                        </a>
                                        <a href="{{ route('product-catalog.print-qr', $productCatalog) }}" target="_blank" class="block w-full text-center px-4 py-2 rounded-[10px] bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition">
                                            Print QR
                                        </a>
                                        <form action="{{ route('product-catalog.regenerate-qr', $productCatalog) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to regenerate the QR code?');">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="w-full px-4 py-2 rounded-[10px] bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition">
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
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 rounded-full bg-[#00fff2] text-black text-xs font-semibold hover:bg-[#00e6da] transition shadow-sm">
                        Update Product
                    </button>
                    <a href="{{ route('product-catalog.index') }}" class="px-5 py-2 rounded-full border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-white transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
