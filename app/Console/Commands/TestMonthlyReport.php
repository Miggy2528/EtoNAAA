<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SalesAnalyticsController;
use ReflectionClass;
use Illuminate\Http\Request;

class TestMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:monthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the monthly sales report generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing monthly sales report generation...');
        
        // Create controller instance
        $controller = new SalesAnalyticsController();
        
        // Use reflection to access private method
        $reflector = new ReflectionClass($controller);
        $method = $reflector->getMethod('getMonthlySalesAndProfitReport');
        $method->setAccessible(true);
        
        // Invoke the method
        $result = $method->invoke($controller);
        
        // Display the results
        $this->info('Monthly Report Data:');
        $this->line('Total Months: ' . count($result['data']));
        
        foreach ($result['data'] as $monthData) {
            $this->line($monthData['month'] . ': ₱' . number_format($monthData['total_sales'], 2));
        }
        
        $this->info('Grand Total Sales: ₱' . number_format($result['grand_total_sales'], 2));
        $this->info('Grand Total Profit: ₱' . number_format($result['grand_total_profit'], 2));
        
        // Test monthly details for each month shown in the report
        $this->info('');
        $this->info('Testing monthly details for all months in the report...');
        
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October'];
        
        foreach ($monthNames as $index => $monthName) {
            $monthNumber = $index + 1;
            
            // Create a mock request
            $request = new Request();
            $request->merge(['month' => $monthNumber, 'year' => 2025]);
            
            // Call the getMonthlyDetails method
            $details = $controller->getMonthlyDetails($request);
            
            // Display the results
            $data = $details->getData();
            if (isset($data->success) && $data->success) {
                $hasData = $data->summary->total_sales > 0 ? '✓' : '✗';
                $this->line($hasData . ' ' . $monthName . ': ₱' . number_format($data->summary->total_sales, 2) . ' (' . $data->summary->total_orders . ' orders)');
            } else {
                $this->error('✗ ' . $monthName . ': FAILED');
            }
        }
        
        return 0;
    }
}