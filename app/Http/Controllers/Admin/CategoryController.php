<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.catalog.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? 'active';

        Category::create($validated);

        return redirect()->route('admin.catalog.categories')->with('success', 'Category created successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if ($request->has('force') || $request->has('delete_products')) {
            $deletedCount = $category->products()->count();
            \Illuminate\Support\Facades\DB::transaction(function () use ($category) {
                $category->products()->delete();
                $category->delete();
            });

            return redirect()->route('admin.catalog.categories')
                ->with('success', "Category '{$category->name}' and all {$deletedCount} associated product(s) deleted successfully.");
        }

        $productCount = $category->products()->count();
        if ($productCount > 0) {
            return redirect()->route('admin.catalog.categories')
                ->with('error', "Category '{$category->name}' contains {$productCount} product(s). Use 'Delete Category & All Products' button to cascade delete, or reassign products first.");
        }

        try {
            $category->delete();
            return redirect()->route('admin.catalog.categories')->with('success', 'Category deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.catalog.categories')->with('error', 'Unable to delete category due to database constraints.');
        }
    }
}
