<x-layouts.app :title="__('Archived Suppliers')">
    <div class="space-y-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Archived Suppliers</h1>
                <p class="text-sm text-slate-500 mt-1">View and manage archived supplier records.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <a href="{{ route('supplier.assessment') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700">
                    Back to Active Suppliers
                </a>
            </div>
        </div>

        @if(session('success'))
            <div id="success-toast" class="fixed top-4 right-4 z-50 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-lg">
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
                }, 3000);
            </script>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @if($archivedSuppliers->isEmpty())
                <div class="text-center py-12">
                    <p class="text-slate-500">No archived suppliers found.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Supplier Name</th>
                                <th class="px-4 py-3">Contact Person</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Address</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($archivedSuppliers as $supplier)
                                <tr>
                                    <td class="px-4 py-4 font-medium text-slate-900">{{ $supplier->name }}</td>
                                    <td class="px-4 py-4">{{ $supplier->contact_person ?? '-' }}</td>
                                    <td class="px-4 py-4">{{ $supplier->email ?? '-' }}</td>
                                    <td class="px-4 py-4">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="px-4 py-4">{{ $supplier->address ?? '-' }}</td>
                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('supplier.assessment.restore', ['supplier' => $supplier->id]) }}">
                                            @csrf
                                            <button type="submit" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium">Restore</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($archivedSuppliers->hasPages())
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-slate-500">
                            Showing {{ $archivedSuppliers->firstItem() }} to {{ $archivedSuppliers->lastItem() }} of {{ $archivedSuppliers->total() }} results
                        </div>
                        <div class="flex items-center gap-2">
                            @if($archivedSuppliers->onFirstPage())
                                <span class="px-3 py-1 text-sm text-slate-400">Previous</span>
                            @else
                                <a href="{{ $archivedSuppliers->previousPageUrl() }}" class="px-3 py-1 text-sm text-slate-600 hover:text-slate-900">Previous</a>
                            @endif

                            @foreach($archivedSuppliers->getUrlRange(1, $archivedSuppliers->lastPage()) as $page => $url)
                                @if($page == $archivedSuppliers->currentPage())
                                    <span class="px-3 py-1 text-sm font-medium text-white bg-cyan-600 rounded">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1 text-sm text-slate-600 hover:text-slate-900">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($archivedSuppliers->hasMorePages())
                                <a href="{{ $archivedSuppliers->nextPageUrl() }}" class="px-3 py-1 text-sm text-slate-600 hover:text-slate-900">Next</a>
                            @else
                                <span class="px-3 py-1 text-sm text-slate-400">Next</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
