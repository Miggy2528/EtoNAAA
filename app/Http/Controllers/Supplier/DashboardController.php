<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the supplier dashboard with comprehensive transaction tracking.
     */
    public function index(): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;
        
        // If no supplier record exists, show empty stats
        if (!$supplier) {
            $stats = [
                'pending_purchases' => 0,
                'completed_purchases' => 0,
                'total_revenue' => 0,
                'total_procurements' => 0,
                'delivery_rating' => 0,
                'on_time_percentage' => 0,
                'active_orders' => 0,
                'pending_deliveries' => 0,
            ];
            
            $recentPurchases = collect([]);
            $recentProcurements = collect([]);
            $monthlyRevenue = [];
            
            return view('supplier.dashboard', compact('user', 'stats', 'supplier', 'recentPurchases', 'recentProcurements', 'monthlyRevenue'));
        }

        // Get comprehensive statistics from database
        $stats = [
            'pending_purchases' => $supplier->purchases()->where('status', 'pending')->count(),
            'completed_purchases' => $supplier->purchases()->where('status', 'approved')->count(),
            'total_revenue' => $supplier->purchases()->where('status', 'approved')->sum('total_amount'),
            'total_procurements' => $supplier->procurements()->count(),
            'delivery_rating' => $supplier->delivery_rating ?? 0,
            'on_time_percentage' => $supplier->on_time_percentage ?? 0,
            'active_orders' => $supplier->purchases()->whereIn('status', ['pending', 'processing'])->count(),
            'pending_deliveries' => $supplier->procurements()->where('status', 'pending')->count(),
        ];

        // Get recent purchases (last 5)
        $recentPurchases = $supplier->purchases()
            ->with(['details.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent procurements (last 5)
        $recentProcurements = $supplier->procurements()
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get monthly revenue for chart (last 6 months)
        $monthlyRevenue = $this->getMonthlyRevenue($supplier->id);

        return view('supplier.dashboard', compact('user', 'stats', 'supplier', 'recentPurchases', 'recentProcurements', 'monthlyRevenue'));
    }

    /**
     * Get monthly revenue data for charts
     */
    private function getMonthlyRevenue($supplierId)
    {
        $months = [];
        $revenue = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            
            $monthRevenue = DB::table('purchases')
                ->where('supplier_id', $supplierId)
                ->where('status', 'approved')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
                
            $revenue[] = $monthRevenue;
        }
        
        return [
            'months' => $months,
            'revenue' => $revenue,
        ];
    }
}
