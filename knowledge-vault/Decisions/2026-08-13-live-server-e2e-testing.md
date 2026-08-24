---
title: Live Server E2E Testing with Playwright
tags: [decision, testing, architecture]
updated: 2026-08-13
---

# Live Server E2E Testing with Playwright

**Purpose:** Document the decision and methodology for running E2E tests against the live production server without causing data pollution.

**Summary:** We decided to inject `ADMIN_EMAIL` and `ADMIN_PASSWORD` via environment variables to test protected CJ Dropshipping endpoints live, with an explicit cleanup phase.

## Content

### Context
The user requested a "dry-run" of CJ Dropshipping imports using Playwright directly on the live server (`atozgadgetz.com`). E2E testing on production is risky because creating dummy products (e.g., "Playwright Test Gadget") pollutes the live catalog.

### Decision
1. **Env-Based Auth:** The script requires `ADMIN_EMAIL` and `ADMIN_PASSWORD` via environment variables. If absent, it gracefully skips the live test to prevent pipeline failures.
2. **Auto-Cleanup:** The script saves the `internal_id` of the imported CJ product. In the `test.afterAll` block, it uses the authenticated session to hit the `DELETE` API and remove the product from the live database.

### Reason
Allows testing live integration (like Hostinger network limits or CJ Dropshipping real responses) while preventing permanent junk data on the storefront.

### Alternatives
- **Staging Server:** Best practice, but we do not have a separate staging server configured on Hostinger.
- **SQLite Local Testing:** Doesn't prove that live outbound requests to CJ Dropshipping APIs are succeeding through Hostinger's firewall.

### Consequences
If the test fails abruptly before the `afterAll` hook, a junk product might remain in the database.

## Related
- [[DevOps-MOC]]
- Repo: `../../e2e/api-routes.spec.ts`

## References
- None
