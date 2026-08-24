---
title: Live Production PayPal Integration & Schema Mismatch Fix
tags: [decision, paypal, laravel, hostinger, production-bug, devops]
updated: 2026-08-13
---

# Live Production PayPal Integration & Schema Mismatch Fix

**Purpose:** Document the diagnosis, root causes, and resolutions for the production PayPal checkout failures on `atozgadgetz.com`.

**Summary:** Fixed multiple production checkout issues caused by Laravel `env()` helper caching traps, cached sandbox OAuth tokens, database schema mismatches (`transaction_id` column missing in `payments` table, missing `order_number` in `Order` model), and unhandled JavaScript promise rejections in PayPal SDK.

## Content

### Context
When testing PayPal checkout on the live production website (`atozgadgetz.com`), the browser console logged:
1. `POST https://atozgadgetz.com/payment/paypal/create-order 500 (Internal Server Error)`
2. `Uncaught Error: PayPal Error: Illuminate\Http\Client\PendingRequest::withBasicAuth(): Argument #1 ($username) must be of type string, null given`
3. `Uncaught Error: Expected an order id to be passed`
4. `unhandled_error Object`

### Root Causes
1. **Laravel Config Caching (`env()` Trap):** In production, running `php artisan optimize` caches the configuration array and disables `env()` calls at runtime outside of `config/*.php`. Calling `env('PAYPAL_CLIENT_ID')` inside `checkout.blade.php` evaluated to `null`, defaulting to `'test'`.
2. **Environment Mismatch & Application Token Cache:** Live credentials were updated in `.env`, but `PAYPAL_MODE` was initially `sandbox`. Furthermore, `PayPalService` stored access tokens in `Cache::remember('paypal_access_token', 3200, ...)`. When switched to `live`, Laravel returned the cached sandbox token, causing Live PayPal API endpoints to return `401 Unauthorized`.
3. **Database Schema Mismatches:**
   - `orders` table required `order_number` (`NOT NULL`), but `PaymentController::paypalCaptureOrder` created `Order` instances without populating `order_number`.
   - `payments` table lacked a `transaction_id` column (only contained `payoneer_transaction_id`), causing `PaymentService::processPayment` to fail with a SQL `Unknown column 'transaction_id'` exception.
4. **Unhandled Frontend Error Rejection:** `paypal.Buttons` lacked an `onError` lifecycle callback, causing PayPal JS SDK to emit generic unhandled `Object` errors when `createOrder` failed.

### Decisions & Solutions
1. **Config Hardening:** Replaced all `env()` direct calls in Blade views and Services with `config('paypal.*')` and `config('services.cj.*')`.
2. **Cache Management:** Executed `php artisan cache:clear && php artisan optimize` to purge stale cached access tokens and rebuild configuration caches.
3. **Database Fixes:**
   - Added a `booted()` model hook in `app/Models/Order.php` to auto-generate unique `order_number` (e.g. `ORD-XXXXXX`) whenever an order is created.
   - Created migration `2026_08_13_000001_add_transaction_id_to_payments_table.php` to add `transaction_id` to `payments` table.
   - Updated `PaymentService.php` to populate both `transaction_id` and `payoneer_transaction_id`.
4. **Frontend SDK Resilience:**
   - Implemented `!orderData.id` validation in `checkout.blade.php` to parse `message`, `error`, or `details[0].description`.
   - Added `onError: function(err)` handler to `paypal.Buttons({...})` to show readable user alerts.
5. **CI/CD Automation:** Added `php artisan migrate --force` to `.github/workflows/deploy.yml` post-deployment steps.

### Consequences & Verification
- PayPal live checkout flow initializes with valid Live Client ID and returns active order IDs.
- CI/CD automatically runs database migrations and flushes caches on every push to `main`.

## Related
- [[00-MOC/DevOps-MOC]]
- Files: `app/Services/PayPalService.php`, `app/Http/Controllers/PaymentController.php`, `resources/views/store/checkout.blade.php`, `database/migrations/2026_08_13_000001_add_transaction_id_to_payments_table.php`
