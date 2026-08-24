<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;

class ReviewAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user']);

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        $counts = [
            'all' => ProductReview::count(),
            'pending' => ProductReview::where('status', 'pending')->count(),
            'approved' => ProductReview::where('status', 'approved')->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
        ];

        return view('admin.commerce.reviews', compact('reviews', 'counts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $review = ProductReview::findOrFail($id);
        $review->update(['status' => $request->input('status')]);

        return redirect()->back()->with('success', 'Review status updated to ' . ucfirst($request->input('status')));
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Review removed successfully.');
    }
}
