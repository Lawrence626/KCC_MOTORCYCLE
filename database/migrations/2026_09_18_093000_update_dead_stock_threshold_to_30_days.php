<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update dead stock threshold from 90 to 30 days.
     */
    public function up(): void
    {
        DB::table('dss_settings')
            ->where('key', 'dead_stock_threshold_days')
            ->update([
                'value' => '30',
                'description' => 'Number of days without sale to classify as dead stock (default: 30)',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('dss_settings')
            ->where('key', 'dead_stock_threshold_days')
            ->update([
                'value' => '90',
                'description' => 'Number of days without sale to classify as dead stock (default: 90)',
            ]);
    }
};
