<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Sync warehouse data from product_catalog to products table based on SKU
        DB::statement("
            UPDATE products p
            LEFT JOIN product_catalog pc ON p.sku = pc.sku
            SET p.warehouse = COALESCE(pc.warehouse, 'Shop')
            WHERE p.warehouse IS NULL OR p.warehouse = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed as this is a one-time sync
    }
};
