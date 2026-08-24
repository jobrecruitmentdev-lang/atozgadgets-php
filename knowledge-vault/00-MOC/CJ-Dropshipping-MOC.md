---
title: CJ Dropshipping — Map of Content
tags: [cj, dropshipping, architecture, moc]
updated: 2026-08-14
source-branch: origin/latest-sync
---

# CJ Dropshipping — Map of Content

**Purpose:** Central hub for all CJ Dropshipping integration knowledge — auth, catalog, sync, orders, shipments.

**Summary:** AtoZGadgets uses CJDropshipping as its Phase-1 fulfillment provider. All CJ logic lives under `app/Services/Cj/`. The system has a Sandbox fallback mode when credentials are missing. Phase 2 switches to own inventory by flipping `fulfillment_type = 'own'` without data loss.

## Service Layer Map

| Service | File | Status |
|---|---|---|
| Auth & Token | `CjAuthService.php` | ✅ Implemented |
| Product Search | `CjProductService.php` | ✅ Implemented |
| Bulk Sync | `CjSyncService.php` | ✅ Implemented |
| Order Placement | `CjOrderService.php` | ✅ Implemented |
| Shipment Tracking | `CjShipmentService.php` | ✅ Implemented |
| Admin Import Gateway | `CatalogController.php` | ✅ Implemented |
| Category Service | `CjCategoryService.php` | ⚠️ Stub only |
| HTTP Client | `CjHttp.php` | ⚠️ Stub only |
| Inventory Service | `CjInventoryService.php` | ⚠️ Stub only |
| Legacy Facade | `app/Services/CJDropshippingService.php` | ⚠️ Empty shell |

## Atomic Notes
- [[CJ-Auth-Token-Flow]]
- [[CJ-Product-Search-Sandbox-Fallback]]
- [[CJ-Admin-Import-Pipeline]]
- [[CJ-Bulk-Sync-O1-Upsert]]
- [[CJ-Order-Placement-Flow]]
- [[CJ-Shipment-Tracking-Webhook]]

## Related MOCs
- [[Architecture-MOC]]

## Repo Docs
- `../../app/Services/Cj/` — all live service files
- `../../app/Http/Controllers/Admin/CatalogController.php` — admin gateway
- `../../.agents/AGENTS.md` — rate-limit and staging rules
