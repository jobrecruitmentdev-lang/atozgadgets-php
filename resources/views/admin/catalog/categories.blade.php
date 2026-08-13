@extends('layouts.admin')

@section('title', 'Categories - AtoZGadgets Admin')

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
    .form-group input, .form-group textarea { padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary); outline: none; }
</style>

<!-- Ponytail: Select2 via CDN for lazy hierarchical dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 8px; border: 1px solid var(--border-color); height: 38px; padding: 4px;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Categories ({{ $categories->count() }})</h1>
    <button class="btn-primary" onclick="openAddCategory()">
        <i data-lucide="plus" style="width:16px;"></i> Add Category
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
                <th>Category Name</th>
                <th>Slug</th>
                <th>Products Count</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody id="categoryTableBody">
            @forelse($categories as $cat)
                <tr id="category-row-{{ $cat->id }}">
                    <td style="font-weight: 600;">{{ $cat->full_path }}</td>
                    <td style="font-family: monospace; font-size: 12px; color: var(--text-secondary);">{{ $cat->slug }}</td>
                    <td>{{ $cat->products_count }} products</td>
                    <td><span class="badge-status">{{ ucfirst($cat->status ?? 'active') }}</span></td>
                    <td style="text-align: right; display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
                        <a href="{{ route('admin.catalog.products', ['category_id' => $cat->id]) }}" style="display:inline-flex; align-items:center; gap:4px; padding: 6px 12px; border-radius:6px; background: rgba(37,99,235,0.1); color: var(--accent); font-size:12px; font-weight:600; text-decoration:none;" title="View & Filter Category Products">
                            <i data-lucide="filter" style="width:14px;"></i> Products ({{ $cat->products_count }})
                        </a>

                        <button type="button" style="background:none; border:none; color: var(--accent); cursor:pointer; padding: 6px; display: inline-flex; align-items: center;" onclick="openEditCategory({{ json_encode($cat) }})" title="Edit Category">
                            <i data-lucide="edit" style="width:16px;"></i>
                        </button>

                        @if($cat->products_count > 0)
                            <button type="button" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #ef4444; padding: 6px 10px; border-radius: 6px; cursor:pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" onclick="showCategoryDeleteConfirm({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->products_count }})">
                                <i data-lucide="trash-2" style="width:14px;"></i> Delete All
                            </button>
                        @else
                            <button type="button" style="background:none; border:none; color: #ef4444; cursor:pointer; padding: 6px; display: inline-flex; align-items: center;" onclick="showCategoryDeleteConfirm({{ $cat->id }}, '{{ addslashes($cat->name) }}', 0)" title="Delete Category">
                                <i data-lucide="trash-2" style="width:16px;"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: var(--text-secondary);">No categories configured yet. Click "Add Category" to create one.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Add New Category</h3>
            <button onclick="closeModal()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <form action="{{ route('admin.catalog.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" required placeholder="e.g. Smartwatches">
            </div>
            <div class="form-group">
                <label>Parent Category (Optional)</label>
                <select name="parent_id" id="categoryParentSelect" style="width: 100%;">
                    <option value="">-- None (Top Level) --</option>
                    @foreach($categories as $parentCat)
                        <option value="{{ $parentCat->id }}">{{ $parentCat->full_path }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Category description..."></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeModal()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('categoryModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('categoryModal').style.display = 'none'; }

    function openEditCategory(category) {
        const modal = document.getElementById('categoryModal');
        const title = modal.querySelector('.modal-header h3');
        const form = modal.querySelector('form');
        
        // Change action and method
        form.action = `/admin/catalog/categories/${category.id}`;
        
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
        
        title.innerText = 'Edit Category';
        form.querySelector('button[type="submit"]').innerText = 'Update Category';
        
        // Fill fields
        form.querySelector('input[name="name"]').value = category.name;
        
        const parentSelect = form.querySelector('select[name="parent_id"]');
        if (parentSelect) {
            parentSelect.value = category.parent_id || '';
            
            // Disable its own option so it can't be its own parent
            Array.from(parentSelect.options).forEach(opt => {
                opt.disabled = (opt.value == category.id);
            });
        }

        form.querySelector('textarea[name="description"]').value = category.description || '';
        
        modal.style.display = 'flex';
    }

    function openAddCategory() {
        const modal = document.getElementById('categoryModal');
        const title = modal.querySelector('.modal-header h3');
        const form = modal.querySelector('form');
        
        // Change action and remove _method
        form.action = "{{ route('admin.catalog.categories.store') }}";
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.remove();
        }
        
        title.innerText = 'Add New Category';
        form.querySelector('button[type="submit"]').innerText = 'Save Category';
        
        // Clear fields
        form.querySelector('input[name="name"]').value = '';
        
        const parentSelect = form.querySelector('select[name="parent_id"]');
        if (parentSelect) {
            parentSelect.value = '';
            // Re-enable all options
            Array.from(parentSelect.options).forEach(opt => {
                opt.disabled = false;
            });
        }

        form.querySelector('textarea[name="description"]').value = '';
        
        modal.style.display = 'flex';
    }

    function showCategoryDeleteConfirm(id, name, productsCount) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteForm').action = `/admin/catalog/categories/${id}`;
        
        const title = document.getElementById('deleteModalTitle');
        const message = document.getElementById('deleteModalMessage');
        const forceInput = document.getElementById('deleteForceInput');
        const submitBtn = document.getElementById('deleteSubmitBtn');

        if (productsCount > 0) {
            title.innerText = '⚠️ Cascade Delete Category';
            message.innerHTML = `Category <strong>${name}</strong> contains <strong>${productsCount} product(s)</strong>.<br><br>Deleting it will <strong>PERMANENTLY ERASE</strong> the category and all associated products! Are you absolutely sure?`;
            forceInput.value = '1';
            submitBtn.innerText = 'Delete All';
        } else {
            title.innerText = 'Confirm Delete';
            message.innerHTML = `Are you sure you want to delete category <strong>${name}</strong>?`;
            forceInput.value = '0';
            submitBtn.innerText = 'Delete Category';
        }

        modal.style.display = 'flex';
    }

    function closeDeleteConfirm() {
        document.getElementById('deleteConfirmModal').style.display = 'none';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Ponytail: Lazy Select2 initialization and AJAX form intercepts
    $(document).ready(function() {
        $('#categoryParentSelect').select2({ placeholder: "-- None (Top Level) --", allowClear: true });
        
        const categoryModal = document.getElementById('categoryModal');
        const form = categoryModal.querySelector('form');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true; btn.innerText = 'Saving...';
            
            try {
                const fd = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                
                if (res.ok) {
                    window.location.reload(); // Lazy DOM update: fast reload beats writing 50 lines of dynamic row insertion.
                } else {
                    alert('Validation failed or error occurred.');
                }
            } catch (err) {
                alert('Network error');
            }
            btn.disabled = false; btn.innerText = 'Save Category';
        });

        const deleteForm = document.getElementById('deleteForm');
        deleteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('deleteSubmitBtn');
            btn.disabled = true; btn.innerText = 'Deleting...';
            
            try {
                const fd = new FormData(deleteForm);
                const res = await fetch(deleteForm.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                if (res.ok) {
                    window.location.reload();
                } else {
                    alert('Cannot delete this category.');
                }
            } catch (err) {
                alert('Network error');
            }
            btn.disabled = false; btn.innerText = 'Delete Category';
        });
    });
</script>

<!-- Custom Delete Modal -->
<div class="modal-overlay" id="deleteConfirmModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(4px);">
    <div class="modal-card" style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 420px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 id="deleteModalTitle" style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Confirm Delete</h3>
            <button onclick="closeDeleteConfirm()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        <div id="deleteModalMessage" style="margin-bottom: 24px; color: var(--text-secondary); font-size: 14px; line-height: 1.5;">
            Are you sure you want to delete <strong id="deleteCategoryName" style="color: var(--text-primary);"></strong>?
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <input type="hidden" name="force" id="deleteForceInput" value="0">
            <div style="display:flex; justify-content:flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteConfirm()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer; font-weight: 600; font-size: 13px;">Cancel</button>
                <button type="submit" id="deleteSubmitBtn" class="btn btn-primary" style="background: #ef4444; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Delete Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
