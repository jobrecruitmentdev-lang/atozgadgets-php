<?php

namespace App\Services\Shipping;

use App\Models\Setting;
use App\Services\Cj\CjAddressNormalizer;

class ShippingService
{
    public static function calculateShipping(float $subtotal, array $address = [], array $cart = []): float
    {
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 99999.00);

        if ($freeShippingThreshold > 0 && $subtotal >= $freeShippingThreshold && $subtotal > 0) {
            return 0.00;
        }

        $country = CjAddressNormalizer::normalizeCountryCode($address['country'] ?? 'US');

        if (empty($cart)) {
            $cart = session()->get('cart', []);
        }

        if (!empty($cart)) {
            try {
                $eligibility = CjShippingEligibilityService::checkEligibility($cart, $country);
                if (!empty($eligibility['shipping_fee']) && (float)$eligibility['shipping_fee'] > 0) {
                    return (float)$eligibility['shipping_fee'];
                }
            } catch (\Throwable $e) {
                // Fallback to zone rate below
            }
        }

        // Flat rates by zone fallback
        if (in_array($country, ['US', 'CA', 'GB', 'DE', 'FR', 'AU'])) {
            return ($country === 'US') ? 5.07 : 5.99;
        }

        return 8.99;
    }
}
