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
        Schema::table('products', function (Blueprint $table) {
            $table->enum('disposal_status', ['None', 'Pending', 'Approved', 'Disposed'])->default('None')->after('is_archived');
            $table->date('disposal_date_identified')->nullable()->after('disposal_status');
            $table->date('disposal_date_disposed')->nullable()->after('disposal_date_identified');
            $table->string('disposal_reason')->nullable()->after('disposal_date_disposed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['disposal_status', 'disposal_date_identified', 'disposal_date_disposed', 'disposal_reason']);
        });
    }
};
