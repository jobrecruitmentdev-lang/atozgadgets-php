<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the order associated with this shipment.
     */
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the fulfillment associated with this shipment.
     */
    public function fulfillment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Fulfillment::class, 'fulfillment_id');
    }

    /**
     * Get the white-labeled carrier assigned to this shipment.
     */
    public function carrier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShipmentCarrier::class, 'carrier_id');
    }
}
