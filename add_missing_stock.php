<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::all();
$shelf = App\Models\ShopShelf::firstOrCreate(
    ['name' => 'General Accessories'],
    ['location' => 'General Area', 'capacity' => 2000, 'is_active' => true]
);

$count = 0;
foreach($products as $p) {
    // If it has NO SHOP stock, or 0 SHOP stock, give it some!
    $shopStock = App\Models\ProductWarehouseStock::where('product_id', $p->id)
                    ->where('warehouse', 'SHOP')->first();
    
    if (!$shopStock || $shopStock->quantity == 0) {
        // Let's add 20 to SHOP and 10 to Warehouse A
        $qtyToAdd = 20;
        
        // Update product total
        $p->stock_quantity += $qtyToAdd + 10;
        $p->save();
        
        // Update SHOP stock
        App\Models\ProductWarehouseStock::updateOrCreate(
            ['product_id' => $p->id, 'warehouse' => 'SHOP'],
            ['quantity' => \Illuminate\Support\Facades\DB::raw("quantity + $qtyToAdd")]
        );
        
        // Update Warehouse A stock
        App\Models\ProductWarehouseStock::updateOrCreate(
            ['product_id' => $p->id, 'warehouse' => 'Warehouse A'],
            ['quantity' => \Illuminate\Support\Facades\DB::raw("quantity + 10")]
        );
        
        // Add to shelf
        $inventory = App\Models\ShopInventory::firstOrNew([
            'shop_shelf_id' => $shelf->id,
            'product_id' => $p->id
        ]);
        $inventory->quantity = ($inventory->quantity ?? 0) + $qtyToAdd;
        $inventory->save();
        
        echo "Added stock for {$p->sku}\n";
        $count++;
    }
}
echo "Added stock for $count products.\n";
