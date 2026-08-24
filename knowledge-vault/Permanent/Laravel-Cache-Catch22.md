---
title: Laravel Cache Catch-22
tags: [deploy, laravel, bug]
updated: 2026-08-13
---

# Laravel Cache Catch-22

**Purpose:** Document the Catch-22 bug where artisan commands fail in production due to a missing dev dependency in the cached packages list.

**Summary:** During deployment, if `composer install --no-dev` removes dev packages (like `Facade\Ignition`), any subsequent `php artisan` commands (like `optimize:clear`) will instantly crash because Laravel's old `bootstrap/cache/packages.php` still expects those dev packages to exist.

## Content
When syncing vendor files from a CI/CD runner to a live server using `rsync`, the `.rsyncignore` file normally excludes `bootstrap/cache/*.php`. 
This leaves the old cache on the production server.

When `php artisan optimize:clear` runs on the server to flush that cache, it attempts to boot Laravel. During the boot process, it reads the old `bootstrap/cache/packages.php`.
Since that old cache references dev-packages (e.g. Ignition error pages) that are no longer present in the `vendor/` folder, the boot crashes with:
`Class "Facade\Ignition\IgnitionServiceProvider" not found`

### The Fix
Never rely on `artisan optimize:clear` to clear the cache during deployment if the vendor environment has shifted (e.g., dev vs no-dev). 
Instead, aggressively delete the cache files directly from the OS shell **before** running artisan commands:

```bash
rm -f bootstrap/cache/*.php
php artisan optimize:clear
php artisan optimize
```

This ensures Laravel starts with a clean slate and generates a fresh `packages.php` without crashing.

## Related
- [[DevOps-MOC]]

## References
- None
