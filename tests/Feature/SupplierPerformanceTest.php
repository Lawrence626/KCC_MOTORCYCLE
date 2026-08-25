<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ReverseLogistics;
use App\Models\StockArrivalNotice;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Models\User;
use App\Services\SupplierPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('it calculates on-time delivery rate using actual received date', function () {
    $service = new SupplierPerformanceService();

    $supplier = Supplier::create([
        'name' => 'Speedy Supplier',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Tire',
        'sku' => 'TR-001',
        'unit_price' => 500,
        'stock_quantity' => 10,
        'supplier_name' => $supplier->name,
    ]);

    // Order 1: Delivered ON TIME (Expected: Jul 15, Completed: Jul 10)
    $po1 = PurchaseOrder::create([
        'order_number' => 'PO-101',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'expected_delivery_date' => Carbon::parse('2026-07-15'),
        'completed_at' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    PurchaseOrderItem::create([
        'purchase_order_id' => $po1->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 10,
        'received_quantity' => 10,
        'unit_price' => 500,
        'total_price' => 5000,
    ]);

    // Order 2: Delivered LATE (Expected: Jul 12, Completed: Jul 14)
    $po2 = PurchaseOrder::create([
        'order_number' => 'PO-102',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'expected_delivery_date' => Carbon::parse('2026-07-12'),
        'completed_at' => Carbon::parse('2026-07-14 10:00:00'),
    ]);
    PurchaseOrderItem::create([
        'purchase_order_id' => $po2->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 10,
        'received_quantity' => 10,
        'unit_price' => 500,
        'total_price' => 5000,
    ]);

    $orders = PurchaseOrder::with('items')->where('supplier_id', $supplier->id)->get();
    $products = Product::where('supplier_name', $supplier->name)->get();

    $result = $service->calculateForSupplier($supplier, $products, $orders);

    expect($result['delivered_orders_count'])->toBe(2);
    expect($result['on_time_deliveries'])->toBe(1);
    expect($result['on_time_rate'])->toBe(50);
});

test('it calculates order completion rate accounting for backorders and short deliveries', function () {
    $service = new SupplierPerformanceService();

    $supplier = Supplier::create([
        'name' => 'Partial Supplier',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Chain',
        'sku' => 'CH-001',
        'unit_price' => 200,
        'stock_quantity' => 0,
        'supplier_name' => $supplier->name,
    ]);

    // Ordered 100, Received 80 (Back Order of 20)
    $po = PurchaseOrder::create([
        'order_number' => 'PO-201',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'expected_delivery_date' => Carbon::parse('2026-07-15'),
        'completed_at' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 80,
        'unit_price' => 200,
        'total_price' => 20000,
    ]);

    $orders = PurchaseOrder::with('items')->where('supplier_id', $supplier->id)->get();
    $products = Product::where('supplier_name', $supplier->name)->get();

    $result = $service->calculateForSupplier($supplier, $products, $orders);

    expect($result['total_quantity_ordered'])->toBe(100);
    expect($result['total_quantity_received'])->toBe(80);
    expect($result['completion_rate'])->toBe(80);
});

test('it calculates defect rate and quality score from reverse logistics', function () {
    $service = new SupplierPerformanceService();

    $supplier = Supplier::create([
        'name' => 'Quality Supplier',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Spark Plug',
        'sku' => 'SP-001',
        'unit_price' => 150,
        'stock_quantity' => 95,
        'supplier_name' => $supplier->name,
    ]);

    // Received 100 items
    $po = PurchaseOrder::create([
        'order_number' => 'PO-301',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'expected_delivery_date' => Carbon::parse('2026-07-15'),
        'completed_at' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 100,
        'unit_price' => 150,
        'total_price' => 15000,
    ]);

    // 5 Defective items returned
    ReverseLogistics::create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 5,
        'warehouse' => 'Warehouse A',
        'return_reason' => 'Defective from supplier',
        'condition' => 'Damaged',
        'source' => 'Receiving inspection',
        'status' => 'Under Review',
        'reported_date' => Carbon::parse('2026-07-11'),
    ]);

    $orders = PurchaseOrder::with('items')->where('supplier_id', $supplier->id)->get();
    $products = Product::where('supplier_name', $supplier->name)->get();

    $result = $service->calculateForSupplier($supplier, $products, $orders);

    expect($result['defective_quantity'])->toBe(5);
    expect($result['defect_rate'])->toBe(5.0);
    expect($result['quality_score'])->toBe(95);
});

test('it calculates weighted performance score accurately', function () {
    $service = new SupplierPerformanceService();

    $supplier = Supplier::create([
        'name' => 'Weighted Supplier',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Helmet',
        'sku' => 'HL-001',
        'unit_price' => 1000,
        'stock_quantity' => 100,
        'supplier_name' => $supplier->name,
    ]);

    // On-Time Delivery: 100%
    $po = PurchaseOrder::create([
        'order_number' => 'PO-401',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'expected_delivery_date' => Carbon::parse('2026-07-20'),
        'completed_at' => Carbon::parse('2026-07-15 10:00:00'),
    ]);
    // Order Completion: 90% (90 received / 100 ordered)
    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 100,
        'received_quantity' => 90,
        'unit_price' => 1000,
        'total_price' => 100000,
    ]);

    // Price History: 10% price change => Price Stability = 90%
    SupplierPriceHistory::create([
        'supplier_id' => $supplier->id,
        'product_id' => $product->id,
        'supplier_cost' => 1100,
        'previous_cost' => 1000,
        'change_percentage' => 10.0,
    ]);

    $orders = PurchaseOrder::with('items')->where('supplier_id', $supplier->id)->get();
    $products = Product::where('supplier_name', $supplier->name)->get();

    $result = $service->calculateForSupplier($supplier, $products, $orders);

    // Expected:
    // On-Time: 100% * 0.40 = 40
    // Completion: 90% * 0.30 = 27
    // Quality: 100% * 0.20 = 20
    // Price Stability: 90% * 0.10 = 9
    // Performance Score = 40 + 27 + 20 + 9 = 96
    expect($result['on_time_rate'])->toBe(100);
    expect($result['completion_rate'])->toBe(90);
    expect($result['quality_score'])->toBe(100);
    expect($result['price_stability'])->toBe(90);
    expect($result['performance_score'])->toBe(96);
});

test('it returns zero metrics when supplier has no completed orders or insufficient data', function () {
    $service = new SupplierPerformanceService();

    $supplier = Supplier::create([
        'name' => 'Brand New Supplier',
        'status' => 'active',
    ]);

    $orders = collect();
    $products = collect();

    $result = $service->calculateForSupplier($supplier, $products, $orders);

    expect($result['orders_count'])->toBe(0);
    expect($result['delivered_orders_count'])->toBe(0);
    expect($result['on_time_rate'])->toBe(0);
    expect($result['completion_rate'])->toBe(0);
    expect($result['defect_rate'])->toBe(0.0);
    expect($result['quality_score'])->toBe(0);
    expect($result['price_stability'])->toBe(0);
    expect($result['performance_score'])->toBe(0);
});

test('supplier assessment page displays data-driven supplier metrics for admin', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $supplier = Supplier::create([
        'name' => 'Active Supplier Co',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Brake Disc',
        'sku' => 'BD-999',
        'unit_price' => 750,
        'stock_quantity' => 20,
        'supplier_name' => $supplier->name,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-501',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'completed',
        'expected_delivery_date' => Carbon::parse('2026-08-01'),
        'completed_at' => Carbon::parse('2026-07-28 14:00:00'),
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 20,
        'received_quantity' => 20,
        'unit_price' => 750,
        'total_price' => 15000,
    ]);

    $response = $this->actingAs($admin)->get(route('supplier.assessment'));

    $response->assertStatus(200);
    $response->assertSee('Active Supplier Co');
    $response->assertSee('Supplier overview');
    $response->assertSee('Performance Score');
    $response->assertSee('On-Time Delivery');
    $response->assertSee('Order Completion');
});

test('it accurately filters and sorts latest orders by most recent relevant date', function () {
    $supplierA = Supplier::create([
        'name' => 'Yamaha Motors',
        'status' => 'active',
    ]);

    $supplierB = Supplier::create([
        'name' => 'Honda Motors',
        'status' => 'active',
    ]);

    $productA = Product::create([
        'name' => 'Yamaha Piston',
        'sku' => 'YP-100',
        'unit_price' => 500,
        'stock_quantity' => 10,
        'supplier_name' => $supplierA->name,
    ]);

    // Order 1 for Supplier A: Old order created 20 days ago, completed 2 days ago (most recent activity is completion date)
    $po1 = PurchaseOrder::create([
        'order_number' => 'PO-YAM-01',
        'supplier_id' => $supplierA->id,
        'supplier_name' => $supplierA->name,
        'status' => 'completed',
        'total_amount' => 5000,
        'expected_delivery_date' => Carbon::parse('2026-08-10'),
        'completed_at' => Carbon::parse('2026-08-15 10:00:00'),
    ]);
    $po1->timestamps = false;
    $po1->created_at = Carbon::parse('2026-07-28 10:00:00');
    $po1->updated_at = Carbon::parse('2026-08-15 10:00:00');
    $po1->save();

    // Order 2 for Supplier A: Created 10 days ago, in transit (updated 10 days ago)
    $po2 = PurchaseOrder::create([
        'order_number' => 'PO-YAM-02',
        'supplier_id' => $supplierA->id,
        'supplier_name' => $supplierA->name,
        'status' => 'in transit',
        'total_amount' => 3000,
        'expected_delivery_date' => Carbon::parse('2026-08-20'),
    ]);
    $po2->timestamps = false;
    $po2->created_at = Carbon::parse('2026-08-07 10:00:00');
    $po2->updated_at = Carbon::parse('2026-08-07 10:00:00');
    $po2->save();

    // Order 3 for Supplier A: Created 1 day ago, pending approval (created 1 day ago)
    $po3 = PurchaseOrder::create([
        'order_number' => 'PO-YAM-03',
        'supplier_id' => $supplierA->id,
        'supplier_name' => $supplierA->name,
        'status' => 'pending approval',
        'total_amount' => 8000,
        'expected_delivery_date' => Carbon::parse('2026-08-25'),
    ]);
    $po3->timestamps = false;
    $po3->created_at = Carbon::parse('2026-08-16 10:00:00');
    $po3->updated_at = Carbon::parse('2026-08-16 10:00:00');
    $po3->save();

    // Order for Supplier B: should NOT appear in Supplier A's latest orders
    $poB = PurchaseOrder::create([
        'order_number' => 'PO-HON-99',
        'supplier_id' => $supplierB->id,
        'supplier_name' => $supplierB->name,
        'status' => 'completed',
        'total_amount' => 99000,
        'completed_at' => Carbon::parse('2026-08-17 10:00:00'),
    ]);

    $service = new SupplierPerformanceService();
    $ordersA = PurchaseOrder::where('supplier_id', $supplierA->id)->get();
    $productsA = Product::where('supplier_name', $supplierA->name)->get();

    $performanceA = $service->calculateForSupplier($supplierA, $productsA, $ordersA);

    $latestOrders = $performanceA['orders'];

    // Exactly 3 orders for Supplier A, 0 for Supplier B
    expect($latestOrders)->toHaveCount(3);

    // Verify PO-HON-99 is not present
    $poNumbers = collect($latestOrders)->pluck('order_number')->all();
    expect($poNumbers)->not->toContain('PO-HON-99');

    // Expected order:
    // 1st: PO-YAM-03 (Activity: Aug 16)
    // 2nd: PO-YAM-01 (Received/Completed: Aug 15)
    // 3rd: PO-YAM-02 (Activity: Aug 07)
    expect($latestOrders[0]['order_number'])->toBe('PO-YAM-03');
    expect($latestOrders[1]['order_number'])->toBe('PO-YAM-01');
    expect($latestOrders[2]['order_number'])->toBe('PO-YAM-02');

    // Verify fields
    expect($latestOrders[1]['received_date'])->toBe('Aug 15, 2026');
    expect($latestOrders[1]['expected_delivery_date'])->toBe('Aug 10, 2026');
    expect($latestOrders[1]['total_amount'])->toBe(5000.0);
    expect($latestOrders[1]['status'])->toBe('Completed');
});
