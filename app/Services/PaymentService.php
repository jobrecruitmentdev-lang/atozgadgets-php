<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process a payment payload from the checkout form
     * Handles dummy logic for Razorpay/PayPal mocking
     */
    public static function processPayment($orderId, $paymentMethod, $transactionId, $amount)
    {
        return DB::transaction(function () use ($orderId, $paymentMethod, $transactionId, $amount) {
            $order = Order::find($orderId);
            
            if (!$order) {
                throw new \Exception("Order not found for payment processing.");
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => 'completed',
            ]);

            // Automatically update order status to processing upon successful payment
            OrderService::updateStatus($order->id, 'processing', $order->user_id);

            Log::info("Payment processed successfully for Order {$order->order_number} via {$paymentMethod}");

            return $payment;
        });
    }
}
