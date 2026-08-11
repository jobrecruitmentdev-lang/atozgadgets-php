<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $orders = Order::with(['user', 'items'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.orders', compact('orders'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'order_status' => 'required|string',
            'payment_status' => 'required|string'
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders')->with('success', 'Order updated successfully.');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                if (method_exists($order, 'items')) {
                    $order->items()->delete();
                }
                $order->delete();
            });
            return redirect()->route('admin.orders')->with('success', 'Order deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.orders')->with('error', 'Unable to delete order. It may be linked to other records.');
        }
    }
}
