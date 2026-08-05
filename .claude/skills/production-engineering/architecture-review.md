# Architecture Review

Identify the pattern actually in use (don't assume — read the code):

```
Monolith / Modular Monolith / Microservices
Layered Architecture / MVC / Hexagonal / Clean Architecture
```

Checks:
- Does the pattern match the team size and traffic, or is it over-engineered
  for the current stage?
- Are layers actually separated (e.g. controllers not doing DB queries
  directly), or is the pattern nominal only?
- Is there a single source of truth for domain models (e.g. one Prisma
  schema), or duplicated type definitions across frontend/backend?
- Are cross-cutting concerns (auth, logging, error handling) centralized or
  copy-pasted per route?

Recommend improvements only where they unblock a real, current problem —
not speculative future-proofing.
