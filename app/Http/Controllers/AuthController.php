<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('store.home');
        }
        return view('auth.login');
    }
    public function showRegisterForm()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('store.home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = \App\Models\User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'role_id' => 3, // Customer role
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'is_active' => 1
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
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
            // Redirect admin to catalog import, customer to account dashboard
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                return redirect()->route('admin.catalog.import');
            }
            return redirect()->route('account.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
