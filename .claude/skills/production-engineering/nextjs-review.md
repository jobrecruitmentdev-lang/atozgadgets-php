# Next.js Review

- `error.tsx` / `not-found.tsx` / `global-error.tsx` exist
- API proxy routes (e.g. `app/api/[...path]/route.ts`) don't silently
  fall back to `localhost` when an env var like `API_URL` is missing in
  production — that fallback should fail loudly or be logged
- `images.remotePatterns` scoped to actual domains, not a wildcard
- `output: 'standalone'` considered if deploying via Docker; not required
  for host-native Node.js app hosting
- Middleware (`middleware.ts`) actually exists if auth-guarded routes
  (`/account`, `/admin`, etc.) are documented as protected — verify the
  file is present, don't just trust documentation
- Server vs. client env var usage: `NEXT_PUBLIC_*` for client-exposed
  values only; server-only secrets never prefixed that way
