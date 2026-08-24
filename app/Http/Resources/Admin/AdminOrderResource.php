<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
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
            'customer' => [
                'id' => $this->user_id,
                'name' => trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
                'email' => $this->user->email ?? null,
                'mobile' => $this->user->mobile ?? null,
            ],
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Product',
                        'sku' => $item->product->sku ?? null,
                        'fulfillment_type' => $item->product->fulfillment_type ?? 'own',
                        'supplier_product_id' => $item->product->cjProduct->cj_product_id ?? null,
                        'quantity' => (int)$item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'total_price' => (float)$item->total_price,
                    ];
                });
            }),
            'supply_chain' => $this->whenLoaded('supplierOrder', function () {
                return new SupplyChainResource($this->supplierOrder);
            }),
            'shipment' => $this->whenLoaded('shipment'),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
