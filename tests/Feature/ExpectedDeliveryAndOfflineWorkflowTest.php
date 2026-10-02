<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it calculates exactly 7 days from order date', function () {
    // Test from 2026-10-02 -> 7 days -> 2026-10-09
    $date = Carbon::parse('2026-10-02 09:00:00');
    $delivery = PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7, $date);
    expect($delivery->toDateString())->toBe('2026-10-09');

    // Test from 2026-10-05 -> 7 days -> 2026-10-12
    $monday = Carbon::parse('2026-10-05 09:00:00');
    $deliveryFromMon = PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7, $monday);
    expect($deliveryFromMon->toDateString())->toBe('2026-10-12');
});

test('saving a new purchase order without delivery date auto-fills 7 working days before sending to supplier', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Yamaha Genuine Parts', 'status' => 'active']);
    $product = Product::create([
        'name' => 'Yamaha Aerox V-Belt',
        'product_name' => 'Yamaha Aerox V-Belt',
        'sku' => 'YMH-AER-01',
        'stock_quantity' => 2,
        'unit_price' => 500,
        'supplier_name' => $supplier->name,
        'is_active' => true,
        'is_archived' => false,
    ]);

    // Admin creates purchase order via store action
    $response = $this->actingAs($admin)->post(route('order.store'), [
        'supplier_id' => $supplier->id,
        'products' => [
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'quantity' => 10,
                'unit_price' => 500,
                'selected' => '1',
            ]
        ],
        'notes' => 'Urgent replenishment',
    ]);

    $po = PurchaseOrder::latest('id')->first();
    expect($po)->not->toBeNull()
        ->and($po->status)->toBe('approved')
        ->and($po->expected_delivery_date)->not->toBeNull()
        ->and($po->estimated_delivery_date)->not->toBeNull()
        ->and($po->expected_delivery_date->toDateString())->toBe($po->estimated_delivery_date->toDateString());

    // Check that expected delivery date is exactly 7 days from today
    $expectedDate = PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7)->toDateString();
    expect($po->expected_delivery_date->toDateString())->toBe($expectedDate);

    // Order detail view shows the auto-filled estimated delivery date
    $detailResponse = $this->actingAs($admin)->get(route('order.show', $po));
    $detailResponse->assertStatus(200);
    $detailResponse->assertSee($po->expected_delivery_date->format('M j, Y'));
    $detailResponse->assertSee('Send to Supplier');
});

test('create purchase order page renders with 7 days default date and offline support', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('order.create'));
    $response->assertStatus(200);
    $response->assertSee('Expected Delivery Date');
    $response->assertSee('7 Days Delivery Allotment');
    $response->assertSee('po-create-offline-banner');
    $response->assertSee('offline-manager.js');
});

test('layout includes notification bell with offline manager integration and offline reconciliation sidebar', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('notification-bell-btn');
    $response->assertSee('offline-manager.js');
    $response->assertSee('sidebar-offline-recon-group');
    $response->assertSee('sidebar-offline-pending-badge');
    $response->assertSee('Import Data');
    $response->assertSee('Pending Imports');
    $response->assertSee('Sync History');
});

test('check-synced-orders endpoint accurately returns existing database purchase orders', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'KCC Honda Supplies', 'status' => 'active']);

    $po1 = PurchaseOrder::create([
        'order_number' => 'PO-20261001-9803',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'pending',
        'sync_status' => 'synchronized',
        'total_amount' => 1500,
    ]);

    $response = $this->actingAs($admin)->postJson(route('offline.check.synced'), [
        'order_numbers' => ['PO-20261001-9803', 'PO-NON-EXISTENT-9999']
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'synced_order_numbers' => ['PO-20261001-9803']
    ]);
});

