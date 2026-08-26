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
            'mobile' => 'nullable|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'email.unique' => 'An account with this email address already exists. Please log in.',
            'mobile.unique' => 'This mobile number is already registered. Please log in or use a different number.',
        ]);

        $user = \App\Models\User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'mobile' => !empty($data['mobile']) ? $data['mobile'] : null,
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

    public function showForgotPasswordForm()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('store.home');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($request->email));

        $user = \App\Models\User::where('email', $email)->first();

        if ($user) {
            // Generate cryptographically secure 64-char token (256-bit entropy)
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);

            // Invalidate old tokens for this email
            \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();

            \Illuminate\Support\Facades\DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $tokenHash,
                'created_at' => now(),
            ]);

            // Dispatch Reset Email
            try {
                $resetUrl = route('password.reset', ['token' => $rawToken, 'email' => $email]);
                \Illuminate\Support\Facades\Mail::send('emails.password-reset', ['resetUrl' => $resetUrl, 'user' => $user], function ($m) use ($email) {
                    $m->to($email)->subject('Reset Your Password - AtoZGadgets');
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send password reset email to {$email}: " . $e->getMessage());
            }
        }

        // Anti-Enumeration Principle: Always return the exact same success message
        return back()->with('status', 'If an account exists with that email, we have sent a password reset link.');
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('store.home');
        }
        $email = $request->query('email', '');
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $rawToken = $request->token;
        $tokenHash = hash('sha256', $rawToken);

        // Find token within 30 minutes validity
        $record = \Illuminate\Support\Facades\DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $tokenHash)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        // Also check raw token for backwards compatibility
        if (!$record) {
            $record = \Illuminate\Support\Facades\DB::table('password_resets')
                ->where('email', $email)
                ->where('token', $rawToken)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->first();
        }

        if (!$record) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired. Please request a new one.']);
        }

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        if ($user->getConnection()->getSchemaBuilder()->hasColumn('users', 'password_hash')) {
            $user->password_hash = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

        // Invalidate used reset token
        \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please sign in with your new password.');
    }
}
