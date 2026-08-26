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
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $quantity = (int)($validated['quantity'] ?? 1);
        $product = Product::with(['variants', 'cjProduct'])->findOrFail($validated['product_id']);
        
        $variant = null;
        if (!empty($validated['variant_id'])) {
            $variant = \App\Models\ProductVariant::where('id', $validated['variant_id'])
                ->where('product_id', $product->id)
                ->first();
            
            if (!$variant) {
                return redirect()->back()->with('error', 'The selected product variant is invalid or unavailable.');
            }
        } elseif ($product->variants && $product->variants->isNotEmpty()) {
            $variant = $product->variants->first();
        }

        // Authoritative pricing from DB - NEVER trust frontend values
        $price = \App\Services\Catalog\PricingService::resolveCustomerPrice($product, $variant);
        $cartKey = $variant ? "{$product->id}_{$variant->id}" : "{$product->id}_0";

        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = min(100, $cart[$cartKey]['quantity'] + $quantity);
        } else {
            $cart[$cartKey] = [
                'product_id'     => $product->id,
                'variant_id'     => $variant?->id,
                'name'           => $product->name,
                'variant_name'   => $variant?->name,
                'price'          => (float)$price,
                'quantity'       => $quantity,
                'image'          => $variant?->image_url ?: $product->customer_thumbnail,
                'sku'            => $variant?->sku ?: $product->merchant_sku,
                'cj_product_id'  => $product->cjProduct?->cj_product_id,
                'cj_variant_id'  => $variant?->cj_variant_id ?: $product->cjProduct?->cj_variant_id,
                'cj_variant_sku' => $variant?->cjVariant?->cj_variant_sku ?: $product->cjProduct?->cj_variant_sku,
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
            'country' => 'required',
            'state' => 'nullable'
        ]);

        session(['checkout_shipping' => $validated]);

        $otp = (string)rand(100000, 999999);
        session(['checkout_otp' => $otp, 'checkout_otp_expires_at' => time() + 600]);

        $isLocalOrTesting = app()->environment(['local', 'testing']) || config('app.debug');

        try {
            Mail::raw("Your AtoZGadgets verification OTP is: {$otp}. Valid for 10 minutes.", function ($m) use ($validated) {
                $m->to($validated['email'])
                  ->subject("AtoZGadgets Checkout Verification Code");
            });
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$validated['email']}: " . $e->getMessage());

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

        // Verify that OTP was verified
        if(!session('checkout_otp_verified')) {
            return redirect()->route('store.checkout')->with('error', 'Please verify your phone/email via OTP first.');
        }

        $shipping = session('checkout_shipping', []);
        $paymentMethod = $request->input('payment_method', 'paypal');

        try {
            // 1. Create Immutable Checkout Session with authoritative DB prices
            $session = \App\Services\Checkout\CheckoutService::createSession(auth()->id(), $cart, $shipping);

            // 2. Pre-Create Order in PENDING state
            $order = \App\Services\Order\OrderService::createPendingOrderFromSession($session, $shipping);

            // If paying with PayPal, proceed to PayPal payment authorization
            if ($paymentMethod === 'paypal') {
                return redirect()->route('store.checkout')->with('info', 'Please complete payment with the PayPal button below to finalize your order.');
            }

            return redirect()->route('store.checkout')->with('error', 'Please select a valid payment method.');
        } catch (\Throwable $e) {
            return redirect()->route('store.checkout')->with('error', 'Checkout error: ' . $e->getMessage());
        }
    }

    public function checkShippingEligibility(Request $request)
    {
        $country = $request->input('country', 'US');
        $rawCart = session()->get('cart', []);

        if (empty($rawCart)) {
            return response()->json(['eligible' => false, 'message' => 'Your cart is empty.']);
        }

        $result = \App\Services\Shipping\CjShippingEligibilityService::checkEligibility($rawCart, $country);
        return response()->json($result);
    }
}
