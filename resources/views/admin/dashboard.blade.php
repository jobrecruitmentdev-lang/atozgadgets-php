@extends('layouts.admin')

@section('title', 'Admin Dashboard Overview')

@section('content')
<style>
    .page-title { font-size: 24px; font-weight: 700; margin-bottom: 24px; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 24px; margin-bottom: 32px; }
    @media (min-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .stats-grid { grid-template-columns: repeat(4, 1fr); } }
    
    .stat-card { padding: 24px; display: flex; flex-direction: column; }
    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .stat-title { font-size: 14px; font-weight: 500; color: var(--text-secondary); margin-bottom: 4px; }
    .stat-value { font-size: 30px; font-weight: 700; }
    .stat-icon-wrap { padding: 8px; background: rgba(128,128,128,0.1); border-radius: 8px; }
    
    .trend { display: flex; align-items: center; font-size: 14px; }
    .trend-up { color: #10b981; font-weight: 500; }
    .trend-down { color: #ef4444; font-weight: 500; }
    .trend-text { color: var(--text-secondary); margin-left: 8px; }

    .chart-placeholder { height: 400px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--text-secondary); }
</style>

<h1 class="page-title">Dashboard Overview</h1>

<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Total Revenue</p>
                <h3 class="stat-value">$12,450.00</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="dollar-sign" style="color:var(--text-secondary);"></i></div>
        </div>
        <div class="trend">
            <i data-lucide="arrow-up-right" class="trend-up" style="width:16px;"></i>
            <span class="trend-up">+20.1%</span>
            <span class="trend-text">vs last month</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Orders</p>
                <h3 class="stat-value">156</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="shopping-cart" style="color:var(--text-secondary);"></i></div>
        </div>
        <div class="trend">
            <i data-lucide="arrow-up-right" class="trend-up" style="width:16px;"></i>
            <span class="trend-up">+12.5%</span>
            <span class="trend-text">vs last month</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Active Customers</p>
                <h3 class="stat-value">2,341</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="users" style="color:var(--text-secondary);"></i></div>
        </div>
        <div class="trend">
            <i data-lucide="arrow-up-right" class="trend-up" style="width:16px;"></i>
            <span class="trend-up">+5.4%</span>
            <span class="trend-text">vs last month</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-header">
            <div>
                <p class="stat-title">Products in Stock</p>
                <h3 class="stat-value">14,204</h3>
            </div>
            <div class="stat-icon-wrap"><i data-lucide="package" style="color:var(--text-secondary);"></i></div>
        </div>
        <div class="trend">
            <i data-lucide="arrow-down-right" class="trend-down" style="width:16px;"></i>
            <span class="trend-down">-1.2%</span>
            <span class="trend-text">vs last month</span>
        </div>
    </div>
</div>

<div class="card" style="padding: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Monthly Sales Overview</h3>
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">Gross revenue performance across recent months</p>
        </div>
        <span style="font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; background: rgba(37,99,235,0.1); color: var(--accent);">Year 2026</span>
    </div>
    
    <div style="height: 280px; width: 100%; position: relative;">
        <svg viewBox="0 0 800 240" style="width: 100%; height: 100%; overflow: visible;">
            <!-- Grid Lines -->
            <line x1="40" y1="40" x2="780" y2="40" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
            <line x1="40" y1="100" x2="780" y2="100" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
            <line x1="40" y1="160" x2="780" y2="160" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
            <line x1="40" y1="220" x2="780" y2="220" stroke="var(--border-color)" opacity="0.8" />
            
            <!-- Y-Axis Labels -->
            <text x="30" y="44" fill="var(--text-secondary)" font-size="11" text-anchor="end">$15k</text>
            <text x="30" y="104" fill="var(--text-secondary)" font-size="11" text-anchor="end">$10k</text>
            <text x="30" y="164" fill="var(--text-secondary)" font-size="11" text-anchor="end">$5k</text>
            <text x="30" y="224" fill="var(--text-secondary)" font-size="11" text-anchor="end">$0</text>
            
            <!-- Bars -->
            <!-- Jan -->
            <rect x="70" y="130" width="36" height="90" rx="4" fill="var(--accent)" opacity="0.7" />
            <text x="88" y="238" fill="var(--text-secondary)" font-size="11" text-anchor="middle">Jan</text>

            <!-- Feb -->
            <rect x="180" y="100" width="36" height="120" rx="4" fill="var(--accent)" opacity="0.7" />
            <text x="198" y="238" fill="var(--text-secondary)" font-size="11" text-anchor="middle">Feb</text>

            <!-- Mar -->
            <rect x="290" y="80" width="36" height="140" rx="4" fill="var(--accent)" opacity="0.7" />
            <text x="308" y="238" fill="var(--text-secondary)" font-size="11" text-anchor="middle">Mar</text>

            <!-- Apr -->
            <rect x="400" y="110" width="36" height="110" rx="4" fill="var(--accent)" opacity="0.7" />
            <text x="418" y="238" fill="var(--text-secondary)" font-size="11" text-anchor="middle">Apr</text>

            <!-- May -->
            <rect x="510" y="60" width="36" height="160" rx="4" fill="var(--accent)" opacity="0.85" />
            <text x="528" y="238" fill="var(--text-secondary)" font-size="11" text-anchor="middle">May</text>

            <!-- Jun -->
            <rect x="620" y="45" width="36" height="175" rx="4" fill="var(--accent)" opacity="1" />
            <text x="638" y="238" fill="var(--text-secondary)" font-size="11" font-weight="700" text-anchor="middle">Jun</text>

            <!-- Jul (Current) -->
            <rect x="710" y="30" width="36" height="190" rx="4" fill="url(#barGradient)" />
            <text x="728" y="238" fill="var(--accent)" font-size="11" font-weight="700" text-anchor="middle">Jul</text>

            <!-- Gradient Definition -->
            <defs>
                <linearGradient id="barGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="var(--accent)" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                </linearGradient>
            </defs>
        </svg>
    </div>
</div>

@endsection
