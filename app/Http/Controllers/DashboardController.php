<?php

namespace App\Http\Controllers;

use App\Models\POSTransaction;
use App\Models\Product;
use App\Services\SalesCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
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
                $categoryValue = $item['category'] ?? null;

                if ((!$categoryValue || strcasecmp(trim($categoryValue), 'uncategorized') === 0) && $productId) {
                    $categoryValue = $productCategories[$productId] ?? null;
                }

                return [
                    'name' => $item['name'] ?? 'Unknown Product',
                    'category' => $this->normalizeCategory($categoryValue),
                    'quantity' => $quantity,
                    'revenue' => $quantity * $unitPrice,
                ];
            });
        })->groupBy('name')->map(function ($items) {
            return [
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
                        'backgroundColor' => ['#14b8a6', '#818CF8'],
                        'borderColor' => ['#14b8a6', '#818CF8'],
                    ],
                ],
            ],
            'top_items' => $topItems->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'qty' => $item['quantity'],
                    'revenue' => $item['revenue'],
                ];
            })->values()->all(),
            'inventory' => $inventory,
            'range_label' => $startDate->format('M j, Y') . ' - ' . $endDate->format('M j, Y'),
        ]);
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

        $normalized = trim($value);

        // Return the category as-is if it's not empty
        // This way we show all the actual product categories with their own colors
        return !empty($normalized) ? $normalized : 'Uncategorized';
    }
}
