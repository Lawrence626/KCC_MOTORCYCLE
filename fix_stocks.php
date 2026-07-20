<?php require "vendor/autoload.php"; $app = require_once "bootstrap/app.php"; $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); 
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use Illuminate\Support\Facades\DB;

$products = Product::with("warehouseStocks")->get();

foreach($products as $p) {
    $hasExpiry = false;
    // Check if it has an expiry date or expiry_status != "non_expiring"
    // Wait, expiry_status is non_expiring, expiring, expired. Or category is Oil, etc.
    if ($p->expiry_date !== null || $p->expiry_status !== "non_expiring") {
        $hasExpiry = true;
    }
    
    // Some products like "Oil" are inherently expiring.
    // Let us just rely on $hasExpiry based on DB fields.
    
    $totalStock = $p->stock_quantity;
    
    if ($hasExpiry) {
        // Expiring products: 100% in SHOP. Delete others or set to 0.
        // Get or create SHOP stock
        $shopStock = $p->warehouseStocks()->where("warehouse", "SHOP")->first();
        if (!$shopStock) {
            $shopStock = new ProductWarehouseStock();
            $shopStock->product_id = $p->id;
            $shopStock->warehouse = "SHOP";
        }
        $shopStock->quantity = $totalStock;
        $shopStock->save();
        
        // Set others to 0
        ProductWarehouseStock::where("product_id", $p->id)->where("warehouse", "!=", "SHOP")->update(["quantity" => 0]);
        echo "Expiring {$p->sku}: all {$totalStock} in SHOP.\n";
    } else {
        // Non-expiring products: in ONE warehouse and SHOP.
        // How much in shop? Let us say 20% in shop, 80% in ONE warehouse.
        // Or if we already have a shop qty, keep it. Sum all warehouse qty and put into ONE warehouse.
        $shopStock = $p->warehouseStocks()->where("warehouse", "SHOP")->first();
        $shopQty = $shopStock ? $shopStock->quantity : 0;
        
        $totalWarehouseQty = $p->warehouseStocks()->where("warehouse", "!=", "SHOP")->sum("quantity");
        
        // If they had 0 total warehouse qty, but total stock > shopQty
        if ($shopQty + $totalWarehouseQty < $totalStock) {
            $totalWarehouseQty = $totalStock - $shopQty;
        }
        
        if ($totalWarehouseQty > 0) {
            // Pick ONE warehouse that currently has stock, or randomly A, B, C
            $activeWarehouses = $p->warehouseStocks()->where("warehouse", "!=", "SHOP")->where("quantity", ">", 0)->pluck("warehouse")->toArray();
            
            $targetWarehouse = count($activeWarehouses) > 0 ? $activeWarehouses[0] : "Warehouse A";
            
            // Set the target warehouse to the sum
            $whStock = $p->warehouseStocks()->where("warehouse", $targetWarehouse)->first();
            if (!$whStock) {
                $whStock = new ProductWarehouseStock();
                $whStock->product_id = $p->id;
                $whStock->warehouse = $targetWarehouse;
            }
            $whStock->quantity = $totalWarehouseQty;
            $whStock->save();
            
            // Set all OTHER warehouses to 0
            ProductWarehouseStock::where("product_id", $p->id)
                ->where("warehouse", "!=", "SHOP")
                ->where("warehouse", "!=", $targetWarehouse)
                ->update(["quantity" => 0]);
                
            echo "Non-expiring {$p->sku}: Shop= {$shopQty}, {$targetWarehouse} = {$totalWarehouseQty}\n";
        }
    }
}
echo "Done!\n";

