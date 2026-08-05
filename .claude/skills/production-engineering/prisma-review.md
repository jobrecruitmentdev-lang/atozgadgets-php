# Prisma Review

Check:
- `prisma/migrations/` exists and is committed — `db push` alone in
  production means no migration history and no rollback path
- Connection string is valid: correct scheme (`mysql://`/`postgresql://`),
  password URL-encoded if it contains reserved characters, correct host
  (not `localhost` unless the DB really is co-located), correct port
- Connection pooling configured for serverless/multi-instance deploys
  (`connection_limit` param or a pooler like PgBouncer/PlanetScale)
- No `$queryRawUnsafe` with string-interpolated user input
- N+1 query patterns — look for `.map()` over a list awaiting a query per
  item instead of a single `include`/`findMany` with relations
- Transactions (`$transaction`) used for any multi-step write that must be
  atomic (e.g. inventory deduction + order creation)
- Prisma Client generated for the correct binary target for the deploy
  host's OS (mismatches cause query-engine panics/crashes at runtime)

## Common production errors and root causes
- `invalid port number in database URL` — unescaped special character in
  the password (or host) portion of the connection string
- `Authentication failed against database server` — password in the
  connection string doesn't match the actual DB user's current password
- `PANIC: timer has gone away` — query engine crash, often transient after
  a cold start or long idle period; restart the app process
- `Cannot find module '.../x.repository'` (TS2307) with NodeNext module
  resolution — relative imports missing explicit `.js` extensions
