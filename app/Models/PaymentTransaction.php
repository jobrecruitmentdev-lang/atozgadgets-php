<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = [
        'amount' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
