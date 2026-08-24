---
title: Frontend Performance and UI/UX Fixes (Devil Audit)
tags: [decision, performance, ui-ux]
updated: 2026-08-13
---

# Frontend Performance and UI/UX Fixes (Devil Audit)

**Purpose:** Record the UX and Core Web Vitals fixes implemented in the storefront and admin layouts.

**Summary:** We performed a `/devil` audit and fixed render-blocking assets, GPU thrashing CSS, accessibility violations, and mobile menu functionality.

## Content

### Context
The UI looked premium but suffered from terrible scroll jank on mobile, missing CSRF tokens, blocked rendering, and an unusable mobile menu.

### Decision
1. **GPU Jank Fix:** Removed `background-attachment: fixed` from the `body`. This caused 100% repaints on scroll. Replaced it with a `body::before { position: fixed }` layer containing the gradient.
2. **Render-Blocking:** Deferred the Lucide icon `<script>` load and initialized icons inside a `DOMContentLoaded` + `window.load` fallback.
3. **Core Web Vitals:** Added `preconnect` for Google Fonts to reduce LCP.
4. **Mobile UX:** Implemented a full CSS/JS slide-out mobile menu overlay with `overflow: hidden` on the body to lock scrolling when open.
5. **Accessibility:** Added `.sr-only` labels and `aria-label` tags to all bare search inputs and icon buttons.
6. **Security:** Added the missing `<meta name="csrf-token">` to enable future AJAX cart actions.

### Tradeoffs
Using a JS-driven mobile menu requires JS to be active, but since this app relies on fetch APIs and Lucide anyway, a non-JS fallback was deemed unnecessary.

## Related
- Repo: `../../resources/views/layouts/store.blade.php`, `../../resources/views/layouts/admin.blade.php`

## References
- None
