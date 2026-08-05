<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Auto-seed admin user if the database is completely empty (helpful for testing)
        if (\App\Models\User::count() === 0) {
            \App\Models\User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => $credentials['email'],
                'mobile' => '1234567890',
                'role_id' => 1,
                'password' => \Illuminate\Support\Facades\Hash::make($credentials['password']),
                'is_active' => 1
            ]);
        }

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect admin to catalog import, customer to home
            if (auth()->user()->role_id == 1) {
                return redirect()->route('admin.catalog.import');
            }
            return redirect()->route('store.home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
