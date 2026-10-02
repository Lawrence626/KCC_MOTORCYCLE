<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('filteredSuppliers returns only the designated supplier for a product and excludes unrelated suppliers', function () {
    $supplierA = Supplier::create([
        'name' => 'BCK MOTORCYCLE PARTS',
        'contact_person' => 'BCK Rep',
        'email' => 'bck@example.com',
        'phone' => '09111111111',
        'status' => 'active',
    ]);

    $supplierB = Supplier::create([
        'name' => 'BONTHAI SUPPLIER',
        'contact_person' => 'Bonthai Rep',
        'email' => 'bonthai@example.com',
        'phone' => '09222222222',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'APIDO Exhaust Pipe',
        'product_name' => 'APIDO Pipe Pro',
        'sku' => 'KCC_PIPE_APIDO_004',
        'stock_quantity' => 2,
        'reorder_level' => 5,
        'unit_price' => 1550.00,
        'supplier_name' => 'BCK MOTORCYCLE PARTS',
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->getJson(route('api.order.filtered_suppliers', [
        'product_ids' => [$product->id],
    ]));

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['suppliers'])->toHaveCount(1)
        ->and($data['suppliers'][0]['id'])->toBe($supplierA->id)
        ->and($data['suppliers'][0]['name'])->toBe('BCK MOTORCYCLE PARTS');

    $names = array_column($data['suppliers'], 'name');
    expect($names)->not->toContain('BONTHAI SUPPLIER');
});

test('filteredSuppliers returns error message when products have incompatible suppliers', function () {
    $supplierA = Supplier::create(['name' => 'BCK MOTORCYCLE PARTS', 'status' => 'active']);
    $supplierB = Supplier::create(['name' => 'BONTHAI SUPPLIER', 'status' => 'active']);

    $product1 = Product::create([
        'name' => 'Product 1',
        'sku' => 'SKU-001',
        'stock_quantity' => 2,
        'unit_price' => 500,
        'supplier_name' => 'BCK MOTORCYCLE PARTS',
    ]);

    $product2 = Product::create([
        'name' => 'Product 2',
        'sku' => 'SKU-002',
        'stock_quantity' => 2,
        'unit_price' => 700,
        'supplier_name' => 'BONTHAI SUPPLIER',
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->getJson(route('api.order.filtered_suppliers', [
        'product_ids' => [$product1->id, $product2->id],
    ]));

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['suppliers'])->toBeEmpty()
        ->and($data['message'])->toContain('No supplier can fulfill all selected products');
});

test('supplierComparison strictly compares only qualified suppliers and provides accurate recommendation without false zero cost', function () {
    $supplierA = Supplier::create(['name' => 'BCK MOTORCYCLE PARTS', 'status' => 'active']);
    $supplierB = Supplier::create(['name' => 'BONTHAI SUPPLIER', 'status' => 'active']);

    $product = Product::create([
        'name' => 'APIDO Exhaust Pipe',
        'sku' => 'KCC_PIPE_APIDO_004',
        'stock_quantity' => 2,
        'unit_price' => 1550.00,
        'supplier_name' => 'BCK MOTORCYCLE PARTS',
    ]);

    // Add price history for BCK
    SupplierPriceHistory::create([
        'product_id' => $product->id,
        'supplier_id' => $supplierA->id,
        'supplier_cost' => 1500.00,
        'previous_cost' => 1550.00,
        'change_percentage' => -3.23,
        'recommendation' => 'Supplier cost decreased',
        'suggested_retail_price' => 1800.00,
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->getJson(route('api.order.supplier_comparison', [
        'product_ids' => [$product->id],
    ]));

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['comparison'])->toHaveCount(1)
        ->and($data['comparison'][0]['supplier_name'])->toBe('BCK MOTORCYCLE PARTS')
        ->and($data['comparison'][0]['latest_total_cost'])->toBe(1500)
        ->and($data['recommended']['name'])->toBe('BCK MOTORCYCLE PARTS');

    $names = array_column($data['comparison'], 'supplier_name');
    expect($names)->not->toContain('BONTHAI SUPPLIER');
});

test('store rejects purchase order if selected supplier does not supply the selected products', function () {
    $supplierA = Supplier::create(['name' => 'BCK MOTORCYCLE PARTS', 'status' => 'active']);
    $supplierB = Supplier::create(['name' => 'BONTHAI SUPPLIER', 'status' => 'active']);

    $product = Product::create([
        'name' => 'APIDO Exhaust Pipe',
        'sku' => 'KCC_PIPE_APIDO_004',
        'stock_quantity' => 2,
        'unit_price' => 1550.00,
        'supplier_name' => 'BCK MOTORCYCLE PARTS',
    ]);

    $user = User::factory()->create(['role' => 'admin']);

    // Attempt to order APIDO from BONTHAI
    $response = $this->actingAs($user)->post(route('order.store'), [
        'supplier_id' => $supplierB->id,
        'products' => [[
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 1550.00,
            'selected' => 'on',
        ]],
    ]);

    $response->assertSessionHasErrors('supplier_id');
    expect(PurchaseOrder::count())->toBe(0);

    // Attempt to order APIDO from authorized supplier BCK
    $validResponse = $this->actingAs($user)->post(route('order.store'), [
        'supplier_id' => $supplierA->id,
        'products' => [[
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 1550.00,
            'selected' => 'on',
        ]],
    ]);

    $validResponse->assertSessionHasNoErrors();
    expect(PurchaseOrder::count())->toBe(1)
        ->and(PurchaseOrder::first()->supplier_name)->toBe('BCK MOTORCYCLE PARTS');
});
