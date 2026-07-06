# Sales Category Service - Complete Guide

## Overview

The **SalesCategoryService** is a Laravel service class that provides a comprehensive system for tracking, calculating, and displaying sales by product category in your POS system. It automatically extracts transaction data and organizes it by category for analytics and reporting.

## Features

✅ **Category Sales Breakdown** - Get sales amount and quantity by category  
✅ **Date Range Filtering** - Filter by any date range or use preset ranges (today, month)  
✅ **Percentage Calculations** - Automatically calculates percentage of total sales  
✅ **Color Palette** - Built-in colors for each category for chart display  
✅ **Chart-Ready Formatting** - Exports data formatted for Chart.js or similar libraries  
✅ **Performance Optimized** - Efficient database queries with proper indexing  
✅ **Top Categories** - Get top N categories by sales volume  

## Installation & Setup

### 1. Service Already Created
The service is located at: `app/Services/SalesCategoryService.php`

### 2. Import in Your Controllers
```php
use App\Services\SalesCategoryService;
```

### 3. Initialize
```php
$categoryService = new SalesCategoryService();
```

## Data Structure

### Transaction Items Format
The POS transactions store items as JSON with this structure:

```json
{
  "id": 1,
  "name": "Akrapovic Exhaust",
  "category": "Exhaust",
  "quantity": 2,
  "unit_price": 7500.00,
  "cost_price": 5000.00,
  "price": 7500.00
}
```

### Product Categories
Products must have a `category` field that matches one of the standard categories:
- Exhaust
- Helmets
- Tires
- Brakes
- Oils
- Batteries
- Accessories

## Standard Categories

These are the predefined product categories in your system:

```php
$categories = [
    'Exhaust',
    'Helmets',
    'Tires',
    'Brakes',
    'Oils',
    'Batteries',
    'Accessories'
];
```

## Category Colors

Each category has an assigned color for consistent visualization:

| Category | Color | Hex |
|----------|-------|-----|
| Exhaust | Cyan | #06b6d4 |
| Helmets | Lime | #a3e635 |
| Tires | Amber | #fbbf24 |
| Brakes | Red | #ef4444 |
| Oils | Orange | #fb923c |
| Batteries | Blue | #3b82f6 |
| Accessories | Emerald | #10b981 |
| Uncategorized | Gray | #6b7280 |

## Method Reference

### 1. Get Today's Category Breakdown
```php
$categoryService->getTodaysCategoryBreakdown();
```
**Returns:** Collection with today's sales by category

**Example Output:**
```php
Collection {
    'Exhaust' => ['amount' => 15000, 'quantity' => 25],
    'Helmets' => ['amount' => 12000, 'quantity' => 30],
    ...
}
```

### 2. Get Category Breakdown for Date Range
```php
$categoryService->getCategoryBreakdown(
    Carbon::parse('2024-01-01'),
    Carbon::parse('2024-01-31')
);
```

### 3. Get Breakdown with Percentages
```php
$categoryService->getCategoryBreakdownWithPercentages(
    $startDate,
    $endDate
);
```
**Returns:** Includes `percentage` field for each category

### 4. Get Top Categories
```php
$topCategories = $categoryService->getTopCategories(
    5,  // Limit
    $startDate,
    $endDate
);
```
**Returns:** Top 5 categories sorted by sales amount

### 5. Get Specific Category Sales
```php
// Get total sales amount
$amount = $categoryService->getCategorySalesTotal('Exhaust');

// Get quantity sold
$quantity = $categoryService->getCategorySalesCount('Exhaust');
```

### 6. Format for Chart Display
```php
$chartData = $categoryService->formatForChart(
    $startDate,
    $endDate
);
```

**Returns Complete Chart Package:**
```php
[
    'labels' => ['Exhaust', 'Helmets', ...],
    'data' => [15000, 12000, ...],
    'backgroundColors' => ['#06b6d4', '#a3e635', ...],
    'legend' => [
        [
            'label' => 'Exhaust',
            'value' => 15000,
            'quantity' => 25,
            'percentage' => 35.5,
            'color' => '#06b6d4'
        ],
        ...
    ],
    'total' => 42250.50,
    'totalQuantity' => 120
]
```

## Dashboard Integration

The service is already integrated into your Dashboard:

**Location:** `app/Http/Controllers/DashboardController.php`

The dashboard automatically displays:
- Sales by Category doughnut chart
- Category breakdown with percentages
- Today's category sales only (daily reset)

## Creating Custom Reports

### Example: Monthly Category Performance Report

```php
<?php

namespace App\Http\Controllers;

use App\Services\SalesCategoryService;
use Illuminate\Support\Carbon;

class SalesReportController extends Controller
{
    protected $categoryService;
    
    public function __construct(SalesCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    
    public function monthlyCategoryReport($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $breakdown = $this->categoryService->getCategoryBreakdownWithPercentages(
            $startDate,
            $endDate
        );
        
        return view('reports.category-performance', [
            'period' => $startDate->format('F Y'),
            'categories' => $breakdown,
            'total' => $breakdown->sum('amount'),
            'total_items' => $breakdown->sum('quantity')
        ]);
    }
}
```

### Example: Compare Two Periods

```php
class ComparisonController extends Controller
{
    public function comparePeriods()
    {
        $service = new SalesCategoryService();
        
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = $currentMonth->copy()->subMonth();
        
        $currentData = $service->getCategoryBreakdownWithPercentages(
            $currentMonth,
            $currentMonth->copy()->endOfMonth()
        );
        
        $previousData = $service->getCategoryBreakdownWithPercentages(
            $previousMonth,
            $previousMonth->copy()->endOfMonth()
        );
        
        return [
            'current' => $currentData,
            'previous' => $previousData,
            'growth' => $this->calculateGrowth($currentData, $previousData)
        ];
    }
}
```

## API Endpoint Example

Add to your `routes/api.php`:

```php
Route::get('/categories/sales', function (Request $request) {
    $service = new SalesCategoryService();
    
    $startDate = $request->has('start_date') 
        ? Carbon::parse($request->start_date)->startOfDay()
        : now()->startOfMonth();
    
    $endDate = $request->has('end_date') 
        ? Carbon::parse($request->end_date)->endOfDay()
        : now()->endOfMonth();
    
    return response()->json([
        'status' => 'success',
        'period' => "{$startDate->format('M d, Y')} - {$endDate->format('M d, Y')}",
        'chart' => $service->formatForChart($startDate, $endDate),
        'raw_data' => $service->getCategoryBreakdownWithPercentages($startDate, $endDate),
    ]);
});
```

## Blade Template Usage

### Inject Service in View

```php
@inject('categoryService', 'App\Services\SalesCategoryService')

@php
    $today = $categoryService->getTodaysCategoryBreakdown();
@endphp

<div class="category-sales">
    @foreach($today as $category => $data)
        <div class="category-card">
            <h3>{{ $category }}</h3>
            <p class="amount">₱{{ number_format($data['amount'], 2) }}</p>
            <p class="quantity">{{ $data['quantity'] }} items</p>
        </div>
    @endforeach
</div>
```

## Database Optimization

For optimal performance, ensure these indexes exist:

```sql
-- Index for faster transaction queries
CREATE INDEX idx_pos_transactions_status_completed_at 
ON pos_transactions(status, completed_at);

-- Index for product lookups
CREATE INDEX idx_products_category_archived 
ON products(category, is_archived);
```

## Testing the Service

### Unit Test Example

```php
<?php

namespace Tests\Unit;

use App\Services\SalesCategoryService;
use App\Models\POSTransaction;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SalesCategoryServiceTest extends TestCase
{
    protected SalesCategoryService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SalesCategoryService();
    }
    
    public function test_get_today_category_breakdown()
    {
        // Create test transaction with items
        POSTransaction::factory()->create([
            'status' => 'completed',
            'completed_at' => now(),
            'items' => json_encode([
                [
                    'id' => 1,
                    'name' => 'Test Exhaust',
                    'category' => 'Exhaust',
                    'quantity' => 2,
                    'unit_price' => 1000,
                ]
            ])
        ]);
        
        $breakdown = $this->service->getTodaysCategoryBreakdown();
        
        $this->assertNotEmpty($breakdown);
        $this->assertTrue($breakdown->has('Exhaust'));
    }
    
    public function test_format_for_chart()
    {
        $chartData = $this->service->formatForChart();
        
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertArrayHasKey('data', $chartData);
        $this->assertArrayHasKey('backgroundColors', $chartData);
        $this->assertArrayHasKey('legend', $chartData);
    }
}
```

## Troubleshooting

### Issue: No sales showing in chart
**Solutions:**
1. Verify transactions have `status = 'completed'`
2. Check that `completed_at` timestamp is set
3. Verify items have `id` and `category` fields
4. Check if products exist and have category assigned

### Issue: Wrong categories appearing
**Solutions:**
1. Verify product categories match standard categories
2. Check for typos (case-sensitive)
3. Use `getStandardCategories()` to verify available categories

### Issue: Percentages not calculating
**Solutions:**
1. Ensure transactions have valid `unit_price` or `price` fields
2. Verify `quantity` field is present in items

## Notes

- The service uses **today's transactions only** for the dashboard display (daily reset)
- Categories are dynamically generated from transaction items
- Fallback to product's category if item doesn't have one
- "Uncategorized" is used for items without category
- All amounts are in PHP currency (₱)
- Timestamps use Laravel's Carbon library

## Next Steps

1. ✅ Service is integrated with DashboardController
2. 📊 Use in custom reports (see examples above)
3. 📱 Expose via API endpoints
4. 📈 Create additional analytics features
5. 🧪 Write comprehensive tests
