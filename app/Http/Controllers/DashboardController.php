<?php

namespace App\Http\Controllers;

use App\Models\POSTransaction;
use App\Models\Product;
use App\Services\SalesCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $lowStockNotifications = $this->getLowStockNotificationsForUser(auth()->id());

        return view('dashboard', compact('lowStockNotifications'));
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

        $productCategories = Product::query()
            ->whereIn('id', array_merge($productIds, $todayProductIds))
            ->where('is_archived', false)
            ->pluck('category', 'id')
            ->all();

        // Use TODAY's transactions for category breakdown (daily reset)
        $categoryService = new SalesCategoryService();
        $categoryBreakdown = $categoryService->getTodaysCategoryBreakdown();

        $topItems = $currentTransactions->flatMap(function ($transaction) use ($productCategories) {
            return collect($transaction->items ?? [])->map(function ($item) use ($productCategories) {
                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
            $productId = $item['id'] ?? null;
                $categoryValue = null;

                if ($productId && isset($productCategories[$productId])) {
                    $categoryValue = $productCategories[$productId];
                }

                if (($categoryValue === null || strcasecmp(trim($categoryValue), 'uncategorized') === 0) && isset($item['category'])) {
                    $categoryValue = $item['category'];
                }

                return [
                    'product_id' => $productId,
                    'name' => $item['name'] ?? 'Unknown Product',
                    'category' => $this->normalizeCategory($categoryValue),
                    'quantity' => $quantity,
                    'revenue' => $quantity * $unitPrice,
                ];
            });
        })->groupBy('name')->map(function ($items) {
            return [
                'product_id' => $items->first()['product_id'] ?? null,
                'name' => $items->first()['name'],
                'category' => $items->first()['category'],
                'quantity' => $items->sum('quantity'),
                'revenue' => $items->sum('revenue'),
            ];
        })->sortByDesc('quantity')->take(5)->values();

        $inventory = [
            'total_products' => Product::query()->where('is_archived', false)->count(),
            'low_stock' => Product::query()->where('is_archived', false)->whereColumn('stock_quantity', '<=', 'reorder_level')->count(),
            'out_of_stock' => Product::query()->where('is_archived', false)->where('stock_quantity', '<=', 0)->count(),
            'in_stock' => Product::query()->where('is_archived', false)->where('stock_quantity', '>', 0)->count(),
        ];

        $salesChart = $this->buildSalesTrendData();
        $lowStockNotifications = $this->getLowStockNotificationsForUser($request->user()?->id);

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
            'top_items' => $topItems->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'product_id' => $item['product_id'] ?? null,
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'qty' => $item['quantity'],
                    'revenue' => $item['revenue'],
                ];
            })->values()->all(),
            'inventory' => $inventory,
            'range_label' => $startDate->format('M j, Y') . ' - ' . $endDate->format('M j, Y'),
            'low_stock_notifications' => $lowStockNotifications,
        ]);
    }

    public function dismissLowStockNotification(Request $request, Product $product)
    {
        $userId = auth()->id();

        if (! $userId) {
            abort(403);
        }

        $key = $this->lowStockNotificationCacheKey($userId);
        $notifications = Cache::get($key, []);

        if (! is_array($notifications)) {
            $notifications = [];
        }

        $notifications = collect($notifications)
            ->map(function ($notification) use ($product) {
                if (($notification['product_id'] ?? null) == $product->id) {
                    $notification['status'] = 'dismissed';
                    $notification['dismissed_at'] = now()->toISOString();
                }

                return $notification;
            })
            ->values()
            ->all();

        Cache::put($key, $notifications, now()->addDays(7));

        return back()->with('success', 'Low-stock reminder dismissed.');
    }

    protected function getLowStockNotificationsForUser(?int $userId): array
    {
        if (! $userId) {
            return [];
        }

        $products = Product::query()
            ->where('is_active', true)
            ->where('is_archived', false)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity')
            ->orderBy('name')
            ->get();

        $key = $this->lowStockNotificationCacheKey($userId);
        $notifications = Cache::get($key, []);

        if (! is_array($notifications)) {
            $notifications = [];
        }

        $notifications = collect($notifications)
            ->filter(fn ($notification) => ($notification['status'] ?? 'active') !== 'dismissed')
            ->values()
            ->all();

        $currentNotifications = [];
        foreach ($products as $product) {
            $existing = collect($notifications)->firstWhere('product_id', $product->id);

            if ($existing) {
                $existing['product_name'] = $product->product_name ?? $product->name;
                $existing['stock_quantity'] = (int) $product->stock_quantity;
                $existing['reorder_level'] = (int) $product->reorder_level;
                $existing['sku'] = $product->sku;
                $existing['supplier_name'] = $product->supplier_name;
                $existing['message'] = 'Stock for ' . ($product->product_name ?? $product->name) . ' is at ' . (int) $product->stock_quantity . '. Reorder now.';
                $existing['url'] = route('order.create', ['product_id' => $product->id]);
                $existing['is_dashboard_alert'] = true;
                $existing['dashboard_alert_visible'] = true;
                $existing['dashboard_alert_delay_ms'] = 60000;
                $currentNotifications[] = $existing;
                continue;
            }

            $currentNotifications[] = $this->buildLowStockNotification($product);
        }

        Cache::put($key, $currentNotifications, now()->addDays(7));

        return $currentNotifications;
    }

    protected function buildLowStockNotification(Product $product): array
    {
        $productName = $product->product_name ?? $product->name;

        return [
            'id' => (string) Str::uuid(),
            'product_id' => $product->id,
            'product_name' => $productName,
            'sku' => $product->sku,
            'stock_quantity' => (int) $product->stock_quantity,
            'reorder_level' => (int) $product->reorder_level,
            'supplier_name' => $product->supplier_name,
            'message' => 'Stock for ' . $productName . ' is at ' . (int) $product->stock_quantity . '. Reorder now.',
            'url' => route('order.create', ['product_id' => $product->id]),
            'created_at' => now()->toISOString(),
            'status' => 'active',
            'is_dashboard_alert' => true,
            'dashboard_alert_visible' => true,
            'dashboard_alert_delay_ms' => 60000,
        ];
    }

    protected function lowStockNotificationCacheKey(int $userId): string
    {
        return "admin_low_stock_notifications:{$userId}";
    }

    protected function calculateProfit($transactions)
    {
        return $transactions->sum(function ($transaction) {
            return collect($transaction->items ?? [])->sum(function ($item) {
                $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
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

        $categoryMap = [
            'pipe' => 'Exhaust',
            'exhaust' => 'Exhaust',
            'muffler' => 'Exhaust',
            'silencer' => 'Exhaust',
            'helmet' => 'Helmets',
            'helmets' => 'Helmets',
            'tire' => 'Tires',
            'tires' => 'Tires',
            'wheel' => 'Tires',
            'rim' => 'Tires',
            'mags' => 'Tires',
            'brake' => 'Brakes',
            'brakes' => 'Brakes',
            'brake master' => 'Brakes',
            'brake shoe' => 'Brakes',
            'caliper' => 'Brakes',
            'disc' => 'Brakes',
            'oil' => 'Oils',
            'oils' => 'Oils',
            'engine oil' => 'Oils',
            'engine_oil' => 'Oils',
            'battery' => 'Batteries',
            'batteries' => 'Batteries',
            'shock' => 'Accessories',
            'spring' => 'Accessories',
            'suspension' => 'Accessories',
            'seat' => 'Accessories',
            'mirror' => 'Accessories',
            'lever' => 'Accessories',
            'clutch' => 'Accessories',
            'perch' => 'Accessories',
            'stand' => 'Accessories',
            'support' => 'Accessories',
            'cover' => 'Accessories',
            'frame' => 'Accessories',
        ];

        if (isset($categoryMap[$normalized])) {
            return $categoryMap[$normalized];
        }

        foreach ($categoryMap as $key => $mappedCategory) {
            if (str_contains($normalized, $key)) {
                return $mappedCategory;
            }
        }

        return !empty(trim((string) $value)) ? trim((string) $value) : 'Uncategorized';
    }
}
