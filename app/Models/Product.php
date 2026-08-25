<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Inventory\InventoryService;
use App\Services\Catalog\ProductContentService;
use App\Services\Catalog\PricingService;

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

    /**
     * Prevent internal supplier IDs from leaking in public JSON responses
     */
    protected $hidden = [
        'cj_product_id',
    ];

    /**
     * Local query scope for public published catalog items
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

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

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function approvedReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductReview::class, 'product_id')->where('status', 'approved')->latest();
    }

    public function media(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductMedia::class, 'product_id')->orderBy('sort_order', 'asc');
    }

    public function specifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductSpecification::class, 'product_id')->orderBy('sort_order', 'asc');
    }

    /**
     * White-label customer thumbnail URL (proxied if external supplier URL)
     */
    public function getCustomerThumbnailAttribute(): string
    {
        if (!empty($this->thumbnail_image)) {
            if (str_starts_with($this->thumbnail_image, 'http')) {
                return route('media.product.thumbnail', ['product' => $this->id]);
            }
            return asset($this->thumbnail_image);
        }
        return asset('favicon.png');
    }

    /**
     * Customer-safe Merchant SKU (guarantees no raw supplier ID or "CJ" leakage)
     */
    public function getMerchantSkuAttribute(): string
    {
        $rawSku = (string)($this->sku ?? '');
        if (str_starts_with(strtoupper($rawSku), 'CJ') || empty($rawSku)) {
            $catName = $this->category->name ?? 'GDT';
            return ProductContentService::generateMerchantSku($catName, $this->id);
        }
        return $rawSku;
    }

    /**
     * Dynamic inventory availability factoring in quantity and sync freshness
     */
    public function getAvailabilityAttribute(): array
    {
        return InventoryService::getAvailability($this);
    }

    /**
     * Customer-facing shipping promise string
     */
    public function getShippingPromiseAttribute(): string
    {
        return 'Standard Delivery: 7–15 Business Days';
    }

    public function getEffectivePriceAttribute(): float
    {
        return PricingService::resolveCustomerPrice($this);
    }

    public function getHasActiveDiscountAttribute(): bool
    {
        return PricingService::hasActiveDiscount($this);
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->where('status', 'approved')->avg('rating');
        return $avg ? round((float)$avg, 1) : 5.0;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->where('status', 'approved')->count();
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