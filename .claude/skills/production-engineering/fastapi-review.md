# FastAPI / Python Review

- Pydantic models validate all external input at the boundary
- Async DB driver used consistently (no blocking sync calls inside async
  handlers)
- `/health` and `/ready` endpoints distinct (liveness vs. readiness)
- CORS middleware scoped to explicit origins
- Rate limiting middleware present on auth/public endpoints
- Structured logging (not print statements) with request IDs
- Secrets loaded via environment/settings object, never hardcoded
- Uvicorn/Gunicorn worker count sized to available CPU, not left at
  framework defaults blindly
