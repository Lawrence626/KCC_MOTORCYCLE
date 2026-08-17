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
        Schema::create('defective_return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name');
            $table->string('sku')->nullable();
            $table->unsignedInteger('defective_quantity')->default(1);
            $table->string('defect_reason')->nullable();
            $table->string('warehouse')->default('Shop');
            $table->string('status')->default('Pending Supplier Response'); // Pending Supplier Response, Replacement Approved, Refund Approved, Awaiting Replacement, Completed, Cancelled
            $table->string('resolution')->nullable(); // Replacement, Refund/Credit, No Replacement
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('replacement_order_number')->nullable();
            $table->unsignedInteger('replacement_received_quantity')->default(0);
            $table->timestamp('replacement_received_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defective_return_requests');
    }
};
