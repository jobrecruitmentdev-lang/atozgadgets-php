<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentCarrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'internal_code',
        'customer_name',
        'tracking_url_template',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function shipments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shipment::class, 'carrier_id');
    }
}
