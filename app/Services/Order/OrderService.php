<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CheckoutSession;
use App\Models\OutboxEvent;
use App\Services\Inventory\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public static function createPendingOrderFromSession(CheckoutSession $session, array $address = []): Order
    {
        return DB::transaction(function () use ($session, $address) {
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            $order = Order::create([
                'user_id' => $session->user_id,
                'order_number' => $orderNumber,
                'subtotal' => $session->subtotal ?? 0.00,
                'net_amount' => $session->subtotal ?? 0.00,
                'tax_amount' => $session->tax ?? 0.00,
                'shipping_charge' => $session->shipping ?? 0.00,
                'shipping_cost' => $session->shipping ?? 0.00,
                'total_amount' => $session->grand_total ?? 0.00,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address' => json_encode($address),
            ]);

            foreach ($session->line_items as $item) {
                $product = Product::find($item['product_id']);
                $variant = !empty($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;

                // Exact Variant Wholesale Cost and SKU Snapshot
                $supplierCost = $variant ? (float)$variant->cost_price : (float)($product->cost_price ?? 0.00);
                $sku = $variant?->sku ?? ($product?->merchant_sku ?? 'AZG-GDT');
                $variantName = $variant ? $variant->name : null;
                $cjVariant = $variant?->cjVariant;
                $cjProduct = $product?->cjProduct;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'merchant_sku_snapshot' => $sku,
                    'product_name_snapshot' => $product?->name ?? 'AtoZ Gadget',
                    'variant_name_snapshot' => $variantName,
                    'cj_product_id' => $cjProduct?->cj_product_id,
                    'cj_variant_id' => $variant?->cj_variant_id ?: $cjProduct?->cj_variant_id,
                    'cj_variant_sku' => $cjVariant?->cj_variant_sku ?: $cjProduct?->cj_variant_sku,
                    'supplier_cost_snapshot' => $supplierCost,
                    'freight_cost_snapshot' => 0.00,
                    'contribution_margin_snapshot' => (float)$item['unit_price'] - $supplierCost,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'status' => 'active',
                ]);
            }

            // Create Immutable Address Snapshot
            \App\Models\OrderAddress::create([
                'order_id' => $order->id,
                'type' => 'shipping',
                'first_name' => $address['first_name'] ?? ($address['name'] ?? 'Customer'),
                'last_name' => $address['last_name'] ?? '',
                'email' => $address['email'] ?? null,
                'phone' => $address['phone'] ?? null,
                'address_line1' => $address['address_line1'] ?? ($address['address1'] ?? ($address['address_line_1'] ?? '')),
                'address_line2' => $address['address_line2'] ?? ($address['address2'] ?? ($address['address_line_2'] ?? null)),
                'city' => $address['city'] ?? '',
                'state' => $address['state'] ?? ($address['province'] ?? ''),
                'country' => $address['country'] ?? 'US',
                'postal_code' => $address['postal_code'] ?? ($address['zip'] ?? ''),
            ]);

            // Reserve inventory with pessimistic row lock
            $reserved = InventoryService::reserve($order->id, $session->line_items);
            if (!$reserved) {
                throw new \Exception('One or more items in your cart are currently out of stock.');
            }

            $session->update(['status' => 'converted']);

            return $order;
        });
    }

    public static function markAsPaid(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'processing',
                'payment_status' => 'paid',
            ]);

            // Confirm inventory
            InventoryService::confirm($order->id);

            // Create Outbox Event for async fulfillment
            OutboxEvent::create([
                'event_name' => 'ORDER_PAID',
                'aggregate_type' => 'Order',
                'aggregate_id' => $order->id,
                'payload' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                ],
                'status' => 'PENDING',
                'attempts' => 0,
            ]);
        });
    }
}
