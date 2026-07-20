<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Engine Parts',
                'sku_prefix' => 'ENG',
                'description' => 'Internal and external engine components including pistons, cylinders, valves, gaskets, and more.',
            ],
            [
                'category_name' => 'Brake System',
                'sku_prefix' => 'BRK',
                'description' => 'Brake pads, discs, calipers, master cylinders, and brake lines.',
            ],
            [
                'category_name' => 'Electrical',
                'sku_prefix' => 'ELE',
                'description' => 'Electrical components includingspark plugs, batteries, wiring, and lighting systems.',
            ],
            [
                'category_name' => 'Suspension',
                'sku_prefix' => 'SUS',
                'description' => 'Forks, shocks, swing arms, and related suspension components.',
            ],
            [
                'category_name' => 'Transmission',
                'sku_prefix' => 'TRN',
                'description' => 'Clutch, transmission gears, chains, sprockets, and drivetrain components.',
            ],
            [
                'category_name' => 'Fuel System',
                'sku_prefix' => 'FUE',
                'description' => 'Fuel pumps, carburetors, fuel injectors, and fuel tanks.',
            ],
            [
                'category_name' => 'Cooling System',
                'sku_prefix' => 'COL',
                'description' => 'Radiators, water pumps, hoses, and cooling system components.',
            ],
            [
                'category_name' => 'Lubricants',
                'sku_prefix' => 'LUB',
                'description' => 'Engine oils, transmission fluids, brake fluids, and other lubricants.',
            ],
            [
                'category_name' => 'Accessories',
                'sku_prefix' => 'ACC',
                'description' => 'Helmets, gloves, jackets, phone mounts, windshields, and other accessories.',
            ],
            [
                'category_name' => 'Body Parts',
                'sku_prefix' => 'BDY',
                'description' => 'Fairings, fenders, mirrors, seats, and other body components.',
            ],
            [
                'category_name' => 'Tires',
                'sku_prefix' => 'TIR',
                'description' => 'Front and rear tires, inner tubes, and wheel components.',
            ],
            [
                'category_name' => 'Bearings',
                'sku_prefix' => 'BRG',
                'description' => 'Wheel bearings, steering head bearings, and other bearing components.',
            ],
            [
                'category_name' => 'Chains and Sprockets',
                'sku_prefix' => 'CHN',
                'description' => 'Drive chains, front and rear sprockets, and chain maintenance products.',
            ],
            [
                'category_name' => 'Lights',
                'sku_prefix' => 'LGT',
                'description' => 'Headlights, taillights, turn signals, and lighting accessories.',
            ],
            [
                'category_name' => 'Others',
                'sku_prefix' => 'OTH',
                'description' => 'Miscellaneous items that do not fit into other categories.',
            ],
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                ['category_name' => $category['category_name']],
                [
                    'sku_prefix' => $category['sku_prefix'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
