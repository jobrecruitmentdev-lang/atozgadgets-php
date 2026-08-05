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

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.catalog.products', compact('products', 'categories', 'brands'));
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
            'subcategory_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
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
            'subcategory_id' => 'nullable|integer',
            'brand_id' => 'nullable|integer',
        ]);

        $product->update($validated);

        return redirect()->route('admin.catalog.products')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.catalog.products')->with('success', 'Product deleted successfully.');
    }
}
