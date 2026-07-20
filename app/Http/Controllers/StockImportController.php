<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
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

                    // Create product
                    $product = Product::create([
                        'name' => $row['name'],
                        'product_name' => $row['category'] ?? null,
                        'description' => $row['description'] ?? null,
                        'sku' => $row['sku'],
                        'barcode' => $row['barcode'] ?? null,
                        'category' => $row['category'] ?? 'Uncategorized',
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
                $mapped['category'] = !empty($mapped['bodega']) ? $mapped['bodega'] : 'Uncategorized';
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
        $query = Product::with('warehouseStocks');

        // Filter by status
        $query->where('is_archived', false);

        // Apply search filter
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhere('compatibility', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('category') && !empty($request->input('category'))) {
            $category = $request->input('category');
            $query->where('category', $category);
        }

        // Apply warehouse filter
        if ($request->has('warehouse') && !empty($request->input('warehouse'))) {
            $warehouse = $request->input('warehouse');
            $query->whereHas('warehouseStocks', function($q) use ($warehouse) {
                $q->where('warehouse', $warehouse)
                  ->where('quantity', '>', 0);
            });
        }

        // Apply product name filter (actually filters by description/category)
        if ($request->has('product_name') && !empty($request->input('product_name'))) {
            $productName = $request->input('product_name');
            $query->where(function($q) use ($productName) {
                $q->where('description', 'like', "%{$productName}%")
                  ->orWhere('category', 'like', "%{$productName}%");
            });
        }

        // Apply brand filter
        if ($request->has('brand') && !empty($request->input('brand'))) {
            $brand = $request->input('brand');
            $query->where('brand', 'like', "%{$brand}%");
        }

        // Apply size filter
        if ($request->has('size') && !empty($request->input('size'))) {
            $size = $request->input('size');
            $query->where('size', $size);
        }

        // Apply status filter for inventory monitoring
        if ($request->has('status') && !empty($request->input('status'))) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('stock_quantity', '>', DB::raw('reorder_level'))
                      ->where('stock_quantity', '>', 0);
            } elseif ($status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'reorder_level')
                      ->where('stock_quantity', '>', 0);
            } elseif ($status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Apply restock date filter
        if ($request->has('restock_date') && !empty($request->input('restock_date'))) {
            $query->whereDate('last_restock_date', $request->input('restock_date'));
        }

        // Apply date_of_stock filter (alias for restock_date)
        if ($request->has('date_of_stock') && !empty($request->input('date_of_stock'))) {
            $query->whereDate('last_restock_date', $request->input('date_of_stock'));
        }

        // Apply expiry status filter
        if ($request->has('expiry_status') && !empty($request->input('expiry_status'))) {
            $expiryStatus = $request->input('expiry_status');
            if ($expiryStatus === 'expired') {
                $query->whereDate('expiry_date', '<', now());
            } elseif ($expiryStatus === 'expiring') {
                $query->whereDate('expiry_date', '>=', now())
                      ->whereDate('expiry_date', '<=', now()->addDays(30));
            } elseif ($expiryStatus === 'non_expiring') {
                $query->whereNull('expiry_date');
            }
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        $formattedProducts = collect($products->items())->map(function ($product) {
            $productArray = $product->toArray();
            
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

        // Warehouse breakdown stats
        $shopCount = \App\Models\ProductWarehouseStock::where('warehouse', 'SHOP')
            ->where('quantity', '>', 0)
            ->whereHas('product', function($q) {
                $q->where('is_archived', false);
            })
            ->sum('quantity');

        $warehouseACount = \App\Models\ProductWarehouseStock::where('warehouse', 'Warehouse A')
            ->where('quantity', '>', 0)
            ->whereHas('product', function($q) {
                $q->where('is_archived', false);
            })
            ->sum('quantity');

        $warehouseBCount = \App\Models\ProductWarehouseStock::where('warehouse', 'Warehouse B')
            ->where('quantity', '>', 0)
            ->whereHas('product', function($q) {
                $q->where('is_archived', false);
            })
            ->sum('quantity');

        $warehouseCCount = \App\Models\ProductWarehouseStock::where('warehouse', 'Warehouse C')
            ->where('quantity', '>', 0)
            ->whereHas('product', function($q) {
                $q->where('is_archived', false);
            })
            ->sum('quantity');

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
                    'product_name' => $movement->product->name ?? 'Unknown',
                    'sku' => $movement->product->sku ?? null,
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
     * Get a single product for the edit modal.
     */
    public function getProduct($id)
    {
        $product = Product::findOrFail($id);

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

            if (empty($payload)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No changes detected.',
                    'product' => $product,
                ], 200);
            }

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

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product,
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
