@extends('layouts.store')

@section('title', 'About Us | AtoZ Gadgetz')

@section('content')
<style>
  .about-container {
    max-width: 56rem;
    margin: 0 auto;
    padding: 3rem 1rem;
    text-align: center;
  }
  @media (min-width: 768px) {
    .about-container {
      padding: 5rem 1rem;
    }
  }
  .about-title {
    font-size: 1.875rem;
    line-height: 2.25rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: #111827;
  }
  @media (min-width: 768px) {
    .about-title {
      font-size: 3rem;
      line-height: 1;
    }
  }
  .about-card {
    margin-top: 3rem;
    padding: 2rem;
    background: linear-gradient(to right, #eff6ff, #eef2ff);
    border-radius: 1rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    border: 1px solid #dbeafe;
  }
  .about-text {
    font-size: 1.25rem;
    line-height: 1.75rem;
    font-weight: 500;
    color: #1f2937;
    line-height: 1.625;
  }
  @media (min-width: 768px) {
    .about-text {
      font-size: 1.875rem;
      line-height: 2.25rem;
    }
  }
  .about-text-highlight {
    font-weight: 700;
    color: #2563eb;
    display: block;
    margin-bottom: 1rem;
  }
</style>

<div class="about-container">
  <h1 class="about-title">About Us</h1>
  
  <div class="about-card">
    <p class="about-text">
      <span class="about-text-highlight">Get all the trending gadgets here at an affordable price</span>
      Only at Atoz Gadgetz.com with lesser price.
    </p>
  </div>
</div>
@endsection
