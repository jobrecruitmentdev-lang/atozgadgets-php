# Incident Response

```
Collect Logs -> Identify Layer -> Reproduce -> Determine Root Cause
  -> Estimate Impact -> Recommend Fix -> Recommend Rollback -> Verify Recovery
```

Rules:
- Identify the layer (DNS/CDN, app process, database, third-party API)
  before proposing a fix — a DB auth error and a WAF block produce similar
  user-facing symptoms (site down) but need completely different fixes.
- Prefer the smallest reversible change that resolves the incident over a
  larger "fix everything" change made under pressure.
- After applying a fix, verify with a single targeted check — avoid
  hammering the production endpoint with repeated automated requests
  while debugging, since that traffic pattern can itself trigger
  bot-protection/WAF blocks and create a second, self-inflicted incident.
- Write down the root cause and fix once resolved — this is what feeds
  future `troubleshooting.md` entries.
