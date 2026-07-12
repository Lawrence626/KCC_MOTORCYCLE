<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_arrival_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->integer('quantity');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('purchase_order_number')->nullable();
            $table->string('supplier_name')->nullable();
            $table->timestamp('arrived_at');
            $table->boolean('is_assigned')->default(false);
            $table->unsignedBigInteger('assigned_warehouse_id')->nullable();
            $table->string('assigned_warehouse_name')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_arrival_notices');
    }
};
