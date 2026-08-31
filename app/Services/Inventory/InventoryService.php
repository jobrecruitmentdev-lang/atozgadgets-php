<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\InventoryReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InventoryService
{
    public const STATUS_IN_STOCK = 'in_stock';
    public const STATUS_LOW_STOCK = 'low_stock';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';
    public const STATUS_CONFIRMING = 'confirming';

    /**
     * Compute dynamic inventory availability status factoring in quantity and sync freshness.
     */
    public static function getAvailability(Product $product, int $staleThresholdHours = 48): array
    {
        $isSupplierLinked = ($product->fulfillment_type === 'cj' || $product->cjProduct()->exists());
        $isStale = false;

        if ($isSupplierLinked) {
            $updatedAt = $product->updated_at ? Carbon::parse($product->updated_at) : null;
            if (!$updatedAt || $updatedAt->diffInHours(now()) > $staleThresholdHours) {
                $isStale = true;
            }
        }

        $stock = (int)($product->stock_quantity ?? 0);

        if ($isStale && $stock > 0) {
            return [
                'status' => self::STATUS_CONFIRMING,
                'label' => 'Availability being confirmed',
                'badge_class' => 'pill-confirming',
                'color' => '#3b82f6',
                'icon' => 'clock',
                'is_purchasable' => true,
                'stock' => $stock,
            ];
        }

        if ($stock <= 0) {
            return [
                'status' => self::STATUS_OUT_OF_STOCK,
                'label' => 'Out of Stock',
                'badge_class' => 'pill-outofstock',
                'color' => '#ef4444',
                'icon' => 'x-circle',
                'is_purchasable' => false,
                'stock' => 0,
            ];
        }

        if ($stock <= 5) {
            return [
                'status' => self::STATUS_LOW_STOCK,
                'label' => "Low Stock (Only {$stock} left)",
                'badge_class' => 'pill-lowstock',
                'color' => '#f59e0b',
                'icon' => 'alert-circle',
                'is_purchasable' => true,
                'stock' => $stock,
            ];
        }

        return [
            'status' => self::STATUS_IN_STOCK,
            'label' => 'In Stock',
            'badge_class' => 'pill-instock',
            'color' => '#10b981',
            'icon' => 'check-circle',
            'is_purchasable' => true,
            'stock' => $stock,
        ];
    }

    /**
     * Atomically reserve inventory during checkout using pessimistic row locks.
     */
    public static function reserve(int $orderId, array $items, int $ttlMinutes = 15): bool
    {
        return DB::transaction(function () use ($orderId, $items, $ttlMinutes) {
            // Auto-expire stale reservations first
            InventoryReservation::where('status', 'RESERVED')
                ->where('expires_at', '<=', now())
                ->update(['status' => 'EXPIRED']);

            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $qty = (int)($item['quantity'] ?? 1);

                if (!$productId) continue;

                // 1. Atomic Row Lock on Product
                $product = Product::lockForUpdate()->find($productId);
                if (!$product) {
                    return false;
                }

                // Auto-heal dropshipping/active products if initial stock was uninitialized
                if ((int)$product->stock_quantity <= 0 && $product->status === 'active' && ($product->fulfillment_type === 'cj' || $product->cjProduct()->exists())) {
                    $product->update(['stock_quantity' => 100]);
                    $product->stock_quantity = 100;
                }

                // 2. Sum Active Reservations within same locked transaction
                $activeReserved = (int) InventoryReservation::where('product_id', $productId)
                    ->where('status', 'RESERVED')
                    ->where('expires_at', '>', now())
                    ->where('order_id', '!=', $orderId)
                    ->sum('quantity');

                $availableToSell = max(0, (int)$product->stock_quantity - $activeReserved);

                if ($availableToSell < $qty) {
                    return false; // Insufficient sellable inventory
                }

                // 3. Create Reservation
                InventoryReservation::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $qty,
                    'status' => 'RESERVED',
                    'expires_at' => now()->addMinutes($ttlMinutes),
                ]);
            }
            return true;
        });
    }

    /**
     * Confirm reservations on successful payment capture and decrement physical stock.
     */
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

    /**
     * Release reservations on cancellation or timeout.
     */
    public static function release(int $orderId): void
    {
        InventoryReservation::where('order_id', $orderId)
            ->where('status', 'RESERVED')
            ->update(['status' => 'RELEASED']);
    }
}