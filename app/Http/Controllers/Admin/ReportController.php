<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $range = $request->input('range', '30');
        $days = (int)$range;
        if ($days <= 0) $days = 30;

        $startDate = now()->subDays($days)->startOfDay();

        $revenue = Order::where('created_at', '>=', $startDate)
            ->whereIn('payment_status', ['paid', 'completed', 'success'])
            ->sum('total_amount');

        $orderCount = Order::where('created_at', '>=', $startDate)->count();
        $aov = $orderCount > 0 ? ($revenue / $orderCount) : 0;

        $salesTrend = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as orders')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->get();

        $categoryBreakdown = Category::withCount('products')->get();
        $recentSales = Order::with(['user'])->latest()->take(8)->get();

        return view('admin.reports', compact('revenue', 'orderCount', 'aov', 'salesTrend', 'categoryBreakdown', 'recentSales', 'days'));
    }

    public function export(Request $request, $type)
    {
        $filename = "atozgadgets_{$type}_report_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'orders') {
                fputcsv($handle, ['Order Number', 'Customer Name', 'Email', 'Total ($)', 'Status', 'Payment Status', 'Date']);
                Order::with('user')->chunk(100, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->order_number,
                            $order->user ? ($order->user->first_name . ' ' . $order->user->last_name) : 'Guest',
                            $order->user ? $order->user->email : 'N/A',
                            number_format($order->total_amount, 2),
                            $order->status,
                            $order->payment_status,
                            $order->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
            } elseif ($type === 'inventory') {
                fputcsv($handle, ['Product ID', 'SKU', 'Name', 'Stock', 'Price ($)', 'Fulfillment Type', 'Created At']);
                Product::chunk(100, function ($products) use ($handle) {
                    foreach ($products as $prod) {
                        fputcsv($handle, [
                            $prod->id,
                            $prod->sku,
                            $prod->name,
                            $prod->stock_quantity,
                            number_format($prod->price, 2),
                            $prod->fulfillment_type ?? 'cj',
                            $prod->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
            } elseif ($type === 'customers') {
                fputcsv($handle, ['User ID', 'First Name', 'Last Name', 'Email', 'Mobile', 'Role', 'Registered Date']);
                User::chunk(100, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        fputcsv($handle, [
                            $user->id,
                            $user->first_name,
                            $user->last_name,
                            $user->email,
                            $user->mobile ?? 'N/A',
                            $user->role_id == 1 ? 'Admin' : 'Customer',
                            $user->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}