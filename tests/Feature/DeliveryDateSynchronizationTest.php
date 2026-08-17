<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SupplierPerformanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it synchronizes estimated_delivery_date and expected_delivery_date on model save and update', function () {
    $supplier = Supplier::create(['name' => 'Yamaha Genuine Parts', 'status' => 'active']);

    // 1. Setting expected_delivery_date auto-populates estimated_delivery_date
    $po1 = PurchaseOrder::create([
        'order_number' => 'PO-SYNC-001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'sent to supplier',
        'expected_delivery_date' => '2026-08-18',
    ]);

    expect($po1->expected_delivery_date->toDateString())->toBe('2026-08-18')
        ->and($po1->estimated_delivery_date->toDateString())->toBe('2026-08-18');

    // 2. Setting estimated_delivery_date auto-populates expected_delivery_date
    $po2 = PurchaseOrder::create([
        'order_number' => 'PO-SYNC-002',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'sent to supplier',
        'estimated_delivery_date' => '2026-08-25',
    ]);

    expect($po2->expected_delivery_date->toDateString())->toBe('2026-08-25')
        ->and($po2->estimated_delivery_date->toDateString())->toBe('2026-08-25');

    // 3. Updating estimated_delivery_date synchronizes both
    $po1->update(['estimated_delivery_date' => '2026-08-22']);
    $po1->refresh();
    expect($po1->expected_delivery_date->toDateString())->toBe('2026-08-22')
        ->and($po1->estimated_delivery_date->toDateString())->toBe('2026-08-22');
});

test('order details displays identical delivery date for ETA and Estimated Delivery Date card', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Honda Phils', 'status' => 'active']);
    $product = Product::create([
        'name' => 'Honda Drive Belt',
        'product_name' => 'Honda Drive Belt',
        'sku' => 'HND-BELT-01',
        'unit_price' => 850,
        'supplier_name' => $supplier->name,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-SYNC-003',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'sent to supplier',
        'expected_delivery_date' => '2026-08-18',
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 20,
        'unit_price' => 850,
        'total_price' => 17000,
    ]);

    $response = $this->actingAs($admin)->get(route('order.show', $po));
    $response->assertStatus(200);

    // Both ETA and Estimated Delivery Date card show Aug 18, 2026
    $response->assertSee('ETA:</span> Aug 18, 2026', false);
    $response->assertSee('Aug 18, 2026');
    $response->assertDontSee('Not yet provided');

    // Updating delivery date via controller updates both and displays new date
    $updateResp = $this->actingAs($admin)->put(route('order.update_estimated_delivery', $po), [
        'estimated_delivery_date' => '2026-08-30',
    ]);
    $updateResp->assertRedirect(route('order.show', $po));

    $po->refresh();
    expect($po->estimated_delivery_date->toDateString())->toBe('2026-08-30')
        ->and($po->expected_delivery_date->toDateString())->toBe('2026-08-30');

    $viewResp2 = $this->actingAs($admin)->get(route('order.show', $po));
    $viewResp2->assertSee('ETA:</span> Aug 30, 2026', false);
    $viewResp2->assertSee('Aug 30, 2026');
});

test('order details and orders table show Not yet provided when no date is set', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Suzuki Parts', 'status' => 'active']);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-SYNC-004',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'pending approval',
        'expected_delivery_date' => null,
        'estimated_delivery_date' => null,
    ]);

    $response = $this->actingAs($admin)->get(route('order.show', $po));
    $response->assertStatus(200);
    $response->assertSee('ETA:</span> Not yet provided', false);
    $response->assertSee('Not yet provided');

    $mgmtResp = $this->actingAs($admin)->get(route('order.management'));
    $mgmtResp->assertStatus(200);
    $mgmtResp->assertSee('Not yet provided');
});

test('supplier overview latest orders uses the same synchronized expected delivery date', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $supplier = Supplier::create(['name' => 'Kawasaki Genuine', 'status' => 'active']);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-SYNC-005',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'sent to supplier',
        'expected_delivery_date' => '2026-08-18',
    ]);

    $service = app(SupplierPerformanceService::class);
    $summary = $service->calculateForSupplier($supplier, collect(), collect([$po]));

    $latestOrder = collect($summary['orders'])->firstWhere('order_number', 'PO-SYNC-005');
    expect($latestOrder)->not->toBeNull()
        ->and($latestOrder['expected_delivery_date'])->toBe('Aug 18, 2026');
});
