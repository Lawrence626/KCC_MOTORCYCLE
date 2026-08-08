<div class="rounded-[18px] border border-slate-200 bg-white p-3 shadow-sm mb-4">
    <div class="flex flex-wrap gap-2">
        @php
            $offlineMenuLinks = [
                ['route' => 'offline.reconciliation', 'label' => 'Overview'],
                ['route' => 'offline.purchase-orders', 'label' => 'Purchase Orders'],
                ['route' => 'offline.export', 'label' => 'Export Data'],
                ['route' => 'offline.import', 'label' => 'Import Data'],
                ['route' => 'offline.pending.imports', 'label' => 'Pending Imports'],
                ['route' => 'offline.history', 'label' => 'Sync History'],
            ];
        @endphp

        @foreach ($offlineMenuLinks as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-[12px] transition {{ $isActive ? 'bg-[#0f172a] text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>
