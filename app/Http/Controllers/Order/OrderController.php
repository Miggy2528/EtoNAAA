<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        Cart::instance('order')->destroy();

        return view('orders.create', [
            'carts' => Cart::content(),
            'customers' => Customer::all(['id', 'name']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            // Handle proof of payment upload if exists
            $proofOfPaymentPath = null;
            if ($request->hasFile('gcash_receipt')) {
                $proofOfPaymentPath = $request->file('gcash_receipt')->store('proofs', 'public');
            }

            // Create the order
            $order = Order::create([
                'customer_id' => Auth::guard('web_customer')->id(),
                'gcash_reference' => $request->gcash_reference,
                'proof_of_payment' => $proofOfPaymentPath,
                'order_status' => OrderStatus::PENDING,
                'total_price' => Cart::instance('order')->subtotal(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create order details
            $contents = Cart::instance('order')->content();
            $oDetails = [];

            foreach ($contents as $content) {
                $oDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $content->id,
                    'quantity' => $content->qty,
                    'unitcost' => $content->price,
                    'total' => $content->subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            OrderDetails::insert($oDetails);

            // Clear cart
            Cart::destroy();

            DB::commit();

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details.product.unit']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function update(Order $order, Request $request)
    {
        $products = OrderDetails::where('order_id', $order->id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['quantity' => DB::raw('quantity - ' . $product->quantity)]);
        }

        $order->update([
            'order_status' => OrderStatus::COMPLETE,
        ]);

        return redirect()
            ->route('orders.complete')
            ->with('success', 'Order has been completed!');
    }

    public function destroy(Order $order)
    {
        // Use soft delete instead of hard delete
        $order->delete();
        
        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been removed successfully!');
    }

    /**
     * Update order status via AJAX
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:Cancelled,For Delivery,Completed',
        ]);

        try {
            // Map the new status to the enum values
            $statusMap = [
                'Cancelled' => OrderStatus::CANCELLED,
                'For Delivery' => OrderStatus::FOR_DELIVERY,
                'Completed' => OrderStatus::COMPLETE,
            ];

            $newStatus = $statusMap[$request->status];
            
            $order->update(['order_status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'status' => $request->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadInvoice($order)
    {
        $order = Order::with(['customer', 'details'])
            ->where('id', $order)
            ->first();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }

    /**
     * Show customer's orders (for authenticated customers)
     */
    public function myOrders()
    {
        $customer = Auth::guard('web_customer')->user();

        if (!$customer) {
            return redirect()->route('customer.login')->with('error', 'You must be logged in to view your orders.');
        }

        $orders = Order::where('customer_id', $customer->id)
            ->with(['details.product'])
            ->latest()
            ->get();

        return view('customer.orders', [
            'orders' => $orders,
            'customer' => $customer
        ]);
    }

//  Update Status
public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'order_status' => 'required|in:pending,for_delivery,complete,cancelled',
    ]);

    $newStatus = OrderStatus::from($request->order_status);
    
    // If cancelling the order, use the proper cancel method to trigger notifications
    if ($newStatus === OrderStatus::CANCELLED && $order->order_status !== OrderStatus::CANCELLED) {
        try {
            DB::beginTransaction();
            
            // Use the cancel method which triggers notifications
            $order->cancel('Order cancelled via status update', Auth::user());
            
            // Restore product quantities
            foreach ($order->details as $detail) {
                $detail->product->increment('quantity', $detail->quantity);
            }
            
            DB::commit();
            
            return back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    } else {
        // For other status updates, use update method to trigger model events
        $order->update(['order_status' => $newStatus]);
        
        return back()->with('success', 'Order status updated successfully.');
    }
}

/**
 * Cancel an order (Admin)
 */
public function cancel(Request $request, Order $order)
{
    $request->validate([
        'cancellation_reason' => 'required|string|max:500',
    ]);

    if (!$order->canBeCancelled()) {
        return back()->with('error', 'This order cannot be cancelled.');
    }

    try {
        DB::beginTransaction();

        // Cancel the order (this will trigger the notification)
        $order->cancel($request->cancellation_reason, Auth::user());

        // Restore product quantities
        foreach ($order->details as $detail) {
            $detail->product->increment('quantity', $detail->quantity);
        }

        DB::commit();

        return back()->with('success', 'Order cancelled successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
    }
}
}