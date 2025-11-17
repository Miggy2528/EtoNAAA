<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Procurement;
use App\Enums\PurchaseStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the supplier user and record
        $supplierUser = User::where('email', 'supplier@supplier.com')->first();
        
        if (!$supplierUser || !$supplierUser->supplier) {
            $this->command->error('Supplier account not found. Please run SupplierUserSeeder first.');
            return;
        }

        $supplier = $supplierUser->supplier;
        $this->command->info('Creating dummy data for: ' . $supplier->name);

        // Clear existing data for this supplier
        $this->command->info('Clearing existing supplier data...');
        
        // Delete related records first
        PurchaseDetails::whereIn('purchase_id', Purchase::where('supplier_id', $supplier->id)->pluck('id'))->delete();
        Purchase::where('supplier_id', $supplier->id)->delete();
        Procurement::where('supplier_id', $supplier->id)->delete();
        
        // Force delete products (including soft deleted ones)
        Product::where('supplier_id', $supplier->id)->withTrashed()->forceDelete();

        // Create products supplied by this supplier (15 products)
        $this->command->info('Creating products...');
        $products = $this->createProducts($supplier);
        
        // Create purchase orders (20 purchases over the last 6 months)
        $this->command->info('Creating purchase orders...');
        $this->createPurchases($supplier, $products);
        
        // Create procurements/deliveries (50 procurements)
        $this->command->info('Creating procurement deliveries...');
        $this->createProcurements($supplier, $products);
        
        // Update supplier analytics
        $this->command->info('Updating supplier analytics...');
        $supplier->updateAnalytics();
        
        $this->command->info('✅ Supplier dummy data created successfully!');
        $this->command->info('   - Products: ' . $products->count());
        $this->command->info('   - Purchases: ' . Purchase::where('supplier_id', $supplier->id)->count());
        $this->command->info('   - Procurements: ' . Procurement::where('supplier_id', $supplier->id)->count());
        $this->command->info('   - Delivery Rating: ' . $supplier->fresh()->delivery_rating);
        $this->command->info('   - On-Time %: ' . $supplier->fresh()->on_time_percentage);
    }

    /**
     * Create products for the supplier
     */
    private function createProducts($supplier)
    {
        $productNames = [
            ['name' => 'Premium Beef Tenderloin', 'price' => 850.00, 'stock' => 45],
            ['name' => 'Wagyu Ribeye Steak', 'price' => 1200.00, 'stock' => 30],
            ['name' => 'Pork Belly Strips', 'price' => 320.00, 'stock' => 80],
            ['name' => 'Chicken Breast Fillet', 'price' => 180.00, 'stock' => 120],
            ['name' => 'Fresh Lamb Chops', 'price' => 680.00, 'stock' => 35],
            ['name' => 'Ground Beef (80/20)', 'price' => 280.00, 'stock' => 60],
            ['name' => 'Pork Sausages', 'price' => 220.00, 'stock' => 90],
            ['name' => 'Beef Short Ribs', 'price' => 560.00, 'stock' => 40],
            ['name' => 'Chicken Thighs', 'price' => 150.00, 'stock' => 100],
            ['name' => 'Pork Chops', 'price' => 280.00, 'stock' => 70],
            ['name' => 'Beef Brisket', 'price' => 480.00, 'stock' => 50],
            ['name' => 'Duck Breast', 'price' => 420.00, 'stock' => 25],
            ['name' => 'Bacon Strips', 'price' => 340.00, 'stock' => 85],
            ['name' => 'Turkey Breast', 'price' => 380.00, 'stock' => 45],
            ['name' => 'Veal Cutlets', 'price' => 720.00, 'stock' => 30],
        ];

        $products = collect();
        foreach ($productNames as $index => $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'code' => 'SUP-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'category_id' => 1, // Assuming category 1 exists
                'unit_id' => 1, // Assuming unit 1 exists (kg)
                'product_image' => null,
                'quantity' => $productData['stock'],
                'buying_price' => $productData['price'] * 0.70, // 70% of selling price
                'selling_price' => $productData['price'],
                'quantity_alert' => 20,
            ]);
            $products->push($product);
        }

        return $products;
    }

    /**
     * Create purchase orders
     */
    private function createPurchases($supplier, $products)
    {
        // Get any admin user, or create a dummy one if needed
        $adminUser = User::where('role', 'admin')->first();
        
        if (!$adminUser) {
            // If no admin exists, use user ID 1
            $adminUserId = 1;
        } else {
            $adminUserId = $adminUser->id;
        }
        
        // Create 20 purchases over the last 6 months
        for ($i = 1; $i <= 20; $i++) {
            $daysAgo = rand(1, 180);
            $date = Carbon::now()->subDays($daysAgo);
            
            // 75% approved, 25% pending
            $rand = rand(1, 100);
            if ($rand <= 75) {
                $status = PurchaseStatus::APPROVED;
            } else {
                $status = PurchaseStatus::PENDING;
            }

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'date' => $date,
                'purchase_no' => 'PO-' . $date->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'status' => $status,
                'total_amount' => 0, // Will be calculated
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ]);

            // Add 3-7 products to each purchase
            $numProducts = rand(3, 7);
            $totalAmount = 0;
            
            $selectedProducts = $products->random($numProducts);
            foreach ($selectedProducts as $product) {
                $quantity = rand(5, 30);
                $unitcost = $product->buying_price;
                $total = $quantity * $unitcost;
                $totalAmount += $total;

                PurchaseDetails::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unitcost' => $unitcost,
                    'total' => $total,
                ]);
            }

            // Update purchase total
            $purchase->update(['total_amount' => $totalAmount]);
        }
    }

    /**
     * Create procurement/delivery records
     */
    private function createProcurements($supplier, $products)
    {
        // Create 50 procurements over the last 6 months
        for ($i = 1; $i <= 50; $i++) {
            $daysAgo = rand(1, 180);
            $expectedDate = Carbon::now()->subDays($daysAgo);
            
            // 75% delivered, 25% pending
            $isDelivered = rand(1, 100) <= 75;
            
            if ($isDelivered) {
                // 80% on-time, 20% delayed
                $isOnTime = rand(1, 100) <= 80;
                
                if ($isOnTime) {
                    // Delivered on time or early
                    $deliveryDate = $expectedDate->copy()->subDays(rand(0, 2));
                    $status = 'on-time';
                } else {
                    // Delayed by 1-7 days
                    $deliveryDate = $expectedDate->copy()->addDays(rand(1, 7));
                    $status = 'delayed';
                }
            } else {
                $deliveryDate = null;
                $status = 'pending';
            }

            $product = $products->random();
            $quantity = rand(10, 100);
            $totalCost = $quantity * $product->buying_price;
            
            // Defective rate: 0-5%
            $defectiveRate = rand(0, 500) / 100;

            Procurement::create([
                'supplier_id' => $supplier->id,
                'product_id' => $product->id,
                'quantity_supplied' => $quantity,
                'expected_delivery_date' => $expectedDate,
                'delivery_date' => $deliveryDate,
                'total_cost' => $totalCost,
                'status' => $status,
                'defective_rate' => $defectiveRate,
            ]);
        }
    }
}
