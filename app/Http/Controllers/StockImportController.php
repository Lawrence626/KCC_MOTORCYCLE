<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\TokenMismatchException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class StockImportController extends Controller
{
    /**
     * Handle Excel file import
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            ]);

            $file = $request->file('file');

            if (!$file || !$file->isValid()) {
                throw new Exception('Uploaded file is missing or invalid.');
            }

            $tempPath = $file->getRealPath() ?: $file->getPathname();
            if (empty($tempPath)) {
                throw new Exception('Uploaded file temporary path is empty.');
            }

            $filename = $file->hashName();
            $filePath = 'imports/' . $filename;

            $stream = fopen($tempPath, 'r');
            if ($stream === false) {
                throw new Exception('Unable to open uploaded temp file for reading.');
            }

            $stored = Storage::disk('local')->put($filePath, $stream);
            if ($stored === false) {
                throw new Exception('Failed to store uploaded file.');
            }

            fclose($stream);
            $storagePath = Storage::disk('local')->path($filePath);

            // Read Excel file
            $data = $this->readExcelFile($storagePath);

            // Validate and import data
            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    // Skip empty rows
                    if (empty($row['name']) || empty($row['sku'])) {
                        continue;
                    }

                    // Check for duplicate SKU
                    if (Product::where('sku', $row['sku'])->exists()) {
                        $errors[] = "Row " . ($index + 1) . ": SKU '{$row['sku']}' already exists";
                        continue;
                    }

                    $category = !empty($row['category']) && strcasecmp(trim($row['category']), 'uncategorized') !== 0 
                        ? $row['category'] 
                        : ($row['product_name'] ?? $row['name'] ?? 'Accessories');

                    // Create product
                    $product = Product::create([
                        'name' => $row['name'],
                        'product_name' => $row['category'] ?? null,
                        'description' => $row['description'] ?? null,
                        'sku' => $row['sku'],
                        'barcode' => $row['barcode'] ?? null,
                        'category' => $category,
                        'brand' => $row['brand'] ?? null,
                        'size' => $row['size'] ?? null,
                        'color' => $row['color'] ?? null,
                        'unit_price' => (float) ($row['unit_price'] ?? 0),
                        'stock_quantity' => (int) ($row['stock_quantity'] ?? 0),
                        'reorder_level' => (int) ($row['reorder_level'] ?? 10),
                        'supplier_name' => $row['supplier_name'] ?? null,
                        'last_restock_date' => $row['last_restock_date'] ?? now(),
                        'expiry_date' => $row['expiry_date'] ?? null,
                        'is_active' => true,
                        'is_archived' => false,
                    ]);

                    if ($product->stock_quantity > 0) {
                        InventoryMovement::create([
                            'product_id' => $product->id,
                            'type' => 'import',
                            'quantity_change' => $product->stock_quantity,
                            'unit_price' => $product->unit_price,
                            'supplier_name' => $product->supplier_name,
                            'notes' => 'Initial stock imported',
                        ]);
                    }

                    $imported++;
                } catch (Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            // Clean up uploaded file
            @unlink($storagePath);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$imported} products",
                'imported' => $imported,
                'errors' => $errors,
                'debug_total_rows_processed' => count($data),
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()->all(),
            ], 422);
        } catch (TokenMismatchException $e) {
            return response()->json([
                'success' => false,
                'message' => 'CSRF token mismatch. Please refresh and try again.',
            ], 419);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Read Excel file and extract data
     */
    private function readExcelFile($filePath)
    {
        $data = [];

        try {
            // Use PhpSpreadsheet to read the file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = $worksheet->toArray();

            if (empty($rows)) {
                throw new Exception('Excel file is empty');
            }

            // Get headers from first row
            $headers = array_shift($rows);
            if (empty($headers)) {
                throw new Exception('No headers found in Excel file');
            }

            // Map headers to lowercase
            $headers = array_map(function($h) {
                return strtolower(trim($h ?? ''));
            }, $headers);

            foreach ($rows as $index => $row) {
                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $mappedRow = $this->mapRowToHeaders($headers, $row);
                if (!empty($mappedRow['name'])) {
                    $data[] = $mappedRow;
                }
            }

            return $data;

        } catch (Exception $e) {
            throw new Exception('Error reading Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Map Excel row to expected fields
     * Flexible mapping to handle various header names
     */
    private function mapRowToHeaders($headers, $row)
    {
        $mapped = [];

        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header ?? ''));
            $value = isset($row[$index]) ? trim($row[$index] ?? '') : null;

            // Skip empty values
            if (empty($value) && $value !== '0') {
                continue;
            }

            // Map BODEGA format headers
            if (in_array($header, ['bodega'])) {
                $mapped['bodega'] = $value;
            } elseif (in_array($header, ['product desc', 'product description', 'product descriptions', 'product_description', 'product_desc'])) {
                // PRODUCT DESC = Product Type/Name (PIPE, TIRES, etc.)
                $mapped['category'] = $value;
            } elseif (in_array($header, ['product name', 'product', 'motorcycle compatibility', 'motor compatibility'])) {
                // PRODUCT NAME = Motorcycle Compatibility (CLICK, BEAT, etc.)
                $mapped['name'] = $value;
            } elseif (in_array($header, ['brand'])) {
                $mapped['brand'] = $value;
            } elseif (in_array($header, ['size'])) {
                $mapped['size'] = $value;
            } elseif (in_array($header, ['color'])) {
                $mapped['color'] = $value;
            } elseif (in_array($header, ['current stock', 'current_stock'])) {
                $mapped['stock_quantity'] = (int) $value;
            } elseif (in_array($header, ['actual count', 'actual_count'])) {
                $mapped['actual_count'] = (int) $value;
            } elseif (in_array($header, ['reorder level', 'reorder_level'])) {
                $mapped['reorder_level'] = (int) $value;
            } elseif (in_array($header, ['status'])) {
                $mapped['status'] = $value;
            }

            // Standard format mappings (fallback)
            elseif (in_array($header, ['description', 'desc'])) {
                $mapped['description'] = $value;
            } elseif (in_array($header, ['sku', 'code'])) {
                $mapped['sku'] = $value;
            } elseif (in_array($header, ['barcode', 'bar code'])) {
                $mapped['barcode'] = $value;
            } elseif (in_array($header, ['category', 'cat'])) {
                $mapped['category'] = $value;
            } elseif (in_array($header, ['price', 'unit price', 'unit_price'])) {
                $mapped['unit_price'] = (float) $value;
            } elseif (in_array($header, ['quantity', 'qty', 'stock'])) {
                $mapped['stock_quantity'] = (int) $value;
            } elseif (in_array($header, ['supplier', 'supplier name', 'supplier_name'])) {
                $mapped['supplier_name'] = $value;
            } elseif (in_array($header, ['last restock', 'last restock date', 'last_restock_date'])) {
                $mapped['last_restock_date'] = $value;
            } elseif (in_array($header, ['expiry', 'expiry date', 'expiration', 'expiration date', 'expiration_date'])) {
                $mapped['expiry_date'] = $value;
            }
        }

        // Process BODEGA format data OR motorcycle compatibility format
        if (!empty($mapped['name'])) {
            // Generate SKU if not provided
            if (empty($mapped['sku'])) {
                $cleaned_name = preg_replace('/[^a-zA-Z0-9]/', '', $mapped['name']);
                $sku_base = !empty($mapped['bodega']) ? $mapped['bodega'] : (!empty($mapped['category']) ? $mapped['category'] : '');

                $sku_parts = [];
                if (!empty($sku_base)) {
                    $sku_parts[] = $sku_base;
                }
                $sku_parts[] = substr($cleaned_name, 0, 8);

                // Include size in SKU to make each variant unique
                if (!empty($mapped['size'])) {
                    $cleaned_size = preg_replace('/[^a-zA-Z0-9]/', '', $mapped['size']);
                    if (!empty($cleaned_size)) {
                        $sku_parts[] = $cleaned_size;
                    }
                }

                // Include color if no size
                if (empty($mapped['size']) && !empty($mapped['color'])) {
                    $cleaned_color = preg_replace('/[^a-zA-Z0-9]/', '', $mapped['color']);
                    if (!empty($cleaned_color)) {
                        $sku_parts[] = $cleaned_color;
                    }
                }

                $mapped['sku'] = implode('-', array_filter($sku_parts));
            }

            // Build description from available fields
            $description_parts = [];
            if (!empty($mapped['product_description'])) {
                $description_parts[] = $mapped['product_description'];
            }
            if (!empty($mapped['brand'])) {
                $description_parts[] = 'Brand: ' . $mapped['brand'];
            }
            if (!empty($mapped['size'])) {
                $description_parts[] = 'Size: ' . $mapped['size'];
            }
            if (!empty($mapped['color'])) {
                $description_parts[] = 'Color: ' . $mapped['color'];
            }

            if (!empty($description_parts)) {
                $mapped['description'] = implode(' | ', $description_parts);
            }

            // Set category if not already set
            if (empty($mapped['category'])) {
                $mapped['category'] = !empty($mapped['bodega']) ? $mapped['bodega'] : (!empty($mapped['product_name']) ? $mapped['product_name'] : (!empty($mapped['name']) ? $mapped['name'] : 'Accessories'));
            }

            // Use CURRENT STOCK or ACTUAL COUNT
            if (empty($mapped['stock_quantity']) && !empty($mapped['actual_count'])) {
                $mapped['stock_quantity'] = $mapped['actual_count'];
            }

            // Default reorder level if not set
            if (empty($mapped['reorder_level'])) {
                $mapped['reorder_level'] = 10;
            }

            // Default unit price
            if (empty($mapped['unit_price'])) {
                $mapped['unit_price'] = 0;
            }
        }

        return $mapped;
    }

    /**
     * Get import status
     */
    public function status()
    {
        $total = Product::count();
        $active = Product::where('is_active', true)->where('is_archived', false)->count();
        $archived = Product::where('is_archived', true)->count();
        $totalValue = Product::where('is_archived', false)->sum(DB::raw('stock_quantity * unit_price'));

        return response()->json([
            'total' => $total,
            'active' => $active,
            'archived' => $archived,
            'total_value' => $totalValue,
        ]);
    }

    /**
     * Get products for inventory table
     */
    public function getProducts(Request $request)
    {
        // Fetch active warehouse shelves (non-archived shelves assigned to a warehouse)
        $activeShelves = \App\Models\WarehouseShelf::where('archived', false)
            ->whereNotNull('warehouse_id')
            ->with('warehouse')
            ->get();

        $activeWarehouseLocationsByProductId = [];
        $activeWarehouseLocationsBySku = [];

        foreach ($activeShelves as $shelf) {
            $whName = $shelf->warehouse->name ?? null;
            if (!$whName) {
                continue;
            }

            $shelfProducts = is_string($shelf->products)
                ? json_decode($shelf->products, true)
                : ($shelf->products ?? []);

            if (!is_array($shelfProducts)) {
                continue;
            }

            foreach ($shelfProducts as $shelfItem) {
                if (!is_array($shelfItem)) {
                    continue;
                }

                $pId = $shelfItem['product_id'] ?? $shelfItem['id'] ?? null;
                $sku = isset($shelfItem['sku']) ? strtolower(trim($shelfItem['sku'])) : null;
                $qty = isset($shelfItem['qty']) ? (int) $shelfItem['qty'] : 1;

                if ($qty <= 0) {
                    continue; // Skip 0 quantity or empty slots
                }

                if ($pId) {
                    if (!isset($activeWarehouseLocationsByProductId[$pId])) {
                        $activeWarehouseLocationsByProductId[$pId] = [];
                    }
                    if (!in_array($whName, $activeWarehouseLocationsByProductId[$pId])) {
                        $activeWarehouseLocationsByProductId[$pId][] = $whName;
                    }
                }

                if ($sku) {
                    if (!isset($activeWarehouseLocationsBySku[$sku])) {
                        $activeWarehouseLocationsBySku[$sku] = [];
                    }
                    if (!in_array($whName, $activeWarehouseLocationsBySku[$sku])) {
                        $activeWarehouseLocationsBySku[$sku][] = $whName;
                    }
                }
            }
        }

        $query = Product::with('warehouseStocks');

        // Filter by status (exclude archived products)
        $query->where('is_archived', false);

        // Apply search filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('compatibility', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $category = trim($request->input('category'));
            $standardCategory = \App\Services\SalesCategoryService::mapToPredefinedCategory($category);
            $query->where('category', $standardCategory);
        }

        // Apply warehouse location filter
        if ($request->filled('warehouse')) {
            $warehouse = trim($request->input('warehouse'));
            if (strcasecmp($warehouse, 'shop') === 0) {
                $warehouseProductIds = [];
                foreach ($activeWarehouseLocationsByProductId as $pId => $whs) {
                    if (!in_array('Shop', $whs) && !empty($whs)) {
                        $warehouseProductIds[] = $pId;
                    }
                }
                $query->where(function ($q) use ($warehouseProductIds) {
                    $q->where('warehouse', 'Shop')
                      ->orWhere('warehouse', 'SHOP')
                      ->orWhereNull('warehouse')
                      ->orWhereNotIn('id', $warehouseProductIds);
                });
            } else {
                $matchingProductIds = [];
                foreach ($activeWarehouseLocationsByProductId as $pId => $whs) {
                    if (in_array($warehouse, $whs)) {
                        $matchingProductIds[] = $pId;
                    }
                }
                $matchingSkus = [];
                foreach ($activeWarehouseLocationsBySku as $sku => $whs) {
                    if (in_array($warehouse, $whs)) {
                        $matchingSkus[] = $sku;
                    }
                }

                $query->where(function ($q) use ($warehouse, $matchingProductIds, $matchingSkus) {
                    $q->where('warehouse', $warehouse);
                    if (!empty($matchingProductIds)) {
                        $q->orWhereIn('id', $matchingProductIds);
                    }
                    if (!empty($matchingSkus)) {
                        $q->orWhereIn('sku', $matchingSkus);
                    }
                });
            }
        }

        // Apply product name / description filter
        if ($request->filled('product_name')) {
            $productName = trim($request->input('product_name'));
            $query->where(function($q) use ($productName) {
                $q->where('product_name', $productName)
                  ->orWhere('name', 'like', "%{$productName}%")
                  ->orWhere('description', 'like', "%{$productName}%");
            });
        }

        // Apply brand filter
        if ($request->filled('brand')) {
            $brand = trim($request->input('brand'));
            $query->where('brand', $brand);
        }

        // Apply size filter
        if ($request->filled('size')) {
            $size = trim($request->input('size'));
            $query->where('size', $size);
        }

        // Apply status filter (Available, Low Stock, Out of Stock)
        if ($request->filled('status')) {
            $status = trim($request->input('status'));
            if ($status === 'active' || $status === 'available') {
                $query->where('stock_quantity', '>', DB::raw('COALESCE(reorder_level, 10)'))
                      ->where('stock_quantity', '>', 0);
            } elseif ($status === 'low') {
                $query->where('stock_quantity', '<=', DB::raw('COALESCE(reorder_level, 10)'))
                      ->where('stock_quantity', '>', 0);
            } elseif ($status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Apply restock date / date of stock filter
        $restockDate = $request->input('date_of_stock') ?: $request->input('restock_date');
        if (!empty($restockDate)) {
            $query->whereDate('last_restock_date', $restockDate);
        }

        // Apply expiry status filter
        if ($request->filled('expiry_status')) {
            $expiryStatus = trim($request->input('expiry_status'));
            if ($expiryStatus === 'expired') {
                $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now());
            } elseif ($expiryStatus === 'expiring') {
                $query->whereNotNull('expiry_date')
                      ->whereDate('expiry_date', '>=', now())
                      ->whereDate('expiry_date', '<=', now()->addDays(30));
            } elseif ($expiryStatus === 'non_expiring') {
                $query->whereNull('expiry_date');
            }
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        $formattedProducts = collect($products->items())->map(function ($product) use ($activeWarehouseLocationsByProductId, $activeWarehouseLocationsBySku) {
            $productArray = $product->toArray();
            
            $locations = [];
            if (isset($activeWarehouseLocationsByProductId[$product->id])) {
                $locations = $activeWarehouseLocationsByProductId[$product->id];
            } elseif (!empty($product->sku) && isset($activeWarehouseLocationsBySku[strtolower(trim($product->sku))])) {
                $locations = $activeWarehouseLocationsBySku[strtolower(trim($product->sku))];
            }

            $productArray['warehouse_locations'] = array_values(array_unique($locations));

            // Map location quantities from warehouseStocks
            $productArray['shop_qty'] = 0;
            $productArray['warehouse_a_qty'] = 0;
            $productArray['warehouse_b_qty'] = 0;
            $productArray['warehouse_c_qty'] = 0;
            
            if (isset($product->warehouseStocks)) {
                foreach ($product->warehouseStocks as $stock) {
                    if ($stock->warehouse === 'SHOP') $productArray['shop_qty'] = $stock->quantity;
                    elseif ($stock->warehouse === 'Warehouse A') $productArray['warehouse_a_qty'] = $stock->quantity;
                    elseif ($stock->warehouse === 'Warehouse B') $productArray['warehouse_b_qty'] = $stock->quantity;
                    elseif ($stock->warehouse === 'Warehouse C') $productArray['warehouse_c_qty'] = $stock->quantity;
                }
            }
            return $productArray;
        });

        return response()->json([
            'data' => $formattedProducts,
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    /**
     * Get product descriptions from Product Categorization module
     */
    public function getProductDescriptions()
    {
        $productDescriptions = \App\Models\ProductDescription::active()->get();
        
        $descriptions = $productDescriptions->map(function($description) {
            return [
                'id' => $description->id,
                'name' => $description->name,
                'brands' => $description->brands ?? []
            ];
        });

        return response()->json($descriptions);
    }

    /**
     * Add stock to a product
     */
    public function addStock(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'nullable|numeric|min:0',
                'supplier_name' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $product = Product::findOrFail($request->input('product_id'));

            // Update product stock
            $newQuantity = $product->stock_quantity + (int) $request->input('quantity');
            $product->stock_quantity = $newQuantity;

            // Update unit price if provided
            if ($request->has('unit_price') && $request->input('unit_price') !== null) {
                $product->unit_price = (float) $request->input('unit_price');
            }

            // Update supplier if provided
            if ($request->has('supplier_name') && !empty($request->input('supplier_name'))) {
                $product->supplier_name = $request->input('supplier_name');
            }

            // Update last restock date
            $product->last_restock_date = now();

            $product->save();

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'restock',
                'quantity_change' => (int) $request->input('quantity'),
                'unit_price' => $product->unit_price,
                'supplier_name' => $product->supplier_name,
                'notes' => $request->input('notes') ?? 'Stock restocked',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Stock updated successfully for {$product->name}",
                'product' => $product,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()->all(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stock: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update unit price for a product
     */
    public function updatePrice(Request $request, $id)
    {
        try {
            $request->validate([
                'unit_price' => 'required|numeric|min:0',
            ]);

            $product = Product::findOrFail($id);
            $previousPrice = $product->unit_price;
            $product->unit_price = (float) $request->input('unit_price');
            $product->save();

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'price_update',
                'quantity_change' => 0,
                'unit_price' => $product->unit_price,
                'notes' => "Price updated from ₱{$previousPrice} to ₱{$product->unit_price}",
                'metadata' => [
                    'old_price' => $previousPrice,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit price updated successfully',
                'product' => $product,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()->all(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update price: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get stats for dashboard
     */
    public function getStats()
    {
        $products = Product::where('is_archived', false);
        $totalItems = $products->count();
        
        // Calculate total value as sum of unit prices only (not multiplied by stock)
        $productsList = Product::where('is_archived', false)->get();
        $totalValue = $productsList->sum(function($product) {
            return ($product->unit_price ?? 0);
        });
        
        $avgPrice = $totalItems > 0 ? $totalValue / $totalItems : 0;
        $categories = Product::where('is_archived', false)->distinct('category')->count('category');

        $lowStockCount = Product::where('is_archived', false)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->where('stock_quantity', '>', 0)
            ->count();

        $activeCount = Product::where('is_archived', false)
            ->where('stock_quantity', '>', DB::raw('reorder_level'))
            ->where('stock_quantity', '>', 0)
            ->count();

        $outOfStockCount = Product::where('is_archived', false)
            ->where('stock_quantity', '<=', 0)
            ->count();

        $expiredCount = Product::where('is_archived', false)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now())
            ->count();

        $expiringSoonCount = Product::where('is_archived', false)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->count();

        $nonExpiringCount = Product::where('is_archived', false)
            ->whereNull('expiry_date')
            ->count();

        // Warehouse breakdown stats strictly from active WarehouseShelves
        $activeShelves = \App\Models\WarehouseShelf::where('archived', false)
            ->whereNotNull('warehouse_id')
            ->with('warehouse')
            ->get();

        $warehouseACount = 0;
        $warehouseBCount = 0;
        $warehouseCCount = 0;

        foreach ($activeShelves as $shelf) {
            $whName = $shelf->warehouse->name ?? null;
            if (!$whName) {
                continue;
            }

            $shelfProducts = is_string($shelf->products)
                ? json_decode($shelf->products, true)
                : ($shelf->products ?? []);

            if (!is_array($shelfProducts)) {
                continue;
            }

            foreach ($shelfProducts as $shelfItem) {
                if (is_array($shelfItem) && isset($shelfItem['qty'])) {
                    $qty = max(0, (int) $shelfItem['qty']);
                    if ($whName === 'Warehouse A') {
                        $warehouseACount += $qty;
                    } elseif ($whName === 'Warehouse B') {
                        $warehouseBCount += $qty;
                    } elseif ($whName === 'Warehouse C') {
                        $warehouseCCount += $qty;
                    }
                }
            }
        }

        $shopCount = \App\Models\Product::where('is_archived', false)
            ->where('warehouse', 'Shop')
            ->sum('stock_quantity');

        return response()->json([
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'avg_price' => $avgPrice,
            'categories' => $categories,
            'low_stock_count' => $lowStockCount,
            'active_items' => $activeCount,
            'out_of_stock_count' => $outOfStockCount,
            'expired_count' => $expiredCount,
            'expiring_soon_count' => $expiringSoonCount,
            'non_expiring_count' => $nonExpiringCount,
            'shop_count' => $shopCount,
            'warehouse_a_count' => $warehouseACount,
            'warehouse_b_count' => $warehouseBCount,
            'warehouse_c_count' => $warehouseCCount,
        ]);
    }

    /**
     * Export products as CSV applying current filters
     */
    public function getMovements(Request $request)
    {
        $movements = InventoryMovement::with('product')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'product_id' => $movement->product_id,
                    'product_name' => $movement->product->name ?? 'Unknown',
                    'sku' => $movement->product->sku ?? null,
                    'image' => $movement->product->image ?? null,
                    'type' => $movement->type,
                    'quantity_change' => $movement->quantity_change,
                    'unit_price' => $movement->unit_price,
                    'supplier_name' => $movement->supplier_name,
                    'notes' => $movement->notes,
                    'created_at' => $movement->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['data' => $movements]);
    }

    /**
     * Get all active suppliers.
     */
    public function getSuppliers()
    {
        $suppliers = Supplier::orderBy('name')
            ->where(function ($query) {
                $query->where('status', 'active')->orWhereNull('status');
            })
            ->get(['id', 'name']);

        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        }

        return response()->json([
            'success' => true,
            'suppliers' => $suppliers,
        ], 200);
    }

    /**
     * Get a single product for the edit modal.
     */
    public function getProduct($id)
    {
        $product = Product::with('suppliers:id,name')->findOrFail($id);

        if ($product->suppliers->isEmpty() && !empty($product->supplier_name)) {
            $supplierNames = array_map('trim', explode(',', $product->supplier_name));
            $matchedSuppliers = Supplier::whereIn('name', $supplierNames)->get(['id', 'name']);
            if ($matchedSuppliers->isNotEmpty()) {
                $product->setRelation('suppliers', $matchedSuppliers);
            }
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);
    }

    /**
     * Update product details.
     */
    public function updateProduct(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'product_name' => 'sometimes|nullable|string|max:255',
                'sku' => 'sometimes|nullable|string|max:255',
                'brand' => 'sometimes|nullable|string|max:255',
                'size' => 'sometimes|nullable|string|max:255',
                'color' => 'sometimes|nullable|string|max:255',
                'stock_quantity' => 'sometimes|required|integer|min:0',
                'unit_price' => 'sometimes|required|numeric|min:0',
                'supplier_name' => 'sometimes|nullable|string|max:255',
                'supplier_ids' => 'sometimes|nullable|array',
                'supplier_ids.*' => 'integer|exists:suppliers,id',
                'category' => 'sometimes|nullable|string|max:255',
                'last_restock_date' => 'sometimes|nullable|date',
                'expiry_date' => 'sometimes|nullable|date',
                'reorder_level' => 'sometimes|nullable|integer|min:0',
                'barcode' => 'sometimes|nullable|string|max:255',
                'description' => 'sometimes|nullable|string',
            ]);

            $payload = [];
            $fields = [
                'name',
                'product_name',
                'sku',
                'brand',
                'size',
                'color',
                'stock_quantity',
                'unit_price',
                'supplier_name',
                'category',
                'last_restock_date',
                'expiry_date',
                'reorder_level',
                'barcode',
                'description',
            ];

            foreach ($fields as $field) {
                if (!$request->exists($field)) {
                    continue;
                }

                $newValue = $request->input($field);

                switch ($field) {
                    case 'stock_quantity':
                    case 'reorder_level':
                        $newValue = (int) $newValue;
                        break;
                    case 'unit_price':
                        $newValue = (float) $newValue;
                        break;
                    case 'category':
                        $newValue = $newValue ?: 'uncategorized';
                        break;
                    case 'last_restock_date':
                    case 'expiry_date':
                        $newValue = $newValue === '' ? null : $newValue;
                        break;
                    default:
                        $newValue = $newValue === '' ? null : $newValue;
                        break;
                }

                if ($this->normalizeValueForComparison($product->{$field}) !== $this->normalizeValueForComparison($newValue)) {
                    $payload[$field] = $newValue;
                }
            }

            $suppliersChanged = false;
            if ($request->exists('supplier_ids')) {
                $rawSupplierIds = $request->input('supplier_ids', []);
                $supplierIds = is_array($rawSupplierIds)
                    ? array_values(array_filter(array_map('intval', $rawSupplierIds)))
                    : [];

                $currentSupplierIds = $product->suppliers()->pluck('suppliers.id')->map(fn ($id) => (int) $id)->toArray();
                sort($currentSupplierIds);
                $newSupplierIds = $supplierIds;
                sort($newSupplierIds);

                if ($currentSupplierIds !== $newSupplierIds) {
                    $suppliersChanged = true;
                    $product->suppliers()->sync($supplierIds);

                    $selectedSupplierNames = Supplier::whereIn('id', $supplierIds)
                        ->orderBy('name')
                        ->pluck('name')
                        ->implode(', ');

                    $product->supplier_name = $selectedSupplierNames ?: null;
                    $product->save();
                    unset($payload['supplier_name']);
                }
            }

            if (empty($payload) && !$suppliersChanged) {
                return response()->json([
                    'success' => true,
                    'message' => 'No changes detected.',
                    'product' => $product->load('suppliers:id,name'),
                ], 200);
            }

            if (!empty($payload)) {
                $previousPrice = $product->unit_price;
                $product->fill($payload);
                $product->save();

                if (array_key_exists('unit_price', $payload) && $previousPrice !== $product->unit_price) {
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'type' => 'price_update',
                        'quantity_change' => 0,
                        'unit_price' => $product->unit_price,
                        'notes' => "Price updated from ₱{$previousPrice} to ₱{$product->unit_price}",
                        'metadata' => [
                            'old_price' => $previousPrice,
                        ],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product->load('suppliers:id,name'),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()->toArray(),
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
            ], 400);
        }
    }

    private function normalizeValueForComparison($value)
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }

    public function exportProducts(Request $request)
    {
        $query = Product::query();
        $query->where('is_archived', false);

        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && !empty($request->input('category'))) {
            $query->where('category', $request->input('category'));
        }
        if ($request->has('product_name') && !empty($request->input('product_name'))) {
            $query->where('product_name', $request->input('product_name'));
        }
        if ($request->has('brand') && !empty($request->input('brand'))) {
            $query->where('brand', $request->input('brand'));
        }
        if ($request->has('size') && !empty($request->input('size'))) {
            $query->where('size', $request->input('size'));
        }

        $filename = 'products_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['id','name','product_name','sku','barcode','category','brand','size','color','unit_price','stock_quantity','supplier_name','last_restock_date','expiry_date'];

        $callback = function() use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            // Header row
            fputcsv($handle, $columns);

            $query->orderBy('id')->chunk(200, function($products) use ($handle) {
                foreach ($products as $p) {
                    $row = [
                        $p->id,
                        $p->name,
                        $p->product_name,
                        $p->sku,
                        $p->barcode,
                        $p->category,
                        $p->brand,
                        $p->size,
                        $p->color,
                        number_format((float)$p->unit_price, 2, '.', ''),
                        $p->stock_quantity,
                        $p->supplier_name,
                        $p->last_restock_date,
                        $p->expiry_date,
                    ];
                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Archive a product
     */
    public function archiveProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_archived = true;
            $product->save();

            // Immediately remove active inventory notifications for archived product
            app(\App\Services\InventoryAlertService::class)->removeProductAlerts($product->id);

            \Log::info('Product archived successfully', ['id' => $id, 'is_archived' => $product->is_archived]);

            return response()->json([
                'success' => true,
                'message' => 'Product archived successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to archive product', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore an archived product
     */
    public function restoreProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_archived = false;
            $product->save();

            // Sync stock alerts based on current stock level
            app(\App\Services\InventoryAlertService::class)->syncProductAlert($product);

            return response()->json([
                'success' => true,
                'message' => 'Product restored successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get archived products
     */
    public function getArchivedProducts(Request $request)
    {
        $query = Product::query()->where('is_archived', true);

        \Log::info('Fetching archived products', ['count' => $query->count()]);

        // Apply search filter
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('category') && !empty($request->input('category'))) {
            $query->where('category', $request->input('category'));
        }

        $products = $query->orderBy('updated_at', 'desc')->paginate($request->input('per_page', 10));

        \Log::info('Archived products fetched', ['total' => $products->total(), 'items_count' => count($products->items())]);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    /**
     * Permanently delete a product
     */
    public function permanentDeleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product permanently deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get location quantities for a specific product
     */
    public function getLocationQuantities($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $locationStocks = \App\Models\ProductWarehouseStock::where('product_id', $productId)->get();
            
            $quantities = [
                'SHOP' => 0,
                'Warehouse A' => 0,
                'Warehouse B' => 0,
                'Warehouse C' => 0,
            ];
            
            foreach ($locationStocks as $stock) {
                if (isset($quantities[$stock->warehouse])) {
                    $quantities[$stock->warehouse] = $stock->quantity;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $quantities,
                'total' => $product->stock_quantity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch location quantities: ' . $e->getMessage(),
            ], 404);
        }
    }
}
