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
        Schema::create('fast_moving_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('units_sold_7_days')->default(0);
            $table->integer('units_sold_30_days')->default(0);
            $table->integer('units_sold_60_days')->default(0);
            $table->integer('units_sold_90_days')->default(0);
            $table->decimal('velocity_score', 5, 2)->default(0); // Higher = faster
            $table->decimal('turnover_rate', 5, 2)->default(0);
            $table->integer('current_stock')->default(0);
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
        Schema::dropIfExists('fast_moving_products');
    }
};
