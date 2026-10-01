<x-layouts.app :title="__('Purchase Order History')">
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Purchase Order History</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review completed and archived purchase orders for historical reference.</p>
        </div>
    </x-slot>

    <div class="space-y-4">

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('order.history') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Search</span>
                    <input name="search" value="{{ $search }}" type="search" placeholder="Order ID or supplier" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Supplier</span>
                    <select name="supplier" class="appearance-none mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none pr-8" style="background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 20 20%27 fill=%27none%27 stroke=%27%2338445d%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E'); background-repeat:no-repeat; background-position:right 0.75rem center; background-size:1.2em;" onchange="this.form.submit()">
                        <option value="">All suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->name }}" {{ $supplierFilter === $supplier->name ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">From</span>
                    <input name="date_from" value="{{ $dateFrom }}" type="date" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">To</span>
                    <input name="date_to" value="{{ $dateTo }}" type="date" class="mt-2 w-full rounded-[10px] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-[#105f68] focus:ring-2 focus:ring-[#105f68]/20 outline-none" />
                </label>
                <div class="flex items-end gap-2 sm:col-span-2 xl:col-span-4">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-[10px] bg-[#105f68] px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-[#105f68]/20 hover:bg-[#0c474e]">Apply Filters</button>
                    <a href="{{ route('order.history') }}" class="rounded-[10px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-[#105f68] hover:text-slate-900">Clear</a>
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
