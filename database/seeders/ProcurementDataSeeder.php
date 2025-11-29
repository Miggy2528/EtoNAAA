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
     * Creates procurement records for 2025 (Jan-Oct) for the first 2 suppliers
     */
    public function run(): void
    {
        // Get first 2 suppliers (Magnolia and A.U)
        $suppliers = Supplier::take(2)->get();
        
        if ($suppliers->count() < 2) {
            $this->command->warn('Less than 2 suppliers found. Please add more suppliers first.');
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
        
        $this->command->info('Creating procurement records for 2025 (Jan-Oct) for 2 suppliers...');

        // Define procurement data for each supplier
        $supplierProcurements = [
            // Supplier 1 - Magnolia (Higher volume)
            0 => [
                'procurements_per_month' => [3, 4, 3, 5, 4, 3, 4, 5, 3, 4], // Jan-Oct
                'avg_cost_per_procurement' => 13500,
                'on_time_percentage' => 92,
            ],
            // Supplier 2 - A.U (Slightly lower volume)
            1 => [
                'procurements_per_month' => [3, 3, 4, 3, 4, 3, 3, 4, 3, 3], // Jan-Oct
                'avg_cost_per_procurement' => 12800,
                'on_time_percentage' => 89,
            ],
        ];

        foreach ($suppliers as $index => $supplier) {
            $config = $supplierProcurements[$index];
            $monthlyDistribution = $config['procurements_per_month'];
            $avgCost = $config['avg_cost_per_procurement'];
            $onTimePercentage = $config['on_time_percentage'];
            
            $totalProcurements = array_sum($monthlyDistribution);
            $onTimeCount = (int)($totalProcurements * ($onTimePercentage / 100));
            $procurementCounter = 0;

            $this->command->info("Creating {$totalProcurements} procurements for: {$supplier->name}");

            // Loop through months Jan-Oct 2025
            for ($month = 1; $month <= 10; $month++) {
                $procurementsThisMonth = $monthlyDistribution[$month - 1];
                
                for ($i = 0; $i < $procurementsThisMonth; $i++) {
                    // Random day within the month
                    $day = rand(1, min(28, Carbon::create(2025, $month, 1)->daysInMonth));
                    $deliveryDate = Carbon::create(2025, $month, $day);
                    
                    // Expected delivery date 2-5 days before actual delivery
                    $expectedDeliveryDate = (clone $deliveryDate)->subDays(rand(2, 5));
                    
                    // Determine if on-time or delayed
                    $isOnTime = $procurementCounter < $onTimeCount;
                    $status = $isOnTime ? 'on-time' : 'delayed';
                    
                    // If delayed, adjust delivery date to be after expected
                    if (!$isOnTime) {
                        $deliveryDate = (clone $expectedDeliveryDate)->addDays(rand(1, 7));
                    }
                    
                    // Randomize cost slightly (±20%)
                    $variance = rand(-20, 20) / 100;
                    $cost = round($avgCost * (1 + $variance), 2);
                    
                    // Random quantity
                    $quantity = rand(50, 200);
                    
                    // Defective rate (0.5-4%)
                    $defectiveRate = rand(5, 40) / 10;

                    Procurement::create([
                        'supplier_id' => $supplier->id,
                        'product_id' => $products->random()->id,
                        'quantity_supplied' => $quantity,
                        'total_cost' => $cost,
                        'expected_delivery_date' => $expectedDeliveryDate,
                        'delivery_date' => $deliveryDate,
                        'status' => $status,
                        'defective_rate' => $defectiveRate,
                        'created_at' => $deliveryDate,
                        'updated_at' => $deliveryDate,
                    ]);
                    
                    $procurementCounter++;
                }
            }
        }

        $this->command->info('✅ Procurement data seeded successfully for 2 suppliers!');
        $this->command->info('Total procurements created: ' . Procurement::count());
        $this->command->info('Data range: January 2025 - October 2025');
    }
}
