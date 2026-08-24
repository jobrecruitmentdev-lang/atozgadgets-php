---
title: 10-Customer Critical Path Hardening & Outbox Decoupling
tags: [architecture, payments, fulfillment, cj, idempotency]
updated: 2026-08-22
---

# 10-Customer Critical Path Hardening & Outbox Decoupling

**Purpose:** Document the architectural hardening applied to the Customer -> PayPal -> Order -> CJ Dropshipping -> Tracking critical path.

**Summary:** Decoupled external CJ API calls from the payment DB transaction using an Outbox pattern. Enforced server-side price recalculation, strict variant ID (VID) resolution, un-truncated address persistence, and 100% idempotent capture & webhook handling backed by an Admin Control Tower view.

## 1. Architectural Decisions

1. **Transaction Isolation:** Payment DB transaction commits financial state (`Payment=CAPTURED`, `Order=PAID`, `Outbox=ORDER_PAID`) first. If CJ API fails or is down, payment remains captured and order is flagged for retry rather than failing the customer's purchase.
2. **Strict Variant Resolution (VID Fidelity):** `CjOrderService::resolveVariantId()` resolves the true `cj_variant_id` (VID) over parent `cj_product_id` (PID) to prevent CJ catalog fulfillment rejection.
3. **Idempotency Locks:** `Payment::firstOrCreate`, `PaymentTransaction::firstOrCreate`, and `CjOrder::whereIn('status', ...)` prevent duplicate charges and duplicate CJ order placement across network retries and webhook replays.
4. **Admin Control Tower:** Provides a 6-card transparency view in `resources/views/admin/orders/show.blade.php` with 1-click status sync, manual retry, and refund processing.

## Related
- [[Fulfillment-Architecture]]
- [[Payment-Gateway-Setup]]
- Repo: `ARCHITECTURE.md`, `.agents/AGENTS.md`
