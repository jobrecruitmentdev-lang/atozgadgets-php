---
title: DevOps MOC
tags: [deploy, infra, moc]
updated: 2026-08-13
---

# DevOps MOC

**Purpose:** Index for all deployment, CI/CD, and server operations knowledge.

**Summary:** This MOC groups notes regarding Hostinger deployment, GitHub Actions CI/CD, environmental secrets management, and ignore rules.

## Content
- **CI/CD:** The main pipeline lives in `.github/workflows/main.yml`. It handles code quality, static analysis, automated testing via SQLite, and deployment via `appleboy/ssh-action`.
- **Rsync Deployment:** Fast deployment script using WSL rsync to Hostinger (`deploy.sh`).
- **Ignore Rules:** Handled via `.gitignore` and `.rsyncignore` to ensure `node_modules` and `.env` credentials are never exposed or blindly uploaded.

## Related
- [[CI-CD-Pipeline]]
- [[2026-08-13-live-server-e2e-testing]]
- [[2026-08-13-live-paypal-production-debugging-and-fix]]
- [[Hostinger-GitHub-Actions-SSH]]
- [[Laravel-Cache-Catch22]]
- Repo: `../../.github/workflows/deploy.yml`, `../../deploy.sh`

## References
- None
