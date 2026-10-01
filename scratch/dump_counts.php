<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $counts = [
        'users' => \App\Models\User::count(),
        'products' => \App\Models\Product::count(),
        'pos_transactions' => \App\Models\POSTransaction::count(),
        'purchase_orders' => \App\Models\PurchaseOrder::count(),
        'suppliers' => \App\Models\Supplier::count(),
        'warehouse_shelves' => \Illuminate\Support\Facades\DB::table('warehouse_shelves')->count(),
    ];
    echo json_encode($counts, JSON_PRETTY_PRINT);
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
