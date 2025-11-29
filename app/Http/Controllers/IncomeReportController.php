<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UtilityExpense;
use App\Models\PayrollRecord;
use App\Models\OtherExpense;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomeReportController extends Controller
{
    /**
     * Display the income report
     */
    public function index(Request $request)
    {
        // Get date filters from request or use defaults
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        // Convert to Carbon instances
        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate = Carbon::parse($dateTo)->endOfDay();
        
        // Calculate total sales from completed orders
        $totalSales = Order::where('order_status', OrderStatus::COMPLETE)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total');
        
        // Calculate total expenses (only non-voided)
        $utilities = UtilityExpense::notVoid()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $payroll = PayrollRecord::where(function($query) {
                $query->whereColumn('is_void', false)
                      ->orWhereNull('is_void');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_salary');
        
        $otherExpenses = OtherExpense::where(function($query) {
                $query->whereColumn('is_void', false)
                      ->orWhereNull('is_void');
            })
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $totalExpenses = $utilities + $payroll + $otherExpenses;
        
        // Calculate net income
        $netIncome = $totalSales - $totalExpenses;
        
        // Calculate profit margin
        $profitMargin = $totalSales > 0 ? ($netIncome / $totalSales) * 100 : 0;
        
        // Get expense breakdown
        $expenseBreakdown = [
            'Utilities' => $utilities,
            'Payroll' => $payroll,
            'Other Expenses' => $otherExpenses,
        ];
        
        // Get monthly breakdown
        $monthlyData = $this->getMonthlyBreakdown($startDate, $endDate);
        
        // Get daily sales trend
        $dailyTrend = $this->getDailyTrend($startDate, $endDate);
        
        return view('reports.income', compact(
            'totalSales',
            'totalExpenses',
            'netIncome',
            'profitMargin',
            'expenseBreakdown',
            'monthlyData',
            'dailyTrend',
            'dateFrom',
            'dateTo'
        ));
    }
    
    /**
     * Get monthly breakdown of income
     */
    private function getMonthlyBreakdown($startDate, $endDate)
    {
        $months = [];
        $current = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->endOfMonth();
        
        while ($current <= $end) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            // Sales for the month
            $sales = Order::where('order_status', OrderStatus::COMPLETE)
                ->whereBetween('order_date', [$monthStart, $monthEnd])
                ->sum('total');
            
            // Expenses for the month (non-voided only)
            $utilities = UtilityExpense::notVoid()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $payroll = PayrollRecord::where(function($query) {
                    $query->where('is_void', false)->orWhereNull('is_void');
                })
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_salary');
            
            $other = OtherExpense::where(function($query) {
                    $query->where('is_void', false)->orWhereNull('is_void');
                })
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $expenses = $utilities + $payroll + $other;
            $income = $sales - $expenses;
            
            $months[] = [
                'month' => $current->format('M Y'),
                'sales' => $sales,
                'expenses' => $expenses,
                'income' => $income,
                'margin' => $sales > 0 ? ($income / $sales) * 100 : 0,
            ];
            
            $current->addMonth();
        }
        
        return $months;
    }
    
    /**
     * Get daily sales trend
     */
    private function getDailyTrend($startDate, $endDate)
    {
        $days = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();
            
            $sales = Order::where('order_status', OrderStatus::COMPLETE)
                ->whereBetween('order_date', [$dayStart, $dayEnd])
                ->sum('total');
            
            $days[] = [
                'date' => $current->format('M d'),
                'sales' => $sales,
            ];
            
            $current->addDay();
        }
        
        return $days;
    }
    
    /**
     * Export income report to CSV
     */
    public function exportCsv(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate = Carbon::parse($dateTo)->endOfDay();
        
        $monthlyData = $this->getMonthlyBreakdown($startDate, $endDate);
        
        $filename = 'income_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($monthlyData) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Month', 'Sales', 'Expenses', 'Net Income', 'Profit Margin %']);
            
            // Data
            foreach ($monthlyData as $data) {
                fputcsv($file, [
                    $data['month'],
                    number_format($data['sales'], 2),
                    number_format($data['expenses'], 2),
                    number_format($data['income'], 2),
                    number_format($data['margin'], 2),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
