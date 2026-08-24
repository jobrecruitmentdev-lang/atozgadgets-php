---
title: ADR - Customer Review Star Rating Widget & Storefront Micro-Interactions
tags: [frontend, ux, accessibility, storefront]
updated: 2026-08-24
---

# ADR: Customer Review Interactive 5-Star Rating Widget

**Purpose:** Documents the deprecation of legacy \`<select>\` dropdowns and Bootstrap form classes on customer-facing product reviews in favor of an accessible, pure CSS/JS 5-star rating bar.

**Summary:** Upgraded the storefront product review form to use semantic radio inputs styled as vibrant gold stars with hover glow, reverse flex ordering, and live selection feedback.

## 1. Context & Architectural Decisions

1. **Elimination of Dropdown Anti-Pattern:**
   - Replacing native \`<select>\` dropdowns with a 1-tap star bar reduced mobile touch interactions from 3 clicks (open modal, scroll wheel, confirm) to 1 tap.
2. **Accessible Radio Group:**
   - Uses native \`<input type="radio" name="rating">\` wrapped in a container with \`role="radiogroup"\` and \`aria-label="Product rating"\` for full WCAG compliance and keyboard arrow navigation.
3. **Pure CSS Star Hover Glow:**
   - Leverages \`flex-direction: row-reverse\` and the general sibling combinator (\`label:hover ~ label\`) to light up stars 1 through N seamlessly without heavy JavaScript listeners.

## Related
- [[Architecture-MOC]]
- Repo: \`resources/views/store/product.blade.php\`
