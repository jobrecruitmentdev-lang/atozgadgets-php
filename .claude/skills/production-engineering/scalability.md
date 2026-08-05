# Scalability

- Identify the actual current bottleneck before recommending scaling —
  don't add infrastructure speculatively
- Shared hosting resource pools (CPU/processes/IOPS shared across every
  site on the account) are a real ceiling for persistent Node.js
  processes; know where that ceiling is before promising uptime
- Stateless app processes (no in-memory session state) so horizontal
  scaling is actually possible later if needed
- Database connection pooling sized for the realistic concurrent load,
  not the shared host's absolute max
