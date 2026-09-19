<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY sync_status ENUM('pending_sync', 'exported', 'imported', 'synchronized', 'duplicate', 'failed') NULL DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE purchase_orders MODIFY sync_status ENUM('pending_sync', 'exported', 'imported', 'synchronized', 'duplicate', 'failed') NOT NULL DEFAULT 'pending_sync'");
        }
    }
};
