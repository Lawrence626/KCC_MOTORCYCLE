<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Throwable;

class WarehouseManagementController extends Controller
{
    public function index()
    {
        $products = Product::where('is_archived', false)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $warehouses = collect([
            ['name' => 'Warehouse A', 'code' => 'WH-A'],
            ['name' => 'Warehouse B', 'code' => 'WH-B'],
            ['name' => 'Warehouse C', 'code' => 'WH-C'],
        ])->map(function ($warehouse, $index) use ($products) {
            $warehouseProducts = $products->slice($index * 90, 90)->values();
            $locations = collect(range(0, 8))->map(function ($slotIndex) use ($warehouseProducts) {
                $shelfProducts = $warehouseProducts->slice($slotIndex * 10, 10)->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'qty' => $product->stock_quantity,
                        'price' => (float) $product->unit_price,
                        'category' => $product->category,
                    ];
                })->toArray();

                return [
                    'name' => 'Shelf '.($slotIndex + 1),
                    'products' => $shelfProducts,
                ];
            })->toArray();

            return array_merge($warehouse, ['locations' => $locations]);
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

    public function addProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'shelf_name' => 'nullable|string|max:255',
        ]);

        try {
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
            ], 400);
        }
    }
}
