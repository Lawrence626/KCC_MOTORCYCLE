<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_to_supplier_at')) {
                $table->timestamp('sent_to_supplier_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('purchase_orders', 'in_transit_at')) {
                $table->timestamp('in_transit_at')->nullable()->after('sent_to_supplier_at');
            }
            if (!Schema::hasColumn('purchase_orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('in_transit_at');
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'received_quantity')) {
                $table->unsignedInteger('received_quantity')->default(0)->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'sent_to_supplier_at', 'in_transit_at', 'completed_at']);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('received_quantity');
        });
    }
};
