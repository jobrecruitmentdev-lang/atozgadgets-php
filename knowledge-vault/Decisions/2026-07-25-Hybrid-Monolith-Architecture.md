---
title: Hybrid Monolith and Headless API Architecture
tags: [architecture, decision, api]
updated: 2026-07-25
---

# Hybrid Monolith and Headless API Architecture

**Purpose:** Document the decision to serve both Blade UI and a pure Headless JSON REST API from the same Laravel backend.

**Summary:** The project initially migrated away from a decoupled Node.js API to a PHP Monolith to solve Hostinger process limits. However, to preserve compatibility with the old Next.js React frontend, we implemented a full clone of the 24 REST APIs via `routes/api.php` secured by Laravel Sanctum.

## Content
**Context:** The old backend was a pure Express API. The new backend is a Laravel monolith. But the user wanted to "clone the backend" so their React app could still function if needed.

**Decision:** We implemented a Hybrid approach. 
1. `routes/web.php` serves the UI via Blade and session-based auth.
2. `routes/api.php` serves pure JSON payloads (`{success: true, data: {}, message: ""}`) matching the exact schema of the old Node.js app.
3. Laravel Sanctum is used for API token generation (replacing JWT).

**Reason:** This allows the user to have a working website immediately (Blade) while preserving the massive investment in their Next.js frontend, allowing them to switch seamlessly by changing `NEXT_PUBLIC_API_URL` to point to `/api`.

**Tradeoffs:**
- Requires duplicating some controller logic (e.g., `Api\CartController` vs `CartController`).
- Session Auth and Token Auth exist side-by-side.

## Related
- [[Architecture-MOC]]
- Repo: `../../ARCHITECTURE.md`
