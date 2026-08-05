# Pre-Ship Production Checklist

```
[ ] Build succeeds cleanly (no warnings ignored)
[ ] Migrations committed and applied (not just `db push`)
[ ] All required env vars set on the correct app/domain
[ ] DB connection string valid: correct host, port, URL-encoded password
[ ] /health endpoint returns 200 with dependencies actually reachable
[ ] Security headers present (helmet or equivalent)
[ ] CORS scoped to explicit origins
[ ] Rate limiting on public + auth-sensitive routes
[ ] No secrets committed to git; .env gitignored
[ ] Structured logging in place, no secrets logged
[ ] Rollback point (git tag/commit) recorded before deploying
[ ] DNS/CDN routes correctly to the app (not a bare A record bypassing
    a reverse-proxy layer the app depends on)
[ ] Resource limits (shared hosting process caps) checked, not assumed
```

Never mark production-ready with any unchecked item that falls under
Security, Database, or Rollback — those are the categories where a
"warning" today becomes an incident later.
