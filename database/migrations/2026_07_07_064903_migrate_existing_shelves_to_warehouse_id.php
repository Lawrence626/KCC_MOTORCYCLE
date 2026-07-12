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
        // Map existing warehouse_index to warehouse_id
        // warehouse_index 0 -> Warehouse A (WH-A)
        // warehouse_index 1 -> Warehouse B (WH-B)
        // warehouse_index 2 -> Warehouse C (WH-C)
        
        $warehouseA = \App\Models\Warehouse::where('code', 'WH-A')->first();
        $warehouseB = \App\Models\Warehouse::where('code', 'WH-B')->first();
        $warehouseC = \App\Models\Warehouse::where('code', 'WH-C')->first();

        if ($warehouseA) {
            \DB::table('warehouse_shelves')
                ->where('warehouse_index', 0)
                ->update(['warehouse_id' => $warehouseA->id]);
        }

        if ($warehouseB) {
            \DB::table('warehouse_shelves')
                ->where('warehouse_index', 1)
                ->update(['warehouse_id' => $warehouseB->id]);
        }

        if ($warehouseC) {
            \DB::table('warehouse_shelves')
                ->where('warehouse_index', 2)
                ->update(['warehouse_id' => $warehouseC->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('warehouse_shelves')->update(['warehouse_id' => null]);
    }
};
