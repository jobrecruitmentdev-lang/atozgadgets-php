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
        $query = Order::with(['user', 'orderAddress', 'items.product', 'cjOrder']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('orderAddress', function($aq) use ($search) {
                      $aq->where('first_name', 'LIKE', "%{$search}%")
                         ->orWhere('last_name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $tab = $request->input('tab', 'all');
        if ($tab === 'paid') {
            $query->whereIn('payment_status', ['paid', 'completed', 'success']);
        } elseif ($tab === 'pending') {
            $query->where('payment_status', 'pending');
        } elseif ($tab === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $counts = [
            'all' => Order::count(),
            'paid' => Order::whereIn('payment_status', ['paid', 'completed', 'success'])->count(),
            'pending' => Order::where('payment_status', 'pending')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders', compact('orders', 'counts', 'tab'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product.cjProduct',
            'items.variant',
            'orderAddress',
            'payments',
            'paymentTransactions',
            'fulfillments.provider',
            'fulfillments.items.orderItem.product',
            'fulfillments.attempts',
            'fulfillments.exceptions',
            'fulfillments.shipments.carrier',
            'cjOrder',
            'supplierOrders',
        ])->findOrFail($id);

        $customerStatus = \App\Services\Order\CustomerOrderStatusResolver::resolve($order);

        return view('admin.orders.show', compact('order', 'customerStatus'));
    }

    public function fulfillOrder($id)
    {
        try {
            $order = Order::with('items')->findOrFail($id);

            // Guard: Never fulfill unpaid orders
            if (!in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success'])) {
                return redirect()->back()->with('error', 'Cannot fulfill order: Payment status is "' . ($order->payment_status ?? 'pending') . '". Orders must be paid before dispatching.');
            }

            $fulfillment = $order->fulfillments()->whereIn('fulfillment_status', ['PENDING', 'EXCEPTION'])->first();
            if (!$fulfillment) {
                $fulfillment = \App\Services\Fulfillment\FulfillmentService::createFulfillmentsForOrder($order);
            }

            $result = \App\Services\Fulfillment\FulfillmentService::executeFulfillment($fulfillment);

            if ($result->success) {
                return redirect()->back()->with('success', 'Order dispatched to fulfillment provider successfully! Provider Ref: ' . ($result->externalOrderId ?? ''));
            }

            return redirect()->back()->with('error', 'Fulfillment Provider Error: ' . ($result->errorMessage ?? 'Unknown error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Fulfillment Error: ' . $e->getMessage());
        }
    }

    public function fulfillWithCj($id)
    {
        return $this->fulfillOrder($id);
    }

    public function syncCjStatus($id)
    {
        try {
            $order = Order::with('cjOrder')->findOrFail($id);
            if (!$order->cjOrder || empty($order->cjOrder->cj_order_id)) {
                return redirect()->back()->with('error', 'No CJ Order ID linked to sync.');
            }

            $detail = CjOrderService::getOrderDetail($order->cjOrder->cj_order_id);
            if ($detail) {
                $order->cjOrder->update([
                    'status' => $detail['orderStatus'] ?? $order->cjOrder->status,
                    'tracking_number' => $detail['trackNumber'] ?? $order->cjOrder->tracking_number,
                    'logistic_name' => $detail['logisticName'] ?? $order->cjOrder->logistic_name,
                ]);
                return redirect()->back()->with('success', 'CJ Order status synced successfully.');
            }

            return redirect()->back()->with('info', 'CJ Order status check completed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'CJ Sync Error: ' . $e->getMessage());
        }
    }

    public function processRefund(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $amount = $request->input('amount') ? (float)$request->input('amount') : null;
            $reason = $request->input('reason', 'Admin initiated refund');

            $result = \App\Services\Payment\PaymentService::processRefund($order, $amount, $reason);

            if ($result['success']) {
                return redirect()->back()->with('success', 'Refund processed successfully! Refund Ref: ' . ($result['refund_id'] ?? ''));
            }

            return redirect()->back()->with('error', 'Refund failed: ' . ($result['error'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Refund Error: ' . $e->getMessage());
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
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Order marked as cancelled successfully.');
    }
}