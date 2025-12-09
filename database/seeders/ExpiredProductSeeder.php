<?php

namespace Database\Seeders;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExpiredProductSeeder extends Seeder
{
    /**
     * Seed expired product data for testing
     * This adds expiration dates to existing products
     */
    public function run(): void
    {
        // Get some existing products
        $products = Product::limit(15)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please seed products first.');
            return;
        }
        
        $now = Carbon::now();
        $expiredCount = 0;
        $expiringCount = 0;
        
        foreach ($products as $index => $product) {
            if ($index < 5) {
                // Create 5 expired products (1-30 days ago)
                $daysAgo = rand(1, 30);
                $product->update([
                    'expiration_date' => $now->copy()->subDays($daysAgo)->format('Y-m-d')
                ]);
                $expiredCount++;
                $this->command->info("  ✓ {$product->name} - Expired {$daysAgo} days ago");
            } elseif ($index < 10) {
                // Create 5 expiring soon products (1-7 days from now)
                $daysUntil = rand(1, 7);
                $product->update([
                    'expiration_date' => $now->copy()->addDays($daysUntil)->format('Y-m-d')
                ]);
                $expiringCount++;
                $this->command->info("  ⚠ {$product->name} - Expires in {$daysUntil} days");
            } else {
                // Create 5 products with future expiration (8-60 days from now)
                $daysUntil = rand(8, 60);
                $product->update([
                    'expiration_date' => $now->copy()->addDays($daysUntil)->format('Y-m-d')
                ]);
                $this->command->info("  ✓ {$product->name} - Expires in {$daysUntil} days");
            }
        }
        
        $this->command->info("\n✅ Expired product data seeded successfully!");
        $this->command->info("   📦 Total products updated: {$products->count()}");
        $this->command->info("   ❌ Expired products: {$expiredCount}");
        $this->command->info("   ⚠️  Expiring soon (1-7 days): {$expiringCount}");
        $this->command->info("   ✅ Future expiration: " . ($products->count() - $expiredCount - $expiringCount));
    }
}
