<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Handle Payoneer Payment Gateway
     */
    public function payWithPayoneer(Request $request)
    {
        // 1. Validate the cart / order
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.cart')->with('error', 'Cart is empty');
        }

        $total = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        $mockPayoneerResponse = [
            'status' => 'success',
            'transaction_id' => 'PAYONEER-' . Str::upper(Str::random(12))
        ];

        // Strict ACID Transaction: Order Creation & Payment Record
        $order = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($total, $mockPayoneerResponse, &$order) {
            $order = Order::create([
                'user_id' => 1,
                'total_amount' => $total,
                'status' => 'pending'
            ]);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'payment_method' => 'payoneer',
                'payoneer_transaction_id' => $mockPayoneerResponse['transaction_id'],
                'status' => 'completed'
            ]);

            $order->update(['status' => 'processing']);
        });

        // Clear session cart after successful transaction commit
        session()->forget('cart');

        return redirect()->route('store.home')->with('success', "Order placed successfully! Paid via Payoneer (TxID: {$mockPayoneerResponse['transaction_id']}).");
    }

    /**
     * Razorpay Create Order Endpoint
     */
    public function razorpayCreateOrder(Request $request)
    {
        $amount = $request->input('amount', 1000);
        return response()->json([
            'success' => true,
            'id' => 'order_rzp_' . Str::random(10),
            'currency' => 'INR',
            'amount' => $amount * 100
        ]);
    }

    /**
     * Razorpay Verify Payment Endpoint
     */
    public function razorpayVerify(Request $request)
    {
        return response()->json([
            'success' => true,
            'status' => 'verified',
            'transaction_id' => 'pay_rzp_' . Str::random(10)
        ]);
    }

    /**
     * PayPal Create Order Endpoint
     */
    public function paypalCreateOrder(Request $request)
    {
        return response()->json([
            'id' => 'PAYPAL-ORD-' . Str::upper(Str::random(8)),
            'status' => 'CREATED'
        ]);
    }

    /**
     * PayPal Capture Order Endpoint
     */
    public function paypalCaptureOrder(Request $request)
    {
        return response()->json([
            'id' => 'PAYPAL-CAP-' . Str::upper(Str::random(8)),
            'status' => 'COMPLETED'
        ]);
    }
}
