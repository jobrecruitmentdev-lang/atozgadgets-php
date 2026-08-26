<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        } elseif ($request->filled('category')) {
            $cat = Category::where('slug', $request->input('category'))->orWhere('id', $request->input('category'))->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        if ($request->filled('fulfillment_type')) {
            $query->where('fulfillment_type', $request->input('fulfillment_type'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('status', 'active')->where('is_active', true);
            } elseif ($request->input('status') === 'draft') {
                $query->where(function($q) {
                    $q->where('status', 'draft')->orWhere('is_active', false);
                });
            }
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::all();
        $brands = Brand::all();

        // Calculate summary statistics for header KPI cards
        $stats = [
            'total' => Product::count(),
            'live' => Product::where('status', 'active')->where('is_active', true)->count(),
            'draft' => Product::where(function($q) {
                $q->where('status', 'draft')->orWhere('is_active', false);
            })->count(),
            'cj' => Product::where('fulfillment_type', 'cj')->count(),
            'own' => Product::where('fulfillment_type', '!=', 'cj')->orWhereNull('fulfillment_type')->count(),
        ];

        return view('admin.catalog.products', compact('products', 'categories', 'brands', 'stats'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,draft,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:products,id'
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        if ($action === 'publish') {
            Product::whereIn('id', $ids)->update([
                'status' => 'active',
                'is_active' => true
            ]);
            $msg = "{$count} products published to live storefront.";
        } elseif ($action === 'draft') {
            Product::whereIn('id', $ids)->update([
                'status' => 'draft',
                'is_active' => false
            ]);
            $msg = "{$count} products moved to draft.";
        } elseif ($action === 'delete') {
            Product::whereIn('id', $ids)->delete();
            $msg = "{$count} products permanently deleted.";
        }

        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'category_id' => 'required|integer',
            'brand_id' => 'nullable|integer',
            'thumbnail_image' => 'nullable|string|max:255',
        ]);

        $validated['sku'] = 'MAN-' . strtoupper(Str::random(6));
        $validated['status'] = 'active';
        $validated['is_active'] = true;
        $validated['fulfillment_type'] = 'own';
        $validated['created_by'] = 1; // Assuming default admin

        Product::create($validated);

        return redirect()->route('admin.catalog.products')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'category_id' => 'required|integer',
            'brand_id' => 'nullable|integer',
            'thumbnail_image' => 'nullable|string|max:255',
        ]);

        $product->update($validated);

        return redirect()->route('admin.catalog.products')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        try {
            $product->delete();
            return redirect()->route('admin.catalog.products')->with('success', 'Product deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.catalog.products')->with('error', 'Unable to delete product due to database constraints: ' . $e->getMessage());
        }
    }
}
