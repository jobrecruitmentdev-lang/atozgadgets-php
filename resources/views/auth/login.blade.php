@extends('layouts.auth')

@section('title', 'Login - AtoZGadgets')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Welcome Back</h1>
        <p>Enter your credentials to access your account</p>
    </div>

    <form method="POST" action="/login">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" autofocus placeholder="you@example.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
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
