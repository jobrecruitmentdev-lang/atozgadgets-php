<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierOrder extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'product_cost' => 'float',
        'shipping_cost' => 'float',
        'total_cost' => 'float',
        'submitted_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
