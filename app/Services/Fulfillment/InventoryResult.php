<?php

namespace App\Services\Fulfillment;

class InventoryResult
{
    public function __construct(
        public bool $success,
        public int $quantity = 0,
        public string $status = 'available',
        public ?string $errorMessage = null
    ) {}

    public static function success(int $quantity, string $status = 'available'): self
    {
        return new self(
            success: true,
            quantity: $quantity,
            status: $status
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
