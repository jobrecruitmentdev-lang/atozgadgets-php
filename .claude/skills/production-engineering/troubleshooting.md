# Troubleshooting — Common Failure Signatures

| Symptom | Likely cause |
|---|---|
| Generic host 503 "server is temporarily busy" on every path including `/health` | App process not running / crash-looping, or a WAF/edge block before the app is even reached |
| 503 on one app, 200 on a sibling app on the same account | App-specific crash (env var, DB), not a platform-wide outage |
| `Cannot GET /` | App is actually running — this is Express's normal response for an unregistered root route, not an error |
| `invalid port number in database URL` | Unescaped special character in the DB password within the connection string |
| `Authentication failed against database server` | Password in the connection string doesn't match the DB user's actual current password |
| `PANIC: timer has gone away` (Prisma) | Query engine crash, often transient — restart the app process |
| 401 on an auth-check endpoint (`/api/me`, `/auth/me`) on a public page | Expected behavior for a logged-out visitor, not a bug |
| 500 immediately after adding an env var | The app usually needs an explicit restart — a rebuild/redeploy does not always restart the running process |
| Everything flaps between 200 and 503/504 right after heavy automated testing | Check if your own test traffic tripped a WAF/rate-limit — reduce request volume and re-check |
