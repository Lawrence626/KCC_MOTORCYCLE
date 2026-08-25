<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('users', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->string('role')->nullable();
        $table->timestamps();
    });

    Schema::create('products', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('category')->nullable();
        $table->string('sku')->nullable();
        $table->integer('stock_quantity')->default(0);
        $table->integer('reorder_level')->default(0);
        $table->boolean('is_active')->default(true);
        $table->boolean('is_archived')->default(false);
        $table->timestamps();
    });

    Schema::create('inventory_notifications', function ($table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->string('sku');
        $table->string('notification_type');
        $table->integer('current_stock')->default(0);
        $table->integer('reorder_point')->default(0);
        $table->string('status')->default('unread');
        $table->timestamp('dismissed_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
    });

    Schema::create('pos_transactions', function ($table) {
        $table->id();
        $table->string('invoice_number')->unique();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->json('items')->nullable();
        $table->decimal('subtotal', 10, 2)->default(0);
        $table->decimal('services_total', 10, 2)->default(0);
        $table->decimal('extra_charge', 10, 2)->default(0);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('tax', 10, 2)->default(0);
        $table->decimal('total_amount', 10, 2)->default(0);
        $table->string('payment_method')->nullable();
        $table->string('status')->default('completed');
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
    });

    Schema::create('inventory_movements', function ($table) {
        $table->id();
        $table->unsignedBigInteger('product_id')->nullable();
        $table->string('type');
        $table->integer('quantity_change')->default(0);
        $table->decimal('unit_price', 10, 2)->nullable();
        $table->string('supplier_name')->nullable();
        $table->text('notes')->nullable();
        $table->json('metadata')->nullable();
        $table->string('sync_status')->default('pending_sync');
        $table->timestamps();
    });
});

afterEach(function () {
    Schema::dropIfExists('inventory_notifications');
    Schema::dropIfExists('inventory_movements');
    Schema::dropIfExists('pos_transactions');
    Schema::dropIfExists('products');
    Schema::dropIfExists('users');
});

it('reduces stock for products sold through the POS', function () {
    $user = User::create([
        'name' => 'POS User',
        'email' => 'pos@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    $product = Product::create([
        'name' => 'Brake Pads',
        'category' => 'Brakes',
        'stock_quantity' => 10,
        'reorder_level' => 2,
    ]);

    $response = $this->actingAs($user)->postJson(route('api.pos.transactions.store'), [
        'invoice_number' => 'INV-2001',
        'items' => [[
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 3,
            'unit_price' => 1500,
            'category' => 'Brakes',
        ]],
        'subtotal' => 4500,
        'services_total' => 0,
        'extra_charge' => 0,
        'discount' => 0,
        'tax' => 540,
        'total_amount' => 5040,
        'payment_method' => 'cash',
    ]);

    $response->assertCreated();
    $product->refresh();
    expect($product->stock_quantity)->toBe(7);
});

it('validates stock correctly via api.pos.validate_stock', function () {
    $user = User::create([
        'name' => 'Cashier User',
        'email' => 'cashier@example.com',
        'password' => Hash::make('password123'),
        'role' => 'cashier',
    ]);

    $inStock = Product::create([
        'name' => 'Chain Lube',
        'stock_quantity' => 5,
    ]);

    $outOfStock = Product::create([
        'name' => 'Helmet Visor',
        'stock_quantity' => 0,
    ]);

    $lowStock = Product::create([
        'name' => 'Spark Plug',
        'stock_quantity' => 2,
    ]);

    // Test with sufficient stock
    $res1 = $this->actingAs($user)->postJson(route('api.pos.validate_stock'), [
        'items' => [
            ['id' => $inStock->id, 'quantity' => 3],
        ],
    ]);
    $res1->assertOk();
    expect($res1->json('valid'))->toBeTrue();
    expect($res1->json('errors'))->toBeEmpty();

    // Test with out of stock product
    $res2 = $this->actingAs($user)->postJson(route('api.pos.validate_stock'), [
        'items' => [
            ['id' => $outOfStock->id, 'quantity' => 1],
        ],
    ]);
    $res2->assertOk();
    expect($res2->json('valid'))->toBeFalse();
    expect($res2->json('items.0.is_out_of_stock'))->toBeTrue();

    // Test with quantity exceeding available stock
    $res3 = $this->actingAs($user)->postJson(route('api.pos.validate_stock'), [
        'items' => [
            ['id' => $lowStock->id, 'quantity' => 5],
        ],
    ]);
    $res3->assertOk();
    expect($res3->json('valid'))->toBeFalse();
    expect($res3->json('items.0.is_insufficient'))->toBeTrue();
    expect($res3->json('items.0.current_stock'))->toBe(2);
});

it('prevents checkout when product is out of stock (0 stock)', function () {
    $user = User::create([
        'name' => 'Cashier User',
        'email' => 'cashier@example.com',
        'password' => Hash::make('password123'),
        'role' => 'cashier',
    ]);

    $zeroStockProduct = Product::create([
        'name' => 'Zero Stock Item',
        'stock_quantity' => 0,
    ]);

    $response = $this->actingAs($user)->postJson(route('api.pos.transactions.store'), [
        'invoice_number' => 'INV-ZERO-1',
        'items' => [[
            'id' => $zeroStockProduct->id,
            'name' => $zeroStockProduct->name,
            'quantity' => 1,
            'unit_price' => 500,
        ]],
        'subtotal' => 500,
        'services_total' => 0,
        'extra_charge' => 0,
        'discount' => 0,
        'tax' => 60,
        'total_amount' => 560,
        'payment_method' => 'cash',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['success' => false]);
    $zeroStockProduct->refresh();
    expect($zeroStockProduct->stock_quantity)->toBe(0);
});

it('prevents checkout when requested quantity exceeds available stock', function () {
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    $product = Product::create([
        'name' => 'Limited Stock Item',
        'stock_quantity' => 2,
    ]);

    $response = $this->actingAs($user)->postJson(route('api.pos.transactions.store'), [
        'invoice_number' => 'INV-EXCEED-1',
        'items' => [[
            'id' => $product->id,
            'name' => $product->name,
            'quantity' => 5,
            'unit_price' => 200,
        ]],
        'subtotal' => 1000,
        'services_total' => 0,
        'extra_charge' => 0,
        'discount' => 0,
        'tax' => 120,
        'total_amount' => 1120,
        'payment_method' => 'cash',
    ]);

    $response->assertStatus(422);
    $response->assertJsonFragment(['success' => false]);
    $product->refresh();
    expect($product->stock_quantity)->toBe(2);
});

