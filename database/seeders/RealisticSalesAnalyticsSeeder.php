<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Customer;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RealisticSalesAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates realistic sales data based on actual market prices
     * for a meat shop business, generating orders from 2020-2025 with:
     * - Seasonal variations (higher sales in holidays)
     * - Realistic product mix (beef, pork, chicken, lamb)
     * - Market-appropriate pricing
     * - Growth trends year over year
     */
    public function run(): void
    {
        $this->command->info('Starting realistic sales analytics data seeding...');
        
        // Get all active products
        $products = Product::whereNotNull('selling_price')
            ->whereNotNull('buying_price')
            ->get();
            
        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductSeeder first.');
            return;
        }
        
        // Get or create sample customers
        $customers = Customer::all();
        if ($customers->count() < 10) {
            $this->command->info('Creating sample customers...');
            
            // Create customers manually without bank fields if they cause issues
            for ($i = $customers->count(); $i < 50; $i++) {
                try {
                    Customer::create([
                        'name' => fake()->name(),
                        'email' => fake()->unique()->safeEmail(),
                        'phone' => fake()->unique()->phoneNumber(),
                        'address' => fake()->address(),
                    ]);
                } catch (\Exception $e) {
                    // Skip if duplicate email/phone
                    continue;
                }
            }
            $customers = Customer::all();
        }
        
        // Generate realistic sales data for 2020-2025
        $years = [2020, 2021, 2022, 2023, 2024, 2025];
        
        foreach ($years as $year) {
            $this->command->info("Generating sales data for {$year}...");
            
            // Year-over-year growth rate (12% annual growth)
            $growthMultiplier = pow(1.12, $year - 2020);
            
            for ($month = 1; $month <= 12; $month++) {
                // Skip future months for 2025
                if ($year == 2025 && $month > Carbon::now()->month) {
                    continue;
                }
                
                // Seasonal multipliers for meat business
                $seasonalMultipliers = [
                    1 => 0.85,  // January - Post-holiday slowdown
                    2 => 0.90,  // February - Valentine's day boost
                    3 => 0.95,  // March
                    4 => 1.00,  // April - Easter season
                    5 => 1.05,  // May - Summer grilling season starts
                    6 => 1.10,  // June - Peak grilling season
                    7 => 1.12,  // July - Peak summer
                    8 => 1.08,  // August - Back to school
                    9 => 1.00,  // September
                    10 => 1.15, // October - Holiday prep begins
                    11 => 1.25, // November - Thanksgiving prep
                    12 => 1.35  // December - Christmas & New Year peak
                ];
                
                // Base number of orders per month (scaled by season and growth)
                $baseOrdersPerMonth = 45; // Average 45 orders per month in 2020
                $ordersThisMonth = round($baseOrdersPerMonth * $seasonalMultipliers[$month] * $growthMultiplier);
                
                // Generate orders for this month
                for ($i = 0; $i < $ordersThisMonth; $i++) {
                    $this->createRealisticOrder($year, $month, $products, $customers);
                }
            }
        }
        
        $totalOrders = Order::count();
        $totalOrderDetails = OrderDetails::count();
        $totalRevenue = Order::where('order_status', OrderStatus::COMPLETE)->sum('total');
        
        $this->command->info('✅ Realistic sales analytics data seeded successfully!');
        $this->command->info("📊 Summary:");
        $this->command->info("   - Total Orders: {$totalOrders}");
        $this->command->info("   - Total Order Items: {$totalOrderDetails}");
        $this->command->info("   - Total Revenue: ₱" . number_format($totalRevenue, 2));
        $this->command->info("   - Period: 2020 - " . Carbon::now()->format('Y-m'));
    }
    
    /**
     * Create a realistic order with order details
     */
    private function createRealisticOrder($year, $month, $products, $customers)
    {
        // Random day in the month
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $day = rand(1, $daysInMonth);
        $orderDate = Carbon::create($year, $month, $day);
        
        // Random customer
        $customer = $customers->random();
        
        // Determine order size (small, medium, large)
        $orderSizeRand = rand(1, 100);
        if ($orderSizeRand <= 60) {
            // 60% small orders (1-3 items)
            $itemCount = rand(1, 3);
        } elseif ($orderSizeRand <= 90) {
            // 30% medium orders (4-7 items)
            $itemCount = rand(4, 7);
        } else {
            // 10% large orders (8-12 items)
            $itemCount = rand(8, 12);
        }
        
        // Select random products for this order
        $selectedProducts = $products->random(min($itemCount, $products->count()));
        
        $subTotal = 0;
        $totalProducts = 0;
        $orderDetailsData = [];
        
        foreach ($selectedProducts as $product) {
            // Realistic quantity ranges based on product type
            $productName = strtolower($product->name);
            
            if (strpos($productName, 'chicken') !== false) {
                // Chicken: 1-5 kg per order
                $quantity = rand(10, 50) / 10; // 1.0 - 5.0 kg
            } elseif (strpos($productName, 'pork') !== false) {
                // Pork: 0.5-4 kg per order
                $quantity = rand(5, 40) / 10; // 0.5 - 4.0 kg
            } elseif (strpos($productName, 'beef') !== false) {
                // Beef: 0.5-3 kg per order (premium product)
                $quantity = rand(5, 30) / 10; // 0.5 - 3.0 kg
            } elseif (strpos($productName, 'lamb') !== false) {
                // Lamb: 0.5-2 kg per order (premium product)
                $quantity = rand(5, 20) / 10; // 0.5 - 2.0 kg
            } else {
                // Default: 1-3 kg
                $quantity = rand(10, 30) / 10; // 1.0 - 3.0 kg
            }
            
            $quantity = round($quantity, 2);
            
            // Use selling price from product
            $price = floatval($product->selling_price);
            $unitcost = floatval($product->buying_price);
            
            // Calculate line total (using selling price for calculations)
            $lineTotal = round($quantity * $price, 2);
            
            $subTotal += $lineTotal;
            $totalProducts += $quantity;
            
            $orderDetailsData[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unitcost' => $unitcost,
                'total' => $lineTotal,
            ];
        }
        
        // Calculate VAT (12%)
        $vat = round($subTotal * 0.12, 2);
        $total = round($subTotal + $vat, 2);
        
        // Determine payment type (weighted distribution)
        $paymentRand = rand(1, 100);
        if ($paymentRand <= 45) {
            $paymentType = 'Cash';
        } elseif ($paymentRand <= 70) {
            $paymentType = 'GCash';
        } elseif ($paymentRand <= 85) {
            $paymentType = 'Credit Card';
        } else {
            $paymentType = 'Bank Transfer';
        }
        
        // 95% complete orders, 5% pending or cancelled
        $statusRand = rand(1, 100);
        if ($statusRand <= 95) {
            $orderStatus = OrderStatus::COMPLETE;
            $pay = $total;
            $due = 0;
        } elseif ($statusRand <= 98) {
            $orderStatus = OrderStatus::PENDING;
            $pay = 0;
            $due = $total;
        } else {
            $orderStatus = OrderStatus::CANCELLED;
            $pay = 0;
            $due = 0;
        }
        
        // Create order
        $invoiceNo = 'INV-' . $orderDate->format('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $trackingNo = 'TRK-' . $orderDate->format('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'receiver_name' => $customer->name,
            'customer_email' => $customer->email,
            'order_date' => $orderDate,
            'order_status' => $orderStatus,
            'total_products' => round($totalProducts, 2),
            'sub_total' => $subTotal,
            'vat' => $vat,
            'total' => $total,
            'invoice_no' => $invoiceNo,
            'tracking_number' => $trackingNo,
            'payment_type' => $paymentType,
            'pay' => $pay,
            'due' => $due,
            'delivery_address' => $customer->address ?? 'Metro Manila',
            'contact_phone' => $customer->phone ?? '09171234567',
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ]);
        
        // Create order details
        foreach ($orderDetailsData as $detail) {
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'unitcost' => $detail['unitcost'],
                'total' => $detail['total'],
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }
    }
}
