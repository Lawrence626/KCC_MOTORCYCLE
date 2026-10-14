<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('warehouses')) {
            $duplicateShop = DB::table('warehouses')->where('name', 'SHOP')->first();
            $mainShop = DB::table('warehouses')->where('name', 'Shop')->first();

            if ($duplicateShop) {
                if (!$mainShop) {
                    DB::table('warehouses')->where('id', $duplicateShop->id)->update([
                        'name' => 'Shop',
                    ]);
                    $mainShop = DB::table('warehouses')->where('id', $duplicateShop->id)->first();
                } else {
                    if (Schema::hasTable('warehouse_shelves')) {
                        DB::table('warehouse_shelves')
                            ->where('warehouse_id', $duplicateShop->id)
                            ->update([
                                'warehouse_id' => $mainShop->id,
                                'warehouse_code' => $mainShop->code ?? 'WH-SHOP',
                            ]);
                    }
                    DB::table('warehouses')->where('id', $duplicateShop->id)->delete();
                }
            }
        }

        if (Schema::hasTable('warehouse_shelves')) {
            $shelves = DB::table('warehouse_shelves')->where('name', 'like', 'SHOP - %')->get();
            foreach ($shelves as $shelf) {
                $newName = 'Shop - ' . substr($shelf->name, 7);
                DB::table('warehouse_shelves')->where('id', $shelf->id)->update([
                    'name' => $newName,
                ]);
            }
        }

        if (Schema::hasTable('product_warehouse_stock')) {
            $shopStocks = DB::table('product_warehouse_stock')->where('warehouse', 'SHOP')->get();
            foreach ($shopStocks as $stock) {
                $existing = DB::table('product_warehouse_stock')
                    ->where('product_id', $stock->product_id)
                    ->where('warehouse', 'Shop')
                    ->first();

                if ($existing) {
                    DB::table('product_warehouse_stock')
                        ->where('id', $existing->id)
                        ->update([
                            'quantity' => (int) $existing->quantity + (int) $stock->quantity,
                            'updated_at' => now(),
                        ]);
                    DB::table('product_warehouse_stock')->where('id', $stock->id)->delete();
                } else {
                    DB::table('product_warehouse_stock')
                        ->where('id', $stock->id)
                        ->update([
                            'warehouse' => 'Shop',
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        if (Schema::hasTable('products')) {
            DB::table('products')->where('warehouse', 'SHOP')->update([
                'warehouse' => 'Shop',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as SHOP was consolidated to Shop
    }
};
