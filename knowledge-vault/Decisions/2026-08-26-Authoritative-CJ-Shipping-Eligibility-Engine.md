---
title: Authoritative CJ Shipping & Country Eligibility Engine
tags: [shipping, logistics, cj-dropshipping, checkout, security]
updated: 2026-08-26
---

# Authoritative CJ Shipping & Country Eligibility Engine

**Purpose:** Document the architectural design and invariants of the authoritative real-time and cached shipping eligibility verification engine.

**Summary:** The system prevents unfulfillable checkouts by dynamically validating destination countries against cart items, variant IDs (VIDs), CJ warehouse routing, available courier lines (CJPacket, USPS, Royal Mail, DHL), and delivery ETAs. Country dropdown selection alone is no longer authoritative; server-side and client-side guards block payments to restricted or unroutable destinations.

## Core Architectural Invariants

### 1. Multi-Dimensional Verification Pipeline
Every checkout attempt checks:
```
Cart Items (Product + VID + Qty)
         +
Destination Country Code
         ↓
[CjShippingEligibilityService::checkEligibility]
         ├── 1. Cached Logistics Lookup (1-hr TTL)
         ├── 2. CJ Freight API (/logistic/freightCalculate)
         ├── 3. Carrier Line & Warehouse Resolution
         └── 4. Tier-1 High-Speed Corridors Fallback
```

### 2. Supported Tier-1 Corridors & ETAs
- **United States (US):** `CJPacket Fast Line / USPS` (3–7 Days US Warehouse / 7–10 Days Direct)
- **United Kingdom (GB):** `CJPacket UK / Royal Mail` (6–10 Business Days)
- **Canada (CA):** `CJPacket Canada / Canada Post` (7–12 Business Days)
- **Australia (AU):** `CJPacket Australia / Australia Post` (7–12 Business Days)
- **Germany (DE):** `CJ Frankfurt Hub / DHL Express` (3–7 Days EU Hub / 7–10 Days)
- **France (FR):** `CJPacket Europe / La Poste` (7–11 Business Days)
- **Netherlands (NL):** `CJPacket Europe / PostNL` (6–10 Business Days)
- **Italy (IT):** `CJPacket Europe / Poste Italiane` (7–12 Business Days)
- **Spain (ES):** `CJPacket Europe / Correos` (7–12 Business Days)
- **New Zealand (NZ):** `CJPacket NZ / NZ Post` (8–12 Business Days)

### 3. Client & Server-Side Dual Guard
- **Frontend Live Telemetry:** As soon as the customer picks a country on `/checkout`, an AJAX request calls `POST /checkout/check-eligibility`. If deliverable, a green delivery card displays carrier, ETA, and fulfillment hub. If undeliverable, an alert banner blocks the checkout button.
- **Backend Authoritative Guard:** In `PaymentController::paypalCreateOrder`, eligibility is re-verified before creating the payment intent. If ineligible, the server throws `422 Unprocessable Entity` with reason.

## Related Notes & Code Links
- [[CJ-Order-Placement-Flow]]
- [[Architecture-MOC]]
- Service: `../../app/Services/Shipping/CjShippingEligibilityService.php`
- Controller: `../../app/Http/Controllers/CartController.php`, `../../app/Http/Controllers/PaymentController.php`
- Views: `../../resources/views/store/checkout.blade.php`
- Test Suite: `../../tests/Feature/CjShippingEligibilityEngineTest.php`
