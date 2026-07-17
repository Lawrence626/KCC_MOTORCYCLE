<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reformat all existing product_catalog SKUs from the old format
     * (e.g. KCC-HYPERCOOL, KCC-PCX160-3) to:
     *   KCC_{PRODUCT_DESC}_{BRAND}_{SEQ}
     *   e.g. KCC_RADIATOR_BRD_001
     *
     * SEQ is a 3-digit counter that increments per (product_description, brand) group,
     * ordered by id so the assignment is stable.
     */
    public function up(): void
    {
        // Fetch all products grouped by (product_description, brand), ordered by id
        $products = DB::table('product_catalog')
            ->whereNull('deleted_at')
            ->orderBy('product_description')
            ->orderBy('brand')
            ->orderBy('id')
            ->select('id', 'product_description', 'brand', 'sku', 'qr_code_path')
            ->get();

        $counters = []; // key: "desc||brand" => next seq int

        foreach ($products as $product) {
            $desc  = strtoupper(str_replace(' ', '_', trim($product->product_description)));
            $brand = strtoupper(str_replace(' ', '_', trim($product->brand)));
            $key   = $desc . '||' . $brand;

            if (!isset($counters[$key])) {
                $counters[$key] = 1;
            }

            $seq    = str_pad($counters[$key], 3, '0', STR_PAD_LEFT);
            $newSku = "KCC_{$desc}_{$brand}_{$seq}";

            // Update SKU (and clear qr_code_path so it can be regenerated)
            DB::table('product_catalog')
                ->where('id', $product->id)
                ->update([
                    'sku'          => $newSku,
                    'qr_code_path' => null,   // invalidate old QR so it won't 404
                ]);

            $counters[$key]++;
        }
    }

    public function down(): void
    {
        // Cannot reliably reverse; SKUs are now in new format.
        // Rolling back would require the original values which aren't stored here.
    }
};
