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
            $table->foreignId('replacement_purchase_order_id')->nullable()->after('replacement_order_number')->constrained('purchase_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defective_return_requests', function (Blueprint $table) {
            $table->dropForeign(['replacement_purchase_order_id']);
            $table->dropColumn('replacement_purchase_order_id');
        });
    }
};
