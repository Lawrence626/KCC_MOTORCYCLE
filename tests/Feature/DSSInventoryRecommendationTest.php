<?php

use App\Models\DSSRecommendation;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\DSSRecommendationEngineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('fast-moving product with high sales gets recommendation to increase reorder level', function () {
    // 1. Create a product with stock=20 and reorder_level=30 (as in user prompt example)
    $product = Product::create([
        'name' => 'Pipe 2-inch',
        'product_name' => 'Pipe 2-inch',
        'sku' => 'PIP-2IN-001',
        'category' => 'Exhaust',
        'brand' => 'Standard',
        'stock_quantity' => 20,
        'reorder_level' => 30,
        'unit_price' => 150.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // 2. Create POS completed transaction in current month with 150 units sold
    POSTransaction::create([
        'invoice_number' => 'INV-TEST-001',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(5),
        'subtotal' => 22500.00,
        'total_amount' => 22500.00,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 150,
                'unit_price' => 150.00,
            ]
        ]
    ]);

    // 3. Run DSS recommendation engine
    $engine = app(DSSRecommendationEngineService::class);
    $engine->generateAllRecommendations();

    // 4. Verify recommendation was generated for this product
    $recommendation = DSSRecommendation::where('product_id', $product->id)
        ->where('is_active', true)
        ->first();

    expect($recommendation)->not->toBeNull();
    expect($recommendation->recommendation_type)->toBe('reorder_level');
    expect($recommendation->priority)->toBe('Critical'); // stock=20 is <= reorder_level=30 and below monthly demand
    expect($recommendation->hasStockoutRisk())->toBeTrue();

    // Verify recommendation text contains key elements from user specification
    expect($recommendation->description)->toContain('High sales detected this month');
    expect($recommendation->description)->toContain('150 units sold');
    expect($recommendation->description)->toContain('fast-moving');
    expect($recommendation->description)->toContain('increasing the reorder level');
    expect($recommendation->description)->toContain('maintaining higher stock levels');
    expect($recommendation->description)->toContain('Warning: Current stock is critically low and at high risk of stockout');

    // Verify metadata contains actual data
    expect($recommendation->metadata['sales_this_month'])->toBe(150);
    expect($recommendation->metadata['current_stock'])->toBe(20);
    expect($recommendation->metadata['current_reorder_level'])->toBe(30);
    expect($recommendation->metadata['suggested_reorder_level'])->toBeGreaterThan(30);
    expect($recommendation->metadata['rationale'])->toContain('150 units sold this month');
});

test('normal sales product receives recommendation to maintain current reorder level without unnecessary increase', function () {
    $product = Product::create([
        'name' => 'Standard Brake Pad',
        'product_name' => 'Standard Brake Pad',
        'sku' => 'BRK-PAD-001',
        'category' => 'Brakes',
        'brand' => 'Brembo',
        'stock_quantity' => 50,
        'reorder_level' => 10,
        'unit_price' => 300.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // POS sales: 12 units sold this month (moderate/normal)
    POSTransaction::create([
        'invoice_number' => 'INV-TEST-002',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(2),
        'subtotal' => 3600.00,
        'total_amount' => 3600.00,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 12,
                'unit_price' => 300.00,
            ]
        ]
    ]);

    $engine = app(DSSRecommendationEngineService::class);
    $engine->generateAllRecommendations();

    $recommendation = DSSRecommendation::where('product_id', $product->id)
        ->where('is_active', true)
        ->first();

    expect($recommendation)->not->toBeNull();
    expect($recommendation->recommendation_type)->toBe('normal_stock');
    expect($recommendation->description)->toContain('Sales performance is normal and steady');
    expect($recommendation->description)->toContain('Do not recommend increasing the reorder level unnecessarily');
    expect($recommendation->metadata['sales_this_month'])->toBe(12);
});

test('low sales product receives recommendation to review excess stock or maintain reorder level', function () {
    $product = Product::create([
        'name' => 'Vintage Clutch Cable',
        'product_name' => 'Vintage Clutch Cable',
        'sku' => 'CLT-CAB-001',
        'category' => 'Cables',
        'brand' => 'OEM',
        'stock_quantity' => 40,
        'reorder_level' => 10,
        'unit_price' => 200.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // Only 1 unit sold this month
    POSTransaction::create([
        'invoice_number' => 'INV-TEST-003',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(1),
        'subtotal' => 200.00,
        'total_amount' => 200.00,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'unit_price' => 200.00,
            ]
        ]
    ]);

    $engine = app(DSSRecommendationEngineService::class);
    $engine->generateAllRecommendations();

    $recommendation = DSSRecommendation::where('product_id', $product->id)
        ->where('is_active', true)
        ->first();

    expect($recommendation)->not->toBeNull();
    expect($recommendation->recommendation_type)->toBe('low_sales_review');
    expect($recommendation->description)->toContain('Low sales detected this month');
    expect($recommendation->description)->toContain('maintaining the current reorder level');
});

test('admin can apply suggested reorder level from recommendation', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $product = Product::create([
        'name' => 'Motor Oil 4T',
        'product_name' => 'Motor Oil 4T',
        'sku' => 'OIL-4T-001',
        'category' => 'Lubricants',
        'brand' => 'Castrol',
        'stock_quantity' => 15,
        'reorder_level' => 20,
        'unit_price' => 250.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // 100 units sold this month
    POSTransaction::create([
        'invoice_number' => 'INV-TEST-004',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(3),
        'subtotal' => 25000.00,
        'total_amount' => 25000.00,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 100,
                'unit_price' => 250.00,
            ]
        ]
    ]);

    $engine = app(DSSRecommendationEngineService::class);
    $engine->generateAllRecommendations();

    $recommendation = DSSRecommendation::where('product_id', $product->id)
        ->where('recommendation_type', 'reorder_level')
        ->first();

    $suggestedLevel = $recommendation->getSuggestedReorderLevel();
    expect($suggestedLevel)->toBeGreaterThan(20);

    // Apply via web route
    $response = $this->actingAs($admin)
        ->post(route('dss.recommendations.apply-reorder', $recommendation->id));

    $response->assertSessionHas('success');

    // Product reorder level must be updated
    $product->refresh();
    expect($product->reorder_level)->toBe($suggestedLevel);

    // Recommendation must be marked as actioned
    $recommendation->refresh();
    expect($recommendation->action_taken_at)->not->toBeNull();
});

test('admin and inventory clerk can view dss recommendations index and show pages', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $product = Product::create([
        'name' => 'LED Headlight Bulb',
        'product_name' => 'LED Headlight Bulb',
        'sku' => 'LED-HL-001',
        'category' => 'Lighting',
        'brand' => 'Osram',
        'stock_quantity' => 25,
        'reorder_level' => 15,
        'unit_price' => 350.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $recommendation = DSSRecommendation::create([
        'product_id' => $product->id,
        'recommendation_type' => 'reorder_level',
        'title' => 'Fast-Moving Stock Alert: Increase Reorder Level',
        'description' => 'High sales detected this month (80 units sold). Classify as fast-moving.',
        'priority' => 'High',
        'metadata' => [
            'sales_this_month' => 80,
            'current_stock' => 25,
            'current_reorder_level' => 15,
            'suggested_reorder_level' => 35,
            'sales_velocity' => 4.5,
            'stockout_risk' => 'medium',
            'rationale' => 'Sales increased sharply requiring higher reorder point.',
        ],
        'is_active' => true,
        'generated_at' => now(),
    ]);

    // Admin access
    $this->actingAs($admin)
        ->get(route('dss.recommendations.index'))
        ->assertStatus(200)
        ->assertSee('DSS Inventory Recommendations')
        ->assertSee('LED Headlight Bulb');

    $this->actingAs($admin)
        ->get(route('dss.recommendations.show', $recommendation->id))
        ->assertStatus(200)
        ->assertSee('LED Headlight Bulb')
        ->assertSee('80 units');

    // Inventory Clerk access
    $this->actingAs($clerk)
        ->get(route('dss.recommendations.index'))
        ->assertStatus(200)
        ->assertSee('LED Headlight Bulb');

    $this->actingAs($clerk)
        ->get(route('dss.recommendations.show', $recommendation->id))
        ->assertStatus(200)
        ->assertSee('LED Headlight Bulb');
});

test('notifications include DSS fast-moving suggestions and direct purchase order link', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $product = Product::create([
        'name' => 'Drive Chain 428H',
        'product_name' => 'Drive Chain 428H',
        'sku' => 'CHN-428H-001',
        'category' => 'Drive Chain',
        'brand' => 'DID',
        'stock_quantity' => 10,
        'reorder_level' => 20,
        'unit_price' => 600.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // Create fast moving sales (120 units this month)
    POSTransaction::create([
        'invoice_number' => 'INV-TEST-CHN',
        'status' => 'completed',
        'completed_at' => Carbon::now()->startOfMonth()->addDays(4),
        'subtotal' => 72000.00,
        'total_amount' => 72000.00,
        'items' => [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => 120,
                'unit_price' => 600.00,
            ]
        ]
    ]);

    // Generate DSS recommendations
    $engine = app(DSSRecommendationEngineService::class);
    $engine->generateAllRecommendations();

    // Sync inventory alerts
    $alertService = app(\App\Services\InventoryAlertService::class);
    $alertService->syncAlerts();

    // Fetch notifications via API
    $response = $this->actingAs($admin)
        ->getJson(route('api.inventory-notifications.index'));

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['notifications'])->not->toBeEmpty();

    $notification = collect($data['notifications'])->firstWhere('product_id', $product->id);
    expect($notification)->not->toBeNull();
    expect($notification['order_url'])->toContain('/purchase-order/create?product_id=' . $product->id);
    expect($notification['dss_suggestion'])->not->toBeNull();
    expect($notification['dss_suggestion']['is_fast_moving'])->toBeTrue();
    expect($notification['dss_suggestion']['suggested_reorder_level'])->toBeGreaterThan(20);
    expect($notification['dss_suggestion']['sales_this_month'])->toBe(120);
});
