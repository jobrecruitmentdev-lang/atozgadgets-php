<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CjProduct extends Model
{
    use HasFactory;
    protected $table = 'cj_products';
    protected $guarded = ['id'];

    /**
     * Get the local store product associated with this CJ item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'internal_product_id');
    }
}
