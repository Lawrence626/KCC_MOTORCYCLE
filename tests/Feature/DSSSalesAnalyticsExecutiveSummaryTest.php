<?php

use App\Models\Product;
use App\Models\POSTransaction;
use App\Models\DSSRecommendation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'admin',
    ]);
});

test('sales analytics executive summary dynamically displays dss fast moving recommendation', function () {
    $product = Product::create([
        'name' => 'High Demand Racing Pipe',
        'product_name' => 'High Demand Racing Pipe',
        'sku' => 'PIPE-RACE-001',
        'category' => 'Exhaust',
        'brand' => 'Apido',
        'stock_quantity' => 20,
        'reorder_level' => 10,
        'unit_price' => 1500.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // Create high sales this month (60 units sold)
    POSTransaction::create([
        'invoice_number' => 'INV-HIGH-001',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(2),
        'subtotal' => 90000.00,
        'total_amount' => 90000.00,
        'items' => [
            [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => 60,
                'unit_price' => 1500.00,
            ]
        ]
    ]);

    $response = $this->actingAs($this->user)->get(route('sales.analytics'));

    $response->assertStatus(200);
    $response->assertSee('Executive summary');
    $response->assertSee('High Demand Racing Pipe');
    $response->assertSee('PIPE-RACE-001');
    $response->assertSee('Fast-Moving');
    $response->assertSee('60 units sold');
    $response->assertSee('Apply Suggested Reorder Level');
});

test('applying suggested reorder level from dss recommendation updates product reorder level and marks recommendation actioned', function () {
    $product = Product::create([
        'name' => 'Premium Brake Disc',
        'product_name' => 'Premium Brake Disc',
        'sku' => 'BRK-DISC-999',
        'category' => 'Brakes',
        'brand' => 'Brembo',
        'stock_quantity' => 15,
        'reorder_level' => 10,
        'unit_price' => 850.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $recommendation = DSSRecommendation::create([
        'product_id' => $product->id,
        'recommendation_type' => 'reorder_level',
        'title' => 'Fast-Moving Stock Alert: Increase Reorder Level',
        'description' => 'High sales detected this month (55 units sold). Consider increasing the reorder level from 10 to 25 units.',
        'priority' => 'High',
        'metadata' => [
            'classification' => 'fast_moving',
            'sales_this_month' => 55,
            'current_stock' => 15,
            'current_reorder_level' => 10,
            'suggested_reorder_level' => 25,
            'suggested_stock_quantity' => 50,
            'stockout_risk' => 'high',
        ],
        'is_active' => true,
        'generated_at' => now(),
    ]);

    // Apply via JSON request (AJAX from Sales Analytics)
    $response = $this->actingAs($this->user)->postJson(route('dss.recommendations.apply-reorder', $recommendation->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'new_reorder_level' => 25,
    ]);

    // Verify product reorder level was updated in database
    $product->refresh();
    expect($product->reorder_level)->toBe(25);

    // Verify recommendation was marked as actioned
    $recommendation->refresh();
    expect($recommendation->action_taken_at)->not->toBeNull();
});

test('sales filtered widgets api returns dss summary with fast moving insights', function () {
    $product = Product::create([
        'name' => 'LED Headlight Bulb',
        'product_name' => 'LED Headlight Bulb',
        'sku' => 'BULB-LED-001',
        'category' => 'Lighting',
        'brand' => 'Osram',
        'stock_quantity' => 30,
        'reorder_level' => 10,
        'unit_price' => 450.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $start = Carbon::now()->startOfMonth()->toDateString();
    $end = Carbon::now()->endOfMonth()->toDateString();

    POSTransaction::create([
        'invoice_number' => 'INV-WIDGET-001',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(3),
        'subtotal' => 22500.00,
        'total_amount' => 22500.00,
        'items' => [
            [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => 50,
                'unit_price' => 450.00,
            ]
        ]
    ]);

    $response = $this->actingAs($this->user)->getJson(route('api.analytics.sales_widgets', [
        'start_date' => $start,
        'end_date' => $end,
    ]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'quickStats',
        'salesTrend',
        'categoryBreakdown',
        'topProducts',
        'fastMoving',
        'slowMoving',
        'dssSummary' => [
            'has_fast_moving',
            'high_priority_banner',
            'recommendations',
            'opportunity',
            'attention',
            'next_step',
        ],
    ]);

    $json = $response->json();
    expect($json['dssSummary']['has_fast_moving'])->toBeTrue();
    expect(count($json['dssSummary']['recommendations']))->toBeGreaterThanOrEqual(1);
    expect($json['dssSummary']['recommendations'][0]['product_name'])->toBe('LED Headlight Bulb');
});
