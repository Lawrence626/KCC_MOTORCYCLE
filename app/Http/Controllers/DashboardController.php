<?php

namespace App\Http\Controllers;

use App\Models\POSTransaction;
use App\Models\Product;
use App\Services\InventoryAlertService;
use App\Services\SalesCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Sync inventory alerts on dashboard load
        $alertService = app(InventoryAlertService::class);
        $alertService->syncAlerts();

        return view('dashboard');
    }

    public function data(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : now()->endOfMonth();

        $currentTransactions = POSTransaction::query()
            ->completed()
            ->whereBetween('completed_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();

        // Get today's transactions ONLY for Sales by Category chart (daily reset)
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $todayTransactions = POSTransaction::query()
            ->completed()
            ->whereBetween('completed_at', [$todayStart, $todayEnd])
            ->get();

        $previousStart = (clone $startDate)->subMonthsNoOverflow(1);
        $previousEnd = (clone $endDate)->subMonthsNoOverflow(1);

        $previousTransactions = POSTransaction::query()
            ->completed()
            ->whereBetween('completed_at', [$previousStart->startOfDay(), $previousEnd->endOfDay()])
            ->get();

        $currentSales = $currentTransactions->sum('total_amount');
        $previousSales = $previousTransactions->sum('total_amount');
        $salesComparison = $this->buildComparison($currentSales, $previousSales);

        $currentProfit = $this->calculateProfit($currentTransactions);
        $previousProfit = $this->calculateProfit($previousTransactions);
        $profitComparison = $this->buildComparison($currentProfit, $previousProfit);

        $currentItemsSold = $currentTransactions->sum(function ($transaction) {
            return collect($transaction->items ?? [])->sum(fn ($item) => (int) ($item['quantity'] ?? $item['qty'] ?? 0));
        });
        $previousItemsSold = $previousTransactions->sum(function ($transaction) {
            return collect($transaction->items ?? [])->sum(fn ($item) => (int) ($item['quantity'] ?? $item['qty'] ?? 0));
        });
        $itemsSoldComparison = $this->buildComparison($currentItemsSold, $previousItemsSold);

        $currentTransactionsCount = $currentTransactions->count();
        $previousTransactionsCount = $previousTransactions->count();
        $transactionsComparison = $this->buildComparison($currentTransactionsCount, $previousTransactionsCount);

        // Get product IDs from today's transactions for category chart
        $todayProductIds = $todayTransactions->flatMap(function ($transaction) {
            return collect($transaction->items ?? [])->pluck('id');
        })->filter()->unique()->all();

        // Get all product categories (for backup lookup)
        $productIds = $currentTransactions->flatMap(function ($transaction) {
            return collect($transaction->items ?? [])->pluck('id');
        })->filter()->unique()->all();

        $productsForCat = Product::query()
            ->whereIn('id', array_merge($productIds, $todayProductIds))
            ->get(['id', 'category', 'product_name', 'name', 'sku', 'unit_price'])
            ->keyBy('id');
            
        $productCategories = [];
        foreach ($productsForCat as $id => $prod) {
            $cat = $prod->category;
            if (!$cat || strtolower(trim($cat)) === 'uncategorized') {
                $cat = $prod->product_name ?: ($prod->name ?? 'Uncategorized');
            }
            $productCategories[$id] = $cat;
        }

        // Use TODAY's transactions for category breakdown (daily reset)
        $categoryService = new SalesCategoryService();
        $categoryBreakdown = $categoryService->getTodaysCategoryBreakdown();

        $soldItems = $currentTransactions->flatMap(function ($transaction) use ($productCategories, $productsForCat) {
            return collect($transaction->items ?? [])->map(function ($item) use ($productCategories, $productsForCat) {
                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $productId = $item['id'] ?? null;
                $product = $productId ? $productsForCat->get($productId) : null;

                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                if ($unitPrice <= 0 && $product) {
                    $unitPrice = (float) ($product->unit_price ?? 0);
                }

                $categoryValue = null;
                if ($productId && isset($productCategories[$productId])) {
                    $categoryValue = $productCategories[$productId];
                }

                if (($categoryValue === null || strcasecmp(trim($categoryValue), 'uncategorized') === 0) && isset($item['category'])) {
                    $categoryValue = $item['category'];
                }

                $name = $item['name'] ?? $product?->product_name ?? $product?->name ?? 'Unknown Product';
                $sku = $item['sku'] ?? $product?->sku ?? '';

                return [
                    'product_id' => $productId,
                    'name' => $name,
                    'sku' => $sku,
                    'category' => $this->normalizeCategory($categoryValue),
                    'quantity' => $quantity,
                    'revenue' => $quantity * $unitPrice,
                ];
            });
        })->filter(fn ($item) => $item['quantity'] > 0);

        $groupedSoldItems = $soldItems->groupBy(function ($item) {
            return $item['product_id'] ?? $item['sku'] ?? $item['name'];
        })->map(function ($items) {
            return [
                'product_id' => $items->first()['product_id'] ?? null,
                'name' => $items->first()['name'],
                'sku' => $items->first()['sku'] ?? '',
                'category' => $items->first()['category'],
                'quantity' => $items->sum('quantity'),
                'revenue' => $items->sum('revenue'),
            ];
        })->values();

        $topItems = $groupedSoldItems->sortByDesc('quantity')->take(5)->values();

        $allFastMoving = $groupedSoldItems->sortByDesc('quantity')->values()->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                'id' => $item['product_id'] ?? null,
                'product_id' => $item['product_id'] ?? null,
                'name' => $item['name'],
                'sku' => $item['sku'] ?: 'N/A',
                'category' => $item['category'] ?? 'General',
                'quantity' => (int) $item['quantity'],
                'revenue' => (float) $item['revenue'],
            ];
        })->all();

        $allSlowMoving = $groupedSoldItems->sortBy('quantity')->values()->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                'id' => $item['product_id'] ?? null,
                'product_id' => $item['product_id'] ?? null,
                'name' => $item['name'],
                'sku' => $item['sku'] ?: 'N/A',
                'category' => $item['category'] ?? 'General',
                'quantity' => (int) $item['quantity'],
                'revenue' => (float) $item['revenue'],
            ];
        })->all();

        $fastMoving = array_slice($allFastMoving, 0, 5);
        $slowMoving = array_slice($allSlowMoving, 0, 5);

        $inventory = [
            'total_products' => Product::query()->where('is_archived', false)->count(),
            'low_stock' => Product::query()->where('is_archived', false)->whereColumn('stock_quantity', '<=', 'reorder_level')->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::query()->where('is_archived', false)->where('stock_quantity', '<=', 0)->count(),
            'in_stock' => Product::query()->where('is_archived', false)->where('stock_quantity', '>', 0)->count(),
        ];

        $salesChart = $this->buildSalesTrendData();

        // Sync and get inventory alerts from database
        $alertService = app(InventoryAlertService::class);
        $alertService->syncAlerts();
        $dashboardAlerts = $alertService->getDashboardAlerts();
        $unreadCount = $alertService->getUnreadCount();

        return response()->json([
            'metrics' => [
                'sales' => ['label' => 'Total Sales', 'value' => $currentSales, 'comparison' => $salesComparison],
                'transactions' => ['label' => 'Total Transactions', 'value' => $currentTransactionsCount, 'comparison' => $transactionsComparison],
                'profit' => ['label' => 'Total Profit', 'value' => $currentProfit, 'comparison' => $profitComparison],
                'items_sold' => ['label' => 'Total Item Sold', 'value' => $currentItemsSold, 'comparison' => $itemsSoldComparison],
            ],
            'sales_chart' => $salesChart,
            'category_chart' => [
                'labels' => $categoryBreakdown->keys()->values()->all(),
                'data' => $categoryBreakdown->values()->pluck('amount')->values()->all(),
                'legend' => $categoryBreakdown->map(function ($item, $key) {
                    return [
                        'label' => $key,
                        'value' => $item['amount'],
                    ];
                })->values()->all(),
            ],
            'comparison_chart' => [
                'labels' => ['Current Period', 'Previous Period'],
                'datasets' => [
                    [
                        'label' => 'Sales Comparison',
                        'data' => [(float) $currentSales, (float) $previousSales],
                        'backgroundColor' => ['#A4DD00', '#A4DD00'],
                        'borderColor' => ['#A4DD00', '#A4DD00'],
                    ],
                ],
            ],
            'fast_moving' => $fastMoving,
            'slow_moving' => $slowMoving,
            'all_fast_moving' => $allFastMoving,
            'all_slow_moving' => $allSlowMoving,
            'top_items' => $topItems->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'product_id' => $item['product_id'] ?? null,
                    'name' => $item['name'],
                    'sku' => $item['sku'] ?? '',
                    'category' => $item['category'],
                    'qty' => $item['quantity'],
                    'revenue' => $item['revenue'],
                ];
            })->values()->all(),
            'inventory' => $inventory,
            'range_label' => $startDate->format('M j, Y') . ' - ' . $endDate->format('M j, Y'),
            'inventory_alerts' => $alertService->formatNotifications($dashboardAlerts),
            'inventory_alerts_unread_count' => $unreadCount,
            'low_stock_notifications' => collect($dashboardAlerts)->map(function ($alert) {
                return [
                    'product_id' => $alert->product_id,
                    'product_name' => $alert->product?->product_name ?? $alert->product?->name,
                    'stock_quantity' => (int) $alert->current_stock,
                    'reorder_level' => (int) $alert->reorder_point,
                    'sku' => $alert->sku,
                    'is_dashboard_alert' => true,
                    'dashboard_alert_visible' => true,
                    'dashboard_alert_delay_ms' => 60000,
                ];
            })->values()->all(),
        ]);
    }



    protected function calculateProfit($transactions)
    {
        $productIds = $transactions->flatMap(function ($transaction) {
            return collect($transaction->items ?? [])->pluck('id');
        })->filter()->unique()->all();

        $products = Product::whereIn('id', $productIds)->get(['id', 'unit_price'])->keyBy('id');

        return $transactions->sum(function ($transaction) use ($products) {
            return collect($transaction->items ?? [])->sum(function ($item) use ($products) {
                $productId = $item['id'] ?? null;
                $product = $productId ? $products->get($productId) : null;

                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
                if ($unitPrice <= 0 && $product) {
                    $unitPrice = (float) ($product->unit_price ?? 0);
                }

                $costPrice = (float) ($item['cost_price'] ?? 0);

                return max(0, ($unitPrice - $costPrice) * $quantity);
            });
        });
    }

    protected function buildComparison($current, $previous)
    {
        if ($previous <= 0) {
            return $current > 0 ? ['direction' => 'up', 'value' => 100, 'label' => 'No previous period data'] : ['direction' => 'neutral', 'value' => 0, 'label' => 'No data'];
        }

        $change = (($current - $previous) / $previous) * 100;
        return [
            'direction' => $change >= 0 ? 'up' : 'down',
            'value' => round(abs($change), 1),
            'label' => $change >= 0 ? 'increase' : 'decrease',
        ];
    }

    protected function buildPeriodLabels(Carbon $startDate, Carbon $endDate)
    {
        $labels = [];
        $period = clone $startDate;

        while ($period->lte($endDate)) {
            $labels[] = $period->format('M j');
            $period->addDay();
        }

        return $labels;
    }

    protected function buildSalesTrendData()
    {
        $now = now();

        $yearly = [];
        $yearlyLabels = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->copy()->subYears($i);
            $yearlyLabels[] = $year->format('Y');
            $yearly[] = (float) POSTransaction::query()
                ->completed()
                ->whereBetween('completed_at', [$year->copy()->startOfYear(), $year->copy()->endOfYear()])
                ->sum('total_amount');
        }

        $monthly = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonthsNoOverflow($i);
            $monthlyLabels[] = $month->format('M');
            $monthly[] = (float) POSTransaction::query()
                ->completed()
                ->whereBetween('completed_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('total_amount');
        }

        $daily = [];
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dailyLabels[] = $day->format('M j');
            $daily[] = (float) POSTransaction::query()
                ->completed()
                ->whereDate('completed_at', $day)
                ->sum('total_amount');
        }

        $weekly = [];
        $weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
        $weekStart = $now->copy()->startOfDay()->subDays(34);

        for ($week = 0; $week < 5; $week++) {
            $start = $weekStart->copy()->addDays($week * 7);
            $end = $start->copy()->endOfDay()->addDays(6);
            $weekly[] = (float) POSTransaction::query()
                ->completed()
                ->whereBetween('completed_at', [$start, $end])
                ->sum('total_amount');
        }

        return [
            'yearly' => ['labels' => $yearlyLabels, 'values' => $yearly],
            'monthly' => ['labels' => $monthlyLabels, 'values' => $monthly],
            'weekly' => ['labels' => $weeklyLabels, 'values' => $weekly],
            'daily' => ['labels' => $dailyLabels, 'values' => $daily],
        ];
    }

    protected function normalizeCategory($value)
    {
        if (empty($value)) {
            return 'Uncategorized';
        }

        $normalized = strtolower(trim((string) $value));
        $normalized = str_replace(['-', '_'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Brakes
        if (str_contains($normalized, 'brake') || str_contains($normalized, 'caliper') || str_contains($normalized, 'disc') || str_contains($normalized, 'lever')) {
            return 'Brakes';
        }

        // Exhaust
        if (str_contains($normalized, 'pipe') || str_contains($normalized, 'exhaust')) {
            return 'Exhaust';
        }

        // Tires
        if (str_contains($normalized, 'tire') || str_contains($normalized, 'tyre')) {
            return 'Tires';
        }

        // Oils
        if (str_contains($normalized, 'oil') || str_contains($normalized, 'lubricant')) {
            return 'Oils';
        }

        // Batteries
        if (str_contains($normalized, 'battery') || str_contains($normalized, 'batteries')) {
            return 'Batteries';
        }

        // Helmets
        if (str_contains($normalized, 'helmet')) {
            return 'Helmets';
        }

        // Catch-all for uncategorized
        if (in_array($normalized, ['uncategorized', 'unknown', 'n/a', ''], true)) {
            return 'Uncategorized';
        }

        // Everything else defaults to Accessories (Mags, Seats, Shocks, Mirrors, etc.)
        return 'Accessories';
    }
}
