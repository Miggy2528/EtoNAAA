<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SyncSupplierUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This creates Supplier records for any User accounts with role='supplier' that don't have one yet.
     */
    public function run(): void
    {
        $this->command->info('🔍 Checking for supplier users without supplier records...');
        
        // Find all users with role='supplier' who don't have a supplier record
        $supplierUsers = User::where('role', 'supplier')
            ->whereDoesntHave('supplier')
            ->get();
        
        if ($supplierUsers->isEmpty()) {
            $this->command->info('✅ All supplier users already have supplier records!');
            return;
        }
        
        $this->command->info("📝 Found {$supplierUsers->count()} supplier user(s) without supplier records.");
        
        foreach ($supplierUsers as $user) {
            // Check if a supplier with this email already exists
            $existingSupplier = Supplier::where('email', $user->email)->first();
            
            if ($existingSupplier) {
                // Link the existing supplier to this user
                $existingSupplier->update(['user_id' => $user->id]);
                $this->command->info("✅ Linked existing supplier to user: {$user->name} ({$user->email})");
                continue;
            }
            
            // Create supplier record for this user
            $supplier = Supplier::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'contact_person' => $user->name,
                'phone' => null, // Can be updated later in profile
                'address' => null, // Can be updated later in profile
                'shopname' => $user->name . "'s Shop",
                'type' => 'wholesaler', // Default type
                'status' => 'active',
                'account_holder' => $user->name,
                'account_number' => null,
                'bank_name' => null,
            ]);
            
            $this->command->info("✅ Created supplier record for: {$user->name} ({$user->email})");
        }
        
        $this->command->info("\n🎉 Successfully synced {$supplierUsers->count()} supplier record(s)!");
    }
}
