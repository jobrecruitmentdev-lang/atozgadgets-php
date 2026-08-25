<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\CjOrder;
use App\Services\Payment\PaymentService;
use App\Services\Catalog\ProductContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityContainmentAndInvariantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cj_webhook_rejects_unauthorized_requests()
    {
        Setting::set('cj_webhook_secret', 'valid-secret-token-12345', 'cj', true);

        // 1. Missing Token -> 401
        $res = $this->postJson('/api/cj/webhook', [
            'orderNumber' => 'ORD-TEST-999',
            'orderStatus' => 'cancelled',
        ]);
        $res->assertStatus(401);
        $res->assertJson(['success' => false]);

        // 2. Invalid Token -> 401
        $res = $this->postJson('/api/cj/webhook', [
            'orderNumber' => 'ORD-TEST-999',
            'orderStatus' => 'cancelled',
        ], [
            'X-CJ-Webhook-Token' => 'wrong-secret-token',
        ]);
        $res->assertStatus(401);
    }

    public function test_cj_webhook_accepts_valid_token_and_rejects_illegal_state_transitions()
    {
        Setting::set('cj_webhook_secret', 'valid-secret-token-12345', 'cj', true);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-LEGAL-01',
            'total_amount' => 100.00,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        CjOrder::create([
            'internal_order_id' => $order->id,
            'cj_order_id' => 'CJ-ORDER-01',
            'status' => 'delivered',
            'order_amount' => 50.00,
        ]);

        // Attempt to cancel a delivered/completed order via webhook -> must be rejected
        $res = $this->postJson('/api/cj/webhook', [
            'orderNumber' => 'ORD-LEGAL-01',
            'orderStatus' => 'cancelled',
        ], [
            'X-CJ-Webhook-Token' => 'valid-secret-token-12345',
        ]);

        $res->assertStatus(422);
        $this->assertEquals('completed', $order->fresh()->status);
    }

    public function test_refund_rejects_negative_and_zero_amounts()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-REF-01',
            'total_amount' => 100.00,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method' => 'paypal',
            'status' => 'success',
        ]);

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'type' => 'CAPTURE',
            'amount' => 100.00,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => 'TX-100',
            'status' => 'completed',
        ]);

        // 1. Negative Refund -> Reject
        $resNeg = PaymentService::processRefund($order, -25.00);
        $this->assertFalse($resNeg['success']);
        $this->assertStringContainsString('greater than $0.00', $resNeg['error']);

        // 2. Zero Refund -> Reject
        $resZero = PaymentService::processRefund($order, 0.00);
        $this->assertFalse($resZero['success']);

        // Assert ledger remains intact ($100 net paid)
        $ledger = PaymentService::getLedgerSummary($order);
        $this->assertEquals(100.00, $ledger->net_paid);
    }

    public function test_refund_rejects_amounts_exceeding_refundable_balance()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-REF-02',
            'total_amount' => 50.00,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => 50.00,
            'currency' => 'USD',
            'payment_method' => 'paypal',
            'status' => 'success',
        ]);

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'type' => 'CAPTURE',
            'amount' => 50.00,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => 'TX-50',
            'status' => 'completed',
        ]);

        // 1. First partial refund of $30.00 -> Success
        $res1 = PaymentService::processRefund($order, 30.00);
        $this->assertTrue($res1['success']);
        $this->assertEquals(20.00, $res1['remaining_balance']);

        // 2. Second refund of $25.00 (exceeds remaining $20.00) -> Reject
        $res2 = PaymentService::processRefund($order, 25.00);
        $this->assertFalse($res2['success']);
        $this->assertStringContainsString('exceeds available refundable balance', $res2['error']);

        // 3. Exact remaining refund of $20.00 -> Success
        $res3 = PaymentService::processRefund($order, 20.00);
        $this->assertTrue($res3['success']);
        $this->assertEquals(0.00, $res3['remaining_balance']);
        $this->assertEquals('refunded', $order->fresh()->status);
    }

    public function test_staff_role_cannot_access_settings_hub()
    {
        // Role 2 = Staff
        $staffUser = User::factory()->create(['role_id' => 2]);
        // Role 1 = Superadmin
        $adminUser = User::factory()->create(['role_id' => 1]);

        // 1. Staff User -> 403 Forbidden
        $resStaff = $this->actingAs($staffUser)->get('/admin/settings');
        $resStaff->assertStatus(403);

        $resStaffPost = $this->actingAs($staffUser)->post('/admin/settings', [
            'store_name' => 'Hacked Store',
        ]);
        $resStaffPost->assertStatus(403);

        // 2. Superadmin User -> 200 OK
        $resAdmin = $this->actingAs($adminUser)->get('/admin/settings');
        $resAdmin->assertStatus(200);
    }

    public function test_settings_view_masks_sensitive_secrets()
    {
        $adminUser = User::factory()->create(['role_id' => 1]);

        Setting::set('cj_api_key', 'REAL_SECRET_KEY_123456789', 'cj', true);
        Setting::set('paypal_live_client_secret', 'REAL_PAYPAL_LIVE_SECRET_9876', 'payments', true);

        $res = $this->actingAs($adminUser)->get('/admin/settings');
        $res->assertStatus(200);

        // Secrets must NOT appear in plaintext in the DOM
        $res->assertDontSee('REAL_SECRET_KEY_123456789');
        $res->assertDontSee('REAL_PAYPAL_LIVE_SECRET_9876');

        // Masked placeholders must appear
        $res->assertSee('••••••••••••');
    }

    public function test_media_downloader_blocks_ssrf_private_ips()
    {
        // 1. AWS/Cloud Metadata IP
        $resultMeta = ProductContentService::downloadAndStoreMedia('http://169.254.169.254/latest/meta-data/');
        $this->assertEmpty($resultMeta);

        // 2. Localhost
        $resultLocal = ProductContentService::downloadAndStoreMedia('http://127.0.0.1:8000/internal-secret');
        $this->assertEmpty($resultLocal);
    }
}