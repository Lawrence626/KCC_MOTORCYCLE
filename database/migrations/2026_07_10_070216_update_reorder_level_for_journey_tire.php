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
        // Set reorder_level = 100 for all JOURNEY brand TIRE products
        DB::table('product_catalog')
            ->where('product_description', 'TIRE')
            ->where('brand', 'JOURNEY')
            ->update(['reorder_level' => 100]);
    }

    public function down(): void
    {
        DB::table('product_catalog')
            ->where('product_description', 'TIRE')
            ->where('brand', 'JOURNEY')
            ->update(['reorder_level' => 10]);
    }
};
