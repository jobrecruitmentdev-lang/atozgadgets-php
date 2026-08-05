<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'subcategory_id', 'brand_id', 'name', 'slug', 
        'short_description', 'description', 'sku', 'barcode', 'price', 
        'discount_price', 'tax_percentage', 'stock_quantity', 'weight', 
        'length', 'width', 'height', 'thumbnail_image', 'handle', 'title', 
        'option1_name', 'option2_name', 'option3_name', 'hs_code', 
        'country_of_origin', 'location', 'bin_name', 'incoming', 
        'unavailable', 'committed', 'available', 'onhand_old', 'onhand_new', 
        'status', 'is_featured', 'is_active', 'created_by', 'fulfillment_type'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
