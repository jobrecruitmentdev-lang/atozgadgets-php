<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'subtotal' => (float)$this->subtotal,
            'tax_amount' => (float)$this->tax_amount,
            'shipping_charge' => (float)$this->shipping_charge,
            'total_amount' => (float)$this->total_amount,
            'currency' => $this->currency ?? 'USD',
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'items' => $this->relationLoaded('items') ? OrderItemResource::collection($this->items)->toArray($request) : [],
            'shipment' => $this->relationLoaded('shipment') && $this->shipment ? (new ShipmentResource($this->shipment))->toArray($request) : null,
            'shipping_address' => $this->whenLoaded('orderAddress', function () {
                return [
                    'name' => trim(($this->orderAddress->first_name ?? '') . ' ' . ($this->orderAddress->last_name ?? '')),
                    'address_line1' => $this->orderAddress->address_line1,
                    'address_line2' => $this->orderAddress->address_line2,
                    'city' => $this->orderAddress->city,
                    'state' => $this->orderAddress->state,
                    'country' => $this->orderAddress->country,
                    'postal_code' => $this->orderAddress->postal_code,
                ];
            }),
        ];
    }
}
