<x-layouts.app :title="__('Inventory Movements')">
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Inventory Movements</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage inventory movements created during offline operations</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Pending Sync</p>
                <p class="text-2xl font-bold text-amber-600">{{ $movements->where('sync_status', 'pending_sync')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Awaiting export</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Exported</p>
                <p class="text-2xl font-bold text-blue-600">{{ $movements->where('sync_status', 'exported')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Ready for import</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Synchronized</p>
                <p class="text-2xl font-bold text-green-600">{{ $movements->where('sync_status', 'synchronized')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Successfully synced</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                <p class="text-xs text-slate-600 font-medium mb-0.5">Failed</p>
                <p class="text-2xl font-bold text-red-600">{{ $movements->where('sync_status', 'failed')->count() }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Sync errors</p>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="bg-white rounded-lg border border-slate-200 p-2.5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div>
                    <input type="text" placeholder="Search by product name or SKU..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent" />
                </div>
                <div>
                    <select class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">All Sync Status</option>
                        <option value="pending_sync">Pending Sync</option>
                        <option value="exported">Exported</option>
                        <option value="imported">Imported</option>
                        <option value="synchronized">Synchronized</option>
                        <option value="duplicate">Duplicate</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div>
                    <a href="{{ route('offline.export') }}" class="inline-flex items-center justify-center w-full px-3 py-1.5 rounded-lg bg-cyan-600 text-white text-xs font-medium hover:bg-cyan-700 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Product</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Type</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Sync Status</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Quantity Change</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-slate-700 uppercase tracking-wide">Unit Price</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Supplier</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 uppercase tracking-wide">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($movements as $movement)
                        <tr>
                            <td class="px-3 py-2 text-slate-900 font-medium">{{ $movement->product?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($movement->type == 'in') bg-green-100 text-green-700 @elseif($movement->type == 'out') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                    {{ ucfirst($movement->type) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium @if($movement->sync_status == 'pending_sync') bg-amber-100 text-amber-700 @elseif($movement->sync_status == 'exported') bg-blue-100 text-blue-700 @elseif($movement->sync_status == 'synchronized') bg-green-100 text-green-700 @elseif($movement->sync_status == 'duplicate') bg-red-100 text-red-700 @elseif($movement->sync_status == 'failed') bg-red-100 text-red-700 @else bg-slate-100 text-slate-700 @endif">
                                    {{ str_replace('_', ' ', ucfirst($movement->sync_status)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium">{{ $movement->quantity_change }}</td>
                            <td class="px-3 py-2 text-right text-slate-900 font-medium">₱{{ number_format($movement->unit_price, 2) }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $movement->supplier_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-slate-500">No inventory movements found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-3 py-2 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                <p class="text-slate-600">Showing {{ $movements->firstItem() }} to {{ $movements->lastItem() }} of {{ $movements->total() }} items</p>
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
