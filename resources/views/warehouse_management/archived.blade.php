<x-layouts.app :title="__('Archived Warehouses')">
    <style>
        :root {
            --brand: #0f766e;
            --brand-soft: #d1fae5;
            --brand-dark: #134e4a;
            --card-bg: #ffffff;
            --surface: #f8fafc;
            --muted: #6b7280;
            --border: rgba(148,163,184,0.2);
        }
        .wm-badge { background: linear-gradient(90deg,var(--brand),var(--brand-dark)); color: #fff; box-shadow: 0 10px 30px rgba(15,118,110,0.08); }
        .wm-card { border: 1px solid var(--border); background: var(--card-bg); box-shadow: 0 12px 30px rgba(15,23,42,0.06); }
        .toast-container { position: fixed; top: 1.5rem; right: 1.5rem; z-index: 60; display: flex; flex-direction: column; gap: 0.85rem; pointer-events: none; width: max-content; min-width: 280px; }
        .toast { pointer-events: auto; display: flex; align-items: center; justify-content: space-between; gap: 0.8rem; background: #0f766e; color: #fff; border-radius: 1rem; box-shadow: 0 18px 50px rgba(15,23,42,0.18); padding: 0.85rem 1rem; font-size: 0.95rem; animation: toast-in 0.22s ease forwards; }
        .toast.success { background: #0f766e; }
        .toast.error { background: #ef4444; }
        .toast button { background: transparent; border: none; color: rgba(255,255,255,0.95); cursor: pointer; font-size: 1rem; line-height: 1; padding: 0; }
        @keyframes toast-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .btn-primary { background: linear-gradient(90deg, var(--brand), var(--brand-dark)); color: #fff; box-shadow: 0 4px 15px rgba(15,118,110,0.25); transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,118,110,0.35); }
        .btn-secondary { background: #f8fafc; color: #334155; border: 1px solid rgba(148,163,184,0.35); transition: all 0.2s ease; }
        .btn-secondary:hover { background: #f1f5f9; border-color: rgba(148,163,184,0.5); }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>

                <h1 class="text-lg font-bold text-slate-900 leading-tight">Archived Warehouses</h1>
                <p class="text-xs text-slate-500 mt-0.5">Manage and restore archived warehouses.</p>

            </div>
            <a href="{{ route('warehouse.management') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Warehouse Management
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

        @if($archivedWarehouses->count() > 0)
            <div class="grid gap-6 mt-4">
                @foreach($archivedWarehouses as $warehouse)
                <div class="wm-card rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-md flex items-center justify-center text-white font-semibold bg-gradient-to-r from-orange-500 to-orange-600">
                                    {{ strtoupper(substr($warehouse->code, -1)) }}
                                </div>
                                <div>
                                    <div class="text-lg font-semibold text-slate-900">{{ $warehouse->name }}</div>
                                    <div class="text-xs text-gray-500">Code: {{ $warehouse->code }}</div>
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                <span class="font-medium">{{ $warehouse->shelves_count }}</span> shelves
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="restoreWarehouse({{ $warehouse->id }})" class="btn-primary rounded-xl px-4 py-2 text-sm font-semibold">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Restore
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <p class="text-lg text-gray-400 mb-2">No archived warehouses</p>
                <p class="text-sm text-gray-500">Warehouses that you archive will appear here.</p>
            </div>
        @endif
    </div>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            `;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        async function restoreWarehouse(warehouseId) {
            if (!confirm('Are you sure you want to restore this warehouse?')) {
                return;
            }

            try {
                const response = await fetch(`/warehouse-management/restore/${warehouseId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Warehouse restored successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(result.message || 'Failed to restore warehouse', 'error');
                }
            } catch (error) {
                console.error('Error restoring warehouse:', error);
                showToast('Failed to restore warehouse. Please try again.', 'error');
            }
        }
    </script>
</x-layouts.app>
