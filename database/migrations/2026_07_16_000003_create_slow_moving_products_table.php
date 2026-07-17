<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slow_moving_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('days_without_sale');
            $table->dateTime('last_sold_date')->nullable();
            $table->integer('units_sold_30_days')->default(0);
            $table->integer('units_sold_60_days')->default(0);
            $table->integer('units_sold_90_days')->default(0);
            $table->integer('current_stock')->default(0);
            $table->decimal('stock_value', 12, 2)->default(0);
            $table->decimal('velocity_score', 5, 2)->default(0); // Lower = slower
            $table->timestamps();

            $table->unique('product_id');
            $table->index('velocity_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slow_moving_products');
    }
};
