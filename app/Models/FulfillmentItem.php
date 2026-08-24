<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulfillmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fulfillment_id',
        'order_item_id',
        'quantity',
        'status',
    ];

    public function fulfillment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Fulfillment::class);
    }

    public function orderItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
