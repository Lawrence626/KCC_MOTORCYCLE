<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\POSTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSTransactionController extends Controller
{
    /**
     * Store a new POS transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:pos_transactions',
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric|min:0',
            'services_total' => 'required|numeric|min:0',
            'extra_charge' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qr',
        ]);

        $transaction = DB::transaction(function () use ($validated) {
            $transaction = POSTransaction::create([
                'invoice_number' => $validated['invoice_number'],
                'user_id' => auth()->id(),
                'items' => $validated['items'],
                'subtotal' => $validated['subtotal'],
                'services_total' => $validated['services_total'],
                'extra_charge' => $validated['extra_charge'],
                'discount' => $validated['discount'],
                'tax' => $validated['tax'],
                'total_amount' => $validated['total_amount'],
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $quantity = max(0, (int) ($item['quantity'] ?? $item['qty'] ?? 0));

                if ($quantity <= 0) {
                    continue;
                }

                $product = $this->resolveProductFromItem($item);
                if (!$product) {
                    continue;
                }

                if ($product->stock_quantity < $quantity) {
                    throw new \RuntimeException('Insufficient stock for product: ' . ($product->name ?? $product->product_name ?? $product->id));
                }

                $product->decrement('stock_quantity', $quantity);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity_change' => -$quantity,
                    'unit_price' => $item['unit_price'] ?? $product->unit_price,
                    'supplier_name' => null,
                    'notes' => 'Sold via POS invoice ' . $validated['invoice_number'],
                    'metadata' => [
                        'pos_transaction_id' => $transaction->id,
                        'invoice_number' => $validated['invoice_number'],
                    ],
                ]);
            }

            return $transaction;
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaction saved successfully',
            'transaction' => $transaction,
        ], 201);
    }

    protected function resolveProductFromItem(array $item): ?Product
    {
        foreach (['product_id', 'id', 'productId'] as $key) {
            $value = $item[$key] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            $product = Product::find($value);
            if ($product) {
                return $product;
            }
        }

        foreach (['sku', 'barcode'] as $key) {
            $value = $item[$key] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            $product = Product::where('sku', $value)
                ->orWhere('barcode', $value)
                ->first();
            if ($product) {
                return $product;
            }
        }

        $name = $item['name'] ?? $item['product_name'] ?? null;
        if ($name) {
            $product = Product::where('name', $name)
                ->orWhere('product_name', $name)
                ->first();
            if ($product) {
                return $product;
            }
        }

        return null;
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

