<?php
/**
 * SALES CATEGORY SERVICE - USAGE EXAMPLES
 * 
 * This document shows how to use the SalesCategoryService throughout your application.
 */

// ============================================================================
// 1. BASIC USAGE IN CONTROLLERS
// ============================================================================

use App\Services\SalesCategoryService;

class ReportsController extends Controller
{
    public function categoryReport()
    {
        $categoryService = new SalesCategoryService();
        
        // Get today's category breakdown
        $todayCategories = $categoryService->getTodaysCategoryBreakdown();
        
        return view('reports.categories', [
            'categories' => $todayCategories
        ]);
    }
}

// ============================================================================
// 2. GET CATEGORY BREAKDOWN FOR A DATE RANGE
// ============================================================================

use Illuminate\Support\Carbon;

$categoryService = new SalesCategoryService();

// Get categories for a specific month
$breakdown = $categoryService->getCategoryBreakdown(
    Carbon::parse('2024-01-01'),
    Carbon::parse('2024-01-31')
);

// With percentages included
$breakdownWithPercent = $categoryService->getCategoryBreakdownWithPercentages(
    Carbon::parse('2024-01-01'),
    Carbon::parse('2024-01-31')
);

// ============================================================================
// 3. GET TOP SELLING CATEGORIES
// ============================================================================

// Get top 5 categories by sales for today
$topCategories = $categoryService->getTopCategories(5);

// Get top 10 categories for January
$topCategories = $categoryService->getTopCategories(
    10,
    Carbon::parse('2024-01-01'),
    Carbon::parse('2024-01-31')
);

// Result format:
// [
//     {
//         'amount' => 15000.50,
//         'quantity' => 25,
//         'percentage' => 35.5
//     },
//     ...
// ]

// ============================================================================
// 4. GET SALES FOR A SPECIFIC CATEGORY
// ============================================================================

// Get total sales amount for Exhausts today
$exhaustSales = $categoryService->getCategorySalesTotal('Exhaust');

// Get total sales amount for Helmets in January
$helmetSales = $categoryService->getCategorySalesTotal(
    'Helmets',
    Carbon::parse('2024-01-01'),
    Carbon::parse('2024-01-31')
);

// Get quantity of items sold in a category
$exhaustQty = $categoryService->getCategorySalesCount('Exhaust');

// ============================================================================
// 5. FORMAT FOR FRONTEND CHART DISPLAY
// ============================================================================

// Get formatted data ready for Chart.js or any frontend chart library
$chartData = $categoryService->formatForChart();

// Example output:
// [
//     'labels' => ['Exhaust', 'Helmets', 'Tires', ...],
//     'data' => [15000, 12000, 8500, ...],
//     'backgroundColors' => ['#06b6d4', '#a3e635', '#fbbf24', ...],
//     'legend' => [
//         [
//             'label' => 'Exhaust',
//             'value' => 15000,
//             'quantity' => 30,
//             'percentage' => 35.5,
//             'color' => '#06b6d4'
//         ],
//         ...
//     ],
//     'total' => 42250.50,
//     'totalQuantity' => 120
// ]

// ============================================================================
// 6. GET AVAILABLE CATEGORIES
// ============================================================================

$categoryService = new SalesCategoryService();
$categories = $categoryService->getStandardCategories();

// Returns:
// ['Exhaust', 'Helmets', 'Tires', 'Brakes', 'Oils', 'Batteries', 'Accessories']

// Get category colors for display
$colors = $categoryService->getCategoryColors();
// Returns associative array with color hex codes for each category

// ============================================================================
// 7. IN BLADE TEMPLATES
// ============================================================================

// In your Blade template, inject the service:
// @inject('categoryService', 'App\Services\SalesCategoryService')

// Then use it:
// @php
//     $today = $categoryService->getTodaysCategoryBreakdown();
// @endphp

// @foreach($today as $category => $data)
//     <div class="category-item">
//         <span>{{ $category }}</span>
//         <span>₱{{ number_format($data['amount'], 2) }}</span>
//     </div>
// @endforeach

// ============================================================================
// 8. CREATING CUSTOM REPORTS
// ============================================================================

class SalesReportService
{
    private SalesCategoryService $categoryService;
    
    public function __construct(SalesCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    
    public function generateCategoryPerformanceReport($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $breakdown = $this->categoryService->getCategoryBreakdownWithPercentages($startDate, $endDate);
        $top5 = $this->categoryService->getTopCategories(5, $startDate, $endDate);
        
        return [
            'period' => "{$startDate->format('F Y')}",
            'all_categories' => $breakdown,
            'top_categories' => $top5,
            'total_sales' => $breakdown->sum('amount'),
            'total_items' => $breakdown->sum('quantity'),
        ];
    }
}

// ============================================================================
// 9. USING IN API ENDPOINTS
// ============================================================================

Route::get('/api/categories/sales', function (Request $request) {
    $categoryService = new SalesCategoryService();
    
    $startDate = $request->has('start_date') 
        ? Carbon::parse($request->start_date) 
        : now()->startOfMonth();
    
    $endDate = $request->has('end_date') 
        ? Carbon::parse($request->end_date) 
        : now()->endOfMonth();
    
    return response()->json([
        'status' => 'success',
        'data' => $categoryService->getCategoryBreakdownWithPercentages($startDate, $endDate),
        'chart' => $categoryService->formatForChart($startDate, $endDate),
    ]);
});

// ============================================================================
// 10. EXPORTED DATA FORMATTING
// ============================================================================

// For CSV or Excel exports
$categoryService = new SalesCategoryService();
$data = $categoryService->getCategoryBreakdownWithPercentages();

$rows = $data->map(function($item, $category) {
    return [
        'Category' => $category,
        'Sales Amount' => number_format($item['amount'], 2),
        'Items Sold' => $item['quantity'],
        'Percentage' => $item['percentage'] . '%',
    ];
});

// Export to CSV
$csv = collect($rows)->prepend([
    'Category', 'Sales Amount', 'Items Sold', 'Percentage'
]);
