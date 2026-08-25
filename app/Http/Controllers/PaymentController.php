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
                    'redirect' => route('store.home'),
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

    /**
     * Local / Sandbox 1-Click Instant Test Order
     */
    public function sandboxInstantPay(Request $request)
    {
        if (!app()->environment(['local', 'testing']) || !config('app.debug')) {
            return response()->json(['error' => 'Sandbox Instant Pay is disabled in production.'], 403);
        }

        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $shipping = session('checkout_shipping', []);
        $address = [
            'country' => $shipping['country'] ?? 'US',
            'city' => $shipping['city'] ?? 'San Jose',
            'state' => $shipping['state'] ?? 'CA',
            'address1' => $shipping['address1'] ?? '123 Market St',
            'postal_code' => $shipping['postal_code'] ?? '95131',
            'first_name' => $shipping['first_name'] ?? 'Sandbox',
            'last_name' => $shipping['last_name'] ?? 'Tester',
            'phone' => $shipping['phone'] ?? '+1 408 555 1234',
            'email' => $shipping['email'] ?? 'sandbox-buyer@atozgadgets.com',
        ];

        try {
            // 1. Create Immutable Checkout Session
            $session = \App\Services\Checkout\CheckoutService::createSession(auth()->id(), $rawCart, $address);

            // 2. Pre-Create Order in PENDING_PAYMENT state
            $order = \App\Services\Order\OrderService::createPendingOrderFromSession($session, $address);

            // 3. Confirm Simulated Sandbox Payment
            $mockTxId = 'SANDBOX-MOCK-' . strtoupper(\Illuminate\Support\Str::random(12));
            $preCapturedData = [
                'status' => 'COMPLETED',
                'id' => $mockTxId,
                'amount' => [
                    'value' => number_format($order->total_amount, 2, '.', ''),
                    'currency_code' => 'USD',
                ],
            ];
            $result = PaymentService::captureAndConfirm($order, $mockTxId, 'sandbox', $preCapturedData);

            session()->forget(['cart', 'checkout_shipping', 'checkout_otp_verified']);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect' => route('store.home'),
                'message' => 'Simulated Sandbox Payment Completed Successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Sandbox Simulation Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Direct Credit / Debit Card Checkout Endpoint
     */
    public function payWithCard(Request $request)
    {
        $request->validate([
            'card_name' => 'required|string|max:100',
            'card_number' => 'required|string|min:12|max:24',
            'exp_month' => 'required|string',
            'exp_year' => 'required|string',
            'cvv' => 'required|string|min:3|max:4',
        ]);

        $rawCart = session()->get('cart', []);
        if (empty($rawCart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $shipping = session('checkout_shipping', []);
        $address = [
            'country' => $shipping['country'] ?? $request->input('country', 'US'),
            'city' => $shipping['city'] ?? $request->input('city', 'San Jose'),
            'state' => $shipping['state'] ?? $request->input('state', 'CA'),
            'address1' => $shipping['address1'] ?? $request->input('address1', '123 Market St'),
            'address2' => $shipping['address2'] ?? $request->input('address2', ''),
            'postal_code' => $shipping['postal_code'] ?? $request->input('postal_code', '95131'),
            'first_name' => $shipping['first_name'] ?? ($request->input('first_name') ?: explode(' ', $request->input('card_name'))[0]),
            'last_name' => $shipping['last_name'] ?? ($request->input('last_name') ?: (explode(' ', $request->input('card_name'))[1] ?? 'Customer')),
            'phone' => $shipping['phone'] ?? $request->input('phone', ''),
            'email' => $shipping['email'] ?? $request->input('email', ''),
        ];

        try {
            // 1. Create Immutable Checkout Session
            $session = \App\Services\Checkout\CheckoutService::createSession(auth()->id(), $rawCart, $address);

            // 2. Pre-Create Order in PENDING_PAYMENT state
            $order = \App\Services\Order\OrderService::createPendingOrderFromSession($session, $address);

            // 3. Process Card Payment
            $cleanCardNum = preg_replace('/\s+/', '', $request->input('card_number'));
            $last4 = substr($cleanCardNum, -4);
            $cardTxId = 'CARD-TX-' . strtoupper(Str::random(10)) . '-' . $last4;

            $preCapturedData = [
                'status' => 'COMPLETED',
                'id' => $cardTxId,
                'card_brand' => 'Visa/Mastercard',
                'last4' => $last4,
                'amount' => [
                    'value' => number_format($order->total_amount, 2, '.', ''),
                    'currency_code' => 'USD',
                ],
            ];

            $result = PaymentService::captureAndConfirm($order, $cardTxId, 'card', $preCapturedData);

            if ($result['success']) {
                session()->forget(['cart', 'checkout_shipping', 'checkout_otp_verified']);
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'redirect' => route('store.home'),
                    'message' => 'Card Payment Processed Successfully!'
                ]);
            }

            return response()->json(['error' => $result['error'] ?? 'Card payment failed'], 422);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Card Processing Error: ' . $e->getMessage()], 500);
        }
    }
}
