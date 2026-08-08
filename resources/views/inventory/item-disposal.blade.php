<x-layouts.app :title="__('Item Disposal')">
    <div class="space-y-4">
        <!-- Header -->
        <div class="rounded-[22px] border border-slate-200 bg-white p-5 text-slate-900 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Item Disposal List</h1>
                    <p class="mt-1 text-xs text-slate-500">Automatically identifies expired, damaged, or recalled inventory items requiring disposal.</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <div onclick="openItemsByStatus('all')" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm cursor-pointer hover:border-[#00fff2] transition">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Items Identified</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ $stats['total'] }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Total flagged items</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div onclick="openItemsByStatus('Pending')" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm cursor-pointer hover:border-[#00fff2] transition">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Pending Review</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ $stats['pending'] }}</p>
                        <p class="text-xs text-amber-600 mt-0.5">Awaiting disposal check</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div onclick="openItemsByStatus('Approved')" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm cursor-pointer hover:border-[#00fff2] transition">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 mb-1">Approved Disposal</p>
                        <p class="text-2xl font-semibold text-slate-900">{{ $stats['approved'] }}</p>
                        <p class="text-xs text-[#105f68] mt-0.5">Ready for removal</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-[12px] bg-[#00fff2] text-black shadow-sm shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                        <select id="statusFilter" class="px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent">
                            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        </select>
                        <input type="text" id="searchInput" placeholder="Search items..." value="{{ request('search') }}" class="px-3 py-2 rounded-[12px] border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00fff2] focus:border-transparent w-64" />
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-[#0f172a] border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Product Image</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Item Name</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">SKU</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Category</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Total Stock</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Shop Qty</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Wh Qty</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Expiration Date</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Days Expired</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Reason</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Status</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Date Identified</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-white">Actions</th>
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
    <div id="itemModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-[9999] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl lg:max-w-3xl overflow-hidden transform transition-all max-h-[90vh] relative z-[10000] border border-slate-200">
            <div class="px-6 py-5 bg-[#0f172a] relative flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-bold text-white mb-0.5">Item Details</h2>
                    <p class="text-xs text-slate-300">View complete item information</p>
                </div>
                <button onclick="document.getElementById('itemModal').classList.add('hidden')" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-700/50 rounded-lg cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div id="itemModalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)] text-xs">
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

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleDropdown"]') && !e.target.closest('[id^="dropdown-"]')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                    d.classList.add('hidden');
                });
            }
        });

        // Status filter
        document.getElementById('statusFilter').addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('status', this.value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        });

        // Search input
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const url = new URL(window.location);
                url.searchParams.set('search', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            }
        });
    </script>
</x-layouts.app>
