<?php

namespace Database\Seeders;

use App\Models\MotorcycleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MotorcycleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motorcycles = [
            // Honda
            ['brand' => 'Honda', 'model_name' => 'Click 125', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'Beat', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'Wave', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'ADV160', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'PCX160', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'CB150', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'Wave 110', 'year' => null],
            ['brand' => 'Honda', 'model_name' => 'XRM 110', 'year' => null],
            
            // Yamaha
            ['brand' => 'Yamaha', 'model_name' => 'Mio Sporty', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'Mio Gear', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'Aerox', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'NMAX', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'Sniper', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'Mio i125', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'Mio Soul', 'year' => null],
            ['brand' => 'Yamaha', 'model_name' => 'FZ150i', 'year' => null],
            
            // Suzuki
            ['brand' => 'Suzuki', 'model_name' => 'Raider', 'year' => null],
            ['brand' => 'Suzuki', 'model_name' => 'Smash', 'year' => null],
            ['brand' => 'Suzuki', 'model_name' => 'Skydrive', 'year' => null],
            ['brand' => 'Suzuki', 'model_name' => 'Address', 'year' => null],
            ['brand' => 'Suzuki', 'model_name' => 'V-Strom', 'year' => null],
            
            // Kawasaki
            ['brand' => 'Kawasaki', 'model_name' => 'Barako', 'year' => null],
            ['brand' => 'Kawasaki', 'model_name' => 'Rouser', 'year' => null],
            ['brand' => 'Kawasaki', 'model_name' => 'CT100', 'year' => null],
            ['brand' => 'Kawasaki', 'model_name' => 'KSR', 'year' => null],
            ['brand' => 'Kawasaki', 'model_name' => 'KLX', 'year' => null],
        ];

        foreach ($motorcycles as $motorcycle) {
            MotorcycleModel::firstOrCreate(
                [
                    'brand' => $motorcycle['brand'],
                    'model_name' => $motorcycle['model_name'],
                    'year' => $motorcycle['year'],
                ],
                ['is_active' => true]
            );
        }
    }
}
