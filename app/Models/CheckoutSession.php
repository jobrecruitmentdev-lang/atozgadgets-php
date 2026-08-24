<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutSession extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'line_items' => 'array',
        'subtotal' => 'float',
        'discount' => 'float',
        'shipping' => 'float',
        'tax' => 'float',
        'grand_total' => 'float',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
