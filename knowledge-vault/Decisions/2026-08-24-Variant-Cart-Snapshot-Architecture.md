---
title: ADR - Product Variant Hierarchy, Cart Composite Keys & Snapshot Immutability
tags: [ecommerce, variants, cj-dropshipping, architecture, security]
updated: 2026-08-24
---

# ADR: Product Variant Architecture, Cart Composite Keys & Order Item Snapshots

**Purpose:** Documents the design and implementation of the multi-variant catalog, composite cart session keys, pessimistic database row locks during checkout, and immutable commercial/CJ order snapshots.

**Summary:** Establishes a 2-layer variant architecture where client inputs are completely untrusted. Cart items are identified by composite keys (\`{$productId}_{$variantId}\`), database queries validate variant ownership and authoritative pricing, checkout applies database-level pessimistic locking (\`lockForUpdate\`), and orders capture immutable CJ identifiers (\`cj_product_id\`, \`cj_variant_id\`, \`cj_variant_sku\`) before asynchronous fulfillment.

## 1. Context & Problems Solved

1. **Empty Variant Pills on Storefront:**
   - *Cause:* Suppliers on CJ Dropshipping API 2.0 frequently provide null or empty \`variantNameEn\`, storing descriptors in \`variantKey\` (e.g. \`Light (0.46mm)\`), \`variantStandard\`, or \`variantValue1..3\`.
   - *Fix:* Created a priority normalization pipeline in \`CjProductService\` and a defense-in-depth \`displayName\` accessor on \`ProductVariant\` model:
     $$\text{variantNameEn} \rightarrow \text{variantName} \rightarrow \text{variantKey} \rightarrow \text{variantStandard} \rightarrow \text{variantValues} \rightarrow \text{SKU Suffix}$$

2. **Single-Variant Cart Collision:**
   - *Cause:* Standard cart indexed by integer \`$product_id\` overwrote quantities when a customer selected multiple variants (e.g., Grey Scarf + Black Scarf).
   - *Fix:* Implemented composite string keys: \`"{$product->id}_{$variant->id}"\` (or \`"{$product->id}_0"\` for single-SKU products).

3. **Tamper-Resistant Pricing & Stock Concurrency:**
   - *Cause:* Never trust frontend-submitted prices or stock values.
   - *Fix:* Backend \`CartController\` queries authoritative prices from \`ProductVariant::where("id", $variantId)->where("product_id", $productId)->first()\`. During checkout, \`lockForUpdate()\` executes within a DB transaction to safely decrement local stock without locking external API calls.

4. **Immutable Order Item Snapshots:**
   - Added \`merchant_sku_snapshot\`, \`product_name_snapshot\`, \`variant_name_snapshot\`, \`cj_product_id\`, \`cj_variant_id\`, and \`cj_variant_sku\` directly to \`order_items\` table so subsequent catalog edits or supplier price shifts never corrupt past financial records.

## Related
- [[Architecture-MOC]]
- [[CJ-Dropshipping-MOC]]
- Repo: \`app/Models/ProductVariant.php\`, \`app/Http/Controllers/CartController.php\`, \`resources/views/store/product.blade.php\`
