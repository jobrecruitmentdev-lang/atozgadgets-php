# Disaster Recovery

- Database backups exist AND have been test-restored at least once
- File/media uploads are backed up separately from the database
- Environment variables/secrets are recorded somewhere recoverable (a
  password manager or secrets vault), not only inside one hPanel/host UI
  that could itself become inaccessible
- A documented, even if informal, "what do we do if X is down" runbook
  exists for the critical dependencies (DB, payment gateway, email)
