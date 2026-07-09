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
        Schema::table('warehouse_shelves', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });

        // Migrate existing data - map warehouse_index to warehouse_id
        // This will be done in a seeder or manually after creating default warehouses
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_shelves', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });
    }
};
