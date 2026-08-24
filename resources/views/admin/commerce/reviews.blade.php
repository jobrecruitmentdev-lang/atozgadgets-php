@extends('layouts.admin')

@section('title', 'Customer Reviews Moderation')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; color: var(--text-primary); }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 24px; }
    .table-modern { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .table-modern th { text-align: left; padding: 12px 14px; background: rgba(128,128,128,0.05); color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); }
    .table-modern td { padding: 14px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; }
    .btn-success { background: #10b981; color: #fff; }
    .btn-danger { background: #ef4444; color: #fff; }
    .badge-verified { background: rgba(16,185,129,0.15); color: #10b981; font-size: 11px; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
    .badge-status { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-pending { background: rgba(245,158,11,0.15); color: #d97706; }
    .badge-approved { background: rgba(16,185,129,0.15); color: #10b981; }
    .badge-rejected { background: rgba(239,68,68,0.15); color: #ef4444; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Customer Reviews Moderation</h1>
        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Moderate customer reviews, verified purchase flags, and aggregate rating schemas.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.commerce.reviews') }}" class="btn-action" style="background: rgba(128,128,128,0.1); color: var(--text-primary);">All ({{ $counts['all'] }})</a>
        <a href="{{ route('admin.commerce.reviews', ['status' => 'pending']) }}" class="btn-action" style="background: rgba(245,158,11,0.15); color: #d97706;">Pending ({{ $counts['pending'] }})</a>
        <a href="{{ route('admin.commerce.reviews', ['status' => 'approved']) }}" class="btn-action" style="background: rgba(16,185,129,0.15); color: #10b981;">Approved ({{ $counts['approved'] }})</a>
    </div>
</div>

<div class="panel-card">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Product</th>
                <th>Author & Verified</th>
                <th>Rating</th>
                <th>Review Content</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $rev)
                <tr>
                    <td style="font-weight: 700; max-width: 180px;">{{ $rev->product->name ?? 'Product' }}</td>
                    <td>
                        <div><strong>{{ $rev->user->first_name ?? 'Guest' }}</strong></div>
                        @if($rev->verified_purchase)
                            <span class="badge-verified"><i data-lucide="check" style="width:11px;display:inline;"></i> Verified</span>
                        @endif
                    </td>
                    <td>
                        <span style="color: #f59e0b; font-weight: 700;">★ {{ $rev->rating }}/5</span>
                    </td>
                    <td style="max-width: 300px;">
                        @if(!empty($rev->title))
                            <div style="font-weight: 700; margin-bottom: 2px;">{{ $rev->title }}</div>
                        @endif
                        <div style="font-size: 13px; color: var(--text-secondary);">{{ Str::limit($rev->review, 140) }}</div>
                    </td>
                    <td>
                        <span class="badge-status badge-{{ $rev->status }}">{{ $rev->status }}</span>
                    </td>
                    <td style="color: var(--text-secondary); font-size: 12px;">{{ $rev->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            @if($rev->status !== 'approved')
                                <form action="{{ route('admin.commerce.reviews.update_status', $rev->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn-action btn-success">Approve</button>
                                </form>
                            @endif
                            @if($rev->status !== 'rejected')
                                <form action="{{ route('admin.commerce.reviews.update_status', $rev->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn-action btn-danger">Reject</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">No reviews found in this filter queue.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
