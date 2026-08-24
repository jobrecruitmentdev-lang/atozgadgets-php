<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product->name ?? ($this->name ?? 'Product'),
            'product_slug' => $this->product->slug ?? null,
            'product_image' => $this->product->thumbnail_image ?? null,
            'variant_name' => $this->variant->name ?? ($this->variant_name ?? null),
            'quantity' => (int)$this->quantity,
            'unit_price' => (float)$this->unit_price,
            'total_price' => (float)$this->total_price,
        ];
    }
}
