# Architectural Decision Record (ADR): Full Production Admin Overhaul & Interactive Strategy Hub

- **Date**: 2026-08-20
- **Status**: **ACCEPTED & DEPLOYED**
- **Impact Areas**: Admin Panel, CJ Dropshipping 2.0 Integration, Dynamic Settings DB, Reports & Analytics, Strategy Hub.

---

## 1. Context & Motivation
The AtoZGadgets platform previously contained static mock metrics on the dashboard, unpersisted forms in settings, and lack of visual tooling for the store owner to make business decisions regarding CJ Dropshipping (margins, fulfillment automation, US warehouse filtering, rate limit protection).

---

## 2. Key Decisions & Architectures Implemented

1. **Interactive Requirement Gathering & Decision Engine (`public/admin-strategy-hub.html`)**:
   - Deployed at `https://atozgadgetz.com/admin-strategy-hub.html`.
   - Provides a 6-stage interactive wizard for the owner to choose:
     - Account Mode (Live API vs Sandbox Simulation)
     - Order Fulfillment (Automatic CJ Wallet `payType: 2` vs Manual Approval)
     - Multi-Variant Integrity (`VID` mapping vs Single variant)
     - Shipping & Free Shipping Margin Built-in vs Live Freight
     - Stock Outage & Supplier Price Change Safeguard
   - Includes live unit economics calculator (CAC, CJ Cost, Shipping, Gross & Net Margin).
   - Generates machine-readable `atozgadgets_cj_requirements_blueprint.json`.

2. **Live Database-Backed Admin Dashboard (`DashboardController.php`)**:
   - Real-time aggregates for Total Revenue, Total Orders, Active Customers, Low-Stock alerts (`< 5` units).
   - 30-day daily sales history and Top 5 selling gadgets.
   - 5-minute intelligent cache layer (`Cache::remember('admin_dashboard_metrics', 300, ...)`).

3. **Dynamic Database Configuration Storage (`Setting.php` & `SettingController.php`)**:
   - MySQL `settings` table with key-value pairs (`key`, `value`, `group`, `is_secret`).
   - Live tabbed UI for General settings, CJ Dropshipping keys, PayPal/Payoneer sandbox toggles, and catalog markups without raw `.env` edits.

4. **Dynamic Reports Suite & CSV Streaming (`ReportController.php`)**:
   - Date range filters (7D, 30D, 90D, Year-to-Date).
   - Instant downloadable CSV streams for `orders.csv`, `inventory.csv`, and `customers.csv`.

5. **Orders Management & Single-Click CJ Dispatching (`OrderController.php`)**:
   - Added `fulfillWithCj($id)` endpoint connecting to `CjOrderService::placeOrder()`.
   - Transitions order state to `processing` and captures `cj_order_id`.