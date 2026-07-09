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

    $historyEntry = SupplierPriceHistory::query()
        ->where('product_id', $product->id)
        ->where('supplier_id', $supplier->id)
        ->latest('id')
        ->first();

    expect($historyEntry)->not->toBeNull()
        ->and($historyEntry->supplier_cost)->toEqual(280)
        ->and($historyEntry->change_percentage)->toEqual(12.0);
});
