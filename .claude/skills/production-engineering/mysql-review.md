# MySQL Review

- Indexes exist on foreign keys and frequently-filtered/sorted columns
- No unbounded `SELECT *` on large tables in hot paths
- Backups: confirm an actual backup exists and has been restored at least
  once as a test — an untested backup is not a recovery plan
- Character set/collation consistent across tables (`utf8mb4` recommended)
- Connection limits: shared hosting MySQL has a max-connections cap:
  confirm the app's pool size won't exhaust it, especially with multiple
  app instances or serverless cold starts
- Long-running or stuck queries: check for locks blocking other queries
