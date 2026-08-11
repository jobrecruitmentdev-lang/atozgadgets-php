<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\CjProduct;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    private $demoCatalog = [
        [
            'pid' => 'CJ-SMART-PRO-PROJECTOR-01',
            'productNameEn' => 'AtoZ Mini HD Smart LED Projector 1080P WiFi Portable',
            'productSku' => 'CJ-PROJ-1080P',
            'sellPrice' => 29.50,
            'productImage' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Electronics & Gadgets',
        ],
        [
            'pid' => 'CJ-WIRELESS-LAMP-02',
            'productNameEn' => 'AtoZ 3-in-1 Fast Wireless Charging Station LED Desk Lamp',
            'productSku' => 'CJ-LAMP-3IN1',
            'sellPrice' => 14.80,
            'productImage' => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Smart Home',
        ],
        [
            'pid' => 'CJ-RGB-ORB-SPEAKER-03',
            'productNameEn' => 'AtoZ Magnetic Levitation Floating Bluetooth Speaker RGB',
            'productSku' => 'CJ-FLOAT-SPK',
            'sellPrice' => 34.20,
            'productImage' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Audio & Sound',
        ],
        [
            'pid' => 'CJ-4K-MINI-DRONE-04',
            'productNameEn' => 'AtoZ 4K Ultra HD Foldable Mini Drone with Obstacle Avoidance',
            'productSku' => 'CJ-DRONE-4K',
            'sellPrice' => 42.00,
            'productImage' => 'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Drones & Toys',
        ],
        [
            'pid' => 'CJ-SMART-BOTTLE-05',
            'productNameEn' => 'AtoZ Digital Temperature Display Smart Vacuum Flask 500ml',
            'productSku' => 'CJ-BOTTLE-LED',
            'sellPrice' => 8.90,
            'productImage' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Home & Kitchen',
        ],
        [
            'pid' => 'CJ-PORTABLE-BLENDER-06',
            'productNameEn' => 'AtoZ USB Rechargeable Personal Smoothie Juicer Blender 6 Blades',
            'productSku' => 'CJ-BLENDER-USB',
            'sellPrice' => 11.50,
            'productImage' => 'https://images.unsplash.com/photo-1570222094114-d054a817e56b?q=80&w=800&auto=format&fit=crop',
            'categoryName' => 'Home & Kitchen',
        ]
    ];

    public function import()
    {
        $categories = \App\Models\Category::with('children')->get();
        $brands = \App\Models\Brand::all();
        $stagedProducts = \App\Models\Product::where('fulfillment_type', 'cj')->get();
        return view('admin.catalog.import', compact('categories', 'brands', 'stagedProducts'));
    }

    private function getCjAccessToken()
    {
        return \Illuminate\Support\Facades\Cache::remember('cj_access_token', 86400, function () {
            $email = env('CJ_API_EMAIL');
            $key = env('CJ_API_KEY');
            if (!$email || !$key) return null;

            $response = \Illuminate\Support\Facades\Http::post('https://developers.cjdropshipping.com/api2.0/v1/authentication/getAccessToken', [
                'email' => $email,
                'password' => $key
            ]);

            if ($response->successful() && $response->json('result') === true) {
                return $response->json('data.accessToken');
            }
            return null;
        });
    }

    public function searchCjApi(Request $request)
    {
        $keyword = strtolower($request->query('keyword', ''));
        $token = $this->getCjAccessToken();

        // Check if we are using the live API
        if ($token) {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'CJ-Access-Token' => $token,
                'Content-Type' => 'application/json'
            ])->get('https://developers.cjdropshipping.com/api2.0/v1/product/list', [
                'productNameEn' => $keyword,
                'pageNum' => 1,
                'pageSize' => 100
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['list']) && count($data['data']['list']) > 0) {
                    return response()->json($data);
                }
            }
        }

        return response()->json([
            'result' => false,
            'message' => 'No real products found or CJ API error.',
            'data' => [
                'list' => []
            ]
        ]);
    }

    public function importCjProduct(Request $request)
    {
        $data = $request->validate([
            'pid' => 'required|string',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'category' => 'nullable|string',
            'categoryId' => 'nullable|integer'
        ]);

        $categoryId = $data['categoryId'] ?? 1;

        // Clean title logic exactly like the backend
        $cleanTitle = $data['title'];
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $cleanTitle) || strlen(trim($cleanTitle)) < 3) {
            $cleanTitle = 'AtoZ Smart Gadget - Imported Edition';
        }
        $cleanTitle = substr(trim($cleanTitle), 0, 200);
        $slug = Str::slug($cleanTitle) . '-' . substr((string) Str::uuid(), 0, 6);

        // Strict ACID Transaction - Create Product & CJ Product Pointer
        $product = \Illuminate\Support\Facades\DB::transaction(function () use ($categoryId, $cleanTitle, $slug, $data) {
            $product = Product::create([
                'category_id' => $categoryId,
                'name' => $cleanTitle,
                'slug' => $slug,
                'sku' => 'CJ-' . substr((string) Str::uuid(), 0, 8),
                'price' => $data['price'] * 2.0, // Default 100% markup
                'discount_price' => $data['price'] * 1.5,
                'thumbnail_image' => $data['image'],
                'stock_quantity' => 100,
                'status' => 'active',
                'is_active' => true,
                'fulfillment_type' => 'cj',
                'created_by' => 1
            ]);

            CjProduct::updateOrCreate(
                ['cj_product_id' => $data['pid']],
                [
                    'internal_product_id' => $product->id,
                    'title' => $cleanTitle,
                    'sell_price' => $data['price'],
                    'cj_image' => $data['image'],
                    'category_name' => $data['category'],
                    'status' => 'imported'
                ]
            );

            return $product;
        });

        return response()->json(['success' => true, 'internal_id' => $product->id]);
    }
}
