<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'butcherpro:performance-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the performance of the ButcherPro application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 ButcherPro Performance Test');
        $this->line('==========================');

        // Test 1: Database connection time
        $this->info('Testing database connection...');
        $start = microtime(true);
        try {
            DB::connection()->getPdo();
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            $this->line('  ✅ Database connection: ' . number_format($executionTime, 2) . ' ms');
        } catch (\Exception $e) {
            $this->error('  ❌ Database connection failed: ' . $e->getMessage());
            return 1;
        }

        // Test 2: Simple query performance
        $this->info('Testing simple query performance...');
        $start = microtime(true);
        try {
            $count = DB::table('products')->count();
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            $this->line('  ✅ Products count query: ' . number_format($executionTime, 2) . ' ms (' . $count . ' products)');
        } catch (\Exception $e) {
            $this->error('  ❌ Products count query failed: ' . $e->getMessage());
        }

        // Test 3: Cache performance
        $this->info('Testing cache performance...');
        $start = microtime(true);
        try {
            Cache::put('performance_test', 'test_value', 10);
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            $this->line('  ✅ Cache write: ' . number_format($executionTime, 2) . ' ms');
            
            $start = microtime(true);
            $value = Cache::get('performance_test');
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000;
            $this->line('  ✅ Cache read: ' . number_format($executionTime, 2) . ' ms');
        } catch (\Exception $e) {
            $this->error('  ❌ Cache test failed: ' . $e->getMessage());
        }

        // Test 4: Memory usage
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024; // Convert to MB
        $this->line('  📊 Peak memory usage: ' . number_format($memoryUsage, 2) . ' MB');

        $this->info('✅ Performance test completed!');
        
        return 0;
    }
}