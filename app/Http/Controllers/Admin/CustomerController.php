<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.customers', compact('customers'));
    }
}
