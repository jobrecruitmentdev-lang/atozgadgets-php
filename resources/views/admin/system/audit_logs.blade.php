@extends('layouts.admin')

@section('title', 'System Audit Trail & Product Histories')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .nav-tabs { display: flex; gap: 12px; margin-bottom: 20px; }
    .nav-tab { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-secondary); }
    .nav-tab.active { background: var(--accent); color: #fff; border-color: var(--accent); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Audit Trail & Change History</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Track product changes, price updates, sync events, and administrative actions.</p>
    </div>
</div>

<div class="nav-tabs">
    <a href="{{ route('admin.system.audit_logs', ['tab' => 'products']) }}" class="nav-tab {{ $tab === 'products' ? 'active' : '' }}">Product Change History</a>
    <a href="{{ route('admin.system.audit_logs', ['tab' => 'system']) }}" class="nav-tab {{ $tab === 'system' ? 'active' : '' }}">Administrative Logs</a>
</div>

<div class="panel-card">
    @if($tab === 'products')
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Action Type</th>
                    <th>Description</th>
                    <th>User</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productHistories as $ph)
                    <tr>
                        <td style="font-weight: 700;">{{ $ph->product->name ?? 'Product #' . $ph->product_id }}</td>
                        <td><span style="font-size: 11px; font-weight: 700; text-transform: uppercase;">{{ $ph->action_type }}</span></td>
                        <td style="color: var(--text-secondary);">{{ $ph->description ?: 'State updated' }}</td>
                        <td>{{ $ph->user->first_name ?? 'System Worker' }}</td>
                        <td style="color: var(--text-secondary); font-size: 12px;">{{ $ph->created_at->format('M d, Y H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">No product history records logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $productHistories->links() }}</div>
    @else
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>User</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($systemAudits as $log)
                    <tr>
                        <td style="font-weight: 700;">{{ $log->action }}</td>
                        <td>{{ $log->user->first_name ?? 'System' }}</td>
                        <td style="color: var(--text-secondary);">{{ $log->details ?: 'Action executed' }}</td>
                        <td style="font-family: monospace; font-size: 12px;">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        <td style="color: var(--text-secondary); font-size: 12px;">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">No system audit entries logged.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $systemAudits->links() }}</div>
    @endif
</div>
@endsection
