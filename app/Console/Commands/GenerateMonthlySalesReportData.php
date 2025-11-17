<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Enums\OrderStatus;
use Carbon\Carbon;

class GenerateMonthlySalesReportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate-monthly-sales {--months=12 : Number of months to generate data for} {--year= : Year to generate data for (defaults to current year)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy data for monthly sales report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->option('months');
        $year = $this->option('year') ?? Carbon::now()->year;
        
        $this->info("Generating monthly sales report data for {$months} months in year {$year}...");
        
        // Clear existing test data for the specified year
        $deletedCount = Order::where('customer_name', 'like', 'Test Customer%')
            ->whereYear('order_date', $year)
            ->delete();
            
        $this->info("Deleted {$deletedCount} existing test orders.");
        
        $totalOrdersGenerated = 0;
        
        // Generate dummy orders for each month
        for ($month = 1; $month <= min($months, 12); $month++) {
            // Calculate how many orders we need to generate to reach the target range
            // We want total sales between 130,000 and 160,000 per month
            $targetMonthlySales = rand(130000, 160000);
            
            // Average order value is around 3,000 based on our factory
            $ordersNeeded = ceil($targetMonthlySales / 3000);
            
            // Generate orders for this month
            for ($i = 0; $i < $ordersNeeded; $i++) {
                Order::factory()->create([
                    'order_date' => Carbon::create($year, $month, rand(1, 28)),
                    'order_status' => OrderStatus::COMPLETE,
                    'customer_name' => 'Test Customer - ' . Carbon::create($year, $month, 1)->format('F Y'),
                    'receiver_name' => 'Test Customer - ' . Carbon::create($year, $month, 1)->format('F Y'),
                    'customer_email' => 'test' . $month . '@example.com',
                ]);
            }
            
            $totalOrdersGenerated += $ordersNeeded;
            
            $this->info("Generated {$ordersNeeded} orders for " . Carbon::create($year, $month, 1)->format('F Y'));
        }
        
        $this->info("Successfully generated {$totalOrdersGenerated} orders for monthly sales report!");
        $this->info("You can now view the report at: " . route('reports.monthly.sales'));
    }
}