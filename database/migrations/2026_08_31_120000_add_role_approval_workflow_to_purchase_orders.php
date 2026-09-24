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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('supplier_name');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('purchase_orders', 'created_by_role')) {
                $table->string('created_by_role')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('purchase_orders', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }

            if (!Schema::hasColumn('purchase_orders', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
                $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('purchase_orders', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }

            if (Schema::hasColumn('purchase_orders', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            $columnsToDrop = array_filter(['created_by_role', 'rejected_at', 'rejection_reason'], function ($col) {
                return Schema::hasColumn('purchase_orders', $col);
            });

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
