<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    public function run()
    {
        // Create realistic suppliers for motorcycle parts
        $suppliers = [
            [
                'name' => 'Yamaha Philippines',
                'contact_person' => 'Juan Dela Cruz',
                'contact_position' => 'Sales Manager',
                'address' => '123 Pasig City, Metro Manila',
                'email' => 'sales@yamaha.ph',
                'phone' => '0917-123-4567',
                'status' => 'active',
                'notes' => 'Official Yamaha distributor for motorcycle parts and accessories.',
            ],
            [
                'name' => 'Honda Motorcycle Parts',
                'contact_person' => 'Maria Santos',
                'contact_position' => 'Procurement Lead',
                'address' => '456 Quezon City, Metro Manila',
                'email' => 'procurement@honda.ph',
                'phone' => '0922-765-4321',
                'status' => 'active',
                'notes' => 'Authorized Honda parts supplier with OEM quality components.',
            ],
            [
                'name' => 'Kawasaki Motors Supply',
                'contact_person' => 'Pedro Reyes',
                'contact_position' => 'Warehouse Manager',
                'address' => '789 Makati City, Metro Manila',
                'email' => 'warehouse@kawasaki.ph',
                'phone' => '0933-987-6543',
                'status' => 'active',
                'notes' => 'Kawasaki motorcycle parts and performance components.',
            ],
            [
                'name' => 'Suzuki Parts Depot',
                'contact_person' => 'Ana Garcia',
                'contact_position' => 'Inventory Supervisor',
                'address' => '321 Taguig City, Metro Manila',
                'email' => 'inventory@suzuki.ph',
                'phone' => '0944-111-2222',
                'status' => 'active',
                'notes' => 'Suzuki motorcycle parts and accessories supplier.',
            ],
            [
                'name' => 'Universal Motorcycle Parts',
                'contact_person' => 'Carlos Mendoza',
                'contact_position' => 'General Manager',
                'address' => '654 Caloocan City, Metro Manila',
                'email' => 'gm@universalparts.ph',
                'phone' => '0955-333-4444',
                'status' => 'active',
                'notes' => 'Universal parts supplier for various motorcycle brands.',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(
                ['name' => $supplierData['name']],
                $supplierData
            );
        }

        $this->command->info("Created " . count($suppliers) . " suppliers.");

        // Get all products without supplier_name
        $products = Product::whereNull('supplier_name')->get();
        $supplierNames = collect($suppliers)->pluck('name');

        // Distribute products unevenly among suppliers
        // Yamaha: 40% of products
        // Honda: 30% of products
        // Kawasaki: 15% of products
        // Suzuki: 10% of products
        // Universal: 5% of products
        $distribution = [
            0 => 0.40, // Yamaha Philippines
            1 => 0.30, // Honda Motorcycle Parts
            2 => 0.15, // Kawasaki Motors Supply
            3 => 0.10, // Suzuki Parts Depot
            4 => 0.05, // Universal Motorcycle Parts
        ];

        $productIndex = 0;
        foreach ($distribution as $supplierIndex => $percentage) {
            $count = (int) ($products->count() * $percentage);
            for ($i = 0; $i < $count && $productIndex < $products->count(); $i++) {
                $products[$productIndex]->update(['supplier_name' => $supplierNames[$supplierIndex]]);
                $productIndex++;
            }
        }

        // Assign any remaining products to the first supplier
        while ($productIndex < $products->count()) {
            $products[$productIndex]->update(['supplier_name' => $supplierNames[0]]);
            $productIndex++;
        }

        $this->command->info("Assigned {$products->count()} products to suppliers with uneven distribution.");
    }
}
