<?php

namespace App\Services\Fulfillment;

class CancellationResult
{
    public function __construct(
        public bool $success,
        public ?string $errorMessage = null
    ) {}

    public static function success(): self
    {
        return new self(success: true);
    }

    public static function failure(string $errorMessage): self
    {
        return new self(success: false, errorMessage: $errorMessage);
    }
}
