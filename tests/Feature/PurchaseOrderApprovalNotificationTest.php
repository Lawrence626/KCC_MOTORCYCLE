<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('it queues an admin approval toast notification when a purchase order is created', function () {
    Cache::flush();

    $staff = User::factory()->create([
        'role' => 'inventory_clerk',
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $supplier = Supplier::create([
        'name' => 'Test Supplier',
        'contact_person' => 'Test Contact',
        'email' => 'supplier@example.com',
        'phone' => '09170000000',
        'status' => 'active',
        'notes' => 'Test supplier',
    ]);

    $product = Product::create([
        'name' => 'Test Product',
        'product_name' => 'Test Product',
        'description' => 'Test product',
        'sku' => 'SKU-001',
        'barcode' => '123456789',
        'category' => 'Accessories',
        'brand' => 'Test Brand',
        'size' => 'One Size',
        'color' => 'Black',
        'unit_price' => 150,
        'stock_quantity' => 10,
        'reorder_level' => 5,
        'supplier_name' => $supplier->name,
        'is_active' => true,
        'is_archived' => false,
    ]);

    $response = $this->actingAs($staff)->post(route('order.store'), [
        'supplier_id' => $supplier->id,
        'expected_delivery_date' => now()->addWeek()->toDateString(),
        'notes' => 'Please review this order.',
        'products' => [[
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 150,
            'selected' => 'on',
        ]],
    ]);

    $response->assertRedirect(route('order.management'));

    $purchaseOrder = PurchaseOrder::query()->latest()->first();
    expect($purchaseOrder)->not->toBeNull();

    $notifications = Cache::get("admin_purchase_order_notifications:{$admin->id}");

    expect($notifications)->toBeArray()
        ->and($notifications)->toHaveCount(1)
        ->and($notifications[0]['order_number'])->toBe($purchaseOrder->order_number)
        ->and($notifications[0]['message'])->toBe('New Purchase Order Submitted – Approval Required.');
});
