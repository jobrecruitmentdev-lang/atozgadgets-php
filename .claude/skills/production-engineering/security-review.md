# Security Review

Detect, with concrete evidence (file:line), not just a generic checklist:

- SQL injection: any raw query built via string interpolation/concatenation
- XSS: unescaped user content rendered into HTML
- CSRF: state-changing requests without a token/same-site cookie protection
- Open redirects: redirect targets taken directly from user input
- Missing CSP / security headers
- Weak CORS (wildcard origin with credentials)
- `.env` or other secret files committed to git (check `git ls-files`, not
  just `.gitignore` — a `.gitignore` entry added after the fact does not
  remove history)
- Hardcoded secrets/API keys in source
- Missing `helmet()` / equivalent security-headers middleware
- Passwords/tokens logged in plaintext anywhere
- JWT verification without an explicit algorithm allowlist
