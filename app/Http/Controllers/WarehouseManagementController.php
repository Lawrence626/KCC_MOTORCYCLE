<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\WarehouseShelf;
use Illuminate\Http\Request;
use Throwable;

class WarehouseManagementController extends Controller
{
    public function index()
    {
        $products = Product::where('is_archived', false)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $totalProducts = $products->count();
        $productsPerWarehouse = ceil($totalProducts / 3);
        $shelvesPerWarehouse = max(1, ceil($productsPerWarehouse / 10));
        $savedShelvesByWarehouse = WarehouseShelf::all()
            ->groupBy('warehouse_index')
            ->map(function ($group) {
                return $group->keyBy('slot_index');
            });

        // Distribute product quantities across 3 warehouses by splitting each product's stock
        $warehouseAssignments = [collect(), collect(), collect()];
        foreach ($products as $product) {
            $qty = (int) $product->stock_quantity;
            if ($qty <= 0) {
                // still include product as zero in first warehouse to keep listing consistent
                $warehouseAssignments[0]->push($product);
                continue;
            }
            $base = intdiv($qty, 3);
            $rem = $qty % 3;
            for ($i = 0; $i < 3; $i++) {
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

        $warehouses = collect([
            ['name' => 'Warehouse A', 'code' => 'WH-A'],
            ['name' => 'Warehouse B', 'code' => 'WH-B'],
            ['name' => 'Warehouse C', 'code' => 'WH-C'],
        ])->map(function ($warehouse, $warehouseIndex) use ($warehouseAssignments, $savedShelvesByWarehouse) {
            $warehouseProducts = $warehouseAssignments[$warehouseIndex]->values();
            $chunkedProducts = $warehouseProducts->chunk(10);
            $warehouseSavedShelves = $savedShelvesByWarehouse->get($warehouseIndex, collect());
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

            return array_merge($warehouse, ['locations' => $locations, 'archivedShelves' => $archivedShelves]);
        })->toArray();

        return view('warehouse_management.warehouse_manage', [
            'warehouses' => $warehouses,
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
                'warehouse_index' => 'required|integer|min:0|max:2',
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

$warehouseIndex = $request->input('warehouse_index');
        $warehouseCodes = ['WH-A', 'WH-B', 'WH-C'];
        $warehouseCode = $warehouseCodes[$warehouseIndex] ?? 'WH-A';

$shelfName = $request->input('name');
            $slotIndex = $request->input('slot_index');
            $duplicateShelf = WarehouseShelf::where('warehouse_index', $warehouseIndex)
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
                    'warehouse_index' => $warehouseIndex,
                    'slot_index' => $slotIndex,
                ],
                [
                    'warehouse_code' => $warehouseCode,
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
}
