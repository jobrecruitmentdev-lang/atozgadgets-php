<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\SupplierOrder;

interface SupplierAdapterInterface
{
    /**
     * Submit an order to the supplier for fulfillment.
     */
    public function fulfill(Order $order): SupplierOrder;

    /**
     * Cancel an existing supplier order.
     */
    public function cancel(string $externalOrderId): bool;

    /**
     * Fetch the latest shipment tracking status from the supplier.
     */
    public function track(string $externalOrderId): ?array;
}
