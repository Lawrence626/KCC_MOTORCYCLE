<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class SetProductPricesSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $price = $this->calculateRealisticPrice($product);
            $product->unit_price = $price;
            $product->save();
        }
    }

    private function calculateRealisticPrice(Product $product): float
    {
        $brand = strtoupper(trim($product->brand ?? ''));
        $name = strtoupper(trim($product->name ?? ''));
        $sku = strtoupper(trim($product->sku ?? ''));
        $category = strtoupper(trim($product->category ?? ''));
        $pname = strtoupper(trim($product->product_name ?? ''));
        $size = trim($product->size ?? '');

        // ----------------------------------------------------
        // 0. SPECIFIC ACCESSORIES & CHEMICALS FIRST
        // ----------------------------------------------------
        if (str_contains($sku, 'TIRE_SEALANT') || str_contains($pname, 'TIRE SEALANT') || str_contains($name, 'SEALANT')) {
            return 180.00;
        }
        if (str_contains($sku, 'TIRE_HUGGER') || str_contains($pname, 'TIRE HUGGER') || str_contains($name, 'HUGGER')) {
            return 550.00;
        }
        if (str_contains($sku, 'SWING_ARM') || str_contains($pname, 'SWING ARM') || str_contains($name, 'SWING ARM')) {
            return 3450.00;
        }
        if (str_contains($sku, 'ENGINE_SUPPORT') || str_contains($pname, 'ENGINE SUPPORT') || str_contains($name, 'ENGINE SUPPORT')) {
            return 1650.00;
        }
        if (str_contains($sku, 'MONORACK') || str_contains($sku, 'FRAME') || str_contains($name, 'GRAB') || str_contains($name, 'PANDA')) {
            return 750.00;
        }
        if (str_contains($sku, 'RADIATOR') && !str_contains($sku, 'COOLANT')) {
            return 1850.00;
        }

        // ----------------------------------------------------
        // 1. OILS, FLUIDS, CHEMICALS & COOLANTS
        // ----------------------------------------------------
        if (str_contains($sku, 'ENGINE_OIL') || str_contains($pname, 'ENGINE OIL') || (str_contains($name, 'OIL') && !str_contains($sku, 'BRAKE') && !str_contains($sku, 'GEAR'))) {
            $is1L = str_contains($sku, '1L') || str_contains($name, '1L');
            if (str_contains($brand, 'MOTUL') || str_contains($name, 'MOTUL')) {
                return $is1L ? 480.00 : 380.00;
            }
            if (str_contains($brand, 'SHELL') || str_contains($brand, 'ADVANCE')) {
                if (str_contains($name, 'ULTRA')) return 480.00;
                return $is1L ? 360.00 : 280.00;
            }
            if (str_contains($brand, 'REPSOL')) {
                if (str_contains($name, 'RACING')) return 520.00;
                return $is1L ? 380.00 : 290.00;
            }
            if (str_contains($brand, 'YAMALUBE') || str_contains($name, 'YAMALUBE')) {
                if (str_contains($name, 'PREMIUM')) return 350.00;
                return $is1L ? 320.00 : 260.00;
            }
            return $is1L ? 320.00 : 250.00;
        }

        if (str_contains($sku, 'GEAR_OIL') || str_contains($pname, 'GEAR OIL') || str_contains($name, 'GEAR OIL')) {
            return 85.00;
        }

        if (str_contains($sku, 'BRAKE_FLUID') || str_contains($pname, 'BRAKE FLUID') || str_contains($name, 'BRAKE OIL') || str_contains($name, 'BRAKE FLUID')) {
            return 180.00;
        }

        if (str_contains($sku, 'COOLANT') || str_contains($pname, 'COOLANT') || str_contains($name, 'COOLANT')) {
            return (str_contains($brand, 'PRESTONE') || str_contains($name, 'PRESTONE')) ? 260.00 : 220.00;
        }

        if (str_contains($sku, 'CVT_CLEANER') || str_contains($pname, 'CVT CLEANER') || str_contains($name, 'CVT CLEANER')) {
            return 220.00;
        }

        // ----------------------------------------------------
        // 2. MAGS (Wheels)
        // ----------------------------------------------------
        if (str_contains($sku, 'MAGS') || str_contains($pname, 'MAGS') || str_contains($brand, 'CT600') || str_contains($brand, 'SP500') || str_contains($brand, 'SP800')) {
            $isMaxi = str_contains($name, 'NMAX') || str_contains($name, 'AEROX') || str_contains($name, 'PCX') || str_contains($name, 'ADV');
            if (str_contains($brand, 'RCB') || str_contains($sku, 'RCB')) {
                if (str_contains($brand, 'SP800') || str_contains($sku, 'SP800')) return $isMaxi ? 6800.00 : 5600.00;
                if (str_contains($brand, 'SP500') || str_contains($sku, 'SP500')) return $isMaxi ? 6200.00 : 5200.00;
                if (str_contains($brand, 'CT600') || str_contains($sku, 'CT600')) return 4800.00;
                return $isMaxi ? 6400.00 : 5200.00;
            }
            if (str_contains($brand, 'MUTARRU') || str_contains($brand, 'TRC') || str_contains($brand, 'NA THONG') || str_contains($brand, 'YAMAHA') || str_contains($brand, 'G REN')) {
                return $isMaxi ? 4800.00 : 3800.00;
            }
            return $isMaxi ? 4500.00 : 3600.00;
        }

        // ----------------------------------------------------
        // 3. EXHAUST PIPES
        // ----------------------------------------------------
        if (str_contains($sku, 'PIPE') || str_contains($pname, 'PIPE') || str_contains($category, 'EXHAUST')) {
            $isMaxi = str_contains($name, 'NMAX') || str_contains($name, 'AEROX') || str_contains($name, 'PCX') || str_contains($name, 'ADV') || str_contains($sku, 'NMAX') || str_contains($sku, 'AEROX');
            $isUnderbone = str_contains($name, 'SNIPER') || str_contains($name, 'RAIDER') || str_contains($name, 'CLICK') || str_contains($name, 'VARIO');
            
            $base = 1350.00;
            if (str_contains($brand, 'APIDO')) {
                $base = 1300.00;
            } elseif (str_contains($brand, 'KVIN') || str_contains($brand, 'K-VIN')) {
                $base = 1350.00;
            } elseif (str_contains($brand, 'MT8')) {
                $base = 1550.00;
                if (str_contains($brand, 'V3') || str_contains($brand, 'TT')) $base = 1750.00;
            } elseif (str_contains($brand, 'TRC')) {
                $base = 1650.00;
            } elseif (str_contains($brand, 'ORBR')) {
                $base = 2800.00;
            } elseif (str_contains($brand, 'MVR1')) {
                $base = 2200.00;
            } elseif (str_contains($brand, 'SOLFILI')) {
                $base = 2100.00;
            } elseif (str_contains($brand, 'TSMP')) {
                $base = 1850.00;
            } elseif (str_contains($brand, 'MUTARRU')) {
                $base = 1600.00;
            } elseif (str_contains($brand, 'NA THONG')) {
                $base = 1450.00;
            }

            if ($isMaxi) {
                $base += 250.00;
            } elseif ($isUnderbone) {
                $base += 100.00;
            }
            return $base;
        }

        // ----------------------------------------------------
        // 4. SUSPENSION / SHOCK ABSORBERS
        // ----------------------------------------------------
        if (str_contains($sku, 'SHOCK') || str_contains($pname, 'SHOCK') || str_contains($category, 'SUSPENSION') || str_contains($brand, 'TARMAX')) {
            $isDual = str_contains($name, 'NMAX') || str_contains($name, 'AEROX') || str_contains($name, 'PCX') || str_contains($name, 'ADV');
            $isFront = str_contains($sku, 'FRONT_SHOCK') || str_contains($pname, 'FRONT_SHOCK');
            
            if ($isFront) {
                return (str_contains($brand, 'RCB') || str_contains($sku, 'RCB')) ? 3800.00 : 2800.00;
            }

            if (str_contains($brand, 'RCB') || str_contains($sku, 'RCB')) {
                if (str_contains($brand, 'TARMAX FLOW') || str_contains($sku, 'TARMAX_FLOW')) return $isDual ? 4800.00 : 2850.00;
                if (str_contains($brand, 'TARMAX') || str_contains($sku, 'TARMAX')) return $isDual ? 4200.00 : 2450.00;
                if (str_contains($brand, 'S2') || str_contains($sku, 'S2')) return $isDual ? 3800.00 : 2250.00;
                if (str_contains($brand, 'A3') || str_contains($sku, 'A3')) return $isDual ? 3400.00 : 1950.00;
                return $isDual ? 3600.00 : 2100.00;
            }

            if (str_contains($brand, 'MUTARRU') || str_contains($brand, 'NA THONG') || str_contains($brand, 'TRC') || str_contains($brand, 'DT10') || str_contains($brand, 'BOMX')) {
                return $isDual ? 2800.00 : 1650.00;
            }
            return $isDual ? 2400.00 : 1450.00;
        }

        // ----------------------------------------------------
        // 5. BRAKES & LEVERS
        // ----------------------------------------------------
        if (str_contains($sku, 'CALIPER') || str_contains($pname, 'CALIPER')) {
            if (str_contains($name, '4 PISTON') || str_contains($sku, '4 PISTON') || str_contains($sku, 'R1')) {
                return 2650.00;
            }
            if (str_contains($brand, 'S26') || str_contains($brand, 'S27') || str_contains($sku, 'S26') || str_contains($sku, 'S27')) {
                return 2250.00;
            }
            if (str_contains($brand, 'ES') || str_contains($sku, 'ES')) {
                return 1750.00;
            }
            return 1850.00;
        }

        if (str_contains($sku, 'LEVER') || str_contains($sku, 'E-3') || str_contains($sku, 'E3+') || str_contains($sku, 'E4+') || str_contains($sku, 'PUMP') || str_contains($category, 'BRAKES') || str_contains($sku, 'BRAKE')) {
            if (str_contains($sku, 'BRAKE_SHOE') || str_contains($pname, 'SHOE')) {
                return 220.00;
            }
            if (str_contains($sku, 'E4+') || str_contains($name, 'E4+') || str_contains($brand, 'E4+')) {
                return 1950.00;
            }
            if (str_contains($sku, 'E3+') || str_contains($name, 'E3+') || str_contains($brand, 'E3+')) {
                return 1750.00;
            }
            if (str_contains($sku, 'E-3') || str_contains($name, 'E-3') || str_contains($brand, 'E-3') || str_contains($brand, 'RCB E3')) {
                return 1650.00;
            }
            if (str_contains($sku, 'S3_W/STOPPER') || str_contains($brand, 'STOPPER')) {
                return 1350.00;
            }
            if (str_contains($brand, 'S3') || str_contains($sku, 'S3')) {
                return 1150.00;
            }
            if (str_contains($brand, 'E2') || str_contains($sku, 'E2')) {
                return 850.00;
            }
            if (str_contains($brand, 'MUTARRU') || str_contains($brand, 'NA THONG') || str_contains($brand, 'SOLFILI')) {
                return 750.00;
            }
            return 650.00;
        }

        // ----------------------------------------------------
        // 6. FLAT SEATS
        // ----------------------------------------------------
        if (str_contains($sku, 'FLAT_SEAT') || str_contains($pname, 'SEAT') || str_contains($category, 'SEAT')) {
            $isMaxi = str_contains($name, 'PCX') || str_contains($name, 'NMAX') || str_contains($name, 'AEROX') || str_contains($name, 'ADV');
            if (str_contains($brand, 'NA THONG')) {
                return $isMaxi ? 1550.00 : 1200.00;
            }
            return $isMaxi ? 1350.00 : 950.00;
        }

        // ----------------------------------------------------
        // 7. TIRES
        // ----------------------------------------------------
        if (str_contains($sku, 'TIRE') || str_contains($pname, 'TIRE') || str_contains($category, 'TIRE') || str_contains($pname, 'TIRES')) {
            if (empty($size)) {
                if (preg_match('/_(\d{2,3})_(\d{2,3})_(\d{2})_/', $sku, $matches)) {
                    $size = "{$matches[1]}/{$matches[2]}-{$matches[3]}";
                } elseif (preg_match('/_(\d{2,3})__(\d{2,3})_(\d{2})_/', $sku, $matches)) {
                    $size = "{$matches[1]}/{$matches[2]}-{$matches[3]}";
                }
            }

            $width = 80;
            $rim = 14;
            if (preg_match('/(\d{2,3})\s*[\/_]\s*(\d{2,3})[\s\-]*(\d{2})/i', $size ?: $sku, $m)) {
                $width = (int)$m[1];
                $aspect = (int)$m[2];
                $rim = (int)$m[3];
            }

            $tier = 1050;
            if (str_contains($brand, 'PIRELLI')) {
                $tier = str_contains($name, 'DIABLO') ? 2450 : 2350;
            } elseif (str_contains($brand, 'MAXXIS')) {
                $tier = 1250;
            } elseif (str_contains($brand, 'CORSA') || str_contains($brand, 'METZELLER')) {
                $tier = 1250;
            } elseif (str_contains($brand, 'ZENEOS') || str_contains($brand, 'FDR') || str_contains($brand, 'PRIMAAX')) {
                $tier = 1150;
            } elseif (str_contains($brand, 'APC') || str_contains($brand, 'ARISUN')) {
                $tier = 1050;
            } elseif (str_contains($brand, 'BEAST') || str_contains($brand, 'QUICK') || str_contains($brand, 'JOURNEY') || str_contains($brand, 'VEE RUBBER') || str_contains($brand, 'MUTARRU')) {
                $tier = 950;
            }

            $sizeAdjustment = 0;
            if ($width <= 70) {
                $sizeAdjustment = -100;
            } elseif ($width == 80) {
                $sizeAdjustment = 0;
            } elseif ($width == 90) {
                $sizeAdjustment = 150;
            } elseif ($width == 100) {
                $sizeAdjustment = 300;
            } elseif ($width == 110) {
                $sizeAdjustment = 450;
            } elseif ($width == 120) {
                $sizeAdjustment = 650;
            } elseif ($width >= 130) {
                $sizeAdjustment = 850;
            }

            if ($rim == 10) $sizeAdjustment -= 100;
            if ($rim == 17 && $width >= 110) $sizeAdjustment += 200;

            return max(750, $tier + $sizeAdjustment);
        }

        // ----------------------------------------------------
        // 8. GENERAL ACCESSORIES
        // ----------------------------------------------------
        if (str_contains($brand, 'NA THONG') || str_contains($brand, 'MUTARRU')) {
            $isMaxi = str_contains($name, 'PCX') || str_contains($name, 'NMAX') || str_contains($name, 'AEROX');
            return $isMaxi ? 1450.00 : 950.00;
        }

        return 650.00;
    }
}
