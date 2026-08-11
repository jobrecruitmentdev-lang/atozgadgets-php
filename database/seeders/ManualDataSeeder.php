<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManualDataSeeder extends Seeder
{
    public function run()
    {
        // Add 7 Categories
        $categories = [
            ['name' => 'Audio & Sound', 'slug' => 'audio-sound'],
            ['name' => 'Smart Wearables', 'slug' => 'smart-wearables'],
            ['name' => 'Gaming Gear', 'slug' => 'gaming-gear'],
            ['name' => 'Computer Peripherals', 'slug' => 'computer-peripherals'],
            ['name' => 'Virtual Reality', 'slug' => 'virtual-reality'],
            ['name' => 'Cameras & Drones', 'slug' => 'cameras-drones'],
            ['name' => 'Tablets & Pads', 'slug' => 'tablets-pads']
        ];
        
        $categoryIds = [];
        foreach ($categories as $cat) {
            $existingCat = DB::table('categories')->where('slug', $cat['slug'])->first();
            if ($existingCat) {
                $categoryIds[] = $existingCat->id;
            } else {
                $categoryIds[] = DB::table('categories')->insertGetId([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'description' => 'Premium ' . $cat['name'],
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Add 7 Brands
        $brands = [
            ['name' => 'Sony', 'slug' => 'sony'],
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Logitech', 'slug' => 'logitech'],
            ['name' => 'Razer', 'slug' => 'razer'],
            ['name' => 'Oculus', 'slug' => 'oculus'],
            ['name' => 'DJI', 'slug' => 'dji'],
            ['name' => 'Samsung', 'slug' => 'samsung']
        ];
        
        $brandIds = [];
        foreach ($brands as $brand) {
            $existingBrand = DB::table('brands')->where('slug', $brand['slug'])->first();
            if ($existingBrand) {
                $brandIds[] = $existingBrand->id;
            } else {
                $brandIds[] = DB::table('brands')->insertGetId([
                    'name' => $brand['name'],
                    'slug' => $brand['slug'],
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Add 10 Products using the copied images
        $products = [
            [
                'name' => 'Sony WF-1000XM4 Wireless Earbuds',
                'sku' => 'SNY-EAR-001',
                'category_id' => $categoryIds[0],
                'brand_id' => $brandIds[0],
                'price' => 279.99,
                'thumbnail_image' => asset('images/products/earbuds.png'),
                'description' => 'Industry-leading noise canceling true wireless earbuds.',
            ],
            [
                'name' => 'Apple Watch Series 9',
                'sku' => 'APL-WTCH-S9',
                'category_id' => $categoryIds[1],
                'brand_id' => $brandIds[1],
                'price' => 399.00,
                'thumbnail_image' => asset('images/products/smartwatch.png'),
                'description' => 'The ultimate device for a healthy life. Now smarter and faster.',
            ],
            [
                'name' => 'Logitech G Pro X Superlight',
                'sku' => 'LOG-MSE-PRO',
                'category_id' => $categoryIds[2],
                'brand_id' => $brandIds[2],
                'price' => 149.99,
                'thumbnail_image' => asset('images/products/mouse.png'),
                'description' => 'Ultra-lightweight wireless gaming mouse for esports.',
            ],
            [
                'name' => 'Razer BlackWidow V4 Pro',
                'sku' => 'RZR-KBD-V4',
                'category_id' => $categoryIds[3],
                'brand_id' => $brandIds[3],
                'price' => 229.99,
                'thumbnail_image' => asset('images/products/keyboard.png'),
                'description' => 'Premium mechanical gaming keyboard with Razer Chroma RGB.',
            ],
            [
                'name' => 'Oculus Quest 3 Advanced VR',
                'sku' => 'OCL-VR-Q3',
                'category_id' => $categoryIds[4],
                'brand_id' => $brandIds[4],
                'price' => 499.99,
                'thumbnail_image' => asset('images/products/vr_headset.png'),
                'description' => 'Breakthrough mixed reality headset for immersive experiences.',
            ],
            [
                'name' => 'Sony SRS-XB43 Portable Speaker',
                'sku' => 'SNY-SPK-XB43',
                'category_id' => $categoryIds[0],
                'brand_id' => $brandIds[0],
                'price' => 199.99,
                'thumbnail_image' => asset('images/products/speaker.png'),
                'description' => 'Extra Bass portable bluetooth speaker with party lights.',
            ],
            [
                'name' => 'DJI Action 4 Camera',
                'sku' => 'DJI-ACT-04',
                'category_id' => $categoryIds[5],
                'brand_id' => $brandIds[5],
                'price' => 329.00,
                'thumbnail_image' => asset('images/products/action_camera.png'),
                'description' => '4K rugged action camera with super steady stabilization.',
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'sku' => 'SNY-HDP-XM5',
                'category_id' => $categoryIds[0],
                'brand_id' => $brandIds[0],
                'price' => 348.00,
                'thumbnail_image' => asset('images/products/headphones.png'),
                'description' => 'Premium over-ear noise canceling headphones.',
            ],
            [
                'name' => 'DJI Mini 4 Pro Drone',
                'sku' => 'DJI-DRN-M4P',
                'category_id' => $categoryIds[5],
                'brand_id' => $brandIds[5],
                'price' => 759.00,
                'thumbnail_image' => asset('images/products/drone.png'),
                'description' => 'Miniature camera drone with obstacle avoidance and 4K HDR video.',
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 Ultra',
                'sku' => 'SAM-TAB-S9U',
                'category_id' => $categoryIds[6],
                'brand_id' => $brandIds[6],
                'price' => 1199.99,
                'thumbnail_image' => asset('images/products/tablet.png'),
                'description' => '14.6-inch Dynamic AMOLED 2X display, water and dust resistant.',
            ]
        ];

        foreach ($products as $p) {
            try {
                $slug = Str::slug($p['name']);
                $existingProduct = DB::table('products')->where('slug', $slug)->orWhere('sku', $p['sku'])->first();
                if (!$existingProduct) {
                    DB::table('products')->insert([
                        'name' => $p['name'],
                        'sku' => $p['sku'],
                        'slug' => $slug,
                        'category_id' => $p['category_id'],
                        'subcategory_id' => 1,
                        'brand_id' => $p['brand_id'],
                        'price' => $p['price'],
                        'thumbnail_image' => $p['thumbnail_image'],
                        'description' => $p['description'],
                        'stock_quantity' => 100,
                        'fulfillment_type' => 'own',
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore duplicates
            }
        }
    }
}
