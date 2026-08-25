<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('UPDATE purchase_orders SET estimated_delivery_date = expected_delivery_date WHERE estimated_delivery_date IS NULL AND expected_delivery_date IS NOT NULL');
        DB::statement('UPDATE purchase_orders SET expected_delivery_date = estimated_delivery_date WHERE expected_delivery_date IS NULL AND estimated_delivery_date IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for data synchronization
    }
};
