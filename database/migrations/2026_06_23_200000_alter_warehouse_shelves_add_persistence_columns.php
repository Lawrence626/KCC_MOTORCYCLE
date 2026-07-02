<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warehouse_shelves', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouse_shelves', 'warehouse_index')) {
                $table->unsignedTinyInteger('warehouse_index')->default(0)->after('warehouse_code');
            }
            if (!Schema::hasColumn('warehouse_shelves', 'slot_index')) {
                $table->unsignedSmallInteger('slot_index')->default(0)->after('warehouse_index');
            }
            if (!Schema::hasColumn('warehouse_shelves', 'products')) {
                $table->json('products')->nullable()->after('name');
            }
            if (!Schema::hasColumn('warehouse_shelves', 'archived')) {
                $table->boolean('archived')->default(false)->after('products');
            }
        });

        DB::table('warehouse_shelves')
            ->update([
                'warehouse_index' => DB::raw("CASE warehouse_code WHEN 'WH-A' THEN 0 WHEN 'WH-B' THEN 1 WHEN 'WH-C' THEN 2 ELSE 0 END"),
                'slot_index' => DB::raw('sort_order'),
                'archived' => DB::raw('is_archived'),
                'products' => DB::raw('JSON_ARRAY()'),
            ]);
    }

    public function down()
    {
        Schema::table('warehouse_shelves', function (Blueprint $table) {
            if (Schema::hasColumn('warehouse_shelves', 'warehouse_index')) {
                $table->dropColumn('warehouse_index');
            }
            if (Schema::hasColumn('warehouse_shelves', 'slot_index')) {
                $table->dropColumn('slot_index');
            }
            if (Schema::hasColumn('warehouse_shelves', 'products')) {
                $table->dropColumn('products');
            }
            if (Schema::hasColumn('warehouse_shelves', 'archived')) {
                $table->dropColumn('archived');
            }
        });
    }
};
