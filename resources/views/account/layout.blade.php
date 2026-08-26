@extends('layouts.store')

@section('title', 'My Account - AtoZ Gadgetz')

@section('content')
<style>
    .account-container {
        max-width: 1200px;
        margin: 30px auto 60px auto;
        padding: 0 16px;
        display: flex;
        gap: 32px;
        flex-direction: column;
    }
    @media (min-width: 992px) {
        .account-container {
            flex-direction: row;
            padding: 0 24px;
            margin: 40px auto 80px auto;
        }
    }
    
    .account-sidebar {
        width: 100%;
        flex-shrink: 0;
        background: rgba(18, 18, 20, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 20px;
        height: fit-content;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    @media (min-width: 992px) {
        .account-sidebar {
            width: 280px;
            padding: 28px 24px;
            position: sticky;
            top: 100px;
        }
    }
    
    .sidebar-user {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .avatar {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, #c9a962 0%, #8c7335 100%);
        color: #0b0b0d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(201, 169, 98, 0.3);
        flex-shrink: 0;
    }
    
    .sidebar-nav {
        display: flex;
        flex-direction: row;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 6px;
        -webkit-overflow-scrolling: touch;
    }
    @media (min-width: 992px) {
        .sidebar-nav {
            flex-direction: column;
            overflow-x: visible;
            padding-bottom: 0;
        }
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .nav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }
    .nav-link.active {
        background: rgba(201, 169, 98, 0.12);
        color: var(--accent);
        border-color: rgba(201, 169, 98, 0.3);
    }
    .nav-link i {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }
    
    .account-content {
        flex-grow: 1;
        min-width: 0; /* Prevents overflow in flex child */
    }
    
    .content-header {
        margin-bottom: 24px;
    }
    @media (min-width: 768px) {
        .content-header {
            margin-bottom: 32px;
        }
    }
    .content-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
        color: #ffffff;
    }
    @media (min-width: 768px) {
        .content-title {
            font-size: 28px;
        }
    }
    
    .card-dark {
        background: rgba(18, 18, 20, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }
    @media (min-width: 768px) {
        .card-dark {
            padding: 28px;
        }
    }
</style>

<div class="account-container">
    <aside class="account-sidebar" data-aos="fade-right">
        <div class="sidebar-user">
            <div class="avatar">{{ substr($user->first_name ?? 'U', 0, 1) }}</div>
            <div style="min-width: 0; overflow: hidden;">
                <div style="font-weight: 600; font-size: 15px; color: #fff; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                    {{ $user->first_name }} {{ $user->last_name }}
                </div>
                <div style="font-size: 13px; color: var(--text-secondary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                    {{ $user->email }}
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('account.dashboard') }}" class="nav-link {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                <i data-lucide="user"></i> <span>Profile</span>
            </a>
            <a href="{{ route('account.orders') }}" class="nav-link {{ request()->routeIs('account.orders') ? 'active' : '' }}">
                <i data-lucide="shopping-bag"></i> <span>Orders</span>
            </a>
            <a href="{{ route('account.addresses') }}" class="nav-link {{ request()->routeIs('account.addresses') ? 'active' : '' }}">
                <i data-lucide="map-pin"></i> <span>Addresses</span>
            </a>
            <form id="logout-form-account" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form-account').submit();" style="color: #ef4444;">
                <i data-lucide="log-out"></i> <span>Logout</span>
            </a>
        </nav>
    </aside>

    <main class="account-content" data-aos="fade-up">
        @yield('account_content')
    </main>
</div>
@endsection
