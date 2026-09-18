<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductCatalog;
use App\Models\ProductWarehouseStock;
use App\Models\StockArrivalNotice;
use App\Models\Warehouse;
use App\Models\WarehouseShelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class WarehouseManagementController extends Controller
{
    public function index()
    {
        // Load all products (not filtered by warehouse stock)
        $products = Product::where('is_archived', false)
            ->with('productCatalog')
            ->with('warehouseStocks')
            ->orderBy('description')
            ->orderBy('brand')
            ->get();

        $warehouses = Warehouse::active()
            ->where('name', '!=', 'SHOP')
            ->orderBy('name')
            ->get();
        $totalWarehouses = $warehouses->count();

        if ($totalWarehouses === 0) {
            // Create default warehouses if none exist
            $defaultWarehouses = [
                ['name' => 'Warehouse A', 'code' => 'WH-A'],
                ['name' => 'Warehouse B', 'code' => 'WH-B'],
                ['name' => 'Warehouse C', 'code' => 'WH-C'],
            ];

            foreach ($defaultWarehouses as $default) {
                Warehouse::create($default);
            }

            $warehouses = Warehouse::active()->orderBy('name')->get();
            $totalWarehouses = 3;
        }

        $totalProducts = $products->count();
        $productsPerWarehouse = ceil($totalProducts / $totalWarehouses);
        $shelvesPerWarehouse = max(1, ceil($productsPerWarehouse / 10));

        $savedShelvesByWarehouse = WarehouseShelf::all()
            ->groupBy('warehouse_id')
            ->map(function ($group) {
                return $group->keyBy('slot_index');
            });

        // Use actual warehouse stock data from product_warehouse_stock table
        $warehouseAssignments = [];
        for ($i = 0; $i < $totalWarehouses; $i++) {
            $warehouseAssignments[] = collect();
        }

        // Map warehouse names to indices
        $warehouseNameToIndex = [];
        foreach ($warehouses as $index => $warehouse) {
            $warehouseNameToIndex[$warehouse->name] = $index;
        }

        foreach ($products as $product) {
            // Always prioritize ProductCatalog data for correct product information
            if ($product->productCatalog) {
                $productDescription = $product->productCatalog->product_description;
                $brand = $product->productCatalog->brand;
                $sku = $product->productCatalog->sku;
                // Use product_name from catalog as compatible model (it contains the motorcycle model)
                $compatibility = $product->productCatalog->product_name;
            } else {
                // Fallback to Product data if no catalog relationship
                $productDescription = $product->description ?? $product->product_name ?? $product->name;
                $brand = $product->brand;
                $sku = $product->sku;
                $compatibility = $product->compatibility;
            }
            
            // Get warehouse stocks for this product
            $warehouseStocks = $product->warehouseStocks ?? [];
            
            // Distribute based on actual warehouse stocks
            foreach ($warehouseStocks as $stock) {
                if ($stock->warehouse === 'SHOP') {
                    continue; // Skip SHOP products
                }
                
                $warehouseName = $stock->warehouse;
                $warehouseIndex = $warehouseNameToIndex[$warehouseName] ?? -1;
                
                if ($warehouseIndex >= 0 && $stock->quantity > 0) {
                    $warehouseAssignments[$warehouseIndex]->push((object) [
                        'id' => $product->id,
                        'sku' => $sku,
                        'name' => $productDescription,
                        'description' => $productDescription,
                        'brand' => $brand,
                        'compatible_model' => $compatibility,
                        'stock_quantity' => $stock->quantity,
                        'unit_price' => $product->unit_price ?? 0,
                        'category' => $productDescription,
                    ]);
                }
            }
        }

        // Build a catalog lookup map for server-side shelf product enrichment.
        // Keyed by: lowercase SKU, lowercase product_description, lowercase product_name (compatible model).
        $catalogBySku         = [];  // sku_lower => catalog data
        $catalogByDescription = [];  // description_lower => catalog data (for name-based fallback)
        $catalogByCompatible  = [];  // compatible_model_lower => catalog data (for name-based fallback)

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
                if ($cat->sku) {
                    $catalogBySku[strtolower(trim($cat->sku))] = $entry;
                }
                if ($cat->product_description) {
                    $catalogByDescription[strtolower(trim($cat->product_description))] = $entry;
                }
                if ($cat->product_name) {
                    $catalogByCompatible[strtolower(trim($cat->product_name))] = $entry;
                }
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
                if ($sku) {
                    $catalogBySku[strtolower(trim($sku))] = $entry;
                }
            }
        }

        /**
         * Enrich a single stored shelf-product array with full catalog data.
         * Priority: exact SKU match → partial SKU match → name matches compatible_model → name matches description.
         */
        $enrichProduct = function (array $storedProduct) use ($catalogBySku, $catalogByDescription, $catalogByCompatible): array {
            $storedSku  = strtolower(trim($storedProduct['sku'] ?? ''));
            $storedName = strtolower(trim($storedProduct['name'] ?? $storedProduct['description'] ?? ''));

            $cat = null;

            // 1. Exact SKU match
            if ($storedSku && isset($catalogBySku[$storedSku])) {
                $cat = $catalogBySku[$storedSku];
            }

            // 2. Partial SKU match (catalog SKU contains stored SKU or vice-versa)
            if (!$cat && $storedSku && strlen($storedSku) > 3) {
                foreach ($catalogBySku as $catSku => $entry) {
                    if (str_contains($catSku, $storedSku) || str_contains($storedSku, $catSku)) {
                        $cat = $entry;
                        break;
                    }
                }
            }

            // 3. Stored name matches a product's compatible_model (e.g. "SNIPER 150/155")
            if (!$cat && $storedName && isset($catalogByCompatible[$storedName])) {
                $cat = $catalogByCompatible[$storedName];
            }

            // 4. Stored name matches a product's description (e.g. "CALIPER")
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

        $warehousesData = $warehouses->map(function ($warehouse, $warehouseIndex) use ($warehouseAssignments, $savedShelvesByWarehouse, $enrichProduct) {
            $warehouseProducts = $warehouseAssignments[$warehouseIndex]->values();
            $chunkedProducts = $warehouseProducts->chunk(10);
            $warehouseSavedShelves = $savedShelvesByWarehouse->get($warehouse->id, collect());
            $visibleSavedShelves = $warehouseSavedShelves->filter(function ($shelf) {
                return !$shelf->archived;
            })->sortBy('slot_index');

            // Only include saved (non-archived) shelves as locations.
            // Do not show default/empty shelves — user will add shelves manually.
            $locations = $visibleSavedShelves->map(function ($shelf) use ($enrichProduct) {
                $products = $shelf->products ?? [];
                if (is_string($products)) {
                    $products = json_decode($products, true);
                }
                if (!is_array($products)) {
                    $products = [];
                }
                $enrichedProducts = array_map($enrichProduct, array_values($products));
                return [
                    'name'       => $shelf->name,
                    'products'   => $enrichedProducts,
                    'archived'   => $shelf->archived,
                    'slot_index' => $shelf->slot_index,
                ];
            })->values()->toArray();

            $archivedShelves = $warehouseSavedShelves->filter(function ($shelf) {
                return $shelf->archived;
            })->sortBy('slot_index')->map(function ($shelf) use ($enrichProduct) {
                $products = $shelf->products ?? [];
                if (is_string($products)) {
                    $products = json_decode($products, true);
                }
                if (!is_array($products)) {
                    $products = [];
                }
                $enrichedProducts = array_map($enrichProduct, array_values($products));
                return [
                    'name'       => $shelf->name,
                    'products'   => $enrichedProducts,
                    'archived'   => true,
                    'slot_index' => $shelf->slot_index,
                ];
            })->values()->toArray();

            return [
                'id'             => $warehouse->id,
                'name'           => $warehouse->name,
                'code'           => $warehouse->code,
                'locations'      => $locations,
                'archivedShelves'=> $archivedShelves,
            ];
        })->toArray();

        $pendingArrivals = StockArrivalNotice::pending()
            ->orderByDesc('arrived_at')
            ->get()
            ->map(fn ($notice) => [
                'id'                     => $notice->id,
                'product_id'             => $notice->product_id,
                'product_name'           => $notice->product_name,
                'sku'                    => $notice->sku,
                'quantity'               => $notice->quantity,
                'purchase_order_number'  => $notice->purchase_order_number,
                'supplier_name'          => $notice->supplier_name,
                'arrived_at'             => $notice->arrived_at?->format('Y-m-d H:i'),
            ])
            ->values()
            ->toArray();

        return view('warehouse_management.warehouse_manage', [
            'warehouses' => $warehousesData,
            'pendingArrivals' => $pendingArrivals,
            'products' => $products->map(function ($product) {
                // Always prioritize ProductCatalog data for correct product information
                if ($product->productCatalog) {
                    $productDescription = $product->productCatalog->product_description;
                    $brand = $product->productCatalog->brand;
                    $sku = $product->productCatalog->sku;
                    // Use product_name from catalog as compatible model (it contains the motorcycle model)
                    $compatibility = $product->productCatalog->product_name;
                } else {
                    // Fallback to Product data if no catalog relationship
                    $productDescription = $product->description ?? $product->product_name ?? $product->name;
                    $brand = $product->brand;
                    $sku = $product->sku;
                    $compatibility = $product->compatibility;
                }
                
                return [
                    'id' => $product->id,
                    'sku' => $sku,
                    'name' => $compatibility ?: $productDescription,
                    'product_name' => $compatibility ?: $productDescription,
                    'description' => $productDescription,
                    'brand' => $brand,
                    'compatible_model' => $compatibility,
                    'qty' => $product->stock_quantity ?? 1,
                    'price' => $product->unit_price ?? 0,
                ];
            }),
        ]);
    }

    public function saveShelf(Request $request)
    {
        try {
            $request->validate([
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'slot_index' => 'required|integer|min:0',
                'name' => 'required|string|max:255',
                'capacity' => 'nullable|integer|min:1|max:100',
                'products' => 'nullable|array',
                'archived' => 'nullable|boolean',
            ]);

            $warehouseId = $request->input('warehouse_id');
            $warehouse = Warehouse::findOrFail($warehouseId);

            $shelfName = $request->input('name');
            $slotIndex = $request->input('slot_index');
            $capacity = $request->input('capacity', 10);
            $duplicateShelf = WarehouseShelf::where('warehouse_id', $warehouseId)
                ->where('name', $shelfName)
                ->where('slot_index', '<>', $slotIndex)
                ->exists();

            if ($duplicateShelf) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shelf name already exists for this warehouse.',
                ], 422);
            }

            // Process product data (support existing catalog products and newly added categories/brands)
            $products = $request->input('products', []);
            $processedProducts = [];

            foreach ($products as $shelfProduct) {
                $category = trim($shelfProduct['category'] ?? $shelfProduct['description'] ?? '');
                $brand    = trim($shelfProduct['brand'] ?? '');
                $name     = trim($shelfProduct['product_name'] ?? $shelfProduct['compatible_model'] ?? $shelfProduct['name'] ?? '');
                $sku      = trim($shelfProduct['sku'] ?? '');
                $price    = floatval($shelfProduct['price'] ?? 0);
                $qty      = intval($shelfProduct['qty'] ?? $shelfProduct['stock_quantity'] ?? 1);
                if ($qty <= 0) $qty = 1;

                if (empty($category) && empty($name) && empty($sku)) {
                    continue;
                }

                $product = null;
                if (!empty($shelfProduct['id'])) {
                    $product = Product::with('productCatalog')->find($shelfProduct['id']);
                }
                if (!$product && !empty($sku)) {
                    $product = Product::with('productCatalog')->where('sku', $sku)->first();
                    if (!$product) {
                        $cat = ProductCatalog::where('sku', $sku)->first();
                        if ($cat) {
                            $product = Product::where('product_catalog_id', $cat->id)->first();
                        }
                    }
                }
                if (!$product && !empty($category) && !empty($name)) {
                    $cat = ProductCatalog::where('product_description', $category)
                        ->where('product_name', $name)
                        ->where('brand', $brand)
                        ->first();
                    if ($cat) {
                        $product = Product::where('product_catalog_id', $cat->id)->first();
                    }
                }

                if ($product) {
                    if ($product->productCatalog) {
                        $catalogUpdates = [];
                        if (!empty($category)) $catalogUpdates['product_description'] = $category;
                        if (!empty($brand))    $catalogUpdates['brand']               = $brand;
                        if (!empty($name))     $catalogUpdates['product_name']        = $name;
                        if (!empty($sku))      $catalogUpdates['sku']                 = $sku;
                        if (!empty($catalogUpdates)) {
                            $product->productCatalog->update($catalogUpdates);
                        }
                    }
                    if ($price > 0) {
                        $product->unit_price = $price;
                        $product->save();
                    }

                    $cat = $product->productCatalog;
                    $finalCategory = $cat ? $cat->product_description : ($category ?: $product->description);
                    $finalBrand    = $cat ? $cat->brand : ($brand ?: $product->brand);
                    $finalSku      = $cat ? $cat->sku : ($sku ?: $product->sku);
                    $finalName     = $cat ? $cat->product_name : ($name ?: $product->compatibility);
                    $productId     = $product->id;
                } else {
                    // Create new ProductCatalog & Product
                    if (empty($sku)) {
                        $cleanCat = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $category ?: 'ITEM'));
                        $cleanBrand = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $brand ?: 'GEN'));
                        $sku = 'KCC_' . $cleanCat . '_' . $cleanBrand . '_' . substr(uniqid(), -4);
                    }
                    $catalog = ProductCatalog::create([
                        'sku' => $sku,
                        'product_description' => $category ?: 'General',
                        'brand' => $brand ?: 'Generic',
                        'product_name' => $name ?: ($category ?: 'Item'),
                    ]);

                    $product = Product::create([
                        'product_catalog_id' => $catalog->id,
                        'sku' => $sku,
                        'name' => $category ?: 'Item',
                        'product_name' => $name ?: ($category ?: 'Item'),
                        'description' => $category ?: 'General',
                        'brand' => $brand ?: 'Generic',
                        'compatibility' => $name ?: 'Universal',
                        'unit_price' => $price,
                        'stock_quantity' => $qty,
                        'is_archived' => false,
                    ]);

                    $finalCategory = $catalog->product_description;
                    $finalBrand    = $catalog->brand;
                    $finalSku      = $catalog->sku;
                    $finalName     = $catalog->product_name;
                    $productId     = $product->id;
                }

                // Update / create warehouse stock
                ProductWarehouseStock::updateOrCreate(
                    [
                        'product_id' => $productId,
                        'warehouse' => $warehouse->name,
                    ],
                    [
                        'quantity' => $qty,
                    ]
                );

                $processedProducts[] = [
                    'id' => $productId,
                    'sku' => $finalSku,
                    'name' => $finalName ?: $finalCategory,
                    'description' => $finalCategory,
                    'brand' => $finalBrand,
                    'compatible_model' => $finalName ?: $finalCategory,
                    'qty' => $qty,
                    'stock_quantity' => $qty,
                    'price' => $price,
                    'category' => $finalCategory,
                    'product_name' => $finalName ?: $finalCategory,
                ];
            }

            $shelf = WarehouseShelf::updateOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'slot_index' => $slotIndex,
                ],
                [
                    'warehouse_code' => $warehouse->code ?: ('WH-' . $warehouseId),
                    'warehouse_index' => 0, // Legacy field, kept for compatibility
                    'sort_order' => $slotIndex,
                    'name' => $shelfName,
                    'capacity' => $capacity,
                    'products' => array_values($processedProducts),
                    'archived' => $request->boolean('archived', false),
                ]
            );

            return response()->json([
                'success' => true,
                'shelf' => $shelf,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }


    public function addProduct(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:product_catalogs,id',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'shelf_name' => 'nullable|string|max:255',
            ]);

            $product = ProductCatalog::findOrFail($request->input('product_id'));
            $quantity = (int) $request->input('quantity');
            $unitPrice = $request->input('unit_price');

            // ProductCatalog doesn't have stock_quantity, so we just return success
            // The warehouse management will track quantities in shelves

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->product_description,
                    'qty' => $quantity,
                    'price' => 0, // ProductCatalog doesn't have unit_price
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    public function assignStockArrival(Request $request, int $id)
    {
        try {
            $request->validate([
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'note'         => 'nullable|string|max:1000',
                'assigned_date' => 'nullable|date',
            ]);

            $notice = StockArrivalNotice::findOrFail($id);

            if ($notice->is_assigned) {
                return response()->json(['success' => false, 'message' => 'Already assigned.'], 422);
            }

            $warehouse = Warehouse::findOrFail($request->input('warehouse_id'));

            $notice->update([
                'is_assigned'            => true,
                'assigned_warehouse_id'  => $warehouse->id,
                'assigned_warehouse_name'=> $warehouse->name,
                'note'                   => $request->input('note'),
                'assigned_at'            => $request->input('assigned_date') ? \Carbon\Carbon::parse($request->input('assigned_date')) : now(),
                'assigned_by'            => Auth::id(),
            ]);

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    public function showTransfer($warehouseId, $slotIndex)
    {
        $shelf = WarehouseShelf::where('warehouse_id', $warehouseId)
            ->where('slot_index', $slotIndex)
            ->where('archived', false)
            ->first();

        if (!$shelf) {
            abort(404, 'Shelf not found');
        }

        // Ensure products are decoded as array
        if (is_string($shelf->products)) {
            $decoded = json_decode($shelf->products, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $shelf->products = [];
            } else {
                $shelf->products = $decoded;
            }
        } else {
            $shelf->products = $shelf->products ?? [];
        }

        // Get all shelves in the same warehouse with available space
        $allShelves = WarehouseShelf::where('warehouse_id', $warehouseId)
            ->where('archived', false)
            ->where('slot_index', '!=', $slotIndex)
            ->get();

        $availableShelves = [];
        $shelfCapacity = 10; // Assuming each shelf can hold 10 products

        foreach ($allShelves as $otherShelf) {
            // Ensure products are decoded
            if (is_string($otherShelf->products)) {
                $otherShelf->products = json_decode($otherShelf->products, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $otherShelf->products = [];
                }
            } else {
                $otherShelf->products = $otherShelf->products ?? [];
            }

            $currentOccupancy = count($otherShelf->products ?? []);
            $availableSpace = $shelfCapacity - $currentOccupancy;

            // Only show shelves with available space
            if ($availableSpace > 0) {
                $availableShelves[] = [
                    'slot_index' => $otherShelf->slot_index,
                    'name' => $otherShelf->name,
                    'current_occupancy' => $currentOccupancy,
                    'available_space' => $availableSpace,
                    'status' => $availableSpace >= 5 ? 'Available' : ($availableSpace >= 3 ? 'Almost Full' : 'Limited'),
                ];
            }
        }

        return view('warehouse_management.transfer', [
            'shelf' => $shelf,
            'shelfProducts' => array_values($shelf->products ?? []),
            'warehouseId' => $warehouseId,
            'slotIndex' => $slotIndex,
            'availableShelves' => $availableShelves,
            'shelfCapacity' => $shelfCapacity,
        ]);
    }

    public function executeTransfer(Request $request)
    {
        try {
            $request->validate([
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'source_slot_index' => 'required|integer|min:0',
                'destination_slot_index' => 'required|integer|min:0',
                'transfers' => 'required|array',
                'transfers.*.product_id' => 'nullable|integer',
                'transfers.*.product_name' => 'required|string',
                'transfers.*.sku' => 'required|string',
                'transfers.*.current_qty' => 'required|integer|min:0',
                'transfers.*.transfer_qty' => 'required|integer|min:1',
                'transfers.*.unit_price' => 'required|numeric|min:0',
            ]);

            $warehouseId = $request->input('warehouse_id');
            $sourceSlotIndex = $request->input('source_slot_index');
            $destinationSlotIndex = $request->input('destination_slot_index');
            $transfers = $request->input('transfers');

            // Get source shelf
            $sourceShelf = WarehouseShelf::where('warehouse_id', $warehouseId)
                ->where('slot_index', $sourceSlotIndex)
                ->where('archived', false)
                ->first();

            if (!$sourceShelf) {
                return response()->json(['success' => false, 'message' => 'Source shelf not found'], 404);
            }

            // Get destination shelf
            $destinationShelf = WarehouseShelf::where('warehouse_id', $warehouseId)
                ->where('slot_index', $destinationSlotIndex)
                ->where('archived', false)
                ->first();

            if (!$destinationShelf) {
                return response()->json(['success' => false, 'message' => 'Destination shelf not found'], 404);
            }

            $shelfCapacity = 10;
            $destinationProducts = $destinationShelf->products ?? [];
            $currentDestinationOccupancy = count($destinationProducts);

            // Calculate total products to transfer (number of products, not quantity)
            $totalToTransfer = count($transfers);

            // Validate destination has enough space
            if ($currentDestinationOccupancy + $totalToTransfer > $shelfCapacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Destination shelf does not have enough space. Available: ' . ($shelfCapacity - $currentDestinationOccupancy) . ' slots'
                ], 422);
            }

            // Update source shelf products
            $sourceProducts = $sourceShelf->products ?? [];
            $updatedSourceProducts = [];
            $productsToAddToDestination = [];

            foreach ($transfers as $transfer) {
                $transferSku = $transfer['sku'];
                $transferQty = $transfer['transfer_qty'];
                $currentQty = $transfer['current_qty'];

                // Find product in source shelf by SKU (since product_id may not exist)
                $foundInSource = false;
                foreach ($sourceProducts as $index => $sourceProduct) {
                    if ($sourceProduct['sku'] == $transferSku) {
                        $foundInSource = true;

                        if ($transferQty >= $currentQty) {
                            // Transfer all quantity, remove from source
                            // Don't add to updatedSourceProducts
                        } else {
                            // Partial transfer, update quantity in source
                            $sourceProduct['qty'] = $currentQty - $transferQty;
                            $updatedSourceProducts[] = $sourceProduct;
                        }

                        // Add to destination
                        $productsToAddToDestination[] = [
                            'product_id' => $sourceProduct['product_id'] ?? null,
                            'sku' => $transfer['sku'],
                            'name' => $transfer['product_name'],
                            'qty' => $transferQty,
                            'price' => $transfer['unit_price'],
                        ];

                        break;
                    }
                }

                if (!$foundInSource) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Product not found in source shelf: ' . $transfer['product_name']
                    ], 422);
                }
            }

            // Keep products that are not being transferred
            $transferredSkus = array_column($transfers, 'sku');
            foreach ($sourceProducts as $sourceProduct) {
                if (!in_array($sourceProduct['sku'], $transferredSkus)) {
                    $updatedSourceProducts[] = $sourceProduct;
                }
            }

            // Merge destination products with new transfers
            $mergedDestinationProducts = array_merge($destinationProducts, $productsToAddToDestination);

            // Update shelves
            $sourceShelf->update(['products' => array_values($updatedSourceProducts)]);
            $destinationShelf->update(['products' => array_values($mergedDestinationProducts)]);

            // Create inventory movement records
            foreach ($transfers as $transfer) {
                // Look up the Product (not ProductCatalog) — inventory_movements.product_id FK references products.id
                $product = \App\Models\Product::whereHas('productCatalog', function ($q) use ($transfer) {
                    $q->where('sku', $transfer['sku']);
                })->first();

                // Fallback: try matching directly on products.sku
                if (!$product) {
                    $product = \App\Models\Product::where('sku', $transfer['sku'])->first();
                }

                // Only create inventory movement if we have a valid products.id
                if ($product) {
                    InventoryMovement::create([
                        'product_id'      => $product->id,
                        'type'            => 'transfer',
                        'quantity_change' => -$transfer['transfer_qty'],
                        'unit_price'      => $transfer['unit_price'],
                        'supplier_name'   => 'Shelf Transfer',
                        'notes'           => "Transferred from {$sourceShelf->name} to {$destinationShelf->name}",
                        'metadata'        => [
                            'source'            => 'warehouse_management',
                            'source_shelf'      => $sourceShelf->name,
                            'destination_shelf' => $destinationShelf->name,
                            'warehouse_id'      => $warehouseId,
                        ],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Products transferred successfully',
                'redirect_url' => route('warehouse.management')
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    public function addWarehouse(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:warehouses,code',
            ]);

            $warehouse = Warehouse::create([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'warehouse' => [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                    'code' => $warehouse->code,
                    'is_active' => $warehouse->is_active,
                ],
                'message' => 'Warehouse added successfully',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    public function listWarehouses()
    {
        $warehouses = Warehouse::active()->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'warehouses' => $warehouses->map(function ($warehouse) {
                return [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                    'code' => $warehouse->code,
                ];
            }),
        ]);
    }

    public function transferShelf(Request $request)
    {
        try {
            $request->validate([
                'source_warehouse_id' => 'required|integer|exists:warehouses,id',
                'destination_warehouse_id' => 'required|integer|exists:warehouses,id|different:source_warehouse_id',
                'slot_index' => 'required|integer|min:0',
            ]);

            $sourceWarehouseId = $request->input('source_warehouse_id');
            $destinationWarehouseId = $request->input('destination_warehouse_id');
            $slotIndex = $request->input('slot_index');

            // Get source shelf
            $sourceShelf = WarehouseShelf::where('warehouse_id', $sourceWarehouseId)
                ->where('slot_index', $slotIndex)
                ->where('archived', false)
                ->first();

            if (!$sourceShelf) {
                return response()->json(['success' => false, 'message' => 'Source shelf not found'], 404);
            }

            // Get the next available slot in destination warehouse
            $nextSlot = (WarehouseShelf::where('warehouse_id', $destinationWarehouseId)->max('slot_index') ?? -1) + 1;

            // Update the shelf to move it to the new warehouse
            $sourceShelf->update([
                'warehouse_id' => $destinationWarehouseId,
                'warehouse_code' => Warehouse::findOrFail($destinationWarehouseId)->code,
                'warehouse_index' => 0, // Legacy field
                'slot_index' => $nextSlot,
                'sort_order' => $nextSlot,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shelf transferred successfully',
                'new_slot_index' => $nextSlot,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }

    /**
     * Archive a warehouse
     */
    public function archiveWarehouse($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->is_active = false;
            $warehouse->save();

            return response()->json([
                'success' => true,
                'message' => 'Warehouse archived successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive warehouse: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore an archived warehouse
     */
    public function restoreWarehouse($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->is_active = true;
            $warehouse->save();

            return response()->json([
                'success' => true,
                'message' => 'Warehouse restored successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore warehouse: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display archived warehouses
     */
    public function archivedWarehouses()
    {
        $archivedWarehouses = Warehouse::where('is_active', false)
            ->orderBy('name')
            ->get()
            ->map(function ($warehouse) {
                $warehouse->shelves_count = WarehouseShelf::where('warehouse_id', $warehouse->id)
                    ->where('archived', false)
                    ->count();
                return $warehouse;
            });

        return view('warehouse_management.archived', compact('archivedWarehouses'));
    }
}
