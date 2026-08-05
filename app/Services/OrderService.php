<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Role;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Transaction for placing an order (Handles guest shadow accounts, address creation, and cart clearing)
     */
    public static function placeOrder($userId, $payload)
    {
        return DB::transaction(function () use ($userId, $payload) {
            $finalUserId = $userId;
            $addressId = $payload['address_id'] ?? null;

            // 1. Handle Guest / Shadow Account Creation
            if (!$finalUserId && !empty($payload['guest'])) {
                $guestEmail = $payload['guest']['email'] ?? null;
                if (!$guestEmail) {
                    throw new \Exception("Guest email is required for checkout.");
                }

                $shadowUser = User::where('email', $guestEmail)->first();
                
                if (!$shadowUser) {
                    $customerRole = Role::where('role_name', 'Customer')->first();
                    if (!$customerRole) {
                        throw new \Exception("Customer role not found in system");
                    }

                    $shadowUser = User::create([
                        'email' => $guestEmail,
                        'first_name' => $payload['guest']['firstName'] ?? 'Guest',
                        'last_name' => $payload['guest']['lastName'] ?? '',
                        'mobile' => $payload['guest']['phone'] ?? (string)time(),
                        'password_hash' => 'shadow_account_no_password',
                        'role_id' => $customerRole->id,
                        'is_active' => false // Shadow accounts are inactive until they verify
                    ]);
                }
                $finalUserId = $shadowUser->id;
            }

            if (!$finalUserId) {
                throw new \Exception("Could not determine user for checkout");
            }

            // 2. Handle Address Creation
            if (!$addressId && !empty($payload['address'])) {
                $newAddress = UserAddress::create([
                    'user_id' => $finalUserId,
                    'address_line1' => $payload['address']['address_line1'] ?? '',
                    'address_line2' => $payload['address']['address_line2'] ?? null,
                    'city' => $payload['address']['city'] ?? '',
                    'state' => $payload['address']['state'] ?? '',
                    'postal_code' => $payload['address']['postal_code'] ?? '',
                    'country' => $payload['address']['country'] ?? '',
                ]);
                $addressId = $newAddress->id;
            }

            if (!$addressId) {
                throw new \Exception("No shipping address provided");
            }

            // 3. Resolve Items
            $itemsToOrder = [];
            $cartIdToClear = null;

            if (!empty($payload['items'])) {
                foreach ($payload['items'] as $i) {
                    $itemsToOrder[] = [
                        'product_id' => $i['product_id'],
                        'quantity' => $i['quantity'],
                        'price' => (float)$i['price'],
                        'product_name' => $i['name'] ?? 'Product',
                    ];
                }
            } else {
                $cart = Cart::where('user_id', $finalUserId)->with('items.product')->first();
                if (!$cart || $cart->items->isEmpty()) {
                    throw new \Exception("Cart is empty");
                }
                
                foreach ($cart->items as $i) {
                    $itemsToOrder[] = [
                        'product_id' => $i->product_id,
                        'quantity' => $i->quantity,
                        'price' => (float)$i->price,
                        'product_name' => $i->product ? $i->product->name : 'Product',
                    ];
                }
                $cartIdToClear = $cart->id;
            }

            // 4. Calculate Subtotal
            $subtotal = 0;
            foreach ($itemsToOrder as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            // 5. Create Order
            $orderNumber = 'ORD-' . time();

            $order = Order::create([
                'user_id' => $finalUserId,
                'address_id' => $addressId,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'total_amount' => $subtotal, // Ignoring shipping/coupons for now
                'coupon_id' => $payload['coupon_id'] ?? null,
                'order_status' => 'pending',
            ]);

            // 6. Create Order Items
            foreach ($itemsToOrder as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // 7. Create Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'new_status' => 'pending',
                'changed_by' => $userId ?? null,
            ]);

            // 8. Clear Cart if it was a cart checkout
            if ($cartIdToClear) {
                CartItem::where('cart_id', $cartIdToClear)->delete();
            }

            return $order;
        });
    }

    /**
     * Update order status with history tracking
     */
    public static function updateStatus($id, $status, $changedBy)
    {
        return DB::transaction(function () use ($id, $status, $changedBy) {
            $order = Order::find($id);
            if (!$order) {
                throw new \Exception("Order not found");
            }

            $oldStatus = $order->order_status;

            $order->update(['order_status' => $status]);

            OrderStatusHistory::create([
                'order_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'changed_by' => $changedBy,
            ]);

            return $order;
        });
    }
}
