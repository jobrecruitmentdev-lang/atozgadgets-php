@extends('layouts.store')

@section('content')
<style>
    .policy-container {
        max-width: 56rem;
        margin: 0 auto;
        padding: 3rem 1rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    @media (min-width: 768px) {
        .policy-container {
            padding: 5rem 1rem;
        }
    }
    .policy-title {
        font-size: 1.875rem;
        line-height: 2.25rem;
        font-weight: 700;
        margin-bottom: 2rem;
        color: #111827;
    }
    @media (min-width: 768px) {
        .policy-title {
            font-size: 2.25rem;
            line-height: 2.5rem;
        }
    }
    .policy-body {
        color: #374151;
        line-height: 1.625;
    }
    .policy-body > * + * {
        margin-top: 1.5rem;
    }
    .policy-strong-text {
        font-weight: 600;
        font-size: 1.125rem;
        color: #111827;
    }
    
    .glass-panel {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-top: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    
    .panel-amber {
        background: rgba(254, 243, 199, 0.6); /* amber-50ish */
        border: 1px solid rgba(253, 230, 138, 0.8); /* amber-200ish */
        color: #78350f; /* amber-900 */
        margin-top: 1.5rem;
    }
    .panel-amber h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .panel-amber p + p {
        margin-top: 1rem;
    }
    .font-medium {
        font-weight: 500;
    }
    
    .panel-gray {
        background: rgba(249, 250, 251, 0.7); /* gray-50 */
        border: 1px solid rgba(243, 244, 246, 0.8); /* gray-100 */
    }
    .panel-gray h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #111827;
    }
    
    .policy-list {
        list-style-type: disc;
        list-style-position: inside;
    }
    .policy-list li + li {
        margin-top: 1rem;
    }
    
    .panel-red {
        background: rgba(254, 242, 242, 0.8); /* red-50 */
        color: #7f1d1d; /* red-900 */
        font-weight: 700;
        font-size: 1.125rem;
        text-align: center;
        margin-top: 2.5rem;
        border: 1px solid rgba(254, 226, 226, 0.8);
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
