<?php

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ReverseLogistics;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SupplierPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test('it records receive quantity, defective quantity, accepted quantity and stores reverse logistics defect tracking', function () {
    $supplier = Supplier::create([
        'name' => 'Yamaha Genuine Parts',
        'email' => 'yamaha@example.com',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'LEVER',
        'product_name' => 'LEVER',
        'sku' => 'LV-001',
        'category' => 'Controls',
        'unit_price' => 120,
        'stock_quantity' => 0,
        'warehouse' => 'Shop',
        'supplier_name' => $supplier->name,
        'is_active' => true,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-9001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 10800,
        'expected_delivery_date' => now()->addDays(2),
    ]);

    $poItem = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 90,
        'received_quantity' => 0,
        'defective_quantity' => 0,
        'accepted_quantity' => 0,
        'unit_price' => 120,
        'total_price' => 10800,
    ]);

    $controller = new \App\Http\Controllers\PurchaseOrderController();

    // User example: Ordered 90 -> Receive Qty 90 -> Defective Qty 5 -> Accepted Qty 85
    $receiveRequest = new Request([
        'items' => [
            $poItem->id => [
                'item_id' => $poItem->id,
                'received_quantity' => 90,
                'defective_quantity' => 5,
                'defect_reason' => 'Broken lever tip during transit',
                'unit_price' => 120,
            ],
        ],
    ]);

    $response = $controller->receive($receiveRequest, $po);

    expect($response->getSession()->get('success'))->toContain('Purchase order receipt recorded');

    $poItem->refresh();
    expect($poItem->received_quantity)->toBe(90)
        ->and($poItem->defective_quantity)->toBe(5)
        ->and($poItem->accepted_quantity)->toBe(85)
        ->and($poItem->defect_reason)->toBe('Broken lever tip during transit');

    // Verify reverse logistics defect record
    $reverseLogistics = ReverseLogistics::where('product_id', $product->id)->first();
    expect($reverseLogistics)->not->toBeNull()
        ->and($reverseLogistics->quantity)->toBe(5)
        ->and($reverseLogistics->return_reason)->toBe('Broken lever tip during transit');

    // Confirm receipt to inventory (Warehouse: Shop)
    $confirmRequest = new Request([
        'warehouse' => 'Shop',
    ]);
    $controller->confirmReceive($confirmRequest, $po);

    // Verify only ACCEPTED quantity (85) was added to usable inventory
    $product->refresh();
    expect($product->stock_quantity)->toBe(85);

    // Verify StockArrivalNotice recorded accepted quantity (85)
    $arrivalNotice = StockArrivalNotice::where('purchase_order_id', $po->id)->first();
    expect($arrivalNotice)->not->toBeNull()
        ->and($arrivalNotice->quantity)->toBe(85);

    // Verify InventoryMovement recorded accepted quantity (85)
    $movement = InventoryMovement::where('product_id', $product->id)->latest()->first();
    expect($movement)->not->toBeNull()
        ->and($movement->quantity_change)->toBe(85);

    // Verify Supplier Performance Service reflects Defect Rate and Performance Score
    $service = new SupplierPerformanceService();
    $orders = PurchaseOrder::with('items')->where('supplier_id', $supplier->id)->get();
    $products = Product::where('supplier_name', $supplier->name)->get();

    $performance = $service->calculateForSupplier($supplier, $products, $orders);

    expect($performance['total_quantity_ordered'])->toBe(90);
    expect($performance['total_quantity_received'])->toBe(90);
    expect($performance['completion_rate'])->toBe(100);
    expect($performance['defective_quantity'])->toBe(5);
    expect($performance['defect_rate'])->toBe(5.56); // 5 / 90 * 100 = 5.56%
    expect($performance['quality_score'])->toBe(94); // 100 - 5.56 = 94.44% => 94
});

test('it prevents defective quantity from exceeding receive quantity', function () {
    $supplier = Supplier::create([
        'name' => 'Honda Parts',
        'email' => 'honda@example.com',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Cable',
        'product_name' => 'Cable',
        'sku' => 'CB-001',
        'category' => 'Controls',
        'unit_price' => 50,
        'stock_quantity' => 0,
        'supplier_name' => $supplier->name,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-9002',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 500,
    ]);

    $poItem = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 10,
        'received_quantity' => 0,
        'unit_price' => 50,
        'total_price' => 500,
    ]);

    $controller = new \App\Http\Controllers\PurchaseOrderController();

    // Attempt to receive 10 with 15 defects
    $request = new Request([
        'items' => [
            $poItem->id => [
                'item_id' => $poItem->id,
                'received_quantity' => 10,
                'defective_quantity' => 15,
                'defect_reason' => 'Too many defects',
                'unit_price' => 50,
            ],
        ],
    ]);

    $response = $controller->receive($request, $po);

    expect($response->getSession()->get('warning'))->toContain('cannot exceed received quantity');

    $poItem->refresh();
    expect($poItem->received_quantity)->toBe(0);
});

test('it requires defect reason when defective quantity is greater than zero', function () {
    $supplier = Supplier::create([
        'name' => 'Kawasaki Parts',
        'email' => 'kawa@example.com',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Mirror',
        'product_name' => 'Mirror',
        'sku' => 'MR-001',
        'category' => 'Body',
        'unit_price' => 200,
        'stock_quantity' => 0,
        'supplier_name' => $supplier->name,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-9003',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 2000,
    ]);

    $poItem = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 10,
        'received_quantity' => 0,
        'unit_price' => 200,
        'total_price' => 2000,
    ]);

    $controller = new \App\Http\Controllers\PurchaseOrderController();

    // Defective Qty = 2 but empty defect reason
    $request = new Request([
        'items' => [
            $poItem->id => [
                'item_id' => $poItem->id,
                'received_quantity' => 10,
                'defective_quantity' => 2,
                'defect_reason' => '',
                'unit_price' => 200,
            ],
        ],
    ]);

    $response = $controller->receive($request, $po);

    expect($response->getSession()->get('warning'))->toContain('provide a defect reason/remarks');

    $poItem->refresh();
    expect($poItem->received_quantity)->toBe(0);
});

test('it handles partial delivery correctly: remaining = ordered - previously received', function () {
    $supplier = Supplier::create([
        'name' => 'Suzuki Supplier',
        'email' => 'suzuki@example.com',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Brake Pad',
        'product_name' => 'Brake Pad',
        'sku' => 'BP-100',
        'category' => 'Braking',
        'unit_price' => 300,
        'stock_quantity' => 0,
        'supplier_name' => $supplier->name,
    ]);

    // Ordered = 100, Previously Received = 0, Remaining = 100
    $po = PurchaseOrder::create([
        'order_number' => 'PO-9004',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'in transit',
        'total_amount' => 30000,
    ]);

    $poItem = PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 0,
        'unit_price' => 300,
        'total_price' => 30000,
    ]);

    $controller = new \App\Http\Controllers\PurchaseOrderController();

    // Partial delivery: Receive Qty = 70, Defective = 0, Accepted = 70
    $request1 = new Request([
        'items' => [
            $poItem->id => [
                'item_id' => $poItem->id,
                'received_quantity' => 70,
                'defective_quantity' => 0,
                'unit_price' => 300,
            ],
        ],
    ]);

    $controller->receive($request1, $po);

    $poItem->refresh();
    $po->refresh();

    // After receipt: Received = 70, Remaining = 30, Status = partially received
    expect($poItem->received_quantity)->toBe(70)
        ->and($poItem->accepted_quantity)->toBe(70)
        ->and($poItem->quantity - $poItem->received_quantity)->toBe(30)
        ->and($po->status)->toBe('partially received');

    // Second partial delivery: Receive remaining 30 with 2 defects (Accepted = 28)
    $request2 = new Request([
        'items' => [
            $poItem->id => [
                'item_id' => $poItem->id,
                'received_quantity' => 30,
                'defective_quantity' => 2,
                'defect_reason' => 'Scratched pads',
                'unit_price' => 300,
            ],
        ],
    ]);

    $controller->receive($request2, $po);

    $poItem->refresh();
    $po->refresh();

    expect($poItem->received_quantity)->toBe(100)
        ->and($poItem->defective_quantity)->toBe(2)
        ->and($poItem->accepted_quantity)->toBe(98)
        ->and($poItem->quantity - $poItem->received_quantity)->toBe(0)
        ->and($po->status)->toBe('awaiting confirmation');
});

test('it shows only PO ordering information before approval/sending stage and hides receiving fields', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $supplier = Supplier::create([
        'name' => 'Apido Racing Supply',
        'email' => 'apido@example.com',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'PIPE',
        'product_name' => 'PIPE',
        'sku' => 'KCC_PIPE_APIDO_004',
        'category' => 'Exhaust',
        'unit_price' => 150,
        'stock_quantity' => 5,
        'supplier_name' => $supplier->name,
    ]);

    // Create PO in 'approved' stage (waiting to send to supplier)
    $po = PurchaseOrder::create([
        'order_number' => 'PO-9005',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'approved',
        'total_amount' => 15000,
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'unit_price' => 150,
        'total_price' => 15000,
    ]);

    $response = $this->actingAs($admin)->get(route('order.show', $po));

    $response->assertStatus(200);
    $response->assertSee('PIPE');
    $response->assertSee('KCC_PIPE_APIDO_004');
    $response->assertSee('100');
    $response->assertSee('₱150.00');
    $response->assertSee('₱15,000.00');
    $response->assertSee('Supplier Cost/Unit');

    // Make sure 'Received', 'Defective', 'Accepted' table headers are NOT in the items table for approved stage
    $content = $response->getContent();
    expect($content)->toContain('Supplier Cost/Unit')
        ->and($content)->toContain('Send to Supplier')
        ->and($content)->not->toContain('<th class="px-4 py-3">Received</th>')
        ->and($content)->not->toContain('<th class="px-4 py-3">Defective</th>')
        ->and($content)->not->toContain('<th class="px-4 py-3">Accepted</th>');
});
