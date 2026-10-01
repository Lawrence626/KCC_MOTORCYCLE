<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ShopShelf;
use App\Models\ShopInventory;
use App\Models\ShopInventoryHistory;
use App\Models\ProductWarehouseStock;
use App\Models\Warehouse;
use App\Models\WarehouseShelf;

class LocationDistributionSeeder extends Seeder
{
    /**
     * Expirable product category keywords — these go primarily to SHOP.
     */
    protected array $expirableKeywords = [
        'engine oil', 'brake fluid', 'gear oil', 'coolant',
        'cvt cleaner', 'tire sealant', 'transmission oil',
        'fork oil', 'shock oil',
    ];

    /**
     * Fast-moving / small consumable keywords — more SHOP allocation.
     */
    protected array $fastMovingKeywords = [
        'brake pad', 'spark plug', 'air filter', 'oil filter',
        'clutch lever', 'brake lever', 'throttle', 'cable',
        'bulb', 'fuse', 'battery', 'grease', 'cleaner',
        'chain lube', 'contact cleaner',
    ];

    /**
     * Heavy / large / slow-moving keywords — mostly Warehouse A.
     */
    protected array $heavyKeywords = [
        'tire', 'rim', 'wheel', 'frame', 'engine', 'cylinder',
        'muffler', 'exhaust', 'fork', 'swing arm', 'shock',
        'radiator', 'tank', 'seat', 'fender', 'panel', 'cover',
    ];

    /**
     * Large / overflow keywords — mostly Warehouse B.
     */
    protected array $largeKeywords = [
        'chain', 'sprocket', 'bearing', 'bushing', 'gasket',
        'piston', 'crankshaft', 'camshaft', 'valve', 'rocker arm',
        'carb', 'carburetor', 'fuel pump', 'injector',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Starting Location Distribution Seeder...');

        // ─── 1. Ensure warehouses exist ──────────────────────────────────────
        $this->ensureWarehousesExist();

        // ─── 2. Clear existing distribution data ─────────────────────────────
        $this->command?->info('Clearing existing distribution data...');
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        ShopInventoryHistory::query()->delete();
        ShopInventory::query()->delete();
        ShopShelf::query()->delete();
        ProductWarehouseStock::query()->delete();
        WarehouseShelf::query()->delete();
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // ─── 3. Load all active products ─────────────────────────────────────
        $products = Product::where('is_archived', false)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        $this->command?->info("Found {$products->count()} active products to distribute.");

        // ─── 4. Distribute each product across locations ──────────────────────
        $distributionSummary = [
            'SHOP' => 0,
            'Warehouse A' => 0,
            'Warehouse B' => 0,
            'Warehouse C' => 0,
            'Warehouse D' => 0,
        ];

        foreach ($products as $product) {
            $distribution = $this->calculateDistribution($product);

            foreach ($distribution as $location => $qty) {
                if ($qty > 0) {
                    ProductWarehouseStock::create([
                        'product_id' => $product->id,
                        'warehouse'  => $location,
                        'quantity'   => $qty,
                    ]);
                    $distributionSummary[$location] += $qty;
                }
            }
        }

        $this->command?->info('Location distribution complete:');
        foreach ($distributionSummary as $location => $total) {
            $this->command?->line("  {$location}: {$total} units");
        }

        // ─── 5. Create shop shelves and populate shop_inventory ──────────────
        $this->command?->info('Populating Shop Inventory from SHOP allocations...');
        $this->populateShopInventory();

        // ─── 6. Create warehouse shelves and populate them ───────────────────
        $this->command?->info('Populating Warehouse Shelves...');
        $this->populateWarehouseShelves();

        $this->command?->info('Location Distribution Seeder completed successfully!');
    }

    /**
     * Determine the distribution ratios for a product based on its type.
     * Returns an array of [location => quantity].
     * Total quantities must ALWAYS equal product->stock_quantity.
     */
    protected function calculateDistribution(Product $product): array
    {
        $total = $product->stock_quantity;

        // Gather identifying text for matching
        $searchText = strtolower(implode(' ', [
            $product->name ?? '',
            $product->description ?? '',
            $product->product_name ?? '',
            $product->category ?? '',
            $product->brand ?? '',
        ]));

        // ── Determine distribution ratios based on product type ──
        if ($this->matchesKeywords($searchText, $this->expirableKeywords)) {
            // Expirable products: mostly SHOP, some reserve in warehouses
            $ratios = ['SHOP' => 0.40, 'Warehouse A' => 0.20, 'Warehouse B' => 0.15, 'Warehouse C' => 0.15, 'Warehouse D' => 0.10];
        } elseif ($this->matchesKeywords($searchText, $this->fastMovingKeywords)) {
            // Fast-moving consumables: more in SHOP
            $ratios = ['SHOP' => 0.30, 'Warehouse A' => 0.25, 'Warehouse B' => 0.20, 'Warehouse C' => 0.15, 'Warehouse D' => 0.10];
        } elseif ($this->matchesKeywords($searchText, $this->heavyKeywords)) {
            // Heavy / large items: mostly Warehouse A
            $ratios = ['SHOP' => 0.10, 'Warehouse A' => 0.40, 'Warehouse B' => 0.25, 'Warehouse C' => 0.15, 'Warehouse D' => 0.10];
        } elseif ($this->matchesKeywords($searchText, $this->largeKeywords)) {
            // Large items / overflow: mostly Warehouse B
            $ratios = ['SHOP' => 0.10, 'Warehouse A' => 0.20, 'Warehouse B' => 0.35, 'Warehouse C' => 0.20, 'Warehouse D' => 0.15];
        } else {
            // Default: balanced with slight SHOP preference
            $ratios = ['SHOP' => 0.20, 'Warehouse A' => 0.25, 'Warehouse B' => 0.25, 'Warehouse C' => 0.15, 'Warehouse D' => 0.15];
        }

        return $this->applyRatios($total, $ratios);
    }

    /**
     * Check if the search text matches any of the given keywords.
     */
    protected function matchesKeywords(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Apply ratios to total quantity and ensure they sum exactly to total.
     * Uses floor for each location, then assigns remainder to SHOP.
     */
    protected function applyRatios(int $total, array $ratios): array
    {
        if ($total <= 0) {
            return array_fill_keys(array_keys($ratios), 0);
        }

        // For very small quantities, ensure at least 1 in SHOP
        if ($total === 1) {
            return ['SHOP' => 1, 'Warehouse A' => 0, 'Warehouse B' => 0, 'Warehouse C' => 0, 'Warehouse D' => 0];
        }

        if ($total <= 4) {
            // Distribute minimally: at least 1 SHOP, rest to WH-A
            $shopQty = 1;
            $remainder = $total - $shopQty;
            return [
                'SHOP'        => $shopQty,
                'Warehouse A' => $remainder,
                'Warehouse B' => 0,
                'Warehouse C' => 0,
                'Warehouse D' => 0,
            ];
        }

        // Normal distribution
        $distribution = [];
        $allocated = 0;

        foreach ($ratios as $location => $ratio) {
            if ($location === 'SHOP') {
                continue; // Handle SHOP last to absorb remainder
            }
            $qty = (int) floor($total * $ratio);
            // Ensure at least 1 unit per non-shop location if ratio > 0 and total allows
            $qty = max(1, $qty);
            $distribution[$location] = $qty;
            $allocated += $qty;
        }

        // SHOP gets the remainder (may be slightly more or less than ratio)
        $shopQty = max(1, $total - $allocated);

        // Safety check: if over-allocated, reduce WH-D then WH-C then WH-B then WH-A
        if ($allocated >= $total) {
            $shopQty = 1;
            $excess = $allocated - ($total - $shopQty);
            foreach (['Warehouse D', 'Warehouse C', 'Warehouse B', 'Warehouse A'] as $loc) {
                if ($excess <= 0) break;
                $reduce = min($distribution[$loc] - 1, $excess);
                $distribution[$loc] -= $reduce;
                $excess -= $reduce;
            }
        }

        $distribution['SHOP'] = $shopQty;

        // Final safety: clamp negatives to 0
        foreach ($distribution as $loc => $qty) {
            $distribution[$loc] = max(0, $qty);
        }

        return $distribution;
    }

    /**
     * Ensure the four warehouses exist in the database.
     */
    protected function ensureWarehousesExist(): void
    {
        $defaultWarehouses = [
            ['name' => 'Warehouse A', 'code' => 'WH-A'],
            ['name' => 'Warehouse B', 'code' => 'WH-B'],
            ['name' => 'Warehouse C', 'code' => 'WH-C'],
            ['name' => 'Warehouse D', 'code' => 'WH-D'],
        ];

        foreach ($defaultWarehouses as $wh) {
            Warehouse::firstOrCreate(
                ['code' => $wh['code']],
                ['name' => $wh['name'], 'is_active' => true]
            );
        }
    }

    /**
     * Create shop shelves and populate shop_inventory from product_warehouse_stock (SHOP location).
     */
    protected function populateShopInventory(): void
    {
        // Get all SHOP allocations
        $shopStocks = ProductWarehouseStock::where('warehouse', 'SHOP')
            ->where('quantity', '>', 0)
            ->get();

        $productsPerShelf = 10;
        $totalProducts = $shopStocks->count();
        $totalShelves = max(1, ceil($totalProducts / $productsPerShelf));

        // Create shop shelves
        $shelves = [];
        for ($i = 1; $i <= $totalShelves; $i++) {
            $section = ceil($i / 4);
            $shelves[] = ShopShelf::create([
                'name'        => 'Shop Shelf ' . $i,
                'location'    => 'Main Store - Section ' . $section,
                'capacity'    => $productsPerShelf,
                'description' => 'Shop shelf ' . $i,
                'is_active'   => true,
            ]);
        }

        // Distribute products across shelves
        $shelfIndex = 0;
        $productsOnCurrentShelf = 0;

        foreach ($shopStocks as $stock) {
            $currentShelf = $shelves[$shelfIndex];

            ShopInventory::create([
                'shop_shelf_id' => $currentShelf->id,
                'product_id'    => $stock->product_id,
                'quantity'      => $stock->quantity,
            ]);

            ShopInventoryHistory::create([
                'shop_shelf_id'    => $currentShelf->id,
                'product_id'       => $stock->product_id,
                'action_type'      => 'transfer_in',
                'quantity_change'  => $stock->quantity,
                'source_type'      => 'warehouse',
                'source_id'        => null,
                'destination_type' => 'shop_shelf',
                'destination_id'   => $currentShelf->id,
                'user_id'          => null,
                'notes'            => 'Initial location distribution — assigned to SHOP',
            ]);

            $productsOnCurrentShelf++;

            if ($productsOnCurrentShelf >= $productsPerShelf) {
                $shelfIndex++;
                $productsOnCurrentShelf = 0;
            }
        }

        $this->command?->info("  Created {$totalShelves} shop shelves with {$totalProducts} product allocations.");
    }

    /**
     * Create warehouse shelves and populate them from product_warehouse_stock.
     */
    protected function populateWarehouseShelves(): void
    {
        $warehouses = Warehouse::active()->where('name', '!=', 'SHOP')->orderBy('name')->get();

        foreach ($warehouses as $warehouse) {
            // Get all products allocated to this warehouse
            $warehouseStocks = ProductWarehouseStock::where('warehouse', $warehouse->name)
                ->where('quantity', '>', 0)
                ->with('product')
                ->get();

            if ($warehouseStocks->isEmpty()) {
                $this->command?->line("  {$warehouse->name}: No products to distribute.");
                continue;
            }

            // Chunk into shelves of 10 products each
            $chunks = $warehouseStocks->chunk(10);
            $slotIndex = 0;

            foreach ($chunks as $chunk) {
                $shelfProducts = $chunk->map(function ($stock) {
                    $product = $stock->product;
                    return [
                        'id'          => $product->id,
                        'sku'         => $product->sku ?? '',
                        'name'        => $product->name ?? 'Unknown Product',
                        'description' => $product->description ?? $product->name ?? '',
                        'brand'       => $product->brand ?? '',
                        'qty'         => $stock->quantity,
                        'price'       => $product->unit_price ?? 0,
                    ];
                })->values()->toArray();

                WarehouseShelf::create([
                    'warehouse_id'    => $warehouse->id,
                    'warehouse_code'  => $warehouse->code,
                    'warehouse_index' => $warehouse->id - 1, // 0-based index
                    'slot_index'      => $slotIndex,
                    'sort_order'      => $slotIndex,
                    'name'            => $warehouse->name . ' - Shelf ' . ($slotIndex + 1),
                    'products'        => $shelfProducts,
                    'archived'        => false,
                ]);

                $slotIndex++;
            }

            $this->command?->info("  {$warehouse->name}: {$warehouseStocks->count()} products across {$slotIndex} shelves.");
        }
    }
}
