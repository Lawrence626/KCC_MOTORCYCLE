<x-layouts.app :title="__('Add Product')">
    <style>
        .motorcycle-group { max-height: 300px; overflow-y: auto; }
        .motorcycle-group::-webkit-scrollbar { width: 6px; }
        .motorcycle-group::-webkit-scrollbar-track { background: #f1f5f9; }
        .motorcycle-group::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .qr-preview { width: 150px; height: 150px; }
    </style>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('product-catalog.index') }}" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">← Back to Products</a>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Add New Product</h1>
                <p class="text-sm text-slate-500 mt-1">Create a new product with SKU generation, QR code, and motorcycle compatibility.</p>
            </div>

            <form action="{{ route('product-catalog.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Warehouse</label>
                            <input type="text" 
                                   name="warehouse" 
                                   value="{{ old('warehouse') }}" 
                                   placeholder="e.g., Warehouse A" 
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            @error('warehouse')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Product Description <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <select name="product_description" 
                                        id="productDescriptionSelect"
                                        class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
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
                                        class="px-3 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                                    + New
                                </button>
                            </div>
                            @error('product_description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Brand <span class="text-red-500">*</span></label>
                            <select name="brand" 
                                    id="brandSelect"
                                    class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                                    required>
                                <option value="">Select product description first</option>
                            </select>
                            @error('brand')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Product Name (Optional)</label>
                            <input type="text" 
                                   name="product_name" 
                                   value="{{ old('product_name') }}" 
                                   placeholder="e.g., Additional product name" 
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                            @error('product_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Size (Optional)</label>
                                <input type="text" 
                                       name="size" 
                                       value="{{ old('size') }}" 
                                       placeholder="e.g., L, XL, 14 inch" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('size')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Color (Optional)</label>
                                <input type="text" 
                                       name="color" 
                                       value="{{ old('color') }}" 
                                       placeholder="e.g., Black, Red, Blue" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('color')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Stock Quantity</label>
                                <input type="number" 
                                       name="stock_quantity" 
                                       value="{{ old('stock_quantity') ?? 0 }}" 
                                       min="0" 
                                       placeholder="0" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('stock_quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Reorder Level</label>
                                <input type="number" 
                                       name="reorder_level" 
                                       value="{{ old('reorder_level') ?? 10 }}" 
                                       min="0" 
                                       placeholder="10" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('reorder_level')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">SKU <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="sku" 
                                       id="skuInput"
                                       value="{{ old('sku') }}" 
                                       placeholder="Auto-generated or enter manually" 
                                       class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono" 
                                       required>
                                <button type="button" 
                                        id="generateSkuBtn"
                                        class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium hover:bg-slate-200 transition">
                                    Generate SKU
                                </button>
                            </div>
                            @error('sku')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Description</label>
                            <textarea name="description" 
                                      rows="3" 
                                      placeholder="Product description (optional)" 
                                      class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiration Fields - Only for expirable products -->
                        <div id="expirationFields" class="hidden space-y-4 bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <h4 class="text-sm font-semibold text-amber-900 mb-2">Expiration Information</h4>
                            
                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Manufacturing Date (Optional)</label>
                                <input type="date" 
                                       name="manufacturing_date" 
                                       value="{{ old('manufacturing_date') }}" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('manufacturing_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Batch/Lot Number (Optional)</label>
                                <input type="text" 
                                       name="batch_lot_number" 
                                       value="{{ old('batch_lot_number') }}" 
                                       placeholder="e.g., LOT-2024-001" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('batch_lot_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700 mb-1 block">Expiration Date (Optional)</label>
                                <input type="date" 
                                       name="expiration_date" 
                                       value="{{ old('expiration_date') }}" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500">
                                @error('expiration_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-1 block">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" required>
                                <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-700 mb-2 block">Compatible Motorcycle Models <span class="text-red-500">*</span></label>
                            <div class="border border-slate-200 rounded-lg p-4 motorcycle-group">
                                @foreach($motorcycles as $brand => $models)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-semibold text-slate-900 mb-2">{{ $brand }}</h4>
                                        <div class="space-y-2">
                                            @foreach($models as $model)
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 p-1 rounded">
                                                    <input type="checkbox" 
                                                           name="motorcycle_models[]" 
                                                           value="{{ $model->id }}" 
                                                           class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                                    <span class="text-sm text-slate-700">{{ $model->full_name }}</span>
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
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">QR Code Preview</h3>
                            <div class="flex items-center justify-center mb-4">
                                <div id="qrPreview" class="qr-preview bg-white rounded-lg border border-slate-200 flex items-center justify-center">
                                    <span class="text-xs text-slate-400">Enter SKU to preview</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 text-center">QR code will be automatically generated when you save the product.</p>
                        </div>
                    </div>                </div>

                <div class="mt-6 flex items-center gap-3 pt-6 border-t border-slate-200">
                    <button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold hover:from-cyan-700 hover:to-cyan-600 transition">
                        Save Product
                    </button>
                    <a href="{{ route('product-catalog.index') }}" class="px-6 py-2 rounded-lg border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Add New Product Description Modal -->
    <div id="addProductDescModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-2xl shadow-2xl w-full mx-4 sm:max-w-md overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-500">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Add New Product Description</h3>
                        <button id="closeProductDescModal" class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <form id="addProductDescForm" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Product Description Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="newProductDescName" 
                               name="name" 
                               placeholder="e.g., Brake Pads" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Brand <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="newProductDescBrand" 
                               name="brand" 
                               placeholder="e.g., Bosch" 
                               class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500" 
                               required>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" id="cancelProductDesc" class="flex-1 px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                            Add Description
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    width: 150,
                    height: 150,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            // Add New Product Description Modal
            addNewProductDescBtn.addEventListener('click', function() {
                addProductDescModal.classList.remove('hidden');
                newProductDescName.focus();
            });

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
    @vite(['resources/js/qrcode.js'])
</x-layouts.app>
