<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductCatalogRequest;
use App\Http\Requests\UpdateProductCatalogRequest;
use App\Models\MotorcycleModel;
use App\Models\ProductCatalog;
use App\Models\ProductDescription;
use App\Models\Warehouse;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductCatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductCatalog::with(['motorcycleModels', 'stockProduct'])
            ->when($request->search, function ($q) use ($request) {
                $q->search($request->search);
            })
            ->when($request->warehouse, function ($q) use ($request) {
                $q->where('warehouse', $request->warehouse);
            })
            ->when($request->product_description, function ($q) use ($request) {
                $q->byProductDescription($request->product_description);
            })
            ->when($request->brand, function ($q) use ($request) {
                $q->byBrand($request->brand);
            })
            ->when($request->size, function ($q) use ($request) {
                $q->where('size', $request->size);
            })
            ->when($request->motorcycle, function ($q) use ($request) {
                $q->byMotorcycle($request->motorcycle);
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->active();
                } elseif ($request->status === 'inactive') {
                    $q->inactive();
                }
            });

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->per_page ?? 10);

        $productDescriptions = ProductDescription::active()->get();

        // Get brands based on selected product description
        if ($request->product_description) {
            $selectedDescription = ProductDescription::where('name', $request->product_description)->first();
            $brands = $selectedDescription ? $selectedDescription->brands : [];
        } else {
            $brands = ProductCatalog::distinct()->pluck('brand')->filter()->toArray();
        }

        $motorcycles = MotorcycleModel::active()->get()->groupBy('brand');

        return view('product-catalog.index', compact('products', 'productDescriptions', 'brands', 'motorcycles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productDescriptions = ProductDescription::active()->get();
        $motorcycles = MotorcycleModel::active()->get()->groupBy('brand');
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return view('product-catalog.create', compact('productDescriptions', 'motorcycles', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCatalogRequest $request)
    {
        DB::beginTransaction();
        try {
            // Set reorder level to 100 only for JOURNEY brand TIRE products
            $reorderLevel = ($request->product_description === 'TIRE' && strtoupper(trim($request->brand)) === 'JOURNEY')
                ? 100
                : ($request->reorder_level ?? 10);

            $product = ProductCatalog::create([
                'product_name' => $request->product_name,
                'brand' => $request->brand,
                'product_description' => $request->product_description,
                'sku' => $request->sku,
                'description' => $request->description,
                'size' => $request->size,
                'color' => $request->color,
                'stock_quantity' => $request->stock_quantity ?? 0,
                'reorder_level' => $reorderLevel,
                'warehouse' => $request->warehouse,
                'status' => $request->status,
                'manufacturing_date' => $request->manufacturing_date,
                'batch_lot_number' => $request->batch_lot_number,
                'expiration_date' => $request->expiration_date,
            ]);

            // Generate QR Code
            $qrCodePath = $this->generateQRCode($product->sku);
            $product->update(['qr_code_path' => $qrCodePath]);

            // Attach motorcycle models if not general
            if (!$request->boolean('is_general') && $request->filled('motorcycle_models')) {
                $product->motorcycleModels()->attach($request->motorcycle_models);
            }

            DB::commit();
            return redirect()->route('product-catalog.index')
                ->with('success', 'Product added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCatalog $productCatalog)
    {
        $productCatalog->load(['motorcycleModels']);
        return view('product-catalog.show', compact('productCatalog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCatalog $productCatalog)
    {
        $productCatalog->load('motorcycleModels');
        $productDescriptions = ProductDescription::active()->get();
        $motorcycles = MotorcycleModel::active()->get()->groupBy('brand');
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return view('product-catalog.edit', compact('productCatalog', 'productDescriptions', 'motorcycles', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCatalogRequest $request, ProductCatalog $productCatalog)
    {
        DB::beginTransaction();
        try {
            // Set reorder level to 100 only for JOURNEY brand TIRE products
            $reorderLevel = ($request->product_description === 'TIRE' && strtoupper(trim($request->brand)) === 'JOURNEY')
                ? 100
                : ($request->reorder_level ?? 10);

            $productCatalog->update([
                'product_name' => $request->product_name,
                'brand' => $request->brand,
                'product_description' => $request->product_description,
                'sku' => $request->sku,
                'description' => $request->description,
                'size' => $request->size,
                'color' => $request->color,
                'stock_quantity' => $request->stock_quantity ?? 0,
                'reorder_level' => $reorderLevel,
                'warehouse' => $request->warehouse,
                'status' => $request->status,
                'manufacturing_date' => $request->manufacturing_date,
                'batch_lot_number' => $request->batch_lot_number,
                'expiration_date' => $request->expiration_date,
            ]);

            if ($request->boolean('is_general')) {
                // General product has no specific motorcycle models
                $productCatalog->motorcycleModels()->detach();
            } else {
                // Sync motorcycle models - handle both array and comma-separated string
                $motorcycleModels = $request->motorcycle_models;
                if (is_string($motorcycleModels)) {
                    $motorcycleModels = array_filter(explode(',', $motorcycleModels), function($id) {
                        return !empty(trim($id));
                    });
                }
                
                // Separate existing model IDs from new model names
                $existingModelIds = [];
                $newModelNames = [];
                
                if (is_array($motorcycleModels)) {
                    foreach ($motorcycleModels as $item) {
                        $item = trim($item);
                        if (is_numeric($item)) {
                            $existingModelIds[] = (int) $item;
                        } elseif (!empty($item)) {
                            $newModelNames[] = $item;
                        }
                    }
                }
                
                // Sync existing models by ID
                $productCatalog->motorcycleModels()->sync($existingModelIds);
                
                // Add new models by name (create if not exists)
                foreach ($newModelNames as $modelName) {
                    // Try to find existing model by name
                    $motorcycleModel = MotorcycleModel::where('model_name', $modelName)
                        ->orWhere('brand', $modelName)
                        ->first();
                    
                    if (!$motorcycleModel) {
                        // Create new model
                        $motorcycleModel = MotorcycleModel::create([
                            'brand' => 'Custom',
                            'model_name' => $modelName,
                            'is_active' => true
                        ]);
                    }
                    
                    // Attach if not already attached
                    if (!$productCatalog->motorcycleModels()->where('motorcycle_models.id', $motorcycleModel->id)->exists()) {
                        $productCatalog->motorcycleModels()->attach($motorcycleModel->id);
                    }
                }
            }

            DB::commit();
            return redirect()->route('product-catalog.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCatalog $productCatalog)
    {
        try {
            $productCatalog->delete(); // soft delete
            return redirect()->route('product-catalog.index')
                ->with('success', 'Product moved to Trash successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Generate SKU: KCC_{PRODUCT_DESC}_{BRAND}_{SIZE?}_{SEQ}
     * SEQ is unique per (product_description, brand, size) group.
     */
    public function generateSKU(Request $request)
    {
        $productDescriptionName = $request->product_description;
        $brand = strtoupper(trim($request->brand ?? ''));
        $rawSize = trim($request->size ?? '');
        $productName = trim($request->product_name ?? '');

        if (!$productDescriptionName) {
            return response()->json(['error' => 'Product description is required'], 422);
        }

        // Slugify: spaces → underscores, uppercase
        $descSlug  = strtoupper(str_replace(' ', '_', trim($productDescriptionName)));
        $brandSlug = $brand !== '' ? str_replace(' ', '_', $brand) : 'UNKNOWN';

        // Normalize size / liter slug
        $sizeSlug = self::normalizeSizeSlug($rawSize, $productDescriptionName, $productName);

        // Count existing (non-deleted) products with same desc + brand (+ size if specified)
        $query = ProductCatalog::where('product_description', $productDescriptionName)
            ->where('brand', $request->brand ?? '');

        if ($sizeSlug !== '') {
            $query->where(function ($q) use ($rawSize, $sizeSlug) {
                if ($rawSize !== '') {
                    $q->where('size', $rawSize);
                }
                $q->orWhere('sku', 'like', "%_{$sizeSlug}_%");
            });
        }

        $count = $query->count();
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        if ($sizeSlug !== '') {
            $sku = "KCC_{$descSlug}_{$brandSlug}_{$sizeSlug}_{$seq}";
        } else {
            $sku = "KCC_{$descSlug}_{$brandSlug}_{$seq}";
        }

        return response()->json([
            'sku' => $sku,
            'size_slug' => $sizeSlug,
        ]);
    }

    /**
     * Normalize size / liter / volume into a clean uppercase SKU slug.
     */
    public static function normalizeSizeSlug(?string $size, string $productDescription = '', string $productName = ''): string
    {
        $size = trim($size ?? '');

        // If size is empty, try to auto-extract from product name (especially for oils/liquids)
        if ($size === '' && !empty($productName)) {
            if (preg_match('/\((\d+(?:\.\d+)?\s*(?:ml|l|liter|liters|litre|litres))\)/i', $productName, $matches)) {
                $size = $matches[1];
            } elseif (preg_match('/\b(\d+(?:\.\d+)?)\s*(ml|l|liter|liters|litre|litres)\b/i', $productName, $matches)) {
                $size = $matches[1] . $matches[2];
            }
        }

        if ($size === '') {
            return '';
        }

        $upper = strtoupper(trim($size));

        // Handle Liter variations (e.g. 1L, 1 L, 1 LITER, 1.0L)
        if (preg_match('/^(\d+(?:\.\d+)?)\s*(?:L|LITER|LITERS|LITRE|LITRES)$/i', $upper, $m)) {
            $num = (float) $m[1];
            if ($num == 1.0) {
                return '1L';
            }
            if ($num == 0.8) {
                return '800ML';
            }
            $formattedNum = (string) $num;
            return $formattedNum . 'L';
        }

        // Handle mL variations (e.g. 800ML, 800 ML, 1000ML -> 1L)
        if (preg_match('/^(\d+)\s*(?:ML|MILLILITER|MILLILITERS)$/i', $upper, $m)) {
            $ml = (int) $m[1];
            if ($ml === 1000) {
                return '1L';
            }
            return $ml . 'ML';
        }

        // Handle numeric-only input for oil/fluid products
        $isOil = preg_match('/oil|fluid|coolant|cleaner|sealant|lubricant/i', $productDescription . ' ' . $productName);
        if ($isOil && is_numeric($size)) {
            $val = (float) $size;
            if ($val == 1) {
                return '1L';
            }
            if ($val == 800 || $val == 0.8) {
                return '800ML';
            }
            if ($val == 120) {
                return '120ML';
            }
            if ($val == 500) {
                return '500ML';
            }
            if ($val >= 50) {
                return ((int) $val) . 'ML';
            }
            return ((string) $val) . 'L';
        }

        // General size normalization (replace spaces, slashes, dashes with underscores, keep alphanumeric & dots)
        $slug = preg_replace('/[^A-Za-z0-9_\.]/', '', str_replace([' ', '/', '-'], '_', $upper));
        return trim($slug, '_');
    }

    /**
     * Download QR Code
     */
    public function downloadQR(ProductCatalog $productCatalog)
    {
        if (!$productCatalog->qr_code_path || !Storage::exists($productCatalog->qr_code_path)) {
            return redirect()->back()->with('error', 'QR Code not found.');
        }

        return Storage::download($productCatalog->qr_code_path);
    }

    /**
     * Print QR Code
     */
    public function printQR(ProductCatalog $productCatalog)
    {
        if (!$productCatalog->qr_code_path || !Storage::exists($productCatalog->qr_code_path)) {
            return redirect()->back()->with('error', 'QR Code not found.');
        }

        $qrCodeData = base64_encode(Storage::get($productCatalog->qr_code_path));
        return view('product-catalog.print-qr', compact('productCatalog', 'qrCodeData'));
    }

    /**
     * Regenerate QR Code
     */
    public function regenerateQR(ProductCatalog $productCatalog)
    {
        try {
            // Delete old QR code
            if ($productCatalog->qr_code_path && Storage::exists($productCatalog->qr_code_path)) {
                Storage::delete($productCatalog->qr_code_path);
            }

            // Generate new QR code
            $qrCodePath = $this->generateQRCode($productCatalog->sku);
            $productCatalog->update(['qr_code_path' => $qrCodePath]);

            return redirect()->back()->with('success', 'QR Code regenerated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to regenerate QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR Code and save to storage
     */
    private function generateQRCode($sku)
    {
        $filename = "qrcodes/{$sku}.png";
        $path = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $qrCode = new QrCode($sku);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($path);

        return $filename;
    }

    /**
     * Bulk soft-delete products
     */
    public function bulkDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No products selected for deletion.');
        }

        try {
            $count = ProductCatalog::whereIn('id', $ids)->delete(); // soft delete
            return redirect()->back()->with('success', $count . ' product(s) moved to Trash successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete products: ' . $e->getMessage());
        }
    }

    /**
     * Show trashed (soft-deleted) products as JSON for the Trash modal
     */
    public function trash()
    {
        $trashed = ProductCatalog::onlyTrashed()
            ->select('id', 'brand', 'product_description', 'sku', 'warehouse', 'deleted_at')
            ->orderByDesc('deleted_at')
            ->get();

        return response()->json($trashed);
    }

    /**
     * Restore a single soft-deleted product
     */
    public function restore($id)
    {
        try {
            ProductCatalog::onlyTrashed()->findOrFail($id)->restore();
            return response()->json(['success' => true, 'message' => 'Product restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk restore soft-deleted products
     */
    public function bulkRestore(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected.'], 422);
        }

        try {
            $count = ProductCatalog::onlyTrashed()->whereIn('id', $ids)->restore();
            return response()->json(['success' => true, 'message' => $count . ' product(s) restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Permanently delete from trash
     */
    public function forceDelete(Request $request)
    {
        $ids = array_filter(explode(',', $request->input('ids', '')));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected.'], 422);
        }

        try {
            $products = ProductCatalog::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($products as $product) {
                if ($product->qr_code_path && Storage::exists($product->qr_code_path)) {
                    Storage::delete($product->qr_code_path);
                }
                $product->forceDelete();
            }
            return response()->json(['success' => true, 'message' => count($ids) . ' product(s) permanently deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
