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
        $table->integer('stock_quantity')->default(0);
        $table->integer('reorder_level')->default(0);
        $table->boolean('is_archived')->default(false);
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
