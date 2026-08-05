# Rollback

Every deploy must have an answer to: "if this breaks, how do we undo it in
under 5 minutes?"

Checklist before deploying:
- A git tag or commit SHA is recorded as the last-known-good state
- Database migrations are additive/backward-compatible where possible, so
  rolling back the app doesn't require rolling back the schema too
- The previous build artifact/deployment is still available to redeploy
  (don't rely on "just redeploy from git" if the git-connected redeploy
  path itself can be the thing that's broken)
- Rollback has actually been rehearsed at least once, not just documented
