<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@kcc.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'contact' => '0123456789',
                'address' => 'KCC Warehouse',
                'age' => 30,
                'gender' => 'Male',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create default inventory clerk user
        User::firstOrCreate(
            ['email' => 'clerk@kcc.local'],
            [
                'name' => 'Inventory Clerk',
                'password' => Hash::make('password'),
                'role' => 'inventory_clerk',
                'contact' => '0123456789',
                'address' => 'KCC Warehouse',
                'age' => 25,
                'gender' => 'Female',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create sample users for other roles
        User::firstOrCreate(
            ['email' => 'cashier@kcc.local'],
            [
                'name' => 'Cashier User',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'contact' => '0123456789',
                'address' => 'KCC Store',
                'age' => 28,
                'gender' => 'Male',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'warehouse@kcc.local'],
            [
                'name' => 'Warehouse Personnel',
                'password' => Hash::make('password'),
                'role' => 'warehouse_personnel',
                'contact' => '0123456789',
                'address' => 'KCC Warehouse',
                'age' => 32,
                'gender' => 'Male',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
