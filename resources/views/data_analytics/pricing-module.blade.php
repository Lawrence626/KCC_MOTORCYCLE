<x-layouts.app :title="__('Pricing Module')">
    <div class="space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Pricing Module</h1>
                <p class="text-xs text-slate-500 mt-1">Analyze pricing trends, monitor stock value, and track recent price breaks.</p>
            </div>
            <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">Export price report</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Average unit price</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">₱{{ number_format($averageUnitPrice, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500">Average current price for active inventory items.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Most expensive SKU</p>
                <p class="mt-2 text-xl font-semibold text-slate-900">{{ $mostExpensive?->name ?? '—' }}</p>
                <p class="mt-1 text-xs text-slate-500">₱{{ number_format($mostExpensive?->unit_price ?? 0, 2) }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-[10px] uppercase tracking-[0.24em] text-slate-400">Cheapest SKU</p>
                <p class="mt-2 text-xl font-semibold text-slate-900">{{ $cheapest?->name ?? '—' }}</p>
                <p class="mt-1 text-xs text-slate-500">₱{{ number_format($cheapest?->unit_price ?? 0, 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.4fr_1fr] gap-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Category pricing overview</h2>
                        <p class="text-xs text-slate-500 mt-1">Average price by category and inventory exposure.</p>
                    </div>
                    <span class="text-[10px] uppercase tracking-[0.2em] text-slate-400">Current</span>
                </div>
                <div class="mt-4 h-52">
                    <canvas id="pricingCategoryChart" class="h-full w-full"></canvas>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach($pricingByCategory as $row)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $row['label'] }}</p>
                            <p class="text-[11px] text-slate-500">Avg price: ₱{{ number_format($row['avg_price'], 2) }}</p>
                            <p class="text-[11px] text-slate-500">Stock units: {{ number_format($row['total_qty']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Latest price updates</h2>
                    <p class="text-xs text-slate-500 mt-1">Track recent unit price revisions across inventory.</p>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Old</th>
                                <th class="px-4 py-3">New</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($priceUpdates as $update)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-4 font-semibold text-slate-900">{{ $update['product'] }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $update['old_price'] ? '₱'.number_format($update['old_price'], 2) : '—' }}</td>
                                    <td class="px-4 py-4 text-slate-900">₱{{ number_format($update['new_price'], 2) }}</td>
                                    <td class="px-4 py-4 text-slate-500">{{ $update['updated_at'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">No recent pricing updates available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const pricingCategoryCanvas = document.getElementById('pricingCategoryChart');
                if (!pricingCategoryCanvas || typeof Chart === 'undefined') {
                    return;
                }

                const categoryLabels = @json($pricingByCategory->pluck('label')->map(fn($label) => $label ?: 'Uncategorized')->toArray());
                const categoryPrices = @json($pricingByCategory->pluck('avg_price')->map(fn($price) => (float) $price)->toArray());

                if (!categoryLabels.length || !categoryPrices.length) {
                    return;
                }

                new Chart(pricingCategoryCanvas, {
                    type: 'bar',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Average unit price',
                            data: categoryPrices,
                            backgroundColor: categoryLabels.map(() => '#0f766e'),
                            borderRadius: 12,
                            maxBarThickness: 40,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return '₱' + Number(context.parsed.y).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#475569' }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e2e8f0' },
                                ticks: {
                                    color: '#475569',
                                    callback: function (value) {
                                        return '₱' + Number(value).toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-layouts.app>
