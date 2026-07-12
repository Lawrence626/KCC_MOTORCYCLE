<?php

namespace App\Http\Controllers;

use App\Models\ShopShelf;
use App\Models\ShopInventory;
use App\Models\ShopInventoryHistory;
use App\Models\Product;
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
        $shelves = ShopShelf::with('shopInventory.product')
            ->where('is_active', true)
            ->get()
            ->map(function ($shelf) {
                // Ensure the relationship is loaded as a collection
                $shelf->shop_inventory = $shelf->shopInventory;
                $shelf->occupied = $shelf->shop_inventory->count();
                $shelf->available = max(0, $shelf->capacity - $shelf->occupied);
                return $shelf;
            });

        // Calculate totals - count distinct products, not quantities
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
                    // Find or create product by SKU
                    $product = Product::firstOrCreate(
                        ['sku' => $productData['sku']],
                        [
                            'name' => $productData['name'],
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
                                    'name' => $productData['name'],
                                    'sku' => $sku,
                                    'unit_price' => $productData['price'] ?? 0,
                                    'category' => 'General',
                                    'status' => 'active',
                                ]);
                            }

                            ShopInventory::create([
                                'shop_shelf_id' => $shelf->id,
                                'product_id' => $product->id,
                                'quantity' => $productData['qty'],
                            ]);

                            // Log history
                            ShopInventoryHistory::create([
                                'shop_shelf_id' => $shelf->id,
                                'product_id' => $product->id,
                                'action_type' => 'updated',
                                'quantity_change' => $productData['qty'],
                                'user_id' => auth()->id(),
                                'notes' => "Updated in shelf {$shelf->name}",
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
        $shelf = ShopShelf::with('shopInventory.product')->findOrFail($id);
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
        $shelves = ShopShelf::where('is_active', true)
            ->get()
            ->map(function ($shelf) {
                $shelf->occupied = $shelf->shopInventory->count();
                $shelf->available = max(0, $shelf->capacity - $shelf->occupied);
                return $shelf;
            });

        return response()->json(['data' => $shelves]);
    }

    /**
     * Get warehouse shelves for return to warehouse
     */
    public function getWarehouseShelves()
    {
        $warehouseShelves = DB::table('warehouse_shelves')
            ->where('archived', false)
            ->whereNotNull('warehouse_id')
            ->get();

        // Get all active warehouses
        $warehouses = DB::table('warehouses')
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Group by warehouse_id
        $warehousesGrouped = [];
        foreach ($warehouseShelves as $shelf) {
            $warehouseId = $shelf->warehouse_id;
            if (!isset($warehousesGrouped[$warehouseId])) {
                $warehouse = $warehouses[$warehouseId] ?? null;
                $warehousesGrouped[$warehouseId] = [
                    'id' => $warehouseId,
                    'name' => $warehouse ? $warehouse->name : "Unknown Warehouse",
                    'code' => $warehouse ? $warehouse->code : "Unknown",
                    'shelves' => []
                ];
            }
            $warehousesGrouped[$warehouseId]['shelves'][] = $shelf;
        }

        // Re-index array
        $warehousesGrouped = array_values($warehousesGrouped);

        return response()->json(['data' => $warehousesGrouped]);
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
            'transfers.*.product_id' => 'required', // SKU is sent as product_id
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

                // Find or create product by SKU
                $product = Product::firstOrCreate(
                    ['sku' => $productSku],
                    [
                        'name' => 'Product ' . $productSku,
                        'unit_price' => 0,
                        'stock_quantity' => 0,
                        'category' => 'Uncategorized',
                    ]
                );

                // Check warehouse stock
                $warehouseShelf = DB::table('warehouse_shelves')->where('id', $warehouseShelfId)->first();
                $warehouseProducts = json_decode($warehouseShelf->products ?? '[]', true);

                $productIndex = collect($warehouseProducts)->search(function ($item) use ($productSku) {
                    return ($item['sku'] ?? '') == $productSku;
                });

                if ($productIndex === false || $warehouseProducts[$productIndex]['qty'] < $quantity) {
                    throw new \Exception("Insufficient stock in warehouse for product SKU: $productSku");
                }

                // Deduct from warehouse
                $warehouseProducts[$productIndex]['qty'] -= $quantity;
                if ($warehouseProducts[$productIndex]['qty'] <= 0) {
                    array_splice($warehouseProducts, $productIndex, 1);
                }
                DB::table('warehouse_shelves')
                    ->where('id', $warehouseShelfId)
                    ->update(['products' => json_encode($warehouseProducts)]);

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

                // Log history
                ShopInventoryHistory::create([
                    'shop_shelf_id' => $shelf->id,
                    'product_id' => $returnData['product_id'],
                    'action_type' => 'return_to_warehouse',
                    'quantity_change' => -$returnData['quantity'],
                    'user_id' => Auth::id(),
                    'notes' => "Returned to warehouse from shelf: {$shelf->name}",
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
        $query = ShopInventory::with('product')
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

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $inventory = $query->paginate($perPage, ['*'], 'page', $page);

        // Map to product format for POS
        $products = $inventory->map(function ($item) {
            $product = $item->product;
            return [
                'id' => $product->id,
                'name' => $product->name,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'category' => $product->category,
                'brand' => $product->brand,
                'size' => $product->size,
                'color' => $product->color,
                'unit_price' => $product->unit_price,
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
