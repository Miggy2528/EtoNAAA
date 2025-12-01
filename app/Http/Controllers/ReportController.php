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

class ReportController extends Controller
{
    /**
     * Display the reports index page
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Display inventory report page
     */
    public function inventory()
    {
        return view('reports.inventory');
    }

    /**
     * Display sales report page
     */
    public function sales()
    {
        return view('reports.sales');
    }

    /**
     * Display purchases report page (Supplier Analytics)
     */
    public function purchases()
    {
        return view('reports.purchases');
    }

    /**
     * Display stock levels report page (Staff Performance Analytics)
     */
    public function stockLevels()
    {
        return view('reports.stock-levels');
    }

    /**
     * Export inventory report
     */
    public function exportInventory()
    {
        // TODO: Implement inventory export functionality
        return response()->json(['message' => 'Inventory export not implemented yet']);
    }

    /**
     * Export sales report
     */
    public function exportSales()
    {
        // TODO: Implement sales export functionality
        return response()->json(['message' => 'Sales export not implemented yet']);
    }

    /**
     * Get inventory analytics
     */
    public function inventoryAnalytics(): JsonResponse
    {
        try {
            // Total items count
            $totalItems = Product::count();
            
            // Total stock count (sum of all product quantities)
            $totalStockCount = Product::sum('quantity');
            
            // Low stock items (using quantity_alert field or default threshold of 10)
            $lowStockItems = Product::where(function($query) {
                $query->where('quantity', '<', DB::raw('COALESCE(quantity_alert, 10)'))
                      ->orWhere('quantity', '<', 10);
            })->count();
            
            // Soon to expire items (within 7 days from expiration_date)
            $soonToExpireItems = Product::where('expiration_date', '<=', Carbon::now()->addDays(7))
                ->where('expiration_date', '>', Carbon::now())
                ->whereNotNull('expiration_date')
                ->count();
            
            // Get low stock products details with category and unit info
            $lowStockProducts = Product::with(['category', 'unit'])
                ->where(function($query) {
                    $query->where('quantity', '<', DB::raw('COALESCE(quantity_alert, 10)'))
                          ->orWhere('quantity', '<', 10);
                })
                ->select('id', 'name', 'quantity', 'expiration_date', 'category_id', 'unit_id', 'quantity_alert')
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

            // Get soon to expire products details
            $soonToExpireProducts = Product::with(['category', 'unit'])
                ->where('expiration_date', '<=', Carbon::now()->addDays(7))
                ->where('expiration_date', '>', Carbon::now())
                ->whereNotNull('expiration_date')
                ->select('id', 'name', 'quantity', 'expiration_date', 'category_id', 'unit_id')
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

            // Calculate total inventory value
            $totalInventoryValue = Product::sum(DB::raw('quantity * COALESCE(price_per_kg, 0)'));

            // Get category-wise inventory breakdown
            $categoryBreakdown = Product::with('category')
                ->select('category_id', DB::raw('COUNT(*) as product_count'), DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('category_id')
                ->get()
                ->map(function($item) {
                    return [
                        'category' => $item->category?->name ?? 'Uncategorized',
                        'product_count' => $item->product_count,
                        'total_quantity' => $item->total_quantity
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_items' => $totalItems,
                    'total_stock_count' => $totalStockCount,
                    'low_stock_items' => $lowStockItems,
                    'soon_to_expire_items' => $soonToExpireItems,
                    'total_inventory_value' => round($totalInventoryValue, 2),
                    'low_stock_products' => $lowStockProducts,
                    'soon_to_expire_products' => $soonToExpireProducts,
                    'category_breakdown' => $categoryBreakdown,
                    'generated_at' => Carbon::now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch inventory analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales analytics
     */
    public function salesAnalytics(Request $request): JsonResponse
    {
        try {
            // Get date range from request
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            // Build base query
            $ordersQuery = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1]);
            
            if ($dateFrom && $dateTo) {
                $ordersQuery->whereBetween('order_date', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay()
                ]);
            } elseif ($dateFrom) {
                $ordersQuery->where('order_date', '>=', Carbon::parse($dateFrom)->startOfDay());
            } elseif ($dateTo) {
                $ordersQuery->where('order_date', '<=', Carbon::parse($dateTo)->endOfDay());
            }
            
            // Total sales (sum of all completed orders)
            $totalSales = (clone $ordersQuery)->sum('total');
            
            // Average daily sales (last 30 days or date range)
            $daysCount = 30;
            if ($dateFrom && $dateTo) {
                $daysCount = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
            }
            $averageDailySales = $daysCount > 0 ? $totalSales / $daysCount : 0;
            
            // Highest selling product by quantity
            $topProductQuery = OrderDetails::select('products.name', 'products.id', DB::raw('SUM(order_details.quantity) as total_quantity'))
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->whereIn('orders.order_status', [OrderStatus::COMPLETE, '1', 1]);
                
            if ($dateFrom && $dateTo) {
                $topProductQuery->whereBetween('orders.order_date', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay()
                ]);
            } elseif ($dateFrom) {
                $topProductQuery->where('orders.order_date', '>=', Carbon::parse($dateFrom)->startOfDay());
            } elseif ($dateTo) {
                $topProductQuery->where('orders.order_date', '<=', Carbon::parse($dateTo)->endOfDay());
            }
                
            $topProductByQuantity = $topProductQuery->groupBy('products.id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->first();
            
            // Highest selling product by revenue
            $topProductByRevenue = OrderDetails::select('products.name', 'products.id', DB::raw('SUM(order_details.total) as total_revenue'))
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->whereIn('orders.order_status', [OrderStatus::COMPLETE, '1', 1])
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_revenue', 'desc')
                ->first();
            
            // Sales trend over the last 7 days with detailed data
            $salesTrend = [];
            $salesTrendDetails = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                
                $dailySales = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])
                    ->whereDate('order_date', $dateStr)
                    ->sum('total');
                
                $dailyOrders = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])
                    ->whereDate('order_date', $dateStr)
                    ->count();
                
                $salesTrend[] = (float) $dailySales;
                $salesTrendDetails[] = [
                    'date' => $dateStr,
                    'sales' => (float) $dailySales,
                    'orders' => $dailyOrders,
                    'average_order_value' => $dailyOrders > 0 ? round($dailySales / $dailyOrders, 2) : 0
                ];
            }
            
            // Additional metrics
            $totalOrders = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])->count();
            $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
            
            // Monthly sales comparison
            $currentMonthSales = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])
                ->whereMonth('order_date', Carbon::now()->month)
                ->whereYear('order_date', Carbon::now()->year)
                ->sum('total');
            
            $lastMonthSales = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])
                ->whereMonth('order_date', Carbon::now()->subMonth()->month)
                ->whereYear('order_date', Carbon::now()->subMonth()->year)
                ->sum('total');
            
            $monthlyGrowth = $lastMonthSales > 0 ? (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
            
            // Top 5 selling products
            $topProducts = OrderDetails::select('products.name', 'products.id', 
                    DB::raw('SUM(order_details.quantity) as total_quantity'),
                    DB::raw('SUM(order_details.total) as total_revenue'))
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->whereIn('orders.order_status', [OrderStatus::COMPLETE, '1', 1])
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();

            // Sales by payment type
            $salesByPaymentType = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])
                ->select('payment_type', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total) as total_amount'))
                ->groupBy('payment_type')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_sales' => (float) $totalSales,
                    'average_daily_sales' => (float) $averageDailySales,
                    'top_product_by_quantity' => $topProductByQuantity ? $topProductByQuantity->name : 'No sales data',
                    'top_product_quantity' => $topProductByQuantity ? $topProductByQuantity->total_quantity : 0,
                    'top_product_by_revenue' => $topProductByRevenue ? $topProductByRevenue->name : 'No sales data',
                    'top_product_revenue' => $topProductByRevenue ? (float) $topProductByRevenue->total_revenue : 0,
                    'trend' => $salesTrend,
                    'trend_details' => $salesTrendDetails,
                    'total_orders' => $totalOrders,
                    'average_order_value' => (float) $averageOrderValue,
                    'current_month_sales' => (float) $currentMonthSales,
                    'last_month_sales' => (float) $lastMonthSales,
                    'monthly_growth_percentage' => round($monthlyGrowth, 2),
                    'top_products' => $topProducts,
                    'sales_by_payment_type' => $salesByPaymentType,
                    'generated_at' => Carbon::now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sales analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier analytics
     */
    public function supplierAnalytics(): JsonResponse
    {
        try {
            // Total suppliers
            $totalSuppliers = Supplier::count();
            
            // Active suppliers (with purchases in the last 30 days)
            $activeSuppliers = Supplier::whereHas('purchases', function ($query) {
                $query->where('date', '>=', Carbon::now()->subDays(30));
            })->count();
            
            // Most frequent supplier (by purchase count)
            $mostFrequentSupplier = Supplier::select('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone', 
                    DB::raw('COUNT(purchases.id) as purchase_count'))
                ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone')
                ->orderBy('purchase_count', 'desc')
                ->first();
            
            // Supplier with highest total purchase amount
            $topSupplierByAmount = Supplier::select('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone',
                    DB::raw('SUM(purchases.total_amount) as total_amount'))
                ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                ->where('purchases.status', PurchaseStatus::APPROVED)
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone')
                ->orderBy('total_amount', 'desc')
                ->first();
            
            // Recent purchases count (last 30 days)
            $recentPurchases = Purchase::where('date', '>=', Carbon::now()->subDays(30))->count();
            
            // Total purchase amount (last 30 days)
            $totalPurchaseAmount = Purchase::where('date', '>=', Carbon::now()->subDays(30))
                ->where('status', PurchaseStatus::APPROVED)
                ->sum('total_amount');

            // All-time total purchase amount
            $allTimePurchaseAmount = Purchase::where('status', PurchaseStatus::APPROVED)
                ->sum('total_amount');

            // Average purchase value
            $averagePurchaseValue = Purchase::where('status', PurchaseStatus::APPROVED)
                ->avg('total_amount');

            // Top 5 suppliers by purchase count
            $topSuppliersByCount = Supplier::select('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone',
                    DB::raw('COUNT(purchases.id) as purchase_count'),
                    DB::raw('SUM(purchases.total_amount) as total_amount'))
                ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                ->where('purchases.status', PurchaseStatus::APPROVED)
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone')
                ->orderBy('purchase_count', 'desc')
                ->limit(5)
                ->get();

            // Top 5 suppliers by purchase amount
            $topSuppliersByAmount = Supplier::select('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone',
                    DB::raw('COUNT(purchases.id) as purchase_count'),
                    DB::raw('SUM(purchases.total_amount) as total_amount'))
                ->leftJoin('purchases', 'suppliers.id', '=', 'purchases.supplier_id')
                ->where('purchases.status', PurchaseStatus::APPROVED)
                ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone')
                ->orderBy('total_amount', 'desc')
                ->limit(5)
                ->get();

            // Purchase trend over last 7 days
            $purchaseTrend = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $dailyPurchases = Purchase::where('date', $date)
                    ->where('status', PurchaseStatus::APPROVED)
                    ->sum('total_amount');
                $purchaseTrend[] = (float) $dailyPurchases;
            }

            // Supplier status breakdown
            $supplierStatusBreakdown = Supplier::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            // Monthly purchase comparison
            $currentMonthPurchases = Purchase::where('status', PurchaseStatus::APPROVED)
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->sum('total_amount');

            $lastMonthPurchases = Purchase::where('status', PurchaseStatus::APPROVED)
                ->whereMonth('date', Carbon::now()->subMonth()->month)
                ->whereYear('date', Carbon::now()->subMonth()->year)
                ->sum('total_amount');

            $monthlyPurchaseGrowth = $lastMonthPurchases > 0 ? (($currentMonthPurchases - $lastMonthPurchases) / $lastMonthPurchases) * 100 : 0;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_suppliers' => $totalSuppliers,
                    'active_suppliers' => $activeSuppliers,
                    'most_frequent_supplier' => $mostFrequentSupplier ? $mostFrequentSupplier->name : 'No data',
                    'most_frequent_supplier_count' => $mostFrequentSupplier ? $mostFrequentSupplier->purchase_count : 0,
                    'most_frequent_supplier_details' => $mostFrequentSupplier ? [
                        'id' => $mostFrequentSupplier->id,
                        'name' => $mostFrequentSupplier->name,
                        'email' => $mostFrequentSupplier->email,
                        'phone' => $mostFrequentSupplier->phone,
                        'purchase_count' => $mostFrequentSupplier->purchase_count
                    ] : null,
                    'top_supplier_by_amount' => $topSupplierByAmount ? $topSupplierByAmount->name : 'No data',
                    'top_supplier_amount' => (float) ($topSupplierByAmount ? $topSupplierByAmount->total_amount : 0),
                    'top_supplier_details' => $topSupplierByAmount ? [
                        'id' => $topSupplierByAmount->id,
                        'name' => $topSupplierByAmount->name,
                        'email' => $topSupplierByAmount->email,
                        'phone' => $topSupplierByAmount->phone,
                        'total_amount' => (float) $topSupplierByAmount->total_amount
                    ] : null,
                    'recent_purchases' => $recentPurchases,
                    'total_purchase_amount' => (float) $totalPurchaseAmount,
                    'all_time_purchase_amount' => (float) $allTimePurchaseAmount,
                    'average_purchase_value' => (float) $averagePurchaseValue,
                    'top_suppliers_by_count' => $topSuppliersByCount,
                    'top_suppliers_by_amount' => $topSuppliersByAmount,
                    'purchase_trend' => $purchaseTrend,
                    'supplier_status_breakdown' => $supplierStatusBreakdown,
                    'current_month_purchases' => (float) $currentMonthPurchases,
                    'last_month_purchases' => (float) $lastMonthPurchases,
                    'monthly_purchase_growth_percentage' => round($monthlyPurchaseGrowth, 2),
                    'generated_at' => Carbon::now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch supplier analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get staff performance analytics
     */
    public function staffPerformanceAnalytics(): JsonResponse
    {
        try {
            // Total staff count (admin and staff roles)
            $totalStaff = User::whereIn('role', ['admin', 'staff'])->count();
            
            // Active staff count (not suspended or inactive)
            $activeStaff = User::whereIn('role', ['admin', 'staff'])
                ->where('status', 'active')
                ->count();
            
            // Staff with order management activity (orders they've cancelled or processed)
            $staffWithActivity = User::whereIn('role', ['admin', 'staff'])
                ->where(function($query) {
                    $query->whereHas('orders', function($q) {
                        $q->where('order_status', OrderStatus::COMPLETE);
                    })->orWhereHas('cancelledOrders', function($q) {
                        $q->whereNotNull('cancelled_by');
                    });
                })
                ->count();
            
            // Top performing staff by order processing (using cancelled_by as proxy for staff activity)
            $topStaffByActivity = User::select('users.id', 'users.name', 'users.role', 'users.email', 'users.status',
                    DB::raw('COUNT(orders.id) as processed_orders'))
                ->leftJoin('orders', 'users.id', '=', 'orders.cancelled_by')
                ->whereIn('users.role', ['admin', 'staff'])
                ->whereNotNull('orders.cancelled_by')
                ->groupBy('users.id', 'users.name', 'users.role', 'users.email', 'users.status')
                ->orderBy('processed_orders', 'desc')
                ->first();
            
            // Staff performance summary (based on order management activity)
            $staffPerformance = User::select('users.id', 'users.name', 'users.role', 'users.email', 'users.status',
                    DB::raw('COUNT(orders.id) as processed_orders'),
                    DB::raw('COUNT(CASE WHEN orders.order_status = ' . OrderStatus::COMPLETE->value . ' THEN 1 END) as completed_orders'),
                    DB::raw('COUNT(CASE WHEN orders.order_status = ' . OrderStatus::CANCELLED->value . ' THEN 1 END) as cancelled_orders'))
                ->leftJoin('orders', 'users.id', '=', 'orders.cancelled_by')
                ->whereIn('users.role', ['admin', 'staff'])
                ->whereNotNull('orders.cancelled_by')
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

            // Staff activity over last 30 days
            $staffActivity = User::select('users.id', 'users.name', 'users.role',
                    DB::raw('COUNT(orders.id) as recent_activity'))
                ->leftJoin('orders', 'users.id', '=', 'orders.cancelled_by')
                ->whereIn('users.role', ['admin', 'staff'])
                ->where('orders.created_at', '>=', Carbon::now()->subDays(30))
                ->whereNotNull('orders.cancelled_by')
                ->groupBy('users.id', 'users.name', 'users.role')
                ->orderBy('recent_activity', 'desc')
                ->get();

            // Staff status breakdown
            $staffStatusBreakdown = User::whereIn('role', ['admin', 'staff'])
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            // Staff role breakdown
            $staffRoleBreakdown = User::whereIn('role', ['admin', 'staff'])
                ->select('role', DB::raw('COUNT(*) as count'))
                ->groupBy('role')
                ->get();

            // Monthly staff activity comparison
            $currentMonthActivity = User::whereIn('role', ['admin', 'staff'])
                ->whereHas('cancelledOrders', function($query) {
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                })
                ->count();

            $lastMonthActivity = User::whereIn('role', ['admin', 'staff'])
                ->whereHas('cancelledOrders', function($query) {
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                          ->whereYear('created_at', Carbon::now()->subMonth()->year);
                })
                ->count();

            $monthlyActivityGrowth = $lastMonthActivity > 0 ? (($currentMonthActivity - $lastMonthActivity) / $lastMonthActivity) * 100 : 0;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_staff' => $totalStaff,
                    'active_staff' => $activeStaff,
                    'staff_with_activity' => $staffWithActivity,
                    'top_staff_by_activity' => $topStaffByActivity ? $topStaffByActivity->name : 'No data',
                    'top_staff_processed_orders' => $topStaffByActivity ? $topStaffByActivity->processed_orders : 0,
                    'top_staff_details' => $topStaffByActivity ? [
                        'id' => $topStaffByActivity->id,
                        'name' => $topStaffByActivity->name,
                        'role' => $topStaffByActivity->role,
                        'email' => $topStaffByActivity->email,
                        'status' => $topStaffByActivity->status,
                        'processed_orders' => $topStaffByActivity->processed_orders
                    ] : null,
                    'staff_performance' => $staffPerformance,
                    'staff_activity' => $staffActivity,
                    'staff_status_breakdown' => $staffStatusBreakdown,
                    'staff_role_breakdown' => $staffRoleBreakdown,
                    'current_month_activity' => $currentMonthActivity,
                    'last_month_activity' => $lastMonthActivity,
                    'monthly_activity_growth_percentage' => round($monthlyActivityGrowth, 2),
                    'generated_at' => Carbon::now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch staff performance analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive dashboard analytics
     */
    public function dashboardAnalytics(): JsonResponse
    {
        try {
            // Get all analytics in one call
            $inventoryData = $this->inventoryAnalytics()->getData(true);
            $salesData = $this->salesAnalytics()->getData(true);
            $supplierData = $this->supplierAnalytics()->getData(true);
            $staffData = $this->staffPerformanceAnalytics()->getData(true);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'inventory' => $inventoryData['data'],
                    'sales' => $salesData['data'],
                    'suppliers' => $supplierData['data'],
                    'staff' => $staffData['data'],
                    'generated_at' => Carbon::now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch dashboard analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 