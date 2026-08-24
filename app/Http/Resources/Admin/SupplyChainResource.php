<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplyChainResource extends JsonResource
{
    public function toArray($request)
    {
        $productCost = (float)($this->product_cost ?? ($this->order_amount ?? 0.00));
        $shippingCost = (float)($this->shipping_cost ?? ($this->shipping_fee ?? 0.00));
        $totalCost = (float)($this->total_cost ?? ($productCost + $shippingCost));
        $sellingPrice = (float)($this->order->total_amount ?? 0.00);
        $margin = max(0, $sellingPrice - $totalCost);

        return [
            'provider' => strtoupper($this->supplier ?? 'CJ'),
            'supplier_order_id' => $this->external_order_id ?? ($this->cj_order_id ?? null),
            'status' => $this->status ?? 'submitted',
            'product_cost' => $productCost,
            'shipping_cost' => $shippingCost,
            'total_cost' => $totalCost,
            'selling_price' => $sellingPrice,
            'estimated_margin' => $margin,
            'carrier_name' => $this->carrier_name ?? ($this->logistic_name ?? 'CJPacket / Standard Direct'),
            'tracking_number' => $this->tracking_number ?? null,
            'submitted_at' => $this->submitted_at ?? $this->created_at,
            'failure_message' => $this->failure_message ?? null,
        ];
    }
}
