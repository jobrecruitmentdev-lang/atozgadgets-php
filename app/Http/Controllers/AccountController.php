<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $recentOrders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(3)->get();
        return view('account.dashboard', compact('user', 'recentOrders'));
    }

    public function orders()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('account.orders', compact('user', 'orders'));
    }

    public function addresses()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $addresses = Address::where('user_id', $user->id)->get();
        return view('account.addresses', compact('user', 'addresses'));
    }

    public function saveAddress(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
        ]);

        $validated['user_id'] = $user->id;
        $validated['is_default'] = $request->has('is_default');

        if ($validated['is_default']) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        Address::create($validated);

        return redirect()->back()->with('success', 'Address added successfully.');
    }
}
