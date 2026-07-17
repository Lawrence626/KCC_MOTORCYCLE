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
        // Add is_expirable flag to product_descriptions
        Schema::table('product_descriptions', function (Blueprint $table) {
            $table->boolean('is_expirable')->default(false)->after('is_active');
        });

        // Add expiry tracking fields to product_catalog
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->date('manufacturing_date')->nullable()->after('warehouse');
            $table->string('batch_lot_number')->nullable()->after('manufacturing_date');
            $table->date('expiry_date')->nullable()->after('batch_lot_number');
        });
    }

    public function down(): void
    {
        Schema::table('product_descriptions', function (Blueprint $table) {
            $table->dropColumn('is_expirable');
        });
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->dropColumn(['manufacturing_date', 'batch_lot_number', 'expiry_date']);
        });
    }
};
