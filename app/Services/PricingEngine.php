<?php

namespace App\Services;

use App\Models\Setting;

class PricingEngine
{
    public static function calculateSellingPrice(float $supplierPrice, ?float $customMarkup = null): float
    {
        $markup = $customMarkup ?? (float) Setting::get('default_markup', 2.0);
        $markup = max(1.0, $markup);

        $sellingPrice = $supplierPrice * $markup;
        // Standard e-commerce .99 charm pricing
        return round($sellingPrice, 2);
    }

    public static function validateProfitMargin(float $sellingPrice, float $supplierCost, float $estimatedShipping = 0.0): array
    {
        $totalCost = $supplierCost + $estimatedShipping;
        $grossMargin = round($sellingPrice - $totalCost, 2);
        $marginPercentage = $sellingPrice > 0 ? round(($grossMargin / $sellingPrice) * 100, 1) : 0;

        return [
            'selling_price' => $sellingPrice,
            'total_cost' => $totalCost,
            'gross_margin' => $grossMargin,
            'margin_percentage' => $marginPercentage,
            'is_profitable' => $grossMargin > 0,
        ];
    }
}
