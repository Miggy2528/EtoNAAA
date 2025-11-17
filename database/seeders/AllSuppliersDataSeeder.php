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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AllSuppliersDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating suppliers and their data from January to October...');
        
        // Remove previously created demo suppliers if they exist
        $removeEmails = ['john@premiummeats.com','sarah@freshfarm.com','michael@globalprotein.com','david@localbutcher.com'];
        $toRemove = Supplier::whereIn('email', $removeEmails)->get();
        foreach ($toRemove as $s) {
            $this->clearSupplierData($s);
            $s->forceDelete();
        }
        
        // Build supplier list from existing admin-side suppliers
        $targetNames = [
            'A.U. Coloma Corporation',
            'Marketing Corporation',
            'MAC Marketing Corporation',
            'ABITC',
        ];
        
        $performanceMap = [
            'A.U. Coloma Corporation' => 'average', // ~65%
            'Marketing Corporation' => 'average', // ~58%
            'MAC Marketing Corporation' => 'average', // ~64%
            'ABITC' => 'good', // ~78%
        ];
        
        $existingSuppliers = Supplier::whereIn('name', $targetNames)->get()->keyBy('name');
        
        $suppliersData = [];
        foreach ($targetNames as $name) {
            if ($existingSuppliers->has($name)) {
                $s = $existingSuppliers->get($name);
                $suppliersData[] = [
                    'name' => $s->name,
                    'contact_person' => $s->contact_person ?? $s->name,
                    'email' => $s->email ?: Str::slug($s->name) . '@example.com',
                    'phone' => $s->phone,
                    'address' => $s->address,
                    'shopname' => $s->shopname,
                    'type' => is_string($s->type) ? $s->type : ($s->type?->value ?? 'wholesaler'),
                    'account_holder' => $s->account_holder,
                    'account_number' => $s->account_number,
                    'bank_name' => $s->bank_name,
                    'performance' => $performanceMap[$name] ?? 'average',
                ];
            }
        }
        
        if (empty($suppliersData)) {
            $this->command->warn('No matching suppliers found. Please ensure the admin-side suppliers exist.');
        }

        foreach ($suppliersData as $index => $supplierData) {
            $this->command->info('\n===== Processing: ' . $supplierData['name'] . ' =====');
            
            // Check if supplier already exists
            $supplier = Supplier::where('email', $supplierData['email'])->first();
            
            if (!$supplier) {
                // Check if user already exists
                $user = User::where('email', $supplierData['email'])->first();
                
                if (!$user) {
                    // Create user account for supplier
                    $user = User::create([
                        'name' => $supplierData['contact_person'],
                        'username' => strtolower(str_replace(' ', '', $supplierData['contact_person'])),
                        'email' => $supplierData['email'],
                        'password' => Hash::make('password'),
                        'role' => 'supplier',
                        'status' => 'active',
                    ]);
                    $this->command->info('Created user account: ' . $supplierData['email']);
                } else {
                    $this->command->info('User already exists: ' . $supplierData['email']);
                }

                // Create supplier record
                $supplier = Supplier::create([
                    'user_id' => $user->id,
                    'name' => $supplierData['name'],
                    'contact_person' => $supplierData['contact_person'],
                    'email' => $supplierData['email'],
                    'phone' => $supplierData['phone'],
                    'address' => $supplierData['address'],
                    'shopname' => $supplierData['shopname'],
                    'type' => $supplierData['type'],
                    'status' => 'active',
                    'account_holder' => $supplierData['account_holder'],
                    'account_number' => $supplierData['account_number'],
                    'bank_name' => $supplierData['bank_name'],
                ]);
                $this->command->info('Created supplier record: ' . $supplier->name);
            } else {
                $this->command->info('Supplier already exists: ' . $supplier->name);
                
                // Update existing supplier if user_id is null
                if (!$supplier->user_id) {
                    $user = User::where('email', $supplierData['email'])->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $supplierData['contact_person'],
                            'username' => strtolower(str_replace(' ', '', $supplierData['contact_person'])),
                            'email' => $supplierData['email'],
                            'password' => Hash::make('password'),
                            'role' => 'supplier',
                            'status' => 'active',
                        ]);
                        $this->command->info('Created user account: ' . $supplierData['email']);
                    }
                    $supplier->update(['user_id' => $user->id]);
                }
            }

            // Ensure user account exists and link it
            $user = User::where('email', $supplierData['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $supplierData['contact_person'],
                    'username' => Str::slug($supplierData['contact_person']),
                    'email' => $supplierData['email'],
                    'password' => Hash::make('password'),
                    'role' => 'supplier',
                    'status' => 'active',
                ]);
                $this->command->info('Created user account: ' . $supplierData['email']);
            } else {
                $user->update([
                    'role' => 'supplier',
                    'status' => 'active',
                ]);
                $this->command->info('Ensured user role/status: ' . $supplierData['email']);
            }
            // Force link supplier to this user
            $supplier->update(['user_id' => $user->id]);

            // Clear existing data for this supplier
            $this->clearSupplierData($supplier);

            // Create products for this supplier
            $products = $this->createProducts($supplier, $index);
            $this->command->info('Created ' . $products->count() . ' products');

            // Create purchases from January to October
            $this->createPurchases($supplier, $products, $supplierData['performance']);
            $this->command->info('Created purchase orders (Jan-Oct)');

            // Create procurements from January to October
            $this->createProcurements($supplier, $products, $supplierData['performance']);
            $this->command->info('Created procurement deliveries (Jan-Oct)');

            // Update supplier analytics
            $supplier->updateAnalytics();
            $supplier = $supplier->fresh();
            
            $this->command->info('Updated analytics:');
            $this->command->info('  - Delivery Rating: ' . $supplier->delivery_rating);
            $this->command->info('  - On-Time %: ' . $supplier->on_time_percentage);
            $this->command->info('  - Total Procurements: ' . $supplier->total_procurements);
        }

        $this->command->info('\n✅ All suppliers data created successfully!');
        $this->command->info('\nLogin credentials (password for all: password):');
        foreach ($suppliersData as $data) {
            $this->command->info('  - ' . $data['email']);
        }
    }

    /**
     * Clear existing data for supplier
     */
    private function clearSupplierData($supplier)
    {
        $this->command->info('Clearing existing data...');
        PurchaseDetails::whereIn('purchase_id', Purchase::where('supplier_id', $supplier->id)->pluck('id'))->delete();
        Purchase::where('supplier_id', $supplier->id)->delete();
        Procurement::where('supplier_id', $supplier->id)->delete();
        Product::where('supplier_id', $supplier->id)->withTrashed()->forceDelete();
    }

    /**
     * Create products for supplier
     */
    private function createProducts($supplier, $supplierIndex)
    {
        $productsData = [
            // Supplier 1 - Premium products
            [
                ['name' => 'Premium Wagyu Beef', 'price' => 1500, 'stock' => 25],
                ['name' => 'Angus Ribeye Steak', 'price' => 950, 'stock' => 40],
                ['name' => 'Prime Beef Tenderloin', 'price' => 1100, 'stock' => 35],
                ['name' => 'Kobe Beef Strips', 'price' => 1800, 'stock' => 20],
                ['name' => 'Premium Lamb Rack', 'price' => 880, 'stock' => 30],
                ['name' => 'Dry-Aged Beef', 'price' => 1250, 'stock' => 28],
                ['name' => 'Premium Veal', 'price' => 820, 'stock' => 32],
                ['name' => 'Grass-Fed Beef', 'price' => 680, 'stock' => 45],
                ['name' => 'Organic Chicken', 'price' => 380, 'stock' => 60],
                ['name' => 'Free-Range Turkey', 'price' => 520, 'stock' => 35],
            ],
            // Supplier 2 - Farm fresh products
            [
                ['name' => 'Farm Fresh Chicken', 'price' => 220, 'stock' => 100],
                ['name' => 'Free-Range Eggs', 'price' => 180, 'stock' => 150],
                ['name' => 'Farm Pork Belly', 'price' => 380, 'stock' => 70],
                ['name' => 'Fresh Duck', 'price' => 450, 'stock' => 40],
                ['name' => 'Farm Lamb Chops', 'price' => 580, 'stock' => 45],
                ['name' => 'Organic Beef', 'price' => 650, 'stock' => 55],
                ['name' => 'Farm Sausages', 'price' => 280, 'stock' => 90],
                ['name' => 'Fresh Bacon', 'price' => 320, 'stock' => 80],
                ['name' => 'Farm Ground Beef', 'price' => 350, 'stock' => 75],
                ['name' => 'Chicken Wings', 'price' => 240, 'stock' => 95],
            ],
            // Supplier 3 - Global variety
            [
                ['name' => 'Imported Beef Cuts', 'price' => 720, 'stock' => 50],
                ['name' => 'International Lamb', 'price' => 640, 'stock' => 42],
                ['name' => 'Global Pork Cuts', 'price' => 420, 'stock' => 65],
                ['name' => 'Frozen Chicken', 'price' => 190, 'stock' => 120],
                ['name' => 'Imported Turkey', 'price' => 480, 'stock' => 38],
                ['name' => 'Beef Mince', 'price' => 320, 'stock' => 85],
                ['name' => 'Pork Ribs', 'price' => 450, 'stock' => 58],
                ['name' => 'Chicken Thighs', 'price' => 210, 'stock' => 95],
                ['name' => 'Duck Breast', 'price' => 520, 'stock' => 32],
                ['name' => 'Beef Brisket', 'price' => 580, 'stock' => 48],
            ],
            // Supplier 4 - Local butcher products
            [
                ['name' => 'Local Beef Steaks', 'price' => 550, 'stock' => 60],
                ['name' => 'Butcher Pork Chops', 'price' => 340, 'stock' => 75],
                ['name' => 'Fresh Lamb Shanks', 'price' => 480, 'stock' => 40],
                ['name' => 'Local Chicken', 'price' => 250, 'stock' => 85],
                ['name' => 'Butcher Sausages', 'price' => 290, 'stock' => 95],
                ['name' => 'Local Beef Mince', 'price' => 310, 'stock' => 80],
                ['name' => 'Pork Belly Slices', 'price' => 380, 'stock' => 68],
                ['name' => 'Chicken Drumsticks', 'price' => 230, 'stock' => 90],
                ['name' => 'Beef Short Ribs', 'price' => 620, 'stock' => 45],
                ['name' => 'Local Turkey', 'price' => 420, 'stock' => 35],
            ],
        ];

        $products = collect();
        $supplierProducts = $productsData[$supplierIndex];

        foreach ($supplierProducts as $index => $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'code' => 'SUP' . $supplier->id . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'category_id' => 1,
                'unit_id' => 1,
                'product_image' => null,
                'quantity' => $productData['stock'],
                'buying_price' => $productData['price'] * 0.65,
                'selling_price' => $productData['price'],
                'quantity_alert' => 15,
            ]);
            $products->push($product);
        }

        return $products;
    }

    /**
     * Create purchases from January to October
     */
    private function createPurchases($supplier, $products, $performance)
    {
        $adminUser = User::where('role', 'admin')->first();
        $adminUserId = $adminUser ? $adminUser->id : 1;

        // Create 30-50 purchases spread from January to October
        $totalPurchases = rand(30, 50);
        
        for ($i = 1; $i <= $totalPurchases; $i++) {
            // Random date between Jan 1 and Oct 31, 2025
            $month = rand(1, 10);
            $day = rand(1, 28);
            $date = Carbon::create(2025, $month, $day);
            
            // Determine status based on performance
            $approvalRate = match($performance) {
                'excellent' => 95,
                'good' => 85,
                'average' => 75,
                default => 80,
            };
            
            $status = rand(1, 100) <= $approvalRate ? PurchaseStatus::APPROVED : PurchaseStatus::PENDING;

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'date' => $date,
                'purchase_no' => 'PO-' . $date->format('Ymd') . '-' . $supplier->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => $status,
                'total_amount' => 0,
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ]);

            // Add 2-6 products to each purchase
            $numProducts = rand(2, 6);
            $totalAmount = 0;
            
            $selectedProducts = $products->random(min($numProducts, $products->count()));
            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 50);
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

            $purchase->update(['total_amount' => $totalAmount]);
        }
    }

    /**
     * Create procurements from January to October
     */
    private function createProcurements($supplier, $products, $performance)
    {
        // Create 80-120 procurements spread from January to October
        $totalProcurements = rand(80, 120);
        
        // On-time percentage based on performance
        $onTimeRate = match($performance) {
            'excellent' => 90,
            'good' => 75,
            'average' => 65,
            default => 70,
        };

        for ($i = 1; $i <= $totalProcurements; $i++) {
            // Random expected delivery date between Jan 1 and Oct 31, 2025
            $month = rand(1, 10);
            $day = rand(1, 28);
            $expectedDate = Carbon::create(2025, $month, $day);
            
            // 85% delivered, 15% pending
            $isDelivered = rand(1, 100) <= 85;
            
            if ($isDelivered) {
                // Determine if on-time based on performance
                $isOnTime = rand(1, 100) <= $onTimeRate;
                
                if ($isOnTime) {
                    // Delivered on time or early (0-2 days early)
                    $deliveryDate = $expectedDate->copy()->subDays(rand(0, 2));
                    $status = 'on-time';
                } else {
                    // Delayed by 1-10 days
                    $deliveryDate = $expectedDate->copy()->addDays(rand(1, 10));
                    $status = 'delayed';
                }
            } else {
                $deliveryDate = null;
                $status = 'pending';
            }

            $product = $products->random();
            $quantity = rand(20, 150);
            $totalCost = $quantity * $product->buying_price;
            
            // Defective rate: excellent 0-2%, good 0-4%, average 0-6%
            $maxDefectiveRate = match($performance) {
                'excellent' => 200,
                'good' => 400,
                'average' => 600,
                default => 400,
            };
            $defectiveRate = rand(0, $maxDefectiveRate) / 100;

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
