<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ShopInventory;
use App\Models\ShopShelf;

echo "Checking Shop Inventory data...\n\n";

// Check shop inventory count
$shopInventoryCount = ShopInventory::count();
echo "Total shop inventory entries: {$shopInventoryCount}\n";

// Check shop shelves
$shopShelves = ShopShelf::where('is_active', true)->get();
echo "Total active shop shelves: {$shopShelves->count()}\n\n";

// Check each shelf
foreach ($shopShelves as $shelf) {
    $inventoryCount = ShopInventory::where('shop_shelf_id', $shelf->id)->count();
    echo "Shelf {$shelf->name} (ID: {$shelf->id}): {$inventoryCount} products\n";
}

// Check if products have proper relationships
echo "\nChecking sample shop inventory entry...\n";
$sampleInventory = ShopInventory::with('product.productCatalog')->first();
if ($sampleInventory) {
    echo "Shop Inventory ID: {$sampleInventory->id}\n";
    echo "Shelf ID: {$sampleInventory->shop_shelf_id}\n";
    echo "Product ID: {$sampleInventory->product_id}\n";
    
    if ($sampleInventory->product) {
        echo "Product Name: {$sampleInventory->product->name}\n";
        if ($sampleInventory->product->productCatalog) {
            echo "Product Catalog exists\n";
            echo "Description: {$sampleInventory->product->productCatalog->product_description}\n";
            echo "Brand: {$sampleInventory->product->productCatalog->brand}\n";
        } else {
            echo "Product Catalog missing\n";
        }
    } else {
        echo "Product relationship missing\n";
    }
} else {
    echo "No shop inventory found\n";
}
