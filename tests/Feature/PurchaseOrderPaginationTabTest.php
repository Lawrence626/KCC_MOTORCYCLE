<?php

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('purchase order management pagination retains active tab for received orders', function () {
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

    // Create 15 completed purchase orders so that received orders have multiple pages
    for ($i = 1; $i <= 15; $i++) {
        PurchaseOrder::create([
            'order_number' => "PO-RCV-{$i}",
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->name,
            'status' => 'completed',
            'total_amount' => 100 * $i,
            'approved_at' => now(),
            'completed_at' => now(),
        ]);
    }

    // 1. Visit page 1 with tab=received
    $response = $this->actingAs($admin)->get(route('order.management', ['tab' => 'received']));
    $response->assertStatus(200);
    $response->assertViewHas('activeTab', 'received');
    // Ensure pagination link for page 2 contains tab=received
    $response->assertSee('received_page=2');
    $response->assertSee('tab=received');

    // 2. Visit page 2 with received_page=2 (even without tab in url, should infer received tab)
    $responsePage2 = $this->actingAs($admin)->get(route('order.management', ['received_page' => 2]));
    $responsePage2->assertStatus(200);
    $responsePage2->assertViewHas('activeTab', 'received');
    $responsePage2->assertSee('tab=received');

    // 3. Make AJAX request for page 2 of received orders and verify JSON partial response
    $ajaxResponse = $this->actingAs($admin)->getJson(route('order.management', ['tab' => 'received', 'received_page' => 2]), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);
    $ajaxResponse->assertStatus(200);
    $ajaxResponse->assertJsonStructure(['tab', 'html']);
    expect($ajaxResponse->json('tab'))->toBe('received');
    expect($ajaxResponse->json('html'))->toContain('PO-RCV-');
    expect($ajaxResponse->json('html'))->toContain('tab=received');
});

