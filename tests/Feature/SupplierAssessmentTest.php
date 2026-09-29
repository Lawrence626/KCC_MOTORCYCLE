<?php

use App\Models\FastMovingProduct;
use App\Models\POSTransaction;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SlowMovingProduct;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it loads supplier assessment with fast and slow moving breakdown per supplier', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $supplier = Supplier::create([
        'name' => 'Yamaha Official',
        'contact_person' => 'Kenji Sato',
        'contact_position' => 'Sales Director',
        'email' => 'kenji@yamaha.com',
        'phone' => '09171234567',
        'address' => 'Mandaluyong City',
        'status' => 'active',
    ]);

    // Product 1: Fast moving (sold 20 units)
    $prodFast = Product::create([
        'name' => 'Yamaha Engine Oil 4T',
        'sku' => 'YAM-OIL-01',
        'unit_price' => 350,
        'stock_quantity' => 50,
        'category' => 'Lubricants',
        'supplier_name' => $supplier->name,
    ]);

    // Product 2: Slow moving (sold 0 units)
    $prodSlow = Product::create([
        'name' => 'Yamaha Vintage Decal',
        'sku' => 'YAM-DEC-99',
        'unit_price' => 150,
        'stock_quantity' => 10,
        'category' => 'Accessories',
        'supplier_name' => $supplier->name,
    ]);

    // Record POS transaction for fast product
    POSTransaction::create([
        'invoice_number' => 'INV-TEST-01',
        'user_id' => $admin->id,
        'status' => 'completed',
        'completed_at' => now(),
        'total_amount' => 7000,
        'items' => [
            [
                'id' => $prodFast->id,
                'name' => $prodFast->name,
                'sku' => $prodFast->sku,
                'quantity' => 20,
                'unit_price' => 350,
            ],
        ],
    ]);

    $response = $this->actingAs($admin)->get(route('supplier.assessment'));

    $response->assertStatus(200);
    $response->assertSee('Supplier Assessment');
    $response->assertSee('Yamaha Official');
    $response->assertSee('Fast Moving Items');
    $response->assertSee('Slow Moving Items');
    $response->assertSee('Select Supplier Partner');
    $response->assertSee('Yamaha Engine Oil 4T');
    $response->assertSee('Yamaha Vintage Decal');
});

test('it allows adding a new supplier and redirects with selected supplier parameter', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post(route('supplier.assessment.store'), [
        'name' => 'Kawasaki Motors Ph',
        'contact_person' => 'Maria Santos',
        'contact_position' => 'Account Manager',
        'email' => 'maria@kawasaki.ph',
        'phone' => '09189876543',
        'address' => 'Laguna Technopark, Biñan',
        'notes' => 'Direct OEM distributor partner',
    ]);

    $response->assertRedirect(route('supplier.assessment', ['selected_supplier' => 'Kawasaki Motors Ph']));
    $response->assertSessionHas('success', 'Supplier added successfully.');

    $this->assertDatabaseHas('suppliers', [
        'name' => 'Kawasaki Motors Ph',
        'contact_person' => 'Maria Santos',
        'email' => 'maria@kawasaki.ph',
        'status' => 'active',
    ]);
});

test('it prevents adding a duplicate supplier name', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Supplier::create([
        'name' => 'Existing Supplier Co',
        'status' => 'active',
    ]);

    $response = $this->actingAs($admin)->post(route('supplier.assessment.store'), [
        'name' => 'Existing Supplier Co',
    ]);

    $response->assertSessionHasErrors(['name']);
});
