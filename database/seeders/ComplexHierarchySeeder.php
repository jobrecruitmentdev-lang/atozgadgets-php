<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Str;

class ComplexHierarchySeeder extends Seeder
{
    public function run()
    {
        // 1. Create Brands
        $brands = [
            'Apple' => Brand::firstOrCreate(['slug' => 'apple'], ['name' => 'Apple', 'status' => 'active']),
            'Samsung' => Brand::firstOrCreate(['slug' => 'samsung'], ['name' => 'Samsung', 'status' => 'active']),
            'Sony' => Brand::firstOrCreate(['slug' => 'sony'], ['name' => 'Sony', 'status' => 'active']),
            'Bose' => Brand::firstOrCreate(['slug' => 'bose'], ['name' => 'Bose', 'status' => 'active']),
            'DJI' => Brand::firstOrCreate(['slug' => 'dji'], ['name' => 'DJI', 'status' => 'active']),
        ];

        // 2. Create Categories (Hierarchical)
        // Level 1
        $electronics = Category::firstOrCreate(['slug' => 'electronics'], ['name' => 'Electronics', 'status' => 'active', 'parent_id' => null]);
        
        // Level 2
        $mobiles = Category::firstOrCreate(['slug' => 'mobile-phones'], ['name' => 'Mobile Phones', 'status' => 'active', 'parent_id' => $electronics->id]);
        $accessories = Category::firstOrCreate(['slug' => 'phone-accessories'], ['name' => 'Phone Accessories', 'status' => 'active', 'parent_id' => $electronics->id]);
        $audio = Category::firstOrCreate(['slug' => 'audio'], ['name' => 'Audio', 'status' => 'active', 'parent_id' => $electronics->id]);
        $drones = Category::firstOrCreate(['slug' => 'drones-cameras'], ['name' => 'Drones & Cameras', 'status' => 'active', 'parent_id' => $electronics->id]);

        // Level 3
        $applePhones = Category::firstOrCreate(['slug' => 'apple-iphones'], ['name' => 'Apple iPhones', 'status' => 'active', 'parent_id' => $mobiles->id]);
        $samsungPhones = Category::firstOrCreate(['slug' => 'samsung-galaxy'], ['name' => 'Samsung Galaxy', 'status' => 'active', 'parent_id' => $mobiles->id]);
        
        $cases = Category::firstOrCreate(['slug' => 'mobile-cases'], ['name' => 'Mobile Cases', 'status' => 'active', 'parent_id' => $accessories->id]);
        $chargers = Category::firstOrCreate(['slug' => 'chargers'], ['name' => 'Chargers', 'status' => 'active', 'parent_id' => $accessories->id]);
        
        $headphones = Category::firstOrCreate(['slug' => 'headphones'], ['name' => 'Headphones', 'status' => 'active', 'parent_id' => $audio->id]);
        $earbuds = Category::firstOrCreate(['slug' => 'earbuds'], ['name' => 'Earbuds', 'status' => 'active', 'parent_id' => $audio->id]);
        
        $droneSubs = Category::firstOrCreate(['slug' => 'drones'], ['name' => 'Drones', 'status' => 'active', 'parent_id' => $drones->id]);
        $gimbals = Category::firstOrCreate(['slug' => 'gimbals'], ['name' => 'Gimbals', 'status' => 'active', 'parent_id' => $drones->id]);

        // 3. Create 15 Products
        $products = [
            // Apple
            [
                'name' => 'iPhone 17 Pro Titanium',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $applePhones->id,
                'price' => 1199.00,
                'thumbnail' => '/images/products/iphone_17_pro.png'
            ],
            [
                'name' => 'iPhone 17 Silicone Case',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $cases->id,
                'price' => 49.00,
                'thumbnail' => '/images/products/iphone_case.png'
            ],
            [
                'name' => 'Apple 30W USB-C Power Adapter',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $chargers->id,
                'price' => 39.00,
                'thumbnail' => '/images/products/apple_charger.png'
            ],
            [
                'name' => 'iPhone 16 Basic Edition',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $applePhones->id,
                'price' => 799.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Apple Leather Wallet with MagSafe',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $cases->id, // Fits in accessories/cases conceptually
                'price' => 59.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1622618991746-fe6004db3a47?auto=format&fit=crop&w=400&q=80'
            ],
            
            // Samsung
            [
                'name' => 'Samsung Galaxy S25 Ultra',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $samsungPhones->id,
                'price' => 1299.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Samsung S-Pen Pro',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $accessories->id,
                'price' => 99.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1583485088034-697b5a62f559?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Samsung Galaxy Buds 3',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $earbuds->id,
                'price' => 149.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Samsung 45W Fast Charger',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $chargers->id,
                'price' => 45.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&w=400&q=80'
            ],

            // Sony
            [
                'name' => 'Sony WH-1000XM6 Noise Cancelling Headphones',
                'brand_id' => $brands['Sony']->id,
                'category_id' => $headphones->id,
                'price' => 398.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Sony WF-1000XM5 Earbuds',
                'brand_id' => $brands['Sony']->id,
                'category_id' => $earbuds->id,
                'price' => 298.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1572569432705-d68f02908f9f?auto=format&fit=crop&w=400&q=80'
            ],

            // Bose
            [
                'name' => 'Bose QuietComfort Ultra',
                'brand_id' => $brands['Bose']->id,
                'category_id' => $headphones->id,
                'price' => 429.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1546435770-a3e426fa75a6?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'Bose SoundLink Flex Bluetooth Speaker',
                'brand_id' => $brands['Bose']->id,
                'category_id' => $audio->id, // Sticking it in general audio
                'price' => 149.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?auto=format&fit=crop&w=400&q=80'
            ],

            // DJI
            [
                'name' => 'DJI Mavic 4 Pro Drone',
                'brand_id' => $brands['DJI']->id,
                'category_id' => $droneSubs->id,
                'price' => 2199.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1507582020474-9a35b7d455d9?auto=format&fit=crop&w=400&q=80'
            ],
            [
                'name' => 'DJI Osmo Mobile 7 Gimbal',
                'brand_id' => $brands['DJI']->id,
                'category_id' => $gimbals->id,
                'price' => 159.00,
                'thumbnail' => 'https://images.unsplash.com/photo-1622384666491-1c5c0d297a7a?auto=format&fit=crop&w=400&q=80'
            ]
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(
                ['name' => $prod['name']],
                [
                    'brand_id' => $prod['brand_id'],
                    'category_id' => $prod['category_id'],
                    'slug' => Str::slug($prod['name']) . '-' . Str::random(5),
                    'sku' => 'SEED-' . strtoupper(Str::random(6)),
                    'price' => $prod['price'],
                    'thumbnail_image' => $prod['thumbnail'],
                    'stock_quantity' => rand(10, 100),
                    'is_active' => true,
                    'status' => 'active',
                    'fulfillment_type' => 'own',
                    'created_by' => 1
                ]
            );
        }
    }
}
