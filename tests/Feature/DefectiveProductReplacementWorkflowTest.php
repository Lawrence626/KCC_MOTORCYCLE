<?php

use App\Models\DefectiveReturnRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates defective return request during receipt without creating a back order when full quantity delivered', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create([
        'name' => 'Akrapovic Exhausts',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Akrapovic Pipe Slip-On',
        'product_name' => 'Akrapovic Pipe Slip-On',
        'sku' => 'AKRA-001',
        'category' => 'Exhaust',
        'unit_price' => 1500,
        'stock_quantity' => 10,
        'reorder_level' => 5,
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-DEF-101',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'sent to supplier',
        'total_amount' => 150000,
    ]);

    $item = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 0,
        'defective_quantity' => 0,
        'accepted_quantity' => 0,
        'unit_price' => 1500,
        'total_price' => 150000,
    ]);

    // Record receipt: Ordered 100 -> Receive Qty 100 -> Defective Qty 5 -> Accepted Qty 95 -> Remaining 0
    $response = $this->actingAs($admin)->post(route('order.receive', $po), [
        'items' => [
            $item->id => [
                'item_id' => $item->id,
                'received_quantity' => 100,
                'defective_quantity' => 5,
                'defect_reason' => 'Dent on silencer canister',
                'unit_price' => 1500,
            ],
        ],
    ]);

    $response->assertRedirect(route('order.show', $po));

    $item->refresh();
    $po->refresh();

    // Verify PO Item values
    expect($item->received_quantity)->toBe(100)
        ->and($item->defective_quantity)->toBe(5)
        ->and($item->accepted_quantity)->toBe(95)
        ->and($item->defect_reason)->toBe('Dent on silencer canister');

    // Verify PO status is awaiting confirmation because all 100 units were received (Remaining = 0)
    expect($po->status)->toBe('awaiting confirmation');

    // Verify DefectiveReturnRequest created
    $defectReq = DefectiveReturnRequest::where('purchase_order_id', $po->id)->first();
    expect($defectReq)->not->toBeNull()
        ->and($defectReq->product_id)->toBe($product->id)
        ->and($defectReq->defective_quantity)->toBe(5)
        ->and($defectReq->defect_reason)->toBe('Dent on silencer canister')
        ->and($defectReq->status)->toBe('Pending Supplier Response')
        ->and($defectReq->resolution)->toBeNull();
});

test('it records supplier resolution as replacement and generates replacement back order', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create(['name' => 'Brembo Brakes', 'status' => 'active']);
    $product = Product::create([
        'name' => 'Brembo Caliper P4',
        'product_name' => 'Brembo Caliper P4',
        'sku' => 'BRM-004',
        'category' => 'Brakes',
        'unit_price' => 3000,
        'stock_quantity' => 5,
        'reorder_level' => 2,
        'supplier_name' => $supplier->name,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-DEF-202',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
    ]);

    $item = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 50,
        'received_quantity' => 50,
        'defective_quantity' => 4,
        'accepted_quantity' => 46,
        'unit_price' => 3000,
        'total_price' => 150000,
    ]);

    $defectReq = DefectiveReturnRequest::create([
        'purchase_order_id' => $po->id,
        'purchase_order_item_id' => $item->id,
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'defective_quantity' => 4,
        'defect_reason' => 'Missing bleeder screw',
        'status' => 'Pending Supplier Response',
    ]);

    // Record Resolution: Replacement with Expected Replacement Delivery Date
    $response = $this->actingAs($admin)->post(route('order.defective.resolve', [$po, $defectReq]), [
        'resolution' => 'Replacement',
        'expected_replacement_date' => '2026-08-20',
        'resolution_notes' => 'Supplier agreed to dispatch 4 replacement units via LBC next week.',
    ]);

    $response->assertRedirect(route('order.show', $po));

    $defectReq->refresh();
    expect($defectReq->resolution)->toBe('Replacement')
        ->and($defectReq->status)->toBe('Awaiting Replacement')
        ->and($defectReq->expected_replacement_date->toDateString())->toBe('2026-08-20')
        ->and($defectReq->replacement_order_number)->toBe('RPO-PO-DEF-202-' . $defectReq->id)
        ->and($defectReq->replacement_purchase_order_id)->not->toBeNull()
        ->and($defectReq->resolution_notes)->toBe('Supplier agreed to dispatch 4 replacement units via LBC next week.')
        ->and($defectReq->resolved_at)->not->toBeNull();

    // Verify separate PurchaseOrder was created for the replacement
    $replacementPO = PurchaseOrder::find($defectReq->replacement_purchase_order_id);
    expect($replacementPO)->not->toBeNull()
        ->and($replacementPO->order_number)->toBe('RPO-PO-DEF-202-' . $defectReq->id)
        ->and($replacementPO->supplier_name)->toBe($supplier->name)
        ->and($replacementPO->status)->toBe('sent to supplier')
        ->and($replacementPO->expected_delivery_date->toDateString())->toBe('2026-08-20');

    // Verify replacement PO item
    $replacementItem = $replacementPO->items->first();
    expect($replacementItem)->not->toBeNull()
        ->and($replacementItem->product_id)->toBe($product->id)
        ->and($replacementItem->quantity)->toBe(4)
        ->and($replacementItem->received_quantity)->toBe(0);

    // Verify original PO items and quantities remain untouched
    $item->refresh();
    expect($item->quantity)->toBe(50)
        ->and($item->received_quantity)->toBe(50)
        ->and($item->defective_quantity)->toBe(4)
        ->and($item->accepted_quantity)->toBe(46);

    // Verify order detail view renders Replacement PO link and expected date
    $viewResponse = $this->actingAs($admin)->get(route('order.show', $po));
    $viewResponse->assertStatus(200);
    $viewResponse->assertSee($replacementPO->order_number);
    $viewResponse->assertSee('Expected replacement:');
    $viewResponse->assertSee('Aug 20, 2026');
});

test('it processes replacement through separate Replacement PO workflow and updates inventory upon completion', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create(['name' => 'Ohlins Suspension', 'status' => 'active']);
    $product = Product::create([
        'name' => 'Ohlins Rear Shock YA-768',
        'product_name' => 'Ohlins Rear Shock YA-768',
        'sku' => 'OHL-768',
        'category' => 'Suspension',
        'unit_price' => 18000,
        'stock_quantity' => 90,
        'supplier_name' => $supplier->name,
    ]);

    // Original PO: Ordered 94 -> Received 94 -> Defective 4 -> Accepted 90 -> Completed
    $po = PurchaseOrder::create([
        'order_number' => 'PO-DEF-404',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'completed_at' => now()->subDays(3),
    ]);

    $poItem = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 94,
        'received_quantity' => 94,
        'defective_quantity' => 4,
        'accepted_quantity' => 90,
        'unit_price' => 18000,
        'total_price' => 1692000,
    ]);

    $defectReq = DefectiveReturnRequest::create([
        'purchase_order_id' => $po->id,
        'purchase_order_item_id' => $poItem->id,
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'defective_quantity' => 4,
        'defect_reason' => 'Damaged preload knob',
        'status' => 'Pending Supplier Response',
    ]);

    // 1. Resolve as Replacement -> Creates separate Replacement PO for 4 units
    $this->actingAs($admin)->post(route('order.defective.resolve', [$po, $defectReq]), [
        'resolution' => 'Replacement',
        'expected_replacement_date' => '2026-08-25',
    ]);

    $defectReq->refresh();
    $replacementPO = PurchaseOrder::find($defectReq->replacement_purchase_order_id);
    expect($replacementPO)->not->toBeNull();
    $replacementItem = $replacementPO->items->first();

    // 2. Replacement arrives: Staff receives it through the Replacement PO
    $receiveResp = $this->actingAs($admin)->post(route('order.receive', $replacementPO), [
        'items' => [
            $replacementItem->id => [
                'item_id' => $replacementItem->id,
                'received_quantity' => 4,
                'defective_quantity' => 0,
                'unit_price' => 0,
            ],
        ],
    ]);

    $receiveResp->assertRedirect(route('order.show', $replacementPO));

    // Confirm receipt to warehouse
    $confirmResp = $this->actingAs($admin)->post(route('order.confirm_receive', $replacementPO), [
        'warehouse' => 'Shop',
    ]);

    $confirmResp->assertRedirect(route('order.show', $replacementPO));

    $replacementPO->refresh();
    $replacementItem->refresh();
    $defectReq->refresh();
    $product->refresh();
    $po->refresh();
    $poItem->refresh();

    // Verify Replacement PO is completed
    expect($replacementPO->status)->toBe('completed')
        ->and($replacementItem->received_quantity)->toBe(4)
        ->and($replacementItem->accepted_quantity)->toBe(4);

    // Verify accepted replacement units added to usable stock (90 + 4 = 94)
    expect($product->stock_quantity)->toBe(94);

    // Verify DefectiveReturnRequest is completed
    expect($defectReq->status)->toBe('Completed')
        ->and($defectReq->replacement_received_quantity)->toBe(4)
        ->and($defectReq->replacement_received_at)->not->toBeNull();

    // Verify Original PO remains completed with unchanged numbers
    expect($po->status)->toBe('completed')
        ->and($poItem->quantity)->toBe(94)
        ->and($poItem->received_quantity)->toBe(94)
        ->and($poItem->defective_quantity)->toBe(4)
        ->and($poItem->accepted_quantity)->toBe(90);
});

test('it displays delivery date not provided if replacement date is not set', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $supplier = Supplier::create(['name' => 'Kyt Helmets', 'status' => 'active']);
    $product = Product::create([
        'name' => 'KYT TT Course Helmet',
        'product_name' => 'KYT TT Course Helmet',
        'sku' => 'KYT-001',
        'unit_price' => 4500,
        'stock_quantity' => 5,
        'supplier_name' => $supplier->name,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-DEF-505',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
    ]);

    $defectReq = DefectiveReturnRequest::create([
        'purchase_order_id' => $po->id,
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'defective_quantity' => 1,
        'defect_reason' => 'Visor latch broken',
        'status' => 'Awaiting Replacement',
        'resolution' => 'Replacement',
        'replacement_order_number' => 'RBO-PO-DEF-505-1',
        'expected_replacement_date' => null,
    ]);

    $response = $this->actingAs($admin)->get(route('order.show', $po));
    $response->assertStatus(200);
    $response->assertSee('Delivery date not provided');
});
