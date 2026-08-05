@extends('layouts.admin')

@section('title', 'Brands - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .btn-primary { padding: 10px 16px; border-radius: 8px; background: var(--accent); color: #fff; font-size: 13px; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; }
    .btn-primary:hover { opacity: 0.9; }

    .data-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 16px 24px; font-size: 13px; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); background: rgba(128,128,128,0.05); }
    td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    
    .badge-status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(16, 185, 129, 0.1); color: #059669; }
    
    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px); }
    .modal-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 450px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: 12px; font-weight: 600; color: var(--text-secondary); }
    .form-group input { padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); outline: none; }
</style>

<div class="page-header">
    <h1 class="page-title">Brands ({{ $brands->count() }})</h1>
    <button class="btn-primary" onclick="openModal()">
        <i data-lucide="plus" style="width:16px;"></i> Add Brand
    </button>
</div>

@if(session('success'))
    <div style="padding: 12px 16px; background: rgba(16,185,129,0.1); color: #059669; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16,185,129,0.2);">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="padding: 12px 16px; background: rgba(239,68,68,0.1); color: #dc2626; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(239,68,68,0.2);">
        {{ session('error') }}
    </div>
@endif

<div class="data-card">
    <table>
        <thead>
            <tr>
                <th>Brand Name</th>
                <th>Slug</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
                <tr>
                    <td style="font-weight: 600;">{{ $brand->name }}</td>
                    <td style="font-family: monospace; font-size: 12px; color: var(--text-secondary);">{{ $brand->slug }}</td>
                    <td><span class="badge-status">{{ ucfirst($brand->status ?? 'active') }}</span></td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.catalog.brands.destroy', $brand->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none; border:none; color: #ef4444; cursor:pointer;" onclick="return confirm('Delete this brand?')">
                                <i data-lucide="trash-2" style="width:16px;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding: 40px; color: var(--text-secondary);">No brands configured yet. Click "Add Brand" to create one.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal-overlay" id="brandModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Add New Brand</h3>
            <button onclick="closeModal()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <form action="{{ route('admin.catalog.brands.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Brand Name</label>
                <input type="text" name="name" required placeholder="e.g. Sony">
            </div>
            <div class="form-group">
                <label>Logo URL (optional)</label>
                <input type="url" name="logo" placeholder="https://...">
            </div>
            <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeModal()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-primary">Save Brand</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('brandModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('brandModal').style.display = 'none'; }
</script>
@endsection
