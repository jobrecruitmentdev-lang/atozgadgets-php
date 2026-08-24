<?php

namespace App\Services\Checkout;

use App\Models\Product;
use App\Models\CheckoutSession;
use App\Services\Shipping\ShippingService;
use App\Services\Tax\TaxService;
use Illuminate\Support\Str;

class CheckoutService
{
    public static function createSession(?int $userId, array $rawCart, array $address = []): CheckoutSession
    {
        $lineItems = [];
        $subtotal = 0.0;

        foreach ($rawCart as $key => $item) {
            $productId = is_numeric($key) ? (int)$key : ($item['product_id'] ?? null);
            $product = $productId ? Product::find($productId) : null;

            $unitPrice = $product ? (float)$product->price : (float)($item['price'] ?? 0.0);
            $qty = max(1, (int)($item['quantity'] ?? 1));
            $itemTotal = round($unitPrice * $qty, 2);

            $subtotal += $itemTotal;

            $lineItems[] = [
                'product_id' => $product ? $product->id : $productId,
                'name' => $product ? $product->name : ($item['name'] ?? 'Gadget Item'),
                'sku' => $product ? $product->sku : ($item['sku'] ?? 'SKU-ITEM'),
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'total_price' => $itemTotal,
                'fulfillment_type' => $product ? $product->fulfillment_type : 'cj',
            ];
        }

        $shipping = ShippingService::calculateShipping($subtotal, $address);
        $tax = TaxService::calculateTax($subtotal, $address);
        $grandTotal = round($subtotal + $shipping + $tax, 2);

        return CheckoutSession::create([
            'session_token' => 'cs_' . Str::random(32),
            'user_id' => $userId,
            'currency' => 'USD',
            'country' => $address['country'] ?? 'US',
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'discount' => 0.00,
            'shipping' => $shipping,
            'tax' => $tax,
            'grand_total' => $grandTotal,
            'status' => 'active',
            'expires_at' => now()->addHours(2),
        ]);
    }
}
