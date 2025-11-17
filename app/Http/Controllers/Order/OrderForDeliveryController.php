<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderForDeliveryController extends Controller
{
    public function __invoke(Request $request)
    {
        $orders = Order::query()
            ->where('order_status', OrderStatus::FOR_DELIVERY)
            ->with('customer')
            ->latest()
            ->get();

        return view('orders.for-delivery-orders', [
            'orders' => $orders
        ]);
    }
}
