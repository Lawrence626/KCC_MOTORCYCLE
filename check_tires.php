<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::where('sku', 'like', '%TIRE%')
    ->orWhere('sku', 'like', '%BRAKE_MASTER%')
    ->get();

foreach($products as $p) {
    echo "SKU: {$p->sku} | Total Qty: {$p->stock_quantity}\n";
    $stocks = App\Models\ProductWarehouseStock::where('product_id', $p->id)->get();
    foreach($stocks as $s) {
        echo "  - {$s->warehouse}: {$s->quantity}\n";
    }
    $shelves = App\Models\ShopInventory::where('product_id', $p->id)->with('shopShelf')->get();
    foreach($shelves as $sh) {
        echo "  - Shelf: " . ($sh->shopShelf ? $sh->shopShelf->name : 'Unknown') . " : {$sh->quantity}\n";
    }
}
