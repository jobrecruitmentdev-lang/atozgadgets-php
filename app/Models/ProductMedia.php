<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'variant_id',
        'type',
        'url',
        'storage_path',
        'alt_text',
        'sort_order',
        'is_primary',
        'mime_type'
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the customer-safe public URL routed via the local media proxy.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('media.product.image', ['product' => $this->product_id, 'mediaId' => $this->id]);
    }
}
