<?php

namespace App\Services\Tax;

use App\Services\Cj\CjAddressNormalizer;

class TaxService
{
    public static function calculateTax(float $subtotal, array $address = []): float
    {
        $country = CjAddressNormalizer::normalizeCountryCode($address['country'] ?? 'US');

        // Dynamic tax rates: USA (state nexus standard 6.5%), UK (20% VAT standard), EU (19%), Rest 0% (DDU)
        $taxRates = [
            'US' => 0.065,
            'GB' => 0.20,
            'DE' => 0.19,
            'FR' => 0.20,
            'CA' => 0.05,
            'AU' => 0.10,
        ];

        $rate = $taxRates[$country] ?? 0.00;
        return round($subtotal * $rate, 2);
    }
}
