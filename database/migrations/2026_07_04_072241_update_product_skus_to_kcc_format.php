<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing product SKUs to KCC- format
        $products = DB::table('products')->get();
        
        foreach ($products as $product) {
            // Skip if already has KCC- prefix
            if (strpos($product->sku, 'KCC-') === 0) {
                continue;
            }
            
            // Generate new SKU format
            $baseSKU = 'KCC-' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $product->name));
            $newSKU = $baseSKU;
            
            // Check if SKU exists and add unique identifier if needed
            $counter = 1;
            while (DB::table('products')->where('sku', $newSKU)->where('id', '!=', $product->id)->exists()) {
                $newSKU = $baseSKU . '-' . $counter;
                $counter++;
            }
            
            // Update the SKU
            DB::table('products')
                ->where('id', $product->id)
                ->update(['sku' => $newSKU]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily reversed as we don't have the original SKUs
        // You would need to restore from backup if needed
    }
};
