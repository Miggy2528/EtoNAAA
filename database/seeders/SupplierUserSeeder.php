<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupplierUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if supplier user already exists
        $existingSupplier = User::where('email', 'supplier@supplier.com')->first();

        if (!$existingSupplier) {
            $user = User::create([
                'name' => 'Default Supplier',
                'username' => 'supplier',
                'email' => 'supplier@supplier.com',
                'password' => Hash::make('password'),
                'role' => 'supplier',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Create supplier record linked to this user
            Supplier::create([
                'user_id' => $user->id,
                'name' => 'Default Supplier Company',
                'email' => 'supplier@supplier.com',
                'phone' => '+1234567890',
                'address' => '123 Supplier Street',
                'shopname' => 'Default Supplier Shop',
                'type' => 'distributor',
                'status' => 'active',
                'contact_person' => 'Default Supplier',
            ]);

            $this->command->info('✅ Default supplier account created successfully!');
            $this->command->info('   Email: supplier@supplier.com');
            $this->command->info('   Password: password');
        } else {
            $this->command->info('ℹ️  Supplier account already exists.');
            
            // Check if supplier record exists
            if (!$existingSupplier->supplier) {
                Supplier::create([
                    'user_id' => $existingSupplier->id,
                    'name' => 'Default Supplier Company',
                    'email' => 'supplier@supplier.com',
                    'phone' => '+1234567890',
                    'address' => '123 Supplier Street',
                    'shopname' => 'Default Supplier Shop',
                    'type' => 'distributor',
                    'status' => 'active',
                    'contact_person' => 'Default Supplier',
                ]);
                $this->command->info('✅ Created supplier record for existing user.');
            }
        }
    }
}
