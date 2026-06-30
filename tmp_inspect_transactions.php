<?php
require __DIR__ . '/vendor/autoload.php';
use App\Models\POSTransaction;
use App\Models\Product;

$transactions = POSTransaction::where('status','completed')->take(5)->get();
echo "Completed POS transactions: " . $transactions->count() . "\n\n";
foreach ($transactions as $transaction) {
    echo "Transaction ID: {$transaction->id}\n";
    echo "Completed At: {$transaction->completed_at}\n";
    echo "Items: \n";
    $items = $transaction->items;
    if (!is_array($items)) {
        var_export($items);
        echo "\n";
        continue;
    }
    foreach ($items as $item) {
        echo "  item id: " . ($item['id'] ?? 'null') . "\n";
        echo "    item name: " . ($item['name'] ?? 'null') . "\n";
        echo "    item category: " . ($item['category'] ?? 'null') . "\n";
        echo "    item qty: " . ($item['qty'] ?? $item['quantity'] ?? 'null') . "\n";
        echo "    item price: " . ($item['price'] ?? $item['unit_price'] ?? 'null') . "\n";
        if (isset($item['id'])) {
            $product = Product::find($item['id']);
            echo "    product record: ";
            if ($product) {
                echo "id={$product->id}, name={$product->name}, product_name={$product->product_name}, category={$product->category}\n";
            } else {
                echo "not found\n";
            }
        }
    }
    echo "\n";
}
?>