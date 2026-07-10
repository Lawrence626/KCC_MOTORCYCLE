<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShopShelf;
use App\Models\ShopInventory;
use App\Models\ShopInventoryHistory;
use App\Models\Product;

class PopulateShopInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing shop inventory to redistribute (delete in correct order)
        ShopInventoryHistory::query()->delete();
        ShopInventory::query()->delete();
        ShopShelf::query()->delete();

        // Get all products with stock
        $products = Product::where('stock_quantity', '>', 0)
            ->where('is_archived', false)
            ->get();

        $productsPerShelf = 10;
        $totalShelves = ceil($products->count() / $productsPerShelf);
        
        // Create multiple shop shelves
        for ($i = 1; $i <= $totalShelves; $i++) {
            $shelf = ShopShelf::create([
                'name' => 'Shelf ' . $i,
                'location' => 'Main Store - Section ' . ceil($i / 4),
                'capacity' => $productsPerShelf,
                'description' => 'Shop shelf ' . $i,
                'is_active' => true,
            ]);
        }

        // Distribute products across shelves (10 per shelf)
        $shelves = ShopShelf::all();
        $shelfIndex = 0;
        $productsOnCurrentShelf = 0;

        foreach ($products as $product) {
            $currentShelf = $shelves[$shelfIndex];
            
            // Add product to current shelf
            ShopInventory::create([
                'shop_shelf_id' => $currentShelf->id,
                'product_id' => $product->id,
                'quantity' => $product->stock_quantity,
            ]);

            // Log in history as initial transfer
            ShopInventoryHistory::create([
                'shop_shelf_id' => $currentShelf->id,
                'product_id' => $product->id,
                'action_type' => 'transfer_in',
                'quantity_change' => $product->stock_quantity,
                'source_type' => 'warehouse',
                'source_id' => null,
                'destination_type' => 'shop_shelf',
                'destination_id' => $currentShelf->id,
                'user_id' => null,
                'notes' => 'Initial migration from products table',
            ]);

            $productsOnCurrentShelf++;

            // Move to next shelf after 10 products
            if ($productsOnCurrentShelf >= $productsPerShelf) {
                $shelfIndex++;
                $productsOnCurrentShelf = 0;
            }
        }

        $this->command->info('Shop Inventory populated with ' . $products->count() . ' products across ' . $totalShelves . ' shelves.');
    }
}
