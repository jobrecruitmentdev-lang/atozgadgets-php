@extends('account.layout')

@section('account_content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <h1 class="content-title">Addresses</h1>
        <p style="color: var(--text-secondary);">Manage your shipping addresses.</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('addAddressForm').style.display = 'block'">+ Add New</button>
</div>

@if(session('success'))
    <div style="background: rgba(16,185,129,0.1); color: #34d399; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
        {{ session('success') }}
    </div>
@endif

<div class="card-dark" id="addAddressForm" style="display: none; border-color: var(--accent);">
    <h3 style="margin-bottom: 16px;">Add New Address</h3>
    <form action="{{ route('account.addresses.save') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
            <input type="text" name="address_line_1" placeholder="Address Line 1" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: #fff;">
            <input type="text" name="city" placeholder="City" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: #fff;">
            <input type="text" name="state" placeholder="State/Province" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: #fff;">
            <input type="text" name="postal_code" placeholder="Postal Code" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: #fff;">
            <input type="text" name="country" placeholder="Country" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; color: #fff;">
            <label style="display: flex; align-items: center; gap: 8px; color: var(--text-secondary);">
                <input type="checkbox" name="is_default" value="1"> Set as default address
            </label>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">Save Address</button>
            <button type="button" class="btn" style="background: rgba(255,255,255,0.1);" onclick="document.getElementById('addAddressForm').style.display = 'none'">Cancel</button>
        </div>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
    @forelse($addresses as $address)
        <div class="card-dark" style="position: relative;">
            @if($address->is_default)
                <span style="position: absolute; top: 16px; right: 16px; background: rgba(201,169,98,0.2); color: var(--accent); padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase;">Default</span>
            @endif
            <h4 style="margin-bottom: 8px; font-size: 16px;">{{ $user->first_name }} {{ $user->last_name }}</h4>
            <p style="color: var(--text-secondary); line-height: 1.6; font-size: 14px; margin-bottom: 16px;">
                {{ $address->address_line_1 }}<br>
                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                {{ $address->country }}
            </p>
            <button style="background: none; border: none; color: var(--accent); cursor: pointer; padding: 0; font-size: 14px;">Edit</button>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: rgba(255,255,255,0.02); border-radius: 16px; border: 1px dashed var(--glass-border);">
            <p style="color: var(--text-secondary);">No addresses saved yet.</p>
        </div>
    @endforelse
</div>
@endsection
