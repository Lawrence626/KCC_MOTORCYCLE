<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Temporarily drop the unique index on products.sku ─────────────
        if (DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('products', function ($table) {
                    if (Schema::hasIndex('products', 'products_sku_unique')) {
                        $table->dropUnique('products_sku_unique');
                    }
                    if (Schema::hasIndex('products', 'products_sku_warehouse_unique')) {
                        $table->dropUnique('products_sku_warehouse_unique');
                    }
                });
            } catch (\Throwable $e) {
                // Safe to ignore if index does not exist
            }
        }

        // ── 2. Build the catalog map: (desc||brand) → [new_sku_001, 002, …] ──
        $catalogRows = DB::table('product_catalog')
            ->whereNull('deleted_at')
            ->whereNotNull('product_description')
            ->where('product_description', '!=', '')
            ->select('id', 'product_description', 'brand', 'sku')
            ->orderBy('product_description')
            ->orderBy('brand')
            ->orderBy('id')
            ->get();

        $catalogGroups = [];
        foreach ($catalogRows as $row) {
            $key = $row->product_description . '||' . $row->brand;
            $catalogGroups[$key][] = $row->sku;
        }

        // ── 3. Fetch all products rows and update SKU ─────────────────────────
        $productRows = DB::table('products')
            ->select('id', 'brand', 'category', 'sku')
            ->orderBy('category')
            ->orderBy('brand')
            ->orderBy('id')
            ->get();

        $groupIndexes = [];

        foreach ($productRows as $product) {
            $cat   = (string) ($product->category ?? '');
            $brand = (string) ($product->brand    ?? '');
            $key   = $cat . '||' . $brand;

            if ($cat === '' && $brand === '') {
                continue; // skip completely blank rows
            }

            if (isset($catalogGroups[$key])) {
                // Match to catalog group sequentially
                if (!isset($groupIndexes[$key])) {
                    $groupIndexes[$key] = 0;
                }
                $idx    = $groupIndexes[$key];
                $skuArr = $catalogGroups[$key];
                $newSku = $skuArr[$idx] ?? end($skuArr); // clamp to last if over
                $groupIndexes[$key]++;
            } else {
                // No matching catalog — build a new-format SKU directly
                $descSlug  = strtoupper(str_replace(' ', '_', trim($cat)));
                $brandSlug = strtoupper(str_replace(' ', '_', trim($brand)));

                if (!isset($groupIndexes[$key])) {
                    $groupIndexes[$key] = 1;
                }
                $seq    = str_pad($groupIndexes[$key], 3, '0', STR_PAD_LEFT);
                $newSku = "KCC_{$descSlug}_{$brandSlug}_{$seq}";
                $groupIndexes[$key]++;
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update(['sku' => $newSku]);
        }

        // ── 4. Removed restoring unique index since variants can share SKUs ────
    }

    public function down(): void
    {
        // Not reversible without an original backup
    }
};
