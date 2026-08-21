<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 
        'short_description', 'description', 'sku', 'barcode', 'price', 
        'discount_price', 'tax_percentage', 'stock_quantity', 'weight', 
        'length', 'width', 'height', 'thumbnail_image', 'handle', 'title', 
        'option1_name', 'option2_name', 'option3_name', 'hs_code', 
        'country_of_origin', 'location', 'bin_name', 'incoming', 
        'unavailable', 'committed', 'available', 'onhand_old', 'onhand_new', 
        'status', 'is_featured', 'is_active', 'created_by', 'fulfillment_type'
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function cjProduct(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CjProduct::class, 'internal_product_id');
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    protected static function booted()
    {
        static::deleting(function ($product) {
            $tablesToDelete = [
                'cart_items', 'featured_products', 'inventory', 'offer_products',
                'product_attributes', 'product_images', 'product_reviews', 'product_variants',
                'stock_movements', 'user_behaviour', 'wishlist_items', 'ratings'
            ];
            
            foreach ($tablesToDelete as $table) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table) && \Illuminate\Support\Facades\Schema::hasColumn($table, 'product_id')) {
                    \Illuminate\Support\Facades\DB::table($table)->where('product_id', $product->id)->delete();
                }
            }
            
            if (\Illuminate\Support\Facades\Schema::hasTable('cj_products')) {
                \Illuminate\Support\Facades\DB::table('cj_products')->where('internal_product_id', $product->id)->delete();
            }
            
            if (\Illuminate\Support\Facades\Schema::hasTable('order_items')) {
                \Illuminate\Support\Facades\DB::table('order_items')->where('product_id', $product->id)->update(['product_id' => null]);
            }
        });
    }
}
