<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
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
        $products = Product::where('is_archived', false)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $warehouses = Warehouse::active()->orderBy('name')->get();
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

        // Distribute product quantities across warehouses by splitting each product's stock
        $warehouseAssignments = [];
        for ($i = 0; $i < $totalWarehouses; $i++) {
            $warehouseAssignments[] = collect();
        }

        foreach ($products as $product) {
            $qty = (int) $product->stock_quantity;
            if ($qty <= 0) {
                // still include product as zero in first warehouse to keep listing consistent
                $warehouseAssignments[0]->push($product);
                continue;
            }
            $base = intdiv($qty, $totalWarehouses);
            $rem = $qty % $totalWarehouses;
            for ($i = 0; $i < $totalWarehouses; $i++) {
                $assignQty = $base + ($i < $rem ? 1 : 0);
                if ($assignQty > 0) {
                    $warehouseAssignments[$i]->push((object) [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'stock_quantity' => $assignQty,
                        'unit_price' => $product->unit_price,
                        'category' => $product->category,
                    ]);
                }
            }
        }

        $warehousesData = $warehouses->map(function ($warehouse, $warehouseIndex) use ($warehouseAssignments, $savedShelvesByWarehouse) {
            $warehouseProducts = $warehouseAssignments[$warehouseIndex]->values();
            $chunkedProducts = $warehouseProducts->chunk(10);
            $warehouseSavedShelves = $savedShelvesByWarehouse->get($warehouse->id, collect());
            $visibleSavedShelves = $warehouseSavedShelves->filter(function ($shelf) {
                return !$shelf->archived;
            })->sortBy('slot_index');

            // Only include saved (non-archived) shelves as locations.
            // Do not show default/empty shelves — user will add shelves manually.
            $locations = $visibleSavedShelves->map(function ($shelf) {
                return [
                    'name' => $shelf->name,
                    'products' => array_values($shelf->products ?? []),
                    'archived' => $shelf->archived,
                    'slot_index' => $shelf->slot_index,
                ];
            })->values()->toArray();

            $archivedShelves = $warehouseSavedShelves->filter(function ($shelf) {
                return $shelf->archived;
            })->sortBy('slot_index')->map(function ($shelf) {
                return [
                    'name' => $shelf->name,
                    'products' => array_values($shelf->products ?? []),
                    'archived' => true,
                    'slot_index' => $shelf->slot_index,
                ];
            })->values()->toArray();

            return [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'code' => $warehouse->code,
                'locations' => $locations,
                'archivedShelves' => $archivedShelves,
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
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'qty' => $product->stock_quantity,
                    'price' => (float) $product->unit_price,
                    'category' => $product->category,
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
                'products' => 'nullable|array',
                    'products.*.product_id' => 'nullable|integer|exists:products,id',
                    'products.*.sku' => 'nullable|string|max:255',
                    'products.*.name' => 'nullable|string|max:255',
                    'products.*.qty' => 'nullable|integer|min:0',
                    'products.*.price' => 'nullable|numeric|min:0',
                'archived' => 'nullable|boolean',
            ]);

            $warehouseId = $request->input('warehouse_id');
            $warehouse = Warehouse::findOrFail($warehouseId);

            $shelfName = $request->input('name');
            $slotIndex = $request->input('slot_index');
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

            $shelf = WarehouseShelf::updateOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'slot_index' => $slotIndex,
                ],
                [
                    'warehouse_code' => $warehouse->code,
                    'warehouse_index' => 0, // Legacy field, kept for compatibility
                    'sort_order' => $slotIndex,
                    'name' => $shelfName,
                    'products' => array_values($request->input('products', [])),
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
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'shelf_name' => 'nullable|string|max:255',
            ]);

            $product = Product::findOrFail($request->input('product_id'));
            $quantity = (int) $request->input('quantity');
            $unitPrice = $request->input('unit_price');

            $product->stock_quantity += $quantity;
            if ($unitPrice !== null && $unitPrice !== '') {
                $product->unit_price = (float) $unitPrice;
            }
            $product->save();

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'restock',
                'quantity_change' => $quantity,
                'unit_price' => $product->unit_price,
                'supplier_name' => $request->input('shelf_name') ?: 'Warehouse Management',
                'notes' => $request->input('notes') ?: 'Added via warehouse management',
                'metadata' => [
                    'source' => 'warehouse_management',
                ],
            ]);

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'qty' => $product->stock_quantity,
                    'price' => (float) $product->unit_price,
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
                // Try to find the actual product by SKU to get real product_id
                $product = \App\Models\Product::where('sku', $transfer['sku'])->first();
                $actualProductId = $product ? $product->id : null;

                // Only create inventory movement if we have a valid product_id
                if ($actualProductId) {
                    InventoryMovement::create([
                        'product_id' => $actualProductId,
                        'type' => 'transfer',
                        'quantity_change' => -$transfer['transfer_qty'],
                        'unit_price' => $transfer['unit_price'],
                        'supplier_name' => 'Shelf Transfer',
                        'notes' => "Transferred from {$sourceShelf->name} to {$destinationShelf->name}",
                        'metadata' => [
                            'source' => 'warehouse_management',
                            'source_shelf' => $sourceShelf->name,
                            'destination_shelf' => $destinationShelf->name,
                            'warehouse_id' => $warehouseId,
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
