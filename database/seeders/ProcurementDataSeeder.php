<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Procurement;
use Carbon\Carbon;

class ProcurementDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates procurement records for the first 4 suppliers
     */
    public function run(): void
    {
        // Get first 4 suppliers
        $suppliers = Supplier::take(4)->get();
        
        if ($suppliers->count() < 4) {
            $this->command->warn('Less than 4 suppliers found. Please add more suppliers first.');
            return;
        }

        // Get all products for random assignment
        $products = \App\Models\Product::all();
        
        if ($products->isEmpty()) {
            $this->command->error('No products found. Please add products first.');
            return;
        }

        // Delete existing procurement data to avoid duplicates
        Procurement::truncate();
        
        $this->command->info('Creating procurement records for 4 suppliers...');

        // Define procurement data for each supplier
        $supplierProcurements = [
            // Supplier 1 - Highest performer (52 deliveries, ₱685,000)
            0 => [
                'total_deliveries' => 52,
                'total_cost' => 685000,
                'on_time_percentage' => 92,
            ],
            // Supplier 2 - Second best (46 deliveries, ₱598,000)
            1 => [
                'total_deliveries' => 46,
                'total_cost' => 598000,
                'on_time_percentage' => 93,
            ],
            // Supplier 3 - Third (38 deliveries, ₱425,000)
            2 => [
                'total_deliveries' => 38,
                'total_cost' => 425000,
                'on_time_percentage' => 89,
            ],
            // Supplier 4 - Fourth (32 deliveries, ₱375,000)
            3 => [
                'total_deliveries' => 32,
                'total_cost' => 375000,
                'on_time_percentage' => 91,
            ],
        ];

        foreach ($suppliers as $index => $supplier) {
            $config = $supplierProcurements[$index];
            $totalDeliveries = $config['total_deliveries'];
            $totalCost = $config['total_cost'];
            $onTimePercentage = $config['on_time_percentage'];
            
            $avgCostPerDelivery = $totalCost / $totalDeliveries;
            $onTimeCount = (int)($totalDeliveries * ($onTimePercentage / 100));

            $this->command->info("Creating {$totalDeliveries} procurements for: {$supplier->name}");

            for ($i = 0; $i < $totalDeliveries; $i++) {
                // Distribute procurements over last 12 months
                $monthsAgo = rand(0, 11);
                $deliveryDate = Carbon::now()->subMonths($monthsAgo)->subDays(rand(0, 28));
                $expectedDeliveryDate = (clone $deliveryDate)->subDays(rand(0, 3));
                
                // Determine if on-time or delayed
                $isOnTime = $i < $onTimeCount;
                $status = $isOnTime ? 'on-time' : 'delayed';
                
                // Randomize cost slightly
                $variance = rand(-2000, 2000);
                $cost = max(5000, $avgCostPerDelivery + $variance);
                
                // Random quantity
                $quantity = rand(50, 200);
                
                // Defective rate (1-3%)
                $defectiveRate = rand(10, 30) / 10;

                Procurement::create([
                    'supplier_id' => $supplier->id,
                    'product_id' => $products->random()->id,
                    'quantity_supplied' => $quantity,
                    'total_cost' => $cost,
                    'expected_delivery_date' => $expectedDeliveryDate,
                    'delivery_date' => $deliveryDate,
                    'status' => $status,
                    'defective_rate' => $defectiveRate,
                ]);
            }
        }

        $this->command->info('✅ Procurement data seeded successfully for 4 suppliers!');
        $this->command->info('Total procurements created: ' . Procurement::count());
    }
}
