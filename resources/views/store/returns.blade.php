@extends('layouts.store')

@section('content')
<style>
    .policy-container {
        max-width: 56rem;
        margin: 0 auto;
        padding: 2rem 1rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    @media (min-width: 768px) {
        .policy-container {
            padding: 3.5rem 1.5rem;
        }
    }
    .policy-title {
        font-size: clamp(1.75rem, 5vw, 2.5rem);
        line-height: 1.25;
        font-weight: 800;
        margin-bottom: 2rem;
        color: #fff;
    }
    .policy-body {
        color: var(--text-secondary);
        line-height: 1.7;
    }
    .policy-body > * + * {
        margin-top: 1.5rem;
    }
    .policy-strong-text {
        font-weight: 600;
        font-size: 1.125rem;
        color: #fff;
    }
    
    .glass-panel {
        background: rgba(20, 20, 25, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid var(--glass-border);
    }
    
    .panel-amber {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fcd34d;
    }
    .panel-amber h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #fbbf24;
    }
    .panel-amber p {
        color: #fde68a;
    }
    .panel-amber p + p {
        margin-top: 1rem;
    }
    .font-medium {
        font-weight: 500;
    }
    
    .panel-gray {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
    }
    .panel-gray h2 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: #fff;
    }
    
    .policy-list {
        list-style-type: disc;
        list-style-position: inside;
        color: var(--text-secondary);
    }
    .policy-list li + li {
        margin-top: 0.75rem;
    }
    
    .panel-red {
        background: rgba(239, 68, 68, 0.1);
        color: #fca5a5;
        font-weight: 700;
        font-size: 1.1rem;
        text-align: center;
        margin-top: 2rem;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
</style>

<div class="policy-container">
    <h1 class="policy-title">Cancellation, Return & Refund Policy</h1>
    
    <div class="policy-body">
        <p class="policy-strong-text">
            All items come with a 24-hour warranty.
        </p>
        
        <div class="glass-panel panel-amber">
            <h2>PROPER UNBOXING VIDEO</h2>
            <p>
                Make sure you have the right video of your parcel for security purposes.
            </p>
            <p>
                If you are sent a wrong/empty/damaged/missing parcel, your complaint will not be accepted unless you have the correct unboxing video starting from the time the parcel was sealed until you turn the product on in the same video. (If the device is not charged, then you must charge it and show it in the same video). Video that starts between the two will not be accepted.
            </p>
            <p class="font-medium">
                The video must be emailed to us with your Name, Issue, and mention that you purchased from this website for better assistance.
            </p>
        </div>

        <section class="glass-panel panel-gray">
            <h2>Exchange Process</h2>
            <p>
                You can contact us at <strong>contact@atozgadgetz.com</strong> within 7 days of delivery to report a defect and request an exchange. We will email you the next steps if the product is found defective.
            </p>
        </section>

        <section class="glass-panel panel-gray">
            <h2>Return Conditions</h2>
            <ul class="policy-list">
                <li>
                    Returns must be in their original packaging and in their original condition. All returned goods will be inspected upon return. We may not accept exchange requests if the item is returned in an unacceptable condition.
                </li>
                <li>
                    We are not responsible for any items that get damaged or lost during return shipping. Therefore, we recommend an insured and tracked mail service. INDIA POST is a good option as it’s affordable.
                </li>
            </ul>
        </section>

        <div class="glass-panel panel-red">
            <p>
                There is no REFUND policy.
            </p>
        </div>
    </div>
</div>
@endsection
