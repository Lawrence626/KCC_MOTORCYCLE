<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\POSTransaction;
use App\Http\Controllers\ShopInventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSTransactionController extends Controller
{
    /**
     * Validate stock for items in cart before payment.
     */
    public function validateStock(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $productIds = collect($validated['items'])->pluck('id')->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $results = [];
        $isValid = true;
        $errors = [];

        foreach ($validated['items'] as $item) {
            $productId = $item['id'];
            $requestedQty = (int) $item['quantity'];
            $product = $products->get($productId);

            if (!$product || $product->is_archived) {
                $isValid = false;
                $name = $product ? $product->name : "Product #{$productId}";
                $errors[] = "{$name} is no longer available.";
                $results[] = [
                    'id' => $productId,
                    'name' => $name,
                    'requested_quantity' => $requestedQty,
                    'current_stock' => 0,
                    'available' => 0,
                    'is_out_of_stock' => true,
                    'is_insufficient' => true,
                    'message' => "{$name} is no longer available.",
                ];
                continue;
            }

            $currentStock = (int) $product->stock_quantity;
            $isOutOfStock = $currentStock <= 0;
            $isInsufficient = $requestedQty > $currentStock;

            if ($isInsufficient || $isOutOfStock) {
                $isValid = false;
                $msg = $isOutOfStock
                    ? "Product '{$product->name}' is out of stock."
                    : "Insufficient stock. Only {$currentStock} item(s) available for '{$product->name}'.";
                $errors[] = $msg;
            }

            $results[] = [
                'id' => $productId,
                'name' => $product->name,
                'sku' => $product->sku,
                'requested_quantity' => $requestedQty,
                'current_stock' => $currentStock,
                'available' => max(0, $currentStock),
                'is_out_of_stock' => $isOutOfStock,
                'is_insufficient' => $isInsufficient,
                'message' => ($isInsufficient || $isOutOfStock)
                    ? ($isOutOfStock ? "Product '{$product->name}' is out of stock." : "Insufficient stock. Only {$currentStock} item(s) available.")
                    : null,
            ];
        }

        return response()->json([
            'valid' => $isValid,
            'items' => $results,
            'errors' => $errors,
        ]);
    }

    /**
     * Store a new POS transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:pos_transactions',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.name' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric',
            'items.*.price' => 'nullable|numeric',
            'items.*.cost_price' => 'nullable|numeric',
            'items.*.category' => 'nullable|string',
            'items.*.sku' => 'nullable|string',
            'items.*.compatibility' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'services_total' => 'required|numeric|min:0',
            'extra_charge' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,qr',
        ]);

        DB::beginTransaction();
        try {
            $productIds = collect($validated['items'])->pluck('id')->all();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $insufficientItems = [];

            foreach ($validated['items'] as $item) {
                $productId = $item['id'];
                $requestedQty = (int) $item['quantity'];
                $product = $products->get($productId);

                if (!$product || $product->is_archived) {
                    $productName = $product ? $product->name : "Product #{$productId}";
                    $insufficientItems[] = "{$productName} is unavailable or archived.";
                    continue;
                }

                if ($product->stock_quantity < $requestedQty) {
                    $available = max(0, (int) $product->stock_quantity);
                    if ($available <= 0) {
                        $insufficientItems[] = "Product '{$product->name}' is out of stock (0 available).";
                    } else {
                        $insufficientItems[] = "Insufficient stock for '{$product->name}'. Only {$available} available (requested: {$requestedQty}).";
                    }
                }
            }

            if (!empty($insufficientItems)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => implode("\n", $insufficientItems),
                    'errors' => $insufficientItems,
                ], 422);
            }

            // Deduct stock from Product table and sync alerts
            $alertService = app(\App\Services\InventoryAlertService::class);
            foreach ($validated['items'] as $item) {
                $product = $products->get($item['id']);
                $newStock = max(0, $product->stock_quantity - (int) $item['quantity']);
                $product->stock_quantity = $newStock;
                $product->save();
                try {
                    $alertService->syncProductAlert($product);
                } catch (\Exception $e) {
                    Log::warning("InventoryAlertService sync failed: " . $e->getMessage());
                }
            }

            // Deduct stock from shop inventory / warehouse stock where available
            $itemsForDeduction = collect($validated['items'])->map(function ($item) {
                return [
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ];
            })->toArray();

            try {
                $shopInventoryController = new ShopInventoryController();
                $deductRequest = new Request(['items' => $itemsForDeduction]);
                $shopInventoryController->deductFromShopInventory($deductRequest);
            } catch (\Exception $e) {
                Log::warning("Shop inventory deduction warning: " . $e->getMessage());
            }

            $itemsToStore = collect($validated['items'])->map(function ($item) use ($products) {
                $product = $products->get($item['id']);
                $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price']) && (float) $item['unit_price'] > 0
                    ? (float) $item['unit_price']
                    : (isset($item['price']) && is_numeric($item['price']) && (float) $item['price'] > 0 ? (float) $item['price'] : (float) ($product?->unit_price ?? 0));

                $category = $item['category'] ?? null;
                if (!$category || strcasecmp(trim($category), 'uncategorized') === 0) {
                    $category = $product?->category;
                    if (!$category || strcasecmp(trim($category), 'uncategorized') === 0) {
                        $category = $product?->product_name ?: ($product?->name ?? 'Uncategorized');
                    }
                }

                return [
                    'id' => (int) $item['id'],
                    'product_id' => (int) $item['id'],
                    'name' => $item['name'] ?? $product?->product_name ?? $product?->name ?? 'Unknown Product',
                    'sku' => $item['sku'] ?? $product?->sku ?? '',
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => $unitPrice,
                    'cost_price' => isset($item['cost_price']) && is_numeric($item['cost_price']) ? (float) $item['cost_price'] : 0,
                    'category' => $category ?? 'Uncategorized',
                    'compatibility' => $item['compatibility'] ?? $product?->compatibility ?? null,
                ];
            })->toArray();

            $amountPaid = isset($validated['amount_paid']) && is_numeric($validated['amount_paid'])
                ? (float) $validated['amount_paid']
                : (float) $validated['total_amount'];
            $changeAmount = isset($validated['change_amount']) && is_numeric($validated['change_amount'])
                ? (float) $validated['change_amount']
                : max(0, $amountPaid - (float) $validated['total_amount']);

            $transaction = POSTransaction::create([
                'invoice_number' => $validated['invoice_number'],
                'user_id' => auth()->id(),
                'items' => $itemsToStore,
                'subtotal' => $validated['subtotal'],
                'services_total' => $validated['services_total'],
                'extra_charge' => $validated['extra_charge'],
                'discount' => $validated['discount'],
                'tax' => $validated['tax'],
                'total_amount' => $validated['total_amount'],
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction saved successfully',
                'transaction' => $transaction,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top selling products from completed transactions.
     */
    public function getTopSellingProducts($limit = 5, $startDate = null, $endDate = null)
    {
        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        try {
            $products = POSTransaction::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->select(DB::raw('
                    JSON_EXTRACT(items, "$.*.name") as product_name,
                    JSON_EXTRACT(items, "$.*.id") as product_id,
                    JSON_EXTRACT(items, "$.*.category") as category,
                    SUM(JSON_EXTRACT(items, "$.*.quantity")) as total_quantity,
                    SUM(JSON_EXTRACT(items, "$.*.quantity") * JSON_EXTRACT(items, "$.*.unit_price")) as total_revenue
                '))
                ->groupByRaw('JSON_EXTRACT(items, "$.*.id")')
                ->orderByDesc(DB::raw('SUM(JSON_EXTRACT(items, "$.*.quantity"))'))
                ->limit($limit)
                ->get();

            return $products;
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get all POS transactions (paginated).
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        try {
            $query = POSTransaction::query();

            if ($status) {
                $query->where('status', $status);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('completed_at', [$startDate, $endDate]);
            }

            $transactions = $query->latest('completed_at')->paginate($perPage);

            return response()->json($transactions);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'message' => 'Unable to fetch transactions'
            ], 500);
        }
    }

    public function topSellingProducts(Request $request)
    {
        $limit = min(20, max(1, (int) $request->get('limit', 5)));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        try {
            $transactions = POSTransaction::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->get();

            $productSales = [];
            $productIds = [];

            foreach ($transactions as $transaction) {
                if (!is_array($transaction->items) || empty($transaction->items)) {
                    continue;
                }

                foreach ($transaction->items as $item) {
                    $productId = $item['id'] ?? null;
                    $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
                    $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);

                    if (!$productId || $quantity <= 0) {
                        continue;
                    }

                    $productIds[$productId] = $productId;

                    if (!isset($productSales[$productId])) {
                        $productSales[$productId] = [
                            'qty' => 0,
                            'revenue' => 0,
                        ];
                    }

                    $productSales[$productId]['qty'] += $quantity;
                    $productSales[$productId]['revenue'] += $quantity * $unitPrice;
                }
            }

            if (empty($productSales)) {
                return response()->json(['data' => []]);
            }

            $products = Product::whereIn('id', array_keys($productIds))
                ->get(['id', 'name', 'category'])
                ->keyBy('id');

            $topProducts = collect($productSales)
                ->map(function ($sales, $productId) use ($products) {
                    $product = $products->get($productId);
                    return [
                        'id' => $productId,
                        'name' => $product?->name ?? 'Unknown Product',
                        'category' => $product?->category ?? 'Uncategorized',
                        'qty' => $sales['qty'],
                        'revenue' => $sales['revenue'],
                    ];
                })
                ->filter(fn ($product) => $product['qty'] > 0)
                ->sortByDesc('qty')
                ->values()
                ->slice(0, $limit)
                ->map(function ($product, $index) {
                    return [
                        'rank' => $index + 1,
                        'name' => $product['name'],
                        'category' => $product['category'],
                        'qty' => $product['qty'],
                        'revenue' => '₱' . number_format($product['revenue'], 2),
                    ];
                });

            return response()->json(['data' => $topProducts]);
        } catch (\Exception $e) {
            return response()->json(['data' => []], 500);
        }
    }
}

