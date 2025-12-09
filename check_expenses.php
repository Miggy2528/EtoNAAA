<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UtilityExpense;
use App\Models\PayrollRecord;
use App\Models\OtherExpense;
use Carbon\Carbon;

echo "=== Checking Expense Data ===\n\n";

// Check UtilityExpense
echo "UtilityExpense:\n";
echo "  Total count: " . UtilityExpense::count() . "\n";
echo "  notVoid() scope count: " . UtilityExpense::notVoid()->count() . "\n";
echo "  where is_void=false count: " . UtilityExpense::where('is_void', false)->count() . "\n";
echo "  where is_void=false OR NULL: " . UtilityExpense::where(function($q) { $q->where('is_void', false)->orWhereNull('is_void'); })->count() . "\n";

$util = UtilityExpense::first();
if ($util) {
    echo "\n  First record:\n";
    echo "    ID: {$util->id}\n";
    echo "    is_void: " . var_export($util->is_void, true) . "\n";
    echo "    is_void (raw): " . var_export($util->getAttributes()['is_void'], true) . "\n";
    echo "    amount: {$util->amount}\n";
    echo "    created_at: {$util->created_at}\n";
}

// Check PayrollRecord
echo "\n\nPayrollRecord:\n";
echo "  Total count: " . PayrollRecord::count() . "\n";
echo "  where is_void=false OR NULL: " . PayrollRecord::where(function($q) { $q->where('is_void', false)->orWhereNull('is_void'); })->count() . "\n";

$payroll = PayrollRecord::first();
if ($payroll) {
    echo "\n  First record:\n";
    echo "    ID: {$payroll->id}\n";
    echo "    is_void: " . var_export($payroll->is_void ?? 'NULL', true) . "\n";
    echo "    total_salary: {$payroll->total_salary}\n";
    echo "    created_at: {$payroll->created_at}\n";
}

// Check OtherExpense
echo "\n\nOtherExpense:\n";
echo "  Total count: " . OtherExpense::count() . "\n";

// Test date range queries
echo "\n\n=== Testing Date Range Queries ===\n";
$currentYear = date('Y');
$currentMonth = date('n');

echo "Current Year: $currentYear\n";
echo "Current Month: $currentMonth\n\n";

for ($month = 1; $month <= min(12, $currentMonth); $month++) {
    $monthStart = Carbon::create($currentYear, $month, 1)->startOfMonth();
    $monthEnd = Carbon::create($currentYear, $month, 1)->endOfMonth();
    
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
    
    echo "Month $month (" . date('F', mktime(0, 0, 0, $month, 1)) . "):\n";
    echo "  Utilities: ₱" . number_format($utilities, 2) . "\n";
    echo "  Payroll: ₱" . number_format($payroll, 2) . "\n";
    echo "  Other: ₱" . number_format($other, 2) . "\n";
    echo "  Total: ₱" . number_format($utilities + $payroll + $other, 2) . "\n\n";
}
