<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\Checkout\CheckoutService;
use App\Services\Order\OrderService;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * PayPal Create Order Endpoint (Commerce Core Flow)
     */
    public function paypalCreateOrder(Request $request)
    {
        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $shipping = session('checkout_shipping', []);
        $address = $request->input('address', [
            'country' => $shipping['country'] ?? $request->input('country', 'US'),
            'city' => $shipping['city'] ?? $request->input('city', ''),
            'state' => $shipping['state'] ?? $request->input('state', ''),
            'address1' => $shipping['address1'] ?? $request->input('address1', ''),
            'address2' => $shipping['address2'] ?? $request->input('address2', ''),
            'postal_code' => $shipping['postal_code'] ?? $request->input('postal_code', ''),
            'first_name' => $shipping['first_name'] ?? $request->input('first_name', ''),
            'last_name' => $shipping['last_name'] ?? $request->input('last_name', ''),
            'phone' => $shipping['phone'] ?? $request->input('phone', ''),
            'email' => $shipping['email'] ?? $request->input('email', ''),
        ]);

        try {
            // 0. Authoritative CJ Shipping & Country Eligibility Guard
            $countryCode = $address['country'] ?? 'US';
            $eligibility = \App\Services\Shipping\CjShippingEligibilityService::checkEligibility($rawCart, $countryCode);
            if (!$eligibility['eligible']) {
                return response()->json(['error' => $eligibility['message']], 422);
            }

            // 1. Create Immutable Checkout Session
            $session = CheckoutService::createSession(auth()->id(), $rawCart, $address);

            // 2. Pre-Create Order in PENDING_PAYMENT state
            $order = OrderService::createPendingOrderFromSession($session, $address);

            // 3. Create Gateway Payment Intent
            $gatewayOrder = PaymentService::createIntent($order, 'paypal');

            return response()->json([
                'id' => $gatewayOrder['id'] ?? null,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Payment Intent Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * PayPal Capture Order Endpoint
     */
    public function paypalCaptureOrder(Request $request)
    {
        $request->validate([
            'paypal_order_id' => 'required|string',
            'order_id' => 'required|integer',
        ]);

        $order = Order::find($request->input('order_id'));

        if (!$order) {
            return response()->json(['success' => false, 'error' => 'Order not found'], 404);
        }

        try {
            $result = PaymentService::captureAndConfirm($order, $request->paypal_order_id, 'paypal');

            if ($result['success']) {
                session()->forget(['cart', 'checkout_shipping', 'checkout_otp_verified']);
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'redirect' => route('store.order_confirmation', ['order_number' => $order->order_number]),
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Capture failed',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => 'Capture Error: ' . $e->getMessage()], 500);
        }
    }
}
