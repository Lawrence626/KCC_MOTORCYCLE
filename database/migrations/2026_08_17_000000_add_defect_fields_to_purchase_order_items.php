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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'defective_quantity')) {
                $table->unsignedInteger('defective_quantity')->default(0)->after('received_quantity');
            }
            if (!Schema::hasColumn('purchase_order_items', 'accepted_quantity')) {
                $table->unsignedInteger('accepted_quantity')->default(0)->after('defective_quantity');
            }
            if (!Schema::hasColumn('purchase_order_items', 'defect_reason')) {
                $table->string('defect_reason')->nullable()->after('accepted_quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('purchase_order_items', 'defective_quantity')) {
                $columns[] = 'defective_quantity';
            }
            if (Schema::hasColumn('purchase_order_items', 'accepted_quantity')) {
                $columns[] = 'accepted_quantity';
            }
            if (Schema::hasColumn('purchase_order_items', 'defect_reason')) {
                $columns[] = 'defect_reason';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
