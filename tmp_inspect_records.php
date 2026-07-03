<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\POSTransaction;
use App\Models\Product;

$product = Product::first();
echo "First product record:\n";
echo "  id=" . $product->id . "\n";
echo "  name=" . $product->name . "\n";
echo "  product_name=" . $product->product_name . "\n";
echo "  category=" . $product->category . "\n\n";

$tx = POSTransaction::where('status','completed')->first();
if (!$tx) {
    echo "No completed POS transaction found.\n";
    exit(0);
}

echo "Completed transaction id={$tx->id}\n";
echo "Items: \n";
var_export($tx->items);
echo "\n\n";
foreach ($tx->items as $item) {
    echo "item id=" . ($item['id'] ?? 'null') . " name=" . ($item['name'] ?? 'null') . " category=" . ($item['category'] ?? 'null') . " qty=" . (($item['quantity'] ?? $item['qty']) ?? 'null') . " price=" . (($item['unit_price'] ?? $item['price']) ?? 'null') . "\n";
    if (isset($item['id'])) {
        $product = Product::find($item['id']);
        if ($product) {
            echo "  product lookup: id={$product->id}, name={$product->name}, product_name={$product->product_name}, category={$product->category}\n";
        } else {
            echo "  product lookup: not found\n";
        }
    }
}
