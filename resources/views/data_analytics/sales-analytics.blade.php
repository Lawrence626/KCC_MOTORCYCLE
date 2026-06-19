<x-layouts.app :title="__('Sales Analytics')">
    @php
        $quickStats = $quickStats ?? [
            'total_inventory_value' => 0,
            'average_unit_price' => 0,
            'total_units_in_stock' => 0,
            'healthy_skus' => 0,
            'low_stock_skus' => 0,
            'out_of_stock_skus' => 0,
        ];

        $salesTrend = $salesTrend ?? [
            'labels' => ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25', 'Apr 30'],
            'values' => [11800, 14200, 13500, 15800, 17200, 16800, 18400],
        ];

        $categoryBreakdown = $categoryBreakdown ?? [
            'labels' => ['Exhausts', 'Helmets', 'Tires', 'Brakes', 'Accessories'],
            'values' => [34, 24, 18, 12, 12],
        ];

        $brandMomentum = $brandMomentum ?? [
            ['brand' => 'Honda', 'value' => 0, 'share' => 0],
            ['brand' => 'Yamaha', 'value' => 0, 'share' => 0],
            ['brand' => 'Kawasaki', 'value' => 0, 'share' => 0],
            ['brand' => 'Suzuki', 'value' => 0, 'share' => 0],
        ];

        $topProducts = $topProducts ?? [
            ['rank' => 1, 'name' => 'Akrapovic Exhaust', 'category' => 'Exhausts', 'qty' => 132, 'revenue' => '₱15,840'],
            ['rank' => 2, 'name' => 'SHARK EVO Helmet', 'category' => 'Helmets', 'qty' => 98, 'revenue' => '₱11,760'],
            ['rank' => 3, 'name' => 'Dunlop Q3+ Tire', 'category' => 'Tires', 'qty' => 84, 'revenue' => '₱10,080'],
            ['rank' => 4, 'name' => 'Brembo Brake Pads', 'category' => 'Brakes', 'qty' => 65, 'revenue' => '₱5,850'],
            ['rank' => 5, 'name' => 'Cub Battery', 'category' => 'Accessories', 'qty' => 58, 'revenue' => '₱4,640'],
        ];
    @endphp

    <div class="space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Sales Analytics</h1>
                <p class="text-sm text-slate-500 mt-1">Track revenue performance, product demand, and market momentum in a compact analytics workspace.</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <button class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">📅 Apr 1, 2026 - Apr 30, 2026</button>
                <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">Export report</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Inventory value</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">₱{{ number_format($quickStats['total_inventory_value'], 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">Current value of stocked items across active inventory.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Average unit price</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">₱{{ number_format($quickStats['average_unit_price'], 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">Average per-unit price for products currently in stock.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Units in stock</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($quickStats['total_units_in_stock']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Total quantity of items currently available for sale.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Healthy SKUs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($quickStats['healthy_skus']) }}</p>
                <p class="mt-1 text-xs text-slate-500">SKUs with stock above reorder threshold and ready to sell.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Low stock SKUs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($quickStats['low_stock_skus']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Items at or below reorder level that need replenishment soon.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Out of stock SKUs</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($quickStats['out_of_stock_skus']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Products currently unavailable that need immediate restock.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 items-start gap-4">
            <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Sales trend</h2>
                        <p class="text-xs text-slate-500 mt-1">Revenue progression across the selected date range.</p>
                    </div>
                    <div class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 p-2 text-[11px] font-medium text-slate-600">
                        <span class="px-2 py-1 rounded-xl bg-slate-100">Monthly</span>
                        <span class="px-2 py-1 rounded-xl hover:bg-slate-100 cursor-pointer">Weekly</span>
                        <span class="px-2 py-1 rounded-xl hover:bg-slate-100 cursor-pointer">Daily</span>
                    </div>
                </div>
                <div class="mt-4 h-40">
                    <canvas id="salesTrendChart" class="h-full w-full"></canvas>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Category distribution</h2>
                        <p class="text-xs text-slate-500 mt-1">Where revenue is strongest by product segment.</p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Share</span>
                </div>
                <div class="mt-3 flex flex-col items-start gap-3">
                    <canvas id="categoryChart" class="h-32 w-32"></canvas>
                    <div class="w-full grid grid-cols-1 gap-2 text-[11px]">
                        @foreach($categoryBreakdown['labels'] as $index => $label)
                            @php
                                $colors = ['bg-teal-500','bg-emerald-500','bg-amber-500','bg-sky-500','bg-rose-500'];
                            @endphp
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <span class="h-2.5 w-2.5 rounded-full {{ $colors[$index % count($colors)] }}"></span>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $label }}</p>
                                    <p class="text-slate-500">{{ $categoryBreakdown['values'][$index] }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Top selling products</h2>
                        <p class="text-[11px] text-slate-500 mt-1">The best performing SKUs by revenue and volume.</p>
                    </div>
                    <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">View all</button>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Rank</th>
                                <th class="px-3 py-3">Product</th>
                                <th class="px-3 py-3">Category</th>
                                <th class="px-3 py-3">Units</th>
                                <th class="px-3 py-3">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($topProducts as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-3 font-semibold text-slate-900">{{ $product['rank'] }}</td>
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $product['category'] }}</td>
                                    <td class="px-3 py-3 text-slate-900">{{ $product['qty'] }}</td>
                                    <td class="px-3 py-3 font-semibold text-slate-900">{{ $product['revenue'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Brand momentum</h2>
                    <p class="text-xs text-slate-500 mt-1">Inventory exposure by top-performing brands.</p>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($brandMomentum as $brand)
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $brand['brand'] }}</p>
                                    <p class="text-sm text-slate-500">Inventory weight: {{ number_format($brand['value'], 2) }}</p>
                                </div>
                                <span class="text-sm font-semibold text-emerald-600">{{ $brand['share'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Executive summary</h2>
                        <p class="text-xs text-slate-500 mt-1">Key observations and recommended actions for the next period.</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-3 py-2 text-xs text-slate-600">
                        <span class="font-semibold text-slate-900">High priority:</span> Focus on helmet campaigns for continued revenue growth.
                    </div>
                </div>
                <div class="mt-4 grid gap-3 lg:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Opportunity</p>
                        <p class="mt-3 text-sm text-slate-700">Boost cross-sell bundles for high-margin accessories during weekend promotions.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Attention</p>
                        <p class="mt-3 text-sm text-slate-700">Review Mindanao stock replenishment after a strong 7.1% lift in sales demand.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Next step</p>
                        <p class="mt-3 text-sm text-slate-700">Align pricing and promotions ahead of next month’s seasonal demand spike.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const salesTrendCtx = document.getElementById('salesTrendChart');
            if (salesTrendCtx) {
                new Chart(salesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($salesTrend['labels']),
                        datasets: [{
                            label: 'Revenue',
                            data: @json($salesTrend['values']),
                            borderColor: '#0f766e',
                            backgroundColor: 'rgba(15, 118, 110, 0.12)',
                            pointBackgroundColor: '#0f766e',
                            pointBorderColor: '#fff',
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.35,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#475569' }
                            },
                            y: {
                                grid: { color: '#e2e8f0' },
                                ticks: {
                                    color: '#475569',
                                    callback: (value) => '₱' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            }

            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($categoryBreakdown['labels']),
                        datasets: [{
                            data: @json($categoryBreakdown['values']),
                            backgroundColor: ['#0f766e', '#16a34a', '#f59e0b', '#0ea5e9', '#ef4444'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: '72%'
                    }
                });
            }
        </script>
    @endpush
</x-layouts.app>
