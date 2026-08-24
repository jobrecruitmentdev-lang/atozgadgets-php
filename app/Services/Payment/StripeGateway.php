<?php

namespace App\Services\Payment;

class StripeGateway implements PaymentGatewayInterface
{
    public function createOrder(float $amount, string $orderReference, string $currency = 'USD'): array
    {
        return [
            'id' => 'pi_mock_' . uniqid(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'requires_payment_method',
        ];
    }

    public function captureOrder(string $providerOrderId): array
    {
        return [
            'id' => $providerOrderId,
            'status' => 'succeeded',
        ];
    }

    public function verifyWebhookSignature(array $headers, string $rawBody): bool
    {
        return true;
    }
}
