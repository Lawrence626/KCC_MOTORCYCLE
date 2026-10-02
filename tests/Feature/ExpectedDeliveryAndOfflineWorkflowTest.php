<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it calculates exactly 7 working days excluding weekends', function () {
    // Test from a Monday (2026-10-05) -> 7 working days: Tue(1), Wed(2), Thu(3), Fri(4), Mon(5), Tue(6), Wed(7) -> 2026-10-14
    $monday = Carbon::parse('2026-10-05 09:00:00');
    $delivery = PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7, $monday);
    expect($delivery->toDateString())->toBe('2026-10-14')
        ->and($delivery->isWeekend())->toBeFalse();

    // Test from a Friday (2026-10-09) -> 7 working days: Mon(1), Tue(2), Wed(3), Thu(4), Fri(5), Mon(6), Tue(7) -> 2026-10-20
    $friday = Carbon::parse('2026-10-09 15:00:00');
    $deliveryFromFri = PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7, $friday);
    expect($deliveryFromFri->toDateString())->toBe('2026-10-20')
        ->and($deliveryFromFri->isWeekend())->toBeFalse();
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
        ->and($po->expected_delivery_date->toDateString())->toBe($po->estimated_delivery_date->toDateString())
        ->and($po->expected_delivery_date->isWeekend())->toBeFalse();

    // Check that expected delivery date is exactly 7 working days from today
    $expectedDate = PurchaseOrder::calculateDefaultWorkingDaysDeliveryDate(7)->toDateString();
    expect($po->expected_delivery_date->toDateString())->toBe($expectedDate);

    // Order detail view shows the auto-filled estimated delivery date
    $detailResponse = $this->actingAs($admin)->get(route('order.show', $po));
    $detailResponse->assertStatus(200);
    $detailResponse->assertSee($po->expected_delivery_date->format('M j, Y'));
    $detailResponse->assertSee('Send to Supplier');
});

test('create purchase order page renders with 7 working days default date and offline support', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $response = $this->actingAs($admin)->get(route('order.create'));
    $response->assertStatus(200);
    $response->assertSee('Expected Delivery Date');
    $response->assertSee('7 Working Days Allotment');
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

