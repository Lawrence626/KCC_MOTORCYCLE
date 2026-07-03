<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        Supplier::firstOrCreate(
            ['name' => 'Apex Motors Supply'],
            [
                'contact_person' => 'Rafael Dela Cruz',
                'contact_position' => 'Procurement Lead',
                'address' => '123 Makati Ave, Makati City',
                'email' => 'rafael@apexmotors.ph',
                'phone' => '0917-123-4567',
                'status' => 'active',
                'notes' => 'Top supplier for performance parts and fast delivery.',
            ]
        );

        Supplier::firstOrCreate(
            ['name' => 'Velocity Parts Co.'],
            [
                'contact_person' => 'Ma. Theresa Lopez',
                'contact_position' => 'Vendor Relations',
                'address' => '456 EDSA, Quezon City',
                'email' => 'theresa@velocityph.com',
                'phone' => '0922-765-4321',
                'status' => 'active',
                'notes' => 'Reliable supplier for engine and chassis components.',
            ]
        );
    }
}
