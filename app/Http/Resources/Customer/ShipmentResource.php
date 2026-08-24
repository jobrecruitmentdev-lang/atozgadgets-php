<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status ?? 'processing',
            'shipping_method' => 'Standard Delivery',
            'tracking_number' => $this->tracking_number,
            'tracking_url' => $this->tracking_number ? url('/orders/track?tracking=' . urlencode($this->tracking_number)) : null,
            'estimated_delivery' => $this->estimated_delivery ?? '7–12 business days',
            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,
        ];
    }
}
