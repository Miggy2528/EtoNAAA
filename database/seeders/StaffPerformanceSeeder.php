<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffPerformance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StaffPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 dummy staff members
        $staffData = [
            ['John Dela Cruz', 'Butcher', '09123456789'],
            ['Maria Santos', 'Cashier', '09234567890'],
            ['Paolo Reyes', 'Inventory Clerk', '09345678901'],
            ['Ana Dizon', 'Supervisor', '09456789012'],
            ['Mark Tan', 'Delivery Staff', '09567890123'],
            ['Jessica Lim', 'Cashier', '09678901234'],
            ['Carlo Mendoza', 'Butcher', '09789012345'],
            ['Ella Robles', 'Inventory Clerk', '09890123456'],
            ['Nathan Cruz', 'Cleaner', '09901234567'],
            ['Lea Villanueva', 'Cashier', '09012345678'],
            ['Rico Bautista', 'Delivery Staff', '09123456780'],
            ['Tina Ramos', 'Supervisor', '09234567891'],
            ['Miguel Garcia', 'Butcher', '09345678902'],
            ['Sofia Rodriguez', 'Cashier', '09456789013'],
            ['Gabriel Torres', 'Inventory Clerk', '09567890124'],
            ['Isabella Navarro', 'Delivery Staff', '09678901235'],
            ['Luis Hernandez', 'Cleaner', '09789012346'],
            ['Carmen Flores', 'Butcher', '09890123457'],
            ['Diego Castillo', 'Cashier', '09901234568'],
            ['Valentina Morales', 'Inventory Clerk', '09012345679']
        ];

        $staff = [];
        foreach ($staffData as $data) {
            $staff[] = Staff::create([
                'name' => $data[0],
                'position' => $data[1],
                'department' => 'Operations',
                'contact_number' => $data[2],
                'date_hired' => now()->subMonths(rand(1, 24)),
                'status' => 'Active',
            ]);
        }

        // Create sample performance data for the last 3 months
        foreach ($staff as $staffMember) {
            for ($i = 0; $i < 3; $i++) {
                $month = now()->subMonths($i)->startOfMonth();
                
                StaffPerformance::create([
                    'staff_id' => $staffMember->id,
                    'month' => $month,
                    'attendance_rate' => rand(75, 100),
                    'task_completion_rate' => rand(70, 100),
                    'customer_feedback_score' => rand(3, 5) + (rand(0, 10) / 10), // 3.0 to 5.0
                    'remarks' => $this->generateRemarks()
                ]);
            }
        }
    }

    /**
     * Generate random remarks for performance
     */
    private function generateRemarks(): string
    {
        $remarks = [
            'Excellent performance this month',
            'Consistent and reliable',
            'Shows great initiative',
            'Good team player',
            'Needs improvement in time management',
            'Very customer-focused',
            'Outstanding work quality',
            'Room for improvement',
            'Meets all expectations',
            'Exceptional dedication'
        ];

        return $remarks[array_rand($remarks)];
    }
}