<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Fulfillment;
use App\Models\FulfillmentException;
use App\Models\PaymentTransaction;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // 1. Action Required Live Triage Data
        $twoHoursAgo = now()->subHours(2);
        $staleSyncThreshold = now()->subHours(48);

        $actionRequired = [
            'fulfillment_exceptions' => FulfillmentException::where('resolution_status', 'OPEN')->count(),
            'payment_failures' => PaymentTransaction::where('status', 'FAILED')->count(),
            'pending_stale' => Fulfillment::whereIn('fulfillment_status', ['PENDING', 'SUBMITTING'])->where('created_at', '<=', $twoHoursAgo)->count(),
            'stale_sync_products' => Product::where('fulfillment_type', 'cj')->where(function($q) use ($staleSyncThreshold) {
                $q->whereNull('updated_at')->orWhere('updated_at', '<=', $staleSyncThreshold);
            })->count(),
            'low_margin_products' => Product::whereRaw('(price - discount_price) < (price * 0.20)')->where('price', '>', 0)->count(),
        ];

        // 2. Today's Live Operational Pulse
        $todayStart = now()->startOfDay();
        $todayPulse = [
            'orders' => Order::where('created_at', '>=', $todayStart)->count(),
            'revenue' => (float)Order::whereIn('payment_status', ['paid', 'completed', 'success'])->where('created_at', '>=', $todayStart)->sum('total_amount'),
            'paid_orders' => Order::whereIn('payment_status', ['paid', 'completed', 'success'])->where('created_at', '>=', $todayStart)->count(),
            'shipped' => Shipment::where('created_at', '>=', $todayStart)->count(),
            'delivered' => Shipment::where('status', 'DELIVERED')->where('updated_at', '>=', $todayStart)->count(),
            'refunded' => PaymentTransaction::where('type', 'REFUND')->where('status', 'SUCCESS')->where('created_at', '>=', $todayStart)->count(),
        ];

        // 3. Core Store Aggregate Metrics
        $stats = [
            'totalRevenue' => (float)Order::whereIn('payment_status', ['paid', 'completed', 'success'])->sum('total_amount'),
            'totalOrders' => Order::count(),
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::where('stock_quantity', '<=', 5)->count(),
        ];

        // 4. Fetch recent 10 live orders
        $recentOrders = Order::with(['user', 'orderAddress', 'items.product', 'fulfillments'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('actionRequired', 'todayPulse', 'stats', 'recentOrders'));
    }
}