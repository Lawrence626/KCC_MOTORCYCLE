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

        @if($supplierCostAlerts->isNotEmpty())
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.24em] text-amber-600">Pricing alert</p>
                        <h2 class="mt-1 text-base font-semibold text-amber-900">Supplier cost review needed</h2>
                        <p class="mt-1 text-sm text-amber-800">
                            @foreach($supplierCostAlerts as $alert)
                                {{ $alert->product?->name ?? 'A product' }} supplier cost increased by {{ number_format((float) $alert->change_percentage, 0) }}%. Review retail pricing to protect your target margin.
                            @endforeach
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">⚠ Needs attention</span>
                </div>
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Supplier Cost Analysis</h2>
                    <p class="text-xs text-slate-500 mt-1">Track recent supplier cost shifts and pricing recommendations for administrators.</p>
                </div>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Previous Cost</th>
                            <th class="px-4 py-3">Current Cost</th>
                            <th class="px-4 py-3">Change</th>
                            <th class="px-4 py-3">Suggested Retail Price</th>
                            <th class="px-4 py-3">DSS Recommendation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($supplierCostAnalysis as $analysis)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-4 font-semibold text-slate-900">{{ $analysis->product?->name ?? $analysis->product?->product_name ?? 'Unknown' }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $analysis->previous_cost !== null && $analysis->previous_cost > 0 ? '₱' . number_format((float) $analysis->previous_cost, 2) : '—' }}</td>
                                <td class="px-4 py-4 text-slate-900">₱{{ number_format((float) $analysis->supplier_cost, 2) }}</td>
                                <td class="px-4 py-4">
                                    @php
                                        $change = (float) ($analysis->change_percentage ?? 0);
                                        $badgeClass = $change > 0 ? 'bg-red-100 text-red-700' : ($change < 0 ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700');
                                        $changeLabel = $change != 0 ? (($change > 0 ? '+' : '') . number_format($change, 2) . '%') : '0.00%';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $changeLabel }}</span>
                                </td>
                                <td class="px-4 py-4 text-slate-900 font-semibold">
                                    @php
                                        $suggestedPrice = (float) $analysis->supplier_cost / 0.70;
                                    @endphp
                                    ₱{{ number_format($suggestedPrice, 2) }}
                                </td>
                                <td class="px-4 py-4 text-slate-700">{{ $analysis->recommendation ?? 'Maintain current retail price.' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">No supplier cost changes available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4">
                {{ $supplierCostAnalysis->links() }}
            </div>

            @if($supplierCostHighlight)
                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Retail recommendation</p>
                    <div class="mt-2 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $supplierCostHighlight->product?->name ?? 'Selected product' }}</p>
                            <p class="mt-1 text-sm text-slate-600">Current Retail Price: ₱{{ number_format((float) ($supplierCostHighlight->product?->unit_price ?? 0), 2) }}</p>
                            <p class="text-sm text-slate-600">Suggested Retail Price: ₱{{ number_format((float) $supplierCostHighlight->suggested_retail_price, 2) }}</p>
                            <p class="mt-2 text-sm text-slate-600">Reason: {{ $supplierCostHighlight->reason ?? 'Supplier cost has changed.' }}</p>
                        </div>
                        <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">{{ $supplierCostHighlight->recommendation ?? 'Review pricing' }}</span>
                    </div>
                </div>
            @endif
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
                    <h2 class="text-base font-semibold text-slate-900">Retail Price Update</h2>
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
                                    <td class="px-4 py-4 font-semibold text-slate-900">{{ $update->product->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ data_get($update, 'metadata.old_price') ? '₱' . number_format(data_get($update, 'metadata.old_price'), 2) : '—' }}</td>
                                    <td class="px-4 py-4 text-slate-900">₱{{ number_format((float) $update->unit_price, 2) }}</td>
                                    <td class="px-4 py-4 text-slate-500">{{ $update->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-500">No recent pricing updates available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 px-4">
                    {{ $priceUpdates->links() }}
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
