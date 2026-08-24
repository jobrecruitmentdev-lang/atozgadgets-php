<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function createOrder(float $amount, string $orderReference, string $currency = 'USD'): array;
    public function captureOrder(string $providerOrderId): array;
    public function verifyWebhookSignature(array $headers, string $rawBody): bool;
}
