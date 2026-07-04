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
            'monthly' => [
                'labels' => ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                'values' => [0, 0, 11800, 14200, 16800, 18400],
            ],
            'weekly' => [
                'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                'values' => [0, 0, 0, 0, 0],
            ],
            'daily' => [
                'labels' => ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25', 'Apr 30'],
                'values' => [11800, 14200, 13500, 15800, 17200, 16800, 18400],
            ],
        ];

        $categoryBreakdown = $categoryBreakdown ?? [
            'labels' => ['Exhausts', 'Helmets', 'Tires', 'Brakes', 'Accessories'],
            'values' => [34, 24, 18, 12, 12],
            'shares' => [34, 24, 18, 12, 12],
            'formatted' => ['₱34.00', '₱24.00', '₱18.00', '₱12.00', '₱12.00'],
        ];

        $categoryBreakdown['formatted'] = $categoryBreakdown['formatted'] ?? array_map(fn($value) => '₱' . number_format((float) $value, 2), $categoryBreakdown['values'] ?? []);
        $categoryBreakdown['shares'] = $categoryBreakdown['shares'] ?? array_fill(0, count($categoryBreakdown['labels'] ?? []), 0);



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
                        <button type="button" data-range="monthly" class="sales-trend-range-btn px-2 py-1 rounded-xl bg-slate-900 text-white">Monthly</button>
                        <button type="button" data-range="weekly" class="sales-trend-range-btn px-2 py-1 rounded-xl hover:bg-slate-100">Weekly</button>
                        <button type="button" data-range="daily" class="sales-trend-range-btn px-2 py-1 rounded-xl hover:bg-slate-100">Daily</button>
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
                        <p class="text-xs text-slate-500 mt-1">Revenue contribution per product category.</p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Revenue</span>
                </div>
                <div class="mt-4 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-start">
                    <div class="h-52 w-full">
                        <canvas id="categoryChart" class="h-full w-full"></canvas>
                    </div>
                    <div class="max-h-52 overflow-y-auto pr-1 space-y-2 text-sm">
                        @php
                            $legendColors = ['bg-teal-500','bg-emerald-500','bg-amber-500','bg-sky-500','bg-rose-500','bg-violet-500','bg-cyan-500','bg-lime-500','bg-fuchsia-500','bg-orange-500'];
                        @endphp
                        @foreach($categoryBreakdown['labels'] as $index => $label)
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <span class="h-2.5 w-2.5 rounded-full {{ $legendColors[$index % count($legendColors)] }}"></span>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $label }}</p>
                                    <p class="text-slate-500">
                                        {{ data_get($categoryBreakdown, 'formatted.' . $index, '—') }} • {{ data_get($categoryBreakdown, 'shares.' . $index, 0) }}%
                                    </p>
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
                    <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition" id="viewAllTopProductsBtn">View all</button>
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
                        <tbody class="divide-y divide-slate-200 bg-white" id="topProductsBody">
                            @forelse($topProducts as $product)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-3 py-3 font-semibold text-slate-900">{{ $product['rank'] }}</td>
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-slate-900">{{ $product['name'] }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $product['category'] }}</td>
                                    <td class="px-3 py-3 text-slate-900">{{ $product['qty'] }}</td>
                                    <td class="px-3 py-3 font-semibold text-slate-900">{{ $product['revenue'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-3 text-center text-slate-500">No sales data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Fast-moving products</h2>
                        <p class="text-[11px] text-slate-500 mt-1">Top 5 best-selling products by quantity.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Product</th>
                                <th class="px-3 py-3 text-right">Quantity Sold</th>
                                <th class="px-3 py-3 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="fastMovingProductsBody">
                            <tr>
                                <td colspan="3" class="px-3 py-3 text-center text-slate-500">No sales data available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Slow-moving products</h2>
                        <p class="text-[11px] text-slate-500 mt-1">Bottom 5 least-selling products by quantity.</p>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-700">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.24em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Product</th>
                                <th class="px-3 py-3 text-right">Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white" id="slowMovingProductsBody">
                            <tr>
                                <td colspan="2" class="px-3 py-3 text-center text-slate-500">No sales data available</td>
                            </tr>
                        </tbody>
                    </table>
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
            const salesTrendRangeButtons = document.querySelectorAll('.sales-trend-range-btn');
            const salesTrendCtx = document.getElementById('salesTrendChart');
            const posSalesStorageKey = 'posTransactionHistory';

            const defaultSalesTrendData = {
                monthly: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    values: [0, 0, 0, 0, 0, 0],
                },
                weekly: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                    values: [0, 0, 0, 0, 0],
                },
                daily: {
                    labels: ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25', 'Apr 30'],
                    values: [0, 0, 0, 0, 0, 0, 0],
                }
            };

            const parseLocalTransactionHistory = () => {
                try {
                    const stored = localStorage.getItem(posSalesStorageKey);
                    if (!stored) return [];
                    const parsed = JSON.parse(stored);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    console.error('Could not read POS transaction history', error);
                    return [];
                }
            };

            const salesFromTransactions = (transactions) => {
                return transactions
                    .map((transaction) => ({
                        date: transaction.createdAt ? new Date(transaction.createdAt) : null,
                        total: Number(transaction.total) || 0,
                    }))
                    .filter((transaction) => transaction.date instanceof Date && !Number.isNaN(transaction.date.getTime()) && transaction.total > 0);
            };

            const buildTrendData = () => {
                const transactions = salesFromTransactions(parseLocalTransactionHistory());
                if (!transactions.length) {
                    return defaultSalesTrendData;
                }

                const now = new Date();
                const monthlyLabels = [];
                const monthlyValues = [];
                for (let index = 5; index >= 0; index -= 1) {
                    const month = new Date(now.getFullYear(), now.getMonth() - index, 1);
                    monthlyLabels.push(month.toLocaleString('en-US', { month: 'short' }));
                    monthlyValues.push(0);
                }

                const dailyLabels = [];
                const dailyValues = [];
                for (let index = 6; index >= 0; index -= 1) {
                    const day = new Date(now.getFullYear(), now.getMonth(), now.getDate() - index);
                    dailyLabels.push(day.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                    dailyValues.push(0);
                }

                const weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
                const weeklyValues = [0, 0, 0, 0, 0];
                const weekStart = new Date(now);
                weekStart.setHours(0, 0, 0, 0);
                weekStart.setDate(weekStart.getDate() - 34);

                transactions.forEach(({ date, total }) => {
                    const diffMs = date.getTime() - weekStart.getTime();
                    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

                    const monthLabel = date.toLocaleString('en-US', { month: 'short' });
                    const monthIndex = monthlyLabels.indexOf(monthLabel);
                    if (monthIndex !== -1) {
                        monthlyValues[monthIndex] += total;
                    }

                    const dayLabel = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    const dayIndex = dailyLabels.indexOf(dayLabel);
                    if (dayIndex !== -1) {
                        dailyValues[dayIndex] += total;
                    }

                    if (diffDays >= 0 && diffDays < 35) {
                        const weekIndex = Math.min(4, Math.floor(diffDays / 7));
                        weeklyValues[weekIndex] += total;
                    }
                });

                return {
                    monthly: { labels: monthlyLabels, values: monthlyValues.map((value) => Number(value.toFixed(2))) },
                    weekly: { labels: weeklyLabels, values: weeklyValues.map((value) => Number(value.toFixed(2))) },
                    daily: { labels: dailyLabels, values: dailyValues.map((value) => Number(value.toFixed(2))) },
                };
            };

            const serverSalesTrend = @json($salesTrend ?? null);
            const salesTrendData = (() => {
                const localData = buildTrendData();
                const serverData = serverSalesTrend && serverSalesTrend.monthly && Array.isArray(serverSalesTrend.monthly.values)
                    && serverSalesTrend.weekly && Array.isArray(serverSalesTrend.weekly.values)
                    && serverSalesTrend.daily && Array.isArray(serverSalesTrend.daily.values)
                    ? serverSalesTrend
                    : null;

                if (serverData) {
                    return serverData;
                }

                return localData;
            })();

            const salesTrendChartConfig = {
                type: 'line',
                data: {
                    labels: salesTrendData.monthly.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: salesTrendData.monthly.values,
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
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: {
                                color: '#475569',
                                callback: (value) => '₱' + value.toLocaleString()
                            }
                        }
                    }
                }
            };

            let salesTrendChart;
            if (salesTrendCtx) {
                salesTrendChart = new Chart(salesTrendCtx, salesTrendChartConfig);
            }

            const setActiveSalesTrendButton = (activeRange) => {
                salesTrendRangeButtons.forEach((button) => {
                    const isActive = button.dataset.range === activeRange;
                    button.classList.toggle('bg-slate-900', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('shadow-sm', isActive);
                    button.classList.toggle('bg-slate-100', !isActive);
                    button.classList.toggle('text-slate-600', !isActive);
                });
            };

            const updateSalesTrendChart = (range) => {
                const nextData = salesTrendData[range];
                if (!salesTrendChart || !nextData) {
                    return;
                }
                salesTrendChart.data.labels = nextData.labels;
                salesTrendChart.data.datasets[0].data = nextData.values;
                salesTrendChart.update();
                setActiveSalesTrendButton(range);
            };

            salesTrendRangeButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    updateSalesTrendChart(button.dataset.range);
                });
            });

            setActiveSalesTrendButton('monthly');

            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                const chartColors = ['#0f766e', '#16a34a', '#f59e0b', '#0ea5e9', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#ec4899', '#f97316'];
                const categories = @json($categoryBreakdown['labels']);
                const values = @json($categoryBreakdown['values']);
                const backgroundColors = categories.map((_, index) => chartColors[index % chartColors.length]);

                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categories,
                        datasets: [{
                            data: values,
                            backgroundColor: backgroundColors,
                            borderColor: '#ffffff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((sum, item) => sum + Number(item || 0), 0);
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                        return `${context.label}: ₱${Number(value).toLocaleString()} (${percent}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            // Fast-Moving and Slow-Moving Products Logic
            const aggregateProductsFromTransactions = () => {
                const transactions = parseLocalTransactionHistory();
                const productMap = new Map();

                transactions.forEach(transaction => {
                    if (!Array.isArray(transaction.items)) return;

                    transaction.items.forEach(item => {
                        const key = item.name || 'Unknown';
                        if (!productMap.has(key)) {
                            productMap.set(key, {
                                name: key,
                                quantity: 0,
                                revenue: 0,
                            });
                        }
                        const product = productMap.get(key);
                        product.quantity += (item.qty || 1);
                        product.revenue += (item.price * (item.qty || 1)) || 0;
                    });
                });

                return Array.from(productMap.values());
            };

            const renderFastMovingProducts = () => {
                const products = aggregateProductsFromTransactions();
                const fastMoving = products
                    .sort((a, b) => b.quantity - a.quantity)
                    .slice(0, 5);

                const tbody = document.getElementById('fastMovingProductsBody');
                if (!tbody) return;

                if (fastMoving.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-3 py-3 text-center text-slate-500">No sales data available</td></tr>';
                    return;
                }

                tbody.innerHTML = fastMoving.map((product) => {
                    const formattedRevenue = '₱' + Number(product.revenue).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-medium text-slate-900">${product.name}</td>
                            <td class="px-3 py-3 text-right text-slate-900">${product.quantity}</td>
                            <td class="px-3 py-3 text-right font-semibold text-slate-900">${formattedRevenue}</td>
                        </tr>
                    `;
                }).join('');
            };

            const renderSlowMovingProducts = () => {
                const products = aggregateProductsFromTransactions();
                const slowMoving = products
                    .filter(p => p.quantity > 0)
                    .sort((a, b) => a.quantity - b.quantity)
                    .slice(0, 5);

                const tbody = document.getElementById('slowMovingProductsBody');
                if (!tbody) return;

                if (slowMoving.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="2" class="px-3 py-3 text-center text-slate-500">No sales data available</td></tr>';
                    return;
                }

                tbody.innerHTML = slowMoving.map((product) => {
                    return `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-3 py-3 font-medium text-slate-900">${product.name}</td>
                            <td class="px-3 py-3 text-right text-slate-900">${product.quantity}</td>
                        </tr>
                    `;
                }).join('');
            };

            const updateProductTables = () => {
                renderFastMovingProducts();
                renderSlowMovingProducts();
            };

            // Initial render and update on range change
            updateProductTables();
            salesTrendRangeButtons.forEach((button) => {
                button.addEventListener('click', updateProductTables);
            });

            // ===== TOP PRODUCTS AUTO-UPDATE FUNCTIONALITY =====
            const topProductsBody = document.getElementById('topProductsBody');
            const autoUpdateInterval = 30000; // 30 seconds

            const fetchAndUpdateTopProducts = async () => {
                try {
                    const response = await fetch('/api/pos/transactions/top-selling?limit=5');
                    if (!response.ok) {
                        console.error('Failed to fetch top selling products');
                        return;
                    }

                    const data = await response.json();
                    const topProducts = data.data || [];

                    if (!topProducts.length) {
                        if (topProductsBody) {
                            topProductsBody.innerHTML = '<tr><td colspan="5" class="px-3 py-3 text-center text-slate-500">No sales data available</td></tr>';
                        }
                        return;
                    }

                    if (!topProductsBody) return;

                    topProductsBody.innerHTML = topProducts.map((product) => {
                        return `
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-3 py-3 font-semibold text-slate-900">${product.rank}</td>
                                <td class="px-3 py-3">
                                    <div class="font-medium text-slate-900">${product.name}</div>
                                </td>
                                <td class="px-3 py-3 text-slate-600">${product.category}</td>
                                <td class="px-3 py-3 text-slate-900">${product.qty}</td>
                                <td class="px-3 py-3 font-semibold text-slate-900">${product.revenue}</td>
                            </tr>
                        `;
                    }).join('');
                } catch (error) {
                    console.error('Error fetching top products:', error);
                }
            };

            // Fetch and update top products on page load and then every 30 seconds
            fetchAndUpdateTopProducts();
            setInterval(fetchAndUpdateTopProducts, autoUpdateInterval);

            // "View all" button - could navigate to a full products list
            const viewAllBtn = document.getElementById('viewAllTopProductsBtn');
            if (viewAllBtn) {
                viewAllBtn.addEventListener('click', () => {
                    console.log('View all top products');
                    // You can implement pagination or full list view here
                });
            }
        </script>
    @endpush
</x-layouts.app>
