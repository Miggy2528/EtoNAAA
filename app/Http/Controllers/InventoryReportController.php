<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Staff;
use App\Models\ProductUpdateLog;
use App\Models\MeatCut;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportController extends Controller
{
    /**
     * Display inventory report with product data and staff tracking
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $staffFilter = $request->get('staff_id');
        $animalType = $request->get('animal_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $stockStatus = $request->get('stock_status');

        // Optimized query with eager loading
        $products = Product::with([
            'category',
            'unit',
            'meatCut',
            'updatedByUser',
            'latestUpdateLog.user'
        ])
        ->when($staffFilter, function($query) use ($staffFilter) {
            $query->where('updated_by', $staffFilter);
        })
        ->when($animalType, function($query) use ($animalType) {
            $query->whereHas('meatCut', function($q) use ($animalType) {
                $q->where('animal_type', $animalType);
            });
        })
        ->when(($dateFrom || $dateTo), function($query) use ($dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                $from = Carbon::parse($dateFrom)->startOfDay();
                $to = Carbon::parse($dateTo)->endOfDay();
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($dateFrom) {
                $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            } else {
                $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            }
        })
        ->when($stockStatus, function($query) use ($stockStatus) {
            if ($stockStatus === 'low') {
                $query->whereColumn('quantity', '<=', 'quantity_alert');
            } elseif ($stockStatus === 'out') {
                $query->where('quantity', '<=', 0);
            } elseif ($stockStatus === 'in_stock') {
                $query->whereColumn('quantity', '>', 'quantity_alert');
            }
        })
        ->orderBy('updated_at', 'desc')
        ->get();

        $productsByBatch = $products->groupBy(function($product) {
            return optional($product->created_at)->format('Y-m-d');
        });

        // Advanced Stock Analytics
        $totalProducts = $products->count();
        $totalStock = $products->sum('quantity');
        
        // Stock Status Breakdown
        $inStockItems = $products->filter(function($product) {
            return $product->quantity > ($product->quantity_alert ?? 10);
        })->count();
        
        $lowStockItems = $products->filter(function($product) {
            return $product->quantity > 0 && $product->quantity <= ($product->quantity_alert ?? 10);
        })->count();
        
        $outOfStockItems = $products->filter(function($product) {
            return $product->quantity <= 0;
        })->count();

        // Expiration Tracking
        $now = Carbon::now();
        $expiringItems = Product::whereNotNull('expiration_date')
            ->where('expiration_date', '>', $now)
            ->where('expiration_date', '<=', $now->copy()->addDays(7))
            ->count();
        
        $expiredItems = Product::whereNotNull('expiration_date')
            ->where('expiration_date', '<', $now)
            ->count();

        // Stock Value Calculation
        $totalStockValue = $products->sum(function($product) {
            return $product->quantity * ($product->buying_price ?? $product->price_per_kg ?? 0);
        });

        // Get active staff count
        $activeStaff = Staff::where('status', 'Active')->count();

        // Get product distribution by animal type
        $productDistribution = Product::with('meatCut')
            ->get()
            ->groupBy(function($product) {
                return $product->meatCut->animal_type ?? 'Other';
            })
            ->map(function($group) {
                return $group->count();
            });

        // Stock Level Distribution
        $stockLevelDistribution = [
            'In Stock' => $inStockItems,
            'Low Stock' => $lowStockItems,
            'Out of Stock' => $outOfStockItems
        ];

        // Stock Value by Category (Top 5)
        $stockValueByCategory = Product::with('category')
            ->get()
            ->groupBy(function($product) {
                return $product->category->name ?? 'Uncategorized';
            })
            ->map(function($group) {
                return $group->sum(function($product) {
                    return $product->quantity * ($product->buying_price ?? $product->price_per_kg ?? 0);
                });
            })
            ->sortDesc()
            ->take(5);

        $today = Carbon::today();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $topSellingDaily = \App\Models\OrderDetails::whereHas('order', function($q) use ($today) {
                $q->where('order_status', \App\Enums\OrderStatus::COMPLETE)
                  ->whereDate('order_date', $today);
            })
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_qty, SUM(total) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        $topSellingMonthly = \App\Models\OrderDetails::whereHas('order', function($q) use ($currentMonth, $currentYear) {
                $q->where('order_status', \App\Enums\OrderStatus::COMPLETE)
                  ->whereYear('order_date', $currentYear)
                  ->whereMonth('order_date', $currentMonth);
            })
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_qty, SUM(total) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        $topSellingYearly = \App\Models\OrderDetails::whereHas('order', function($q) use ($currentYear) {
                $q->where('order_status', \App\Enums\OrderStatus::COMPLETE)
                  ->whereYear('order_date', $currentYear);
            })
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_qty, SUM(total) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(5)
            ->get();

        // Recent Stock Movements (Last 7 days)
        $recentActivity = ProductUpdateLog::with(['product', 'staff'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get staff activity (product updates per staff)
        $staffActivity = ProductUpdateLog::select('staff_id', DB::raw('count(*) as update_count'))
            ->with('staff')
            ->whereNotNull('staff_id')
            ->groupBy('staff_id')
            ->orderBy('update_count', 'desc')
            ->limit(12)
            ->get();

        // Stock Trend Analysis (Last 30 days)
        $stockTrend = ProductUpdateLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total_updates')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_updates', 'date');

        // Get all staff for filter dropdown
        $allStaff = Staff::where('status', 'Active')->orderBy('name')->get();

        // Get unique animal types for filter
        $animalTypes = MeatCut::select('animal_type')
            ->distinct()
            ->whereNotNull('animal_type')
            ->pluck('animal_type');

        // Get products expiring soon (within 7 days)
        $expiringProducts = Product::with(['meatCut', 'unit'])
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '>', $now)
            ->where('expiration_date', '<=', $now->copy()->addDays(7))
            ->orderBy('expiration_date')
            ->limit(10)
            ->get();

        // Expired products history
        $expiredProducts = Product::with(['meatCut', 'unit'])
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', $now)
            ->orderBy('expiration_date', 'desc')
            ->get();

        return view('reports.inventory', compact(
            'products',
            'totalProducts',
            'totalStock',
            'lowStockItems',
            'inStockItems',
            'outOfStockItems',
            'expiringItems',
            'expiredItems',
            'totalStockValue',
            'activeStaff',
            'productDistribution',
            'stockLevelDistribution',
            'stockValueByCategory',
            'topSellingDaily',
            'topSellingMonthly',
            'topSellingYearly',
            'staffActivity',
            'recentActivity',
            'stockTrend',
            'allStaff',
            'animalTypes',
            'expiringProducts',
            'expiredProducts',
            'productsByBatch'
        ));
    }

    /**
     * Get inventory analytics data (for AJAX requests - Real-time updates)
     */
    public function analytics()
    {
        $now = Carbon::now();
        
        // Real-time stock statistics
        $products = Product::all();
        $totalStock = $products->sum('quantity');
        
        $inStockItems = $products->filter(fn($p) => $p->quantity > ($p->quantity_alert ?? 10))->count();
        $lowStockItems = $products->filter(fn($p) => $p->quantity > 0 && $p->quantity <= ($p->quantity_alert ?? 10))->count();
        $outOfStockItems = $products->filter(fn($p) => $p->quantity <= 0)->count();
        
        // Expiration tracking
        $expiringItems = Product::whereNotNull('expiration_date')
            ->where('expiration_date', '>', $now)
            ->where('expiration_date', '<=', $now->copy()->addDays(7))
            ->count();
        
        $productDistribution = Product::with('meatCut')
            ->get()
            ->groupBy(function($product) {
                return $product->meatCut->animal_type ?? 'Other';
            })
            ->map(function($group) {
                return $group->count();
            });

        $stockLevelDistribution = [
            'In Stock' => $inStockItems,
            'Low Stock' => $lowStockItems,
            'Out of Stock' => $outOfStockItems
        ];

        $staffActivity = ProductUpdateLog::select('staff_id', DB::raw('count(*) as update_count'))
            ->with('staff')
            ->whereNotNull('staff_id')
            ->groupBy('staff_id')
            ->orderBy('update_count', 'desc')
            ->limit(12)
            ->get();

        // Stock trend (last 30 days)
        $stockTrend = ProductUpdateLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total_updates')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'total_stock' => $totalStock,
            'in_stock_items' => $inStockItems,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'expiring_items' => $expiringItems,
            'product_distribution' => $productDistribution,
            'stock_level_distribution' => $stockLevelDistribution,
            'staff_activity' => $staffActivity,
            'stock_trend' => $stockTrend,
            'last_updated' => now()->toIso8601String()
        ]);
    }
}
