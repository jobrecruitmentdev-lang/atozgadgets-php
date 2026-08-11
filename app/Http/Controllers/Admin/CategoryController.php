<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // For the table list and the dropdown, we need all categories.
        $categories = Category::with('parent')->withCount('products')->orderBy('parent_id')->orderBy('name')->get();
        return view('admin.catalog.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $slug = $validated['slug'] ?? null;
        $validated['slug'] = $slug ?: Str::slug($validated['name']);
        if (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . time();
        }
        $validated['status'] = $validated['status'] ?? 'active';

        Category::create($validated);

        return redirect()->route('admin.catalog.categories')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        // Prevent setting itself or its descendants as its parent (basic check: can't be itself)
        if (!empty($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            $validated['parent_id'] = null;
        }

        $slug = $validated['slug'] ?? null;
        $validated['slug'] = $slug ?: Str::slug($validated['name']);
        if (Category::where('slug', $validated['slug'])->where('id', '!=', $category->id)->exists()) {
            $validated['slug'] .= '-' . time();
        }
        $validated['status'] = $validated['status'] ?? 'active';

        $category->update($validated);

        return redirect()->route('admin.catalog.categories')->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if ($request->boolean('force') || $request->has('delete_products')) {
            $deletedCount = $category->products()->count();
            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($category) {
                    // Iterate and delete products individually to fire Product deleting events (constraint cleanup)
                    $category->products->each(function($product) {
                        $product->delete();
                    });
                    $category->delete();
                });

                return redirect()->route('admin.catalog.categories')
                    ->with('success', "Category '{$category->name}' and all {$deletedCount} associated product(s) deleted successfully.");
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect()->route('admin.catalog.categories')
                    ->with('error', "Cannot cascade delete Category '{$category->name}'. It might be constrained by subcategories or other entities.");
            }
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
