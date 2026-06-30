<?php
require __DIR__ . '/bootstrap/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\POSTransaction;
use App\Models\Product;

$transactions = POSTransaction::where('status', 'completed')->take(5)->get();
echo "Completed POS transactions: " . $transactions->count() . "\n\n";
foreach ($transactions as $transaction) {
    echo "Transaction ID: {$transaction->id}\n";
    echo "Completed At: {$transaction->completed_at}\n";
    echo "Items:\n";
    $items = $transaction->items;
    if (!is_array($items)) {
        var_export($items);
        echo "\n";
        continue;
    }
    foreach ($items as $item) {
        $productId = $item['id'] ?? null;
        echo "  item id: " . ($productId ?? 'null') . "\n";
        echo "    item name: " . ($item['name'] ?? 'null') . "\n";
        echo "    item category: " . ($item['category'] ?? 'null') . "\n";
        echo "    item quantity: " . (($item['quantity'] ?? $item['qty']) ?? 'null') . "\n";
        echo "    item price: " . (($item['unit_price'] ?? $item['price']) ?? 'null') . "\n";
        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                echo "    product record: id={$product->id}, name={$product->name}, product_name={$product->product_name}, category={$product->category}\n";
            } else {
                echo "    product record: not found\n";
            }
        }
    }
    echo "\n";
}
