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
    
    .panel-gray {
        background: rgba(249, 250, 251, 0.7);
        border: 1px solid rgba(243, 244, 246, 0.8);
    }
    .panel-gray h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #111827;
    }

    .policy-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .policy-list li + li {
        margin-top: 1rem;
    }
    .font-semibold-text {
        font-weight: 600;
        color: #111827;
    }

    .panel-blue {
        background: rgba(239, 246, 255, 0.8); /* blue-50 */
        color: #1e3a8a; /* blue-900 */
        border: 1px solid rgba(219, 234, 254, 0.8);
        margin-top: 2.5rem;
    }
    .panel-blue p {
        font-weight: 500;
    }
</style>

<div class="policy-container">
    <h1 class="policy-title">Shipping & Payment Policy</h1>
    
    <div class="policy-body">
        <p>
            We endeavour to dispatch all products ordered within 48 hours after the order has been placed and accepted by us. 10-15 days is the expected delivery time for your parcel — as fast as possible for our beloved customers. It might take a bit longer in Festive Season because of rush and oversurge of parcels in delivery hubs. You will be receiving an OTP from the delivery company at the time of delivery to ensure that the delivery is given to the right person. In case of any queries or issues regarding your parcel you may email us at <strong>contact@atozgadgetz.com</strong>.
        </p>

        <section class="glass-panel panel-gray">
            <h2>Delivery Information</h2>
            <ul class="policy-list">
                <li>
                    <span class="font-semibold-text">Delivery Charges:</span> All domestic orders are delivered for free of charge.
                </li>
                <li>
                    <span class="font-semibold-text">Additional Charges:</span> There are no additional charges. The total payable amount is indicated on the individual items.
                </li>
                <li>
                    <span class="font-semibold-text">Delivery Time:</span> This may vary depending on the delivery location and services of our logistics partner. However, we endeavour to deliver orders within 10 to 15 days — as fast as possible for our beloved customers.
                </li>
                <li>
                    <span class="font-semibold-text">Delivery Areas:</span> We deliver All Over India.
                </li>
            </ul>
        </section>

        <section class="glass-panel panel-gray">
            <h2>Payment Modes</h2>
            <p>
                Online through Internet banking, UPI, Visa, MasterCard, American Express, Maestro, Debit cards, IMPS
            </p>
        </section>

        <div class="glass-panel panel-blue">
            <p>
                For further information please mail us at <strong>contact@atozgadgetz.com</strong>, 11 AM to 9pm, All days (excludes public holidays).
            </p>
        </div>
    </div>
</div>
@endsection
