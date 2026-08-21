<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CjOrder extends Model
{
    use HasFactory;
    protected $table = 'cj_orders';
    protected $guarded = ['id'];

    /**
     * Get the internal store order associated with this CJ order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'internal_order_id');
    }
}
