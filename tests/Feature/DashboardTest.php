<?php

use App\Models\POSTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

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
        $table->string('product_name')->nullable();
        $table->string('category')->nullable();
        $table->string('sku')->nullable();
        $table->decimal('unit_price', 10, 2)->default(0);
        $table->decimal('cost_price', 10, 2)->default(0);
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
});

afterEach(function () {
    Schema::dropIfExists('inventory_notifications');
    Schema::dropIfExists('pos_transactions');
    Schema::dropIfExists('products');
    Schema::dropIfExists('users');
});

it('returns dashboard analytics for completed pos transactions', function () {
    $user = User::create([
        'name' => 'Dashboard User',
        'email' => 'dashboard@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    POSTransaction::create([
        'invoice_number' => 'INV-1001',
        'user_id' => $user->id,
        'items' => [
            [
                'id' => 1,
                'name' => 'Brake Pads',
                'quantity' => 2,
                'unit_price' => 1500,
                'cost_price' => 1000,
                'category' => 'Brakes',
            ],
        ],
        'subtotal' => 3000,
        'services_total' => 0,
        'extra_charge' => 0,
        'discount' => 0,
        'tax' => 360,
        'total_amount' => 3360,
        'payment_method' => 'cash',
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    POSTransaction::create([
        'invoice_number' => 'INV-1002',
        'user_id' => $user->id,
        'items' => [
            [
                'id' => 2,
                'name' => 'Pipe Set',
                'quantity' => 3,
                'unit_price' => 1200,
                'cost_price' => 900,
                'category' => 'pipe',
            ],
        ],
        'subtotal' => 100,
        'services_total' => 0,
        'extra_charge' => 0,
        'discount' => 0,
        'tax' => 12,
        'total_amount' => 112,
        'payment_method' => 'cash',
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson(route('dashboard.data'));

    $response->assertOk()
        ->assertJsonStructure([
            'metrics',
            'sales_chart',
            'category_chart',
            'comparison_chart',
            'top_items',
            'inventory',
            'range_label',
        ])
        ->assertJsonPath('metrics.sales.value', 3472)
        ->assertJsonPath('comparison_chart.labels.0', 'Current Period')
        ->assertJsonPath('top_items.0.category', 'Exhaust');
});

it('allows otp login to reach the dashboard when the user is not yet email verified', function () {
    $user = User::create([
        'name' => 'OTP User',
        'email' => 'otp@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'email_verified_at' => null,
    ]);

    \Illuminate\Support\Facades\Cache::put('login_otp_' . hash('sha256', strtolower(trim($user->email))), [
        'user_id'  => $user->id,
        'code'     => '123456',
        'remember' => false,
    ], now()->addMinutes(10));

    $verifyResponse = $this->postJson(route('login.otp.verify'), [
        'email' => $user->email,
        'code' => '123456',
    ]);

    $verifyResponse->assertOk()
        ->assertJsonPath('redirectUrl', route('dashboard'));

    $dashboardResponse = $this->get(route('dashboard'));

    $dashboardResponse->assertOk();
});

it('calculates sales by category even when transaction items have minimal fields', function () {
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin_cat@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
    ]);

    $product = \App\Models\Product::create([
        'name' => 'ICON BEAT',
        'product_name' => 'PIPE',
        'category' => 'uncategorized',
        'sku' => 'KCC_PIPE_001',
        'unit_price' => 200,
        'stock_quantity' => 50,
        'reorder_level' => 10,
        'is_active' => true,
        'is_archived' => false,
    ]);

    POSTransaction::create([
        'invoice_number' => 'INV-CAT-1',
        'user_id' => $user->id,
        'items' => [
            [
                'id' => $product->id,
                'quantity' => 5,
            ],
        ],
        'subtotal' => 1000,
        'services_total' => 0,
        'extra_charge' => 0,
        'discount' => 0,
        'tax' => 0,
        'total_amount' => 1000,
        'payment_method' => 'cash',
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson(route('dashboard.data'));

    $response->assertOk()
        ->assertJsonPath('category_chart.labels.0', 'Exhaust')
        ->assertJsonPath('category_chart.data.0', 1000);
});

it('automatically sets product category from product_name or name when uncategorized or empty', function () {
    $p1 = \App\Models\Product::create([
        'name' => 'ICON BEAT',
        'product_name' => 'PIPE',
        'category' => 'uncategorized',
        'unit_price' => 200,
    ]);

    expect($p1->category)->toBe('Exhaust');

    $p2 = \App\Models\Product::create([
        'name' => 'SHOCK A3',
        'product_name' => '',
        'category' => '',
        'unit_price' => 500,
    ]);

    expect($p2->category)->toBe('Accessories');
});


