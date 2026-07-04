document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('dashboard-root');
    if (!root) {
        return;
    }

    const url = root.dataset.dashboardUrl || '/dashboard/data';
    const refreshInterval = Number(root.dataset.refreshInterval || 15000);

    const currency = new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        maximumFractionDigits: 2,
    });

    let dashboardSalesTrendData = null;
    const salesRangeButtons = Array.from(document.querySelectorAll('.sales-range-btn'));

    const setMetric = (element, value, prefix = '') => {
        if (!element) {
            return;
        }

        if (typeof value === 'number') {
            element.textContent = `${prefix}${value.toLocaleString('en-PH')}`;
            return;
        }

        element.textContent = value;
    };

    const renderMetric = (metric, key) => {
        const valueEl = document.getElementById(`${key}Value`);
        const comparisonEl = document.getElementById(`${key}Comparison`);
        const comparison = metric?.comparison;

        if (valueEl) {
            if (key === 'sales' || key === 'profit') {
                setMetric(valueEl, metric.value, '₱');
            } else {
                setMetric(valueEl, metric.value);
            }
        }

        if (comparisonEl) {
            if (!comparison || comparison.direction === 'neutral') {
                comparisonEl.textContent = 'No previous period data';
                comparisonEl.className = 'text-gray-500 text-xs mt-1 font-medium';
                return;
            }

            const arrow = comparison.direction === 'up' ? '↑' : '↓';
            const color = comparison.direction === 'up' ? 'text-teal-500' : 'text-red-500';
            comparisonEl.textContent = `${arrow} ${comparison.value}% vs. previous period`;
            comparisonEl.className = `${color} text-xs mt-1 font-medium`;
        }
    };

    const renderCategoryChart = (chartData) => {
        const legend = document.getElementById('categoryLegend');
        const canvas = document.getElementById('categoryChart');

        if (!canvas) {
            return;
        }

        if (window.dashboardCategoryChart) {
            window.dashboardCategoryChart.destroy();
        }

        if (chartData?.labels?.length) {
            const ctx = canvas.getContext('2d');
            window.dashboardCategoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.data,
                        backgroundColor: ['#14b8a6', '#22c55e', '#eab308', '#a855f7', '#9ca3af'],
                        borderColor: '#fff',
                        borderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
                },
            });
        }

        if (legend && chartData?.legend?.length) {
            legend.innerHTML = chartData.legend.map((item, index) => {
                const colors = ['bg-teal-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-gray-400'];
                return `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 ${colors[index % colors.length]} rounded-full"></span>
                            <span class="text-gray-700">${item.label}</span>
                        </div>
                        <span class="text-gray-900 font-medium">${currency.format(item.value)}</span>
                    </div>
                `;
            }).join('');
        }
    };

    const setRangeLabel = (value) => {
        const rangeLabelEl = document.getElementById('dashboardRangeLabel');
        if (!rangeLabelEl) {
            return;
        }

        rangeLabelEl.textContent = value || '📅 No range available';
    };

    const renderComparisonChart = (comparisonChart) => {
        const canvas = document.getElementById('barChart');
        if (!canvas) {
            return;
        }

        if (window.dashboardComparisonChart) {
            window.dashboardComparisonChart.destroy();
        }

        if (!comparisonChart?.labels?.length || !comparisonChart?.datasets?.length) {
            return;
        }

        const ctx = canvas.getContext('2d');
        window.dashboardComparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: comparisonChart.labels,
                datasets: comparisonChart.datasets.map((dataset, index) => ({
                    label: dataset.label,
                    data: dataset.data,
                    backgroundColor: dataset.backgroundColor || ['#14b8a6', '#818CF8'],
                    borderColor: dataset.borderColor || ['#14b8a6', '#818CF8'],
                    borderWidth: 1,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 } } },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (value) => currency.format(value) },
                    },
                },
            },
        });
    };

    const renderTopItems = (items) => {
        const body = document.getElementById('topItemsTableBody');
        if (!body) {
            return;
        }

        if (!items?.length) {
            body.innerHTML = '<tr><td colspan="5" class="py-2 px-1 text-center text-gray-500 text-xs">No data available</td></tr>';
            return;
        }

        body.innerHTML = items.map((item) => `
            <tr class="hover:bg-gray-50 transition">
                <td class="py-1 px-1"><span class="font-bold text-gray-900">${item.rank}</span></td>
                <td class="py-1 px-1">
                    <div class="flex items-center gap-1">
                        <div class="w-4 h-4 bg-gray-200 rounded"></div>
                        <span class="text-gray-900 font-medium text-xs">${item.name}</span>
                    </div>
                </td>
                <td class="py-1 px-1 text-gray-600">${item.category}</td>
                <td class="py-1 px-1 text-gray-900">${item.qty}</td>
                <td class="py-1 px-1 text-gray-900 font-medium">${currency.format(item.revenue)}</td>
            </tr>
        `).join('');
    };

    const renderInventory = (inventory) => {
        if (!inventory) {
            return;
        }

        const totalProducts = document.getElementById('totalProductsValue');
        const lowStock = document.getElementById('lowStockValue');
        const outOfStock = document.getElementById('outOfStockValue');
        const inStock = document.getElementById('inStockValue');

        if (totalProducts) totalProducts.textContent = inventory.total_products?.toLocaleString('en-PH') ?? '0';
        if (lowStock) lowStock.textContent = inventory.low_stock?.toLocaleString('en-PH') ?? '0';
        if (outOfStock) outOfStock.textContent = inventory.out_of_stock?.toLocaleString('en-PH') ?? '0';
        if (inStock) inStock.textContent = inventory.in_stock?.toLocaleString('en-PH') ?? '0';
    };

    const setActiveSalesRange = (range) => {
        salesRangeButtons.forEach((button) => {
            const isActive = button.dataset.range === range;
            button.classList.toggle('bg-teal-500', isActive);
            button.classList.toggle('text-white', isActive);
            button.classList.toggle('shadow-sm', isActive);
            button.classList.toggle('bg-slate-100', !isActive);
            button.classList.toggle('text-gray-600', !isActive);
        });
    };

    const updateSalesChart = (range) => {
        if (!window.dashboardSalesChart || !dashboardSalesTrendData) {
            return;
        }

        const data = dashboardSalesTrendData[range] || dashboardSalesTrendData.monthly;
        if (!data?.labels?.length) {
            return;
        }

        window.dashboardSalesChart.data.labels = data.labels;
        window.dashboardSalesChart.data.datasets[0].data = data.values;
        window.dashboardSalesChart.update();
        setActiveSalesRange(range);
    };

    const renderSalesChart = (salesChart) => {
        const canvas = document.getElementById('salesChart');
        if (!canvas) {
            return;
        }

        dashboardSalesTrendData = salesChart ?? null;
        const chartData = salesChart?.monthly || (salesChart?.labels ? {
            labels: salesChart.labels,
            values: salesChart.data,
        } : null);
        if (!chartData?.labels?.length) {
            return;
        }

        if (window.dashboardSalesChart) {
            window.dashboardSalesChart.destroy();
        }

        const ctx = canvas.getContext('2d');
        window.dashboardSalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.values,
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, 0.12)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0f766e',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e2e8f0' },
                        ticks: { callback: (value) => currency.format(value) },
                    },
                },
            },
        });

        setActiveSalesRange('monthly');
    };

    const loadDashboard = () => {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => response.json())
            .then((data) => {
                renderMetric(data.metrics?.sales, 'sales');
                renderMetric(data.metrics?.transactions, 'transactions');
                renderMetric(data.metrics?.profit, 'profit');
                renderMetric(data.metrics?.items_sold, 'itemsSold');
                renderSalesChart(data.sales_chart);
                setRangeLabel(data.range_label);
                renderCategoryChart(data.category_chart);
                renderComparisonChart(data.comparison_chart);
                renderTopItems(data.top_items);
                renderInventory(data.inventory);
            })
            .catch((error) => {
                console.error('Dashboard load failed', error);
            });
    };

    salesRangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            updateSalesChart(button.dataset.range);
        });
    });

    loadDashboard();

    if (refreshInterval > 0) {
        window.setInterval(loadDashboard, refreshInterval);
    }
});

