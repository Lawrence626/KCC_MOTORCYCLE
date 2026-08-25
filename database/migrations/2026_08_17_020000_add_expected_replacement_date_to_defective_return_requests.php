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
        Schema::table('defective_return_requests', function (Blueprint $table) {
            $table->date('expected_replacement_date')->nullable()->after('replacement_order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defective_return_requests', function (Blueprint $table) {
            $table->dropColumn('expected_replacement_date');
        });
    }
};
