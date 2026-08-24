<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fulfillment;
use App\Models\FulfillmentException;
use App\Models\Shipment;
use App\Models\Order;
use App\Services\Fulfillment\FulfillmentService;

class FulfillmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function overview()
    {
        $counts = [
            'pending' => Fulfillment::whereIn('fulfillment_status', ['PENDING', 'SUBMITTING'])->count(),
            'submitted' => Fulfillment::where('fulfillment_status', 'SUBMITTED')->count(),
            'exceptions' => FulfillmentException::where('resolution_status', 'OPEN')->count(),
            'shipments' => Shipment::count(),
        ];

        $recentFulfillments = Fulfillment::with(['order.user', 'order.orderAddress', 'provider', 'shipments'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.fulfillment.overview', compact('counts', 'recentFulfillments'));
    }

    public function queue(Request $request)
    {
        $query = Fulfillment::with(['order.user', 'order.orderAddress', 'provider', 'items.orderItem.product']);

        if ($request->input('filter') === 'stale') {
            $twoHoursAgo = now()->subHours(2);
            $query->whereIn('fulfillment_status', ['PENDING', 'SUBMITTING'])
                  ->where('created_at', '<=', $twoHoursAgo);
        } else {
            $query->whereIn('fulfillment_status', ['PENDING', 'SUBMITTING']);
        }

        $fulfillments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fulfillment.queue', compact('fulfillments'));
    }

    public function shipments(Request $request)
    {
        $query = Shipment::with(['order.user', 'order.orderAddress', 'fulfillment.provider', 'carrier']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('tracking_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('order_number', 'LIKE', "%{$search}%");
                  });
        }

        $shipments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fulfillment.shipments', compact('shipments'));
    }

    public function exceptions(Request $request)
    {
        $query = FulfillmentException::with(['fulfillment.order.user', 'fulfillment.provider']);

        if ($request->input('status', 'OPEN') !== 'all') {
            $query->where('resolution_status', $request->input('status', 'OPEN'));
        }

        $exceptions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fulfillment.exceptions', compact('exceptions'));
    }

    public function retry($id)
    {
        $fulfillment = Fulfillment::with('order')->findOrFail($id);
        $result = FulfillmentService::executeFulfillment($fulfillment);

        if ($result->success) {
            return redirect()->back()->with('success', 'Fulfillment successfully retried and dispatched!');
        }

        return redirect()->back()->with('error', 'Retry failed: ' . ($result->errorMessage ?? 'Unknown error'));
    }

    public function resolveException(Request $request, $id)
    {
        $exception = FulfillmentException::findOrFail($id);
        $exception->update([
            'resolution_status' => 'RESOLVED',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Exception marked as resolved.');
    }
}
