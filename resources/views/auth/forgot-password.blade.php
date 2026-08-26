@extends('layouts.auth')

@section('title', 'Forgot Password - AtoZGadgets')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Forgot Password</h1>
        <p>Enter your email address and we will send you a link to reset your password.</p>
    </div>

    @if (session('status'))
        <div style="background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #22c55e; padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; text-align: center;">
            {{ session('status') }}
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" autofocus value="{{ old('email') }}" placeholder="you@example.com">
        </div>

        <button type="submit" class="btn-primary">Send Reset Link</button>
    </form>

    <div class="auth-footer">
        <p>Remember your password? <a href="{{ route('login') }}">Sign In</a></p>
    </div>
</div>
@endsection
