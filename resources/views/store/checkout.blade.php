@extends('layouts.store')

@section('title', 'Secure Checkout - AtoZGadgets')

@section('content')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency=USD" defer></script>
<style>
    .checkout-layout { display: flex; flex-direction: column; gap: 48px; margin-top: 40px; }
    @media (min-width: 1024px) { .checkout-layout { flex-direction: row; } }
    
    .checkout-form { flex-grow: 1; }
    .order-summary { width: 100%; flex-shrink: 0; }
    @media (min-width: 1024px) { .order-summary { width: 380px; position: sticky; top: 100px; height: max-content; } }

    /* Steps */
    .step-indicator { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px; font-size: 14px; }
    .step-btn { background: none; border: none; font-weight: 500; font-size: 14px; color: var(--text-secondary); }
    .step-btn.active { color: var(--text-primary); }

    /* Forms */
    .form-section h2 { font-size: 20px; font-weight: 700; margin-bottom: 24px; }
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px; }
    @media (min-width: 640px) { .form-grid { grid-template-columns: 1fr 1fr; } }
    
    .input-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
    .input-group input, .input-group select { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--glass-border); background: var(--bg-color); color: var(--text-primary); font-size: 14px; outline: none; transition: border-color 0.3s; }
    .input-group input:focus, .input-group select:focus { border-color: var(--accent); }

    /* Payment Methods */
    .payment-option { border: 2px solid #3b82f6; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 8px; background: rgba(59,130,246,0.05); margin-bottom: 16px; }
    .payment-header { display: flex; justify-content: space-between; align-items: center; }
    .payment-title { font-weight: 700; font-size: 16px; color:#3b82f6; }
    .payment-desc { font-size: 12px; color: var(--text-secondary); }

    /* Order Summary block */
    .summary-card { background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 16px; padding: 24px; }
    .summary-item { display: flex; gap: 12px; margin-bottom: 16px; }
    .summary-item img { width: 56px; height: 56px; border-radius: 8px; object-fit: cover; background: #000; }
    .item-info { flex-grow: 1; }
    .item-info p { font-size: 14px; font-weight: 500; margin-bottom: 4px; }
    .item-info span { font-size: 12px; color: var(--text-secondary); }
    .item-price { font-size: 14px; font-weight: 600; }

    .summary-totals { border-top: 1px solid var(--glass-border); padding-top: 16px; margin-top: 8px; }
    .total-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; }
    .total-row.final { font-size: 18px; font-weight: 700; border-top: 1px solid var(--glass-border); padding-top: 16px; margin-top: 4px; }
    .free-text { color: #10b981; font-weight: 600; }

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
            <div class="payment-option">
                <div class="payment-header">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="payment-title"><i data-lucide="shield-check" style="display:inline; width:20px; vertical-align:middle; margin-right:4px;"></i> PayPal</span>
                    </div>
                </div>
                <p class="payment-desc">Fast, secure checkout via PayPal.</p>
            </div>
            
            <div id="paypal-button-container" style="margin-top: 24px;"></div>
            
            <p style="font-size:12px; color:var(--text-secondary); text-align:center; margin-top:16px;">
                By placing your order you agree to our <a href="{{ route('store.terms') }}" style="color:var(--accent);">Terms</a> and <a href="{{ route('store.privacy') }}" style="color:var(--accent);">Privacy Policy</a>.
            </p>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="order-summary" data-aos="fade-left">
        <div class="summary-card">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Order Summary</h2>
            <div style="max-height: 300px; overflow-y: auto; padding-right: 8px;">
                @php $subtotal = 29.99; @endphp
                <div class="summary-item">
                    <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=400&q=80" alt="Watch">
                    <div class="item-info">
                        <p>Ultra Smart Watch Series 9</p>
                        <span>Qty: 1</span>
                    </div>
                    <div class="item-price">$29.99</div>
                </div>
            </div>

            <div class="summary-totals">
                <div class="total-row"><span style="color:var(--text-secondary);">Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                <div class="total-row">
                    <span style="color:var(--text-secondary);">Shipping</span>
                    @if($subtotal >= 30) <span class="free-text">FREE</span>
                    @else <span>$5.99</span> @php $subtotal += 5.99; @endphp @endif
                </div>
                <div class="total-row final"><span>Total</span><span>${{ number_format($subtotal, 2) }}</span></div>
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

        // Variable to ensure PayPal is rendered only once
        let isPayPalRendered = false;

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

                    // Initialize PayPal SDK ONLY when the container is visible
                    if(typeof paypal !== 'undefined' && !isPayPalRendered) {
                        isPayPalRendered = true;
                        paypal.Buttons({
                            createOrder: function(data, actions) {
                                return fetch("{{ route('payment.paypal.create') }}", {
                                    method: "post",
                                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" }
                                }).then(res => res.json()).then(orderData => {
                                    if (orderData.error) throw new Error(orderData.error);
                                    return orderData.id;
                                });
                            },
                            onApprove: function(data, actions) {
                                return fetch("{{ route('payment.paypal.capture') }}", {
                                    method: "post",
                                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json", "Accept": "application/json" },
                                    body: JSON.stringify({ paypal_order_id: data.orderID })
                                }).then(res => res.json()).then(orderData => {
                                    if (orderData.success) {
                                        localStorage.removeItem('atoz_shipping_cache'); // Clear cache on success
                                        window.location.href = orderData.redirect;
                                    } else {
                                        alert('Payment capture failed.');
                                    }
                                });
                            }
                        }).render('#paypal-button-container');
                    }
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
