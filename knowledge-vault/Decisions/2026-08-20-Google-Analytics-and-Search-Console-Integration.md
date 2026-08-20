---
title: 2026-08-20-Google-Analytics-and-Search-Console-Integration
tags: [decision, seo, analytics, devops, frontend]
updated: 2026-08-20
---

# Google Analytics 4 & Google Search Console Verification Integration

**Purpose:** Document the setup of Google Analytics 4 (GA4) traffic measurement and Google Search Console (GSC) site verification for AtoZGadgets.

**Summary:** We integrated the Google tag (`gtag.js`) with Measurement ID `G-LS0E52WE2D` across the customer storefront and authentication layouts, and deployed `google1c9d8b5c6dcf337b.html` in both `public/` and root directories to ensure reliable web verification and indexation.

## Context
To track user interactions, marketing conversions, and monitor search ranking/indexing performance in Google Search Console:
1. GA4 requires the asynchronous gtag library loaded in the HTML `<head>`.
2. Google Search Console requires domain ownership verification via an HTML file uploaded to the root web directory.

## Decision
1. **Google Analytics 4 Script (`G-LS0E52WE2D`)**: Injected into `resources/views/layouts/store.blade.php` and `resources/views/layouts/auth.blade.php`.
2. **Search Console Static Verification**: `google1c9d8b5c6dcf337b.html` is maintained in `public/` (and root) so requests to `https://atozgadgetz.com/google1c9d8b5c6dcf337b.html` respond with `200 OK`.
3. **CI/CD Auto-Deployment**: GitHub Actions (`.github/workflows/deploy.yml`) is triggered on every push to `main` to build and deploy changes to Hostinger via Rsync.

## Related
- [[Architecture-MOC]]
- [[DevOps-MOC]]
- [[CI-CD-Pipeline]]
- Layouts: `resources/views/layouts/store.blade.php`, `resources/views/layouts/auth.blade.php`
- Public static file: `public/google1c9d8b5c6dcf337b.html`
