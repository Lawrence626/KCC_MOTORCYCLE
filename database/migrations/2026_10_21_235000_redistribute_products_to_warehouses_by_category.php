<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseShelf;
use App\Models\ProductWarehouseStock;

return new class extends Migration
{
    /**
     * Determine target warehouse by product details.
     */
    private function getTargetWarehouse(string $desc, string $cat, string $name): string
    {
        $text = strtoupper(trim($desc . ' ' . $cat . ' ' . $name));

        // Warehouse A: Heavy, Exterior, Body, Suspension, Exhaust, Wheels
        $patternsA = [
            'TIRE', 'TYP', 'MAGS', 'MAG', 'WHEEL', 'RIM',
            'PIPE', 'PIP', 'EXHAUST', 'MUFFLER',
            'FRONT SHOCK', 'MONOSHOCK', 'SHOCK', 'SHK', 'FSK', 'MSK',
            'SWING ARM', 'SWA', 'TIRE HUGGER', 'THG', 'MONORACK', 'MNF', 'FRAME',
            'RADIATOR COVER', 'RADIATOR CAP', 'RADIATOR', 'RAD', 'RDC', 'RCV',
            'FLAT SEAT', 'INDO SEAT', 'SEAT', 'FLS', 'IDS',
        ];

        // Warehouse B: Transmission, Engine, Drivetrain, Fuel System
        $patternsB = [
            'CVT HALF', 'CVT', 'CVH',
            'ENGINE SUPPORT', 'ENS', 'ENGINE', 'ENG',
            'CHAIN', 'SPROCKET', 'BEARING', 'BUSHING', 'GASKET',
            'PISTON', 'CYLINDER', 'CRANKSHAFT', 'CAMSHAFT', 'VALVE', 'ROCKER',
            'CARBURETOR', 'CARB', 'FUEL PUMP', 'INJECTOR', 'FUEL',
        ];

        // Warehouse C: Brakes, Controls, Handles, Mirrors, Electrical & Accessories
        $patternsC = [
            'BRAKE MASTER PAIR', 'BRAKE MASTER PUMP', 'BRAKE MASTER', 'BKM', 'BMP', 'BPP',
            'CALIPER', 'CAL', 'DISC', 'DSC', 'ROTOR',
            'CLUTCH PERCH', 'CLP', 'CLUTCH',
            'QUICK THROTTLE', 'THROTTLE', 'QTR',
            'LEVER', 'LVR',
            'FOOT REST', 'FTR',
            'SIDE MIRROR', 'MIRROR', 'SMI',
            'LIGHT', 'BULB', 'HORN', 'BATTERY', 'FUSE', 'SWITCH', 'CABLE',
        ];

        foreach ($patternsA as $p) {
            if (str_contains($text, $p)) return 'Warehouse A';
        }
        foreach ($patternsB as $p) {
            if (str_contains($text, $p)) return 'Warehouse B';
        }
        foreach ($patternsC as $p) {
            if (str_contains($text, $p)) return 'Warehouse C';
        }

        return 'Warehouse C';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('warehouses') || !Schema::hasTable('products')) {
            return;
        }

        // 1. Ensure Warehouses A, B, C exist and are active
        $warehousesConfig = [
            ['name' => 'Warehouse A', 'code' => 'WH-A'],
            ['name' => 'Warehouse B', 'code' => 'WH-B'],
            ['name' => 'Warehouse C', 'code' => 'WH-C'],
        ];

        $warehouseModels = [];
        foreach ($warehousesConfig as $cfg) {
            $wh = Warehouse::where('name', $cfg['name'])->first();
            if (!$wh) {
                $wh = Warehouse::create([
                    'name'      => $cfg['name'],
                    'code'      => $cfg['code'],
                    'is_active' => true,
                ]);
            } else {
                $wh->update(['is_active' => true, 'code' => $cfg['code']]);
            }
            $warehouseModels[$cfg['name']] = $wh;
        }

        // 2. Fetch all active products with catalogs
        $products = Product::where('is_archived', false)
            ->with('productCatalog')
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        // 3. Group products by target warehouse
        $grouped = [
            'Warehouse A' => [],
            'Warehouse B' => [],
            'Warehouse C' => [],
        ];

        foreach ($products as $p) {
            $catDesc = $p->productCatalog?->product_description ?? '';
            $catName = $p->productCatalog?->product_name ?? '';
            $pName   = $p->name ?? $p->product_name ?? '';
            $category = $p->category ?? '';

            // Check if expirable (oils, fluids, coolants, sealants) - belongs to Shop
            $isExpirable = in_array(strtoupper(trim($catDesc)), [
                'ENGINE OIL', 'BRAKE FLUID (BRAKE OIL)', 'GEAR OIL',
                'COOLANT / RADIATOR COOLANT', 'CVT CLEANER', 'TIRE SEALANT'
            ], true);

            if ($isExpirable) {
                // Keep expirable consumables in Shop
                if (Schema::hasTable('product_warehouse_stock')) {
                    ProductWarehouseStock::updateOrCreate(
                        ['product_id' => $p->id, 'warehouse' => 'Shop'],
                        ['quantity' => max(10, (int)($p->stock_quantity ?? 10))]
                    );
                }
                continue;
            }

            $targetWh = $this->getTargetWarehouse($catDesc, $category, $pName . ' ' . $catName);
            $grouped[$targetWh][] = $p;

            // Sync product_warehouse_stock
            if (Schema::hasTable('product_warehouse_stock')) {
                // Remove from wrong warehouses
                ProductWarehouseStock::where('product_id', $p->id)
                    ->where('warehouse', '!=', 'Shop')
                    ->where('warehouse', '!=', $targetWh)
                    ->delete();

                ProductWarehouseStock::updateOrCreate(
                    ['product_id' => $p->id, 'warehouse' => $targetWh],
                    ['quantity' => max(1, (int)($p->stock_quantity ?? 10))]
                );
            }

            // Sync product warehouse column
            if (Schema::hasColumn('products', 'warehouse')) {
                DB::table('products')->where('id', $p->id)->update(['warehouse' => $targetWh]);
            }
        }

        // 4. Populate warehouse_shelves table cleanly
        if (Schema::hasTable('warehouse_shelves')) {
            foreach ($grouped as $whName => $prods) {
                $wh = $warehouseModels[$whName] ?? null;
                if (!$wh) continue;

                // Delete existing non-archived shelves for this warehouse to recreate cleanly
                WarehouseShelf::where('warehouse_id', $wh->id)->where('archived', false)->delete();

                $chunks = array_chunk($prods, 10);
                $slotIdx = 0;

                foreach ($chunks as $chunk) {
                    $shelfItems = [];
                    foreach ($chunk as $p) {
                        $desc = $p->productCatalog?->product_description ?? ($p->description ?? $p->category ?? 'Unknown');
                        $brand = $p->productCatalog?->brand ?? ($p->brand ?? '');
                        $compat = $p->productCatalog?->product_name ?? ($p->compatibility ?? '');
                        $sku = $p->productCatalog?->sku ?? ($p->sku ?? '');
                        $name = $p->productCatalog?->product_name ?? ($p->name ?? $desc);

                        $shelfItems[] = [
                            'id'               => $p->id,
                            'sku'              => $sku,
                            'name'             => $name ?: $desc,
                            'description'      => $desc,
                            'brand'            => $brand,
                            'compatible_model' => $compat,
                            'qty'              => max(1, (int)($p->stock_quantity ?? 1)),
                            'stock_quantity'   => max(1, (int)($p->stock_quantity ?? 1)),
                            'price'            => (float)($p->unit_price ?? 0),
                            'category'         => $desc,
                            'product_name'     => $name ?: $desc,
                        ];
                    }

                    $shelfNumber = $slotIdx + 1;
                    $shelfData = [
                        'warehouse_id' => $wh->id,
                        'slot_index'   => $slotIdx,
                        'name'         => "Shelf {$shelfNumber}",
                        'products'     => $shelfItems,
                        'archived'     => false,
                    ];

                    if (Schema::hasColumn('warehouse_shelves', 'warehouse_index')) {
                        $shelfData['warehouse_index'] = max(0, $wh->id - 1);
                    }
                    if (Schema::hasColumn('warehouse_shelves', 'warehouse_code')) {
                        $shelfData['warehouse_code'] = $wh->code;
                    }
                    if (Schema::hasColumn('warehouse_shelves', 'sort_order')) {
                        $shelfData['sort_order'] = $slotIdx;
                    }
                    if (Schema::hasColumn('warehouse_shelves', 'capacity')) {
                        $shelfData['capacity'] = 10;
                    }

                    WarehouseShelf::create($shelfData);
                    $slotIdx++;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to leave untouched on rollback
    }
};
