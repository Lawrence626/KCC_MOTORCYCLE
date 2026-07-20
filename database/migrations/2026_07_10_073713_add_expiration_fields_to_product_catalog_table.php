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
            if (!Schema::hasColumn('product_catalog', 'manufacturing_date')) {
                $table->date('manufacturing_date')->nullable()->after('warehouse');
            }
            if (!Schema::hasColumn('product_catalog', 'batch_lot_number')) {
                $table->string('batch_lot_number')->nullable()->after('manufacturing_date');
            }
            if (!Schema::hasColumn('product_catalog', 'expiration_date')) {
                $table->date('expiration_date')->nullable()->after('batch_lot_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->dropColumn(['manufacturing_date', 'batch_lot_number', 'expiration_date']);
        });
    }
};
