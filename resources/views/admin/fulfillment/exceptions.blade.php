@extends('layouts.admin')

@section('title', 'Fulfillment Exceptions Hub')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .badge-danger { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="alert-triangle" style="color: #ef4444;"></i> Fulfillment Exceptions Hub</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Dead-letter queue for supplier rejections, balance shortages, or address mismatches.</p>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Exception ID</th>
                <th>Order #</th>
                <th>Error Code</th>
                <th>Error Reason</th>
                <th>Logged At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exceptions as $exc)
                <tr>
                    <td style="font-family: monospace; font-weight: 700;">#EXC-{{ str_pad($exc->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $exc->fulfillment->order_id ?? 1) }}" style="color: var(--accent); font-weight: 700;">
                            {{ $exc->fulfillment->order->order_number ?? 'Order #' . ($exc->fulfillment->order_id ?? '') }}
                        </a>
                    </td>
                    <td><span class="badge-danger">{{ $exc->error_code }}</span></td>
                    <td style="color: #ef4444; font-weight: 500; max-width: 320px;">{{ $exc->error_message }}</td>
                    <td style="color: var(--text-secondary);">{{ $exc->created_at->format('M d, H:i') }}</td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <form action="{{ route('admin.fulfillment.retry', $exc->fulfillment_id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-primary">Retry Dispatch</button>
                            </form>
                            @if($exc->resolution_status === 'OPEN')
                                <form action="{{ route('admin.fulfillment.resolve_exception', $exc->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-action btn-outline">Resolve</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                        <i data-lucide="check-circle" style="width: 32px; height: 32px; color: #10b981; margin-bottom: 8px; display:block; margin-inline:auto;"></i>
                        Zero fulfillment exceptions. All supplier queues operating smoothly!
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $exceptions->links() }}
    </div>
</div>
@endsection
