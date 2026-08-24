<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CjVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'cj_product_id',
        'cj_variant_id',
        'cj_variant_sku',
        'variant_name',
        'option1_name',
        'option1_value',
        'option2_name',
        'option2_value',
        'cost_price',
        'inventory_quantity',
        'status',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'cost_price' => 'decimal:2',
        'inventory_quantity' => 'integer',
    ];

    public function cjProduct()
    {
        return $this->belongsTo(CjProduct::class, 'cj_product_id', 'cj_product_id');
    }
}
