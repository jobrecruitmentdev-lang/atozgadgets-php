<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\CjProduct;
use Illuminate\Support\Str;

class CatalogAdminController extends ApiController
{
    private $cjApiUrl = 'https://developers.cjdropshipping.com/api2.0/v1/product/list';
    
    public function browseCjProducts(Request $request)
    {
        // Must be admin
        if ($request->user()->role_id !== 1 && $request->user()->role_id !== 2) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $keyword = $request->input('keyword', '');
        $page = $request->input('page', 1);

        try {
            $response = Http::withHeaders([
                'CJ-Access-Token' => env('CJ_API_KEY')
            ])->get($this->cjApiUrl, [
                'keyword' => $keyword,
                'pageNum' => $page,
                'pageSize' => 20
            ]);

            if ($response->successful()) {
                return $this->successResponse($response->json(), 'CJ Products fetched');
            }

            return $this->errorResponse('CJ API Error', 500, $response->json());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reach CJ Dropshipping API', 500, $e->getMessage());
        }
    }

    public function importCjProduct(Request $request)
    {
        if ($request->user()->role_id !== 1 && $request->user()->role_id !== 2) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'cj_id' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|url'
        ]);

        // Duplicate check
        if (CjProduct::where('cj_product_id', $validated['cj_id'])->exists()) {
            return $this->errorResponse('Product already imported', 400);
        }

        // Apply a 100% markup rule for retail
        $retailPrice = $validated['price'] * 2;
        $slug = Str::slug($validated['name']) . '-' . rand(1000, 9999);

        // 1. Insert into main Products table
        $product = Product::create([
            'category_id' => 1, // Default category
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => 'Imported from CJ Dropshipping',
            'price' => $retailPrice,
            'image' => $validated['image'],
            'status' => 'active',
            'fulfillment_type' => 'cj'
        ]);

        // 2. Insert into CjProduct table
        CjProduct::create([
            'product_id' => $product->id,
            'cj_product_id' => $validated['cj_id'],
            'cj_price' => $validated['price']
        ]);

        return $this->successResponse($product, 'Product imported successfully', 201);
    }
}
