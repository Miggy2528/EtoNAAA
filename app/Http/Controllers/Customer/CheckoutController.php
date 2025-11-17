<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\InventoryMovement;
use App\Enums\OrderStatus;
use App\Rules\PhoneValidation;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        $cartItems = Cart::instance('customer')->content();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty!');
        }

        // Regenerate CSRF token to prevent 419 errors
        request()->session()->regenerateToken();
        
        $cartSubtotal = str_replace(',', '', Cart::instance('customer')->subtotal());
        $customer = auth()->user();

        // Define barangays in Cabuyao, Laguna
        $barangays = [
            'Baclaran',
            'Banay-banay',
            'Banlic',
            'Bigaa',
            'Butong',
            'Casile',
            'Diezmo',
            'Gulod',
            'Mamatid',
            'Marinig',
            'Niugan',
            'Pittland',
            'Pulo',
            'Sala',
            'San Isidro',
            'Barangay Uno (Poblacion)',
            'Barangay Dos (Poblacion)',
            'Barangay Tres (Poblacion)'
        ];

        return view('customer.checkout.index', compact('cartItems', 'cartSubtotal', 'customer', 'barangays'));
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'receiver_name' => 'nullable|string|max:255',
            'payment_type' => 'required|in:cash,gcash,bank_transfer,card',
            'delivery_notes' => 'nullable|string|max:500',
            'contact_phone' => ['required', 'string', new PhoneValidation],
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'barangay' => 'required|string|max:100',
            'street_name' => 'required|string|max:255',
            'building' => 'nullable|string|max:100',
            'house_no' => 'nullable|string|max:50',
            'gcash_reference' => 'nullable|string|max:100',
            'gcash_receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Process phone number - if it starts with 09, convert to +63
        $requestData = $request->all();
        if (isset($requestData['contact_phone']) && preg_match('/^09\d{9}$/', $requestData['contact_phone'])) {
            $requestData['contact_phone'] = '+63' . substr($requestData['contact_phone'], 1);
        }
        
        // Construct delivery address from location fields
        $requestData['delivery_address'] = $this->constructDeliveryAddress($requestData);
    
        $cartItems = Cart::instance('customer')->content();
    
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }
    
        $customer = auth()->user();
    
        try {
            DB::beginTransaction();
    
            $totalProducts = $cartItems->sum('qty');
            $subTotal = Cart::instance('customer')->subtotal();
            $total = $subTotal;
    
            // Handle gcash_receipt upload
            $proofOfPaymentPath = null;
            if ($request->payment_type === 'gcash' && $request->hasFile('gcash_receipt')) {
                $proofOfPaymentPath = $request->file('gcash_receipt')->store('gcash_receipts', 'public');
            }
    
            // Generate invoice number in format INV-ORD-YYYYMMDD-XXXX
            $invoiceNo = $this->generateInvoiceNumber();
            
            // Construct delivery address from location fields
            $deliveryAddress = $this->constructDeliveryAddress($requestData);

            // Create order with customer name and email from the form
            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'receiver_name' => $request->receiver_name,
                'customer_email' => $customer->email,
                'order_date' => now()->timezone('Asia/Manila')->format('Y-m-d'),
                'order_status' => OrderStatus::PENDING,
                'total_products' => $totalProducts,
                'sub_total' => $subTotal,
                'vat' => 0,
                'total' => $total,
                'invoice_no' => $invoiceNo,
                'tracking_number' => 'TRK-' . strtoupper(Str::random(10)),
                'payment_type' => $request->payment_type,
                'pay' => 0,
                'due' => $total,
                'delivery_notes' => $request->delivery_notes,
                'delivery_address' => $deliveryAddress,
                'contact_phone' => $request->contact_phone,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'barangay' => $request->barangay,
                'street_name' => $request->street_name,
                'building' => $request->building,
                'house_no' => $request->house_no,
                'estimated_delivery' => now()->addDays(3),
                'gcash_reference' => $request->gcash_reference,
                'proof_of_payment' => $proofOfPaymentPath,
            ]);
    
            // Save order details and update inventory
            foreach ($cartItems as $item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => $item->qty,
                    'unitcost' => $item->price,
                    'total' => $item->subtotal,
                ]);
    
                DB::table('products')
                    ->where('id', $item->id)
                    ->decrement('quantity', $item->qty);
    
                InventoryMovement::create([
                    'product_id' => $item->id,
                    'type' => 'out',
                    'quantity' => $item->qty,
                ]);
            }
    
            Cart::instance('customer')->destroy();
    
            DB::commit();
    
            // Clear any old input to prevent 419 on refresh
            session()->flash('success', 'Order placed successfully! Order #' . $order->invoice_no);
            
            return redirect()->route('customer.orders');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. ' . $e->getMessage());
        }
    }

    private function generateInvoiceNumber(): string
    {
        $date = now()->timezone('Asia/Manila')->format('Ymd');
        $prefix = "INV-ORD-{$date}-";
        
        // Get the latest invoice number for today
        $latestOrder = DB::table('orders')
            ->where('invoice_no', 'like', "{$prefix}%")
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($latestOrder) {
            // Extract the sequence number and increment it
            $lastSequence = intval(substr($latestOrder->invoice_no, -4));
            $nextSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First order of the day
            $nextSequence = '0001';
        }
        
        return $prefix . $nextSequence;
    }
    
    private function constructDeliveryAddress(array $data): string
    {
        $addressParts = [];
        
        if (!empty($data['house_no'])) {
            $addressParts[] = $data['house_no'];
        }
        
        if (!empty($data['building'])) {
            $addressParts[] = $data['building'];
        }
        
        if (!empty($data['street_name'])) {
            $addressParts[] = $data['street_name'];
        }
        
        if (!empty($data['barangay'])) {
            $addressParts[] = $data['barangay'];
        }
        
        $addressParts[] = $data['city'] ?? 'Cabuyao';
        $addressParts[] = 'Laguna';
        $addressParts[] = $data['postal_code'] ?? '4025';
        
        return implode(', ', $addressParts);
    }
}