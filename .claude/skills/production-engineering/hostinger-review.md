# Hostinger Review

Verify, in this order:
- Node.js version compatibility (`engines.node` vs. what's selected in
  hPanel)
- Build command actually runs (check build logs, not just "completed")
- Start command / entry file matches the real build output path
- Environment variables are set **on the correct app** — Hostinger's
  Node.js Web Apps are per-domain/subdomain; each one has its own separate
  Environment Variables page. A var set on the frontend app does nothing
  for the backend app on a different (sub)domain, and vice versa.
- SSL is active on the domain
- Automatic deployment (GitHub connection) is wired to the right branch and
  root directory
- Restart behavior: after any env var change, the app must be explicitly
  restarted — a rebuild alone does not necessarily restart the process
- Resource limits: Business/shared hosting plans share CPU, IOPS, and **Max
  Processes across every site on the account**, not per-site. Running
  persistent Node.js server processes (unlike short-lived PHP-FPM workers)
  is comparatively resource-heavy; check the account-wide Resources Usage
  graph, not just the one site, when diagnosing instability.
- Runtime logs (hPanel sidebar) for actual crash output — build logs alone
  do not show runtime crashes after a successful build
- Deployment mechanism matters: an archive-uploaded Node app and a
  GitHub-connected Node app are different underlying objects. Archive
  uploads may not get a full hPanel management UI (no Environment
  Variables page). Prefer GitHub-connected deploys for anything that needs
  ongoing env var management.
- Subdomain DNS: a Node.js Web App subdomain needs to route through
  Hostinger's CDN/reverse proxy layer, not a bare A/AAAA record to the
  server IP, for the Node process to actually receive traffic.
