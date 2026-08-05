# Performance Review

Targets:
```
TTFB < 600ms   LCP < 2.5s   INP < 200ms   CLS < 0.1
```

Check:
- Caching headers on static assets and cacheable API responses
- Compression (gzip/brotli) enabled
- Images optimized and correctly sized (no full-resolution images served
  to thumbnail slots)
- Font loading doesn't block render
- Bundle size — no unused heavy dependencies shipped to the client
- Server components used where they reduce client JS, not applied
  dogmatically where interactivity is actually needed
- Database query latency — slow queries identified and indexed
- Memory/CPU headroom on the host, especially on shared hosting plans
  with pooled resource limits across multiple sites
