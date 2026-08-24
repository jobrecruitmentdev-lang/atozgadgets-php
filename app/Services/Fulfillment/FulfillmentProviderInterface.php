<?php

namespace App\Services\Fulfillment;

use App\Models\Fulfillment;
use App\Models\SupplierProduct;

interface FulfillmentProviderInterface
{
    /**
     * Submit fulfillment order to upstream provider.
     */
    public function submit(Fulfillment $fulfillment): FulfillmentResult;

    /**
     * Reconcile: Query external supplier system to find if order was already created.
     * Prevents duplicate orders when previous submit encountered network timeout.
     */
    public function findExistingOrder(Fulfillment $fulfillment): ExternalOrderLookupResult;

    /**
     * Cancel an existing fulfillment with upstream provider.
     */
    public function cancel(Fulfillment $fulfillment): CancellationResult;

    /**
     * Fetch latest courier tracking and logistics details.
     */
    public function getTracking(Fulfillment $fulfillment): TrackingResult;

    /**
     * Check real-time inventory level from upstream provider.
     */
    public function getInventory(SupplierProduct $product): InventoryResult;
}
