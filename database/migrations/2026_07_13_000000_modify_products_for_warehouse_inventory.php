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
        Schema::table('products', function (Blueprint $table) {
            // Drop the unique constraint on sku
            $table->dropUnique(['sku']);
            
            // Add foreign key to product_catalog
            $table->foreignId('product_catalog_id')->nullable()->after('id')->constrained('product_catalog')->onDelete('cascade');
            
            // Add unique constraint on (sku, warehouse) combination
            $table->unique(['sku', 'warehouse']);
            
            // Add batch number for expirable products
            $table->string('batch_lot_number')->nullable()->after('expiry_date');
            
            // Add manufacturing date for expirable products
            $table->date('manufacturing_date')->nullable()->after('batch_lot_number');
            
            // Add indexes for better performance
            $table->index('warehouse');
            $table->index('product_catalog_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the new unique constraint on (sku, warehouse)
            $table->dropUnique(['sku', 'warehouse']);
            
            // Drop the foreign key and column
            $table->dropForeign(['product_catalog_id']);
            $table->dropColumn('product_catalog_id');
            
            // Drop batch and manufacturing date columns
            $table->dropColumn(['batch_lot_number', 'manufacturing_date']);
            
            // Drop warehouse index
            $table->dropIndex(['warehouse']);
            
            // Restore unique constraint on sku
            $table->unique('sku');
        });
    }
};
