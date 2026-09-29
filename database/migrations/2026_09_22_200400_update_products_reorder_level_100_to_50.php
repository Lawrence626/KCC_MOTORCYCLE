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
        // Update all products with reorder_level = 100 to reorder_level = 50
        DB::table('products')
            ->where('reorder_level', 100)
            ->update(['reorder_level' => 50]);

        if (Schema::hasTable('product_catalog')) {
            DB::table('product_catalog')
                ->where('reorder_level', 100)
                ->update(['reorder_level' => 50]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')
            ->where('reorder_level', 50)
            ->update(['reorder_level' => 100]);

        if (Schema::hasTable('product_catalog')) {
            DB::table('product_catalog')
                ->where('reorder_level', 50)
                ->update(['reorder_level' => 100]);
        }
    }
};
