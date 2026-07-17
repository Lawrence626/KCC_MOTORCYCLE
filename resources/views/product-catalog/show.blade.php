<x-layouts.app :title="__('Product Details')">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('product-catalog.index') }}" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">← Back to Products</a>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">{{ $productCatalog->product_description }}</h1>
                        @if($productCatalog->product_name)
                            <p class="text-sm text-slate-500 mt-1">{{ $productCatalog->product_name }}</p>
                        @endif
                        <p class="text-sm text-slate-500 mt-1">{{ $productCatalog->brand }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $productCatalog->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $productCatalog->status }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Info -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Product Information</h3>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-slate-500">SKU</dt>
                                    <dd class="font-mono text-slate-900">{{ $productCatalog->sku }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Product Description</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->product_description }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Brand</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->brand }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Status</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->status }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Size</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->size ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Color</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->color ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Stock Quantity</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->stock_quantity ?? 0 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Reorder Level</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->reorder_level ?? 10 }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Warehouse</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->warehouse ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Expiration Information - Only for expirable products -->
                        @if(in_array(strtoupper($productCatalog->product_description), ['ENGINE OIL', 'BRAKE FLUID (BRAKE OIL)', 'GEAR OIL', 'COOLANT / RADIATOR COOLANT', 'CVT CLEANER', 'TIRE SEALANT']))
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 mb-3">Expiration Information</h3>
                                <dl class="grid grid-cols-2 gap-4 text-sm">
                                    @if($productCatalog->manufacturing_date)
                                        <div>
                                            <dt class="text-slate-500">Manufacturing Date</dt>
                                            <dd class="text-slate-900">{{ $productCatalog->manufacturing_date->format('M d, Y') }}</dd>
                                        </div>
                                    @endif
                                    @if($productCatalog->batch_lot_number)
                                        <div>
                                            <dt class="text-slate-500">Batch/Lot Number</dt>
                                            <dd class="text-slate-900">{{ $productCatalog->batch_lot_number }}</dd>
                                        </div>
                                    @endif
                                    @if($productCatalog->expiration_date)
                                        <div>
                                            <dt class="text-slate-500">Expiration Date</dt>
                                            <dd class="text-slate-900">{{ $productCatalog->expiration_date->format('M d, Y') }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        @endif

                        <!-- Description -->
                        @if($productCatalog->description)
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 mb-3">Description</h3>
                                <p class="text-sm text-slate-600">{{ $productCatalog->description }}</p>
                            </div>
                        @endif

                        <!-- Compatible Models -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Compatible Motorcycle Models</h3>
                            @if($productCatalog->motorcycleModels->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($productCatalog->motorcycleModels as $model)
                                        <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm">
                                            {{ $model->full_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-500">No compatible models specified</p>
                            @endif
                        </div>

                        <!-- Timestamps -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Timestamps</h3>
                            <dl class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-slate-500">Created</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->created_at->format('M d, Y - g:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Last Updated</dt>
                                    <dd class="text-slate-900">{{ $productCatalog->updated_at->format('M d, Y - g:i A') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Right Column - QR Code -->
                    <div class="lg:col-span-1">
                        <div class="bg-slate-50 rounded-lg p-6 border border-slate-200">
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">QR Code</h3>
                            @if($productCatalog->qr_code_path)
                                <div class="flex items-center justify-center mb-4">
                                    <img src="{{ asset('storage/' . $productCatalog->qr_code_path) }}" alt="QR Code" class="w-48 h-48 rounded-lg border border-slate-200">
                                </div>
                                <div class="space-y-2">
                                    <a href="{{ route('product-catalog.download-qr', $productCatalog) }}" class="block w-full text-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Download QR
                                    </a>
                                    <a href="{{ route('product-catalog.print-qr', $productCatalog) }}" target="_blank" class="block w-full text-center px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                                        Print QR
                                    </a>
                                </div>
                            @else
                                <div class="flex items-center justify-center mb-4">
                                    <div class="w-48 h-48 bg-white rounded-lg border border-slate-200 flex items-center justify-center">
                                        <span class="text-sm text-slate-400">No QR Code</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 pt-6 border-t border-slate-200 flex items-center gap-3">
                    <a href="{{ route('product-catalog.edit', $productCatalog) }}" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 transition">
                        Edit Product
                    </a>
                    <form action="{{ route('product-catalog.destroy', $productCatalog) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition">
                            Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
