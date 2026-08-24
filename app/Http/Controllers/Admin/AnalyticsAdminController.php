<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AnalyticsAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function sales(Request $request)
    {
        $days = (int)$request->input('days', 30);
        $startDate = now()->subDays($days);

        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
        ->whereIn('payment_status', ['paid', 'completed', 'success'])
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

        $metrics = [
            'total_revenue' => Order::whereIn('payment_status', ['paid', 'completed', 'success'])->where('created_at', '>=', $startDate)->sum('total_amount'),
            'total_orders' => Order::whereIn('payment_status', ['paid', 'completed', 'success'])->where('created_at', '>=', $startDate)->count(),
            'avg_order_val' => Order::whereIn('payment_status', ['paid', 'completed', 'success'])->where('created_at', '>=', $startDate)->avg('total_amount') ?: 0.0,
        ];

        return view('admin.analytics.sales', compact('salesData', 'metrics', 'days'));
    }

    public function products()
    {
        $topProducts = Product::withCount('orderItems')
            ->with('category')
            ->orderBy('order_items_count', 'desc')
            ->take(20)
            ->get();

        return view('admin.analytics.products', compact('topProducts'));
    }

    public function profitability()
    {
        $products = Product::with(['variants', 'category'])->latest()->paginate(25);

        return view('admin.analytics.profitability', compact('products'));
    }
}
