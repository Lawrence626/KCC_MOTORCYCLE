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
        Schema::create('inventory_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku');
            $table->enum('notification_type', ['low_stock', 'out_of_stock']);
            $table->integer('current_stock')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->enum('status', ['unread', 'read', 'resolved'])->default('unread');
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Indexes for fast lookups
            $table->index('product_id');
            $table->index('status');
            $table->index(['product_id', 'status']);
            $table->index('notification_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_notifications');
    }
};
