# Observability

Require before calling something production-ready:
- Structured logging (JSON logs with levels), not scattered `console.log`
- Request IDs threaded through logs for tracing a single request
- A real health endpoint distinguishing "process is up" from "dependencies
  (DB, external APIs) are reachable"
- Uptime monitoring hitting the health endpoint from outside the host
- Error reporting/aggregation (even a simple one) so failures surface
  without someone manually tailing logs
- External API health tracked separately (e.g. a third-party dropshipping
  API being down shouldn't look identical to your own DB being down)
