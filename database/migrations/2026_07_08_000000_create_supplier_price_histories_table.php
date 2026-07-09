<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('previous_cost', 12, 2)->nullable();
            $table->decimal('supplier_cost', 12, 2);
            $table->decimal('change_percentage', 8, 2)->nullable();
            $table->string('recommendation')->nullable();
            $table->text('reason')->nullable();
            $table->decimal('suggested_retail_price', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_price_histories');
    }
};
