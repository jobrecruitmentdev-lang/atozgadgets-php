@extends('layouts.store')

@section('title', 'Secure Checkout - AtoZGadgets')

@section('content')
@php
    $paypalMode = \App\Models\Setting::get('paypal_mode', 'sandbox');
    $paypalClientId = ($paypalMode === 'live')
        ? (\App\Models\Setting::get('paypal_live_client_id') ?: \App\Models\Setting::get('paypal_client_id', config('paypal.client_id')))
        : (\App\Models\Setting::get('paypal_sandbox_client_id') ?: \App\Models\Setting::get('paypal_client_id', config('paypal.client_id')));
    $storeCurrency = \App\Models\Setting::get('currency', 'USD');
@endphp
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency={{ $storeCurrency }}" defer></script>
<style>
    .checkout-layout { display: flex; flex-direction: column; gap: 48px; margin-top: 40px; }
    @media (min-width: 1024px) { .checkout-layout { flex-direction: row; } }
    
    .checkout-form { flex-grow: 1; min-width: 0; }
    .order-summary { width: 100%; flex-shrink: 0; min-width: 0; }
    @media (min-width: 1024px) { .order-summary { width: 380px; position: sticky; top: 100px; height: max-content; } }

    .checkout-title { font-size: 24px; }
    @media (min-width: 768px) { .checkout-title { font-size: 32px; margin-bottom: 36px !important; } }

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
        <h1 class="checkout-title" style="font-weight: 800; margin-bottom: 24px;" data-aos="fade-right">Checkout</h1>
        
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
                        <label>Country / Region *</label>
                        <select name="country" required>
                            <option value="US" selected>United States (US)</option>
                            <option value="CA">Canada (CA)</option>
                            <option value="GB">United Kingdom (UK)</option>
                            <option value="AU">Australia (AU)</option>
                            <option value="DE">Germany (DE)</option>
                            <option value="FR">France (FR)</option>
                            <option value="IT">Italy (IT)</option>
                            <option value="ES">Spain (ES)</option>
                            <option value="NL">Netherlands (NL)</option>
                            <option value="SE">Sweden (SE)</option>
                            <option value="NO">Norway (NO)</option>
                            <option value="CH">Switzerland (CH)</option>
                            <option value="NZ">New Zealand (NZ)</option>
                            <option value="AE">United Arab Emirates (UAE)</option>
                            <option value="SG">Singapore (SG)</option>
                            <option value="IE">Ireland (IE)</option>
                            <option value="JP">Japan (JP)</option>
                            <option value="CN">China (CN)</option>
                            <option value="IN">India (IN)</option>
                        </select>
                    </div>
                </div>

                <!-- Real-Time Logistics & Eligibility Box -->
                <div id="shipping-eligibility-box" style="margin-top: 15px; margin-bottom: 15px; padding: 14px 16px; border-radius: 12px; font-size: 13px; display: none; transition: all 0.3s ease;">
                    <div id="eligibility-content"></div>
                </div>

                <div style="display:flex; align-items:center;">
                    <button type="button" class="btn btn-primary" id="save-shipping-btn" style="padding: 16px 32px; margin-top: 8px; display:flex; align-items:center;">
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
            
            <div class="payment-option">
                <div class="payment-header">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="payment-title"><i data-lucide="shield-check" style="display:inline; width:20px; vertical-align:middle; margin-right:4px;"></i> Secure Checkout</span>
                    </div>
                </div>
                <p class="payment-desc">Pay safely with your PayPal account or Debit / Credit Card via PayPal's encrypted hosted fields.</p>
            </div>
            
            <div id="paypal-button-container" style="margin-top: 20px; min-height: 150px;"></div>
            
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
                $freeThreshold = (float)\App\Models\Setting::get('free_shipping_threshold', 50.00);
                $stdRate = (float)\App\Models\Setting::get('standard_shipping_rate', 5.99);
                $shippingCost = ($subtotal >= $freeThreshold || $subtotal == 0) ? 0 : $stdRate;
                $grandTotal = $subtotal + $shippingCost;
            @endphp

            <div class="summary-totals">
                <div class="total-row"><span style="color:var(--text-secondary);">Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                <div class="total-row">
                    <span style="color:var(--text-secondary);">Shipping</span>
                    @if($subtotal >= $freeThreshold) <span class="free-text">FREE</span>
                    @elseif($subtotal > 0) <span>${{ number_format($stdRate, 2) }}</span>
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

        // --- Real-Time Country & Logistics Eligibility Engine ---
        const countrySelect = document.querySelector('select[name="country"]');
        const eligibilityBox = document.getElementById('shipping-eligibility-box');
        const eligibilityContent = document.getElementById('eligibility-content');

        async function verifyCountryEligibility(countryCode) {
            if (!eligibilityBox || !eligibilityContent) return;

            eligibilityBox.style.display = 'block';
            eligibilityBox.style.background = 'rgba(255, 255, 255, 0.03)';
            eligibilityBox.style.border = '1px solid var(--border-color)';
            eligibilityContent.innerHTML = '<span style="color: var(--text-secondary);"><i data-lucide="loader" style="width:14px;height:14px;display:inline;vertical-align:middle;animation:spin 1s linear infinite;"></i> Checking carrier logistics & warehouse routes...</span>';
            if (window.lucide) lucide.createIcons();

            try {
                const res = await fetch("{{ route('store.checkout.eligibility') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ country: countryCode })
                });

                const data = await res.json();

                if (data.eligible) {
                    eligibilityBox.style.background = 'rgba(34, 197, 94, 0.08)';
                    eligibilityBox.style.border = '1px solid rgba(34, 197, 94, 0.25)';
                    eligibilityContent.innerHTML = `
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="color: #22c55e; font-size: 16px; margin-top: 1px;">✓</div>
                            <div>
                                <div style="font-weight: 600; color: #22c55e; margin-bottom: 2px;">Delivery Available to ${data.country_name || data.country}</div>
                                <div style="color: var(--text-secondary); font-size: 12.5px;">
                                    <strong>Carrier:</strong> ${data.carrier || 'Express Direct Line'} · 
                                    <strong>ETA:</strong> <span style="color: var(--accent); font-weight: 600;">${data.eta || '7–12 Business Days'}</span> · 
                                    <strong>Hub:</strong> ${data.warehouse || 'Priority Distribution Hub'}
                                </div>
                            </div>
                        </div>
                    `;
                    saveShippingBtn.disabled = false;
                    document.getElementById('shipping-error').style.display = 'none';
                } else {
                    eligibilityBox.style.background = 'rgba(239, 68, 68, 0.08)';
                    eligibilityBox.style.border = '1px solid rgba(239, 68, 68, 0.25)';
                    eligibilityContent.innerHTML = `
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div style="color: #ef4444; font-size: 16px; margin-top: 1px;">⚠️</div>
                            <div>
                                <div style="font-weight: 600; color: #ef4444; margin-bottom: 2px;">Shipping Unavailable to ${data.country_name || data.country}</div>
                                <div style="color: #fca5a5; font-size: 12px;">${data.message || 'One or more items in your cart cannot be delivered to this destination due to regional carrier restrictions.'}</div>
                            </div>
                        </div>
                    `;
                    saveShippingBtn.disabled = true;
                }
            } catch (e) {
                eligibilityBox.style.display = 'none';
            }
        }

        if (countrySelect) {
            countrySelect.addEventListener('change', (e) => verifyCountryEligibility(e.target.value));
            // Run check on initial load
            setTimeout(() => verifyCountryEligibility(countrySelect.value || 'US'), 100);
        }

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
                    }).then(async res => {
                        const orderData = await res.json().catch(() => ({ error: 'Invalid JSON response from server (Status ' + res.status + ')' }));
                        if (orderData && orderData.success) {
                            localStorage.removeItem('atoz_shipping_cache'); // Clear cache on success
                            window.location.href = orderData.redirect;
                        } else {
                            const errorMsg = orderData.error || orderData.message || (orderData.errors ? Object.values(orderData.errors).flat().join(', ') : 'Unknown error');
                            console.error('PayPal Capture Failed:', orderData);
                            alert('Payment capture failed: ' + errorMsg);
                        }
                    }).catch(err => {
                        console.error('Capture request network error:', err);
                        alert('Payment capture error: ' + err.message);
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
    });
</script>
@endsection
