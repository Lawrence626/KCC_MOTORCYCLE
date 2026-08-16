<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseShelf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('users', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('role')->nullable();
        $table->timestamps();
    });

    Schema::create('warehouses', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('code')->nullable();
        $table->timestamps();
    });

    Schema::create('warehouse_shelves', function ($table) {
        $table->id();
        $table->string('shelf_number')->nullable();
        $table->foreignId('warehouse_id')->nullable();
        $table->json('products')->nullable();
        $table->boolean('archived')->default(false);
        $table->timestamps();
    });

    Schema::create('products', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('product_name')->nullable();
        $table->text('description')->nullable();
        $table->string('sku')->nullable();
        $table->string('barcode')->nullable();
        $table->string('category')->nullable();
        $table->string('brand')->nullable();
        $table->string('warehouse')->nullable();
        $table->string('size')->nullable();
        $table->string('color')->nullable();
        $table->decimal('unit_price', 10, 2)->default(0);
        $table->decimal('cost_price', 10, 2)->default(0);
        $table->integer('stock_quantity')->default(0);
        $table->integer('reorder_level')->default(10);
        $table->string('supplier_name')->nullable();
        $table->date('last_restock_date')->nullable();
        $table->date('expiry_date')->nullable();
        $table->string('batch_lot_number')->nullable();
        $table->date('manufacturing_date')->nullable();
        $table->boolean('is_active')->default(true);
        $table->boolean('is_archived')->default(false);
        $table->string('disposal_status')->default('None');
        $table->date('disposal_date_identified')->nullable();
        $table->date('disposal_date_disposed')->nullable();
        $table->text('disposal_reason')->nullable();
        $table->text('compatibility')->nullable();
        $table->unsignedBigInteger('warehouse_shelf_id')->nullable();
        $table->timestamps();
    });

    Schema::create('product_warehouse_stock', function ($table) {
        $table->id();
        $table->foreignId('product_id')->nullable();
        $table->string('warehouse')->nullable();
        $table->integer('quantity')->default(0);
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('product_warehouse_stock');
    Schema::dropIfExists('products');
    Schema::dropIfExists('warehouse_shelves');
    Schema::dropIfExists('warehouses');
    Schema::dropIfExists('users');
});

it('filters products correctly by search, category, location, product_name, brand, size, status, expiry, and date_of_stock', function () {
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    $whA = Warehouse::create(['name' => 'Warehouse A', 'code' => 'WHA']);

    $shelf = WarehouseShelf::create([
        'shelf_number' => 'A-01',
        'warehouse_id' => $whA->id,
        'archived' => false,
        'products' => [
            ['product_id' => 1, 'sku' => 'KCC_PIPE_001', 'qty' => 50],
        ],
    ]);

    // Product 1: Exhaust, PIPE, APIDO, Warehouse A, Available, Non-expiring, Restock 2026-08-10
    $p1 = Product::create([
        'name' => 'ICON BEAT',
        'product_name' => 'PIPE',
        'description' => 'Brand: APIDO',
        'sku' => 'KCC_PIPE_001',
        'barcode' => 'BAR111111',
        'category' => 'Exhaust',
        'brand' => 'APIDO',
        'warehouse' => 'Warehouse A',
        'size' => 'Standard',
        'unit_price' => 250,
        'stock_quantity' => 50,
        'reorder_level' => 10,
        'last_restock_date' => '2026-08-10',
        'expiry_date' => null,
        'is_archived' => false,
    ]);

    // Product 2: Tires, MAGS, RCB SP800, Shop, Low Stock, Non-expiring, Restock 2026-08-12
    $p2 = Product::create([
        'name' => 'AEROX V2',
        'product_name' => 'MAGS',
        'description' => 'Brand: RCB SP800',
        'sku' => 'KCC_MAGS_002',
        'barcode' => 'BAR222222',
        'category' => 'Tires',
        'brand' => 'RCB SP800',
        'warehouse' => 'Shop',
        'size' => '14',
        'unit_price' => 3500,
        'stock_quantity' => 5,
        'reorder_level' => 10,
        'last_restock_date' => '2026-08-12',
        'expiry_date' => null,
        'is_archived' => false,
    ]);

    // Product 3: Oils, ENGINE OIL, YAMALUBE, Shop, Out of Stock, Expiring Soon, Restock 2026-08-15
    $p3 = Product::create([
        'name' => '4T 10W-40',
        'product_name' => 'ENGINE OIL',
        'description' => 'Brand: YAMALUBE',
        'sku' => 'KCC_OIL_003',
        'barcode' => 'BAR333333',
        'category' => 'Oils',
        'brand' => 'YAMALUBE',
        'warehouse' => 'Shop',
        'size' => '800ml',
        'unit_price' => 300,
        'stock_quantity' => 0,
        'reorder_level' => 10,
        'last_restock_date' => '2026-08-15',
        'expiry_date' => now()->addDays(10)->format('Y-m-d'),
        'is_archived' => false,
    ]);

    // 1. Search by SKU
    $res = $this->actingAs($user)->getJson(route('api.products', ['search' => 'KCC_PIPE_001']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.sku', 'KCC_PIPE_001');

    // 2. Search by Barcode
    $res = $this->actingAs($user)->getJson(route('api.products', ['search' => 'BAR222222']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.barcode', 'BAR222222');

    // 3. Category Filter
    $res = $this->actingAs($user)->getJson(route('api.products', ['category' => 'Exhaust']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.category', 'Exhaust');

    // 4. Product Name Filter
    $res = $this->actingAs($user)->getJson(route('api.products', ['product_name' => 'MAGS']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.product_name', 'MAGS');

    // 5. Brand Filter
    $res = $this->actingAs($user)->getJson(route('api.products', ['brand' => 'YAMALUBE']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.brand', 'YAMALUBE');

    // 6. Location Filter (Warehouse A)
    $res = $this->actingAs($user)->getJson(route('api.products', ['warehouse' => 'Warehouse A']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p1->id);

    // 7. Status Filter: Available (active)
    $res = $this->actingAs($user)->getJson(route('api.products', ['status' => 'active']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p1->id);

    // 8. Status Filter: Low Stock (low)
    $res = $this->actingAs($user)->getJson(route('api.products', ['status' => 'low']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p2->id);

    // 9. Status Filter: Out of Stock (out)
    $res = $this->actingAs($user)->getJson(route('api.products', ['status' => 'out']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p3->id);

    // 10. Expiry Filter: expiring
    $res = $this->actingAs($user)->getJson(route('api.products', ['expiry_status' => 'expiring']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p3->id);

    // 11. Size Filter
    $res = $this->actingAs($user)->getJson(route('api.products', ['size' => '14']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p2->id);

    // 12. Date of Stock Filter
    $res = $this->actingAs($user)->getJson(route('api.products', ['date_of_stock' => '2026-08-10']));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p1->id);

    // 13. Combined Filters (Category + Brand + Location)
    $res = $this->actingAs($user)->getJson(route('api.products', [
        'category' => 'Exhaust',
        'brand' => 'APIDO',
        'warehouse' => 'Warehouse A',
    ]));
    $res->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $p1->id);
});
