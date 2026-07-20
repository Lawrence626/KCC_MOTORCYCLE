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
        // Sync warehouse data from products to product_catalog table based on SKU
        DB::statement("
            UPDATE product_catalog pc
            LEFT JOIN products p ON pc.sku = p.sku
            SET pc.warehouse = COALESCE(p.warehouse, 'Shop')
            WHERE pc.warehouse IS NULL OR pc.warehouse = ''
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
