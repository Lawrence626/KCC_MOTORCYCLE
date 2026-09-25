<x-layouts.app :title="__('Archived Suppliers')">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <h1 class="text-lg font-bold text-slate-900 leading-tight">Archived Suppliers</h1>
                <p class="text-xs text-slate-500 mt-0.5">View and manage archived supplier records.</p>
            </div>
            <a href="{{ route('supplier.assessment') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-sm hover:bg-slate-50 transition-all duration-200">
                <svg class="h-3.5 w-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Active Suppliers
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">

    @if(session('success'))
    <div id="success-toast" class="fixed top-4 right-4 z-50 rounded-[10px] border border-[#6EC1D1] bg-teal-50 p-4 text-sm font-medium text-slate-900 shadow-lg">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('success-toast');
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s ease';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3500);
    </script>
@endif

        <div class="rounded-[10px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            @if($archivedSuppliers->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <p class="mt-4 text-base font-semibold text-slate-900">No archived suppliers found.</p>
                    <p class="mt-1 text-sm text-slate-500">Archived supplier records will be listed here when removed from active listing.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="text-xs font-semibold uppercase tracking-wider text-white border-b border-slate-800 bg-[#0f172a]" style="background-color: #0f172a;">
                            <tr>
                                <th class="px-4 py-3">Supplier Name</th>
                                <th class="px-4 py-3">Contact Person</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Address</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($archivedSuppliers as $supplier)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3.5 font-semibold text-slate-900">{{ $supplier->name }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->contact_person ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->email ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $supplier->address ?? '-' }}</td>
                                    <td class="px-4 py-3.5 text-right">
                                        <form method="POST" action="{{ route('supplier.assessment.restore', ['supplier' => $supplier->id]) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#6EC1D1] px-3 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all">
                                                Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($archivedSuppliers->hasPages())
                    <div class="p-4 flex items-center justify-between border-t border-slate-200">
                        <div class="text-xs text-slate-500">
                            Showing {{ $archivedSuppliers->firstItem() }} to {{ $archivedSuppliers->lastItem() }} of {{ $archivedSuppliers->total() }} results
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($archivedSuppliers->onFirstPage())
                                <span class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Previous</span>
                            @else
                                <a href="{{ $archivedSuppliers->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 transition-all">Previous</a>
                            @endif

                            @foreach($archivedSuppliers->getUrlRange(1, $archivedSuppliers->lastPage()) as $page => $url)
                                @if($page == $archivedSuppliers->currentPage())
                                    <span class="px-3 py-1.5 text-xs font-bold text-slate-900 bg-[#6EC1D1] border border-slate-200 shadow-sm rounded-[10px]">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 transition-all">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($archivedSuppliers->hasMorePages())
                                <a href="{{ $archivedSuppliers->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 transition-all">Next</a>
                            @else
                                <span class="px-3 py-1.5 text-xs rounded-[10px] border border-slate-200 text-slate-400 bg-slate-50 cursor-not-allowed">Next</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>