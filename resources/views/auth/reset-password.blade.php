@extends('layouts.auth')

@section('title', 'Reset Password - AtoZGadgets')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Set New Password</h1>
        <p>Please choose a new, secure password for your account.</p>
    </div>

    @if ($errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" value="{{ old('email', $email) }}" placeholder="you@example.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">New Password</label>
            <div class="password-input-wrapper">
                <input type="password" id="password" name="password" class="form-input" required placeholder="Min. 8 characters">
                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility(this)" aria-label="Show password" title="Show password">
                    <i data-lucide="eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm New Password</label>
            <div class="password-input-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required placeholder="Confirm new password">
                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility(this)" aria-label="Show password" title="Show password">
                    <i data-lucide="eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Update Password</button>
    </form>

    <div class="auth-footer">
        <p>Remember your password? <a href="{{ route('login') }}">Sign In</a></p>
    </div>
</div>
@endsection
