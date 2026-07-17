<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * COMBINED migration:
     *  1. Reformat product_catalog.sku  → KCC_{DESC}_{BRAND}_{SEQ}
     *  2. Simultaneously update products.sku using the OLD sku as the link
     *     so the join is never broken.
     *  3. Temporarily disables the unique index on products.sku.
     */
    public function up(): void
    {
        // ── Step 0: Drop unique index on products.sku temporarily ─────────────
        Schema::table('products', function ($table) {
            $table->dropUnique(['sku']);
        });

        // ── Step 1: Fetch product_catalog rows with their CURRENT (old) SKUs ──
        $catalogRows = DB::table('product_catalog')
            ->whereNull('deleted_at')
            ->select('id', 'product_description', 'brand', 'sku as old_sku')
            ->orderBy('product_description')
            ->orderBy('brand')
            ->orderBy('id')
            ->get();

        // ── Step 2: Assign new SKUs in sequential groups ───────────────────────
        $counters = []; // "desc||brand" → next int

        foreach ($catalogRows as $row) {
            $desc  = strtoupper(str_replace(' ', '_', trim((string)$row->product_description)));
            $brand = strtoupper(str_replace(' ', '_', trim((string)$row->brand)));

            // Fallback for rows with no product_description
            if ($desc === '') {
                $desc = 'UNCATEGORIZED';
            }

            $key = $desc . '||' . $brand;
            if (!isset($counters[$key])) {
                $counters[$key] = 1;
            }

            $seq    = str_pad($counters[$key], 3, '0', STR_PAD_LEFT);
            $newSku = "KCC_{$desc}_{$brand}_{$seq}";
            $counters[$key]++;

            // ── 2a. Update product_catalog.sku ────────────────────────────────
            DB::table('product_catalog')
                ->where('id', $row->id)
                ->update([
                    'sku'          => $newSku,
                    'qr_code_path' => null,  // invalidate old QR file path
                ]);

            // ── 2b. Update matching products row (old_sku → new_sku) ──────────
            // The join is product_catalog.sku ↔ products.sku, so we use the
            // OLD sku value to find and update the corresponding products row.
            if ($row->old_sku) {
                DB::table('products')
                    ->where('sku', $row->old_sku)
                    ->update(['sku' => $newSku]);
            }
        }

        // ── Step 3: Restore unique index on products.sku ──────────────────────
        // First ensure there are no remaining duplicates caused by partial overlaps
        // (should not happen since we're doing a 1:1 rename)
        Schema::table('products', function ($table) {
            $table->unique('sku');
        });
    }

    public function down(): void
    {
        // Cannot reverse without original values stored
    }
};
