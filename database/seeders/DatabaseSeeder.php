<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            WarehouseSeeder::class,
            ProductCategorySeeder::class,
            ProductDescriptionSeeder::class,
            ProductCatalogSeeder::class,
            LocationDistributionSeeder::class,
        ]);
    }
}
