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
        if (empty($cart)) {
            return redirect()->route('store.shop')->with('info', 'Your cart is empty. Add gadgets to proceed to checkout!');
        }
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

        $isLocalOrTesting = app()->environment(['local', 'testing']) || config('app.debug');

        // Ponytail: Send simple raw email, fallback gracefully to dev OTP in local/testing
        try {
            if (!$isLocalOrTesting || !empty(env('MAIL_HOST'))) {
                Mail::raw("Your AtoZGadgets checkout verification code is: {$otp}\n\nThis code is valid for 10 minutes.", function ($message) use ($validated) {
                    $message->to($validated['email'])
                            ->subject('Your Checkout OTP Code - AtoZGadgets');
                });
            } else {
                Log::info("[DEV/LOCAL OTP] Email: {$validated['email']}, OTP: {$otp}");
            }
        } catch (\Exception $e) {
            Log::warning("Failed to send OTP email: " . $e->getMessage());
            if ($isLocalOrTesting) {
                Log::info("[DEV/LOCAL OTP FALLBACK] Email: {$validated['email']}, OTP: {$otp}");
                return response()->json([
                    'success' => true,
                    'dev_otp' => (string)$otp,
                    'message' => '[DEV] OTP generated & logged. Master code 123456 ready.'
                ]);
            }
            return response()->json(['success' => false, 'error' => 'Failed to send OTP email. Please check your SMTP configuration.']);
        }

        return response()->json([
            'success' => true,
            'dev_otp' => $isLocalOrTesting ? (string)$otp : null
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $enteredOtp = (string)$request->otp;
        $sessionOtp = (string)session('checkout_otp');
        $expiresAt = session('checkout_otp_expires_at');
        $isLocalOrTesting = app()->environment(['local', 'testing']) || config('app.debug');

        // 1. Master Testing Code & Local Bypass (123456)
        if ($isLocalOrTesting && $enteredOtp === '123456') {
            session()->forget(['checkout_otp', 'checkout_otp_expires_at']);
            session(['checkout_otp_verified' => true]);
            return response()->json(['success' => true, 'message' => '[DEV] Verified via master testing OTP.']);
        }

        if (!$sessionOtp || !$expiresAt || time() > $expiresAt) {
            return response()->json(['success' => false, 'error' => 'OTP has expired. Please go back and resend.']);
        }

        if ($enteredOtp !== $sessionOtp) {
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
        $shipping = session('checkout_shipping', []);

        // Create Order and Items in DB transaction
        $order = \Illuminate\Support\Facades\DB::transaction(function () use ($total, $shipping, $cart, $paymentMethod) {
            $order = \App\Models\Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'total_amount' => $total,
                'status' => 'processing',
                'payment_status' => 'paid',
                'shipping_address' => json_encode($shipping),
                'contact_email' => $shipping['email'] ?? null,
                'contact_phone' => $shipping['phone'] ?? null
            ]);

            foreach ($cart as $productId => $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => is_numeric($productId) ? (int)$productId : null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['price'] ?? 0,
                    'total_price' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
                    'status' => 'active'
                ]);
            }

            \App\Models\Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'payment_method' => $paymentMethod,
                'status' => 'completed'
            ]);

            return $order;
        });

        // Auto-dispatch CJ fulfillable items if configured
        try {
            \App\Services\Cj\CjOrderService::placeOrder($order->id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::info('CJ auto-dispatch deferred: ' . $e->getMessage());
        }

        session()->forget(['cart', 'checkout_shipping', 'checkout_otp_verified']);

        return redirect()->route('store.home')->with('success', 'Order placed successfully! Paid via ' . ucfirst($paymentMethod) . '.');
    }
}
