<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find products that have SHOP stock > 0 but are NOT in shop_inventory
$shopStocks = App\Models\ProductWarehouseStock::where('warehouse', 'SHOP')->where('quantity', '>', 0)->get();

$unallocatedProducts = [];
foreach($shopStocks as $stock) {
    $inventoryQty = App\Models\ShopInventory::where('product_id', $stock->product_id)->sum('quantity');
    if ($inventoryQty < $stock->quantity) {
        $product = App\Models\Product::find($stock->product_id);
        if ($product) {
            $unallocatedProducts[] = [
                'product_id' => $stock->product_id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unallocated' => $stock->quantity - $inventoryQty
            ];
        }
    }
}

echo "Found " . count($unallocatedProducts) . " products with unallocated SHOP stock.\n";

if (count($unallocatedProducts) > 0) {
    // Get a shelf to put them in, let's say "Shop Shelf 1" or similar
    $shelf = App\Models\ShopShelf::first();
    if (!$shelf) {
        $shelf = App\Models\ShopShelf::create([
            'name' => 'General Shelf',
            'location' => 'General Area',
            'capacity' => 1000,
            'is_active' => true
        ]);
    }
    
    foreach($unallocatedProducts as $item) {
        echo "Allocating {$item['unallocated']} of {$item['sku']} to {$shelf->name}\n";
        
        $inventory = App\Models\ShopInventory::firstOrNew([
            'shop_shelf_id' => $shelf->id,
            'product_id' => $item['product_id']
        ]);
        $inventory->quantity = ($inventory->quantity ?? 0) + $item['unallocated'];
        $inventory->save();
    }
    echo "Done allocating.\n";
}
