<x-layouts.app :title="__('Purchase Requests')">
    <div class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-slate-900">Purchase Requests</h1>
                <p class="max-w-2xl text-sm text-slate-500">Create and review purchase requests before they become approved orders.</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-2xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-cyan-500/20 hover:bg-cyan-700">New Request</button>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Open requests</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">14</p>
                <p class="mt-2 text-sm text-slate-500">Pending approval from procurement.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Approved</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">7</p>
                <p class="mt-2 text-sm text-slate-500">Ready for conversion to purchase orders.</p>
            </div>
            <div class="rounded-[26px] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Rejected</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">2</p>
                <p class="mt-2 text-sm text-slate-500">Requests declined for budget or stock reasons.</p>
            </div>
        </div>

        <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Recent requests</h2>
                    <p class="text-sm text-slate-500">Most recent request activity in a compact view.</p>
                </div>
                <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">Review</span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Search requests</span>
                    <input type="search" placeholder="Request ID or item" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none" />
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Status</span>
                    <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none">
                        <option>All statuses</option>
                        <option>Open</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </label>
                <label class="block text-sm text-slate-700">
                    <span class="text-xs font-semibold text-slate-500">Department</span>
                    <select class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-100 outline-none">
                        <option>All departments</option>
                        <option>Service</option>
                        <option>Sales</option>
                        <option>Inventory</option>
                    </select>
                </label>
            </div>

            <div class="mt-6 overflow-hidden rounded-[26px] border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.18em]">
                        <tr>
                            <th class="px-4 py-3">Request</th>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold">RQ-1209</td>
                            <td class="px-4 py-3">Brake Pads</td>
                            <td class="px-4 py-3">Service</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">Open</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold">RQ-1206</td>
                            <td class="px-4 py-3">Motor Oil</td>
                            <td class="px-4 py-3">Inventory</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">Approved</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold">RQ-1203</td>
                            <td class="px-4 py-3">Warranty Parts</td>
                            <td class="px-4 py-3">Sales</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-semibold text-rose-800">Rejected</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold">RQ-1201</td>
                            <td class="px-4 py-3">Chain Lubricant</td>
                            <td class="px-4 py-3">Service</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">Approved</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
