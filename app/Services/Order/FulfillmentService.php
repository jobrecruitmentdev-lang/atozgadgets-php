<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\SupplierOrder;
use App\Services\Cj\CjSupplierAdapter;
use Illuminate\Support\Facades\Log;

class FulfillmentService
{
    /**
     * Resolve the supplier adapter for the given provider key.
     */
    public static function resolveAdapter(string $provider = 'cj'): SupplierAdapterInterface
    {
        return match (strtolower($provider)) {
            'cj' => new CjSupplierAdapter(),
            default => new CjSupplierAdapter(),
        };
    }

    /**
     * Fulfill an order with the appropriate supplier adapter.
     */
    public static function fulfill(Order $order, string $provider = 'cj'): SupplierOrder
    {
        $adapter = self::resolveAdapter($provider);
        return $adapter->fulfill($order);
    }

    /**
     * Cancel an existing fulfillment with the supplier adapter.
     */
    public static function cancel(string $externalOrderId, string $provider = 'cj'): bool
    {
        $adapter = self::resolveAdapter($provider);
        return $adapter->cancel($externalOrderId);
    }

    /**
     * Track a shipment from the supplier adapter.
     */
    public static function track(string $externalOrderId, string $provider = 'cj'): ?array
    {
        $adapter = self::resolveAdapter($provider);
        return $adapter->track($externalOrderId);
    }
}
