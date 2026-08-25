<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\ProductReview;
use App\Services\Payment\PaymentGatewayManager;

class StorefrontController extends Controller
{
    public function home()
    {
        $categories = Category::where('status', 'active')->limit(4)->get();
        $featuredProducts = Product::published()
                                   ->latest()
                                   ->limit(8)
                                   ->get();
        
        return view('store.home', compact('categories', 'featuredProducts'));
    }

    public function shop(Request $request)
    {
        $query = Product::published();
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
        $product = Product::published()
            ->with(['variants', 'media', 'specifications', 'approvedReviews.user', 'category.parent'])
            ->where(function($query) use ($slug) {
                $query->where('slug', $slug);
                if (is_numeric($slug)) {
                    $query->orWhere('id', (int)$slug);
                }
            })
            ->firstOrFail();

        $relatedProducts = Product::published()
                                  ->where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->limit(4)
                                  ->get();

        $paymentMethods = PaymentGatewayManager::customerAvailableMethods();
        $trustHeadline = PaymentGatewayManager::getTrustHeadline();
                                  
        return view('store.product', compact('product', 'relatedProducts', 'paymentMethods', 'trustHeadline'));
    }

    public function submitReview(Request $request, $slug)
    {
        $product = Product::published()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'review' => 'required|string|min:5|max:2000',
        ]);

        $userId = auth()->id() ?? 1; // Default to current user or guest user ID 1
        $hasPurchased = false;

        // Authentic Verified Purchase Check
        if (auth()->check()) {
            $hasPurchased = OrderItem::whereHas('order', function ($q) {
                $q->where('user_id', auth()->id())
                  ->whereIn('payment_status', ['paid', 'completed', 'success'])
                  ->whereNotIn('status', ['cancelled', 'failed', 'refunded']);
            })->where('product_id', $product->id)->exists();
        }

        ProductReview::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'review' => $validated['review'],
            'status' => 'approved',
            'verified_purchase' => $hasPurchased,
        ]);

        return back()->with('success', 'Thank you! Your customer review has been submitted.');
    }

    public function orderConfirmation($orderNumber)
    {
        $order = \App\Models\Order::with(['items.product', 'items.variant', 'orderAddress', 'user'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('store.order_confirmation', compact('order'));
    }
}