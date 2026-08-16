<x-layouts.app :title="__('Item Disposal')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between gap-3">
            <div class="pl-3 lg:pl-2">
                <h1 class="text-3xl font-bold text-slate-900">Item Disposal List</h1>
                <p class="text-sm text-slate-500 mt-1">Automatically identifies expired, damaged, or recalled inventory items requiring disposal.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <div onclick="openItemsByStatus('all')" class="border border-gray-200 p-4 cursor-pointer hover:border-[#00fff2] transition" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold">Items Identified</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ $stats['total'] }}</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Total flagged items</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                        <svg class="w-5 h-5" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div onclick="openItemsByStatus('Pending')" class="border border-gray-200 p-4 cursor-pointer hover:border-[#00fff2] transition" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold">Pending Review</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ $stats['pending'] }}</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Awaiting disposal check</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                        <svg class="w-5 h-5" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div onclick="openItemsByStatus('Approved')" class="border border-gray-200 p-4 cursor-pointer hover:border-[#00fff2] transition" style="border-radius: 20px; background: linear-gradient(50deg, #ffffff 0%, #29d5d815 50%);">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-black text-xs font-semibold">Approved Disposal</p>
                        <div class="mt-1">
                            <p class="text-2xl font-bold text-black">{{ $stats['approved'] }}</p>
                            <p class="text-gray-500 text-xs mt-1 font-medium">Ready for removal</p>
                        </div>
                    </div>
                    <div class="border border-gray-200 w-10 h-10 flex items-center justify-center flex-shrink-0" style="border-radius: 10px; background-color: #00fff2ff;">
                        <svg class="w-5 h-5" style="color: #000000ff;" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
            <div class="p-4 border-b border-slate-200 bg-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <h2 class="text-sm font-semibold text-slate-900">Identified Disposal Items</h2>
                    <div class="flex items-center gap-3">
                        @php
                            $currentStatus = request('status', 'all');
                            $statusLabels = [
                                'all' => 'All Status',
                                'Pending' => 'Pending Review',
                                'Approved' => 'Approved',
                            ];
                            $currentLabel = $statusLabels[$currentStatus] ?? 'All Status';
                        @endphp
                        <div class="relative min-w-[150px]" data-dropdown-wrapper="statusFilter">
                            <input type="hidden" id="statusFilter" value="{{ $currentStatus }}" />
                            <button type="button" id="statusFilterButton" onclick="toggleDropdown('statusFilterDropdown', event)" class="w-full px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-left text-xs text-slate-900 flex items-center justify-between hover:ring-1 hover:ring-black/15 focus:outline-none focus:ring-1 focus:ring-black/35 transition shadow-sm h-10 gap-2">
                                <span id="statusFilterLabel">{{ $currentLabel }}</span>
                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                            <div id="statusFilterDropdown" class="dropdown-menu hidden absolute top-full left-0 z-[999] mt-2 w-full rounded-[12px] border border-slate-200 bg-white shadow-xl p-3 space-y-1">
                                <button type="button" onclick="selectStatusOption('all', 'All Status', event)" class="w-full px-4 py-2 text-center text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">All Status</button>
                                <button type="button" onclick="selectStatusOption('Pending', 'Pending Review', event)" class="w-full px-4 py-2 text-center text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Pending Review</button>
                                <button type="button" onclick="selectStatusOption('Approved', 'Approved', event)" class="w-full px-4 py-2 text-center text-xs text-slate-700 hover:bg-slate-100 rounded-[10px] transition">Approved</button>
                            </div>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search items..." value="{{ request('search') }}" class="px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-black/35 hover:ring-1 hover:ring-black/15 transition shadow-sm h-10 w-64" />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap min-w-max">
                    <thead class="bg-[#0f172a] border-b border-slate-200 sticky-header text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-white">Product Image</th>
                            <th class="px-4 py-3 font-semibold text-white">Item Name</th>
                            <th class="px-4 py-3 font-semibold text-white">SKU</th>
                            <th class="px-4 py-3 font-semibold text-white">Category</th>
                            <th class="px-4 py-3 font-semibold text-center text-white">Total Stock</th>
                            <th class="px-4 py-3 font-semibold text-center text-white">Shop Qty</th>
                            <th class="px-4 py-3 font-semibold text-center text-white">Wh Qty</th>
                            <th class="px-4 py-3 font-semibold text-white">Expiration Date</th>
                            <th class="px-4 py-3 font-semibold text-center text-white">Days Expired</th>
                            <th class="px-4 py-3 font-semibold text-white">Reason</th>
                            <th class="px-4 py-3 font-semibold text-center text-white">Status</th>
                            <th class="px-4 py-3 font-semibold text-white">Date Identified</th>
                            <th class="px-4 py-3 font-semibold text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                @php
                                    $shopQty = 0;
                                    $whQty = 0;
                                    if(isset($product->warehouseStocks)) {
                                        foreach($product->warehouseStocks as $stock) {
                                            if ($stock->warehouse === 'SHOP') {
                                                $shopQty += $stock->quantity;
                                            } else {
                                                $whQty += $stock->quantity;
                                            }
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $product->sku ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $product->category ?? '-' }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $product->stock_quantity }}</td>
                                    <td class="px-4 py-3 font-semibold text-blue-600">{{ $shopQty }}</td>
                                    <td class="px-4 py-3 font-semibold text-amber-600">{{ $whQty }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $product->expiry_date?->format('M d, Y') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">
                                        @if($product->expiry_date && $product->expiry_date->isPast())
                                            {{ $product->expiry_date->diffInDays(now()) }} days
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $product->disposal_reason ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($product->disposal_status === 'Pending')
                                            <span onclick="openItemModal({{ $product->id }})" class="cursor-pointer inline-flex items-center rounded-full bg-[#105f68] px-2.5 py-0.5 text-[11px] font-semibold text-white hover:bg-[#0d4f57]">Pending Review</span>
                                        @elseif($product->disposal_status === 'Approved')
                                            <span onclick="openItemModal({{ $product->id }})" class="cursor-pointer inline-flex items-center rounded-full bg-[#00fff2] px-2.5 py-0.5 text-[11px] font-semibold text-black hover:bg-[#00e6da]">Approved</span>
                                        @elseif($product->disposal_status === 'Disposed')
                                            <span class="inline-flex items-center rounded-full bg-[#0f172a] px-2.5 py-0.5 text-[11px] font-semibold text-white">Disposed</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-0.5 text-[11px] font-semibold text-slate-700">None</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $product->disposal_date_identified?->format('M d, Y') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block">
                                            <button onclick="toggleDropdown({{ $product->id }})" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>
                                            <div id="dropdown-{{ $product->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10">
                                                @if($product->disposal_status === 'Pending')
                                                    <form method="POST" action="{{ route('item.disposal.approve', $product->id) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                                            <svg class="w-4 h-4 text-[#105f68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Approve
                                                        </button>
                                                    </form>
                                                @elseif($product->disposal_status === 'Approved')
                                                    <form method="POST" action="{{ route('item.disposal.dispose', $product->id) }}">
                                                        @csrf
                                                        <button type="submit" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Mark as Disposed
                                                        </button>
                                                    </form>
                                                @endif
                                                <button onclick="openItemModal({{ $product->id }})" class="w-full px-4 py-2 text-left text-xs text-slate-700 hover:bg-slate-50 flex items-center gap-2 font-medium">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View Details
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="13" class="px-6 py-16">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-slate-900">No items currently require disposal.</p>
                                        <p class="text-xs text-slate-500 mt-1">The system will automatically identify expired or damaged inventory.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 flex items-center justify-between text-xs bg-slate-50">
                    <p class="text-slate-600">Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results</p>
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Item Details Modal -->
    <div id="itemModal" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-4">
        <div class="absolute inset-0 bg-slate-950/65 backdrop-blur-xl" onclick="document.getElementById('itemModal').classList.add('hidden')"></div>
        <div class="relative w-full max-w-2xl lg:max-w-3xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.18)] max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-[#00fff2] bg-[#00fff2] px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-black">Item Details</h2>
                    <p class="text-sm text-slate-800 font-medium">View complete item information</p>
                </div>
                <button onclick="document.getElementById('itemModal').classList.add('hidden')" class="rounded-[10px] p-2 text-black hover:bg-black/10 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="itemModalContent" class="p-4 sm:p-5 overflow-y-auto max-h-[calc(90vh-120px)] text-xs">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        const productsData = @json($products->items());

        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            
            allDropdowns.forEach(d => {
                if (d.id !== dropdown.id) {
                    d.classList.add('hidden');
                }
            });
            
            dropdown.classList.toggle('hidden');
        }

        function openItemsByStatus(status) {
            const modal = document.getElementById('itemModal');
            const content = document.getElementById('itemModalContent');
            
            let filteredProducts = productsData;
            if (status !== 'all') {
                filteredProducts = productsData.filter(p => p.disposal_status === status);
            }
            
            if (filteredProducts.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-slate-500">No items found for this status.</p>
                    </div>
                `;
            } else {
                let itemsHtml = filteredProducts.map(product => `
                    <div class="border border-slate-200 rounded-xl p-4 mb-3 hover:bg-slate-50 cursor-pointer" onclick="openItemModal(${product.id})">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">${product.name || '-'}</p>
                                <p class="text-xs text-slate-600">${product.sku || '-'}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${
                                product.disposal_status === 'Pending' ? 'bg-[#105f68] text-white' : 
                                product.disposal_status === 'Approved' ? 'bg-[#00fff2] text-black' : 
                                'bg-slate-200 text-slate-700'
                            }">${product.disposal_status || 'None'}</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-500">
                            Stock: ${product.stock_quantity || 0} • Exp: ${product.expiry_date || 'N/A'}
                        </div>
                    </div>
                `).join('');
                
                content.innerHTML = `
                    <div class="mb-4">
                        <h3 class="text-base font-semibold text-slate-900">${status === 'all' ? 'All Items' : status + ' Items'} (${filteredProducts.length})</h3>
                        <p class="text-xs text-slate-500">Click on an item to view details</p>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto">
                        ${itemsHtml}
                    </div>
                `;
            }
            
            modal.classList.remove('hidden');
        }

        function openItemModal(productId) {
            const product = productsData.find(p => p.id === productId);
            if (!product) return;

            const modal = document.getElementById('itemModal');
            const content = document.getElementById('itemModalContent');
            
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Product Name</label>
                            <p class="text-xs font-semibold text-slate-900 mt-1">${product.name || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">SKU</label>
                            <p class="text-xs text-slate-600 mt-1">${product.sku || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</label>
                            <p class="text-xs text-slate-600 mt-1">${product.category || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Brand</label>
                            <p class="text-xs text-slate-600 mt-1">${product.brand || '-'}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Stock</label>
                            <p class="text-xs font-semibold text-slate-900 mt-1">${product.stock_quantity || 0}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Price</label>
                            <p class="text-xs text-slate-600 mt-1">₱${parseFloat(product.unit_price || 0).toFixed(2)}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Expiration Date</label>
                            <p class="text-xs text-slate-600 mt-1">${product.expiry_date || 'Non-expiring'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Days Expired</label>
                            <p class="text-xs text-slate-600 mt-1">${product.expiry_date ? product.days_expired + ' days' : '-'}</p>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Disposal Reason</label>
                            <p class="text-xs text-slate-600 mt-1">${product.disposal_reason || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Disposal Status</label>
                            <p class="text-xs font-semibold text-slate-900 mt-1">${product.disposal_status || 'None'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Date Identified</label>
                            <p class="text-xs text-slate-600 mt-1">${product.disposal_date_identified || '-'}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Supplier</label>
                            <p class="text-xs text-slate-600 mt-1">${product.supplier_name || '-'}</p>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function toggleDropdown(id, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const dropdown = document.getElementById(id);
            if (!dropdown) return;

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== id) {
                    menu.classList.add('hidden');
                }
            });

            dropdown.classList.toggle('hidden');
        }

        function selectStatusOption(value, label, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const input = document.getElementById('statusFilter');
            if (input) {
                input.value = value;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const labelSpan = document.getElementById('statusFilterLabel');
            if (labelSpan) {
                labelSpan.textContent = label;
            }
            const dropdown = document.getElementById('statusFilterDropdown');
            if (dropdown) {
                dropdown.classList.add('hidden');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[data-dropdown-wrapper]') && !e.target.closest('[onclick^="toggleDropdown"]') && !e.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('.dropdown-menu, [id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });
            }
        });

        // Status filter
        const statusFilterEl = document.getElementById('statusFilter');
        if (statusFilterEl) {
            statusFilterEl.addEventListener('change', function() {
                const url = new URL(window.location);
                url.searchParams.set('status', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });
        }

        // Search input
        const searchInputEl = document.getElementById('searchInput');
        if (searchInputEl) {
            searchInputEl.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const url = new URL(window.location);
                    url.searchParams.set('search', this.value);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                }
            });
        }
    </script>
</x-layouts.app>
