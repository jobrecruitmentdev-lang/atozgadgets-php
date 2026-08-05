# Environment & Deployment Review

## Environment files to inspect
```
.env / .env.production / .env.example
Host provider environment variables (Hostinger, etc.)
GitHub Secrets
Cloudflare Secrets
```

Check for:
- Variables required by the app but missing from the target environment
  (grep source for `process.env.X`, diff against what's actually set)
- Wrong database host (`localhost` in a connection string that must reach a
  remote/managed DB)
- Incorrect ports
- Missing third-party API keys
- Secrets committed to git (`.env` tracked, not gitignored)
- Special characters in secrets that aren't URL-encoded when embedded in a
  connection string (`@ : / # %` all break URI parsing)

## Deployment pipeline
```
package.json -> Build Command -> Start Command -> Node Version
  -> Prisma Generate -> Prisma Migration -> Health Endpoint -> Restart Policy
```

Fail the review if any step is missing or ambiguous:
- Build script actually compiles (e.g. `tsc`), not just `prisma generate`
- Start script points at the compiled output (`dist/server.js`), and
  `package.json`'s `main` field agrees with it
- `engines.node` is pinned to match what the host runs
- A `/health` (or equivalent) endpoint exists and is checked post-deploy
- There's a defined restart/crash-loop policy, not silent failure
