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
    let currentSalesRange = 'monthly'; // <-- itong variable ang mag-remember kung anong button ang huling na-click
    const salesRangeButtons = Array.from(document.querySelectorAll('.sales-range-btn'));

    const CATEGORY_DEFS = [
        { name: 'Exhaust', color: '#06b6d4' },
        { name: 'Helmets', color: '#a3e635' },
        { name: 'Tires', color: '#fbbf24' },
        { name: 'Brakes', color: '#ef4444' },
        { name: 'Oils', color: '#fb923c' },
        { name: 'Batteries', color: '#3b82f6' },
        { name: 'Accessories', color: '#10b981' },
    ];
    const INACTIVE_DOT_COLOR = '#7e7e7e8c'; // muted/gray — kapag walang benta ang category sa araw na 'yon
    const EMPTY_RING_COLOR = '#7e7e7e8c'; // flat gray track kapag walang laman/sales

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

    // ---- Neon glow plugin: kada segment na may benta ("active"), gumuhit ng
    // dagdag na glowing stroke sa ibabaw ng arc gamit ang sariling kulay nito
    // (parehong "neon glow" technique gaya ng ginamit sa Sales Overview line
    // chart, pero ang kulay ay galing sa palette mo). ----
    const glowActiveSegmentsPlugin = {
        id: 'glowActiveSegments',
        afterDatasetsDraw(chart) {
            if (chart.canvas.id !== 'categoryChart' || !chart.$activeFlags) {
                return;
            }

            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);
            const colors = chart.data.datasets[0].backgroundColor;

            meta.data.forEach((arc, index) => {
                if (!chart.$activeFlags[index]) {
                    return;
                }

                const props = arc.getProps(
                    ['startAngle', 'endAngle', 'innerRadius', 'outerRadius', 'x', 'y'],
                    true
                );
                const midRadius = (props.innerRadius + props.outerRadius) / 2;
                const thickness = props.outerRadius - props.innerRadius;

                ctx.save();
                ctx.shadowColor = colors[index];
                ctx.shadowBlur = 20;
                ctx.beginPath();
                ctx.arc(props.x, props.y, midRadius, props.startAngle, props.endAngle);
                ctx.lineWidth = thickness;
                ctx.strokeStyle = colors[index];
                ctx.stroke();
                // Double pass para mas makapal/mas neon ang glow.
                ctx.shadowBlur = 30;
                ctx.stroke();
                ctx.restore();
            });
        },
    };

    // Plugin to ensure no canvas shadow/glow is applied to the sales line chart
    const clearLineShadowPlugin = {
        id: 'clearLineShadow',
        beforeDatasetsDraw(chart) {
            if (!chart || !chart.ctx) return;
            if (chart.canvas.id === 'salesChart') {
                const ctx = chart.ctx;
                ctx.save();
                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
                ctx.restore();
            }
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

        // Use incoming data directly from backend - support both predefined and custom categories
        const incomingLabels = chartData?.labels || [];
        const incomingValues = chartData?.data || [];
        
        // Create a color map for predefined categories
        const colorMap = {};
        CATEGORY_DEFS.forEach(cat => {
            colorMap[cat.name.toLowerCase()] = cat.color;
        });
        
        // Color palette for custom categories (not in predefined list)
        const customColors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'];
        let customColorIndex = 0;
        
        // Assign colors to incoming categories
        const categoryColors = incomingLabels.map((label, index) => {
            const lowerLabel = String(label).trim().toLowerCase();
            if (colorMap[lowerLabel]) {
                return colorMap[lowerLabel];
            }
            // Use custom color for non-predefined categories
            const color = customColors[customColorIndex % customColors.length];
            customColorIndex++;
            return color;
        });

        const total = incomingValues.reduce((sum, value) => sum + Number(value || 0), 0);
        const isEmpty = total <= 0;

        const chartLabels = isEmpty ? ['No data'] : incomingLabels;
        const chartValues = isEmpty ? [1] : incomingValues.map(v => Number(v || 0));
        const colors = isEmpty ? [EMPTY_RING_COLOR] : categoryColors;

        const ctx = canvas.getContext('2d');
        window.dashboardCategoryChart = new Chart(ctx, {
            type: 'doughnut',
            plugins: [],
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartValues,
                    backgroundColor: colors,
                    borderColor: 'transparent',
                    borderWidth: 0,
                    hoverBorderColor: 'transparent',
                    hoverBorderWidth: 0,
                    borderRadius: 8,
                    hoverOffset: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                circumference: 270,
                rotation: -135,
                layout: { padding: 0 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: !isEmpty,
                        backgroundColor: '#1a1a1a',
                        titleColor: '#32FFFD',
                        bodyColor: '#ffffff',
                        borderColor: '#32FFFD',
                        borderWidth: 1,
                        padding: 8,
                        callbacks: {
                            label: (context) => `${context.label}: ${currency.format(context.parsed)}`,
                        },
                    },
                },
            },
        });

        const activeFlags = chartValues.map((value) => value > 0);
        window.dashboardCategoryChart.$activeFlags = isEmpty ? [false] : activeFlags;

        const centerValueEl = document.getElementById('categoryCenterValue');
        const centerCaptionEl = document.getElementById('categoryCenterCaption');
        if (centerValueEl) {
            centerValueEl.textContent = isEmpty ? '0' : currency.format(total);
        }
        if (centerCaptionEl) {
            centerCaptionEl.textContent = isEmpty ? 'No sales today' : "Today's Sales";
        }

        if (legend) {
            // Always show all predefined categories with their colors
            // Create a map of incoming data for lookup
            const valueMap = {};
            incomingLabels.forEach((label, index) => {
                valueMap[String(label).trim().toLowerCase()] = chartValues[index] || 0;
            });

            legend.innerHTML = CATEGORY_DEFS.map((cat) => {
                const catLower = cat.name.toLowerCase();
                const value = valueMap[catLower] || 0;
                const hasValue = value > 0;
                const dotStyle = `background-color: ${cat.color};`;
                return `
                    <div class="cat-legend-row" style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="cat-dot" style="${dotStyle} width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span class="cat-label" style="color: ${hasValue ? '#000000' : 'rgba(0,0,0,0.55)'};">${cat.name}</span>
                        </div>
                        ${hasValue ? `<span class="cat-value" style="color: #000000; font-weight: 600;">${currency.format(value)}</span>` : ''}
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
                    backgroundColor: dataset.backgroundColor || (index === 0 ? '#175000' : '#175000'),
                    borderColor: dataset.borderColor || (index === 0 ? '#175000' : '#175000'),
                    borderWidth: 1,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 } } },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleColor: '#2ea300',
                        bodyColor: '#ffffff',
                        borderColor: '#2ea300',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 12, weight: '500' },
                        callbacks: {
                            label: (context) => currency.format(context.parsed.y),
                        },
                    },
                },
                scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(107,114,128,0.12)' },
                            ticks: { color: '#374151', callback: (value) => currency.format(value) },
                        },
                        x: {
                            grid: { color: 'rgba(107,114,128,0.08)' },
                            ticks: { color: '#374151' },
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

        // Row values (sa loob ng dark Inventory Levels container)
        const totalProducts = document.getElementById('totalProductsValue');
        const lowStock = document.getElementById('lowStockValue');
        const outOfStock = document.getElementById('outOfStockValue');
        const inStock = document.getElementById('inStockValue');

        // Modal values (sa loob ng pop-up na lumalabas kapag na-click ang
        // arrow button — dinidissolve ang rows tapos lumalabas ito sa gitna)
        const totalProductsModal = document.getElementById('totalProductsModalValue');
        const lowStockModal = document.getElementById('lowStockModalValue');
        const outOfStockModal = document.getElementById('outOfStockModalValue');
        const inStockModal = document.getElementById('inStockModalValue');

        const totalProductsText = inventory.total_products?.toLocaleString('en-PH') ?? '0';
        const lowStockText = inventory.low_stock?.toLocaleString('en-PH') ?? '0';
        const outOfStockText = inventory.out_of_stock?.toLocaleString('en-PH') ?? '0';
        const inStockText = inventory.in_stock?.toLocaleString('en-PH') ?? '0';

        if (totalProducts) totalProducts.textContent = totalProductsText;
        if (lowStock) lowStock.textContent = lowStockText;
        if (outOfStock) outOfStock.textContent = outOfStockText;
        if (inStock) inStock.textContent = inStockText;

        if (totalProductsModal) totalProductsModal.textContent = totalProductsText;
        if (lowStockModal) lowStockModal.textContent = lowStockText;
        if (outOfStockModal) outOfStockModal.textContent = outOfStockText;
        if (inStockModal) inStockModal.textContent = inStockText;
    };

    const setActiveSalesRange = (range) => {
        salesRangeButtons.forEach((button) => {
            const isActive = button.dataset.range === range;
            button.classList.toggle('active', isActive);
            button.style.backgroundColor = '';
        });
    };

    const updateSalesChart = (range) => {
        currentSalesRange = range; // i-remember yung pinili ng user

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

        // gamitin yung currentSalesRange (huling pinili ng user) sa tuwing may
        // bagong data mula sa auto-refresh, hindi laging 'monthly'
        const chartData = salesChart?.[currentSalesRange]
            || salesChart?.monthly
            || (salesChart?.labels ? { labels: salesChart.labels, values: salesChart.data } : null);
        if (!chartData?.labels?.length) {
            return;
        }

        if (window.dashboardSalesChart) {
            window.dashboardSalesChart.destroy();
        }

        const ctx = canvas.getContext('2d');

        // subtle gradient fill sa ilalim ng linya
        // No gradient fill for light theme: keep background transparent
        const gradient = 'transparent';

        window.dashboardSalesChart = new Chart(ctx, {
            type: 'line',
            plugins: [clearLineShadowPlugin],
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.values,
                    borderColor: '#059669',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#059669',
                    pointHoverBackgroundColor: '#059669',
                    pointHoverBorderColor: '#0f0f0f',
                    pointHoverBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                // FIX: dati "true" ito kaya hindi na-ffill nung chart yung buong height
                // ng #salesOverviewBody container (kaya masyadong nasa taas yung linya).
                // Sa "false", susundin ng chart yung actual width/height ng container.
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 40, bottom: 0, left: 0, right: 0 },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1a1a1a',
                        titleColor: '#059669',
                        bodyColor: '#ffffff',
                        borderColor: '#059669',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 13, weight: 'bold' },
                        callbacks: {
                            label: (context) => currency.format(context.parsed.y),
                        },
                    },
                },
                scales: {
                    x: {
                            grid: { color: 'rgb(214, 214, 214)' },
                            ticks: { color: '#374151', font: { size: 10 } },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgb(214, 214, 214)' },
                            ticks: {
                                color: '#616161',
                                font: { size: 10 },
                                callback: (value) => currency.format(value),
                            },
                        },
                },
            },
        });

        // i-highlight yung button na tugma sa currentSalesRange, hindi lagi 'monthly'
        setActiveSalesRange(currentSalesRange);
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