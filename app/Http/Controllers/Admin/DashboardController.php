<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // 5-minute cached stats with instant fallback
        $stats = Cache::remember('admin_dashboard_metrics', 300, function () {
            $totalRevenue = Order::whereIn('payment_status', ['paid', 'completed', 'success'])->sum('total_amount');
            $totalOrders = Order::count();
            $processingOrders = Order::where('status', 'processing')->orWhere('payment_status', 'pending')->count();
            $completedOrders = Order::whereIn('status', ['delivered', 'completed'])->count();
            $totalCustomers = User::where('role_id', '!=', 1)->count();
            $totalProducts = Product::count();
            $lowStockCount = Product::where('stock_quantity', '<=', 5)->count();

            // 30 days daily sales history
            $thirtyDaysAgo = now()->subDays(30);
            $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

            // Top 5 Selling Products
            $topProducts = Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get();

            return [
                'totalRevenue' => (float)$totalRevenue,
                'totalOrders' => $totalOrders,
                'processingOrders' => $processingOrders,
                'completedOrders' => $completedOrders,
                'totalCustomers' => $totalCustomers,
                'totalProducts' => $totalProducts,
                'lowStockCount' => $lowStockCount,
                'dailySales' => $dailySales,
                'topProducts' => $topProducts,
            ];
        });

        // Fetch recent 10 live orders
        $recentOrders = Order::with(['user', 'items.product'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}