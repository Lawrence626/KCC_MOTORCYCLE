<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultWarehouses = [
            ['name' => 'Warehouse A', 'code' => 'WH-A'],
            ['name' => 'Warehouse B', 'code' => 'WH-B'],
            ['name' => 'Warehouse C', 'code' => 'WH-C'],
            ['name' => 'Warehouse D', 'code' => 'WH-D'],
        ];

        foreach ($defaultWarehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['code' => $warehouse['code']],
                [
                    'name' => $warehouse['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
