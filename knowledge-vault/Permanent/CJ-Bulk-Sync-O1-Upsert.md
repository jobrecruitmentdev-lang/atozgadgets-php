---
title: CJ Bulk Sync — O(1) Upsert Pattern
tags: [cj, sync, performance, database, dropshipping]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Bulk Sync — O(1) Upsert Pattern

**Purpose:** Documents the `CjSyncService` bulk product sync strategy — exactly 3 DB queries per 20 products, eliminating the N+1 problem.

**Summary:** `CjSyncService::processCategoryPage()` fetches one page of CJ results and bulk-upserts Products + CjProduct metadata in 3 queries total. It uses `Model::upsert()` with `sku` as the unique conflict key. The 2× markup is baked in at sync time.

## Source File

- [`app/Services/Cj/CjSyncService.php`](../../app/Services/Cj/CjSyncService.php) — `origin/latest-sync` (L17–L105)

## Algorithm: 3 Queries Per Page

```
processCategoryPage($category, $page)
  │
  ├── [0 queries] CjProductService::searchProducts($category->cj_keyword, $page, 20)
  │
  ├── [LOOP] Build $productUpserts[] array from API response:
  │     pid       → from item['id'] ?? item['pid']
  │     price     → supplierPrice * 2.0   ← 100% markup
  │     slug      → Str::slug(name[0:40]) + '-' + md5(sku)[0:6]
  │     sku       → (string)pid
  │     stock     → 100 (default)
  │     image     → item['bigImage'] ?? item['imageUrl'] ?? item['productImage']
  │     name      → item['nameEn'] ?? item['name'] (truncated to 200)
  │     fulfillment_type → 'cj'
  │
  ├── [Query 1] Product::upsert($productUpserts, ['sku'], ['price','name','thumbnail_image','updated_at'])
  │     ← INSERT ... ON DUPLICATE KEY UPDATE (single query for all 20 items)
  │
  ├── [Query 2] Product::whereIn('sku', $skus)->pluck('id', 'sku')
  │     ← Fetch IDs of newly inserted/updated products (O(1) lookup)
  │
  └── [Query 3] CjProduct::upsert($cjProductUpserts, ['cj_product_id'], ['sell_price','internal_product_id','updated_at'])
        ← Link CJ metadata to internal product IDs
```

## Field Normalization (CJ API inconsistency)

CJ API uses different field names across versions:

| Our field | CJ field variants tried |
|---|---|
| pid | `item['id']` → `item['pid']` |
| sell price | `item['sellPrice']` → `item['nowPrice']` → `item['price']` |
| name | `item['nameEn']` → `item['name']` |
| image | `item['bigImage']` → `item['imageUrl']` → `item['productImage']` |

## DB Schema Requirements

`Product::upsert()` with `['sku']` as conflict key **requires `sku` to be a UNIQUE column** in the `products` table. (Noted inline at L66: `// Requires 'sku' to be unique in schema`).

## Pricing at Sync Time

```php
$markupPercentage = 2.0;
$finalPrice = $supplierPrice * $markupPercentage;
```

Same 100% markup as the Admin Import Pipeline. This is applied to `products.price`. No `discount_price` is set during bulk sync — only set during manual admin import.

## Caller Pattern

`processCategoryPage()` is designed to be called in a loop per category:
```php
$page = 1;
while (CjSyncService::processCategoryPage($category, $page)) {
    $page++;
}
```
Returns `false` when the API returns empty list (no more pages).

## Performance Characteristic

| Approach | Queries for 20 items | Queries for 1000 items |
|---|---|---|
| N+1 (old) | 40 | 2000 |
| **This service** | **3** | **~150** (3 per page) |

## Related
- [[CJ-Dropshipping-MOC]]
- [[CJ-Product-Search-Sandbox-Fallback]]
- [[CJ-Admin-Import-Pipeline]]
- `../../app/Services/Cj/CjSyncService.php`

## References
- `../../.agents/AGENTS.md` — Resource Conservation Rule (never bulk-sync on live server)
