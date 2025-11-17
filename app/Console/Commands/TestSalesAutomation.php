<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesRecord;
use App\Http\Controllers\SalesAnalyticsController;

class TestSalesAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-sales-automation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the sales analytics automation functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing sales analytics automation...');
        
        // Clear any existing 2025 data for testing
        SalesRecord::where('year', 2025)->delete();
        $this->info('Cleared existing 2025 data.');
        
        // Test data generation
        $controller = new SalesAnalyticsController();
        $controller->generate2025DummyData();
        
        // Check if data was generated
        $records = SalesRecord::where('year', 2025)->orderBy('month')->get();
        
        $this->info("Generated " . $records->count() . " records for 2025:");
        
        foreach ($records as $record) {
            $this->line("Month: " . $record->month . ", Sales: " . $record->total_sales . ", Expenses: " . $record->total_expenses . ", Profit: " . $record->net_profit);
        }
        
        $this->info('Test completed successfully!');
    }
}