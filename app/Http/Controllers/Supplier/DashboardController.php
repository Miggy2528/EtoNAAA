<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the supplier dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        $supplier = $user->supplier;
        
        // If no supplier record exists, show empty stats
        if (!$supplier) {
            $stats = [
                'total_products' => 0,
                'pending_purchases' => 0,
                'completed_purchases' => 0,
                'total_revenue' => 0,
                'total_procurements' => 0,
                'delivery_rating' => 0,
                'on_time_percentage' => 0,
            ];
            return view('supplier.dashboard', compact('user', 'stats', 'supplier'));
        }

        // Get real statistics from database
        $stats = [
            'total_products' => $supplier->products()->count(),
            'pending_purchases' => $supplier->purchases()->where('status', 'pending')->count(),
            'completed_purchases' => $supplier->purchases()->where('status', 'approved')->count(),
            'total_revenue' => $supplier->purchases()->where('status', 'approved')->sum('total_amount'),
            'total_procurements' => $supplier->procurements()->count(),
            'delivery_rating' => $supplier->delivery_rating ?? 0,
            'on_time_percentage' => $supplier->on_time_percentage ?? 0,
        ];

        return view('supplier.dashboard', compact('user', 'stats', 'supplier'));
    }
}
