<x-layouts.app :title="__('Purchase Order History')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Purchase Order History</h1>
                <p class="max-w-2xl text-sm text-slate-500">Review completed and archived purchase orders for historical reference.</p>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('order.history') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Search</span>
                    <input name="search" value="{{ $search }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Supplier</span>
                    <select name="supplier" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" onchange="this.form.submit()">
                        <option value="">All suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->name }}" {{ $supplierFilter === $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">From</span>
                    <input name="date_from" value="{{ $dateFrom }}" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">To</span>
                    <input name="date_to" value="{{ $dateTo }}" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 outline-none" />
                </label>
                <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-4">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 hover:bg-emerald-700">Apply Filters</button>
                    <a href="{{ route('order.history') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-emerald-500 hover:text-slate-900">Clear</a>
                </div>
            </form>

            @include('purchase_order.partials.orders-table', [
                'orders' => $orders,
                'dateLabel' => 'Completed',
                'dateType' => 'received',
                'emptyMessage' => 'No completed or archived purchase orders found.',
            ])

            <div class="mt-4 px-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-layouts.app>
