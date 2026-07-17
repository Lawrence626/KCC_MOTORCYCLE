<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::with('productCatalog')->get();
$count = 0;
foreach($products as $p) {
    if ($p->productCatalog && $p->productCatalog->product_description === 'General') {
        $skuParts = explode('_', $p->sku);
        // Find a better description from SKU
        // E.g. KCC_CALIPER_RCB_ES_001 -> CALIPER
        $newDesc = 'General';
        if (strpos($p->sku, 'CALIPER') !== false) $newDesc = 'Caliper';
        elseif (strpos($p->sku, 'BRAKE_SHOE') !== false) $newDesc = 'Brake Shoe';
        elseif (strpos($p->sku, 'BRAKE_MASTER') !== false) $newDesc = 'Brake Master';
        elseif (strpos($p->sku, 'TIRE') !== false) $newDesc = 'Tire';
        elseif (strpos($p->sku, 'DISK') !== false) $newDesc = 'Disk';
        elseif (strpos($p->sku, 'BATTERY') !== false) $newDesc = 'Battery';
        elseif (strpos($p->sku, 'HELMET') !== false) $newDesc = 'Helmet';
        
        if ($newDesc !== 'General') {
            echo "Updating SKU: {$p->sku} from General to {$newDesc}\n";
            $p->productCatalog->product_description = $newDesc;
            $p->productCatalog->save();
            $count++;
        }
    }
}
echo "Updated $count product catalogs.\n";
