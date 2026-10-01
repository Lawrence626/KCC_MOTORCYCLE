<?php

namespace App\Http\Controllers;

use App\Models\ShopShelf;
use App\Models\ShopInventory;
use App\Models\ShopInventoryHistory;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShopInventoryController extends Controller
{
    /**
     * Display shop inventory management page
     */
    public function index()
    {
        // Show all products in shop inventory (not filtered by SHOP stock)
        $shelves = ShopShelf::with(['shopInventory.product.productCatalog'])
            ->where('is_active', true)
            ->get()
            ->map(function ($shelf) {
                // Ensure the relationship is loaded as a collection
                $shelf->shop_inventory = $shelf->shopInventory;
                $shelf->occupied = $shelf->shop_inventory->count();
                $shelf->available = max(0, $shelf->capacity - $shelf->occupied);
                return $shelf;
            });

        // Calculate totals - count distinct products
        $totalProducts = ShopInventory::whereHas('shopShelf', function($q) {
            $q->where('is_active', true);
        })->count();
        $totalShelves = $shelves->count();

        return view('shop_inventory.index', compact('shelves', 'totalProducts', 'totalShelves'));
    }

    /**
     * Create a new shop shelf
     */
    public function createShelf(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'products' => 'nullable|array',
            'products.*.name' => 'required|string|max:255',
            'products.*.sku' => 'required|string|max:255',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ]);

        // Check if section has reached max capacity (4 shelves per section)
        if (!empty($validated['location'])) {
            $maxShelvesPerSection = 4;
            $existingCount = ShopShelf::where('location', $validated['location'])
                ->where('is_active', true)
                ->count();

            if ($existingCount >= $maxShelvesPerSection) {
                return response()->json([
                    'success' => false,
                    'message' => "Section '{$validated['location']}' has reached maximum capacity ({$maxShelvesPerSection} shelves). Please select a different section.",
                ], 422);
            }
        }

        $shelf = ShopShelf::create([
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'capacity' => 10,
            'description' => null,
        ]);

        // Add products to shelf if provided
        if (!empty($validated['products'])) {
            foreach ($validated['products'] as $productData) {
                try {
                    // Generate SKU if not provided
                    if (empty($productData['sku'])) {
                        $category = !empty($productData['category']) && strcasecmp(trim($productData['category']), 'uncategorized') !== 0 
                            ? $productData['category'] 
                            : ($productData['product_name'] ?? $productData['name'] ?? 'ACCESSORIES');
                        $brand = $productData['brand'] ?? $productData['name'];
                        
                        // Find existing products with same category and brand to determine unique identifier
                        $existingCount = Product::where('category', $category)
                            ->where('name', $brand)
                            ->count();
                        $nextId = $existingCount + 1;
                        $uniqueId = str_pad($nextId, 3, '0', STR_PAD_LEFT);
                        
                        $productData['sku'] = 'KCC_' . str_replace(' ', '_', strtoupper($category)) . '_' . $brand . '_' . $uniqueId;
                    }
                    
                    $category = !empty($productData['category']) && strcasecmp(trim($productData['category']), 'uncategorized') !== 0
                        ? $productData['category']
                        : ($productData['product_name'] ?? $productData['name'] ?? 'Accessories');

                    // Find or create product by SKU
                    $product = Product::firstOrCreate(
                        ['sku' => $productData['sku']],
                        [
                            'name' => $productData['name'],
                            'product_name' => $productData['product_name'] ?? $productData['name'] ?? null,
                            'category' => $category,
                            'brand' => $productData['brand'] ?? null,
                            'unit_price' => $productData['price'],
                        ]
                    );

                    // Create shop inventory record
                    ShopInventory::create([
                        'shop_shelf_id' => $shelf->id,
                        'product_id' => $product->id,
                        'quantity' => $productData['qty'],
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error creating product: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Shelf created successfully',
            'shelf' => $shelf,
        ]);
    }

    /**
     * Get available shop sections
     */
    public function getShopSections()
    {
        $maxShelvesPerSection = 4;

        // Get all unique locations from shelves
        $sections = ShopShelf::whereNotNull('location')
            ->where('is_active', true)
            ->select('location')
            ->distinct()
            ->get()
            ->pluck('location')
            ->toArray();

        // Count shelves per section and calculate available
        $sectionCounts = [];
        foreach ($sections as $section) {
            $count = ShopShelf::where('location', $section)
                ->where('is_active', true)
                ->count();
            $available = max(0, $maxShelvesPerSection - $count);
            $sectionCounts[] = [
                'name' => $section,
                'shelf_count' => $count,
                'available' => $available,
                'max' => $maxShelvesPerSection,
            ];
        }

        // Sort by section name
        usort($sectionCounts, function($a, $b) {
            return strnatcmp($a['name'], $b['name']);
        });

        return response()->json([
            'success' => true,
            'sections' => $sectionCounts,
        ]);
    }

    /**
     * Update a shop shelf
     */
    public function updateShelf(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'products' => 'nullable|array',
            ]);

            $shelf = ShopShelf::findOrFail($id);
            $shelf->update([
                'name' => $validated['name'],
                'location' => $validated['location'] ?? null,
            ]);

            // Update products if provided and has valid data
            if (isset($validated['products']) && is_array($validated['products']) && !empty($validated['products'])) {
                // Check if any product has valid data
                $hasValidProducts = false;
                foreach ($validated['products'] as $productData) {
                    if (!empty($productData['name']) && !empty($productData['qty'])) {
                        $hasValidProducts = true;
                        break;
                    }
                }

                if ($hasValidProducts) {
                    // Delete existing inventory for this shelf
                    ShopInventory::where('shop_shelf_id', $shelf->id)->delete();

                    // Add new products
                    foreach ($validated['products'] as $productData) {
                        if (!empty($productData['name']) && !empty($productData['qty'])) {
                            // Try to find product by SKU first, then by name
                            $product = null;
                            if (!empty($productData['sku'])) {
                                $product = Product::where('sku', $productData['sku'])->first();
                            }
                            if (!$product) {
                                $product = Product::where('name', $productData['name'])->first();
                            }

                            // If product doesn't exist, create it
                            if (!$product) {
                                $baseSKU = $productData['sku'] ?? 'KCC-' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $productData['name']));
                                $sku = $baseSKU;

                                // Check if SKU exists and add unique identifier if needed
                                $counter = 1;
                                while (Product::where('sku', $sku)->exists()) {
                                    $sku = $baseSKU . '-' . $counter;
                                    $counter++;
                                }

                                $product = Product::create([
                                    'name'       => $productData['name'],
                                    'sku'        => $sku,
                                    'unit_price' => $productData['price'] ?? 0,
                                    'category'   => 'General',
                                    'status'     => 'active',
                                ]);
                            } else {
                                // Update price if provided
                                if (isset($productData['price'])) {
                                    $product->unit_price = $productData['price'];
                                }
                                if (isset($productData['qty'])) {
                                    $product->stock_quantity = $productData['qty'];
                                }
                                $product->save();

                                // Update ProductCatalog fields if relationship exists
                                if ($product->productCatalog) {
                                    $catalogUpdates = [];
                                    if (!empty($productData['description'])) {
                                        $catalogUpdates['product_description'] = $productData['description'];
                                    }
                                    if (!empty($productData['brand'])) {
                                        $catalogUpdates['brand'] = $productData['brand'];
                                    }
                                    if (!empty($productData['compatible_model'])) {
                                        $catalogUpdates['product_name'] = $productData['compatible_model'];
                                    }
                                    if (!empty($productData['sku'])) {
                                        $catalogUpdates['sku'] = $productData['sku'];
                                    }
                                    if (!empty($catalogUpdates)) {
                                        $product->productCatalog->update($catalogUpdates);
                                    }
                                }
                            }

                            ShopInventory::create([
                                'shop_shelf_id' => $shelf->id,
                                'product_id'    => $product->id,
                                'quantity'      => $productData['qty'],
                            ]);

                            // Log history
                            ShopInventoryHistory::create([
                                'shop_shelf_id'   => $shelf->id,
                                'product_id'      => $product->id,
                                'action_type'     => 'updated',
                                'quantity_change' => $productData['qty'],
                                'user_id'         => auth()->id(),
                                'notes'           => "Updated in shelf {$shelf->name}",
                            ]);
                        }
                    }
                }
            }

            // Load shelf with inventory for response
            $shelf->load('shopInventory.product');

            return response()->json([
                'success' => true,
                'message' => 'Shelf updated successfully',
                'shelf' => $shelf,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error updating shelf: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating shelf: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating shelf: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get shelf data for AJAX request
     */
    public function getShelfData($id)
    {
        $shelf = ShopShelf::with('shopInventory.product.productCatalog')->findOrFail($id);
        return response()->json($shelf);
    }

    /**
     * Show edit form for a shop shelf
     */
    public function edit($id)
    {
        $shelf = ShopShelf::findOrFail($id);
        return view('shop_inventory.edit', compact('shelf'));
    }

    /**
     * Archive a shop shelf
     */
    public function archiveShelf($id)
    {
        $shelf = ShopShelf::findOrFail($id);
        $shelf->is_active = false;
        $shelf->save();

        return response()->json([
            'success' => true,
            'message' => 'Shelf archived successfully',
        ]);
    }

    /**
     * Display archived shelves
     */
    public function archived()
    {
        $archivedShelves = ShopShelf::with('shopInventory.product')
            ->where('is_active', false)
            ->get()
            ->map(function ($shelf) {
                $shelf->occupied = $shelf->shopInventory->count();
                return $shelf;
            });

        return view('shop_inventory.archived', compact('archivedShelves'));
    }

    /**
     * Restore an archived shelf
     */
    public function restoreShelf($id)
    {
        $shelf = ShopShelf::findOrFail($id);
        $shelf->is_active = true;
        $shelf->save();

        return response()->json([
            'success' => true,
            'message' => 'Shelf restored successfully',
        ]);
    }

    /**
     * Permanently delete a shelf
     */
    public function deleteShelf($id)
    {
        $shelf = ShopShelf::findOrFail($id);

        // Delete associated inventory
        ShopInventory::where('shop_shelf_id', $id)->delete();

        // Delete the shelf
        $shelf->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shelf deleted permanently',
        ]);
    }

    /**
     * Show transfer page for shop inventory
     */
    public function showTransfer($shelfId)
    {
        $shelf = ShopShelf::with('shopInventory.product')
            ->where('id', $shelfId)
            ->where('is_active', true)
            ->first();

        if (!$shelf) {
            abort(404, 'Shelf not found');
        }

        // Get all other active shelves with available space
        $allShelves = ShopShelf::where('is_active', true)
            ->with('shopInventory')
            ->get();

        \Log::info('showTransfer - Source shelf ID: ' . $shelfId . ' (type: ' . gettype($shelfId) . ')');
        \Log::info('showTransfer - All shelves count: ' . $allShelves->count());
        \Log::info('showTransfer - All shelf IDs: ' . $allShelves->pluck('id')->implode(', '));

        $availableShelves = [];
        $shelfCapacity = 10;

        foreach ($allShelves as $otherShelf) {
            \Log::info('Checking shelf: ' . $otherShelf->id . ' (type: ' . gettype($otherShelf->id) . ') vs source: ' . $shelfId . ' (type: ' . gettype($shelfId) . ')');
            \Log::info('Strict comparison result: ' . ((int)$otherShelf->id === (int)$shelfId ? 'MATCH - will skip' : 'DIFFERENT - will include'));

            // Explicitly exclude source shelf in the loop with strict comparison
            if ((int)$otherShelf->id === (int)$shelfId) {
                \Log::info('Skipping shelf ' . $otherShelf->id . ' as it matches source shelf');
                continue;
            }

            $currentOccupancy = $otherShelf->shopInventory->count();
            $availableSpace = $shelfCapacity - $currentOccupancy;

            if ($availableSpace > 0) {
                $availableShelves[] = [
                    'id' => $otherShelf->id,
                    'name' => $otherShelf->name,
                    'location' => $otherShelf->location,
                    'current_occupancy' => $currentOccupancy,
                    'available_space' => $availableSpace,
                    'status' => $availableSpace >= 5 ? 'Available' : ($availableSpace >= 3 ? 'Almost Full' : 'Limited'),
                ];
            }
        }

        \Log::info('showTransfer - Available shelves count: ' . count($availableShelves));
        \Log::info('showTransfer - Available shelf IDs: ' . collect($availableShelves)->pluck('id')->implode(', '));

        // Prepare shelf products for display
        $shelfProducts = $shelf->shopInventory->map(function ($item) {
            return [
                'product_id' => $item->product->id,
                'name' => $item->product->name,
                'sku' => $item->product->sku ?? '',
                'qty' => $item->quantity,
                'price' => $item->product->unit_price ?? 0,
            ];
        })->toArray();

        return view('shop_inventory.transfer', [
            'shelf' => $shelf,
            'shelfProducts' => array_values($shelfProducts),
            'availableShelves' => $availableShelves,
            'shelfCapacity' => $shelfCapacity,
        ]);
    }

    /**
     * Execute transfer between shop shelves
     */
    public function executeTransfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'source_shelf_id' => 'required|exists:shop_shelves,id',
                'destination_shelf_id' => 'required|exists:shop_shelves,id',
                'transfers' => 'required|array|min:1',
                'transfers.*.product_id' => 'required|exists:products,id',
                'transfers.*.product_name' => 'required|string',
                'transfers.*.sku' => 'required|string',
                'transfers.*.current_qty' => 'required|integer|min:0',
                'transfers.*.transfer_qty' => 'required|integer|min:1',
                'transfers.*.unit_price' => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()['destination_shelf_id'] ?? $e->errors()['source_shelf_id'] ?? ['Invalid input']),
            ], 422);
        }

        // Manual check for same shelf
        if ($validated['source_shelf_id'] == $validated['destination_shelf_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot transfer to the same shelf',
            ], 422);
        }

        $sourceShelf = ShopShelf::findOrFail($validated['source_shelf_id']);
        $destShelf = ShopShelf::findOrFail($validated['destination_shelf_id']);

        // Check destination shelf capacity
        $currentDestOccupied = ShopInventory::where('shop_shelf_id', $destShelf->id)->count();
        $newProductsCount = count($validated['transfers']);

        // Count how many of these products are already in destination
        $existingInDest = 0;
        foreach ($validated['transfers'] as $transfer) {
            if (ShopInventory::where('shop_shelf_id', $destShelf->id)
                ->where('product_id', $transfer['product_id'])->exists()) {
                $existingInDest++;
            }
        }

        $totalAfterTransfer = $currentDestOccupied + ($newProductsCount - $existingInDest);

        if ($totalAfterTransfer > $destShelf->capacity) {
            $availableDestSlots = $destShelf->capacity - $currentDestOccupied;
            $newProductsNeeded = $newProductsCount - $existingInDest;
            return response()->json([
                'success' => false,
                'message' => "Destination shelf does not have enough space. Available: {$availableDestSlots} slots"
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($validated['transfers'] as $transfer) {
                $productId = $transfer['product_id'];
                $transferQty = $transfer['transfer_qty'];
                $currentQty = $transfer['current_qty'];

                // Find product in source shelf
                $sourceInventory = ShopInventory::where('shop_shelf_id', $sourceShelf->id)
                    ->where('product_id', $productId)
                    ->first();

                if (!$sourceInventory || $sourceInventory->quantity < $transferQty) {
                    throw new \Exception("Insufficient stock in source shelf for product ID: $productId");
                }

                // Deduct from source
                if ($transferQty >= $currentQty) {
                    // Transfer all quantity, remove from source
                    $sourceInventory->delete();
                } else {
                    // Partial transfer, update quantity in source
                    $sourceInventory->quantity = $currentQty - $transferQty;
                    $sourceInventory->save();
                }

                // Add to destination
                $destInventory = ShopInventory::updateOrCreate(
                    [
                        'shop_shelf_id' => $destShelf->id,
                        'product_id' => $productId,
                    ],
                    [
                        'quantity' => DB::raw("COALESCE(quantity, 0) + $transferQty"),
                    ]
                );

                // Log history for source
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $sourceShelf->id,
                    'product_id' => $productId,
                    'action_type' => 'transfer_out',
                    'quantity_change' => -$transferQty,
                    'source_type' => 'shop_shelf',
                    'source_id' => $sourceShelf->id,
                    'destination_type' => 'shop_shelf',
                    'destination_id' => $destShelf->id,
                    'user_id' => Auth::id(),
                    'notes' => "Transferred to shop shelf: {$destShelf->name}",
                ]);

                // Log history for destination
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $destShelf->id,
                    'product_id' => $productId,
                    'action_type' => 'transfer_in',
                    'quantity_change' => $transferQty,
                    'source_type' => 'shop_shelf',
                    'source_id' => $sourceShelf->id,
                    'destination_type' => 'shop_shelf',
                    'destination_id' => $destShelf->id,
                    'user_id' => Auth::id(),
                    'notes' => "Transferred from shop shelf: {$sourceShelf->name}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Products transferred successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    /**
     * Get all shop shelves with inventory
     */
    public function getShelves()
    {
        $shelves = ShopShelf::with(['shopInventory.product.productCatalog'])
            ->where('is_active', true)
            ->get()
            ->map(function ($shelf) {
                $shelf->shop_inventory = $shelf->shopInventory;
                $shelf->occupied = $shelf->shopInventory->count();
                $shelf->available = max(0, $shelf->capacity - $shelf->occupied);
                return $shelf;
            });

        return response()->json(['data' => $shelves]);
    }

    /**
     * Get warehouse shelves for transfer to shop and return to warehouse
     */
    public function getWarehouseShelves()
    {
        // Ensure default warehouses exist (Warehouse A, B, C, D)
        $defaultWarehouses = [
            ['name' => 'Warehouse A', 'code' => 'WH-A'],
            ['name' => 'Warehouse B', 'code' => 'WH-B'],
            ['name' => 'Warehouse C', 'code' => 'WH-C'],
            ['name' => 'Warehouse D', 'code' => 'WH-D'],
        ];

        foreach ($defaultWarehouses as $default) {
            \App\Models\Warehouse::firstOrCreate(
                ['code' => $default['code']],
                ['name' => $default['name'], 'is_active' => true]
            );
        }

        // Load all active products with catalog and warehouse stocks
        $products = \App\Models\Product::where('is_archived', false)
            ->with('productCatalog')
            ->with('warehouseStocks')
            ->orderBy('description')
            ->orderBy('brand')
            ->get();

        $warehouses = \App\Models\Warehouse::active()
            ->where('name', '!=', 'SHOP')
            ->orderBy('name')
            ->get();
        $totalWarehouses = $warehouses->count();

        $savedShelvesByWarehouse = \App\Models\WarehouseShelf::all()
            ->groupBy('warehouse_id');

        // Map warehouse names / codes to index
        $warehouseNameToIndex = [];
        foreach ($warehouses as $index => $warehouse) {
            $warehouseNameToIndex[$warehouse->name] = $index;
            $warehouseNameToIndex[$warehouse->code] = $index;
            $warehouseNameToIndex[strtoupper(trim($warehouse->name))] = $index;
            $warehouseNameToIndex[strtoupper(trim($warehouse->code))] = $index;
            $warehouseNameToIndex[strtolower(trim($warehouse->name))] = $index;
            $warehouseNameToIndex[strtolower(trim($warehouse->code))] = $index;
            if (preg_match('/warehouse\s+([a-z0-9]+)/i', $warehouse->name, $m)) {
                $warehouseNameToIndex['WH-' . strtoupper($m[1])] = $index;
                $warehouseNameToIndex[strtoupper($m[1])] = $index;
            }
        }

        $warehouseAssignments = [];
        for ($i = 0; $i < $totalWarehouses; $i++) {
            $warehouseAssignments[] = collect();
        }

        foreach ($products as $product) {
            if ($product->productCatalog) {
                $productDescription = $product->productCatalog->product_description;
                $brand = $product->productCatalog->brand;
                $sku = $product->productCatalog->sku;
                $compatibility = $product->productCatalog->product_name;
            } else {
                $productDescription = $product->description ?? $product->product_name ?? $product->name;
                $brand = $product->brand;
                $sku = $product->sku;
                $compatibility = $product->compatibility;
            }

            $warehouseStocks = $product->warehouseStocks ?? [];
            foreach ($warehouseStocks as $stock) {
                if (strtoupper(trim($stock->warehouse)) === 'SHOP') {
                    continue;
                }

                $whKey = trim($stock->warehouse);
                $warehouseIndex = $warehouseNameToIndex[$whKey] 
                    ?? $warehouseNameToIndex[strtoupper($whKey)] 
                    ?? $warehouseNameToIndex[strtolower($whKey)] 
                    ?? -1;

                if ($warehouseIndex >= 0 && $stock->quantity > 0 && isset($warehouseAssignments[$warehouseIndex])) {
                    $warehouseAssignments[$warehouseIndex]->push([
                        'id'               => $product->id,
                        'sku'              => $sku ?: ('PROD-' . $product->id),
                        'name'             => $productDescription,
                        'description'      => $productDescription,
                        'brand'            => $brand,
                        'compatible_model' => $compatibility,
                        'qty'              => (int) $stock->quantity,
                        'stock_quantity'   => (int) $stock->quantity,
                        'price'            => (float) ($product->unit_price ?? 0),
                        'category'         => $productDescription,
                    ]);
                }
            }
        }

        // Build catalog lookup for enrichment
        $catalogBySku = [];
        $catalogByDescription = [];
        $catalogByCompatible = [];
        foreach ($products as $p) {
            if ($p->productCatalog) {
                $cat = $p->productCatalog;
                $entry = [
                    'description'      => $cat->product_description,
                    'brand'            => $cat->brand,
                    'compatible_model' => $cat->product_name,
                    'sku'              => $cat->sku,
                    'price'            => $p->unit_price ?? 0,
                    'product_name'     => $p->product_name ?? $p->name,
                ];
                if ($cat->sku) $catalogBySku[strtolower(trim($cat->sku))] = $entry;
                if ($cat->product_description) $catalogByDescription[strtolower(trim($cat->product_description))] = $entry;
                if ($cat->product_name) $catalogByCompatible[strtolower(trim($cat->product_name))] = $entry;
            } else {
                $sku  = $p->sku;
                $desc = $p->description ?? $p->product_name ?? $p->name;
                $entry = [
                    'description'      => $desc,
                    'brand'            => $p->brand,
                    'compatible_model' => $p->compatibility,
                    'sku'              => $sku,
                    'price'            => $p->unit_price ?? 0,
                    'product_name'     => $p->product_name ?? $p->name,
                ];
                if ($sku) $catalogBySku[strtolower(trim($sku))] = $entry;
            }
        }

        $enrichProduct = function (array $storedProduct) use ($catalogBySku, $catalogByDescription, $catalogByCompatible): array {
            $storedSku  = strtolower(trim($storedProduct['sku'] ?? ''));
            $storedName = strtolower(trim($storedProduct['name'] ?? $storedProduct['description'] ?? ''));

            $cat = null;
            if ($storedSku && isset($catalogBySku[$storedSku])) {
                $cat = $catalogBySku[$storedSku];
            }
            if (!$cat && $storedName && isset($catalogByCompatible[$storedName])) {
                $cat = $catalogByCompatible[$storedName];
            }
            if (!$cat && $storedName && isset($catalogByDescription[$storedName])) {
                $cat = $catalogByDescription[$storedName];
            }

            if ($cat) {
                return array_merge($storedProduct, [
                    'description'      => $cat['description'],
                    'brand'            => $cat['brand'],
                    'compatible_model' => $cat['compatible_model'],
                    'sku'              => $cat['sku'] ?: ($storedProduct['sku'] ?? ''),
                    'product_name'     => $cat['product_name'],
                ]);
            }

            return $storedProduct;
        };

        $warehousesGrouped = [];

        foreach ($warehouses as $warehouseIndex => $warehouse) {
            $warehouseSavedShelves = $savedShelvesByWarehouse->get($warehouse->id, collect());
            $visibleSavedShelves = $warehouseSavedShelves->filter(function ($shelf) {
                return !$shelf->archived;
            })->sortBy('slot_index');

            $hasAnyProducts = false;
            foreach ($visibleSavedShelves as $s) {
                $pList = is_string($s->products) ? json_decode($s->products, true) : ($s->products ?? []);
                if (!empty($pList) && is_array($pList) && count($pList) > 0) {
                    $hasAnyProducts = true;
                    break;
                }
            }

            $allocatedProducts = ($warehouseAssignments[$warehouseIndex] ?? collect())->values();

            // Auto-create/sync shelves in DB if missing or empty
            if (($visibleSavedShelves->isEmpty() || !$hasAnyProducts) && $allocatedProducts->isNotEmpty()) {
                $chunkedProducts = $allocatedProducts->chunk(10);
                $slotIdx = 0;
                foreach ($chunkedProducts as $chunk) {
                    $shelfProducts = $chunk->values()->toArray();
                    try {
                        \App\Models\WarehouseShelf::updateOrCreate(
                            [
                                'warehouse_id' => $warehouse->id,
                                'slot_index'   => $slotIdx,
                            ],
                            [
                                'warehouse_code'  => $warehouse->code ?: ('WH-' . $warehouse->id),
                                'warehouse_index' => $warehouseIndex,
                                'sort_order'      => $slotIdx,
                                'name'            => $warehouse->name . ' - Shelf ' . ($slotIdx + 1),
                                'capacity'        => 10,
                                'products'        => $shelfProducts,
                                'archived'        => false,
                            ]
                        );
                    } catch (\Throwable $e) {}
                    $slotIdx++;
                }

                $visibleSavedShelves = \App\Models\WarehouseShelf::where('warehouse_id', $warehouse->id)
                    ->where('archived', false)
                    ->orderBy('slot_index')
                    ->get();
            }

            $formattedShelves = [];

            if ($visibleSavedShelves->isNotEmpty()) {
                foreach ($visibleSavedShelves as $shelf) {
                    $products = is_string($shelf->products) ? json_decode($shelf->products, true) : ($shelf->products ?? []);
                    if (!is_array($products)) {
                        $products = [];
                    }
                    $normalizedProducts = array_map(function ($p) use ($enrichProduct) {
                        $enriched = $enrichProduct($p);
                        return [
                            'id'          => $enriched['id'] ?? $p['id'] ?? null,
                            'sku'         => $enriched['sku'] ?? $p['sku'] ?? '',
                            'name'        => $enriched['description'] ?? $enriched['name'] ?? $p['name'] ?? $p['description'] ?? 'Product',
                            'description' => $enriched['description'] ?? $p['description'] ?? '',
                            'brand'       => $enriched['brand'] ?? $p['brand'] ?? '',
                            'qty'         => max(1, (int) ($p['qty'] ?? $p['stock_quantity'] ?? $p['quantity'] ?? 1)),
                            'price'       => (float) ($enriched['price'] ?? $p['price'] ?? $p['unit_price'] ?? 0),
                        ];
                    }, array_values($products));

                    $formattedShelves[] = [
                        'id'           => $shelf->id,
                        'warehouse_id' => $shelf->warehouse_id,
                        'name'         => $shelf->name,
                        'slot_index'   => $shelf->slot_index,
                        'products'     => $normalizedProducts,
                    ];
                }
            } elseif ($allocatedProducts->isNotEmpty()) {
                // If DB shelf write was pending, chunk in-memory so shelves are ALWAYS present
                $chunked = $allocatedProducts->chunk(10);
                foreach ($chunked as $slotIdx => $chunk) {
                    $normalizedProducts = $chunk->map(function ($p) use ($enrichProduct) {
                        $enriched = $enrichProduct((array)$p);
                        return [
                            'id'          => $enriched['id'] ?? $p['id'] ?? null,
                            'sku'         => $enriched['sku'] ?? $p['sku'] ?? '',
                            'name'        => $enriched['description'] ?? $enriched['name'] ?? $p['name'] ?? $p['description'] ?? 'Product',
                            'description' => $enriched['description'] ?? $p['description'] ?? '',
                            'brand'       => $enriched['brand'] ?? $p['brand'] ?? '',
                            'qty'         => max(1, (int) ($p['qty'] ?? $p['stock_quantity'] ?? 1)),
                            'price'       => (float) ($enriched['price'] ?? $p['price'] ?? $p['unit_price'] ?? 0),
                        ];
                    })->values()->toArray();

                    $formattedShelves[] = [
                        'id'           => 'temp_' . $warehouse->id . '_' . $slotIdx,
                        'warehouse_id' => $warehouse->id,
                        'name'         => $warehouse->name . ' - Shelf ' . ($slotIdx + 1),
                        'slot_index'   => $slotIdx,
                        'products'     => $normalizedProducts,
                    ];
                }
            }

            $warehousesGrouped[] = [
                'id'      => $warehouse->id,
                'name'    => $warehouse->name,
                'code'    => $warehouse->code,
                'shelves' => $formattedShelves,
            ];
        }

        return response()->json(['data' => $warehousesGrouped]);
    }

    /**
     * Get all unique brands from products
     */
    public function getBrands()
    {
        $brands = Product::where('is_archived', false)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->toArray();

        return response()->json($brands);
    }

    /**
     * Get products available for transfer from warehouse
     */
    public function getWarehouseProducts()
    {
        // Get products from warehouse shelves
        $warehouseShelves = DB::table('warehouse_shelves')->get();

        $products = [];
        foreach ($warehouseShelves as $shelf) {
            $shelfProducts = json_decode($shelf->products ?? '[]', true);
            if (is_array($shelfProducts)) {
                foreach ($shelfProducts as $product) {
                    if (isset($product['sku']) && isset($product['name'])) {
                        $products[] = [
                            'id' => $product['sku'], // Use SKU as ID since warehouse products don't have ID
                            'name' => $product['name'],
                            'sku' => $product['sku'],
                            'quantity' => $product['qty'] ?? 0,
                            'warehouse_shelf_id' => $shelf->id,
                            'warehouse_shelf_name' => $shelf->name,
                        ];
                    }
                }
            }
        }

        return response()->json(['data' => $products]);
    }

    /**
     * Transfer products from warehouse to shop
     */
    public function transferFromWarehouse(Request $request)
    {
        $validated = $request->validate([
            'shop_shelf_id' => 'required|exists:shop_shelves,id',
            'transfers' => 'required|array|min:1',
            'transfers.*.product_id' => 'required', // SKU or ID is sent as product_id
            'transfers.*.warehouse_shelf_id' => 'required',
            'transfers.*.quantity' => 'required|integer|min:1',
        ]);

        $shopShelf = ShopShelf::findOrFail($validated['shop_shelf_id']);

        DB::beginTransaction();
        try {
            foreach ($validated['transfers'] as $transfer) {
                $productSku = $transfer['product_id'];
                $quantity = $transfer['quantity'];
                $warehouseShelfId = $transfer['warehouse_shelf_id'];

                // Handle temporary shelf IDs if any
                if (is_string($warehouseShelfId) && str_starts_with($warehouseShelfId, 'temp_')) {
                    $parts = explode('_', $warehouseShelfId);
                    $whId = $parts[1] ?? null;
                    $sIdx = (int) ($parts[2] ?? 0);
                    $whRecord = \App\Models\Warehouse::find($whId);
                    $tempShelf = \App\Models\WarehouseShelf::firstOrCreate(
                        ['warehouse_id' => $whId, 'slot_index' => $sIdx],
                        [
                            'warehouse_code'  => $whRecord ? $whRecord->code : ('WH-' . $whId),
                            'warehouse_index' => 0,
                            'sort_order'      => $sIdx,
                            'name'            => ($whRecord ? $whRecord->name : 'Warehouse') . ' - Shelf ' . ($sIdx + 1),
                            'capacity'        => 10,
                            'products'        => [],
                            'archived'        => false,
                        ]
                    );
                    $warehouseShelfId = $tempShelf->id;
                }

                // Find or create product by SKU or ID
                $product = Product::where('sku', $productSku)->first();
                if (!$product && is_numeric($productSku)) {
                    $product = Product::find($productSku);
                }
                if (!$product) {
                    $productName = $transfer['product_name'] ?? ('Product ' . $productSku);
                    $product = Product::firstOrCreate(
                        ['sku' => $productSku],
                        [
                            'name' => $productName,
                            'unit_price' => 0,
                            'stock_quantity' => 0,
                            'category' => 'Accessories',
                            'is_archived' => false,
                        ]
                    );
                }

                // Check warehouse stock
                $warehouseShelf = DB::table('warehouse_shelves')->where('id', $warehouseShelfId)->first();
                if ($warehouseShelf) {
                    $warehouseProducts = is_string($warehouseShelf->products) ? json_decode($warehouseShelf->products, true) : ($warehouseShelf->products ?? []);
                    if (!is_array($warehouseProducts)) {
                        $warehouseProducts = [];
                    }

                    $productIndex = collect($warehouseProducts)->search(function ($item) use ($productSku, $product) {
                        $iSku = strtolower(trim($item['sku'] ?? ''));
                        $tSku = strtolower(trim($productSku ?? ''));
                        if ($iSku && $tSku && $iSku === $tSku) return true;
                        if (!empty($item['id']) && $product && $item['id'] == $product->id) return true;
                        return false;
                    });

                    if ($productIndex !== false && isset($warehouseProducts[$productIndex])) {
                        $currentQty = (int) ($warehouseProducts[$productIndex]['qty'] ?? $warehouseProducts[$productIndex]['stock_quantity'] ?? 1);
                        $warehouseProducts[$productIndex]['qty'] = max(0, $currentQty - $quantity);
                        if ($warehouseProducts[$productIndex]['qty'] <= 0) {
                            array_splice($warehouseProducts, $productIndex, 1);
                        }
                        DB::table('warehouse_shelves')
                            ->where('id', $warehouseShelfId)
                            ->update(['products' => json_encode($warehouseProducts)]);
                    }
                }

                // Add to shop inventory
                $shopInventory = ShopInventory::updateOrCreate(
                    [
                        'shop_shelf_id' => $shopShelf->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => DB::raw("quantity + $quantity"),
                    ]
                );

                // Sync ProductWarehouseStock: Deduct from Warehouse
                $warehouseName = DB::table('warehouses')->where('id', $warehouseShelf->warehouse_id)->value('name');
                if ($warehouseName) {
                    $whStock = \App\Models\ProductWarehouseStock::where('product_id', $product->id)
                        ->where('warehouse', $warehouseName)
                        ->first();
                    if ($whStock) {
                        $whStock->quantity -= $quantity;
                        if ($whStock->quantity < 0) $whStock->quantity = 0;
                        $whStock->save();
                    }
                }

                // Sync ProductWarehouseStock: Add to SHOP
                $shopStock = \App\Models\ProductWarehouseStock::firstOrCreate(
                    ['product_id' => $product->id, 'warehouse' => 'SHOP'],
                    ['quantity' => 0]
                );
                $shopStock->quantity += $quantity;
                $shopStock->save();

                // Log history
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $shopShelf->id,
                    'product_id' => $product->id,
                    'action_type' => 'transfer_in',
                    'quantity_change' => $quantity,
                    'source_type' => 'warehouse',
                    'source_id' => $warehouseShelfId,
                    'destination_type' => 'shop_shelf',
                    'destination_id' => $shopShelf->id,
                    'user_id' => Auth::id(),
                    'notes' => "Transferred from warehouse shelf: {$warehouseShelf->name}",
                ]);

                // Log Inventory Movement
                \App\Models\InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'transfer',
                    'quantity_change' => 0, // It's a transfer between locations, so overall stock change is 0
                    'from_location' => $warehouseName ?? 'Warehouse',
                    'to_location' => 'SHOP',
                    'notes' => "Transferred {$quantity} units from warehouse shelf to shop shelf.",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Products transferred successfully from warehouse to shop',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Transfer products between shop shelves
     */
    public function transferBetweenShelves(Request $request)
    {
        $validated = $request->validate([
            'source_shelf_id' => 'required|exists:shop_shelves,id',
            'destination_shelf_id' => 'required|exists:shop_shelves,id|different:source_shelf_id',
            'transfers' => 'required|array|min:1',
            'transfers.*.product_id' => 'required|exists:products,id',
            'transfers.*.quantity' => 'required|integer|min:1',
        ]);

        $sourceShelf = ShopShelf::findOrFail($validated['source_shelf_id']);
        $destShelf = ShopShelf::findOrFail($validated['destination_shelf_id']);

        // Check destination shelf capacity
        $currentDestOccupied = ShopInventory::where('shop_shelf_id', $destShelf->id)->count();
        $newProductsCount = count($validated['transfers']);

        // Count how many of these products are already in destination
        $existingInDest = 0;
        foreach ($validated['transfers'] as $transfer) {
            if (ShopInventory::where('shop_shelf_id', $destShelf->id)
                ->where('product_id', $transfer['product_id'])->exists()) {
                $existingInDest++;
            }
        }

        $totalAfterTransfer = $currentDestOccupied + ($newProductsCount - $existingInDest);

        if ($totalAfterTransfer > $destShelf->capacity) {
            $availableDestSlots = $destShelf->capacity - $currentDestOccupied;
            $newProductsNeeded = $newProductsCount - $existingInDest;
            return response()->json([
                'success' => false,
                'message' => "Destination shelf capacity exceeded. Available slots: {$availableDestSlots}, New products needed: {$newProductsNeeded}",
            ], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($validated['transfers'] as $transfer) {
                $productId = $transfer['product_id'];
                $quantity = $transfer['quantity'];

                // Check source shelf stock
                $sourceInventory = ShopInventory::where('shop_shelf_id', $sourceShelf->id)
                    ->where('product_id', $productId)
                    ->first();

                if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
                    throw new \Exception("Insufficient stock in source shelf for product ID: $productId");
                }

                // Deduct from source
                $sourceInventory->quantity -= $quantity;
                if ($sourceInventory->quantity <= 0) {
                    $sourceInventory->delete();
                } else {
                    $sourceInventory->save();
                }

                // Add to destination
                $destInventory = ShopInventory::updateOrCreate(
                    [
                        'shop_shelf_id' => $destShelf->id,
                        'product_id' => $productId,
                    ],
                    [
                        'quantity' => DB::raw("quantity + $quantity"),
                    ]
                );

                // Log history for source
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $sourceShelf->id,
                    'product_id' => $productId,
                    'action_type' => 'transfer_out',
                    'quantity_change' => -$quantity,
                    'source_type' => 'shop_shelf',
                    'source_id' => $sourceShelf->id,
                    'destination_type' => 'shop_shelf',
                    'destination_id' => $destShelf->id,
                    'user_id' => Auth::id(),
                    'notes' => "Transferred to shop shelf: {$destShelf->name}",
                ]);

                // Log history for destination
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $destShelf->id,
                    'product_id' => $productId,
                    'action_type' => 'transfer_in',
                    'quantity_change' => $quantity,
                    'source_type' => 'shop_shelf',
                    'source_id' => $sourceShelf->id,
                    'destination_type' => 'shop_shelf',
                    'destination_id' => $destShelf->id,
                    'user_id' => Auth::id(),
                    'notes' => "Transferred from shop shelf: {$sourceShelf->name}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Products transferred successfully between shop shelves',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Return products to warehouse
     */
    public function returnToWarehouse(Request $request)
    {
        $validated = $request->validate([
            'shelf_id' => 'required|integer|exists:shop_shelves,id',
            'warehouse_shelf_id' => 'required|integer|exists:warehouse_shelves,id',
            'returns' => 'required|array',
            'returns.*.inventory_id' => 'required|integer|exists:shop_inventory,id',
            'returns.*.product_id' => 'required|integer|exists:products,id',
            'returns.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $shelf = ShopShelf::findOrFail($validated['shelf_id']);
            $warehouseShelfId = $validated['warehouse_shelf_id'];

            // Get the warehouse shelf
            $warehouseShelf = DB::table('warehouse_shelves')->where('id', $warehouseShelfId)->first();
            if (!$warehouseShelf) {
                throw new \Exception('Warehouse shelf not found');
            }

            foreach ($validated['returns'] as $returnData) {
                $shopInventory = ShopInventory::findOrFail($returnData['inventory_id']);

                // Validate that the inventory belongs to the shelf
                if ($shopInventory->shop_shelf_id != $shelf->id) {
                    throw new \Exception('Inventory item does not belong to this shelf');
                }

                // Validate quantity
                if ($returnData['quantity'] > $shopInventory->quantity) {
                    throw new \Exception('Cannot return more than available quantity');
                }

                // Decode existing products
                $products = json_decode($warehouseShelf->products ?? '[]', true);

                // Find if product already exists in warehouse
                $productFound = false;
                foreach ($products as &$product) {
                    if ($product['sku'] === $shopInventory->product->sku) {
                        $product['quantity'] += $returnData['quantity'];
                        $productFound = true;
                        break;
                    }
                }

                if (!$productFound) {
                    $products[] = [
                        'sku' => $shopInventory->product->sku,
                        'name' => $shopInventory->product->name,
                        'quantity' => $returnData['quantity'],
                        'price' => $shopInventory->product->unit_price,
                    ];
                }

                // Update warehouse shelf
                DB::table('warehouse_shelves')
                    ->where('id', $warehouseShelfId)
                    ->update([
                        'products' => json_encode($products),
                        'updated_at' => now(),
                    ]);

                // Update or remove shop inventory
                if ($returnData['quantity'] == $shopInventory->quantity) {
                    // Remove completely
                    $shopInventory->delete();
                } else {
                    // Reduce quantity
                    $shopInventory->quantity -= $returnData['quantity'];
                    $shopInventory->save();
                }

                // Sync ProductWarehouseStock: Deduct from SHOP
                $shopStock = \App\Models\ProductWarehouseStock::where('product_id', $returnData['product_id'])
                    ->where('warehouse', 'SHOP')
                    ->first();
                if ($shopStock) {
                    $shopStock->quantity -= $returnData['quantity'];
                    if ($shopStock->quantity < 0) $shopStock->quantity = 0;
                    $shopStock->save();
                }

                // Sync ProductWarehouseStock: Add to Warehouse
                $warehouseName = DB::table('warehouses')->where('id', $warehouseShelf->warehouse_id)->value('name');
                if ($warehouseName) {
                    $whStock = \App\Models\ProductWarehouseStock::firstOrCreate(
                        ['product_id' => $returnData['product_id'], 'warehouse' => $warehouseName],
                        ['quantity' => 0]
                    );
                    $whStock->quantity += $returnData['quantity'];
                    $whStock->save();
                }

                // Log history
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $shelf->id,
                    'product_id' => $returnData['product_id'],
                    'action_type' => 'return_to_warehouse',
                    'quantity_change' => -$returnData['quantity'],
                    'user_id' => Auth::id(),
                    'notes' => "Returned to warehouse from shelf: {$shelf->name}",
                ]);

                // Log Inventory Movement
                \App\Models\InventoryMovement::create([
                    'product_id' => $returnData['product_id'],
                    'type' => 'transfer',
                    'quantity_change' => 0, // Transfer between locations
                    'from_location' => 'SHOP',
                    'to_location' => $warehouseName ?? 'Warehouse',
                    'notes' => "Returned {$returnData['quantity']} units from shop shelf to warehouse.",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Products returned to warehouse successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Return failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get shop inventory history
     */
    public function getHistory(Request $request)
    {
        $query = ShopInventoryHistory::with(['product', 'shopShelf', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('shop_shelf_id')) {
            $query->where('shop_shelf_id', $request->shop_shelf_id);
        }

        if ($request->has('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $perPage = $request->get('per_page', 20);
        $history = $query->paginate($perPage);

        return response()->json($history);
    }

    /**
     * Get products for POS from shop inventory
     */
    public function getProductsForPOS(Request $request)
    {
        $query = ShopInventory::with('product.productCatalog')
            ->where('quantity', '>', 0)
            ->whereHas('shopShelf', function ($q) {
                $q->where('is_active', true);
            });

        // Apply search filter
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('category') && !empty($request->input('category')) && $request->input('category') !== 'All') {
            $category = $request->input('category');
            $query->whereHas('product', function ($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('productCatalog', function ($catalogQ) use ($category) {
                      $catalogQ->where('product_description', $category);
                  });
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $inventory = $query->paginate($perPage, ['*'], 'page', $page);

        // Map to product format for POS
        $products = $inventory->map(function ($item) {
            $product = $item->product;
            $productDescription = $product->description ?? $product->product_name ?? $product->name;
            $category = $product->category ?? 'Uncategorized';
            
            // Use product catalog data if available
            if ($product->productCatalog) {
                $productDescription = $product->productCatalog->product_description ?? $productDescription;
                $category = $product->productCatalog->product_description ?? $category;
            }
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'category' => $category,
                'product_description' => $productDescription,
                'brand' => $product->brand,
                'compatibility' => $product->compatibility ?? '',
                'size' => $product->size,
                'color' => $product->color,
                'unit_price' => $product->unit_price,
                'discount_type' => $product->discount_type ?? null,
                'discount_value' => $product->discount_value ? (float) $product->discount_value : 0,
                'stock_quantity' => $item->quantity, // Use shop inventory quantity
                'shop_shelf_id' => $item->shop_shelf_id,
                'shop_shelf_name' => $item->shopShelf->name,
            ];
        });

        return response()->json([
            'data' => $products,
            'pagination' => [
                'total' => $inventory->total(),
                'per_page' => $inventory->perPage(),
                'current_page' => $inventory->currentPage(),
                'last_page' => $inventory->lastPage(),
            ]
        ]);
    }

    /**
     * Deduct stock from shop inventory after POS sale
     */
    public function deductFromShopInventory(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                // Find product in shop inventory
                $shopInventory = ShopInventory::where('product_id', $productId)
                    ->where('quantity', '>=', $quantity)
                    ->first();

                if (!$shopInventory) {
                    throw new \Exception("Insufficient stock in shop inventory for product ID: $productId");
                }

                // Deduct quantity
                $shopInventory->quantity -= $quantity;
                if ($shopInventory->quantity <= 0) {
                    $shopInventory->delete();
                } else {
                    $shopInventory->save();
                }

                // Deduct from ProductWarehouseStock for SHOP
                $shopStock = \App\Models\ProductWarehouseStock::where('product_id', $productId)
                    ->where('warehouse', 'SHOP')
                    ->first();
                if ($shopStock) {
                    $shopStock->quantity -= $quantity;
                    if ($shopStock->quantity < 0) $shopStock->quantity = 0;
                    $shopStock->save();
                }

                // Log history
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $shopInventory->shop_shelf_id,
                    'product_id' => $productId,
                    'action_type' => 'pos_sale',
                    'quantity_change' => -$quantity,
                    'source_type' => 'shop_shelf',
                    'source_id' => $shopInventory->shop_shelf_id,
                    'user_id' => Auth::id(),
                    'notes' => 'POS sale deduction',
                ]);

                // Log Inventory Movement
                \App\Models\InventoryMovement::create([
                    'product_id' => $productId,
                    'type' => 'pos_sale',
                    'quantity_change' => -$quantity,
                    'from_location' => 'SHOP',
                    'to_location' => 'Customer',
                    'notes' => 'POS sale deduction from shop shelf: ' . ($shopInventory->shopShelf->name ?? ''),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock deducted successfully from shop inventory',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Stock deduction failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get all products from shop inventory for dropdown
     */
    public function getShopProducts()
    {
        try {
            $products = ShopInventory::with('product')
                ->whereHas('shopShelf', function($q) {
                    $q->where('is_active', true);
                })
                ->where('quantity', '>', 0)
                ->get()
                ->map(function($inventory) {
                    return [
                        'id' => $inventory->product_id,
                        'name' => $inventory->product->name ?? 'Unknown',
                        'sku' => $inventory->product->sku ?? '',
                        'quantity' => $inventory->quantity,
                    ];
                })
                ->unique('id')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
