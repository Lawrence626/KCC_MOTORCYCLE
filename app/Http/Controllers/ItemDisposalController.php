<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ReverseLogistics;
use Illuminate\Http\Request;

class ItemDisposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('warehouseStocks')
            ->where('expiry_date', '<=', now())
            ->where('disposal_status', '!=', 'Disposed')
            ->where('stock_quantity', '>', 0);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('disposal_status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('expiry_date', 'asc')->paginate(10)->withQueryString();

        // Auto-identify expired products as Pending if not already identified
        foreach ($products as $product) {
            if ($product->disposal_status === 'None' || $product->disposal_status === null) {
                $product->update([
                    'disposal_status' => 'Pending',
                    'disposal_date_identified' => now(),
                    'disposal_reason' => 'Expired',
                ]);
            }
        }

        // Get stats
        $stats = [
            'total' => Product::where('expiry_date', '<=', now())
                ->where('disposal_status', '!=', 'Disposed')
                ->where('stock_quantity', '>', 0)
                ->count(),
            'pending' => Product::where('disposal_status', 'Pending')->where('stock_quantity', '>', 0)->count(),
            'approved' => Product::where('disposal_status', 'Approved')->where('stock_quantity', '>', 0)->count(),
        ];

        return view('inventory.item-disposal', compact('products', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'disposal_status' => 'Approved',
        ]);

        return back()->with('success', 'Item approved for disposal.');
    }

    public function markAsDisposed(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Remove from ProductWarehouseStock
        \App\Models\ProductWarehouseStock::where('product_id', $product->id)->update(['quantity' => 0]);
        
        // Remove from ShopInventory
        \App\Models\ShopInventory::where('product_id', $product->id)->delete();
        
        // Remove from Warehouse Shelves (JSON arrays)
        $shelves = \Illuminate\Support\Facades\DB::table('warehouse_shelves')->get();
        foreach ($shelves as $shelf) {
            $products = json_decode($shelf->products ?? '[]', true);
            if (!empty($products)) {
                $newProducts = [];
                $changed = false;
                foreach ($products as $p) {
                    if (isset($p['sku']) && $p['sku'] === $product->sku) {
                        $changed = true;
                        continue; // Remove product by skipping
                    }
                    $newProducts[] = $p;
                }
                if ($changed) {
                    \Illuminate\Support\Facades\DB::table('warehouse_shelves')
                        ->where('id', $shelf->id)
                        ->update(['products' => json_encode($newProducts)]);
                }
            }
        }

        // Add inventory movement log for disposal
        \App\Models\InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity_change' => -$product->stock_quantity,
            'from_location' => 'System',
            'to_location' => 'Disposal',
            'notes' => "Item disposed. Removed physical stock from all locations.",
        ]);

        $product->update([
            'disposal_status' => 'Disposed',
            'disposal_date_disposed' => now(),
            'stock_quantity' => 0,
        ]);

        return back()->with('success', 'Item marked as disposed and physical inventory cleared.');
    }

    public function createFromReverseLogistics(Request $request, $reverseLogisticsId)
    {
        $reverseLogistics = ReverseLogistics::findOrFail($reverseLogisticsId);
        
        // Check if a product with matching SKU already exists
        $product = Product::where('sku', $reverseLogistics->sku)->first();
        
        if ($product) {
            // Update existing product with disposal information
            $product->update([
                'disposal_status' => 'Pending',
                'disposal_date_identified' => now(),
                'disposal_reason' => $reverseLogistics->return_reason,
                'expiry_date' => now(), // Set to current date since it's being disposed
            ]);
        } else {
            // Create a new product record for disposal tracking
            $product = Product::create([
                'name' => $reverseLogistics->product_name,
                'sku' => $reverseLogistics->sku,
                'stock_quantity' => $reverseLogistics->quantity,
                'disposal_status' => 'Pending',
                'disposal_date_identified' => now(),
                'disposal_reason' => $reverseLogistics->return_reason,
                'expiry_date' => now(),
                'category' => 'Reverse Logistics',
                'unit_price' => 0,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Item added to disposal module',
            'product_id' => $product->id
        ]);
    }
}
