<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PurchaseStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Optimized ReportController with performance improvements
 * 
 * Performance Optimizations:
 * 1. Database query caching (5-15 minutes)
 * 2. Reduced N+1 queries with eager loading
 * 3. Optimized database queries with proper indexing
 * 4. Batch processing for multiple calculations
 * 5. Memory-efficient data processing
 */
class OptimizedReportController extends Controller
{
    // Cache duration in minutes
    private const CACHE_DURATION = 10;
    
    // Cache keys
    private const CACHE_KEYS = [
        'inventory' => 'analytics:inventory',
        'sales' => 'analytics:sales',
        'suppliers' => 'analytics:suppliers',
        'staff' => 'analytics:staff',
        'dashboard' => 'analytics:dashboard'
    ];

    /**
     * Get inventory analytics with caching and optimized queries
     */
    public function inventoryAnalytics(): JsonResponse
    {
        return Cache::remember(self::CACHE_KEYS['inventory'], self::CACHE_DURATION, function () {
            try {
                // Single query to get all basic metrics
                $basicMetrics = DB::table('products')
                    ->selectRaw('
                        COUNT(*) as total_items,
                        SUM(quantity) as total_stock_count,
                        SUM(CASE WHEN quantity < COALESCE(quantity_alert, 10) THEN 1 ELSE 0 END) as low_stock_items,
                        SUM(CASE WHEN expiration_date <= ? AND expiration_date > ? AND expiration_date IS NOT NULL THEN 1 ELSE 0 END) as soon_to_expire_items,
                        SUM(quantity * COALESCE(price_per_kg, 0)) as total_inventory_value
                    ', [
                        Carbon::now()->addDays(7)->format('Y-m-d'),
                        Carbon::now()->format('Y-m-d')
                    ])
                    ->first();

                // Optimized query for low stock products with eager loading
                $lowStockProducts = Product::with(['category:id,name', 'unit:id,name'])
                    ->select('id', 'name', 'quantity', 'expiration_date', 'category_id', 'unit_id', 'quantity_alert')
                    ->where(function($query) {
                        $query->where('quantity', '<', DB::raw('COALESCE(quantity_alert, 10)'))
                              ->orWhere('quantity', '<', 10);
                    })
                    ->orderBy('quantity', 'asc')
                    ->limit(10)
                    ->get()
                    ->map(function($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->quantity,
                            'expiration_date' => $product->expiration_date?->format('Y-m-d'),
                            'category' => $product->category?->name,
                            'unit' => $product->unit?->name,
                            'alert_threshold' => $product->quantity_alert ?? 10,
                            'status' => $product->quantity <= ($product->quantity_alert ?? 10) ? 'critical' : 'low'
                        ];
                    });

                // Optimized query for expiring products
                $soonToExpireProducts = Product::with(['category:id,name', 'unit:id,name'])
                    ->select('id', 'name', 'quantity', 'expiration_date', 'category_id', 'unit_id')
                    ->where('expiration_date', '<=', Carbon::now()->addDays(7))
                    ->where('expiration_date', '>', Carbon::now())
                    ->whereNotNull('expiration_date')
                    ->orderBy('expiration_date', 'asc')
                    ->limit(10)
                    ->get()
                    ->map(function($product) {
                        $daysUntilExpiry = Carbon::now()->diffInDays($product->expiration_date, false);
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => $product->quantity,
                            'expiration_date' => $product->expiration_date->format('Y-m-d'),
                            'days_until_expiry' => $daysUntilExpiry,
                            'category' => $product->category?->name,
                            'unit' => $product->unit?->name,
                            'status' => $daysUntilExpiry <= 3 ? 'critical' : 'warning'
                        ];
                    });

                // Category breakdown with single query
                $categoryBreakdown = DB::table('products')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->select('categories.name as category', 
                             DB::raw('COUNT(products.id) as product_count'),
                             DB::raw('SUM(products.quantity) as total_quantity'))
                    ->groupBy('categories.id', 'categories.name')
                    ->get();

                return [
                    'status' => 'success',
                    'data' => [
                        'total_items' => (int) $basicMetrics->total_items,
                        'total_stock_count' => (int) $basicMetrics->total_stock_count,
                        'low_stock_items' => (int) $basicMetrics->low_stock_items,
                        'soon_to_expire_items' => (int) $basicMetrics->soon_to_expire_items,
                        'total_inventory_value' => round((float) $basicMetrics->total_inventory_value, 2),
                        'low_stock_products' => $lowStockProducts,
                        'soon_to_expire_products' => $soonToExpireProducts,
                        'category_breakdown' => $categoryBreakdown,
                        'generated_at' => Carbon::now()->toISOString()
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to fetch inventory analytics',
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get sales analytics with caching and optimized queries
     */
    public function salesAnalytics(): JsonResponse
    {
        return Cache::remember(self::CACHE_KEYS['sales'], self::CACHE_DURATION, function () {
            try {
                // Single query for basic sales metrics
                $basicMetrics = DB::table('orders')
                    ->where('order_status', OrderStatus::COMPLETE->value)
                    ->selectRaw('
                        SUM(total) as total_sales,
                        AVG(total) as average_order_value,
                        COUNT(*) as total_orders,
                        AVG(CASE WHEN order_date >= ? THEN total END) as average_daily_sales
                    ', [Carbon::now()->subDays(30)->format('Y-m-d')])
                    ->first();

                // Top products by quantity (single optimized query)
                $topProductByQuantity = DB::table('order_details')
                    ->join('products', 'order_details.product_id', '=', 'products.id')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.order_status', OrderStatus::COMPLETE->value)
                    ->select('products.id', 'products.name', DB::raw('SUM(order_details.quantity) as total_quantity'))
                    ->groupBy('products.id', 'products.name')
                    ->orderBy('total_quantity', 'desc')
                    ->first();

                // Top products by revenue (single optimized query)
                $topProductByRevenue = DB::table('order_details')
                    ->join('products', 'order_details.product_id', '=', 'products.id')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->whereIn('orders.order_status', [OrderStatus::COMPLETE->value, '1', 1])
                    ->select('products.id', 'products.name', DB::raw('SUM(order_details.total) as total_revenue'))
                    ->groupBy('products.id', 'products.name')
                    ->orderBy('total_revenue', 'desc')
                    ->first();

                // Sales trend with single query
                $salesTrendData = [];
                $salesTrendDetails = [];
                
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $dateStr = $date->format('Y-m-d');
                    
                    $dailyData = DB::table('orders')
                        ->whereIn('order_status', [OrderStatus::COMPLETE->value, '1', 1])
                        ->whereDate('order_date', $dateStr)
                        ->selectRaw('SUM(total) as sales, COUNT(*) as orders')
                        ->first();
                    
                    $sales = (float) ($dailyData->sales ?? 0);
                    $orders = (int) ($dailyData->orders ?? 0);
                    
                    $salesTrendData[] = $sales;
                    $salesTrendDetails[] = [
                        'date' => $dateStr,
                        'sales' => $sales,
                        'orders' => $orders,
                        'average_order_value' => $orders > 0 ? round($sales / $orders, 2) : 0
                    ];
                }

                // Monthly comparison with single query
                $monthlyData = DB::table('orders')
                    ->whereIn('order_status', [OrderStatus::COMPLETE->value, '1', 1])
                    ->selectRaw('
                        SUM(CASE WHEN MONTH(order_date) = ? AND YEAR(order_date) = ? THEN total ELSE 0 END) as current_month_sales,
                        SUM(CASE WHEN MONTH(order_date) = ? AND YEAR(order_date) = ? THEN total ELSE 0 END) as last_month_sales
                    ', [
                        Carbon::now()->month,
                        Carbon::now()->year,
                        Carbon::now()->subMonth()->month,
                        Carbon::now()->subMonth()->year
                    ])
                    ->first();

                $monthlyGrowth = $monthlyData->last_month_sales > 0 
                    ? (($monthlyData->current_month_sales - $monthlyData->last_month_sales) / $monthlyData->last_month_sales) * 100 
                    : 0;

                // Top 5 products with single query
                $topProducts = DB::table('order_details')
                    ->join('products', 'order_details.product_id', '=', 'products.id')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->whereIn('orders.order_status', [OrderStatus::COMPLETE->value, '1', 1])
                    ->select('products.id', 'products.name', 
                             DB::raw('SUM(order_details.quantity) as total_quantity'),
                             DB::raw('SUM(order_details.total) as total_revenue'))
                    ->groupBy('products.id', 'products.name')
                    ->orderBy('total_quantity', 'desc')
                    ->limit(5)
                    ->get();

                // Sales by payment type with single query
                $salesByPaymentType = DB::table('orders')
                    ->whereIn('order_status', [OrderStatus::COMPLETE->value, '1', 1])
                    ->select('payment_type', 
                             DB::raw('COUNT(*) as order_count'),
                             DB::raw('SUM(total) as total_amount'))
                    ->groupBy('payment_type')
                    ->get();

                // Calculate total expenses from expense tables (non-void where applicable)
                $utilities = DB::table('utility_expenses')
                    ->where('is_void', false)
                    ->sum('amount');

                $payroll = DB::table('payroll_records')
                    ->where(function($query) {
                        $query->where('is_void', false)->orWhereNull('is_void');
                    })
                    ->sum('total_salary');

                $otherExpenses = DB::table('other_expenses')
                    ->where(function($query) {
                        $query->where('is_void', false)->orWhereNull('is_void');
                    })
                    ->sum('amount');

                $totalExpenses = (float) $utilities + (float) $payroll + (float) $otherExpenses;
                $netIncome = (float) $basicMetrics->total_sales - $totalExpenses;

                return [
                    'status' => 'success',
                    'data' => [
                        'total_sales' => (float) $basicMetrics->total_sales,
                        'average_daily_sales' => (float) $basicMetrics->average_daily_sales,
                        'total_expenses' => $totalExpenses,
                        'net_income' => $netIncome,
                        'top_product_by_quantity' => $topProductByQuantity?->name ?? 'No sales data',
                        'top_product_quantity' => (int) ($topProductByQuantity?->total_quantity ?? 0),
                        'top_product_by_revenue' => $topProductByRevenue?->name ?? 'No sales data',
                        'top_product_revenue' => (float) ($topProductByRevenue?->total_revenue ?? 0),
                        'trend' => $salesTrendData,
                        'trend_details' => $salesTrendDetails,
                        'total_orders' => (int) $basicMetrics->total_orders,
                        'average_order_value' => (float) $basicMetrics->average_order_value,
                        'current_month_sales' => (float) $monthlyData->current_month_sales,
                        'last_month_sales' => (float) $monthlyData->last_month_sales,
                        'monthly_growth_percentage' => round($monthlyGrowth, 2),
                        'top_products' => $topProducts,
                        'sales_by_payment_type' => $salesByPaymentType,
                        'generated_at' => Carbon::now()->toISOString()
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to fetch sales analytics',
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get supplier analytics with caching and optimized queries
     */
    public function supplierAnalytics(Request $request): JsonResponse
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return Cache::remember(self::CACHE_KEYS['suppliers'], self::CACHE_DURATION, function () use ($dateFrom, $dateTo) {
            try {
                $now = Carbon::now();
                $defaultFrom = $now->subDays(30)->format('Y-m-d');

                $effectiveFrom = $dateFrom ?: $defaultFrom;
                $effectiveTo = $dateTo ?: $now->format('Y-m-d');

                // Basic supplier metrics with single query
                $basicMetrics = DB::table('suppliers')
                    ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                    ->selectRaw('
                        COUNT(DISTINCT suppliers.id) as total_suppliers,
                        COUNT(DISTINCT CASE WHEN purchases.date >= ? AND purchases.date <= ? THEN suppliers.id END) as active_suppliers,
                        COUNT(CASE WHEN purchases.date >= ? AND purchases.date <= ? THEN purchases.id END) as recent_purchases,
                        SUM(CASE WHEN purchases.date >= ? AND purchases.date <= ? AND purchases.status = ? THEN purchases.total_amount ELSE 0 END) as total_purchase_amount
                    ', [
                        $effectiveFrom,
                        $effectiveTo,
                        $effectiveFrom,
                        $effectiveTo,
                        $effectiveFrom,
                        $effectiveTo,
                        PurchaseStatus::APPROVED->value
                    ])
                    ->first();

                // Most frequent supplier with single query
                $mostFrequentSupplier = DB::table('suppliers')
                    ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                    ->select('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone',
                             DB::raw('COUNT(purchases.id) as purchase_count'))
                    ->when($dateFrom || $dateTo, function ($query) use ($effectiveFrom, $effectiveTo) {
                        return $query->whereBetween('purchases.date', [$effectiveFrom, $effectiveTo]);
                    })
                    ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone')
                    ->orderBy('purchase_count', 'desc')
                    ->first();

                // Top supplier by amount with single query
                $topSupplierByAmount = DB::table('suppliers')
                    ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                    ->where('purchases.status', PurchaseStatus::APPROVED->value)
                    ->when($dateFrom || $dateTo, function ($query) use ($effectiveFrom, $effectiveTo) {
                        return $query->whereBetween('purchases.date', [$effectiveFrom, $effectiveTo]);
                    })
                    ->select('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone',
                             DB::raw('SUM(purchases.total_amount) as total_amount'))
                    ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone')
                    ->orderBy('total_amount', 'desc')
                    ->first();

                // All-time metrics with single query (still all-time, not filtered)
                $allTimeMetrics = DB::table('purchases')
                    ->where('status', PurchaseStatus::APPROVED->value)
                    ->selectRaw('
                        SUM(total_amount) as all_time_purchase_amount,
                        AVG(total_amount) as average_purchase_value
                    ')
                    ->first();

                // Purchase trend over last 7 days (unfiltered, recent daily deliveries)
                $purchaseTrend = [];
                $recentDailyDeliveries = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i)->format('Y-m-d');
                    $dailyPurchases = DB::table('purchases')
                        ->where('date', $date)
                        ->where('status', PurchaseStatus::APPROVED->value)
                        ->sum('total_amount');
                    $purchaseTrend[] = (float) $dailyPurchases;
                    $recentDailyDeliveries[] = [
                        'date' => $date,
                        'total_amount' => (float) $dailyPurchases
                    ];
                }

                // Today deliveries (recent daily deliver)
                $todayDate = Carbon::now()->format('Y-m-d');
                $todayDeliveriesAmount = DB::table('purchases')
                    ->where('date', $todayDate)
                    ->where('status', PurchaseStatus::APPROVED->value)
                    ->sum('total_amount');
                $todayDeliveriesCount = DB::table('purchases')
                    ->where('date', $todayDate)
                    ->where('status', PurchaseStatus::APPROVED->value)
                    ->count();

                // Monthly purchase comparison (still using calendar months)
                $monthlyPurchaseData = DB::table('purchases')
                    ->where('status', PurchaseStatus::APPROVED->value)
                    ->selectRaw('
                        SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN total_amount ELSE 0 END) as current_month_purchases,
                        SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN total_amount ELSE 0 END) as last_month_purchases
                    ', [
                        Carbon::now()->month,
                        Carbon::now()->year,
                        Carbon::now()->subMonth()->month,
                        Carbon::now()->subMonth()->year
                    ])
                    ->first();

                $monthlyPurchaseGrowth = $monthlyPurchaseData->last_month_purchases > 0 
                    ? (($monthlyPurchaseData->current_month_purchases - $monthlyPurchaseData->last_month_purchases) / $monthlyPurchaseData->last_month_purchases) * 100 
                    : 0;

                return [
                    'status' => 'success',
                    'data' => [
                        'total_suppliers' => (int) $basicMetrics->total_suppliers,
                        'active_suppliers' => (int) $basicMetrics->active_suppliers,
                        'most_frequent_supplier' => $mostFrequentSupplier?->name ?? 'No data',
                        'most_frequent_supplier_count' => (int) ($mostFrequentSupplier?->purchase_count ?? 0),
                        'most_frequent_supplier_details' => $mostFrequentSupplier ? [
                            'id' => $mostFrequentSupplier->id,
                            'name' => $mostFrequentSupplier->name,
                            'email' => $mostFrequentSupplier->email,
                            'phone' => $mostFrequentSupplier->phone,
                            'purchase_count' => $mostFrequentSupplier->purchase_count
                        ] : null,
                        'top_supplier_by_amount' => $topSupplierByAmount?->name ?? 'No data',
                        'top_supplier_amount' => (float) ($topSupplierByAmount?->total_amount ?? 0),
                        'top_supplier_details' => $topSupplierByAmount ? [
                            'id' => $topSupplierByAmount->id,
                            'name' => $topSupplierByAmount->name,
                            'email' => $topSupplierByAmount->email,
                            'phone' => $topSupplierByAmount->phone,
                            'total_amount' => (float) $topSupplierByAmount->total_amount
                        ] : null,
                        'recent_purchases' => (int) $basicMetrics->recent_purchases,
                        'total_purchase_amount' => (float) $basicMetrics->total_purchase_amount,
                        'all_time_purchase_amount' => (float) $allTimeMetrics->all_time_purchase_amount,
                        'average_purchase_value' => (float) $allTimeMetrics->average_purchase_value,
                        'purchase_trend' => $purchaseTrend,
                        'recent_daily_deliveries' => $recentDailyDeliveries,
                        'today_deliveries_amount' => (float) $todayDeliveriesAmount,
                        'today_deliveries_count' => (int) $todayDeliveriesCount,
                        'current_month_purchases' => (float) $monthlyPurchaseData->current_month_purchases,
                        'last_month_purchases' => (float) $monthlyPurchaseData->last_month_purchases,
                        'monthly_purchase_growth_percentage' => round($monthlyPurchaseGrowth, 2),
                        'filter_date_from' => $effectiveFrom,
                        'filter_date_to' => $effectiveTo,
                        'generated_at' => Carbon::now()->toISOString()
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to fetch supplier analytics',
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get staff performance analytics with caching and optimized queries
     */
    public function staffPerformanceAnalytics(): JsonResponse
    {
        return Cache::remember(self::CACHE_KEYS['staff'], self::CACHE_DURATION, function () {
            try {
                // Basic staff metrics with single query
                $basicMetrics = DB::table('users')
                    ->whereIn('role', ['admin', 'staff'])
                    ->selectRaw('
                        COUNT(*) as total_staff,
                        SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_staff
                    ')
                    ->first();

                // Staff activity metrics with single query
                $activityMetrics = DB::table('users')
                    ->leftJoin('orders', 'users.id', '=', 'orders.cancelled_by')
                    ->whereIn('users.role', ['admin', 'staff'])
                    ->whereNotNull('orders.cancelled_by')
                    ->selectRaw('
                        COUNT(DISTINCT users.id) as staff_with_activity,
                        users.id as top_staff_id,
                        users.name as top_staff_name,
                        users.role as top_staff_role,
                        users.email as top_staff_email,
                        users.status as top_staff_status,
                        COUNT(orders.id) as top_staff_processed_orders
                    ')
                    ->groupBy('users.id', 'users.name', 'users.role', 'users.email', 'users.status')
                    ->orderBy('top_staff_processed_orders', 'desc')
                    ->first();

                // Staff performance summary with single query
                $staffPerformance = DB::table('users')
                    ->leftJoin('orders', 'users.id', '=', 'orders.cancelled_by')
                    ->whereIn('users.role', ['admin', 'staff'])
                    ->whereNotNull('orders.cancelled_by')
                    ->select('users.id', 'users.name', 'users.role', 'users.email', 'users.status',
                             DB::raw('COUNT(orders.id) as processed_orders'),
                             DB::raw('COUNT(CASE WHEN orders.order_status = ? THEN 1 END) as completed_orders'),
                             DB::raw('COUNT(CASE WHEN orders.order_status = ? THEN 1 END) as cancelled_orders'))
                    ->setBindings([OrderStatus::COMPLETE->value, OrderStatus::CANCELLED->value])
                    ->groupBy('users.id', 'users.name', 'users.role', 'users.email', 'users.status')
                    ->orderBy('processed_orders', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($staff) {
                        return [
                            'id' => $staff->id,
                            'name' => $staff->name,
                            'role' => $staff->role,
                            'email' => $staff->email,
                            'status' => $staff->status,
                            'processed_orders' => $staff->processed_orders,
                            'completed_orders' => $staff->completed_orders,
                            'cancelled_orders' => $staff->cancelled_orders,
                            'completion_rate' => $staff->processed_orders > 0 ? round(($staff->completed_orders / $staff->processed_orders) * 100, 2) : 0
                        ];
                    });

                // Staff status and role breakdown with single query
                $breakdownData = DB::table('users')
                    ->whereIn('role', ['admin', 'staff'])
                    ->selectRaw('
                        status,
                        role,
                        COUNT(*) as count
                    ')
                    ->groupBy('status', 'role')
                    ->get();

                $staffStatusBreakdown = $breakdownData->groupBy('status')->map(function($group) {
                    return ['status' => $group->first()->status, 'count' => $group->sum('count')];
                })->values();

                $staffRoleBreakdown = $breakdownData->groupBy('role')->map(function($group) {
                    return ['role' => $group->first()->role, 'count' => $group->sum('count')];
                })->values();

                return [
                    'status' => 'success',
                    'data' => [
                        'total_staff' => (int) $basicMetrics->total_staff,
                        'active_staff' => (int) $basicMetrics->active_staff,
                        'staff_with_activity' => (int) ($activityMetrics?->staff_with_activity ?? 0),
                        'top_staff_by_activity' => $activityMetrics?->top_staff_name ?? 'No data',
                        'top_staff_processed_orders' => (int) ($activityMetrics?->top_staff_processed_orders ?? 0),
                        'top_staff_details' => $activityMetrics ? [
                            'id' => $activityMetrics->top_staff_id,
                            'name' => $activityMetrics->top_staff_name,
                            'role' => $activityMetrics->top_staff_role,
                            'email' => $activityMetrics->top_staff_email,
                            'status' => $activityMetrics->top_staff_status,
                            'processed_orders' => $activityMetrics->top_staff_processed_orders
                        ] : null,
                        'staff_performance' => $staffPerformance,
                        'staff_status_breakdown' => $staffStatusBreakdown,
                        'staff_role_breakdown' => $staffRoleBreakdown,
                        'generated_at' => Carbon::now()->toISOString()
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to fetch staff performance analytics',
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get comprehensive dashboard analytics with caching
     */
    public function dashboardAnalytics(): JsonResponse
    {
        return Cache::remember(self::CACHE_KEYS['dashboard'], self::CACHE_DURATION, function () {
            try {
                // Get all analytics data
                $inventoryData = $this->inventoryAnalytics()->getData(true);
                $salesData = $this->salesAnalytics()->getData(true);
                $supplierData = $this->supplierAnalytics(new Request([]))->getData(true);
                $staffData = $this->staffPerformanceAnalytics()->getData(true);

                return [
                    'status' => 'success',
                    'data' => [
                        'inventory' => $inventoryData['data'],
                        'sales' => $salesData['data'],
                        'suppliers' => $supplierData['data'],
                        'staff' => $staffData['data'],
                        'generated_at' => Carbon::now()->toISOString()
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to fetch dashboard analytics',
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Clear all analytics cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            foreach (self::CACHE_KEYS as $key) {
                Cache::forget($key);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Analytics cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
