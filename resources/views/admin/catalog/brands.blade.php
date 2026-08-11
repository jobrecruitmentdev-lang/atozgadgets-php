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
    <button class="btn-primary" onclick="openAddBrand()">
        <i data-lucide="plus" style="width:16px;"></i> Add Brand
    </button>
</div>

@if(session('success'))
    <div style="padding: 12px 16px; background: rgba(16,185,129,0.1); color: #059669; border-radius: 8px; margin-top: 20px; border: 1px solid rgba(16,185,129,0.2);">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="padding: 12px 16px; background: rgba(239,68,68,0.1); color: #ef4444; border-radius: 8px; margin-top: 20px; border: 1px solid rgba(239,68,68,0.2);">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li style="font-size: 13px;">{{ $error }}</li>
            @endforeach
        </ul>
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
                        <button type="button" style="background:none; border:none; color: var(--accent); cursor:pointer; padding: 6px; display: inline-flex; align-items: center;" onclick="openEditBrand({{ json_encode($brand) }})" title="Edit Brand">
                            <i data-lucide="edit" style="width:16px;"></i>
                        </button>
                        <button type="button" style="background:none; border:none; color: #ef4444; cursor:pointer; padding: 6px; display: inline-flex; align-items: center;" onclick="showBrandDeleteConfirm({{ $brand->id }}, '{{ addslashes($brand->name) }}', {{ $brand->products_count ?? 0 }})" title="Delete Brand">
                            <i data-lucide="trash-2" style="width:16px;"></i>
                        </button>
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

    function openEditBrand(brand) {
        const modal = document.getElementById('brandModal');
        const title = modal.querySelector('.modal-header h3');
        const form = modal.querySelector('form');
        
        // Change action and method
        form.action = `/admin/catalog/brands/${brand.id}`;
        
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);
        } else {
            methodInput.value = 'PUT';
        }
        
        title.innerText = 'Edit Brand';
        form.querySelector('button[type="submit"]').innerText = 'Update Brand';
        
        // Fill fields
        form.querySelector('input[name="name"]').value = brand.name;
        form.querySelector('input[name="logo"]').value = brand.logo || '';
        
        modal.style.display = 'flex';
    }

    function openAddBrand() {
        const modal = document.getElementById('brandModal');
        const title = modal.querySelector('.modal-header h3');
        const form = modal.querySelector('form');
        
        // Change action and remove _method
        form.action = "{{ route('admin.catalog.brands.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.remove();
        }
        
        title.innerText = 'Add New Brand';
        form.querySelector('button[type="submit"]').innerText = 'Save Brand';
        
        // Clear fields
        form.querySelector('input[name="name"]').value = '';
        form.querySelector('input[name="logo"]').value = '';
        
        modal.style.display = 'flex';
    }

    function showBrandDeleteConfirm(id, name, productsCount) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteForm').action = `/admin/catalog/brands/${id}`;
        
        const title = document.getElementById('deleteModalTitle');
        const message = document.getElementById('deleteModalMessage');
        const forceInput = document.getElementById('deleteForceInput');
        const submitBtn = document.getElementById('deleteSubmitBtn');

        if (productsCount > 0) {
            title.innerText = '⚠️ Cascade Delete Brand';
            message.innerHTML = `Brand <strong>${name}</strong> is associated with <strong>${productsCount} product(s)</strong>.<br><br>Deleting it will <strong>PERMANENTLY ERASE</strong> the brand and all associated products! Are you absolutely sure?`;
            forceInput.value = '1';
            submitBtn.innerText = 'Delete All';
        } else {
            title.innerText = 'Confirm Delete';
            message.innerHTML = `Are you sure you want to delete brand <strong>${name}</strong>?`;
            forceInput.value = '0';
            submitBtn.innerText = 'Delete Brand';
        }

        modal.style.display = 'flex';
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }
</script>

<!-- Custom Delete Modal -->
<div class="modal-overlay" id="deleteConfirmModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">
    <div class="modal-card" style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 420px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 id="deleteModalTitle" style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Confirm Delete</h3>
            <button onclick="closeDeleteConfirm()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <div id="deleteModalMessage" style="margin-bottom: 24px; color: var(--text-secondary); font-size: 14px; line-height: 1.5;">
            Are you sure you want to delete <strong id="deleteBrandName" style="color: var(--text-primary);"></strong>?
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <input type="hidden" name="force" id="deleteForceInput" value="0">
            <div style="display:flex; justify-content:flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteConfirm()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer; font-weight: 600; font-size: 13px;">Cancel</button>
                <button type="submit" id="deleteSubmitBtn" class="btn btn-primary" style="background: #ef4444; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Delete Brand</button>
            </div>
        </form>
    </div>
</div>
@endsection
