@extends('layouts.auth')

@section('title', 'Login - AtoZGadgets')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Enter your credentials to access your account</p>
    </div>

    @if (session('success'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #22c55e; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" autofocus value="{{ old('email') }}" placeholder="you@example.com">
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                <a href="{{ route('password.request') }}" style="color: var(--accent); font-size: 12.5px; text-decoration: none; font-weight: 500;">Forgot Password?</a>
            </div>
            <div class="password-input-wrapper">
                <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility(this)" aria-label="Show password" title="Show password">
                    <i data-lucide="eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <div class="auth-footer">
        <p>Don't have an account? <a href="{{ route('register') }}">Sign up now</a></p>
    </div>
</div>
@endsection
