<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('store.cart', compact('cart', 'total'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);
        
        if(isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->discount_price ?? $product->price,
                "image" => $product->thumbnail_image
            ];
        }
        
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
    
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if(empty($cart)) {
            return redirect()->route('store.cart');
        }
        
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('store.checkout', compact('cart', 'total'));
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);
        if(empty($cart)) {
            return redirect()->route('store.cart');
        }

        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Add dummy shipping cost if under 30
        if($total < 30) {
            $total += 5.99;
        }

        $paymentMethod = $request->input('payment_method', 'paypal');

        // Create Order (Mock logic for now)
        $order = \App\Models\Order::create([
            'user_id' => auth()->id() ?? 1,
            'total_amount' => $total,
            'status' => 'processing'
        ]);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'amount' => $total,
            'payment_method' => $paymentMethod,
            'status' => 'completed'
        ]);

        session()->forget('cart');

        return redirect()->route('store.home')->with('success', 'Order placed successfully! Paid via ' . ucfirst($paymentMethod) . '.');
    }
}
