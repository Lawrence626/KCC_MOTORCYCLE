<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Auto-categorize products where category is 'uncategorized', null, or empty
        $uncategorizedProducts = DB::table('products')
            ->where('category', 'uncategorized')
            ->orWhereNull('category')
            ->orWhere('category', '')
            ->get();

        foreach ($uncategorizedProducts as $product) {
            $newCategory = !empty($product->product_name) 
                ? $product->product_name 
                : (!empty($product->name) ? $product->name : 'Accessories');

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'category' => $newCategory,
                    'updated_at' => now(),
                ]);
        }

        // 2. Update pos_transactions items json where item category was stored as 'Uncategorized'
        $transactions = DB::table('pos_transactions')
            ->where('items', 'LIKE', '%uncategorized%')
            ->orWhere('items', 'LIKE', '%Uncategorized%')
            ->get();

        foreach ($transactions as $transaction) {
            $items = json_decode($transaction->items, true);
            if (!is_array($items)) {
                continue;
            }

            $changed = false;
            foreach ($items as &$item) {
                $itemCategory = $item['category'] ?? '';
                if (empty($itemCategory) || strcasecmp(trim($itemCategory), 'uncategorized') === 0) {
                    $productId = $item['product_id'] ?? $item['id'] ?? null;
                    $product = $productId ? DB::table('products')->where('id', $productId)->first() : null;

                    $item['category'] = !empty($product->category) && strcasecmp(trim($product->category), 'uncategorized') !== 0
                        ? $product->category
                        : (!empty($item['name']) ? $item['name'] : 'Accessories');
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
        // Irreversible data fix
    }
};
