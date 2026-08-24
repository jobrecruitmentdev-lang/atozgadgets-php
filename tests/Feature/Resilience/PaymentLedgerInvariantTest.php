<?php

namespace Tests\Feature\Resilience;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\PaymentService;

class PaymentLedgerInvariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_ledger_summary_calculates_net_paid_authoritatively()
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-FIN-100',
            'subtotal' => 100.00,
            'total_amount' => 100.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'transaction_id' => 'CAP-100',
            'amount' => 100.00,
            'payment_method' => 'paypal',
            'status' => 'success',
        ]);

        // Capture $100
        PaymentTransaction::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'type' => 'CAPTURE',
            'amount' => 100.00,
            'currency' => 'USD',
            'provider' => 'paypal',
            'provider_transaction_id' => 'CAP-100',
            'status' => 'completed',
        ]);

        $ledger1 = PaymentService::getLedgerSummary($order);
        $this->assertEquals(100.00, $ledger1->net_paid);
        $this->assertTrue($ledger1->is_fully_paid);
        $this->assertEquals('PAID', $ledger1->status);

        // Refund $30
        PaymentService::processRefund($order, 30.00, 'Partial return');

        $ledger2 = PaymentService::getLedgerSummary($order);
        $this->assertEquals(70.00, $ledger2->net_paid);
        $this->assertFalse($ledger2->is_fully_paid);
        $this->assertEquals('PARTIALLY_REFUNDED', $ledger2->status);

        // Refund remaining $70
        PaymentService::processRefund($order, 70.00, 'Remaining refund');

        $ledger3 = PaymentService::getLedgerSummary($order);
        $this->assertEquals(0.00, $ledger3->net_paid);
        $this->assertFalse($ledger3->is_fully_paid);
        $this->assertEquals('REFUNDED', $ledger3->status);
    }

    public function test_order_with_unpaid_ledger_cannot_be_fulfilled_even_if_status_flag_is_paid()
    {
        $user = User::factory()->create();
        // Malicious or desynchronized order where mutable column is 'paid' but no ledger transactions exist
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-SPOOF-001',
            'subtotal' => 150.00,
            'total_amount' => 150.00,
            'status' => 'processing',
            'payment_status' => 'paid', // Fake projection
        ]);

        $ledger = PaymentService::getLedgerSummary($order);
        $this->assertFalse($ledger->is_fully_paid);
        $this->assertEquals(0.00, $ledger->net_paid);
    }
}
