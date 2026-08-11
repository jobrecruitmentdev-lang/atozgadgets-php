<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $brands = Brand::withCount('products')->get();
        return view('admin.catalog.brands', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'logo' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $slug = $validated['slug'] ?? null;
        $validated['slug'] = $slug ?: Str::slug($validated['name']);
        if (Brand::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . time();
        }
        $validated['status'] = $validated['status'] ?? 'active';

        Brand::create($validated);

        return redirect()->route('admin.catalog.brands')->with('success', 'Brand created successfully.');
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'logo' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $slug = $validated['slug'] ?? null;
        $validated['slug'] = $slug ?: Str::slug($validated['name']);
        if (Brand::where('slug', $validated['slug'])->where('id', '!=', $brand->id)->exists()) {
            $validated['slug'] .= '-' . time();
        }
        $validated['status'] = $validated['status'] ?? 'active';

        $brand->update($validated);

        return redirect()->route('admin.catalog.brands')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $productCount = $brand->products()->count();
        if ($productCount > 0) {
            if ($request->boolean('force')) {
                try {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($brand) {
                        $brand->products->each(function($product) {
                            $product->delete();
                        });
                        $brand->delete();
                    });
                    return redirect()->route('admin.catalog.brands')->with('success', "Brand '{$brand->name}' and all associated products deleted.");
                } catch (\Illuminate\Database\QueryException $e) {
                    return redirect()->route('admin.catalog.brands')->with('error', "Cannot cascade delete Brand '{$brand->name}'. It might be constrained by other entities.");
                }
            }
            return redirect()->route('admin.catalog.brands')->with('error', "Brand '{$brand->name}' is associated with {$productCount} product(s). Use 'Delete Brand & All Products' button to cascade delete, or reassign products first.");
        }

        try {
            $brand->delete();
            return redirect()->route('admin.catalog.brands')->with('success', 'Brand deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.catalog.brands')->with('error', 'Unable to delete brand due to database constraints.');
        }
    }
}
