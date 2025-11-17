<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Performance Monitoring Service
 * 
 * This service helps identify and log slow database queries
 * for performance optimization.
 */
class PerformanceMonitoringService
{
    private static $slowQueryThreshold = 1000; // milliseconds
    private static $isEnabled = false;

    /**
     * Enable query monitoring
     */
    public static function enable(float $threshold = 1000): void
    {
        self::$slowQueryThreshold = $threshold;
        self::$isEnabled = true;
        
        DB::listen(function ($query) {
            if ($query->time > self::$slowQueryThreshold) {
                Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'connection' => $query->connectionName
                ]);
            }
        });
    }

    /**
     * Disable query monitoring
     */
    public static function disable(): void
    {
        self::$isEnabled = false;
    }

    /**
     * Check if monitoring is enabled
     */
    public static function isEnabled(): bool
    {
        return self::$isEnabled;
    }

    /**
     * Get current threshold
     */
    public static function getThreshold(): float
    {
        return self::$slowQueryThreshold;
    }

    /**
     * Log performance metrics
     */
    public static function logPerformanceMetrics(string $operation, float $executionTime, array $context = []): void
    {
        Log::info('Performance Metrics', array_merge([
            'operation' => $operation,
            'execution_time_ms' => $executionTime,
            'timestamp' => now()->toISOString()
        ], $context));
    }

    /**
     * Get database connection info
     */
    public static function getDatabaseInfo(): array
    {
        try {
            $connection = DB::connection();
            $config = $connection->getConfig();
            
            return [
                'driver' => $config['driver'] ?? 'unknown',
                'host' => $config['host'] ?? 'localhost',
                'database' => $config['database'] ?? 'unknown',
                'charset' => $config['charset'] ?? 'utf8',
                'collation' => $config['collation'] ?? 'utf8_unicode_ci'
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get table sizes and row counts
     */
    public static function getTableStats(): array
    {
        try {
            $tables = ['products', 'orders', 'order_details', 'purchases', 'suppliers', 'users'];
            $stats = [];
            
            foreach ($tables as $table) {
                $count = DB::table($table)->count();
                $stats[$table] = [
                    'row_count' => $count,
                    'estimated_size' => self::estimateTableSize($table, $count)
                ];
            }
            
            return $stats;
        } catch (\Exception $e) {
            // Return error as a separate key, not mixed with table data
            return ['_error' => $e->getMessage()];
        }
    }

    /**
     * Estimate table size based on row count
     */
    private static function estimateTableSize(string $table, int $rowCount): string
    {
        // Rough estimation based on typical row sizes
        $estimatedBytesPerRow = match($table) {
            'products' => 500,
            'orders' => 300,
            'order_details' => 200,
            'purchases' => 400,
            'suppliers' => 300,
            'users' => 400,
            default => 300
        };
        
        $totalBytes = $rowCount * $estimatedBytesPerRow;
        
        if ($totalBytes < 1024) {
            return $totalBytes . ' B';
        } elseif ($totalBytes < 1024 * 1024) {
            return round($totalBytes / 1024, 2) . ' KB';
        } else {
            return round($totalBytes / (1024 * 1024), 2) . ' MB';
        }
    }
}
