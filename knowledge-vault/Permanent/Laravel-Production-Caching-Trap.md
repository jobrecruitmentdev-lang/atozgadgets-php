---
title: Laravel Production Caching Trap
tags: [architecture, deploy, security, performance]
updated: 2026-08-14
---

# Laravel Production Caching Trap

**Purpose:** Document why `env()` calls return null in production and how to safely access environment variables.

**Summary:** In Laravel, running `php artisan config:cache` (which is standard for Hostinger/production deployments) causes all direct `env()` calls outside of `config/` files to evaluate to `null`. This breaks services silently.

## Content
If you use `env('API_KEY')` directly inside a Controller or Service, and the deployment script runs `config:cache`, Laravel stops reading the `.env` file directly and only loads values from cached config files.

Because of this, `env()` returns `null` for everything. This can cause the application to fall back to sandbox mode or throw unauthenticated errors, even when the `.env` is perfectly valid on the server.

### The Fix
1. **Map to Config:** Always map environment variables inside a file in `config/` (e.g., `config/services.php`).
   ```php
   'cj' => [
       'email' => env('CJ_API_EMAIL'),
       'key' => env('CJ_API_KEY'),
   ]
   ```
2. **Access via Config:** Use `config('services.cj.email')` inside the codebase.
3. **Clear Cache on Update:** If you update the `.env` or `config/` files, you MUST run `php artisan optimize:clear` via SSH or deployment script to flush the old cache.

## Related
- [[Backend-Architecture-MOC]]
- [[2026-08-14-CJ-Dropshipping-Import-Security-Refactor]]
- Repo: `../../ARCHITECTURE.md`

## References
- Laravel Configuration Documentation: https://laravel.com/docs/11.x/configuration#configuration-caching
