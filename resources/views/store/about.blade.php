@extends('layouts.store')

@section('title', 'About Us — The AtoZ Story | AtoZGadgets')

@section('content')
<style>
  .about-hero {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 4rem;
  }
  .about-badge {
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
  .about-title {
    font-size: 2.25rem;
    line-height: 1.2;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.02em;
    margin-bottom: 1.25rem;
  }
  @media (min-width: 768px) {
    .about-title {
      font-size: 3.25rem;
    }
  }
  .about-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
    line-height: 1.7;
  }

  /* Story Glass Card */
  .story-card {
    background: rgba(20, 20, 20, 0.65);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 2.5rem;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    margin-bottom: 3.5rem;
    position: relative;
    overflow: hidden;
  }
  .story-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
    opacity: 0.6;
  }
  .story-text {
    font-size: 1.15rem;
    color: #e4e4e7;
    line-height: 1.8;
  }
  .story-text p + p {
    margin-top: 1.25rem;
  }

  /* Pillars Grid */
  .pillars-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 4rem;
  }
  @media (min-width: 768px) {
    .pillars-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  .pillar-card {
    background: rgba(20, 20, 20, 0.5);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 2rem;
    transition: all 0.3s var(--ease-premium);
    display: flex;
    flex-direction: column;
  }
  .pillar-card:hover {
    transform: translateY(-4px);
    border-color: rgba(201, 169, 98, 0.4);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(201, 169, 98, 0.1);
  }
  .pillar-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(201, 169, 98, 0.1);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
  }
  .pillar-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.75rem;
  }
  .pillar-desc {
    font-size: 0.95rem;
    color: var(--text-secondary);
    line-height: 1.6;
  }

  /* Stats Bar */
  .stats-bar {
    background: linear-gradient(135deg, rgba(201, 169, 98, 0.08), rgba(20, 20, 20, 0.8));
    border: 1px solid rgba(201, 169, 98, 0.25);
    border-radius: 20px;
    padding: 2.5rem 1.5rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    text-align: center;
    margin-bottom: 4rem;
  }
  @media (min-width: 768px) {
    .stats-bar {
      grid-template-columns: repeat(4, 1fr);
    }
  }
  .stat-val {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--accent);
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  .stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* CTA Section */
  .about-cta {
    text-align: center;
    padding: 3rem 1.5rem;
    background: rgba(20, 20, 20, 0.4);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
  }
  .cta-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 1rem;
  }
</style>

<div style="max-width: 1100px; margin: 0 auto; padding: 2rem 1rem 5rem;">
  
  <!-- Hero -->
  <div class="about-hero" data-aos>
    <span class="about-badge"><i data-lucide="sparkles" style="width:14px;height:14px;"></i> The AtoZ Gadgetz Story</span>
    <h1 class="about-title">Curating the Future of Everyday Technology</h1>
    <p class="about-subtitle">
      We bridge the gap between breakthrough gadget innovations and everyday consumers worldwide — delivering smart lifestyle solutions at transparent, accessible prices.
    </p>
  </div>

  <!-- Story Card -->
  <div class="story-card" data-aos>
    <div class="story-text">
      <p>
        Founded with a relentless passion for modern electronics, <strong>AtoZGadgets</strong> began with a simple observation: the world’s most exciting, time-saving, and innovative lifestyle gadgets were trapped behind huge retail markups and slow, fragmented supply chains.
      </p>
      <p>
        By partnering directly with cutting-edge electronics manufacturers and global fulfillment hubs across the United States, Europe, and Asia, we eliminate unnecessary middlemen. Every item in our catalog — from multi-device wireless charging docks and levitating speakers to intelligent smart-home automation — undergoes strict quality screening before reaching your hands.
      </p>
    </div>
  </div>

  <!-- 3 Pillars -->
  <div class="pillars-grid">
    <div class="pillar-card" data-aos>
      <div class="pillar-icon-box">
        <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
      </div>
      <h3 class="pillar-title">Vetted Quality</h3>
      <p class="pillar-desc">
        We rigorously test every gadget for build quality, battery safety, and real-world durability before listing it in our store.
      </p>
    </div>

    <div class="pillar-card" data-aos>
      <div class="pillar-icon-box">
        <i data-lucide="tag" style="width: 24px; height: 24px;"></i>
      </div>
      <h3 class="pillar-title">Fair Direct Pricing</h3>
      <p class="pillar-desc">
        By optimizing our direct-to-consumer logistics, we offer premium technology at prices up to 40% lower than traditional retail outlets.
      </p>
    </div>

    <div class="pillar-card" data-aos>
      <div class="pillar-icon-box">
        <i data-lucide="truck" style="width: 24px; height: 24px;"></i>
      </div>
      <h3 class="pillar-title">Global Fast Shipping</h3>
      <p class="pillar-desc">
        Equipped with end-to-end carrier tracking and partnerships with global logistics hubs to deliver orders safely and swiftly to your doorstep.
      </p>
    </div>
  </div>

  <!-- Stats Bar -->
  <div class="stats-bar" data-aos>
    <div>
      <div class="stat-val">50,000+</div>
      <div class="stat-label">Gadgets Delivered</div>
    </div>
    <div>
      <div class="stat-val">50+</div>
      <div class="stat-label">Global Warehouses</div>
    </div>
    <div>
      <div class="stat-val">4.8 / 5.0</div>
      <div class="stat-label">Customer Rating</div>
    </div>
    <div>
      <div class="stat-val">24/7</div>
      <div class="stat-label">Dedicated Support</div>
    </div>
  </div>

  <!-- CTA Box -->
  <div class="about-cta" data-aos>
    <h2 class="cta-title">Ready to Upgrade Your Tech Arsenal?</h2>
    <p style="color: var(--text-secondary); margin-bottom: 1.75rem; max-width: 500px; margin-left: auto; margin-right: auto;">
      Discover our handpicked collection of smart electronics, workspace accessories, and trending lifestyle essentials.
    </p>
    <a href="{{ route('store.shop') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; padding: 14px 28px;">
      Explore Trending Gadgets <i data-lucide="arrow-right" style="width:18px;height:18px;"></i>
    </a>
  </div>

</div>
@endsection

