<?php

it('renders sales analytics when category breakdown formatting is missing', function () {
    $html = view('data_analytics.sales-analytics', [
        'quickStats' => [
            'total_inventory_value' => 0,
            'average_unit_price' => 0,
            'total_units_in_stock' => 0,
            'healthy_skus' => 0,
            'low_stock_skus' => 0,
            'out_of_stock_skus' => 0,
        ],
        'salesTrend' => [
            'labels' => [],
            'values' => [],
        ],
        'categoryBreakdown' => [
            'labels' => ['Exhausts'],
            'values' => [100],
            'shares' => [100],
        ],
        'topProducts' => [],
    ])->render();

    expect($html)->toContain('Category distribution');
    expect($html)->toContain('Exhausts');
});
