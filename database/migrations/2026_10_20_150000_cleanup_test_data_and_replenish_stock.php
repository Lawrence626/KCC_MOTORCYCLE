<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean POS Transactions and their movement history
        if (Schema::hasTable('pos_transactions')) {
            DB::table('pos_transactions')->delete();
            try {
                DB::statement("ALTER TABLE `pos_transactions` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('inventory_movements')) {
            DB::table('inventory_movements')->where('type', 'pos_sale')->delete();
        }

        if (Schema::hasTable('shop_inventory_history')) {
            DB::table('shop_inventory_history')->where('action_type', 'pos_sale')->delete();
        }

        // 2. Clean Purchase Orders and Purchase Order Items
        if (Schema::hasTable('purchase_order_items')) {
            DB::table('purchase_order_items')->delete();
            try {
                DB::statement("ALTER TABLE `purchase_order_items` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('purchase_orders')) {
            DB::table('purchase_orders')->delete();
            try {
                DB::statement("ALTER TABLE `purchase_orders` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        // 3. Clean DSS and Analytics generated test data
        if (Schema::hasTable('dss_recommendations')) {
            DB::table('dss_recommendations')->delete();
            try {
                DB::statement("ALTER TABLE `dss_recommendations` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('dead_stocks')) {
            DB::table('dead_stocks')->delete();
            try {
                DB::statement("ALTER TABLE `dead_stocks` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('inventory_notifications')) {
            DB::table('inventory_notifications')->delete();
            try {
                DB::statement("ALTER TABLE `inventory_notifications` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('reverse_logistics')) {
            DB::table('reverse_logistics')->delete();
            try {
                DB::statement("ALTER TABLE `reverse_logistics` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('replacements')) {
            DB::table('replacements')->delete();
            try {
                DB::statement("ALTER TABLE `replacements` AUTO_INCREMENT = 1;");
            } catch (\Throwable $e) {}
        }

        // 4. Replenish low stock / out of stock products to a healthy level (25 units)
        if (Schema::hasTable('products')) {
            DB::table('products')
                ->where('is_archived', false)
                ->where(function ($q) {
                    $q->whereColumn('stock_quantity', '<=', 'reorder_level')
                      ->orWhere('stock_quantity', '<', 25);
                })
                ->update([
                    'stock_quantity' => 25,
                    'updated_at' => now(),
                ]);
        }

        // Ensure shop inventory has display stock for POS
        if (Schema::hasTable('shop_inventory')) {
            DB::table('shop_inventory')
                ->where('quantity', '<', 10)
                ->update([
                    'quantity' => 10,
                    'updated_at' => now(),
                ]);
        }

        // Ensure warehouse stock has buffer stock
        if (Schema::hasTable('product_warehouse_stock')) {
            DB::table('product_warehouse_stock')
                ->where('quantity', '<', 15)
                ->update([
                    'quantity' => 15,
                    'updated_at' => now(),
                ]);
        }

        // 5. Clear application cache
        try {
            Cache::flush();
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for cleanup
    }
};
