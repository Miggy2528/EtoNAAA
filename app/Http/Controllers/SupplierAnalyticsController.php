<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Procurement;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierAnalyticsController extends Controller
{
    /**
     * Display the Supplier Analytics dashboard
     */
    public function index(Request $request)
    {
        // Get date filters and search term
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');
        
        $suppliers = Supplier::withCount(['procurements', 'purchases'])->get();
        
        // Generate dummy data if no real data exists
        $hasProcurementData = Procurement::exists();
        
        if (!$hasProcurementData) {
            $performanceData = $this->generateDummyPerformanceData();
            $deliveryTracking = $this->generateDummyDeliveryTracking();
            $procurementInsights = $this->generateDummyProcurementInsights();
            $topSuppliers = $this->generateDummyTopSuppliers();
            $monthlyTrends = $this->generateDummyMonthlyTrends();
            $monthlySupplierTrends = $this->generateDummyMonthlyTrendsBySupplier();
            $monthlySupplierDatasets = $this->decorateDatasets($monthlySupplierTrends['datasets']);
            $recentDeliveries = $this->generateDummyRecentDeliveries();
        } else {
            $performanceData = $this->getSupplierPerformance($dateFrom, $dateTo);
            $deliveryTracking = $this->getDeliveryTracking($dateFrom, $dateTo);
            $procurementInsights = $this->getProcurementInsights($dateFrom, $dateTo);
            $topSuppliers = $this->getTopSuppliers(4, $dateFrom, $dateTo);
            $monthlyTrends = $this->getMonthlyProcurementTrends($dateFrom, $dateTo);
            $monthlySupplierTrends = $this->getMonthlyProcurementTrendsBySupplier($dateFrom, $dateTo);
            $monthlySupplierDatasets = $this->decorateDatasets($monthlySupplierTrends['datasets']);
            $recentDeliveries = $this->getRecentDeliveries($dateFrom, $dateTo, $search);
        }
        
        return view('reports.supplier-analytics', compact(
            'suppliers',
            'performanceData',
            'deliveryTracking',
            'procurementInsights',
            'topSuppliers',
            'monthlyTrends',
            'monthlySupplierTrends',
                        'monthlySupplierDatasets',
            'recentDeliveries',
            'hasProcurementData'
        ));
    }

    /**
     * Get supplier performance metrics
     */
    private function getSupplierPerformance($dateFrom = null, $dateTo = null)
    {
        $suppliers = Supplier::with(['procurements' => function($query) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $query->where('updated_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('updated_at', '<=', $dateTo . ' 23:59:59');
            }
        }])->get();
        
        $performance = [];
        
        foreach ($suppliers as $supplier) {
            $procurements = $supplier->procurements;
            
            if ($procurements->isEmpty()) {
                continue;
            }
            
            $totalDeliveries = $procurements->count();
            $onTimeDeliveries = $procurements->where('status', 'on-time')->count();
            $onTimeRate = $totalDeliveries > 0 ? ($onTimeDeliveries / $totalDeliveries) * 100 : 0;
            
            $avgDefectiveRate = $procurements->avg('defective_rate') ?? 0;
            $totalCost = $procurements->sum('total_cost');
            
            // Calculate average delivery delay for delayed deliveries
            $delayedDeliveries = $procurements->filter(function ($procurement) {
                return $procurement->delivery_date && 
                       $procurement->expected_delivery_date && 
                       $procurement->delivery_date > $procurement->expected_delivery_date;
            });
            
            $avgDelayDays = 0;
            if ($delayedDeliveries->count() > 0) {
                $totalDelayDays = $delayedDeliveries->sum(function ($procurement) {
                    return $procurement->delivery_date->diffInDays($procurement->expected_delivery_date);
                });
                $avgDelayDays = round($totalDelayDays / $delayedDeliveries->count(), 1);
            }
            
            $performance[] = [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'total_deliveries' => $totalDeliveries,
                'on_time_deliveries' => $onTimeDeliveries,
                'on_time_rate' => round($onTimeRate, 2),
                'avg_defective_rate' => round($avgDefectiveRate, 2),
                'total_cost' => $totalCost,
                'avg_delay_days' => $avgDelayDays,
                'performance_score' => $this->calculatePerformanceScore($onTimeRate, $avgDefectiveRate),
            ];
        }
        
        return collect($performance)->sortByDesc('performance_score')->values();
    }

    /**
     * Calculate supplier performance score (0-100)
     */
    private function calculatePerformanceScore($onTimeRate, $defectiveRate)
    {
        // 70% weight on on-time delivery, 30% weight on quality (low defective rate)
        $onTimeScore = $onTimeRate * 0.7;
        $qualityScore = (100 - ($defectiveRate * 20)) * 0.3; // Penalize defective rate
        
        return round(max(0, min(100, $onTimeScore + $qualityScore)), 2);
    }

    /**
     * Get delivery tracking data
     */
    private function getDeliveryTracking($dateFrom = null, $dateTo = null)
    {
        $query = Procurement::query();
        
        if ($dateFrom) {
            $query->where('updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('updated_at', '<=', $dateTo . ' 23:59:59');
        }
        
        $onTime = (clone $query)->where('status', 'on-time')->count();
        $delayed = (clone $query)->where('status', 'delayed')->count();
        $total = $onTime + $delayed;
        
        $onTimePercentage = $total > 0 ? round(($onTime / $total) * 100, 2) : 0;
        $delayedPercentage = $total > 0 ? round(($delayed / $total) * 100, 2) : 0;
        
        return [
            'on_time' => $onTime,
            'delayed' => $delayed,
            'total' => $total,
            'on_time_percentage' => $onTimePercentage,
            'delayed_percentage' => $delayedPercentage,
        ];
    }

    /**
     * Get procurement insights
     */
    private function getProcurementInsights($dateFrom = null, $dateTo = null)
    {
        $query = Procurement::query();
        
        if ($dateFrom) {
            $query->where('updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('updated_at', '<=', $dateTo . ' 23:59:59');
        }
        
        $totalProcurementCost = (clone $query)->sum('total_cost');
        $totalQuantitySupplied = (clone $query)->sum('quantity_supplied');
        $avgCostPerProcurement = (clone $query)->avg('total_cost');
        $totalProcurements = (clone $query)->count();
        
        // Get recent procurement trend (last 30 days vs previous 30 days)
        $last30Days = Procurement::where('updated_at', '>=', Carbon::now()->subDays(30))->sum('total_cost');
        $previous30Days = Procurement::whereBetween('updated_at', [
            Carbon::now()->subDays(60),
            Carbon::now()->subDays(30)
        ])->sum('total_cost');
        
        $trendPercentage = $previous30Days > 0 
            ? round((($last30Days - $previous30Days) / $previous30Days) * 100, 2)
            : 0;
        
        return [
            'total_cost' => $totalProcurementCost,
            'total_quantity' => $totalQuantitySupplied,
            'avg_cost' => round($avgCostPerProcurement, 2),
            'total_procurements' => $totalProcurements,
            'trend_percentage' => $trendPercentage,
            'trend_direction' => $trendPercentage >= 0 ? 'up' : 'down',
        ];
    }

    /**
     * Get top suppliers by total cost
     */
    private function getTopSuppliers($limit = 5, $dateFrom = null, $dateTo = null)
    {
        $query = Supplier::select('suppliers.id', 'suppliers.name')
            ->join('procurements', 'suppliers.id', '=', 'procurements.supplier_id');
            
        if ($dateFrom) {
            $query->where('procurements.updated_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('procurements.updated_at', '<=', $dateTo . ' 23:59:59');
        }
        
        return $query->groupBy('suppliers.id', 'suppliers.name')
            ->selectRaw('SUM(procurements.total_cost) as total_spent')
            ->selectRaw('COUNT(procurements.id) as total_procurements')
            ->selectRaw('AVG(procurements.defective_rate) as avg_defect_rate')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function ($supplier) {
                $supplier->avg_defect_rate = round($supplier->avg_defect_rate, 2);
                return $supplier;
            });
    }

    /**
     * Get monthly procurement cost trends (last 12 months)
     */
    private function getMonthlyProcurementTrends($dateFrom = null, $dateTo = null)
    {
        $query = Procurement::select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(total_cost) as total_cost'),
                DB::raw('COUNT(*) as procurement_count')
            );
            
        if ($dateFrom && $dateTo) {
            $query->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);
        } else {
            $query->where('updated_at', '>=', Carbon::now()->subMonths(12));
        }
        
        $trends = $query->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return $trends->map(function ($trend) {
            return [
                'month' => Carbon::parse($trend->month . '-01')->format('M Y'),
                'total_cost' => $trend->total_cost,
                'procurement_count' => $trend->procurement_count,
            ];
        });
    }

    /**
     * Decorate chart datasets with colors and styles
     */
    private function decorateDatasets(array $datasets)
    {
        $palette = [
            'rgb(75, 192, 192)',
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 159, 64)'
        ];

        $styled = [];
        foreach ($datasets as $i => $ds) {
            $color = $palette[$i % count($palette)];
            $rgba = 'rgba(' . substr($color, 4, -1) . ', 0.1)';
            $styled[] = [
                'label' => $ds['label'],
                'data' => $ds['data'],
                'borderColor' => $color,
                'backgroundColor' => $rgba,
                'tension' => 0.4,
                'fill' => true,
            ];
        }

        return $styled;
    }

    /**
     * Get monthly procurement cost trends by supplier (last 12 months)
     */
    private function getMonthlyProcurementTrendsBySupplier($dateFrom = null, $dateTo = null)
    {
        $monthQuery = Procurement::query();
        
        if ($dateFrom && $dateTo) {
            $monthQuery->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);
        } else {
            $monthQuery->where('updated_at', '>=', Carbon::now()->subMonths(12));
        }
        
        $rawMonths = $monthQuery->select(DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('month')
            ->toArray();

        $months = array_map(function ($m) {
            return Carbon::parse($m . '-01')->format('M Y');
        }, $rawMonths);

        $suppliersQuery = Supplier::select('suppliers.id', 'suppliers.name')
            ->join('procurements', 'suppliers.id', '=', 'procurements.supplier_id');
            
        if ($dateFrom && $dateTo) {
            $suppliersQuery->whereBetween('procurements.updated_at', [$dateFrom, $dateTo . ' 23:59:59']);
        } else {
            $suppliersQuery->where('procurements.updated_at', '>=', Carbon::now()->subMonths(12));
        }
        
        $suppliers = $suppliersQuery->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('suppliers.name')
            ->limit(4)
            ->get();

        $datasets = [];
        foreach ($suppliers as $supplier) {
            $rowsQuery = Procurement::where('supplier_id', $supplier->id);
            
            if ($dateFrom && $dateTo) {
                $rowsQuery->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);
            } else {
                $rowsQuery->where('updated_at', '>=', Carbon::now()->subMonths(12));
            }
            
            $rows = $rowsQuery->select(DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'), DB::raw('SUM(total_cost) as total_cost'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total_cost', 'month');

            $data = [];
            foreach ($rawMonths as $m) {
                $data[] = isset($rows[$m]) ? (float)$rows[$m] : 0;
            }

            $datasets[] = [
                'label' => $supplier->name,
                'data' => $data,
            ];
        }

        return [
            'months' => $months,
            'datasets' => $datasets,
        ];
    }

    /**
     * Get recent daily deliveries (last 60 days by default)
     */
    private function getRecentDeliveries($dateFrom = null, $dateTo = null, $search = null)
    {
        $query = Procurement::with(['supplier', 'product'])
            ->whereNotNull('delivery_date');
            
        // Apply date filters if provided
        if ($dateFrom || $dateTo) {
            if ($dateFrom && $dateTo) {
                // Both dates provided - use between
                $query->whereBetween('updated_at', [$dateFrom, $dateTo . ' 23:59:59']);
            } elseif ($dateFrom) {
                // Only date from provided
                $query->where('updated_at', '>=', $dateFrom);
            } elseif ($dateTo) {
                // Only date to provided
                $query->where('updated_at', '<=', $dateTo . ' 23:59:59');
            }
        } else {
            // No filters - show last 60 days by default
            $query->where('updated_at', '>=', Carbon::now()->subDays(60));
        }
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('supplier', function($sq) use ($search) {
                    $sq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('product', function($pq) use ($search) {
                    $pq->where('name', 'like', '%' . $search . '%');
                });
            });
        }
        
        return $query->orderBy('updated_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($procurement) {
                return [
                    'id' => $procurement->id,
                    'supplier_name' => $procurement->supplier->name ?? 'N/A',
                    'product_name' => $procurement->product->name ?? 'N/A',
                    'quantity' => $procurement->quantity_supplied,
                    'total_cost' => $procurement->total_cost,
                    'delivery_date' => $procurement->delivery_date,
                    'expected_date' => $procurement->expected_delivery_date,
                    'status' => $procurement->status,
                    'defective_rate' => $procurement->defective_rate,
                    'delay_days' => $procurement->getDeliveryDelayDays(),
                    'is_on_time' => $procurement->isOnTime(),
                ];
            });
    }

    /**
     * Generate dummy monthly procurement trends by supplier (preview)
     */
    private function generateDummyMonthlyTrendsBySupplier()
    {
        $suppliers = Supplier::take(4)->get();
        if ($suppliers->count() < 4) {
            $placeholders = collect();
            for ($i = $suppliers->count() + 1; $i <= 4; $i++) {
                $placeholders->push((object)[
                    'id' => 100 + $i,
                    'name' => 'Supplier ' . $i,
                ]);
            }
            $suppliers = $suppliers->concat($placeholders);
        }

        // Fixed months: Jan-Oct 2025
        $months = [];
        for ($m = 1; $m <= 10; $m++) {
            $months[] = Carbon::create(2025, $m, 1)->format('M Y');
        }

        // Target yearly totals per supplier (aligns with Top Suppliers)
        $totals = [685000, 598000, 425000, 375000];
        $datasets = [];

        foreach ($suppliers as $idx => $supplier) {
            $targetTotal = $totals[$idx % count($totals)];
            $base = $targetTotal / 10;
            $data = [];
            $acc = 0;

            // First 9 months with variance ±20%
            for ($i = 0; $i < 9; $i++) {
                $variance = ($base * rand(-20, 20)) / 100; // ±20%
                $val = max(0, round($base + $variance));
                $data[] = $val;
                $acc += $val;
            }
            // Last month adjusts to exact total
            $data[] = max(0, $targetTotal - $acc);

            $datasets[] = [
                'label' => $supplier->name,
                'data' => $data,
            ];
        }

        return [
            'months' => $months,
            'datasets' => $datasets,
        ];
    }

    /**
     * Export supplier analytics data (Optional future enhancement)
     */
    public function export()
    {
        // TODO: Implement CSV/PDF export
        return redirect()->back()->with('info', 'Export feature coming soon!');
    }

    /**
     * Generate dummy supplier performance data
     */
    private function generateDummyPerformanceData()
    {
        // Get actual suppliers from database
        $suppliers = Supplier::take(4)->get();
        
        if ($suppliers->count() < 2) {
            // Fallback to generic names if no suppliers exist
            return collect([
                [
                    'supplier_id' => 1,
                    'supplier_name' => 'Supplier 1',
                    'total_deliveries' => 52,
                    'on_time_deliveries' => 48,
                    'on_time_rate' => 92.31,
                    'avg_defective_rate' => 1.8,
                    'total_cost' => 685000,
                    'avg_delay_days' => 1.3,
                    'performance_score' => 93.85,
                ],
                [
                    'supplier_id' => 2,
                    'supplier_name' => 'Supplier 2',
                    'total_deliveries' => 46,
                    'on_time_deliveries' => 43,
                    'on_time_rate' => 93.48,
                    'avg_defective_rate' => 1.5,
                    'total_cost' => 598000,
                    'avg_delay_days' => 1.1,
                    'performance_score' => 95.12,
                ],
            ]);
        }
        
        // Generate data for actual suppliers
        $performanceData = [];
        $costs = [685000, 598000, 425000, 375000];
        $deliveries = [52, 46, 38, 32];
        
        foreach ($suppliers as $index => $supplier) {
            $totalDeliveries = $deliveries[$index] ?? rand(30, 50);
            $onTimeDeliveries = (int)($totalDeliveries * (rand(90, 95) / 100));
            $onTimeRate = round(($onTimeDeliveries / $totalDeliveries) * 100, 2);
            $defectiveRate = rand(10, 25) / 10;
            
            $performanceData[] = [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'total_deliveries' => $totalDeliveries,
                'on_time_deliveries' => $onTimeDeliveries,
                'on_time_rate' => $onTimeRate,
                'avg_defective_rate' => $defectiveRate,
                'total_cost' => $costs[$index] ?? rand(300000, 500000),
                'avg_delay_days' => rand(10, 15) / 10,
                'performance_score' => round($onTimeRate * 0.7 + (100 - $defectiveRate * 20) * 0.3, 2),
            ];
        }
        
        return collect($performanceData)->sortByDesc('performance_score')->values();
    }

    /**
     * Generate dummy delivery tracking data
     */
    private function generateDummyDeliveryTracking()
    {
        return [
            'on_time' => 91,
            'delayed' => 7,
            'total' => 98,
            'on_time_percentage' => 92.86,
            'delayed_percentage' => 7.14,
        ];
    }

    /**
     * Generate dummy procurement insights
     */
    private function generateDummyProcurementInsights()
    {
        return [
            'total_cost' => 1283000,
            'total_quantity' => 4850,
            'avg_cost' => 13091.84,
            'total_procurements' => 98,
            'trend_percentage' => 8.5,
            'trend_direction' => 'up',
        ];
    }

    /**
     * Generate dummy top suppliers data
     */
    private function generateDummyTopSuppliers()
    {
        // Get actual suppliers from database (up to 4)
        $suppliers = Supplier::take(4)->get();

        // Ensure we have 4 supplier entries with names
        if ($suppliers->count() < 4) {
            $placeholders = collect();
            for ($i = $suppliers->count() + 1; $i <= 4; $i++) {
                $placeholders->push((object)[
                    'id' => 100 + $i,
                    'name' => 'Supplier ' . $i,
                ]);
            }
            $suppliers = $suppliers->concat($placeholders);
        }

        $costs = [685000, 598000, 425000, 375000];
        $procurements = [52, 46, 38, 32];
        $defectRates = [1.80, 1.50, 2.10, 1.90];

        $topSuppliersData = [];
        foreach ($suppliers as $index => $supplier) {
            $topSuppliersData[] = (object)[
                'id' => $supplier->id,
                'name' => $supplier->name,
                'total_spent' => $costs[$index] ?? rand(300000, 500000),
                'total_procurements' => $procurements[$index] ?? rand(30, 50),
                'avg_defect_rate' => $defectRates[$index] ?? (rand(15, 25) / 10),
            ];
        }

        return collect($topSuppliersData)->sortByDesc('total_spent')->values();
    }

    /**
     * Generate dummy monthly procurement trends (last 12 months)
     */
    private function generateDummyMonthlyTrends()
    {
        $trends = [];
        $baseAmount = 95000; // Increased to align with yearly totals
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $variance = rand(-15000, 20000);
            $amount = $baseAmount + $variance;
            $procurementCount = rand(6, 12);
            
            $trends[] = [
                'month' => $month->format('M Y'),
                'total_cost' => $amount,
                'procurement_count' => $procurementCount,
            ];
        }
        
        return collect($trends);
    }

    /**
     * Generate dummy recent deliveries (last 15 days)
     */
    private function generateDummyRecentDeliveries()
    {
        $suppliers = Supplier::take(4)->get();
        if ($suppliers->isEmpty()) {
            $suppliers = collect([
                (object)['name' => 'Supplier 1'],
                (object)['name' => 'Supplier 2'],
            ]);
        }
        
        $deliveries = [];
        $products = ['Beef Sirloin', 'Pork Chops', 'Chicken Breast', 'Lamb Ribs', 'Beef Tenderloin'];
        $statuses = ['on-time', 'on-time', 'on-time', 'on-time', 'delayed']; // 80% on-time
        
        for ($i = 0; $i < 15; $i++) {
            $daysAgo = $i;
            $deliveryDate = Carbon::now()->subDays($daysAgo);
            $expectedDate = (clone $deliveryDate)->subDays(rand(2, 5));
            $status = $statuses[array_rand($statuses)];
            
            if ($status === 'delayed') {
                $deliveryDate = (clone $expectedDate)->addDays(rand(1, 5));
                $delayDays = rand(1, 5);
            } else {
                $delayDays = 0;
            }
            
            $deliveries[] = [
                'id' => 1000 + $i,
                'supplier_name' => $suppliers->random()->name,
                'product_name' => $products[array_rand($products)],
                'quantity' => rand(50, 200),
                'total_cost' => rand(8000, 25000),
                'delivery_date' => $deliveryDate,
                'expected_date' => $expectedDate,
                'status' => $status,
                'defective_rate' => rand(0, 30) / 10,
                'delay_days' => $delayDays,
                'is_on_time' => $status === 'on-time',
            ];
        }
        
        return collect($deliveries);
    }
}
