<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class SetProductPricesSeeder extends Seeder
{
    public function run(): void
    {
        $pipePricesByBrand = [
            'APIDO'    => 1250.00,
            'KVIN'     => 1350.00,
            'TRC'      => 1500.00,
            'MVR1'     => 2200.00,
            'MT8 TT'   => 1850.00,
            'MT8 ST'   => 1750.00,
            'MT8 V3'   => 1950.00,
            'MT8 RL'   => 1800.00,
            'ORBR ST'  => 2800.00,
            'ORBR V2'  => 3200.00,
            'ORBR BT'  => 2650.00,
            'HUN'      => 1750.00,
            'SOLFILI'  => 2100.00,
            'TSMP'     => 1900.00,
        ];

        $tirePrices = [
            'ZENEOS' => [
                '80/80 14'  => 1250.00,
                '90/80 14'  => 1450.00,
                '100/80 14' => 1650.00,
                'default'   => 1400.00,
            ],
            'PIRELLI' => [
                'ANGEL' => [
                    '90/80 14'  => 2350.00,
                    '100/80 14' => 2650.00,
                    'default'   => 2500.00,
                ],
                'DIABLO' => [
                    '80/80 14'  => 2200.00,
                    '90/80 14'  => 2450.00,
                    '100/80 14' => 2750.00,
                    'default'   => 2500.00,
                ],
                'default' => 2450.00,
            ],
            'APC' => [
                '70/90 14'  => 950.00,
                '80/80 14'  => 1050.00,
                '80/90 14'  => 1150.00,
                '90/80 14'  => 1250.00,
                '90/90 14'  => 1300.00,
                '100/80 14' => 1400.00,
                'default'   => 1200.00,
            ],
            'ARISUN' => [
                '70/90 14'  => 850.00,
                '70/90 17'  => 950.00,
                '80/80 14'  => 950.00,
                '80/90 14'  => 1050.00,
                '80/90 17'  => 1150.00,
                '90/80 14'  => 1150.00,
                '90/80 17'  => 1250.00,
                '90/90 14'  => 1200.00,
                '100/80 14' => 1350.00,
                '110/70 13' => 1550.00,
                '110/80 14' => 1600.00,
                '120/70 12' => 1450.00,
                '120/70 13' => 1650.00,
                '120/70 17' => 1750.00,
                '130/70 12' => 1600.00,
                '130/70 13' => 1750.00,
                '140/70 14' => 1850.00,
                'default'   => 1250.00,
            ],
        ];

        // 1. Update PIPES
        $pipes = Product::where('product_name', 'PIPE')
            ->orWhere('sku', 'like', '%PIPE%')
            ->get();

        foreach ($pipes as $p) {
            $brand = trim($p->brand ?? '');
            $price = $pipePricesByBrand[$brand] ?? 1400.00;
            $p->unit_price = $price;
            $p->save();
        }

        // 2. Update TIRES
        $tires = Product::where('product_name', 'TIRES')
            ->orWhere('sku', 'like', '%TIRES%')
            ->get();

        foreach ($tires as $t) {
            $brand = strtoupper(trim($t->brand ?? ''));
            $name = strtoupper(trim($t->name ?? ''));
            $size = trim($t->size ?? '');

            $price = 1200.00;
            if ($brand === 'ZENEOS') {
                $price = $tirePrices['ZENEOS'][$size] ?? $tirePrices['ZENEOS']['default'];
            } elseif ($brand === 'PIRELLI') {
                if (str_contains($name, 'ANGEL')) {
                    $price = $tirePrices['PIRELLI']['ANGEL'][$size] ?? $tirePrices['PIRELLI']['ANGEL']['default'];
                } elseif (str_contains($name, 'DIABLO')) {
                    $price = $tirePrices['PIRELLI']['DIABLO'][$size] ?? $tirePrices['PIRELLI']['DIABLO']['default'];
                } else {
                    $price = $tirePrices['PIRELLI']['default'];
                }
            } elseif ($brand === 'APC') {
                $price = $tirePrices['APC'][$size] ?? $tirePrices['APC']['default'];
            } elseif ($brand === 'ARISUN') {
                $price = $tirePrices['ARISUN'][$size] ?? $tirePrices['ARISUN']['default'];
            }

            $t->unit_price = $price;
            $t->save();
        }
    }
}
