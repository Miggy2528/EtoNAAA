<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "================================================\n";
echo "  REALISTIC SALES DATA VERIFICATION REPORT     \n";
echo "================================================\n\n";

// Overall Statistics
echo "📊 OVERALL STATISTICS\n";
echo "--------------------\n";
$totalOrders = App\Models\Order::count();
$totalOrderDetails = App\Models\OrderDetails::count();
$completedOrders = App\Models\Order::where('order_status', 'complete')->count();
$totalRevenue = App\Models\Order::where('order_status', 'complete')->sum('total');
$avgOrderValue = App\Models\Order::where('order_status', 'complete')->avg('total');

echo "Total Orders: " . number_format($totalOrders) . "\n";
echo "Completed Orders: " . number_format($completedOrders) . " (" . round(($completedOrders/$totalOrders)*100, 1) . "%)\n";
echo "Total Order Items: " . number_format($totalOrderDetails) . "\n";
echo "Total Revenue: ₱" . number_format($totalRevenue, 2) . "\n";
echo "Average Order Value: ₱" . number_format($avgOrderValue, 2) . "\n\n";

// Year by Year
echo "📅 YEAR BY YEAR BREAKDOWN\n";
echo "-------------------------\n";
$yearlyStats = DB::table('orders')
    ->selectRaw('YEAR(order_date) as year, COUNT(*) as order_count, SUM(total) as revenue')
    ->where('order_status', 'complete')
    ->groupBy('year')
    ->orderBy('year')
    ->get();

foreach ($yearlyStats as $year) {
    echo sprintf("Year %d: %4d orders | Revenue: ₱%s\n", 
        $year->year, 
        $year->order_count, 
        number_format($year->revenue, 2)
    );
}
echo "\n";

// Top Products
echo "🥩 TOP 10 PRODUCTS BY REVENUE\n";
echo "------------------------------\n";
$topProducts = DB::table('order_details')
    ->join('products', 'order_details.product_id', '=', 'products.id')
    ->join('orders', 'order_details.order_id', '=', 'orders.id')
    ->where('orders.order_status', 'complete')
    ->selectRaw('products.name, SUM(order_details.quantity) as total_kg, SUM(order_details.total) as revenue, COUNT(order_details.id) as times_ordered')
    ->groupBy('products.id', 'products.name')
    ->orderByDesc('revenue')
    ->limit(10)
    ->get();

foreach ($topProducts as $idx => $product) {
    echo sprintf("%2d. %-30s | %6.1f kg | ₱%10s | %3d orders\n",
        $idx + 1,
        substr($product->name, 0, 30),
        $product->total_kg,
        number_format($product->revenue, 2),
        $product->times_ordered
    );
}
echo "\n";

// Payment Methods
echo "💳 PAYMENT METHOD DISTRIBUTION\n";
echo "-------------------------------\n";
$paymentMethods = DB::table('orders')
    ->selectRaw('payment_type, COUNT(*) as count, ROUND(COUNT(*) * 100.0 / ?, 1) as percentage', [$totalOrders])
    ->groupBy('payment_type')
    ->orderByDesc('count')
    ->get();

foreach ($paymentMethods as $method) {
    echo sprintf("%-15s: %4d orders (%5.1f%%)\n", $method->payment_type, $method->count, $method->percentage);
}
echo "\n";

// Recent Orders Sample
echo "🛒 RECENT ORDERS SAMPLE (Latest 5)\n";
echo "-----------------------------------\n";
$recentOrders = App\Models\Order::latest()->limit(5)->get();
foreach ($recentOrders as $order) {
    echo sprintf("#%-15s | %s | %-25s | ₱%8s | %s\n",
        $order->invoice_no,
        $order->order_date->format('Y-m-d'),
        substr($order->customer_name, 0, 25),
        number_format($order->total, 2),
        ucfirst($order->order_status->value)
    );
}
echo "\n";

// Monthly Peak Analysis
echo "📈 SEASONAL PATTERN (2024 Monthly Sales)\n";
echo "----------------------------------------\n";
$monthlyData = DB::table('orders')
    ->selectRaw('MONTH(order_date) as month, COUNT(*) as orders, SUM(total) as revenue')
    ->where('order_status', 'complete')
    ->whereYear('order_date', 2024)
    ->groupBy('month')
    ->orderBy('month')
    ->get();

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
foreach ($monthlyData as $data) {
    echo sprintf("%-4s: %3d orders | ₱%10s\n",
        $months[$data->month - 1],
        $data->orders,
        number_format($data->revenue, 2)
    );
}

echo "\n";
echo "================================================\n";
echo "✅ Data verification complete!\n";
echo "================================================\n\n";
