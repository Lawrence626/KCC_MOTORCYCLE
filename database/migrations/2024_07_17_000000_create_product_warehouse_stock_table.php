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
        Schema::create('product_warehouse_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('warehouse'); // SHOP, Warehouse A, Warehouse B, Warehouse C
            $table->integer('quantity')->default(0);
            $table->timestamps();
            
            // Ensure unique combination of product and warehouse
            $table->unique(['product_id', 'warehouse']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouse_stock');
    }
};
