<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\UtilityExpense;
use App\Models\PayrollRecord;
use App\Models\OtherExpense;
use App\Models\SalesRecord;
use App\Models\User;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = (int) date('Y');

        // Clear existing data for a clean simulation
        UtilityExpense::query()->delete();
        OtherExpense::query()->delete();
        PayrollRecord::query()->where('year', $year)->delete();

        // Get staff users (fallback to any users if no staff flag/role)
        $staffUsers = User::query()
            ->when(DB::getSchemaBuilder()->hasColumn('users', 'role'), function ($q) {
                $q->whereIn('role', ['staff', 'admin']);
            })
            ->limit(5)
            ->get();

        // Month loop (Jan to Dec)
        for ($month = 1; $month <= 12; $month++) {
            $period = Carbon::create($year, $month)->format('Y-m');

            // Get monthly sales (aligned with SalesAnalytics projections)
            $salesRecord = SalesRecord::where('year', $year)->where('month', $month)->first();
            $monthlySales = $salesRecord?->total_sales ?? rand(130000, 160000);

            // Allocate realistic expense portions (aligned with shop scale)
            // Utilities: 3% - 6% of sales
            $utilitiesTarget = round($monthlySales * (rand(3, 6) / 100), 2);

            // Payroll: 12% - 18% of sales
            $payrollTarget = round($monthlySales * (rand(12, 18) / 100), 2);

            // Other expenses: 2% - 5% of sales
            $otherTarget = round($monthlySales * (rand(2, 5) / 100), 2);

            // --- Create Utility Expenses ---
            $utilityBreakdown = [
                ['type' => 'electricity', 'ratio' => 0.45],
                ['type' => 'water',       'ratio' => 0.12],
                ['type' => 'rent',        'ratio' => 0.28],
                ['type' => 'internet',    'ratio' => 0.10],
                ['type' => 'misc',        'ratio' => 0.05],
            ];

            foreach ($utilityBreakdown as $item) {
                $amount = round($utilitiesTarget * $item['ratio'], 2);

                UtilityExpense::create([
                    'type' => ucfirst($item['type']),
                    'amount' => $amount,
                    'billing_period' => $period,
                    'due_date' => Carbon::create($year, $month, rand(20, 28)),
                    'paid_date' => rand(0, 1) ? Carbon::create($year, $month, rand(10, 28)) : null,
                    'status' => rand(0, 1) ? 'paid' : 'pending',
                    'notes' => 'Simulated utility billing',
                    'created_by' => $staffUsers->first()->id ?? null,
                ]);
            }

            // --- Create Payroll Records ---
            if ($staffUsers->isNotEmpty()) {
                // Daily wage range: 550 - 650 per day; working days per month: 20 - 26
                foreach ($staffUsers as $user) {
                    $dailyRate = rand(550, 650);
                    $workingDays = rand(20, 26);
                    $basic = round($dailyRate * $workingDays, 2);
                    $bonuses = round($basic * (rand(3, 10) / 100), 2);
                    $deductions = round($basic * (rand(0, 5) / 100), 2);
                    $total = round($basic + $bonuses - $deductions, 2);

                    PayrollRecord::create([
                        'user_id' => $user->id,
                        'month' => $month,
                        'year' => $year,
                        'basic_salary' => $basic,
                        'bonuses' => $bonuses,
                        'deductions' => $deductions,
                        'total_salary' => $total,
                        'payment_date' => rand(0, 1) ? Carbon::create($year, $month, rand(25, 28)) : null,
                        'status' => rand(0, 1) ? 'paid' : 'pending',
                        'notes' => 'Simulated payroll record',
                        'created_by' => $staffUsers->first()->id ?? null,
                    ]);
                }
            }

            // --- Create Other Expenses ---
            $otherCategories = [
                ['category' => 'Supplies',    'ratio' => 0.40, 'desc' => 'Consumables & packaging'],
                ['category' => 'Maintenance', 'ratio' => 0.30, 'desc' => 'Equipment maintenance'],
                ['category' => 'Marketing',   'ratio' => 0.20, 'desc' => 'Local promotions'],
                ['category' => 'Transport',   'ratio' => 0.10, 'desc' => 'Delivery fuel & fees'],
            ];

            foreach ($otherCategories as $cat) {
                $amount = round($otherTarget * $cat['ratio'], 2);
                OtherExpense::create([
                    'category' => $cat['category'],
                    'description' => $cat['desc'],
                    'amount' => $amount,
                    'expense_date' => Carbon::create($year, $month, rand(5, 25)),
                    'notes' => 'Simulated operational expense',
                    'created_by' => $staffUsers->first()->id ?? null,
                ]);
            }
        }
    }
}
