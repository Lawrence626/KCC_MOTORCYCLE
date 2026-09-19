<?php

use App\Http\Controllers\ProductCatalogController;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Known standard oil/liquid capacities if not specified in name.
     */
    protected array $knownCapacities = [
        'Motul 3100' => '800mL',
        'Motul 5100' => '1L',
        'Motul 7100' => '1L',
        'Castrol Power1' => '1L',
        'Castrol Activ' => '800mL',
        'Shell Advance AX7' => '800mL',
        'Shell Advance Ultra' => '1L',
        'Yamalube Sport' => '800mL',
        'Yamalube Premium' => '1L',
        'Honda Pro Oil' => '800mL',
        'Honda SPX' => '800mL',
        'Repsol Moto Rider' => '800mL',
        'Repsol Racing' => '1L',
        'Motul DOT 4' => '500mL',
        'Motul DOT 5.1' => '500mL',
        'Brembo DOT 4' => '500mL',
        'Bosch DOT 4' => '500mL',
        'Honda Brake Fluid' => '500mL',
        'Yamaha Brake Fluid' => '500mL',
        'Motul Gear Oil' => '120mL',
        'Shell Spirax Gear Oil' => '120mL',
        'Honda Gear Oil' => '120mL',
        'Yamalube Gear Oil' => '100mL',
        'Castrol Gear Oil' => '120mL',
        'HYPERCOOL' => '500mL',
        'SUPERCOOL' => '500mL',
        'Motul Motocool' => '1L',
        'Honda Coolant' => '1L',
        'Yamaha Coolant' => '1L',
        'Prestone Radiator Coolant' => '1L',
        'Caltex Coolant' => '1L',
    ];

    public function up(): void
    {
        // 1. Temporarily append _temp_id to SKUs to avoid unique constraint collisions
        DB::statement("UPDATE product_catalog SET sku = CONCAT(sku, '_temp_', id)");

        // 2. Fetch all products from catalog
        $catalogRows = DB::table('product_catalog')
            ->whereNull('deleted_at')
            ->orderBy('product_description')
            ->orderBy('brand')
            ->orderBy('id')
            ->get();

        $groupCounters = []; // key: desc||brand||sizeSlug => next seq

        foreach ($catalogRows as $product) {
            $desc = trim($product->product_description ?? '');
            $brand = trim($product->brand ?? '');
            $name = trim($product->product_name ?? '');
            $size = trim($product->size ?? '');
            $oldSku = preg_replace('/_temp_\d+$/', '', $product->sku);

            $isLiquid = preg_match('/oil|fluid|coolant|cleaner|sealant|lubricant/i', $desc . ' ' . $name);

            // Infer size for oils/liquids if missing
            if ($size === '' && $isLiquid) {
                // Try from product name regex
                if (preg_match('/\((\d+(?:\.\d+)?\s*(?:ml|l|liter|liters|litre|litres))\)/i', $name, $m)) {
                    $size = $m[1];
                } elseif (preg_match('/\b(\d+(?:\.\d+)?)\s*(ml|l|liter|liters|litre|litres)\b/i', $name, $m)) {
                    $size = $m[1] . $m[2];
                } else {
                    // Check known capacities map
                    foreach ($this->knownCapacities as $pattern => $capacity) {
                        if (stripos($name, $pattern) !== false) {
                            $size = $capacity;
                            break;
                        }
                    }
                }
            }

            $sizeSlug = ProductCatalogController::normalizeSizeSlug($size, $desc, $name);
            $descSlug = strtoupper(str_replace(' ', '_', $desc));
            $brandSlug = $brand !== '' ? strtoupper(str_replace(' ', '_', $brand)) : 'UNKNOWN';

            $groupKey = $descSlug . '||' . $brandSlug . '||' . $sizeSlug;
            if (!isset($groupCounters[$groupKey])) {
                $groupCounters[$groupKey] = 1;
            }

            $seq = str_pad($groupCounters[$groupKey], 3, '0', STR_PAD_LEFT);

            if ($sizeSlug !== '') {
                $newSku = "KCC_{$descSlug}_{$brandSlug}_{$sizeSlug}_{$seq}";
            } else {
                $newSku = "KCC_{$descSlug}_{$brandSlug}_{$seq}";
            }

            // Update product_catalog
            DB::table('product_catalog')
                ->where('id', $product->id)
                ->update([
                    'sku' => $newSku,
                    'size' => $size !== '' ? $size : $product->size,
                ]);

            // Sync to matching products table row
            DB::table('products')
                ->where('sku', $oldSku)
                ->orWhere('product_catalog_id', $product->id)
                ->orWhere(function ($q) use ($product) {
                    $q->where('product_name', $product->product_name)
                      ->where('brand', $product->brand);
                })
                ->update([
                    'sku' => $newSku,
                    'size' => $size !== '' ? $size : $product->size,
                ]);

            $groupCounters[$groupKey]++;
        }
    }

    public function down(): void
    {
        // Reversible via previous migration logic if needed
    }
};
