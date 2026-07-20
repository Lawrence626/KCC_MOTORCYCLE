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
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->string('size')->nullable()->after('brand');
            $table->string('color')->nullable()->after('size');
            $table->integer('stock_quantity')->default(0)->after('color');
            $table->integer('reorder_level')->default(10)->after('stock_quantity');
            $table->string('warehouse')->nullable()->after('reorder_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->dropColumn(['size', 'color', 'stock_quantity', 'reorder_level', 'warehouse']);
        });
    }
};
