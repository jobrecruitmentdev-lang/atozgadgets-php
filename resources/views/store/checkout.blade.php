@extends('layouts.store')

@section('title', 'Secure Checkout - AtoZGadgets')

@section('content')
<style>
    .checkout-layout { display: flex; flex-direction: column; gap: 48px; margin-top: 40px; }
    @media (min-width: 1024px) { .checkout-layout { flex-direction: row; } }
    
    .checkout-form { flex-grow: 1; }
    .order-summary { width: 100%; flex-shrink: 0; }
    @media (min-width: 1024px) { .order-summary { width: 380px; position: sticky; top: 100px; height: max-content; } }

    /* Steps */
    .step-indicator { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; border-bottom: 1px solid var(--glass-border); padding-bottom: 16px; font-size: 14px; }
    .step-btn { background: none; border: none; font-weight: 500; font-size: 14px; color: var(--text-secondary); cursor: pointer; }
    .step-btn.active { color: var(--text-primary); }

    /* Forms */
    .form-section h2 { font-size: 20px; font-weight: 700; margin-bottom: 24px; }
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px; }
    @media (min-width: 640px) { .form-grid { grid-template-columns: 1fr 1fr; } }
    
    .input-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
    .input-group input, .input-group select { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--glass-border); background: var(--bg-color); color: var(--text-primary); font-size: 14px; outline: none; transition: border-color 0.3s; }
    .input-group input:focus, .input-group select:focus { border-color: var(--accent); }

    /* Payment Methods */
    .payment-option { cursor: pointer; border: 2px solid var(--glass-border); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 8px; transition: all 0.3s; background: rgba(255,255,255,0.02); margin-bottom: 16px; }
    .payment-option:hover { border-color: rgba(255,255,255,0.2); }
    .payment-option.selected { border-color: var(--accent); background: rgba(201,169,98,0.05); }
    .payment-option.selected-paypal { border-color: #3b82f6; background: rgba(59,130,246,0.05); }
    
    .payment-header { display: flex; justify-content: space-between; align-items: center; }
    .payment-title { font-weight: 700; font-size: 16px; }
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
</style>

<div class="checkout-layout">
    <div class="checkout-form">
        <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 40px;" data-aos="fade-right">Checkout</h1>
        
        <div class="step-indicator">
            <button class="step-btn active" id="btn-step-1">1. Shipping</button>
            <span style="color: var(--text-secondary);">/</span>
            <button class="step-btn" id="btn-step-2" disabled>2. Payment</button>
        </div>

        <form id="checkout-form" action="{{ route('store.checkout') }}" method="POST">
            @csrf
            
            <!-- Step 1: Shipping -->
            <div id="step-1" class="form-section" data-aos="fade-in">
                <h2>Shipping Information</h2>
                
                <div class="form-grid">
                    <div class="input-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="input-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" placeholder="+1 234 567 8900" required>
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 20px;">
                    <label>Address Line 1 *</label>
                    <input type="text" name="address1" required>
                </div>

                <div class="input-group" style="margin-bottom: 20px;">
                    <label>Address Line 2</label>
                    <input type="text" name="address2" placeholder="Apt, Suite, Floor (optional)">
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>City *</label>
                        <input type="text" name="city" required>
                    </div>
                    <div class="input-group">
                        <label>State / Province</label>
                        <input type="text" name="state">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>Postal Code *</label>
                        <input type="text" name="postal_code" required>
                    </div>
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

                <button type="button" class="btn btn-primary" id="continue-btn" style="padding: 16px 32px; margin-top: 16px;">
                    Continue to Payment &rarr;
                </button>
            </div>

            <!-- Step 2: Payment -->
            <div id="step-2" class="form-section hidden" data-aos="fade-in">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
                    <h2>Select Payment Method</h2>
                    <button type="button" class="step-btn" id="back-btn" style="color:var(--accent);">
                        &larr; Edit Shipping
                    </button>
                </div>

                <input type="hidden" name="payment_method" id="payment_method_input" value="paypal">

                <div class="payment-option selected-paypal" id="opt-paypal">
                    <div class="payment-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="payment-title" style="color:#3b82f6;">PayPal</span>
                            <span style="font-size:10px; background:#3b82f6; color:#fff; padding:2px 8px; border-radius:12px; font-weight:700;">Priority #1</span>
                        </div>
                        <input type="radio" name="gateway_dummy" checked style="accent-color:#3b82f6;">
                    </div>
                    <p class="payment-desc">Fast, secure global checkout with PayPal balance, Credit Card, or Pay Later.</p>
                </div>

                <div class="payment-option" id="opt-razorpay">
                    <div class="payment-header">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="payment-title">Credit / Debit Card / UPI</span>
                        </div>
                        <input type="radio" name="gateway_dummy" style="accent-color:var(--accent);">
                    </div>
                    <p class="payment-desc">Powered by Razorpay. Supports Visa, Mastercard, Amex, UPI & Net Banking.</p>
                </div>

                <button type="submit" class="btn" id="pay-btn" style="width:100%; padding: 20px; font-size:18px; font-weight:700; background:#2563eb; color:#fff; border:none; margin-top: 24px; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);">
                    <i data-lucide="shield-check" style="display:inline; width:20px; vertical-align:middle; margin-right:8px;"></i>
                    Pay Now
                </button>
                
                <p style="font-size:12px; color:var(--text-secondary); text-align:center; margin-top:16px;">
                    By placing your order you agree to our <a href="{{ route('store.terms') }}" style="color:var(--accent);">Terms</a> and <a href="{{ route('store.privacy') }}" style="color:var(--accent);">Privacy Policy</a>.
                </p>
            </div>
        </form>
    </div>

    <div class="order-summary" data-aos="fade-left">
        <div class="summary-card">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 24px;">Order Summary</h2>
            
            <div style="max-height: 300px; overflow-y: auto; padding-right: 8px;">
                @php 
                    $subtotal = 0; 
                    // Mock cart item for demo since session cart logic is backend dependent
                    $subtotal += 29.99;
                @endphp
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
                <div class="total-row">
                    <span style="color:var(--text-secondary);">Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="total-row">
                    <span style="color:var(--text-secondary);">Shipping</span>
                    @if($subtotal >= 30)
                        <span class="free-text">FREE</span>
                    @else
                        <span>$5.99</span>
                        @php $subtotal += 5.99; @endphp
                    @endif
                </div>
                @if($subtotal >= 30)
                    <p style="font-size: 12px; color: #10b981; margin-top:-8px; margin-bottom:12px;">Free shipping applied (order over $30)</p>
                @endif
                <div class="total-row final">
                    <span>Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
            </div>
            
            <div style="display:flex; align-items:center; gap:8px; margin-top:20px; font-size:12px; color:var(--text-secondary);">
                <i data-lucide="shield-check" style="color:#10b981; width:16px;"></i>
                <span>SSL encrypted · Secure Checkout</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const btnStep1 = document.getElementById('btn-step-1');
        const btnStep2 = document.getElementById('btn-step-2');
        const continueBtn = document.getElementById('continue-btn');
        const backBtn = document.getElementById('back-btn');

        const optPaypal = document.getElementById('opt-paypal');
        const optRazorpay = document.getElementById('opt-razorpay');
        const payBtn = document.getElementById('pay-btn');
        const paymentInput = document.getElementById('payment_method_input');

        // Form Validation & Step Switch
        continueBtn.addEventListener('click', () => {
            const requiredInputs = step1.querySelectorAll('input[required]');
            let valid = true;
            requiredInputs.forEach(input => {
                if (!input.value) { valid = false; input.style.borderColor = 'red'; }
                else { input.style.borderColor = 'var(--glass-border)'; }
            });
            if (valid) {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                btnStep1.classList.remove('active');
                btnStep2.classList.add('active');
                btnStep2.disabled = false;
            }
        });

        backBtn.addEventListener('click', () => {
            step2.classList.add('hidden');
            step1.classList.remove('hidden');
            btnStep2.classList.remove('active');
            btnStep1.classList.add('active');
        });

        // Payment Toggle
        optPaypal.addEventListener('click', () => {
            optPaypal.classList.add('selected-paypal');
            optRazorpay.classList.remove('selected');
            optPaypal.querySelector('input').checked = true;
            paymentInput.value = 'paypal';
            payBtn.style.background = '#2563eb';
            payBtn.innerHTML = '<i data-lucide="shield-check" style="display:inline; width:20px; vertical-align:middle; margin-right:8px;"></i> Pay with PayPal';
            lucide.createIcons();
        });

        optRazorpay.addEventListener('click', () => {
            optRazorpay.classList.add('selected');
            optPaypal.classList.remove('selected-paypal');
            optRazorpay.querySelector('input').checked = true;
            paymentInput.value = 'razorpay';
            payBtn.style.background = 'var(--accent)';
            payBtn.style.color = '#000';
            payBtn.innerHTML = '<i data-lucide="shield-check" style="display:inline; width:20px; vertical-align:middle; margin-right:8px;"></i> Pay with Razorpay';
            lucide.createIcons();
        });
    });
</script>
@endsection
