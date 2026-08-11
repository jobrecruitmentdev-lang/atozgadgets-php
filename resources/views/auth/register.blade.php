@extends('layouts.auth')

@section('title', 'Register - AtoZGadgets')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Create an Account</h1>
        <p>Sign up to start shopping with AtoZGadgets</p>
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

    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label" for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-input" required autocomplete="given-name" value="{{ old('first_name') }}" placeholder="John">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-input" required autocomplete="family-name" value="{{ old('last_name') }}" placeholder="Doe">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" value="{{ old('email') }}" placeholder="you@example.com">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="mobile">Mobile Number (Optional)</label>
            <input type="tel" id="mobile" name="mobile" class="form-input" autocomplete="tel" value="{{ old('mobile') }}" placeholder="+1234567890">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary" style="margin-top: 10px;">Create Account</button>
    </form>

    <div class="auth-footer">
        <p>Already have an account? <a href="{{ route('login') }}">Sign in instead</a></p>
    </div>
</div>
@endsection
