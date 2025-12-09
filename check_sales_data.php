<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Sales Records Summary:\n";
echo "=====================\n\n";

$results = DB::table('sales_records')
    ->select('year', DB::raw('COUNT(*) as count'))
    ->groupBy('year')
    ->orderBy('year')
    ->get();

foreach ($results as $row) {
    echo "Year {$row->year}: {$row->count} records\n";
}

echo "\nTotal records: " . DB::table('sales_records')->count() . "\n";

echo "\nYearly Summary (Aggregated):\n";
echo "============================\n\n";

$yearlyData = DB::table('sales_records')
    ->select(
        'year',
        DB::raw('SUM(total_sales) as total_sales'),
        DB::raw('SUM(total_expenses) as total_expenses'),
        DB::raw('SUM(net_profit) as net_profit')
    )
    ->where('year', '<', 2025)
    ->groupBy('year')
    ->orderBy('year')
    ->get();

foreach ($yearlyData as $year) {
    echo "Year {$year->year}:\n";
    echo "  Total Sales: ₱" . number_format($year->total_sales, 2) . "\n";
    echo "  Total Expenses: ₱" . number_format($year->total_expenses, 2) . "\n";
    echo "  Net Profit: ₱" . number_format($year->net_profit, 2) . "\n\n";
}
