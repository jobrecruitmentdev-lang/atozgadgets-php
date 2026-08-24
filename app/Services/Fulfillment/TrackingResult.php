<?php

namespace App\Services\Fulfillment;

class TrackingResult
{
    public function __construct(
        public bool $success,
        public ?string $status = null,
        public ?string $trackingNumber = null,
        public ?string $carrierName = null,
        public array $events = [],
        public ?string $errorMessage = null
    ) {}

    public static function success(string $status, ?string $trackingNumber = null, ?string $carrierName = null, array $events = []): self
    {
        return new self(
            success: true,
            status: $status,
            trackingNumber: $trackingNumber,
            carrierName: $carrierName,
            events: $events
        );
    }

    public static function failure(string $errorMessage): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage
        );
    }
}
