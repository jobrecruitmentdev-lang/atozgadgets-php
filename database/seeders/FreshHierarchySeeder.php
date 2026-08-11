<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FreshHierarchySeeder extends Seeder
{
    public function run()
    {
        // 1. Truncate tables (disable foreign key checks to allow truncation)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        Category::truncate();
        Brand::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Create Brands
        $brands = [
            'Apple' => Brand::create(['slug' => 'apple', 'name' => 'Apple', 'status' => 'active']),
            'Samsung' => Brand::create(['slug' => 'samsung', 'name' => 'Samsung', 'status' => 'active']),
            'Sony' => Brand::create(['slug' => 'sony', 'name' => 'Sony', 'status' => 'active']),
            'Dell' => Brand::create(['slug' => 'dell', 'name' => 'Dell', 'status' => 'active']),
            'Asus' => Brand::create(['slug' => 'asus', 'name' => 'Asus', 'status' => 'active']),
        ];

        // 3. Create Hierarchical Categories
        
        // --- ELECTRONICS BRANCH ---
        $electronics = Category::create(['slug' => 'electronics', 'name' => 'Electronics', 'status' => 'active', 'parent_id' => null]);
        
        // Electronics -> Audio
        $audio = Category::create(['slug' => 'audio', 'name' => 'Audio', 'status' => 'active', 'parent_id' => $electronics->id]);
        $headphones = Category::create(['slug' => 'headphones', 'name' => 'Headphones', 'status' => 'active', 'parent_id' => $audio->id]);
        $earbuds = Category::create(['slug' => 'earbuds', 'name' => 'Earbuds', 'status' => 'active', 'parent_id' => $audio->id]);
        
        // Electronics -> Laptops
        $laptops = Category::create(['slug' => 'laptops', 'name' => 'Laptops', 'status' => 'active', 'parent_id' => $electronics->id]);
        $gamingLaptops = Category::create(['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops', 'status' => 'active', 'parent_id' => $laptops->id]);
        $ultrabooks = Category::create(['slug' => 'ultrabooks', 'name' => 'Ultrabooks', 'status' => 'active', 'parent_id' => $laptops->id]);

        // --- MOBILES BRANCH ---
        $mobiles = Category::create(['slug' => 'mobiles', 'name' => 'Mobiles', 'status' => 'active', 'parent_id' => null]);
        $smartphones = Category::create(['slug' => 'smartphones', 'name' => 'Smartphones', 'status' => 'active', 'parent_id' => $mobiles->id]);
        $iphoneModels = Category::create(['slug' => 'iphone-models', 'name' => 'Apple iPhone', 'status' => 'active', 'parent_id' => $smartphones->id]);
        $galaxyModels = Category::create(['slug' => 'galaxy-models', 'name' => 'Samsung Galaxy', 'status' => 'active', 'parent_id' => $smartphones->id]);

        // --- ACCESSORIES BRANCH ---
        $accessories = Category::create(['slug' => 'accessories', 'name' => 'Accessories', 'status' => 'active', 'parent_id' => null]);
        
        // Accessories -> Mobile Cases
        $mobileCases = Category::create(['slug' => 'mobile-cases', 'name' => 'Mobile Cases', 'status' => 'active', 'parent_id' => $accessories->id]);
        $iphone17Cases = Category::create(['slug' => 'iphone-17-cases', 'name' => 'iPhone 17 Models', 'status' => 'active', 'parent_id' => $mobileCases->id]);
        $s25Cases = Category::create(['slug' => 'galaxy-s25-cases', 'name' => 'Galaxy S25 Models', 'status' => 'active', 'parent_id' => $mobileCases->id]);
        
        // Accessories -> Chargers
        $chargers = Category::create(['slug' => 'chargers', 'name' => 'Chargers', 'status' => 'active', 'parent_id' => $accessories->id]);
        $wirelessChargers = Category::create(['slug' => 'wireless-chargers', 'name' => 'Wireless Chargers', 'status' => 'active', 'parent_id' => $chargers->id]);
        $wallAdapters = Category::create(['slug' => 'wall-adapters', 'name' => 'Wall Adapters', 'status' => 'active', 'parent_id' => $chargers->id]);


        // 4. Create 15 Products
        $products = [
            // 1. iPhone 17 Silicone Case (Accessories > Mobile Cases > iPhone 17 Models)
            [
                'name' => 'iPhone 17 Silicone Case',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $iphone17Cases->id,
                'price' => 49.00,
                'thumbnail' => '/images/products/iphone_case.png',
                'description' => 'The premium silicone case for iPhone 17 offers ultimate protection with a silky, soft-touch finish. Designed to perfectly fit the new titanium edges, providing a comfortable grip while keeping your device safe from drops.'
            ],
            // 2. iPhone 17 Pro Titanium (Mobiles > Smartphones > Apple iPhone)
            [
                'name' => 'iPhone 17 Pro Titanium',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $iphoneModels->id,
                'price' => 1199.00,
                'thumbnail' => '/images/products/iphone_17_pro.png',
                'description' => 'Experience the pinnacle of mobile innovation. The iPhone 17 Pro features a revolutionary titanium chassis, the ultra-fast A19 Pro chip, and a next-generation periscope zoom camera for breathtaking photography.'
            ],
            // 3. Apple 30W USB-C Power Adapter (Accessories > Chargers > Wall Adapters)
            [
                'name' => 'Apple 30W USB-C Power Adapter',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $wallAdapters->id,
                'price' => 39.00,
                'thumbnail' => '/images/products/apple_charger.png',
                'description' => 'Fast and efficient charging for your Apple devices at home, in the office, or on the go. This 30W USB-C power adapter is optimized for iPad Pro, MacBook Air, and fast-charging the latest iPhones.'
            ],
            // 4. Samsung Galaxy S25 Ultra (Mobiles > Smartphones > Samsung Galaxy)
            [
                'name' => 'Samsung Galaxy S25 Ultra',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $galaxyModels->id,
                'price' => 1299.00,
                'thumbnail' => 'https://loremflickr.com/400/400/smartphone,samsung?lock=4',
                'description' => 'The ultimate Android flagship is here. With an integrated S-Pen, a mesmerizing Dynamic AMOLED 2X display, and a 200MP camera system, the Galaxy S25 Ultra is built for creators and power users.'
            ],
            // 5. Galaxy S25 Ultra Leather Cover (Accessories > Mobile Cases > Galaxy S25 Models)
            [
                'name' => 'Galaxy S25 Ultra Leather Cover',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $s25Cases->id,
                'price' => 59.00,
                'thumbnail' => 'https://loremflickr.com/400/400/phone,case,leather?lock=5',
                'description' => 'Wrap your Galaxy S25 Ultra in luxury. This genuine European leather cover adds an incredibly soft texture and sophisticated look while protecting your device from everyday bumps.'
            ],
            // 6. MagSafe Wireless Charging Stand (Accessories > Chargers > Wireless Chargers)
            [
                'name' => 'MagSafe 15W Wireless Charging Stand',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $wirelessChargers->id,
                'price' => 79.00,
                'thumbnail' => 'https://loremflickr.com/400/400/wireless,charger,magsafe?lock=6',
                'description' => 'Charge your iPhone beautifully. This magnetic wireless charging stand perfectly aligns your device for up to 15W of fast wireless charging, holding it at the perfect viewing angle for FaceTime or StandBy mode.'
            ],
            // 7. Sony WH-1000XM6 Headphones (Electronics > Audio > Headphones)
            [
                'name' => 'Sony WH-1000XM6 Noise Cancelling Headphones',
                'brand_id' => $brands['Sony']->id,
                'category_id' => $headphones->id,
                'price' => 398.00,
                'thumbnail' => 'https://loremflickr.com/400/400/headphones,sony?lock=7',
                'description' => 'Industry-leading noise cancellation gets even better. The WH-1000XM6 features dual-processor noise cancellation, ultra-comfortable ear pads, and up to 40 hours of battery life for uninterrupted listening.'
            ],
            // 8. Sony WF-1000XM5 Earbuds (Electronics > Audio > Earbuds)
            [
                'name' => 'Sony WF-1000XM5 Earbuds',
                'brand_id' => $brands['Sony']->id,
                'category_id' => $earbuds->id,
                'price' => 298.00,
                'thumbnail' => 'https://loremflickr.com/400/400/earbuds,sony?lock=8',
                'description' => 'Premium sound, incredibly compact. These true wireless earbuds deliver astonishing High-Resolution Audio and the best noise cancellation in their class, fitting perfectly in your pocket.'
            ],
            // 9. Apple AirPods Pro 3 (Electronics > Audio > Earbuds)
            [
                'name' => 'Apple AirPods Pro 3',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $earbuds->id,
                'price' => 249.00,
                'thumbnail' => 'https://loremflickr.com/400/400/airpods?lock=9',
                'description' => 'Magic remastered. AirPods Pro 3 bring adaptive audio, improved active noise cancellation, and a new USB-C charging case. Experience spatial audio that surrounds you completely.'
            ],
            // 10. Samsung Galaxy Buds 3 Pro (Electronics > Audio > Earbuds)
            [
                'name' => 'Samsung Galaxy Buds 3 Pro',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $earbuds->id,
                'price' => 199.00,
                'thumbnail' => 'https://loremflickr.com/400/400/earbuds,samsung?lock=10',
                'description' => 'Studio-quality sound for the Galaxy ecosystem. With seamless switching, 24-bit Hi-Fi audio support, and intelligent active noise cancellation, these are the ultimate companions for your Samsung phone.'
            ],
            // 11. Dell XPS 15 (Electronics > Laptops > Ultrabooks)
            [
                'name' => 'Dell XPS 15 OLED',
                'brand_id' => $brands['Dell']->id,
                'category_id' => $ultrabooks->id,
                'price' => 1899.00,
                'thumbnail' => 'https://loremflickr.com/400/400/laptop,dell?lock=11',
                'description' => 'Power your wildest creations. The Dell XPS 15 pairs a stunning 3.5K OLED touchscreen display with the latest Intel Core i9 processors, packed into a sleek, CNC-machined aluminum chassis.'
            ],
            // 12. Asus ROG Zephyrus G14 (Electronics > Laptops > Gaming Laptops)
            [
                'name' => 'Asus ROG Zephyrus G14',
                'brand_id' => $brands['Asus']->id,
                'category_id' => $gamingLaptops->id,
                'price' => 1649.00,
                'thumbnail' => 'https://loremflickr.com/400/400/laptop,gaming?lock=12',
                'description' => 'Unleash portable gaming power. This 14-inch gaming laptop packs an NVIDIA RTX 4070 and an ultra-fast AMD Ryzen 9 processor, perfect for high-framerate gaming on the go.'
            ],
            // 13. MacBook Air M3 (Electronics > Laptops > Ultrabooks)
            [
                'name' => 'MacBook Air M3',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $ultrabooks->id,
                'price' => 1099.00,
                'thumbnail' => 'https://loremflickr.com/400/400/macbook?lock=13',
                'description' => 'Lean. Mean. M3 machine. The wildly thin and light MacBook Air is now supercharged by the M3 chip, giving you blazing fast performance and up to 18 hours of battery life.'
            ],
            // 14. Samsung 45W Travel Adapter (Accessories > Chargers > Wall Adapters)
            [
                'name' => 'Samsung 45W Super Fast Travel Adapter',
                'brand_id' => $brands['Samsung']->id,
                'category_id' => $wallAdapters->id,
                'price' => 45.00,
                'thumbnail' => 'https://loremflickr.com/400/400/charger,samsung?lock=14',
                'description' => 'Get your Galaxy back to 100% in record time. This 45W Super Fast Charger safely and rapidly charges your compatible Samsung devices so you can get back to what matters most.'
            ],
            // 15. Apple iPhone 16 Basic (Mobiles > Smartphones > Apple iPhone)
            [
                'name' => 'Apple iPhone 16 Basic',
                'brand_id' => $brands['Apple']->id,
                'category_id' => $iphoneModels->id,
                'price' => 799.00,
                'thumbnail' => 'https://loremflickr.com/400/400/iphone?lock=15',
                'description' => 'Everything you need, beautifully designed. The iPhone 16 features an upgraded dual-camera system, a vibrant Super Retina XDR display, and the A18 chip for snappy performance.'
            ]
        ];

        foreach ($products as $prod) {
            Product::create([
                'name' => $prod['name'],
                'brand_id' => $prod['brand_id'],
                'category_id' => $prod['category_id'],
                'slug' => Str::slug($prod['name']) . '-' . Str::random(5),
                'sku' => 'ITM-' . strtoupper(Str::random(6)),
                'price' => $prod['price'],
                'thumbnail_image' => $prod['thumbnail'],
                'description' => $prod['description'],
                'stock_quantity' => rand(15, 120),
                'is_active' => true,
                'status' => 'active',
                'fulfillment_type' => 'own',
                'created_by' => 1
            ]);
        }
    }
}
