<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cj_variant_id',
        'sku',
        'name',
        'option1_name',
        'option1_value',
        'option2_name',
        'option2_value',
        'selling_price',
        'cost_price',
        'stock_quantity',
        'status',
        'image_url',
    ];

    /**
     * Prevent internal supplier variant IDs from leaking in public JSON responses
     */
    protected $hidden = [
        'cj_variant_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cjVariant()
    {
        return $this->belongsTo(CjVariant::class, 'cj_variant_id', 'cj_variant_id');
    }
}
