<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float)$this->price,
            'discount_price' => $this->has_active_discount ? (float)$this->discount_price : null,
            'effective_price' => (float)$this->effective_price,
            'has_active_discount' => (bool)$this->has_active_discount,
            'stock_quantity' => (int)($this->stock_quantity ?? 0),
            'in_stock' => ($this->stock_quantity ?? 0) > 0,
            'thumbnail_image' => $this->thumbnail_image,
            'images' => is_array($this->images) ? $this->images : json_decode($this->images ?? '[]', true),
            'rating' => (float)($this->rating ?? 5.0),
            'rating_count' => (int)($this->rating_count ?? 0),
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name ?? $variant->variant_name,
                        'sku' => $variant->sku,
                        'price' => (float)\App\Services\Catalog\PricingService::resolveCustomerPrice($this->resource, $variant),
                        'selling_price' => !is_null($variant->selling_price) ? (float)$variant->selling_price : null,
                        'stock_quantity' => (int)($variant->stock_quantity ?? 0),
                    ];
                });
            }),
        ];
    }
}
