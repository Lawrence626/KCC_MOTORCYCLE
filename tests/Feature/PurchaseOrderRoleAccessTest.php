<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('inventory clerk can see purchase order links in sidebar and access purchase order pages', function () {
    $clerk = User::factory()->create([
        'role' => 'inventory_clerk',
    ]);

    // Check dashboard response includes Purchase Order sidebar elements for clerk
    $response = $this->actingAs($clerk)->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Purchase Order');
    $response->assertSee(route('order.management'));
    $response->assertSee(route('received.orders'));

    // Check inventory clerk can access Order Management
    $orderManagementResponse = $this->actingAs($clerk)->get(route('order.management'));
    $orderManagementResponse->assertStatus(200);
    $orderManagementResponse->assertSee('Order Management');
    $orderManagementResponse->assertSee(route('order.create'));

    // Check inventory clerk can access Create Purchase Order page
    $createResponse = $this->actingAs($clerk)->get(route('order.create'));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Create Purchase Order');

    // Check inventory clerk can access Received Orders page
    $receivedResponse = $this->actingAs($clerk)->get(route('received.orders'));
    $receivedResponse->assertStatus(200);
    $receivedResponse->assertSee('Received Orders');
});

test('order detail page shows approve and reject buttons only for admin and not inventory clerk', function () {
    $clerk = User::factory()->create([
        'role' => 'inventory_clerk',
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $supplier = Supplier::create([
        'name' => 'Supplier Test',
        'contact_person' => 'Contact Test',
        'email' => 'supplier@test.com',
        'phone' => '09123456789',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Test Item',
        'product_name' => 'Test Item',
        'description' => 'Test item description',
        'sku' => 'TEST-001',
        'barcode' => '987654321',
        'category' => 'Accessories',
        'brand' => 'Test',
        'size' => 'M',
        'color' => 'Red',
        'unit_price' => 200,
        'stock_quantity' => 15,
        'reorder_level' => 5,
        'supplier_name' => $supplier->name,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $po = PurchaseOrder::create([
        'order_number' => 'PO-TEST-0001',
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'status' => 'pending approval',
        'total_amount' => 400,
        'expected_delivery_date' => now()->addDays(5),
    ]);

    PurchaseOrderItem::create([
        'purchase_order_id' => $po->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 2,
        'unit_price' => 200,
        'total_price' => 400,
    ]);

    // As inventory clerk: can view details, but cannot see Approve/Reject buttons or update estimated delivery
    $clerkShow = $this->actingAs($clerk)->get(route('order.show', $po));
    $clerkShow->assertStatus(200);
    $clerkShow->assertSee('PO-TEST-0001');
    $clerkShow->assertDontSee('Approve</button>', false);
    $clerkShow->assertDontSee('Reject</button>', false);

    // As admin: can view details and sees Approve/Reject buttons for clerk-submitted PO
    $adminShow = $this->actingAs($admin)->get(route('order.show', $po));
    $adminShow->assertStatus(200);
    $adminShow->assertSee('Approve</button>', false);
    $adminShow->assertSee('Reject</button>', false);
});

test('when admin creates a purchase order it is immediately approved and ready to send to supplier', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $supplier = Supplier::create([
        'name' => 'Direct Supplier',
        'contact_person' => 'Direct Contact',
        'email' => 'direct@supplier.com',
        'phone' => '09180000000',
        'status' => 'active',
    ]);

    $product = Product::create([
        'name' => 'Direct Item',
        'product_name' => 'Direct Item',
        'description' => 'Direct item description',
        'sku' => 'DIR-001',
        'barcode' => '1122334455',
        'category' => 'Accessories',
        'brand' => 'Direct Brand',
        'size' => 'L',
        'color' => 'Blue',
        'unit_price' => 300,
        'stock_quantity' => 10,
        'reorder_level' => 5,
        'supplier_name' => $supplier->name,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $response = $this->actingAs($admin)->post(route('order.store'), [
        'supplier_id' => $supplier->id,
        'expected_delivery_date' => now()->addDays(3)->toDateString(),
        'notes' => 'Admin purchase order test',
        'products' => [[
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 5,
            'unit_price' => 300,
            'selected' => 'on',
        ]],
    ]);

    $purchaseOrder = PurchaseOrder::query()->latest()->first();
    expect($purchaseOrder)->not->toBeNull()
        ->and($purchaseOrder->status)->toBe('approved')
        ->and($purchaseOrder->approved_at)->not->toBeNull()
        ->and($purchaseOrder->user_id)->toBe($admin->id)
        ->and($purchaseOrder->created_by_role)->toBe('admin');

    $response->assertRedirect(route('order.show', $purchaseOrder));

    // Inspect the order detail page for this admin-created PO:
    // Should NOT show Approve or Reject buttons, but SHOULD show Send to Supplier button
    $detailResponse = $this->actingAs($admin)->get(route('order.show', $purchaseOrder));
    $detailResponse->assertStatus(200);
    $detailResponse->assertDontSee('Approve</button>', false);
    $detailResponse->assertDontSee('Reject</button>', false);
    $detailResponse->assertSee('Send to Supplier</button>', false);
});

