@extends('layouts.store')

@section('title', 'Shipping & Payment Policy | AtoZGadgets')

@section('content')
<style>
    .policy-hero {
        text-align: center;
        max-width: 750px;
        margin: 0 auto 3.5rem;
    }
    .policy-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(201, 169, 98, 0.12);
        color: var(--accent);
        border: 1px solid rgba(201, 169, 98, 0.3);
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1.5rem;
    }
    .policy-title {
        font-size: 2.25rem;
        line-height: 1.2;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.02em;
        margin-bottom: 1rem;
    }
    @media (min-width: 768px) {
        .policy-title {
            font-size: 3rem;
        }
    }
    .policy-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* Highlight Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 3.5rem;
    }
    @media (min-width: 768px) {
        .features-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    .feature-card {
        background: rgba(20, 20, 20, 0.6);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.75rem;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        transition: all 0.3s var(--ease-premium);
    }
    .feature-card:hover {
        transform: translateY(-3px);
        border-color: rgba(201, 169, 98, 0.4);
    }
    .feature-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: rgba(201, 169, 98, 0.12);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .feature-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 0.5rem;
    }
    .feature-desc {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* Section Cards */
    .policy-section {
        background: rgba(20, 20, 20, 0.5);
        border: 1px solid var(--glass-border);
        border-radius: 18px;
        padding: 2.25rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }
    .section-heading {
        font-size: 1.4rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-heading i {
        color: var(--accent);
    }
    .policy-content {
        color: #d4d4d8;
        font-size: 1rem;
        line-height: 1.75;
    }
    .policy-content p + p {
        margin-top: 1rem;
    }

    .policy-table-box {
        background: rgba(10, 10, 10, 0.6);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        overflow: hidden;
        margin-top: 1.5rem;
    }
    .policy-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.95rem;
    }
    .policy-table th {
        background: rgba(201, 169, 98, 0.08);
        color: var(--accent);
        padding: 14px 18px;
        font-weight: 700;
        border-bottom: 1px solid var(--glass-border);
    }
    .policy-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--glass-border);
        color: var(--text-secondary);
    }
    .policy-table tr:last-child td {
        border-bottom: none;
    }

    .badges-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 1.25rem;
    }
    .pay-badge {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #f4f4f5;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .support-callout {
        background: linear-gradient(135deg, rgba(201, 169, 98, 0.1), rgba(20, 20, 20, 0.8));
        border: 1px solid rgba(201, 169, 98, 0.3);
        border-radius: 18px;
        padding: 2rem;
        text-align: center;
        margin-top: 3rem;
    }
</style>

<div style="max-width: 1000px; margin: 0 auto; padding: 2rem 1rem 5rem;">
    
    <!-- Hero -->
    <div class="policy-hero" data-aos>
        <span class="policy-badge"><i data-lucide="shield" style="width:14px;height:14px;"></i> Transparent & Secure Logistics</span>
        <h1 class="policy-title">Shipping & Payment Policy</h1>
        <p class="policy-subtitle">
            Reliable worldwide delivery, verified courier tracking, and multi-currency encrypted checkout across all orders.
        </p>
    </div>

    <!-- Highlight Cards -->
    <div class="features-grid" data-aos>
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="gift" style="width: 22px; height: 22px;"></i>
            </div>
            <h3 class="feature-title">Free Worldwide Shipping</h3>
            <p class="feature-desc">Enjoy 100% free delivery on all domestic orders and global orders above $30 / ₹499.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="truck" style="width: 22px; height: 22px;"></i>
            </div>
            <h3 class="feature-title">Fast 24-48h Dispatch</h3>
            <p class="feature-desc">Orders are processed swiftly with live tracking links emailed straight to your inbox.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="lock" style="width: 22px; height: 22px;"></i>
            </div>
            <h3 class="feature-title">256-Bit SSL Checkout</h3>
            <p class="feature-desc">Bank-grade security across PayPal, Credit/Debit Cards, UPI, Net Banking, and Payoneer.</p>
        </div>
    </div>

    <!-- 1. Shipping Details -->
    <section class="policy-section" data-aos>
        <h2 class="section-heading"><i data-lucide="clock" style="width:22px;height:22px;"></i> Processing & Delivery Timelines</h2>
        <div class="policy-content">
            <p>
                Every order placed on <strong>AtoZGadgets</strong> is verified and dispatched from our logistics network within <strong>24 to 48 hours</strong>. We prioritize automated tracking synchronization so you receive continuous transit milestones until package delivery.
            </p>

            <div class="policy-table-box">
                <table class="policy-table">
                    <thead>
                        <tr>
                            <th>Shipping Tier</th>
                            <th>Estimated Delivery</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Standard Global Shipping</strong></td>
                            <td>7 – 15 Business Days</td>
                            <td><span style="color:var(--accent);font-weight:700;">FREE</span> on orders &gt; $30 (or $4.99 flat)</td>
                        </tr>
                        <tr>
                            <td><strong>Express Regional Delivery</strong></td>
                            <td>3 – 7 Business Days</td>
                            <td>Calculated dynamically at checkout</td>
                        </tr>
                        <tr>
                            <td><strong>Festive / Peak Seasons</strong></td>
                            <td>10 – 18 Business Days</td>
                            <td>Couriers experience high hub volume</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 2. Tracking & OTP Security -->
    <section class="policy-section" data-aos>
        <h2 class="section-heading"><i data-lucide="map-pin" style="width:22px;height:22px;"></i> Live Tracking & Secure Handover</h2>
        <div class="policy-content">
            <p>
                Once your order leaves our fulfillment center, you will receive an automated shipping confirmation email containing your official tracking number and live tracking link.
            </p>
            <p>
                <strong>Delivery Verification:</strong> For high-value electronics and specific courier routes, an OTP code or physical signature is required upon delivery to ensure that the package is handed directly to the rightful recipient.
            </p>
        </div>
    </section>

    <!-- 3. Payment Modes -->
    <section class="policy-section" data-aos>
        <h2 class="section-heading"><i data-lucide="credit-card" style="width:22px;height:22px;"></i> Accepted Payment Methods</h2>
        <div class="policy-content">
            <p>
                We support instant and secure global checkout across major international and regional payment providers. Your payment details are processed through tokenized, PCI-DSS Level 1 certified gateways.
            </p>
            
            <div class="badges-row">
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> PayPal Express</span>
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> Visa</span>
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> Mastercard</span>
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> American Express</span>
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> UPI & QR Pay</span>
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> Net Banking (50+ Banks)</span>
                <span class="pay-badge"><i data-lucide="check-circle" style="width:14px;color:var(--accent);"></i> Payoneer Checkout</span>
            </div>
        </div>
    </section>

    <!-- Support Callout -->
    <div class="support-callout" data-aos>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;">Have a Question Regarding Your Order?</h3>
        <p style="color: var(--text-secondary); margin-bottom: 1.25rem; font-size: 0.95rem;">
            Our dedicated support team is available 7 days a week (11:00 AM – 9:00 PM IST) to assist with courier tracking and dispatch queries.
        </p>
        <a href="mailto:contact@atozgadgetz.com" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700;">
            <i data-lucide="mail" style="width: 18px; height: 18px;"></i> Email Support: contact@atozgadgetz.com
        </a>
    </div>

</div>
@endsection

