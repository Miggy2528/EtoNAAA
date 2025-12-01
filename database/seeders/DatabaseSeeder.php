<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StaffSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
            StaffPerformanceSeeder::class, // Add Staff Performance Module
            StaffNotificationSeeder::class, // Add Staff Notification System
            SupplierUserSeeder::class, // Add Supplier User Seeder
            SupplierDummyDataSeeder::class, // Add Supplier Dummy Data
            MarketAnalysisTrendSeeder::class, // Add Market Analysis Trend Data
        ]);

        $orders = Order::factory(50)->create();
        $customers = Customer::factory(30)
            ->recycle($orders)
            ->create();

        $purchases = Purchase::factory(60)->create();
        $suppliers = Supplier::factory(20)->create();

        $users = User::factory(10)
            ->recycle($suppliers)
            ->recycle($purchases)
            ->create();

        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'status' => 'active'
        ]);

        /*
        for ($i=0; $i < 10; $i++) {
            Product::factory()->create([
                'product_code' => IdGenerator::generate([
                    'table' => 'products',
                    'field' => 'product_code',
                    'length' => 4,
                    'prefix' => 'PC'
                ]),
            ]);
        }
        */

    }
}
