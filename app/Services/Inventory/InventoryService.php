<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\InventoryReservation;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public static function reserve(int $orderId, array $items, int $ttlMinutes = 30): bool
    {
        return DB::transaction(function () use ($orderId, $items, $ttlMinutes) {
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $quantity = $item['quantity'] ?? 1;

                if (!$productId) continue;

                $product = Product::lockForUpdate()->find($productId);
                if (!$product) continue;

                // Create reservation record
                InventoryReservation::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'status' => 'RESERVED',
                    'expires_at' => now()->addMinutes($ttlMinutes),
                ]);
            }
            return true;
        });
    }

    public static function confirm(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $reservations = InventoryReservation::where('order_id', $orderId)
                ->where('status', 'RESERVED')
                ->get();

            foreach ($reservations as $res) {
                $product = Product::lockForUpdate()->find($res->product_id);
                if ($product && $product->stock_quantity >= $res->quantity) {
                    $product->decrement('stock_quantity', $res->quantity);
                }
                $res->update(['status' => 'CONFIRMED']);
            }
        });
    }

    public static function release(int $orderId): void
    {
        InventoryReservation::where('order_id', $orderId)
            ->where('status', 'RESERVED')
            ->update(['status' => 'RELEASED']);
    }
}
