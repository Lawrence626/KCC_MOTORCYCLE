<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Product;
$products = Product::where('is_archived', false)->orderBy('category')->orderBy('name')->get();
echo 'total=' . $products->count() . PHP_EOL;
$warehouseProducts = $products->slice(0, 90)->values();
echo 'warehouseA=' . $warehouseProducts->count() . PHP_EOL;
for ($slot = 0; $slot < 9; $slot++) {
    $shelf = $warehouseProducts->slice($slot * 10, 10);
    echo 'slot' . ($slot + 1) . '=' . $shelf->count() . PHP_EOL;
}
