<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
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
                'subtotal' => $session->subtotal,
                'tax_amount' => $session->tax,
                'shipping_charge' => $session->shipping,
                'total_amount' => $session->grand_total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address' => json_encode($address),
            ]);

            foreach ($session->line_items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
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

            // Reserve inventory
            InventoryService::reserve($order->id, $session->line_items);

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
