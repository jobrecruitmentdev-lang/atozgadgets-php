@extends('layouts.admin')

@section('title', 'Customers - AtoZGadgets Admin')

@section('content')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 700; color: var(--text-primary); }
    
    .btn-outline { padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background: transparent; color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
    .btn-outline:hover { background: rgba(128,128,128,0.1); }

    .data-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
    
    .filter-bar { padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; gap: 16px; }
    .search-wrapper { position: relative; flex-grow: 1; max-width: 400px; }
    .search-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); width: 16px; }
    .search-wrapper input { width: 100%; padding: 12px 16px 12px 42px; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(128,128,128,0.05); color: var(--text-primary); font-size: 14px; outline: none; }
    .search-wrapper input:focus { border-color: var(--accent); }

    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; padding: 16px 24px; font-size: 13px; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); background: rgba(128,128,128,0.05); }
    td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
    
    .empty-state { text-align: center; padding: 64px 24px; color: var(--text-secondary); font-size: 14px; }

    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px); }
    .modal-card { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 450px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: 12px; font-weight: 600; color: var(--text-secondary); }
    .form-group input, .form-group select { padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); outline: none; }
</style>

<div class="page-header">
    <h1 class="page-title">Customers</h1>
    <button class="btn-outline">
        <i data-lucide="filter" style="width:16px;"></i> Advanced Filters
    </button>
</div>

<div class="data-card">
    <div class="filter-bar">
        <div class="search-wrapper">
            <i data-lucide="search"></i>
            <input type="text" placeholder="Search customers...">
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>#{{ $customer->id }}</td>
                    <td style="font-weight: 600;">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->orders()->whereIn('payment_status', ['paid', 'completed', 'success'])->count() }}</td>
                    <td style="font-weight: 700; color: var(--accent);">${{ number_format($customer->orders()->whereIn('payment_status', ['paid', 'completed', 'success'])->sum('total_amount'), 2) }}</td>
                    <td>
                        @if($customer->is_active)
                            <span style="padding: 4px 8px; border-radius: 4px; background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 12px; font-weight: 600;">Active</span>
                        @else
                            <span style="padding: 4px 8px; border-radius: 4px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 12px; font-weight: 600;">Suspended</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button onclick="openCustomerModal({{ json_encode($customer) }})" style="background:transparent; border:none; color:var(--accent); cursor:pointer; padding: 4px;"><i data-lucide="edit" style="width:16px;"></i></button>
                        <button type="button" style="background:transparent; border:none; color:#ef4444; cursor:pointer; padding: 4px;" onclick="showCustomerDeleteConfirm({{ $customer->id }}, '{{ addslashes($customer->first_name) }} {{ addslashes($customer->last_name) }}')" title="Delete Customer"><i data-lucide="trash-2" style="width:16px;"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No customers found for the current filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
    <div style="padding: 12px 16px; background: rgba(239,68,68,0.1); color: #dc2626; border-radius: 8px; margin-top: 20px; border: 1px solid rgba(239,68,68,0.2);">
        {{ session('error') }}
    </div>
@endif

<!-- Edit Customer Modal -->
<div class="modal-overlay" id="customerModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Edit Customer Profile</h3>
            <button onclick="closeCustomerModal()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <form id="customerEditForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" id="modalCustomerFirstName" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" id="modalCustomerLastName" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="modalCustomerEmail" required>
            </div>
            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobile" id="modalCustomerMobile">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label>System Role</label>
                    <select name="role_id" id="modalCustomerRole" required>
                        <option value="1">Super Admin</option>
                        <option value="2">Admin</option>
                        <option value="3">Customer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" id="modalCustomerStatus" required>
                        <option value="1">Active</option>
                        <option value="0">Suspended</option>
                    </select>
                </div>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeCustomerModal()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer;">Cancel</button>
                <button type="submit" style="padding: 10px 16px; border-radius: 8px; background: var(--accent); color: #fff; border:none; cursor:pointer; font-weight:600;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCustomerModal(customer) {
        document.getElementById('modalCustomerFirstName').value = customer.first_name || '';
        document.getElementById('modalCustomerLastName').value = customer.last_name || '';
        document.getElementById('modalCustomerEmail').value = customer.email || '';
        document.getElementById('modalCustomerMobile').value = customer.mobile || '';
        document.getElementById('modalCustomerRole').value = customer.role_id || '3';
        document.getElementById('modalCustomerStatus').value = customer.is_active ? '1' : '0';

        document.getElementById('customerEditForm').action = `/admin/customers/${customer.id}`;
        document.getElementById('customerModal').style.display = 'flex';
        lucide.createIcons();
    }

    function closeCustomerModal() {
        document.getElementById('customerModal').style.display = 'none';
    }

    function showCustomerDeleteConfirm(id, name) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteCustomerName').innerText = name;
        document.getElementById('deleteForm').action = `/admin/customers/${id}`;
        modal.style.display = 'flex';
        lucide.createIcons();
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }
</script>

<!-- Custom Delete Modal -->
<div class="modal-overlay" id="deleteConfirmModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">
    <div class="modal-card" style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 400px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Confirm Delete</h3>
            <button onclick="closeDeleteConfirm()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <div style="margin-bottom: 24px; color: var(--text-secondary); font-size: 14px; line-height: 1.5;">
            Are you sure you want to delete customer <strong id="deleteCustomerName" style="color: var(--text-primary);"></strong>? This action cannot be undone.
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display:flex; justify-content:flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteConfirm()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer; font-weight: 600; font-size: 13px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Delete Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
