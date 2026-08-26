---
title: Auth Lifecycle, Nullable Mobile Schema, and International E-Commerce Hardening
tags: [auth, database, security, i18n, cj-dropshipping]
updated: 2026-08-26
---

# Auth Lifecycle, Nullable Mobile Schema, and International E-Commerce Hardening

**Purpose:** Document the architectural decisions and schema invariants for optional customer mobile numbers, password visibility toggles, and global checkout adaptations.

**Summary:** Fixed MySQL 1048 `Column 'mobile' cannot be null` and 1062 unique key violations by updating `users.mobile` to be explicitly `NULLABLE` with `unique:users,mobile` input validation. Enhanced the auth flow with interactive password visibility toggles and aligned checkout country selectors with CJ Dropshipping's Tier-1 priority logistics corridors.

## Architectural Decisions & Invariants

### 1. Database Schema & Strict Null Invariant
- **Problem:** Database `users` table was originally created with `$table->string('mobile', 20)->unique()` without `->nullable()`. When international buyers registered with empty mobile numbers, Laravel's `ConvertEmptyStringsToNull` middleware converted `""` to `null`, causing MySQL `SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'mobile' cannot be null`.
- **Resolution:**
  - Migrated `users` table via `2026_08_26_140200_make_mobile_nullable_on_users_table.php` (`ALTER TABLE users MODIFY mobile VARCHAR(20) NULL DEFAULT NULL;`).
  - Invariant: Blank mobile numbers MUST be stored as strict SQL `NULL`, not empty strings `''`, allowing MySQL unique indexing without conflict.
  - Validation: Form input accepts `'mobile' => 'nullable|string|max:20|unique:users,mobile'`.

### 2. Password Visibility ("Eye Button") Micro-Interactions
- Added accessible toggle buttons to all password fields across `/login` and `/register`.
- Dynamic Lucide `eye` ↔ `eye-off` swap with smooth gold accent hover (`rgba(201, 169, 98, 0.12)`).

### 3. Production Forgot & Reset Password Flow
- **Anti-Enumeration Invariant:** The system always returns the identical response (`"If an account exists with that email, we have sent a password reset link."`), preventing attackers from scraping registered customer emails.
- **Cryptographic Security:** Generates 256-bit cryptographically secure raw tokens (`bin2hex(random_bytes(32))`), hashed with SHA-256 in the database.
- **Strict Expiry & Invalidation:** 30-minute validity window. Immediate single-use purging upon password redemption (`DB::table('password_resets')->where('email', $email)->delete()`).
- **Brute-Force Rate Limiting:** 5 requests / minute per IP/email on `/forgot-password`.

### 4. International Targeting & CJ Dropshipping Corridors
- Filtered checkout destinations to prioritize Tier-1 countries with CJ local warehouses and fast dedicated lines (US, UK, CA, AU, DE, FR, IT, ES, NL, NZ).
- Customer portal `/account/orders` filters out unpaid draft attempts, displaying only confirmed/paid orders.

## Related Notes & Code Links
- [[2026-08-22-10-Customer-Critical-Path-Hardening]]
- [[Architecture-MOC]]
- App Controllers: `../../app/Http/Controllers/AuthController.php`, `../../app/Http/Controllers/AccountController.php`
- Migrations: `../../database/migrations/2026_08_26_140200_make_mobile_nullable_on_users_table.php`
- Test Suite: `../../tests/Feature/AuthLifecycleAndValidationTest.php`
