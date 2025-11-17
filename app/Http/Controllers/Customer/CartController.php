<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index()
    {
        $cartItems = Cart::instance('customer')->content();
        $cartSubtotal = str_replace(',', '', Cart::instance('customer')->subtotal());

        return view('customer.cart.index', compact('cartItems', 'cartSubtotal'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is available
        if ($product->quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock for ' . $product->name);
        }

        // Check if product already exists in cart
        $existingItem = Cart::instance('customer')->search(function ($cartItem) use ($product) {
            return $cartItem->id == $product->id;
        });

        if ($existingItem->isNotEmpty()) {
            // Update quantity if product already in cart
            $rowId = $existingItem->first()->rowId;
            $newQuantity = $existingItem->first()->qty + $request->quantity;
            
            if ($product->quantity < $newQuantity) {
                return back()->with('error', 'Insufficient stock for ' . $product->name);
            }
            
            Cart::instance('customer')->update($rowId, $newQuantity);
        } else {
            // Add new item to cart
            Cart::instance('customer')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => $request->quantity,
                'price' => $product->selling_price ?? 0,
                'weight' => 1,
                'options' => [
                    'code' => $product->code,
                    'stock' => $product->quantity,
                    'unit' => $product->unit->name ?? 'kg',
                    'image' => $product->product_image ?? null,
                ],
            ]);
        }

        return back()->with('success', $product->name . ' added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $rowId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::instance('customer')->get($rowId);
        
        if (!$cartItem) {
            return back()->with('error', 'Item not found in cart');
        }

        $product = Product::find($cartItem->id);
        
        if (!$product) {
            Cart::instance('customer')->remove($rowId);
            return back()->with('error', 'Product no longer available');
        }

        if ($product->quantity < $request->quantity) {
            return back()->with('error', 'Insufficient stock for ' . $product->name);
        }

        Cart::instance('customer')->update($rowId, $request->quantity);

        return back()->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove item from cart
     */
    public function remove($rowId)
    {
        Cart::instance('customer')->remove($rowId);
        return back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        Cart::instance('customer')->destroy();
        return back()->with('success', 'Cart cleared successfully!');
    }
} 