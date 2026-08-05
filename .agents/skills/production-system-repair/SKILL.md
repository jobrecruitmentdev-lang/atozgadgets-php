---
name: production-system-repair
description: >-
  A strict diagnostic checklist and protocol for the AI to follow when debugging
  or deploying to a production server with strict NPROC and memory limits (like Hostinger).
  Enforces Next.js deployment safety, database connection pooling rules, and proxy route auditing.
---

# Universal Production System Repair

## Overview
This skill provides strict architectural rules and protocols that any AI must follow when designing, deploying, or debugging the production environment. It prevents common Hostinger shared-hosting crashes (such as NPROC exhaustion from Next.js builds or Prisma spawns) and handles Next.js API proxy routing bugs.

## Dependencies
None. This is an Instruction-Only protocol.

## Workflow

When investigating a production issue or planning a deployment, you MUST follow these rules:

### 1. The "No Live Build" Rule
- **Description:** NEVER run `npm run build` or `npx next build` directly on the live Hostinger shared server. The Next.js build process spawns over 90 child workers (Turbopack, Jest, etc.), which instantly exhausts the strict NPROC limit and triggers a 503 crash and SSH lockout.
- **Protocol:** ALWAYS build the Next.js application locally (`wsl npx next build`), then deploy the compiled `.next/` directory to the server using `rsync`.

### 2. The "Prisma Singleton" Rule
- **Description:** The Prisma Query Engine runs as a detached process. Instantiating `new PrismaClient()` in multiple files will spawn zombie processes that drain the NPROC limit.
- **Protocol:** Ensure that the codebase strictly exports a single, shared `PrismaClient` singleton from a central `prisma.ts` file, and that no other files instantiate their own client.

### 3. The "SSH Lockout / EAGAIN Backdoor" Check
- **Description:** If you encounter `EAGAIN` errors or SSH timeouts, it means the server's NPROC limit is completely exhausted by zombie Node/Prisma processes.
- **Protocol:** Use the `kill.php` backdoor script uploaded to `public_html`. A pure PHP script bypasses the OS `spawn` limits since it uses `posix_kill(pid, 9)` as a direct system call without spawning a new shell. 

### 4. The "Live .env.local Override" Audit
- **Description:** Hardcoded `localhost` fallbacks in the frontend proxy routes OR local development URLs left behind in the live server's `.env.local` file will cause API calls (like authentication) to fail silently or return HTML instead of JSON.
- **Protocol:**
  1. Check the proxy route files (e.g., `server-proxy/auth/login/route.ts`) to ensure fallback URLs point to the live backend, not `localhost`.
  2. **CRITICAL:** Check the LIVE SERVER'S `.env.local` (and `.env`) via SSH. Ensure variables like `API_URL` are not pointing to `http://127.0.0.1:8080/api` or other local ports used for development testing.
  3. If local ports are found in the live `.env.local`, rewrite them to the live backend domain and restart the Passenger app by touching `tmp/restart.txt`.

## Rate Limiting
Not applicable.

## Common Mistakes
- Relying on `localhost:8080` to answer API calls on the live server while a background task `npm run dev` is secretly running and taking up memory. Kill those tasks and point the `API_URL` directly to the live domain.
- Attempting to use `sed` or `cat` in bash commands locally instead of specific tools, though remote SSH server manipulation requires standard bash tools.
