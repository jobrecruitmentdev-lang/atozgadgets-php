@extends('layouts.store')

@section('title', 'My Account - AtoZ Gadgetz')

@section('content')
<style>
    .account-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: flex; gap: 40px; flex-direction: column; }
    @media (min-width: 768px) { .account-container { flex-direction: row; } }
    
    .account-sidebar { width: 100%; flex-shrink: 0; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; height: fit-content; }
    @media (min-width: 768px) { .account-sidebar { width: 280px; } }
    
    .sidebar-user { margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 16px; }
    .avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; }
    .sidebar-nav { display: flex; flex-direction: column; gap: 8px; }
    .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; color: var(--text-secondary); transition: all 0.3s; }
    .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .nav-link i { width: 18px; }
    
    .account-content { flex-grow: 1; }
    
    .content-header { margin-bottom: 32px; }
    .content-title { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
    
    .card-dark { background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; margin-bottom: 24px; }
</style>

<div class="account-container">
    <aside class="account-sidebar" data-aos="fade-right">
        <div class="sidebar-user">
            <div class="avatar">{{ substr($user->first_name, 0, 1) }}</div>
            <div>
                <div style="font-weight: 600;">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div style="font-size: 14px; color: var(--text-secondary);">{{ $user->email }}</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('account.dashboard') }}" class="nav-link {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                <i data-lucide="user"></i> My Profile
            </a>
            <a href="{{ route('account.orders') }}" class="nav-link {{ request()->routeIs('account.orders') ? 'active' : '' }}">
                <i data-lucide="shopping-bag"></i> My Orders
            </a>
            <a href="{{ route('account.addresses') }}" class="nav-link {{ request()->routeIs('account.addresses') ? 'active' : '' }}">
                <i data-lucide="map-pin"></i> Addresses
            </a>
            <form id="logout-form-account" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form-account').submit();" style="color: #ef4444; margin-top: 20px;">
                <i data-lucide="log-out"></i> Logout
            </a>
        </nav>
    </aside>

    <main class="account-content" data-aos="fade-up">
        @yield('account_content')
    </main>
</div>
@endsection
