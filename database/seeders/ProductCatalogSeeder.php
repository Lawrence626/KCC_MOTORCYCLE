<?php

namespace Database\Seeders;

use App\Models\MotorcycleModel;
use App\Models\ProductCatalog;
use App\Models\ProductDescription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample products based on actual business requirements
        $products = [
            // PIPE products
            [
                'product_description' => 'PIPE',
                'brand' => 'APIDO',
                'product_name' => 'APIDO Exhaust Pipe',
                'sku' => 'KCC-PIP-0001',
                'description' => 'High-quality APIDO exhaust pipe for improved performance.',
                'motorcycle_models' => ['Honda Click 125', 'Honda Beat', 'Yamaha Mio Sporty'],
            ],
            [
                'product_description' => 'PIPE',
                'brand' => 'MT8 TT',
                'product_name' => 'MT8 TT Racing Pipe',
                'sku' => 'KCC-PIP-0002',
                'description' => 'MT8 TT racing exhaust pipe for maximum performance.',
                'motorcycle_models' => ['Honda ADV160', 'Yamaha NMAX'],
            ],
            [
                'product_description' => 'PIPE',
                'brand' => 'ORBR V2',
                'product_name' => 'ORBR V2 Sport Pipe',
                'sku' => 'KCC-PIP-0003',
                'description' => 'ORBR V2 sport exhaust pipe with enhanced sound.',
                'motorcycle_models' => ['Yamaha Aerox', 'Honda PCX160'],
            ],

            // SHOCK products
            [
                'product_description' => 'SHOCK',
                'brand' => 'RCB A3',
                'product_name' => 'RCB A3 Rear Shock',
                'sku' => 'KCC-SHK-0001',
                'description' => 'RCB A3 rear shock absorber with adjustable settings.',
                'motorcycle_models' => ['Honda Click 125', 'Honda Beat'],
            ],
            [
                'product_description' => 'SHOCK',
                'brand' => 'BOMX X2',
                'product_name' => 'BOMX X2 Performance Shock',
                'sku' => 'KCC-SHK-0002',
                'description' => 'BOMX X2 high-performance shock absorber.',
                'motorcycle_models' => ['Yamaha Mio Sporty', 'Yamaha Mio Gear'],
            ],
            [
                'product_description' => 'SHOCK',
                'brand' => 'MUTARRU',
                'product_name' => 'MUTARRU Racing Shock',
                'sku' => 'KCC-SHK-0003',
                'description' => 'MUTARRU racing shock absorber for sport bikes.',
                'motorcycle_models' => ['Honda CB150', 'Yamaha Sniper'],
            ],

            // SWING ARM products
            [
                'product_description' => 'SWING ARM',
                'brand' => 'DT10',
                'product_name' => 'DT10 Swing Arm',
                'sku' => 'KCC-SWA-0001',
                'description' => 'DT10 swing arm for improved stability.',
                'motorcycle_models' => ['Honda Wave', 'Honda XRM 110'],
            ],
            [
                'product_description' => 'SWING ARM',
                'brand' => 'V3',
                'product_name' => 'V3 Racing Swing Arm',
                'sku' => 'KCC-SWA-0002',
                'description' => 'V3 racing swing arm for enhanced handling.',
                'motorcycle_models' => ['Yamaha Sniper', 'Suzuki Raider'],
            ],

            // ENGINE SUPPORT products
            [
                'product_description' => 'ENGINE SUPPORT',
                'brand' => 'P TITANIUM',
                'product_name' => 'P TITANIUM Engine Support',
                'sku' => 'KCC-ENS-0001',
                'description' => 'P TITANIUM engine support bracket for added durability.',
                'motorcycle_models' => ['Honda Click 125', 'Yamaha Mio Sporty'],
            ],

            // SIDE MIRROR products
            [
                'product_description' => 'SIDE MIRROR',
                'brand' => 'H2C',
                'product_name' => 'H2C Side Mirror',
                'sku' => 'KCC-SMI-0001',
                'description' => 'H2C side mirror with wide-angle view.',
                'motorcycle_models' => ['Honda Click 125', 'Honda Beat', 'Yamaha Mio Sporty', 'Suzuki Smash'],
            ],

            // TIRE HUGGER products
            [
                'product_description' => 'TIRE HUGGER',
                'brand' => 'YAMAHA',
                'product_name' => 'YAMAHA Tire Hugger',
                'sku' => 'KCC-THG-0001',
                'description' => 'OEM Yamaha tire hugger for protection.',
                'motorcycle_models' => ['Yamaha NMAX', 'Yamaha Aerox'],
            ],
            [
                'product_description' => 'TIRE HUGGER',
                'brand' => 'OEM V1',
                'product_name' => 'OEM V1 Tire Hugger',
                'sku' => 'KCC-THG-0002',
                'description' => 'OEM V1 tire hugger for various models.',
                'motorcycle_models' => ['Honda ADV160', 'Honda PCX160'],
            ],

            // MONORACK FRAME products
            [
                'product_description' => 'MONORACK FRAME',
                'brand' => 'DC',
                'product_name' => 'DC Monorack Frame',
                'sku' => 'KCC-MNF-0001',
                'description' => 'DC monorack frame for luggage mounting.',
                'motorcycle_models' => ['Honda ADV160', 'Yamaha NMAX'],
            ],

            // QUICK THROTTLE products
            [
                'product_description' => 'QUICK THROTTLE',
                'brand' => 'KYTA',
                'product_name' => 'KYTA Quick Throttle',
                'sku' => 'KCC-QTR-0001',
                'description' => 'KYTA quick throttle for improved throttle response.',
                'motorcycle_models' => ['Honda CB150', 'Yamaha Sniper', 'Suzuki Raider'],
            ],

            // TIRE products
            [
                'product_description' => 'TIRE',
                'brand' => 'PRIMAAX',
                'product_name' => 'PRIMAAX Front Tire',
                'sku' => 'KCC-TYP-0001',
                'description' => 'PRIMAAX front tire with excellent grip.',
                'motorcycle_models' => ['Honda Click 125', 'Honda Beat', 'Yamaha Mio Sporty'],
            ],
            [
                'product_description' => 'TIRE',
                'brand' => 'FDR',
                'product_name' => 'FDR Rear Tire',
                'sku' => 'KCC-TYP-0002',
                'description' => 'FDR rear tire for improved traction.',
                'motorcycle_models' => ['Honda Wave', 'Yamaha Mio Gear'],
            ],
            [
                'product_description' => 'TIRE',
                'brand' => 'MUTARRU',
                'product_name' => 'MUTARRU Sport Tire',
                'sku' => 'KCC-TYP-0003',
                'description' => 'MUTARRU sport tire for high performance.',
                'motorcycle_models' => ['Honda CB150', 'Yamaha Sniper'],
            ],
        ];

        foreach ($products as $productData) {
            // Create or update product
            $product = ProductCatalog::firstOrCreate(
                ['sku' => $productData['sku']],
                [
                    'product_name' => $productData['product_name'],
                    'brand' => $productData['brand'],
                    'product_description' => $productData['product_description'],
                    'description' => $productData['description'],
                    'status' => 'Active',
                ]
            );

            // Attach motorcycle models
            foreach ($productData['motorcycle_models'] as $modelName) {
                // Parse the model name to extract brand and model
                $parts = explode(' ', $modelName, 2);
                if (count($parts) >= 2) {
                    $brand = $parts[0];
                    $model = $parts[1];
                    $motorcycle = MotorcycleModel::where('brand', $brand)
                        ->where('model_name', $model)
                        ->first();
                    if ($motorcycle) {
                        $product->motorcycleModels()->syncWithoutDetaching([$motorcycle->id]);
                    }
                }
            }
        }
    }
}
