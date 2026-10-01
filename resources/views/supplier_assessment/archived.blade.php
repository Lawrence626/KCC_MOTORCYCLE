<x-layouts.app :title="__('Archived Suppliers')">

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Archived Suppliers</h1>
                <p class="text-xs text-slate-500 mt-0.5">View and restore archived supplier records.</p>
            </div>
            <a href="{{ route('supplier.assessment') }}" class="inline-flex items-center gap-1.5 rounded-[10px] border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all duration-200">
                <svg class="h-3.5 w-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Active Suppliers
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div id="success-toast" class="fixed top-4 right-8 z-50 rounded-[12px] border border-[#6EC1D1] bg-teal-50 px-4 py-3.5 text-sm font-medium text-slate-900 shadow-xl flex items-center gap-2">
                <svg class="w-4 h-4 text-teal-700" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('success-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.4s ease';
                        setTimeout(() => toast.remove(), 400);
                    }
                }, 3500);
            </script>
        @endif

        <div class="rounded-[20px] border border-slate-200 bg-white overflow-hidden shadow-sm">
            @if($archivedSuppliers->isEmpty())
                <div class="p-12 py-20 text-center text-slate-500">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3.5">
                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <p class="text-lg font-bold text-slate-900">No archived suppliers found.</p>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Archived supplier records will appear here when deactivated from the assessment list.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="text-xs font-semibold uppercase tracking-wider text-white border-b border-slate-800 bg-[#0f172a]">
                            <tr>
                                <th class="px-5 py-3.5">Supplier Name</th>
                                <th class="px-5 py-3.5">Contact Person</th>
                                <th class="px-5 py-3.5">Email</th>
                                <th class="px-5 py-3.5">Phone</th>
                                <th class="px-5 py-3.5">Address</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($archivedSuppliers as $supplier)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-5 py-3.5 font-bold text-slate-900 text-xs">{{ $supplier->name }}</td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $supplier->contact_person ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $supplier->email ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs font-mono">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $supplier->address ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('supplier.assessment.restore', ['supplier' => $supplier->id]) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-[8px] bg-[#6EC1D1] px-3.5 py-1.5 text-xs font-bold text-slate-900 border border-slate-200 shadow-sm hover:bg-[#59b2c2] transition-all cursor-pointer">
                                                    Restore
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('supplier.assessment.force-delete', ['supplier' => $supplier->id]) }}" onsubmit="return confirm('Are you sure you want to permanently delete \'{{ addslashes($supplier->name) }}\'? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-[8px] bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1.5 text-xs font-semibold hover:bg-rose-100 hover:border-rose-300 transition-all cursor-pointer shadow-xs">
                                                    <svg class="h-3.5 w-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Delete Permanently
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($archivedSuppliers->hasPages())
                    <div class="p-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-t border-slate-100">
                        <div class="text-xs text-slate-500">
                            Showing {{ $archivedSuppliers->firstItem() }} to {{ $archivedSuppliers->lastItem() }} of {{ $archivedSuppliers->total() }} results
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
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