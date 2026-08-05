<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentAndAddressApiTest extends TestCase
{
    public function test_razorpay_create_order_endpoint()
    {
        $response = $this->postJson('/api/payment/razorpay/create-order', ['amount' => 500]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'id', 'currency', 'amount']);
        $response->assertJson(['currency' => 'INR', 'amount' => 50000]);
    }

    public function test_razorpay_verify_endpoint()
    {
        $response = $this->postJson('/api/payment/razorpay/verify', [
            'razorpay_order_id' => 'order_123',
            'razorpay_payment_id' => 'pay_123'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'verified']);
    }

    public function test_paypal_create_order_endpoint()
    {
        $response = $this->postJson('/api/payment/paypal/create-order');

        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'status']);
    }

    public function test_paypal_capture_order_endpoint()
    {
        $response = $this->postJson('/api/payment/paypal/capture-order');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'COMPLETED']);
    }
}
