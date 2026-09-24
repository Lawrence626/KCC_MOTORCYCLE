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
        Schema::table('pos_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_transactions', 'amount_paid')) {
                $table->decimal('amount_paid', 12, 2)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('pos_transactions', 'change_amount')) {
                $table->decimal('change_amount', 12, 2)->nullable()->after('amount_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('pos_transactions', 'change_amount')) {
                $table->dropColumn('change_amount');
            }
            if (Schema::hasColumn('pos_transactions', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
        });
    }
};
