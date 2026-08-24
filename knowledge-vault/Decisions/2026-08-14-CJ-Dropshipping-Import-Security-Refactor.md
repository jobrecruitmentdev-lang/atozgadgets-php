---
title: 2026-08-14-CJ-Dropshipping-Import-Security-Refactor
tags: [decision, security, backend, database]
updated: 2026-08-14
---

# CJ Dropshipping Import Security Refactor

**Purpose:** Document the architectural decisions made to secure the CJ Dropshipping product import gateway against business logic exploits.

**Summary:** We refactored `CatalogController::importCjProduct` to fix three critical flaws: dynamic markup ignorance, negative price injection, and an orphaned database row race condition.

## Context
During an adversarial code review (`/devil`), three severe vulnerabilities were discovered in the product import logic:
1. **The Price Manipulation Exploit:** The backend validated price with `numeric` but lacked a `min:0.01` constraint, allowing potential negative price injection.
2. **The "Fake UI" Bug:** The frontend allowed admins to select a `markup` multiplier, but failed to pass it to the backend. The backend hardcoded `2.0` (100% markup).
3. **The Orphan Zombie Race Condition:** `Product::create()` was executing unconditionally before `CjProduct::updateOrCreate()`. Double-clicking the import button created duplicate internal products and left the older product completely orphaned in the database.

## Decision
1. **Price Integrity:** Enforced `min:0.01` on the cost price and `min:1.0` on the markup payload.
2. **Dynamic Margins:** The frontend now transmits `markup: markup` in the JSON payload, and the backend dynamically calculates the `price` and `discount_price` (25% off the marked-up price).
3. **Idempotency Guard:** An early-return check was added to the top of the transaction. If `CjProduct::where('cj_product_id', $data['pid'])->first()` exists, the backend instantly returns the existing `internal_product_id` without touching the `products` table.

## Reason
An idempotency guard is significantly simpler and safer than attempting complex database UPSERT locking. Passing the markup from the frontend honors the user's explicit intent without restricting pricing strategies to a hardcoded double margin.

## Related
- [[Backend-Architecture-MOC]]
- Repo: `../../app/Http/Controllers/Admin/CatalogController.php`
