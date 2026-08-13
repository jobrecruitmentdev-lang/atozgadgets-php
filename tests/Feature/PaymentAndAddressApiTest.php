<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentAndAddressApiTest extends TestCase
{
    public function test_paypal_create_order_endpoint()
    {
        // 400 because cart is empty
        $response = $this->postJson('/api/payment/paypal/create-order');
        $response->assertStatus(400);
    }

    public function test_paypal_capture_order_endpoint()
    {
        // 422 because order_id is missing
        $response = $this->postJson('/api/payment/paypal/capture-order');
        $response->assertStatus(422);
    }
}
