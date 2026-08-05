<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class StorefrontController extends Controller
{
    public function home()
    {
        $categories = Category::where('status', 'active')->limit(4)->get();
        $featuredProducts = Product::where('is_active', true)
                                   ->where('is_featured', true)
                                   ->limit(8)
                                   ->get();
        
        return view('store.home', compact('categories', 'featuredProducts'));
    }

    public function shop(Request $request)
    {
        $query = Product::where('is_active', true);
        
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }
        
        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('status', 'active')->get();
        
        return view('store.shop', compact('products', 'categories'));
    }

    public function product($slug)
    {
        $product = Product::where(function($query) use ($slug) {
            $query->where('slug', $slug);
            if (is_numeric($slug)) {
                $query->orWhere('id', (int)$slug);
            }
        })->where('is_active', true)->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->limit(4)
                                  ->get();
                                  
        return view('store.product', compact('product', 'relatedProducts'));
    }
}
