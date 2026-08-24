---
title: CI-CD Pipeline Architecture
tags: [deploy, security, testing]
updated: 2026-08-13
---

# CI-CD Pipeline Architecture

**Purpose:** Document the GitHub Actions CI/CD setup for AtoZGadgets.

**Summary:** We use a simple, ponytail-approved single-job GitHub Actions workflow that runs Composer setup, Composer Audit, and PHPUnit SQLite testing before executing the live deploy via SSH.

## Content

### Why this architecture?
Instead of a multi-job complex pipeline, a single `ubuntu-latest` run is used to save time and billable minutes. 
The pipeline acts as a strict quality gate:
1. **Security:** `composer audit` runs natively in 1 second.
2. **Testing:** An in-memory SQLite database is generated on-the-fly (`DB_CONNECTION=sqlite`) to run `php artisan test`. If a single test fails, deployment stops.
3. **Deployment:** If tests pass on the `main` branch, `appleboy/ssh-action` logs into Hostinger via secrets and runs a pull and `optimize:clear`.

### Secret Management
- We DO NOT upload the `.env` file via Git. The `.env` file stays persistent on Hostinger.
- Needed GitHub Secrets: `HOSTINGER_HOST`, `HOSTINGER_USER`, `HOSTINGER_SSH_KEY`.

## Related
- [[DevOps-MOC]]
- Repo: `../../.github/workflows/main.yml`

## References
- None
