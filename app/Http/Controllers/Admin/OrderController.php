<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CjOrder;
use App\Models\Shipment;
use App\Services\Cj\CjOrderService;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'cjOrder']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders', compact('orders'));
    }

    public function fulfillWithCj($id)
    {
        try {
            $result = CjOrderService::placeOrder($id);
            $order = Order::findOrFail($id);
            $order->update(['status' => 'processing']);

            return redirect()->back()->with('success', 'Order dispatched to CJ Dropshipping successfully! CJ Order ID: ' . ($result['cjOrderId'] ?? ''));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'CJ Fulfillment Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->only(['status', 'payment_status']));
        return redirect()->back()->with('success', 'Order updated successfully!');
    }

    public function destroy($id)
    {
        Order::destroy($id);
        return redirect()->back()->with('success', 'Order deleted successfully!');
    }
}