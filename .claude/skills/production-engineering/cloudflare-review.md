# Cloudflare / CDN Review

- DNS records point where they should (A/AAAA/CNAME/ALIAS matching the
  actual hosting target)
- Proxy status (orange-cloud) correct for each record — proxied for web
  traffic, DNS-only for anything needing direct connection (mail, some
  API integrations)
- SSL mode (Full/Full Strict) matches what the origin actually supports
- Cache rules don't cache dynamic/authenticated responses
- WAF / firewall rules — check for rules blocking legitimate traffic
  (including your own automated health checks/monitoring, which can look
  like bot traffic under heavy polling)
- Rate limiting thresholds are sane for real traffic patterns
- Brotli/HTTP/3 enabled where supported
- Security headers (HSTS, CSP, X-Frame-Options) present at the edge
