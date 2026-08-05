# Express / Node API Review

- `helmet()` applied
- CORS scoped to an explicit origin allowlist (env-driven), not
  `cors()` with no options (reflects any origin)
- Rate limiting on the global API surface and tighter limits on
  auth-sensitive routes (login, register, forgot-password)
- Centralized error-handling middleware (4-arg `(err, req, res, next)`)
  registered last, formats/logs errors consistently
- `process.on('unhandledRejection')` and `process.on('uncaughtException')`
  handlers present — an unhandled rejection should not leave the process
  in a zombie state
- Structured logger (pino/winston) in use — no raw `console.log` of
  request data, and never log secrets or tokens (a password-reset link
  logged in plaintext is a full account-takeover vector if it ever reaches
  production logs)
- `/health` endpoint exists and is genuinely cheap (no DB call needed to
  answer it, so it doesn't falsely report "up" or "down" based on DB
  state alone — or, if it does check the DB, that's an intentional design
  choice, not an accident)
