<x-layouts.app :title="__('Product Details')">
<<<<<<< HEAD
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">{{ $productCatalog->product_description }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $productCatalog->product_name ?: $productCatalog->brand }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $productCatalog->status === 'Active' ? 'bg-[#105f68] text-white' : 'bg-red-600 text-white' }}">
=======
    <div class="max-w-4xl mx-auto space-y-4">
        <div class="mb-2">
            <a href="{{ route('product-catalog.index') }}" class="inline-flex items-center gap-2 rounded-[10px] border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 transition-all">
                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Products
            </a>
        </div>

        <!-- Page Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ $productCatalog->product_description }}</h1>
                    @if($productCatalog->product_name)
                        <p class="text-xs text-slate-500 mt-1">{{ $productCatalog->product_name }}</p>
                    @endif
                    <p class="text-xs text-slate-500 mt-0.5">{{ $productCatalog->brand }}</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $productCatalog->status === 'Active' ? 'bg-[#105f68] text-white' : 'bg-red-600 text-white' }}">
>>>>>>> 594490397ebecd1f37adadd252bb79d7a67298f2
                    {{ $productCatalog->status }}
                </span>
                <a href="{{ route('product-catalog.index') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Products
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-4">

        <!-- Details Card -->
        <div class="rounded-[20px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Info -->
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 mb-3 uppercase tracking-wider">Product Information</h3>
                            <dl class="grid grid-cols-2 gap-4 text-xs">
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">SKU</dt>
                                    <dd class="font-mono text-slate-900 font-semibold">{{ $productCatalog->sku }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Product Category</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->product_description }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Brand</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->brand }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Status</dt>
                                    <dd>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $productCatalog->status === 'Active' ? 'bg-[#105f68] text-white' : 'bg-red-600 text-white' }}">{{ $productCatalog->status }}</span>
                                    </dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Size</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->size ?? '-' }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Color</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->color ?? '-' }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Stock Quantity</dt>
                                    <dd>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#6EC1D1] text-black">{{ $productCatalog->stock_quantity ?? 0 }}</span>
                                    </dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Reorder Level</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->reorder_level ?? 10 }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100 col-span-2">
                                    <dt class="text-slate-500 font-medium mb-0.5">Warehouse</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->warehouse ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Expiration Information - Only for expirable products -->
                        @if(in_array(strtoupper($productCatalog->product_description), ['ENGINE OIL', 'BRAKE FLUID (BRAKE OIL)', 'GEAR OIL', 'COOLANT / RADIATOR COOLANT', 'CVT CLEANER', 'TIRE SEALANT']))
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 mb-3 uppercase tracking-wider">Expiration Information</h3>
                                <dl class="grid grid-cols-2 gap-4 text-xs">
                                    @if($productCatalog->manufacturing_date)
                                        <div class="bg-amber-50/50 rounded-[12px] p-3 border border-amber-100">
                                            <dt class="text-slate-500 font-medium mb-0.5">Manufacturing Date</dt>
                                            <dd class="text-slate-900 font-semibold">{{ $productCatalog->manufacturing_date->format('M d, Y') }}</dd>
                                        </div>
                                    @endif
                                    @if($productCatalog->batch_lot_number)
                                        <div class="bg-amber-50/50 rounded-[12px] p-3 border border-amber-100">
                                            <dt class="text-slate-500 font-medium mb-0.5">Batch/Lot Number</dt>
                                            <dd class="text-slate-900 font-semibold">{{ $productCatalog->batch_lot_number }}</dd>
                                        </div>
                                    @endif
                                    @if($productCatalog->expiration_date)
                                        <div class="bg-amber-50/50 rounded-[12px] p-3 border border-amber-100">
                                            <dt class="text-slate-500 font-medium mb-0.5">Expiration Date</dt>
                                            <dd class="text-slate-900 font-semibold">{{ $productCatalog->expiration_date->format('M d, Y') }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        @endif

                        <!-- Description -->
                        @if($productCatalog->description)
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 mb-3 uppercase tracking-wider">Description</h3>
                                <p class="text-xs text-slate-600 bg-slate-50 rounded-[12px] p-3 border border-slate-100">{{ $productCatalog->description }}</p>
                            </div>
                        @endif

                        <!-- Compatible Models -->
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 mb-3 uppercase tracking-wider">Compatible Motorcycle Models</h3>
                            @if($productCatalog->motorcycleModels->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($productCatalog->motorcycleModels as $model)
                                        <span class="px-3 py-1 bg-[#105f68]/10 text-[#105f68] rounded-full text-xs font-semibold border border-[#105f68]/20">
                                            {{ $model->full_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 rounded-full text-xs font-semibold border border-emerald-200">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    General Item / Universal (Fits all models)
                                </span>
                            @endif
                        </div>

                        <!-- Timestamps -->
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 mb-3 uppercase tracking-wider">Timestamps</h3>
                            <dl class="grid grid-cols-2 gap-4 text-xs">
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Created</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->created_at->format('M d, Y - g:i A') }}</dd>
                                </div>
                                <div class="bg-slate-50 rounded-[12px] p-3 border border-slate-100">
                                    <dt class="text-slate-500 font-medium mb-0.5">Last Updated</dt>
                                    <dd class="text-slate-900 font-semibold">{{ $productCatalog->updated_at->format('M d, Y - g:i A') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Right Column - QR Code -->
                    <div class="lg:col-span-1">
                        <div class="bg-slate-50 rounded-[18px] p-6 border border-slate-200">
                            <h3 class="text-xs font-bold text-slate-900 mb-3">QR Code</h3>
                            @if($productCatalog->qr_code_path)
                                <div class="flex items-center justify-center mb-4">
                                    <img src="{{ asset('storage/' . $productCatalog->qr_code_path) }}" alt="QR Code" class="w-48 h-48 rounded-[12px] border border-slate-200">
                                </div>
                                <div class="space-y-2">
                                    <a href="{{ route('product-catalog.download-qr', $productCatalog) }}" class="block w-full text-center px-4 py-2 rounded-[10px] bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition">
                                        Download QR
                                    </a>
                                    <a href="{{ route('product-catalog.print-qr', $productCatalog) }}" target="_blank" class="block w-full text-center px-4 py-2 rounded-[10px] bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition">
                                        Print QR
                                    </a>
                                </div>
                            @else
                                <div class="flex items-center justify-center mb-4">
                                    <div class="w-48 h-48 bg-white rounded-[12px] border border-slate-200 flex items-center justify-center">
                                        <span class="text-xs text-slate-400">No QR Code</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center gap-3">
                <a href="{{ route('product-catalog.edit', $productCatalog) }}" class="px-5 py-2 rounded-full bg-[#6EC1D1] text-black text-xs font-semibold hover:bg-[#59b2c2] transition shadow-sm">
                    Edit Product
                </a>
                <form action="{{ route('product-catalog.destroy', $productCatalog) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2 rounded-full border border-red-300 text-red-600 text-xs font-semibold hover:bg-red-50 transition">
                        Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
