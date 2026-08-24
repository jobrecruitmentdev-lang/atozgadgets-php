<?php

namespace App\Services\Catalog;

class PricingService
{
    /**
     * Calculate retail selling price based on cost tiers and shipping allowance.
     *
     * @param float $costPrice
     * @param float|null $customMultiplier
     * @return array
     */
    public static function calculateRetailPrice(float $costPrice, ?float $customMultiplier = null): array
    {
        $cost = max(0.01, $costPrice);

        if ($customMultiplier !== null && $customMultiplier > 0) {
            $rawPrice = round($cost * $customMultiplier, 2);
            $shippingAllowance = 0.00;
            $multiplier = $customMultiplier;
            $roundedPrice = $rawPrice;
        } else {
            // Tiered dynamic pricing engine
            if ($cost < 10.00) {
                $multiplier = 2.50;
                $shippingAllowance = 3.00;
            } elseif ($cost <= 50.00) {
                $multiplier = 2.00;
                $shippingAllowance = 5.00;
            } else {
                $multiplier = 1.60;
                $shippingAllowance = 8.00;
            }

            $rawPrice = ($cost * $multiplier) + $shippingAllowance;

            // Psychological pricing: round up to nearest .99
            $roundedPrice = floor($rawPrice) + 0.99;
            if ($roundedPrice < $rawPrice) {
                $roundedPrice += 1.00;
            }
        }

        $profitMargin = $roundedPrice - $cost;
        $marginPercentage = ($profitMargin / $roundedPrice) * 100;

        return [
            'cost_price' => round($cost, 2),
            'multiplier' => $multiplier,
            'shipping_allowance' => round($shippingAllowance ?? 0, 2),
            'selling_price' => round($roundedPrice, 2),
            'estimated_profit' => round($profitMargin, 2),
            'margin_percent' => round($marginPercentage, 1),
        ];
    }
}
