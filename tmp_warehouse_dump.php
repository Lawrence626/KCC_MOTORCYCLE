<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Product;
$products = Product::where('is_archived', false)->orderBy('category')->orderBy('name')->get();
$warehouses = collect([
    ['name' => 'Warehouse A', 'code' => 'WH-A'],
    ['name' => 'Warehouse B', 'code' => 'WH-B'],
    ['name' => 'Warehouse C', 'code' => 'WH-C'],
])->map(function ($warehouse, $index) use ($products) {
    $warehouseProducts = $products->slice($index * 90, 90)->values();
    $locations = collect(range(0, 8))->map(function ($slotIndex) use ($warehouseProducts) {
        $shelfProducts = $warehouseProducts->slice($slotIndex * 10, 10)->map(function ($product) {
            return [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'qty' => $product->stock_quantity,
                'price' => (float) $product->unit_price,
                'category' => $product->category,
            ];
        })->toArray();
        return [
            'name' => 'Shelf '.($slotIndex + 1),
            'products' => $shelfProducts,
        ];
    })->toArray();
    return array_merge($warehouse, ['locations' => $locations]);
})->toArray();
echo json_encode($warehouses[0], JSON_PRETTY_PRINT);
