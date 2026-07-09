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
        Schema::create('shop_inventory_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_shelf_id')->nullable()->constrained('shop_shelves')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('action_type'); // 'transfer_in', 'transfer_out', 'pos_sale', 'restock'
            $table->integer('quantity_change');
            $table->string('source_type')->nullable(); // 'warehouse', 'shop_shelf'
            $table->foreignId('source_id')->nullable(); // warehouse_shelf_id or shop_shelf_id
            $table->string('destination_type')->nullable(); // 'shop_shelf'
            $table->foreignId('destination_id')->nullable(); // shop_shelf_id
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('shop_shelf_id');
            $table->index('action_type');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_inventory_history');
    }
};
