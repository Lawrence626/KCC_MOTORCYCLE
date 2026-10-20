<?php

use App\Http\Controllers\PurchaseOrderController;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('it records supplier price history and calculates the change when a purchase order is received', function () {
    $supplier = Supplier::create([
        'name' => 'Honda Supplier',
        'email' => 'supplier@example.com',
    ]);

    $product = Product::create([
        'name' => 'Brake Pad',
        'sku' => 'BP-001',
        'category' => 'Braking',
        'unit_price' => 380,
        'stock_quantity' => 0,
        'reorder_level' => 2,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    SupplierPriceHistory::create([
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'supplier_cost' => 250,
    ]);

    $purchaseOrder = PurchaseOrder::create([
        'order_number' => 'PO-1001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 2800,
    ]);

    $purchaseOrderItem = PurchaseOrderItem::create([
        'purchase_order_id' => $purchaseOrder->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 10,
        'received_quantity' => 0,
        'unit_price' => 280,
        'total_price' => 2800,
    ]);

    $controller = new PurchaseOrderController();
    $request = new Request([
        'items' => [
            [
                'item_id' => $purchaseOrderItem->id,
                'received_quantity' => 10,
            ],
        ],
    ]);

    $response = $controller->receive($request, $purchaseOrder);

    expect($response->getTargetUrl())->toContain('purchase-order');

    $confirmRequest = new Request([
        'warehouse' => 'Warehouse A',
    ]);
    $controller->confirmReceive($confirmRequest, $purchaseOrder);

    $historyEntry = SupplierPriceHistory::query()
        ->where('product_id', $product->id)
        ->where('supplier_id', $supplier->id)
        ->latest('id')
        ->first();

    expect($historyEntry)->not->toBeNull()
        ->and($historyEntry->supplier_cost)->toEqual(280)
        ->and($historyEntry->change_percentage)->toEqual(12.0);
});

test('it updates product latest cost and analysis when a custom receiving cost is recorded', function () {
    $supplier = Supplier::create([
        'name' => 'Suzuki Supplier',
        'email' => 'suzuki@example.com',
    ]);

    $product = Product::create([
        'name' => 'PIPE',
        'product_name' => 'PIPE',
        'sku' => 'KCC-ICONBEAT',
        'category' => 'Exhaust',
        'unit_price' => 320,
        'stock_quantity' => 10,
        'reorder_level' => 2,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    // Create the first purchase order (already completed)
    $po1 = PurchaseOrder::create([
        'order_number' => 'PO-1001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'total_amount' => 20000,
        'completed_at' => now()->subDay(),
    ]);

    $poItem1 = PurchaseOrderItem::create([
        'purchase_order_id' => $po1->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 100,
        'unit_price' => 200,
        'total_price' => 20000,
    ]);

    // Create the second purchase order (in transit, pending receipt)
    $po2 = PurchaseOrder::create([
        'order_number' => 'PO-1002',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 22000,
    ]);

    $poItem2 = PurchaseOrderItem::create([
        'purchase_order_id' => $po2->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 0,
        'unit_price' => 200,
        'total_price' => 20000,
    ]);

    // Record receipt with updated supplier cost (₱220.00)
    $controller = new PurchaseOrderController();
    $request = new Request([
        'items' => [
            $poItem2->id => [
                'item_id' => $poItem2->id,
                'received_quantity' => 100,
                'unit_price' => 220,
            ],
        ],
    ]);

    $controller->receive($request, $po2);

    // Call AnalyticsController pricing module logic
    $analyticsController = new \App\Http\Controllers\AnalyticsController(new \App\Services\VatCalculationService());
    $view = $analyticsController->pricing();
    $data = $view->getData();
    $supplierCostAnalysis = $data['supplierCostAnalysis'];

    // Find our product's analysis result
    $analysis = collect($supplierCostAnalysis->items())->firstWhere('product_id', $product->id);

    expect($analysis)->not->toBeNull();
    expect((float) $analysis->previous_cost)->toEqual(200.0);
    expect((float) $analysis->supplier_cost)->toEqual(220.0);
    expect((float) $analysis->change_percentage)->toEqual(10.0);
    expect((float) $analysis->suggested_retail_price)->toEqual(264.0); // round(220 * 1.20, 2)
    expect($analysis->recommendation)->toContain('Increase the retail price');
});

test('supplierDetails API returns catalog baseline price and suggested retail price when no previous history exists', function () {
    $supplier = Supplier::create([
        'name' => 'BCK MOTORCYCLE PARTS',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'APIDO Exhaust Pipe',
        'sku' => 'KCC_PIPE_APIDO_004',
        'stock_quantity' => 2,
        'unit_price' => 1550.00,
        'supplier_name' => 'BCK MOTORCYCLE PARTS',
    ]);

    $user = \App\Models\User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->getJson(route('api.order.supplier_details', [
        'supplier_id' => $supplier->id,
        'product_ids' => [$product->id],
    ]));

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['price_histories'])->toHaveCount(1)
        ->and($data['price_histories'][0]['catalog_price'])->toBe(1550)
        ->and($data['price_histories'][0]['current_cost'])->toBe(1550)
        ->and($data['price_histories'][0]['previous_cost'])->toBeNull()
        ->and($data['price_histories'][0]['suggested_retail'])->toBe(1860) // 1550 * 1.20
        ->and($data['price_histories'][0]['trend'])->toBe('stable');
});

test('supplierDetails API returns previous_cost as null when only one purchase order exists and price has not changed', function () {
    $supplier = Supplier::create([
        'name' => 'Suzuki Supplier',
        'email' => 'suzuki@example.com',
    ]);

    $product = Product::create([
        'name' => 'PIPE',
        'product_name' => 'PIPE',
        'sku' => 'KCC-PIPE-001',
        'unit_price' => 1300.00,
        'stock_quantity' => 10,
        'reorder_level' => 2,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    // Single PO in history with cost 1,400.00
    $po = PurchaseOrder::create([
        'order_number' => 'PO-20261013235936-IS4V',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'total_amount' => 1400,
        'completed_at' => now(),
    ]);

    SupplierPriceHistory::create([
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'purchase_order_id' => $po->id,
        'previous_cost' => null,
        'supplier_cost' => 1400.00,
        'change_percentage' => 0,
        'recommendation' => 'Supplier pricing is stable. Maintain the current retail price.',
        'suggested_retail_price' => 1680.00,
    ]);

    $user = \App\Models\User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->getJson(route('api.order.supplier_details', [
        'supplier_id' => $supplier->id,
        'product_ids' => [$product->id],
    ]));

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['price_histories'])->toHaveCount(1)
        ->and($data['price_histories'][0]['current_cost'])->toEqual(1400.00)
        ->and($data['price_histories'][0]['previous_cost'])->toBeNull()
        ->and($data['price_histories'][0]['trend'])->toBe('stable');
});

test('it records previous_cost as null on first PO receipt and captures previous_cost on second PO when price changes', function () {
    $supplier = Supplier::create([
        'name' => 'Kawasaki Supplier',
        'email' => 'kawasaki@example.com',
    ]);

    $product = Product::create([
        'name' => 'Exhaust Muffler',
        'sku' => 'EM-001',
        'unit_price' => 1000.00,
        'stock_quantity' => 5,
        'reorder_level' => 2,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    // 1. Create and receive first PO at 1400.00
    $po1 = PurchaseOrder::create([
        'order_number' => 'PO-FIRST',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 1400,
    ]);

    $item1 = PurchaseOrderItem::create([
        'purchase_order_id' => $po1->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 1,
        'received_quantity' => 0,
        'unit_price' => 1400.00,
        'total_price' => 1400.00,
    ]);

    $controller = new PurchaseOrderController();
    $controller->receive(new Request(['items' => [['item_id' => $item1->id, 'received_quantity' => 1]]]), $po1);
    $controller->confirmReceive(new Request(['warehouse' => 'Warehouse A']), $po1);

    $entry1 = SupplierPriceHistory::where('product_id', $product->id)
        ->where('supplier_id', $supplier->id)
        ->latest('id')
        ->first();

    // First PO should NOT have a previous_cost from catalog, should be null
    expect($entry1)->not->toBeNull()
        ->and($entry1->supplier_cost)->toEqual(1400.00)
        ->and($entry1->previous_cost)->toBeNull()
        ->and($entry1->change_percentage)->toEqual(0.00);

    // 2. Create and receive second PO with updated cost 1500.00
    $po2 = PurchaseOrder::create([
        'order_number' => 'PO-SECOND',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 1500,
    ]);

    $item2 = PurchaseOrderItem::create([
        'purchase_order_id' => $po2->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 1,
        'received_quantity' => 0,
        'unit_price' => 1500.00,
        'total_price' => 1500.00,
    ]);

    $controller->receive(new Request(['items' => [['item_id' => $item2->id, 'received_quantity' => 1]]]), $po2);
    $controller->confirmReceive(new Request(['warehouse' => 'Warehouse A']), $po2);

    $entry2 = SupplierPriceHistory::where('product_id', $product->id)
        ->where('supplier_id', $supplier->id)
        ->latest('id')
        ->first();

    // Second PO should have previous_cost = 1400.00, change_percentage = 7.14%
    expect($entry2)->not->toBeNull()
        ->and($entry2->supplier_cost)->toEqual(1500.00)
        ->and($entry2->previous_cost)->toEqual(1400.00)
        ->and($entry2->change_percentage)->toEqual(7.14);
});

