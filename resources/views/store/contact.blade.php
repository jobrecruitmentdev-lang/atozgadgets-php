@extends('layouts.store')

@section('title', 'Contact Us & Support | AtoZGadgets')

@section('content')
<style>
  .contact-hero {
    text-align: center;
    max-width: 750px;
    margin: 0 auto 3.5rem;
  }
  .contact-badge {
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
  .contact-title {
    font-size: 2.25rem;
    line-height: 1.2;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
    margin-bottom: 1rem;
  }
  @media (min-width: 768px) {
    .contact-title {
      font-size: 3rem;
    }
  }
  .contact-subtitle {
    font-size: 1.1rem;
    color: var(--text-secondary);
    line-height: 1.6;
  }

  /* 2 Column Layout */
  .contact-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
    align-items: flex-start;
  }
  @media (min-width: 1024px) {
    .contact-layout {
      grid-template-columns: 1fr 1.2fr;
      gap: 3.5rem;
    }
  }

  /* Info Cards */
  .info-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  .info-card {
    background: rgba(20, 20, 20, 0.6);
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    padding: 1.75rem;
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    transition: all 0.3s var(--ease-premium);
  }
  .info-card:hover {
    border-color: rgba(201, 169, 98, 0.4);
    transform: translateY(-2px);
  }
  .info-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 0.75rem;
  }
  .info-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(201, 169, 98, 0.12);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .info-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #fff;
  }
  .info-card-body {
    font-size: 0.95rem;
    color: var(--text-secondary);
    line-height: 1.6;
  }
  .info-card-link {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
  }
  .info-card-link:hover {
    text-decoration: underline;
  }

  /* Form Card */
  .contact-form-box {
    background: rgba(20, 20, 20, 0.65);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 2.5rem;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    position: relative;
    overflow: hidden;
  }
  .contact-form-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
    opacity: 0.6;
  }
  .form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
  }
  .form-desc {
    font-size: 0.95rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
  }
  .contact-grid-2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
  }
  @media (min-width: 640px) {
    .contact-grid-2 {
      grid-template-columns: 1fr 1fr;
    }
  }

  .form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 1.25rem;
  }
  .field-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #e4e4e7;
  }
  .field-input, .field-textarea {
    width: 100%;
    background: rgba(10, 10, 10, 0.7);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    padding: 12px 16px;
    color: #fff;
    font-size: 0.95rem;
    outline: none;
    transition: all 0.25s ease;
    font-family: inherit;
  }
  .field-input:focus, .field-textarea:focus {
    border-color: var(--accent);
    background: rgba(10, 10, 10, 0.9);
    box-shadow: 0 0 0 3px rgba(201, 169, 98, 0.2);
  }
  .field-textarea {
    resize: vertical;
    min-height: 120px;
  }

  .btn-submit-contact {
    width: 100%;
    background: linear-gradient(135deg, #c9a962, #b89851);
    color: #0a0a0a;
    font-weight: 700;
    font-size: 1rem;
    padding: 14px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s var(--ease-premium);
  }
  .btn-submit-contact:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(201, 169, 98, 0.5);
  }
</style>

<div style="max-width: 1100px; margin: 0 auto; padding: 2rem 1rem 5rem;">
  
  <!-- Hero -->
  <div class="contact-hero" data-aos>
    <span class="contact-badge"><i data-lucide="message-square" style="width:14px;height:14px;"></i> Customer Care & Inquiries</span>
    <h1 class="contact-title">We're Here to Help</h1>
    <p class="contact-subtitle">
      Have a question about an order, tracking updates, or product specifications? Our dedicated support team responds within 24 hours.
    </p>
  </div>

  <div class="contact-layout">
    
    <!-- Left Column: Contact Channels -->
    <div class="info-column" data-aos>
      <div class="info-card">
        <div class="info-header">
          <div class="info-icon-box">
            <i data-lucide="mail" style="width: 22px; height: 22px;"></i>
          </div>
          <div>
            <h3 class="info-card-title">Email Inquiries</h3>
            <span style="font-size:12px; color:var(--accent); font-weight:600;">Response in &lt; 24 hrs</span>
          </div>
        </div>
        <p class="info-card-body">
          For general questions, order updates, or wholesale inquiries:
          <br>
          <a href="mailto:contact@atozgadgetz.com" class="info-card-link">contact@atozgadgetz.com</a>
        </p>
      </div>

      <div class="info-card">
        <div class="info-header">
          <div class="info-icon-box">
            <i data-lucide="clock" style="width: 22px; height: 22px;"></i>
          </div>
          <div>
            <h3 class="info-card-title">Operating Hours</h3>
            <span style="font-size:12px; color:#10b981; font-weight:600;">Active 7 Days a Week</span>
          </div>
        </div>
        <p class="info-card-body">
          Monday – Sunday: <strong>11:00 AM – 9:00 PM IST</strong>
          <br>
          (Excluding major public holidays)
        </p>
      </div>

      <div class="info-card">
        <div class="info-header">
          <div class="info-icon-box">
            <i data-lucide="instagram" style="width: 22px; height: 22px;"></i>
          </div>
          <div>
            <h3 class="info-card-title">Social Community</h3>
            <span style="font-size:12px; color:var(--text-secondary);">Direct Messages & Updates</span>
          </div>
        </div>
        <p class="info-card-body">
          Follow us for product demonstrations, new arrivals, and promo drops:
          <br>
          <a href="https://instagram.com/atozgadgetzofficial" target="_blank" rel="noopener noreferrer" class="info-card-link">Instagram @atozgadgetzofficial</a>
        </p>
      </div>

      <div class="info-card" style="background: rgba(201, 169, 98, 0.05); border-color: rgba(201, 169, 98, 0.2);">
        <div class="info-header">
          <div class="info-icon-box">
            <i data-lucide="help-circle" style="width: 22px; height: 22px;"></i>
          </div>
          <div>
            <h3 class="info-card-title">Quick Tracking Tip</h3>
          </div>
        </div>
        <p class="info-card-body" style="font-size: 0.9rem;">
          Please include your <strong>Order ID (e.g. ORD-XXXXX)</strong> in your inquiry to accelerate tracking resolution.
        </p>
      </div>
    </div>

    <!-- Right Column: Interactive Form -->
    <div class="contact-form-box" data-aos>
      <h2 class="form-title">Send Us a Message</h2>
      <p class="form-desc">Fill out the form below and our team will get in touch shortly.</p>

      <form action="mailto:contact@atozgadgetz.com" method="GET" enctype="text/plain">
        <div class="contact-grid-2">
          <div class="form-field">
            <label for="contact-name" class="field-label">Full Name *</label>
            <input type="text" id="contact-name" name="name" class="field-input" placeholder="e.g. Alex Smith" required>
          </div>

          <div class="form-field">
            <label for="contact-email" class="field-label">Email Address *</label>
            <input type="email" id="contact-email" name="email" class="field-input" placeholder="e.g. alex@example.com" required>
          </div>
        </div>

        <div class="contact-grid-2">
          <div class="form-field">
            <label for="contact-phone" class="field-label">Phone / WhatsApp Number</label>
            <input type="tel" id="contact-phone" name="phone" class="field-input" placeholder="+1 (555) 000-0000">
          </div>

          <div class="form-field">
            <label for="contact-order" class="field-label">Order Number (If Applicable)</label>
            <input type="text" id="contact-order" name="order_id" class="field-input" placeholder="ORD-XXXXXXXXXX">
          </div>
        </div>

        <div class="form-field">
          <label for="contact-subject" class="field-label">Subject *</label>
          <input type="text" id="contact-subject" name="subject" class="field-input" placeholder="e.g. Question about my shipment" required>
        </div>

        <div class="form-field">
          <label for="contact-message" class="field-label">Message *</label>
          <textarea id="contact-message" name="body" class="field-textarea" placeholder="How can we help you? Please describe your query..." required></textarea>
        </div>

        <button type="submit" class="btn-submit-contact">
          <i data-lucide="send" style="width: 18px; height: 18px;"></i> Send Message
        </button>
      </form>
    </div>

  </div>

</div>
@endsection

