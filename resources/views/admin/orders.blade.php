@extends('layouts.admin')

@section('title', 'Orders - AtoZGadgets Admin')

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
</style>

<div class="page-header">
    <h1 class="page-title">Orders</h1>
    <div style="display: flex; gap: 8px; align-items: center;">
        <a href="{{ route('admin.orders', ['tab' => 'all']) }}" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; {{ ($tab ?? 'all') === 'all' ? 'background: var(--accent); color: #0a0a0c;' : 'background: rgba(255,255,255,0.05); color: var(--text-secondary); border: 1px solid var(--border-color);' }}">
            All ({{ $counts['all'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['tab' => 'paid']) }}" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; {{ ($tab ?? 'all') === 'paid' ? 'background: #10b981; color: #0a0a0c;' : 'background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.3);' }}">
            ● Paid ({{ $counts['paid'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['tab' => 'pending']) }}" style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; {{ ($tab ?? 'all') === 'pending' ? 'background: #f59e0b; color: #0a0a0c;' : 'background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3);' }}">
            ● Pending / Abandoned ({{ $counts['pending'] ?? 0 }})
        </a>
    </div>
</div>

<div class="data-card">
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.orders') }}" style="display:flex; gap:12px; width:100%;">
            <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
            <div class="search-wrapper" style="flex-grow:1;">
                <i data-lucide="search"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders, customers, or emails...">
            </div>
            <button type="submit" class="btn-outline" style="padding: 10px 18px; background: rgba(201, 169, 98, 0.1); border-color: var(--accent); color: var(--accent);">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.orders', ['tab' => $tab ?? 'all']) }}" class="btn-outline" style="color:var(--text-secondary); text-decoration:none;">Clear</a>
            @endif
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment</th>
                <th>CJ Dropshipping</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-weight: 700;">#{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    @php
                        $custName = 'Guest Customer';
                        if ($order->user) {
                            $custName = $order->user->first_name . ' ' . $order->user->last_name;
                        } elseif ($order->orderAddress && !empty($order->orderAddress->first_name)) {
                            $custName = $order->orderAddress->first_name . ' ' . ($order->orderAddress->last_name ?? '');
                        } elseif ($order->shipping_address) {
                            $sa = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true);
                            if (!empty($sa['first_name'])) {
                                $custName = $sa['first_name'] . ' ' . ($sa['last_name'] ?? '');
                            }
                        }
                    @endphp
                    <td style="font-weight: 600;">{{ $custName }}</td>
                    <td>{{ $order->items->count() }} items</td>
                    <td style="font-weight: 700; color: var(--accent);">${{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        @php
                            $status = strtolower($order->status ?? 'pending');
                            $bgColor = 'rgba(128,128,128,0.1)';
                            $fgColor = 'var(--text-primary)';
                            if ($status == 'completed' || $status == 'delivered') {
                                $bgColor = 'rgba(16, 185, 129, 0.1)';
                                $fgColor = '#059669';
                            } elseif ($status == 'shipped' || $status == 'processing') {
                                $bgColor = 'rgba(37, 99, 235, 0.1)';
                                $fgColor = 'var(--accent)';
                            } elseif ($status == 'cancelled') {
                                $bgColor = 'rgba(239, 68, 68, 0.1)';
                                $fgColor = '#ef4444';
                            }
                        @endphp
                        <span style="padding: 4px 8px; border-radius: 4px; background: {{ $bgColor }}; color: {{ $fgColor }}; font-size: 12px; font-weight: 600;">{{ ucfirst($order->status ?? 'Pending') }}</span>
                    </td>
                    <td>
                        @php
                            $pStatus = strtolower($order->payment_status ?? 'pending');
                            $pBg = $pStatus === 'paid' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)';
                            $pFg = $pStatus === 'paid' ? '#059669' : '#d97706';
                        @endphp
                        <span style="padding: 4px 8px; border-radius: 4px; background: {{ $pBg }}; color: {{ $pFg }}; font-size: 11px; font-weight: 700; text-transform: uppercase;">{{ $order->payment_status ?? 'Pending' }}</span>
                    </td>
                    <td>
                        @if($order->cjOrder)
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; background:rgba(201,169,98,0.15); color:#c9a962; padding:3px 8px; border-radius:6px; border:1px solid rgba(201,169,98,0.3);">
                                <i data-lucide="check" style="width:12px;"></i> {{ $order->cjOrder->cj_order_id }}
                            </span>
                        @elseif(in_array(strtolower($order->payment_status ?? ''), ['paid', 'completed', 'success']))
                            <form action="{{ route('admin.orders.fulfill_cj', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" style="padding: 4px 10px; border-radius: 6px; background: linear-gradient(135deg, #c9a962, #b89851); color: #0a0a0c; font-size: 11px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" title="Dispatch to CJ Dropshipping">
                                    <i data-lucide="send" style="width:12px;"></i> Fulfill on CJ
                                </button>
                            </form>
                        @else
                            <span style="font-size:11px; color:var(--text-secondary);">Awaiting Payment</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="{{ route('admin.orders.show', $order->id) }}" style="display:inline-flex; align-items:center; background:rgba(37,99,235,0.1); border:1px solid rgba(37,99,235,0.2); color:var(--accent); border-radius:6px; padding: 4px 8px; font-size:12px; font-weight:700; margin-right:4px;" title="Open Control Tower">
                            <i data-lucide="shield-check" style="width:14px; margin-right:4px;"></i> Tower
                        </a>
                        <button onclick="openOrderModal({{ json_encode($order) }})" style="background:transparent; border:none; color:var(--text-secondary); cursor:pointer; padding: 4px;" title="Quick Preview"><i data-lucide="eye" style="width:16px;"></i></button>
                        <button type="button" style="background:transparent; border:none; color:#ef4444; cursor:pointer; padding: 4px;" onclick="showOrderDeleteConfirm({{ $order->id }})" title="Cancel Order"><i data-lucide="trash-2" style="width:16px;"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-state">No orders found for the current filters.</td>
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

<!-- Order Detail Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal-card" style="max-width: 600px;">
        <div class="modal-header">
            <h3>Order Details #<span id="modalOrderId"></span></h3>
            <button onclick="closeOrderModal()" style="background:none; border:none; cursor:pointer; color: var(--text-secondary);"><i data-lucide="x" style="width:20px;"></i></button>
        </div>
        
        <div style="margin-bottom: 20px; font-size: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
                <p style="margin-bottom:4px; color: var(--text-secondary); font-size:12px; font-weight:600; text-transform:uppercase;">Customer Info</p>
                <p><strong id="modalCustomerName"></strong></p>
                <p id="modalCustomerEmail" style="color: var(--text-secondary);"></p>
            </div>
            <div>
                <p style="margin-bottom:4px; color: var(--text-secondary); font-size:12px; font-weight:600; text-transform:uppercase;">Order Date</p>
                <p id="modalOrderDate"></p>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <p style="margin-bottom:8px; color: var(--text-secondary); font-size:12px; font-weight:600; text-transform:uppercase;">Order Items</p>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px;">
                <table id="modalItemsTable" style="width: 100%;">
                    <thead>
                        <tr style="background: rgba(128,128,128,0.02);">
                            <th style="padding: 8px 12px; font-size: 11px; border-bottom: 1px solid var(--border-color);">Item</th>
                            <th style="padding: 8px 12px; font-size: 11px; text-align: center; border-bottom: 1px solid var(--border-color);">Qty</th>
                            <th style="padding: 8px 12px; font-size: 11px; text-align: right; border-bottom: 1px solid var(--border-color);">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div style="margin-bottom: 20px; background: rgba(128,128,128,0.05); padding: 12px; border-radius: 8px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px;">
            <div>Subtotal:</div><div style="text-align: right; font-weight: 600;" id="modalSubtotal"></div>
            <div>Tax:</div><div style="text-align: right; font-weight: 600;" id="modalTax"></div>
            <div>Shipping:</div><div style="text-align: right; font-weight: 600;" id="modalShipping"></div>
            <div style="font-weight: 700;">Total Amount:</div><div style="text-align: right; font-weight: 700; color: var(--accent);" id="modalTotal"></div>
        </div>

        <form id="orderStatusForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Order Status</label>
                    <select name="order_status" id="modalOrderStatus" style="padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary);">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status" id="modalPaymentStatus" style="padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-primary);">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeOrderModal()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer;">Cancel</button>
                <button type="submit" style="padding: 10px 16px; border-radius: 8px; background: var(--accent); color: #fff; border:none; cursor:pointer; font-weight:600;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openOrderModal(order) {
        document.getElementById('modalOrderId').innerText = order.id;
        document.getElementById('modalCustomerName').innerText = order.user ? (order.user.first_name + ' ' + order.user.last_name) : 'Guest';
        document.getElementById('modalCustomerEmail').innerText = order.user ? order.user.email : '';
        document.getElementById('modalOrderDate').innerText = new Date(order.created_at).toLocaleDateString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric'
        });

        // Set totals
        document.getElementById('modalSubtotal').innerText = '$' + parseFloat(order.subtotal || 0).toFixed(2);
        document.getElementById('modalTax').innerText = '$' + parseFloat(order.tax_amount || 0).toFixed(2);
        document.getElementById('modalShipping').innerText = '$' + parseFloat(order.shipping_charge || 0).toFixed(2);
        document.getElementById('modalTotal').innerText = '$' + parseFloat(order.total_amount || 0).toFixed(2);

        // Set status
        document.getElementById('modalOrderStatus').value = order.status || 'pending';
        document.getElementById('modalPaymentStatus').value = order.payment_status || 'pending';
        
        // Set form action
        document.getElementById('orderStatusForm').action = `/admin/orders/${order.id}`;

        // Fill Items Table
        const tbody = document.getElementById('modalItemsTable').querySelector('tbody');
        tbody.innerHTML = '';
        if (order.items && order.items.length > 0) {
            order.items.forEach(item => {
                const prodName = item.product ? item.product.name : (item.product_name || 'Product #' + (item.product_id || ''));
                const itemTotal = parseFloat(item.total_price || item.subtotal || ((item.unit_price || 0) * (item.quantity || 1))).toFixed(2);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 8px 12px; border-bottom: 1px solid var(--border-color);">${prodName}</td>
                    <td style="padding: 8px 12px; text-align: center; border-bottom: 1px solid var(--border-color);">${item.quantity}</td>
                    <td style="padding: 8px 12px; text-align: right; border-bottom: 1px solid var(--border-color); font-weight: 600;">$${itemTotal}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="3" style="text-align:center; padding:12px; color:var(--text-secondary);">No items in this order.</td></tr>`;
        }

        document.getElementById('orderModal').style.display = 'flex';
        lucide.createIcons();
    }

    function closeOrderModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

    function showOrderDeleteConfirm(id) {
        const modal = document.getElementById('deleteConfirmModal');
        document.getElementById('deleteOrderId').innerText = id;
        document.getElementById('deleteForm').action = `/admin/orders/${id}`;
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
            Are you sure you want to delete order <strong style="color: var(--text-primary);">#<span id="deleteOrderId"></span></strong>? This action cannot be undone.
        </div>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display:flex; justify-content:flex-end; gap: 12px;">
                <button type="button" onclick="closeDeleteConfirm()" style="padding: 10px 16px; border-radius: 8px; border: 1px solid var(--border-color); background:transparent; color: var(--text-secondary); cursor:pointer; font-weight: 600; font-size: 13px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; cursor: pointer;">Delete Order</button>
            </div>
        </form>
    </div>
</div>
@endsection
