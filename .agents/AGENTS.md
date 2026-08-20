# AtoZGadgets Workspace Custom Rules & Architecture Guidelines

## Catalog Management Strategy: Local-to-Live Staging
- **Resource Conservation Rule:** Never execute live bulk CJDropshipping API searches or heavy external product queries directly on the production live server to avoid hitting CJ rate limits (1,000 requests/day) and server CPU/memory spikes.
- **Admin Gateway Workflow:**
  1. Use the local Admin Panel Gateway (`/admin/catalog/import`) to search, filter by budget/markup, and import products into the local database (`atoz_gadgets_db`).
  2. Perform quality review and price customization locally.
  3. Run `npm run sync:live -- --push` or use `cj_products_export.json` to push curated product records directly from local database to live production server.
- **Data Integrity Rule:** All imported products must preserve the nullable foreign key pointer in `CjProduct` table to allow seamless Phase 2 switching to own inventory (`fulfillment_type: 'own'`) without data loss.
- **Test Isolation & Data Cleanup Rule:** Automated security or unit tests must use isolated database transactions or unique test prefixes (`E2E-TEST-*`). Test scripts must never leave raw XSS payloads (`<script>`) or dummy test records in the development/production database view. Run `npm run db:clean-garbage` to purge test artifacts.

## Hostinger Deployment & NPROC (Process) Limits
- **Prisma Client Instantiation:** Hostinger Shared/Business hosting has a strict maximum background process limit (NPROC). Prisma's `query-engine` binary creates new detached processes. **NEVER** instantiate multiple `new PrismaClient()` instances in the backend code (e.g., in background workers, servers, etc.). Always import a single shared singleton instance from `prisma.ts`. Failure to do so will spawn multiple engines, hit the `EAGAIN` limit, and cause 503 errors.
- **EAGAIN Deadlocks & SSH Drops:** If the NPROC limit is exhausted, Hostinger will completely block SSH logins (resulting in "Connection reset" or repeated password prompts), and `spawn` / `child_process.exec` calls inside Node.js will fail with `EAGAIN`.
- **Zombie Process Cleanup (The kill.php method):** When `EAGAIN` occurs, Hostinger's standard "Stop" button in hPanel often fails to kill detached Prisma zombie processes. Since SSH is blocked, use a pure PHP script (`kill.php`) uploaded to `public_html` that utilizes `posix_kill(pid, 9)` to loop through `/proc` and kill `query-engine` and `node` processes. This bypasses the OS `spawn` limits entirely because PHP executes `posix_kill` as a direct system call without spawning a new shell.
- **Deployment Safety:** Do NOT use `npx tsc` or Git-based deployment on the live Hostinger server as it consumes excessive CPU/Memory and triggers the CloudLinux LVE limits. Always compile the `dist` folder locally and manually upload it via File Manager.
- **Next.js Frontend Build Rule:** Never run `npm run build` or `next build` on the live Hostinger server. The Next.js build process (Turbopack, Jest workers, etc.) spawns over 90 processes and consumes massive amounts of RAM, instantly crashing the server. Always run `npx next build` locally and upload the `.next/` directory via `rsync` or File Manager.

## SEO, Webmaster & Analytics Setup
- **Static Verification Placement:** Always place third-party verification files (e.g. Google Search Console `google*.html`, Bing/IndexNow verification files) in both the project root and `public/` directory (e.g. `public/google1c9d8b5c6dcf337b.html`). This ensures Apache/Nginx web server root rewrites serve `200 OK` status immediately without 404 or route collisions.
- **Analytics Integrity:** Google Analytics (`gtag.js`, GA4 ID `G-LS0E52WE2D`) must be loaded asynchronously in the `<head>` of storefront and auth Blade layouts (`resources/views/layouts/store.blade.php`, `resources/views/layouts/auth.blade.php`).

## GitHub Push & CI/CD Deployment Workflow
- **Git Push on Every Change:** After completing code changes, knowledge vault updates, or configuration fixes, always stage (`git add`), commit with a descriptive semantic message, and push directly to GitHub `origin main`.
- **CI/CD Auto-Deploy:** Pushing to `main` automatically triggers GitHub Actions (`.github/workflows/deploy.yml`) to compile Composer autoloader, rsync filtered files to Hostinger production (`domains/atozgadgetz.com/public_html/`), run migrations, and optimize caches.
- **Manual Fallback (`deploy.sh`):** If immediate deployment or verification is needed via terminal/WSL, execute `./deploy.sh push` step-by-step to push changes directly over SSH port 65002.

## Agent Behavior & Verification Rules
- **Cross-Verification Rule:** Never give false-positive answers or assume context based solely on markdown documentation. When checking `.md` files (like `ARCHITECTURE.md`), you must always cross-verify the information against the actual codebase files (using search or view tools) to ensure it is accurate and currently implemented. Our goal is to solve the problem, not scale it into the future with incorrect assumptions.

