<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Str;
use App\Services\PayPalService;
use App\Services\PaymentService;

class PaymentController extends Controller
{


    /**
     * PayPal Create Order Endpoint
     */
    public function paypalCreateOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $total = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });

        if ($total < 30) {
            $total += 5.99; // Shipping
        }

        try {
            $order = PayPalService::createOrder($total);
            return response()->json($order);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'PayPal Error: ' . $e->getMessage()], 500);
        }
    }

    public function paypalCaptureOrder(Request $request)
    {
        $request->validate([
            'paypal_order_id' => 'required|string'
        ]);
        
        try {
            $capture = PayPalService::captureOrder($request->paypal_order_id);
            
            if (isset($capture['status']) && $capture['status'] === 'COMPLETED') {
                $cart = session()->get('cart', []);
                $total = collect($cart)->sum(function($item) {
                    return $item['price'] * $item['quantity'];
                });
                if ($total < 30) { $total += 5.99; }

                // Create Order, Items, and Process Payment atomically
                \Illuminate\Support\Facades\DB::transaction(function () use ($total, $capture, $cart) {
                    $order = Order::create([
                        'user_id' => auth()->id(),
                        'total_amount' => $total,
                        'status' => 'processing',
                        'payment_status' => 'paid',
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

                    $amount = $capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0;
                    PaymentService::processPayment($order->id, 'paypal', $capture['id'], $amount);
                });
                
                session()->forget('cart');

                return response()->json(['success' => true, 'capture' => $capture, 'redirect' => route('store.home')]);
            }
            
            return response()->json(['success' => false, 'error' => 'Capture failed', 'details' => $capture], 400);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => 'PayPal Error: ' . $e->getMessage()], 500);
        }
    }
}
