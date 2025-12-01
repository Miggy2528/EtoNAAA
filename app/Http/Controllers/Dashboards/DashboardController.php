<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\MeatCut;
use App\Services\AdminNotificationService;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Use caching for dashboard data (10 minutes)
        $dashboardData = Cache::remember('dashboard_data', 600, function() {
            // Orders statistics with optimized queries
            $orders = Order::count();
            $completedOrders = Order::whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])->count();
            $todayOrders = Order::whereDate('created_at', today())->count();
            $pendingOrders = Order::where('order_status', OrderStatus::PENDING)->count();

            // Products and Categories with optimized queries
            $products = Product::count();
            $categories = Category::count();
            
            // Product-specific statistics with optimized queries
            $availableProducts = Product::where('quantity', '>', 0)->count();
            $lowStockProducts = Product::whereColumn('quantity', '<=', 'quantity_alert')
                ->where('quantity', '>', 0)
                ->count();
            $outOfStockProducts = Product::where('quantity', 0)->count();

            // Meat-specific statistics (keeping for backward compatibility)
            $totalMeatCuts = MeatCut::count();
            $availableMeatCuts = MeatCut::where('is_available', true)
                ->where('quantity', '>', 0)
                ->count();
            $lowStockMeatCuts = MeatCut::whereColumn('quantity', '<=', 'minimum_stock_level')
                ->count();

            $meatByAnimalType = MeatCut::selectRaw('animal_type, COUNT(*) as count')
                ->groupBy('animal_type')
                ->get()
                ->pluck('count', 'animal_type')
                ->toArray();
                
            return compact(
                'orders',
                'completedOrders',
                'todayOrders',
                'pendingOrders',
                'products',
                'categories',
                'availableProducts',
                'lowStockProducts',
                'outOfStockProducts',
                'totalMeatCuts',
                'availableMeatCuts',
                'lowStockMeatCuts',
                'meatByAnimalType'
            );
        });

        // Notification statistics (don't cache these as they change frequently)
        $notificationService = app(AdminNotificationService::class);
        $unreadNotifications = $notificationService->getUnreadCount();
        $notifications = $notificationService->getRecentNotifications(5);

        // Merge all data
        $data = array_merge($dashboardData, compact('unreadNotifications', 'notifications'));

        return view('dashboard', $data);
    }
}