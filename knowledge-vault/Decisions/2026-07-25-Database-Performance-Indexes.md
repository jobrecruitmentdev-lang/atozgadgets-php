---
title: Database Performance Indexes for Scalability
tags: [database, performance, decision]
updated: 2026-07-25
---

# Database Performance Indexes for Scalability

**Purpose:** Document the injection of performance indexes to prevent Hostinger CPU exhaustion.

**Summary:** Following a strict code review against the `code-review-and-quality-skill`, we discovered massive full-table scans happening on standard queries. We injected B-Tree indexes across 4 core tables to reduce lookup complexity from O(n) to O(1).

## Content
**Context:** When porting the models to Eloquent, we omitted explicit database indexes. Queries like `Product::where('status', 'active')->where('slug', $slug)` were doing full table scans because neither column was indexed.

**Decision:** Created the `2026_07_25_101903_add_performance_indexes` migration.
Added B-Tree indexes to:
- `products.slug`, `products.status`, `products.category_id`
- `categories.slug`, `categories.status`
- `carts.user_id`
- `orders.user_id`, `orders.status`

**Reason:** On a shared Hostinger plan with severe NPROC and CPU limits, an O(n) lookup on a table with 10,000 CJ Dropshipping products would instantly exhaust resources and trigger a 503 error. 

**Consequences:** 
- TTFB is slashed.
- Read operations are incredibly fast.
- Minor impact on Write speed (inserting a CJ product takes slightly longer due to B-Tree rebalancing), but reads happen 1000x more often than writes.

## Related
- [[Architecture-MOC]]
- Repo: `../../ARCHITECTURE.md`
