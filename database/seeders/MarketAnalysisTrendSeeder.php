<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\MeatCut;
use App\Enums\OrderStatus;
use Carbon\Carbon;

class MarketAnalysisTrendSeeder extends Seeder
{
    /**
     * Run the database seeds to add sample order data for market analysis trends.
     */
    public function run(): void
    {
        // First, let's make sure we have some meat cuts with proper classifications
        $this->ensureMeatCutsExist();
        
        // Then create sample orders with order details over the past 12 months
        $this->createSampleOrders();
    }
    
    /**
     * Ensure we have meat cuts with proper classifications
     */
    private function ensureMeatCutsExist()
    {
        // Check if we already have meat cuts with classifications
        $existingCuts = MeatCut::whereNotNull('meat_type')
            ->whereNotNull('preparation_type')
            ->count();
            
        if ($existingCuts >= 5) {
            // We have enough classified meat cuts
            return;
        }
        
        // Create or update meat cuts with proper classifications
        $meatCutsData = [
            [
                'name' => 'Beef Ribeye',
                'meat_type' => 'beef',
                'preparation_type' => 'grill',
                'quality_grade' => 'prime',
                'meat_subtype' => 'steak',
                'quality' => 'premium',
                'preparation_style' => 'dry_aged'
            ],
            [
                'name' => 'Pork Chops',
                'meat_type' => 'pork',
                'preparation_type' => 'grill',
                'quality_grade' => 'choice',
                'meat_subtype' => 'chop',
                'quality' => 'choice',
                'preparation_style' => 'pan_seared'
            ],
            [
                'name' => 'Chicken Breast',
                'meat_type' => 'chicken',
                'preparation_type' => 'grill',
                'quality_grade' => 'a_grade',
                'meat_subtype' => 'breast',
                'quality' => 'standard',
                'preparation_style' => 'lemon_pepper'
            ],
            [
                'name' => 'Lamb Chops',
                'meat_type' => 'lamb',
                'preparation_type' => 'grill',
                'quality_grade' => 'choice',
                'meat_subtype' => 'chop',
                'quality' => 'premium',
                'preparation_style' => 'mediterranean'
            ],
            [
                'name' => 'Pork Ribs',
                'meat_type' => 'pork',
                'preparation_type' => 'bbq',
                'quality_grade' => 'choice',
                'meat_subtype' => 'ribs',
                'quality' => 'choice',
                'preparation_style' => 'glazed'
            ],
            [
                'name' => 'Beef Brisket',
                'meat_type' => 'beef',
                'preparation_type' => 'smoke',
                'quality_grade' => 'select',
                'meat_subtype' => 'brisket',
                'quality' => 'standard',
                'preparation_style' => 'slow_smoked'
            ],
            [
                'name' => 'Chicken Thighs',
                'meat_type' => 'chicken',
                'preparation_type' => 'roast',
                'quality_grade' => 'a_grade',
                'meat_subtype' => 'thigh',
                'quality' => 'standard',
                'preparation_style' => 'garlic_herb'
            ],
        ];
        
        foreach ($meatCutsData as $cutData) {
            MeatCut::updateOrCreate(
                ['name' => $cutData['name']],
                $cutData
            );
        }
        
        $this->command->info('Ensured meat cuts with classifications exist!');
    }
    
    /**
     * Create sample orders with order details over the past 12 months
     */
    private function createSampleOrders()
    {
        // Get existing meat cuts with classifications
        $meatCuts = MeatCut::whereNotNull('meat_type')
            ->whereNotNull('preparation_type')
            ->get();
            
        if ($meatCuts->isEmpty()) {
            $this->command->error('No meat cuts with classifications found!');
            return;
        }
        
        // Create products for each meat cut if they don't exist
        $products = [];
        foreach ($meatCuts as $meatCut) {
            $product = Product::firstOrCreate(
                ['name' => $meatCut->name . ' Product'],
                [
                    'category_id' => 1, // Assuming category 1 is for meat
                    'unit_id' => 1, // Assuming unit 1 is kg
                    'meat_cut_id' => $meatCut->id,
                    'quantity' => 100,
                    'buying_price' => $meatCut->default_price_per_kg ?? 200,
                    'selling_price' => ($meatCut->default_price_per_kg ?? 200) * 1.3,
                    'quantity_alert' => 10,
                ]
            );
            $products[] = $product;
        }
        
        // Create orders for the past 12 months with varying frequencies
        $startDate = Carbon::now()->subMonths(12);
        $endDate = Carbon::now();
        
        // Generate orders for each month
        for ($month = 0; $month < 12; $month++) {
            $monthStart = $startDate->copy()->addMonths($month)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            
            // Number of orders for this month (more recent months have more orders)
            $orderCount = rand(15 + $month, 25 + $month);
            
            for ($i = 0; $i < $orderCount; $i++) {
                // Random date within the month
                $orderDate = Carbon::createFromTimestamp(
                    rand($monthStart->timestamp, $monthEnd->timestamp)
                );
                
                // Get or create a customer for this order
                $customer = \App\Models\Customer::inRandomOrder()->first();
                if (!$customer) {
                    $customer = \App\Models\Customer::factory()->create();
                }
                
                // Create order
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'receiver_name' => fake()->name(),
                    'customer_email' => $customer->email,
                    'order_date' => $orderDate,
                    'order_status' => OrderStatus::COMPLETE,
                    'total_products' => rand(1, 5),
                    'sub_total' => rand(500, 3000),
                    'vat' => rand(60, 360),
                    'total' => rand(500, 3000) + rand(60, 360),
                    'invoice_no' => 'INV-' . $orderDate->format('Ym') . '-' . rand(1000, 9999),
                    'tracking_number' => 'TRK-' . $orderDate->format('Ym') . '-' . rand(1000, 9999),
                    'payment_type' => fake()->randomElement(['Cash', 'Credit Card', 'GCash', 'Bank Transfer']),
                    'pay' => rand(500, 3000) + rand(60, 360),
                    'due' => 0,
                    'barangay' => fake()->randomElement(['San Juan', 'Malate', 'Makati', 'Taguig', 'Pasay', 'Quezon City', 'Mandaluyong', 'Pasig']),
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
                
                // Create order details (1-3 items per order)
                $detailCount = rand(1, 3);
                $totalQuantity = 0;
                
                for ($j = 0; $j < $detailCount; $j++) {
                    $product = $products[array_rand($products)];
                    $quantity = rand(1, 10);
                    $unitCost = $product->buying_price;
                    $price = $product->selling_price;
                    $total = $quantity * $price;
                    $totalQuantity += $quantity;
                    
                    OrderDetails::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unitcost' => $unitCost,
                        'total' => $total,
                    ]);
                }
                
                // Update order total_products
                $order->update(['total_products' => $totalQuantity]);
            }
        }
        
        $this->command->info('Created sample orders with order details for trend analysis!');
    }
}