<?php

namespace App\Http\Controllers;

use App\Models\ProductDescription;
use Illuminate\Http\Request;

class ProductDescriptionController extends Controller
{
    /**
     * Get all product descriptions for POS filtering.
     */
    public function index()
    {
        $descriptions = ProductDescription::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($descriptions);
    }

    /**
     * Store a newly created product description.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_descriptions,name',
            'brand' => 'required|string|max:255',
        ]);

        // Check if product description already exists
        $existingDescription = ProductDescription::where('name', $request->name)->first();
        
        if ($existingDescription) {
            // Add brand to existing description if not already present
            $brands = $existingDescription->brands ?? [];
            if (!in_array($request->brand, $brands)) {
                $brands[] = $request->brand;
                $existingDescription->brands = $brands;
                $existingDescription->save();
            }
            
            return response()->json([
                'success' => true,
                'product_description' => $existingDescription,
                'message' => 'Brand added to existing product description'
            ]);
        }

        // Generate unique SKU prefix
        $basePrefix = strtoupper(str_replace(' ', '_', substr($request->name, 0, 15)));
        $skuPrefix = $basePrefix;
        $counter = 1;
        
        while (ProductDescription::where('sku_prefix', $skuPrefix)->exists()) {
            $skuPrefix = $basePrefix . '_' . $counter;
            $counter++;
        }

        // Create new product description
        $productDescription = ProductDescription::create([
            'name' => $request->name,
            'sku_prefix' => $skuPrefix,
            'brands' => [$request->brand],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'product_description' => $productDescription,
            'message' => 'Product description created successfully'
        ]);
    }
}
