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
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Make token column nullable using raw SQL
        DB::statement("ALTER TABLE password_reset_tokens CHANGE COLUMN token token VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Make token column required again
        DB::statement("ALTER TABLE password_reset_tokens CHANGE COLUMN token token VARCHAR(255) NOT NULL");
    }
};
