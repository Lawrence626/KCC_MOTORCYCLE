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
        DB::statement("ALTER TABLE synchronization_history MODIFY COLUMN synchronization_status ENUM('pending', 'in_progress', 'completed', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE synchronization_history MODIFY COLUMN synchronization_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending'");
    }
};
