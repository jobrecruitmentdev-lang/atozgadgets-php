<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Order;

class PaymentAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['order.user', 'order.orderAddress']);

        if ($request->input('filter') === 'failed') {
            $query->where('status', 'FAILED');
        } elseif ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('provider_transaction_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('order_number', 'LIKE', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total_volume' => PaymentTransaction::where('status', 'SUCCESS')->where('type', 'CAPTURE')->sum('amount'),
            'total_refunded' => PaymentTransaction::where('status', 'SUCCESS')->where('type', 'REFUND')->sum('amount'),
            'failed_count' => PaymentTransaction::where('status', 'FAILED')->count(),
            'total_count' => PaymentTransaction::count(),
        ];

        return view('admin.commerce.payments', compact('transactions', 'stats'));
    }
}
