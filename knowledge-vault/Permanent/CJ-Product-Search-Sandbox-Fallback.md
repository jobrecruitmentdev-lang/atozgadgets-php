---
title: CJ Product Search & Sandbox Fallback
tags: [cj, product, search, sandbox, dropshipping]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Product Search & Sandbox Fallback

**Purpose:** Documents the product search logic in `CjProductService` — live API path, param normalization, and the demo catalog fallback chain.

**Summary:** `CjProductService::searchProducts()` is the canonical way to query CJ. It handles token detection, 1.1s rate-limit throttle, multi-format response normalization, and falls back to a hardcoded 1-item demo catalog on any failure or sandbox mode.

## Source Files

- **Primary:** [`app/Services/Cj/CjProductService.php`](../../app/Services/Cj/CjProductService.php) — `origin/latest-sync` (L41–L86)
- **Legacy controller path:** [`app/Http/Controllers/Admin/CatalogController.php`](../../app/Http/Controllers/Admin/CatalogController.php) — `searchCjApi()` L97–L128 (uses older `/product/list` endpoint)

## Search Flow

```
searchProducts($keyword, $pageNum=1, $pageSize=20, $filters=[])
  │
  ├── CjAuthService::getAccessToken()
  │     ├── === 'SANDBOX_DEMO_TOKEN' → return getDemoCatalog() immediately
  │     └── real token → continue
  │
  ├── usleep(1100000)  ← 1.1s throttle (respect CJ rate limit)
  │
  ├── GET /product/listV2 with params:
  │     keyWord, page, size, pageNum, pageSize
  │     + optional: minPrice, maxPrice, categoryId, countryCode
  │
  ├── Response normalization (CJ API inconsistency handling):
  │     $list = $data['data']['list']
  │          ?? $data['data']['content'][0]['productList']
  │          ?? $data['data']['content']
  │          ?? []
  │
  │     $total = $data['data']['totalRecords']
  │           ?? $data['data']['total']
  │           ?? count($list)
  │
  ├── count($list) > 0 → return ['list' => $list, 'total' => $total]
  │
  └── else / Exception → return getDemoCatalog()
```

## Demo Catalog (Sandbox Fixture)

**Source:** `CjProductService::getDemoCatalog()` L21–L39

Single hardcoded product for sandbox/dev mode:
```
pid:           CJ-SMART-PRO-PROJECTOR-01
productNameEn: AtoZ Mini HD Smart LED Projector 1080P WiFi Portable
productSku:    CJ-PROJ-1080P
sellPrice:     29.50
categoryName:  Electronics & Gadgets
image:         Unsplash photo (portable projector)
```

**Note:** `CatalogController::$demoCatalog` (L19–L68) has **6 demo products** — this is the richer demo set shown in the admin import UI.

## Available Search Filters

| Filter param | PHP key | Notes |
|---|---|---|
| Min price | `minPrice` | Optional |
| Max price | `maxPrice` | Optional |
| Category | `categoryId` | CJ's internal category ID |
| Country | `countryCode` | e.g. `IN`, `US` |

## Endpoint Discrepancy (⚠️ Debt)

| Location | Endpoint | Notes |
|---|---|---|
| `CjProductService` | `/product/listV2` | Newer, better pagination |
| `CatalogController::searchCjApi()` | `/product/list` | Older endpoint, no filters |

**Decision needed:** `CatalogController::searchCjApi()` should delegate to `CjProductService::searchProducts()` instead of making its own HTTP call.

## Related
- [[CJ-Dropshipping-MOC]]
- [[CJ-Auth-Token-Flow]]
- [[CJ-Admin-Import-Pipeline]]
- [[CJ-Bulk-Sync-O1-Upsert]]
