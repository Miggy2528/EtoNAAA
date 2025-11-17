<?php

namespace Database\Factories;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Generate a random date within the current year
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Generate random month from January to current month
        $month = rand(1, $currentMonth);
        $daysInMonth = Carbon::create($currentYear, $month, 1)->daysInMonth;
        $day = rand(1, $daysInMonth);
        
        $orderDate = Carbon::create($currentYear, $month, $day);
        
        // Generate realistic sales data between 130,000 and 160,000 for monthly totals
        // For individual orders, we'll use smaller amounts between 1,000 and 5,000
        $total = $this->faker->randomFloat(2, 1000, 5000);
        $subTotal = $total * 0.88; // 88% for subtotal (before 12% VAT)
        $vat = $total * 0.12; // 12% VAT
        
        return [
            'customer_name' => $this->faker->name(),
            'receiver_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'order_date' => $orderDate,
            'order_status' => OrderStatus::COMPLETE,
            'total_products' => $this->faker->numberBetween(1, 15),
            'sub_total' => $subTotal,
            'vat' => $vat,
            'total' => $total,
            'invoice_no' => 'INV-' . $orderDate->format('Ym') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'tracking_number' => 'TRK-' . $orderDate->format('Ym') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'payment_type' => $this->faker->randomElement(['Cash', 'Credit Card', 'GCash', 'Bank Transfer']),
            'pay' => $total,
            'due' => 0,
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ];
    }
    
    /**
     * Indicate that the order is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_status' => OrderStatus::PENDING,
            ];
        });
    }
    
    /**
     * Indicate that the order is cancelled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_status' => OrderStatus::CANCELLED,
                'cancellation_reason' => $this->faker->sentence(),
                'cancelled_at' => Carbon::now(),
            ];
        });
    }
}