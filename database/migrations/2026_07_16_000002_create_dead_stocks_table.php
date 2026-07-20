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
        Schema::create('dead_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->integer('days_without_sale');
            $table->dateTime('last_sold_date')->nullable();
            $table->integer('current_stock')->default(0);
            $table->decimal('stock_value', 12, 2)->default(0);
            $table->string('priority_level'); // Critical, High, Medium, Low
            $table->text('analysis_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('detected_at');
            $table->dateTime('last_analyzed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('priority_level');
            $table->index('is_active');
            $table->index('last_sold_date');
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dead_stocks');
    }
};
