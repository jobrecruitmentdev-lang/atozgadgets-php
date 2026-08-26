@extends('account.layout')

@section('account_content')
<style>
    .address-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    @media (min-width: 600px) {
        .address-form-grid {
            grid-template-columns: 1fr 1fr;
        }
        .address-col-full {
            grid-column: span 2;
        }
    }
    
    .address-input {
        width: 100%;
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.09);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }
    .address-input:focus {
        border-color: var(--accent);
    }
    
    .addresses-card-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    @media (min-width: 600px) {
        .addresses-card-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }
</style>

<div class="content-header" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
    <div>
        <h1 class="content-title">Saved Addresses</h1>
        <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Manage your primary delivery locations.</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('addAddressForm').style.display = 'block'; document.getElementById('addAddressForm').scrollIntoView({behavior: 'smooth'});" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px;">
        <i data-lucide="plus" style="width: 16px; height: 16px;"></i> <span>Add New</span>
    </button>
</div>

@if(session('success'))
    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #22c55e; padding: 14px 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
        {{ session('success') }}
    </div>
@endif

<div class="card-dark" id="addAddressForm" style="display: none; border-color: rgba(201, 169, 98, 0.4); margin-bottom: 28px;">
    <h3 style="margin-bottom: 18px; font-size: 18px; font-weight: 700; color: #fff;">Add New Delivery Address</h3>
    <form action="{{ route('account.addresses.save') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
        @csrf
        <div class="address-form-grid">
            <div class="address-col-full">
                <input type="text" name="address_line_1" placeholder="Street Address *" required class="address-input">
            </div>
            <div>
                <input type="text" name="city" placeholder="City *" required class="address-input">
            </div>
            <div>
                <input type="text" name="state" placeholder="State / Province *" required class="address-input">
            </div>
            <div>
                <input type="text" name="postal_code" placeholder="Postal Code *" required class="address-input">
            </div>
            <div>
                <select name="country" required class="address-input" style="background: #141416;">
                    <option value="US" selected>United States (US)</option>
                    <option value="CA">Canada (CA)</option>
                    <option value="GB">United Kingdom (UK)</option>
                    <option value="AU">Australia (AU)</option>
                    <option value="DE">Germany (DE)</option>
                    <option value="FR">France (FR)</option>
                    <option value="IT">Italy (IT)</option>
                    <option value="ES">Spain (ES)</option>
                    <option value="NL">Netherlands (NL)</option>
                    <option value="NZ">New Zealand (NZ)</option>
                </select>
            </div>
            <div class="address-col-full">
                <label style="display: flex; align-items: center; gap: 10px; color: var(--text-secondary); font-size: 14px; cursor: pointer;">
                    <input type="checkbox" name="is_default" value="1" style="accent-color: var(--accent); width: 16px; height: 16px;"> Set as default delivery address
                </label>
            </div>
        </div>
        <div style="display: flex; gap: 12px; margin-top: 8px;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Save Address</button>
            <button type="button" class="btn" style="background: rgba(255,255,255,0.08); color: #fff; padding: 10px 18px;" onclick="document.getElementById('addAddressForm').style.display = 'none'">Cancel</button>
        </div>
    </form>
</div>

<div class="addresses-card-grid">
    @forelse($addresses as $address)
        <div class="card-dark" style="position: relative; margin-bottom: 0; padding: 22px;">
            @if($address->is_default)
                <span style="position: absolute; top: 16px; right: 16px; background: rgba(201,169,98,0.15); color: var(--accent); border: 1px solid rgba(201,169,98,0.3); padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Default</span>
            @endif
            <div style="font-weight: 700; font-size: 15px; color: #fff; margin-bottom: 6px;">{{ $user->first_name }} {{ $user->last_name }}</div>
            <div style="color: var(--text-secondary); line-height: 1.6; font-size: 13.5px; margin-bottom: 16px;">
                {{ $address->address_line_1 }}<br>
                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                <strong style="color: #fff;">{{ $address->country }}</strong>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: rgba(255,255,255,0.02); border-radius: 18px; border: 1px dashed rgba(255,255,255,0.1);">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(201,169,98,0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <i data-lucide="map-pin" style="width: 24px; height: 24px;"></i>
            </div>
            <h4 style="margin-bottom: 6px; font-size: 17px; font-weight: 600; color: #fff;">No Addresses Saved</h4>
            <p style="color: var(--text-secondary); font-size: 13.5px; margin-bottom: 20px;">Save your delivery addresses for seamless 1-click checkouts.</p>
            <button class="btn btn-primary" onclick="document.getElementById('addAddressForm').style.display = 'block'; document.getElementById('addAddressForm').scrollIntoView({behavior: 'smooth'});" style="padding: 10px 20px; font-size: 13px;">
                + Add New Address
            </button>
        </div>
    @endforelse
</div>
@endsection
