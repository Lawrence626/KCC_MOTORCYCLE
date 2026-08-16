<?php

use App\Services\SalesCategoryService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Standardize all product categories to the 7 predefined categories
        $products = DB::table('products')->get();

        foreach ($products as $product) {
            $rawCategory = !empty($product->category) ? $product->category : ($product->product_name ?: $product->name);
            $standardCategory = SalesCategoryService::mapToPredefinedCategory($rawCategory);

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'category' => $standardCategory,
                    'updated_at' => now(),
                ]);
        }

        // 2. Standardize all past POS transaction items categories
        $transactions = DB::table('pos_transactions')->get();

        foreach ($transactions as $transaction) {
            $items = json_decode($transaction->items, true);
            if (!is_array($items)) {
                continue;
            }

            $changed = false;
            foreach ($items as &$item) {
                $rawCategory = $item['category'] ?? '';
                if (empty($rawCategory)) {
                    $productId = $item['product_id'] ?? $item['id'] ?? null;
                    $product = $productId ? DB::table('products')->where('id', $productId)->first() : null;
                    $rawCategory = $product->category ?? ($item['name'] ?? '');
                }

                $standardCategory = SalesCategoryService::mapToPredefinedCategory($rawCategory);
                if (($item['category'] ?? '') !== $standardCategory) {
                    $item['category'] = $standardCategory;
                    $changed = true;
                }
            }

            if ($changed) {
                DB::table('pos_transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'items' => json_encode($items),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data standardization migration
    }
};
