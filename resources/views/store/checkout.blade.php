@extends('layouts.store')

@section('title', 'Secure Checkout - AtoZGadgets')

@section('content')
@php
    $paypalMode = \App\Models\Setting::get('paypal_mode', 'sandbox');
    $paypalClientId = ($paypalMode === 'live')
        ? (\App\Models\Setting::get('paypal_live_client_id') ?: \App\Models\Setting::get('paypal_client_id', config('paypal.client_id')))
        : (\App\Models\Setting::get('paypal_sandbox_client_id') ?: \App\Models\Setting::get('paypal_client_id', config('paypal.client_id')));
@endphp
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency=USD&disable-funding=card,paylater" defer></script>
<style>
    .checkout-layout { display: flex; flex-direction: column; gap: 48px; margin-top: 40px; }
    @media (min-width: 1024px) { .checkout-layout { flex-direction: row; } }
    
    .checkout-form { flex-grow: 1; min-width: 0; }
    .order-summary { width: 100%; flex-shrink: 0; min-width: 0; }
    @media (min-width: 1024px) { .order-summary { width: 380px; position: sticky; top: 100px; height: max-content; } }

    /* Steps */
    .step-indicator { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px; font-size: 14px; flex-wrap: wrap; }
    .step-btn { background: none; border: none; font-weight: 500; font-size: 14px; color: var(--text-secondary); cursor: pointer; transition: color 0.2s; min-height: 36px; }
    .step-btn.active { color: var(--accent); font-weight: 700; }

    /* Forms */
    .form-section h2 { font-size: 20px; font-weight: 700; margin-bottom: 24px; color: var(--text-primary); }
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px; }
    @media (min-width: 640px) { .form-grid { grid-template-columns: 1fr 1fr; } }
    
    .input-group { min-width: 0; }
    .input-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary); }
    .input-group input, .input-group select { width: 100%; padding: 13px 16px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(15, 15, 20, 0.85); color: var(--text-primary); font-size: 16px; outline: none; transition: all 0.2s; box-sizing: border-box; }
    .input-group input:focus, .input-group select:focus { border-color: var(--accent); box-shadow: 0 0 0 2px rgba(201, 169, 98, 0.2); }

    @media (max-width: 480px) {
        .checkout-layout { gap: 28px; margin-top: 20px; }
        .summary-card { padding: 16px; }
        .payment-option { padding: 16px; }
        .step-indicator { gap: 8px; font-size: 13px; margin-bottom: 24px; }
    }

    /* Payment Methods */
    .payment-option { border: 1px solid rgba(201, 169, 98, 0.35); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 8px; background: rgba(201, 169, 98, 0.06); margin-bottom: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .payment-header { display: flex; justify-content: space-between; align-items: center; }
    .payment-title { font-weight: 700; font-size: 16px; color: var(--accent); display: flex; align-items: center; gap: 8px; }
    .payment-desc { font-size: 13px; color: var(--text-secondary); }

    /* Order Summary block */
    .summary-card { background: rgba(20, 20, 28, 0.8); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    .summary-item { display: flex; gap: 12px; margin-bottom: 16px; align-items: center; }
    .summary-item img { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; background: #000; border: 1px solid var(--glass-border); }
    .item-info { flex-grow: 1; }
    .item-info p { font-size: 14px; font-weight: 600; margin-bottom: 4px; color: var(--text-primary); }
    .item-info span { font-size: 12px; color: var(--text-secondary); }
    .item-price { font-size: 14px; font-weight: 700; color: var(--accent); }

    .summary-totals { border-top: 1px solid var(--glass-border); padding-top: 16px; margin-top: 8px; }
    .total-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-secondary); }
    .total-row.final { font-size: 18px; font-weight: 700; border-top: 1px solid var(--glass-border); padding-top: 16px; margin-top: 4px; color: var(--text-primary); }
    .free-text { color: #10b981; font-weight: 700; }

    /* Utilities */
    .hidden { display: none; }
    
    /* Loader */
    .loader { display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 1s ease-in-out infinite; margin-left: 10px; }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="checkout-layout">
    <div class="checkout-form">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 40px;" data-aos="fade-right">Checkout</h1>
        
        <div class="step-indicator">
            <button class="step-btn active" id="btn-step-1">1. Shipping</button>
            <span style="color: var(--text-secondary);">/</span>
            <button class="step-btn" id="btn-step-1-5">2. Verification</button>
            <span style="color: var(--text-secondary);">/</span>
            <button class="step-btn" id="btn-step-2">3. Payment</button>
        </div>

        <!-- Step 1: Shipping Form -->
        <form id="shipping-form" data-aos="fade-in">
            @csrf
            <div id="step-1" class="form-section">
                <h2>Shipping Information</h2>
                <div class="form-grid">
                    <div class="input-group"><label>First Name *</label><input type="text" name="first_name" required></div>
                    <div class="input-group"><label>Last Name *</label><input type="text" name="last_name" required></div>
                </div>
                <div class="form-grid">
                    <div class="input-group"><label>Email *</label><input type="email" name="email" id="user-email" required></div>
                    <div class="input-group"><label>Phone *</label><input type="tel" name="phone" id="user-phone" placeholder="+1 234 567 8900" required></div>
                </div>
                <div class="input-group" style="margin-bottom: 20px;"><label>Address Line 1 *</label><input type="text" name="address1" required></div>
                <div class="input-group" style="margin-bottom: 20px;"><label>Address Line 2</label><input type="text" name="address2" placeholder="Apt, Suite (optional)"></div>
                <div class="form-grid">
                    <div class="input-group"><label>City *</label><input type="text" name="city" required></div>
                    <div class="input-group"><label>State / Province</label><input type="text" name="state"></div>
                </div>
                <div class="form-grid">
                    <div class="input-group"><label>Postal Code *</label><input type="text" name="postal_code" required></div>
                    <div class="input-group">
                        <label>Country *</label>
                        <select name="country" required>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="IN">India</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; align-items:center;">
                    <button type="button" class="btn btn-primary" id="save-shipping-btn" style="padding: 16px 32px; margin-top: 16px; display:flex; align-items:center;">
                        Verify & Continue <span class="loader" id="shipping-loader"></span>
                    </button>
                    <p id="shipping-error" style="color: #ef4444; margin-left: 15px; display:none;"></p>
                </div>
            </div>
        </form>

        <!-- Step 1.5: OTP Verification -->
        <div id="step-1-5" class="form-section hidden" data-aos="fade-in">
            <h2>Verify Your Details</h2>
            <p style="color:var(--text-secondary); margin-bottom: 20px;">We have sent a 6-digit OTP to your email (<span id="display-email" style="color:#fff;"></span>).</p>
            
            <div class="input-group" style="max-width: 300px; margin-bottom: 20px;">
                <label>Enter OTP Code *</label>
                <input type="text" id="otp-input" placeholder="123456" maxlength="6" style="font-size:24px; letter-spacing:8px; text-align:center;">
            </div>

            <div style="display:flex; align-items:center;">
                <button type="button" class="btn btn-primary" id="verify-otp-btn" style="padding: 16px 32px; display:flex; align-items:center;">
                    Confirm & Proceed <span class="loader" id="otp-loader"></span>
                </button>
                <p id="otp-error" style="color: #ef4444; margin-left: 15px; display:none;">Invalid OTP code.</p>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
                <button type="button" id="edit-shipping-btn" style="background:none; border:none; color:var(--accent); cursor:pointer; font-weight:500;">&larr; Edit Shipping Info</button>
                <button type="button" id="resend-otp-btn" style="background:none; border:none; color:var(--text-secondary); cursor:pointer; font-size: 14px;">Resend OTP</button>
            </div>
            <p id="resend-msg" style="color: #10b981; font-size: 12px; margin-top: 10px; display:none; text-align:right;">OTP resent successfully.</p>
        </div>

        <!-- Step 2: Payment -->
        <div id="step-2" class="form-section hidden" data-aos="fade-in">
            <h2>Select Payment Method</h2>

            <!-- Payment Method Tabs -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px;">
                <div id="tab-card-select" class="payment-tab active" onclick="switchPaymentMethod('card')" style="padding:16px; border-radius:14px; border:2px solid var(--accent); background:rgba(201, 169, 98, 0.1); cursor:pointer; text-align:center; transition:all 0.2s;">
                    <div style="font-weight:700; color:var(--accent); font-size:14px; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <i data-lucide="credit-card" style="width:18px;"></i> Credit / Debit Card
                    </div>
                    <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">Visa, Mastercard, Amex</div>
                </div>
                <div id="tab-paypal-select" class="payment-tab" onclick="switchPaymentMethod('paypal')" style="padding:16px; border-radius:14px; border:1px solid var(--glass-border); background:rgba(255, 255, 255, 0.03); cursor:pointer; text-align:center; transition:all 0.2s;">
                    <div style="font-weight:700; color:var(--text-primary); font-size:14px; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <i data-lucide="shield-check" style="width:18px;"></i> PayPal
                    </div>
                    <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">PayPal Wallet & Fast Pay</div>
                </div>
            </div>

            <!-- Card Payment Container -->
            <div id="card-payment-box">
                <form id="card-payment-form" onsubmit="handleCardPayment(event)">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <span style="font-size:13px; font-weight:600; color:var(--text-primary);">Card Details</span>
                        <button type="button" onclick="autoFillTestCard()" style="background:none; border:1px dashed var(--accent); color:var(--accent); font-size:11px; padding:4px 10px; border-radius:6px; cursor:pointer; font-weight:600;">
                            ⚡ Auto-Fill Test Card
                        </button>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Name on Card</label>
                        <input type="text" id="card_name" name="card_name" placeholder="John Doe" required style="width:100%; padding:12px 16px; border-radius:12px; border:1px solid var(--glass-border); background:rgba(15,15,20,0.85); color:#fff; font-size:14px;">
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="4035 7777 7777 7777" maxlength="19" required style="width:100%; padding:12px 16px; border-radius:12px; border:1px solid var(--glass-border); background:rgba(15,15,20,0.85); color:#fff; font-size:14px; letter-spacing:1px;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:20px;">
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Month</label>
                            <input type="text" id="exp_month" name="exp_month" placeholder="12" maxlength="2" required style="width:100%; padding:12px 16px; border-radius:12px; border:1px solid var(--glass-border); background:rgba(15,15,20,0.85); color:#fff; font-size:14px; text-align:center;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Year</label>
                            <input type="text" id="exp_year" name="exp_year" placeholder="28" maxlength="4" required style="width:100%; padding:12px 16px; border-radius:12px; border:1px solid var(--glass-border); background:rgba(15,15,20,0.85); color:#fff; font-size:14px; text-align:center;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:var(--text-primary);">CVV / CVC</label>
                            <input type="password" id="cvv" name="cvv" placeholder="123" maxlength="4" required style="width:100%; padding:12px 16px; border-radius:12px; border:1px solid var(--glass-border); background:rgba(15,15,20,0.85); color:#fff; font-size:14px; text-align:center;">
                        </div>
                    </div>

                    <p id="card-error" style="color:#ef4444; font-size:13px; margin-bottom:14px; display:none; padding:10px 14px; border-radius:8px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);"></p>

                    <button type="submit" id="pay-card-btn" class="btn-primary" style="width:100%; padding:15px; border-radius:12px; background:var(--accent); color:#0a0a0c; font-weight:800; font-size:15px; border:none; cursor:pointer; display:flex; justify-content:center; align-items:center; gap:8px; box-shadow:0 4px 20px rgba(201,169,98,0.3); transition:all 0.2s;">
                        <span>Pay Securely with Card</span>
                        <div class="loader" id="card-pay-loader" style="border-top-color:#000;"></div>
                    </button>
                </form>
            </div>

            <!-- PayPal Container -->
            <div id="paypal-payment-box" style="display:none;">
                <div class="payment-option">
                    <div class="payment-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="payment-title"><i data-lucide="shield-check" style="display:inline; width:20px; vertical-align:middle; margin-right:4px;"></i> Official PayPal</span>
                        </div>
                    </div>
                    <p class="payment-desc">Fast, secure checkout via official PayPal popup.</p>
                </div>
                
                <div id="paypal-button-container" style="margin-top: 20px;"></div>
            </div>
            
            <p style="font-size:12px; color:var(--text-secondary); text-align:center; margin-top:20px;">
                🔒 256-bit Encrypted Checkout · By placing your order you agree to our <a href="{{ route('store.terms') }}" style="color:var(--accent);">Terms</a> and <a href="{{ route('store.privacy') }}" style="color:var(--accent);">Privacy Policy</a>.
            </p>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="order-summary" data-aos="fade-left">
        <div class="summary-card">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Order Summary</h2>
            <div style="max-height: 300px; overflow-y: auto; padding-right: 8px;">
                @php 
                    $subtotal = 0; 
                @endphp
                @forelse($cart as $id => $item)
                    @php 
                        $itemPrice = (float)($item['price'] ?? 0);
                        $itemQty = (int)($item['quantity'] ?? 1);
                        $subtotal += ($itemPrice * $itemQty);
                    @endphp
                    <div class="summary-item">
                        <img src="{{ $item['image'] ?? asset('favicon.png') }}" alt="{{ $item['name'] ?? 'Product' }}" onerror="this.src='{{ asset('favicon.png') }}'">
                        <div class="item-info">
                            <p style="font-weight:600; color:var(--text-primary);">{{ $item['name'] ?? 'Product' }}</p>
                            <span>Qty: {{ $itemQty }} &times; ${{ number_format($itemPrice, 2) }}</span>
                        </div>
                        <div class="item-price">${{ number_format($itemPrice * $itemQty, 2) }}</div>
                    </div>
                @empty
                    <p style="color:var(--text-secondary); font-size:14px; text-align:center; padding:20px 0;">No items in cart.</p>
                @endforelse
            </div>

            @php
                $shippingCost = ($subtotal >= 30 || $subtotal == 0) ? 0 : 5.99;
                $grandTotal = $subtotal + $shippingCost;
            @endphp

            <div class="summary-totals">
                <div class="total-row"><span style="color:var(--text-secondary);">Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                <div class="total-row">
                    <span style="color:var(--text-secondary);">Shipping</span>
                    @if($subtotal >= 30) <span class="free-text">FREE</span>
                    @elseif($subtotal > 0) <span>$5.99</span>
                    @else <span>$0.00</span>
                    @endif
                </div>
                <div class="total-row final"><span>Total</span><span style="color:var(--accent);">${{ number_format($grandTotal, 2) }}</span></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const step1 = document.getElementById('step-1');
        const step15 = document.getElementById('step-1-5');
        const step2 = document.getElementById('step-2');
        
        const btnStep1 = document.getElementById('btn-step-1');
        const btnStep15 = document.getElementById('btn-step-1-5');
        const btnStep2 = document.getElementById('btn-step-2');

        const saveShippingBtn = document.getElementById('save-shipping-btn');
        const verifyOtpBtn = document.getElementById('verify-otp-btn');
        const editShippingBtn = document.getElementById('edit-shipping-btn');
        const resendOtpBtn = document.getElementById('resend-otp-btn');
        const shippingForm = document.getElementById('shipping-form');
        
        // --- Caching System for Mobile Users ---
        // Auto-load saved shipping data
        const savedData = localStorage.getItem('atoz_shipping_cache');
        if (savedData) {
            try {
                const parsed = JSON.parse(savedData);
                Object.keys(parsed).forEach(key => {
                    const input = shippingForm.elements[key];
                    if (input && key !== '_token') input.value = parsed[key];
                });
            } catch(e) {}
        }

        // Auto-save shipping data on input
        shippingForm.addEventListener('input', () => {
            const formData = new FormData(shippingForm);
            const dataObj = {};
            formData.forEach((value, key) => dataObj[key] = value);
            localStorage.setItem('atoz_shipping_cache', JSON.stringify(dataObj));
        });
        
        // --- Step 1 -> Step 1.5 (Send OTP) ---
        saveShippingBtn.addEventListener('click', async () => {
            if(!shippingForm.checkValidity()) { shippingForm.reportValidity(); return; }

            document.getElementById('shipping-loader').style.display = 'inline-block';
            saveShippingBtn.disabled = true;
            document.getElementById('shipping-error').style.display = 'none';

            const formData = new FormData(shippingForm);
            
            try {
                const res = await fetch("{{ route('store.checkout.send-otp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                    body: formData
                });
                
                const data = await res.json();
                
                if(data.success) {
                    document.getElementById('display-email').innerText = document.getElementById('user-email').value;
                    if(data.dev_otp) {
                        const otpInput = document.getElementById('otp-input');
                        if (otpInput) otpInput.value = data.dev_otp;
                        const resendMsg = document.getElementById('resend-msg');
                        if (resendMsg) {
                            resendMsg.innerText = `[DEV Mode: Test OTP ${data.dev_otp} Auto-Filled (or use 123456)]`;
                            resendMsg.style.color = 'var(--accent)';
                            resendMsg.style.display = 'block';
                        }
                    }
                    step1.classList.add('hidden');
                    step15.classList.remove('hidden');
                    btnStep1.classList.remove('active');
                    btnStep15.classList.add('active');
                } else {
                    document.getElementById('shipping-error').innerText = data.error || 'Failed to generate OTP';
                    document.getElementById('shipping-error').style.display = 'block';
                }
            } catch (e) {
                document.getElementById('shipping-error').innerText = 'Network Error. Try again.';
                document.getElementById('shipping-error').style.display = 'block';
            }

            document.getElementById('shipping-loader').style.display = 'none';
            saveShippingBtn.disabled = false;
        });

        // Edit Shipping (Go back)
        editShippingBtn.addEventListener('click', () => {
            step15.classList.add('hidden');
            step1.classList.remove('hidden');
            btnStep15.classList.remove('active');
            btnStep1.classList.add('active');
        });

        // Resend OTP
        resendOtpBtn.addEventListener('click', async () => {
            if(!shippingForm.checkValidity()) return;
            
            resendOtpBtn.disabled = true;
            resendOtpBtn.innerText = 'Sending...';
            document.getElementById('resend-msg').style.display = 'none';

            try {
                const res = await fetch("{{ route('store.checkout.send-otp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                    body: new FormData(shippingForm)
                });
                
                const data = await res.json();
                if(res.status === 429) {
                    document.getElementById('resend-msg').innerText = 'Please wait a minute before resending.';
                    document.getElementById('resend-msg').style.color = '#ef4444';
                } else if(data.success) {
                    document.getElementById('resend-msg').innerText = 'OTP resent successfully!';
                    document.getElementById('resend-msg').style.color = '#10b981';
                } else {
                    document.getElementById('resend-msg').innerText = data.error || 'Failed to resend OTP.';
                    document.getElementById('resend-msg').style.color = '#ef4444';
                }
                document.getElementById('resend-msg').style.display = 'block';
            } catch (e) {
                document.getElementById('resend-msg').innerText = 'Network error.';
                document.getElementById('resend-msg').style.color = '#ef4444';
                document.getElementById('resend-msg').style.display = 'block';
            }

            setTimeout(() => {
                resendOtpBtn.disabled = false;
                resendOtpBtn.innerText = 'Resend OTP';
            }, 5000);
        });

        // Variables for PayPal Order resolution
        let currentInternalOrderId = null;
        let isPayPalRendered = false;

        function initPayPalButtons() {
            if (isPayPalRendered || !document.getElementById('paypal-button-container')) return;
            if (typeof paypal === 'undefined') {
                setTimeout(initPayPalButtons, 300);
                return;
            }

            isPayPalRendered = true;
            paypal.Buttons({
                createOrder: function(data, actions) {
                    const formData = new FormData(shippingForm);
                    const address = {};
                    formData.forEach((value, key) => address[key] = value);

                    return fetch("{{ route('payment.paypal.create') }}", {
                        method: "post",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
                        body: JSON.stringify({ address: address })
                    }).then(res => res.json()).then(orderData => {
                        if (!orderData || !orderData.id) {
                            const errMsg = orderData.error || orderData.message || (orderData.details && orderData.details[0] ? orderData.details[0].description : 'Server failed to create PayPal Order');
                            throw new Error(errMsg);
                        }
                        currentInternalOrderId = orderData.order_id;
                        return orderData.id;
                    });
                },
                onApprove: function(data, actions) {
                    return fetch("{{ route('payment.paypal.capture') }}", {
                        method: "post",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
                        body: JSON.stringify({ 
                            paypal_order_id: data.orderID,
                            order_id: currentInternalOrderId 
                        })
                    }).then(res => res.json()).then(orderData => {
                        if (orderData.success) {
                            localStorage.removeItem('atoz_shipping_cache'); // Clear cache on success
                            window.location.href = orderData.redirect;
                        } else {
                            alert('Payment capture failed: ' + (orderData.error || 'Unknown error'));
                        }
                    });
                },
                onError: function(err) {
                    console.error('PayPal Integration Error:', err);
                    alert('Payment Error: ' + (err.message || 'Unable to process PayPal payment. Please try again.'));
                }
            }).render('#paypal-button-container');
        }

        // Step 1.5 -> Step 2 (Verify OTP)
        verifyOtpBtn.addEventListener('click', async () => {
            const otp = document.getElementById('otp-input').value;
            if(otp.length !== 6) return;

            document.getElementById('otp-loader').style.display = 'inline-block';
            verifyOtpBtn.disabled = true;
            document.getElementById('otp-error').style.display = 'none';

            try {
                const res = await fetch("{{ route('store.checkout.verify-otp') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ otp: otp })
                });
                
                const data = await res.json();
                
                if(data.success) {
                    step15.classList.add('hidden');
                    step2.classList.remove('hidden');
                    btnStep15.classList.remove('active');
                    btnStep2.classList.add('active');

                    // Initialize PayPal SDK securely
                    initPayPalButtons();
                } else {
                    document.getElementById('otp-error').innerText = data.error || 'Invalid OTP code.';
                    document.getElementById('otp-error').style.display = 'block';
                }
            } catch (e) {
                document.getElementById('otp-error').innerText = 'Network error.';
                document.getElementById('otp-error').style.display = 'block';
            }

            document.getElementById('otp-loader').style.display = 'none';
            verifyOtpBtn.disabled = false;
        });

        // Card number auto-spacing
        const cardNumInput = document.getElementById('card_number');
        if (cardNumInput) {
            cardNumInput.addEventListener('input', (e) => {
                let val = e.target.value.replace(/\D/g, '');
                val = val.substring(0, 16);
                let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
                e.target.value = formatted;
            });
        }
    });

    // Switch Payment Method Tab
    function switchPaymentMethod(method) {
        const cardBox = document.getElementById('card-payment-box');
        const paypalBox = document.getElementById('paypal-payment-box');
        const tabCard = document.getElementById('tab-card-select');
        const tabPaypal = document.getElementById('tab-paypal-select');

        if (method === 'card') {
            cardBox.style.display = 'block';
            paypalBox.style.display = 'none';
            tabCard.style.border = '2px solid var(--accent)';
            tabCard.style.background = 'rgba(201, 169, 98, 0.1)';
            tabCard.querySelector('div').style.color = 'var(--accent)';
            tabPaypal.style.border = '1px solid var(--glass-border)';
            tabPaypal.style.background = 'rgba(255, 255, 255, 0.03)';
            tabPaypal.querySelector('div').style.color = 'var(--text-primary)';
        } else {
            cardBox.style.display = 'none';
            paypalBox.style.display = 'block';
            tabPaypal.style.border = '2px solid var(--accent)';
            tabPaypal.style.background = 'rgba(201, 169, 98, 0.1)';
            tabPaypal.querySelector('div').style.color = 'var(--accent)';
            tabCard.style.border = '1px solid var(--glass-border)';
            tabCard.style.background = 'rgba(255, 255, 255, 0.03)';
            tabCard.querySelector('div').style.color = 'var(--text-primary)';
        }
    }

    // Auto-fill Test Card Details
    function autoFillTestCard() {
        document.getElementById('card_name').value = 'John Doe';
        document.getElementById('card_number').value = '4035 7777 7777 7777';
        document.getElementById('exp_month').value = '12';
        document.getElementById('exp_year').value = '28';
        document.getElementById('cvv').value = '123';
    }

    // Handle Direct Card Payment Submission
    async function handleCardPayment(e) {
        e.preventDefault();
        const btn = document.getElementById('pay-card-btn');
        const loader = document.getElementById('card-pay-loader');
        const errorEl = document.getElementById('card-error');

        btn.disabled = true;
        loader.style.display = 'inline-block';
        errorEl.style.display = 'none';

        const form = document.getElementById('card-payment-form');
        const formData = new FormData(form);

        try {
            const res = await fetch("{{ route('payment.card') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                localStorage.removeItem('atoz_shipping_cache');
                window.location.href = data.redirect;
            } else {
                errorEl.innerText = data.error || (data.message || 'Card payment processing failed.');
                errorEl.style.display = 'block';
                btn.disabled = false;
                loader.style.display = 'none';
            }
        } catch (err) {
            errorEl.innerText = 'Network error during card payment. Please try again.';
            errorEl.style.display = 'block';
            btn.disabled = false;
            loader.style.display = 'none';
        }
    }
</script>
@endsection
