---
title: CJ Admin Import Pipeline
tags: [cj, admin, catalog, import, product, dropshipping]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Admin Import Pipeline

**Purpose:** Documents the Admin Gateway workflow for manually importing CJ products into the local `atoz_gadgets_db`.

**Summary:** The admin visits `/admin/catalog/import`, searches CJ API via AJAX, selects products, and hits "Import". Each import runs a strict ACID DB transaction that creates a `Product` + `CjProduct` record simultaneously. The `fulfillment_type = 'cj'` + nullable FK pattern is the Phase 2 migration escape hatch.

## Source File

- [`app/Http/Controllers/Admin/CatalogController.php`](../../app/Http/Controllers/Admin/CatalogController.php) — `origin/latest-sync`

## Route → Method Map

| Route | Method | Lines | Purpose |
|---|---|---|---|
| `GET /admin/catalog/import` | `import()` | L70–L76 | Render import page with staged products |
| `GET /admin/catalog/search-cj` | `searchCjApi()` | L97–L128 | AJAX: proxy search to CJ API |
| `POST /admin/catalog/import-cj` | `importCjProduct()` | L130–L184 | Save 1 product to DB |

## Import Flow Detail

```
POST /admin/catalog/import-cj
  │
  ├── Validate: pid, title, price, image (required), category, categoryId (nullable)
  │
  ├── Title Sanitization (L145–L148):
  │     if contains Chinese chars (U+4E00–U+9FA5) OR len < 3:
  │       → title = 'AtoZ Smart Gadget - Imported Edition'
  │     truncate to 200 chars
  │
  ├── slug = Str::slug(cleanTitle) + '-' + substr(uuid, 0, 6)
  │
  └── DB::transaction():
        │
        ├── Product::create([
        │     category_id     → $categoryId (default: 1)
        │     name            → $cleanTitle
        │     slug            → unique slug
        │     sku             → 'CJ-' + substr(uuid, 0, 8)
        │     price           → $data['price'] * 2.0   ← 100% markup (sell price)
        │     discount_price  → $data['price'] * 1.5   ← 50% markup (strike price)
        │     thumbnail_image → $data['image']
        │     stock_quantity  → 100 (default)
        │     status          → 'active'
        │     is_active       → true
        │     fulfillment_type → 'cj'                 ← Phase 2 switch point
        │     created_by      → 1
        │   ])
        │
        └── CjProduct::updateOrCreate(
              where: cj_product_id = $data['pid']
              set:   internal_product_id, title, sell_price,
                     cj_image, category_name, status='imported'
            )
```

## Pricing Logic

| Field | Formula | Example (CJ sell price = $10) |
|---|---|---|
| `price` (retail) | `cj_sell_price × 2.0` | $20.00 |
| `discount_price` (sale) | `cj_sell_price × 1.5` | $15.00 |

> Default 100% markup hardcoded at L158. Admins should override per product in the UI after import.

## Phase 2 Data Integrity Rule

From `../../.agents/AGENTS.md`:
> All imported products must preserve the nullable FK pointer in `CjProduct` table to allow seamless Phase 2 switching to own inventory (`fulfillment_type: 'own'`) without data loss.

The `CjProduct.internal_product_id` FK is nullable. To switch a product to own inventory:
1. Update `products.fulfillment_type = 'own'`
2. The `cj_products` row stays intact as a reference — no deletion needed.

## Import Page Data

`import()` method loads:
- `categories` — all categories with children (for category picker)
- `brands` — all brands
- `stagedProducts` — `Product::where('fulfillment_type', 'cj')->get()` — already-imported CJ products

## Related
- [[CJ-Dropshipping-MOC]]
- [[CJ-Auth-Token-Flow]]
- [[CJ-Product-Search-Sandbox-Fallback]]
- [[CJ-Bulk-Sync-O1-Upsert]]
- `../../.agents/AGENTS.md` — Catalog Management Strategy section
