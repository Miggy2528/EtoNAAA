<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderDetails;
use App\Enums\OrderStatus;

class CreateTestOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test order for testing notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test order...');
        
        // Get or create a test customer
        $customer = Customer::first();
        if (!$customer) {
            $this->error('No customers found. Creating one...');
            $customer = Customer::create([
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '1234567890',
                'address' => 'Test Address',
                'status' => 'active',
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }
        
        $this->info("Using customer: {$customer->name} (ID: {$customer->id})");
        
        // Get or create a test product
        $product = Product::first();
        if (!$product) {
            $this->error('No products found. Please create products first.');
            return;
        }
        
        $this->info("Using product: {$product->name} (ID: {$product->id})");
        
        // Create test order
        $order = Order::create([
            'customer_id' => $customer->id,
            'order_date' => now()->timezone('Asia/Manila')->format('Y-m-d'),
            'order_status' => OrderStatus::PENDING,
            'total_products' => 1,
            'sub_total' => $product->price_per_kg,
            'vat' => $product->price_per_kg * 0.12,
            'total' => $product->price_per_kg * 1.12,
            'invoice_no' => 'INV-' . now()->format('YmdHis'),
            'payment_type' => 'cash',
            'pay' => 0,
            'due' => $product->price_per_kg * 1.12,
        ]);
        
        // Create order details
        OrderDetails::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unitcost' => $product->price_per_kg,
            'total' => $product->price_per_kg,
        ]);
        
        $this->info("✅ Test order created successfully!");
        $this->info("Order ID: {$order->id}");
        $this->info("Invoice No: {$order->invoice_no}");
        $this->info("Status: {$order->order_status->value}");
        $this->info("Total: ₱" . number_format($order->total, 2));
        
        return 0;
    }
}
