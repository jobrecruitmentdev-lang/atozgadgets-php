<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('admin.catalog.brands', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? 'active';

        Brand::create($validated);

        return redirect()->route('admin.catalog.brands')->with('success', 'Brand created successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        if ($request->has('force') || $request->has('delete_products')) {
            $deletedCount = $brand->products()->count();
            \Illuminate\Support\Facades\DB::transaction(function () use ($brand) {
                $brand->products()->delete();
                $brand->delete();
            });

            return redirect()->route('admin.catalog.brands')
                ->with('success', "Brand '{$brand->name}' and all {$deletedCount} associated product(s) deleted successfully.");
        }

        $productCount = $brand->products()->count();
        if ($productCount > 0) {
            return redirect()->route('admin.catalog.brands')
                ->with('error', "Brand '{$brand->name}' is associated with {$productCount} product(s). Use 'Delete Brand & All Products' button to cascade delete, or reassign products first.");
        }

        try {
            $brand->delete();
            return redirect()->route('admin.catalog.brands')->with('success', 'Brand deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.catalog.brands')->with('error', 'Unable to delete brand due to database constraints.');
        }
    }
}
