<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use App\Models\Product;
use App\Models\CustomerNotification;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get customer's orders
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');

        $query = $customer->orders()->with(['details.product', 'payments']);

        if ($status) {
            $query->where('order_status', $status);
        }

        $orders = $query->latest()->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Show order details for customer (Web view)
     */
    public function showOrder(Request $request, Order $order)
    {
        $customer = $request->user();

        // Ensure customer can only view their own orders
        if ($order->customer_id !== $customer->id) {
            abort(404, 'Order not found');
        }

        $order->load(['details.product.unit', 'customer']);

        return view('customer.order-details', [
            'order' => $order,
        ]);
    }

    /**
     * Get specific order details
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only view their own orders
        if ($order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->load(['details.product', 'payments', 'customer']);

        return response()->json($order);
    }

    /**
     * Place a new order
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:cash,gcash,bank_transfer,card',
            'delivery_notes' => 'nullable|string|max:500',
            
        ]);

        $customer = $request->user();

        try {
            DB::beginTransaction();

            // Calculate order totals
            $totalProducts = 0;
            $subTotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $totalProducts += $item['quantity'];
                $subTotal += $product->price_per_kg * $item['quantity'];
                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price_per_kg,
                ];
            }

            $total = $subTotal;

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_date' => now()->timezone('Asia/Manila')->format('Y-m-d'),
                'order_status' => OrderStatus::PENDING,
                'total_products' => $totalProducts,
                'sub_total' => $subTotal,
                'vat' => 0,
                'total' => $total,
                'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
                'payment_type' => $request->payment_type,
                'pay' => 0,
                'due' => $total,
                'delivery_notes' => $request->delivery_notes,
                'estimated_delivery' => now()->addDays(3), // 3 days delivery estimate
            ]);

            // Create order details
            foreach ($items as $item) {
                $order->details()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Update product quantity
                $item['product']->decrement('quantity', $item['quantity']);
            }

            // Create notification
            CustomerNotification::create([
                'customer_id' => $customer->id,
                'type' => 'order_placed',
                'title' => 'Order Placed Successfully',
                'message' => "Your order #{$order->invoice_no} has been placed successfully. We'll notify you once it's ready.",
                'data' => ['order_id' => $order->id, 'invoice_no' => $order->invoice_no],
            ]);

            DB::commit();

            $order->load(['details.product', 'customer']);

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order)
    {
        $customer = $request->user();

        // Ensure customer can only cancel their own orders
        if ($order->customer_id !== $customer->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Order not found'], 404);
            }
            return back()->with('error', 'Order not found.');
        }

        if (!$order->canBeCancelled()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This order cannot be cancelled',
                ], 400);
            }
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Cancel the order (pass null for cancelledBy since it's customer-initiated)
            $order->cancel($request->reason, null);

            // Restore product quantities
            foreach ($order->details as $detail) {
                $detail->product->increment('quantity', $detail->quantity);
            }

            // Create notification
            CustomerNotification::create([
                'customer_id' => $customer->id,
                'type' => 'order_cancelled',
                'title' => 'Order Cancelled',
                'message' => "Your order #{$order->invoice_no} has been cancelled successfully.",
                'data' => ['order_id' => $order->id, 'invoice_no' => $order->invoice_no],
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Order cancelled successfully',
                    'order' => $order->fresh(),
                ]);
            }

            return back()->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to cancel order: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as received by customer
     */
    public function received(Request $request, Order $order)
    {
        $customer = $request->user();

        if ($order->customer_id !== $customer->id) {
            return back()->with('error', 'Order not found.');
        }

        if ($order->order_status === OrderStatus::COMPLETE || $order->order_status === OrderStatus::CANCELLED) {
            return back()->with('error', 'This order cannot be marked as received.');
        }

        $order->update(['order_status' => OrderStatus::COMPLETE]);

        return back()->with('success', 'Order marked as received.');
    }

    /**
     * Track order status
     */
    public function track(Request $request, Order $order): JsonResponse
    {
        $customer = $request->user();

        // Ensure customer can only track their own orders
        if ($order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $trackingInfo = [
            'order' => $order->load(['details.product', 'payments']),
            'status_timeline' => [
                [
                    'status' => 'Order Placed',
                    'date' => $order->created_at,
                    'completed' => true,
                ],
                [
                    'status' => 'Processing',
                    'date' => $order->order_status === OrderStatus::PENDING ? null : $order->updated_at,
                    'completed' => $order->order_status !== OrderStatus::PENDING,
                ],
                [
                    'status' => 'Ready for Pickup/Delivery',
                    'date' => $order->order_status === OrderStatus::COMPLETE ? $order->updated_at : null,
                    'completed' => $order->order_status === OrderStatus::COMPLETE,
                ],
            ],
        ];

        if ($order->isCancelled()) {
            $trackingInfo['status_timeline'][] = [
                'status' => 'Cancelled',
                'date' => $order->cancelled_at,
                'completed' => true,
                'cancelled' => true,
            ];
        }

        return response()->json($trackingInfo);
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $customer = $request->user();

        $stats = [
            'total_orders' => $customer->orders()->count(),
            'pending_orders' => $customer->pendingOrders()->count(),
            'completed_orders' => $customer->completedOrders()->count(),
            'cancelled_orders' => $customer->orders()->cancelled()->count(),
            'total_spent' => $customer->completedOrders()->sum('total'),
            'average_order_value' => $customer->completedOrders()->avg('total') ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Download invoice for customer order
     */
    public function downloadInvoice(Request $request, Order $order)
    {
        $customer = $request->user();

        // Ensure customer can only download invoices for their own orders
        if ($order->customer_id !== $customer->id) {
            abort(404, 'Order not found');
        }

        $order->load(['customer', 'details.product.unit']);

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }
} 