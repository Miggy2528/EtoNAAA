<?php

namespace App\Http\Controllers;

use App\Models\UtilityExpense;
use App\Models\PayrollRecord;
use App\Models\OtherExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display the expense management dashboard
     */
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $currentYear = Carbon::now()->year;
        
        // Monthly summary
        $monthlyUtilities = UtilityExpense::notVoid()->where('billing_period', $currentMonth)->sum('amount');
        $monthlyPayroll = PayrollRecord::where('month', Carbon::now()->month)
            ->where('year', $currentYear)
            ->sum('total_salary');
        $monthlyOther = OtherExpense::whereYear('expense_date', $currentYear)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->sum('amount');
        
        $monthlyTotal = $monthlyUtilities + $monthlyPayroll + $monthlyOther;
        
        // Pending payments
        $pendingUtilities = UtilityExpense::notVoid()->where('status', 'pending')->count();
        $pendingPayroll = PayrollRecord::where('status', 'pending')->count();
        
        // Yearly total
        $yearlyUtilities = UtilityExpense::notVoid()->whereYear('created_at', $currentYear)->sum('amount');
        $yearlyPayroll = PayrollRecord::where('year', $currentYear)->sum('total_salary');
        $yearlyOther = OtherExpense::whereYear('expense_date', $currentYear)->sum('amount');
        $yearlyTotal = $yearlyUtilities + $yearlyPayroll + $yearlyOther;
        
        // Recent expenses
        $recentUtilities = UtilityExpense::notVoid()->with('creator')->latest()->limit(5)->get();
        $recentPayroll = PayrollRecord::with(['user', 'staff', 'creator'])->latest()->limit(5)->get();
        $recentOther = OtherExpense::with('creator')->latest()->limit(5)->get();
        
        // Monthly breakdown for chart (last 6 months)
        $monthlyBreakdown = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $period = $month->format('Y-m');
            $monthName = $month->format('M Y');
            
            $utilities = UtilityExpense::notVoid()->where('billing_period', $period)->sum('amount');
            $payroll = PayrollRecord::where('month', $month->month)
                ->where('year', $month->year)
                ->sum('total_salary');
            $other = OtherExpense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');
            
            $monthlyBreakdown[] = [
                'month' => $monthName,
                'utilities' => $utilities,
                'payroll' => $payroll,
                'other' => $other,
                'total' => $utilities + $payroll + $other,
            ];
        }
        
        return view('expenses.index', compact(
            'monthlyUtilities',
            'monthlyPayroll',
            'monthlyOther',
            'monthlyTotal',
            'pendingUtilities',
            'pendingPayroll',
            'yearlyUtilities',
            'yearlyPayroll',
            'yearlyOther',
            'yearlyTotal',
            'recentUtilities',
            'recentPayroll',
            'recentOther',
            'monthlyBreakdown'
        ));
    }

    /**
     * Display all voided expenses
     */
    public function voided()
    {
        // Get all voided expenses from all categories
        $voidedUtilities = UtilityExpense::voided()->with(['voidedBy'])->latest('voided_at')->get();
        $voidedPayroll = PayrollRecord::latest()->get(); // Payroll doesn't have void yet
        $voidedOther = OtherExpense::latest()->get(); // Other doesn't have void yet

        // Combine all voided expenses
        $allVoidedExpenses = collect();
        
        foreach ($voidedUtilities as $expense) {
            $allVoidedExpenses->push([
                'type' => 'Utility',
                'category' => $expense->type,
                'description' => $expense->notes ?? $expense->type,
                'amount' => $expense->amount,
                'date' => $expense->billing_period,
                'voided_at' => $expense->voided_at,
                'voided_by' => $expense->voidedBy->name ?? 'N/A',
                'void_reason' => $expense->void_reason,
            ]);
        }

        // Sort by voided_at descending
        $allVoidedExpenses = $allVoidedExpenses->sortByDesc('voided_at');

        return view('expenses.voided', compact('allVoidedExpenses'));
    }
}
