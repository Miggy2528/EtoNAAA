<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\PerformanceMonitoringService;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'butcherpro:optimize 
                            {--cache : Clear and rebuild caches}
                            {--indexes : Add database indexes}
                            {--monitor : Enable performance monitoring}
                            {--stats : Show performance statistics}
                            {--all : Run all optimizations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize ButcherPro system performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 ButcherPro Performance Optimization');
        $this->line('=====================================');

        if ($this->option('all') || $this->option('cache')) {
            $this->optimizeCaches();
        }

        if ($this->option('all') || $this->option('indexes')) {
            $this->addDatabaseIndexes();
        }

        if ($this->option('all') || $this->option('monitor')) {
            $this->enablePerformanceMonitoring();
        }

        if ($this->option('all') || $this->option('stats')) {
            $this->showPerformanceStats();
        }

        $this->info('✅ Performance optimization completed!');
    }

    /**
     * Optimize Laravel caches
     */
    private function optimizeCaches(): void
    {
        $this->info('📦 Optimizing caches...');

        try {
            // Clear existing caches
            $this->line('  Clearing existing caches...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Rebuild caches
            $this->line('  Rebuilding caches...');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // Clear analytics cache
            $this->line('  Clearing analytics cache...');
            Cache::forget('analytics:inventory');
            Cache::forget('analytics:sales');
            Cache::forget('analytics:suppliers');
            Cache::forget('analytics:staff');
            Cache::forget('analytics:dashboard');

            $this->info('  ✅ Caches optimized successfully');
        } catch (\Exception $e) {
            $this->error('  ❌ Cache optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Add database indexes
     */
    private function addDatabaseIndexes(): void
    {
        $this->info('🗄️  Adding database indexes...');

        try {
            Artisan::call('migrate', ['--path' => 'database/migrations/2025_10_14_031705_add_performance_indexes.php']);
            $this->info('  ✅ Database indexes added successfully');
        } catch (\Exception $e) {
            $this->error('  ❌ Database index creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Enable performance monitoring
     */
    private function enablePerformanceMonitoring(): void
    {
        $this->info('📊 Enabling performance monitoring...');

        try {
            PerformanceMonitoringService::enable(1000); // 1 second threshold
            $this->info('  ✅ Performance monitoring enabled (1s threshold)');
            $this->line('  📝 Check storage/logs/laravel.log for slow query alerts');
        } catch (\Exception $e) {
            $this->error('  ❌ Performance monitoring setup failed: ' . $e->getMessage());
        }
    }

    /**
     * Show performance statistics
     */
    private function showPerformanceStats(): void
    {
        $this->info('📈 Performance Statistics');
        $this->line('========================');

        try {
            // Database info
            $dbInfo = PerformanceMonitoringService::getDatabaseInfo();
            if (isset($dbInfo['error'])) {
                $this->error('Database Info Error: ' . $dbInfo['error']);
            } else {
                $this->line('Database: ' . $dbInfo['driver'] . '://' . $dbInfo['host'] . '/' . $dbInfo['database']);
            }

            // Table statistics
            $tableStats = PerformanceMonitoringService::getTableStats();
            $this->line('');
            $this->line('Table Statistics:');
            
            // Check if we got an error instead of table stats
            if (isset($tableStats['_error'])) {
                $this->error('  Table Stats Error: ' . $tableStats['_error']);
            } else {
                foreach ($tableStats as $table => $stats) {
                    if (isset($stats['error'])) {
                        $this->error("  {$table}: Error - {$stats['error']}");
                    } else {
                        $this->line("  {$table}: {$stats['row_count']} rows (~{$stats['estimated_size']})");
                    }
                }
            }

            // Cache statistics
            $this->line('');
            $this->line('Cache Status:');
            $this->line('  Driver: ' . config('cache.default'));
            $this->line('  Analytics Cache: ' . (Cache::has('analytics:inventory') ? '✅ Cached' : '❌ Not cached'));

        } catch (\Exception $e) {
            $this->error('❌ Failed to get performance statistics: ' . $e->getMessage());
        }
    }
}