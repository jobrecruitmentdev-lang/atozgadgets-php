<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CartController extends Controller
{
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
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
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        return view('store.checkout', compact('cart', 'total'));
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address1' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'country' => 'required'
        ]);

        // Save shipping details to session
        session(['checkout_shipping' => $validated]);

        // Generate 6 digit OTP (Cryptographically Secure)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        session([
            'checkout_otp' => (string) $otp,
            'checkout_otp_expires_at' => time() + 600 // 600 seconds = 10 minutes
        ]);

        // Ponytail: Send simple raw email instead of complex Mailable
        try {
            Mail::raw("Your AtoZGadgets checkout verification code is: {$otp}\n\nThis code is valid for 10 minutes.", function ($message) use ($validated) {
                $message->to($validated['email'])
                        ->subject('Your Checkout OTP Code - AtoZGadgets');
            });
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to send OTP email. Please check your SMTP configuration.']);
        }

        return response()->json(['success' => true]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);

        $sessionOtp = session('checkout_otp');
        $expiresAt = session('checkout_otp_expires_at');

        if (!$sessionOtp || !$expiresAt || time() > $expiresAt) {
            return response()->json(['success' => false, 'error' => 'OTP has expired. Please go back and resend.']);
        }

        if ($request->otp !== $sessionOtp) {
            return response()->json(['success' => false, 'error' => 'Invalid OTP code.']);
        }

        // OTP Validated
        session()->forget(['checkout_otp', 'checkout_otp_expires_at']);
        session(['checkout_otp_verified' => true]);

        return response()->json(['success' => true]);
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);
        if(empty($cart)) {
            return redirect()->route('store.cart');
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Add dummy shipping cost if under 30
        if($total < 30) {
            $total += 5.99;
        }

        // Verify that OTP was verified
        if(!session('checkout_otp_verified')) {
            return redirect()->route('store.checkout')->with('error', 'Please verify your phone/email via OTP first.');
        }

        $paymentMethod = $request->input('payment_method', 'paypal');

        // Create Order
        $shipping = session('checkout_shipping', []);
        
        $order = \App\Models\Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => auth()->id(), // Nullable if guest checkout allowed
            'total_amount' => $total,
            'status' => 'processing',
            'shipping_address' => json_encode($shipping),
            'contact_email' => $shipping['email'] ?? null,
            'contact_phone' => $shipping['phone'] ?? null
        ]);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'amount' => $total,
            'payment_method' => $paymentMethod,
            'status' => 'completed'
        ]);

        // ponytail: Sync to CJ inline. Move to Queue job if CJ API latency hurts UX.
        \App\Services\CjDropshippingService::syncOrder($order, $cart);

        session()->forget(['cart', 'checkout_shipping', 'checkout_otp_verified']);

        return redirect()->route('store.home')->with('success', 'Order placed successfully! Paid via ' . ucfirst($paymentMethod) . '.');
    }
}
