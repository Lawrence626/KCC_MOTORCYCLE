<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseShelf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure test warehouses exist
    Warehouse::firstOrCreate(['name' => 'Warehouse A'], ['code' => 'WH-A', 'is_active' => true]);
    Warehouse::firstOrCreate(['name' => 'Warehouse B'], ['code' => 'WH-B', 'is_active' => true]);
    Warehouse::firstOrCreate(['name' => 'Warehouse C'], ['code' => 'WH-C', 'is_active' => true]);
});

test('order detail view provides shelves grouped by warehouse and auto-generates shelves for warehouse B', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $supplier = Supplier::create([
        'name' => 'Test Supplier',
        'contact_person' => 'Juan',
        'email' => 'juan@supplier.test',
        'phone' => '09123456789',
        'address' => 'Supplier Address',
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-TEST-SHELVES-001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'awaiting confirmation',
        'total_amount' => 500,
        'user_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->get(route('order.show', $po));
    $response->assertOk();
    $response->assertViewHas('shelvesByWarehouse');

    $shelves = $response->viewData('shelvesByWarehouse');
    expect($shelves)->toBeArray()
        ->and($shelves)->toHaveKeys(['Shop', 'Warehouse A', 'Warehouse B', 'Warehouse C']);

    // Warehouse B should have shelves populated
    expect(count($shelves['Warehouse B']))->toBeGreaterThan(0);
});

test('authorized users can create a new shelf for warehouse B', function () {
    $clerk = User::factory()->create(['role' => 'inventory_clerk']);

    $response = $this->actingAs($clerk)->postJson(route('order.store_shelf'), [
        'warehouse' => 'Warehouse B',
        'name' => 'Warehouse B - Shelf 99',
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'shelf' => [
            'name' => 'Warehouse B - Shelf 99',
            'warehouse_name' => 'Warehouse B',
        ],
    ]);

    $this->assertDatabaseHas('warehouse_shelves', [
        'name' => 'Warehouse B - Shelf 99',
    ]);
});

test('confirm receive assigns product to the selected warehouse shelf', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $supplier = Supplier::create([
        'name' => 'Parts Supplier',
        'contact_person' => 'Pedro',
        'email' => 'pedro@parts.test',
        'phone' => '09223334444',
        'address' => 'Parts City',
    ]);

    $product = Product::create([
        'sku' => 'KCC_PART_WH_001',
        'name' => 'Motorcycle Brake Pad',
        'unit_price' => 250,
        'stock_quantity' => 0,
        'warehouse' => 'Warehouse B',
        'is_archived' => false,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-TEST-SHELVES-002',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'awaiting confirmation',
        'total_amount' => 2500,
        'user_id' => $admin->id,
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'sku' => $product->sku,
        'product_name' => $product->name,
        'quantity' => 10,
        'received_quantity' => 10,
        'accepted_quantity' => 10,
        'defective_quantity' => 0,
        'unit_price' => 250,
        'total_price' => 2500,
    ]);

    $whB = Warehouse::where('name', 'Warehouse B')->first();
    $shelf = WarehouseShelf::create([
        'warehouse_id' => $whB->id,
        'warehouse_index' => 1,
        'slot_index' => 0,
        'name' => 'Warehouse B - Shelf Special',
        'capacity' => 10,
        'products' => [],
        'archived' => false,
    ]);

    $response = $this->actingAs($admin)->post(route('order.confirm_receive', $po), [
        'warehouse' => 'Warehouse B',
        'shelf_id' => $shelf->id,
    ]);

    $response->assertRedirect(route('order.show', $po));

    $shelf->refresh();
    expect($shelf->products)->toBeArray()
        ->and(count($shelf->products))->toBe(1)
        ->and($shelf->products[0]['sku'])->toBe('KCC_PART_WH_001')
        ->and($shelf->products[0]['qty'])->toBe(10);
});
