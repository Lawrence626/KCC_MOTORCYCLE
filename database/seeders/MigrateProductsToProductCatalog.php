<?php

namespace Database\Seeders;

use App\Models\MotorcycleModel;
use App\Models\Product;
use App\Models\ProductCatalog;
use App\Models\ProductDescription;
use Illuminate\Database\Seeder;

class MigrateProductsToProductCatalog extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products from the old Product model
        $products = Product::where('is_archived', false)->get();
        
        // Get all product descriptions
        $productDescriptions = ProductDescription::active()->get()->keyBy('name');
        
        $migratedCount = 0;
        $skippedCount = 0;
        
        foreach ($products as $product) {
            // Check if product already exists in ProductCatalog by SKU
            $existing = ProductCatalog::where('sku', $product->sku)->first();
            if ($existing) {
                $skippedCount++;
                continue;
            }
            
            // Skip products without brand (required field)
            if (empty($product->brand)) {
                $skippedCount++;
                continue;
            }
            
            // Map category to product_description
            $productDescription = $this->mapCategoryToProductDescription($product->category, $productDescriptions);
            
            if (!$productDescription) {
                // If no matching product description, use the category as-is
                $productDescription = $product->category;
            }
            
            // Create new ProductCatalog entry
            ProductCatalog::create([
                'product_name' => $product->name,
                'brand' => $product->brand,
                'product_description' => $productDescription,
                'sku' => $product->sku,
                'description' => $product->description,
                'status' => $product->is_active ? 'Active' : 'Inactive',
            ]);
            
            $migratedCount++;
        }
        
        echo "Migrated {$migratedCount} products to ProductCatalog\n";
        echo "Skipped {$skippedCount} products (already exist)\n";
    }
    
    private function mapCategoryToProductDescription($category, $productDescriptions)
    {
        // Mapping of old categories to new product descriptions
        $mapping = [
            'Engine Parts' => 'ENGINE SUPPORT',
            'Brake System' => 'SHOCK',
            'Electrical' => 'SIDE MIRROR',
            'Suspension' => 'SHOCK',
            'Transmission' => 'ENGINE SUPPORT',
            'Fuel System' => 'PIPE',
            'Cooling System' => 'ENGINE SUPPORT',
            'Lubricants' => 'TIRE',
            'Accessories' => 'SIDE MIRROR',
            'Body Parts' => 'TIRE HUGGER',
            'Tires' => 'TIRE',
            'Bearings' => 'ENGINE SUPPORT',
            'Chains and Sprockets' => 'ENGINE SUPPORT',
            'Lights' => 'SIDE MIRROR',
            'Others' => 'MONORACK FRAME',
        ];
        
        // Return mapped product description if it exists
        if (isset($mapping[$category]) && $productDescriptions->has($mapping[$category])) {
            return $mapping[$category];
        }
        
        // Try to find a product description that contains the category name
        foreach ($productDescriptions as $description) {
            if (stripos($description->name, $category) !== false || stripos($category, $description->name) !== false) {
                return $description->name;
            }
        }
        
        // Default to PIPE if no match found
        return 'PIPE';
    }
}
