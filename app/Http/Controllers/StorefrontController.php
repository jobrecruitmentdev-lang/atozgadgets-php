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
                                   ->latest()
                                   ->limit(8)
                                   ->get();
        
        return view('store.home', compact('categories', 'featuredProducts'));
    }

    public function shop(Request $request)
    {
        $query = Product::where('is_active', true);
        $currentCategory = null;
        
        if ($request->filled('category')) {
            $cat = Category::where('slug', $request->category)->first();
            if ($cat) {
                $currentCategory = $cat;
                $query->whereIn('category_id', $cat->getAllDescendantIds());
            }
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('brand', function($q3) use ($search) {
                      $q3->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhereHas('parent', function($p1) use ($search) {
                             $p1->where('name', 'like', "%{$search}%")
                                ->orWhereHas('parent', function($p2) use ($search) {
                                    $p2->where('name', 'like', "%{$search}%");
                                });
                         });
                  });
            });
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
        
        return view('store.shop', compact('products', 'currentCategory'));
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
