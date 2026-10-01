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
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique(['sku']);
            });
        } catch (\Throwable $e) {
            // Ignore if index doesn't exist
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_catalog_id')) {
                $table->foreignId('product_catalog_id')->nullable()->after('id')->constrained('product_catalog')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('products', 'batch_lot_number')) {
                $table->string('batch_lot_number')->nullable()->after('expiry_date');
            }
            
            if (!Schema::hasColumn('products', 'manufacturing_date')) {
                $table->date('manufacturing_date')->nullable()->after('batch_lot_number');
            }
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
