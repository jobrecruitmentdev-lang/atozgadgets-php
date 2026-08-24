<?php

namespace App\Services\Shipping;

use App\Models\Setting;
use App\Services\Cj\CjAddressNormalizer;

class ShippingService
{
    public static function calculateShipping(float $subtotal, array $address = []): float
    {
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 50.00);

        if ($subtotal >= $freeShippingThreshold && $subtotal > 0) {
            return 0.00;
        }

        $country = CjAddressNormalizer::normalizeCountryCode($address['country'] ?? 'US');

        // Flat rates by zone
        if (in_array($country, ['US', 'CA', 'GB', 'DE', 'FR', 'AU'])) {
            return 5.99;
        }

        return 9.99;
    }
}
