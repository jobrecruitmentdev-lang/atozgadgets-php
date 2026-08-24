<?php

namespace App\Services\Fulfillment;

class FulfillmentResult
{
    public function __construct(
        public bool $success,
        public ?string $externalOrderId = null,
        public ?float $cost = 0.00,
        public ?float $shippingFee = 0.00,
        public ?string $trackingNumber = null,
        public ?string $carrier = null,
        public ?string $errorMessage = null,
        public array $rawPayload = []
    ) {}

    public static function success(string $externalOrderId, float $cost = 0.0, float $shippingFee = 0.0, ?string $trackingNumber = null, ?string $carrier = null, array $rawPayload = []): self
    {
        return new self(
            success: true,
            externalOrderId: $externalOrderId,
            cost: $cost,
            shippingFee: $shippingFee,
            trackingNumber: $trackingNumber,
            carrier: $carrier,
            rawPayload: $rawPayload
        );
    }

    public static function failure(string $errorMessage, array $rawPayload = []): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            rawPayload: $rawPayload
        );
    }
}
