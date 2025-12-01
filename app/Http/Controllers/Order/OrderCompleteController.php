<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderCompleteController extends Controller
{
    public function __invoke(Request $request)
    {
        $orders = Order::query()
            ->whereIn('order_status', [OrderStatus::COMPLETE, '1', 1])
            ->with('customer')
            ->latest()
            ->get();

        return view('orders.complete-orders', [
            'orders' => $orders
        ]);
    }
}
