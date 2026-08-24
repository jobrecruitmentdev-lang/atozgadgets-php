@extends('layouts.admin')

@section('title', 'System Health & Diagnostics')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .health-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px; }
    .health-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between; }
    .health-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .probe-name { font-size: 15px; font-weight: 700; color: var(--text-primary); }
    .probe-badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-healthy { background: rgba(16,185,129,0.15); color: #10b981; }
    .badge-warning { background: rgba(245,158,11,0.15); color: #d97706; }
    .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; }
    .probe-val { font-size: 14px; color: var(--text-secondary); margin-top: 4px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">System Health & Live Probes</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Dynamic hosting-aware diagnostics for MySQL, Outbox, Cron, and Gateways.</p>
    </div>
</div>

<div class="health-grid">
    @foreach($probes as $key => $probe)
        <div class="health-card">
            <div>
                <div class="health-card-header">
                    <span class="probe-name">{{ $probe['name'] }}</span>
                    <span class="probe-badge {{ $probe['is_healthy'] ? 'badge-healthy' : 'badge-danger' }}">
                        {{ $probe['is_healthy'] ? 'HEALTHY' : 'CHECK' }}
                    </span>
                </div>
                <div class="probe-val"><strong>Status:</strong> {{ $probe['status'] }}</div>
                <div class="probe-val"><strong>Latency / State:</strong> {{ $probe['latency'] }}</div>
            </div>
            <div style="font-size: 11px; color: var(--text-secondary); margin-top: 14px; border-top: 1px solid var(--border-color); padding-top: 8px;">
                {{ $probe['required'] ? '● Essential Core Dependency' : '○ Optional / Hostinger Fallback' }}
            </div>
        </div>
    @endforeach
</div>
@endsection
