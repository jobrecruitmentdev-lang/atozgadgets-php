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

    /**
     * Authoritative Single Source of Truth for customer-facing retail price.
     *
     * Rule:
     * 1. If variant exists and has a valid selling_price > 0 -> return variant.selling_price
     * 2. Otherwise, if discount_price > 0 AND discount_price < base price -> return discount_price
     * 3. Otherwise -> return base product.price
     *
     * @param \App\Models\Product $product
     * @param \App\Models\ProductVariant|null $variant
     * @return float
     */
    public static function resolveCustomerPrice(\App\Models\Product $product, ?\App\Models\ProductVariant $variant = null): float
    {
        // 1. Selected Variant has absolute first priority if selling_price > 0
        if ($variant && !is_null($variant->selling_price)) {
            $variantPrice = (float)$variant->selling_price;
            if ($variantPrice > 0) {
                return round($variantPrice, 2);
            }
        }

        $basePrice = !is_null($product->price) ? (float)$product->price : 0.00;
        $discountPrice = !is_null($product->discount_price) ? (float)$product->discount_price : 0.00;

        // 2. Discount price is ONLY valid if it is positive (> 0) AND strictly lower than base price
        if ($discountPrice > 0 && $discountPrice < $basePrice) {
            return round($discountPrice, 2);
        }

        // 3. Fallback to base regular price
        return round($basePrice, 2);
    }

    /**
     * Determine if a product has a valid active customer discount.
     *
     * Returns TRUE only when discount_price > 0 AND discount_price < price.
     *
     * @param \App\Models\Product $product
     * @return bool
     */
    public static function hasActiveDiscount(\App\Models\Product $product): bool
    {
        $basePrice = !is_null($product->price) ? (float)$product->price : 0.00;
        $discountPrice = !is_null($product->discount_price) ? (float)$product->discount_price : 0.00;

        return $discountPrice > 0 && $discountPrice < $basePrice;
    }
}
