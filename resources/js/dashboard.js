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

    // Notification system variables
    const notificationButton = document.getElementById('dashboardNotificationButton');
    const notificationDropdown = document.getElementById('dashboardNotificationDropdown');
    const notificationClose = document.getElementById('dashboardNotificationClose');
    const notificationList = document.getElementById('dashboardNotificationList');
    const lowStockBanner = document.getElementById('dashboardLowStockBanner');
    const lowStockBannerDismiss = document.getElementById('dashboardLowStockBannerDismiss');
    let lowStockAlertTimer = null;
    let currentBannerProductId = null;
    let lowStockBannerHandled = false;

    const clearLowStockAlertTimer = () => {
        if (lowStockAlertTimer) {
            window.clearTimeout(lowStockAlertTimer);
            lowStockAlertTimer = null;
        }
    };

    const updateNotificationBadge = (count) => {
        if (!notificationButton) {
            return;
        }

        const badge = notificationButton.querySelector('span');
        if (!badge) {
            return;
        }

        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.textContent = '0';
            badge.classList.add('hidden');
        }
    };

    const renderNotificationList = (notifications) => {
        if (!notificationList) {
            return;
        }

        const items = Array.isArray(notifications) ? notifications : [];
        if (items.length === 0) {
            notificationList.innerHTML = '<div class="p-4 text-sm text-slate-600">You have no new reorder notifications.</div>';
            return;
        }

        notificationList.innerHTML = items.map((notification) => `
            <div class="border-b border-slate-100 px-4 py-3 last:border-b-0" data-notification-item data-product-id="${notification.product_id ?? ''}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">${notification.product_name ?? 'Product'}</p>
                        <p class="mt-1 text-sm text-slate-600">${notification.message ?? 'Low stock alert.'}</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-600">Stock ${notification.stock_quantity ?? 0} • Reorder ${notification.reorder_level ?? 0}</span>
                    <a href="${notification.url ?? '/order/create'}" class="rounded-xl bg-emerald-600 px-3 py-2 text-[13px] font-semibold text-white transition hover:bg-emerald-700">Create order</a>
                </div>
            </div>
        `).join('');
    };

    const highlightNotification = (productId) => {
        if (!productId || !notificationList) {
            return;
        }

        const item = notificationList.querySelector(`[data-product-id="${productId}"]`);
        if (item) {
            item.classList.add('bg-amber-50', 'border-amber-200');
            item.scrollIntoView({ block: 'nearest' });
        }
    };

    const promoteLowStockAlert = () => {
        if (!lowStockBanner) {
            return;
        }

        clearLowStockAlertTimer();
        lowStockBannerHandled = true;
        lowStockBanner.classList.add('hidden', 'translate-x-6', 'opacity-0');
        lowStockBanner.classList.remove('translate-x-0', 'opacity-100');
        if (notificationDropdown) {
            notificationDropdown.classList.remove('hidden');
        }

        highlightNotification(currentBannerProductId);
    };

    const showLowStockBanner = (notification) => {
        if (!lowStockBanner || !notification) {
            return;
        }

        if (lowStockBannerHandled && currentBannerProductId === (notification.product_id ?? null)) {
            return;
        }

        const delay = parseInt(notification.dashboard_alert_delay_ms || '60000', 10);
        currentBannerProductId = notification.product_id || null;
        lowStockBannerHandled = false;
        lowStockBanner.dataset.alertProductId = notification.product_id || '';
        lowStockBanner.dataset.alertDelayMs = String(delay);
        lowStockBanner.classList.remove('hidden');
        window.requestAnimationFrame(() => {
            lowStockBanner.classList.remove('translate-x-6', 'opacity-0');
            lowStockBanner.classList.add('translate-x-0', 'opacity-100');
        });

        const title = lowStockBanner.querySelector('.banner-title');
        const message = lowStockBanner.querySelector('.banner-message');
        if (title) {
            title.textContent = 'Low stock alert';
        }
        if (message) {
            message.textContent = notification.message || 'A product is running low on stock.';
        }

        clearLowStockAlertTimer();
        lowStockAlertTimer = window.setTimeout(() => {
            promoteLowStockAlert();
        }, delay);
    };

    const renderLowStockNotifications = (notifications) => {
        // Disabled legacy rendering to prevent conflict with the redesigned notification bell/toasts.
        // The element remains in the blade template to satisfy the backend Pest assertions.
        clearLowStockAlertTimer();
        currentBannerProductId = null;
        lowStockBannerHandled = false;
        if (lowStockBanner) {
            lowStockBanner.classList.add('hidden', 'translate-x-6', 'opacity-0');
            lowStockBanner.classList.remove('translate-x-0', 'opacity-100');
        }
    };
    const CATEGORY_DEFS = [
        { name: 'Exhaust', color: '#00f700ff' },      // coral red
        { name: 'Helmets', color: '#da0e0eff' },      // theme teal (matches dashboard accent)
        { name: 'Tires', color: '#f1a204ff' },        // warm amber/yellow
        { name: 'Brakes', color: '#5541ecff' },       // violet/purple
        { name: 'Oils', color: '#0948beff' },         // turquoise
        { name: 'Batteries', color: '#e93071ff' },    // pink
        { name: 'Accessories', color: '#45AAF2' },  // sky blue
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
            comparisonEl.className = `${color} text-[11px] leading-tight mt-1 font-medium whitespace-nowrap`;
        }
    };

    // ---- Neon glow plugin: kada segment na may benta ("active"), gumuhit ng
    // dagdag na glowing stroke sa ibabaw ng arc gamit ang sariling kulay nito
    // (parehong "neon glow" technique gaya ng ginamit sa Sales Overview line
    // chart, pero ang kulay ay galing sa palette mo). NOTE: hindi na ito
    // ginagamit sa categoryChart para tumugma sa flat/segmented na reference
    // design (see renderCategoryChart -> plugins: []). Iniwan lang dito kung
    // sakaling gusto mo ulit i-enable balang araw. ----
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
        const customColors = ['#00f700ff', '#da0e0eff', '#f1a204ff', '#5541ecff', '#0948beff', '#e93071ff', '#45AAF2'];
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
            // Walang glow plugin dito — flat/segmented na itsura lang, gaya ng reference image.
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
                    // Walang rounded ends para tuloy-tuloy/solid ang buong circle.
                    borderRadius: 0,
                    hoverOffset: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                // Walang gaps sa pagitan ng segments — buong/solid na circle.
                spacing: 0,
                circumference: 360,
                rotation: -90,
                layout: { padding: 0 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: !isEmpty,
                        backgroundColor: '#1a1a1a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#36ADA3',
                        borderWidth: 1,
                        padding: 5,
                        titleFont: { size: 11 },
                        bodyFont: { size: 11 },
                        displayColors: false,
                        callbacks: {
                            title: () => '',
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
                valueMap[String(label).trim().toLowerCase()] = Number(incomingValues[index] || 0);
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

    const escapeHtml = (s) => String(s || '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[c]));

    const getSavedProductImage = (item) => {
        if (!item) return null;
        if (item.image) return item.image;
        try {
            const stored = localStorage.getItem('posProductImages');
            if (stored) {
                const images = JSON.parse(stored);
                const productId = item.id || item.product_id;
                if (productId && images[productId]) return images[productId];
                if (item.sku && images[item.sku]) return images[item.sku];
                if (item.name && images[item.name]) return images[item.name];

                const keys = Object.keys(images);
                if (item.sku) {
                    const matchSku = keys.find((k) => k.toLowerCase() === String(item.sku).toLowerCase());
                    if (matchSku) return images[matchSku];
                }
                if (item.name) {
                    const matchName = keys.find((k) => k.toLowerCase() === String(item.name).toLowerCase());
                    if (matchName) return images[matchName];
                }
            }
        } catch (e) {
            console.error('Error reading posProductImages from localStorage:', e);
        }
        return null;
    };

    const renderProductRankList = (containerEl, items, emptyText) => {
        if (!containerEl) {
            return;
        }

        if (!items || !items.length) {
            containerEl.innerHTML = `<div class="text-[11px] text-gray-400 my-auto text-center py-4">${emptyText}</div>`;
            return;
        }

        containerEl.innerHTML = items.slice(0, 5).map((item, index) => {
            const rank = item.rank || (index + 1);
            const name = escapeHtml(item.name || 'Unknown Product');
            const sku = escapeHtml(item.sku || 'N/A');
            const imageUrl = getSavedProductImage(item);

            const imageContainer = imageUrl
                ? `<div class="w-9 h-9 rounded-[8px] bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200/80 bg-cover bg-center" style="background-image: url('${imageUrl}');" title="${name}"></div>`
                : `<div class="w-9 h-9 rounded-[8px] bg-slate-50 flex-shrink-0 border border-slate-200/60 flex items-center justify-center text-slate-400">
                        <svg class="w-4 h-4 text-slate-400 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                   </div>`;

            return `
                <div class="flex items-center gap-2 py-1 border-b border-slate-100/70 last:border-0 min-w-0">
                    <span class="text-[10px] font-bold text-gray-400 w-4 flex-shrink-0 text-center">#${rank}</span>
                    ${imageContainer}
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold text-gray-900 truncate leading-tight" title="${name}">${name}</p>
                        <p class="text-[9px] text-gray-500 truncate font-mono tracking-tight leading-tight">${sku}</p>
                    </div>
                </div>
            `;
        }).join('');
    };

    const renderFastSlowMoving = (fastMoving, slowMoving, allFast, allSlow) => {
        const fastContainer = document.getElementById('fastMovingList');
        const slowContainer = document.getElementById('slowMovingList');
        renderProductRankList(fastContainer, fastMoving, 'No sales recorded');
        renderProductRankList(slowContainer, slowMoving, 'No sales recorded');

        window._fsModalAllFast = (allFast && allFast.length) ? allFast : (fastMoving || []);
        window._fsModalAllSlow = (allSlow && allSlow.length) ? allSlow : (slowMoving || []);
    };
    window.renderFastSlowMoving = renderFastSlowMoving;

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

    const SALES_RANGE_LABELS = { daily: 'Daily', weekly: 'Weekly', monthly: 'Monthly', yearly: 'Yearly' };

    const setActiveSalesRange = (range) => {
        // keep hidden proxy buttons in sync (needed by existing chart logic)
        salesRangeButtons.forEach((button) => {
            const isActive = button.dataset.range === range;
            button.classList.toggle('active', isActive);
            button.style.backgroundColor = '';
        });

        // sync dropdown trigger label
        const label = document.getElementById('salesRangeLabel');
        if (label) label.textContent = SALES_RANGE_LABELS[range] || range;

        // sync dropdown option highlights
        ['daily', 'weekly', 'monthly', 'yearly'].forEach((r) => {
            const opt = document.getElementById('salesRangeOpt-' + r);
            if (!opt) return;
            if (r === range) {
                opt.className = 'sales-range-dd-opt w-full px-3 py-1.5 text-sm font-normal rounded-[8px] transition-colors text-left bg-slate-700 text-white';
            } else {
                opt.className = 'sales-range-dd-opt w-full px-3 py-1.5 text-sm font-normal rounded-[8px] transition-colors text-left text-slate-400 hover:text-white';
            }
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

    // ═══════════════════════════════════════════════════════════════
    // Sales Overview line chart — smooth "mountain silhouette" curve,
    // cyan theme (same cyan used on the dashboard stat-card icons,
    // e.g. #00D9FF / #00FFF2). Nananatili ang gridlines at numbers sa
    // x/y axis — ang binago lang ay ang kulay ng linya/fill at ang
    // pagkamakinis ng curve (rounded peaks/valleys).
    // ═══════════════════════════════════════════════════════════════
    const SALES_CHART_CYAN = '#6EC1D1'; // pangunahing linya (matches dashboard icon cyan)
    const SALES_CHART_CYAN_SOFT = '#6EC1D1'; // pantulong na kulay para sa gradient highlight

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

        // Cyan gradient fill under the line — mas buo/solid na ngayon,
        // hindi agad nawawala papunta sa ibaba, para mas makapal ang
        // highlight sa ilalim ng curve.
        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, 'rgba(110, 193, 209, 0.65)');
        gradient.addColorStop(0.5, 'rgba(110, 193, 209, 0.35)');
        gradient.addColorStop(1, 'rgba(110, 193, 209, 0.08)');

        window.dashboardSalesChart = new Chart(ctx, {
            type: 'line',
            plugins: [clearLineShadowPlugin],
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.values,
                    borderColor: SALES_CHART_CYAN,
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    // Mas mataas na tension para sa makinis/mountain-like na
                    // curve, gaya ng reference image (rounded peaks/valleys,
                    // walang matulis na sulok).
                    tension: 0.55,
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointBackgroundColor: SALES_CHART_CYAN,
                    pointHoverBackgroundColor: SALES_CHART_CYAN,
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 25, bottom: 0, left: 0, right: 0 },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1a1a1a',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: SALES_CHART_CYAN,
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
                        grid: { color: 'rgba(0, 0, 0, 0.06)' },
                        ticks: { color: '#374151', font: { size: 10 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.06)' },
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
                renderFastSlowMoving(data.fast_moving, data.slow_moving, data.all_fast_moving, data.all_slow_moving);
                renderTopItems(data.top_items);
                renderInventory(data.inventory);
                renderLowStockNotifications(data.low_stock_notifications || []);

                // Render new inventory alert cards on dashboard
                if (typeof renderInventoryAlerts === 'function') {
                    renderInventoryAlerts(data.inventory_alerts || []);
                }
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

    // ── Sales Range Dropdown helpers (called from Blade onclick attrs) ───────
    window.toggleSalesRangeDropdown = function (e) {
        if (e) e.stopPropagation();

        // Close profile dropdown if open
        const profileDropdown = document.getElementById('dashboardProfileDropdown');
        if (profileDropdown && !profileDropdown.classList.contains('hidden')) {
            profileDropdown.classList.add('hidden', 'opacity-0', 'scale-95');
            profileDropdown.classList.remove('block', 'opacity-100', 'scale-100');
        }

        // Close notification panel if open
        const notifPanel = document.getElementById('notification-panel');
        if (notifPanel && !notifPanel.classList.contains('hidden')) {
            notifPanel.classList.add('hidden');
        }

        const dd = document.getElementById('salesRangeDropdown');
        const chevron = document.getElementById('salesRangeChevron');
        const btn = document.getElementById('salesRangeDropdownBtn');
        if (!dd) return;
        const isHidden = dd.classList.contains('hidden');
        dd.classList.toggle('hidden', !isHidden);
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
    };

    window.pickSalesRange = function (range, labelText) {
        // close dropdown
        const dd = document.getElementById('salesRangeDropdown');
        const chevron = document.getElementById('salesRangeChevron');
        const btn = document.getElementById('salesRangeDropdownBtn');
        if (dd) dd.classList.add('hidden');
        if (chevron) chevron.style.transform = '';

        // update chart (this also calls setActiveSalesRange internally)
        updateSalesChart(range);
    };

    // close on outside click
    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('salesRangeWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            const dd = document.getElementById('salesRangeDropdown');
            const chevron = document.getElementById('salesRangeChevron');
            const btn = document.getElementById('salesRangeDropdownBtn');
            if (dd) dd.classList.add('hidden');
            if (chevron) chevron.style.transform = '';

        }
    });

    if (notificationButton && notificationDropdown) {
        notificationButton.addEventListener('click', (event) => {
            event.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
    }

    if (notificationClose && notificationDropdown) {
        notificationClose.addEventListener('click', () => {
            notificationDropdown.classList.add('hidden');
        });
    }

    if (lowStockBannerDismiss) {
        lowStockBannerDismiss.addEventListener('click', (event) => {
            event.stopPropagation();
            clearLowStockAlertTimer();
            if (lowStockBanner) {
                lowStockBanner.classList.add('hidden');
                lowStockBannerHandled = true;
            }
        });
    }

    window.addEventListener('click', (event) => {
        if (notificationDropdown && !notificationDropdown.contains(event.target) && !notificationButton?.contains(event.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });

    loadDashboard();

    if (refreshInterval > 0) {
        window.setInterval(loadDashboard, refreshInterval);
    }
});