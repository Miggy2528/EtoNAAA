<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PayrollRecord;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;

class PayrollRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generates payroll data for January to October 2025
     * Aligned with staff daily wage range: 550-650 PHP
     * Working days: 20-26 per month
     */
    public function run(): void
    {
        // Clear existing payroll records
        PayrollRecord::truncate();
        
        // Get all active staff
        $staffMembers = Staff::where('status', 'active')->get();
        
        if ($staffMembers->isEmpty()) {
            $this->command->warn('No active staff found. Please seed staff data first.');
            return;
        }
        
        // Get admin user for created_by field
        $admin = User::where('role', 'admin')->first();
        
        // Current year
        $year = 2025;
        
        // Generate payroll for January to October
        for ($month = 1; $month <= 10; $month++) {
            foreach ($staffMembers as $staff) {
                // Generate realistic daily rate based on position
                $dailyRate = $this->getDailyRateByPosition($staff->position);
                
                // Generate realistic working days (20-26 days per month)
                $workingDays = rand(20, 26);
                
                // Calculate basic salary
                $basicSalary = $dailyRate * $workingDays;
                
                // Generate bonuses (3-10% of basic salary, some staff get none)
                $bonuses = rand(0, 100) > 30 ? round($basicSalary * (rand(3, 10) / 100), 2) : 0;
                
                // Generate deductions (0-5% of basic salary for SSS, PhilHealth, Pag-IBIG, tardiness, etc.)
                $deductions = round($basicSalary * (rand(0, 5) / 100), 2);
                
                // Calculate total salary
                $totalSalary = $basicSalary + $bonuses - $deductions;
                
                // Payment date (usually 5th-10th of the following month, or end of current month)
                $paymentDate = rand(0, 1) 
                    ? Carbon::create($year, $month, 1)->addMonth()->setDay(rand(5, 10))
                    : Carbon::create($year, $month, 1)->endOfMonth()->subDays(rand(0, 2));
                
                // Status (90% paid, 10% pending for current/recent months)
                $status = ($month >= 9) ? (rand(0, 100) > 50 ? 'pending' : 'paid') : 'paid';
                
                // Notes for some records
                $notes = $this->generateNotes($staff->position, $bonuses, $deductions);
                
                PayrollRecord::create([
                    'staff_id' => $staff->id,
                    'user_id' => null, // Staff table is separate from users
                    'month' => $month,
                    'year' => $year,
                    'working_days' => $workingDays,
                    'daily_rate' => $dailyRate,
                    'basic_salary' => $basicSalary,
                    'bonuses' => $bonuses,
                    'deductions' => $deductions,
                    'total_salary' => $totalSalary,
                    'payment_date' => $paymentDate,
                    'status' => $status,
                    'notes' => $notes,
                    'created_by' => $admin->id ?? null,
                ]);
            }
        }
        
        $totalRecords = PayrollRecord::count();
        $totalPaid = PayrollRecord::where('status', 'paid')->sum('total_salary');
        $totalPending = PayrollRecord::where('status', 'pending')->sum('total_salary');
        
        $this->command->info('✅ Payroll records seeded successfully!');
        $this->command->info('   📊 Total records: ' . $totalRecords);
        $this->command->info('   👥 Staff members: ' . $staffMembers->count());
        $this->command->info('   📅 Months covered: January - October 2025');
        $this->command->info('   💰 Total paid: ₱' . number_format($totalPaid, 2));
        $this->command->info('   ⏳ Total pending: ₱' . number_format($totalPending, 2));
    }
    
    /**
     * Get daily rate based on staff position
     * Following memory specification: 550-650 PHP daily wage range
     */
    private function getDailyRateByPosition(string $position): float
    {
        $rates = [
            'Supervisor' => rand(620, 650),
            'Butcher' => rand(580, 630),
            'Cashier' => rand(550, 590),
            'Inventory Clerk' => rand(550, 590),
            'Delivery Staff' => rand(550, 580),
            'Cleaner' => rand(550, 570),
        ];
        
        // Return position-based rate or default range
        return $rates[$position] ?? rand(550, 650);
    }
    
    /**
     * Generate contextual notes for payroll records
     */
    private function generateNotes(string $position, float $bonuses, float $deductions): ?string
    {
        $notes = [];
        
        if ($bonuses > 0) {
            $bonusReasons = [
                'Performance bonus',
                'Attendance bonus',
                'Holiday bonus',
                'Sales incentive',
                'Perfect attendance',
            ];
            $notes[] = $bonusReasons[array_rand($bonusReasons)];
        }
        
        if ($deductions > 0) {
            $deductionReasons = [
                'SSS, PhilHealth, Pag-IBIG contributions',
                'Tardiness deduction',
                'Cash advance repayment',
                'Uniform cost',
                'Standard government deductions',
            ];
            $notes[] = $deductionReasons[array_rand($deductionReasons)];
        }
        
        // 30% chance of having notes
        return rand(0, 100) < 30 ? implode(', ', $notes) : null;
    }
}
