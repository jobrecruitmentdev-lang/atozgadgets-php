---
title: CJ Auth Token Flow
tags: [cj, auth, cache, dropshipping]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Auth Token Flow

**Purpose:** Documents how CJDropshipping access tokens are fetched, cached, and used across all CJ services.

**Summary:** `CjAuthService::getAccessToken()` is the single entry point for auth. It uses Laravel Cache to avoid repeated token calls. If `.env` credentials are missing, it returns `SANDBOX_DEMO_TOKEN` — a magic string that all downstream services check before hitting live API.

## Auth Logic

**Source:** [`app/Services/Cj/CjAuthService.php`](../../app/Services/Cj/CjAuthService.php) — `origin/latest-sync`

```
CJ_API_EMAIL + CJ_API_KEY present?
  ├── NO  → return 'SANDBOX_DEMO_TOKEN' (sandbox mode, no API call)
  └── YES → POST /authentication/getAccessToken
              ├── response.code === 200?
              │     ├── YES → extract accessToken + tokenExpiryDate
              │     │         compute TTL = (expiryMs - now - 5min safety buffer)
              │     │         Cache::put('cj_access_token', token, ttlSeconds)
              │     │         return token
              │     └── NO  → log warning → return 'SANDBOX_DEMO_TOKEN'
              └── Exception → log warning → return 'SANDBOX_DEMO_TOKEN'
```

## Key Implementation Details

| Detail | Value |
|---|---|
| **Cache key** | `cj_access_token` |
| **Token TTL** | `tokenExpiryDate` from CJ response minus 5-min safety buffer |
| **Fallback TTL** | `now + 86400s` (if CJ doesn't send expiry) |
| **Throttle** | `usleep(1100000)` = 1.1s delay before every live auth call |
| **API endpoint** | `POST /authentication/getAccessToken` with `email` + `password` |
| **Header name** | `CJ-Access-Token: <token>` |

## Auth Headers

`CjAuthService::getAuthHeaders()` returns:
```php
[
  'Content-Type'   => 'application/json',
  'CJ-Access-Token' => self::getAccessToken(),
]
```
Used by: `CjProductService`, `CjOrderService`, `CjShipmentService`.

## Sandbox Mode Trigger

Any service receiving `SANDBOX_DEMO_TOKEN` must short-circuit to demo/local data:
- `CjProductService::searchProducts()` → returns `getDemoCatalog()` (hardcoded 1-item array)
- `CatalogController::getCjAccessToken()` → checks `if ($token)` — null/false skips API entirely

## CatalogController Duplicate (⚠️ Debt)

`CatalogController::getCjAccessToken()` (line 78–95) reimplements auth inline with `Cache::remember('cj_access_token', 86400, ...)`. This is a **duplicate** of `CjAuthService` — the controller was written before the service was extracted.

**Decision needed:** Remove inline auth from `CatalogController`, inject `CjAuthService::getAuthHeaders()` instead.

## Related
- [[CJ-Dropshipping-MOC]]
- [[CJ-Product-Search-Sandbox-Fallback]]
- `../../app/Services/Cj/CjAuthService.php`

## References
- CJ API Docs: `https://developers.cjdropshipping.com/api2.0/v1/authentication/getAccessToken`
- Rate limit: 1,000 requests/day (see `../../.agents/AGENTS.md`)
