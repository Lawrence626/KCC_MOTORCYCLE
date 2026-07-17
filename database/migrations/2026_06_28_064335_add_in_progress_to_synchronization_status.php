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
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE synchronization_history RENAME COLUMN synchronization_status TO synchronization_status_old");
            DB::statement("ALTER TABLE synchronization_history ADD COLUMN synchronization_status VARCHAR(255) DEFAULT 'pending'");
            DB::statement("UPDATE synchronization_history SET synchronization_status = synchronization_status_old");
            DB::statement("ALTER TABLE synchronization_history DROP COLUMN synchronization_status_old");

            return;
        }

        DB::statement("ALTER TABLE synchronization_history MODIFY COLUMN synchronization_status ENUM('pending', 'in_progress', 'completed', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE synchronization_history RENAME COLUMN synchronization_status TO synchronization_status_old");
            DB::statement("ALTER TABLE synchronization_history ADD COLUMN synchronization_status VARCHAR(255) DEFAULT 'pending'");
            DB::statement("UPDATE synchronization_history SET synchronization_status = synchronization_status_old");
            DB::statement("ALTER TABLE synchronization_history DROP COLUMN synchronization_status_old");

            return;
        }

        DB::statement("ALTER TABLE synchronization_history MODIFY COLUMN synchronization_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'");
    }
};
