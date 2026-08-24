# AtoZGadgets PHP Architecture Memory

## System Overview
This project is a full rewrite of the legacy Next.js/Node.js/Prisma application into a monolithic **PHP 8 / Laravel 8** architecture, specifically optimized for Hostinger Shared/Business hosting with strict NPROC and memory limits.

## 1. Database Architecture
The application connects directly to the legacy `atoz_gadgets_db` (MySQL). All models have been carefully mapped to reuse existing tables created by Prisma, avoiding data loss.
- **Core Models**: `User`, `Category`, `SubCategory`, `Brand`, `Product`, `Order`, `OrderItem`
- **Secondary Models**: `Cart`, `Wishlist`, `Coupon`, `ProductReview`, `Banner`, `Offer`, `Shipment`
- **Dropshipping Models**: `CjProduct` (maps to `cjproduct`), `CjOrder` (maps to `cjorder`)
- **Payments**: `Payment` (Supports Payoneer with `payoneer_transaction_id`)

*Rule*: All models utilize `protected $guarded = ['id'];` to seamlessly interact with legacy schema columns.

## 2. Controllers & Routing
The application follows a traditional MVC structure without the need for a Next.js proxy:
- **Storefront**: `StorefrontController` handles customer-facing UI (`/`, `/shop`, `/product/{slug}`).
- **Cart & Checkout**: `CartController` manages PHP-session based shopping cart.
- **Payment Gateway**: `PaymentController` handles Payoneer simulation and order state mutation via `/payment/payoneer`.
- **Admin Gateway**: `Admin/CatalogController` manages the CJ Dropshipping search and import workflow (`/admin/catalog/import`).

## 3. CJ Dropshipping Integration
- **Live Search**: Uses Laravel's `Http` facade to ping `https://developers.cjdropshipping.com/api2.0/v1/product/list`.
- **Import Logic**: Imports products into BOTH `products` (for storefront visibility with a default 100% markup) and `cjproduct` (for CJ fulfillment tracking).
- **Live Sync**: `php artisan sync:live` extracts local curated CJ products to `cj_products_export.json` for manual Hostinger sync, avoiding live API limits.

## 4. Frontend Styling & Performance
- **CSS Strategy**: Pure Vanilla CSS / Glassmorphism. **No Tailwind**. This drastically reduces DOM size and bundle size.
- **Micro-animations**: Native JS and CSS transitions utilized for hover effects and ripple clicks to provide a premium feel.
- **Fallback Mocks**: The UI elegantly falls back to mock data arrays if the database is empty.

## 5. Deployment Security & Rules
- **Zombie Process Management**: `public/kill.php` is deployed to terminate frozen processes on Hostinger (`posix_kill`).
- **Build Generation**: `hostinger_deploy.ps1` builds a lean, uncompiled artifact `.zip` excluding `node_modules` and `.git`.
- **E2E Testing**: Universal testing suite active via `php artisan test` (SQLite In-Memory).

*Memory MCP Node Saved: Type = ArchitectureRule & Convention*

## 6. Hybrid Monolith & Headless API (2026-07-25 Update)
To preserve compatibility with the legacy Next.js React frontend, the system now functions as a Hybrid Monolith:
- `routes/web.php` serves the Blade UI (Monolith).
- `routes/api.php` serves a 1-to-1 clone of the legacy Node.js Express REST API, secured by Laravel Sanctum (replacing JWT).
- **Obsidian ADR**: `knowledge-vault/Decisions/2026-07-25-Hybrid-Monolith-Architecture.md`

## 7. Database Scalability & Performance (2026-07-25 Update)
Following strict Code-Quality and Ponytail reviews, critical bottlenecks were patched:
- B-Tree Indexes injected into `products`, `categories`, `carts`, and `orders` to eliminate full-table scans.
- Hostinger NPROC constraints minimized by forcing `php artisan optimize` route/config caching.
- **Obsidian ADR**: `knowledge-vault/Decisions/2026-07-25-Database-Performance-Indexes.md`

## 8. Variant Hierarchy, Cart Composite Keys & Order Snapshots (2026-08-24 Update)
- **CJ Variant Priority Normalization**: Resolves supplier variation via `variantNameEn` -> `variantName` -> `variantKey` -> `variantStandard` -> `variantValue1..3`.
- **Defense-in-Depth `displayName`**: `ProductVariant::getDisplayNameAttribute()` guarantees non-empty customer labels across all UI states.
- **Composite Cart Keying**: Cart session indexed by `{$product_id}_{$variant_id}` to allow multiple variants of the same product to coexist.
- **Authoritative Price & Stock Locking**: Backend queries authoritative prices directly from the DB; checkout employs pessimistic database row locks (`lockForUpdate()`) inside ACID transactions.
- **Immutable Financial & CJ Snapshots**: `order_items` snapshots `merchant_sku_snapshot`, `product_name_snapshot`, `variant_name_snapshot`, `cj_product_id`, `cj_variant_id`, and `cj_variant_sku`.
- **Obsidian ADR**: `knowledge-vault/Decisions/2026-08-24-Variant-Cart-Snapshot-Architecture.md`
- **Obsidian ADR**: `knowledge-vault/Decisions/2026-08-24-Storefront-Review-UX-Overhaul.md`

