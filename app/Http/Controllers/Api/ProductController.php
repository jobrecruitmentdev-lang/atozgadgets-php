<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        // Query published white-labeled products
        $query = Product::published();

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $limit = $request->input('limit', 20);
        $products = $query->paginate($limit);

        return $this->successResponse([
            'products' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'page' => $products->currentPage(),
                'limit' => $products->perPage(),
                'totalPages' => $products->lastPage()
            ]
        ], 'Products retrieved successfully');
    }

    public function show($slug)
    {
        $product = Product::published()->with(['category', 'brand', 'variants', 'media', 'specifications'])->where('slug', $slug)->first();

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse($product, 'Product retrieved successfully');
    }
}
