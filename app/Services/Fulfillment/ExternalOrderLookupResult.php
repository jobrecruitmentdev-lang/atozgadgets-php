<?php

namespace App\Services\Fulfillment;

class ExternalOrderLookupResult
{
    public function __construct(
        public bool $found,
        public ?string $externalOrderId = null,
        public ?string $status = null,
        public ?string $trackingNumber = null,
        public ?string $carrierName = null,
        public ?float $cost = 0.00,
        public ?float $shippingFee = 0.00,
        public array $rawPayload = []
    ) {}

    public static function found(
        string $externalOrderId,
        ?string $status = 'SUBMITTED',
        ?string $trackingNumber = null,
        ?string $carrierName = 'Standard Delivery',
        float $cost = 0.00,
        float $shippingFee = 0.00,
        array $rawPayload = []
    ): self {
        return new self(
            found: true,
            externalOrderId: $externalOrderId,
            status: $status,
            trackingNumber: $trackingNumber,
            carrierName: $carrierName,
            cost: $cost,
            shippingFee: $shippingFee,
            rawPayload: $rawPayload
        );
    }

    public static function notFound(array $rawPayload = []): self
    {
        return new self(
            found: false,
            rawPayload: $rawPayload
        );
    }
}
