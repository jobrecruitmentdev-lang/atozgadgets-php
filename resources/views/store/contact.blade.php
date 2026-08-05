@extends('layouts.store')

@section('title', 'Contact Us | AtoZ Gadgetz')

@section('content')
<style>
  .contact-container {
    max-width: 64rem;
    margin: 0 auto;
    padding: 3rem 1rem;
  }
  @media (min-width: 768px) {
    .contact-container {
      padding: 5rem 1rem;
    }
  }
  .contact-header {
    text-align: center;
    margin-bottom: 3rem;
  }
  .contact-title {
    font-size: 1.875rem;
    line-height: 2.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #111827;
  }
  @media (min-width: 768px) {
    .contact-title {
      font-size: 3rem;
      line-height: 1;
    }
  }
  .contact-subtitle {
    color: #4b5563;
    max-width: 42rem;
    margin: 0 auto;
  }
  .contact-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    margin-top: 3rem;
  }
  @media (min-width: 768px) {
    .contact-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  .contact-info-card {
    background-color: #f9fafb;
    padding: 2rem;
    border-radius: 1rem;
    border: 1px solid #f3f4f6;
  }
  .contact-info-title {
    font-size: 1.5rem;
    line-height: 2rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1.5rem;
  }
  .contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
  }
  .contact-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: #2563eb;
    margin-top: 0.25rem;
  }
  .contact-item-label {
    font-weight: 500;
    color: #111827;
  }
  .contact-item-link {
    color: #4b5563;
    text-decoration: none;
    transition: color 0.2s;
  }
  .contact-item-link:hover {
    color: #2563eb;
  }

  .contact-form-card {
    background-color: #ffffff;
    padding: 2rem;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  }
  .contact-form-title {
    font-size: 1.5rem;
    line-height: 2rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 1.5rem;
  }
  .contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  .form-group {
    display: flex;
    flex-direction: column;
  }
  .form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.25rem;
  }
  .form-input, .form-textarea {
    width: 100%;
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    font-size: 1rem;
    box-sizing: border-box;
  }
  .form-input:focus, .form-textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
  }
  .form-submit {
    width: 100%;
    background-color: #2563eb;
    color: #ffffff;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
    font-size: 1rem;
  }
  .form-submit:hover {
    background-color: #1d4ed8;
  }
</style>

<div class="contact-container">
  <div class="contact-header">
    <h1 class="contact-title">Say Hello</h1>
    <p class="contact-subtitle">
      By asking any queries, please also mention your contact number so we can get back to you as fast as possible.
    </p>
  </div>
  
  <div class="contact-grid">
    <!-- Contact Info -->
    <div class="contact-info-card">
      <h2 class="contact-info-title">Contact Information</h2>
      
      <div class="contact-item">
        <i data-lucide="mail" class="contact-icon"></i>
        <div>
          <p class="contact-item-label">Email</p>
          <a href="mailto:contact@atozgadgetz.com" class="contact-item-link">contact@atozgadgetz.com</a>
        </div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form-card">
      <h2 class="contact-form-title">Ask Your Queries</h2>
      <form class="contact-form" action="#">
        <div class="form-group">
          <label for="email" class="form-label">Email *</label>
          <input 
            type="email" 
            id="email" 
            class="form-input" 
            placeholder="Your Email" 
            required 
          />
        </div>
        
        <div class="form-group">
          <label for="subject" class="form-label">Subject *</label>
          <input 
            type="text" 
            id="subject" 
            class="form-input" 
            placeholder="Subject" 
            required 
          />
        </div>

        <div class="form-group">
          <label for="message" class="form-label">Message *</label>
          <textarea 
            id="message" 
            rows="5"
            class="form-textarea" 
            placeholder="Message (Please include your contact number)" 
            required 
          ></textarea>
        </div>

        <button 
          type="submit" 
          class="form-submit"
        >
          SEND MESSAGE
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
