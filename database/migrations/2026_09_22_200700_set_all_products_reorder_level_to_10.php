<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set reorder_level = 10 for all products
        DB::table('products')
            ->where('reorder_level', '!=', 10)
            ->update(['reorder_level' => 10]);

        if (Schema::hasTable('product_catalog')) {
            DB::table('product_catalog')
                ->where('reorder_level', '!=', 10)
                ->update(['reorder_level' => 10]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback default
    }
};
