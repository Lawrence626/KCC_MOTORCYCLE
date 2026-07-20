<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns low stock notifications when products are at or below ten units', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $product = Product::create([
        'name' => 'Brake Pad',
        'product_name' => 'Brake Pad',
        'sku' => 'BP-001',
        'category' => 'Brakes',
        'stock_quantity' => 10,
        'reorder_level' => 20,
        'unit_price' => 25.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $response = $this->actingAs($admin)->getJson(route('dashboard.data'));

    $response->assertOk();
    $response->assertJsonFragment([
        'product_id' => $product->id,
        'product_name' => 'Brake Pad',
        'stock_quantity' => 10,
    ]);
    $response->assertJsonPath('low_stock_notifications.0.is_dashboard_alert', true);
    $response->assertJsonPath('low_stock_notifications.0.dashboard_alert_visible', true);
    $response->assertJsonPath('low_stock_notifications.0.dashboard_alert_delay_ms', 60000);
});

it('renders a floating dashboard toast for active low stock alerts', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    Product::create([
        'name' => 'Helmet',
        'product_name' => 'Helmet',
        'sku' => 'HL-001',
        'category' => 'Helmets',
        'stock_quantity' => 5,
        'reorder_level' => 10,
        'unit_price' => 30.00,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('id="dashboardLowStockBanner"', false);
    $response->assertSee('role="status"', false);
    $response->assertSee('fixed right-4 top-24', false);
});
