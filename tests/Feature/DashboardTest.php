<?php

use App\Models\POSTransaction;
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
});

afterEach(function () {
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
