<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoryController extends ApiController
{
    public function index()
    {
        // Equivalent to the old Express backend fetching categories tree
        $categories = Category::where('status', 'active')->get();
        return $this->successResponse($categories, 'Categories retrieved successfully');
    }
}
